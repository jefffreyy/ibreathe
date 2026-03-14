<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>QR Code</title>
    <style>
        body,
        html {
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

        .logo-container {
            display: flex;
            justify-content: center;
            gap: 4rem;
            margin-bottom: 10px;
        }

        .logo-container img {
            height: 70px;
            width: auto;
        }

        .request-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
            text-align: left;
            background: white;
        }

        .text-approved {
            color: #28a745;
        }

        .text-rejected {
            color: #dc3545;
        }

        .text-pending {
            color: #6c757d;
        }

        .text-terminated {
            color: #b02a37;
        }

        .notif-box {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        </div>

        <div class="container mt-3">
            <a href="<?= base_url('index/homepage') ?>" class="btn btn-secondary">← Back to home</a>
        </div>

        <main class="content">
            <div class="request-container p-4 border rounded shadow-sm" style="max-width: 600px; margin: 30px auto; position: relative; background: white;">

                <!-- Back Button -->
                <a href="<?= site_url('index/notification') ?>" class="btn btn-sm btn-outline-secondary mb-3" style="position: absolute; top: 16px; left: 16px;">
                    ← Back
                </a>

                <h4 class="fw-bold text-center mb-4">Notification</h4>

                <h5 class="fw-semibold text-muted mb-3 text-center"><?= ucfirst($type) ?> Details</h5>

                <div class="notif-box p-4 rounded" style="background-color: #f1f1f1;">
                    <!-- Date and Slot Info -->
                    <div class="mb-3 text-center">
                        <strong>Requested Reservation:</strong><br>
                        <?= date('m/d/Y', strtotime($reservation->date)); ?> -
                        <?= $slot_name ?>
                    </div>

                    <!-- Professor Note -->
                    <div class="mb-4 text-secondary">
                        <?= nl2br(htmlspecialchars($reservation->prof_note)); ?>
                    </div>

                    <!-- Status -->
                    <div class="text-center fw-semibold">
                        Your request for the reservation has been
                        <span class="text-<?= strtolower($reservation->status); ?>">
                            <?= strtoupper($reservation->status); ?>
                        </span>
                    </div>

                    <?php if (!empty($reservation->rejection_reason)) : ?>
                        <!-- Rejection Reason -->
                        <div class="mt-4 alert alert-warning">
                            <strong>Reason for Rejection:</strong><br>
                            <?= nl2br(htmlspecialchars($reservation->rejection_reason)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>






        <footer class="footer">
            <img src="<?= base_url('assets_systems/RS LOGO.png') ?>" alt="LPU Logo" class="footer-logo">
            <p class="footer-text">
                Copyrights © Lyceum of the Philippines University - Cavite 2024.<br>
                All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>" class="footer-link">Privacy Policy</a>
            </p>
            <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>