/**
 * iBreathe SCADA - Data Analytics Page
 */

var distributionChart = null;
var hourlyChart = null;

$(document).ready(function() {
    loadAnalytics();
});

function loadAnalytics() {
    var deviceId = $('#analytics-device').val();
    var hours = $('#analytics-range').val();
    var from = moment().subtract(hours, 'hours').format('YYYY-MM-DD HH:mm:ss');
    var to = moment().format('YYYY-MM-DD HH:mm:ss');

    $('#analytics-loading').show();
    $('#analytics-stats, #analytics-charts, #analytics-bottom').hide();

    $.getJSON(BASE_URL + 'api/analytics?device_id=' + deviceId + '&from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to), function(resp) {
        $('#analytics-loading').hide();
        $('#analytics-stats, #analytics-charts, #analytics-bottom').show();

        renderStatCards(resp.analytics);
        renderDistributionChart(resp.analytics);
        renderHourlyChart(resp.analytics);
        renderCorrelations(resp.correlations);
        loadAnalyticsInsights(deviceId, from, to);
    }).fail(function() {
        $('#analytics-loading').hide();
        toastr.error('Failed to load analytics data');
    });
}

function renderStatCards(analytics) {
    var sensorMeta = {
        'temperature': { label: 'Temperature', unit: '°C', icon: 'fa-thermometer-half', color: '#ef4444', bg: '#fef2f2' },
        'humidity':    { label: 'Humidity', unit: '%', icon: 'fa-tint', color: '#6366f1', bg: '#eef2ff' },
        'gas':         { label: 'PM2.5', unit: 'µg/m³', icon: 'fa-smog', color: '#10b981', bg: '#ecfdf5' },
        'co':          { label: 'CO', unit: 'ppm', icon: 'fa-skull-crossbones', color: '#f59e0b', bg: '#fffbeb' }
    };

    $.each(sensorMeta, function(key, meta) {
        var $card = $('#card-' + key);
        if (!analytics[key]) {
            $card.html('<div class="card scada-card text-center p-3"><small class="text-muted">' + meta.label + '</small><p class="text-muted mb-0" style="font-size:12px">No data</p></div>');
            return;
        }
        var s = analytics[key];
        $card.html(
            '<div class="card analytics-stat-card" style="border-left:4px solid ' + meta.color + '; background:' + meta.bg + '">' +
            '<div class="card-body p-3">' +
            '<div class="d-flex justify-content-between align-items-center mb-2">' +
            '<span style="font-weight:700; color:' + meta.color + '; font-size:13px"><i class="fas ' + meta.icon + ' mr-1"></i>' + meta.label + '</span>' +
            '<span class="text-muted" style="font-size:11px">' + s.count + ' pts</span>' +
            '</div>' +
            '<div class="row text-center" style="font-size:11px">' +
            '<div class="col-3"><div class="text-muted">Min</div><div style="font-weight:700; color:#3b82f6">' + s.min + '</div></div>' +
            '<div class="col-3"><div class="text-muted">Max</div><div style="font-weight:700; color:#ef4444">' + s.max + '</div></div>' +
            '<div class="col-3"><div class="text-muted">Mean</div><div style="font-weight:700; color:#22c55e">' + s.mean + '</div></div>' +
            '<div class="col-3"><div class="text-muted">Median</div><div style="font-weight:700; color:#8b5cf6">' + s.median + '</div></div>' +
            '</div>' +
            '<div class="row text-center mt-2" style="font-size:10px">' +
            '<div class="col-4"><span class="text-muted">Std Dev</span><br><strong>' + s.std_dev + '</strong></div>' +
            '<div class="col-4"><span class="text-muted">P25</span><br><strong>' + s.p25 + '</strong></div>' +
            '<div class="col-4"><span class="text-muted">P75</span><br><strong>' + s.p75 + '</strong></div>' +
            '</div>' +
            '</div></div>'
        );
    });
}

function renderDistributionChart(analytics) {
    if (distributionChart) distributionChart.destroy();

    // Pick first available sensor with distribution data
    var sensor = null;
    var order = ['temperature', 'humidity', 'gas', 'co'];
    for (var i = 0; i < order.length; i++) {
        if (analytics[order[i]] && analytics[order[i]].distribution && analytics[order[i]].distribution.length > 0) {
            sensor = order[i];
            break;
        }
    }
    if (!sensor) return;

    var dist = analytics[sensor].distribution;
    var labels = dist.map(function(b) { return b.label; });
    var counts = dist.map(function(b) { return b.count; });
    var colors = { temperature: '#ef4444', humidity: '#6366f1', gas: '#10b981', co: '#f59e0b'};

    distributionChart = new Chart($('#distribution-chart')[0].getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: sensor.charAt(0).toUpperCase() + sensor.slice(1) + ' Distribution',
                backgroundColor: colors[sensor] + '88',
                borderColor: colors[sensor],
                borderWidth: 1,
                data: counts
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#64748b' } } },
            scales: {
                x: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 9 }, maxRotation: 45 } },
                y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' }, beginAtZero: true }
            }
        }
    });
}

