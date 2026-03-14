/**
 * iBreathe SCADA - Predictive Analysis Page
 */

var forecastChart = null;
var cachedForecasts = {};
var cachedCurrentValues = {};
var currentSensor = 'temperature';

$(document).ready(function() {
    loadPredictive();

    $(document).on('click', '#forecast-sensor-btns .btn', function() {
        $('#forecast-sensor-btns .btn').removeClass('active');
        $(this).addClass('active');
        currentSensor = $(this).data('sensor');
        renderForecastChart(currentSensor);
    });
});

function loadPredictive() {
    var deviceId = $('#pred-device').val();

    $('#pred-loading').show();
    $('#pred-risk-cards, #pred-charts').hide();

    $.getJSON(BASE_URL + 'api/predictive?device_id=' + deviceId, function(resp) {
        $('#pred-loading').hide();
        $('#pred-risk-cards, #pred-charts').show();

        // ML status badge
        if (resp.ml_available) {
            $('#pred-ml-badge').removeClass('badge-secondary').addClass('badge-success')
                .html('<i class="fas fa-robot mr-1"></i>ML Active');
        } else {
            $('#pred-ml-badge').removeClass('badge-success').addClass('badge-secondary')
                .html('<i class="fas fa-robot mr-1"></i>ML Offline — Using rule-based fallback');
        }

        cachedForecasts = resp.forecasts || {};
        cachedCurrentValues = resp.current_values || {};

        renderRiskCards(resp.risks, resp.current_values, resp.forecasts);
        renderForecastChart(currentSensor);
        renderAnomalies(resp.anomalies);
        renderRecommendations(resp.recommendations);
    }).fail(function() {
        $('#pred-loading').hide();
        toastr.error('Failed to load predictive data');
    });
}

function renderRiskCards(risks, current, forecasts) {
    var sensorMeta = {
        'temperature': { label: 'Temperature', unit: '°C', icon: 'fa-thermometer-half' },
        'humidity':    { label: 'Humidity', unit: '%', icon: 'fa-tint' },
        'gas':         { label: 'Gas', unit: 'pm2.5', icon: 'fa-cloud' },
        'pm25':        { label: 'PM2.5', unit: 'μg/m³', icon: 'fa-smog' }
    };

    var riskColors = {
        'low':      { bg: '#ecfdf5', border: '#10b981', badge: 'badge-success', label: 'Low Risk' },
        'warning':  { bg: '#fffbeb', border: '#f59e0b', badge: 'badge-warning', label: 'Warning' },
        'critical': { bg: '#fef2f2', border: '#ef4444', badge: 'badge-danger', label: 'Critical' }
    };

    $.each(sensorMeta, function(key, meta) {
        var $card = $('#risk-' + key);
        var risk = (risks && risks[key]) ? risks[key] : 'low';
        var rc = riskColors[risk] || riskColors['low'];
        var curVal = (current && current[key] !== undefined) ? current[key] : '--';
        var predVal = '--';
        if (forecasts && forecasts[key] && forecasts[key].predictions && forecasts[key].predictions.length > 0) {
            var lastPred = forecasts[key].predictions[forecasts[key].predictions.length - 1];
            predVal = lastPred.predicted_value.toFixed(1);
        }

        $card.html(
            '<div class="card predictive-risk-card" style="border-left:4px solid ' + rc.border + '; background:' + rc.bg + '">' +
            '<div class="card-body p-3">' +
            '<div class="d-flex justify-content-between align-items-center mb-2">' +
            '<span style="font-weight:700; font-size:13px"><i class="fas ' + meta.icon + ' mr-1"></i>' + meta.label + '</span>' +
            '<span class="badge ' + rc.badge + '" style="font-size:10px">' + rc.label + '</span>' +
            '</div>' +
            '<div class="row text-center">' +
            '<div class="col-6">' +
            '<div class="text-muted" style="font-size:10px">Current</div>' +
            '<div style="font-size:20px; font-weight:700; color:#1e293b">' + (typeof curVal === 'number' ? curVal.toFixed(1) : curVal) + '</div>' +
            '<div class="text-muted" style="font-size:10px">' + meta.unit + '</div>' +
            '</div>' +
            '<div class="col-6">' +
            '<div class="text-muted" style="font-size:10px">Predicted (2hr)</div>' +
            '<div style="font-size:20px; font-weight:700; color:' + rc.border + '">' + predVal + '</div>' +
            '<div class="text-muted" style="font-size:10px">' + meta.unit + '</div>' +
            '</div>' +
            '</div>' +
            '</div></div>'
        );
    });
}

