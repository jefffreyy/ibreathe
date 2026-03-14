<?php
class user_model extends CI_Model
{
    public function get_all_users()
    {
        $sql = "SELECT id, CONCAT(last_name, ', ', first_name) AS name FROM tbl_library_users";
        $query = $this->db->query($sql);

        return $query->result();
    }


    public function get_total_seats()
    {
        $this->db->select_max('id');
        $query = $this->db->get('tbl_library_seats');
        $row = $query->row();
        return $row ? (int)$row->id : 0;
    }

    // Add seats from start_id to end_id (inclusive)
    public function add_seats($start_id, $end_id)
    {
        $data = array();
        for ($i = $start_id; $i <= $end_id; $i++) {
            $data[] = array('id' => $i);
        }
        if (!empty($data)) {
            $this->db->insert_batch('tbl_library_seats', $data);
        }
    }

    // Remove seats from start_id to end_id (inclusive)
    public function remove_seats($start_id, $end_id)
    {
        $this->db->where('id >=', $start_id);
        $this->db->where('id <=', $end_id);
        $this->db->delete('tbl_library_seats');
    }
    public function remove_disabled_specific_date($date)
    {
        $this->db->where('date_disabled', $date);
        $this->db->delete('tbl_library_assign_disabled');
    }

 public function get_opening_hours()
{
    $query = $this->db->get_where('tbl_library_admin_setting', array('id' => 1), 1);
    return $query->row(); // returns an object with start_time and end_time
}

public function update_opening_hours($start, $end)
{
    $data = array(
        'start_time' => $start,
        'end_time'   => $end
    );

    $this->db->where('id', 1);
    return $this->db->update('tbl_library_admin_setting', $data);
}


    public function get_summarya($start_date, $end_date, $user_id = null)
    {
        $this->db->select('DATE(date_reserve) as date_reserve, COUNT(*) as reservation_count');
        $this->db->from('tbl_library_assign');
        $this->db->where('date_reserve >=', $start_date);
        $this->db->where('date_reserve <=', $end_date);

        if (!empty($user_id)) {
            $this->db->where('id', $user_id);
        }

        $this->db->group_by('DATE(date_reserve)');
        $this->db->order_by('DATE(date_reserve)', 'ASC');

        return $this->db->get()->result();
    }

    public function get_top_users_this_month()
{
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');

    $this->db->select('u.first_name,u.last_name, COUNT(a.id) as reservation_count');
    $this->db->from('tbl_library_assign as a');
    $this->db->join('tbl_library_users as u', 'u.id = a.id'); // Adjust if user table is named differently
    $this->db->where('a.date_reserve >=', $start_date);
    $this->db->where('a.date_reserve <=', $end_date);
    $this->db->group_by('u.id');
    $this->db->order_by('reservation_count', 'DESC');
    $this->db->limit(10);

    return $this->db->get()->result();
}

    public function get_summary_report($filter_type = 'daily', $user_id = '')
    {
        // Base select: count and formatted date
        $this->db->select("DATE(date_reserve) as date_reserve, COUNT(*) as reservation_count");
        $this->db->from('tbl_library_assign');

        // Optional user filter
        if (!empty($user_id)) {
            $this->db->where('id', $user_id);
        }

        // Adjust select and group by based on filter_type
        switch ($filter_type) {
            case 'weekly':
                // Week starts on Monday; Mode 1 for ISO-compliant weeks
                $this->db->select("YEAR(date_reserve) as year, WEEK(date_reserve, 1) as week", false);
                $this->db->group_by(['YEAR(date_reserve)', 'WEEK(date_reserve, 1)']);
                break;

            case 'monthly':
                $this->db->select("YEAR(date_reserve) as year, MONTH(date_reserve) as month", false);
                $this->db->group_by(['YEAR(date_reserve)', 'MONTH(date_reserve)']);
                break;

            case 'daily':
            default:
                $this->db->group_by('DATE(date_reserve)');
                break;
        }

        $query = $this->db->get();
        $results = $query->result();

        // Post-process results for labeling
        foreach ($results as &$row) {
            if ($filter_type == 'weekly') {
                // Format label: YYYY-WW and date range (Monday to Sunday)
                $dt = new DateTime();
                $dt->setISODate($row->year, $row->week); // ISO week
                $monday = $dt->format('Y-m-d');
                $sunday = $dt->modify('+6 days')->format('Y-m-d');

                $row->date_reserve = $row->year . '-W' . str_pad($row->week, 2, '0', STR_PAD_LEFT);
                $row->date_range = $monday . ' to ' . $sunday;

                unset($row->year);
                unset($row->week);
            } elseif ($filter_type == 'monthly') {
                // Format label: YYYY-MM
                $row->date_reserve = $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT);
                unset($row->year);
                unset($row->month);
            } else {
                // Already formatted via DATE(date_reserve)
                // No additional processing needed
            }
        }

