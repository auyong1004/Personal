<?php
declare(strict_types=1);

use App\Application\Controllers\Option\OptionController;
use App\Application\Controllers\Expense\ExpenseController;
use App\Application\Controllers\Task\TaskController;
use App\Application\Controllers\TaskList\TaskListController;
use App\Application\Controllers\Calculator\CalculatorController;

use App\Application\Controllers\Sync\SyncController;
use App\Application\Controllers\Report\ReportController;
use App\Application\Controllers\Dashboard\DashboardController;
use App\Application\Controllers\Event\EventController;



use App\Application\Controllers\Google\GoogleSheetController;
use App\Application\Controllers\Google\GoogleDriveController;
use App\Application\Controllers\Google\GoogleAuthController;
use App\Application\Controllers\Google\GoogleCalendarController;



use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;



return function (App $app) {
    // cors
    $app->options('/{routes:.*}', function ($request, $response, $args) {
        return $response;
    });
    /*

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Personal-API');
        return $response;
    });
    */
    $app->get('/',DashboardController::class . ':index');
    $app->get('/todo',TaskController::class . ':index');
    $app->get('/expense',ExpenseController::class . ':index');
    $app->get('/calculator',CalculatorController::class . ':index');
    $app->get('/report',ReportController::class . ':index');
    $app->get('/schedule',EventController::class . ':index');

    $app->group('/api/event', function (Group $group) {
        //$group->get('/{id}', ViewUserAction::class);
        $group->get('',EventController::class . ':list');
        $group->post('',EventController::class . ':create');
        $group->put('/{id}',EventController::class . ':update');
        $group->delete('/{id}',EventController::class . ':remove');
    });


    $app->group('/api/dashboard', function (Group $group) {
        //$group->get('/{id}', ViewUserAction::class);
        $group->get('',DashboardController::class . ':list');
    });

    $app->group('/api/sync', function (Group $group) {
        $group->get('/excel',SyncController::class . ':excel');
    });

    $app->group('/api/google', function (Group $group) {
        //$group->get('/{id}', ViewUserAction::class);
        $group->get('/sheet',GoogleSheetController::class . ':list');
        $group->get('/drive',GoogleDriveController::class . ':list');
        
        $group->get('/calendar',GoogleCalendarController::class . ':list');


        //oAuth
        $group->get('/oAuth/login',GoogleAuthController::class . ':login');
        $group->get('/oAuth/logout',GoogleAuthController::class . ':logout');
        $group->get('/oAuth/session',GoogleAuthController::class . ':session');
 

    });
    $app->group('/api/report', function (Group $group) {
        //$group->get('/{id}', ViewUserAction::class);
        $group->get('',ReportController::class . ':list');
    });

    $app->group('/api/option', function (Group $group) {
        //$group->get('/{id}', ViewUserAction::class);
        $group->get('/month',OptionController::class . ':month');
        $group->get('/year',OptionController::class . ':year');
        $group->get('/remark',OptionController::class . ':remark');
        $group->get('/paymentType',OptionController::class . ':paymentType');
        $group->get('/expenseType',OptionController::class . ':expenseType');
        $group->get('/session',OptionController::class . ':session');
        $group->get('/taskList',OptionController::class . ':taskList');

 

    });

    $app->group('/api/expense', function (Group $group) {
        //$group->get('/{id}', ViewUserAction::class);
        $group->get('',ExpenseController::class . ':list');
        $group->post('',ExpenseController::class . ':create');
        $group->put('/{id}',ExpenseController::class . ':update');
        $group->delete('/{id}',ExpenseController::class . ':remove');
    });

    $app->group('/api/task', function (Group $group) {
        //$group->get('/{id}', ViewUserAction::class);
        $group->get('',TaskController::class . ':list');
        $group->post('',TaskController::class . ':create');
        $group->put('/{id}',TaskController::class . ':update');
        $group->delete('/{id}',TaskController::class . ':remove');
    });

    $app->group('/api/taskList', function (Group $group) {
        //$group->get('/{id}', ViewUserAction::class);
        $group->get('',TaskListController::class . ':list');
        $group->post('',TaskListController::class . ':create');
        $group->put('/{id}',TaskListController::class . ':update');
        $group->delete('/{id}',TaskListController::class . ':remove');
    });
};