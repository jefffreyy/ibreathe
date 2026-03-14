<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-microchip mr-2"></i>Devices</h1></div>
            <div class="col-sm-6">
                <?php if ($this->session->userdata('role') === 'admin'): ?>
                <a href="<?= base_url('devices/add') ?>" class="btn btn-scada float-sm-right"><i class="fas fa-plus mr-1"></i>Add Device</a>
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
                <table id="devices-table" class="table table-sm table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Last Seen</th>
                            <th>Device Key</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($devices)): foreach ($devices as $d): ?>
                        <tr>
                            <td><a href="<?= base_url('devices/detail/' . $d->id) ?>" class="text-info"><?= htmlspecialchars($d->name) ?></a></td>
                            <td><?= htmlspecialchars($d->location) ?></td>
                            <td><span class="badge badge-secondary"><?= htmlspecialchars($d->type) ?></span></td>
                            <td>
                                <?php if ($d->status == 'online'): ?>
                                    <span class="badge badge-success"><i class="fas fa-circle mr-1" style="font-size:8px"></i>Online</span>
                                <?php elseif ($d->status == 'maintenance'): ?>
                                    <span class="badge badge-warning text-dark">Maintenance</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Offline</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12px"><?= $d->last_seen ? date('M j, H:i', strtotime($d->last_seen)) : '<span class="text-muted">Never</span>' ?></td>
                            <td><code style="font-size:11px"><?= htmlspecialchars($d->device_key) ?></code></td>
                            <td>
                                <a href="<?= base_url('devices/detail/' . $d->id) ?>" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                <?php if ($this->session->userdata('role') === 'admin'): ?>
                                <a href="<?= base_url('devices/edit/' . $d->id) ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="<?= base_url('devices/delete/' . $d->id) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this device? All readings will be lost.')"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
$(function() { $('#devices-table').DataTable({ responsive: true, order: [[3, 'asc'], [0, 'asc']] }); });
</script>
