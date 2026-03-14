<?php
$user_id        = $this->session->userdata('SESS_USER_ID');



$theme_icon     = $this->header_model->GET_MAYA_THEME();
$header         = $this->header_model->get_header_content();
$user_access_id = $this->header_model->get_user_access_id($this->session->userdata('SESS_USER_ID'));
$modules        = $this->header_model->get_user_access_modules($user_access_id['col_user_access']);

$notifications  = $this->header_model->get_user_notifications($user_id);



if ($notifications > 0 && $notifications < 100) {
    $notifications = $notifications;
} elseif ($notifications >= 100) {
    $notifications = '99+';
} else {
    $notifications = '';
}

$uploader_location  = 'uploaders/upload_file';
if ($theme_icon["value"] == 1) {
    $page_icon = "maiya_icon.ico";
    $page_title = "Maiya HRMS";
} else {
    $page_icon = "favicon.ico";
    $page_title = "Eyebox HRMS";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <link rel="shortcut icon" href="<?= base_url() ?>assets_system/images/<?= $page_icon ?>" type="image/x-icon">
    <title><?= $page_title ?></title>
    <?php $this->load->view('templates/css_link'); ?>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://technos-systems.com/_eyeboxroot/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="https://technos-systems.com/_eyeboxroot/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"><!-- Bootstrap -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://technos-systems.com/_eyeboxroot/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://technos-systems.com/_eyeboxroot/plugins/jquery/jquery.min.js"></script>
    <link href="https://technos-systems.com/_eyeboxroot/css/head_styles.css" rel="stylesheet">
    <!--for bootstrap select-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!---->
    <link href="https://releases.transloadit.com/uppy/v3.18.1/uppy.min.css" rel="stylesheet">
</head>

<style>
    .inactive {
        display: none;
    }

    .dropdown {
        cursor: pointer;
    }

    .numberCircle {
        border-radius: 50%;
        width: 25px;
        height: 25px;
        background: #FF335A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        font: 10px Arial, sans-serif;
    }

    .numberCircle span {
        justify-content: center;
        line-height: 27px;
    }

    .os-scrollbar-handle {
        background-color: #D4D1D1 !important;
    }

    .firebase_dropdown {
        transition: 0.5s;
        cursor: pointer;
    }

    .firebase_dropdown_open {
        transition: 0.5s;
        cursor: pointer;
    }

    .settings_button {
        transition: 0.4s;
    }

    .settings_button:hover {
        transition: 0.4s;
        -ms-transform: rotate(40deg);
        /* IE 9 */
        transform: rotate(80deg);
        border-radius: 50px;
    }

    @media only screen and (max-width: 1010px) {
        .navbar_full_width {
            display: none;
        }

        .profile_name {
            display: none;
        }
    }

    @media only screen and (max-width: 992px) {
        .sms_buttons {
            width: 100% !important;
            text-align: center !important;
            margin-top: 10px;
        }

        .sms_length {
            width: 100% !important;
            text-align: center !important;
            margin-top: 5px !important;
        }

        .sms_length_container {
            margin-top: 10px !important;
        }

        .quick_section_right {
            padding: 0px !important;
        }

        .message_counter {
            display: block;
        }

        .main-header.navbar.navbar-expand.navbar-white.navbar-light {
            height: 55px !important;
        }
    }

    @media only screen and (max-width: 500px) {
        #sidebar-overlay {
            z-index: 1000 !important;
        }

        #username_nav {
            display: none;
        }

        .dropdown-toggle {
            right: 8px;
            top: 20px;
            font-size: 29px;
        }

        .dropdown-menu {
            top: 13px !important;
            font-size: 18px !important;
        }

        .main-header.navbar.navbar-expand.navbar-white.navbar-light {
            height: 55px !important;
        }
    }

    .guides {
        padding: 10px;
    }

    .guides:hover {
        color: #0D74BC !important;
        background-color: #eaeaea !important;
        border-radius: 8px;
    }

    .spinner-border {
        width: 10px;
        height: 10px;
    }

    #btn_notif i:hover {
        color: #0D74BC !important;
    }

    .head-icon {
        padding-left: 5px;
    }

    li a i:not(.right) {
        margin-right: 0px !important;
    }

    a:hover.nav-link {
        color: #0f0f0f !important;
        box-shadow: none !important;
    }

    a:hover.nav-link.active {
        color: #0f0f0f !important;

        box-shadow: none !important;
    }

    .nav-link.active {
        background-color: #F2F2F2 !important;
        color: #0f0f0f !important;
        box-shadow: none !important;
    }

    .nav-link.active.firebase_dropdown_open {
        background-color: #E5E5E5 !important;
        color: #212529 !important;
    }

    .nav-link.active.firebase_dropdown {
        background-color: #E5E5E5 !important;
        color: #212529 !important;
    }

    .nav-link.main.active {
        color: #212529 !important;
    }

    .nav-link.main {
        color: #212529 !important;
    }

    .nav-link.main.active {
        color: #ffffff !important;
    }

    .nav-link.soon {
        color: #6c757d !important;
    }

    .nav-link.home {
        color: #212529 !important;
    }

    .nav-link.home.active {
        color: #ffffff !important;
    }

    @media screen and (min-width: 800px) {
        .mob-footer {
            display: none;
        }
    }

    a.emger:hover #top-header {
        background: #E6E6E6;
    }

    #header {
        background: none;
        padding-top: 0px;
        margin-top: 50px;
    }

    .em-standard {
        -webkit-box-align: start;
        -ms-flex-align: start;
        align-items: flex-start;
    }

    .em-featured {
        -webkit-box-align: start;
        -ms-flex-align: start;
        align-items: flex-start;
    }

    @media (max-width: 500px) {
        .em-featured {
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
        }
    }

    #top-header {
        background: white;
        position: fixed;
        width: 100%;
        z-index: 9000;
        left: 0;
        top: 0;
        border-top: 0;
    }

    #emergency-response-opt {
        max-width: 1140px;
        margin: 0 auto;
        padding: 10px 20px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        border-radius: 20px;
        font-family: 'arial';
    }

    #emergency-response-opt .txt {
        display: flex;
        align-items: center;
    }

    #emergency-response-opt p {
        font-size: 14px;
        margin-bottom: 0;
        color: #181818;
        line-height: 20px;
        margin-right: 10px;
    }

    #emergency-response-opt span {
        color: #E70052;
        font-weight: bold;
        border: 1px solid #E70052;
        padding: 5px 10px;
        border-radius: 8px;
        white-space: nowrap;
    }

    #emergency-response-opt p:last-of-type {
        margin-bottom: 0;
        padding-bottom: 0;
        margin-top: 0;
    }

    #emergency-response-opt .emergency_header {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
    }

    @media (max-width: 500px) {
        #emergency-response-opt .emergency_header {
            text-align: center;
        }
    }

    #emergency-response-opt img {
        width: 100%;
        height: auto;
    }

    #emergency-response-opt .emgergency-image {
        width: 35%;
        margin-right: 20px;
    }

    @media (max-width: 500px) {
        #emergency-response .emgergency-image {
            width: 100%;
            margin-bottom: 20px;
        }
    }

    #emergency-response-opt .txt.full {
        width: 65%;
    }

    @media (max-width: 500px) {
        #emergency-response .txt.full {
            width: 100%;
        }
    }

    #emergency-response-opt .btn {
        text-transform: uppercase;
        text-decoration: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: bold;
        letter-spacing: 1px;
        display: inline-block;
        margin-top: 15px;
    }

    #emergency-response-opt .btn-donate {
        color: white;
        background: #ff1d34;
    }

    #emergency-response-opt .circle {
        margin-right: 15px;
    }

    #emergency-response-opt .circle .circle-wrapper {
        height: 12px;
        width: 12px;
        background: #0C884A;
        border-radius: 50px;
        display: block;
    }

    .pulsate-fwd {
        -webkit-animation: pulsate-fwd 1.5s ease-in-out infinite both;
        animation: pulsate-fwd 1.5s ease-in-out infinite both;
    }

    @-webkit-keyframes pulsate-fwd {
        0% {
            -webkit-transform: scale(1);
            transform: scale(1);
        }

        50% {
            -webkit-transform: scale(2);
            transform: scale(2);
        }

        100% {
            -webkit-transform: scale(1.4);
            transform: scale(1.4);
        }
    }

    @keyframes pulsate-fwd {
        0% {
            -webkit-transform: scale(1);
            transform: scale(1);
            -webkit-box-shadow: 0 0 0 0 rgba(12, 136, 74, 0.6);
        }

        50% {
            -webkit-transform: scale(1.4);
            transform: scale(1.4);
            -webkit-box-shadow: 0 0 0 10px rgba(12, 136, 74, 0);
        }

        100% {
            -webkit-transform: scale(1);
            transform: scale(1);
            -webkit-box-shadow: 0 0 0 0 rgba(12, 136, 74, 0);
        }
    }

    .carousel-caption .btn-success {
        background: #0c884a;
    }

    @media (max-width: 767px) {
        .intro .jumbotron h1 {
            color: #e70052;
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        #emergency-response-opt p span {
            color: #E70052;
            font-weight: bold;
            border: 1px solid #E70052;
            padding: 4px 8px;
            border-radius: 8px;
            margin-top: 4px;
            display: inline-block;
        }

        #header {
            margin-top: 70px;
        }

        #emergency-response-opt p {
            font-size: 15px;
        }

        #emergency-response-opt {
            padding: 10px;
        }
    }

    @media (max-width: 450px) {
        #emergency-response-opt p {
            font-size: 11px;
        }

        #emergency-response-opt {
            padding: 10px 15px;
        }

        #header {
            margin-top: 70px;
        }
    }

    @media (max-width: 320px) {
        #header {
            margin-top: 90px;
        }
    }

    .img-circle_md {
        border-radius: 50% !important;
        width: 35px !important;
        height: 35px !important;
        object-fit: scale-down;
    }

    #badge {
        position: relative;
        display: inline-block;

    }

    #badge:after {
        content: attr(data-count);
        position: absolute;
        text-align: right;
        top: 3px;
        color: red;
        font-size: 14px;
    }
