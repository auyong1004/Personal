<?php
declare(strict_types=1);

namespace App\Application\Controllers\Google;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Application\Controllers\Google\GoogleController;

use App\Dependency\MysqlService;
use Google_Client;
use Google_Service_Sheets;

class GoogleSheetController extends GoogleController
{

    /**
     * @var Google_Client
     */
    protected $google;

    /**
     * @param ContainerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        parent::__construct($container);

    }
    public function view(Request $request, Response $response, $args){


        print_r($this->google);
        exit('view');

    }



    
}