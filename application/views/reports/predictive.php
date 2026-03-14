<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-crystal-ball mr-2"></i>Predictive Analysis</h1></div>
            <div class="col-sm-6 text-right">
                <span class="badge" id="pred-ml-badge" style="font-size:12px"><i class="fas fa-robot mr-1"></i>Checking ML...</span>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Controls -->
        <div class="card scada-card mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label style="font-size:13px; font-weight:600; color:#475569; margin-bottom:4px;">Device</label>
                        <select class="form-control form-control-sm" id="pred-device">
                            <?php foreach ($devices as $d): ?>
                            <option value="<?= $d->id ?>"><?= htmlspecialchars($d->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-scada btn-sm" onclick="loadPredictive()"><i class="fas fa-sync mr-1"></i>Predict</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="pred-loading" class="text-center p-5" style="display:none">
            <i class="fas fa-spinner fa-spin" style="color:#6366f1; font-size:28px"></i>
            <p class="text-muted mt-2" style="font-size:14px">Running predictive models...</p>
        </div>

        <!-- Risk Assessment Cards -->
        <div class="row" id="pred-risk-cards" style="display:none">
            <div class="col-md-3" id="risk-temperature"></div>
            <div class="col-md-3" id="risk-humidity"></div>
            <div class="col-md-3" id="risk-pm25"></div>
            <div class="col-md-3" id="risk-co"></div>
        </div>

        <!-- Forecast Chart + Anomaly -->
        <div class="row mt-3" id="pred-charts" style="display:none">
            <div class="col-lg-8">
                <div class="card scada-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-2" style="color:#6366f1"></i>Forecast (Next 2 Hours)</h3>
                        <div class="btn-group btn-group-sm" id="forecast-sensor-btns">
                            <button class="btn btn-outline-secondary btn-xs active" data-sensor="temperature" style="font-size:10px; padding:2px 8px;">Temp</button>
                            <button class="btn btn-outline-secondary btn-xs" data-sensor="humidity" style="font-size:10px; padding:2px 8px;">Humidity</button>
                            <button class="btn btn-outline-secondary btn-xs" data-sensor="pm25" style="font-size:10px; padding:2px 8px;">PM2.5</button>
                            <button class="btn btn-outline-secondary btn-xs" data-sensor="co" style="font-size:10px; padding:2px 8px;">CO</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position:relative; height:320px"><canvas id="forecast-chart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Anomaly Status -->
                <div class="card scada-card mb-3">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-search mr-2" style="color:#f59e0b"></i>Anomaly Detection</h3></div>
                    <div class="card-body p-0" id="anomaly-list" style="max-height:200px; overflow-y:auto">
                        <p class="text-muted text-center p-3 mb-0">No anomaly data</p>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="card scada-card ai-insights-card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-lightbulb mr-2" style="color:#22c55e"></i>Recommendations</h3></div>
                    <div class="card-body p-0" id="recommendations-list" style="max-height:250px; overflow-y:auto">
                        <p class="text-muted text-center p-3 mb-0">No recommendations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
