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

if( isset($_POST["send_plans"])==true ){
	
	$back_plans_state = $_POST["back_plans_state"];
	$back_plans_hour = $_POST["back_plans_hour"];
	$back_plans_minute = $_POST["back_plans_minute"];
	$attendance_staff_new_id = $_POST["attendance_staff_new_id"];
	
	if( ($back_plans_state == "-1") || ($back_plans_hour == "-1") || ($back_plans_minute == "-1") ){
		
		$error = '<span style="color:red;">未選択です</span>';
		
	}
	
	if( $attendance_staff_new_id == "" ){
	
		$error = '<span style="color:red;">本日は出勤日ではありません</span>';
	
	}
	
	if( $error == "" ){
		
		update_back_plans($attendance_staff_new_id,$back_plans_state,$back_plans_hour,$back_plans_minute);
		
		$type = "plans";
		send_mail_for_driver_page_5_common(
$staff_id,$area,$type,$back_plans_hour,$back_plans_minute,$back_plans_state);
		
		$error = '<span style="color:blue;">戻り予定を更新しました</span>';
		
	}
	
}else if( isset($_POST["send_arrival"])==true ){
	
	$back_plans_state = $_POST["back_plans_state"];
	$back_plans_hour = $_POST["back_plans_hour"];
	$back_plans_minute = $_POST["back_plans_minute"];
	$attendance_staff_new_id = $_POST["attendance_staff_new_id"];
	
	if( ($back_plans_state == "-1") || ($back_plans_hour == "-1") || ($back_plans_minute == "-1") ){
		
		$error = '<span style="color:red;">未選択です</span>';
		
	}
	
	if( $attendance_staff_new_id == "" ){
	
		$error = '<span style="color:red;">本日は出勤日ではありません</span>';
	
	}
	
	if( $error == "" ){
	
		$back_plans_state_2 = "9";
		update_back_plans($attendance_staff_new_id,$back_plans_state_2,$back_plans_hour,$back_plans_minute);
		
		$type = "arrival";
		send_mail_for_driver_page_5_common(
$staff_id,$area,$type,$back_plans_hour,$back_plans_minute,$back_plans_state);
		
		$error = '<span style="color:blue;">到着に設定しました</span>';
	
	}
	
}else if( isset($_POST["send_top_message_1"])==true ){
	
	$staff_id = $_POST["staff_id"];
	$ch = $_POST["ch"];
	$area = $_POST["area"];
	$message_id = $_POST["message_id"];
	
	$content = "了解";
	$type = "2";
	insert_board_message_history_common($type,$staff_id,$content);
	
	send_mail_for_driver_page_1_common($staff_id,$message_id,$ch,$area);
	
	$url = sprintf("%sdriver/communication.php?area=%s&id=%s&ch=%s",WWW_URL_TOKYO,$area,$staff_id,$ch);
	
	header("Location: ".$url);
	exit();
	
}else if( isset($_POST["send_top_message_2"])==true ){
	
	$staff_id = $_POST["staff_id"];
	$ch = $_POST["ch"];
	$area = $_POST["area"];
	
	$url = sprintf("%sdriver/history.php?area=%s&id=%s&ch=%s",WWW_URL_TOKYO,$area,$staff_id,$ch);
	
	header("Location: ".$url);
	exit();
	
}else if( isset($_POST["send_okuri_1"])==true ){
	
	//確認
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$okuri_hour = $_POST["okuri_hour"];
	$okuri_minute = $_POST["okuri_minute"];
	
	$type = "okuri";
	$state = "1";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$okuri_hour,$okuri_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$okuri_hour,$okuri_minute);
	
	$error = '<span style="color:blue;">確認に設定しました</span>';
	
}else if( isset($_POST["send_okuri_2"])==true ){
	
	//着予定
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$okuri_hour = $_POST["okuri_hour"];
	$okuri_minute = $_POST["okuri_minute"];
	
	$type = "okuri";
	$state = "2";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$okuri_hour,$okuri_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$okuri_hour,$okuri_minute);
	
	$error = '<span style="color:blue;">着予定に設定しました</span>';
	
}else if( isset($_POST["send_okuri_3"])==true ){
	
	//到着待機
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$okuri_hour = $_POST["okuri_hour"];
	$okuri_minute = $_POST["okuri_minute"];
	
	$type = "okuri";
	$state = "3";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$okuri_hour,$okuri_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$okuri_hour,$okuri_minute);
	
	$error = '<span style="color:blue;">到着待機に設定しました</span>';
	
}else if( isset($_POST["send_okuri_4"])==true ){
	
	//降車
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$okuri_hour = $_POST["okuri_hour"];
	$okuri_minute = $_POST["okuri_minute"];
	
	$type = "okuri";
	$state = "9";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$okuri_hour,$okuri_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$okuri_hour,$okuri_minute);
	
	$error = '<span style="color:blue;">降車に設定しました</span>';
	
}else if( isset($_POST["send_okuri_5"])==true ){
	
	//送信
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$okuri_hour = $_POST["okuri_hour"];
	$okuri_minute = $_POST["okuri_minute"];
	
	$comment = $_POST["comment"];
	
	$type = "okuri";
	$state = "0";
	
	update_reservation_board_2_comment_common($reservation_for_board_id,$type,$comment);
	
	send_mail_for_driver_page_4_common(
$staff_id,$area,$type,$comment);
	
	$error = '<span style="color:blue;">コメントを送信しました</span>';
	
}else if( isset($_POST["send_mukae_1"])==true ){
	
	//確認
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$mukae_hour = $_POST["mukae_hour"];
	$mukae_minute = $_POST["mukae_minute"];
	
	$type = "mukae";
	$state = "1";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$mukae_hour,$mukae_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$mukae_hour,$mukae_minute);
	
	$error = '<span style="color:blue;">確認に設定しました</span>';
	
}else if( isset($_POST["send_mukae_2"])==true ){
	
	//着予定
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$mukae_hour = $_POST["mukae_hour"];
	$mukae_minute = $_POST["mukae_minute"];
	
	$type = "mukae";
	$state = "2";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$mukae_hour,$mukae_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$mukae_hour,$mukae_minute);
	
	$error = '<span style="color:blue;">着予定に設定しました</span>';
	
}else if( isset($_POST["send_mukae_3"])==true ){
	
	//到着待機
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$mukae_hour = $_POST["mukae_hour"];
	$mukae_minute = $_POST["mukae_minute"];
	
	$type = "mukae";
	$state = "3";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$mukae_hour,$mukae_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$mukae_hour,$mukae_minute);
	
	$error = '<span style="color:blue;">到着待機に設定しました</span>';
	
}else if( isset($_POST["send_mukae_4"])==true ){
	
	//合流
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$mukae_hour = $_POST["mukae_hour"];
	$mukae_minute = $_POST["mukae_minute"];
	
	$type = "mukae";
	$state = "4";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$mukae_hour,$mukae_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$mukae_hour,$mukae_minute);
	
	$error = '<span style="color:blue;">合流に設定しました</span>';
	
}else if( isset($_POST["send_mukae_5"])==true ){
	
	//降車
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$mukae_hour = $_POST["mukae_hour"];
	$mukae_minute = $_POST["mukae_minute"];
	
	$type = "mukae";
	$state = "9";
	
	update_reservation_board_2_state_common($reservation_for_board_id,$mukae_hour,$mukae_minute,$type,$state);
	
	send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$mukae_hour,$mukae_minute);
	
	$error = '<span style="color:blue;">降車に設定しました</span>';
	
}else if( isset($_POST["send_mukae_6"])==true ){
	
	//送信
	
	$reservation_for_board_id = $_POST["reservation_for_board_id"];
	$mukae_hour = $_POST["mukae_hour"];
	$mukae_minute = $_POST["mukae_minute"];
	
	$comment = $_POST["comment"];
	
	$type = "mukae";
	$state = "0";
	
	update_reservation_board_2_comment_common($reservation_for_board_id,$type,$comment);
	
	send_mail_for_driver_page_4_common(
$staff_id,$area,$type,$comment);
	
	$error = '<span style="color:blue;">コメントを送信しました</span>';
	
}

