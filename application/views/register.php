<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBreathe - Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .fa, .fas, .far, .fal, .fad, .fab { font-family: 'Font Awesome 5 Free' !important; }
        .fab { font-family: 'Font Awesome 5 Brands' !important; }
        body {
            background: #f0f2f5;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            -webkit-font-smoothing: antialiased;
        }
        .register-wrapper { width: 100%; max-width: 460px; padding: 20px; }
        .register-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 40px 32px;
            box-shadow: 0 4px 24px rgba(0,0,0,.06), 0 1px 4px rgba(0,0,0,.04);
            border: 1px solid rgba(0,0,0,.04);
        }
        .brand-section { text-align: center; margin-bottom: 28px; }
        .brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
            font-size: 22px; color: #fff;
            box-shadow: 0 6px 16px rgba(99,102,241,.3);
        }
        .brand-title {
            color: #1e293b; font-size: 22px; font-weight: 800;
            letter-spacing: -0.3px;
        }
        .brand-subtitle {
            color: #94a3b8; font-size: 12px; font-weight: 500;
            margin-top: 2px;
        }

        .form-group { margin-bottom: 14px; }
        .form-group label {
            color: #334155; font-weight: 600; font-size: 13px;
            margin-bottom: 5px;
        }
        .form-group .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px; height: 46px;
            padding: 10px 16px; font-size: 14px;
            color: #334155; background: #f8fafc;
            transition: all .15s ease;
        }
        .form-group .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
            background: #fff;
        }
        .form-group .form-control::placeholder { color: #94a3b8; }

        .btn-register {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none; border-radius: 10px; color: #fff;
            font-weight: 600; font-size: 14px; letter-spacing: 0.3px;
            transition: all .2s ease; margin-top: 4px;
            box-shadow: 0 4px 14px rgba(99,102,241,.3);
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,.4);
            color: #fff;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }

        .validation-errors {
            background: #fef2f2; border: 1px solid #fecaca;
            color: #dc2626; border-radius: 10px; padding: 12px 16px;
            font-size: 13px; font-weight: 500; margin-bottom: 16px;
        }
        .validation-errors p { margin-bottom: 2px; }

        .login-link {
            text-align: center; margin-top: 20px;
            padding-top: 18px; border-top: 1px solid #f1f5f9;
        }
        .login-link a {
            color: #6366f1; text-decoration: none;
            font-size: 13px; font-weight: 500;
            transition: color .15s;
        }
        .login-link a:hover { color: #4f46e5; text-decoration: none; }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="register-card">
            <div class="brand-section">
                <div class="brand-icon"><i class="fas fa-wind"></i></div>
                <div class="brand-title">Create Account</div>
                <div class="brand-subtitle">iBreathe Air Quality Monitoring</div>
            </div>

            <?php if (validation_errors()): ?>
            <div class="validation-errors"><?= validation_errors() ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('scada/register') ?>">
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-id-card mr-1"></i>Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter your full name" value="<?= set_value('full_name') ?>" required>
                </div>
                <div class="form-group">
                    <label for="username"><i class="fas fa-user mr-1"></i>Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" value="<?= set_value('username') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope mr-1"></i>Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?= set_value('email') ?>" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock mr-1"></i>Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock mr-1"></i>Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn btn-register"><i class="fas fa-user-plus mr-2"></i>Register</button>
            </form>

            <div class="login-link">
                <a href="<?= base_url('scada/') ?>"><i class="fas fa-arrow-left mr-1"></i>Back to Login</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
