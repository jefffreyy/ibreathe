"""
iBreathe SCADA - ML Microservice
Provides anomaly detection (adaptive thresholds) and air quality forecasting.
Run: python ml_service.py
"""

import time
import threading
from datetime import datetime, timedelta
from flask import Flask, jsonify, request
from flask_cors import CORS
import mysql.connector
import numpy as np
import pandas as pd

app = Flask(__name__)
CORS(app)

# ==================== CONFIGURATION ====================

DB_CONFIG = {
    'host': 'localhost',
    'user': 'ibreathe_db',
    'password': 'ibreathe_db',
    'database': 'ibreathe_db'
}

ML_CONFIG = {
    'zscore_threshold': 2.5,
    'training_window_days': 7,
    'baseline_refresh_seconds': 3600,
    'forecast_cache_seconds': 300,
    'forecast_lookback_hours': 2,
    'min_samples_baseline': 10,
    'min_samples_forecast': 20,
}

SENSOR_LABELS = {
    'temperature': ('Temperature', '\u00b0C'),
    'humidity': ('Humidity', '%'),
    'co2': ('CO\u2082', 'µg/m³'),
    'pm25': ('PM2.5', '\u03bcg/m\u00b3'),
}

# In-memory caches
_baseline_cache = {}
_forecast_cache = {}

# ==================== DATABASE ====================

def get_db():
    return mysql.connector.connect(**DB_CONFIG)

def query_df(sql, params=None):
    conn = get_db()
    try:
        return pd.read_sql(sql, conn, params=params)
    finally:
        conn.close()

# ==================== BASELINE COMPUTATION ====================

def compute_baseline(device_id, sensor_type, hour_of_day):
    window_start = datetime.now() - timedelta(days=ML_CONFIG['training_window_days'])
    sql = """
        SELECT value FROM tbl_scada_readings
        WHERE device_id = %s AND sensor_type = %s
          AND HOUR(recorded_at) = %s
          AND recorded_at >= %s
        ORDER BY recorded_at DESC
    """
    df = query_df(sql, (device_id, sensor_type, hour_of_day,
                        window_start.strftime('%Y-%m-%d %H:%M:%S')))

    if df.empty or len(df) < ML_CONFIG['min_samples_baseline']:
        return None

    values = df['value'].astype(float)
    return {
        'mean': float(values.mean()),
        'std': float(values.std()),
        'min': float(values.min()),
        'max': float(values.max()),
        'count': len(values),
        'updated': time.time()
    }

def refresh_all_baselines():
    devices_df = query_df("SELECT id FROM tbl_scada_devices WHERE status != 'maintenance'")
    if devices_df.empty:
        return 0

    sensor_types = ['temperature', 'humidity', 'co2', 'pm25']
    count = 0
    conn = get_db()
    cursor = conn.cursor()

    for device_id in devices_df['id'].tolist():
        for sensor_type in sensor_types:
            for hour in range(24):
                baseline = compute_baseline(device_id, sensor_type, hour)
                if baseline is None:
                    continue

                sql = """
                    INSERT INTO tbl_scada_ml_baselines
                        (device_id, sensor_type, hour_of_day, mean_value, std_dev,
                         sample_count, min_value, max_value, computed_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, NOW())
                    ON DUPLICATE KEY UPDATE
                        mean_value = VALUES(mean_value),
                        std_dev = VALUES(std_dev),
                        sample_count = VALUES(sample_count),
                        min_value = VALUES(min_value),
                        max_value = VALUES(max_value),
                        computed_at = NOW()
                """
                cursor.execute(sql, (
                    device_id, sensor_type, hour,
                    baseline['mean'], baseline['std'], baseline['count'],
                    baseline['min'], baseline['max']
                ))

                cache_key = f"{device_id}_{sensor_type}_{hour}"
                _baseline_cache[cache_key] = baseline
                count += 1

    conn.commit()
    conn.close()
    return count

# ==================== FORECAST COMPUTATION ====================

def compute_forecast(device_id, sensor_type):
    lookback = datetime.now() - timedelta(hours=ML_CONFIG['forecast_lookback_hours'])
    sql = """
        SELECT value, recorded_at FROM tbl_scada_readings
        WHERE device_id = %s AND sensor_type = %s AND recorded_at >= %s
        ORDER BY recorded_at ASC
    """
    df = query_df(sql, (device_id, sensor_type, lookback.strftime('%Y-%m-%d %H:%M:%S')))

    if df.empty or len(df) < ML_CONFIG['min_samples_forecast']:
        return None

    df['recorded_at'] = pd.to_datetime(df['recorded_at'])
    df = df.set_index('recorded_at')
    df_r = df['value'].resample('1min').mean().dropna()

    if len(df_r) < 10:
        return None

    values = df_r.values.astype(float)
    n = len(values)
    x = np.arange(n, dtype=float)

    # Exponential weights: recent data gets more influence
    weights = np.exp(np.linspace(-1, 0, n))

    # Weighted linear regression
    coeffs = np.polyfit(x, values, deg=1, w=weights)
    fitted = np.polyval(coeffs, x)
    residual_std = float(np.std(values - fitted))

    current_value = float(values[-1])
    predictions = []

    for h in [30, 60, 120]:
        future_x = n - 1 + h
        predicted = float(np.polyval(coeffs, future_x))
        margin = 1.96 * residual_std * np.sqrt(1 + h / n)

        # Clamp predictions to reasonable ranges
        if sensor_type == 'humidity':
            predicted = max(0, min(100, predicted))
        elif sensor_type in ('co2', 'pm25'):
            predicted = max(0, predicted)

        predictions.append({
            'horizon_minutes': h,
            'horizon_label': f"{h}min" if h < 60 else f"{h // 60}hr" if h % 60 == 0 else f"{h}min",
            'predicted_value': round(predicted, 2),
            'confidence_lower': round(max(0, predicted - margin), 2),
            'confidence_upper': round(predicted + margin, 2),
            'trend_per_hour': round(float(coeffs[0]) * 60, 2),
        })

    return {
        'device_id': device_id,
        'sensor_type': sensor_type,
        'status': 'ok',
        'current_value': round(current_value, 2),
        'data_points': n,
        'trend_per_hour': round(float(coeffs[0]) * 60, 2),
        'predictions': predictions,
        'computed_at': datetime.now().isoformat()
    }

