/**
 * iBreathe SCADA - Unified Dashboard (Floor Plan + Dashboard Widgets)
 */

// Room-to-SVG element mapping
var roomMap = {
    'Living Room': 'device-living-room',
    'Bedroom':     'device-bedroom',
    'Kitchen':     'device-kitchen',
    'Bathroom':    'device-bathroom',
    'Garage':      'device-garage'
};

var previousAlarmCount = 0;
var cachedInsights = [];
var currentFilter = 'all';

$(document).ready(function() {
    console.log("Document Ready");
    pollFloorPlan();
    pollFloorPlanInsights();
    pollDashboard();
    setInterval(pollFloorPlan, POLLING_INTERVAL);
    setInterval(pollFloorPlanInsights, POLLING_INTERVAL * 3);
    setInterval(pollDashboard, POLLING_INTERVAL);
 console.log("Document Ready");
    // Insights filter buttons
    $(document).on('click', '#insights-filter .btn', function() {
        $('#insights-filter .btn').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        renderFloorPlanInsights(cachedInsights);
    });
});

// ==================== FLOOR PLAN POLLING ====================

function pollFloorPlan() {
    $.ajax({
        url: BASE_URL + 'api/floorplan',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
             console.log("Floorplan data received:", data);
            if (data.devices) {
                updateFloorPlan(data.devices);
                
            }
        },
        error: function() {}
    });
}

function updateFloorPlan(devices) {
    devices.forEach(function(device) {
        var elementId = roomMap[device.location];
        if (!elementId) {
            Object.keys(roomMap).forEach(function(key) {
                if (device.location.indexOf(key) !== -1 || device.name.indexOf(key) !== -1) {
                    elementId = roomMap[key];
                }
            });
        }
        if (!elementId) return;

        var $group = $('#' + elementId);
        if ($group.length === 0) return;

        // Update status LED
        var $led = $group.find('.status-led');
        $led.removeClass('led-online led-offline led-maintenance');
        if (device.status === 'online') {
            $led.addClass('led-online');
        } else if (device.status === 'maintenance') {
            $led.addClass('led-maintenance');
        } else {
            $led.addClass('led-offline');
        }

        // Update status text
        var statusText = device.status.charAt(0).toUpperCase() + device.status.slice(1);
        if (device.last_seen) {
            statusText += ' • Last: ' + device.last_seen.split(' ')[1];
        }
        $group.find('.device-status').text(statusText);

        // Update sensor values
        var sensors = device.sensors;
        if (sensors) {
            if (sensors.temperature) $group.find('.val-temp').text(sensors.temperature.value.toFixed(1));
            if (sensors.humidity) $group.find('.val-hum').text(sensors.humidity.value.toFixed(1));
  // Gas - with null check
    var $gasElement = $group.find('.val-gas');
    if ($gasElement.length) {
        if (sensors.gas && sensors.gas.value != null) {
            $gasElement.text(sensors.gas.value.toFixed(1));
        } else {
            $gasElement.text('--');
        }
    }
    // CO - computed value
    var $coElement = $group.find('.val-co');
    if ($coElement.length) {
        if (sensors.co && sensors.co.value != null) {
            $coElement.text(sensors.co.value.toFixed(1));
        } else {
            $coElement.text('--');
        }
    }
       }

        // Highlight high values
        if (sensors && sensors.temperature && sensors.temperature.value > 35) {
            $group.find('.val-temp').attr('fill', '#dc2626');
        } else {
            $group.find('.val-temp').attr('fill', '#1e293b');
        }
        if (sensors && sensors.gas && sensors.gas.value > 50) {
            $group.find('.val-gas').attr('fill', '#dc2626');
        } else if (sensors && sensors.gas && sensors.gas.value > 10) {
            $group.find('.val-gas').attr('fill', '#d97706');
        } else {
            $group.find('.val-gas').attr('fill', '#1e293b');
        }
        // CO highlight
        if (sensors && sensors.co && sensors.co.value > 35) {
            $group.find('.val-co').attr('fill', '#dc2626');
        } else if (sensors && sensors.co && sensors.co.value > 9) {
            $group.find('.val-co').attr('fill', '#d97706');
        } else {
            $group.find('.val-co').attr('fill', '#1e293b');
        }
    });
}

// ==================== DASHBOARD POLLING ====================

function pollDashboard() {
    $.ajax({
        url: BASE_URL + 'api/dashboard',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            updateDeviceStatus(data.devices);
            updateAlarmTicker(data.alarms);
            updateAQI(data.aqi);
            updateStats(data);
            $('#last-update').text('Last update: ' + data.timestamp.split(' ')[1]);
            $('#connection-status').removeClass('badge-danger').addClass('badge-success').html('<i class="fas fa-circle mr-1" style="font-size:8px"></i>Live');
        },
        error: function() {
            $('#connection-status').removeClass('badge-success').addClass('badge-danger').html('<i class="fas fa-circle mr-1" style="font-size:8px"></i>Disconnected');
        }
    });
}

