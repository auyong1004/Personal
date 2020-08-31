<?php
declare(strict_types=1);

namespace App\Application\Controllers\Option;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class OptionController extends Controller
{

    /**
     * @param ContainerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        
        parent::__construct($container);

    }

    public function month(Request $request,Response $response,$args){

        $sql="SELECT DISTINCT(MONTH(e_receipt_date)) as 'month' FROM `p_expense` ORDER BY `month`  ASC";
        $result=array_map(function($row){
            return [
                'label'=>$row['month'],
                'value'=>$row['month'],
            ];
        },
        $this->mysqlService->queryOps($sql));
        $payload = new ControllerPayload(200, $result);
        return $this->respondJSON($response,$payload);    
    }


    public function year(Request $request,Response $response,$args){

        $sql="SELECT DISTINCT(YEAR(e_receipt_date)) as 'year' FROM `p_expense` ORDER BY `e_remark`  DESC";
        $result=array_map(function($row){
            return [
                'label'=>$row['year'],
                'value'=>$row['year'],
            ];
        },
        $this->mysqlService->queryOps($sql));
        $payload = new ControllerPayload(200, $result);
        return $this->respondJSON($response,$payload);    
    }

    public function remark(Request $request, Response $response, $args){

        
        $get=$this->mysqlService->escapeStrArrOps($_GET);
        $params=[
            'remark'=>'',
        ];
        foreach ($params as $paramsProp => $paramsValue) {
            if(isset($get[$paramsProp]))$params[$paramsProp]=$get[$paramsProp];
 
        }

        if(strlen($params['remark'])==0){
            $payload = new ControllerPayload(200, '');
            return $this->respondJSON($response,$payload);    
        }
        $this->mysqlService->changeDbOps("personal");

        $sql="SELECT DISTINCT (e_remark) as 'remark' FROM p_expense WHERE e_remark like '%{$params['remark']}%' LIMIT 5";
        $result=array_map(function($row){
            return $row['remark'];
        },
        $this->mysqlService->queryOps($sql));
        $payload = new ControllerPayload(200, $result);
        return $this->respondJSON($response,$payload);    

    }

    public function paymentType(Request $request, Response $response, $args){


        $this->mysqlService->changeDbOps("personal");
        $sql="SELECT pt_id as 'value',pt_label as 'label' FROM p_payment_type";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql));
        return $this->respondJSON($response,$payload);    

    }

    public function taskList(Request $request, Response $response, $args){
        //set and execuate query
        $this->mysqlService->changeDbOps("personal");
        $sql="SELECT tk_id as 'value',tk_name as 'label',tk_color as 'color' FROM p_task_list";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql));
        return $this->respondJSON($response,$payload);    
    }
    public function expenseType(Request $request, Response $response, $args){
        //set and execuate query
        $this->mysqlService->changeDbOps("personal");
        $sql="SELECT et_id as 'value',et_label as 'label' FROM p_expense_type";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql));
        return $this->respondJSON($response,$payload);    
    }



    public function session(Request $request, Response $response, $args){

    
        $this->mysqlService->changeDbOps("personal");
        $sql="SELECT s_id as 'value',s_label as 'label' FROM p_session";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql));
        return $this->respondJSON($response,$payload);    

    }


    
}