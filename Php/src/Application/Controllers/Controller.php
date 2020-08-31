<?php
declare(strict_types=1);

namespace App\Application\Controllers;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

use App\Dependency\MysqlService;
use App\Dependency\CurlService;

use Slim\Views\Twig;

use \Firebase\JWT\JWT;

use Google_Client;
use Google_Service_Sheets;


class Controller
{



    /**
     * @var Twig
    */
    protected $twig;

    /**
     * @var CurlService
    */
    protected $curlService;

    /**
     * @var MysqlService
     */
    protected $mysqlService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $args;

    /**
     * @param ContainerInterface $container
     */
     public function __construct(ContainerInterface $container)
     {
        $settings=$container->get('settings');
        //set DI
        $this->twig=$container->get(Twig::class );
        $this->logger=$container->get(LoggerInterface::class );
        $this->mysqlService=$container->get(MysqlService::class );
        $this->curlService=new CurlService();

        //if ($container->has('mysqlService')) $this->mysqlService = $container->get('mysqlService');
        //shared
        //$tempShared=$request->getAttribute('Shared');
   
    }


    protected function getAuthenticationInfo(Request $request){
        $this->mysqlService->changeDbOps("PMSv3");
        $authForm=$request->getAttribute('authForm');

        $authForm=$this->mysqlService->escapeStrArrOps($authForm);
        if($authForm['type']=='UNKNOWN')return false;
        
        if($authForm['type']=='BASIC_AUTH' || $authForm['type']=='JWT_AUTH'){
            $sql="SELECT u_admin as 'isAdmin',u_name as 'name', u_email  as 'email', u_id as 'id' ,u_designation as 'designation',u_company as 'company' ,u_superior as 'superior'  
                  FROM p_user WHERE u_token='{$authForm['userToken']}' AND u_email='{$authForm['userMail']}'";
        }
        else {
            $sql="SELECT u_admin as 'isAdmin',u_name as 'name', u_email  as 'email', u_id as 'id' ,u_designation as 'designation',u_company as 'company' ,u_superior as 'superior' 
                  FROM p_user WHERE u_token='{$authForm['userToken']}'";
        }


        if($this->mysqlService->countOps($sql)==0)return false;
        
        $result=$this->mysqlService->queryOps($sql);
        return $result[0];

    }
    protected function getPostData(Request $request){
        $types=['multipart/form-data','application/x-www-form-urlencoded'];
        $contentType=$_SERVER['CONTENT_TYPE'];
  
        $contentType=explode(';',$contentType)[0];
        $post=false;

        if(count($_POST)>=1)$post=$_POST;
        if($contentType=='multipart/form-data' && !$post)$post=$this->parseRawsForMultipart();
        if($contentType=='application/x-www-form-urlencoded' && !$post)$post=$this->parseRawsForMultipart();
        if($contentType=='application/json' && !$post)$post = json_decode(file_get_contents('php://input'),true);
        


        return $post;
    }
    protected function parseRawForUrldecode(){
        $bodies=[];
        foreach (explode('&',urldecode (file_get_contents('php://input'))) as $fieldStr) {
            $field=explode('=',$fieldStr);
            if(count($field)==2)$bodies[trim($field[0])]=$field[1];
        }
        return $bodies;
    }
    //copy from https://gist.github.com/cwhsu1984/3419584ad31ce12d2ad5fed6155702e2
    protected function parseRawsForMultipart($a_data = [])
    {
        // read incoming data
        $input = file_get_contents('php://input');
    
        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
    
        // content type is probably regular form-encoded
        if (!count($matches))
        {
            // we expect regular puts to containt a query string containing data
            parse_str(urldecode($input), $a_data);
            return $a_data;
        }
    
        $boundary = $matches[1];
    
        // split content by boundary and get rid of last -- element
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);
    
        $keyValueStr = '';
        // loop data blocks
        foreach ($a_blocks as $id => $block)
        {
            if (empty($block))
                continue;
    
            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char
    
            // parse uploaded files
            if (strpos($block, 'application/octet-stream') !== FALSE)
            {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                $a_data['files'][$matches[1]] = $matches[2];
            }
            // parse all other fields
            else
            {
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                $keyValueStr .= $matches[1]."=".$matches[2]."&";
            }
        }
        $keyValueArr = [];
        parse_str($keyValueStr, $keyValueArr);
        return array_merge($a_data, $keyValueArr);
    }
    /**
     * @param Request $request
     * @return Object
     */
     protected function getTokenByHeader($request){
        $jwt="";
        $headers=($request->getHeaders());
        if(!isset($headers['Access-Token']))return false;
        $jwt=$headers['Access-Token'][0];

        $decoded = JWT::decode($jwt, "api_key", array('HS256'));

        return \get_object_vars($decoded);
    }
     /**
     * @param Request $request
     * @return Object
     */
    protected function getTokenByCookie($request){
        $cookies=($request->getCookieParams());
        $decoded = JWT::decode($cookies['token'], "api_key", array('HS256'));
        return $decoded;
    }
    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    //abstract protected function action(): Response;

    /**
     * @return array|object
     * @throws HttpBadRequestException
     */
    protected function getJsonData()
    {
        $input = json_decode(file_get_contents('php://input'),true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpBadRequestException($this->request, 'Malformed JSON input.');
        }



        return $this->mysqlService->escapeStrArrOps($input);
    }

    /**
     * @param  string $name
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }
    /**
     * @param ControllerPayLoad $payload
     * @return Response
    */

    protected function respondHTML(Response $response,ControllerPayLoad $payload): Response
    {
        $response->getBody()->write($payload->getdata());

        return $response
                    ->withHeader('Content-Type', 'text/html')
                    ->withStatus($payload->getStatusCode());
    }
     /**
     * @param ControllerPayLoad $payload
     * @return Response
     */
    protected function respondJSON(Response $response,ControllerPayLoad $payload): Response
    {

        $json = json_encode($payload->getData(), JSON_PRETTY_PRINT);


        if($payload->getStatusCode()!=204)$response->getBody()->write($json);

        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($payload->getStatusCode());
    }
    /**
    * Moves the uploaded file to the upload directory and assigns it a unique name
    * to avoid overwriting an existing uploaded file.
    *
    * @param string $directory directory to which the file is moved
    * @param UploadedFile $uploadedFile file uploaded file to move
    * @return string filename of moved file
    */
    protected function moveUploadedFile($directory,  $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}