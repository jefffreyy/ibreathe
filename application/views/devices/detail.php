<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-microchip mr-2"></i><?= htmlspecialchars($device->name) ?></h1></div>
            <div class="col-sm-6">
                <?php if ($this->session->userdata('role') === 'admin'): ?>
                <a href="<?= base_url('devices/edit/' . $device->id) ?>" class="btn btn-outline-warning float-sm-right ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
                <?php endif; ?>
                <a href="<?= base_url('devices') ?>" class="btn btn-outline-secondary float-sm-right"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Device Info -->
            <div class="col-lg-4">
                <div class="card scada-card">
                    <div class="card-header"><h3 class="card-title">Device Info</h3></div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless" style="color:rgba(255,255,255,0.7)">
                            <tr><td class="text-muted">Status</td><td>
                                <?php if ($device->status == 'online'): ?>
                                    <span class="badge badge-success">Online</span>
                                <?php elseif ($device->status == 'maintenance'): ?>
                                    <span class="badge badge-warning text-dark">Maintenance</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Offline</span>
                                <?php endif; ?>
                            </td></tr>
                            <tr><td class="text-muted">Location</td><td><?= htmlspecialchars($device->location) ?></td></tr>
                            <tr><td class="text-muted">Type</td><td><?= htmlspecialchars($device->type) ?></td></tr>
                            <tr><td class="text-muted">Device Key</td><td><code style="font-size:11px"><?= htmlspecialchars($device->device_key) ?></code></td></tr>
                            <tr><td class="text-muted">Last Seen</td><td><?= $device->last_seen ? date('M j, Y H:i:s', strtotime($device->last_seen)) : 'Never' ?></td></tr>
                            <tr><td class="text-muted">IP Address</td><td><?= $device->ip_address ?: 'N/A' ?></td></tr>
                            <tr><td class="text-muted">Created</td><td><?= date('M j, Y', strtotime($device->created_at)) ?></td></tr>
                        </table>
                    </div>
                </div>

                <!-- Send Command -->
                <?php if ($this->session->userdata('role') === 'admin'): ?>
                <div class="card scada-card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-terminal mr-2"></i>Send Command</h3></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <select class="form-control form-control-sm" id="cmd-type">
                                <option value="restart">Restart Device</option>
                                <option value="recalibrate">Recalibrate Sensors</option>
                                <option value="set_interval">Set Report Interval</option>
                                <option value="identify">Identify (Blink LED)</option>
                            </select>
                        </div>
                        <button class="btn btn-scada btn-sm" onclick="sendCommand()"><i class="fas fa-paper-plane mr-1"></i>Send</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Current Readings -->
            <div class="col-lg-8">
                <div class="card scada-card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Current Readings</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            $sensor_icons = array('temperature'=>'fa-thermometer-half text-danger','humidity'=>'fa-tint text-info','co2'=>'fa-cloud text-warning','pm25'=>'fa-smog text-success');
                            $sensor_units = array('temperature'=>'°C','humidity'=>'%','co2'=>'pm2.5','pm25'=>'μg/m³');
                            $readings_map = array();
                            foreach ($latest_readings as $r) { $readings_map[$r->sensor_type] = $r; }
                            foreach (array('temperature','humidity','co2','pm25') as $type):
                                $val = isset($readings_map[$type]) ? $readings_map[$type]->value : '--';
                                $time = isset($readings_map[$type]) ? date('H:i:s', strtotime($readings_map[$type]->recorded_at)) : '--';
                            ?>
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="card bg-light text-center p-3">
                                    <i class="fas <?= $sensor_icons[$type] ?>" style="font-size:24px"></i>
                                    <h3 class="mt-2 mb-0"><?= $val != '--' ? ($type=='co2' ? round($val) : number_format($val,1)) : '--' ?></h3>
                                    <small class="text-muted"><?= $sensor_units[$type] ?></small>
                                    <div style="font-size:11px" class="text-muted mt-1"><?= $time ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- 24h Chart -->
                <div class="card scada-card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Last 24 Hours</h3></div>
                    <div class="card-body">
                        <canvas id="device-chart" height="280"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
var deviceId = <?= $device->id ?>;

$(document).ready(function() { loadDeviceChart(); });

function loadDeviceChart() {
    $.getJSON(BASE_URL + 'api/data/history/' + deviceId + '?sensor_type=temperature&from=' + moment().subtract(24,'hours').format('YYYY-MM-DD HH:mm:ss') + '&to=' + moment().format('YYYY-MM-DD HH:mm:ss'), function(resp) {
        var labels = [], values = [];
        if (resp.data) {
            resp.data.forEach(function(d) {
                labels.push(d.time_bucket.split(' ')[1].substring(0,5));
                values.push(parseFloat(d.avg_value));
            });
        }
        new Chart($('#device-chart')[0].getContext('2d'), {
            type: 'line',
            data: { labels: labels, datasets: [{ label: 'Temperature (°C)', borderColor: '#ef4444', data: values, fill: false, tension: 0.3, pointRadius: 0 }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: 'rgba(255,255,255,0.6)' } } },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.4)', maxTicksLimit: 12 } },
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.4)' } }
                }
            }
        });
    });
}

function sendCommand() {
    var cmd = $('#cmd-type').val();
    $.ajax({
        url: BASE_URL + 'api/commands/' + deviceId,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ command: cmd, parameters: {} }),
        success: function(r) { toastr.success('Command "' + cmd + '" sent to device.'); },
        error: function() { toastr.error('Failed to send command.'); }
    });
}
</script>
