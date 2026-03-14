<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPU Seat Reservation - Login Page</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets_systems/RS LOGO.png') ?>">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="LPU Seat Reservation">
    <meta property="og:description" content="Reserve your seat now at Lyceum of the Philippines University - Cavite!">
    <meta property="og:image" content="<?= base_url('assets_systems/social-preview.jpg') ?>">
    <meta property="og:url" content="https://www.cavite.lpu.edu.ph">
    <meta property="og:type" content="website">

    <!-- Twitter Card (for better sharing on Twitter) -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="LPU Seat Reservation">
    <meta name="twitter:description" content="Reserve your seat now at Lyceum of the Philippines University - Cavite!">
    <meta name="twitter:image" content="<?= base_url('assets_systems/social-preview.jpg') ?>">

    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
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

        @media (max-width: 576px) {
            .logo-container {
                gap: 1.5rem;
            }

            .login-card {
                padding: 20px;
            }

            .footer-logo {
                height: 60px;
            }
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 8px 12px;
            align-items: center;
        }

        .card-header-color {
            background-color: #880007;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
    </div>

    <div class="container mt-3">
        <a href="<?= base_url('index/homepage') ?>" class="btn btn-secondary">← Back to home</a>
    </div>

    <div class="container mt-4 mb-5">
        <h5 class="mb-3">Upcoming Reservations</h5>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Date Created</th>
                        <th>Date Reserve</th>
                                 <th>Time</th>
                        <th>Seat No.</th>
               
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reservations)): ?>
                        <?php foreach ($reservations as $reservation){ 
                            

                            ?>
                            <tr>
                                <td><?= 'RSV' . str_pad(htmlspecialchars($reservation['id']), 5, '0', STR_PAD_LEFT); ?></td>
                                <td><?= htmlspecialchars($reservation['created_date']); ?></td>
                                <td><?= htmlspecialchars($reservation['date_reserve']); ?></td>
                                 <td>   <?php
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $reservation['start_time']);
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $reservation['end_time']);
        echo htmlspecialchars($start->format('H:i:s') . ' - ' . $end->format('H:i:s'));
    ?></td>
                                <td><?= htmlspecialchars($reservation['seat_id']); ?></td>
                               
                                <td>Reserved</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($reservation['status'] == "Closed") { ?>
                                            <!-- <button class="btn btn-primary">QR Code</button> -->
                                            <a href="<?= base_url('index/display_qr/'  . $reservation['id']) . '/'; ?>"
                                                class="btn btn-primary">
                                                QR Code
                                            </a>
                                            <a href="<?= base_url('index/cancel_reservation/' . $reservation['id']); ?>"
                                                class="btn btn-danger"
                                                onclick="return confirm('Are you sure you want to cancel this reservation?');">
                                                Cancel
                                            </a>
                                        <?php } ?>
                                    </div>

                                </td>
                            </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No reservations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <br>
        <h5 class="mb-3">Occupied</h5>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Date Created</th>
                        <th>Date Reserve</th>
                        <th>Seat No.</th>
                        <th>Slot Time</th>


                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reservations_occupied)): ?>
                        <?php foreach ($reservations_occupied as $reservationo): ?>
                            <tr>
                                <td><?= 'RSV' . str_pad(htmlspecialchars($reservationo['id']), 5, '0', STR_PAD_LEFT); ?></td>
                                <td><?= htmlspecialchars($reservationo['created_date']); ?></td>
                                <td><?= htmlspecialchars($reservationo['date_reserve']); ?></td>
                                                   <td>   <?php
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $reservationo['start_time']);
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $reservationo['end_time']);
        echo htmlspecialchars($start->format('H:i:s') . ' - ' . $end->format('H:i:s'));
    ?></td>
                                <td><?= htmlspecialchars($reservationo['seat_id']); ?></td>
                                <!-- <td><?= htmlspecialchars($reservationo['slot_name']); ?></td> Show slot_name instead of slot_id -->


                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No reservations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <br>
        <h5 class="mb-3">Cancelled Reservations</h5>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Date Created</th>
                        <th>Date Reserve</th>
                             <th>Time</th>
                        <th>Seat No.</th>
                   


                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reservations_cancelled)): ?>
                        <?php foreach ($reservations_cancelled as $reservationc): ?>
                            <tr>
                                <td><?= 'RSV' . str_pad(htmlspecialchars($reservationc['id']), 5, '0', STR_PAD_LEFT); ?></td>
                                <td><?= htmlspecialchars($reservationc['created_date']); ?></td>
                                <td><?= htmlspecialchars($reservationc['date_reserve']); ?></td>
                   <td>   <?php
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $reservationc['start_time']);
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $reservationc['end_time']);
        echo htmlspecialchars($start->format('H:i:s') . ' - ' . $end->format('H:i:s'));
    ?></td>
                                <td><?= htmlspecialchars($reservationc['seat_id']); ?></td>
                                <!-- <td><?= htmlspecialchars($reservationc['slot_name']); ?></td> Show slot_name instead of slot_id -->


                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No reservations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>





    <footer class="footer">
        <img src="<?= base_url('assets_systems/RS LOGO.png') ?>" alt="LPU Logo" class="footer-logo">
        <p class="footer-text">
            Copyrights © Lyceum of the Philippines University - Cavite 2024.<br>
            All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>" class="footer-link">Privacy Policy</a>
        </p>
        <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let passwordValue;

        if (passwordValue && passwordValue.trim() !== "") {
            document.getElementById("password").textContent = "•".repeat(passwordValue.length);
        } else {
            document.getElementById("password").textContent = "•".repeat(8);
        }
    </script>


</body>

</html>