<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
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
            margin: 20px;
            /* Adds space on all sides */
            border-radius: 10px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: calc(100% - 40px);
            /* Account for horizontal margin to avoid overflow */
            height: calc(100% - 40px);
            /* Optional: for full-page height minus margin */
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        <span>Password Reset</span>
        <div class="dropdown">
            <img src="<?= base_url('assets_systems/usericon.png') ?>" alt="User Icon" class="user-icon dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item text-danger" id="logoutBtn">Log Out</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/reservation_request') ?>">Reservation Request</a></li>
            </ul>
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

    </div>
    <div class="main-container d-flex justify-content-center align-items-center">
        <form class="w-100" style="max-width: 400px;" method="post" action="<?= site_url('admin/update_credentials') ?>">
            <div class="mb-3">
                <label for="newPassword" class="form-label fw-bold">New Password</label>
                <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="Type here" required>
            </div>

            <div class="mb-3">
                <label for="confirmPassword" class="form-label fw-bold">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Type here" required>
            </div>

            <div class="mb-3">
                <label for="securityQuestion" class="form-label fw-bold">New Security Question</label>
                <select id="securityQuestion" name="securityQuestion" class="form-select" required>
                    <option selected disabled>Select here</option>
                    <option value="HERO">What is the name of your childhood hero?</option>
                    <option value="CITY">In what city or town did your parents meet?</option>
                    <option value="SONG">What is your favorite song?</option>
                    <option value="RESTAURANT">What is your favorite restaurant?</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="securityAnswer" class="form-label fw-bold">New Security Answer</label>
                <input type="text" id="securityAnswer" name="securityAnswer" class="form-control" placeholder="Type here" required>
            </div>

            <div class="mb-3">
                <label for="confirmSecurityAnswer" class="form-label fw-bold">Confirm New Security Answer</label>
                <input type="text" id="confirmSecurityAnswer" name="confirmSecurityAnswer" class="form-control" placeholder="Type here" required>
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-success px-4 py-2">Submit</button>
            </div>
        </form>
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
            // Dropdown logic remains the same
            let userDropdown = document.getElementById("userDropdown");
            let dropdownMenu = userDropdown?.nextElementSibling;

            document.addEventListener("mouseleave", function(event) {
                if (userDropdown && !userDropdown.contains(event.relatedTarget) && !dropdownMenu.contains(event.relatedTarget)) {
                    dropdownMenu.classList.remove("show");
                    userDropdown.setAttribute("aria-expanded", "false");
                }
            });

            document.getElementById("logoutBtn")?.addEventListener("click", function() {
                let logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
                logoutModal.show();
            });

            document.getElementById('confirmLogout')?.addEventListener('click', function() {
                window.location.href = "<?= base_url('admin/logout') ?>";
            });

            
            const form = document.querySelector("form");
            form.addEventListener("submit", function(e) {
                const password = document.getElementById("newPassword").value;
                const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

                if (!specialCharRegex.test(password)) {
                    e.preventDefault();
                    alert("Password must contain at least one special character.");
                }
            });
        });
    </script>

</body>

</html>