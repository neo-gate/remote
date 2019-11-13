<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$therapist_id = $_POST["therapist_id"];

$area = "tokyo";

$therapist_name = get_therapist_name_by_therapist_id($therapist_id, $area);

$disp_data = array();

for($i=0;$i<7;$i++){
	
	$year = $day_array[$i]["year"];
	$month = $day_array[$i]["month"];
	$day = $day_array[$i]["day"];
	$week_name = $day_array[$i]["week_name"];

	//出勤データを取得
	$disp_data[$i]["attendance"] = get_therapist_attendance_data($therapist_id,$year,$month,$day);
	
	$disp_data[$i]["month"] = $month;
	$disp_data[$i]["day"] = $day;
	$disp_data[$i]["week_name"] = $week_name;

}

$html = "";

$html .= '<div style="padding:10px 10px 10px 10px;background-color:#fff;border:solid 1px #000;">';
$html .= '<div style="margin:0px 0px 10px 50px;cursor:pointer;" onclick="yotei_win_close('.$therapist_id.');">';
$html .= '×&nbsp;予定表を閉じる';
$html .= '</div>';
$html .= '<div style="font-weight:bold;">';
$html .= $therapist_name;
$html .= '</div>';
$html .= '<div>';
$html .= '<table>';
for($i=0;$i<7;$i++){
$html .= '<tr>';
$html .= '<td>';
$html .= '<div style="padding:5px;">';
$html .= $disp_data[$i]["month"].'/'.$disp_data[$i]["day"].'('.$disp_data[$i]["week_name"].')';
$html .= '</div>';
$html .= '</td>';
$html .= '<td>';
$html .= '<div style="padding:5px;">';
if($disp_data[$i]["attendance"]==false){
$html .= "お休み";
}else{
$start_time = $disp_data[$i]["attendance"]["start_time"];
$end_time = $disp_data[$i]["attendance"]["end_time"];
$hour_start = $time_array[$start_time]["hour"];
$minute_start = $time_array[$start_time]["minute"];
if($minute_start=="0"){
$minute_start = "0".$minute_start;
}
$hour_end = $time_array[$end_time]["hour"];
$minute_end = $time_array[$end_time]["minute"];
if($minute_end=="0"){
$minute_end = "0".$minute_end;
}
$html .= $hour_start.':'.$minute_start.'　-　'.$hour_end.':'.$minute_end;
}
$html .= '</div>';
$html .= '</td>';
$html .= '</tr>';
}
$html .= '</table>';
$html .= '</div>';
$html .= '</div>';

echo $html;
exit();

?>