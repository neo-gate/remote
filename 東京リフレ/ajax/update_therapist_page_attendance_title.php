<?php

$year = $_POST["year"];
$month = $_POST["month"];
$day = $_POST["day"];

$now_hour = intval(date('H'));
if($now_hour <= 6){
	//昨日の日付
	$year_today = intval(date('Y', strtotime('-1 day')));
	$month_today = intval(date('m', strtotime('-1 day')));
	$day_today = intval(date('d', strtotime('-1 day')));
}else{
	$year_today = intval(date('Y'));
	$month_today = intval(date('m'));
	$day_today = intval(date('d'));
}

$html = "";

if( ( $year == $year_today ) && ( $month == $month_today ) && ( $day == $day_today ) ){
	
	$html = "本日の出勤セラピスト";
	
}else{
	
	$html = $month."月".$day."日の出勤セラピスト";
	
}

echo $html;
exit();

?>