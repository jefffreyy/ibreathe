<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            background-color: #dce3ed;
            max-width: 1200px;
            background: #DBE4EE;
            padding: 70px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }

        .custom-btn {
            background-color: #880007;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .custom-btn:hover {
            background-color: #6b0005;
            color: white;
        }

        .header {
            background-color: #880007;
            color: white;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .logo {
            height: 40px;
            width: auto;
        }

        .logo-placeholder {
            width: 60px;
            height: 60px;
        }



        .footer {
            background-color: #880007;
            color: white;
            text-align: center;
            padding: 10px;
        }

        .footer-logo {
            height: 100px;
            /* Adjust the size as needed */
            margin-bottom: 5px;
        }

        .footer-text {
            font-size: 12px;
            margin: 5px 0;
        }

        .footer-link,
        .footer-text a {
            color: white;
            text-decoration: underline;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header d-flex align-items-center">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        <h2 class="flex-grow-1 text-center"><b>Admin Settings</b></h2>
        <div class="logo-placeholder"></div> <!-- Balancing the space -->
    </div>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form action="<?= base_url('admin/update_qr_setting') ?>" method="post">
            <div class="card p-4 shadow" style="width: 400px;">
                <h4 class="mb-3 text-center">QR Code Display Settings</h4>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="strictTimingToggle" name="qr_restriction" value="1" <?php echo isset($qr_restriction) && $qr_restriction ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="strictTimingToggle">
                        Enable QR only during reserved time slot
                    </label>
                </div>
                <div class="text-muted small">
                    <p>✔️ If enabled, QR codes will only show during the exact time slot you reserved.</p>
                    <p>❌ If disabled, QR codes will appear on the day of reservation regardless of time.</p>
                </div>
                <button class="btn btn-primary w-100 mt-3" type="submit">Save Setting</button>
            </div>
        </form>
    </div>


    <footer class="footer">
        <img src="<?= base_url('assets_systems/RS LOGO 2.png') ?>" alt="LPU Logo" class="footer-logo">
        <p class="footer-text">
            Copyright © Lyceum of the Philippines University - Cavite 2024.<br>
            All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>">Privacy Policy</a>
        </p>
        <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
    </footer>


    <script>
        function alertSetting() {
            const enabled = document.getElementById('strictTimingToggle').checked;
            alert('Strict QR Timing is ' + (enabled ? 'enabled ✅' : 'disabled ❌'));
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>




</body>

</html>