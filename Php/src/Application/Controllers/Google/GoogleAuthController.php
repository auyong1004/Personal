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
use Google_Service_PeopleService;

class GoogleAuthController extends GoogleController
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
    public function login(Request $request, Response $response, $args){
        $url="";
        if (!isset($_GET['code'])) {
            $auth_url = $this->google->createAuthUrl();
            $url=filter_var($auth_url, FILTER_SANITIZE_URL);
        
        } 
        else {
            $this->google->fetchAccessTokenWithAuthCode($_GET['code']);
            $_SESSION['google'] = $this->google->getAccessToken();
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
            $url=filter_var($redirect_uri, FILTER_SANITIZE_URL);
        }


         return $response->withHeader('Location',$url) ;


    }

    public function logout(Request $request,Response $response,$args){
        $_SESSION = [];
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $url=filter_var($redirect_uri, FILTER_SANITIZE_URL);
        return $response->withHeader('Location',$url) ;

    }
    public function session(Request $request,Response $response,$args){

        if(!isset($_SESSION['google'])){
            $payload = new ControllerPayload(404, "No");
            return $this->respondJSON($response,$payload);  
        }
        $service = new Google_Service_PeopleService($this->google);
        $optParams = [
            'personFields' => 'names,emailAddresses,addresses,phoneNumbers',
        ];
        
        $profile = $service->people->get( 'people/me', $optParams );
        $result=[
          'name'=> $profile->getNames()[0]->displayName ,
          'email'=> $profile->getEmailAddresses()[0]->value,

        ];
        $payload = new ControllerPayload(200, $result);
        return $this->respondJSON($response,$payload);    
    }

    
}