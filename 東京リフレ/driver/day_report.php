<?php

include("include/common.php");



$area = $_GET["area"];
$staff_id = $_GET["id"];
$ch = $_GET["ch"];
if( ($area == "") || ($staff_id == "") ){
	echo "error!";
	exit();
}

$error = "";

if( isset($_POST["send"]) == true ){
	
	$start_time = $_POST["start_time"];
	$end_time = $_POST["end_time"];
	$car_distance = $_POST["car_distance"];
	$highway = $_POST["highway"];
	$parking = $_POST["parking"];
	$pay_finish = $_POST["pay_finish"];
	$comment = $_POST["comment"];
	
	$attendance_staff_new_id = $_POST["attendance_staff_new_id"];
	
	$pieces = explode("_", $start_time);
	$start_hour = $pieces[0];
	$start_minute = $pieces[1];
	
	$pieces = explode("_", $end_time);
	$end_hour = $pieces[0];
	$end_minute = $pieces[1];
	
	if( ($start_time=="-1") || ($end_time=="-1") ){
		
		$error = "時間が未選択です";
		
	}else{
		
		$result = check_start_end_hour_minute_common($start_hour,$start_minute,$end_hour,$end_minute);
		
		if( $result == false ){
			
			$error = "時間が不正です";
			
		}
		
	}
	
	if( $car_distance == "" ){
		$error = "距離が未入力です";
	}else{
		$result = check_car_distance_value_common($car_distance);
		if( $result == false ){
			$error = "距離は半角数字とピリオドのみです";
		}
	}
	
	if( $highway == "" ){
		$error = "高速代が未入力です";
	}else{
		$result = check_hankaku_num_value_common($highway);
		if( $result == false ){
			$error = "高速代は半角数字のみです";
		}
	}
	
	if( $parking == "" ){
		$error = "駐車場代が未入力です";
	}else{
		$result = check_hankaku_num_value_common($parking);
		if( $result == false ){
			$error = "駐車場代は半角数字のみです";
		}
	}
	
	if( $pay_finish == "" ){
		$error = "精算済みが未入力です";
	}else{
		$result = check_hankaku_num_value_common($pay_finish);
		if( $result == false ){
			$error = "精算済みは半角数字のみです";
		}
	}
	
	$num = mb_strlen( $comment );
	if( $num > 240 ){
		$error = "特記事項は240文字までです";
	}
	
	if( $error == "" ){
		
		$_SESSION["d_r_car_distance"] = $car_distance;
		$_SESSION["d_r_highway"] = $highway;
		$_SESSION["d_r_parking"] = $parking;
		$_SESSION["d_r_pay_finish"] = $pay_finish;
		$_SESSION["d_r_comment"] = $comment;
		$_SESSION["d_r_start_hour"] = $start_hour;
		$_SESSION["d_r_start_minute"] = $start_minute;
		$_SESSION["d_r_end_hour"] = $end_hour;
		$_SESSION["d_r_end_minute"] = $end_minute;
		
		$_SESSION["d_r_attendance_staff_new_id"] = $attendance_staff_new_id;
		
		$url = sprintf("day_report_confirm.php?area=%s&id=%s&ch=%s",$area,$staff_id,$ch);
		
		header("Location: ".$url);
		exit();
		
	}
	
}else if(isset($_GET["back"])==true){
	
	$car_distance = $_SESSION["d_r_car_distance"];
	$highway = $_SESSION["d_r_highway"];
	$parking = $_SESSION["d_r_parking"];
	$pay_finish = $_SESSION["d_r_pay_finish"];
	$comment = $_SESSION["d_r_comment"];
	$start_hour = $_SESSION["d_r_start_hour"];
	$start_minute = $_SESSION["d_r_start_minute"];
	$end_hour = $_SESSION["d_r_end_hour"];
	$end_minute = $_SESSION["d_r_end_minute"];
	
	$attendance_staff_new_id = $_SESSION["d_r_attendance_staff_new_id"];
	
}else{

	$data = get_today_year_month_day_common();
	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];
	$attendance_data = get_attendance_staff_new_day_and_staff_id_common($year,$month,$day,$staff_id);
	$attendance_staff_new_id = $attendance_data["id"];
	
	$start_time = $attendance_data["start_time"];
	$end_time = $attendance_data["end_time"];
	$start_hour = $attendance_data["start_hour"];
	$start_minute = $attendance_data["start_minute"];
	$end_hour = $attendance_data["end_hour"];
	$end_minute = $attendance_data["end_minute"];
	
	if( $attendance_staff_new_id != "" ){
	
		$data = get_attendance_staff_2_by_attendance_staff_new_id($attendance_staff_new_id);
	
		$start_hour_d = $data["start_hour_d"];
		$start_minute_d = $data["start_minute_d"];
		$end_hour_d = $data["end_hour_d"];
		$end_minute_d = $data["end_minute_d"];
		$car_distance = if_null_is_zero_common($data["car_distance_d"]);
		$highway = if_null_is_zero_common($data["highway_d"]);
		$parking = if_null_is_zero_common($data["parking_d"]);
		$pay_finish = if_null_is_zero_common($data["pay_finish_d"]);
		$comment = $data["comment_d"];
		
		if(
	($start_hour_d == "-1") || ($start_minute_d == "-1") || ($end_hour_d == "-1") || ($end_minute_d == "-1") || 
	($start_hour_d == "") || ($start_minute_d == "") || ($end_hour_d == "") || ($end_minute_d == "")
		){
			
			if( ($start_hour == "-1") || ($start_minute == "-1") || ($end_hour == "-1") || ($end_minute == "-1") ){
			
				$tmp = change_from_time_to_hour_minute_driver_common($start_time,$end_time);
				
				$start_hour = $tmp["start_hour"];
				$start_minute = $tmp["start_minute"];
				$end_hour = $tmp["end_hour"];
				$end_minute = $tmp["end_minute"];
			
			}
			
		}else{
			
			$start_hour = $start_hour_d;
			$start_minute = $start_minute_d;
			$end_hour = $end_hour_d;
			$end_minute = $end_minute_d;
			
		}
		
	}

}

$select_name = "start_time";
$select_frm_work_time_start = get_select_frm_work_time_driver_common($select_name,$start_hour,$start_minute);

$select_name = "end_time";
$select_frm_work_time_end = get_select_frm_work_time_driver_common($select_name,$end_hour,$end_minute);

$params['select_frm_work_time_start'] = $select_frm_work_time_start;
$params['select_frm_work_time_end'] = $select_frm_work_time_end;

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

/*
echo "<pre>";
print_r($sale_data);
echo "</pre>";
exit();
*/

$page_title = "業務開始・締め処理(入力)";

$params['ch'] = $ch;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;

$params['car_distance'] = $car_distance;
$params['highway'] = $highway;
$params['parking'] = $parking;
$params['pay_finish'] = $pay_finish;
$params['comment'] = $comment;

$params['attendance_staff_new_id'] = $attendance_staff_new_id;

$params['error'] = $error;

$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/day_report.tpl' );
$smarty->display( 'sp/template.tpl' );

?>