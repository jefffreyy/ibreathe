<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBreathe - Login</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
        }
        .login-wrapper {
            width: 100%; max-width: 420px; padding: 20px;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 44px 40px 36px;
            box-shadow: 0 4px 24px rgba(0,0,0,.06), 0 1px 4px rgba(0,0,0,.04);
            border: 1px solid rgba(0,0,0,.04);
        }
        .brand-section { text-align: center; margin-bottom: 36px; }
        .brand-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 26px; color: #fff;
            box-shadow: 0 8px 20px rgba(99,102,241,.3);
        }
        .brand-title {
            color: #1e293b; font-size: 26px; font-weight: 800;
            letter-spacing: -0.5px;
        }
        .brand-subtitle {
            color: #94a3b8; font-size: 13px; font-weight: 500;
            margin-top: 4px;
        }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            color: #334155; font-weight: 600; font-size: 13px;
            margin-bottom: 6px;
        }
        .form-group .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px; height: 48px;
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

        .btn-login {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none; border-radius: 10px; color: #fff;
            font-weight: 600; font-size: 14px; letter-spacing: 0.3px;
            transition: all .2s ease; margin-top: 4px;
            box-shadow: 0 4px 14px rgba(99,102,241,.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,.4);
            color: #fff;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }

        .alert-error {
            background: #fef2f2; border: 1px solid #fecaca;
            color: #dc2626; border-radius: 10px; padding: 12px 16px;
            font-size: 13px; font-weight: 500;
        }
        .alert-success-custom {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            color: #16a34a; border-radius: 10px; padding: 12px 16px;
            font-size: 13px; font-weight: 500;
        }

        .register-link {
            text-align: center; margin-top: 24px;
            padding-top: 20px; border-top: 1px solid #f1f5f9;
        }
        .register-link a {
            color: #6366f1; text-decoration: none;
            font-size: 13px; font-weight: 500;
            transition: color .15s;
        }
        .register-link a:hover { color: #4f46e5; text-decoration: none; }

        .status-bar {
            display: flex; justify-content: center; gap: 20px;
            margin-top: 16px; font-size: 11px; color: #94a3b8;
            font-weight: 500; letter-spacing: 0.5px;
        }
        .status-dot {
            width: 6px; height: 6px; background: #22c55e; border-radius: 50%;
            display: inline-block; margin-right: 5px;
            box-shadow: 0 0 6px rgba(34,197,94,.4);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-icon"><i class="fas fa-wind"></i></div>
                <div class="brand-title">iBreathe</div>
                <div class="brand-subtitle">Air Quality Monitoring System</div>
            </div>

            <?php if (!empty($error)): ?>
            <div class="alert alert-error mb-3"><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
            <div class="alert alert-success-custom mb-3"><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('scada/login') ?>">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user mr-1"></i>Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock mr-1"></i>Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-login"><i class="fas fa-sign-in-alt mr-2"></i>Sign In</button>
            </form>

            <div class="register-link">
                <a href="<?= base_url('scada/register') ?>">Don't have an account? <strong>Register here</strong></a>
            </div>
            <div class="status-bar">
                <span><span class="status-dot"></span>System Online</span>
                <span>v1.0.0</span>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
