<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-table mr-2"></i>Summary Report</h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Date Selector & Export -->
        <div class="card scada-card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="">Report Type</label>
                        <select class="form-control form-control-sm" name="report_type" id="report_type">
                            <option value="daily" <?= ($report_type == 'daily') ? 'selected' : '' ?>>Daily</option>
                            <option value="monthly" <?= ($report_type == 'monthly') ? 'selected' : '' ?>>Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="date_field">
                        <label class="">Date</label>
                        <input type="date" class="form-control form-control-sm" name="date" value="<?= $selected_date ?>" <?= ($report_type == 'monthly') ? 'style="display:none;"' : '' ?>>
                    </div>
                    <div class="col-md-2" id="month_field" style="<?= ($report_type == 'monthly') ? '' : 'display:none;' ?>">
                        <label class="">Month</label>
                        <input type="month" class="form-control form-control-sm" name="month" value="<?= $selected_month ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-scada btn-sm"><i class="fas fa-search mr-1"></i>Load</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if (!empty($devices)): ?>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-info btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fas fa-download mr-1"></i>Export CSV</button>
                            <ul class="dropdown-menu ">
                                <?php foreach ($devices as $d): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('reports/export?device_id=' . $d->id . '&report_type=' . $report_type . '&from=' . ($report_type == 'daily' ? $selected_date : $selected_month . '-01') . '&to=' . ($report_type == 'daily' ? $selected_date : date('Y-m-t', strtotime($selected_month . '-01'))) ) ?>">
                                        <?= htmlspecialchars($d->name) ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <a href="<?= base_url('reports/export_excel?mode=' . $report_type . '&date=' . $selected_date . '&month=' . $selected_month) ?>" class="btn btn-outline-success btn-sm ml-1"><i class="fas fa-file-excel mr-1"></i>Export Excel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>

        <!-- Summary Table -->
        <div class="card scada-card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= ucfirst($report_type) ?> Summary for 
                    <?php if ($report_type == 'daily'): ?>
                        <?= date('F j, Y', strtotime($selected_date)) ?>
                    <?php else: ?>
                        <?= date('F Y', strtotime($selected_month . '-01')) ?>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="card-body">
                <?php if (empty($summary)): ?>
                <p class="text-muted text-center py-4">No data available for this <?= $report_type ?> period.</p>
                <?php else: ?>
                <table id="summary-table" class="table table-sm table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>Sensor</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Average</th>
                            <th>Readings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($summary as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s->device_name) ?></td>
                            <td><?= ucfirst($s->sensor_type) ?></td>
                            <td class="text-info"><?= number_format($s->min_val, 1) ?></td>
                            <td class="text-danger"><?= number_format($s->max_val, 1) ?></td>
                            <td class="text-success"><?= number_format($s->avg_val, 1) ?></td>
                            <td><?= number_format($s->reading_count) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
$(function() { 
    $('#summary-table').DataTable({ 
        responsive: true, 
        paging: false, 
        searching: false 
    });
    
    // Toggle between date and month fields
    $('#report_type').change(function() {
        var reportType = $(this).val();
        if (reportType == 'daily') {
            $('#date_field').show();
            $('#month_field').hide();
            $('#date_field input').prop('disabled', false);
            $('#month_field input').prop('disabled', true);
        } else {
            $('#date_field').hide();
            $('#month_field').show();
            $('#date_field input').prop('disabled', true);
            $('#month_field input').prop('disabled', false);
        }
    });
});
</script>