function updateDeviceStatus(devices) {
    if (!devices || devices.length === 0) return;

    var html = '';
    var onlineCount = 0;

    devices.forEach(function(d) {
        if (d.status === 'online') onlineCount++;
        var badge = '<span class="badge badge-secondary">Offline</span>';
        if (d.status === 'online') badge = '<span class="badge badge-success">Online</span>';
        else if (d.status === 'maintenance') badge = '<span class="badge badge-warning">Maint.</span>';
        var lastSeen = d.last_seen ? d.last_seen : 'Never';
        html += '<tr><td>' + escapeHtml(d.name) + '</td><td>' + badge + '</td><td class="text-muted" style="font-size:12px">' + lastSeen + '</td></tr>';
    });

    $('#device-status-table').html(html);
    $('#stat-online').text(onlineCount);
}

function updateAlarmTicker(alarms) {
    if (!alarms || alarms.length === 0) {
        $('#alarm-ticker').hide();
        $('#recent-alarms-list').html('<p class="text-muted text-center p-3 mb-0">No active alarms</p>');
        $('#stat-alarms').text('0');
        return;
    }

    var latest = alarms[0];
    $('#alarm-ticker-text').text('[' + latest.severity.toUpperCase() + '] ' + latest.device_name + ' - ' + latest.message + ' (Value: ' + latest.value + ')');
    $('#alarm-ticker').show();
    $('#stat-alarms').text(alarms.length);

    // Toastr for new alarms
    if (alarms.length > previousAlarmCount && previousAlarmCount > 0) {
        var newAlarm = alarms[0];
        if (newAlarm.severity === 'critical') {
            toastr.error(newAlarm.message, newAlarm.device_name + ' - CRITICAL');
        } else if (newAlarm.severity === 'warning') {
            toastr.warning(newAlarm.message, newAlarm.device_name + ' - Warning');
        } else {
            toastr.info(newAlarm.message, newAlarm.device_name);
        }
    }
    previousAlarmCount = alarms.length;

    // Recent alarms list
    var html = '';
    alarms.slice(0, 8).forEach(function(a) {
        var time = a.triggered_at.split(' ')[1].substring(0, 5);
        html += '<div class="alarm-item">'
            + '<span class="alarm-severity ' + a.severity + '"></span>'
            + '<strong>' + escapeHtml(a.device_name) + '</strong> - ' + escapeHtml(a.message)
            + '<span class="alarm-time">' + time + '</span>'
            + '</div>';
    });
    $('#recent-alarms-list').html(html);
}

function updateAQI(aqi) {
    if (!aqi) return;
    var deviceId = Object.keys(aqi)[0];
    if (!deviceId) return;
    var data = aqi[deviceId];
    $('#stat-aqi').text(data.value);
    $('#stat-aqi-label').text(data.label);
    $('#aqi-box').css('background', 'linear-gradient(135deg, ' + data.color + '88, ' + data.color + '44)');
}

function updateStats(data) {
    if (data.devices) {
        $('#stat-devices').text(data.devices.length);
    }
    if (typeof data.alarm_count !== 'undefined') {
        $('#stat-alarms').text(data.alarm_count);
    }
}

// ==================== AI INSIGHTS ====================

function pollFloorPlanInsights() {
    $.ajax({
        url: BASE_URL + 'api/insights',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.insights) {
                renderFloorPlanInsights(data.insights);
                var cnt = data.count || data.insights.length;
                $('#fp-insights-count').text(cnt + ' insight' + (cnt !== 1 ? 's' : ''));
            }
            if (typeof data.ml_available !== 'undefined') {
                updateMlStatus(data.ml_available);
            }
            if (data.forecasts && Object.keys(data.forecasts).length > 0) {
                renderForecastCard(data.forecasts);
            }
        },
        error: function() {
            $('#fp-insights-list').html(
                '<p class="text-muted text-center p-3 mb-0"><i class="fas fa-exclamation-circle mr-1"></i>Unable to load insights</p>'
            );
        }
    });
}

