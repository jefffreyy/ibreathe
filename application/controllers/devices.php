<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Devices extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('device_model');
        $this->load->model('reading_model');
        $this->load->model('alarm_model');
        $this->load->model('scada_model');
        $this->load->helper('scada');
        $this->_require_login();
    }

    public function index() {
        $data['devices'] = $this->device_model->get_devices();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Devices';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('devices/index', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function add() {
        $this->_require_admin();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Add Device';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('devices/add', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function create() {
        $this->_require_admin();
        $this->form_validation->set_rules('name', 'Device Name', 'required');
        $this->form_validation->set_rules('location', 'Location', 'required');

        if ($this->form_validation->run()) {
            $data = array(
                'name'       => $this->input->post('name', true),
                'location'   => $this->input->post('location', true),
                'type'       => $this->input->post('type', true) ?: 'air_quality_monitor',
                'created_by' => $this->session->userdata('user_id')
            );
            $device_id = $this->device_model->add_device($data);
            $device = $this->device_model->get_device($device_id);
            $this->scada_model->log_audit('device_created', "Device '{$data['name']}' created. Key: {$device->device_key}");
            $this->session->set_flashdata('success', "Device created! Device Key: <strong>{$device->device_key}</strong> (save this key for your IoT device)");
            redirect('devices');
        } else {
            $this->add();
        }
    }

    public function detail($id) {
        $data['device'] = $this->device_model->get_device($id);
        if (!$data['device']) show_404();

        $data['sensors'] = $this->device_model->get_device_sensors($id);
        $data['latest_readings'] = $this->reading_model->get_latest_readings($id);
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Device Detail';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('devices/detail', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function edit($id) {
        $this->_require_admin();
        $data['device'] = $this->device_model->get_device($id);
        if (!$data['device']) show_404();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $update = array(
                'name'     => $this->input->post('name', true),
                'location' => $this->input->post('location', true),
                'type'     => $this->input->post('type', true),
                'status'   => $this->input->post('status', true)
            );
            $this->device_model->update_device($id, $update);
            $this->scada_model->log_audit('device_updated', "Device #$id updated");
            $this->session->set_flashdata('success', 'Device updated successfully.');
            redirect('devices/detail/' . $id);
        }

        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Edit Device';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('devices/add', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function delete($id) {
        $this->_require_admin();
        $device = $this->device_model->get_device($id);
        if ($device) {
            $this->device_model->delete_device($id);
            $this->scada_model->log_audit('device_deleted', "Device '{$device->name}' deleted");
            $this->session->set_flashdata('success', 'Device deleted.');
        }
        redirect('devices');
    }

    private function _require_login() {
        if (!$this->session->userdata('logged_in')) redirect('scada/');
    }

    private function _require_admin() {
        if ($this->session->userdata('role') !== 'admin') show_error('Access denied.', 403);
    }
}
