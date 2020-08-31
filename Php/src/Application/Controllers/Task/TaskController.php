<?php
declare(strict_types=1);

namespace App\Application\Controllers\Task;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class TaskController extends Controller
{

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        
        parent::__construct($container);

    }
    public function index(Request $request,Response $response,$args){
        return $this->twig->render($response, 'todo.html');

    }
    public function list(Request $request, Response $response,$args){

        $this->mysqlService->changeDbOps("personal");
        $get=$this->mysqlService->escapeStrArrOps($_GET);
        $params=[
            'year'=>2016,
            'month'=>30,
        ];
        foreach ($params as $paramsProp => $paramsValue) {
            if(isset($get[$paramsProp]))$params[$paramsProp]=$get[$paramsProp];
        }
        $sql="SELECT a.t_id as 'id' ,a.t_subject as 'subject',a.t_description as 'description',a.t_priority as 'priority' ,
              DATE_FORMAT(a.t_due_date,'%Y-%m-%d') as 'dueDate',a.t_stat as 'stat', a.tk_id as 'list',b.tk_name as 'listName',b.tk_color as 'listColor'
              FROM `p_task` a
              LEFT JOIN `p_task_list` b on a.tk_id=b.tk_id 
              ";

        $tasks=array_map(function($task){
            $task['stat']=boolval(($task['stat']));
            return $task;
        },$this->mysqlService->queryOps($sql));
        $payload = new ControllerPayload(200, $tasks);


        return $this->respondJSON($response,$payload);    
    }
    
    public function create(Request $request, Response $response, $args){
        $this->mysqlService->changeDbOps("personal");

        $post=$this->getPostData($request);
        $form=[
            'subject'=>'',
            'description'=>'',
            'priority'=>'',
            'dueDate'=>'',
            'stat'=>'',
            'list'=>'1'
        ];
        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }
        
        $sql="INSERT INTO `p_task` (`t_id`, `t_subject`, `t_description`, `t_priority`, `t_create_date`, `t_due_date`, `t_stat`,`tk_id`) 
              VALUES (NULL, '{$form['subject']}', '{$form['description']}', '{$form['priority']}', CURRENT_TIMESTAMP, '{$form['dueDate']}', '{$form['stat']}','{$form['list']}');";
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
        /*
        $form=[
            'subject'=>'',
            'description'=>'',
            'priority'=>'',
            'dueDate'=>'',
            'stat'=>'',
            'list'=>'',
        ];
        */
        $sql="SELECT t_id as 'id' ,t_subject as 'subject',t_description as 'description',t_priority as 'priority' , t_due_date as 'dueDate',t_stat as 'stat',`tk_id` as 'list'
        FROM `p_task` WHERE t_id='$id'";

        if($this->mysqlService->countOps($sql)==0){
            
            $payload = new ControllerPayload(404,'Invalid Id');
            return $this->respondJSON($response,$payload);   
        }
        $form=$this->mysqlService->queryOps($sql)[0];

        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }

        $sql="UPDATE `p_task` SET
        `t_subject`='{$form['subject']}',
        `t_description`='{$form['description']}',
        `t_priority`='{$form['priority']}',
        `t_due_date`='{$form['dueDate']}',
        `t_stat`='{$form['stat']}',
        `tk_id`='{$form['list']}'
        WHERE `t_id`='$id'
        ";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql,"UPDATE"));
        return $this->respondJSON($response,$payload);    
    }

    public function remove(Request $request, Response $response, $args){
        $id=$this->mysqlService->escapeStrOps($args['id']);
        //set and execuate query
        $this->mysqlService->changeDbOps("personal");
        $sql="DELETE FROM `p_task` WHERE t_id='$id'";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql,"DELETE"));
        return $this->respondJSON($response,$payload);    

    }





    
}