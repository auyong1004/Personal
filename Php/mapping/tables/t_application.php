<?php

$sql1="TRUNCATE `$mappingDB`.`t_application`";
$sql2="INSERT INTO `$mappingDB`.`t_application`(a_id,a_mail,c_id,as_id,a_notice,a_remarks,a_docs,a_date) 
       SELECT UserEmail_ID,User_ID,AttendedCourse_ID,Status,notice,remarks,docs,appDate FROM `LAI`.NewUser";
array_push($querys,$sql1);
array_push($querys,$sql2);
?>