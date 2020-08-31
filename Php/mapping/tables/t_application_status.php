<?php

$sql1="TRUNCATE `$mappingDB`.`t_application_status`";
$sql2="INSERT INTO `$mappingDB`.`t_application_status`(as_id,as_label,as_key) 
       SELECT asid,aslabel,askey FROM `LAI`.ApplyStatus";
array_push($querys,$sql1);
array_push($querys,$sql2);
?>