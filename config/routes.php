<?php

/*
 * Define special routes that doesnt exist as written
 * For example:
 * $router->addRoute(array(
 *      'name' => 'news',
 *      'controller' => 'news',
 *      'action' => 'all'
 * ));
 *
 */

$router = Router::getInstance();

$router->addRoute(array(
    'name' => 'news',
    'controller' => 'index',
    'action' => 'news'
    ));
