<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-user mr-2"></i>Profile</h1></div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" style="max-width:600px"><?= $this->session->flashdata('success') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
        <?php endif; ?>

        <div class="card scada-card profile-card">
            <div class="card-header"><h3 class="card-title">Account Information</h3></div>
            <div class="card-body">
                <form method="POST" action="<?= base_url('scada/profile') ?>">
                    <div class="mb-3">
                        <label class="">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user->username) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="">Full Name</label>
                        <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($user->full_name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user->email) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="">Role</label>
                        <input type="text" class="form-control" value="<?= ucfirst($user->role) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                        <input type="password" class="form-control" name="password" minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="">Member Since</label>
                        <input type="text" class="form-control" value="<?= date('F j, Y', strtotime($user->created_at)) ?>" disabled>
                    </div>
                    <button type="submit" class="btn btn-scada"><i class="fas fa-save mr-1"></i>Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</section>
