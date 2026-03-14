<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-plus-circle mr-2"></i><?= isset($device) ? 'Edit Device' : 'Add New Device' ?></h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card scada-card" style="max-width:600px">
            <div class="card-body">
                <?php if (validation_errors()): ?>
                <div class="alert alert-danger"><?= validation_errors() ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= isset($device) ? base_url('devices/edit/' . $device->id) : base_url('devices/create') ?>">
                    <div class="mb-3">
                        <label class="">Device Name</label>
                        <input type="text" class="form-control" name="name" value="<?= isset($device) ? htmlspecialchars($device->name) : set_value('name') ?>" required placeholder="e.g., Living Room Sensor">
                    </div>
                    <div class="mb-3">
                        <label class="">Location</label>
                        <input type="text" class="form-control" name="location" value="<?= isset($device) ? htmlspecialchars($device->location) : set_value('location') ?>" required placeholder="e.g., Living Room">
                    </div>
                    <div class="mb-3">
                        <label class="">Type</label>
                        <select class="form-control" name="type">
                            <option value="air_quality_monitor" <?= (isset($device) && $device->type == 'air_quality_monitor') ? 'selected' : '' ?>>Air Quality Monitor</option>
                            <option value="temperature_sensor" <?= (isset($device) && $device->type == 'temperature_sensor') ? 'selected' : '' ?>>Temperature Sensor</option>
                            <option value="multi_sensor" <?= (isset($device) && $device->type == 'multi_sensor') ? 'selected' : '' ?>>Multi-Sensor</option>
                        </select>
                    </div>
                    <?php if (isset($device)): ?>
                    <div class="mb-3">
                        <label class="">Status</label>
                        <select class="form-control" name="status">
                            <option value="online" <?= $device->status == 'online' ? 'selected' : '' ?>>Online</option>
                            <option value="offline" <?= $device->status == 'offline' ? 'selected' : '' ?>>Offline</option>
                            <option value="maintenance" <?= $device->status == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="">Device Key</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($device->device_key) ?>" disabled>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-scada"><i class="fas fa-save mr-1"></i><?= isset($device) ? 'Update' : 'Create Device' ?></button>
                    <a href="<?= base_url('devices') ?>" class="btn btn-outline-secondary ml-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</section>
