<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * iBreathe SCADA - ML Microservice Client
 * Communicates with Python Flask ML service (localhost:5555).
 * Falls back gracefully if service is unavailable.
 */
class Ml_client {

    const SERVICE_URL = 'http://localhost:5555';
    const TIMEOUT     = 3;

    private $_available = null;

    /**
     * Check if ML service is running (cached per request).
     */
    public function is_available() {
        if ($this->_available !== null) {
            return $this->_available;
        }
        $r = $this->_get('/ml/health');
        $this->_available = ($r !== false && isset($r['status']) && $r['status'] === 'ok');
        return $this->_available;
    }

    /**
     * Check a single value for anomaly.
     */
    public function check_anomaly($device_id, $sensor_type, $value) {
        if (!$this->is_available()) return false;
        $q = http_build_query(array(
            'device_id'   => $device_id,
            'sensor_type' => $sensor_type,
            'value'       => $value
        ));
        return $this->_get('/ml/anomaly?' . $q);
    }

    /**
     * Get forecast for a device+sensor.
     */
    public function get_forecast($device_id, $sensor_type) {
        if (!$this->is_available()) return false;
        $q = http_build_query(array(
            'device_id'   => $device_id,
            'sensor_type' => $sensor_type
        ));
        return $this->_get('/ml/forecast?' . $q);
    }

    /**
     * Batch anomaly check — returns insight-format array.
     */
    public function get_anomaly_insights($readings, $names) {
        $insights = array();
        if (!$this->is_available()) return $insights;

        foreach ($readings as $device_id => $sensors) {
            foreach ($sensors as $sensor_type => $data) {
                if (!isset($data['value'])) continue;
                $r = $this->check_anomaly($device_id, $sensor_type, $data['value']);
                if (!$r || !isset($r['is_anomaly']) || !$r['is_anomaly']) continue;

                $room = isset($names[$device_id]) ? $names[$device_id] : 'Device #' . $device_id;
                $insights[] = array(
                    'severity' => isset($r['severity']) ? $r['severity'] : 'warning',
                    'icon'     => 'fas fa-robot',
                    'message'  => $r['message'] ? $r['message'] : ($room . ' — anomalous reading detected'),
                    'category' => 'Anomaly Detection',
                    'time'     => date('H:i'),
                    'source'   => 'ml'
                );
            }
        }
        return $insights;
    }

    /**
     * Get forecast insights — returns insight-format array (max 3).
     */
    public function get_forecast_insights($readings, $names) {
        $insights = array();
        if (!$this->is_available()) return $insights;

        $sensor_info = array(
            'temperature' => array('label' => 'Temperature', 'unit' => '°C'),
            'humidity'    => array('label' => 'Humidity', 'unit' => '%'),
            'co2'         => array('label' => 'CO₂', 'unit' => 'ppm'),
            'pm25'        => array('label' => 'PM2.5', 'unit' => 'μg/m³'),
        );
        $count = 0;

        foreach ($readings as $device_id => $sensors) {
            foreach ($sensors as $sensor_type => $data) {
                $r = $this->get_forecast($device_id, $sensor_type);
                if (!$r || !isset($r['predictions']) || empty($r['predictions']) || $r['status'] !== 'ok') continue;

                $room = isset($names[$device_id]) ? $names[$device_id] : 'Device #' . $device_id;
                $info = isset($sensor_info[$sensor_type]) ? $sensor_info[$sensor_type] : array('label' => $sensor_type, 'unit' => '');

                // Find 1-hour prediction
                $pred = null;
                foreach ($r['predictions'] as $p) {
                    if ($p['horizon_minutes'] == 60) { $pred = $p; break; }
                }
                if (!$pred) continue;

                $trend = $r['trend_per_hour'];
                if ($trend > 0.5) {
                    $dir = 'rising';
                    $icon = 'fas fa-arrow-trend-up';
                } elseif ($trend < -0.5) {
                    $dir = 'falling';
                    $icon = 'fas fa-arrow-trend-down';
                } else {
                    $dir = 'stable';
                    $icon = 'fas fa-chart-line';
                }

                $msg = $info['label'] . ' in ' . $room . ' forecast: '
                     . round($pred['predicted_value'], 1) . $info['unit']
                     . ' in 1hr (' . $dir . ')';

                $insights[] = array(
                    'severity' => 'info',
                    'icon'     => $icon,
                    'message'  => $msg,
                    'category' => 'Forecast',
                    'time'     => date('H:i'),
                    'source'   => 'ml'
                );

                $count++;
                if ($count >= 3) break 2;
            }
        }
        return $insights;
    }

    /**
     * HTTP GET to ML service.
     */
    private function _get($path) {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => self::SERVICE_URL . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_HTTPHEADER     => array('Accept: application/json'),
        ));
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $code !== 200) return false;
        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : false;
    }
}
