<?php
// insert_data.php
// This file receives data from ESP32 and inserts it into MySQL database

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for JSON response
header('Content-Type: application/json');

// Database configuration - use 'localhost' since PHP runs on the same server as MySQL
$db_host = 'localhost';
$db_user = 'ibreathe_db';
$db_pass = 'ibreathe_db';
$db_name = 'ibreathe_db';

// API authentication - change this to a secure key
$valid_api_key = 'esp32_2024_secure_key_123';

// Get POST data
$device_id = isset($_POST['device_id']) ? $_POST['device_id'] : '';
$sensor_type = isset($_POST['sensor_type']) ? $_POST['sensor_type'] : '';
$value = isset($_POST['value']) ? $_POST['value'] : '';
$api_key = isset($_POST['api_key']) ? $_POST['api_key'] : '';

// Log received data (optional - for debugging)
error_log("Received: device_id=$device_id, sensor_type=$sensor_type, value=$value");

// Validate API key
if ($api_key !== $valid_api_key) {
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid API key'
    ]);
    exit;
}

// Validate required fields
if (empty($device_id)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Device ID is required'
    ]);
    exit;
}

if (empty($sensor_type)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Sensor type is required'
    ]);
    exit;
}

if ($value === '') {
    echo json_encode([
        'success' => false, 
        'error' => 'Value is required'
    ]);
    exit;
}

// Validate sensor type
$valid_sensor_types = ['temperature', 'humidity', 'gas_analog'];
if (!in_array($sensor_type, $valid_sensor_types)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid sensor type. Must be: ' . implode(', ', $valid_sensor_types)
    ]);
    exit;
}

// Validate value is numeric
if (!is_numeric($value)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Value must be numeric'
    ]);
    exit;
}

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        'success' => false, 
        'error' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Prepare SQL statement to prevent SQL injection
$sql = "INSERT INTO tbl_scada_alarms_clone (device_id, sensor_type, value, triggered_at) 
        VALUES (?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false, 
        'error' => 'Prepare failed: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

// Bind parameters
$stmt->bind_param("ssd", $device_id, $sensor_type, $value);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Data inserted successfully',
        'insert_id' => $stmt->insert_id,
        'data' => [
            'device_id' => $device_id,
            'sensor_type' => $sensor_type,
            'value' => $value,
            'triggered_at' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Insert failed: ' . $stmt->error
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>