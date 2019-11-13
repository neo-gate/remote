<?php

include("include/common.php");



$error = "";

if( isset($_POST["send"]) == true ){
	
	$staff_id = $_POST["staff_id"];
	$area = $_POST["area"];
	$year = $_POST["year"];
	$month = $_POST["month"];
	$day = $_POST["day"];
	$start_time = $_POST["start_time"];
	$end_time = $_POST["end_time"];
	$start_start_time = $_POST["start_start_time"];
	$start_end_time = $_POST["start_end_time"];
	$kekkin = $_POST["kekkin"];
	$week_name = $_POST["week_name"];
	$ch = $_POST["ch"];
	
	if( $kekkin == "1" ){
		
		shift_kekkin_action_2($staff_id,$area,$year,$month,$day,$week_name);
		
		$url = sprintf("edit_complete.php?area=%s&id=%s&kekkin=1&ch=%s",$area,$staff_id,$ch);
		
		header("Location: ".$url);
		exit();
		
	}else{
	
		if( ($start_time == $end_time) || ($start_time > $end_time) ){
				
			$error = "時間が不正です";
				
		}
		
		if( $error == "" ){
			
			shift_edit_action_2(
$staff_id,$area,$year,$month,$day,$start_time,$end_time,$start_start_time,$start_end_time,$week_name);
			
			$url = sprintf("edit_complete.php?area=%s&id=%s&kekkin=0&ch=%s",$area,$staff_id,$ch);
			
			header("Location: ".$url);
			exit();
			
		}
	
	}
	

}else if( isset($_POST["send_list_edit"]) == true ){
	
	/*
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	exit();
	*/
	
	$staff_id = $_POST["staff_id"];
	$area = $_POST["area"];
	$year = $_POST["year"];
	$month = $_POST["month"];
	$day = $_POST["day"];
	$start_time = $_POST["start_time"];
	$end_time = $_POST["end_time"];
	$start_start_time = $_POST["start_time"];
	$start_end_time = $_POST["end_time"];
	$ch = $_POST["ch"];
	
	$week = get_week_value($year, $month, $day);
	$week_name = get_week_name($week);
	
}else{
	
	echo "error!";
	exit();
	
}

$staff_name = get_staff_name_by_staff_id($staff_id);

$today_year = intval(date("Y"));
$today_month = intval(date("m"));

$type = "start";
$start_time_option = get_shift_time_option_for_selected_2_2($type,$start_time,$end_time);

$type = "end";
$end_time_option = get_shift_time_option_for_selected_2_2($type,$start_time,$end_time);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;



$params['staff_name'] = $staff_name;
$params['staff_id'] = $staff_id;
$params['area'] = $area;
$params['year'] = $year;
$params['month'] = $month;
$params['day'] = $day;
$params['start_time'] = $start_time;
$params['end_time'] = $end_time;
$params['start_start_time'] = $start_start_time;
$params['start_end_time'] = $start_end_time;
$params['today_month'] = $today_month;
$params['today_year'] = $today_year;
$params['week_name'] = $week_name;
$params['start_time_option'] = $start_time_option;
$params['end_time_option'] = $end_time_option;
$params['error'] = $error;
$params['page_title'] = "シフト修正";
$params['ch'] = $ch;

$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/edit.tpl' );
$smarty->display( 'sp/template.tpl' );

?>