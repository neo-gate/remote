<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");

//----insert by aida at 20180317 from
require_once(SETUP_PATH . "today.inc");
$w_NowTime = PHP_getNowTime();
//----insert by aida at 20180317 To

$now_hour = intval(date('H', $w_NowTime));
$now_minute = intval(date('i', $w_NowTime));

if( $now_minute < 10 ) $now_minute = "0".$now_minute;

if($now_hour <= 6){

	//昨日の日付
	$year_now = intval(date('Y', strtotime('-1 day', $w_NowTime)));
	$month_now = intval(date('m', strtotime('-1 day', $w_NowTime)));
	$day_now = intval(date('d', strtotime('-1 day', $w_NowTime)));
	$week_now = date("w", strtotime('-1 day', $w_NowTime));

}else{

	$year_now = intval(date('Y', $w_NowTime));
	$month_now = intval(date('m', $w_NowTime));
	$day_now = intval(date('d', $w_NowTime));
	$week_now = date("w", $w_NowTime);

}

/*
$week_name = "";

if($week_now==0){

	$week_name = "日曜日";

}else if($week_now==1){

	$week_name = "月曜日";

}else if($week_now==2){

	$week_name = "火曜日";

}else if($week_now==3){

	$week_name = "水曜日";

}else if($week_now==4){

	$week_name = "木曜日";

}else if($week_now==5){

	$week_name = "金曜日";

}else if($week_now==6){

	$week_name = "土曜日";

}
*/

//$week_name_sp = str_replace("曜日","",$week_name);

$week_name_sp = $ARRAY_Week[$week_now];
$week_name = $week_name_sp . "曜日";
//echo $year_now . "/" . $month_now . "/" . $day_now . " " .  $week_name . " " . $week_name_sp . "<br />";

$params['week_name'] = $week_name;
$params['week_name_sp'] = $week_name_sp;

//$area = "tokyo";
//----insert by aida at 20180314 from
if(!$area) {
	if(Domain_Area != "Domain_Area") $area = Domain_Area;
}
//----insert by aida at 20180314 to
$attendance_num = get_therapist_attendance_num_common($year_now,$month_now,$day_now,$area);
$reservation_message = get_reservation_message_common($now_hour,$area);

$params['year_now'] = $year_now;
$params['month_now'] = $month_now;
$params['day_now'] = $day_now;
$params['week_name'] = $week_name;
$params['now_hour'] = $now_hour;
$params['now_minute'] = $now_minute;
$params['attendance_num'] = $attendance_num;
$params['reservation_message'] = $reservation_message;

$vip_login_page = sprintf("%sssl/%s/vip/",WWW_URL_SSL, $area);
define("VIP_LOGIN_PAGE", $vip_login_page);
define("WWW_URL_VIP", $vip_login_page);

$time_disp_1_pc = "10:00&nbsp;～&nbsp;翌5:00";
$time_disp_2_pc = "18:00&nbsp;～&nbsp;翌8:00";
$time_disp_1_sp = "受付10時～翌5時";
$time_disp_2_sp = "営業18時～翌8時";
$css_file_name = "style.css";

$params['time_disp_1_pc'] = $time_disp_1_pc;
$params['time_disp_2_pc'] = $time_disp_2_pc;
$params['time_disp_1_sp'] = $time_disp_1_sp;
$params['time_disp_2_sp'] = $time_disp_2_sp;
$params['css_file_name'] = $css_file_name;
?>
