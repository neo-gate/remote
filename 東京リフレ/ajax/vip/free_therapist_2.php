<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$access_type = $_POST["access_type"];
$customer_id = $_POST["customer_id"];
$day = $_POST["day"];
$time = $_POST["time"];
$year = $_POST["year"];

//echo $customer_id;exit();

$month_day = explode("_",$day);

$month=$month_day[0];
$day=$month_day[1];

$shop_area = "tokyo";

$attendance_data = get_attendance_data_for_vip_page_3_common(
$shop_area,$year,$month,$day,$time,$_SESSION["customer_type"],$customer_id);

$radio_frm_therapist = get_radio_frm_therapist_for_vip_reservation_input_common($attendance_data,$therapist_id,$access_type);

echo $radio_frm_therapist;
exit();

?>