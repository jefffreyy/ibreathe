<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS (optional if you're already including it) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f5f5;
            padding: 40px 0;
        }

        .form-container {
            max-width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 12px;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .send-code-btn {
            white-space: nowrap;
        }

        .submit-btn {
            margin-top: 20px;
        }

        .otp-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 10px;
        }

        .otp-input {
            width: 45px;
            height: 45px;
            font-size: 24px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .otp-input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 3px rgba(0, 123, 255, 0.5);
        }

        .error-message {
            margin-top: 10px;
            color: red;
            font-weight: bold;
        }

        h4 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- FORM 1: Send Verification Code -->
<form method="post" action="<?php echo base_url('index/verify_email'); ?>">
    <div class="form-container">
        <h4>Enter your LPU E-Mail</h4>

        <!-- LPU Email -->
        <label class="form-label">LPU Email</label>
        <div class="input-group">
            <input type="email" name="email" class="form-control" placeholder="Type here..."
                <?= !empty($email) ? 'value="' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '"' : '' ?> required>
            <button type="submit" class="btn btn-primary send-code-btn">Send Code</button>
        </div>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</form>

<!-- FORM 2: Submit Verification Code -->
<form method="post" action="<?php echo base_url('index/registration'); ?>">
    <div class="form-container">
        <h4>Enter the Verification Code</h4>

        <!-- OTP Inputs -->
        <label class="form-label">Verification Code</label>
        <div class="otp-container">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
            <?php endfor; ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success form-control submit-btn">Submit</button>
    </div>
</form>

<!-- Auto-Focus JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = e.clipboardData.getData('text').replace(/\D/g, '');
                paste.split('').forEach((char, i) => {
                    if (inputs[i]) {
                        inputs[i].value = char;
                    }
                });
                const filled = Math.min(paste.length, inputs.length);
                if (filled < inputs.length) {
                    inputs[filled].focus();
                }
            });
        });
    });
</script>

</body>
</html>
