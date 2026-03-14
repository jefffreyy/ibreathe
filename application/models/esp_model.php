<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Esp_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Get latest records (grouped by sensor_type and device_id)
    public function get_latest_esp_data() {
        $sql = "SELECT e1.* 
                FROM tbl_esp e1
                INNER JOIN (
                    SELECT sensor_type, device_id, MAX(id) as max_id
                    FROM tbl_esp
                    GROUP BY sensor_type, device_id
                ) e2 
                ON e1.sensor_type = e2.sensor_type 
                AND e1.device_id = e2.device_id 
                AND e1.id = e2.max_id
                ORDER BY e1.device_id, e1.sensor_type";
        
        $query = $this->db->query($sql);
        return $query->result();
    }

    // Get all records from tbl_esp
    public function get_all_esp_data() {
        $query = $this->db->get('tbl_esp');
        return $query->result();
    }

    // Get single record by ID
    public function get_esp_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('tbl_esp');
        return $query->row();
    }

    // Insert new record
    public function insert_esp_data($data) {
        return $this->db->insert('tbl_esp', $data);
    }

    // Update record
    public function update_esp_data($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_esp', $data);
    }

    // Delete record
    public function delete_esp_data($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_esp');
    }

    // Get records with specific sensor type
    public function get_by_sensor_type($sensor_type) {
        $this->db->where('sensor_type', $sensor_type);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('tbl_esp');
        return $query->result();
    }

    // Get records by device ID
    public function get_by_device_id($device_id) {
        $this->db->where('device_id', $device_id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('tbl_esp');
        return $query->result();
    }

    // Get latest records with limit
    public function get_latest_records($limit = 10) {
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get('tbl_esp');
        return $query->result();
    }
    
    // Get statistics for dashboard
    public function get_statistics() {
        $stats = array();
        
        // Total records
        $stats['total_records'] = $this->db->count_all('tbl_esp');
        
        // Total devices
        $this->db->distinct();
        $this->db->select('device_id');
        $query = $this->db->get('tbl_esp');
        $stats['total_devices'] = $query->num_rows();
        
        // Sensor types
        $this->db->distinct();
        $this->db->select('sensor_type');
        $query = $this->db->get('tbl_esp');
        $stats['sensor_types'] = $query->result();
        
        return $stats;
    }
}
?>