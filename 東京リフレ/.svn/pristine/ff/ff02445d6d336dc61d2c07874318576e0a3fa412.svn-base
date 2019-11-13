<?php

	require_once("../include/common.php");
	
	$time1 = time();
	
	include(COMMON_INC."skill_data.php");
	
	$today_flg = false;
	
	if( ( $_GET["year"] != "" ) && ( $_GET["month"] != "" ) && ( $_GET["day"] != "" ) ){
		
		$year = $_GET["year"];
		$month = $_GET["month"];
		$day = $_GET["day"];
		
		$today_flg = get_today_flg_common($year,$month,$day);
		
	}else{
	
		$now_hour = intval(date('H'));
		
		if($now_hour <= 6){
			//昨日の日付
			$year = intval(date('Y', strtotime('-1 day')));
			$month = intval(date('m', strtotime('-1 day')));
			$day = intval(date('d', strtotime('-1 day')));
		}else{
			$year = intval(date('Y'));
			$month = intval(date('m'));
			$day = intval(date('d'));
		}
		
		$today_flg = true;
	
	}
	
	$area = "tokyo";
	
	//出勤セラピストは「1」、出勤しないセラピストは「2」
	$type = 4;
	
	$attendance_therapist_data = get_therapist_page_data_attendance_2_common($area,$year,$month,$day,$type,WWW_URL,$access_type);
	
	$pankuzu = "　＞　セラピスト";
	
	$params['pankuzu'] = $pankuzu;
	
	$params['attendance_therapist_data'] = $attendance_therapist_data;
	
	$params['therapist_data'] = $therapist_data;
	
	$params['skill_data'] = $skill_data;
	
	$params['year'] = $year;
	$params['month'] = $month;
	$params['day'] = $day;
	$params['day_array'] = $day_array;
	$params['week_array'] = $week_array;
	
	$params['today_flg'] = $today_flg;
	
	$time2 = time();
	$action_time = $time2 - $time1;
	
	$smarty->assign( 'params', $params );
	
	if( $access_type == "sp" ){
	
		$smarty->display( 'sp/lp4/list.tpl' );
	
	}else{
	
		$smarty->display( 'pc/lp4/list.tpl' );
	
	}

?>