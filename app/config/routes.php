<?php

$router = new Phalcon\Mvc\Router();

$router->add('/:controller/:action/:params', [
    'namespace'  => 'MyApp\Controllers',
    'controller' => 1,
    'action'     => 2,
    'params'     => 3,
]);

$router->add('/:controller', [
    'namespace'  => 'MyApp\Controllers',
    'controller' => 1,
]);

$router->add('/admin/:controller/:action', [
    'namespace'  => 'MyApp\Controllers\Admin',
    'controller' => 1,
    'action'     => 2,
    //'params' => 3,
]);

$router->add('/admin/:controller', [
    'namespace'  => 'MyApp\Controllers\Admin',
    'controller' => 1,
]);

return $router;
