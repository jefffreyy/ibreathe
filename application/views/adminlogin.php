<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login</title>

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

    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .bg-image {
            background: url('<?= base_url('assets_systems/cavite-campus4.jpg') ?>') no-repeat center center;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .login-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin-bottom: 200px
        }

        .login-title {
            font-size: 22px;
            font-weight: bold;
            color: #880007;
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 5px;
            padding: 10px;
        }

        .btn-login {
            background-color: #880007;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            width: auto;
            padding: 8px 20px;
            display: block;
            margin-left: 0;
        }

        .form-control {
            text-align: center;
        }

        .forgot-password {
            text-align: left;
            color: #880007;
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
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
    </div>

    <div class="bg-image">
        <div class="login-card">
            <div class="logo-container">
                <img src="<?= base_url('assets_systems/LPUCAVITE.png') ?>" alt="LPU Logo">
                <img src="<?= base_url('assets_systems/RS LOGO 1.png') ?>" alt="RS Logo">
            </div>
            <h3 class="login-title">Administrator</h3>
            <form id="loginForm" action="<?= base_url('admin/adminlogin') ?>" method="post">
            <div class="mb-3">
                    <input type="text" name="user_id" id="user_id" class="form-control" placeholder="Username">
                </div>
                <div class="mb-3">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                    <?php if (isset($error)): ?>
                        <small class="text-danger"><?= $error; ?></small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-login">Login</button>
            </form>
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

</body>

</html>