<?php
declare(strict_types=1);

namespace App\Application\Controllers\TaskList;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class TaskListController extends Controller
{   

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        
        parent::__construct($container);

    }

    public function list(Request $request, Response $response,$args){

        $this->mysqlService->changeDbOps("personal");

        $sql="SELECT `tk_id` as 'id', `tk_name` as 'name', `tk_description` as 'description', `tk_color` as 'color' FROM `p_task_list`";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql));


        return $this->respondJSON($response,$payload);    
    }
    
    public function create(Request $request, Response $response, $args){
        $this->mysqlService->changeDbOps("personal");

        $post=$this->getPostData($request);
        $form=[
            'name'=>'',
            'description'=>'',
            'color'=>'',
            'createDate'=>'',
        ];
        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }
        
        $sql="INSERT INTO `p_task_list` (`tk_id`, `tk_name`, `tk_description`, `tk_color`, `tk_create_date`) VALUES (NULL, '{$form['name']}', '{$form['description']}', '{$form['color']}', CURRENT_TIMESTAMP);";
        $this->mysqlService->queryOps($sql,"INSERT");

        $payload = new ControllerPayload(200, [
            'id'=>$this->mysqlService->getLastID()
        ]);

        return $this->respondJSON($response,$payload);    

    }

    public function update(Request $request, Response $response,$args){
        $this->mysqlService->changeDbOps("personal");
        $id=$this->mysqlService->escapeStrOps($args['id']);

        $post=$this->getPostData($request);
        $form=[
            'name'=>'',
            'description'=>'',
            'color'=>'',
            'createDate'=>'',
        ];
        $sql="SELECT `tk_id` as 'id', `tk_name` as 'name', `tk_description` as 'description', `tk_color` as 'color' FROM `p_task_list` WHERE tk_id='$id'";

        if($this->mysqlService->countOps($sql)==0){
            
            $payload = new ControllerPayload(404,'Invalid Id');
            return $this->respondJSON($response,$payload);   
        }
        $form=$this->mysqlService->queryOps($sql)[0];

        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }

        $sql="UPDATE `p_task` SET
        `t_name`='{$form['subject']}',
        `t_description`='{$form['description']}',
        `t_color`='{$form['priority']}' WHERE `t_id`='$id'
        ";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql,"UPDATE"));
        return $this->respondJSON($response,$payload);    
    }

    public function remove(Request $request, Response $response, $args){
        $id=$this->mysqlService->escapeStrOps($args['id']);
        //set and execuate query
        $this->mysqlService->changeDbOps("personal");
        $sql="DELETE FROM `p_task_list` WHERE tk_id='$id'";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql,"DELETE"));
        return $this->respondJSON($response,$payload);    

    }





    
}