<?php
declare(strict_types=1);

namespace App\Application\Controllers\Google;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Calendar;
use Google_Service_Drive;
use Google_Service_Oauth2;
use Google_Service_PeopleService;

class GoogleController extends Controller
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
        $settings=$container->get('settings');
        $this->setClient($settings['google']);

    }

    protected function setClient($setting){
        $scopes=[
            Google_Service_Sheets::SPREADSHEETS_READONLY,
            Google_Service_Calendar::CALENDAR_READONLY,
            Google_Service_Drive::DRIVE_METADATA_READONLY,
            Google_Service_Oauth2::USERINFO_PROFILE,
            Google_Service_Oauth2::USERINFO_EMAIL,
            Google_Service_PeopleService::USERINFO_EMAIL

        ];

        $this->google=new Google_Client();
        $this->google->setApplicationName('Personal');
        $this->google->setScopes($scopes);



        $this->google->setAuthConfig($setting['credential']);
        $this->google->setAccessType('offline');
        $this->google->setRedirectUri('http://localhost:3000/api/google/oAuth/login');
        //$this->google->setPrompt('select_account consent');


        if(isset($_SESSION['google'])){
            $this->google->setAccessToken($_SESSION['google']);
            
        }

        
    }





    
}