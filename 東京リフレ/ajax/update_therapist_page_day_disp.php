<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$year = $_POST["year"];
$month = $_POST["month"];
$day = $_POST["day"];

$html = "";

for($i=0;$i<$day_array_num;$i++){
if( $i < 7 ){
if( $i != 0 ){
$html .= '<span style="padding-left:10px;">|</span>';
}
if( ( $month == $day_array[$i]["month"] ) && ( $day == $day_array[$i]["day"] ) ){
$html .= '<span style="padding-left:10px;">'.$day_array[$i]["month"].'/'.$day_array[$i]["day"].'('.$day_array[$i]["week_name"].')</span>';
}else{
$html .= '<span style="padding-left:10px;">';
$html .= '<span class="therapist_page_day_list" onclick="therapistpage_day_change('.$day_array[$i]["year"].','.$day_array[$i]["month"].','.$day_array[$i]["day"].');">';
$html .= $day_array[$i]["month"].'/'.$day_array[$i]["day"].'('.$day_array[$i]["week_name"].')';
$html .= '</span>';
$html .= '</span>';
}
}
}

echo $html;
exit();

?>