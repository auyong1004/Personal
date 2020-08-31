<?php
declare(strict_types=1);

namespace App\Application\Controllers\Google;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Application\Controllers\Controller;
use App\Application\Controllers\ControllerPayload;

use App\Application\Controllers\Google\GoogleController;

use App\Dependency\MysqlService;
use Google_Client;
use Google_Service_Calendar;

class GoogleCalendarController extends GoogleController
{

    /**
     * @var Google_Client
     */
    protected $google;

    /**
     * @param ContainerInterface $logger
     */
    public function __construct(ContainerInterface $container)
    {   

        parent::__construct($container);

    }
    public function list(Request $request, Response $response, $args){


        $get=$this->mysqlService->escapeStrArrOps($_GET);
        $params=[
            'year'=>date('Y'),
            'month'=>date('n'),
        ];
        foreach ($params as $paramsProp => $paramsValue) {
            if(isset($get[$paramsProp]))$params[$paramsProp]=$get[$paramsProp];
        }
        $date=new \DateTime();
        $date->setDate(intval ($params['year']),intval ($params['month']),1);

        $service = new Google_Service_Calendar($this->google);
        $calendarId = 'primary';

        $optParams = array(
          'maxResults' => 10,
          'orderBy' => 'startTime',
          'singleEvents' => true,
          'timeMin' => $date->format('Y-m-d\TH:i:sO'),//ISO 8601
        );

        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        $rows= [];
        foreach ($events as $event) {
            $row=[
                'id'=>$event->getId(),
                'subject'=>$event->getSummary(),
                'description'=>$event->getDescription(),
                'startDate'=>$event->start->dateTime,
                'endDate'=>$event->end->dateTime,
                'link'=>$event->htmlLink,
                'type'=>'google'
            ];
            array_push($rows,$row);
            
        }
        $result=[
            'total'=>count($events),
            'events'=>$rows,
        ];
        //$payload = new ControllerPayload(200, $events);
        $payload = new ControllerPayload(200, $result);
        return $this->respondJSON($response,$payload);    

    }



    
}