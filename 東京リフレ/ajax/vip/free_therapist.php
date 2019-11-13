<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$day = $_POST["day"];
$time = $_POST["time"];
$year = $_POST["year"];

$month_day = explode("_",$day);

$month=$month_day[0];
$day=$month_day[1];

$shop_area = "tokyo";

//$attendance_data = get_attendance_data_for_vip_page_common($shop_area,$year,$month,$day,$time);
$attendance_data = get_attendance_data_for_vip_page_2_common($shop_area,$year,$month,$day,$time,$_SESSION["customer_type"]);

$attendance_data_num = count($attendance_data);

if($attendance_data_num==0){
	
	echo '<span style="color:red;font-weight:bold;">申し訳ありません。この時間帯に対応可能なセラピストはおりません。</span>';
	echo '<input type="hidden" name="notherapist" value="true" />';
	exit();
	
}else{

for($i=0;$i<$attendance_data_num;$i++){
echo <<<EOT

<input type="radio" name="therapist" value="{$attendance_data[$i]["therapist_id"]}">{$attendance_data[$i]["name_site"]}<br />

EOT;
}
echo '<input type="radio" name="therapist" value="-1" checked>特に指定しない<br />';

exit();

}

?>