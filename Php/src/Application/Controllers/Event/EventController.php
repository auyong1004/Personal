<?php
declare(strict_types=1);

namespace App\Application\Controllers\Event;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class EventController extends Controller
{

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        
        parent::__construct($container);

    }
    public function index(Request $request,Response $response,$args){
        return $this->twig->render($response, 'schedule.html');

    }

    public function list(Request $request, Response $response,$args){


        $this->mysqlService->changeDbOps("personal");
        $get=$this->mysqlService->escapeStrArrOps($_GET);
        $params=[
            'year'=>date('Y'),
            'month'=>date('n'),
            'total'=>30,
            'page'=>0,
            'status'=>1,
            'column'=>'',
            'order'=>'ASC',
            'like'=>'',
        ];
        foreach ($params as $paramsProp => $paramsValue) {
            if(isset($get[$paramsProp]))$params[$paramsProp]=$get[$paramsProp];
        }
        $offset=(int)$params['total'] * (int)$params['page'];
        $total=$params['total']=='-1'?500:(int)$params['total'];
        $column=$this->getColumnName($params['column']);
        $order=strtoupper ($params['order']);
        $like=strval($params['like']);

        $likeCond=strlen($like)>=1?" AND a.`c_title` LIKE '%$like%'":"";
        $likeCond=$like=='null'||$like=='undefined'?'':$likeCond;


        $expenseSql="SELECT a.e_id as 'id',a.e_subject as 'subject',a.e_description as 'description',a.e_type as 'type',e_cron as 'cron',e_recurring as 'recurring',
              date_format(a.e_start_date,'%Y-%m-%d') as 'startDate',date_format(a.e_end_date,'%Y-%m-%d') as 'endDate'
              FROM `p_event` a
              WHERE MONTH(e_start_date)='{$params['month']}' AND YEAR(e_start_date)='{$params['year']}'
              $likeCond
              GROUP BY a.`e_id`
              ORDER BY a.`$column` $order LIMIT $offset,$total
              ";

        $countSql="SELECT e_id FROM `p_event` WHERE MONTH(e_start_date)='{$params['month']}' AND YEAR(e_start_date)='{$params['year']}' ";
        $result=[
            'total'=>$this->mysqlService->countOps($countSql),
            'events'=>$this->mysqlService->queryOps($expenseSql),
        ];

        $payload = new ControllerPayload(200, $result);

        return $this->respondJSON($response,$payload);    
    }
    
    public function create(Request $request, Response $response, $args){
        $this->mysqlService->changeDbOps("personal");

        $post=$this->getPostData($request);
        $form=[
            'subject'=>'',
            'startDate'=>'',
            'endDate'=>'',
            'description'=>'',
            'type'=>'',
            'cron'=>'',
            'recurring'=>'',
        ];
        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }
        
        $sql="INSERT INTO `p_event` (`e_id`, `e_subject`, `e_start_date`, `e_end_date`, `e_create_date`, `e_update_date`, `e_description`, `e_type`, `e_cron`, `e_recurring`) 
              VALUES (NULL, '{$form['subject']}','{$form['startDate']}','{$form['endDate']}' ,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '{$form['description']}', '{$form['type']}', '{$form['cron']}', '{$form['recurring']}');";
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
            'session'=>'',
            'paymentType'=>'',
            'expenseType'=>'',
            'amount'=>'',
            'claimable'=>'',
            'refundable'=>'',
            'remark'=>'',
            'receiptDate'=>date('Y-m-d'),
        ];
        */
        $sql="SELECT a.e_id as 'id',a.e_subject as 'subject',a.e_description as 'description',a.e_type as 'type',e_cron as 'cron',e_recurring as 'recurring',
        date_format(a.e_start_date,'%Y-%m-%d') as 'startDate',date_format(a.e_end_date,'%Y-%m-%d') as 'endDate'
        FROM `p_event` a WHERE a.e_id ='$id'";


        if($this->mysqlService->countOps($sql)==0){
            $payload = new ControllerPayload(404,'Invalid Id');
            return $this->respondJSON($response,$payload);   
        }
        $form=$this->mysqlService->queryOps($sql)[0];
        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }
        $sql="UPDATE `p_event` SET
        `e_subject`='{$form['subject']}',
        `e_description`='{$form['description']}',
        `e_type`='{$form['type']}',
        `e_cron`='{$form['cron']}',
        `e_recurring`='{$form['recurring']}',
        `e_start_date`='{$form['startDate']}',
        `e_end_date`='{$form['endDate']}',
        `e_update_date`=CURRENT_TIMESTAMP

        WHERE `e_id`='$id'
        ";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql,"UPDATE"));

        return $this->respondJSON($response,$payload);    
    }

    public function remove(Request $request, Response $response, $args){
        $id=$this->mysqlService->escapeStrOps($args['id']);
        //set and execuate query
        $this->mysqlService->changeDbOps("personal");
        $sql="DELETE FROM p_event WHERE e_id='$id'";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql,"DELETE"));
        return $this->respondJSON($response,$payload);    

    }
    protected function getColumnName($label){
        $columns=[
            'startDate'=>'e_start_date',
            'endDate'=>'e_start_date',
            'subject'=>'e_subject',
            'description'=>'e_description',
        ];
        
        if($label=='null' || $label=='undefined')return 'e_id';
        if(!isset($columns[$label]))return 'e_id';
        return $columns[$label];

    }




    
}