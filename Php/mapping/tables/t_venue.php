<?php

$sql1="TRUNCATE `$mappingDB`.`t_venue`";
$sql2="INSERT INTO `$mappingDB`.`t_venue`(v_id,v_name,v_address,v_tel,v_fax,v_pic,v_email,v_ext) 
       SELECT Venue_ID,VenueName,VenueAddress,VenueTel,VenueFax,Pname,VenueEmail,PICnum FROM `LAI`.Venue";
array_push($querys,$sql1);
array_push($querys,$sql2);
?>