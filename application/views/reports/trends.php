<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-chart-line mr-2"></i>Trends</h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Controls -->
        <div class="card scada-card mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label style="font-size:13px; font-weight:600; color:#475569; margin-bottom:4px;">Device</label>
                        <select class="form-control form-control-sm" id="trend-device">
                            <?php foreach ($devices as $d): ?>
                            <option value="<?= $d->id ?>"><?= htmlspecialchars($d->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label style="font-size:13px; font-weight:600; color:#475569; margin-bottom:4px;">Sensor</label>
                        <select class="form-control form-control-sm" id="trend-sensor">
                            <option value="temperature">Temperature</option>
                            <option value="humidity">Humidity</option>
                            <option value="gas">PM2.5</option>
                            <option value="co">CO (Carbon Monoxide)</option>
                            
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label style="font-size:13px; font-weight:600; color:#475569; margin-bottom:4px;">Time Range</label>
                        <select class="form-control form-control-sm" id="trend-range">
                            <option value="1">Last 1 Hour</option>
                            <option value="6">Last 6 Hours</option>
                            <option value="24" selected>Last 24 Hours</option>
                            <option value="168">Last 7 Days</option>
                            <option value="720">Last 30 Days</option>
                            <option value="1440">Last 2 Months</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-scada btn-sm" onclick="loadTrend()"><i class="fas fa-sync mr-1"></i>Update</button>
                        <button class="btn btn-outline-success btn-sm ml-1" onclick="exportExcel()"><i class="fas fa-file-excel mr-1"></i>Excel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card scada-card">
            <div class="card-body">
                <canvas id="trend-main-chart" height="350"></canvas>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row" id="stats-row">
            <div class="col-md-3">
                <div class="card scada-card text-center p-3">
                    <small class="text-muted">Minimum</small>
                    <h3 id="stat-min" style="color:#3b82f6">--</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card scada-card text-center p-3">
                    <small class="text-muted">Maximum</small>
                    <h3 id="stat-max" style="color:#ef4444">--</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card scada-card text-center p-3">
                    <small class="text-muted">Average</small>
                    <h3 id="stat-avg" style="color:#22c55e">--</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card scada-card text-center p-3">
                    <small class="text-muted">Data Points</small>
                    <h3 id="stat-count" style="color:#eab308">--</h3>
                </div>
            </div>
        </div>

        <!-- AI Interpretation -->
        <div class="card scada-card ai-insights-card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-brain mr-2" style="color:#6366f1"></i>AI Interpretation</h3>
                <span class="badge badge-info" id="interp-count" style="font-size:11px">--</span>
            </div>
            <div class="card-body p-0">
                <div id="ai-interpretation-list" class="ai-insights-list">
                    <div class="text-center p-4">
                        <i class="fas fa-spinner fa-spin" style="color:#6366f1; font-size:20px"></i>
                        <p class="text-muted mt-2 mb-0" style="font-size:13px">Analyzing trend data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
var trendMainChart = null;

$(document).ready(function() { loadTrend(); });

function loadTrend() {
    var deviceId = $('#trend-device').val();
    var sensor = $('#trend-sensor').val();
    var hours = $('#trend-range').val();
    var from = moment().subtract(hours, 'hours').format('YYYY-MM-DD HH:mm:ss');
    var to = moment().format('YYYY-MM-DD HH:mm:ss');

    $.getJSON(BASE_URL + 'api/data/history/' + deviceId + '?sensor_type=' + sensor + '&from=' + from + '&to=' + to, function(resp) {
        var labels = [], values = [];
        var min = Infinity, max = -Infinity, sum = 0;

        if (resp.data) {
            resp.data.forEach(function(d) {
                labels.push(d.time_bucket);
                var v = parseFloat(d.avg_value);
                values.push(v);
                if (v < min) min = v;
                if (v > max) max = v;
                sum += v;
            });
        }

        // Stats
        var count = values.length;
        $('#stat-min').text(count ? min.toFixed(1) : '--');
        $('#stat-max').text(count ? max.toFixed(1) : '--');
        $('#stat-avg').text(count ? (sum / count).toFixed(1) : '--');
        $('#stat-count').text(count);

        // Load AI interpretation
        loadInterpretation(deviceId, sensor, from, to);

        // Chart
        if (trendMainChart) trendMainChart.destroy();

        var colors = { temperature: '#ef4444', humidity: '#3b82f6', gas: '#10b981', co: '#f59e0b'};
        var units = { temperature: '°C', humidity: '%', gas: 'µg/m³', co: 'ppm'};

        trendMainChart = new Chart($('#trend-main-chart')[0].getContext('2d'), {
            type: 'line',
            data: {
                labels: labels.map(function(l) { return l.substring(5); }),
                datasets: [{
                    label: sensor.charAt(0).toUpperCase() + sensor.slice(1) + ' (' + units[sensor] + ')',
                    borderColor: colors[sensor],
                    backgroundColor: colors[sensor] + '22',
                    data: values,
                    fill: true,
                    tension: 0.3,
                    pointRadius: hours <= 6 ? 2 : 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#64748b' } } },
                scales: {
                    x: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', maxTicksLimit: 15, font: { size: 10 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    });
}

function loadInterpretation(deviceId, sensor, from, to) {
    $('#ai-interpretation-list').html(
        '<div class="text-center p-4">' +
        '<i class="fas fa-spinner fa-spin" style="color:#6366f1; font-size:20px"></i>' +
        '<p class="text-muted mt-2 mb-0" style="font-size:13px">Analyzing trend data...</p>' +
        '</div>'
    );

    $.getJSON(BASE_URL + 'api/report_insights?device_id=' + deviceId + '&sensor_type=' + sensor + '&from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to), function(resp) {
        if (resp.insights) {
            renderInterpretation(resp.insights, resp.ml_available);
            var cnt = resp.count || resp.insights.length;
            $('#interp-count').text(cnt + ' insight' + (cnt !== 1 ? 's' : ''));
        }
    }).fail(function() {
        $('#ai-interpretation-list').html(
            '<p class="text-muted text-center p-3 mb-0"><i class="fas fa-exclamation-circle mr-1"></i>Unable to load interpretation</p>'
        );
        $('#interp-count').text('--');
    });
}

function renderInterpretation(insights, mlAvailable) {
    if (!insights || insights.length === 0) {
        $('#ai-interpretation-list').html(
            '<div class="text-center p-3"><i class="fas fa-check-circle mr-1" style="color:#16a34a"></i><span class="text-muted" style="font-size:13px">No issues detected</span></div>'
        );
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
            + '</div>'
            + '</div>'
            + '</div>';
    });

    $('#ai-interpretation-list').html(html);
}

function exportExcel() {
    var deviceId = $('#trend-device').val();
    var sensor = $('#trend-sensor').val();
    var hours = $('#trend-range').val();
    var from = moment().subtract(hours, 'hours').format('YYYY-MM-DD');
    var to = moment().format('YYYY-MM-DD');
    window.location.href = BASE_URL + 'reports/export_excel?device_id=' + deviceId + '&sensor_type=' + sensor + '&from=' + from + '&to=' + to;
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
</script>
