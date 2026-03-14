<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logger {

    // Define a custom table name for activity log
    private $table_name = 'tbl_activity_logs';

    // Define a custom field name for user id
    private $create_date='create_date';
    private $empl_id='empl_id';
    private $description='description';
    
    public function __construct()
	{
		// Get an instance of CodeIgniter
		$this->CI =& get_instance();

        // Load the database library
        $this->CI->load->database();
	}

	public function log_activity($user_id,$description)
	{
        // Get the user id from session data or default to 0

        // Get the browser of the user or default to unkno

        // Get the current date and time in Y-m-d H:i:s format
        $date_time = date('Y-m-d H:i:s');

        // Prepare an array of data to insert into the database table
        $data = array($this->create_date=>$date_time,$this->empl_id=>$user_id,$this->description=>$description);

        // Insert the data into the database table
        return $this->CI->db->insert($this->table_name, $data);
	}
}