<?php
declare(strict_types=1);


namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use \Firebase\JWT\JWT;


/*
1- GET token (JWT-encryption)
2- BASIC AUTH HEADER
3- Cookie (JWT-encryption)
*/
class AuthMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {   
        $authForm=[
            "userMail"=>"",
            "userToken"=>"",
            "type"=>"",
        ];
        $authType=false;
        $headers=apache_request_headers();

        if(isset($headers['Access-Token']) &&!$authType){
            //$token=$_GET['token'];
            $authType=AUTH_TYPES["API_HEADER"];
            $authForm['userToken']=$headers['Access-Token'];
        }


        if(isset($headers['Json-Token']) &&!$authType){
            $authType=AUTH_TYPES["JWT_AUTH"];
            //$cookies['JWT'];
            try{
                $decoded = JWT::decode($headers['Json-Token'],APP_KEY, array('HS256'));
                $decoded = is_object($decoded)?\get_object_vars($decoded):[];
   
                foreach ($authForm as $prop => $value) {
                    if(isset($decoded[$prop]))$authForm[$prop]=$decoded[$prop];
                }


            }
            catch(\Exception $e) {
                //write log
            }   

        }
        if(isset($_GET['Access-Token']) &&!$authType){
            //$token=$_GET['token'];
            $authType=AUTH_TYPES["API_KEY"];
            $authForm['userToken']=$_GET['Access-Token'];
        }

        
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && !$authType){
            //exit('1234');
            $authType=AUTH_TYPES["BASIC_AUTH"];
            $authForm['userMail']=$_SERVER['PHP_AUTH_USER'];
            $authForm['userToken']=$_SERVER['PHP_AUTH_PW'];
        }
        if(isset($_SERVER['HTTP_COOKIE']) && !$authType){
            $cookies=[];

            foreach (explode(';',$_SERVER['HTTP_COOKIE']) as $cookieStr) {
                $cookie=explode('=',$cookieStr);
                if(count($cookie)==2)$cookies[trim($cookie[0])]=$cookie[1];
            }

            if(isset($cookies['Access-Token'])){
                $authType=AUTH_TYPES["COOKIE"];
                $authForm['userToken']=$cookies['Access-Token'];
            }
            if(isset($cookies['Json-Token'])){
                $jwt = JWT::encode(['userMail'=>'alex.auyong@redtone.com','userToken'=>'1234'], 'api_key');
                $authType=AUTH_TYPES["JWT_AUTH"];
                //$cookies['JWT'];
                try{
                    $decoded = JWT::decode($cookies['Json-Token'],APP_KEY, array('HS256'));
                    foreach ($authForm as $prop => $value) {
                        if(isset($decoded[$prop]))$authForm[$prop]=$decoded[$prop];
                    }

                }
                catch(\Exception $e) {
                    //write log
                }   

            }
            
        }

        if(!$authType){
            $authType=AUTH_TYPES["UNKNOWN"];
        }

        $authForm['type']=$authType;
        $request = $request->withAttribute('authForm', $authForm);
        return $handler->handle($request);
  
    }
}