function renderForecastChart(sensor) {
    if (forecastChart) forecastChart.destroy();

    var fc = cachedForecasts[sensor];
    if (!fc || !fc.predictions || fc.predictions.length === 0) {
        $('#forecast-chart').parent().html('<div class="text-center p-5"><p class="text-muted">No forecast data available for this sensor.<br>ML service may be offline.</p></div>');
        return;
    }

    // Ensure canvas exists
    var $parent = $('#forecast-chart').parent();
    if ($parent.find('canvas').length === 0) {
        $parent.html('<canvas id="forecast-chart"></canvas>');
    }

    var labels = [];
    var predicted = [];
    var upper = [];
    var lower = [];

    // Add current value as first point
    if (cachedCurrentValues[sensor] !== undefined) {
        labels.push('Now');
        predicted.push(cachedCurrentValues[sensor]);
        upper.push(cachedCurrentValues[sensor]);
        lower.push(cachedCurrentValues[sensor]);
    }

    fc.predictions.forEach(function(p) {
        labels.push(p.horizon);
        predicted.push(p.predicted_value.toFixed(1));
        upper.push(p.confidence_upper.toFixed(1));
        lower.push(p.confidence_lower.toFixed(1));
    });

    var colors = { temperature: '#ef4444', humidity: '#6366f1', gas: '#f59e0b', pm25: '#10b981' };
    var color = colors[sensor] || '#64748b';

    forecastChart = new Chart($('#forecast-chart')[0].getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Predicted',
                    borderColor: color,
                    backgroundColor: color + '22',
                    data: predicted,
                    fill: false,
                    tension: 0.3,
                    pointRadius: 5,
                    borderWidth: 3
                },
                {
                    label: 'Upper Bound',
                    borderColor: color + '44',
                    backgroundColor: color + '11',
                    data: upper,
                    fill: '+1',
                    tension: 0.3,
                    pointRadius: 0,
                    borderWidth: 1,
                    borderDash: [5, 5]
                },
                {
                    label: 'Lower Bound',
                    borderColor: color + '44',
                    backgroundColor: 'transparent',
                    data: lower,
                    fill: false,
                    tension: 0.3,
                    pointRadius: 0,
                    borderWidth: 1,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#64748b' } } },
            scales: {
                x: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' } },
                y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' } }
            }
        }
    });
}

function renderAnomalies(anomalies) {
    if (!anomalies || Object.keys(anomalies).length === 0) {
        $('#anomaly-list').html('<div class="text-center p-3"><i class="fas fa-check-circle mr-1" style="color:#16a34a"></i><span class="text-muted" style="font-size:13px">No anomalies detected</span></div>');
        return;
    }

    var sensorLabels = { temperature: 'Temperature', humidity: 'Humidity', gas: 'Gas', pm25: 'PM2.5' };
    var html = '';

    $.each(anomalies, function(sensor, anom) {
        var isAnomaly = anom.is_anomaly;
        var icon = isAnomaly ? 'fa-exclamation-triangle' : 'fa-check-circle';
        var color = isAnomaly ? (anom.severity === 'critical' ? '#ef4444' : '#f59e0b') : '#16a34a';
        var label = sensorLabels[sensor] || sensor;
        var zScore = anom.z_score ? anom.z_score.toFixed(2) : 'N/A';
        var status = isAnomaly ? 'Anomaly (z=' + zScore + ')' : 'Normal (z=' + zScore + ')';

        html += '<div class="anomaly-item" style="padding:8px 12px; border-bottom:1px solid #f1f5f9; font-size:12px">'
            + '<i class="fas ' + icon + ' mr-2" style="color:' + color + '"></i>'
            + '<strong>' + label + ':</strong> ' + status
            + '</div>';
    });

    $('#anomaly-list').html(html);
}

function renderRecommendations(recs) {
    if (!recs || recs.length === 0) {
        $('#recommendations-list').html('<div class="text-center p-3"><i class="fas fa-check-circle mr-1" style="color:#16a34a"></i><span class="text-muted" style="font-size:13px">All parameters within safe range</span></div>');
        return;
    }

    var html = '';
    var sensorLabels = { temperature: 'Temperature', humidity: 'Humidity', Gas: 'Gas', pm25: 'PM2.5' };

    recs.forEach(function(r) {
        var icon = r.severity === 'critical' ? 'fa-exclamation-circle' : 'fa-exclamation-triangle';
        var color = r.severity === 'critical' ? '#ef4444' : '#f59e0b';
        var label = sensorLabels[r.sensor] || r.sensor;

        html += '<div class="ai-insight-item">'
            + '<div class="ai-insight-icon ' + r.severity + '">'
            + '<i class="fas ' + icon + '"></i>'
            + '</div>'
            + '<div class="ai-insight-content">'
            + '<div class="ai-insight-message" style="font-size:12px">' + escapeHtml(r.message) + '</div>'
            + '<div class="ai-insight-meta"><span class="ai-insight-category">' + label + '</span></div>'
            + '</div></div>';
    });

    $('#recommendations-list').html(html);
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
