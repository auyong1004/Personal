<?php

$sql1="TRUNCATE `$mappingDB`.`t_course_status`";
$sql2="INSERT INTO `$mappingDB`.`t_course_status`(cs_id,cs_label) 
       SELECT * FROM `LAI`.CourseStatus";
array_push($querys,$sql1);
array_push($querys,$sql2);
?>