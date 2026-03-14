<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { height: 100%; margin: 0; }
        .wrapper { display: flex; flex-direction: column; min-height: 100vh; }
        .content { flex: 1; }
        .header {
            background-color: #880007; color: white; padding: 15px;
            text-align: center; font-size: 24px; font-weight: bold;
            display: flex; align-items: center; justify-content: space-between;
        }
        .logo { height: 60px; width: auto; }
        .user-icon { width: 40px; height: 40px; border-radius: 50%; cursor: pointer; }
        .footer { background-color: #880007; color: white; text-align: center; padding: 10px; }
        .footer-logo { height: 100px; margin-bottom: 5px; }
        .footer-text { font-size: 12px; margin: 5px 0; }
        .footer-link { color: white; text-decoration: underline; font-size: 12px; }
        .list-group-item.active { background-color: #e0ff00 !important; color: black !important; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        <span>Masterlist</span>
        <div class="dropdown">
            <img src="<?= base_url('assets_systems/usericon.png') ?>" alt="User Icon" class="user-icon dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item text-danger" id="logoutBtn">Log Out</a></li>
            </ul>
        </div>
    </div>

    <main class="content">
        <div class="container mt-4">
            <h3 class="mb-4 text-center">Manage Librarian Account</h3>

            <div class="d-flex justify-content-between mb-3">
                <a href="<?php echo base_url(); ?>masteradmin/dashboard" class="btn btn-secondary">
                    &larr; Back to Dashboard
                </a>
                <div class="d-flex gap-2 ms-auto">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEmailModal">
                        + Add Email
                    </button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                        Bulk Upload
                    </button>
                </div>
            </div>

            <!-- Flash Alerts -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>

            <!-- Email Table -->
            <table class="table table-bordered">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($emails as $row): ?>
                    <tr>
                        <td><?= $row->id; ?></td>
                        <td><?= htmlspecialchars($row->email); ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row->id ?>">
                                Delete
                            </button>

                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal<?= $row->id ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="post" action="<?= base_url('masteradmin/delete_masterlist_email') ?>">
                                        <input type="hidden" name="id" value="<?= $row->id ?>">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this email?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Email Modal -->
        <div class="modal fade" id="addEmailModal" tabindex="-1" aria-labelledby="addEmailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="post" action="<?= base_url('masteradmin/add_masterlist_email') ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addEmailModalLabel">Add New Email</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Email</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Upload Modal -->
        <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="post" action="<?= base_url('masteradmin/bulk_upload_emails') ?>" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Bulk Upload Emails (CSV)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                Please upload a <strong>.csv</strong> file with one email address per line.<br>
                                <code>example1@email.com<br>example2@email.com</code>
                            </div>
                            <div class="mb-3">
                                <input type="file" name="csv_file" accept=".csv" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer mt-3">
        <img src="<?= base_url('assets_systems/RS LOGO.png') ?>" alt="LPU Logo" class="footer-logo">
        <p class="footer-text">
            Copyright © LPU - Cavite 2024.
            All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>" class="footer-link">Privacy Policy</a>
        </p>
        <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => location.reload(), 60000);

        document.getElementById("logoutBtn").addEventListener("click", function () {
            let logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
            logoutModal.show();
        });

        document.getElementById("confirmLogout")?.addEventListener("click", function () {
            window.location.href = "<?= base_url('masteradmin/logout') ?>";
        });
    });
</script>
</body>
</html>
