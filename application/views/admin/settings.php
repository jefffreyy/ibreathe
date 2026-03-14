<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-sliders-h mr-2"></i>System Settings</h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= $this->session->flashdata('success') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
        <?php endif; ?>

        <div class="card scada-card" style="max-width:700px">
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/settings') ?>">
                    <?php foreach ($settings as $s): ?>
                    <div class="mb-3">
                        <label class="">
                            <strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', $s->setting_key))) ?></strong>
                            <?php if ($s->description): ?>
                            <br><small class="text-muted"><?= htmlspecialchars($s->description) ?></small>
                            <?php endif; ?>
                        </label>
                        <input type="hidden" name="setting_key[]" value="<?= htmlspecialchars($s->setting_key) ?>">
                        <input type="text" class="form-control" name="setting_value[]" value="<?= htmlspecialchars($s->setting_value) ?>">
                    </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-scada"><i class="fas fa-save mr-1"></i>Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</section>
