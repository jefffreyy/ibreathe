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
    </style>
</head>

<body>
    <div class="header d-flex align-items-center">
        <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
        <h2 class="flex-grow-1 text-center"><b>Registration</b></h2>
        <div class="logo-placeholder"></div> <!-- Balancing the space -->
    </div>

    <div class="container">
        <div class="form-container">
            <h4 class="mb-4"><b>Personal Data</b></h4>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo base_url() . 'index/registerUser' ?>" method="post">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="lname" class="form-label"><b>Last Name</b></label>
                        <input id="lname" name="lname" class="form-control" placeholder="Type here">
                    </div>
                    <div class="col-md-4">
                        <label for="fname" class="form-label"><b>First Name</b></label>
                        <input id="fname" name="fname" type="text" class="form-control" placeholder="Type here">
                    </div>
                    <div class="col-md-4">
                        <label for="mname" class="form-label"><b>Middle Name</b></label>
                        <input id="mname" name="mname" type="text" class="form-control" placeholder="Type here">
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label"><b>Role</b></label><br>
                    <input type="radio" name="role" value="Student" class="form-check-input"> <b>Student</b>
                    <input type="radio" name="role" value="Professor" class="form-check-input ms-3"> <b>Professor</b>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="student_id" class="form-label"><b>ID Number</b> (This will be your username)</label>
                        <input id="student_id" name="student_id" type="text" class="form-control" placeholder="Type here">
                    </div>
                    <div class="col-md-4">
                        <label for="password" class="form-label"><b>Password</b>
                            <small class="text-muted">(password must contain a special character)</small>
                        </label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Type here">
                    </div>
                    <div class="col-md-4">
                        <label for="re-password" class="form-label"><b>Confirm Password</b></label>
                        <input id="re-password" name="re_password" type="password" class="form-control" placeholder="Type here">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="security_question" class="form-label"><b>Security Question</b></label>
                        <select class="form-select" name="security_question" id="security_question">
                            <option value="">Select here</option>
                            <option value="pet">What is your pet’s name?</option>
                            <option value="birthcity">In what city were you born?</option>
                            <option value="school">What was your elementary school’s name?</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="security_answer" class="form-label"><b>Security Answer</b>
                            <small class="text-muted">(Have at least 4 characters)</small>
                        </label>
                        <input id="security_answer" name="security_answer" type="text" class="form-control" placeholder="Type here">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="department" class="form-label"><b>Department</b></label>
                        <select class="form-select" name="department" id="department">
                            <option value="">Select here</option>
                            <option value="COECSA">COECSA</option>
                            <option value="CAMS">CAMS</option>
                            <option value="CON">CON</option>
                            <option value="CITHM">CITHM</option>
                            <option value="CBA">CBA</option>
                            <option value="CFAD">CFAD</option>
                            <option value="CLAE">CLAE</option>
                            <option value="IHS">IHS</option>
                            <option value="GS">GS</option>
                        </select>
                    </div>  
                </div>

                <div class="mt-4 mb-3">
                    <p style="font-size: 14px; text-align: center;">
                        By choosing "<b>I Agree</b>" and clicking the "<b>Register</b>" button below, I hereby acknowledge and certify that I <br>
                        have carefully read and understood the Terms and Conditions of the <a href="<?= base_url('index/dataprivacypolicy') ?>"><b><u>Data Privacy Policy/Notice</a></u></b> of the <br>
                        Lyceum of the Philippine University (LPU). By providing personal information to LPU, I am confirming that<br>
                        the data is true and correct. I understand that LPU reserves the right to revise any decision made on the<br>
                        basis of the information I provided should the information be found to be untrue or incorrect. I likewise<br>
                        agree that any issue that may arise in connection with the processing of my personal information will be<br>
                        settled amicably with LPU before resorting to appropriate arbitration or court proceedings within the <br>
                        Philippine jurisdiction. Finally, I am providing my voluntary consent and authorization to LPU and its<br>
                        authorized representatives to lawfully process my data/information.
                    </p>

                    <div style="text-align: center;">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="agreement" id="agree" value="agree" required>
                            <label class="form-check-label" for="agree"><b>I Agree</b></label>
                        </div>
                        <div class="form-check form-check-inline ms-3">
                            <input class="form-check-input" type="radio" name="agreement" id="disagree" value="disagree" required>
                            <label class="form-check-label" for="disagree"><b>I Disagree</b></label>
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success">Register</button>
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