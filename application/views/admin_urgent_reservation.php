<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <title>Urgent Reservation</title>
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

        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px);
            /* Adjust based on your header & footer height */
            padding: 20px;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .custom-btn {
            background-color: #880007;
            color: white;
            border: none;
        }

        .custom-btn:hover {
            background-color: #770007;
            color: white;

        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
    </div>

    <div class="container mt-3">
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">← Back to home</a>
    </div>

    <div class="form-wrapper">
        <div class="form-container">
            <h4 class="text-center mb-3">Urgent Seat Reservation</h4>
            <form id="disabledSeatForm" method="post" action="<?= site_url('admin/urgent_selectseats') ?>">
                <div class="mb-3">
                    <label class="form-label">Urgent Reservations allow bookings even for seats that are already reserved or occupied. Affected users will be notified accordingly.</label>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <input type="hidden" name="created_date" value="<?= date('Y-m-d H:i:s') ?>" />
                <button type="submit" class="btn custom-btn w-100">Select Seats</button>
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

    <!-- Bootstrap JS and Popper.js (required for certain Bootstrap components like dropdowns, tooltips, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz4fnFO9gybYbU6mwRP2VIq0y0iUhtRpI3Jx3I1rVvXCUjGddWJeQ+VgL4"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"
        integrity="sha384-pzjw8f+ua7Kw1TIq0GiJrZfS6mMG9xwDkD6vK7GkP5Y6iF5ww/MmZfP1Z1gCU7D7"
        crossorigin="anonymous"></script>

</body>

</html>