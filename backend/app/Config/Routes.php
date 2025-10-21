<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->group('gender_analytics/api/v1', function($routes){
	$routes->group('auth', function($routes){
		$routes->post('login', 'Auth::login');
		$routes->post('firstLoginChange', 'Auth::firstLoginChangePassword');
	}); 

	$routes->group('users', function($routes){
		$routes->post('create', 'Users::registerUser');
		$routes->post('update', 'Users::updateUser');
		$routes->post('update/status', 'Users::updateUserStatus');
		$routes->post('changePassword', 'Users::ChangePassword');
		$routes->get('getUsersList', 'Users::getAllUserList');
		$routes->post('getUserById', 'Users::getUserDetails');
	});

	$routes->group('analytics', function($routes){
		$routes->post('add/new', 'Analytics::addAnalyticsData');
		$routes->post('delete/data', 'Analytics::deleteAnalyticsData');
		$routes->get('get/list', 'Analytics::getAllAnalyticsData');
		$routes->post('get/graph', 'Analytics::getGraphAnalytics');
		$routes->post('get/graph/dashboard', 'Analytics::getGraphDashboardAnalytics');
		$routes->post('get/graph/options', 'Analytics::getGraphAnalyticOptions');
		$routes->post('get/dashboard', 'Analytics::getDashboard');
	});

	$routes->group('document', function($routes){
		$routes->post('create/content', 'DocumentGFPS::addDocumentContent');
		$routes->post('delete/content', 'DocumentGFPS::deleteDocumentContent');
		$routes->get('get/list', 'DocumentGFPS::getDocumentList');
	});

	$routes->group('events', function($routes){
		$routes->post('add', 'Events::addEventCalendar');
		$routes->post('edit', 'Events::editEventCalendar');
		$routes->post('delete', 'Events::deleteEventCalendar');
		$routes->post('list', 'Events::getListEvents');
	});

	$routes->group('evaluation', function($routes){
		$routes->post('create/content', 'Evaluation::addEventQuestionaire');
		$routes->post('get/questions', 'Evaluation::getListEventQuestionaire');
		$routes->post('get/questions/response', 'Evaluation::getListEventQuestionaireResponse');
		$routes->post('response/submit', 'Evaluation::addEventResponse');
		$routes->post('response/get', 'Evaluation::getEventResponse');
	});

	$routes->group('misc', function($routes){
		$routes->get('getUserTypes', 'Misc::getUserTypes');
		$routes->get('getBranches', 'Misc::getBranches');
		$routes->get('getAddress/(:any)', 'Misc::getAddress/$1');
		$routes->post('database/backup', 'BackupController::backupDatabase');
		$routes->post('database/restore', 'BackupController::restoreDatabase');
	});

	
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

