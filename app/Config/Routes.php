<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function(){
    return view('products_view');
});

$routes->get('products', 'ProductController::index');
$routes->post('product/store', 'ProductController::store');
$routes->post('product/update', 'ProductController::update');

$routes->post('csrf/update', 'CsrfController::updateCsrfToken');