</style>
<?php
//get the notif count


//get the url
$url_count = $this->uri->total_segments();
$url_directory = $this->uri->segment($url_count);
$url_directory_sub = $this->uri->segment($url_count - 1);
$page_myaccount = false;
$page_employees = false;
$page_myaccount_list = array("my_payslips", "my_time_adjustment", "my_leave", "my_daily_time_record", "remote_attendance", "profile", "my_overtime", "tasks", "company_calendar", "knowledge_base");
$page_employees_list = array("employees");
foreach ($page_myaccount_list as $page) {
    $page_myaccount = $page_myaccount || ($url_directory == $page);
}
foreach ($page_employees_list as $page) {
    $page_employees = $page_employees || ($url_directory == $page);
}
$user_info = $this->login_model->get_user_info($this->session->userdata('SESS_USER_ID'));
$FirstName = '';
$LastName = '';
$Fullname = '';
$User_img = '';
$User_access = '';
$Username = '';
$Group = '';
$User_cmid = '';
$User_type = '';
$position = '';
$allowedRemoteAttendance = false;
foreach ($user_info as $info) {
    $User_img = $info->col_imag_path;
    $FirstName = $info->col_frst_name;
    $lastNameSuffix = $info->col_last_name;
    if ($info->col_suffix) {
        $lastNameSuffix = $info->col_last_name . ' ' . $info->col_suffix;
    }
    $Fullname = $lastNameSuffix . ', ' . $info->col_frst_name;
    if ($info->col_midl_name) {
        $Fullname = $Fullname . ' ' . substr($info->col_midl_name, 0, 1) . '.';
    }
    $User_access = $info->col_user_access;
    $Username = $info->col_user_name;
    $Group = $info->col_empl_group;
    $User_cmid = $info->col_empl_cmid;
    $User_type = $info->col_user_type;
    $position = $info->col_empl_posi;
    $allowedRemoteAttendance = $info->remote_att ? true : false;
}
$DISP_PAYROLL_SCHED = $this->login_model->mod_disp_pay_sched();
$db_cutoff_id = '';
$cutoff_period = '';
if ($DISP_PAYROLL_SCHED) {
    $isCutoff_today = false;
    foreach ($DISP_PAYROLL_SCHED as $DISP_PAYROLL_SCHED_ROW) {
        $current_date = date('Y-m-d');
        $start_date = $DISP_PAYROLL_SCHED_ROW->date_from;
        $end_date =  $DISP_PAYROLL_SCHED_ROW->date_to;
        $db_payout = $DISP_PAYROLL_SCHED_ROW->payout;
        $payout = date('F d Y', strtotime($db_payout));
        if (($current_date >= $start_date) && ($current_date <= $end_date)) {
            $cutoff_period = $DISP_PAYROLL_SCHED_ROW->db_name;
        } else {
        }
    }
}
$data['DISP_STATUS'] = $this->header_model->get_status();
$data['DISP_COMPANY_STATUS'] = $this->header_model->get_company_status();
$data['DISP_EMPLOYEE_STATUS'] = $this->header_model->get_employee_status();
$data['DISP_HR_STATUS'] = $this->header_model->get_hr_status();
$data['DISP_ATTENDANCE_STATUS'] = $this->header_model->get_attendance_status();
$data['DISP_LEAVE_STATUS'] = $this->header_model->get_leave_status();
$data['DISP_TEAM_STATUS']  =  $this->header_model->get_teams_status();
$data['DISP_OVERTIME_STATUS'] = $this->header_model->get_overtime_status();
$data['DISP_PAYROLL_STATUS'] = $this->header_model->get_payroll_status();
$data['DISP_REC_STATUS'] = $this->header_model->get_rec_status();
$data['DISP_LEARN_STATUS'] = $this->header_model->get_learn_status();
$data['DISP_PERFORMANCE_STATUS'] = $this->header_model->get_performance_status();
$data['DISP_REWARDS_STATUS'] = $this->header_model->get_rewards_status();
$data['DISP_EXIT_STATUS'] = $this->header_model->get_exit_status();
$data['DISP_ASSET_STATUS'] = $this->header_model->get_asset_status();
$data['DISP_PROJ_STATUS'] = $this->header_model->get_proj_status();
$data['DISP_ADMIN_STATUS'] = $this->header_model->get_admin_status();
$data['DISP_MESSAGING_STATUS'] = $this->header_model->get_messaging_status();
$data['DISP_REPORTS'] = $this->header_model->get_records_status();
$data['DISP_BENEFITS'] = $this->header_model->get_benefits_status();
$user_id = $this->header_model->get_sadmin_status($this->session->userdata('SESS_USER_ID'));
$DISP_LOGO = $this->header_model->get_logo();
$DISP_NAVBAR = $this->header_model->get_navbar();
$DISP_HEADER = $this->header_model->get_header();
?>


