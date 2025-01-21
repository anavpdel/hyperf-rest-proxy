<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/topics/{topicName}', 'App\Controller\IndexController@sendJsonData');

Router::get('/favicon.ico', function () {
    return '';
});
