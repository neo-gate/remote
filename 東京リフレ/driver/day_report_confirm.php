<?php

include("include/common.php");



$area = $_GET["area"];
$staff_id = $_GET["id"];
$ch = $_GET["ch"];
if( ($area == "") || ($staff_id == "") ){
	echo "error!";
	exit();
}

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

if(isset($_POST["send"])==true){
	
	//更新
	update_day_report(
$attendance_staff_new_id,$car_distance,$highway,$parking,$pay_finish,$comment,
$start_hour,$start_minute,$end_hour,$end_minute);
	
	//メール送信
	send_mail_front_day_report(
$attendance_staff_new_id,$car_distance,$highway,$parking,$pay_finish,$comment,
$start_hour,$start_minute,$end_hour,$end_minute,$staff_id,$area);

	$url = sprintf("day_report_complete.php?area=%s&id=%s&ch=%s",$area,$staff_id,$ch);
	
	header("Location: ".$url);
	exit();

}else if(isset($_POST["back"])==true){
	
	$url = sprintf("day_report.php?back=true&area=%s&id=%s&ch=%s",$area,$staff_id,$ch);

	header("Location: ".$url);
	exit();

}

$start_minute_disp = add_zero_when_under_ten_common($start_minute);
$end_minute_disp = add_zero_when_under_ten_common($end_minute);

$start_time = sprintf("%s:%s",$start_hour,$start_minute_disp);

$end_time = sprintf("%s:%s",$end_hour,$end_minute_disp);



$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

/*
echo "<pre>";
print_r($sale_data);
echo "</pre>";
exit();
*/

$page_title = "業務開始・締め処理(確認)";

$params['ch'] = $ch;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;

$params['start_time'] = $start_time;
$params['end_time'] = $end_time;

$params['car_distance'] = $car_distance;
$params['highway'] = $highway;
$params['parking'] = $parking;
$params['pay_finish'] = $pay_finish;
$params['comment'] = $comment;

$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/day_report_confirm.tpl' );
$smarty->display( 'sp/template.tpl' );

?>