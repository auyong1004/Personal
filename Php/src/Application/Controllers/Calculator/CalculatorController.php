<?php
declare(strict_types=1);

namespace App\Application\Controllers\Calculator;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class CalculatorController extends Controller
{

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        
        parent::__construct($container);

    }
    public function index(Request $request,Response $response,$args){
        return $this->twig->render($response, 'calculator.html');

    }
    /*

    public function view(Request $request, Response $response,$args){

        $this->mysqlService->changeDbOps("personal");
        $get=$this->mysqlService->escapeStrArrOps($_GET);
        $params=[
            'year'=>2016,
            'month'=>30,
        ];
        foreach ($params as $paramsProp => $paramsValue) {
            if(isset($get[$paramsProp]))$params[$paramsProp]=$get[$paramsProp];
        }
        // LEFT JOIN t_application_status b on a.as_id=b.as_id 
        $sql="SELECT e_id as 'id'
              FROM `p_expense`
              WHERE 
              MONTH(e_receipt_date)='{$params['month']}' AND YEAR(e_receipt_date)='{$params['year']}'

              ";
        $payload = new ControllerPayload(200, $this->mysqlService->queryOps($sql));


        return $this->respondJSON($response,$payload);    
    }
    
    public function create(Request $request, Response $response, $args){
        $this->mysqlService->changeDbOps("personal");

        $post=$this->getPostData($request);
        $form=[
            'session'=>'',
            'payment'=>'',
            'expenseType'=>'',
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
              VALUES (NULL, '{$form['session']}', '{$form['payment']}', '{$form['expenseType']}', '{$form['amount']}', '{$form['claimable']}', '{$form['refundable']}', '{$form['remark']}','{$form['receiptDate']}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";
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
            'session'=>'',
            'payment'=>'',
            'expenseType'=>'',
            'amount'=>'',
            'claimable'=>'',
            'refundable'=>'',
            'remark'=>'',
            'receiptDate'=>date('Y-m-d'),
        ];
        foreach ($form as $formProp => $formValue) {
            if(isset($post[$formProp]))$form[$formProp]=$post[$formProp];
        }

        $sql="UPDATE `p_expense` SET
        `s_id`='{$form['session']}',
        `pt_id`='{$form['payment']}',
        `et_id`='{$form['expenseType']}',
        `e_amount`='{$form['amount']}',
        `e_claimable`='{$form['claimable']}',
        `e_remark`='{$form['remark']}',
        `e_update_date`=CURRENT_TIMESTAMP,
        `e_receipt_date`='{$form['receiptDate']}',

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


    */
    
}