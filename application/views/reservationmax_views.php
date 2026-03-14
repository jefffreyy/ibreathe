<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Conflict</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
  body, html {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

.container {
    background: white;
    padding: 40px 20px; /* Increased padding for better spacing */
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-height: 200px;
    max-width: 600px;
    width: 100%;
    flex-grow: 1; /* Allows content to expand and push footer down */
    display: flex;
    flex-direction: column; /* Align content vertically */
    justify-content: center; /* Centers vertically */
    align-items: center; /* Centers horizontally */
    text-align: center;
    margin: auto; /* Ensures it is centered */
}
        .open {
            color: green;
            font-weight: bold;
        }

        .reserved {
            color: red;
            font-weight: bold;
        }

        .table tbody tr.past-slot {
            background-color: gray !important;
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
    </style>
</head>

<body>
<div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
    </div>
    <div class="container">
        <h3>Reservation Reached Limit</h3>
        <br>
        <p>You reached maximum slots of 3 per day. Reserve on other days </p>
        <br>
        <button class="btn btn-secondary" onclick="window.history.back()">← Back to Reservation</button>
      
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
