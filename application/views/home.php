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
    /* width: 20px;
    height: 20px; */
    border-radius: 50%;
    display: inline-block;
    border: 2px solid black;
}
    


        .occupied {
            background-color: red;
            color: white;
            border: 2px solid black;
        }

        .closed {
            background-color: yellow;
            color: black;
            border: 2px dotted black;
            
        }
        .seats {
            background-color: lightgray;
            color: black;
        }
        .not-available {
            background-color: gray;
            color: white;
            border: 4px double black;
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

            .custom-modal-z {
                z-index: 9999 !important;
                /* Ensure it overrides others */
            }

            .modal-backdrop.show {
                z-index: 9998 !important;
            }
    </style>
</head>

<body>
    <div class="header d-flex align-items-center px-3">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">

        <!-- Right-side icons -->
        <div class="d-flex align-items-center ms-auto gap-3">
            <!-- Bell Icon Container -->
            <div class="position-relative d-inline-block"
                id="bell-wrapper"
                style="cursor: <?= !empty($seat_notification) ? 'pointer' : 'default' ?>;"
                <?= empty($seat_notification) ? 'onclick="return false;"' : '' ?>>

                <img src="<?= base_url('assets_systems/bell_icon.png') ?>" alt="Bell Logo" class="user-icon" id="bell-icon">

                <?php if (!empty($seat_notification)): ?>
                    <!-- Visible Badge -->
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge">
                        1
                        <span class="visually-hidden">unread notification</span>
                    </span>
                <?php endif; ?>
            </div>

            <?php if (!empty($seat_notification)): ?>
                <!-- Alert only added to DOM if needed -->
                <div class="alert alert-warning alert-dismissible fade show mt-3 d-none" role="alert" id="seat-alert">
                    <strong>Reminder:</strong> You have <?= $seat_notification['time_remaining'] ?> minutes left on Seat #<?= $seat_notification['seat_id'] ?>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <script>
                    document.getElementById('bell-wrapper').addEventListener('click', function() {
                        document.getElementById('seat-alert').classList.remove('d-none');
                        document.getElementById('notification-badge').style.display = 'none';
                    });
                </script>
            <?php endif; ?>

            <a href="<?= base_url('index/notification') ?>">
                <img src="<?= base_url('assets_systems/message_icon.png') ?>" alt="Message Logo" class="user-icon">
            </a>
            <div class="dropdown">
                <img src="<?= base_url('assets_systems/usericon.png') ?>" alt="User Icon" class="user-icon dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="<?= base_url('index/profile') ?>">Profile</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('index/reservationrequest') ?>">My Reservation</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" id="logoutBtn">Log Out</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade custom-modal-z" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
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


    <!-- <div style="text-align: center;">
        Last Update: &nbsp;<?= $current_date ?>

    </div> -->
    <div class="main-container mt-3 ms-3 me-3 mb-1">


      

        <center>
            <div class="position-relative seat-map-container" style="width: 100%; max-width: 100vw; overflow-x: auto;">
                <!-- Scrollable Seat Map -->
                <div class="seat-map mt-3 overflow-auto position-relative"
                    style="width: 1200px; height: 500px; max-height: 80vh; border: 1px solid #ccc; padding: 5px; position: relative; overflow: auto;">
                  
                    <img src="<?= base_url('assets_systems/uploaded_image.jpg') ?>"
                        alt="Seat Map"
                        class="img-fluid"
                        style="width: 1180px; height: auto; display: block; margin: auto;">
                </div>
            </div>
    </div>
     
    </center>
    <?php if (!(isset($is_professor) && $is_professor)): ?>
        <div class="container text-center">
            <h5 class="mt-3 mb-3">Select seat here...</h5>
             
        <div class="status-box">
            <span class="available m-1">Available ⬤</span>
            <span class="occupied m-1">Occupied ⬜</span>
                   <span class="closed m-1">Closed ⬜</span>
            <span class="not-available m-1">Not Available ⬜</span>
        </div>
                <br>
            <div class="seat-grid">

                <!-- Seat Selection Modal -->
                <div class="modal fade" id="seatModal" tabindex="-1" aria-labelledby="seatModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-center custom-modal">
                            <div class="modal-body">
                                <p class="fw-bold fs-4">Get seat <span id="selectedSeat">#</span>?</p>
                                <div class="d-flex justify-content-center gap-3">
                                    <button type="button" class="btn yes-btn" id="confirmSeat">YES</button>
                                    <button type="button" class="btn no-btn" data-bs-dismiss="modal">NO</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($is_professor) && $is_professor): ?>
        <div class="container d-flex justify-content-center align-items-center mt-4 mb-3">
            <a href="<?php echo base_url('index/class_reservation_request'); ?>" class="btn btn-success">
                Reserve a seat for a class
            </a>
        </div>
    <?php endif; ?>


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
            let seatStatus = <?= json_encode($seat_status); ?>;

            console.log(seatStatus);
            // Create seats dynamically
   // Sort the seats by 'id' ascending
seatStatus.sort((a, b) => a.id - b.id);
// Sort the seats by 'id' ascending
seatStatus.sort((a, b) => a.id - b.id);

for (let i = 0; i < seatStatus.length; i++) {
    let seatData = seatStatus[i];
    let reserveId = seatData.reserve_id;
    let seatId = seatData.id;

    console.log(`Seat ID: ${seatId}, Reserve ID: ${reserveId}`);

    // Determine class based on reserve_id value
    let seatClass = "available";
    let isDisabled = false;

    if (reserveId === "Closed") {
        seatClass = "closed";
        isDisabled = true;
    } else if (reserveId === "Occupied") {
        seatClass = "occupied";
        isDisabled = true;
    } else if (reserveId === null || reserveId === "null") {
        seatClass = "available";
    }

    // Create seat button
    let seatButton = document.createElement("button");
    seatButton.className = `seat ${seatClass}`;
    seatButton.innerText = seatId;
    seatButton.setAttribute("data-seat", seatId);

    // Disable if occupied or closed
    if (isDisabled) {
        seatButton.disabled = true;
    } else {
        // Click event for available seats
        seatButton.addEventListener("click", function () {
            selectedSeatNumber = seatId;
            document.getElementById("selectedSeat").innerText = selectedSeatNumber;
            let modal = new bootstrap.Modal(document.getElementById("seatModal"));
            modal.show();
        });
    }

    seatContainer.appendChild(seatButton);
}




            let selectedTimeOption = null; // Stores selected time option

            document.getElementById("confirmSeat").addEventListener("click", function() {
                let selectedSeat = document.getElementById("selectedSeat").innerText;

                const options = {
                    timeZone: 'Asia/Manila',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                };
                const formatter = new Intl.DateTimeFormat('en-CA', options); // en-CA gives YYYY-MM-DD format
                const [{
                    value: year
                }, , {
                    value: month
                }, , {
                    value: day
                }] = formatter.formatToParts(new Date());
                const currentDate = `${year}-${month}-${day} `;



                if (selectedSeatNumber !== null) {
                    // window.location.href = `<?= base_url('index/check_status') ?>/${selectedSeat}`;
                    window.location.href = `<?= base_url('index/reservation') ?>/${selectedSeat}`;
                    // console.log(currentDate);
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
        });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));

        document.getElementById("logoutBtn").addEventListener("click", () => {
            logoutModal.show();
        });

        document.getElementById("confirmLogout").addEventListener("click", () => {
            window.location.href = "<?= base_url('index/logout') ?>";
        });
    });
</script>
</body>

</html>