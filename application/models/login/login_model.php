<?php
class login_model extends CI_Model
{
	function GET_PASSWORD($username)
	{
		$sql = "SELECT * FROM tbl_user WHERE BINARY serial_id = ?";
		$query = $this->db->query($sql, array($username));
		$result = $query->row_array();
		return $result ? $result : "NO RECORD";
	}

	function GET_MACHINE_SETTINGS()
	{
		$query = "SELECT value FROM tbl_system_setup WHERE setting = 'machine_no'";
		$result = $this->db->query($query)->row_array();
		return $result["value"];
	}

	function GET_MACHINE_NAME($id)
	{
		$query = "SELECT * FROM tbl_machine WHERE id = '$id'";
		return $this->db->query($query)->row_array();
	}

	function GET_NAME()
	{
		$query = "SELECT * FROM tbl_system_setup WHERE id = 1";
		return $this->db->query($query)->row_array();
	}

	function GET_LOGO()
	{
		$query = "SELECT * FROM tbl_system_setup WHERE id = 2";
		return $this->db->query($query)->row_array();
	}

	function GET_USER_ACCESS($id)
	{
		$sql = "SELECT user_page FROM tbl_system_useraccess WHERE id=?";
		$res = $this->db->query($sql, array($id))->row_array();
		return $res["user_page"];
	}

	function MOD_CHANGE_PASSWORD($new_password, $user_id)
	{
		$encrypted_password = password_hash($new_password, PASSWORD_DEFAULT);
		$sql = "UPDATE tbl_user SET password='$encrypted_password', is_temppass = 1 WHERE id='$user_id'";
		$this->db->query($sql);
		$sql = "UPDATE tbl_system_setup SET value='$user_id' WHERE setting='user_id'";
		$this->db->query($sql);
		$sql = "UPDATE tbl_system_setup SET value='$encrypted_password' WHERE setting='user_pass'";
		$this->db->query($sql);
	}

	function UPDATE_MACHINE_SETTINGS($machine_id)
	{
		$sql = "UPDATE tbl_system_setup SET value='$machine_id' WHERE setting='machine_no'";
		$this->db->query($sql);
	}

	function GET_IP_ADDRESS($ip_add)
	{
		$sql = "SELECT ip_address FROM tbl_system_whitelist WHERE ip_address=? AND status='Active' AND is_deleted=0";
		$query = $this->db->query($sql, array($ip_add));
		$query->next_result();
		return $query->num_rows();
	}

	function get_system_setup_by_setting2($setting, $value)
	{
		$query_select = "SELECT * FROM tbl_system_setup WHERE setting=?";
		$result = $this->db->query($query_select, array($setting))->row_array();
		if (!$result) {
			$query_insert = "INSERT INTO tbl_system_setup (setting, value) VALUES (?, ?)";
			$this->db->query($query_insert, array($setting, $value));
			$query_select_new = "SELECT * FROM tbl_system_setup WHERE setting=?";
			$result = $this->db->query($query_select_new, array($setting))->row_array();
		}
		return $result ? $result['value'] : null;
	}
}
