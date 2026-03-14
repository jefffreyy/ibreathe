<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            /* Para walang horizontal scroll */
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

        .seat {
            width: 40px;
            /* Mas compact */
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            /* Slightly rounded corners */
            font-size: 14px;
            /* Legible text */
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

        .main-container {
            background-color: #DBE4EE;
            padding: 20px;
            border-radius: 10px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .status-box {
            display: flex;
            flex-wrap: nowrap;
            /* Para hindi bumaba kahit lumiit ang screen */
            justify-content: center;
            /* Pantay sa gitna */
            gap: 10px;
            /* Equal spacing */
            overflow: auto;
            /* Para may scrollbar kung kulang ang space */
            max-width: 100%;
            /* Para hindi lumagpas */
            white-space: nowrap;
            /* Para di mag-wrap ang text */
        }


        .status-box span {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            white-space: nowrap;
            /* Para hindi mag-break into multiple lines */
        }


        /* Responsive scaling */
        @media (max-width: 600px) {
            .status-box {
                gap: 3px;
            }

            .status-box span {
                font-size: 0.7rem;
                min-width: 75px;
                /* Mas maliit para magkasya */
                padding: 4px 8px;
                text-align: center;
            }
        }


        .seat-map {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
        }

        .seat-map img {
            max-width: 95%;
            max-height: 95%;
            object-fit: contain;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(10, minmax(30px, 1fr));
            /* 10 per row */
            gap: 8px;
            /* Equal space sa lahat ng sides */
            justify-content: center;
            /* Sentro */
            max-width: 550px;
            /* Mas maliit na width */
            margin: auto;
            /* Center sa page */
        }

        .seat {
            width: 40px;
            /* Mas compact */
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            /* Slightly rounded corners */
            font-size: 14px;
            /* Legible text */
        }

        /* Kulay ng seats */
        .available {
            background-color: green;
            color: white;
        }

        .occupied {
            background-color: red;
            color: white;
        }

        .reserved {
            background-color: yellow;
            color: black;
        }

        .not-available {
            background-color: gray;
            color: white;
        }

        /* Responsive scaling */
        @media (max-width: 600px) {
            .seat {
                max-width: 30px;
                /* Mas maliit sa mobile */
                height: 30px;
                font-size: 15px;
                font-weight: bold;
            }
        }

        .custom-modal {
            border-radius: 20px;
            padding: 30px;
            border: 3px solid black;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Center the modals */
        .modal-dialog {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Button styles */
        .yes-btn,
        .scanned-btn {
            background-color: limegreen;
            color: black;
            font-weight: bold;
            font-size: 20px;
            padding: 10px 30px;
            border-radius: 10px;
            border: none;
        }

        .no-btn {
            background-color: red;
            color: black;
            font-weight: bold;
            font-size: 20px;
            padding: 10px 30px;
            border-radius: 10px;
            border: none;
        }

        .qr-image {
            width: 150px;
            /* Adjust size as needed */
            height: auto;
            margin-top: 10px;
            border-radius: 10px;
        }

        .small-label {
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
        }

        .custom-modal .time-options {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .custom-modal .time-btn {
            width: 90%;
            margin-bottom: 8px;
            padding: 10px;
            border-radius: 20px;
            border: 2px solid #ccc;
            background-color: white;
            color: black;
            font-weight: bold;
            transition: 0.3s;

            .custom-modal .time-btn:hover,
            .custom-modal .time-btn:focus {
                border-color: black;
                background-color: #DBE4EE;
            }

            .time-btn {
                background-color: #f8f9fa;
            }

            .custom-modal .btn-success {
                width: 45%;
                border-radius: 20px;
            }

            .custom-modal .btn-danger {
                width: 45%;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        <span>Edit Opening Hours</span>

        <!-- Notification and User Icons Container -->
        <div class="d-flex align-items-center" style="gap: 10px;">
            <!-- Notification Icon with Badge -->
            <div class="dropdown position-relative">
                <a href="<?= base_url('admin/notification') ?>" class="d-block" style="position: relative;">
                    <img src="<?= base_url('assets_systems/notification_bell.png') ?>" alt="Notification Bell"
                        class="user-icon dropdown-toggle" id="notificationDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <!-- Notification badge -->
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;">
                        5
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                </a>

            </div>

            <!-- User Icon -->
            <div class="dropdown">
                <img src="<?= base_url('assets_systems/usericon.png') ?>" alt="User Icon"
                    class="user-icon dropdown-toggle" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="<?= base_url('admin/reservation_request') ?>">Reservation Request</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/new_password') ?>">Reset Password of a user</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/summaryreport') ?>">Reports</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/editseats') ?>">Seats</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/opening_hours') ?>">Opening Hours</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/admin_settings') ?>">Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" id="logoutBtn">Log Out</a></li>
                </ul>
            </div>
        </div>


    </div>
  <!-- Back Button -->
             <div class="mt-3 ms-3">
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-dark btn-sm">
                    ← Home
                </a>
            </div>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg rounded-4">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4">Edit Opening Hours</h4>

                   <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

                    <form method="post" action="<?= site_url('admin/update_hours') ?>">
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="<?= set_value('start_time', $start_time ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="<?= set_value('end_time', $end_time ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Hours</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <footer class="footer mt-3">
        <img src="<?= base_url('assets_systems/RS LOGO.png') ?>" alt="LPU Logo" class="footer-logo">
        <p class="footer-text">
            Copyright © Lyceum of the Philippines University - Cavite 2024.<br>
            All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>" class="footer-link">Privacy Policy</a>
        </p>
        <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>