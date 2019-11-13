<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$most_start_time = 23;
$most_end_time = 1;
$under6_flag = false;

$now_hour = intval(date('H'));
$now_day = intval(date('d'));

if($now_hour <= 6){

	//昨日の日付
	$now_year = intval(date('Y', strtotime('-1 day')));
	$now_month = intval(date('m', strtotime('-1 day')));
	$now_day = intval(date('d', strtotime('-1 day')));

	$year = $now_year;
	$month = $now_month;
	$day = $now_day;

	$under6_flag = true;

}else{

	$now_year = intval(date('Y'));
	$now_month = intval(date('m'));
	$now_day = intval(date('d'));

	$year = $now_year;
	$month = $now_month;
	$day = $now_day;

}

if(isset($_GET["year"])==true){
	$year = $_GET["year"];
}

if(isset($_GET["month"])==true){
	$month = $_GET["month"];
}

if(isset($_GET["day"])==true){
	$day = $_GET["day"];
}

$today_flag = false;

if( ($year == $now_year) && ($month == $now_month) && ($day == $now_day) ){
	$today_flag = true;
}

$attendance_data = array();

//出勤情報と対応可否情報の取得
$all_data = get_attendance_data($year,$month,$day,$today_flag,$under6_flag);

$year = $year;
$month = $month;
$day = $day;
$day_array = $day_array;
$week_array = $week_array;

$attendance_data = $all_data["attendance_data"];
$free_therapist_state = $all_data["free_therapist_state"];
$now_day = $now_day;
$today_flag = $today_flag;
$under6_flag = $under6_flag;

$attendance_data_num = count($attendance_data);

?>

<?php

$html = "";

$html .= <<<EOT

<div id="calendar_display">

<div>
<div class="gyou">
<div class="ichiretsu_title">セラピスト</div>
<div class="table_title_first">18:00</div></th>
<div class="table_title">18:30</div>
<div class="table_title">19:00</div>
<div class="table_title">19:30</div>
<div class="table_title">20:00</div>
<div class="table_title">20:30</div>
<div class="table_title">21:00</div>
<div class="table_title">21:30</div>
<div class="table_title">22:00</div>
<div class="table_title">22:30</div>
<div class="table_title">23:00</div>
<div class="table_title">23:30</div>
<div class="table_title">0:00</div>
<div class="table_title">0:30</div>
<div class="table_title">1:00</div>
<div class="table_title">1:30</div>
<div class="table_title">2:00</div>
<div class="table_title">2:30</div>
<div class="table_title">3:00</div>
<div class="table_title">3:30</div>
<div class="table_title">4:00</div>
<div class="table_title">4:30</div>
<div class="table_title">5:00</div>
<br style="clear:both" />

</div>
EOT;

echo $html;


for($i=0;$i<$attendance_data_num;$i++){
echo '<div class="gyou">';
if(($i%2)==1){
echo '<div class="ichiretsu1">';
}else{
echo '<div class="ichiretsu2">';
}

echo $attendance_data[$i]["name_refle"];
echo "</div>";

for($j=1;$j<=23;$j++){

$start_time = $attendance_data[$i]["start_time"];
$end_time = $attendance_data[$i]["end_time"]-1;
$attendance_id = $attendance_data[$i]["attendance_id"];
$therapist_id = $attendance_data[$i]["therapist_id"];
$year = $attendance_data[$i]["year"];
$month = $attendance_data[$i]["month"];
$day = $attendance_data[$i]["day"];
$time = $j;
$reservation_flag = false;
$time_num = $attendance_data[$i]["time_num"];

for($k=0;$k<$time_num;$k++){
$tmp_time = $attendance_data[$i]["time"][$k];

if($tmp_time == $time){
$reservation_flag = true;
}
}
$now_day = intval(date('d'));

if($today_flag==false){

$past_flag = false;

}else if( ($under6_flag==true) && ($j<=12) && ($today_flag==true) ){

$past_flag = true;

}else if($day > $now_day){

$past_flag = false;

}else{

$past_flag = past_time($j);

}
$html = "";
if($reservation_flag==true){

if(($i%2)==1){
if($j==1){
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="batsu_first1">
×
</div>
EOT;
}else{
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="batsu1">
×
</div>
EOT;
}
}else{
if($j==1){
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="batsu_first2">
×
</div>
EOT;
}else{
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="batsu2">
×
</div>
EOT;
}
}



}else if($past_flag==true){


if(($i%2)==1){
if($j==1){

$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="batsu_first1">
×
</div>
EOT;
}else{
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="batsu1">
×
</div>
EOT;
}
}else{
if($j==1){
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="batsu_first2">
×
</div>
EOT;
}else{
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="batsu2">
×
</div>
EOT;
}
}


}else{
if(($i%2)==1){
if($j==1){
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="maru_first1">
<a href="reservation/input.php?therapist_id={$attendance_data[$i]["therapist_id"]}&year={$attendance_data[$i]["year"]}&month={$attendance_data[$i]["month"]}&day={$attendance_data[$i]["day"]}&time={$j}">〇</a>
</div>
EOT;
}else{
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="maru1">
<a href="reservation/input.php?therapist_id={$attendance_data[$i]["therapist_id"]}&year={$attendance_data[$i]["year"]}&month={$attendance_data[$i]["month"]}&day={$attendance_data[$i]["day"]}&time={$j}">〇</a>
</div>
EOT;
}

}else{
if($j==1){
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="maru_first2">
<a href="reservation/input.php?therapist_id={$attendance_data[$i]["therapist_id"]}&year={$attendance_data[$i]["year"]}&month={$attendance_data[$i]["month"]}&day={$attendance_data[$i]["day"]}&time={$j}">〇</a>
</div>
EOT;
}else{
$html .= <<<EOT
<div id="{$attendance_id}_{$j}" class="maru2">
<a href="reservation/input.php?therapist_id={$attendance_data[$i]["therapist_id"]}&year={$attendance_data[$i]["year"]}&month={$attendance_data[$i]["month"]}&day={$attendance_data[$i]["day"]}&time={$j}">〇</a>
</div>
EOT;
}
}
}

if( ($j>=$start_time) && ($j<=$end_time) ){
if($j==1){
echo '<div>';
}else{
echo '<div>';
}
echo $html;
echo '</div>';
}else{

if(($i%2)==1){

if($j==1){
echo '<div class="kuuran_first1">&nbsp;</div>';
}else{
echo '<div class="kuuran1">&nbsp;</div>';
}

}else{

if($j==1){
echo '<div class="kuuran_first2">&nbsp;</div>';
}else{
echo '<div class="kuuran2">&nbsp;</div>';
}
}
}


}
echo '<br style="clear:both" />';
echo "</div>";
}



echo '<div class="gyou">';
if(($i%2)==1){
echo '<div class="ichiretsu1">';
}else{
echo '<div class="ichiretsu2">';
}
echo "セラピスト指定なし";
echo "</div>";
for($z=1;$z<=23;$z++){

if(($i%2)==1){
if($z==1){
echo '<div class="maru_first1">';
}else{
echo '<div class="maru1">';
}
}else{
if($z==1){
echo '<div class="maru_first2">';
}else{
echo '<div class="maru2">';
}
}
if($free_therapist_state[$z]==0){
echo "×";
}else{
echo '<a href="reservation/input.php?therapist_id=-1&year='.$year.'&month='.$month.'&day='.$day.'&time='.$z.'">〇</a>';
}
echo '</div>';

}
echo '<br style="clear:both" />';
echo "</div>";


echo '</div>';

echo '</div>';

?>