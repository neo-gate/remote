<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");

require_once(ROOT_PATH."include/define.php");

$type = $_REQUEST["type"];
$year = $_REQUEST["year"];
$month = $_REQUEST["month"];
$url = $_REQUEST["url"];

if( $type == "kako" ){
	
	$num = 1;
	$tmp = get_kako_month_common($year,$month,$num);
	$year = $tmp["year"];
	$month = $tmp["month"];
	
}else if( $type == "mirai" ){
	
	$num = 1;
	$tmp = get_mirai_month_common($year,$month,$num);
	$year = $tmp["year"];
	$month = $tmp["month"];
	
}else{
	
	return false;
	exit();
	
}

$calendar = get_calendar_course_list_pc_common($year,$month,$url);

echo $calendar;
exit();

?>