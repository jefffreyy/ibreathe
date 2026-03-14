/**
 * iBreathe SCADA Dashboard - Real-time polling and gauge rendering
 */

var gaugeCharts = {};
var trendChart = null;
var previousAlarmCount = 0;

// ==================== INITIALIZATION ====================

$(document).ready(function() {
    initGauges();
    initTrendChart();
    pollDashboard();
    pollInsights();
    setInterval(pollDashboard, POLLING_INTERVAL);
    setInterval(pollInsights, POLLING_INTERVAL * 3); // Every 15s (insights don't change as fast)
});

// ==================== GAUGE INITIALIZATION ====================

function initGauges() {
    var gaugeConfigs = {
        'temperature': { min: 10, max: 45, colors: ['#3b82f6', '#22c55e', '#22c55e', '#ef4444'] },
        'humidity':    { min: 0,  max: 100, colors: ['#ef4444', '#22c55e', '#22c55e', '#3b82f6'] },
        'gas':         { min: 0,  max: 3000, colors: ['#22c55e', '#22c55e', '#eab308', '#ef4444'] },
        'pm25':        { min: 0,  max: 200, colors: ['#22c55e', '#eab308', '#f97316', '#ef4444'] }
    };

    $.each(gaugeConfigs, function(type, config) {
        var ctx = document.getElementById('gauge-' + type);
        if (!ctx) return;

        gaugeCharts[type] = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [0, 100],
                    backgroundColor: ['rgba(99,102,241,0.2)', '#f1f5f9'],
                    borderWidth: 0,
                    borderRadius: 2
                }]
            },
            options: {
                rotation: -90,
                circumference: 180,
                cutout: '78%',
                responsive: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                animation: { duration: 600, easing: 'easeOutCubic' }
            }
        });

        gaugeCharts[type]._scadaConfig = config;
    });
}

// ==================== TREND CHART ====================

function initTrendChart() {
    var ctx = document.getElementById('trend-chart');
    if (!ctx) return;

    trendChart = new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                { label: 'Temperature (°C)', borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.08)', data: [], fill: true, tension: 0.4, pointRadius: 0, borderWidth: 2 },
                { label: 'Humidity (%)', borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.08)', data: [], fill: true, tension: 0.4, pointRadius: 0, borderWidth: 2 },
                { label: 'Gas (pm2.5)', borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.05)', data: [], fill: false, tension: 0.4, pointRadius: 0, borderWidth: 2, yAxisID: 'y2' },
                { label: 'PM2.5 (μg/m³)', borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.05)', data: [], fill: false, tension: 0.4, pointRadius: 0, borderWidth: 2, yAxisID: 'y2' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    labels: {
                        color: '#64748b',
                        font: { size: 11, family: "'Inter', sans-serif", weight: '500' },
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 8
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f1f5f9',
                    bodyColor: '#cbd5e1',
                    titleFont: { family: "'Inter', sans-serif", weight: '600', size: 12 },
                    bodyFont: { family: "'Inter', sans-serif", size: 11 },
                    borderColor: 'rgba(99,102,241,0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 10,
                    displayColors: true,
                    boxPadding: 4
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 10, family: "'Inter', sans-serif" }, maxTicksLimit: 12 }
                },
                y: {
                    position: 'left',
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 10, family: "'Inter', sans-serif" } },
                    title: { display: true, text: 'Temp / Humidity', color: '#94a3b8', font: { size: 11, family: "'Inter', sans-serif", weight: '500' } }
                },
                y2: {
                    position: 'right',
                    grid: { drawOnChartArea: false, drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 10, family: "'Inter', sans-serif" } },
                    title: { display: true, text: 'CO₂ / PM2.5', color: '#94a3b8', font: { size: 11, family: "'Inter', sans-serif", weight: '500' } }
                }
            }
        }
    });
}

// ==================== POLLING ====================

function pollDashboard() {
    $.ajax({
        url: BASE_URL + 'api/dashboard',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            updateGauges(data.readings);
            updateDeviceStatus(data.devices);
            updateAlarmTicker(data.alarms);
            updateTrendChart(data.trends);
            updateAQI(data.aqi);
            updateStats(data);
            $('#last-update').text('Last update: ' + data.timestamp.split(' ')[1]);
            $('#connection-status').removeClass('badge-danger').addClass('badge-success').html('<i class="fas fa-circle mr-1" style="font-size:8px"></i>Live');
        },
        error: function() {
            $('#connection-status').removeClass('badge-success').addClass('badge-danger').html('<i class="fas fa-circle mr-1" style="font-size:8px"></i>Disconnected');
        }
    });
}

// ==================== UPDATE FUNCTIONS ====================

