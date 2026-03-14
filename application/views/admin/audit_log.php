<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-clipboard-list mr-2"></i>Audit Log</h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card scada-card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="">From</label>
                        <input type="date" class="form-control form-control-sm" name="date_from" value="<?= $filters['date_from'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="">To</label>
                        <input type="date" class="form-control form-control-sm" name="date_to" value="<?= $filters['date_to'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-scada btn-sm"><i class="fas fa-filter mr-1"></i>Filter</button>
                        <a href="<?= base_url('admin/audit_log') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card scada-card">
            <div class="card-body">
                <table id="audit-table" class="table table-sm table-hover" style="width:100%">
                    <thead>
                        <tr><th>Time</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="font-size:12px"><?= date('M j, H:i:s', strtotime($log->created_at)) ?></td>
                            <td><?= htmlspecialchars($log->username ?? 'System') ?></td>
                            <td><span class="badge badge-secondary"><?= htmlspecialchars($log->action) ?></span></td>
                            <td><?= htmlspecialchars($log->description) ?></td>
                            <td style="font-size:12px" class="text-muted"><?= htmlspecialchars($log->ip_address) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
$(function() { $('#audit-table').DataTable({ responsive: true, order: [[0, 'desc']] }); });
</script>
