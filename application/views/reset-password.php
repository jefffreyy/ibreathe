<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            background-color: #dce3ed;
            max-width: 1200px;
            background: #DBE4EE;
            padding: 70px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }

        .custom-btn {
            background-color: #880007;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .custom-btn:hover {
            background-color: #6b0005;
            color: white;
        }
    </style>
</head>

<body>

    <?php
    $student_id = isset($_GET['student_id']) ? htmlspecialchars($_GET['student_id']) : '';
    ?>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form-container text-center">
            <h2 class="fw-bold text-dark">Reset Password</h2>
            <br />
            <form action="<?php echo base_url('index/reset_password'); ?>" method="post">
                <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">

                <div class="mb-3 text-start">
                    <label for="password" class="form-label fw-bold">New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="confirm_password" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                </div>
                <?php if (isset($_GET['error'])): ?>
                    <p class="text-danger"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
                <button type="submit" class="btn custom-btn w-100 fw-bold">Reset Password</button>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            form.addEventListener("submit", function(e) {
                const password = document.getElementById("password").value;
                const confirmPassword = document.getElementById("confirm_password").value;
                const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

                if (!specialCharRegex.test(password)) {
                    e.preventDefault();
                    alert("Password must contain at least one special character.");
                    return;
                }

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert("Passwords do not match.");
                    return;
                }
            });
        });
    </script>

</body>

</html>