$message = get_top_message_board_message_history_common($staff_id);

$board_data = get_reservation_for_board_data_today_for_driver($staff_id);

$board_data = board_data_divide_for_communication($board_data,$staff_id);

$data = get_today_year_month_day_common();
$year = $data["year"];
$month = $data["month"];
$day = $data["day"];
$attendance_data = get_attendance_staff_new_day_and_staff_id_common($year,$month,$day,$staff_id);
$attendance_staff_new_id = $attendance_data["id"];

if( $attendance_staff_new_id != "" ){
	
	$data = get_attendance_staff_2_by_attendance_staff_new_id($attendance_staff_new_id);
	
	$back_plans_hour = $data["back_plans_hour"];
	$back_plans_minute = $data["back_plans_minute"];
	$back_plans_state = $data["back_plans_state"];
	
	$select_frm_back_plans_state = get_select_frm_back_plans_state($back_plans_state);
	
	$select_frm_back_plans_hour = get_select_frm_back_plans_hour($back_plans_hour,$back_plans_state);
	
	$select_frm_back_plans_minute = get_select_frm_back_plans_minute($back_plans_minute,$back_plans_state);
	
	$params['select_frm_back_plans_state'] = $select_frm_back_plans_state;
	$params['select_frm_back_plans_hour'] = $select_frm_back_plans_hour;
	$params['select_frm_back_plans_minute'] = $select_frm_back_plans_minute;
	
	$arrival_flg = false;
	
	if( $back_plans_state == "9" ){
		
		$arrival_flg = true;
		
	}
	
	$params['arrival_flg'] = $arrival_flg;
	
}

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

$file_name = "communication.php";
$url_communication = get_driver_url($area,$staff_id,$ch,$file_name);

$file_name = "history.php";
$url_history = get_driver_url($area,$staff_id,$ch,$file_name);

$params['url_communication'] = $url_communication;
$params['url_history'] = $url_history;

/*
echo "<pre>";
print_r($sale_data);
echo "</pre>";
exit();
*/

$page_title = "送迎情報";

$params['ch'] = $ch;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;
$params['board_data'] = $board_data;
$params['message'] = $message;
$params['attendance_staff_new_id'] = $attendance_staff_new_id;
$params['error'] = $error;

$smarty->assign( 'params', $params );

$smarty->display( 'sp/communication.tpl' );

?>