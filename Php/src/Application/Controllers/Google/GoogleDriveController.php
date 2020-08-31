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
use Google_Service_Drive;

class GoogleDriveController extends GoogleController
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
    public function list(Request $request, Response $response, $args){

        $service = new Google_Service_Drive($this->google);

        // Print the names and IDs for up to 10 files.
        $optParams = array(
          'pageSize' => 10,
          'fields' => 'nextPageToken, files(id, name)'
        );
        $results = $service->files->listFiles($optParams);

        $files = $results->getFiles();
        $rows= [];
        foreach ($files as $file) {
            $row=[
                'id'=>$file->getId(),
                'name'=>$file->getName(),

            ];
            array_push($rows,$row);
        }

        $payload = new ControllerPayload(200, $rows);
        return $this->respondJSON($response,$payload);    
    }



    
}