<?php 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once('../src/Dependency/MysqlService.php');
require_once('../src/Dependency/PdoService.php');
use App\Dependency\MysqlService as MysqlService;

exit('Had been disabled');

$access=[
    'host'=>'10.80.10.3',
    'username'=>'api_mis',
    'password'=>'b2NCXCuNhwRV7ubq',
    'database'=>'training',
];
$mysql=new MysqlService($access);

$toMap=true;
$querys=[];
$mappingDB="training";

//get query
$path    = './tables';
$files = scandir($path);
$files = array_diff(scandir($path), array('.', '..'));
foreach ($files as $file) {
    require_once("./tables/$file");

}

//run query

if(!$toMap){
    exit('no mapping');
}
foreach ($querys as $query) {
    $output=$mysql->queryOps($query,"TRUNCATE/INSERT");

    if(!$output){
        echo $query;
        print_r($mysql->errorLog());
    }
}
?>