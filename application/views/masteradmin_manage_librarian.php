<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
        }

        .header {
            background-color: #880007;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            height: 60px;
            width: auto;
        }

        .user-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }


        .footer {
            background-color: #880007;
            color: white;
            text-align: center;
            padding: 10px;
        }

        .footer-logo {
            height: 100px;
            margin-bottom: 5px;
        }

        .footer-text {
            font-size: 12px;
            margin: 5px 0;
        }

        .footer-link {
            color: white;
            text-decoration: underline;
            font-size: 12px;
        }

        .list-group-item.active {
            background-color: #e0ff00 !important;
            color: black !important;
        }

        .reservation-entry {
            background-color: #f8f9fa;
            padding: 6px 12px;
            border-radius: 6px;
            margin-bottom: 6px;
            font-size: 0.9rem;
            text-decoration: none;
            color: inherit;
            transition: background-color 0.2s ease;
        }


        .reservation-entry:hover {
            background-color: #e2e6ea;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
            <span>Librarian Account</span>
            <div class="dropdown">
                <img src="<?= base_url('assets_systems/usericon.png') ?>" alt="User Icon" class="user-icon dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item text-danger" id="logoutBtn">Log Out</a></li>
                </ul>
            </div>

            <!-- Logout Confirmation Modal -->
            <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <p class="text-dark fw-bold">Are you sure you want to log out?</p>
                            <button type="button" class="btn btn-danger" id="confirmLogout">Yes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="content mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">

                    <div class="px-4">
                        <h3 class="mb-4 text-center">Manage Librarian Account</h3>
                        <a href="<?php echo base_url(); ?>masteradmin/dashboard" class="btn btn-secondary mb-2">
                            &larr; Back to Dashboard

                        </a>
                        <!-- Flash Message -->
                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('success'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php elseif ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('error'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?php echo base_url(); ?>masteradmin/manage_librarian" onsubmit="return validateForm();">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" value="<?php echo $librarian['username']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="text" name="password" id="password" class="form-control" value="<?php echo $librarian['password']; ?>" required>
                                <small class="form-text text-muted">
                                    Password must contain at least 1 special character (e.g., @ # $ % ^ & * _ / &lt; &gt;).
                                </small>
                            </div>

                            <div id="passwordError" class="text-danger mb-3" style="display: none;">
                                Password must contain at least one special character.
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="update_librarian" class="btn btn-success">Update Librarian</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </main>



        <footer class="footer mt-3">
            <img src="<?= base_url('assets_systems/RS LOGO.png') ?>" alt="LPU Logo" class="footer-logo">
            <p class="footer-text">
                Copyright © Lyceum of the Philippines University - Cavite 2024.<br>
                All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>" class="footer-link">Privacy Policy</a>
            </p>
            <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            setTimeout(function() {
                location.reload();
            }, 60000); // 10000ms = 10 seconds


            //UserIconDropdown
            let userDropdown = document.getElementById("userDropdown");
            let dropdownMenu = userDropdown.nextElementSibling;

            document.addEventListener("mouseleave", function(event) {
                if (!userDropdown.contains(event.relatedTarget) && !dropdownMenu.contains(event.relatedTarget)) {
                    dropdownMenu.classList.remove("show");
                    userDropdown.setAttribute("aria-expanded", "false");
                }
            });

            document.getElementById("logoutBtn").addEventListener("click", function() {
                let logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
                logoutModal.show();
            });

            document.getElementById('confirmLogout').addEventListener('click', function() {
                window.location.href = "<?= base_url('masteradmin/logout') ?>";
            });

        });
    </script>
    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;

            if (!specialCharRegex.test(password)) {
                document.getElementById('passwordError').style.display = 'block';
                return false; // <- THIS stops submission
            }

            document.getElementById('passwordError').style.display = 'none';
            return true;
        }
    </script>

</body>

</html>