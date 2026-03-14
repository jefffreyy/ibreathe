<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            /* Para walang horizontal scroll */
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

        .seat {
            width: 40px;
            /* Mas compact */
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            /* Slightly rounded corners */
            font-size: 14px;
            /* Legible text */
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
            flex-wrap: nowrap;
            /* Para hindi bumaba kahit lumiit ang screen */
            justify-content: center;
            /* Pantay sa gitna */
            gap: 10px;
            /* Equal spacing */
            overflow: auto;
            /* Para may scrollbar kung kulang ang space */
            max-width: 100%;
            /* Para hindi lumagpas */
            white-space: nowrap;
            /* Para di mag-wrap ang text */
        }


        .status-box span {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            white-space: nowrap;
            /* Para hindi mag-break into multiple lines */
        }


        /* Responsive scaling */
        @media (max-width: 600px) {
            .status-box {
                gap: 3px;
            }

            .status-box span {
                font-size: 0.7rem;
                min-width: 75px;
                /* Mas maliit para magkasya */
                padding: 4px 8px;
                text-align: center;
            }
        }


        .seat-map {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
        }

        .seat-map img {
            max-width: 95%;
            max-height: 95%;
            object-fit: contain;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(10, minmax(30px, 1fr));
            /* 10 per row */
            gap: 8px;
            /* Equal space sa lahat ng sides */
            justify-content: center;
            /* Sentro */
            max-width: 550px;
            /* Mas maliit na width */
            margin: auto;
            /* Center sa page */
        }

        .seat {
            width: 40px;
            /* Mas compact */
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            /* Slightly rounded corners */
            font-size: 14px;
            /* Legible text */
        }

        /* Kulay ng seats */
        .available {
            background-color: green;
            color: white;
        }

        .occupied {
            background-color: red;
            color: white;
        }

        .reserved {
            background-color: yellow;
            color: black;
        }

        .not-available {
            background-color: gray;
            color: white;
        }

        /* Responsive scaling */
        @media (max-width: 600px) {
            .seat {
                max-width: 30px;
                /* Mas maliit sa mobile */
                height: 30px;
                font-size: 15px;
                font-weight: bold;
            }
        }

        .custom-modal {
            border-radius: 20px;
            padding: 30px;
            border: 3px solid black;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Center the modals */
        .modal-dialog {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Button styles */
        .yes-btn,
        .scanned-btn {
            background-color: limegreen;
            color: black;
            font-weight: bold;
            font-size: 20px;
            padding: 10px 30px;
            border-radius: 10px;
            border: none;
        }

        .no-btn {
            background-color: red;
            color: black;
            font-weight: bold;
            font-size: 20px;
            padding: 10px 30px;
            border-radius: 10px;
            border: none;
        }

        .qr-image {
            width: 150px;
            /* Adjust size as needed */
            height: auto;
            margin-top: 10px;
            border-radius: 10px;
        }

        .small-label {
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
        }

        .custom-modal .time-options {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .custom-modal .time-btn {
            width: 90%;
            margin-bottom: 8px;
            padding: 10px;
            border-radius: 20px;
            border: 2px solid #ccc;
            background-color: white;
            color: black;
            font-weight: bold;
            transition: 0.3s;

            .custom-modal .time-btn:hover,
            .custom-modal .time-btn:focus {
                border-color: black;
                background-color: #DBE4EE;
            }

            .time-btn {
                background-color: #f8f9fa;
            }

            .custom-modal .btn-success {
                width: 45%;
                border-radius: 20px;
            }

            .custom-modal .btn-danger {
                width: 45%;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        <span>Librarian Monitoring Interface</span>

        <!-- Notification and User Icons Container -->
        <div class="d-flex align-items-center" style="gap: 10px;">
            <!-- Notification Icon with Badge -->
            <div class="dropdown position-relative">
                <a href="<?= base_url('admin/notification') ?>" class="d-block" style="position: relative;">
                    <img src="<?= base_url('assets_systems/notification_bell.png') ?>" alt="Notification Bell"
                        class="user-icon dropdown-toggle" id="notificationDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <!-- Notification badge -->
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;">
                        5
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                </a>

            </div>

            <!-- User Icon -->
            <div class="dropdown">
                <img src="<?= base_url('assets_systems/usericon.png') ?>" alt="User Icon"
                    class="user-icon dropdown-toggle" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="<?= base_url('admin/reservation_request') ?>">Reservation Request</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/new_password') ?>">Reset Password of a user</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/summaryreport') ?>">Reports</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/editseats') ?>">Seats</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/opening_hours') ?>">Opening Hours</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/admin_settings') ?>">Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" id="logoutBtn">Log Out</a></li>
                </ul>
            </div>
        </div>

        <!-- Logout Confirmation Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <p class="text-dark fw-bold">Are you sure you want to log out?</p>
                        <button type="button" class="btn btn-danger" id="confirmLogout">Yes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div style="text-align: center;">
        Last Update: &nbsp;2025-05-02 07:00:00
    </div> -->


    <!-- <h4 class="text-center mb-4">Select Date and Time Slot</h4> -->

    <div class="p-4 m-4">
    <form method="GET" action="">
        <div class="mb-3 ">
            <label for="day" class="form-label">Choose a Date:</label>
            <input type="date" name="day" id="day" class="form-control"
                value="<?= isset($_GET['day']) ? $_GET['day'] : date('Y-m-d') ?>">
        </div>

        <div class="mb-3">
            <label for="timeslot" class="form-label">Choose a Time Slot:</label>
            <select name="timeslot" id="timeslot" class="form-select">
                <option value="">-- Select Time Slot --</option>
                <?php
                $timeSlots = [
                    1 => ['07:00', '08:30', '07:00 - 08:30 AM'],
                    2 => ['08:30', '10:00', '08:30 - 10:00 AM'],
                    3 => ['10:00', '11:30', '10:00 - 11:30 AM'],
                    4 => ['11:30', '13:00', '11:30 - 01:00 PM'],
                    5 => ['13:00', '14:30', '01:00 - 02:30 PM'],
                    6 => ['14:30', '16:00', '02:30 - 04:00 PM'],
                    7 => ['16:00', '17:00', '04:00 - 05:00 PM']
                ];
                foreach ($timeSlots as $key => $slot):
                ?>
                    <option value="<?= $key ?>" <?= (isset($_GET['timeslot']) && $_GET['timeslot'] == $key) ? 'selected' : '' ?>>
                        <?= $slot[2] ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </div>
        <br>



        <div class="text-center">
            <button type="submit" class="btn btn-primary">Apply Date and Slot</button>
        </div>
    </form>
                </div>
    <div class="p-4 m-4">

        <h2 class="mb-4">Upload Image</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($upload_data)): ?>
            <div class="alert alert-success">Image uploaded successfully!</div>
            <p><strong>File Name:</strong> <?php echo $upload_data['file_name']; ?></p>
            <p><img src="<?php echo base_url('uploads/' . $upload_data['file_name']); ?>" class="img-fluid" alt="Uploaded Image" /></p>
        <?php endif; ?>

        <?php echo form_open_multipart('admin/do_upload'); ?>
        <div class="mb-3">
            <input type="file" name="userfile" class="form-control" size="20" required />
        </div>
        <div class="mb-3">
            <input type="submit" value="Upload" class="btn btn-primary" />
        </div>
        </form>
    </div>


    <div class="main-container mt-3 ms-3 me-3 mb-1">

      
        <br>
        <div class="status-box">
            <span class="available m-1">Available</span>
            <span class="reserved m-1">Reserved</span>
            <span class="occupied m-1">Occupied</span>
            <span class="not-available m-1">Not Available</span>
        </div>


        <div class="position-relative seat-map-container" style="width: 100%; max-width: 100vw; overflow-x: auto;">
            <!-- Scrollable Seat Map -->
            <center>
                <div class="seat-map mt-3 overflow-auto position-relative"
                    style="width: 1275px; height: 500px; max-height: 80vh; border: 1px solid #ccc; padding: 5px; position: relative; overflow: auto;">

                    <!-- Fully Scrollable Image -->
                    <img src="<?= base_url('assets_systems/uploaded_image.jpg') ?>"
                        alt="Seat Map"
                        class="img-fluid"
                        style="width: 1180px; height: auto; display: block; margin: auto;">
                </div>
            </center>
        </div>


    </div>

    <div class="container text-center">
        <h5 class="mt-3 mb-3"> Disable Seats</h5>
        <div class="seat-grid">

            <!-- Seat Selection Modal -->
            <div class="modal fade" id="seatModal" tabindex="-1" aria-labelledby="seatModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center custom-modal">
                        <div class="modal-body">
                            <p class="fw-bold fs-4">Select seat <span id="selectedSeat">#</span>?</p>
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" class="btn yes-btn" id="confirmSeat">YES</button>
                                <button type="button" class="btn no-btn" data-bs-dismiss="modal">NO</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container d-flex justify-content-center align-items-center mt-4 mb-3 gap-2">
            <a href="<?= base_url('admin/enable_seats') ?>" class="btn btn-success">
                Enable Disabled Seats
            </a>
            <a href="<?= base_url('admin/urgent_reservation') ?>" class="btn btn-warning">
                Urgent Reservation
            </a>
            &nbsp;&nbsp;
            <a href="<?= base_url('admin/enable_all_seats_view') ?>" class="btn btn-success">
                Enable Seats (All)
            </a>
            <a href="<?= base_url('admin/disable_all_seats_view') ?>" class="btn btn-danger">
                Disable Seats (All)
            </a>
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
            }, 60000); // 10000ms = 10 seconds

            const seatContainer = document.querySelector(".seat-grid");
            let selectedSeatNumber = null;
            let currentDateTime = new Date("<?php echo $current_date; ?>");
            let currentHours = currentDateTime.getHours();
            let currentMinutes = currentDateTime.getMinutes();
            let seatStatus = <?= json_encode($slot_display); ?>;


            // Create seats dynamically
            for (let i = 0; i < seatStatus.length; i++) {
                let seatButton = document.createElement("button");
                seatButton.className = `seat ${seatStatus[i]}`;
                seatButton.innerText = i + 1;
                seatButton.setAttribute("data-seat", i + 1);

                // if (seatStatus[i] === "available") {
                seatButton.addEventListener("click", function() {
                    selectedSeatNumber = i + 1;
                    document.getElementById("selectedSeat").innerText = selectedSeatNumber;
                    let modal = new bootstrap.Modal(document.getElementById("seatModal"));
                    modal.show();
                });
                // }

                seatContainer.appendChild(seatButton);
            }

            let selectedTimeOption = null; // Stores selected time option





            document.getElementById("confirmSeat").addEventListener("click", function() {
                let selectedSeat = document.getElementById("selectedSeat").innerText;

                if (selectedSeat) {
                    window.location.href = `<?= base_url('admin/disable_seat') ?>/${selectedSeat}`;
                }
            });





            //UserIconDropdown
            let userDropdown = document.getElementById("userDropdown");
            let dropdownMenu = userDropdown.nextElementSibling;

            document.addEventListener("mouseleave", function(event) {
                if (!userDropdown.contains(event.relatedTarget) && !dropdownMenu.contains(event.relatedTarget)) {
                    dropdownMenu.classList.remove("show");
                    userDropdown.setAttribute("aria-expanded", "false");
                }
            });

            document.getElementById("logoutBtn").addEventListener("click", function() {
                let logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
                logoutModal.show();
            });

            document.getElementById('confirmLogout').addEventListener('click', function() {
                window.location.href = "<?= base_url('admin/logout') ?>";
            });


        });
    </script>
</body>

</html>