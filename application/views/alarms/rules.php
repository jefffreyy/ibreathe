<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-cog mr-2"></i>Alarm Rules</h1></div>
            <div class="col-sm-6">
                <?php if ($this->session->userdata('role') === 'admin'): ?>
                <button class="btn btn-scada float-sm-right" data-toggle="modal" data-target="#ruleModal" onclick="resetForm()"><i class="fas fa-plus mr-1"></i>Add Rule</button>
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

        <div class="card scada-card">
            <div class="card-body">
                <table id="rules-table" class="table table-sm table-hover" style="width:100%">
                    <thead>
                        <tr><th>Name</th><th>Device</th><th>Sensor</th><th>Condition</th><th>Threshold</th><th>Severity</th><th>Active</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rules as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r->name) ?></td>
                            <td><?= $r->device_name ?: '<span class="text-muted">All Devices</span>' ?></td>
                            <td><?= ucfirst($r->sensor_type) ?></td>
                            <td><?= ucfirst($r->condition_type) ?></td>
                            <td><strong><?= $r->threshold ?></strong></td>
                            <td>
                                <?php if ($r->severity === 'critical'): ?><span class="badge badge-danger">Critical</span>
                                <?php elseif ($r->severity === 'warning'): ?><span class="badge badge-warning text-dark">Warning</span>
                                <?php else: ?><span class="badge badge-info">Info</span><?php endif; ?>
                            </td>
                            <td><?= $r->is_active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>' ?></td>
                            <td>
                                <?php if ($this->session->userdata('role') === 'admin'): ?>
                                <button class="btn btn-sm btn-outline-warning" onclick='editRule(<?= json_encode($r) ?>)'><i class="fas fa-edit"></i></button>
                                <a href="<?= base_url('alarms/delete_rule/' . $r->id) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this rule?')"><i class="fas fa-trash"></i></a>
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

<!-- Rule Modal -->
<div class="modal fade" id="ruleModal" tabindex="-1" role="dialog" aria-labelledby="ruleModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ruleModalTitle">Add Alarm Rule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= base_url('alarms/save_rule') ?>">
                <div class="modal-body">
                    <input type="hidden" name="rule_id" id="rule_id">
                    <div class="mb-3">
                        <label class="">Rule Name</label>
                        <input type="text" class="form-control" name="name" id="rule_name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="">Device</label>
                            <select class="form-control" name="device_id" id="rule_device">
                                <option value="">All Devices</option>
                                <?php foreach ($devices as $d): ?>
                                <option value="<?= $d->id ?>"><?= htmlspecialchars($d->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="">Sensor Type</label>
                            <select class="form-control" name="sensor_type" id="rule_sensor" required>
                                <option value="temperature">Temperature</option>
                                <option value="humidity">Humidity</option>
                                <option value="pm2.5">PM2.5</option>
                                <option value="co">CO (Carbon Monoxide)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="">Condition</label>
                            <select class="form-control" name="condition_type" id="rule_condition">
                                <option value="above">Above</option>
                                <option value="below">Below</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="">Threshold</label>
                            <input type="number" step="0.01" class="form-control" name="threshold" id="rule_threshold" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="">Severity</label>
                            <select class="form-control" name="severity" id="rule_severity">
                                <option value="info">Info</option>
                                <option value="warning">Warning</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="">Alert Message</label>
                        <input type="text" class="form-control" name="message" id="rule_message">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="">Cooldown (min)</label>
                            <input type="number" class="form-control" name="cooldown_minutes" id="rule_cooldown" value="5">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="rule_active" value="1" checked>
                                <label class="form-check-label" for="rule_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-scada">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function() { 
    $('#rules-table').DataTable({ 
        responsive: true, 
        order: [[5, 'desc']] 
    }); 
});

function resetForm() {
    $('#ruleModalTitle').text('Add Alarm Rule');
    $('#rule_id').val('');
    $('#ruleModal form')[0].reset();
    $('#rule_active').prop('checked', true);
}

function editRule(r) {
    $('#ruleModalTitle').text('Edit Alarm Rule');
    $('#rule_id').val(r.id);
    $('#rule_name').val(r.name);
    $('#rule_device').val(r.device_id || '');
    $('#rule_sensor').val(r.sensor_type);
    $('#rule_condition').val(r.condition_type);
    $('#rule_threshold').val(r.threshold);
    $('#rule_severity').val(r.severity);
    $('#rule_message').val(r.message);
    $('#rule_cooldown').val(r.cooldown_minutes);
    $('#rule_active').prop('checked', r.is_active == 1);
    
    // Bootstrap 4 modal show
    $('#ruleModal').modal('show');
}
</script>