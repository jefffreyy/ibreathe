<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Slots</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
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
    <script>
        function isSunday(dateStr) {
            const date = new Date(dateStr);
            return date.getDay() === 0;
        }

        function reloadPage() {
            var dateInput = document.getElementById("date_select");
            var selectedDate = dateInput.value;

            if (isSunday(selectedDate)) {
                alert("Sunday reservations are not allowed. Returning to today.");

                // Reset to today
                var today = new Date().toISOString().split("T")[0];
                dateInput.value = today;
                return;
            }

            window.location.href = `<?= base_url('index/check_status') ?>/<?= $seat_id ?>/${selectedDate}`;
        }



        function reserveSlot(slotId) {
            if (confirm("Are you sure you want to reserve this slot?")) {
                window.location.href = `<?= base_url('index/reservation') ?>/${slotId}/<?= $seat_id ?>/<?= $selected_date ?>`;
            }
        }

        function goToHomepage() {
            window.location.href = "<?= base_url('index/homepage') ?>";
        }

        function changeDate(days) {
            var dateInput = document.getElementById("date_select");
            var currentDate = new Date(dateInput.value);

            do {
                currentDate.setDate(currentDate.getDate() + days);
            } while (currentDate.getDay() === 0); // skip Sundays

            var newDate = currentDate.toISOString().split('T')[0];
            dateInput.value = newDate;
            reloadPage();
        }

        document.addEventListener("DOMContentLoaded", function() {
            const dateInput = document.getElementById("date_select");
            const today = new Date().toISOString().split("T")[0];
            dateInput.setAttribute("min", today);
        });
    </script>

</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
    </div>

    <div class="container mb-5 mt-5 text-center">


        <h2 class="mb-3">Seat Reservation</h2>
        <h5 class="mb-4">Seat No: <strong><?= htmlspecialchars($seat_id) ?></strong></h5>

        <!-- Date Selection -->
        <label for="date_select" class="form-label">Select Date:</label>
        <div class="input-group mb-3">
            <button class="btn btn-outline-secondary" type="button" onclick="changeDate(-1)">&laquo;</button>
            <input type="date" id="date_select" class="form-control text-center" onchange="reloadPage()" value="<?= $selected_date ?>">
            <button class="btn btn-outline-secondary" type="button" onclick="changeDate(1)">&raquo;</button>
        </div>


        <table class="table table-bordered table-striped text-center">
            <thead class="table-light">
                <tr>
                    <th>Slot</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($slots_status)) { ?>
                    <?php foreach ($slots_status as $slot) {
                        $now_datetime = strtotime($current_datetime);

                        // Ensure slot_name contains a valid time range
                        if (strpos($slot->slot_name, " - ") !== false) {    
                            list($start_time, $end_time) = explode(" - ", $slot->slot_name);
                            $var_datetime = strtotime("$selected_date $start_time");
                        } else {
                            $var_datetime = 0; // Default fallback
                        }

                        // Determine row class
                        $row_class = ($now_datetime > $var_datetime) ? "past-slot" : "";

                        // Determine slot status class
                        $status_class = ($slot->final_status == 'Open') ? 'open' : (($slot->final_status == 'not-available') ? 'disabled-slot' : 'reserved');
                    ?>
                        <tr class="<?= $row_class ?>">
                            <td><?= htmlspecialchars($slot->slot_name) ?></td>
                            <td class="<?= $status_class ?>">
                                <strong>
                                    <?= ($slot->final_status === 'not-available') ? 'Not-Available' : htmlspecialchars($slot->final_status) ?>
                                </strong>
                            </td>
                            <td>
                                <?php
                                if ($slot->final_status == 'Open' && $now_datetime <= $var_datetime) {
                                ?>
                                    <button class="btn btn-primary btn-sm" onclick="reserveSlot(<?= $slot->id ?>)">Reserve</button>
                                <?php } else { ?>
                                    <span>-</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3">No slots available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <button class="btn btn-secondary mb-3" onclick="goToHomepage()">← Back to Home</button>
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