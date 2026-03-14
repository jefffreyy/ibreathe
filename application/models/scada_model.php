<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Scada_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ==================== AUTH ====================

    public function create_user($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('tbl_scada_users', $data);
    }

    public function check_login($username, $password) {
        $this->db->where('username', $username);
        $this->db->where('status', 'active');
        $query = $this->db->get('tbl_scada_users');
        if ($query->num_rows() == 1) {
            $user = $query->row();
            if (password_verify($password, $user->password)) {
                $this->db->set('last_login', date('Y-m-d H:i:s'));
                $this->db->where('id', $user->id);
                $this->db->update('tbl_scada_users');
                return $user;
            }
        }
        return false;
    }

    public function get_user($id) {
        return $this->db->get_where('tbl_scada_users', array('id' => $id))->row();
    }

    public function get_user_by_username($username) {
        return $this->db->get_where('tbl_scada_users', array('username' => $username))->row();
    }

    public function get_all_users() {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('tbl_scada_users')->result();
    }

    public function update_user($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('tbl_scada_users', $data);
    }

    public function delete_user($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_scada_users');
    }

    // ==================== SETTINGS ====================

    public function get_settings() {
        return $this->db->get('tbl_scada_settings')->result();
    }

    public function get_setting($key) {
        $row = $this->db->get_where('tbl_scada_settings', array('setting_key' => $key))->row();
        return $row ? $row->setting_value : null;
    }

    public function update_setting($key, $value) {
        $this->db->where('setting_key', $key);
        return $this->db->update('tbl_scada_settings', array('setting_value' => $value));
    }

    // ==================== AUDIT LOG ====================

    public function log_audit($action, $description = '') {
        $data = array(
            'user_id'     => $this->session->userdata('user_id'),
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $this->input->ip_address(),
            'user_agent'  => $this->input->user_agent(),
            'created_at'  => date('Y-m-d H:i:s')
        );
        return $this->db->insert('tbl_scada_audit_log', $data);
    }

    public function get_audit_logs($limit = 100, $offset = 0, $filters = array()) {
        $this->db->select('tbl_scada_audit_log.*, tbl_scada_users.username');
        $this->db->join('tbl_scada_users', 'tbl_scada_users.id = tbl_scada_audit_log.user_id', 'left');
        if (!empty($filters['date_from'])) {
            $this->db->where('tbl_scada_audit_log.created_at >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('tbl_scada_audit_log.created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        if (!empty($filters['action'])) {
            $this->db->where('tbl_scada_audit_log.action', $filters['action']);
        }
        $this->db->order_by('tbl_scada_audit_log.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get('tbl_scada_audit_log')->result();
    }

    public function count_audit_logs($filters = array()) {
        if (!empty($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        return $this->db->count_all_results('tbl_scada_audit_log');
    }
}
