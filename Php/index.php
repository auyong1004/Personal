<?php
declare(strict_types=1);

define("APP_FOLDER",getcwd());
define("APP_PATH","http://localhost:3000/");
define("AUTH_TYPES", [
    "API_KEY"=>"API_KEY",
    "BASIC_AUTH"=>"BASIC_AUTH",
    "JWT_AUTH"=>"JWT_AUTH",
    "COOKIE"=>"COOKIE",
    "UNKNOWN"=>"UNKNOWN",
]);
define("APP_KEY","APP_KEY");


ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\Emitter\ResponseEmitter;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/var/cache');
}

// Set up settings
$settings = require __DIR__ . '/app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();



/*
//another method set DI
$container->set('myService', function () {
    $settings = [];
    return [
		'Hello'=>'World'
	];
});

*/


// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();


//path resolve
$app->setBasePath("/");

$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require __DIR__ . '/app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/app/routes.php';
$routes($app);

/** @var bool $displayErrorDetails */
$displayErrorDetails = $container->get('settings')['displayErrorDetails'];

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);




// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);