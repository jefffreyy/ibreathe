<?php if(!empty($esp_data)): ?>
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Sensor Type</th>
                <th>Device ID</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($esp_data as $row): ?>
                <tr>
                    <td><span class="badge badge-primary"><?php echo $row->id; ?></span></td>
                    <td>
                        <?php
                            $sensor_display = array('temperature'=>'Temperature','humidity'=>'Humidity','gas'=>'PM2.5','co'=>'CO');
                            echo isset($sensor_display[$row->sensor_type]) ? $sensor_display[$row->sensor_type] : htmlspecialchars($row->sensor_type);
                        ?>
                        <?php if($row->sensor_type == 'humidity'): ?>
                            <span class="badge badge-info">💧</span>
                        <?php elseif($row->sensor_type == 'gas'): ?>
                            <span class="badge badge-warning">⚠️</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-secondary">Device <?php echo $row->device_id; ?></span></td>
                    <td>
                        <?php if($row->sensor_type == 'humidity'): ?>
                            <strong><?php echo $row->value; ?>%</strong>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-info" style="width: <?php echo $row->value; ?>%"></div>
                            </div>
                        <?php else: ?>
                            <strong><?php echo $row->value; ?></strong>
                            <?php if($row->value > 0): ?>
                                <span class="badge badge-danger">PM2.5 Detected!</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo site_url('esp/view/'.$row->id); ?>" class="btn btn-sm btn-info">View</a>
                        <a href="<?php echo site_url('esp/edit/'.$row->id); ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="<?php echo site_url('esp/delete/'.$row->id); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-warning">No records found in tbl_esp</div>
<?php endif; ?>