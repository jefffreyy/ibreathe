<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1><i class="fas fa-users mr-2"></i>User Management</h1></div>
            <div class="col-sm-6">
                <button class="btn btn-scada float-sm-right" data-toggle="modal" data-target="#userModal" onclick="resetUserForm()"><i class="fas fa-plus mr-1"></i>Add User</button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= $this->session->flashdata('success') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= $this->session->flashdata('error') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
        <?php endif; ?>

        <div class="card scada-card">
            <div class="card-body">
                <table id="users-table" class="table table-sm table-hover" style="width:100%">
                    <thead>
                        <tr><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u->username) ?></td>
                            <td><?= htmlspecialchars($u->full_name) ?></td>
                            <td><?= htmlspecialchars($u->email) ?></td>
                            <td><span class="badge badge-<?= $u->role == 'admin' ? 'primary' : 'secondary' ?>"><?= ucfirst($u->role) ?></span></td>
                            <td><span class="badge badge-<?= $u->status == 'active' ? 'success' : 'danger' ?>"><?= ucfirst($u->status) ?></span></td>
                            <td style="font-size:12px"><?= $u->last_login ? date('M j, H:i', strtotime($u->last_login)) : 'Never' ?></td>
                            <td>
                                <a href="<?= base_url('admin/edit_user/' . $u->id) ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                                <?php if ($u->id != $this->session->userdata('user_id')): ?>
                                <a href="<?= base_url('admin/delete_user/' . $u->id) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i></a>
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

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="<?= base_url('admin/create_user') ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-scada">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (isset($edit_user)): ?>
<!-- Edit User Modal (auto-open) -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User: <?= htmlspecialchars($edit_user->username) ?></h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="<?= base_url('admin/edit_user/' . $edit_user->id) ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($edit_user->full_name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($edit_user->email) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>New Password (leave blank to keep)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Role</label>
                            <select class="form-control" name="role">
                                <option value="user" <?= $edit_user->role == 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $edit_user->role == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="active" <?= $edit_user->status == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $edit_user->status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="locked" <?= $edit_user->status == 'locked' ? 'selected' : '' ?>>Locked</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-scada">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>$(function(){ $('#editUserModal').modal('show'); });</script>
<?php endif; ?>

<script>
$(function() { $('#users-table').DataTable({ responsive: true, order: [[0, 'asc']] }); });
function resetUserForm() { $('#userModal form')[0].reset(); }
</script>
