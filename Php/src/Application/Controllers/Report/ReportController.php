<?php
declare(strict_types=1);

namespace App\Application\Controllers\Report;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class ReportController extends Controller
{

    public $year;

    /**
     * @param ContainerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        
        parent::__construct($container);

    }
    public function index(Request $request,Response $response,$args){
        return $this->twig->render($response, 'report.html');

    }




    public function list(Request $request, Response $response, $args){

        $get=$this->mysqlService->escapeStrArrOps($_GET);
        $params=[
            'year'=>date('Y'),
            'month'=>date('n')
        ];
        foreach ($params as $paramsProp => $paramsValue) {
            if(isset($get[$paramsProp]))$params[$paramsProp]=$get[$paramsProp];
        }


        $this->mysqlService->changeDbOps("personal");

        $result=[
            'days'=>$this->monthReport($params),
            'expenses'=>$this->expenseReport($params),
            'sessions'=>$this->sessionReport($params),
            'payments'=>$this->paymentReport($params),
        ];

        $payload = new ControllerPayload(200, $result);
        return $this->respondJSON($response,$payload);    

    }        
    protected function monthReport($params){
        $sql="SELECT DATE_FORMAT(e_receipt_date,'%Y-%m-%d') as 'date',sum(e_amount)as'total' FROM `p_expense` 
        WHERE YEAR(e_receipt_date)='{$params['year']}' AND MONTH(e_receipt_date)='{$params['month']}' GROUP BY e_receipt_date  ";
        $rows=array_map(function($row){
            $row['total']=floatval($row['total']);
            return $row;
        },$this->mysqlService->queryOps($sql));
        return $rows;
        
    }
    protected function expenseReport($params){
        $sql="SELECT a.et_color as 'color',a.et_label as 'expense',IF(ISNULL(b.total),0 ,b.total) as 'total' FROM `p_expense_type` a LEFT JOIN (SELECT et_id,sum(e_amount)as 'total' FROM `p_expense` 
        WHERE YEAR(e_receipt_date)='{$params['year']}' AND MONTH(e_receipt_date)='{$params['month']}' GROUP BY et_id ) as b on a.et_id=b.et_id ";
        $rows=array_map(function($row){
            $row['total']=floatval($row['total']);
            return $row;
        },$this->mysqlService->queryOps($sql));
        return $rows;
    }
    protected function sessionReport($params){
        $sql="SELECT a.s_label as 'session',IF(ISNULL(b.total),0 ,b.total) as 'subTotal' FROM `p_session` a LEFT JOIN (SELECT s_id,sum(e_amount)as 'total' FROM `p_expense` 
        WHERE YEAR(e_receipt_date)='{$params['year']}' AND MONTH(e_receipt_date)='{$params['month']}' GROUP BY s_id ) as b on a.s_id=b.s_id ";
        $rows=$this->mysqlService->queryOps($sql);
        foreach ($rows as $rowIndex => $row) {
            $rows[$rowIndex]['total']=array_sum(array_column($rows,'subTotal'));
            $rows[$rowIndex]['subTotal']=floatval($row['subTotal']);
            $rows[$rowIndex]['percentage']=$rows[$rowIndex]['subTotal']==0?0:($rows[$rowIndex]['subTotal']/$rows[$rowIndex]['total'])*100;
        }
        return $rows;
    }

    protected function paymentReport($params){
        $sql="SELECT a.pt_label as 'paymentType',IF(ISNULL(b.total),0 ,b.total) as 'total' FROM `p_payment_type` a LEFT JOIN (SELECT pt_id,sum(e_amount)as 'total' FROM `p_expense` 
        WHERE YEAR(e_receipt_date)='{$params['year']}' AND MONTH(e_receipt_date)='{$params['month']}' GROUP BY pt_id ) as b on a.pt_id=b.pt_id ";
        $rows=array_map(function($row){
            $row['total']=floatval($row['total']);
            return $row;
        },$this->mysqlService->queryOps($sql));
        return $rows;
    }

    
}