<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <!-- <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="padding: 0px; width: 100%;border-bottom: 1px solid #e2e2e2; position:fixed"> -->
    
    <aside class="main-sidebar sidebar-light-primary elevation-0 " style="border-right: 1px solid #EEEEEE;">
        <div class="<?= $this->session->userdata('SESS_ADMIN') ? '' : 'd-none' ?>" style="background-color: #FF0000;text-align:center;color:white">
            Super Administrator
        </div>
        <div class="<?= $this->session->userdata('SESS_ADMIN') ? '' : 'd-none' ?>" style="background-color: #FF7700;text-align:center;color:white">
            Administrator
        </div>

        <div class="sidebar">

            <div class="w-100 text-center pt-2">
                <img src="<?= base_url(); ?>assets_system/images/<?= $DISP_NAVBAR['value'] ?>" style="height: auto; width: 170px; !important; padding-top: 10px"> <!-- header_logo.png -->
            </div>
            <hr>
            <div class="border-bottom mt-3 pl-3 pb-3 mb-3 d-flex align-items-center">
                <div class="image position-relative">
                    <img class="img-circle_md elevation-2" src="<?= $this->system_functions->profileImageCheck('assets_user/user_profile/', $User_img) ?>">
                    <a href="<?= base_url('selfservices/notifications') ?>">
                        <div class="  p-0 m-0 position-absolute bg-danger rounded-circle <?= $notifications > 0 ? '' : 'd-none' ?>" style="top:-5px;right:-7px;width:20px;height:20px;font-size:10px;text-align:center">
                            <div style="padding-top:2px; ">
                                <?= $notifications ?>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="dropdown">
                    <a href="<?= base_url() ?>" class="ml-2 dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $Fullname ?>
                    </a>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="<?= site_url('selfservices/my_profile_personal') ?>">Profile</a>
                        <a class="dropdown-item" href="<?= site_url('selfservices/notifications') ?>">Notifications
                            <span class="badge badge-danger <?= $notifications > 0 ? '' : 'd-none' ?>"> <?= $notifications ?></span>
                        </a>
                        <a class="dropdown-item" href="<?= site_url('userguide') ?>">User Guide</a>
                        <a class="dropdown-item" href="<?= site_url('selfservices/change_password') ?>">Change Password</a>
                        <a class="dropdown-item" href="<?= site_url('login/sign_out') ?>">Logout</a>
                    </div>
                </div>

            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item nav-size">
                        <a href="<?= base_url(); ?>home" class='nav-link <?php if ($url_directory == 'home') {
                                                                                echo 'active';
                                                                            } ?>' style="width: 100% !important;">

                            <div>
                                <img src="<?= base_url('assets_system/icons/house-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Home</p>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_STATUS']['value'] == '0' || preg_match("/selfservices_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?> ">
                        <a href="<?= base_url() ?>selfservices" class='nav-link <?php
                                                                                if (($url_directory == 'selfservices')) {
                                                                                    echo 'active';
                                                                                } ?>' style="width: 100% !important;">

                            <div>
                                <img class="p-0 m-0" src="<?= base_url('assets_system/icons/hand-sparkles-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p class="p-0 m-0">&nbsp;Self-Service</p>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_COMPANY_STATUS']['value'] == '0' || preg_match("/company_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?> ">
                        <a href="<?= base_url() ?>companies" class="nav-link <?php if (($this->uri->segment(1) == 'companies')) {
                                                                                    echo 'active';
                                                                                } ?>">

                            <div>
                                <img src="<?= base_url('assets_system/icons/building-flag-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Company</p>
                            </div>
                        </a>

                    </li>

                    <li class="nav-item nav-size <?= $data['DISP_TEAM_STATUS']['value'] == '0' || preg_match("/team_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>   ">
                        <a href="<?= base_url('teams') ?>" class="nav-link <?php if (($this->uri->segment(1) == 'teams')) {
                                                                                echo 'active';
                                                                            } ?>">


                            <div>
                                <img src="<?= base_url('assets_system/icons/people-roof-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Teams</p>
                            </div>
                        </a>

                    </li>
                    <li class="<?= $data['DISP_TEAM_STATUS']['value'] == '0' || preg_match("/team_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>">
                        <hr>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_EMPLOYEE_STATUS']['value'] == '0' || preg_match("/employee_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>   ">
                        <a href="<?= base_url() ?>employees" class="nav-link <?php if (($this->uri->segment(1) == 'employees')) {
                                                                                    echo 'active';
                                                                                } ?>">

                            <div>
                                <img src="<?= base_url('assets_system/icons/users-duotone.svg') ?>" width=20px alt="house-solid" rel="preload" />
                                <p>&nbsp;Employees</p>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_ATTENDANCE_STATUS']['value'] == '0' || preg_match("/attendance_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>  ">
                        <a href="<?= base_url() ?>attendances" class="nav-link <?php if (($this->uri->segment(1) == 'attendances')) {
                                                                                    echo 'active';
                                                                                } ?>">


                            <div>
                                <img src="<?= base_url('assets_system/icons/business-time-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Time and Attendance</p>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item nav-size <?= $data['DISP_LEAVE_STATUS']['value'] == '0' || preg_match("/leave_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>   ">
                        <a href="<?= base_url() ?>leaves" class="nav-link <?php if (($this->uri->segment(1) == 'leaves')) {
                                                                                echo 'active';
                                                                            } ?>">


                            <div>
                                <img src="<?= base_url('assets_system/icons/house-person-leave-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Leaves</p>
                            </div>
                        </a>
                    </li>


                    <li class="nav-item nav-size <?= $data['DISP_OVERTIME_STATUS']['value'] == '0' || preg_match("/overtime_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>   ">
                        <a href="<?= base_url() ?>overtimes" class="nav-link <?php if (($this->uri->segment(1) == 'overtimes')) {
                                                                                    echo 'active';
                                                                                } ?>">

                            <div>
                                <img src="<?= base_url('assets_system/icons/clock-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Overtime</p>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_BENEFITS']['value'] == '0' || preg_match("/benefits_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>">
                        <a href="<?= base_url('benefits') ?>" class="nav-link <?php if (($this->uri->segment(1) == 'benefits')) {
                                                                                    echo 'active';
                                                                                } ?>">

                            <div>
                                <img src="<?= base_url('assets_system/icons/rectangle-history-circle-plus-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Earn/Deduct/Loan</p>
                            </div>
                        </a>

                    </li>
                    <li class="<?= $data['DISP_TEAM_STATUS']['value'] == '0' || preg_match("/team_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>">
                        <hr>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_PAYROLL_STATUS']['value'] == '0' || preg_match("/payroll_modules/i", $modules['user_modules']) == FALSE  ? 'inactive' : '' ?>  ">
                        <a href="<?= base_url() ?>payrolls" class="nav-link <?php if (($this->uri->segment(1) == 'payrolls')) {
                                                                                echo 'active';
                                                                            } ?>">

                            <div>
                                <img src="<?= base_url('assets_system/icons/money-from-bracket-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Payroll</p>
                            </div>
                        </a>

                    </li>
                    <li class="<?= $data['DISP_TEAM_STATUS']['value'] == '0' || preg_match("/team_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>">
                        <hr>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_HR_STATUS']['value'] == '0' || preg_match("/hr_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>  ">
                        <a href="<?= base_url() ?>hressentials" class="nav-link <?php if (($this->uri->segment(1) == 'hressentials')) {
                                                                                    echo 'active';
                                                                                } ?>">
                            <div>
                                <img src="<?= base_url('assets_system/icons/users-rectangle-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;HR Essentials</p>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_REC_STATUS']['value'] == '0' || preg_match("/recruitment_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>  ">
                        <a href="<?= base_url() ?>recruitments" class="nav-link <?php if (($this->uri->segment(1) == 'recruitments')) {
                                                                                    echo 'active';
                                                                                } ?>">
                            <i class="nav-icon fas fa-network-wired"></i>
                            <p>&nbsp;Recruitment</p>

                        </a>
                    </li>

                    <li class="nav-item nav-size <?= $data['DISP_PERFORMANCE_STATUS']['value'] == '0' || preg_match("/performance_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?> ">
                        <a href="<?= base_url() ?>performances" class="nav-link <?php if (($this->uri->segment(1) == 'performances')) {
                                                                                    echo 'active';
                                                                                } ?>">
                            <i class="nav-icon fas fa-star"></i>
                            <p>&nbsp;Performance</p>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_REWARDS_STATUS']['value'] == '0' || preg_match("/rewards_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>  ">
                        <a href="<?= base_url() ?>rewardsandrecognitions" class="nav-link <?php if (($this->uri->segment(1) == 'rewardsandrecognitions')) {
                                                                                                echo 'active';
                                                                                            } ?>">
                            <i class="nav-icon fas fa-award"></i>
                            <p>&nbsp;Rewards</p>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_EXIT_STATUS']['value'] == '0' || preg_match("/exist_management_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?> ">
                        <a href="<?= base_url() ?>exitmanagements" class="nav-link <?php if (($this->uri->segment(1) == 'exitmanagements')) {
                                                                                        echo 'active';
                                                                                    } ?>">
                            <i class="nav-icon fas fa-door-open"></i>
                            <p>&nbsp;Exit Management</p>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_ASSET_STATUS']['value'] == '0' || preg_match("/asset_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?> ">
                        <a href="<?= base_url() ?>assets" class="nav-link <?php if (($this->uri->segment(1) == 'assets')) {
                                                                                echo 'active';
                                                                            } ?>">
                            <img class="mb-1" src="<?= base_url('assets_system/icons/boxes.svg') ?>" width=24px alt="house-solid" />
                            <p>&nbsp;Asset</p>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_PROJ_STATUS']['value'] == '0' || preg_match("/project_management_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?> ">
                        <a href="<?= base_url() ?>projectmanagements" class="nav-link <?php if (($this->uri->segment(1) == 'projectmanagements')) {
                                                                                            echo 'active';
                                                                                        } ?>">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>&nbsp;Project Management</p>
                        </a>
                    </li>
                    <li class="nav-item nav-size <?= $data['DISP_REPORTS']['value'] == '0' || preg_match("/report_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?> ">
                        <a href="<?= base_url('reports') ?>" class="nav-link <?php if (($this->uri->segment(1) == 'reports')) {
                                                                                    echo 'active';
                                                                                } ?>">
                            <div>
                                <img src="<?= base_url('assets_system/icons/chart-pie-simple-circle-currency-duotone.svg') ?>" width=20px alt="house-solid" />
                                <p>&nbsp;Reports</p>
                            </div>
                        </a>

                    </li>
                    <li class="<?= $data['DISP_TEAM_STATUS']['value'] == '0' || preg_match("/team_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?>">
                        <hr>
                    </li>

                    <?php if (true) {       ?>
                        <li class="nav-item nav-size <?= $data['DISP_ADMIN_STATUS']['value'] == '0' || preg_match("/administrator_modules/i", $modules['user_modules']) == FALSE || $this->session->userdata('SESS_SPE_ACCOUNT') ? 'inactive' : '' ?> ">
                            <a href="<?= base_url() ?>administrators" class="nav-link <?php if (($this->uri->segment(1) == 'administrators')) {
                                                                                            echo 'active';
                                                                                        } ?>">
                                <div>
                                    <img src="<?= base_url('assets_system/icons/people-roof-duotone.svg') ?>" width=20px alt="house-solid" />
                                    <p>&nbsp;Administrator</p>
                                </div>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($this->session->userdata('SESS_ADMIN')) {       ?>
                        <li class="nav-item nav-size">
                            <a href="<?= base_url() ?>superadministrators" class="nav-link <?php if (($this->uri->segment(1) == 'superadministrators')) {
                                                                                                echo 'active';
                                                                                            } ?>">

                                <div>
                                    <img src="<?= base_url('assets_system/icons/mask-duotone.svg') ?>" width=20px alt="house-solid" />
                                    <p>&nbsp;Super Administrator</p>
                                </div>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>

        </div>
    </aside>


    <!-- </nav> -->
    <?php $this->load->view('templates/partials/_uploader_modal') ?>

</body>

</html>