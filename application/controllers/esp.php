<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Esp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Esp_model');
        $this->load->helper('url');
        $this->load->library('table');
        $this->load->library('session');
    }

    // Main page - Display latest ESP data (grouped by sensor_type and device_id)
    public function index() {
        $data['title'] = 'Latest ESP Data Records';
        $data['esp_data'] = $this->Esp_model->get_latest_esp_data();
        $data['statistics'] = $this->Esp_model->get_statistics();
        
        $this->load->view('esp/index', $data);
    }

    // View all records (historical data)
    public function all_records() {
        $data['title'] = 'All ESP Data Records';
        $data['esp_data'] = $this->Esp_model->get_all_esp_data();
        
        $this->load->view('esp/all_records', $data);
    }

    // View records by device
    public function by_device($device_id = NULL) {
        if ($device_id === NULL) {
            show_404();
        }
        
        $data['title'] = 'ESP Records for Device #' . $device_id;
        $data['esp_data'] = $this->Esp_model->get_by_device_id($device_id);
        $data['device_id'] = $device_id;
        
        $this->load->view('esp/by_device', $data);
    }

    // View records by sensor type
    public function by_sensor($sensor_type = NULL) {
        if ($sensor_type === NULL) {
            show_404();
        }
        
        $data['title'] = 'ESP Records for Sensor: ' . ucfirst($sensor_type);
        $data['esp_data'] = $this->Esp_model->get_by_sensor_type($sensor_type);
        $data['sensor_type'] = $sensor_type;
        
        $this->load->view('esp/by_sensor', $data);
    }

    // View single record
    public function view($id = NULL) {
        if ($id === NULL) {
            show_404();
        }
        
        $data['esp_record'] = $this->Esp_model->get_esp_by_id($id);
        
        if (empty($data['esp_record'])) {
            show_404();
        }
        
        $data['title'] = 'View ESP Record #' . $id;
        
        $this->load->view('esp/view', $data);
    }

    // Create new record
    public function create() {
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $data['title'] = 'Add New ESP Record';
        
        $this->form_validation->set_rules('sensor_type', 'Sensor Type', 'required');
        $this->form_validation->set_rules('device_id', 'Device ID', 'required|integer');
        $this->form_validation->set_rules('value', 'Value', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('esp/create', $data);
        } else {
            $insert_data = array(
                'sensor_type' => $this->input->post('sensor_type'),
                'device_id' => $this->input->post('device_id'),
                'value' => $this->input->post('value')
            );
            
            $this->Esp_model->insert_esp_data($insert_data);
            $this->session->set_flashdata('success', 'Record added successfully!');
            redirect('esp');
        }
    }

    // Edit record
    public function edit($id = NULL) {
        if ($id === NULL) {
            show_404();
        }
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $data['esp_record'] = $this->Esp_model->get_esp_by_id($id);
        
        if (empty($data['esp_record'])) {
            show_404();
        }
        
        $data['title'] = 'Edit ESP Record #' . $id;
        
        $this->form_validation->set_rules('sensor_type', 'Sensor Type', 'required');
        $this->form_validation->set_rules('device_id', 'Device ID', 'required|integer');
        $this->form_validation->set_rules('value', 'Value', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('esp/edit', $data);
        } else {
            $update_data = array(
                'sensor_type' => $this->input->post('sensor_type'),
                'device_id' => $this->input->post('device_id'),
                'value' => $this->input->post('value')
            );
            
            $this->Esp_model->update_esp_data($id, $update_data);
            $this->session->set_flashdata('success', 'Record updated successfully!');
            redirect('esp');
        }
    }

    // Delete record
    public function delete($id = NULL) {
        if ($id === NULL) {
            show_404();
        }
        
        if ($this->Esp_model->delete_esp_data($id)) {
            $this->session->set_flashdata('success', 'Record deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete record!');
        }
        
        redirect('esp');
    }

    // API-like method to get latest data as JSON
    public function api_get_latest() {
        $data = $this->Esp_model->get_latest_esp_data();
        echo json_encode($data);
    }

    // API-like method to get all data as JSON
    public function api_get_all() {
        $data = $this->Esp_model->get_all_esp_data();
        echo json_encode($data);
    }
}
?>