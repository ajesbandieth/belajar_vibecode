<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Frontend Routes
$route['login'] = 'AppController/login';
$route['register'] = 'AppController/register';
$route['dashboard'] = 'AppController/dashboard';
$route['categories'] = 'AppController/categories';
$route['photos'] = 'AppController/photos';
$route['photos/category/(:num)'] = 'AppController/photo_category/$1';
$route['videos'] = 'AppController/videos';
$route['logout'] = 'AppController/logout';

// API Routes
$route['api/users/current']['get'] = 'api/UserController/current_user';
$route['api/users/login']['post'] = 'api/UserController/login';
$route['api/users']['post'] = 'api/UserController/register';
$route['api/users']['get'] = 'api/UserController/index';
$route['api/users/(:num)']['get'] = 'api/UserController/show/$1';
$route['api/users/(:num)']['put'] = 'api/UserController/update/$1';
$route['api/users/(:num)']['delete'] = 'api/UserController/destroy/$1';

$route['api/categories']['post'] = 'api/CategoryController/register';
$route['api/categories']['get'] = 'api/CategoryController/index';
$route['api/categories/(:num)']['get'] = 'api/CategoryController/show/$1';
$route['api/categories/(:num)']['put'] = 'api/CategoryController/update/$1';
$route['api/categories/(:num)']['delete'] = 'api/CategoryController/destroy/$1';

$route['api/files/upload']['post'] = 'api/FileController/upload';
$route['api/files/summary']['get'] = 'api/FileController/summary';
$route['api/files']['get'] = 'api/FileController/index';
$route['api/files/(:num)']['put'] = 'api/FileController/update/$1';
$route['api/files/(:num)']['delete'] = 'api/FileController/destroy/$1';
