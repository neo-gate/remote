<?php

include("include/common.php");



$area = $_GET["area"];
$staff_id = $_GET["id"];
$ch = $_GET["ch"];
if( ($area == "") || ($staff_id == "") ){
	echo "error!";
	exit();
}

if( (isset($_GET["year"])==true) && (isset($_GET["month"])==true) ){

	$year = $_GET["year"];
	$month = $_GET["month"];

}else{

	$now_hour = intval(date('H'));
		
	if($now_hour <= 6){
	
		//昨日の日付
		$year = intval(date('Y', strtotime('-1 day')));
		$month = intval(date('m', strtotime('-1 day')));
	
	}else{
	
		$year = intval(date('Y'));
		$month = intval(date('m'));
	
	}

}



$type="zenkai";
$week_data = get_furikomi_week_data($type,$year,$month);
$furikomi_price_zenkai = get_furikomi_price_staff_by_week_data_common($week_data,$staff_id);

$type="jikai";
$week_data = get_furikomi_week_data($type,$year,$month);
$furikomi_price_jikai = get_furikomi_price_staff_by_week_data_common($week_data,$staff_id);



$top_disp_data = get_top_disp_data_for_operation_list_staff($year,$month,$staff_id);

$remuneration_all = $top_disp_data["remuneration"];

$sale_data = get_sale_history_data_for_operation_list_staff($year,$month,$staff_id);

$month_select_frm = get_operation_list_month_select_frm($year,$month,$staff_id);

$this_month_flg = get_this_month_flg($year,$month);

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

/*
echo "<pre>";
print_r($sale_data);
echo "</pre>";
exit();
*/

$page_title = "報酬一覧";

$params['ch'] = $ch;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;

$params['remuneration_all'] = $remuneration_all;

$params['sale_data'] = $sale_data;
$params['month_select_frm'] = $month_select_frm;
$params['month'] = $month;

$params['furikomi_price_zenkai'] = $furikomi_price_zenkai;
$params['furikomi_price_jikai'] = $furikomi_price_jikai;

$params['this_month_flg'] = $this_month_flg;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/operation_list_bk.tpl' );
$smarty->display( 'sp/template.tpl' );

?>