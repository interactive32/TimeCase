<?php
/**
 * @package PROJECTS
 *
 * APPLICATION-WIDE CONFIGURATION SETTINGS
 *
 * This file contains application-wide configuration settings.  The settings
 * here will be the same regardless of the machine on which the app is running.
 *
 * This configuration should be added to version control.
 *
 * No settings should be added to this file that would need to be changed
 * on a per-machine basic (ie local, staging or production).  Any
 * machine-specific settings should be added to _machine_config.php
 */


/**
 * Number of records per page (pagination)
 */
GlobalConfig::$DEFAULT_PAGE_SIZE = 10;

/**
 * LONG POLLING (auto-refresh table data with AJAX)
 * duration in miliseconds.  (5000 = recommended, 0 = disabled)
 * warning: setting this to a low number will increase server load
 */
GlobalConfig::$LONG_POLLING_DURATION = 0;




/**
 * 
 * Advanced/Developer settings after this line
 ********************************************************************************************************
 */


/**
 * Use session timer instead of database, timer will reset if you logout, useful on demo
 */
GlobalConfig::$USE_SESSION_TIMER = false;


/**
 * APPLICATION ROOT DIRECTORY
 * If the application doesn't detect this correctly then it can be set explicitly
 */
GlobalConfig::$APP_ROOT = realpath("./");



/**
 * INCLUDE PATH
 * Adjust the include path as necessary so PHP can locate required libraries
 */
set_include_path(
		GlobalConfig::$APP_ROOT . '/libs/' . PATH_SEPARATOR .
		GlobalConfig::$APP_ROOT . '/libs/phreeze/libs/' . PATH_SEPARATOR .
		get_include_path()
);

/**
 * SESSION CLASSES
 * Any classes that will be stored in the session can be added here
 * and will be pre-loaded on every page
 */
require_once "Controller/UserController.php";

/**
 * RENDER ENGINE
 */
require_once 'verysimple/Phreeze/SavantRenderEngine.php';
GlobalConfig::$TEMPLATE_ENGINE = 'SavantRenderEngine';
GlobalConfig::$TEMPLATE_PATH = GlobalConfig::$APP_ROOT . '/templates/';

/**
 * ROUTE MAP
 * The route map connects URLs to Controller+Method and additionally maps the
 * wildcards to a named parameter so that they are accessible inside the
 * Controller without having to parse the URL for parameters such as IDs
 */
