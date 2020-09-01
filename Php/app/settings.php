<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            "determineRouteBeforeAppMiddleware" => true,
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'twig'=>[
                'paths' => [
                    __DIR__ . '/../public/pages',                
                ],
                // Twig environment options
                'options' => [
                    // Should be set to true in production
                    'cache_enabled' => false,
                    'cache_path' => __DIR__ . '/../tmp/twig',
                ],
            ],
            //setup ur google id
            'google'=>[
                'id'=>'',
                'secret'=>'',
                'credential'=>__DIR__ . '/../credentials.json',                
            ]
        ],
        'db1'=>[
            'host'=>'',
            'username'=>'',
            'password'=>'',
            'database'=>'personal'
        ],

        
        
    ]);
};
