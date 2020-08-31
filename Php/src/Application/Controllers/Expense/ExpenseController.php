<?php
declare(strict_types=1);

namespace App\Application\Controllers\Expense;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class ExpenseController extends Controller
{

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        
        parent::__construct($container);

    }
    public function index(Request $request,Response $response,$args){
        return $this->twig->render($response, 'expense.html');

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


        $expenseSql="SELECT a.e_id as 'id',a.s_id as 'session',a.pt_id as 'paymentType',a.et_id as 'expenseType',
              a.e_amount as 'amount',a.e_claimable as 'claimable',a.e_remark as 'remark',a.e_update_date as 'updateDate',date_format(a.e_receipt_date,'%Y-%m-%d') as 'receiptDate'
              FROM `p_expense` a
              WHERE MONTH(e_receipt_date)='{$params['month']}' AND YEAR(e_receipt_date)='{$params['year']}'
              $likeCond
              GROUP BY a.`e_id`
              ORDER BY a.`$column` $order LIMIT $offset,$total
              ";

        $countSql="SELECT e_id FROM `p_expense` WHERE MONTH(e_receipt_date)='{$params['month']}' AND YEAR(e_receipt_date)='{$params['year']}' ";
        $result=[
            'total'=>$this->mysqlService->countOps($countSql),
            'expenses'=>$this->mysqlService->queryOps($expenseSql),
        ];

        $payload = new ControllerPayload(200, $result);

        return $this->respondJSON($response,$payload);    
    }
    
    public function create(Request $request, Response $response, $args){
        $this->mysqlService->changeDbOps("personal");

        $post=$this->getPostData($request);
        $form=[
            'session'=>'',
            'expenseType'=>'',
            'paymentType'=>'',
            'amount'=>'',
            'claimable'=>'',
            'refundable'=>'',
            'remark'=>'',
            'receiptDate'=>date('Y-m-d')
        ];
        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }
        
        $sql="INSERT INTO `p_expense` (`e_id`, `s_id`, `pt_id`, `et_id`, `e_amount`, `e_claimable`, `e_refundable`, `e_remark`,`e_receipt_date`, `e_create_date`, `e_update_date`)
              VALUES (NULL, '{$form['session']}', '{$form['paymentType']}', '{$form['expenseType']}', '{$form['amount']}', '{$form['claimable']}', '{$form['refundable']}', '{$form['remark']}','{$form['receiptDate']}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";
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

        $sql="SELECT a.e_id as 'id',a.s_id as 'session',a.pt_id as 'paymentType',a.et_id as 'expenseType',
        a.e_amount as 'amount',a.e_claimable as 'claimable',a.e_remark as 'remark',a.e_refundable,a.e_update_date as 'updateDate',e_receipt_date as 'receiptDate'
        FROM `p_expense` a WHERE a.e_id='$id'";

        if($this->mysqlService->countOps($sql)==0){
            
            $payload = new ControllerPayload(404,'Invalid Id');
            return $this->respondJSON($response,$payload);   
        }

        $form=$this->mysqlService->queryOps($sql)[0];

        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }


        $sql="UPDATE `p_expense` SET
        `s_id`='{$form['session']}',
        `pt_id`='{$form['paymentType']}',
        `et_id`='{$form['expenseType']}',
        `e_amount`='{$form['amount']}',
        `e_claimable`='{$form['claimable']}',
        `e_refundable`='{$form['refundable']}',
        `e_remark`='{$form['remark']}',
        `e_update_date`=CURRENT_TIMESTAMP,
        `e_receipt_date`='{$form['receiptDate']}'

        WHERE `e_id`='$id'
        ";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql,"UPDATE"));

        return $this->respondJSON($response,$payload);    
    }

    public function remove(Request $request, Response $response, $args){
        $id=$this->mysqlService->escapeStrOps($args['id']);
        //set and execuate query
        $this->mysqlService->changeDbOps("personal");
        $sql="DELETE FROM p_expense WHERE e_id='$id'";

        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql,"DELETE"));
        return $this->respondJSON($response,$payload);    

    }
    protected function getColumnName($label){
        $columns=[
            'receiptDate'=>'e_receipt_date',
            'session'=>'s_id',
            'amount'=>'e_amount',
            'remark'=>'e_remark',
        ];
        
        if($label=='null' || $label=='undefined')return 'e_id';
        if(!isset($columns[$label]))return 'e_id';
        return $columns[$label];

    }




    
}