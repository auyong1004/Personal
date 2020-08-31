<?php
declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\CorsMiddleware;
use App\Application\Middleware\AuthMiddleware;
use Slim\Views\TwigMiddleware;

use Slim\App;


return function (App $app) {
    //$app->add(CorsMiddleware::class); // <--- here
    $app->add(CorsMiddleware::class);
    $app->add(SessionMiddleware::class);
    $app->add(AuthMiddleware::class);

    //$app->add(TwigMiddleware::class); // <--- here

};