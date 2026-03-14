<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>QR Code</title>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
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

        .request-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: white;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        </div>

        <div class="container mt-3">
            <a href="<?= base_url('index/homepage') ?>" class="btn btn-secondary">← Back to home</a>
        </div>

        <main class="content">
            <form method="post" action="<?php echo base_url('index/send_prof_request'); ?>" class="request-container">

                <div class="container mt-4 mb-5 p-4 rounded border" style="max-width: 400px; background-color: #fff; border: 2px solid #000; border-radius: 15px;">
                    <h6 class="text-center mb-3 fw-bold">SEND A REQUEST</h6>

                    <input type="text" name="prof_name" class="form-control mb-3" value="<?php echo $professor_name; ?>" readonly style="background-color: #e6edf3; border: none; border-radius: 10px;">

                    <input type="text" name="prof_id" class="form-control mb-3" value="<?php echo $professor_id; ?>" readonly style="background-color: #e6edf3; border: none; border-radius: 10px;">

                    <div class="mb-3">
                        <div class="input-group" style="border: 1px solid #aaa; border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="date" class="form-control border-0" placeholder="Select date here..." style="box-shadow: none;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group" style="border: 1px solid #aaa; border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-clock"></i></span>
                            <select name="time_slot" class="form-control border-0" style="box-shadow: none;">
                                <option value="">Select time here...</option>
                                <?php if (isset($slots) && is_array($slots)): ?>
                                    <?php foreach ($slots as $slot): ?>
                                        <option value="<?php echo $slot['id']; ?>">
                                            <?php echo htmlspecialchars($slot['slot_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option disabled>No slots available</option>
                                <?php endif; ?>
                            </select>

                        </div>
                    </div>


                    <input type="number" name="num_of_seats" class="form-control mb-3" placeholder="Number of Seats" min="1" max="87" style="background-color: #e6edf3; border: none; border-radius: 10px;">

                    <textarea name="prof_note" class="form-control mb-3" rows="4" placeholder="Type here for a note..." style="background-color: #e6edf3; border: none; border-radius: 10px;"></textarea>

                    <div class="d-flex justify-content-between">
                        <button class="btn px-4" style="background-color: #e63900; color: white; border-radius: 5px;">Cancel</button>
                        <button type="submit" class="btn px-4" style="background-color: #00cc33; color: white; border-radius: 5px;">Send</button>
                    </div>
                </div>
            </form>
        </main>

        <footer class="footer">
            <img src="<?= base_url('assets_systems/RS LOGO.png') ?>" alt="LPU Logo" class="footer-logo">
            <p class="footer-text">
                Copyrights © Lyceum of the Philippines University - Cavite 2024.<br>
                All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>" class="footer-link">Privacy Policy</a>
            </p>
            <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector(".request-container");
        const dateInput = form.querySelector("input[type='date']");
        const timeSelect = form.querySelector("select[name='time_slot']");
        const seatsInput = form.querySelector("input[name='num_of_seats']");
        const noteTextarea = form.querySelector("textarea[name='prof_note']");

        // Set min date using local time (blocks past days like May 9)
        const now = new Date();
        const localToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const localTodayStr = localToday.getFullYear() + '-' +
            String(localToday.getMonth() + 1).padStart(2, '0') + '-' +
            String(localToday.getDate()).padStart(2, '0');
        dateInput.setAttribute("min", localTodayStr);

        // Disable Sundays
        dateInput.addEventListener("change", function() {
            const [year, month, day] = this.value.split("-").map(Number);
            const selectedDate = new Date(year, month - 1, day); // Local date

            if (selectedDate.getDay() === 0) {
                alert("Sunday reservations are not allowed.");
                this.value = "";
            } else {
                filterTimeSlots(selectedDate);
            }
        });

        function filterTimeSlots(selectedDate) {
            const isToday = selectedDate.toDateString() === new Date().toDateString();
            const currentTime = new Date();

            const options = timeSelect.querySelectorAll("option");
            options.forEach(option => {
                if (option.value === "") return;

                const text = option.textContent.trim();
                const match = text.match(/^(\d{1,2}:\d{2} (AM|PM))\s*-\s*(\d{1,2}:\d{2} (AM|PM))/i);
                if (!match) return;

                const slotStart = match[1];
                const slotEnd = match[3];

                const startDate = new Date(selectedDate);
                const endDate = new Date(selectedDate);

                let [startHour, startMinute] = slotStart.split(" ")[0].split(":").map(Number);
                const startMeridiem = slotStart.split(" ")[1].toUpperCase();
                if (startMeridiem === "PM" && startHour < 12) startHour += 12;
                if (startMeridiem === "AM" && startHour === 12) startHour = 0;
                startDate.setHours(startHour, startMinute, 0, 0);

                let [endHour, endMinute] = slotEnd.split(" ")[0].split(":").map(Number);
                const endMeridiem = slotEnd.split(" ")[1].toUpperCase();
                if (endMeridiem === "PM" && endHour < 12) endHour += 12;
                if (endMeridiem === "AM" && endHour === 12) endHour = 0;
                endDate.setHours(endHour, endMinute, 0, 0);

                if (!isToday || endDate > currentTime) {
                    option.style.display = "block";
                } else {
                    option.style.display = "none";
                }
            });
        }

        // Auto-correct seats > 87
        seatsInput.addEventListener("input", function() {
            let seats = parseInt(seatsInput.value, 10);
            if (seats > 87) {
                seatsInput.value = 87;
            }
        });

        // Validate form
        form.addEventListener("submit", function(e) {
            let valid = true;
            let messages = [];

            if (!dateInput.value) {
                valid = false;
                messages.push("Date is required.");
            } else {
                const [year, month, day] = dateInput.value.split("-").map(Number);
                const selected = new Date(year, month - 1, day);

                const todayOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

                if (selected < todayOnly) {
                    valid = false;
                    messages.push("Past dates are not allowed.");
                } else if (selected.getDay() === 0) {
                    valid = false;
                    messages.push("Sunday reservations are not allowed.");
                }
            }

            if (!timeSelect.value) {
                valid = false;
                messages.push("Please select a time.");
            }

            const seats = parseInt(seatsInput.value, 10);
            if (isNaN(seats) || seats <= 0) {
                valid = false;
                messages.push("Seats must be more than 0.");
            }

            if (!noteTextarea.value.trim()) {
                valid = false;
                messages.push("Please enter a note.");
            }

            if (!valid) {
                e.preventDefault();
                alert(messages.join("\n"));
            }
        });
    });
</script>



</body>

</html>