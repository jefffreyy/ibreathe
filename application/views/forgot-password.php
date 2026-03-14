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

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form-container text-center">
            <h2 class="fw-bold text-dark">Forgot Password</h2>
            <br />
            <form action="<?php echo base_url('index/forgot_password'); ?>" method="post">
                <div class="mb-3 text-start">
                    <label for="student_id" class="form-label fw-bold">Student  ID Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter your Student ID" required>

                    <?php if (isset($_GET['error']) && !empty($_GET['error'])): ?>
                        <p class="text-danger"><?php echo htmlspecialchars($_GET['error']); ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn custom-btn w-100 fw-bold">Forgot Password</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (!document.referrer.includes(window.location.pathname)) {
            urlParams.delete('error');
            history.replaceState(null, '', window.location.pathname);
        }
    });
</script>


</body>

</html>