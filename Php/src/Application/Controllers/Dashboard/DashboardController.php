<?php
declare(strict_types=1);

namespace App\Application\Controllers\Dashboard;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Dependency\MysqlService;

class DashboardController extends Controller
{

    public $year;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        
        parent::__construct($container);

    }
    public function index(Request $request, Response $response, $args){

    
        return $this->twig->render($response, 'dashboard.html');


    }        
    public function list(Request $request, Response $response, $args){
        //total expense
        //reminder of the day
        //task in pending
        $params=[
            'year'=>date('Y'),
            'month'=>date('n')
        ];
        $this->mysqlService->changeDbOps("personal");
        $totalSql="SELECT SUM(e_amount) as 'total'  FROM `p_expense` WHERE YEAR(e_receipt_date)='{$params['year']}' AND MONTH(e_receipt_date)='{$params['month']}'";
        $taskSql="SELECT a.t_id as 'id' ,a.t_subject as 'subject',a.t_description as 'description',a.t_priority as 'priority' ,
              DATE_FORMAT(a.t_due_date,'%Y-%m-%d') as 'dueDate',a.t_stat as 'stat', a.tk_id as 'list',b.tk_name as 'listName',b.tk_color as 'listColor'
              FROM `p_task` a
              LEFT JOIN `p_task_list` b on a.tk_id=b.tk_id WHERE a.t_stat=0
              ";

        $eventSql="SELECT a.e_id as 'id',a.e_subject as 'subject',a.e_description as 'description',a.e_type as 'type',e_cron as 'cron',e_recurring as 'recurring',
        date_format(a.e_start_date,'%Y-%m-%d') as 'startDate',date_format(a.e_end_date,'%Y-%m-%d') as 'endDate'
        FROM `p_event` a
        ";
        $result=[
            'expense'=>$this->mysqlService->queryOps($totalSql)[0]['total'],
            'tasks'=>$this->mysqlService->queryOps($taskSql),
            'events'=>$this->mysqlService->queryOps($eventSql),
        ];

        $payload = new ControllerPayload(200, $result);

        return $this->respondJSON($response,$payload);    
    }





    
}