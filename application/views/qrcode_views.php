<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
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

        .qr-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 40px auto;
            /* Adds margin on top and bottom */
        }


        .btn-success {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
            text-decoration: none;
            font-family: Arial, sans-serif;
            /* Ensure button text is also in Arial */
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
    </div>

   


    <div class="qr-container">
        <table border="1" cellpadding="8" cellspacing="0" style="margin: auto; border-collapse: collapse;">
            <tr>
                <th style="text-align: left; padding: 8px;">Reservation ID</th>
                <td style="padding: 8px;"><?= 'RSV' . str_pad(htmlspecialchars($reserve_id), 5, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; padding: 8px;">Student ID</th>
                <td style="padding: 8px;"><?= $student_id ?></td>
            </tr>
            <tr>
                <th style="text-align: left; padding: 8px;">Seat No</th>
                <td style="padding: 8px;"><?= $seat_id ?></td>
            </tr>
            <tr>
                <th style="text-align: left; padding: 8px;">Date</th>
                <td style="padding: 8px;"><?= $date_reserve ?></td>
            </tr>
            <tr>
                <th style="text-align: left; padding: 8px;">Reservation time</th>
                <td style="padding: 8px;"><?= $start_time . ' - ' .  $end_time ?></td>
            </tr>
        </table>

    
            <img src="https://quickchart.io/qr?text=<?= $reserve_id ?>&size=200" alt="QR Code">
            <br>Scan QR to Occupy<br>
       

        <a href="<?= base_url(); ?>index/reservationrequest" class="btn-success" style="width: 90%;">← Go to My Reservations</a>
    </div>


    <footer class="footer">
        <img src="<?= base_url('assets_systems/RS LOGO.png') ?>" alt="LPU Logo" class="footer-logo">
        <p class="footer-text">
            Copyrights © Lyceum of the Philippines University - Cavite 2024.<br>
            All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>" class="footer-link">Privacy Policy</a>
        </p>
        <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
    </footer>
</body>

</html>