function updateGauges(readings) {
    if (!readings) return;

    // Use the first device's readings for main gauges
    var deviceId = Object.keys(readings)[0];
    if (!deviceId) return;

    var sensors = readings[deviceId];
    var types = ['temperature', 'humidity', 'gas', 'pm25'];

    types.forEach(function(type) {
        if (!sensors[type] || !gaugeCharts[type]) return;

        var value = sensors[type].value;
        var config = gaugeCharts[type]._scadaConfig;
        var percent = Math.min(100, Math.max(0, ((value - config.min) / (config.max - config.min)) * 100));

        // Update gauge chart
        gaugeCharts[type].data.datasets[0].data = [percent, 100 - percent];

        // Color based on range
        var colorIdx = Math.min(config.colors.length - 1, Math.floor((percent / 100) * config.colors.length));
        gaugeCharts[type].data.datasets[0].backgroundColor[0] = config.colors[colorIdx];
        gaugeCharts[type].update();

        // Update text value
        var display = (type === 'gas') ? Math.round(value) : value.toFixed(1);
        $('#val-' + type).text(display);
    });
}

function updateDeviceStatus(devices) {
    if (!devices || devices.length === 0) return;

    var html = '';
    var onlineCount = 0;

    devices.forEach(function(d) {
        if (d.status === 'online') onlineCount++;

        var badge = '<span class="badge badge-secondary">Offline</span>';
        if (d.status === 'online') badge = '<span class="badge badge-success">Online</span>';
        else if (d.status === 'maintenance') badge = '<span class="badge badge-warning">Maint.</span>';

        var lastSeen = d.last_seen ? d.last_seen.split(' ')[1] : 'Never';
        html += '<tr><td>' + escapeHtml(d.name) + '</td><td>' + badge + '</td><td class="text-muted" style="font-size:12px">' + lastSeen + '</td></tr>';
    });

    $('#device-status-table').html(html);
    $('#stat-online').text(onlineCount);
}

function updateAlarmTicker(alarms) {
    if (!alarms || alarms.length === 0) {
        $('#alarm-ticker').hide();
        $('#recent-alarms-list').html('<p class="text-muted text-center p-3 mb-0">No active alarms</p>');
        $('#stat-alarms').text('0');
        return;
    }

    // Show ticker with latest alarm
    var latest = alarms[0];
    $('#alarm-ticker-text').text('[' + latest.severity.toUpperCase() + '] ' + latest.device_name + ' - ' + latest.message + ' (Value: ' + latest.value + ')');
    $('#alarm-ticker').show();

    // Update count
    $('#stat-alarms').text(alarms.length);
    $('#alarm-badge').text(alarms.length);

    // Toastr for new alarms
    if (alarms.length > previousAlarmCount && previousAlarmCount > 0) {
        var newAlarm = alarms[0];
        if (newAlarm.severity === 'critical') {
            toastr.error(newAlarm.message, newAlarm.device_name + ' - CRITICAL');
        } else if (newAlarm.severity === 'warning') {
            toastr.warning(newAlarm.message, newAlarm.device_name + ' - Warning');
        } else {
            toastr.info(newAlarm.message, newAlarm.device_name);
        }
    }
    previousAlarmCount = alarms.length;

    // Recent alarms list
    var html = '';
    alarms.slice(0, 8).forEach(function(a) {
        var time = a.triggered_at.split(' ')[1].substring(0, 5);
        html += '<div class="alarm-item">'
            + '<span class="alarm-severity ' + a.severity + '"></span>'
            + '<strong>' + escapeHtml(a.device_name) + '</strong> - ' + escapeHtml(a.message)
            + '<span class="alarm-time">' + time + '</span>'
            + '</div>';
    });
    $('#recent-alarms-list').html(html);
}

function updateTrendChart(trends) {
    if (!trendChart || !trends) return;

    // Find the first device's trend data
    var tempKey = null, humKey = null, co2Key = null, pm25Key = null;
    $.each(trends, function(key) {
        if (key.indexOf('_temperature') > -1) tempKey = key;
        if (key.indexOf('_humidity') > -1) humKey = key;
        if (key.indexOf('_gas') > -1) co2Key = key;
        if (key.indexOf('_pm25') > -1) pm25Key = key;
    });

    if (tempKey && trends[tempKey]) {
        trendChart.data.labels = trends[tempKey].labels;
        trendChart.data.datasets[0].data = trends[tempKey].values;
    }
    if (humKey && trends[humKey]) {
        trendChart.data.datasets[1].data = trends[humKey].values;
    }
    if (co2Key && trends[co2Key]) {
        trendChart.data.datasets[2].data = trends[co2Key].values;
    }
    if (pm25Key && trends[pm25Key]) {
        trendChart.data.datasets[3].data = trends[pm25Key].values;
    }

    trendChart.update();
}

function updateAQI(aqi) {
    if (!aqi) return;

    var deviceId = Object.keys(aqi)[0];
    if (!deviceId) return;

    var data = aqi[deviceId];
    $('#stat-aqi').text(data.value);
    $('#stat-aqi-label').text(data.label);
    $('#aqi-box').css('background', 'linear-gradient(135deg, ' + data.color + '88, ' + data.color + '44)');
}

function updateStats(data) {
    if (data.devices) {
        $('#stat-devices').text(data.devices.length);
    }
    if (typeof data.alarm_count !== 'undefined') {
        $('#stat-alarms').text(data.alarm_count);
        $('#alarm-badge').text(data.alarm_count);
    }
}

