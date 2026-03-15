<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ESP Data' : 'ESP Data Management'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { padding: 20px; background-color: #f8f9fa; }
        .stat-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .latest-badge {
            background-color: #28a745;
            color: white;
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 12px;
            margin-left: 10px;
        }
        .refresh-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #28a745;
            border-radius: 50%;
            margin-right: 5px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.3;
            }
            100% {
                opacity: 1;
            }
        }
        .last-update {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .table-container {
            position: relative;
            min-height: 200px;
        }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .loading-overlay.show {
            display: flex;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 rounded">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo site_url('esp'); ?>">ESP Data Management</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo site_url('esp'); ?>">Latest Records</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('esp/all_records'); ?>">All Records</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('esp/create'); ?>">Add New Record</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Statistics Cards - Static for now -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Records</h5>
                        <h2 class="card-text" id="total-records"><?php echo $statistics['total_records']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Active Devices</h5>
                        <h2 class="card-text" id="total-devices"><?php echo $statistics['total_devices']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Sensor Types</h5>
                        <h2 class="card-text" id="sensor-types">
                            <?php 
                            $sensor_list = array();
                            foreach($statistics['sensor_types'] as $sensor) {
                                $sensor_list[] = ucfirst($sensor->sensor_type);
                            }
                            echo implode(', ', $sensor_list);
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <?php echo $title; ?>
                                <span class="latest-badge">Latest Only</span>
                            </h4>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <span class="refresh-indicator"></span>
                                    <span class="last-update text-white" id="last-update">Updating...</span>
                                </div>
                                <a href="<?php echo site_url('esp/create'); ?>" class="btn btn-sm btn-light">Add New Record</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('success'); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('error'); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <strong>Note:</strong> Showing only the latest record for each sensor type per device. 
                            Data automatically refreshes every 5 seconds.
                            <a href="<?php echo site_url('esp/all_records'); ?>" class="alert-link">View all records</a>
                        </div>

                        <div class="table-container">
                            <div class="loading-overlay" id="loading-overlay">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            
                            <div class="table-responsive" id="data-table-container">
                                <!-- Table will be loaded here via AJAX -->
                                <?php $this->load->view('esp/partials/data_table', ['esp_data' => $esp_data]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Auto refresh every 5 seconds (5000 milliseconds)
            var refreshInterval = 5000; // Change this value to adjust refresh rate
            var refreshTimer;
            
            // Function to load latest data
            function loadLatestData() {
                // Show loading overlay
                $('#loading-overlay').addClass('show');
                
                // Make AJAX call to get latest data
                $.ajax({
                    url: '<?php echo site_url('esp/api_get_latest'); ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Update the table with new data
                        updateTable(data);
                        
                        // Update last update time
                        var now = new Date();
                        var timeString = now.toLocaleTimeString();
                        $('#last-update').text('Last updated: ' + timeString);
                        
                        // Hide loading overlay
                        $('#loading-overlay').removeClass('show');
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading data: ' + error);
                        $('#last-update').text('Error updating data');
                        $('#loading-overlay').removeClass('show');
                    }
                });
            }
            
            // Function to update table with new data
            function updateTable(data) {
                var tableHtml = '';
                
                if (data && data.length > 0) {
                    tableHtml += '<table class="table table-bordered table-striped table-hover">';
                    tableHtml += '<thead class="thead-dark">';
                    tableHtml += '<tr>';
                    tableHtml += '<th>ID</th>';
                    tableHtml += '<th>Sensor Type</th>';
                    tableHtml += '<th>Device ID</th>';
                    tableHtml += '<th>Value</th>';
                    tableHtml += '<th>Actions</th>';
                    tableHtml += '</tr>';
                    tableHtml += '</thead>';
                    tableHtml += '<tbody>';
                    
                    $.each(data, function(index, row) {
                        tableHtml += '<tr>';
                        tableHtml += '<td><span class="badge badge-primary">' + row.id + '</span></td>';
                        tableHtml += '<td>' + row.sensor_type;
                        
                        if (row.sensor_type == 'humidity') {
                            tableHtml += ' <span class="badge badge-info">💧</span>';
                        } else if (row.sensor_type == 'gas') {
                            tableHtml += ' <span class="badge badge-warning">⚠️</span>';
                        }
                        
                        tableHtml += '</td>';
                        tableHtml += '<td><span class="badge badge-secondary">Device ' + row.device_id + '</span></td>';
                        tableHtml += '<td>';
                        
                        if (row.sensor_type == 'humidity') {
                            tableHtml += '<strong>' + row.value + '%</strong>';
                            tableHtml += '<div class="progress" style="height: 5px;">';
                            tableHtml += '<div class="progress-bar bg-info" style="width: ' + row.value + '%"></div>';
                            tableHtml += '</div>';
                        } else {
                            tableHtml += '<strong>' + row.value + '</strong>';
                            if (parseFloat(row.value) > 0) {
                                tableHtml += ' <span class="badge badge-danger">PM2.5 Detected!</span>';
                            }
                        }
                        
                        tableHtml += '</td>';
                        tableHtml += '<td>';
                        tableHtml += '<a href="<?php echo site_url('esp/view/'); ?>' + row.id + '" class="btn btn-sm btn-info">View</a> ';
                        tableHtml += '<a href="<?php echo site_url('esp/edit/'); ?>' + row.id + '" class="btn btn-sm btn-warning">Edit</a> ';
                        tableHtml += '<a href="<?php echo site_url('esp/delete/'); ?>' + row.id + '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a>';
                        tableHtml += '</td>';
                        tableHtml += '</tr>';
                    });
                    
                    tableHtml += '</tbody>';
                    tableHtml += '</table>';
                    
                    // Update statistics if needed (you can add more AJAX calls for stats)
                    updateStatistics(data);
                    
                } else {
                    tableHtml = '<div class="alert alert-warning">No records found in tbl_esp</div>';
                }
                
                $('#data-table-container').html(tableHtml);
            }
            
            // Function to update statistics (optional - you can create another API endpoint for this)
            function updateStatistics(data) {
                // Count unique devices
                var devices = [];
                var sensorTypes = [];
                
                $.each(data, function(index, row) {
                    if (devices.indexOf(row.device_id) === -1) {
                        devices.push(row.device_id);
                    }
                    if (sensorTypes.indexOf(row.sensor_type) === -1) {
                        sensorTypes.push(row.sensor_type);
                    }
                });
                
                $('#total-devices').text(devices.length);
                $('#sensor-types').text(sensorTypes.join(', '));
                
                // You might want to get total records count from a separate API
                // For now, we'll leave it as is or you can create another API endpoint
            }
            
            // Start auto refresh
            function startAutoRefresh() {
                // Clear any existing timer
                if (refreshTimer) {
                    clearInterval(refreshTimer);
                }
                
                // Set new timer
                refreshTimer = setInterval(loadLatestData, refreshInterval);
            }
            
            // Initial load
            loadLatestData();
            
            // Start auto refresh
            startAutoRefresh();
            
            // Optional: Stop auto refresh when user is interacting with the page
            $(document).on('mouseenter', '.table-container', function() {
                clearInterval(refreshTimer);
                $('#last-update').append(' <span class="badge badge-warning">Paused</span>');
            }).on('mouseleave', '.table-container', function() {
                startAutoRefresh();
                $('#last-update').text('Last updated: ' + new Date().toLocaleTimeString());
            });
            
            // Manual refresh button (optional - you can add a button in the header)
            // For now, we'll add a keyboard shortcut: Ctrl+R
            $(document).keydown(function(e) {
                if (e.ctrlKey && e.keyCode === 82) {
                    e.preventDefault();
                    loadLatestData();
                }
            });
        });
    </script>
</body>
</html>