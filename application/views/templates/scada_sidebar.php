    <aside class="main-sidebar elevation-4 sidebar-light-primary">
        <a href="<?= base_url('scada/dashboard') ?>" class="brand-link border-bottom-0">
            <div class="brand-logo-wrap">
                <div class="brand-logo-icon"><i class="fas fa-wind"></i></div>
                <span class="brand-text font-weight-bold">iBreathe</span>
            </div>
        </a>
        <div class="sidebar">
            <nav class="mt-3">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="<?= base_url('scada/dashboard') ?>" class="nav-link <?= (isset($page_title) && $page_title == 'Dashboard') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-th-large"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('devices') ?>" class="nav-link <?= (isset($page_title) && strpos($page_title, 'Device') !== false) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-microchip"></i>
                            <p>Devices</p>
                        </a>
                    </li>
                    <li class="nav-item <?= (isset($page_title) && strpos($page_title, 'Alarm') !== false) ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= (isset($page_title) && strpos($page_title, 'Alarm') !== false) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Alarms <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('alarms') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Active Alarms</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('alarms/rules') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Alarm Rules</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('alarms/history') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Alarm History</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item <?= (isset($page_title) && strpos($page_title, 'Report') !== false) ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= (isset($page_title) && strpos($page_title, 'Report') !== false) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Reports <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('reports/trends') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Trends</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('reports/summary') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Summary</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('reports/analytics') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Analytics</p>
                                </a>
                            </li>
                            <!--<li class="nav-item">-->
                            <!--    <a href="<?= base_url('reports/predictive') ?>" class="nav-link">-->
                            <!--        <i class="far fa-dot-circle nav-icon"></i>-->
                            <!--        <p>Predictive</p>-->
                            <!--    </a>-->
                            <!--</li>-->
                        </ul>
                    </li>
                    <?php if ($this->session->userdata('role') === 'admin'): ?>
                    <li class="nav-header">ADMINISTRATION</li>
                    <li class="nav-item <?= (isset($page_title) && strpos($page_title, 'Admin') !== false) ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= (isset($page_title) && strpos($page_title, 'Admin') !== false) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-sliders-h"></i>
                            <p>Admin <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/users') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/settings') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Settings</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/audit_log') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Audit Log</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </aside>
    <div class="content-wrapper">
