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

$switching_result = check_switching_driver_remuneration_common($year,$month,$day,$staff_id);

$month_disp = $month;
$day_disp = add_zero_when_under_ten_common($day);
$week_name = get_week_name_by_time_common($year, $month, $day);

$furikomi_data = get_furikomi_detail_staff($year,$month,$day,$staff_id);

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

$car_distance = $furikomi_data["car_distance"];
$start_hour = add_zero_when_under_ten_common($furikomi_data["start_hour"]);
$start_minute = add_zero_when_under_ten_common($furikomi_data["start_minute"]);
$end_hour = add_zero_when_under_ten_common($furikomi_data["end_hour"]);
$end_minute = add_zero_when_under_ten_common($furikomi_data["end_minute"]);
$start_time = sprintf("%s:%s",$start_hour,$start_minute);
$end_time = sprintf("%s:%s",$end_hour,$end_minute);
$work_time = $furikomi_data["work_time"];
$distance_ave = round($car_distance/$work_time, 1);
$unit_price = $furikomi_data["unit_price"];
$settings_gasoline = $control_value = get_gasoline_value_from_settings_2_common($area,$year,$month,$day);

$remuneration_type = get_remuneration_type_common($staff_id,$year,$month,$day);	//設定報酬データの設定値取得

/*
echo "<pre>";
print_r($sale_data);
echo "</pre>";
exit();
*/
$unit_price = get_unit_price_by_distance_ave_common($distance_ave,$year,$month,$day,$area);
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
$params['distance_ave'] = $distance_ave;
$params['switching_result'] = $switching_result;
$params['start_time'] = $start_time;
$params['end_time'] = $end_time;
$params['unit_price'] = $unit_price;
$params['settings_gasoline'] = $settings_gasoline;
$params['remuneration_type'] = $remuneration_type;


$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/operation_detail.tpl' );
$smarty->display( 'sp/template_beta.tpl' );

?>
