<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Admin - Summary Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
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
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            height: 60px;
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
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <img src="<?= base_url('assets_systems/lpulogo.png') ?>" alt="LPU Logo" class="logo">
            <span>Summary Report</span>
            <div class="dropdown">
                <img src="<?= base_url('assets_systems/usericon.png') ?>" alt="User Icon" class="user-icon dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="<?= base_url('admin/reservation_request') ?>">Reservation Request</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/new_password') ?>">Reset Password of a user</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/summaryreport') ?>">Reports</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/admin_settings') ?>">Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" id="logoutBtn">Log Out</a></li>
                </ul>
            </div>
        </div>

        <main class="content">
            <div class="container mt-4">
                <div class="d-flex justify-content-between mb-3">
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">&larr; Back to Dashboard</a>

                    <form class="d-flex gap-2" method="get" action="">
                        <select class="form-select" name="filter_type" id="filter_type">
                            <option value="daily" <?= $filter_type == 'daily' ? 'selected' : '' ?>>Daily</option>
                            <option value="weekly" <?= $filter_type == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                            <option value="monthly" <?= $filter_type == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        </select>

                        <input type="date" class="form-control" name="filter_date" id="filter_daily"
                            value="<?= $filter_type == 'daily' ? $filter_date : '' ?>"
                            <?= $filter_type == 'daily' ? '' : 'style="display:none;"' ?>>

                        <input type="week" class="form-control" name="filter_weekly" id="filter_weekly"
                            value="<?= $filter_type == 'weekly' ? $filter_date : '' ?>"
                            <?= $filter_type == 'weekly' ? '' : 'style="display:none;"' ?>>

                        <input type="month" class="form-control" name="filter_monthly" id="filter_monthly"
                            value="<?= $filter_type == 'monthly' ? $filter_date : '' ?>"
                            <?= $filter_type == 'monthly' ? '' : 'style="display:none;"' ?>>

                        <select class="form-select" name="user_id">
                            <option value="">All Users</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user->id ?>" <?= $selected_user == $user->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </form>
                </div>

                <!-- Chart Card -->
                <div class="card shadow-sm p-3">
                    <canvas id="reservationChart" height="100"></canvas>
                </div>
            </div>
            <div class="container mt-4">
                  <h5>Top 10 Users This Month</h5>
                <div class="d-flex justify-content-between mb-3">
                  
                    <canvas id="topUsersChart" height="100"></canvas>
                </div>
            </div>
        </main>

        <footer class="footer mt-auto">
            <img src="<?= base_url('assets_systems/RS LOGO.png') ?>" alt="RS Logo" class="footer-logo">
            <p class="footer-text">
                Copyright © Lyceum of the Philippines University - Cavite 2024.<br>
                All Rights Reserved. <a href="<?= base_url('index/dataprivacypolicy') ?>" class="footer-link">Privacy Policy</a>
            </p>
            <a href="https://www.cavite.lpu.edu.ph" target="_blank" class="footer-link">www.cavite.lpu.edu.ph</a>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const reservationData = <?= json_encode($summary, JSON_HEX_TAG); ?>;
            const labels = reservationData.map(item => item.date_reserve);
            const dataCounts = reservationData.map(item => parseInt(item.reservation_count, 10));

            const ctx = document.getElementById('reservationChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reservation Count',
                        data: dataCounts,
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Filter input switch logic
            const typeSelect = document.getElementById('filter_type');
            const dailyInput = document.getElementById('filter_daily');
            const weeklyInput = document.getElementById('filter_weekly');
            const monthlyInput = document.getElementById('filter_monthly');

            function toggleDateInputs() {
                dailyInput.style.display = 'none';
                weeklyInput.style.display = 'none';
                monthlyInput.style.display = 'none';

                switch (typeSelect.value) {
                    case 'daily':
                        dailyInput.style.display = 'block';
                        break;
                    case 'weekly':
                        weeklyInput.style.display = 'block';
                        break;
                    case 'monthly':
                        monthlyInput.style.display = 'block';
                        break;
                }
            }

            typeSelect.addEventListener('change', toggleDateInputs);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Existing chart (reservationChart) stays as is...

            // Top 10 users chart logic (vertical bars)
            const topUsersData = <?= json_encode($top_users, JSON_HEX_TAG); ?>;
            const topUserNames = topUsersData.map(user => `${user.first_name} ${user.last_name}`);
            const topUserCounts = topUsersData.map(user => parseInt(user.reservation_count, 10));

            const ctx2 = document.getElementById('topUsersChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar', // vertical by default
                data: {
                    labels: topUserNames,
                    datasets: [{
                        label: 'Reservation Count',
                        data: topUserCounts,
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>

</body>

</html>