# ==================== ENDPOINTS ====================

@app.route('/ml/health', methods=['GET'])
def health():
    try:
        conn = get_db()
        cursor = conn.cursor()
        cursor.execute("SELECT 1")
        cursor.fetchone()
        conn.close()
        return jsonify({
            'status': 'ok',
            'service': 'iBreathe ML',
            'db': 'connected',
            'baselines_cached': len(_baseline_cache),
            'forecasts_cached': len(_forecast_cache),
            'timestamp': datetime.now().isoformat()
        })
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500


@app.route('/ml/anomaly', methods=['GET'])
def anomaly():
    device_id = request.args.get('device_id', type=int)
    sensor_type = request.args.get('sensor_type', '')
    value = request.args.get('value', type=float)

    if not all([device_id, sensor_type, value is not None]):
        return jsonify({'error': 'Missing: device_id, sensor_type, value'}), 400

    hour = datetime.now().hour
    cache_key = f"{device_id}_{sensor_type}_{hour}"

    baseline = _baseline_cache.get(cache_key)
    if baseline is None:
        baseline = compute_baseline(device_id, sensor_type, hour)
        if baseline:
            _baseline_cache[cache_key] = baseline

    if not baseline or baseline['std'] < 0.001:
        return jsonify({
            'device_id': device_id, 'sensor_type': sensor_type, 'value': value,
            'is_anomaly': False, 'zscore': 0.0,
            'reason': 'insufficient_data', 'message': None
        })

    zscore = (value - baseline['mean']) / baseline['std']
    is_anomaly = abs(zscore) > ML_CONFIG['zscore_threshold']

    message = None
    severity = 'info'
    if is_anomaly:
        label, unit = SENSOR_LABELS.get(sensor_type, (sensor_type, ''))
        direction = 'above' if zscore > 0 else 'below'
        message = (f"{label} ({value}{unit}) is unusually {direction} "
                   f"the learned baseline ({baseline['mean']:.1f}{unit}) for this hour")
        severity = 'warning' if abs(zscore) < 3.5 else 'critical'

    return jsonify({
        'device_id': device_id, 'sensor_type': sensor_type, 'value': value,
        'is_anomaly': is_anomaly, 'zscore': round(zscore, 3),
        'severity': severity,
        'baseline_mean': round(baseline['mean'], 2),
        'baseline_std': round(baseline['std'], 2),
        'sample_count': baseline['count'],
        'message': message
    })


@app.route('/ml/forecast', methods=['GET'])
def forecast():
    device_id = request.args.get('device_id', type=int)
    sensor_type = request.args.get('sensor_type', '')

    if not all([device_id, sensor_type]):
        return jsonify({'error': 'Missing: device_id, sensor_type'}), 400

    cache_key = f"{device_id}_{sensor_type}"
    cached = _forecast_cache.get(cache_key)
    if cached and (time.time() - cached['updated']) < ML_CONFIG['forecast_cache_seconds']:
        return jsonify(cached['data'])

    result = compute_forecast(device_id, sensor_type)
    if result is None:
        return jsonify({
            'device_id': device_id, 'sensor_type': sensor_type,
            'status': 'insufficient_data', 'predictions': []
        })

    _forecast_cache[cache_key] = {'data': result, 'updated': time.time()}
    return jsonify(result)


@app.route('/ml/retrain', methods=['POST'])
def retrain():
    count = refresh_all_baselines()
    return jsonify({
        'status': 'ok',
        'baselines_updated': count,
        'timestamp': datetime.now().isoformat()
    })

# ==================== BACKGROUND THREAD ====================

def baseline_refresh_loop():
    time.sleep(10)
    while True:
        try:
            print(f"[ML] Refreshing baselines at {datetime.now().strftime('%H:%M:%S')}")
            count = refresh_all_baselines()
            print(f"[ML] Refreshed {count} baselines")
        except Exception as e:
            print(f"[ML] Baseline refresh error: {e}")
        time.sleep(ML_CONFIG['baseline_refresh_seconds'])

# ==================== MAIN ====================

if __name__ == '__main__':
    t = threading.Thread(target=baseline_refresh_loop, daemon=True)
    t.start()

    print("=" * 50)
    print("  iBreathe ML Service - localhost:5555")
    print("  GET  /ml/health")
    print("  GET  /ml/anomaly?device_id=&sensor_type=&value=")
    print("  GET  /ml/forecast?device_id=&sensor_type=")
    print("  POST /ml/retrain")
    print("=" * 50)

    app.run(host='0.0.0.0', port=5555, debug=False)
