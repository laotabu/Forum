<?php
date_default_timezone_set("PRC");
error_reporting( E_ALL&~E_NOTICE );
function DatetimeToSeconds($date_time) {
	$obj=array();
	$obj=explode(" ",$date_time);
	$start=$obj[0];
	$end=$obj[1];
	
    $result = array();
    $result = explode("-", $start);
    $year = $result[0];
    $month = $result[1];
    $day = $result[2];
	
	
	$result1 = array();
	$result1 = explode(":", $end);
	$hour = $result1[0];
	$minite = $result1[1];
	$second = $result1[2];
    
 
    // 开始计算
    $seconds = mktime($hour, $minite, $second, $month, $day, $year);
 
    return $seconds;

}
