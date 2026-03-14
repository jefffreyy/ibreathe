<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Scada extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('scada_model');
        $this->load->model('device_model');
        $this->load->model('reading_model');
        $this->load->model('alarm_model');
    }

    // ==================== AUTH ====================

    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect('scada/dashboard');
        }
        $data['error'] = $this->session->flashdata('error');
        $data['success'] = $this->session->flashdata('success');
        $this->load->view('login', $data);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('scada/');
        }

        $username = $this->input->post('username', true);
        $password = $this->input->post('password');

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Please fill in all fields.');
            redirect('scada/');
        }

        $user = $this->scada_model->check_login($username, $password);
        if ($user) {
            $session_data = array(
                'user_id'   => $user->id,
                'username'  => $user->username,
                'full_name' => $user->full_name,
                'role'      => $user->role,
                'logged_in' => true
            );
            $this->session->set_userdata($session_data);
            $this->scada_model->log_audit('login', 'User logged in');
            redirect('scada/dashboard');
        } else {
            $this->session->set_flashdata('error', 'Invalid username or password.');
            redirect('scada/');
        }
    }

    public function logout() {
        $this->scada_model->log_audit('logout', 'User logged out');
        $this->session->sess_destroy();
        redirect('scada/');
    }

    public function register() {
        if ($this->session->userdata('logged_in')) {
            redirect('scada/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[3]|max_length[50]|is_unique[tbl_scada_users.username]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[tbl_scada_users.email]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
            $this->form_validation->set_rules('full_name', 'Full Name', 'required');

            if ($this->form_validation->run()) {
                $data = array(
                    'username'  => $this->input->post('username', true),
                    'email'     => $this->input->post('email', true),
                    'password'  => $this->input->post('password'),
                    'full_name' => $this->input->post('full_name', true),
                    'role'      => 'user'
                );
                $this->scada_model->create_user($data);
                $this->session->set_flashdata('success', 'Registration successful! Please log in.');
                redirect('scada/');
            }
        }

        $this->load->view('register');
    }

    // ==================== DASHBOARD ====================

    public function dashboard() {
        $this->_require_login();

        // Mark offline devices
        $timeout = $this->scada_model->get_setting('device_timeout_minutes') ?: 5;
       

        $data['devices'] = $devices = $this->device_model->get_devices();
        
        foreach($devices as $device) {
            $last_reading = $this->reading_model->get_last_reading($device->id);
          
            if($last_reading) {
                $recorded_time = strtotime($last_reading->recorded_at);
                $current_time = time();
                $time_difference = $current_time - $recorded_time;
                
                // Check kung less than 30 seconds
                if($time_difference < 30) {
                    $this->device_model->update_last_seen($device->id, null);
                } else {
                    $this->device_model->update_last_seen_offline($device->id, null);
                }
            }
        }
        $this->device_model->mark_offline_devices($timeout);
        $data['device_summary'] = $this->device_model->get_device_status_summary();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['total_devices'] = $this->device_model->count_devices();
        $data['polling_interval'] = ($this->scada_model->get_setting('polling_interval') ?: 5) * 1000;
        $data['page_title'] = 'Dashboard';
        $data['page_js'] = 'floorplan.js';

        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('floorplan', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    // ==================== PROFILE ====================

    public function profile() {
        $this->_require_login();
        $data['user'] = $this->scada_model->get_user($this->session->userdata('user_id'));
        $data['page_title'] = 'Profile';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $update = array(
                'full_name' => $this->input->post('full_name', true),
                'email'     => $this->input->post('email', true)
            );
            $password = $this->input->post('password');
            if (!empty($password)) {
                $update['password'] = $password;
            }
            $this->scada_model->update_user($this->session->userdata('user_id'), $update);
            $this->session->set_flashdata('success', 'Profile updated successfully.');
            redirect('scada/profile');
        }

        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('profile', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    // ==================== FLOOR PLAN ====================

    public function floorplan() {
        redirect('scada/dashboard');
    }

    // ==================== HELPERS ====================

    private function _require_login() {
        if (!$this->session->userdata('logged_in')) {
            redirect('scada/');
        }
    }

    private function _require_admin() {
        $this->_require_login();
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Access denied. Administrator privileges required.', 403);
        }
    }
}
