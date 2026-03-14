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
        }

        .occupied {
            background-color: red;
            color: white;
        }

        .reserved {
            background-color: yellow;
            color: black;
        }
  .walkin {
            background-color: blue;
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
    </style>
</head>

<body>
    <div class="header d-flex align-items-center px-3">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">

        <!-- Right-side icons -->
        <!-- <div class="d-flex align-items-center ms-auto gap-3"> -->
            <!-- <img src="<?= base_url('assets_systems/bell_icon.png') ?>" alt="Bell Logo" class="user-icon">
            <a href="<?= base_url('index/notification') ?>">
                <img src="<?= base_url('assets_systems/message_icon.png') ?>" alt="Message Logo" class="user-icon">
            </a> -->
            <!-- <div class="dropdown">
                <img src="<?= base_url('assets_systems/usericon.png') ?>" alt="User Icon" class="user-icon dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="<?= base_url('index/profile') ?>">Profile</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('index/reservationrequest') ?>">My Reservation</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="#" id="logoutBtn">Log Out</a></li>
                </ul>
            </div> -->
        <!-- </div> -->
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


  
    <div class="main-container p-0 mt-0 ms-03 me-0 mb-0">

        <strong>Current Timeslot: &nbsp;<?= $time_name ?></strong>
        <br>
        <div class="status-box">
         <span class="available m-1">Available ⬤</span>
                        <span class="walkin m-1">Walk-in ⬤</span>
            <span class="occupied m-1">Occupied ⬜</span>
                   <span class="closed m-1">Closed ⬜</span>
            <span class="not-available m-1">Not Available ⬜</span>
        </div>

 <center>
        <div class="position-relative seat-map-container" style="width: 100%; max-width: 100vw; overflow-x: auto;">
            <!-- Scrollable Seat Map -->
            <div class="seat-map mt-3 overflow-auto position-relative"
                style="width: 1200px; height: 500px; max-height: 80vh; border: 1px solid #ccc; padding: 5px; position: relative; overflow: auto;">
                <!-- Colored Labels (Moves with the Image) -->

                <div class="position-absolute small-label <?= $slot_display[0] ?>" data-seat="1" style="top: 35px; left: 1033px;">1</div>
                <div class="position-absolute small-label <?= $slot_display[1] ?>" data-seat="2" style="top: 78px; left: 1033px;">2</div>
                <div class="position-absolute small-label <?= $slot_display[2] ?>" data-seat="3" style="top: 121px; left: 1033px;">3</div>
                <div class="position-absolute small-label <?= $slot_display[3] ?>" data-seat="4" style="top: 163px; left: 1033px;">4</div>
                <div class="position-absolute small-label <?= $slot_display[4] ?>" data-seat="5" style="top: 207px; left: 1033px;">5</div>
                <div class="position-absolute small-label <?= $slot_display[5] ?>" data-seat="6" style="top: 252px; left: 1033px;">6</div>
                <div class="position-absolute small-label <?= $slot_display[6] ?>" data-seat="7" style="top: 295px; left: 1033px;">7</div>
                <div class="position-absolute small-label <?= $slot_display[7] ?>" data-seat="8" style="top: 338px; left: 1033px;">8</div>
                <div class="position-absolute small-label <?= $slot_display[8] ?>" data-seat="9" style="top: 381px; left: 1033px;">9</div>
                <div class="position-absolute small-label <?= $slot_display[9] ?>" data-seat="10" style="top: 425px; left: 1030px;">10</div>

                <div class="position-absolute small-label <?= $slot_display[19] ?>" data-seat="20" style="top: 35px; left: 920px;">20</div>
                <div class="position-absolute small-label <?= $slot_display[18] ?>" data-seat="19" style="top: 78px; left: 920px;">19</div>
                <div class="position-absolute small-label <?= $slot_display[17] ?>" data-seat="18" style="top: 121px; left: 920px;">18</div>
                <div class="position-absolute small-label <?= $slot_display[16] ?>" data-seat="17" style="top: 163px; left: 920px;">17</div>
                <div class="position-absolute small-label <?= $slot_display[15] ?>" data-seat="16" style="top: 207px; left: 920px;">16</div>
                <div class="position-absolute small-label <?= $slot_display[14] ?>" data-seat="15" style="top: 252px; left: 920px;">15</div>
                <div class="position-absolute small-label <?= $slot_display[13] ?>" data-seat="14" style="top: 295px; left: 920px;">14</div>
                <div class="position-absolute small-label <?= $slot_display[12] ?>" data-seat="13" style="top: 338px; left: 920px;">13</div>
                <div class="position-absolute small-label <?= $slot_display[11] ?>" data-seat="12" style="top: 381px; left: 920px;">12</div>
                <div class="position-absolute small-label <?= $slot_display[10] ?>" data-seat="11" style="top: 425px; left: 920px;">11</div>

                <div class="position-absolute small-label <?= $slot_display[32] ?>" data-seat="33" style="top: 50px; left: 330px;">33</div>
                <div class="position-absolute small-label <?= $slot_display[31] ?>" data-seat="32" style="top: 50px; left: 370px;">32</div>
                <div class="position-absolute small-label <?= $slot_display[30] ?>" data-seat="31" style="top: 50px; left: 410px;">31</div>
                <div class="position-absolute small-label <?= $slot_display[29] ?>" data-seat="30" style="top: 50px; left: 450px;">30</div>
                <div class="position-absolute small-label <?= $slot_display[28] ?>" data-seat="29" style="top: 50px; left: 490px;">29</div>
                <div class="position-absolute small-label <?= $slot_display[27] ?>" data-seat="28" style="top: 50px; left: 530px;">28</div>
                <div class="position-absolute small-label <?= $slot_display[26] ?>" data-seat="27" style="top: 50px; left: 570px;">27</div>
                <div class="position-absolute small-label <?= $slot_display[25] ?>" data-seat="26" style="top: 50px; left: 610px;">26</div>
                <div class="position-absolute small-label <?= $slot_display[24] ?>" data-seat="25" style="top: 50px; left: 650px;">25</div>
                <div class="position-absolute small-label <?= $slot_display[23] ?>" data-seat="24" style="top: 50px; left: 690px;">24</div>
                <div class="position-absolute small-label <?= $slot_display[22] ?>" data-seat="23" style="top: 50px; left: 730px;">23</div>
                <div class="position-absolute small-label <?= $slot_display[21] ?>" data-seat="22" style="top: 50px; left: 770px;">22</div>
                <div class="position-absolute small-label <?= $slot_display[20] ?>" data-seat="21" style="top: 50px; left: 810px;">21</div>

                <div class="position-absolute small-label <?= $slot_display[44] ?>" data-seat="45" style="top: 145px; left: 370px;">45</div>
                <div class="position-absolute small-label <?= $slot_display[43] ?>" data-seat="44" style="top: 145px; left: 410px;">44</div>
                <div class="position-absolute small-label <?= $slot_display[42] ?>" data-seat="43" style="top: 145px; left: 450px;">43</div>
                <div class="position-absolute small-label <?= $slot_display[41] ?>" data-seat="42" style="top: 145px; left: 490px;">42</div>
                <div class="position-absolute small-label <?= $slot_display[40] ?>" data-seat="41" style="top: 145px; left: 530px;">41</div>
                <div class="position-absolute small-label <?= $slot_display[39] ?>" data-seat="40" style="top: 145px; left: 570px;">40</div>
                <div class="position-absolute small-label <?= $slot_display[38] ?>" data-seat="39" style="top: 145px; left: 610px;">39</div>
                <div class="position-absolute small-label <?= $slot_display[37] ?>" data-seat="38" style="top: 145px; left: 650px;">38</div>
                <div class="position-absolute small-label <?= $slot_display[36] ?>" data-seat="37" style="top: 145px; left: 690px;">37</div>
                <div class="position-absolute small-label <?= $slot_display[35] ?>" data-seat="36" style="top: 145px; left: 730px;">36</div>
                <div class="position-absolute small-label <?= $slot_display[34] ?>" data-seat="35" style="top: 145px; left: 770px;">35</div>
                <div class="position-absolute small-label <?= $slot_display[33] ?>" data-seat="34" style="top: 145px; left: 810px;">34</div>

                <div class="position-absolute small-label <?= $slot_display[55] ?>" data-seat="56" style="top: 20px; left: 245px;">56</div>
                <div class="position-absolute small-label <?= $slot_display[54] ?>" data-seat="55" style="top: 63px; left: 245px;">55</div>
                <div class="position-absolute small-label <?= $slot_display[53] ?>" data-seat="54" style="top: 105px; left: 245px;">54</div>
                <div class="position-absolute small-label <?= $slot_display[52] ?>" data-seat="53" style="top: 147px; left: 245px;">53</div>
                <div class="position-absolute small-label <?= $slot_display[51] ?>" data-seat="52" style="top: 189px; left: 245px;">52</div>
                <div class="position-absolute small-label <?= $slot_display[50] ?>" data-seat="51" style="top: 231px; left: 245px;">51</div>
                <div class="position-absolute small-label <?= $slot_display[49] ?>" data-seat="50" style="top: 273px; left: 245px;">50</div>
                <div class="position-absolute small-label <?= $slot_display[48] ?>" data-seat="49" style="top: 315px; left: 245px;">49</div>
                <div class="position-absolute small-label <?= $slot_display[47] ?>" data-seat="48" style="top: 357px; left: 245px;">48</div>
                <div class="position-absolute small-label <?= $slot_display[46] ?>" data-seat="47" style="top: 399px; left: 245px;">47</div>
                <div class="position-absolute small-label <?= $slot_display[45] ?>" data-seat="46" style="top: 441px; left: 245px;">46</div>

                <div class="position-absolute small-label <?= $slot_display[66] ?>" data-seat="67" style="top: 20px; left: 130px;">67</div>
                <div class="position-absolute small-label <?= $slot_display[65] ?>" data-seat="66" style="top: 63px; left: 130px;">66</div>
                <div class="position-absolute small-label <?= $slot_display[64] ?>" data-seat="65" style="top: 105px; left: 130px;">65</div>
                <div class="position-absolute small-label <?= $slot_display[63] ?>" data-seat="64" style="top: 147px; left: 130px;">64</div>
                <div class="position-absolute small-label <?= $slot_display[62] ?>" data-seat="63" style="top: 189px; left: 130px;">63</div>
                <div class="position-absolute small-label <?= $slot_display[61] ?>" data-seat="62" style="top: 231px; left: 130px;">62</div>
                <div class="position-absolute small-label <?= $slot_display[60] ?>" data-seat="61" style="top: 273px; left: 130px;">61</div>
                <div class="position-absolute small-label <?= $slot_display[59] ?>" data-seat="60" style="top: 315px; left: 130px;">60</div>
                <div class="position-absolute small-label <?= $slot_display[58] ?>" data-seat="59" style="top: 357px; left: 130px;">59</div>
                <div class="position-absolute small-label <?= $slot_display[57] ?>" data-seat="58" style="top: 399px; left: 130px;">58</div>
                <div class="position-absolute small-label <?= $slot_display[56] ?>" data-seat="57" style="top: 441px; left: 130px;">57</div>

                <div class="position-absolute small-label <?= $slot_display[67] ?>" data-seat="68" style="top: 235px; left: 315px;">68</div>
                <div class="position-absolute small-label <?= $slot_display[68] ?>" data-seat="69" style="top: 300px; left: 415px;">69</div>
                <div class="position-absolute small-label <?= $slot_display[69] ?>" data-seat="70" style="top: 300px; left: 455px;">70</div>
                <div class="position-absolute small-label <?= $slot_display[70] ?>" data-seat="71" style="top: 260px; left: 455px;">71</div>
                <div class="position-absolute small-label <?= $slot_display[71] ?>" data-seat="72" style="top: 255px; left: 560px;">72</div>
                <div class="position-absolute small-label <?= $slot_display[72] ?>" data-seat="73" style="top: 255px; left: 605px;">73</div>

                <div class="position-absolute small-label <?= $slot_display[73] ?>" data-seat="74" style="top: 260px; left: 710px;">74</div>
                <div class="position-absolute small-label <?= $slot_display[74] ?>" data-seat="75" style="top: 300px; left: 710px;">75</div>
                <div class="position-absolute small-label <?= $slot_display[75] ?>" data-seat="76" style="top: 300px; left: 750px;">76</div>

                <div class="position-absolute small-label <?= $slot_display[76] ?>" data-seat="77" style="top: 235px; left: 850px;">77</div>
                <div class="position-absolute small-label <?= $slot_display[77] ?>" data-seat="78" style="top: 340px; left: 830px;">78</div>
                <div class="position-absolute small-label <?= $slot_display[78] ?>" data-seat="79" style="top: 395px; left: 760px;">79</div>
                <div class="position-absolute small-label <?= $slot_display[79] ?>" data-seat="80" style="top: 395px; left: 720px;">80</div>
                <div class="position-absolute small-label <?= $slot_display[80] ?>" data-seat="81" style="top: 355px; left: 645px;">81</div>
                <div class="position-absolute small-label <?= $slot_display[81] ?>" data-seat="82" style="top: 340px; left: 605px;">82</div>
                <div class="position-absolute small-label <?= $slot_display[82] ?>" data-seat="83" style="top: 340px; left: 560px;">83</div>
                <div class="position-absolute small-label <?= $slot_display[83] ?>" data-seat="84" style="top: 355px; left: 520px;">84</div>
                <div class="position-absolute small-label <?= $slot_display[84] ?>" data-seat="85" style="top: 395px; left: 445px;">85</div>
                <div class="position-absolute small-label <?= $slot_display[85] ?>" data-seat="86" style="top: 395px; left: 405px;">86</div>
                <div class="position-absolute small-label <?= $slot_display[86] ?>" data-seat="87" style="top: 340px; left: 330px;">87</div>

                <!-- Fully Scrollable Image -->
                <img src="<?= base_url('assets_systems/Picture2.png') ?>"
                    alt="Seat Map"
                    class="img-fluid"
                    style="width: 1180px; height: auto; display: block; margin: auto;">
            </div>
        </div>
    </div>
        </center>

   


    <footer class="footer mt-0">
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
                const currentDate = `${year}-${month}-${day}`;



                if (selectedSeatNumber !== null) {
                    // window.location.href = `<?= base_url('index/check_status') ?>/${selectedSeat}`;
                    window.location.href = `<?= base_url('index/check_status') ?>/${selectedSeat}/${currentDate}`;
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

            document.getElementById("logoutBtn").addEventListener("click", function() {
                let logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
                logoutModal.show();
            });

            document.getElementById('confirmLogout').addEventListener('click', function() {
                window.location.href = "<?= base_url('index/logout') ?>";
            });

        });
    </script>
</body>

</html>