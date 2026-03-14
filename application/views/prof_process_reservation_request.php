<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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

        .detail-card {
            background-color: #e4edf7;
            min-height: 200px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
            <span>Reservation Request</span>
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

        <div class="mt-3 ms-3">
            <a href="<?= base_url('index/homepage') ?>" class="btn btn-outline-dark btn-sm">
                ← Home
            </a>
        </div>

        <main class="content">
            <div class="container-fluid p-4">
                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-md-3 col-lg-2">
                        <div class="list-group">
                            <a href="<?= base_url('index/reservationrequest') ?>" class="list-group-item list-group-item-action active bg-warning border-0 fw-bold text-dark">Reservation Request</a>
                            <a href="<?= base_url('index/ongoing_request') ?>" class="list-group-item list-group-item-action">Ongoing Reservation</a>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-md-9 col-lg-10">
                        <!-- Back Button -->
                        <div class="mb-2">
                            <button onclick="history.back()" class="btn btn-secondary px-4 py-1">
                                ← Back
                            </button>
                        </div>


                        <!-- Reservation Detail Card -->
                        <div class="p-4 rounded detail-card">
                            <h5 class="mb-3 fw-bold">
                                Requested Date & Time:
                                <?php echo date('m/d/Y', strtotime($request['date'])) . ' ' . htmlspecialchars($request['slot_name'] ?? 'N/A'); ?>
                            </h5>

                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-person-circle fs-3 me-2"></i>
                                <span class="me-3 fw-semibold"><?php echo htmlspecialchars($request['prof_name']); ?></span>
                                <span class="text-muted"><?php echo htmlspecialchars($request['prof_id']); ?></span>
                            </div>

                            <div class="mb-4">
                                <p class="mb-0 fw-bold">Note:</p>
                                <p><?php echo nl2br(htmlspecialchars($request['prof_note'])); ?></p>
                            </div>

                            <div class="d-flex justify-content-end mt-4 mb-0 pb-0">
                                <form action="<?php echo site_url('index/reject_request/' . $request['id']); ?>" method="post" style="display:inline;">
                                    <button type="submit" class="btn btn-danger btn-sm me-2">Withdraw</button>
                                </form>
                                <!-- <a href="<?= site_url('index/selectseats/' . $request['id']) ?>" class="btn btn-success btn-sm">Proceed</a> -->
                            </div>
                        </div>

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
                window.location.href = "<?= base_url('index/logout') ?>";
            });


        });
    </script>
</body>

</html>