<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('scada_model');
        $this->load->model('device_model');
        $this->load->model('alarm_model');
        $this->load->helper('scada');
        if (!$this->session->userdata('logged_in')) redirect('scada/');
        if ($this->session->userdata('role') !== 'admin') show_error('Access denied. Admin privileges required.', 403);
    }

    public function index() {
        redirect('admin/users');
    }

    // ==================== USER MANAGEMENT ====================

    public function users() {
        $data['users'] = $this->scada_model->get_all_users();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Admin - Users';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('admin/users', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function create_user() {
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[tbl_scada_users.username]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[tbl_scada_users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run()) {
            $data = array(
                'username'  => $this->input->post('username', true),
                'email'     => $this->input->post('email', true),
                'password'  => $this->input->post('password'),
                'full_name' => $this->input->post('full_name', true),
                'role'      => $this->input->post('role', true),
                'status'    => 'active'
            );
            $this->scada_model->create_user($data);
            $this->scada_model->log_audit('user_created', "User '{$data['username']}' created");
            $this->session->set_flashdata('success', 'User created successfully.');
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        redirect('admin/users');
    }

    public function edit_user($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $update = array(
                'full_name' => $this->input->post('full_name', true),
                'email'     => $this->input->post('email', true),
                'role'      => $this->input->post('role', true),
                'status'    => $this->input->post('status', true)
            );
            $password = $this->input->post('password');
            if (!empty($password)) {
                $update['password'] = $password;
            }
            $this->scada_model->update_user($id, $update);
            $this->scada_model->log_audit('user_updated', "User #$id updated");
            $this->session->set_flashdata('success', 'User updated.');
            redirect('admin/users');
        }

        $data['edit_user'] = $this->scada_model->get_user($id);
        $data['users'] = $this->scada_model->get_all_users();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Admin - Edit User';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('admin/users', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function delete_user($id) {
        if ($id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot delete your own account.');
            redirect('admin/users');
        }
        $user = $this->scada_model->get_user($id);
        if ($user) {
            $this->scada_model->delete_user($id);
            $this->scada_model->log_audit('user_deleted', "User '{$user->username}' deleted");
            $this->session->set_flashdata('success', 'User deleted.');
        }
        redirect('admin/users');
    }

    // ==================== SETTINGS ====================

    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keys = $this->input->post('setting_key');
            $values = $this->input->post('setting_value');
            if ($keys && $values) {
                foreach ($keys as $i => $key) {
                    $this->scada_model->update_setting($key, $values[$i]);
                }
            }
            $this->scada_model->log_audit('settings_updated', 'System settings updated');
            $this->session->set_flashdata('success', 'Settings saved.');
            redirect('admin/settings');
        }

        $data['settings'] = $this->scada_model->get_settings();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Admin - Settings';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('admin/settings', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    // ==================== AUDIT LOG ====================

    public function audit_log() {
        $filters = array(
            'date_from' => $this->input->get('date_from'),
            'date_to'   => $this->input->get('date_to'),
            'action'    => $this->input->get('action')
        );
        $data['logs'] = $this->scada_model->get_audit_logs(200, 0, $filters);
        $data['filters'] = $filters;
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Admin - Audit Log';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('admin/audit_log', $data);
        $this->load->view('templates/scada_footer', $data);
    }
}
