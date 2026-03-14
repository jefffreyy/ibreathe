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


        .pill-box {
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 14px;
            color: white;
            font-weight: 500;
        }

        .status-approved {
            background-color: #28a745;
        }

        .status-rejected,
        .status-terminated {
            background-color: #dc3545;

        }

        .status-pending {
            background-color: #6c757d;

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
            <div class="request-container p-4 border rounded mb-3" style="max-width: 600px; margin: 0 auto;">
                <h4 class="fw-bold text-center mb-4">Notification</h4>

                <!-- Ongoing Header Row -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-semibold mb-0">Ongoing</h5>
                    <div class="text-muted small">Tap to see details...</div>
                </div>

                <!-- Ongoing Notifications -->
                <?php foreach ($ongoing as $item): ?>
                    <a href="<?= site_url('index/notification_detail/' . ($item->status == 'PENDING' ? 'request' : 'ongoing') . '/' . $item->id); ?>" style="text-decoration: none;">
                        <div class="pill-box d-flex justify-content-between align-items-center status-<?= strtolower($item->status); ?> mb-2 p-2 rounded">
                            <span>
                                Requested Reservation: <?= date('m/d/Y', strtotime($item->date)); ?>
                                <?= $this->user_model->get_slot_name_by_id($item->time_slot); ?>
                            </span>
                            <strong class="text-<?= strtolower($item->status); ?>">| <?= strtoupper($item->status); ?></strong>
                        </div>
                    </a>
                <?php endforeach; ?>

                <!-- Lapsed Header -->
                <h5 class="fw-semibold mt-4 mb-2">Lapsed</h5>

                <!-- Lapsed Notifications -->
                <?php foreach ($lapsed as $item): ?>
                    <a href="<?= site_url('index/notification_detail/' . ($item->status == 'TERMINATED' ? 'terminated' : 'ongoing') . '/' . $item->id); ?>" style="text-decoration: none;">
                        <div class="pill-box d-flex justify-content-between align-items-center status-<?= strtolower($item->status); ?> mb-2 p-2 rounded">
                            <span>
                                Requested Reservation: <?= date('m/d/Y', strtotime($item->date)); ?>
                                <?= $this->user_model->get_slot_name_by_id($item->time_slot); ?>
                            </span>
                            <strong class="text-<?= strtolower($item->status); ?>">| <?= strtoupper($item->status); ?></strong>
                        </div>
                    </a>
                <?php endforeach; ?>
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