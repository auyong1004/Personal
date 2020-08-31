<?php

$sql1="TRUNCATE `$mappingDB`.`t_course_payment`";
$sql2="INSERT INTO `$mappingDB`.`t_course_payment`(cp_id,cp_label) 
       SELECT * FROM `LAI`.CoursePayment";
array_push($querys,$sql1);
array_push($querys,$sql2);
?>