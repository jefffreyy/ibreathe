<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-th-large mr-2"></i>Dashboard</h1>
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

        <!-- Floor Plan + Right Sidebar Row -->
        <div class="row">
            <!-- Left: Floor Plan SVG -->
            <div class="col-lg-8">
                <div class="card scada-card">
                    <div class="card-body p-3">
                        <div class="floorplan-wrapper">
                            <svg id="floorplan-svg" viewBox="0 0 1000 650" preserveAspectRatio="xMidYMid meet">
                                <defs>
                                    <filter id="shadow" x="-5%" y="-5%" width="115%" height="115%">
                                        <feDropShadow dx="0" dy="2" stdDeviation="4" flood-color="#000" flood-opacity="0.08"/>
                                    </filter>
                                    <filter id="glow-green">
                                        <feDropShadow dx="0" dy="0" stdDeviation="3" flood-color="#22c55e" flood-opacity="0.5"/>
                                    </filter>
                                    <filter id="glow-red">
                                        <feDropShadow dx="0" dy="0" stdDeviation="3" flood-color="#ef4444" flood-opacity="0.5"/>
                                    </filter>
                                    <linearGradient id="floor-bg" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#f8fafc"/>
                                        <stop offset="100%" style="stop-color:#f1f5f9"/>
                                    </linearGradient>
                                </defs>

                                <!-- Background -->
                                <rect x="0" y="0" width="1000" height="650" rx="16" fill="url(#floor-bg)" stroke="#e2e8f0" stroke-width="2"/>
                                <text x="500" y="35" text-anchor="middle" font-size="16" font-weight="700" fill="#334155" font-family="Inter, sans-serif">Home Floor Plan — IoT Sensor Monitoring</text>

                                <!-- ============ ROOMS ============ -->
                                <rect x="30" y="55" width="420" height="280" rx="12" fill="#fff" stroke="#cbd5e1" stroke-width="1.5" filter="url(#shadow)"/>
                                <rect x="30" y="55" width="420" height="36" rx="12" fill="#6366f1" opacity="0.08"/>
                                <rect x="30" y="79" width="420" height="12" fill="#6366f1" opacity="0.08"/>
                                <text x="50" y="80" font-size="14" font-weight="700" fill="#6366f1" font-family="Inter, sans-serif"><tspan class="fa-icon" font-family="Font Awesome 5 Free" font-weight="900">&#xf4b8;</tspan>  Living Room</text>

                                <rect x="470" y="55" width="500" height="280" rx="12" fill="#fff" stroke="#cbd5e1" stroke-width="1.5" filter="url(#shadow)"/>
                                <rect x="470" y="55" width="500" height="36" rx="12" fill="#8b5cf6" opacity="0.08"/>
                                <rect x="470" y="79" width="500" height="12" fill="#8b5cf6" opacity="0.08"/>
                                <text x="490" y="80" font-size="14" font-weight="700" fill="#8b5cf6" font-family="Inter, sans-serif"><tspan class="fa-icon" font-family="Font Awesome 5 Free" font-weight="900">&#xf236;</tspan>  Bedroom 1</text>

                                <rect x="30" y="355" width="320" height="260" rx="12" fill="#fff" stroke="#cbd5e1" stroke-width="1.5" filter="url(#shadow)"/>
                                <rect x="30" y="355" width="320" height="36" rx="12" fill="#f59e0b" opacity="0.08"/>
                                <rect x="30" y="379" width="320" height="12" fill="#f59e0b" opacity="0.08"/>
                                <text x="50" y="380" font-size="14" font-weight="700" fill="#f59e0b" font-family="Inter, sans-serif"><tspan class="fa-icon" font-family="Font Awesome 5 Free" font-weight="900">&#xf2e7;</tspan>  Kitchen</text>

                                <rect x="370" y="355" width="280" height="260" rx="12" fill="#fff" stroke="#cbd5e1" stroke-width="1.5" filter="url(#shadow)"/>
                                <rect x="370" y="355" width="280" height="36" rx="12" fill="#06b6d4" opacity="0.08"/>
                                <rect x="370" y="379" width="280" height="12" fill="#06b6d4" opacity="0.08"/>
                                <text x="390" y="380" font-size="14" font-weight="700" fill="#06b6d4" font-family="Inter, sans-serif"><tspan class="fa-icon" font-family="Font Awesome 5 Free" font-weight="900">&#xf2cd;</tspan>  Bedroom 2</text>

                                <rect x="670" y="355" width="300" height="260" rx="12" fill="#fff" stroke="#cbd5e1" stroke-width="1.5" filter="url(#shadow)"/>
                                <rect x="670" y="355" width="300" height="36" rx="12" fill="#64748b" opacity="0.08"/>
                                <rect x="670" y="379" width="300" height="12" fill="#64748b" opacity="0.08"/>
                                <text x="690" y="380" font-size="14" font-weight="700" fill="#64748b" font-family="Inter, sans-serif"><tspan class="fa-icon" font-family="Font Awesome 5 Free" font-weight="900">&#xf52b;</tspan>  Garage</text>

                                <!-- ============ SENSOR DEVICES ============ -->

                                <!-- Device 1: Living Room Sensor -->
                                <g id="device-living-room" class="device-group" transform="translate(60, 110)">
                                    <rect class="device-bg" x="0" y="0" width="360" height="200" rx="10" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1"/>
                                    <circle class="status-led" cx="340" cy="18" r="5" fill="#94a3b8"/>
                                    <text x="16" y="25" font-size="12" font-weight="600" fill="#475569" font-family="Inter, sans-serif" class="device-name">Living Room Sensor</text>
                                    <text x="16" y="42" font-size="10" fill="#94a3b8" font-family="Inter, sans-serif" class="device-status">--</text>
                                    <g transform="translate(16, 55)"><rect width="155" height="62" rx="8" fill="#fef2f2" stroke="#fecaca" stroke-width="0.5"/><text x="12" y="20" font-size="10" fill="#dc2626" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf2c9;</tspan> Temperature</text><text x="12" y="48" font-size="24" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-temp">--</text><text x="78" y="48" font-size="12" fill="#94a3b8" font-family="Inter, sans-serif">°C</text></g>
                                    <g transform="translate(185, 55)"><rect width="155" height="62" rx="8" fill="#eff6ff" stroke="#bfdbfe" stroke-width="0.5"/><text x="12" y="20" font-size="10" fill="#2563eb" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf043;</tspan> Humidity</text><text x="12" y="48" font-size="24" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-hum">--</text><text x="78" y="48" font-size="12" fill="#94a3b8" font-family="Inter, sans-serif">%</text></g>
                                    <g transform="translate(16, 128)"><rect width="328" height="62" rx="8" fill="#fef9e7" stroke="#fde3a7" stroke-width="0.5"/><text x="12" y="20" font-size="10" fill="#e67e22" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf0e7;</tspan> Gas</text><text x="12" y="48" font-size="24" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-gas">0</text><text x="88" y="48" font-size="12" fill="#94a3b8" font-family="Inter, sans-serif">pm2.5</text></g>
                                </g>

                                <!-- Device 2: Bedroom Sensor -->
                                <g id="device-bedroom" class="device-group" transform="translate(500, 110)">
                                    <rect class="device-bg" x="0" y="0" width="440" height="200" rx="10" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1"/>
                                    <circle class="status-led" cx="420" cy="18" r="5" fill="#94a3b8"/>
                                    <text x="16" y="25" font-size="12" font-weight="600" fill="#475569" font-family="Inter, sans-serif" class="device-name">Bedroom 1 Sensor</text>
                                    <text x="16" y="42" font-size="10" fill="#94a3b8" font-family="Inter, sans-serif" class="device-status">--</text>
                                    <g transform="translate(16, 55)"><rect width="198" height="62" rx="8" fill="#fef2f2" stroke="#fecaca" stroke-width="0.5"/><text x="12" y="20" font-size="10" fill="#dc2626" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf2c9;</tspan> Temperature</text><text x="12" y="48" font-size="24" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-temp">--</text><text x="78" y="48" font-size="12" fill="#94a3b8" font-family="Inter, sans-serif">°C</text></g>
                                    <g transform="translate(226, 55)"><rect width="198" height="62" rx="8" fill="#eff6ff" stroke="#bfdbfe" stroke-width="0.5"/><text x="12" y="20" font-size="10" fill="#2563eb" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf043;</tspan> Humidity</text><text x="12" y="48" font-size="24" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-hum">--</text><text x="78" y="48" font-size="12" fill="#94a3b8" font-family="Inter, sans-serif">%</text></g>
                                    <g transform="translate(16, 128)"><rect width="408" height="62" rx="8" fill="#fef9e7" stroke="#fde3a7" stroke-width="0.5"/><text x="12" y="20" font-size="10" fill="#e67e22" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf0e7;</tspan> Gas</text><text x="12" y="48" font-size="24" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-gas">0</text><text x="88" y="48" font-size="12" fill="#94a3b8" font-family="Inter, sans-serif">pm2.5</text></g>
                                </g>

                                <!-- Device 3: Kitchen Sensor -->
                                <g id="device-kitchen" class="device-group" transform="translate(50, 405)">
                                    <rect class="device-bg" x="0" y="0" width="280" height="185" rx="10" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1"/>
                                    <circle class="status-led" cx="260" cy="18" r="5" fill="#94a3b8"/>
                                    <text x="16" y="25" font-size="12" font-weight="600" fill="#475569" font-family="Inter, sans-serif" class="device-name">Kitchen Sensor</text>
                                    <text x="16" y="42" font-size="10" fill="#94a3b8" font-family="Inter, sans-serif" class="device-status">--</text>
                                    <g transform="translate(16, 52)"><rect width="120" height="55" rx="8" fill="#fef2f2" stroke="#fecaca" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#dc2626" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf2c9;</tspan> Temp</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-temp">--</text><text x="60" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">°C</text></g>
                                    <g transform="translate(146, 52)"><rect width="120" height="55" rx="8" fill="#eff6ff" stroke="#bfdbfe" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#2563eb" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf043;</tspan> Humidity</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-hum">--</text><text x="60" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">%</text></g>
                                    <g transform="translate(16, 118)"><rect width="250" height="55" rx="8" fill="#fef9e7" stroke="#fde3a7" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#e67e22" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf0e7;</tspan> Gas</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-gas">0</text><text x="60" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">pm2.5</text></g>
                                </g>

                                <!-- Device 4: Bathroom Sensor -->
                                <g id="device-bathroom" class="device-group" transform="translate(385, 405)">
                                    <rect class="device-bg" x="0" y="0" width="250" height="185" rx="10" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1"/>
                                    <circle class="status-led" cx="230" cy="18" r="5" fill="#94a3b8"/>
                                    <text x="16" y="25" font-size="12" font-weight="600" fill="#475569" font-family="Inter, sans-serif" class="device-name">Bedroom 2 Sensor</text>
                                    <text x="16" y="42" font-size="10" fill="#94a3b8" font-family="Inter, sans-serif" class="device-status">--</text>
                                    <g transform="translate(14, 52)"><rect width="106" height="55" rx="8" fill="#fef2f2" stroke="#fecaca" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#dc2626" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf2c9;</tspan> Temp</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-temp">--</text><text x="55" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">°C</text></g>
                                    <g transform="translate(130, 52)"><rect width="106" height="55" rx="8" fill="#eff6ff" stroke="#bfdbfe" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#2563eb" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf043;</tspan> Humidity</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-hum">--</text><text x="55" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">%</text></g>
                                    <g transform="translate(14, 118)"><rect width="222" height="55" rx="8" fill="#fef9e7" stroke="#fde3a7" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#e67e22" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf0e7;</tspan> Gas</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-gas">0</text><text x="55" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">pm2.5</text></g>
                                </g>

                                <!-- Device 5: Garage Sensor -->
                                <g id="device-garage" class="device-group" transform="translate(685, 405)">
                                    <rect class="device-bg" x="0" y="0" width="270" height="185" rx="10" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1"/>
                                    <circle class="status-led" cx="250" cy="18" r="5" fill="#94a3b8"/>
                                    <text x="16" y="25" font-size="12" font-weight="600" fill="#475569" font-family="Inter, sans-serif" class="device-name">Garage Sensor</text>
                                    <text x="16" y="42" font-size="10" fill="#94a3b8" font-family="Inter, sans-serif" class="device-status">--</text>
                                    <g transform="translate(14, 52)"><rect width="116" height="55" rx="8" fill="#fef2f2" stroke="#fecaca" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#dc2626" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf2c9;</tspan> Temp</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-temp">--</text><text x="55" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">°C</text></g>
                                    <g transform="translate(140, 52)"><rect width="116" height="55" rx="8" fill="#eff6ff" stroke="#bfdbfe" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#2563eb" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf043;</tspan> Humidity</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-hum">--</text><text x="55" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">%</text></g>
                                    <g transform="translate(14, 118)"><rect width="242" height="55" rx="8" fill="#fef9e7" stroke="#fde3a7" stroke-width="0.5"/><text x="8" y="18" font-size="9" fill="#e67e22" font-family="Inter, sans-serif" font-weight="600"><tspan font-family="Font Awesome 5 Free" font-weight="900">&#xf0e7;</tspan> Gas</text><text x="8" y="42" font-size="20" font-weight="800" fill="#1e293b" font-family="Inter, sans-serif" class="val-gas">0</text><text x="55" y="42" font-size="11" fill="#94a3b8" font-family="Inter, sans-serif">pm2.5</text></g>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Forecast + Insights + Device Status + Alarms + Legend -->
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
                <div class="card scada-card ai-insights-card fp-insights-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-brain mr-2" style="color:#6366f1"></i>AI Insights</h3>
                        <div>
                            <div class="btn-group btn-group-sm" id="insights-filter">
                                <button class="btn btn-outline-secondary btn-xs active" data-filter="all" style="font-size:10px; padding:2px 8px;">All</button>
                                <button class="btn btn-outline-secondary btn-xs" data-filter="suggestion" style="font-size:10px; padding:2px 8px;"><i class="fas fa-lightbulb mr-1"></i>Actions</button>
                            </div>
                            <span class="badge badge-info ml-1" id="fp-insights-count" style="font-size:11px">--</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="fp-insights-list" class="fp-insights-scroll">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin" style="color:#6366f1; font-size:20px"></i>
                                <p class="text-muted mt-2 mb-0" style="font-size:13px">Analyzing sensor data...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Device Status -->
                <div class="card scada-card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-server mr-2"></i>Device Status</h3></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Device</th><th>Status</th><th>Last Seen</th></tr></thead>
                                <tbody id="device-status-table">
                                    <?php if (!empty($devices)): foreach ($devices as $d): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($d->name) ?></td>
                                        <td><?php if ($d->status == 'online'): ?><span class="badge badge-success">Online</span><?php elseif ($d->status == 'maintenance'): ?><span class="badge badge-warning">Maint.</span><?php else: ?><span class="badge badge-secondary">Offline</span><?php endif; ?></td>
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
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Recent Alarms</h3></div>
                    <div class="card-body p-0">
                        <div id="recent-alarms-list" class="recent-alarms-list">
                            <p class="text-muted text-center p-3 mb-0">No active alarms</p>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="card scada-card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle mr-2" style="color:#94a3b8"></i>Legend</h3></div>
                    <div class="card-body py-3 px-3">
                        <div class="mb-3">
                            <span style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px">Device Status</span>
                            <div class="d-flex flex-wrap mt-2" style="gap:14px">
                                <span style="font-size:12px; color:#64748b"><i class="fas fa-circle text-success mr-1" style="font-size:8px"></i> Online</span>
                                <span style="font-size:12px; color:#64748b"><i class="fas fa-circle text-danger mr-1" style="font-size:8px"></i> Offline</span>
                                <span style="font-size:12px; color:#64748b"><i class="fas fa-circle text-warning mr-1" style="font-size:8px"></i> Maintenance</span>
                            </div>
                        </div>
                        <div>
                            <span style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px">Sensor Types</span>
                            <div class="d-flex flex-wrap mt-2" style="gap:8px">
                                <span class="badge badge-danger" style="font-size:10px"><i class="fas fa-thermometer-half mr-1"></i>Temperature</span>
                                <span class="badge badge-primary" style="font-size:10px"><i class="fas fa-tint mr-1"></i>Humidity</span>
                                <span class="badge badge-warning" style="font-size:10px; background-color:#e67e22; color:white"><i class="fas fa-cloud mr-1"></i>Gas (pm2.5)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.floorplan-wrapper { width: 100%; margin: 0 auto; }
.floorplan-wrapper svg { width: 100%; height: auto; }
.device-group { transition: opacity 0.3s; }
.device-group:hover .device-bg { stroke: #6366f1; stroke-width: 1.5; }
.device-group .status-led { transition: fill 0.5s; }
@keyframes pulse-led { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
.led-online { fill: #22c55e !important; animation: pulse-led 2s infinite; }
.led-offline { fill: #ef4444 !important; }
.led-maintenance { fill: #f59e0b !important; }
</style>

<script>
var POLLING_INTERVAL = <?= isset($polling_interval) ? $polling_interval : 5000 ?>;
</script>

<script src="assets_scada/js/floorplan.js"></script>