function renderFloorPlanInsights(insights) {
    // Cache for filter switching
    if (insights && insights.length > 0) {
        cachedInsights = insights;
    }

    // Apply current filter
    var filtered = cachedInsights;
    if (currentFilter === 'suggestion') {
        filtered = cachedInsights.filter(function(i) { return i.category === 'suggestion'; });
    }

    if (!filtered || filtered.length === 0) {
        var emptyMsg = currentFilter === 'suggestion'
            ? 'No actionable suggestions — all conditions are normal'
            : 'All systems normal — no issues detected';
        $('#fp-insights-list').html(
            '<div class="text-center p-3"><i class="fas fa-check-circle mr-1" style="color:#16a34a"></i><span class="text-muted" style="font-size:13px">' + emptyMsg + '</span></div>'
        );
        $('#fp-insights-count').text(cachedInsights.length + ' insight' + (cachedInsights.length !== 1 ? 's' : ''));
        return;
    }

    var html = '';
    filtered.forEach(function(insight) {
        var pulseClass = (insight.severity === 'critical') ? ' critical-pulse' : '';
        var severityDot = {
            'critical': '#dc2626',
            'warning':  '#d97706',
            'info':     '#2563eb',
            'good':     '#16a34a'
        };
        var dotColor = severityDot[insight.severity] || '#94a3b8';
        var mlBadge = (insight.source === 'ml') ? ' <span class="ml-badge">ML</span>' : '';
        var isSuggestion = (insight.category === 'suggestion');
        var actionBadge = (isSuggestion && insight.action) ? ' <span class="suggestion-badge"><i class="fas fa-lightbulb mr-1"></i>' + escapeHtml(insight.action) + '</span>' : '';

        html += '<div class="fp-insight-item' + pulseClass + (isSuggestion ? ' suggestion-item' : '') + '">'
            + '<div class="fp-insight-dot" style="background:' + dotColor + '"></div>'
            + '<div class="fp-insight-icon ' + escapeHtml(insight.severity) + '">'
            + '<i class="' + escapeHtml(insight.icon) + '"></i>'
            + '</div>'
            + '<div class="fp-insight-body">'
            + '<span class="fp-insight-msg">' + escapeHtml(insight.message) + '</span>'
            + '<div class="mt-1">'
            + '<span class="fp-insight-tag">' + escapeHtml(insight.category) + '</span>'
            + mlBadge
            + actionBadge
            + '</div>'
            + '</div>'
            + '</div>';
    });

    $('#fp-insights-list').html(html);
    $('#fp-insights-count').text(filtered.length + (currentFilter !== 'all' ? '/' + cachedInsights.length : '') + ' insight' + (filtered.length !== 1 ? 's' : ''));
}

// ==================== ML STATUS & FORECAST ====================

function updateMlStatus(available) {
    var $b = $('#ml-status-badge');
    if ($b.length === 0) return;
    if (available) {
        $b.removeClass('badge-secondary').addClass('badge-success')
          .html('<i class="fas fa-robot mr-1"></i>ML Active');
    } else {
        $b.removeClass('badge-success').addClass('badge-secondary')
          .html('<i class="fas fa-robot mr-1"></i>ML Offline');
    }
}

function renderForecastCard(forecasts) {
    var $card = $('#forecast-card-body');
    if ($card.length === 0) return;

    var sensorInfo = {
        'temperature': { label: 'Temp', unit: '°C', icon: 'fas fa-thermometer-half', color: '#ef4444' },
        'humidity':    { label: 'Humidity', unit: '%', icon: 'fas fa-tint', color: '#6366f1' },
        'gas':         { label: 'PM2.5', unit: 'µg/m³', icon: 'fas fa-smog', color: '#10b981' },
        'co':          { label: 'CO', unit: 'ppm', icon: 'fas fa-skull-crossbones', color: '#f59e0b' }
    };

    var html = '<div class="forecast-header-row"><div class="forecast-sensor"></div>'
        + '<div class="forecast-values">'
        + '<div class="forecast-cell-hdr">30min</div>'
        + '<div class="forecast-cell-hdr">1hr</div>'
        + '<div class="forecast-cell-hdr">2hr</div>'
        + '</div><div class="forecast-trend"></div></div>';

    $.each(forecasts, function(sensorType, data) {
        if (!data.predictions || data.predictions.length === 0) return;
        var info = sensorInfo[sensorType] || { label: sensorType, unit: '', icon: 'fas fa-chart-line', color: '#64748b' };

        var trendIcon = data.trend_per_hour > 0.5 ? 'fa-arrow-up' :
                        data.trend_per_hour < -0.5 ? 'fa-arrow-down' : 'fa-minus';
        var trendColor = data.trend_per_hour > 0.5 ? '#ef4444' :
                         data.trend_per_hour < -0.5 ? '#22c55e' : '#94a3b8';

        html += '<div class="forecast-row">';
        html += '<div class="forecast-sensor"><i class="' + info.icon + '" style="color:' + info.color + '"></i> ' + info.label + '</div>';
        html += '<div class="forecast-values">';

        data.predictions.forEach(function(p) {
            html += '<div class="forecast-cell">'
                + '<div class="forecast-value">' + p.predicted_value.toFixed(1) + '</div>'
                + '<div class="forecast-range">' + p.confidence_lower.toFixed(1) + '-' + p.confidence_upper.toFixed(1) + '</div>'
                + '</div>';
        });

        html += '</div>';
        html += '<div class="forecast-trend"><i class="fas ' + trendIcon + '" style="color:' + trendColor + '"></i></div>';
        html += '</div>';
    });

    $card.html(html);
    $('#forecast-card').show();
}

// ==================== UTILITY ====================

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
