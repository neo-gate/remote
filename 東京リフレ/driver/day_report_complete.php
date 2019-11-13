<?php

include("include/common.php");



$area = $_GET["area"];
$staff_id = $_GET["id"];
$ch = $_GET["ch"];
if( ($area == "") || ($staff_id == "") ){
	echo "error!";
	exit();
}

unset($_SESSION["d_r_car_distance"]);
unset($_SESSION["d_r_highway"]);
unset($_SESSION["d_r_parking"]);
unset($_SESSION["d_r_pay_finish"]);
unset($_SESSION["d_r_comment"]);
unset($_SESSION["d_r_start_hour"]);
unset($_SESSION["d_r_start_minute"]);
unset($_SESSION["d_r_end_hour"]);
unset($_SESSION["d_r_end_minute"]);

unset($_SESSION["d_r_attendance_staff_new_id"]);



$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

/*
echo "<pre>";
print_r($sale_data);
echo "</pre>";
exit();
*/

$page_title = "業務開始・締め処理(完了)";

$params['ch'] = $ch;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;

$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/day_report_complete.tpl' );
$smarty->display( 'sp/template.tpl' );

?>