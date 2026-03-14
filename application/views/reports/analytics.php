<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-chart-pie mr-2"></i>Data Analytics</h1></div>
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
                        <select class="form-control form-control-sm" id="analytics-device">
                            <?php foreach ($devices as $d): ?>
                            <option value="<?= $d->id ?>"><?= htmlspecialchars($d->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label style="font-size:13px; font-weight:600; color:#475569; margin-bottom:4px;">Time Range</label>
                        <select class="form-control form-control-sm" id="analytics-range">
                            <option value="24">Last 24 Hours</option>
                            <option value="168" selected>Last 7 Days</option>
                            <option value="720">Last 30 Days</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-scada btn-sm" onclick="loadAnalytics()"><i class="fas fa-sync mr-1"></i>Analyze</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="analytics-loading" class="text-center p-5" style="display:none">
            <i class="fas fa-spinner fa-spin" style="color:#6366f1; font-size:28px"></i>
            <p class="text-muted mt-2" style="font-size:14px">Analyzing sensor data...</p>
        </div>

        <!-- Stats Cards Row -->
        <div class="row" id="analytics-stats" style="display:none">
            <div class="col-md-3" id="card-temperature"></div>
            <div class="col-md-3" id="card-humidity"></div>
            <div class="col-md-3" id="card-pm25"></div>
            <div class="col-md-3" id="card-co"></div>
        </div>

        <!-- Charts Row -->
        <div class="row mt-3" id="analytics-charts" style="display:none">
            <div class="col-lg-12">
                <div class="card scada-card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar mr-2" style="color:#6366f1"></i>Value Distribution</h3></div>
                    <div class="card-body">
                        <div style="position:relative; height:300px"><canvas id="distribution-chart"></canvas></div>
                    </div>
                </div>
            </div>
            <!--<div class="col-lg-6">-->
            <!--    <div class="card scada-card">-->
            <!--        <div class="card-header"><h3 class="card-title"><i class="fas fa-clock mr-2" style="color:#f59e0b"></i>Hourly Pattern (Time of Day)</h3></div>-->
            <!--        <div class="card-body">-->
            <!--            <div style="position:relative; height:300px"><canvas id="hourly-chart"></canvas></div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        </div>

        <!-- Correlation + Insights Row -->
        <div class="row mt-3" id="analytics-bottom" style="display:none">
            <div class="col-lg-6">
                <div class="card scada-card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-project-diagram mr-2" style="color:#10b981"></i>Sensor Correlations</h3></div>
                    <div class="card-body" id="correlation-body">
                        <p class="text-muted text-center">No correlation data</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card scada-card ai-insights-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-brain mr-2" style="color:#6366f1"></i>AI Interpretation</h3>
                        <span class="badge badge-info" id="analytics-interp-count" style="font-size:11px">--</span>
                    </div>
                    <div class="card-body p-0">
                        <div id="analytics-insights-list" class="ai-insights-list" style="max-height:350px; overflow-y:auto">
                            <p class="text-muted text-center p-3 mb-0">Select a device and click Analyze</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
