<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urgent Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
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
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .status-box span {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        .seat-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            width: 100%;
        }

        .seat-row {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        .seat {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            font-size: 14px;
            margin: 0 5px;
            cursor: pointer;
        }

        /* Seat status colors */
        .available {
            background-color: green;
            color: white;
        }

        .occupied {
            background-color: red;
            color: white;
        }

        .closed {
            background-color: yellow;
            color: black;
        }

        .not-available {
            background-color: gray;
            color: white;
        }

        .selected {
            border: 3px solid #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .seat {
                width: 30px;
                height: 30px;
                font-size: 12px;
                margin: 0 3px;
            }
        }

        /* Disabled states */
        .not-available,
        .closed,
        .occupied {
            pointer-events: none;
            cursor: default;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
    </div>

    <div style="text-align: center;">
        Last Update: &nbsp;<?= $current_date ?>
    </div>

    <div class="main-container mt-3 ms-3 me-3 mb-1">
        <p>Selected Date: <strong>&nbsp;<?= $selected_date ?></strong></p>
        <div class="status-box">
            <span class="available m-1">Available</span>
            <span class="closed m-1">Reserved</span>
            <span class="occupied m-1">Occupied</span>
            <span class="not-available m-1">Not Available</span>
        </div>

    <div class="seat-container">
    <?php
    $set_seats = 87; // Total seats to display
    $seatsPerRow = 10; // Seats per row
    $currentSeat = 1;
    
    while ($currentSeat <= $set_seats) {
        echo '<div class="seat-row">';
        
        // Calculate how many seats in this row (either 10 or remaining seats)
        $seatsInThisRow = min($seatsPerRow, $set_seats - $currentSeat + 1);
        
        for ($i = 0; $i < $seatsInThisRow; $i++) {
            $seatNumber = $currentSeat + $i;
            // Use the status if it exists, otherwise default to 'not-available'
            $statusClass = isset($slot_display[$seatNumber-1]) ? $slot_display[$seatNumber-1] : 'not-available';
            echo '<div class="seat small-label ' . $statusClass . '" 
                  data-seat="' . $seatNumber . '" 
                  onclick="handleSeatClick(' . $seatNumber . ')">' . $seatNumber . '</div>';
        }
        
        echo '</div>';
        $currentSeat += $seatsPerRow;
    }
    ?>
</div>

        <h5 class="mb-2 fw-bold mt-2">
            <p>Number of Seats Selected: <span id="selectedCount">0</span></p>
        </h5>

        <div class="d-flex justify-content-end mt-2 mb-0 pb-0">
            <a href="#" class="btn btn-danger btn-sm me-2" onclick="history.back(); return true;">Cancel</a>

            <form id="urgrentReservation" method="post" action="<?= site_url('admin/submit_urgent_reservation') ?>">
                <input type="hidden" name="seat_assigned" id="seat_assigned_input">
                <input type="hidden" name="date_disabled" value="<?= $selected_date ?>">
                <button type="submit" id="reserve" class="btn btn-success btn-sm disabled" style="pointer-events: none;">Reserve</button>
            </form>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                location.reload();
            }, 60000); // 60000ms = 60 seconds

            let currentDateTime = new Date("<?php echo $current_date; ?>");
            let currentHours = currentDateTime.getHours();
            let currentMinutes = currentDateTime.getMinutes();
        });

        let selectedSeatNumbers = [];

        function handleSeatClick(seatNumber) {
            const seatElement = document.querySelector(`[data-seat="${seatNumber}"]`);
            if (seatElement && !seatElement.classList.contains('occupied') && 
                !seatElement.classList.contains('closed') && 
                !seatElement.classList.contains('not-available')) {
                seatElement.classList.toggle('selected');
                updateSelectedSeatInfo();
            }
        }

        function updateSelectedSeatInfo() {
            const selectedSeats = document.querySelectorAll('.small-label.selected');

            selectedSeatNumbers = Array.from(selectedSeats)
                .map(seat => parseInt(seat.getAttribute('data-seat')))
                .sort((a, b) => a - b);

            document.getElementById('selectedCount').textContent = selectedSeatNumbers.length;
            document.getElementById('seat_assigned_input').value = selectedSeatNumbers.join(',');

            const reserveButton = document.getElementById('reserve');
            if (selectedSeatNumbers.length > 0) {
                reserveButton.classList.remove('disabled');
                reserveButton.style.pointerEvents = 'auto';
            } else {
                reserveButton.classList.add('disabled');
                reserveButton.style.pointerEvents = 'none';
            }
        }
    </script>
</body>
</html>