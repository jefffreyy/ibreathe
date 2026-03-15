<?php
/**
 * iBreathe SCADA - IoT Device Simulator
 *
 * Generates realistic air quality sensor data for 5 rooms and POSTs to the API.
 *
 * Usage: php simulator.php
 */

// Configuration
$BASE_URL = 'http://localhost/ibreathe/';
$INTERVAL = 10; // seconds between readings
$REGISTER_DEVICES = true;

// 5 Device definitions with room locations
$devices = array(
    array(
        'name'     => 'Living Room Sensor',
        'location' => 'Living Room',
        'key'      => ''
    ),
    array(
        'name'     => 'Bedroom Sensor',
        'location' => 'Bedroom',
        'key'      => ''
    ),
    array(
        'name'     => 'Kitchen Sensor',
        'location' => 'Kitchen',
        'key'      => ''
    ),
    array(
        'name'     => 'Bathroom Sensor',
        'location' => 'Bathroom',
        'key'      => ''
    ),
    array(
        'name'     => 'Garage Sensor',
        'location' => 'Garage',
        'key'      => ''
    )
);

// ==================================================
// Try to find existing devices first, then register
// ==================================================
echo "Checking for existing devices...\n";

foreach ($devices as &$device) {
    // Check if device already exists by name via lookup API
    $lookup = apiGet($BASE_URL . 'api/device/lookup?name=' . urlencode($device['name']));
    if ($lookup && isset($lookup['status']) && $lookup['status'] === 'found') {
        $device['key'] = $lookup['device_key'];
        echo "  Found existing: {$device['name']} (Key: {$device['key']})\n";
        continue;
    }

    // Register if not found
    if (empty($device['key']) && $REGISTER_DEVICES) {
        echo "Registering device: {$device['name']}...\n";
        $result = apiPost($BASE_URL . 'api/device/register', array(
            'name'     => $device['name'],
            'location' => $device['location'],
            'type'     => 'air_quality_monitor'
        ));
        if ($result && isset($result['device_key'])) {
            $device['key'] = $result['device_key'];
            echo "  -> Registered! Key: {$device['key']}\n";
        } else {
            echo "  -> Registration failed! Response: " . json_encode($result) . "\n";
        }
    }
}
unset($device);

// Check if we have valid keys
$validDevices = array_filter($devices, function($d) { return !empty($d['key']); });
if (empty($validDevices)) {
    echo "\nNo valid device keys. Exiting.\n";
    echo "Make sure:\n";
    echo "1. XAMPP Apache and MySQL are running\n";
    echo "2. Database 'ibreathe_db' exists with the schema imported\n";
    echo "3. The web app is accessible at {$BASE_URL}\n";
    exit(1);
}

echo "\n========================================\n";
echo "iBreathe SCADA Device Simulator\n";
echo "========================================\n";
echo "Devices: " . count($validDevices) . "\n";
echo "Interval: {$INTERVAL}s\n";
echo "API URL: {$BASE_URL}api/data\n";
echo "Press Ctrl+C to stop.\n";
echo "========================================\n\n";

// ==================================================
// Simulation state - each room has different baselines
// ==================================================
$state = array();
$roomProfiles = array(
    0 => array('temp' => 24.0, 'hum' => 50.0, 'co2' => 500, 'pm25' => 12.0),  // Living Room
    1 => array('temp' => 22.0, 'hum' => 55.0, 'co2' => 450, 'pm25' => 8.0),   // Bedroom
    2 => array('temp' => 26.0, 'hum' => 60.0, 'co2' => 600, 'pm25' => 25.0),  // Kitchen (warmer, more PM)
    3 => array('temp' => 25.0, 'hum' => 75.0, 'co2' => 400, 'pm25' => 5.0),   // Bathroom (humid)
    4 => array('temp' => 20.0, 'hum' => 40.0, 'co2' => 380, 'pm25' => 18.0),  // Garage (cooler, dusty)
);

foreach ($devices as $i => $d) {
    $profile = isset($roomProfiles[$i]) ? $roomProfiles[$i] : $roomProfiles[0];
    $state[$i] = array(
        'temperature' => $profile['temp'] + (rand(-20, 20) / 10),
        'humidity'    => $profile['hum'] + (rand(-100, 100) / 10),
        'co2'         => $profile['co2'] + rand(-50, 50),
        'pm25'        => $profile['pm25'] + (rand(-30, 30) / 10)
    );
}

