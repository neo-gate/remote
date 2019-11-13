<?php

include("include/common.php");



$error = "";
$error_mail = "";

$ch = $_GET["ch"];
$staff_id = $_GET["id"];

$result = staff_shift_page_access_check($staff_id,$ch);
if( $result == false ){
	echo "error!";
	exit();
}

if( isset($_POST["send"]) == true ){
	
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
	$ch = $_POST["ch"];
	
	$start_time_check_all = $_POST["start_time_check_all"];
	$end_time_check_all = $_POST["end_time_check_all"];
	
	$match_flg = false;
	$time_miss_match_flg = false;
	$check_flg = false;
	
	if( (($start_time_check_all == "-1") && ($end_time_check_all != "-1")) || (($start_time_check_all != "-1") && ($end_time_check_all == "-1")) ){
		
		$time_miss_match_flg = true;
		
	}
	
	for( $i=1; $i<=31; $i++ ){
		
		$start_time = $_POST["start_time_".$i];
		$end_time = $_POST["end_time_".$i];
		
		$jitaku_taiki_flg = $_POST["jitaku_taiki_flg_".$i];
		
		$att_area = $_POST["att_area_".$i];
		
		$check = $_POST["check_".$i];
		
		if( $check == "" ){
			
			$check = "0";
			
		}
		
		if( $check == "1" ){
			
			$check_flg = true;
			
		}
		
		if( $jitaku_taiki_flg != "1" ){
			
			$jitaku_taiki_flg = 0;
			
		}
		
		if( $start_time == "" ){
			
			$start_time = "-1";
			
		}
		
		if( $end_time == "" ){
				
			$end_time = "-1";
				
		}
		
		if( ($start_time != "-1") && ($end_time != "-1") ){
			
			$match_flg = true;
			
		}
		
		
		if( $start_time > $end_time ){
			
			$time_miss_match_flg = true;
			
		}
		
		if( (($start_time == "-1") && ($end_time != "-1")) || (($start_time != "-1") && ($end_time == "-1")) ){
			
			$time_miss_match_flg = true;
			
		}
		
		if( $start_time == $end_time ){
			
			if($start_time != "-1"){
				
				$time_miss_match_flg = true;
				
			}
			
		}
		
		$shift_time_data[$i]["start_time"] = $start_time;
		$shift_time_data[$i]["end_time"] = $end_time;
		
		$shift_time_data[$i]["jitaku_taiki_flg"] = $jitaku_taiki_flg;
		
		$shift_time_data[$i]["att_area"] = $att_area;
		
		$shift_time_data[$i]["check"] = $check;
		
	}
	
	/*
	echo "<pre>";
	print_r($shift_time_data);
	echo "</pre>";
	exit();
	*/
	
	$start_time_check_all = $_POST["start_time_check_all"];
	$end_time_check_all = $_POST["end_time_check_all"];
	
	if( ( $start_time_check_all != "-1" ) && ( $end_time_check_all != "-1" ) && ( $check_flg == true ) ){
		
		$match_flg = true;
		
	}
	
	if( $match_flg == false ){
		
		$error_list = '<span style="color:red;">選択されていません。</span>';
		
	}else if( $time_miss_match_flg == true ){
		
		$error_list = '<span style="color:red;">時間が不正です。</span>';
		
	}
	
	if( $error_list == "" ){
		
		regist_shift_data_by_staff($staff_id,$area,$year,$month,$shift_time_data,$start_time_check_all,$end_time_check_all);
		
		$error_list = '<span style="color:blue;">シフトを登録しました</span>';
		
	}
	

}else if( ($_GET["area"] != "") && ($_GET["id"] != "") && ($_GET["year"] != "") && ($_GET["month"] != "") ){
	
	$area = $_GET["area"];
	$staff_id = $_GET["id"];
	$year = $_GET["year"];
	$month = $_GET["month"];
	
}else if( ($_GET["area"] != "") && ($_GET["id"] != "") ){
	
	$area = $_GET["area"];
	$staff_id = $_GET["id"];
	
	$year = intval(date('Y'));
	$month = intval(date('m'));
	
}else{

	echo "error!";
	exit();

}

$area = escape_for_db($area);
$staff_id = escape_for_db($staff_id);
$year = escape_for_db($year);
$month = escape_for_db($month);

$staff_name = get_staff_name_by_staff_id($staff_id);

$attendance_data = get_staff_month_attendance_data($staff_id, $year, $month,$area);

$max_day = get_max_day($year,$month);

$day_data = get_day_data($year,$month,$max_day);

//$list_data = get_attendance_list_data_staff($attendance_data,$day_data,$staff_id,$area,$ch);
$list_data = get_attendance_list_data_staff_3($attendance_data,$day_data,$staff_id,$area,$ch);

$today_year = intval(date("Y"));
$today_month = intval(date("m"));

$next_year = get_next_year($today_year,$today_month);
$next_month = get_next_month($today_month);

$year_3 = get_next_year($next_year,$next_month);
$month_3 = get_next_month($next_month);

$period_select_option = creat_period_select_option(
$therapist_id,$today_year,$today_month,$next_year,$next_month,$area);

$top_url = get_top_url($area,$therapist_id,$ch);
$params['top_url'] = $top_url;



$limit_num = "3";
$message_board_data = get_message_board_driver_2($limit_num,$area);
$message_board_data_num = count($message_board_data);

$params['message_board_data'] = $message_board_data;
$params['message_board_data_num'] = $message_board_data_num;

$type = "start";
$start_time_check_all_option = get_shift_time_option_for_selected_4($type,$start_time_check_all,$end_time_check_all);

$type = "end";
$end_time_check_all_option = get_shift_time_option_for_selected_4($type,$start_time_check_all,$end_time_check_all);

$params['start_time_check_all_option'] = $start_time_check_all_option;
$params['end_time_check_all_option'] = $end_time_check_all_option;



/*
echo "<pre>";
print_r($attendance_data);
echo "</pre>";
exit();
*/

$params['top_flg'] = true;
$params['staff_name'] = $staff_name;
$params['day_data'] = $day_data;
$params['list_data'] = $list_data;
$params['period_select_option'] = $period_select_option;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['year'] = $year;
$params['month'] = $month;
$params['next_month'] = $next_month;
$params['next_year'] = $next_year;
$params['today_month'] = $today_month;
$params['today_year'] = $today_year;
$params['ch'] = $ch;
$params['page_title'] = $staff_name."さんのページ";

$params['year_3'] = $year_3;
$params['month_3'] = $month_3;

$params['error_list'] = $error_list;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/shift_regist.tpl' );
$smarty->display( 'sp/template_beta.tpl' );

?>