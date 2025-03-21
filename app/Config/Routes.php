<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group('api', function ($routes) {
    $routes->get('test', 'NetworkController::test');
    $routes->get('ping', 'NetworkController::ping', ['filter' => 'jwt']);
    $routes->get('traceroute', 'NetworkController::traceroute', ['filter' => 'jwt']);
    $routes->get('jitter', 'NetworkController::jitter', ['filter' => 'jwt']);
    // $routes->get('ping/(:any)', 'NetworkController::ping/$1');
    // $routes->get('traceroute/(:any)', 'NetworkController::traceroute/$1');
    // $routes->get('jitter/(:any)', 'NetworkController::jitter/$1');
});
$routes->post('api/setIp', 'NetworkController::setIp');
$routes->get('api/getStoredIps', 'NetworkController::getStoredIps');

$routes->post('auth/login', 'AuthController::login'); // LOGIN
$routes->get('auth/token', 'AuthController::generateToken');

$routes->post('api/testando', 'TestController::testando');
$routes->get('api/protected/test', 'NetworkController::test', ['filter' => 'jwt']);
