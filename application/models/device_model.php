<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Device_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function add_device($data) {
        $data['device_key'] = $this->generate_device_key();
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('tbl_scada_devices', $data);
        $device_id = $this->db->insert_id();

        // Create default sensors for the device
        $sensors = array(
            array('device_id' => $device_id, 'sensor_type' => 'temperature', 'unit' => '°C', 'min_range' => -10, 'max_range' => 60),
            array('device_id' => $device_id, 'sensor_type' => 'humidity', 'unit' => '%', 'min_range' => 0, 'max_range' => 100),
            array('device_id' => $device_id, 'sensor_type' => 'pm2.5', 'unit' => 'µg/m³', 'min_range' => 0, 'max_range' => 500)
        );
        $this->db->insert_batch('tbl_scada_sensors', $sensors);

        return $device_id;
    }

    public function get_devices() {
        $this->db->order_by('name', 'ASC');
        return $this->db->get('tbl_scada_devices')->result();
    }

    public function get_device($id) {
        return $this->db->get_where('tbl_scada_devices', array('id' => $id))->row();
    }

    public function get_device_by_key($device_key) {
        return $this->db->get_where('tbl_scada_devices', array('device_key' => $device_key))->row();
    }

    public function update_device($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('tbl_scada_devices', $data);
    }

    public function delete_device($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_scada_devices');
    }

    public function update_last_seen($device_id, $ip = null) {
        $data = array(
            'last_seen' => date('Y-m-d H:i:s'),
            'status'    => 'online'
        );
        if ($ip) {
            $data['ip_address'] = $ip;
        }
        $this->db->where('id', $device_id);
        return $this->db->update('tbl_scada_devices', $data);
    }
    
    public function update_last_seen_offline($device_id, $ip = null) {
        $data = array(
            'status'    => 'offline'
        );
        if ($ip) {
            $data['ip_address'] = $ip;
        }
        $this->db->where('id', $device_id);
        return $this->db->update('tbl_scada_devices', $data);
    }

    public function get_device_sensors($device_id) {
        $this->db->where('device_id', $device_id);
        return $this->db->get('tbl_scada_sensors')->result();
    }

    public function get_device_status_summary() {
        $sql = "SELECT status, COUNT(*) as count FROM tbl_scada_devices GROUP BY status";
        return $this->db->query($sql)->result();
    }

    public function mark_offline_devices($timeout_minutes = 5) {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$timeout_minutes} minutes"));
        $this->db->where('status', 'online');
        $this->db->where('last_seen <', $cutoff);
        return $this->db->update('tbl_scada_devices', array('status' => 'offline'));
    }

    public function count_devices() {
        return $this->db->count_all('tbl_scada_devices');
    }

    private function generate_device_key() {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8) . '-' .
            substr(md5(uniqid(mt_rand(), true)), 0, 4) . '-' .
            substr(md5(uniqid(mt_rand(), true)), 0, 4) . '-' .
            substr(md5(uniqid(mt_rand(), true)), 0, 12));
    }
}
