<?php

$sql1="TRUNCATE `$mappingDB`.`t_course_type`";
$sql2="INSERT INTO `$mappingDB`.`t_course_type`(ct_id,ct_label) 
       SELECT ctid,ctlabel FROM `LAI`.CourseType";
array_push($querys,$sql1);
array_push($querys,$sql2);
?>