<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-history mr-2"></i>Alarm History</h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card scada-card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="">Severity</label>
                        <select class="form-control form-control-sm" name="severity">
                            <option value="">All</option>
                            <option value="critical" <?= ($filters['severity']??'') == 'critical' ? 'selected' : '' ?>>Critical</option>
                            <option value="warning" <?= ($filters['severity']??'') == 'warning' ? 'selected' : '' ?>>Warning</option>
                            <option value="info" <?= ($filters['severity']??'') == 'info' ? 'selected' : '' ?>>Info</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="">Device</label>
                        <select class="form-control form-control-sm" name="device_id">
                            <option value="">All</option>
                            <?php foreach ($devices as $d): ?>
                            <option value="<?= $d->id ?>" <?= ($filters['device_id']??'') == $d->id ? 'selected' : '' ?>><?= htmlspecialchars($d->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="">From</label>
                        <input type="date" class="form-control form-control-sm" name="date_from" value="<?= $filters['date_from'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="">To</label>
                        <input type="date" class="form-control form-control-sm" name="date_to" value="<?= $filters['date_to'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-scada btn-sm"><i class="fas fa-filter mr-1"></i>Filter</button>
                        <a href="<?= base_url('alarms/history') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card scada-card">
            <div class="card-body">
                <table id="history-table" class="table table-sm table-hover" style="width:100%">
                    <thead>
                        <tr><th>Time</th><th>Severity</th><th>Device</th><th>Sensor</th><th>Value</th><th>Message</th><th>Acknowledged</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $e): ?>
                        <tr>
                            <td style="font-size:12px"><?= date('M j, H:i:s', strtotime($e->triggered_at)) ?></td>
                            <td>
                                <?php if ($e->severity === 'critical'): ?><span class="badge badge-danger">Critical</span>
                                <?php elseif ($e->severity === 'warning'): ?><span class="badge badge-warning text-dark">Warning</span>
                                <?php else: ?><span class="badge badge-info">Info</span><?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($e->device_name) ?></td>
                            <td><?= ucfirst($e->sensor_type) ?></td>
                            <td><strong><?= $e->value ?></strong></td>
                            <td><?= htmlspecialchars($e->message) ?></td>
                            <td>
                                <?php if ($e->acknowledged): ?>
                                    <span class="text-success"><i class="fas fa-check mr-1"></i><?= htmlspecialchars($e->ack_username ?? '') ?></span>
                                    <br><small class="text-muted"><?= $e->acknowledged_at ? date('M j, H:i', strtotime($e->acknowledged_at)) : '' ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
$(function() { $('#history-table').DataTable({ responsive: true, order: [[0, 'desc']] }); });
</script>
