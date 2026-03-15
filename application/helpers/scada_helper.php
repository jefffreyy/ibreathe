<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Format sensor value with appropriate unit
 */
function format_sensor_value($value, $type) {
    $units = array(
        'temperature' => '°C',
        'humidity'    => '%',
        'co2'         => ' µg/m³',
        'gas'         => ' μg/m³',
        'co'          => ' ppm'
    );
    $decimals = ($type === 'co2' || $type === 'co') ? 0 : 1;
    return number_format($value, $decimals) . (isset($units[$type]) ? $units[$type] : '');
}

/**
 * Get sensor display name
 */
function sensor_display_name($type) {
    $names = array(
        'temperature' => 'Temperature',
        'humidity'    => 'Humidity',
        'co2'         => 'CO₂',
        'gas'         => 'PM2.5',
        'co'          => 'CO'
    );
    return isset($names[$type]) ? $names[$type] : ucfirst($type);
}

/**
 * Get sensor icon class (Font Awesome)
 */
function sensor_icon($type) {
    $icons = array(
        'temperature' => 'fas fa-thermometer-half',
        'humidity'    => 'fas fa-tint',
        'co2'         => 'fas fa-cloud',
        'gas'         => 'fas fa-smog',
        'co'          => 'fas fa-skull-crossbones'
    );
    return isset($icons[$type]) ? $icons[$type] : 'fas fa-chart-line';
}

/**
 * Device status badge HTML
 */
function device_status_badge($status) {
    $badges = array(
        'online'      => '<span class="badge bg-success">Online</span>',
        'offline'     => '<span class="badge bg-secondary">Offline</span>',
        'maintenance' => '<span class="badge bg-warning text-dark">Maintenance</span>'
    );
    return isset($badges[$status]) ? $badges[$status] : '<span class="badge bg-dark">' . htmlspecialchars($status) . '</span>';
}

/**
 * Alarm severity badge HTML
 */
function severity_badge($severity) {
    $badges = array(
        'info'     => '<span class="badge bg-info">Info</span>',
        'warning'  => '<span class="badge bg-warning text-dark">Warning</span>',
        'critical' => '<span class="badge bg-danger">Critical</span>'
    );
    return isset($badges[$severity]) ? $badges[$severity] : '<span class="badge bg-secondary">' . htmlspecialchars($severity) . '</span>';
}

/**
 * Human-readable time ago
 */
function time_ago($datetime) {
    if (empty($datetime)) return 'Never';
    $now = time();
    $diff = $now - strtotime($datetime);

    if ($diff < 5)     return 'Just now';
    if ($diff < 60)    return $diff . ' seconds ago';
    if ($diff < 3600)  return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M j, Y', strtotime($datetime));
}
