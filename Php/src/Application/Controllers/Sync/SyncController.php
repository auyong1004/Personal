<?php
declare(strict_types=1);

namespace App\Application\Controllers\Sync;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class SyncController extends Controller
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
    public function excel(Request $request, Response $response,$args){

        exit('done');

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("Expense(2020).xlsx");
        $sheetCount = $spreadsheet->getSheetCount();

        $rows=[];
        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $rows=array_merge($rows, $sheet->toArray(null, true, true, true));
        }
        $rows=array_filter($rows,function($row){
            if(is_null($row['A']))$row['A']='';
            $rowA=\explode('-',$row['A']);
            return(count($rowA)==3);
        });
        $expenses=[];
        foreach($rows as $rowIndex=>$row){
            //if($rowIndex>1)break;
            $temp=[];
            $columns=$this->getColumns();
            foreach ($row as $rowProp => $rowValue) {
                //$expense=$this->getExpenseModel();
                if(isset($columns[$rowProp]))$temp[$columns[$rowProp]]=$rowValue;


            }

            foreach ($temp as $tempProp => $tempValue) {
                if(in_array($tempProp,['Date','Remark','Loan,Bill']))continue;
                if($tempValue==0)continue;
        
                $expense=$this->getExpenseModel();
                $expense['receiptDate']= $this->dateReformat($temp['Date']);//\DateTime::createFromFormat('Y-m-d', $temp['Date'])->format('Y-m-d');
                $expense['amount']=$tempValue;
                $expense['remark']=$tempProp;

                if(in_array($tempProp,['Breakfast','Lunch','Dinner'])){
                 
                    $expense['expenseType']='3';   //period
                    $expense['paymentType']='1';   //cash
                    $expense['session']=$this->getSession($tempProp);
                   
                }else{
                    $expense['session']=3;
                    $expense['expenseType']='3';   //period
                    $expense['paymentType']='1';   //cash
                    if($tempProp=='Entertainment')$expense['expenseType']=3;

                    


                }
        
                $sql="INSERT INTO `p_expense` (`e_id`, `s_id`, `pt_id`, `et_id`, `e_amount`, `e_claimable`, `e_refundable`, `e_remark`,`e_receipt_date`, `e_create_date`, `e_update_date`)
                VALUES (NULL, '{$expense['session']}', '{$expense['paymentType']}', '{$expense['expenseType']}', '{$expense['amount']}', '{$expense['claimable']}', '{$expense['refundable']}', '{$expense['remark']}','{$expense['receiptDate']}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";
                $this->mysqlService->queryOps($sql,"INSERT");

            }


        }

        $payload = new ControllerPayload(200, "");

        return $this->respondJSON($response,$payload);    

    }
    protected function dateReformat($date){
        $arr=explode('-',$date);
        $date=[
            'month'=>strlen($arr[0])==2?$arr[0]:"0{$arr['0']}",
            'day'=>strlen($arr[1])==2?$arr[1]:"0{$arr['1']}",
            'year'=>$arr[2]
        ];
        return "20{$date['year']}-{$date['month']}-{$date['day']}";
    }
    protected function getColumns(){

        return $columns=[
          'A'=>'Date',  
          'B'=>'Breakfast',  
          'C'=>'Lunch',  
          'D'=>'Dinner',  
          'E'=>'Transport',  
          'F'=>'Entertainment',  
          'G'=>'Loan,Bill',  
          'H'=>'Other',  
          'J'=>'Remark',  
          //'K'=>'',  
          //'I'=>''
        ];
    }

    protected function getExpenseModel(){
        return [
            'session'=>'',
            'expenseType'=>'',
            'paymentType'=>'',
            'amount'=>'',
            'claimable'=>0,
            'refundable'=>0,
            'remark'=>'',
            'receiptDate'=>date('Y-m-d')
        ];
    }
  
    protected function getSession($prop){
        $session= [
            'Breakfast'=>'1',
            'Lunch'=>'2',
            'Dinner'=>'3',
        ];
        return $session[$prop];
    }
    protected function getPaymentType(){}



    
}