$iteration = 0;

// ==================================================
// Main loop
// ==================================================
while (true) {
    $iteration++;
    $hour = (int) date('G');

    foreach ($devices as $i => $device) {
        if (empty($device['key'])) continue;

        $s = &$state[$i];
        $profile = isset($roomProfiles[$i]) ? $roomProfiles[$i] : $roomProfiles[0];

        // Temperature: drift with day cycle
        $dayFactor = sin(($hour - 6) * M_PI / 12);
        $s['temperature'] += (rand(-15, 15) / 10);
        $s['temperature'] += ($dayFactor * 0.1);
        // Kitchen gets warmer during cooking hours
        if ($i == 2 && (($hour >= 7 && $hour <= 9) || ($hour >= 17 && $hour <= 20))) {
            $s['temperature'] += 0.3;
        }
        $s['temperature'] = max(16, min(38, $s['temperature']));

        // Humidity: inversely correlated with temperature
        $s['humidity'] += (rand(-20, 20) / 10);
        $s['humidity'] -= (($s['temperature'] - $profile['temp']) * 0.15);
        // Bathroom stays humid
        if ($i == 3) {
            $s['humidity'] = max(60, min(95, $s['humidity']));
        } else {
            $s['humidity'] = max(25, min(85, $s['humidity']));
        }

        // CO2: higher during occupied hours
        $occupancyFactor = ($hour >= 7 && $hour <= 9) || ($hour >= 18 && $hour <= 22) ? 1.5 : 0.5;
        $s['co2'] += (rand(-30, 30)) * $occupancyFactor;
        $s['co2'] += ($occupancyFactor - 0.8) * 10;
        $s['co2'] = max(350, min(2500, $s['co2']));

        // PM2.5: cooking spikes in kitchen, dust in garage
        $cookingSpike = 0;
        if ($i == 2) { // Kitchen
            $cookingSpike = (rand(1, 100) <= 8) ? rand(30, 100) : 0;
        } elseif ($i == 4) { // Garage
            $cookingSpike = (rand(1, 100) <= 5) ? rand(10, 40) : 0;
        } else {
            $cookingSpike = (rand(1, 100) <= 2) ? rand(5, 30) : 0;
        }
        $s['pm25'] += (rand(-15, 15) / 10) + ($cookingSpike * 0.3);
        $s['pm25'] *= 0.98; // Natural decay
        $s['pm25'] = max(1, min(250, $s['pm25']));

        $readings = array(
            'temperature' => round($s['temperature'], 1),
            'humidity'    => round($s['humidity'], 1),
            'gas'         => round($s['pm25'], 1)
        );

        // POST data
        $result = apiPost($BASE_URL . 'api/data', array('readings' => $readings), $device['key']);

        $timestamp = date('H:i:s');
        $status = ($result && isset($result['status'])) ? $result['status'] : 'FAIL';
        $alarms = isset($result['alarms']) ? $result['alarms'] : 0;

        echo "[{$timestamp}] {$device['name']}: "
            . "T={$readings['temperature']}C "
            . "H={$readings['humidity']}% "
            . "CO2={$readings['co2']}µg/m³ "
            . "PM2.5={$readings['pm25']}ug/m3 "
            . "[{$status}]";

        if ($alarms > 0) echo " [!{$alarms} ALARMS]";
        echo "\n";
    }

    echo "--- Iteration #{$iteration} complete. Sleeping {$INTERVAL}s ---\n\n";
    sleep($INTERVAL);
}

// ==================================================
// HTTP helpers
// ==================================================
function apiPost($url, $data, $deviceKey = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $headers = array('Content-Type: application/json');
    if ($deviceKey) {
        $headers[] = 'X-Device-Key: ' . $deviceKey;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo "  [ERROR] cURL: " . curl_error($ch) . "\n";
        curl_close($ch);
        return null;
    }

    curl_close($ch);
    return json_decode($response, true);
}

function apiGet($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    // Send a session cookie so the API recognizes us as logged in
    // For device listing, we bypass auth by calling directly
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }

    curl_close($ch);
    return json_decode($response, true);
}
