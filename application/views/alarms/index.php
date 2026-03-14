<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-bell mr-2"></i>Active Alarms <span class="badge badge-danger"><?= count($events) ?></span></h1></div>
            <div class="col-sm-6">
                <?php if (!empty($events)): ?>
                <a href="<?= base_url('alarms/acknowledge_all') ?>" class="btn btn-outline-success float-sm-right" onclick="return confirm('Acknowledge all alarms?')"><i class="fas fa-check-double mr-1"></i>Acknowledge All</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= $this->session->flashdata('success') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
        <?php endif; ?>

        <?php if (empty($events)): ?>
        <div class="card scada-card">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle text-success" style="font-size:48px"></i>
                <h4 class="mt-3" style="color:rgba(255,255,255,0.7)">No Active Alarms</h4>
                <p class="text-muted">All systems are operating within normal parameters.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="card scada-card">
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr><th>Severity</th><th>Device</th><th>Sensor</th><th>Value</th><th>Message</th><th>Triggered</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $e): ?>
                        <tr class="<?= $e->severity === 'critical' ? 'table-danger' : ($e->severity === 'warning' ? 'table-warning' : '') ?>" style="<?= $e->severity === 'critical' ? 'background:rgba(220,53,69,0.15)!important' : ($e->severity === 'warning' ? 'background:rgba(255,193,7,0.1)!important' : '') ?>">
                            <td>
                                <?php if ($e->severity === 'critical'): ?><span class="badge badge-danger">CRITICAL</span>
                                <?php elseif ($e->severity === 'warning'): ?><span class="badge badge-warning text-dark">WARNING</span>
                                <?php else: ?><span class="badge badge-info">INFO</span><?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($e->device_name) ?></td>
                            <td><?= ucfirst($e->sensor_type) ?></td>
                            <td><strong><?= $e->value ?></strong></td>
                            <td><?= htmlspecialchars($e->message) ?></td>
                            <td style="font-size:12px"><?= date('M j, H:i:s', strtotime($e->triggered_at)) ?></td>
                            <td><a href="<?= base_url('alarms/acknowledge/' . $e->id) ?>" class="btn btn-sm btn-outline-success" title="Acknowledge"><i class="fas fa-check"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