function renderHourlyChart(analytics) {
    if (hourlyChart) hourlyChart.destroy();

    var colors = { temperature: '#ef4444', humidity: '#6366f1', gas: '#10b981', co: '#f59e0b'};
    var datasets = [];
    var labels = [];
    for (var h = 0; h < 24; h++) { labels.push(h + ':00'); }

    $.each(analytics, function(sensor, data) {
        if (!data.hourly_avg) return;
        var vals = [];
        for (var h = 0; h < 24; h++) {
            vals.push(data.hourly_avg[h]);
        }
        datasets.push({
            label: sensor.charAt(0).toUpperCase() + sensor.slice(1),
            borderColor: colors[sensor] || '#64748b',
            backgroundColor: (colors[sensor] || '#64748b') + '22',
            data: vals,
            fill: false,
            tension: 0.3,
            pointRadius: 3,
            yAxisID: (sensor === 'gas' || sensor === 'co') ? 'y2' : 'y'
        });
    });

    if (datasets.length === 0) return;

    var scales = {
        x: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 10 } } },
        y: { position: 'left', grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' } }
    };

    // Add secondary axis if CO₂ is present
    var hasCo2 = datasets.some(function(d) { return d.yAxisID === 'y2'; });
    if (hasCo2) {
        scales.y2 = { position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#f59e0b' } };
    }

    hourlyChart = new Chart($('#hourly-chart')[0].getContext('2d'), {
        type: 'line',
        data: { labels: labels, datasets: datasets },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#64748b' } } },
            scales: scales
        }
    });
}

function renderCorrelations(correlations) {
    if (!correlations || correlations.length === 0) {
        $('#correlation-body').html('<p class="text-muted text-center p-3 mb-0">Insufficient data for correlation analysis</p>');
        return;
    }

    var html = '<table class="table table-sm table-borderless" style="font-size:13px">';
    html += '<thead><tr><th>Sensor Pair</th><th class="text-center">r</th><th class="text-center">Strength</th></tr></thead><tbody>';

    correlations.forEach(function(c) {
        var absR = Math.abs(c.r);
        var strength, color;
        if (absR > 0.7) { strength = 'Strong'; color = '#ef4444'; }
        else if (absR > 0.4) { strength = 'Moderate'; color = '#f59e0b'; }
        else { strength = 'Weak'; color = '#94a3b8'; }

        var direction = c.r > 0 ? '(+)' : '(-)';
        var barWidth = Math.round(absR * 100);

        html += '<tr>'
            + '<td style="font-weight:600">' + escapeHtml(c.pair) + '</td>'
            + '<td class="text-center">' + c.r + ' ' + direction + '</td>'
            + '<td class="text-center">'
            + '<div style="display:inline-block; width:60px; background:#f1f5f9; border-radius:4px; height:8px; vertical-align:middle">'
            + '<div style="width:' + barWidth + '%; background:' + color + '; height:100%; border-radius:4px"></div>'
            + '</div>'
            + ' <span style="color:' + color + '; font-weight:600; font-size:11px">' + strength + '</span>'
            + '</td></tr>';
    });

    html += '</tbody></table>';
    $('#correlation-body').html(html);
}

function loadAnalyticsInsights(deviceId, from, to) {
    var sensors = ['temperature', 'humidity', 'gas', 'co'];
    var allInsights = [];
    var done = 0;

    sensors.forEach(function(sensor) {
        $.getJSON(BASE_URL + 'api/report_insights?device_id=' + deviceId + '&sensor_type=' + sensor + '&from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to), function(resp) {
            if (resp.insights) {
                // Keep only warning/critical/good insights, skip info to reduce noise
                resp.insights.forEach(function(i) {
                    if (i.severity !== 'info') {
                        allInsights.push(i);
                    }
                });
            }
        }).always(function() {
            done++;
            if (done === sensors.length) {
                // Sort by severity
                var order = { critical: 0, warning: 1, good: 2, info: 3 };
                allInsights.sort(function(a, b) {
                    return (order[a.severity] || 4) - (order[b.severity] || 4);
                });
                allInsights = allInsights.slice(0, 10);
                renderAnalyticsInsights(allInsights);
            }
        });
    });
}

function renderAnalyticsInsights(insights) {
    if (!insights || insights.length === 0) {
        $('#analytics-insights-list').html('<div class="text-center p-3"><i class="fas fa-check-circle mr-1" style="color:#16a34a"></i><span class="text-muted" style="font-size:13px">All sensors within normal range</span></div>');
        $('#analytics-interp-count').text('0');
        return;
    }

    var html = '';
    insights.forEach(function(insight) {
        var pulseClass = (insight.severity === 'critical') ? ' critical-pulse' : '';
        var mlBadge = (insight.source === 'ml') ? ' <span class="ml-badge">ML</span>' : '';

        html += '<div class="ai-insight-item' + pulseClass + '">'
            + '<div class="ai-insight-icon ' + escapeHtml(insight.severity) + '">'
            + '<i class="' + escapeHtml(insight.icon) + '"></i>'
            + '</div>'
            + '<div class="ai-insight-content">'
            + '<div class="ai-insight-message">' + escapeHtml(insight.message) + '</div>'
            + '<div class="ai-insight-meta">'
            + '<span class="ai-insight-category">' + escapeHtml(insight.category) + '</span>'
            + mlBadge
            + '</div></div></div>';
    });

    $('#analytics-insights-list').html(html);
    $('#analytics-interp-count').text(insights.length + ' insight' + (insights.length !== 1 ? 's' : ''));
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
