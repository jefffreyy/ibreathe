<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Monitoring Interface</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

        .form-container {
            background-color: #dce3ed;
            max-width: 1200px;
            background: #DBE4EE;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }

        a {
            color: black;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="header d-flex align-items-center">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        <h2 class="flex-grow-1 text-center"><b>Data Privacy Policy</b></h2>
        <div class="logo-placeholder"></div> <!-- Balancing the space -->
    </div>

    <div class="container">
        <div class="form-container">

            <p><strong>Privacy Notice</strong></p>

            <p>
                In Lyceum of the Philippines University we value your privacy and aim to uphold the same when processing your personal data.
            </p>

            <p>
                For purposes of fulfilling an academic requirements for a degree we may collect basic information about you such as your
                <strong>Name, Student Number, and Department</strong>.
            </p>

            <p>
                We are committed to protecting your personal data from loss, misuse, and any unauthorized processing activities, and will take
                all reasonable precautions to safeguard its security and confidentiality. Neither will we disclose, share, or transfer the
                same to any third party without your consent.
            </p>

            <p>
                Unless you agree to have us retain your personal data for the purposes stated above, your data will only be kept for a limited
                period as soon as the purpose for their use has been achieved after which, they will be disposed of in a safe and secure manner.
            </p>

            <p>
                You are also being informed that this online registration is being powered by jsdelivr bootstrap. To Report Abuse and to learn
                more about their Terms of Use please click the links below.
            </p>

            <p>
                This electronic form is being managed by the
                <strong>COECSA (College of Engineering, Computer Science and Architecture)</strong>. To contact the thesis owner, please send an email to:
                <a href="mailto:andrei.castro1@lpunetwork.edu.ph">andrei.castro1@lpunetwork.edu.ph</a>,
                <a href="mailto:shiela.beler@lpunetwork.edu.ph">shiela.beler@lpunetwork.edu.ph</a>,
                <a href="mailto:june.padrid@lpunetwork.edu.ph">june.padrid@lpunetwork.edu.ph</a>,
                <a href="mailto:ryzamae.saracho@lpunetwork.edu.ph">ryzamae.saracho@lpunetwork.edu.ph</a>
            </p>

            <p>
                We recognize your rights with respect to your personal data. Should you wish to exercise any of them or if you have any concerns
                regarding our processing activities, you may contact us at
                <a href="mailto:privacy.cavite@lpu.edu.ph">privacy.cavite@lpu.edu.ph</a>.
            </p>

            <p>Thank you.</p>
        </div>
        </form>

        <!-- Alert Modal -->
        <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Validation Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalMessage"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>


    <footer class="footer">
        <img src="<?= base_url('assets_systems/RS LOGO 2.png') ?>" alt="LPU Logo" class="footer-logo">
        <p class="footer-text">
            Copyright © Lyceum of the Philippines University - Cavite 2024.<br>
            All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>">Privacy Policy</a>
        </p>
        <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector("form").addEventListener("submit", function(event) {
                let student_id = document.getElementById("student_id").value.trim();
                let password = document.getElementById("password").value.trim();
                let confirmPassword = document.getElementById("re-password").value.trim();
                let email = document.getElementById("email").value.trim();
                let cellphone = document.getElementById("cellphone_num").value.trim();
                let errorMessage = "";

                if (student_id === "") {
                    errorMessage = "ID Number cannot be empty.";
                } else if (password === "") {
                    errorMessage = "Password cannot be empty.";
                } else if (password !== confirmPassword) {
                    errorMessage = "Passwords do not match.";
                } else if (email === "" || !email.includes("@")) {
                    errorMessage = "Please enter a valid email address.";
                } else if (cellphone === "" || cellphone.length < 10) {
                    errorMessage = "Please enter a valid cellphone number.";
                }

                if (errorMessage !== "") {
                    event.preventDefault(); // Prevent form submission
                    showAlert(errorMessage);
                }
            });

            function showAlert(message) {
                let modalMessage = document.getElementById("modalMessage");
                if (modalMessage) {
                    modalMessage.innerHTML = message;
                    let alertModal = new bootstrap.Modal(document.getElementById("alertModal"));
                    alertModal.show();
                } else {
                    alert(message);
                }
            }
        });
    </script>

</body>

</html>