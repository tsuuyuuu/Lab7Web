<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->resource('post');
$routes->setAutoRoute(true);

$routes->get('/', 'Home::home');
$routes->get('/about', 'Page::about'); 
$routes->get('/contact', 'Page::contact'); 
$routes->get('/faqs', 'Page::faqs'); 

// Route for the user section
$routes->get('user/login', 'User::login');
$routes->post('user/login', 'User::login');

// AJAX routes
$routes->get('ajax', 'AJAX::index');
$routes->get('ajax/data', 'AJAX::getData');
$routes->delete('ajax/delete/(:num)', 'AJAX::delete/$1');
$routes->post('ajax/create', 'AJAX::create'); // Untuk CREATE
$routes->post('ajax/update/(:num)', 'AJAX::update/$1'); // Untuk UPDATE (atau put jika mau lebih RESTful)


// Route for the admin section
$routes->group('admin', ['filter' => 'auth'], function($routes) { 
    $routes->get('artikel', 'Artikel::admin_index'); 
    $routes->add('artikel/add', 'Artikel::add'); 
    $routes->add('artikel/edit/(:any)', 'Artikel::edit/$1'); 
    $routes->get('artikel/delete/(:any)', 'Artikel::delete/$1'); 
    });
    
// Routes for the Artikel controller
$routes->get('/artikel', 'Artikel::index');
$routes->get('/artikel/(:any)', 'Artikel::view/$1');