GlobalConfig::$ROUTE_MAP = array(

	// default controller when no route specified
	'GET:' => array('route' => 'Default.Home'),
		
	// Authentication routes
	'GET:loginform' => array('route' => 'User.LoginForm'),
	'POST:loginattempt' => array('route' => 'User.LoginAttempt'),
	'GET:logout' => array('route' => 'User.Logout'),
	'GET:accountsettings' => array('route' => 'User.AccountSettingsForm'),
	'POST:accountsettings' => array('route' => 'User.AccountSettingsForm'),
		
	// Common calls 
	'GET:api/getcurrentuser' => array('route' => 'Default.ReadCurrentUser'),

	// Reports
	'GET:reports' => array('route' => 'Reports.ListView'),
	'GET:api/reports' => array('route' => 'Reports.Query'),
	'GET:report/(:any)' => array('route' => 'Reports.Query', 'params' => array('type' => 1)),
	
	// Category
	'GET:categories' => array('route' => 'Category.ListView'),
	'GET:category/(:num)' => array('route' => 'Category.SingleView', 'params' => array('id' => 1)),
	'GET:api/categories' => array('route' => 'Category.Query'),
	'POST:api/category' => array('route' => 'Category.Create'),
	'GET:api/category/(:num)' => array('route' => 'Category.Read', 'params' => array('id' => 2)),
	'POST:api/category/(:num)' => array('route' => 'Category.Update', 'params' => array('id' => 2)),
	'POST:api/category/(:num)/delete/1' => array('route' => 'Category.Delete', 'params' => array('id' => 2)),
		
	// Customer
	'GET:customers' => array('route' => 'Customer.ListView'),
	'GET:customer/(:num)' => array('route' => 'Customer.SingleView', 'params' => array('id' => 1)),
	'GET:api/customers/(:any)' => array('route' => 'Customer.Query', 'params' => array('filter' => 2)),
	'POST:api/customer' => array('route' => 'Customer.Create'),
	'GET:api/customer/(:num)' => array('route' => 'Customer.Read', 'params' => array('id' => 2)),
	'POST:api/customer/(:num)' => array('route' => 'Customer.Update', 'params' => array('id' => 2)),
	'POST:api/customer/(:num)/delete/1' => array('route' => 'Customer.Delete', 'params' => array('id' => 2)),
		
	// Level
	'GET:api/levels' => array('route' => 'Level.Query'),
		
	// Project
	'GET:projects' => array('route' => 'Project.ListView'),
	'GET:project/(:num)' => array('route' => 'Project.SingleView', 'params' => array('id' => 1)),
	'GET:api/projects/(:any)' => array('route' => 'Project.Query', 'params' => array('filter' => 2)),
	'POST:api/project' => array('route' => 'Project.Create'),
	'GET:api/project/(:num)' => array('route' => 'Project.Read', 'params' => array('id' => 2)),
	'POST:api/project/(:num)' => array('route' => 'Project.Update', 'params' => array('id' => 2)),
	'POST:api/project/(:num)/delete/1' => array('route' => 'Project.Delete', 'params' => array('id' => 2)),
		
	// Status
	'GET:statuses' => array('route' => 'Status.ListView'),
	'GET:status/(:num)' => array('route' => 'Status.SingleView', 'params' => array('id' => 1)),
	'GET:api/statuses' => array('route' => 'Status.Query'),
	'POST:api/status' => array('route' => 'Status.Create'),
	'GET:api/status/(:num)' => array('route' => 'Status.Read', 'params' => array('id' => 2)),
	'POST:api/status/(:num)' => array('route' => 'Status.Update', 'params' => array('id' => 2)),
	'POST:api/status/(:num)/delete/1' => array('route' => 'Status.Delete', 'params' => array('id' => 2)),
		
	// TimeEntry
	'GET:timeentries' => array('route' => 'TimeEntry.ListView'),
	'GET:timeentry/(:num)' => array('route' => 'TimeEntry.SingleView', 'params' => array('id' => 1)),
	'GET:api/timeentries' => array('route' => 'TimeEntry.Query'),
	'POST:api/timeentry' => array('route' => 'TimeEntry.Create'),
	'GET:api/timeentry/(:num)' => array('route' => 'TimeEntry.Read', 'params' => array('id' => 2)),
	'POST:api/timeentry/(:num)' => array('route' => 'TimeEntry.Update', 'params' => array('id' => 2)),
	'POST:api/timeentry/(:num)/delete/1' => array('route' => 'TimeEntry.Delete', 'params' => array('id' => 2)),
	'GET:api/starttimetracking' => array('route' => 'TimeEntry.StartTimeTracking'),
	'GET:api/stoptimetracking' => array('route' => 'TimeEntry.StopTimeTracking'),
	'GET:api/checktimetracking' => array('route' => 'TimeEntry.CheckTimeTracking'),
	'POST:api/updatedefaults' => array('route' => 'TimeEntry.UpdateDefaults'),	
		
	// User
	'GET:users' => array('route' => 'User.ListView'),
	'GET:user/(:num)' => array('route' => 'User.SingleView', 'params' => array('id' => 1)),
	'GET:api/users' => array('route' => 'User.Query'),
	'POST:api/user' => array('route' => 'User.Create'),
	'GET:api/user/(:num)' => array('route' => 'User.Read', 'params' => array('id' => 2)),
	'POST:api/user/(:num)' => array('route' => 'User.Update', 'params' => array('id' => 2)),
	'POST:api/user/(:num)/delete/1' => array('route' => 'User.Delete', 'params' => array('id' => 2)),
		
	// catch any broken API urls
	'GET:api/(:any)' => array('route' => 'Default.ErrorApi404'),
	'POST:api/(:any)' => array('route' => 'Default.ErrorApi404'),
	'POST:api/(:any)/delete/1' => array('route' => 'Default.ErrorApi404')
);
