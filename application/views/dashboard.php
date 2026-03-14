<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <span class="text-muted" id="last-update">Last update: --</span>
                    <span class="badge badge-success ml-2" id="connection-status"><i class="fas fa-circle mr-1" style="font-size:8px"></i>Live</span>
                    <span class="badge badge-secondary ml-1" id="ml-status-badge"><i class="fas fa-robot mr-1"></i>ML Offline</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alarm Ticker -->
<div id="alarm-ticker" class="alarm-ticker" style="display:none;">
    <div class="alarm-ticker-content">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <span id="alarm-ticker-text"></span>
    </div>
</div>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">

        <!-- Summary Cards Row -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info-scada">
                    <div class="inner">
                        <h3 id="stat-devices"><?= isset($total_devices) ? $total_devices : 0 ?></h3>
                        <p>Total Devices</p>
                    </div>
                    <div class="icon"><i class="fas fa-microchip"></i></div>
                    <a href="<?= base_url('devices') ?>" class="small-box-footer">Manage Devices <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success-scada">
                    <div class="inner">
                        <h3 id="stat-online">0</h3>
                        <p>Devices Online</p>
                    </div>
                    <div class="icon"><i class="fas fa-wifi"></i></div>
                    <a href="<?= base_url('devices') ?>" class="small-box-footer">View Status <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning-scada">
                    <div class="inner">
                        <h3 id="stat-alarms"><?= isset($active_alarms) ? $active_alarms : 0 ?></h3>
                        <p>Active Alarms</p>
                    </div>
                    <div class="icon"><i class="fas fa-bell"></i></div>
                    <a href="<?= base_url('alarms') ?>" class="small-box-footer">View Alarms <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-aqi-scada" id="aqi-box">
                    <div class="inner">
                        <h3 id="stat-aqi">--</h3>
                        <p id="stat-aqi-label">Air Quality Index</p>
                    </div>
                    <div class="icon"><i class="fas fa-lungs"></i></div>
                    <a href="<?= base_url('reports/trends') ?>" class="small-box-footer">View Trends <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Gauge Widgets Row -->
        <div class="row" id="gauge-row">
            <!-- Temperature Gauge -->
            <div class="col-lg-3 col-md-6">
                <div class="card scada-gauge-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-thermometer-half text-danger mr-2"></i>Temperature</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="gauge-container">
                            <canvas id="gauge-temperature" width="200" height="120"></canvas>
                            <div class="gauge-value" id="val-temperature">--</div>
                            <div class="gauge-unit">°C</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Humidity Gauge -->
            <div class="col-lg-3 col-md-6">
                <div class="card scada-gauge-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tint text-info mr-2"></i>Humidity</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="gauge-container">
                            <canvas id="gauge-humidity" width="200" height="120"></canvas>
                            <div class="gauge-value" id="val-humidity">--</div>
                            <div class="gauge-unit">%</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- CO2 Gauge -->
            <div class="col-lg-3 col-md-6">
                <div class="card scada-gauge-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cloud text-warning mr-2"></i>CO&#8322;</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="gauge-container">
                            <canvas id="gauge-co2" width="200" height="120"></canvas>
                            <div class="gauge-value" id="val-co2">--</div>
                            <div class="gauge-unit">pm2.5</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- PM2.5 Gauge -->
            <div class="col-lg-3 col-md-6">
                <div class="card scada-gauge-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-smog text-success mr-2"></i>PM2.5</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="gauge-container">
                            <canvas id="gauge-pm25" width="200" height="120"></canvas>
                            <div class="gauge-value" id="val-pm25">--</div>
                            <div class="gauge-unit">&mu;g/m&sup3;</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Sparklines & Device Status Row -->
        <div class="row">
            <!-- Sparkline Trends -->
            <div class="col-lg-8">
                <div class="card scada-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-area mr-2"></i>Last Hour Trends</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="trend-chart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <!-- Right Column: Forecast + AI Insights + Device Status + Alarms -->
            <div class="col-lg-4">
                <!-- ML Forecast -->
                <div class="card scada-card" id="forecast-card" style="display:none;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-2" style="color:#8b5cf6"></i>Forecast</h3>
                        <span class="badge ml-badge" style="font-size:10px"><i class="fas fa-robot mr-1"></i>ML Powered</span>
                    </div>
                    <div class="card-body p-2" id="forecast-card-body">
                        <p class="text-muted text-center p-2 mb-0" style="font-size:13px">Loading forecasts...</p>
                    </div>
                </div>

                <!-- AI Insights -->
                <div class="card scada-card ai-insights-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-brain mr-2" style="color:#6366f1"></i>AI Insights</h3>
                        <span class="badge badge-info" id="insights-count" style="font-size:11px">--</span>
                    </div>
                    <div class="card-body p-0">
                        <div id="ai-insights-list" class="ai-insights-list">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin" style="color:#6366f1; font-size:20px"></i>
                                <p class="text-muted mt-2 mb-0" style="font-size:13px">Analyzing sensor data...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Device Status -->
                <div class="card scada-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-server mr-2"></i>Device Status</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr><th>Device</th><th>Status</th><th>Last Seen</th></tr>
                                </thead>
                                <tbody id="device-status-table">
                                    <?php if (!empty($devices)): foreach ($devices as $d): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($d->name) ?></td>
                                        <td>
                                            <?php if ($d->status == 'online'): ?>
                                                <span class="badge badge-success">Online</span>
                                            <?php elseif ($d->status == 'maintenance'): ?>
                                                <span class="badge badge-warning">Maint.</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Offline</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted" style="font-size:12px"><?= $d->last_seen ? date('H:i:s', strtotime($d->last_seen)) : 'Never' ?></td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr><td colspan="3" class="text-center text-muted">No devices registered</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Alarms -->
                <div class="card scada-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Recent Alarms</h3>
                    </div>
                    <div class="card-body p-0">
                        <div id="recent-alarms-list" class="recent-alarms-list">
                            <p class="text-muted text-center p-3 mb-0">No active alarms</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
var POLLING_INTERVAL = <?= isset($polling_interval) ? $polling_interval : 5000 ?>;
</script>
<script src="<?= base_url('assets_scada/js/dashboard.js') ?>"></script>
