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
            <?php if (isset($_GET['error'])): ?>
                <p class="text-danger"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>

            <?php if (isset($remaining_attempts) && $remaining_attempts > 0): ?>
                <p class="text-warning">You have <?= $remaining_attempts ?> attempt<?= $remaining_attempts == 1 ? '' : 's' ?> left before your account gets locked.</p>
            <?php endif; ?>

            <h2 class="fw-bold text-dark">Security Question</h2>
            <br />
            <form action="<?php echo base_url('index/secquestion_validate'); ?>" method="post">
                <input type="hidden" name="correct_answer" value="<?= htmlspecialchars($secanswer) ?>">
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">

                <div class="mb-3 text-start">
                    <label for="answer1" class="form-label fw-bold"><?= htmlspecialchars($secquestion) ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="answer1" name="answer1" placeholder="Answer" required>
                </div>

                <button type="submit" class="btn custom-btn w-100 fw-bold">Submit</button>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>




</body>

</html>