// ==================== AI INSIGHTS ====================

function pollInsights() {
    $.ajax({
        url: BASE_URL + 'api/insights',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.insights) {
                renderInsights(data.insights);
                $('#insights-count').text(data.count + ' insight' + (data.count !== 1 ? 's' : ''));
            }
            if (typeof data.ml_available !== 'undefined') {
                updateMlStatus(data.ml_available);
            }
            if (data.forecasts && Object.keys(data.forecasts).length > 0) {
                renderForecastCard(data.forecasts);
            }
        },
        error: function() {
            $('#ai-insights-list').html(
                '<p class="text-muted text-center p-3 mb-0"><i class="fas fa-exclamation-circle mr-1"></i>Unable to load insights</p>'
            );
        }
    });
}

function renderInsights(insights) {
    if (!insights || insights.length === 0) {
        $('#ai-insights-list').html(
            '<p class="text-muted text-center p-3 mb-0">No insights available</p>'
        );
        return;
    }

    var html = '';
    insights.forEach(function(insight) {
        var pulseClass = (insight.severity === 'critical') ? ' critical-pulse' : '';
        var mlBadge = (insight.source === 'ml') ? '<span class="ml-badge">ML</span>' : '';

        html += '<div class="ai-insight-item' + pulseClass + '">'
            + '<div class="ai-insight-icon ' + escapeHtml(insight.severity) + '">'
            + '<i class="' + escapeHtml(insight.icon) + '"></i>'
            + '</div>'
            + '<div class="ai-insight-content">'
            + '<div class="ai-insight-message">' + escapeHtml(insight.message) + '</div>'
            + '<div class="ai-insight-meta">'
            + '<span class="ai-insight-category">' + escapeHtml(insight.category) + '</span>'
            + mlBadge
            + '<span><i class="far fa-clock mr-1"></i>' + escapeHtml(insight.time) + '</span>'
            + '</div>'
            + '</div>'
            + '</div>';
    });

    $('#ai-insights-list').html(html);
}

// ==================== ML STATUS ====================

function updateMlStatus(available) {
    var $b = $('#ml-status-badge');
    if ($b.length === 0) return;
    if (available) {
        $b.removeClass('badge-secondary').addClass('badge-success')
          .html('<i class="fas fa-robot mr-1"></i>ML Active');
    } else {
        $b.removeClass('badge-success').addClass('badge-secondary')
          .html('<i class="fas fa-robot mr-1"></i>ML Offline');
    }
}

// ==================== FORECAST CARD ====================

function renderForecastCard(forecasts) {
    var $card = $('#forecast-card-body');
    if ($card.length === 0) return;

    var sensorInfo = {
        'temperature': { label: 'Temp', unit: '°C', icon: 'fas fa-thermometer-half', color: '#ef4444' },
        'humidity':    { label: 'Humidity', unit: '%', icon: 'fas fa-tint', color: '#6366f1' },
        'gas':         { label: 'Gas', unit: 'pm2.5', icon: 'fas fa-cloud', color: '#f59e0b' },
        'pm25':        { label: 'PM2.5', unit: 'μg/m³', icon: 'fas fa-smog', color: '#10b981' }
    };

    var html = '<div class="forecast-header-row"><div class="forecast-sensor"></div>'
        + '<div class="forecast-values">'
        + '<div class="forecast-cell-hdr">30min</div>'
        + '<div class="forecast-cell-hdr">1hr</div>'
        + '<div class="forecast-cell-hdr">2hr</div>'
        + '</div><div class="forecast-trend"></div></div>';

    $.each(forecasts, function(sensorType, data) {
        if (!data.predictions || data.predictions.length === 0) return;
        var info = sensorInfo[sensorType] || { label: sensorType, unit: '', icon: 'fas fa-chart-line', color: '#64748b' };

        var trendIcon = data.trend_per_hour > 0.5 ? 'fa-arrow-up' :
                        data.trend_per_hour < -0.5 ? 'fa-arrow-down' : 'fa-minus';
        var trendColor = data.trend_per_hour > 0.5 ? '#ef4444' :
                         data.trend_per_hour < -0.5 ? '#22c55e' : '#94a3b8';

        html += '<div class="forecast-row">';
        html += '<div class="forecast-sensor"><i class="' + info.icon + '" style="color:' + info.color + '"></i> ' + info.label + '</div>';
        html += '<div class="forecast-values">';

        data.predictions.forEach(function(p) {
            html += '<div class="forecast-cell">'
                + '<div class="forecast-value">' + p.predicted_value.toFixed(1) + '</div>'
                + '<div class="forecast-range">' + p.confidence_lower.toFixed(1) + '-' + p.confidence_upper.toFixed(1) + '</div>'
                + '</div>';
        });

        html += '</div>';
        html += '<div class="forecast-trend"><i class="fas ' + trendIcon + '" style="color:' + trendColor + '"></i></div>';
        html += '</div>';
    });

    $card.html(html);
    $('#forecast-card').show();
}

// ==================== UTILITY ====================

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
