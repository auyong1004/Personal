<?php

$sql1="TRUNCATE `$mappingDB`.`t_course`";
$sql2="INSERT INTO `$mappingDB`.`t_course`
      (c_id,c_title,v_id,ct_id,c_description,cs_id,c_duration,c_location,c_start,c_end,c_exam,c_icon,c_docs,cp_id) 
       SELECT 
       AttendedCourse_ID,CourseTitle,Venue_ID,Types,CourseDescription,Status,CourseDuration,Location,
       StartDate,EndDate,Exam,p_icon,docs,
       Payment FROM `LAI`.NewCourse";
array_push($querys,$sql1);
array_push($querys,$sql2);
?>