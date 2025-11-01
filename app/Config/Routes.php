<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication Routes
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('dashboard', 'Auth::dashboard');

$routes->post('/course/enroll', 'Course::enroll');
$routes->post('/course/drop', 'Course::drop');


// Materials management
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
// Teacher alias routes for materials upload (same controller)
$routes->get('/teacher/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/teacher/course/(:num)/upload', 'Materials::upload/$1');

// Course student management (teacher/admin)
$routes->get('/admin/course/(:num)/students', 'Course::manage/$1');
$routes->post('/admin/course/(:num)/students', 'Course::manage/$1');
// Teacher alias for student list
$routes->get('/teacher/course/(:num)/students', 'Course::manage/$1');
$routes->post('/teacher/course/(:num)/students', 'Course::manage/$1');

// Role dashboards and announcements destinations used by Auth::login redirects
$routes->get('admin/dashboard', 'Auth::dashboard');
$routes->get('teacher/dashboard', 'Auth::dashboard');
$routes->get('student/dashboard', 'Auth::dashboard');
$routes->get('announcements', 'Auth::announcements');


// Announcements (teacher/admin)
$routes->get('/teacher/course/(:num)/announce', 'Announcements::create/$1');
$routes->post('/teacher/course/(:num)/announce', 'Announcements::create/$1');
$routes->get('/teacher/announce', 'Announcements::create');
$routes->post('/teacher/announce', 'Announcements::create');


// Notifications API
$routes->get('/notifications/unread-count', 'Notifications::unreadCount');
$routes->get('/notifications/list', 'Notifications::list');
$routes->post('/notifications/mark-all-read', 'Notifications::markAllRead');
$routes->post('/notifications/mark-read/(:num)', 'Notifications::markRead/$1');
$routes->post('/notifications/delete/(:num)', 'Notifications::delete/$1');
$routes->post('/notifications/delete-all', 'Notifications::deleteAll');


