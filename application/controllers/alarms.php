<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Alarms extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('alarm_model');
        $this->load->model('device_model');
        $this->load->model('scada_model');
        $this->load->helper('scada');
        if (!$this->session->userdata('logged_in')) redirect('scada/');
    }

    public function index() {
        $data['events'] = $this->alarm_model->get_active_events();
        $data['active_alarms'] = count($data['events']);
        $data['page_title'] = 'Active Alarms';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('alarms/index', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function acknowledge($event_id) {
        $this->alarm_model->acknowledge_event($event_id, $this->session->userdata('user_id'));
        $this->scada_model->log_audit('alarm_acknowledged', "Alarm event #$event_id acknowledged");
        $this->session->set_flashdata('success', 'Alarm acknowledged.');
        redirect('alarms');
    }

    public function acknowledge_all() {
        $this->alarm_model->acknowledge_all($this->session->userdata('user_id'));
        $this->scada_model->log_audit('alarms_acknowledged_all', 'All alarms acknowledged');
        $this->session->set_flashdata('success', 'All alarms acknowledged.');
        redirect('alarms');
    }

    public function rules() {
        $data['rules'] = $this->alarm_model->get_rules();
        $data['devices'] = $this->device_model->get_devices();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Alarm Rules';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('alarms/rules', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function save_rule() {
        if ($this->session->userdata('role') !== 'admin') show_error('Access denied.', 403);

        $rule_data = array(
            'name'           => $this->input->post('name', true),
            'device_id'      => $this->input->post('device_id') ?: null,
            'sensor_type'    => $this->input->post('sensor_type', true),
            'condition_type' => $this->input->post('condition_type', true),
            'threshold'      => $this->input->post('threshold'),
            'severity'       => $this->input->post('severity', true),
            'message'        => $this->input->post('message', true),
            'cooldown_minutes' => $this->input->post('cooldown_minutes') ?: 5,
            'is_active'      => $this->input->post('is_active') ? 1 : 0,
            'created_by'     => $this->session->userdata('user_id')
        );

        $rule_id = $this->input->post('rule_id');
        if ($rule_id) {
            $this->alarm_model->update_rule($rule_id, $rule_data);
            $this->scada_model->log_audit('alarm_rule_updated', "Alarm rule #$rule_id updated");
        } else {
            $this->alarm_model->create_rule($rule_data);
            $this->scada_model->log_audit('alarm_rule_created', "Alarm rule '{$rule_data['name']}' created");
        }

        $this->session->set_flashdata('success', 'Alarm rule saved.');
        redirect('alarms/rules');
    }

    public function delete_rule($id) {
        if ($this->session->userdata('role') !== 'admin') show_error('Access denied.', 403);
        $this->alarm_model->delete_rule($id);
        $this->scada_model->log_audit('alarm_rule_deleted', "Alarm rule #$id deleted");
        $this->session->set_flashdata('success', 'Alarm rule deleted.');
        redirect('alarms/rules');
    }

    public function history() {
        $filters = array(
            'severity'  => $this->input->get('severity'),
            'device_id' => $this->input->get('device_id'),
            'date_from' => $this->input->get('date_from'),
            'date_to'   => $this->input->get('date_to')
        );
        $data['events'] = $this->alarm_model->get_alarm_history(100, 0, $filters);
        $data['devices'] = $this->device_model->get_devices();
        $data['filters'] = $filters;
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Alarm History';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('alarms/history', $data);
        $this->load->view('templates/scada_footer', $data);
    }
}