        return $results;
    }


    function insert_reservation($student_id, $seat_id, $start_time, $end_time)
    {
        $current_date = date('Y-m-d');

        $sql = "INSERT INTO tbl_library_assign (student_id, seat_id, date_reserve,created_date,start_time, end_time,status) VALUES ('$student_id', '$seat_id', '$current_date','$start_time','$start_time','$end_time','Closed')";
        $query = $this->db->query($sql);

        return $this->db->insert_id();
    }

    function check_reservation($seat_id, $slot_id, $date_reserve)
    {
        $sql = "SELECT COUNT(*) AS count FROM tbl_library_assign WHERE seat_id = '$seat_id' AND slot_id = '$slot_id' AND date_reserve = '$date_reserve'";
        var_dump($sql);
        $query = $this->db->query($sql);

        return ($query->row()->count > 0) ? 1 : 0;
    }
    function check_reservationcount($student_id, $date_reserve)
    {
        $sql = "SELECT COUNT(*) AS count  FROM tbl_library_assign  WHERE student_id   = ?    AND date_reserve  = ?    AND status IN ('Occupied','Closed')";
        $query = $this->db->query($sql, array($student_id, $date_reserve));

        return $query->row()->count;
    }
    function update_reservation($reserve_id)
    {
        $sql = "UPDATE tbl_library_assign SET status = 'Occupied' WHERE id = '$reserve_id'";
        $query = $this->db->query($sql);
    }
    function update_OTP($otp)
    {
        $sql = "UPDATE tbl_library_otp SET otp = '$otp' WHERE id = '1'";
        $query = $this->db->query($sql);
    }

    function get_OTP()
    {
        $sql = "SELECT otp FROM tbl_library_otp WHERE id = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return $query->row()->otp;
        } else {
            return null; // or handle error as needed
        }
    }
    function get_all_slots($current_date, $slot_id)
    {
        $date = date('Y-m-d', strtotime($current_date));

        $sql = "SELECT 
                tba.*, 
                COALESCE(tbb.status, 'Open') AS status,
                CASE 
                    WHEN tbc.seat_id IS NOT NULL THEN 'not-available'
                    ELSE COALESCE(tbb.status, 'Open')
                END AS final_status
            FROM tbl_library_seats AS tba
            LEFT JOIN (
                SELECT * FROM tbl_library_assign 
                WHERE date_reserve = '$date' AND slot_id = '$slot_id'
            ) AS tbb ON tba.id = tbb.seat_id
            LEFT JOIN (
                SELECT seat_id FROM tbl_library_assign_disabled 
                WHERE date_disabled = '$date'
            ) AS tbc ON tba.id = tbc.seat_id
            ORDER BY tba.id ASC";

        $query = $this->db->query($sql);

        return $query->result();
    }

    function get_summary()
    {
        $sql = "SELECT 
    d.date_reserve,
    COUNT(t.id) AS reservation_count
FROM (
    SELECT CURDATE() - INTERVAL n DAY AS date_reserve
    FROM (
        SELECT 0 AS n UNION ALL
        SELECT 1 UNION ALL
        SELECT 2 UNION ALL
        SELECT 3 UNION ALL
        SELECT 4 UNION ALL
        SELECT 5 UNION ALL
        SELECT 6
    ) AS days
) AS d
LEFT JOIN tbl_library_assign t 
    ON DATE(t.date_reserve) = d.date_reserve
GROUP BY d.date_reserve
ORDER BY d.date_reserve ASC
";

        $query = $this->db->query($sql);

        return $query->result();
    }



    function check_slots($seat_id, $date)
    {
        $sql = "SELECT tbb.*, 
                       COALESCE(tba.status, 'Open') AS status,
                       CASE 
                            WHEN EXISTS (
                                SELECT 1 FROM tbl_library_assign_disabled 
                                WHERE date_disabled = ? AND seat_id = ?
                            ) THEN 'not-available'
                            ELSE COALESCE(tba.status, 'Open')
                       END AS final_status
                FROM tbl_library_slots AS tbb
                LEFT JOIN (
                    SELECT * FROM tbl_library_assign 
                    WHERE date_reserve = ? AND seat_id = ?
                ) AS tba ON tba.slot_id = tbb.id
                ORDER BY tbb.id ASC";

        $query = $this->db->query($sql, array($date, $seat_id, $date, $seat_id));

        return $query->result();
    }



    function registerNewUser($student_id, $fname, $mname, $lname, $role, $password, $department, $secquestion, $answer1)
    {
        $sql = 'INSERT INTO tbl_library_users (student_id, first_name, middle_name, last_name, role, password, department, secquestion, answer1) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $this->db->query($sql, array($student_id, $fname, $mname, $lname, $role, $password, $department, $secquestion, $answer1));
    }


    function checkExistingStudentID($student_id)
    {
        $query = $this->db->get_where('tbl_library_users', array('student_id' => $student_id));
        return $query->num_rows() > 0;
    }

    public function checkUserExists($student_id)
    {
        $this->db->where('student_id', $student_id);
        $this->db->from('tbl_library_users');
        return $this->db->count_all_results() > 0;
    }

    public function getSecurityQuestionByStudentID($student_id)
    {
        $this->db->select('secquestion');
        $this->db->from('tbl_library_users');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->secquestion;
        }

        return false;
    }

    public function getSecurityAnswerByStudentID($student_id)
    {
        $this->db->select('answer1');
        $this->db->from('tbl_library_users');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->answer1;
        }

        return false;
    }

    public function isAccountLocked($student_id)
    {
        $this->db->select('is_locked');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('tbl_library_users');
        return $query->row()->is_locked == 1;
    }

    public function incrementAttempts($student_id)
    {
        $this->db->set('sec_attempts', 'sec_attempts + 1', false);
        $this->db->where('student_id', $student_id);
        $this->db->update('tbl_library_users');
    }

    public function getAttempts($student_id)
    {
        $this->db->select('sec_attempts');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('tbl_library_users');
        return (int) $query->row()->sec_attempts;
    }

    public function lockAccount($student_id)
    {
        $this->db->where('student_id', $student_id);
        $this->db->update('tbl_library_users', ['is_locked' => 1]);
    }

    public function resetAttempts($student_id)
    {
        $this->db->where('student_id', $student_id);
        $this->db->update('tbl_library_users', ['sec_attempts' => 0]);
    }

    public function getSecurityQuestionText($code)
    {
        $questions = array(
            "HERO" => "What is the name of your childhood hero?",
            "CITY" => "In what city or town did your parents meet?",
            "SONG" => "What is your favorite song?",
            "RESTAURANT" => "What is your favorite restaurant?"
        );

        return isset($questions[$code]) ? $questions[$code] : null;
    }





    function updatePassword($student_id, $password)
    {
        $sql = "UPDATE tbl_library_users SET password = ? WHERE student_id = ?";
        return $this->db->query($sql, array($password, $student_id));
    }

    function checkLogin($student_id, $password)
    {
        $sql = "SELECT * FROM tbl_library_users WHERE student_id = ? AND password = ?";
        $query = $this->db->query($sql, array($student_id, $password));

        return ($query->num_rows() == 1) ? $query->row() : false;
    }

    function getUserDetails($student_id)
    {
        $query = $this->db->get_where('tbl_library_users', ['student_id' => $student_id]);
        return $query->row_array();
    }

    function getReservationsRequests($student_id)
    {
        $sql = "SELECT * FROM tbl_library_assign
                WHERE student_id = ? AND status = 'Closed'  ORDER BY id DESC";

        $query = $this->db->query($sql, array($student_id));
        return $query->result_array();
    }

    function getReservationsRequests_occupied($student_id)
    {
        $sql = "SELECT * FROM tbl_library_assign
                WHERE student_id = ? AND (status = 'Occupied' OR  status = 'Finished' ) ORDER BY id DESC";

        $query = $this->db->query($sql, array($student_id));
        return $query->result_array();
    }
    function get_current_slot(){
         $sql = "SELECT a.*, b.status as reserve_id FROM tbl_library_seats a LEFT JOIN (SELECT * FROM tbl_library_assign WHERE status = 'Closed' OR status = 'Occupied' ) b ON a.id = b.seat_id";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
    function get_reservation_5min()
    {
        $current_datetime = date('Y-m-d H:i:s');


        $datetime_plus_5 = date('Y-m-d H:i:s', strtotime($current_datetime . ' -5 minutes'));
        var_dump($datetime_plus_5);
        $sql = "SELECT * FROM tbl_library_assign
        WHERE start_time <= '$datetime_plus_5'
        AND status = 'Closed'
        ORDER BY id DESC";

        $query = $this->db->query($sql);
        $results = $query->result();
        return $results;
    }

     function get_reservation_lapse()
    {
        $current_datetime = date('Y-m-d H:i:s');


       
        $sql = "SELECT * FROM tbl_library_assign
        WHERE end_time <= '$current_datetime'
        AND status = 'Occupied'
        ORDER BY id DESC";
        // var_dump($sql);
        $query = $this->db->query($sql);
        $results = $query->result();
        return $results;
    }
// Insert to deleted table
function insert_deleted_reservation($data)
{
    $this->db->insert('tbl_library_assign_deleted', $data);
}
function update_status_reservation($data)
{
      // Step 2: Update original record's status to 'Finished'
    if (isset($data['id'])) {
        $this->db->where('id', $data['id']);
        $this->db->update('tbl_library_assign', ['status' => 'Finished']);
    }
}
// Delete from original table
function delete_reservation_by_id($id)
{
    $this->db->where('id', $id);
    $this->db->delete('tbl_library_assign');
}
    function getReservationsRequests_cancelled($student_id)
    {
        $sql = "SELECT * FROM tbl_library_assign_deleted
                WHERE student_id = ?  ORDER BY id DESC";

        $query = $this->db->query($sql, array($student_id));
        return $query->result_array();
    }

    function cancelReservation($reservation_id)
    {

        $sql = "SELECT * FROM tbl_library_assign WHERE id = '$reservation_id'";

        $query = $this->db->query($sql);
        $result = ($query->result())[0];

        $student_id = $result->student_id;
        $seat_id = $result->seat_id;
        $slot_id = $result->slot_id;
        $current_date = $result->created_date;
        $date_reserve = $result->date_reserve;

        $sql = "INSERT INTO tbl_library_assign_deleted (student_id, seat_id, slot_id,created_date, date_reserve) VALUES ('$student_id', '$seat_id', '$slot_id','$current_date','$date_reserve')";
        $query = $this->db->query($sql);
        // return $this->db->query($sql, array($reservation_id, $student_id));

        $sql = "DELETE FROM tbl_library_assign WHERE id = '$reservation_id'";
        $query = $this->db->query($sql);
        // $query = $this->db->query($sql);
    }


    public function insert_disabled_seat($seat_id, $date_disabled, $created_date)
    {
        $sql = "INSERT INTO tbl_library_assign_disabled (seat_id, date_disabled, created_date) 
                VALUES (?, ?, ?)";

        $this->db->query($sql, array($seat_id, $date_disabled, $created_date));

        return $this->db->insert_id();
    }


    function getReservations_details($id)
    {
        $sql = "SELECT * FROM tbl_library_assign WHERE id = $id";

        $query = $this->db->query($sql);
        return $query->result()[0];
    }



    //From here are new updates.
    public function is_professor($user_id)
    {
        $this->db->select('role');
        $this->db->from('tbl_library_users');
        $this->db->where('student_id', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return ($row->role === 'Professor');
        }

        return false;
    }

    function get_professor_by_id($id)
    {
        $this->db->where('student_id', $id);
        $query = $this->db->get('tbl_library_users');

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $row['name'] = $row['first_name'] . ' ' . $row['last_name']; // combine first + last name
            return $row;
        }

        return false;
    }

    function get_class_reservation_slots()
    {
        $this->db->select('*');
        $this->db->from('tbl_library_slots');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function insert_prof_request($data)
    {
        return $this->db->insert('tbl_library_professor_request', $data);
    }

    public function get_professor_requests_by_id($prof_id) //for specific profs
    {
        $this->db->where('prof_id', $prof_id);
        $this->db->order_by('date', 'ASC');
        return $this->db->get('tbl_library_professor_request')->result_array();
    }

    public function get_professor_ongoing_by_id($prof_id)
    {
        $this->db->where('prof_id', $prof_id);
        $this->db->where('status', 'APPROVED');
        $this->db->order_by('date', 'ASC');
        return $this->db->get('tbl_library_professor_ongoing')->result();
    }

    public function get_all_professor_requests()
    {
        $this->db->order_by('date', 'ASC');
        return $this->db->get('tbl_library_professor_request')->result_array();
    }

    public function get_all_professor_ongoing()
    {
        $query = $this->db->get('tbl_library_professor_ongoing');
        return $query->result();
    }


    public function get_request_by_id($id)
    {
        $query = $this->db->get_where('tbl_library_professor_request', array('id' => $id));
        return $query->row_array();
    }
    public function get_request_by_id_ongoing($id)
    {
        $query = $this->db->get_where('tbl_library_professor_ongoing', array('id' => $id));
        return $query->row_array();
    }

    public function insert_rejected_request($data)
    {
        return $this->db->insert('tbl_library_professor_terminated', $data);
    }

    public function delete_request_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_library_professor_request');
    }

    public function get_slot_name_by_id($id)
    {
        $this->db->select('slot_name');
        $this->db->from('tbl_library_slots');
        $this->db->where('id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->slot_name;
        }
        return null;
    }


    //confirm_reservation functions
    public function get_professor_request($id)
    {
        $query = $this->db->get_where('tbl_library_professor_request', ['id' => $id]);
        return $query->row();
    }
    public function insert_professor_ongoing($data)
    {
        return $this->db->insert('tbl_library_professor_ongoing', $data);
    }
    public function update_professor_request_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_library_professor_request', ['status' => $status]);
    }
    public function insert_library_assign($data)
    {
        $this->db->insert('tbl_library_assign', $data);
    }
    public function delete_professor_request($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tbl_library_professor_request');
    }



    //terminate_reservation functions
    public function get_professor_ongoing($id)
    {
        return $this->db->get_where('tbl_library_professor_ongoing', ['id' => $id])->row();
    }

    public function get_assign_data($prof_id, $seat_id, $date)
    {
        return $this->db->get_where('tbl_library_assign', [
            'student_id'    => $prof_id,
            'seat_id'       => $seat_id,
            'date_reserve'  => $date,
            'is_assigned'   => 1
        ])->row();
    }

    public function archive_assign_data($assign)
    {
        $data = (array) $assign;
        $data['status'] = 'TERMINATED';
        $this->db->insert('tbl_library_assign_deleted', $data);
        $this->db->delete('tbl_library_assign', ['id' => $assign->id]);
    }

    public function archive_professor_ongoing($request)
    {
        $data = [
            'prof_name'     => $request->prof_name,
            'prof_id'       => $request->prof_id,
            'date'          => $request->date,
            'time_slot'     => $request->time_slot,
            'num_of_seats'  => $request->num_of_seats,
            'prof_note'     => $request->prof_note,
            'seat_assigned' => $request->seat_assigned,
            'status'        => $request->status
        ];
        $this->db->insert('tbl_library_professor_terminated', $data);
    }
    public function delete_professor_ongoing($id)
    {
        $this->db->delete('tbl_library_professor_ongoing', ['id' => $id]);
    }

    //functions by notifications
    public function get_professor_requests_by_user($prof_id)
    {
        $this->db->where('prof_id', $prof_id);
        return $this->db->get('tbl_library_professor_request')->result();
    }
    public function get_professor_ongoing_by_user($prof_id)
    {
        $this->db->where('prof_id', $prof_id);
        return $this->db->get('tbl_library_professor_ongoing')->result();
    }
    public function get_professor_terminated_by_user($prof_id)
    {
        $this->db->where('prof_id', $prof_id);
        return $this->db->get('tbl_library_professor_terminated')->result();
    }

    //functions by notification_details
    public function get_professor_reservation_by_id_and_type($id, $type)
    {
        $table_map = [
            'request' => 'tbl_library_professor_request',
            'ongoing' => 'tbl_library_professor_ongoing',
            'terminated' => 'tbl_library_professor_terminated'
        ];

        if (!array_key_exists($type, $table_map)) {
            return null;
        }

        return $this->db
            ->where('id', $id)
            ->get($table_map[$type])
            ->row();
    }




    function check_student_id($student_id)
    {
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('tbl_library_users');
        return $query->num_rows() > 0;
    }

    public function update_credentials($student_id, $password, $secquestion, $answer)
    {
        $data = array(
            'password' => $password,
            'secquestion' => $secquestion,
            'answer1' => $answer,
            'sec_attempts' => 0,
            'is_locked' => 0
        );

        $this->db->where('student_id', $student_id);
        return $this->db->update('tbl_library_users', $data);
    }



    public function get_qr_restriction()
    {
        $query = $this->db->get_where('tbl_library_admin_setting', ['id' => 1]);
        if ($query->num_rows() > 0) {
            return (int) $query->row()->qr_restriction;
        }
        return 0;
    }


    public function update_qr_restriction($value)
    {
        $this->db->where('id', 1);
        $this->db->update('tbl_library_admin_setting', ['qr_restriction' => $value]);
    }

    public function get_seat_expiry_notification($student_id)
    {
        $this->db->where('student_id', $student_id);
        $this->db->where('status', 'Occupied');
        $this->db->where('date_reserve', date('Y-m-d'));
        $query = $this->db->get('tbl_library_assign');

        $slot_end_times = [
            1 => '08:30',
            2 => '10:00',
            3 => '11:30',
            4 => '13:00',
            5 => '14:30',
            6 => '16:00',
            7 => '17:00'
        ];

        foreach ($query->result() as $row) {
            $slot_id = (int)$row->slot_id;

            if (!isset($slot_end_times[$slot_id])) {
                continue;
            }

            $end_time_str = $row->date_reserve . ' ' . $slot_end_times[$slot_id] . ':00';
            $end_time = strtotime($end_time_str);
            $now = time();
            $minutes_left = ($end_time - $now) / 60;

            if ($minutes_left <= 30 && $minutes_left > 0) {
                return [
                    'time_remaining' => round($minutes_left),
                    'seat_id' => $row->seat_id
                ];
            }
        }

        return false;
    }

    public function get_disabled_seats()
    {
        $this->db->order_by('date_disabled', 'DESC');
        $query = $this->db->get('tbl_library_assign_disabled');
        return $query->result_array();
    }

    public function delete_disabled_seat($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tbl_library_assign_disabled');
    }



    //====================================================================================================
    //From here will start the functions for masteradmin creation
    public function get_librarian_by_credentials($username, $password)
    {
        $this->db->where('username', $username);
        $this->db->where('password', $password); // Consider hashing in real apps
        $query = $this->db->get('tbl_library_librarian');

        if ($query->num_rows() == 1) {
            return $query->row();
        }
        return false;
    }

    public function get_all_masterlist()
    {
        return $this->db->get('tbl_library_masterlist')->result();
    }
    public function email_exists($email)
    {
        $query = $this->db->get_where('tbl_library_masterlist', ['email' => $email]);
        return $query->num_rows() > 0;
    }

    public function insert_email($email)
    {
        $this->db->insert('tbl_library_masterlist', ['email' => $email]);
    }
    public function insert_masterlist_email($email)
    {
        $this->db->insert('tbl_library_masterlist', array('email' => $email));
    }

    public function email_exists_in_masterlist($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('tbl_library_masterlist');
        return $query->num_rows() > 0;
    }

    public function get_librarian()
    {
        $query = $this->db->get_where('tbl_library_librarian', array('id' => 1));
        return $query->row_array();
    }

    public function update_librarian($data)
    {
        $this->db->where('id', 1);
        return $this->db->update('tbl_library_librarian', $data);
    }

    //EMAIL CHECK (NOT FINAL)
    public function is_email_in_masterlist($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('tbl_library_masterlist');
        return $query->num_rows() > 0;
    }







    public function get_all_seat_ids()
    {
        return $this->db->select('id')->get('tbl_library_seats')->result_array();
    }


    public function get_reservations_by_date($date)
    {
        $this->db->where('date_reserve', $date);
        return $this->db->get('tbl_library_assign')->result_array();
    }



    public function get_reservations_by_seat_and_date($seat_id, $date)
    {
        $this->db->where('seat_id', $seat_id);
        $this->db->where('date_reserve', $date);
        return $this->db->get('tbl_library_assign')->result_array();
    }

    public function get_slot_time($slot_id)
    {
        $this->db->select('slot_name');
        $this->db->from('tbl_library_slots');
        $this->db->where('id', $slot_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $slot = $query->row_array();
            $parts = explode('-', $slot['slot_name']);
            if (count($parts) == 2) {
                return [
                    'start_time' => trim($parts[0]),
                    'end_time'   => trim($parts[1])
                ];
            }
        }

        return false;
    }



    public function add_notification($student_id, $seat_id, $message)
    {
        $data = [
            'student_id' => $student_id,
            'seat_id'    => $seat_id,
            'message'    => $message,
            'created_at' => date('Y-m-d H:i:s'),
            'is_read'    => 0
        ];
        $this->db->insert('tbl_library_notification', $data);
    }


    public function archive_reservation($student_id, $seat_id, $date)
    {
        $this->db->where('student_id', $student_id);
        $this->db->where('seat_id', $seat_id);
        $this->db->where('date_reserve', $date);
        $res = $this->db->get('tbl_library_assign')->row_array();

        if ($res) {
            $this->db->insert('tbl_library_assign_deleted', $res);
            $this->db->where('id', $res['id']);
            $this->db->delete('tbl_library_assign');
        }
    }

    public function get_all_seat_status_by_date($date)
    {
        $this->db->select('seat_id');
        $this->db->from('tbl_library_assign');
        $this->db->where('date_reserve', $date);
        $this->db->group_by('seat_id');

        $query = $this->db->get();
        $reserved_seats = $query->result_array();

        // Mark all as 'Closed' if reserved
        $status_data = [];
        foreach ($reserved_seats as $row) {
            $status_data[] = (object)[
                'seat_id' => $row['seat_id'],
                'final_status' => 'Closed'
            ];
        }

        return $status_data;
    }
    public function get_all_seat_status_by_date_occupied($date)
    {
        $this->db->select('seat_id');
        $this->db->from('tbl_library_assign');
        $this->db->where('date_reserve', $date);
        $this->db->where('status', 'Occupied');
        $this->db->group_by('seat_id');

        $query = $this->db->get();
        $reserved_seats = $query->result_array();

        // Mark all as 'Closed' if reserved
        $status_data = [];
        foreach ($reserved_seats as $row) {
            $status_data[] = (object)[
                'seat_id' => $row['seat_id'],
                'final_status' => 'Closed'
            ];
        }

        return $status_data;
    }
    public function get_all_seat_status_by_date_closed($date)
    {
        $this->db->select('seat_id');
        $this->db->from('tbl_library_assign');
        $this->db->where('date_reserve', $date);
        $this->db->where('status', 'Closed');
        $this->db->group_by('seat_id');

        $query = $this->db->get();
        $reserved_seats = $query->result_array();

        // Mark all as 'Closed' if reserved
        $status_data = [];
        foreach ($reserved_seats as $row) {
            $status_data[] = (object)[
                'seat_id' => $row['seat_id'],
                'final_status' => 'Closed'
            ];
        }

        return $status_data;
    }
    public function get_all_seat_status_by_date_librarian($date)
    {
        $this->db->select('seat_id');
        $this->db->from('tbl_library_assign_disabled');
        $this->db->where('date_disabled', $date);
        $this->db->group_by('seat_id');

        $query = $this->db->get();
        $reserved_seats = $query->result_array();

        // Mark all as 'Closed' if reserved
        $status_data = [];
        foreach ($reserved_seats as $row) {
            $status_data[] = (object)[
                'seat_id' => $row['seat_id'],
                'final_status' => 'Closed'
            ];
        }

        return $status_data;
    }
}
