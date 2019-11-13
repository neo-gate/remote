<?php

include("include/common.php");

$area = $_GET["area"];
$staff_id = $_GET["id"];
$ch = $_GET["ch"];
if( ($area == "") || ($staff_id == "") ){
	echo "error!";
	exit();
}

$staff_name = get_staff_name_by_staff_id($staff_id);

$month = intval(date('m'));
$day = intval(date('d'));

if( $month < 10 ){

	$month = "0".$month;

}

if( $day < 10 ){

	$day = "0".$month;

}

$limit_num = "30";
$help_page_data = get_message_board_driver_2($limit_num,$area);

/*
echo "<pre>";
print_r($help_page_data);
echo "</pre>";
exit();
*/

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

$params['staff_name'] = $staff_name;
$params['month'] = $month;
$params['day'] = $day;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['ch'] = $ch;
$params['page_title'] = "伝言板";

$params['help_page_data'] = $help_page_data;

$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/message_board.tpl' );
$smarty->display( 'sp/template_beta.tpl' );

?>
