<?php

include("include/common.php");



if( (isset($_GET["id"]) == true) && (isset($_GET["area"]) == true) ){
	
	$staff_id = $_GET["id"];
	$area = $_GET["area"];
	$kekkin = $_GET["kekkin"];
	$ch = $_GET["ch"];
	
	$today_year = intval(date("Y"));
	$today_month = intval(date("m"));
	
}else{
	
	echo "error!";
	exit();
	
}

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;



$params['staff_name'] = $staff_name;
$params['staff_id'] = $staff_id;
$params['area'] = $area;
$params['kekkin'] = $kekkin;
$params['today_year'] = $today_year;
$params['today_month'] = $today_month;
$params['ch'] = $ch;

if($kekkin=="1"){
	
	$params['page_title'] = "シフト削除完了";
	
}else{

	$params['page_title'] = "シフト修正完了";

}

$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/edit_complete.tpl' );
$smarty->display( 'sp/template.tpl' );

?>