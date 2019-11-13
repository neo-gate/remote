<?php

include("include/common.php");



$area = $_GET["area"];
$staff_id = $_GET["id"];
$ch = $_GET["ch"];
if( ($area == "") || ($staff_id == "") ){
	echo "error!";
	exit();
}

if( (isset($_GET["year"])==true) && (isset($_GET["month"])==true) && (isset($_GET["day"])==true) ){

	$year = $_GET["year"];
	$month = $_GET["month"];
	$day = $_GET["day"];

}else{
	
	echo "error!";
	exit();
	
}

$month_disp = $month;
$day_disp = add_zero_when_under_ten_common($day);
$week_name = get_week_name_by_time_common($year, $month, $day);

$furikomi_data = get_furikomi_detail_staff($year,$month,$day,$staff_id);

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

/*
echo "<pre>";
print_r($sale_data);
echo "</pre>";
exit();
*/

$page_title = "報酬詳細";

$params['ch'] = $ch;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;
$params['month_disp'] = $month_disp;
$params['day_disp'] = $day_disp;
$params['week_name'] = $week_name;
$params['furikomi_data'] = $furikomi_data;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/operation_detail_bk.tpl' );
$smarty->display( 'sp/template.tpl' );

?>