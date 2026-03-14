<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'scada';

// API routes
$route['api/data'] = 'api/data';
$route['api/data/latest/(:num)'] = 'api/data_latest/$1';
$route['api/data/history/(:num)'] = 'api/data_history/$1';
$route['api/dashboard'] = 'api/dashboard';
$route['api/devices'] = 'api/devices_list';
$route['api/alarms/active'] = 'api/alarms_active';
$route['api/commands/(:num)'] = 'api/commands/$1';
$route['api/device/register'] = 'api/device_register';
$route['api/device/lookup'] = 'api/device_lookup';
$route['api/floorplan'] = 'api/floorplan';
$route['api/insights'] = 'api/insights';
$route['api/report_insights'] = 'api/report_insights';
$route['api/analytics'] = 'api/analytics';
$route['api/predictive'] = 'api/predictive';

// Floor plan
$route['scada/floorplan'] = 'scada/floorplan';

// $route['404_override'] = '';
// $route['404_override'] = 'errors/show_404';


/* End of file routes.php */
/* Location: ./application/config/routes.php */