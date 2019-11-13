<?php
/* =================================================================================
		Script Name	html_common.php( HTML関係 )
		Update Date	2018/02/22	全面的に整理 by aida
================================================================================= */

//----コース種別選択プルダウン編集
function get_select_course_common($course){

	$course_array = array(
		"0" => "-1",
		"1" => "90分コース(14,000円+交通費)",
		"2" => "120分コース(19,000円+交通費)",
		"3" => "150分コース(23,000円+交通費)",
		"4" => "180分コース(28,000円+交通費)",
		"5" => "210分コース(32,000円+交通費)",
		"6" => "240分コース(37,000円+交通費)"
	);

	$select_course = "";

	if($course==null){
		$select_course .= '<option value="'.$course_array[0].'" selected="selected">選択してください</option>';
	}else{
		$select_course .= '<option value="'.$course_array[0].'">選択してください</option>';
	}

	for($i=1;$i<7;$i++){
		if($course==$course_array[$i]){
			$select_course .= '<option value="'.$course_array[$i].'" selected="selected">'.$course_array[$i].'</option>';
		}else{
			$select_course .= '<option value="'.$course_array[$i].'">'.$course_array[$i].'</option>';
		}
	}

	return $select_course;
	exit();
}

//----
function get_disp_data_driver_back_plans_info_common($hour,$minute,$state){

	$html = "";

	if( $state == "9" ){
		$html = "到着済み";
	}else if( $state == "1" ){
		$html = sprintf("市ヶ谷　%s:%s",$hour,$minute);
	}else if( $state == "2" ){
		$html = sprintf("渋谷　%s:%s",$hour,$minute);
	}else{
		$html = "未設定";
	}

	return $html;
	exit();
}

//----HTML編集
function get_sale_month_select_frm_common($area,$year_hiki,$month_hiki,$type){

	$data = get_most_small_year_and_month_common($area,$type);


	//今日の年と月
	$year_today = intval(date("Y"));
	$month_today = intval(date("m"));

	if( $data["year"] == "" ){
		$most_small_year = $year_today;
	}else{
		$most_small_year = $data["year"];
	}
	if( $data["month"] == "" ){
		$most_small_month = $month_today;
	}else{
		$most_small_month = $data["month"];
	}

	$first_flg = true;

	$data = array();
	$z = 0;

	$now_month_flg = false;

	for($i=$most_small_year; $i<=$year_today; $i++){

		if( $first_flg == true ){

			$start_month = $most_small_month;

			$first_flg = false;

		}else{

			$start_month = 1;

		}

		for($x=$start_month;$x<=12;$x++){

			$year = $i;
			$month = $x;

			//データがあるかどうかチェック(ある：true,ない：false)

			if( $type == "therapist" ){

				$result = check_attendance_new_exist_common($area,$year,$month);

			}else if( $type == "driver" ){

				$result = check_attendance_staff_new_exist_common($area,$year,$month);

			}else if( $type == "honbu" ){

				$result = check_attendance_staff_new_exist_honbu_common($area,$year,$month);

			}else if( $type == "shop" ){

				$result = check_reservation_for_board_exist_common($area,$year,$month);

			}else{

				echo "error!(get_sale_month_select_frm_common)";
				exit();

			}

			if( $result == true ){

				$data[$z]["year"] = $year;
				$data[$z]["month"] = $month;

				$z++;

			}

		}

	}

	$data_num = count($data);

	$id_name = "sale_driver_month_select";

	$html = '<select name="month" id="'.$id_name.'">';
	$html .= '<option value="-1" selected>月選択</option>';

	for($i=($data_num-1);$i>=0;$i--){

		$year = $data[$i]["year"];
		$month = $data[$i]["month"];

		$value_disp = $year."_".$month;
		$word_disp = $year."年".$month."月";

		if( ($year==$year_hiki) && ($month==$month_hiki) ){

			$html .= sprintf('<option value="%s" selected>%s</option>',$value_disp,$word_disp);

		}else{

			$html .= sprintf('<option value="%s">%s</option>',$value_disp,$word_disp);

		}

	}

	$html .= '</select>';

	return $html;
	exit();

}

//----店舗コース選択用プルダウンHTML編集
function get_option_common($type,$value,$shop_name){

	$shop_name = trim($shop_name);

	$shop_id = get_shop_id_by_shop_name_common($shop_name);

	$html = "";

	if( $type == "course" ){

		$data = get_shop_course_by_shop_id_common($shop_id);

		$data_num = count($data);

		for( $i=0; $i<$data_num; $i++ ){

			$tmp = $data[$i];

			if( $tmp == $value ){
				$html .= sprintf('<option value="%s" selected>%s</option>',$tmp,$tmp);
			}else{
				$html .= sprintf('<option value="%s">%s</option>',$tmp,$tmp);
			}
		}
	}

	return $html;
	exit();
}

//----時刻プルダウンHTML取得
function get_time_array_select_option_vip_common($time){
global $time_array;
	//include(COMMON_INC."time_array.php");

	$html = "";

	for($i=1;$i<24;$i++){

		$hour = $time_array[$i]["hour"];
		$minute = $time_array[$i]["minute"];

		if( $minute == "0" ) $minute = "0".$minute;

		if( $i > 12 ){
			$hour_24 = $hour + 24;
			$time_disp = sprintf("%s:%s(%s:%s)",$hour_24,$minute,$hour,$minute);
		}else{
			$time_disp = sprintf("%s:%s",$hour,$minute);
		}

		if( $i == $time ){
			$html .= sprintf("<option value='%s' selected>%s</option>",$i,$time_disp);
		}else{
			$html .= sprintf("<option value='%s'>%s</option>",$i,$time_disp);
		}
	}

	return $html;
	exit();
}

//----年月プルダウンHTML取得
function get_sale_month_select_frm_2_common($area,$year_hiki,$month_hiki){

	$type = "shop";

	$data = get_most_small_year_and_month_common($area,$type);

	if( $data["year"] == "" ){
		$most_small_year = 9999;
	}else{
		$most_small_year = $data["year"];
	}
	if( $data["month"] == "" ){
		$most_small_month = 99;
	}else{
		$most_small_month = $data["month"];
	}

	//今日の年と月
	$year_today = intval(date("Y"));
	$month_today = intval(date("m"));

	$first_flg = true;

	$data = array();
	$z = 0;

	$now_month_flg = false;

	for($i=$most_small_year;$i<=$year_today;$i++){

		if( $first_flg == true ){

			$start_month = $most_small_month;

			$first_flg = false;

		}else{

			$start_month = 1;

		}

		for($x=$start_month;$x<=12;$x++){

			$year = $i;
			$month = $x;

			//データがあるかどうかチェック(ある：true,ない：false)

			$result = check_reservation_for_board_exist_common($area,$year,$month);

			if( $result == true ){

				$data[$z]["year"] = $year;
				$data[$z]["month"] = $month;

				$z++;

			}

		}

	}

	$data_num = count($data);

	$html = '<select name="year_month">';
	$html .= '<option value="-1" selected>月選択</option>';

	for($i=($data_num-1);$i>=0;$i--){

		$year = $data[$i]["year"];
		$month = $data[$i]["month"];

		$value_disp = $year."_".$month;
		$word_disp = $year."年".$month."月";

		if( ($year==$year_hiki) && ($month==$month_hiki) ){

			$html .= sprintf('<option value="%s" selected>%s</option>',$value_disp,$word_disp);

		}else{

			$html .= sprintf('<option value="%s">%s</option>',$value_disp,$word_disp);

		}

	}

	$html .= '</select>';

	return $html;
	exit();
}

function get_select_frm_staff_for_man_driver_instruction_common($data,$staff_id){

	$data_num = count($data);

	$html = "";

	$html .= '<select name="staff_id_select" id="staff_id_select">';
	$html .= '<option value="-1">選択してください</option>';

	for( $i=0; $i<$data_num; $i++ ){

		$id = $data[$i]["id"];
		$name = $data[$i]["name"];

		if( $staff_id == $id ){

			$html .= sprintf('<option value="%s" selected>%s</option>',$id,$name);

		}else{

			$html .= sprintf('<option value="%s">%s</option>',$id,$name);

		}

	}

	$html .= '</select>';

	return $html;
	exit();
}

function get_select_frm_hour_common($value,$select_name){

	$hour = intval(date('H'));

	if( ( $value == "" ) || ( $value == "-1" ) ){

		$value = $hour;

	}

	$max_num = 24;

	$html = "";

	$html .= sprintf('<select name="%s">',$select_name);
	$html .= '<option value="-1">未選択</option>';

	for( $i=0; $i<$max_num; $i++ ){

		if( $value == $i ){

			$html .= sprintf('<option value="%s" selected>%s</option>',$i,$i);

		}else{

			$html .= sprintf('<option value="%s">%s</option>',$i,$i);

		}

	}

	$html .= '</select>';

	return $html;
	exit();

}

function get_select_frm_minute_common($value,$select_name){

	$minute = intval(date('i'));

	if( ( $value == "" ) || ( $value == "-1" ) ){
		$value = $minute;
	}

	$max_num = 60;

	$html = "";

	$html .= sprintf('<select name="%s">',$select_name);
	$html .= '<option value="-1">未選択</option>';

	for( $i=0; $i<$max_num; $i++ ){
		if( $value == $i ){
			$html .= sprintf('<option value="%s" selected>%s</option>',$i,$i);
		}else{
			$html .= sprintf('<option value="%s">%s</option>',$i,$i);
		}
	}

	$html .= '</select>';

	return $html;
	exit();
}

//----時刻選択プルダウン編集
function get_time_select_option_for_sale_driver_one_common($hour,$minute){

	$time_array = get_time_array_10_common();

	$time_array_num = count($time_array);

	$html = "";

	for($i=1;$i<=$time_array_num;$i++){

		$hour_tmp = $time_array[$i]["hour"];
		$minute_tmp = $time_array[$i]["minute"];

		if( $minute_tmp == "0" ){
			$minute_disp = "00";
		}else{
			$minute_disp = $minute_tmp;
		}

		if( ($hour_tmp==$hour) && ($minute_tmp==$minute) ){
			$html .= sprintf('<option value="%s_%s" selected>%s時%s分</option>',$hour_tmp,$minute_tmp,$hour_tmp,$minute_disp);
		}else{
			$html .= sprintf('<option value="%s_%s">%s時%s分</option>',$hour_tmp,$minute_tmp,$hour_tmp,$minute_disp);
		}
	}

	return $html;
	exit();
}

//----時刻選択プルダウン編集
function get_select_frm_work_time_driver_common($select_name,$hour,$minute){

	$html = "";

	$html .= sprintf('<select name="%s">',$select_name);

	$html .= '<option value="-1" selected>未選択</option>';

	$html .= get_time_select_option_for_sale_driver_one_common($hour,$minute);	//時刻選択プルダウン編集 in local

	$html .= '</select>';

	return $html;
	exit();

}

//----メニュープルダウン編集
function get_select_frm_common($select_value,$select_frm_name,$menu){

	$menu_num = count($menu);

	$option = "";

	for( $i=0; $i<$menu_num; $i++ ){

		$value = $menu[$i]["value"];
		$word = $menu[$i]["word"];

		if( $select_value == $value ){

			$option .= sprintf('<option value="%s" selected>%s</option>',$value,$word);

		}else{

			$option .= sprintf('<option value="%s">%s</option>',$value,$word);

		}
	}

$html =<<<EOT
<select name="{$select_frm_name}">
{$option}
</select>

EOT;

	return $html;
	exit();
}

//----受付状態HTML取得
function get_uketsuke_state_html_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area){
global $time_data;

	//ローカルで5秒くらい早くなる(11→5)
	//1～2秒の処理に改善した(2015/10/15)(reservation_newの削除)
	//return "";exit();

	$lp_flg = false;
	if( ( preg_match("/_lp/",$area) ) == true ){

		$area = str_replace("_lp","",$area);

		$lp_flg = true;

	}

	$today_check_result = check_today_common($year,$month,$day);		//本日チェック

	$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);		//セラピスト名取得

	$attendance_id = get_attendance_id_by_time_common($therapist_id,$year,$month,$day);		//指定日の指定セラピストの出勤ID取得

	$attendance_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)

	$start_time = $attendance_data["start_time"];
	$end_time = $attendance_data["end_time"];

	$html = "";

	$html .= '<div class="therapist_page_uketsuke_state">';

	if( $today_check_result == true ){

		$html .= '<div class="info">○本日の受付状況(開始時間) ＜'.$therapist_name.'＞</div>';

	}else{

		$html .= '<div class="info">○'.$month.'月'.$day.'日の受付状況(開始時間) ＜'.$therapist_name.'＞</div>';

	}


	$html .= '<div class="top">';
	for($i=0;$i<12;$i++){

		if( $i == "11" ){

			$html .= '<div class="pa2">'.$time_data[$i]["hour"].'時</div>';

		}else{

			$html .= '<div class="pa">'.$time_data[$i]["hour"].'時</div>';

		}

	}
	$html .= '<br class="clear" />';
	$html .= '</div>';
	$html .= '<div class="bottom">';

	$end_time = $end_time + 1;

	for($i=0;$i<12;$i++){

		$time = $time_data[$i]["time"];

		if( ($start_time>$time) || ($end_time<=$time) ){

			if( $i == "11" ){
				$html .= '<div class="pa2">-</div>';
			}else{
				$html .= '<div class="pa">-</div>';
			}

		}else{

			//$result1 = check_reservation_data_exist_common($attendance_id,$time);
			//$result2 = check_reservation_data_exist_common($attendance_id,($time+1));

			$time_1 = $time;
			$time_2 = $time+1;
			//これに時間がかってる(11→6)
			//1～2秒の処理に改善した(2015/10/15)(reservation_newの削除)
			$result = check_reservation_data_exist_2_common($attendance_id,$time_1,$time_2);

			$result2 = check_past_time_common($year,$month,$day,$time_1);

			if( ($result == true) || ($result2 == true) ){

				if( $lp_flg == true ){

					if( $i == "11" ){
						$html .= '<div class="pa2" style="background:#ffffab;">〇</div>';
					}else{
						$html .= '<div class="pa" style="background:#ffffab;">〇</div>';
					}

				}else{

					if( $i == "11" ){
						$html .= '<div class="pa2">×</div>';
					}else{
						$html .= '<div class="pa">×</div>';
					}

				}

			}else{

				if( $i == "11" ){
					$html .= '<div class="pa2" style="background:#ffffab;">〇</div>';
				}else{
					$html .= '<div class="pa" style="background:#ffffab;">〇</div>';
				}

			}

		}

	}

	$html .= '<br class="clear" />';
	$html .= '</div>';
	$html .= '</div>';

	$refle_www_url = REFLE_WWW_URL;

	if( $access_type == "sp" ){

		if( $area == "tokyo" ){

			$conversion_tel = "'tel:03-5206-5134'";
			$conversion_tag = "'send', 'event', 'smartphone', 'phone-number-tap', 'main'";

		}else if( $area == "yokohama" ){

			$conversion_tel = "'tel:0120-916-796'";

		}else if( $area == "sapporo" ){

			$conversion_tel = "'tel:0120-978-950'";

		}else if( $area == "sendai" ){

			$conversion_tel = "'tel:0120-910-220'";

		}else if( $area == "osaka" ){

			$conversion_tel = "'tel:0120-910-706'";

		}

		$html .= '<div>';
		$html .= '<div style="float:left;padding-left:10px;">';
		$html .= '<a onclick="goog_report_conversion('.$conversion_tel.');ga('.$conversion_tag.');" href='.$conversion_tel.'>';
		$html .= '<img src="'.$refle_www_url.'img/sp/201503/yoyaku_tel.jpg" alt="電話予約" width="130" />';
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<div style="float:left;padding-left:30px;">';
		$html .= '<a href="'.$site_url.'mail/reservation/input.php">';
		$html .= '<img src="'.$refle_www_url.'img/sp/201503/yoyaku_web.jpg" alt="WEB予約" width="130" />';
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<br class="clear" />';
		$html .= '</div>';

	}else{

$html .=<<<EOT
<div class="reservation_3">
<div class="btn">
<a href="javascript:openwin1();">
<img src="{$site_url}img/lp/pc/reservation_btn.png" alt="WEB予約" width="240" />
</a>
</div>
</div>
EOT;

	}

	return $html;
	exit();

}

//----受付状態HTML取得2
function get_uketsuke_state_html_2_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area){
global $time_data;

	$today_check_result = check_today_common($year,$month,$day);	//本日チェック

	$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);

	$attendance_id = get_attendance_id_by_time_common($therapist_id,$year,$month,$day);

	$attendance_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)

	$start_time = $attendance_data["start_time"];
	$end_time = $attendance_data["end_time"];

	$html = "";

	$html .= '<div class="therapist_page_uketsuke_state">';

	if( $today_check_result == true ){

		$html .= '<div class="info">○本日の受付状況(開始時間) ＜'.$therapist_name.'＞</div>';

	}else{

		$html .= '<div class="info">○'.$month.'月'.$day.'日の受付状況(開始時間) ＜'.$therapist_name.'＞</div>';

	}


	$html .= '<div class="top">';
	for($i=0;$i<12;$i++){

		if( $i == "11" ){

			$html .= '<div class="pa2">'.$time_data[$i]["hour"].'時</div>';

		}else{

			$html .= '<div class="pa">'.$time_data[$i]["hour"].'時</div>';

		}

	}
	$html .= '<br class="clear" />';
	$html .= '</div>';
	$html .= '<div class="bottom">';

	$end_time = $end_time + 1;

	for($i=0;$i<12;$i++){

		$time = $time_data[$i]["time"];

		if( ($start_time>$time) || ($end_time<=$time) ){

			if( $i == "11" ){
				$html .= '<div class="pa2">-</div>';
			}else{
				$html .= '<div class="pa">-</div>';
			}

		}else{

			$time_1 = $time;
			$time_2 = $time+1;
			//これに時間がかってる(11→6)
			//1～2秒の処理に改善した(2015/10/15)(reservation_newの削除)
			$result = check_reservation_data_exist_2_common($attendance_id,$time_1,$time_2);

			$result2 = check_past_time_common($year,$month,$day,$time_1);

			if( ($result == true) || ($result2 == true) ){

				if( $i == "11" ){
					$html .= '<div class="pa2">×</div>';
				}else{
					$html .= '<div class="pa">×</div>';
				}

			}else{

				if( $i == "11" ){
					$html .= '<div class="pa2" style="background:#ffffab;">〇</div>';
				}else{
					$html .= '<div class="pa" style="background:#ffffab;">〇</div>';
				}

			}

		}

	}

	$html .= '<br class="clear" />';
	$html .= '</div>';
	$html .= '</div>';

	$refle_www_url = REFLE_WWW_URL;

	if( $access_type == "sp" ){

		if( $area == "tokyo" ){

			$conversion_tel = "'tel:03-5206-5134'";
			$conversion_tag = "'send', 'event', 'smartphone', 'phone-number-tap', 'main'";

		}else if( $area == "yokohama" ){

			$conversion_tel = "'tel:0120-916-796'";

		}else if( $area == "sapporo" ){

			$conversion_tel = "'tel:0120-978-950'";

		}else if( $area == "sendai" ){

			$conversion_tel = "'tel:0120-910-220'";

		}else if( $area == "osaka" ){

			$conversion_tel = "'tel:0120-910-706'";

		}

		$html .= '<div>';
		$html .= '<div style="float:left;padding-left:10px;">';
		$html .= '<a onclick="goog_report_conversion('.$conversion_tel.');ga('.$conversion_tag.');" href='.$conversion_tel.'>';
		$html .= '<img src="'.$refle_www_url.'img/lp4/sp/btn_4.jpg" alt="電話予約" width="135" />';
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<div style="float:left;padding-left:20px;">';
		//$html .= '<a href="'.$site_url.'mail/reservation/input.php">';
$html .=<<<EOT
<a href="index.php?year={$year}&month={$month}&day={$day}&therapist_id={$therapist_id}#mail_frm">
EOT;
		$html .= '<img src="'.$refle_www_url.'img/lp4/sp/btn_5.jpg" alt="ボタン" width="135" />';
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<br class="clear" />';
		$html .= '</div>';

	}else{

$html .=<<<EOT
<div class="reservation_5">
<div class="left_1">
<img src="{$site_url}img/lp4/pc/tel.gif" alt="電話番号" width="300" />
</div>
<div class="left_2">
<a href="index.php?year={$year}&month={$month}&day={$day}&therapist_id={$therapist_id}#mail_frm">
<img src="{$site_url}img/lp4/pc/btn_3.jpg" alt="ボタン" width="260" />
</a>
</div>
<br class="clear" />
</div>
EOT;

	}

	return $html;
	exit();
}

//----受付状態HTML取得3
function get_uketsuke_state_html_3_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area,$attendance_all,$reservation_all){
global $time_data;

	//ローカルで5秒くらい早くなる(11→5)
	//1～2秒の処理に改善した(2015/10/15)(reservation_newの削除)
	//reservation_newデータを引数にして、さらなるスピードアップ

	$lp_flg = false;
	if( ( preg_match("/_lp/",$area) ) == true ){

		$area = str_replace("_lp","",$area);

		$lp_flg = true;

	}

	$today_check_result = check_today_common($year,$month,$day);	//本日チェック

	$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);

	$attendance_id = get_attendance_id_from_attendance_all_by_time_common($therapist_id,$year,$month,$day,$attendance_all);

	//echo $attendance_id;exit();

	$attendance_data = get_attendance_data_one_from_attendance_all_by_attendance_id_common($attendance_id,$attendance_all);

	/*
	echo "<pre>";
	print_r($attendance_data);
	echo "</pre>";
	exit();
	*/

	$start_time = $attendance_data["start_time"];
	$end_time = $attendance_data["end_time"];

	$html = "";

	$html .= '<div class="therapist_page_uketsuke_state">';

	if( $today_check_result == true ){

		$html .= '<div class="info">○本日の受付状況(開始時間) ＜'.$therapist_name.'＞</div>';

	}else{

		$html .= '<div class="info">○'.$month.'月'.$day.'日の受付状況(開始時間) ＜'.$therapist_name.'＞</div>';

	}


	$html .= '<div class="top">';
	for($i=0;$i<12;$i++){

		if( $i == "11" ){

			$html .= '<div class="pa2">'.$time_data[$i]["hour"].'時</div>';

		}else{

			$html .= '<div class="pa">'.$time_data[$i]["hour"].'時</div>';

		}

	}
	$html .= '<br class="clear" />';
	$html .= '</div>';
	$html .= '<div class="bottom">';

	$end_time = $end_time + 1;

	for($i=0;$i<12;$i++){

		$time = $time_data[$i]["time"];

		if( ($start_time>$time) || ($end_time<=$time) ){

			if( $i == "11" ){
				$html .= '<div class="pa2">-</div>';
			}else{
				$html .= '<div class="pa">-</div>';
			}

		}else{

			//$result1 = check_reservation_data_exist_common($attendance_id,$time);
			//$result2 = check_reservation_data_exist_common($attendance_id,($time+1));

			$time_1 = $time;
			$time_2 = $time+1;
			//これに時間がかってる(11→6)
			//1～2秒の処理に改善した(2015/10/15)(reservation_newの削除)
			//$result = check_reservation_data_exist_2_common($attendance_id,$time_1,$time_2);
			//さらにスピードアップ
			$result = check_reservation_data_exist_by_reservation_all_common($attendance_id,$time_1,$time_2,$reservation_all);

			$result2 = check_past_time_common($year,$month,$day,$time_1);

			if( ($result == true) || ($result2 == true) ){

				if( $lp_flg == true ){

					if( $i == "11" ){
						$html .= '<div class="pa2" style="background:#ffffab;">〇</div>';
					}else{
						$html .= '<div class="pa" style="background:#ffffab;">〇</div>';
					}

				}else{

					if( $i == "11" ){
						$html .= '<div class="pa2">×</div>';
					}else{
						$html .= '<div class="pa">×</div>';
					}

				}

			}else{

				if( $i == "11" ){
					$html .= '<div class="pa2" style="background:#ffffab;">〇</div>';
				}else{
					$html .= '<div class="pa" style="background:#ffffab;">〇</div>';
				}

			}

		}

	}

	$html .= '<br class="clear" />';
	$html .= '</div>';
	$html .= '</div>';

	$refle_www_url = REFLE_WWW_URL;

	if( $access_type == "sp" ){

		if( $area == "tokyo" ){

			$conversion_tel = "'tel:03-5206-5134'";
			$conversion_tag = "'send', 'event', 'smartphone', 'phone-number-tap', 'main'";

		}else if( $area == "yokohama" ){

			$conversion_tel = "'tel:0120-916-796'";

		}else if( $area == "sapporo" ){

			$conversion_tel = "'tel:0120-978-950'";

		}else if( $area == "sendai" ){

			$conversion_tel = "'tel:0120-910-220'";

		}else if( $area == "osaka" ){

			$conversion_tel = "'tel:0120-910-706'";

		}

		$html .= '<div>';
		$html .= '<div style="float:left;padding-left:10px;">';
		$html .= '<a onclick="goog_report_conversion('.$conversion_tel.');ga('.$conversion_tag.');" href='.$conversion_tel.'>';
		$html .= '<img src="'.$refle_www_url.'img/sp/201503/yoyaku_tel.jpg" alt="電話予約" width="130" />';
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<div style="float:left;padding-left:30px;">';
		$html .= '<a href="'.$site_url.'mail/reservation/input.php">';
		$html .= '<img src="'.$refle_www_url.'img/sp/201503/yoyaku_web.jpg" alt="WEB予約" width="130" />';
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<br class="clear" />';
		$html .= '</div>';

	}else{

		$html .=<<<EOT
<div class="reservation_3">
<div class="btn">
<a href="javascript:openwin1();">
<img src="{$site_url}img/lp/pc/reservation_btn.png" alt="WEB予約" width="240" />
</a>
</div>
</div>
EOT;

	}

	return $html;
	exit();
}

//----受付状態HTML取得4
function get_uketsuke_state_html_4_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area,$attendance_all,$reservation_all){

	//ローカルで5秒くらい早くなる(11→5)
	//1～2秒の処理に改善した(2015/10/15)(reservation_newの削除)
	//reservation_newデータを引数にして、さらなるスピードアップ

	$lp_flg = false;
	if( ( preg_match("/_lp/",$area) ) == true ){

		$area = str_replace("_lp","",$area);

		$lp_flg = true;

	}

	$today_check_result = check_today_common($year,$month,$day);	//本日チェック

	$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);

	$time_data = array(
		"0" => array("hour" => 12,"time" => "-11"),
		"1" => array("hour" => 13,"time" => "-9"),
		"2" => array("hour" => 14,"time" => "-7"),
		"3" => array("hour" => 15,"time" => "-5"),
		"4" => array("hour" => 16,"time" => "-3"),
		"5" => array("hour" => 17,"time" => "-1")
	);

	$time_data_2 = array(
		"0" => array("hour" => 18,"time" => "1"),
		"1" => array("hour" => 19,"time" => "3"),
		"2" => array("hour" => 20,"time" =>"5"),
		"3" => array("hour" => 21,"time" => "7"),
		"4" => array("hour" => 22,"time" => "9"),
		"5" => array("hour" => 23,"time" => "11"),
		"6" => array("hour" => 24,"time" => "13"),
		"7" => array("hour" => 1,"time" => "15"),
		"8" => array("hour" => 2,"time" => "17"),
		"9" => array("hour" => 3,"time" => "19"),
		"10" => array("hour" => 4,"time" => "21"),
		"11" => array("hour" => 5,"time" => "23")
	);

	$time_data_num = count($time_data);
	$time_data_2_num = count($time_data_2);

	$attendance_id = get_attendance_id_from_attendance_all_by_time_common($therapist_id,$year,$month,$day,$attendance_all);

	//echo $attendance_id;exit();

	$attendance_data = get_attendance_data_one_from_attendance_all_by_attendance_id_common($attendance_id,$attendance_all);

	/*
	echo "<pre>";
	print_r($attendance_data);
	echo "</pre>";
	exit();
	*/

	$start_time = $attendance_data["start_time"];
	$end_time = $attendance_data["end_time"];

	$html = "";

	$html .= '<div class="therapist_page_uketsuke_state">';

	if( $today_check_result == true ){

		$html .= '<div class="info">○本日の受付状況(開始時間) ＜'.$therapist_name.'＞</div>';

	}else{

		$html .= '<div class="info">○'.$month.'月'.$day.'日の受付状況(開始時間) ＜'.$therapist_name.'＞</div>';

	}

	$last_num = $time_data_num - 1;



	$html .= '<div class="top">';
	for($i=0;$i<$time_data_2_num;$i++){

		if( $i == "11" ){

			$html .= '<div class="pa2">'.$time_data_2[$i]["hour"].'時</div>';

		}else{

			$html .= '<div class="pa">'.$time_data_2[$i]["hour"].'時</div>';

		}

	}

	$html .= '<br class="clear" />';
	$html .= '</div>';
	$html .= '<div class="bottom">';

	for($i=0;$i<$time_data_2_num;$i++){

		$time = $time_data_2[$i]["time"];

		if( ($start_time>$time) || ($end_time<=$time) ){

			if( $i == "11" ){
				$html .= '<div class="pa2">-</div>';
			}else{
				$html .= '<div class="pa">-</div>';
			}

		}else{

			//$result1 = check_reservation_data_exist_common($attendance_id,$time);
			//$result2 = check_reservation_data_exist_common($attendance_id,($time+1));

			$time_1 = $time;
			$time_2 = $time+1;
			//これに時間がかってる(11→6)
			//1～2秒の処理に改善した(2015/10/15)(reservation_newの削除)
			//$result = check_reservation_data_exist_2_common($attendance_id,$time_1,$time_2);
			//さらにスピードアップ
			$result = check_reservation_data_exist_by_reservation_all_common($attendance_id,$time_1,$time_2,$reservation_all);

			//$result2 = check_past_time_common($year,$month,$day,$time_1);
			$result2 = check_past_time_2_common($year,$month,$day,$time_1);

			if( ($result == true) || ($result2 == true) ){

				if( $lp_flg == true ){

					if( $i == "11" ){
						$html .= '<div class="pa2" style="background:#ffffab;">〇</div>';
					}else{
						$html .= '<div class="pa" style="background:#ffffab;">〇</div>';
					}

				}else{

					if( $i == "11" ){
						$html .= '<div class="pa2">×</div>';
					}else{
						$html .= '<div class="pa">×</div>';
					}

				}

			}else{

				if( $i == "11" ){
					$html .= '<div class="pa2" style="background:#ffffab;">〇</div>';
				}else{
					$html .= '<div class="pa" style="background:#ffffab;">〇</div>';
				}

			}

		}

	}

	$html .= '<br class="clear" />';
	$html .= '</div>';

	$html .= '</div>';

	$refle_www_url = REFLE_WWW_URL;

	if( $access_type == "sp" ){

		if( $area == "tokyo" ){

			$conversion_tel = "'tel:03-5206-5134'";
			$conversion_tag = "'send', 'event', 'smartphone', 'phone-number-tap', 'main'";

		}else if( $area == "yokohama" ){

			$conversion_tel = "'tel:0120-916-796'";

		}else if( $area == "sapporo" ){

			$conversion_tel = "'tel:0120-978-950'";

		}else if( $area == "sendai" ){

			$conversion_tel = "'tel:0120-910-220'";

		}else if( $area == "osaka" ){

			$conversion_tel = "'tel:0120-910-706'";

		}

		$html .= '<div>';
		$html .= '<div style="float:left;padding-left:10px;">';
		$html .= '<a onclick="goog_report_conversion('.$conversion_tel.');ga('.$conversion_tag.');" href='.$conversion_tel.'>';
		$html .= '<img src="'.$refle_www_url.'img/sp/201503/yoyaku_tel.jpg" alt="電話予約" width="130" />';
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<div style="float:left;padding-left:30px;">';
		$html .= '<a href="'.$site_url.'mail/reservation/input.php">';
		$html .= '<img src="'.$refle_www_url.'img/sp/201503/yoyaku_web.jpg" alt="WEB予約" width="130" />';
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<br class="clear" />';
		$html .= '</div>';

	}else{

		$html .=<<<EOT
<div class="reservation_3">
<div class="btn">
<a href="javascript:openwin1();">
<img src="{$site_url}img/lp/pc/reservation_btn.png" alt="WEB予約" width="240" />
</a>
</div>
</div>
EOT;

	}

	return $html;
	exit();
}

function get_therapist_select_frm_for_reservation_mail_frm_common($area,$therapist_id,$year,$month,$day){

	$aroma_flg = false;

	if($area == "tokyo-aroma"){

		$area = "tokyo";
		$aroma_flg = true;

	}else if($area == "osaka-aroma"){

		$area = "osaka";
		$aroma_flg = true;

	}

	$therapisit_data = get_today_attendance_therapisit_data_common($area,$year,$month,$day);

	$therapisit_data_num = count($therapisit_data);

	$html = "";

	$html .= '<select name="therapist_id">';

	$html .= '<option value="-1">指定セラピストなし</option>';

	for( $i=0; $i<$therapisit_data_num; $i++ ){

		$option_value = $therapisit_data[$i]["id"];

		if( $aroma_flg == true ){

			$option_text = $therapisit_data[$i]["name_aroma"];

		}else{

			$option_text = $therapisit_data[$i]["name_site"];

		}

		if( $option_value == $therapist_id ){

			$html .= sprintf('<option value="%s" selected>%s</option>',$option_value,$option_text);

		}else{

			$html .= sprintf('<option value="%s">%s</option>',$option_value,$option_text);

		}

	}

	$html .= '</select>';

	return $html;
	exit();

}

function get_day_select_frm_for_reservation_mail_frm_common($reservation_day){

	$data = get_today_year_month_day_common();		//本日の年月日取

	$year_today = $data["year"];
	$month_today = $data["month"];
	$day_today = $data["day"];

	$html = "";

	$html .= '<select name="reservation_day" id="reservation_day_rev_mail">';

	for( $i=0; $i<30; $i++ ){

		$data = get_mirai_day_common($year_today,$month_today,$day_today,$i);	//指定した日にち分、未来の日付取得

		$year_tmp = $data["year"];
		$month_tmp = $data["month"];
		$day_tmp = $data["day"];

		$week_name = get_week_name_by_time_common($year_tmp, $month_tmp, $day_tmp);

		$option_value = sprintf('%s_%s_%s',$year_tmp,$month_tmp,$day_tmp);

		$option_text = sprintf('%s年　%s月%s日(%s)',$year_tmp,$month_tmp,$day_tmp,$week_name);

		if( $option_value == $reservation_day ){

			$html .= sprintf('<option value="%s" selected>%s</option>',$option_value,$option_text);

		}else{

			$html .= sprintf('<option value="%s">%s</option>',$option_value,$option_text);

		}

	}

	$html .= '</select>';

	return $html;
	exit();

}

function get_day_option_for_therapist_recruit_common($mensetsu_day){

	$data = array();

	$year = intval(date("Y"));
	$month = intval(date("m"));
	$day = intval(date("d"));

	for($i=0;$i<11;$i++){

		$day_tmp = $day+$i;

		$week = intval(date("w", mktime(0, 0, 0, $month, $day_tmp, $year)));

		$data[$i]["year"] = intval(date("Y", mktime(0, 0, 0, $month, $day_tmp, $year)));
		$data[$i]["month"] = intval(date("m", mktime(0, 0, 0, $month, $day_tmp, $year)));
		$data[$i]["day"] = intval(date("d", mktime(0, 0, 0, $month, $day_tmp, $year)));
		$data[$i]["week"] = get_week_name_common($week);

	}

	$html = '<option value="未選択">未選択</option>';

	for($i=0;$i<11;$i++){

		$month = $data[$i]["month"];
		$day = $data[$i]["day"];
		$week = $data[$i]["week"];

		$disp = sprintf("%s月%s日(%s)",$month,$day,$week);

		if( $disp == $mensetsu_day ){
			$html .= sprintf("<option value='%s' selected>%s</option>",$disp,$disp);
		}else{
			$html .= sprintf("<option value='%s'>%s</option>",$disp,$disp);
		}
	}

	return $html;
	exit();
}

function get_time_option_for_therapist_recruit_common($mensetsu_time){

	$data = array(
			"0"=>"15時",
			"1"=>"16時",
			"2"=>"17時",
			"3"=>"18時",
			"4"=>"19時",
			"5"=>"20時",
			"6"=>"上記以外を希望"
	);

	$html = '<option value="未選択">未選択</option>';

	for($i=0;$i<7;$i++){

		$value = $data[$i];

		if( $value == $mensetsu_time ){

			$html .= sprintf("<option value='%s' selected>%s</option>",$value,$value);

		}else{

			$html .= sprintf("<option value='%s'>%s</option>",$value,$value);

		}

	}

	return $html;
	exit();

}

function get_experience_option_for_therapist_recruit_common($experience){

	$data = array(
			"0"=>"1年未満",
			"1"=>"3年未満",
			"2"=>"3年以上",
			"3"=>"未経験"
	);

	$html = '<option value="未選択">未選択</option>';

	for($i=0;$i<4;$i++){

		$value = $data[$i];

		if( $value == $experience ){

			$html .= sprintf("<option value='%s' selected>%s</option>",$value,$value);

		}else{

			$html .= sprintf("<option value='%s'>%s</option>",$value,$value);

		}

	}

	return $html;
	exit();

}

function get_age_option_for_therapist_recruit_common($age){

	$data = array(
			"0"=>"20歳～24歳",
			"1"=>"25歳～29歳",
			"2"=>"30歳～34歳",
			"3"=>"35歳～39歳",
			"4"=>"40歳以上"
	);

	$html = '<option value="未選択">未選択</option>';

	for($i=0;$i<5;$i++){

		$value = $data[$i];

		if( $value == $age ){

			$html .= sprintf("<option value='%s' selected>%s</option>",$value,$value);

		}else{

			$html .= sprintf("<option value='%s'>%s</option>",$value,$value);

		}

	}

	return $html;
	exit();

}

//----スキルcheckboxHTML編集
function get_skill_checkbox_shift_hp_common($skill_data,$skill_data_num,$skill,$checkbox_name){

	$html = "";

	for($i=1;$i<=$skill_data_num;$i++){

		$result = check_skill_exist_common($i,$skill);	//スキルチェック

		if( $result == true ){
			$html .= '<div class="one">';
			$html .= '<input type="checkbox" name="'.$checkbox_name.'" value="'.$i.'" checked>'.$skill_data[$i];
			$html .= '</div>';
		}else{
			$html .= '<div class="one">';
			$html .= '<input type="checkbox" name="'.$checkbox_name.'" value="'.$i.'">'.$skill_data[$i];
			$html .= '</div>';
		}
	}

	return $html;
	exit();
}

//----スキルcheckboxHTML編集
function get_skill_checkbox_therapist_page_edit_common($skill_data,$skill_data_num,$skill,$checkbox_name){

	$html = "";

	for($i=1;$i<=$skill_data_num;$i++){

		$result = check_skill_exist($i,$skill);		//??? 存在しない？ by aida

		if( $result == true ){

			$html .= '<input type="checkbox" name="'.$checkbox_name.'" value="'.$i.'" checked>'.$skill_data[$i];

		}else{

			$html .= '<input type="checkbox" name="'.$checkbox_name.'" value="'.$i.'">'.$skill_data[$i];

		}

	}

	return $html;
	exit();
}

//----経験店舗checkboxHTML編集 2018/11/07 追加 村瀬
function get_exp_checkbox_therapist_page_edit_common($exp_data,$exp_data_num,$exp,$checkbox_name){

	$html = "";

	for($i=1;$i<=$exp_data_num;$i++){

		$result = check_exp_exist($i,$exp);		//??? 存在しない？ by aida

		if( $result == true ){

			$html .= '<input type="checkbox" name="'.$checkbox_name.'" value="'.$i.'" checked>'.$exp_data[$i];

		}else{

			$html .= '<input type="checkbox" name="'.$checkbox_name.'" value="'.$i.'">'.$exp_data[$i];

		}

	}

	return $html;
	exit();
}

//----外国語checkboxHTML編集 2018/11/07 追加 村瀬
function get_lng_checkbox_therapist_page_edit_common($lng_list,$lng_list_num,$lng,$checkbox_name){

	$html = "";

	for($i=1;$i<=$lng_list_num;$i++){

		$result = check_lng_exist($i,$lng);		//??? 存在しない？ by aida

		if( $result == true ){

			$html .= '<input type="checkbox" name="'.$checkbox_name.'" value="'.$i.'" checked>'.$lng_list[$i];

		}else{

			$html .= '<input type="checkbox" name="'.$checkbox_name.'" value="'.$i.'">'.$lng_list[$i];

		}

	}

	return $html;
	exit();
}

function get_therapist_page_attendance_update_html_common($area,$year,$month,$day,$access_type){

	$time1 = microtime(true);

	$attendance_all = get_attendance_data_by_day_and_area_common($year,$month,$day,$area);

	$reservation_all = get_reservation_new_by_day_and_area_common($year,$month,$day,$area);

	include(COMMON_INC."skill_data.php");

	//出勤セラピストは「1」、出勤しないセラピストは「2」
	$type = 1;
	//$therapist = get_therapist_page_data_attendance_2_common($area,$year,$month,$day,$type,REFLE_WWW_URL,$access_type);
	$therapist = get_therapist_page_data_attendance_3_common($area,$year,$month,$day,$type,REFLE_WWW_URL,$access_type,$attendance_all,$reservation_all);

	$therapist_num = count($therapist);

	$url_root = REFLE_WWW_URL;

	$html = "";

	$html .= '<div>';
	for($i=0;$i<$therapist_num;$i++){
		$html .= '<div class="title_therapist_info_content">';
		$html .= '<div class="top_area">';
		$html .= '<div class="name_img">';
		$html .= '<p class="therapist_name">';
		$html .= $therapist[$i]["therapist_name"].'（'.$therapist[$i]["age"].'）';
		$html .= '</p>';
		$html .= '<p class="img">';
		$html .= '<img src="'.S3_URL.$therapist[$i]["img_url"].'" alt="セラピスト'.$therapist[$i]["therapist_name"].'" width="100" />';
		$html .= '</p>';
		$html .= '<p class="info">';
		$html .= $therapist[$i]["hometown"].'出身｜セラピスト歴'.$therapist[$i]["history"];
		$html .= '</p>';
		$html .= '<p style="padding:5px 0px 0px 60px;">';
		$html .= '<input type="button" value="出勤予定表" style="padding:10px;" onclick="open_attendance_schedule('.$therapist[$i]["therapist_id"].');" />';
		$html .= '</p>';
		$html .= '</div>';



		$html .= '<div class="tokui">';

		if( $therapist[$i]["skill_2_exist_flg"] == true ){
			$html .= '<div class="one" style="margin-bottom:15px;">';
			$html .= '<div class="title_1">○一押しメニュー</div>';
			$html .= '<div class="content">';
			$skill_data = $therapist[$i]["skill_2_data"];
			$skill_data_num = count($skill_data);
			for( $x=0; $x < $skill_data_num; $x++ ){
				$value = $skill_data[$x];
				$html .= '<div class="left"><img src="'.REFLE_WWW_URL.'img/skill/'.$value.'.png" alt="'.$skill_arr[$value].'" width="105" /></div>';
			}
			$html .= '<br class="clear" />';
			$html .= '</div>';
			$html .= '</div>';
		}

		$html .= '<div class="one">';
		$html .= '<div class="title_2">○施術可能メニュー</div>';
		$html .= '<div class="content">';
		$skill_data = $therapist[$i]["skill_data"];
		$skill_data_num = count($skill_data);
		for( $x=0; $x < $skill_data_num; $x++ ){
			$value = $skill_data[$x];
			$html .= '<div class="left"><img src="'.REFLE_WWW_URL.'img/skill/'.$value.'.png" alt="'.$skill_arr[$value].'" width="105" /></div>';
		}
		$html .= '<br class="clear" />';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '</div>';



		$html .= '<br class="clear" />';
		$html .= '</div>';

		$html .=<<<EOT
<div class="separator">
<img src="{$url_root}img/lp/pc/separator.jpg" width="600" alt="セパレーター" />
</div>
EOT;

		$html .= '<p class="exp">';
		$html .= nl2br($therapist[$i]["pr_content"]);
		if( $therapist[$i]["shikaku"] != "" ){
			$html .= '<br />';
			$html .= '【保有資格】<br />';
			$html .= $therapist[$i]["shikaku"].'<br />';
		}
		$html .= '</p>';

$html .=<<<EOT
<div class="separator">
<img src="{$url_root}img/lp/pc/separator.jpg" width="600" alt="セパレーター" />
</div>
EOT;

		$html .= $therapist[$i]["uketsuke_state_html"];

		$html .= '</div>';
	}

	//出勤セラピストは「1」、出勤しないセラピストは「2」
	$type = 2;
	//$therapist = get_therapist_page_data_attendance_2_common($area,$year,$month,$day,$type,REFLE_WWW_URL,$access_type);
	$therapist = get_therapist_page_data_attendance_3_common($area,$year,$month,$day,$type,REFLE_WWW_URL,$access_type,$attendance_all,$reservation_all);
	$therapist_num = count($therapist);

	$html .= '</div>';

	$html .= '<div style="padding:20px 0px 0px 0px;">';
	$html .= '<h2>セラピスト一覧</h2>';
	$html .= '<div id="therapist_list">';

	for($i=0;$i<$therapist_num;$i++){
		$html .= '<div class="title_therapist_info_content">';
		$html .= '<div class="top_area">';
		$html .= '<div class="name_img">';
		$html .= '<p class="therapist_name">';
		$html .= $therapist[$i]["therapist_name"].'（'.$therapist[$i]["age"].'）';
		$html .= '</p>';
		$html .= '<p class="img">';
		$html .= '<img src="'.S3_URL.$therapist[$i]["img_url"].'" alt="セラピスト'.$therapist[$i]["therapist_name"].'" width="100" />';
		$html .= '</p>';
		$html .= '<p class="info">';
		$html .= $therapist[$i]["hometown"].'出身｜セラピスト歴'.$therapist[$i]["history"];
		$html .= '</p>';
		$html .= '<p style="padding:5px 0px 0px 60px;">';
		$html .= '<input type="button" value="出勤予定表" style="padding:10px;" onclick="open_attendance_schedule('.$therapist[$i]["therapist_id"].');" />';
		$html .= '</p>';
		$html .= '</div>';



		$html .= '<div class="tokui">';

		if( $therapist[$i]["skill_2_exist_flg"] == true ){
			$html .= '<div class="one" style="margin-bottom:15px;">';
			$html .= '<div class="title_1">○一押しメニュー</div>';
			$html .= '<div class="content">';
			$skill_data = $therapist[$i]["skill_2_data"];
			$skill_data_num = count($skill_data);
			for( $x=0; $x < $skill_data_num; $x++ ){
				$value = $skill_data[$x];
				$html .= '<div class="left"><img src="'.REFLE_WWW_URL.'img/skill/'.$value.'.png" alt="'.$skill_arr[$value].'" width="105" /></div>';
			}
			$html .= '<br class="clear" />';
			$html .= '</div>';
			$html .= '</div>';
		}

		$html .= '<div class="one">';
		$html .= '<div class="title_2">○施術可能メニュー</div>';
		$html .= '<div class="content">';
		$skill_data = $therapist[$i]["skill_data"];
		$skill_data_num = count($skill_data);
		for( $x=0; $x < $skill_data_num; $x++ ){
			$value = $skill_data[$x];
			$html .= '<div class="left"><img src="'.REFLE_WWW_URL.'img/skill/'.$value.'.png" alt="'.$skill_arr[$value].'" width="105" /></div>';
		}
		$html .= '<br class="clear" />';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '</div>';



		$html .= '<br class="clear" />';
		$html .= '</div>';

$html .=<<<EOT
<div class="separator">
<img src="{$url_root}img/lp/pc/separator.jpg" width="600" alt="セパレーター" />
</div>
EOT;

		$html .= '<p class="exp2">';
		$html .= nl2br($therapist[$i]["pr_content"]);
		if( $therapist[$i]["shikaku"] != "" ){
			$html .= '<br />';
			$html .= '【保有資格】<br />';
			$html .= $therapist[$i]["shikaku"].'<br />';
		}
		$html .= '</p>';

		$html .= '</div>';
	}

	$html .= '</div>';
	$html .= '</div>';

	$action_time = get_microtime_sa_common($time1);		//マイクロタイム差計算
	//$html .= '<div>'.$action_time.'</div>';

	return $html;
	exit();

}

function get_therapist_page_attendance_update_html_sp_common($area,$year,$month,$day,$access_type){

	$time1 = microtime(true);

	$attendance_all = get_attendance_data_by_day_and_area_common($year,$month,$day,$area);

	$reservation_all = get_reservation_new_by_day_and_area_common($year,$month,$day,$area);

	//出勤セラピストは「1」、出勤しないセラピストは「2」
	$type = 1;
	//$therapist = get_therapist_page_data_attendance_2_common($area,$year,$month,$day,$type,WWW_URL,$access_type);
	$therapist = get_therapist_page_data_attendance_3_common($area,$year,$month,$day,$type,WWW_URL,$access_type,$attendance_all,$reservation_all);
	$therapist_num = count($therapist);

	$html = "";

	$html .= '<div>';
	for($i=0;$i<$therapist_num;$i++){
		$html .= '<div class="title_therapist_info_content">';
		$html .= '<div class="top_area">';
		$html .= '<div class="name_img">';
		$html .= '<p class="therapist_name">';
		$html .= $therapist[$i]["therapist_name"].'（'.$therapist[$i]["age"].'）';
		$html .= '</p>';
		$html .= '<p class="img">';
		$html .= '<img src="'.S3_URL.$therapist[$i]["img_url"].'" alt="セラピスト'.$therapist[$i]["therapist_name"].'" width="70" />';
		$html .= '</p>';
		$html .= '<p class="info">';
		$html .= $therapist[$i]["hometown"].'出身<br />';
		$html .= 'セラピスト歴'.$therapist[$i]["history"];
		$html .= '</p>';
		$html .= '<div class="attendance_btn_block">';
		$html .= '<div style="float:left;">';
		$html .= '<input type="button" value="出勤予定表" style="padding:5px;" onclick="open_attendance_schedule_sp('.$therapist[$i]["therapist_id"].');" />';
		$html .= '</div>';
		$html .= '<div class="therapist_attendance_yotei_disp" id="yotei_disp_'.$therapist[$i]["therapist_id"].'">';
		$html .= 'xxxxxxxxxxxxxxxx';
		$html .= '</div>';
		$html .= '<br class="clear" />';
		$html .= '</div>';
		$html .= '</div>';



		$html .= '<div class="tokui">';
		if( $therapist[$i]["skill_2_exist_flg"] == true ){
			$html .= '<div class="one" style="margin-bottom:15px;">';
			$html .= '<div class="title_1">';
			$html .= '○一押しメニュー';
			$html .= '</div>';
			$html .= '<div class="content_sp">';
			$skill_data = $therapist[$i]["skill_2_data"];
			$skill_data_num = count($skill_data);
			for( $x=0; $x < $skill_data_num; $x++ ){
				$value = $skill_data[$x];
				$html .= '<div class="left"><img src="'.REFLE_WWW_URL.'img/skill/'.$value.'.png" alt="'.$skill_arr[$value].'" width="80" /></div>';
			}
			$html .= '<br class="clear" />';
			$html .= '</div>';
			$html .= '</div>';
		}
		$html .= '<div class="one">';
		$html .= '<div class="title_2">';
		$html .= '○施術可能メニュー';
		$html .= '</div>';
		$html .= '<div class="content_sp">';
		$skill_data = $therapist[$i]["skill_data"];
		$skill_data_num = count($skill_data);
		for( $x=0; $x < $skill_data_num; $x++ ){
			$value = $skill_data[$x];
			$html .= '<div class="left"><img src="'.REFLE_WWW_URL.'img/skill/'.$value.'.png" alt="'.$skill_arr[$value].'" width="80" /></div>';
		}
		$html .= '<br class="clear" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';


		$html .= '<br class="clear" />';
		$html .= '</div>';

		$html .= '<div><img src="'.REFLE_WWW_URL.'img/sp/201503/kugiri.jpg" alt="区切り" width="310" /></div>';

		$html .= '<p class="exp">';
		$html .= nl2br($therapist[$i]["pr_content"]);
		if( $therapist[$i]["shikaku"] != "" ){
			$html .= '<br />';
			$html .= '【保有資格】<br />';
			$html .= $therapist[$i]["shikaku"].'<br />';
		}
		$html .= '</p>';

		$html .= '<div><img src="'.REFLE_WWW_URL.'img/sp/201503/kugiri.jpg" alt="区切り" width="310" /></div>';

		$html .= $therapist[$i]["uketsuke_state_html"];

		$html .= '</div>';

	}

	//出勤セラピストは「1」、出勤しないセラピストは「2」
	$type = 2;
	//$therapist = get_therapist_page_data_attendance_2_common($area,$year,$month,$day,$type,WWW_URL,$access_type);
	$therapist = get_therapist_page_data_attendance_3_common($area,$year,$month,$day,$type,WWW_URL,$access_type,$attendance_all,$reservation_all);
	$therapist_num = count($therapist);

	$html .= '</div>';

	$html .= '<div style="padding:20px 0px 0px 0px;">';
	$html .= '<div class="title_bar">セラピスト一覧</div>';
	$html .= '<div id="therapist_list">';

	for($i=0;$i<$therapist_num;$i++){
		$html .= '<div class="title_therapist_info_content">';
		$html .= '<div class="top_area">';
		$html .= '<div class="name_img">';
		$html .= '<p class="therapist_name">';
		$html .= $therapist[$i]["therapist_name"].'（'.$therapist[$i]["age"].'）';
		$html .= '</p>';
		$html .= '<p class="img">';
		$html .= '<img src="'.S3_URL.$therapist[$i]["img_url"].'" alt="セラピスト'.$therapist[$i]["therapist_name"].'" width="70" />';
		$html .= '</p>';
		$html .= '<p class="info">';
		$html .= $therapist[$i]["hometown"].'出身<br />';
		$html .= 'セラピスト歴'.$therapist[$i]["history"];
		$html .= '</p>';
		$html .= '<div class="attendance_btn_block">';
		$html .= '<div style="float:left;">';
		$html .= '<input type="button" value="出勤予定表" style="padding:5px;" onclick="open_attendance_schedule_sp('.$therapist[$i]["therapist_id"].');" />';
		$html .= '</div>';
		$html .= '<div class="therapist_attendance_yotei_disp" id="yotei_disp_'.$therapist[$i]["therapist_id"].'">';
		$html .= 'xxxxxxxxxxxxxxxx';
		$html .= '</div>';
		$html .= '<br class="clear" />';
		$html .= '</div>';
		$html .= '</div>';



		$html .= '<div class="tokui">';
		if( $therapist[$i]["skill_2_exist_flg"] == true ){
			$html .= '<div class="one" style="margin-bottom:15px;">';
			$html .= '<div class="title_1">';
			$html .= '○一押しメニュー';
			$html .= '</div>';
			$html .= '<div class="content_sp">';
			$skill_data = $therapist[$i]["skill_2_data"];
			$skill_data_num = count($skill_data);
			for( $x=0; $x < $skill_data_num; $x++ ){
				$value = $skill_data[$x];
				$html .= '<div class="left"><img src="'.REFLE_WWW_URL.'img/skill/'.$value.'.png" alt="'.$skill_arr[$value].'" width="80" /></div>';
			}
			$html .= '<br class="clear" />';
			$html .= '</div>';
			$html .= '</div>';
		}
		$html .= '<div class="one">';
		$html .= '<div class="title_2">';
		$html .= '○施術可能メニュー';
		$html .= '</div>';
		$html .= '<div class="content_sp">';
		$skill_data = $therapist[$i]["skill_data"];
		$skill_data_num = count($skill_data);
		for( $x=0; $x < $skill_data_num; $x++ ){
			$value = $skill_data[$x];
			$html .= '<div class="left"><img src="'.REFLE_WWW_URL.'img/skill/'.$value.'.png" alt="'.$skill_arr[$value].'" width="80" /></div>';
		}
		$html .= '<br class="clear" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';



		$html .= '<br class="clear" />';
		$html .= '</div>';

		$html .= '<div><img src="'.REFLE_WWW_URL.'img/sp/201503/kugiri.jpg" alt="区切り" width="310" /></div>';

		$html .= '<p class="exp">';
		$html .= nl2br($therapist[$i]["pr_content"]);
		if( $therapist[$i]["shikaku"] != "" ){
			$html .= '<br />';
			$html .= '【保有資格】<br />';
			$html .= $therapist[$i]["shikaku"].'<br />';
		}
		$html .= '</p>';

		$html .= '</div>';
	}

	$html .= '</div>';
	$html .= '</div>';

	$action_time = get_microtime_sa_common($time1);		//マイクロタイム差計算
	//$html .= '<div>'.$action_time.'</div>';

	return $html;
	exit();

}

function get_paging_html_common($file_name,$start_num,$end_num,$max_num,$selected_num,$all_data_num){

	$num_pre = $selected_num-1;
	$num_next = $selected_num+1;

	$html = "";

	$html .= '<div>';
	$html .= '<div>';
	if($max_num != 0){
		if($max_num == 1){
		}else{
			if($selected_num != 1){
				$html .= '<span class="paging_num_forward">';
				$html .= sprintf('<a href="%sselected_num=%s">前へ</a>',$file_name,$num_pre);
				$html .= '</span>';
				$html .= '<span class="paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}else{
				$html .= '<span class="selected_paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}
			if(($selected_num-2) > 2){
				$html .= '<span class="paging_num_dod">...</span>';
			}
			for($i=2;$i<$max_num;$i++){
				if((($selected_num-2) > $i) || (($selected_num+2) < $i)){
				}else{
					if($selected_num == $i){
						$html .= '<span class="selected_paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}else{
						$html .= '<span class="paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}
				}
			}
			if($selected_num+3 < $max_num){
				$html .= '<span class="paging_num_dod">...</span>';
			}
			if($max_num != 1){
				if($selected_num == $max_num){
					$html .= '<span class="selected_paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';
				}else{
					$html .= '<span class="paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';
					$html .= '<span class="paging_num_next">';
					$html .= sprintf('<a href="%sselected_num=%s">次へ</a>',$file_name,$num_next);
					$html .= '</span>';
				}
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<br class="clear" />';

	return $html;
	exit();

}

function get_paging_html_sp_common($file_name,$start_num,$end_num,$max_num,$selected_num,$all_data_num){

	$num_pre = $selected_num-1;
	$num_next = $selected_num+1;

	$html = "";

	$html .= '<div>';

	$html .= '<div>';
	if($max_num != 0){
		if($max_num == 1){
		}else{
			if($selected_num != 1){
				$html .= '<span class="paging_num_forward">';
				$html .= sprintf('<a href="%sselected_num=%s">前へ</a>',$file_name,$num_pre);
				$html .= '</span>';

			}else{

			}

			if($max_num != 1){
				if($selected_num == $max_num){

				}else{

					$html .= '<span class="paging_num_next">';
					$html .= sprintf('<a href="%sselected_num=%s">次へ</a>',$file_name,$num_next);
					$html .= '</span>';
				}
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<br class="clear" />';



	$html .= '<div style="margin:10px 0px 0px 20px;">';

	$html .= '<div>';
	if($max_num != 0){
		if($max_num == 1){
		}else{
			if($selected_num != 1){

				$html .= '<span class="paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}else{
				$html .= '<span class="selected_paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}
			if(($selected_num-1) > 2){
				$html .= '<span class="paging_num_dod">...</span>';
			}
			for($i=2;$i<$max_num;$i++){
				if((($selected_num-1) > $i) || (($selected_num+1) < $i)){
				}else{
					if($selected_num == $i){
						$html .= '<span class="selected_paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}else{
						$html .= '<span class="paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}
				}
			}
			if($selected_num+2 < $max_num){
				$html .= '<span class="paging_num_dod">...</span>';
			}
			if($max_num != 1){
				if($selected_num == $max_num){
					$html .= '<span class="selected_paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';
				}else{
					$html .= '<span class="paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';

				}
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<br class="clear" />';

	return $html;
	exit();

}

function get_paging_html_for_vip_common($file_name,$start_num,$end_num,$max_num,$selected_num,$all_data_num){

	$num_pre = $selected_num-1;
	$num_next = $selected_num+1;

	$html = "";

	$html .= '<div>';
	$html .= '<div>';
	if($max_num != 0){
		if($max_num == 1){
		}else{
			if($selected_num != 1){
				$html .= '<span class="paging_num_forward">';
				$html .= sprintf('<a href="%sselected_num=%s"><img src="%simg/ssl/vip/history/arrow_left.gif" width="10" /></a>',$file_name,$num_pre,WWW_URL);
				$html .= '</span>';
				$html .= '<span class="paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}else{
				$html .= '<span class="selected_paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}
			if(($selected_num-2) > 2){
				$html .= sprintf('<span class="paging_num_dod"><img src="%simg/ssl/vip/history/dot.gif" width="40" /></span>',WWW_URL);
			}
			for($i=2;$i<$max_num;$i++){
				if((($selected_num-2) > $i) || (($selected_num+2) < $i)){
				}else{
					if($selected_num == $i){
						$html .= '<span class="selected_paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}else{
						$html .= '<span class="paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}
				}
			}
			if($selected_num+3 < $max_num){
				$html .= sprintf('<span class="paging_num_dod"><img src="%simg/ssl/vip/history/dot.gif" width="40" /></span>',WWW_URL);
			}
			if($max_num != 1){
				if($selected_num == $max_num){
					$html .= '<span class="selected_paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';
				}else{
					$html .= '<span class="paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';
					$html .= '<span class="paging_num_next">';
					$html .= sprintf('<a href="%sselected_num=%s"><img src="%simg/ssl/vip/history/arrow_right.gif" width="10" /></a>',$file_name,$num_next,WWW_URL);
					$html .= '</span>';
				}
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<br class="clear" />';

	return $html;
	exit();

}

function get_paging_html_sp_2_common($file_name,$start_num,$end_num,$max_num,$selected_num,$all_data_num){

	$num_pre = $selected_num-1;
	$num_next = $selected_num+1;

	$html = "";

	$html .= '<div>';
	$html .= '<div>';
	if($max_num != 0){
		if($max_num == 1){
		}else{
			if($selected_num != 1){
				$html .= '<span class="paging_num_forward">';
				$html .= sprintf('<a href="%sselected_num=%s">前へ</a>',$file_name,$num_pre);
				$html .= '</span>';
			}else{
			}
			if($max_num != 1){
				if($selected_num == $max_num){
				}else{
					$html .= '<span class="paging_num_next">';
					$html .= sprintf('<a href="%sselected_num=%s">次へ</a>',$file_name,$num_next);
					$html .= '</span>';
				}
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<br class="clear" />';
	$html .= '<div class="paging_num_wrapper">';
	$html .= '<div>';
	if($max_num != 0){
		if($max_num == 1){
		}else{
			if($selected_num != 1){
				$html .= '<span class="paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}else{
				$html .= '<span class="selected_paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}
			if(($selected_num-1) > 2){
				$html .= '<span class="paging_num_dod">...</span>';
			}
			for($i=2;$i<$max_num;$i++){
				if((($selected_num-1) > $i) || (($selected_num+1) < $i)){
				}else{
					if($selected_num == $i){
						$html .= '<span class="selected_paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}else{
						$html .= '<span class="paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}
				}
			}
			if($selected_num+2 < $max_num){
				$html .= '<span class="paging_num_dod">...</span>';
			}
			if($max_num != 1){
				if($selected_num == $max_num){
					$html .= '<span class="selected_paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';
				}else{
					$html .= '<span class="paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';

				}
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<br class="clear" />';

	return $html;
	exit();
}

function get_paging_html_sp_3_common($file_name,$start_num,$end_num,$max_num,$selected_num,$all_data_num,$disp_num){

	if( $all_data_num <= $disp_num ){

		return "";
		exit();

	}

	$num_pre = $selected_num-1;
	$num_next = $selected_num+1;

	$html = "";

	$html .= '<div>';

	$html .= '<div>';
	if($max_num != 0){
		if($max_num == 1){
		}else{
			if($selected_num != 1){
				$html .= '<span class="paging_num_forward">';
				$html .= sprintf('<a href="%sselected_num=%s">前へ</a>',$file_name,$num_pre);
				$html .= '</span>';

			}else{

			}

			if($max_num != 1){
				if($selected_num == $max_num){

				}else{

					$html .= '<span class="paging_num_next">';
					$html .= sprintf('<a href="%sselected_num=%s">次へ</a>',$file_name,$num_next);
					$html .= '</span>';
				}
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<br class="clear" />';



	$html .= '<div class="paging_num_wrapper">';

	$html .= '<div>';
	if($max_num != 0){
		if($max_num == 1){
		}else{
			if($selected_num != 1){

				$html .= '<span class="paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}else{
				$html .= '<span class="selected_paging_num_box">';
				$html .= sprintf('<a href="%sselected_num=1">1</a>',$file_name);
				$html .= '</span>';
			}
			if(($selected_num-1) > 2){
				$html .= '<span class="paging_num_dod">...</span>';
			}
			for($i=2;$i<$max_num;$i++){
				if((($selected_num-1) > $i) || (($selected_num+1) < $i)){
				}else{
					if($selected_num == $i){
						$html .= '<span class="selected_paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}else{
						$html .= '<span class="paging_num_box">';
						$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$i,$i);
						$html .= '</span>';
					}
				}
			}
			if($selected_num+2 < $max_num){
				$html .= '<span class="paging_num_dod">...</span>';
			}
			if($max_num != 1){
				if($selected_num == $max_num){
					$html .= '<span class="selected_paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';
				}else{
					$html .= '<span class="paging_num_box">';
					$html .= sprintf('<a href="%sselected_num=%s">%s</a>',$file_name,$max_num,$max_num);
					$html .= '</span>';

				}
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<br class="clear" />';

	return $html;
	exit();

}

function get_time_array_select_option_vip_2_common($time){
global $time_array;

	if( $time == "" ){

		$time = "-99";

	}

	//include(COMMON_INC."time_array.php");

	$html = "";

	$start_num = 1;

	for( $i=$start_num; $i<24; $i++ ){

		$hour = $time_array[$i]["hour"];
		$minute = $time_array[$i]["minute"];

		if( $minute == "0" ){

			$minute = "0".$minute;

		}

		if( $i > 12 ){

			$hour_24 = $hour + 24;

			$time_disp = sprintf("%s:%s(%s:%s)",$hour_24,$minute,$hour,$minute);

		}else{

			$time_disp = sprintf("%s:%s",$hour,$minute);

		}

		if( $i == $time ){

			$html .= sprintf("<option value='%s' selected>%s～</option>",$i,$time_disp);

		}else{

			$html .= sprintf("<option value='%s'>%s～</option>",$i,$time_disp);

		}

	}

	return $html;
	exit();

}

function get_select_frm_course_for_vip_reservation_input_common($course,$area){

	$data = get_course_array_by_area_common($area);

	$data_num = count($data);

	$html = "";

	$html .= '<select name="course">';
	$html .= '<option value="-1">選択してください</option>';

	for( $i=0; $i<$data_num; $i++ ){

		$tmp = $data[$i];

		if( $course == $tmp ){

			$html .= sprintf('<option value="%s" selected>%s</option>',$tmp,$tmp);

		}else{

			$html .= sprintf('<option value="%s">%s</option>',$tmp,$tmp);

		}

	}

	$html .= '</select>';

	return $html;
	exit();
}

function get_radio_frm_therapist_for_vip_reservation_input_common($data,$therapist_id,$access_type){

	//echo $therapist_id;exit();

	$html = "";

	$data_num = count($data);

	if( $data_num == 0 ){

$html =<<<EOT
<span style="color:red;font-weight:bold;">申し訳ありません。この時間帯に対応可能なセラピストはおりません。</span>
<input type="hidden" name="notherapist" value="true" />
EOT;

		return $html;
		exit();

	}

	$www_url = WWW_URL;

	$add = "";

	if( $therapist_id == "-1" ){

		$add = " checked";

	}

	if( $access_type == "sp" ){

		$img_width = "30";
		$br_num = "2";
		$disp = '<span style="font-size:10px;">指定セラピストなし</span>';

	}else{

		$img_width = "35";
		$br_num = "4";
		$disp = '指定セラピストなし';

	}
	$sampl_img = DEFAULT_therapist_img_m;
$html =<<<EOT
<div class="one">
<div class="left_1">
<input type="radio" name="therapist_id" value="-2"{$add}>
</div>
<div class="left_2">
<img src="{$www_url}{$sampl_img}" alt="セラピスト" width="{$img_width}" />
</div>
<div class="left_3">
{$disp}
</div>
<br class="clear" />
</div>
EOT;

	$s2_url = S3_URL;

	$x = 1;

	for( $i=0; $i<$data_num; $i++ ){

		$therapist_id_tmp = $data[$i]["therapist_id"];
		$name_site = $data[$i]["name_site"];

		$img_url_m = get_img_url_m_by_therapist_id_common($therapist_id_tmp);	//セラピスト頁情報から携帯用の画像URL取得

		if( $therapist_id == $therapist_id_tmp ){

			$add = " checked";

		}else{

			$add = "";

		}

		if( $img_url_m == "" ){

			$img_therapist = sprintf('<img src="%simg/ssl/vip/calendar/icon_sample.gif" alt="セラピスト" width="35" />',$www_url);

		}else{

			$img_therapist = sprintf('<img src="%s%s" alt="セラピスト" width="35" />',$s2_url,$img_url_m);

		}

$html .=<<<EOT
<div class="one">
<div class="left_1">
<input type="radio" name="therapist_id" value="{$therapist_id_tmp}"{$add}>
</div>
<div class="left_2">
{$img_therapist}
</div>
<div class="left_3">{$name_site}</div>
<br class="clear" />
</div>
EOT;

		$x++;

		$tmp = $x % $br_num;

		if( $tmp == 0 ){

			$html .= '<br class="clear" />';

		}

	}

	$html .= '<br class="clear" />';

	return $html;
	exit();

}

function get_calendar_course_list_pc_common($year,$month,$url){

	$html = "";

	$html .= sprintf('<input type="hidden" name="calendar_year" value="%s" id="calendar_year" />',$year);
	$html .= sprintf('<input type="hidden" name="calendar_month" value="%s" id="calendar_month" />',$month);

	$week_value = get_week_value_common($year, $month, 1);

	if( $week_value == "0" ){
		$pre_disp_num =  7;
	}else{
		$pre_disp_num =  $week_value;
	}

	$last_day_now = get_last_day_common($year,$month);
	$last_day_pre = get_pre_month_last_day_common($year,$month);

	$pre_disp_start_day = ($last_day_pre - $pre_disp_num) + 1;

	$type = "kako";

	$func=<<<EOT
onclick="calendar_move('{$year}','{$month}','{$type}','{$url}');"
EOT;

	$html .= '<div id="calendar_disp_top">';

	$html .= '<div class="left1">';
	$html .= '<img src="'.REFLE_WWW_URL.'img/calendar/calendar_left_pc.jpg" '.$func.' />';
	$html .= '</div>';

	$html .= '<div class="left2">';
	$html .= sprintf('%s年%s月',$year,$month);
	$html .= '</div>';

	$type = "mirai";

	$func=<<<EOT
onclick="calendar_move('{$year}','{$month}','{$type}','{$url}');"
EOT;

	$html .= '<div class="left3">';
	$html .= '<img src="'.REFLE_WWW_URL.'img/calendar/calendar_right_pc.jpg" '.$func.' />';
	$html .= '</div>';

	$html .= '<br class="clear" />';

	$html .= '</div>';

	$html .= '<div id="calendar_disp">';

	$html .= '<div class="title">日</div>';
	$html .= '<div class="title">月</div>';
	$html .= '<div class="title">火</div>';
	$html .= '<div class="title">水</div>';
	$html .= '<div class="title">木</div>';
	$html .= '<div class="title">金</div>';
	$html .= '<div class="title">土</div>';

	for( $i=$pre_disp_start_day; $i<=$last_day_pre; $i++ ){

		$html .= '<div class="out">'.$i.'</div>';

	}

	for( $i=1; $i<=$last_day_now; $i++ ){

		$day = $i;

		$html .= sprintf('<a href="%syear=%s&month=%s&day=%s" class="in">%s</a>',$url,$year,$month,$day,$day);

	}

	$week_value = get_week_value_common($year, $month, $last_day_now);

	if( $week_value == "0" ){
		$next_disp_num =  6;
	}else if( $week_value == "1" ){
		$next_disp_num =  5;
	}else if( $week_value == "2" ){
		$next_disp_num =  4;
	}else if( $week_value == "3" ){
		$next_disp_num =  3;
	}else if( $week_value == "4" ){
		$next_disp_num =  2;
	}else if( $week_value == "5" ){
		$next_disp_num =  1;
	}else if( $week_value == "6" ){
		$next_disp_num =  7;
	}else{
		$next_disp_num =  0;
	}

	for( $i=1; $i<=$next_disp_num; $i++ ){

		$html .= '<div class="out">'.$i.'</div>';

	}

	$html .= '</div>';

	$html .= '<br class="clear" />';

	$func=<<<EOT
onclick="calendar_close();"
EOT;

	$html .= '<div id="calendar_bottom">';
	$html .= '<div id="calendar_disp_close" '.$func.'>×&nbsp;閉じる</div>';
	$html .= "</div>";

	return $html;
	exit();

}

function get_attendance_list_data_for_man_driver_common($attendance_data,$day_data,$type){

	$time_array = get_time_array_driver_common();	//ドライバー用時刻配列取得

	$data = array();

	$k=0;

	$data_empty_flg = false;

	$day_data_num = count($day_data);
	$attendance_data_num = count($attendance_data);

	for($i=0;$i<$day_data_num;$i++){

		$year = $day_data[$i]["year"];
		$month = $day_data[$i]["month"];
		$day = $day_data[$i]["day"];
		$week = $day_data[$i]["week"];

		$kikan = $year."_".$month."_".$day;

		$past_flg = today_past_check_common($year,$month,$day);	//本日が指定日より大きいか

		if( $past_flg == false ){

			$match_flg = false;

			for($j=0;$j<$attendance_data_num;$j++){

				if($match_flg == false){

					$year_a = $attendance_data[$j]["year"];
					$month_a = $attendance_data[$j]["month"];
					$day_a = $attendance_data[$j]["day"];
					$start_time = $attendance_data[$j]["start_time"];
					$end_time = $attendance_data[$j]["end_time"];
					$syounin_state = $attendance_data[$j]["syounin_state"];
					$comment = $attendance_data[$j]["comment"];
					$shift_change_flg = $attendance_data[$j]["shift_change_flg"];
					$kekkin_flg = $attendance_data[$j]["kekkin_flg"];
					$updated = $attendance_data[$j]["updated"];

					$updated_month = intval(date('m', $updated));
					$updated_day = intval(date('d', $updated));
					$updated_hour = intval(date('H', $updated));
					$updated_minute = intval(date('i', $updated));

					$today_absence = $attendance_data[$j]["today_absence"];

					if( $updated_minute < 10 ){

						$updated_minute = "0".$updated_minute;

					}

					if( ($year==$year_a) && ($month==$month_a) && ($day==$day_a) ){
						$match_flg = true;
					}
				}
			}

			$html = "";

			$day_disp = $month."/".$day;

			if( $type == "regist" ){

				if( ($syounin_state == "0") && ($shift_change_flg == "0") && ($kekkin_flg == "0") && ($today_absence == "0") ){

					if( $match_flg == true ){

						$start_time_ta = $time_array[$start_time]["minute"];
						$end_time_ta = $time_array[$end_time]["minute"];

						if($start_time_ta=="0"){
							$start_time_ta = "0".$start_time_ta;
						}

						if($end_time_ta=="0"){
							$end_time_ta = "0".$end_time_ta;
						}

						$html .= '<div style="font-size:12px;width:50px;float:left;">';
						$html .= $day_disp."(".$week.")";
						$html .= "</div>";
						$html .= '<div style="font-size:12px;width:60px;float:left;">';
						$html .= $time_array[$start_time]["hour"]."時".$start_time_ta."分";
						$html .= "</div>";
						$html .= '<div style="font-size:12px;width:20px;float:left;">';
						$html .= "／";
						$html .= "</div>";
						$html .= '<div style="font-size:12px;width:90px;float:left;">';
						$html .= $time_array[$end_time]["hour"]."時".$end_time_ta."分";
						$html .= '</div>';
						$html .= '<div style="font-size:12px;width:30px;float:left;">';
						$html .= "|";
						$html .= '</div>';
						$html .= '<div style="font-size:12px;width:50px;float:left;padding-left:10px;">';
						if($syounin_state=="1"){
							$html .= '<input type="checkbox" name="syounin[]" value="'.$kikan.'" class="syounin_ckb" id="syounin_'.$kikan.'" checked />';
						}else{
							$html .= '<input type="checkbox" name="syounin[]" value="'.$kikan.'" class="syounin_ckb" id="syounin_'.$kikan.'" />';
						}
						$html .= '</div>';
						$html .= '<div style="font-size:12px;width:50px;float:left;padding-left:10px;">';
						if($syounin_state=="2"){
							$html .= '<input type="checkbox" name="fusyounin[]" value="'.$kikan.'" class="syounin_ckb" id="fusyou_'.$kikan.'" checked />';
						}else{
							$html .= '<input type="checkbox" name="fusyounin[]" value="'.$kikan.'" class="syounin_ckb" id="fusyou_'.$kikan.'" />';
						}
						$html .= '</div>';
						$html .= '<div style="font-size:12px;width:50px;float:left;padding-left:10px;">';
						if($syounin_state=="3"){
							$html .= '<input type="checkbox" name="shimekiri[]" value="'.$kikan.'" class="syounin_ckb" id="shimekiri_'.$kikan.'" checked />';
						}else{
							$html .= '<input type="checkbox" name="shimekiri[]" value="'.$kikan.'" class="syounin_ckb" id="shimekiri_'.$kikan.'" />';
						}
						$html .= '</div>';
						$html .= '<br class="clear" />';

						$data[$k] = $html;

					}else{

						$data_empty_flg = true;

					}

					if($data_empty_flg == false){

						$k++;

					}else{

						$data_empty_flg = false;

					}

				}

			}else if( $type == "edit" ){

				if( ( ($shift_change_flg == "1") && ($syounin_state == "0") ) || ( ($kekkin_flg == "1") && ($syounin_state == "0") ) ){

					$change_type = "";

					if( $kekkin_flg == "1" ){

						$change_type = "欠勤";

					}else{

						$change_type = "変更";

					}

					if( $match_flg == true ){

						$start_time_ta = $time_array[$start_time]["minute"];
						$end_time_ta = $time_array[$end_time]["minute"];

						if( $start_time_ta == "0" ){

							$start_time_ta = "0".$start_time_ta;

						}

						if($end_time_ta=="0"){

							$end_time_ta = "0".$end_time_ta;

						}

						//$updated_time_disp = sprintf("%s/%s　%s：%s",$updated_month,$updated_day,$updated_hour,$updated_minute);

						//$html .= '<div style="padding-bottom:5px;">依頼日　'.$updated_time_disp.'</div>';
						$html .= '<div>';
						$html .= '<div style="font-size:12px;width:50px;float:left;">';
						$html .= $day_disp."(".$week.")";
						$html .= "</div>";
						$html .= '<div style="font-size:12px;width:60px;float:left;">';
						$html .= $time_array[$start_time]["hour"]."時".$start_time_ta."分";
						$html .= "</div>";
						$html .= '<div style="font-size:12px;width:20px;float:left;">';
						$html .= "／";
						$html .= "</div>";
						$html .= '<div style="font-size:12px;width:120px;float:left;">';
						$html .= $time_array[$end_time]["hour"]."時".$end_time_ta."分(".$change_type.")";
						$html .= '</div>';
						$html .= '<div style="font-size:12px;width:70px;float:left;padding-left:30px;">';

						if($kekkin_flg=="1"){

							$html .= '<input type="checkbox" name="syounin[]" value="kekkin_'.$kikan.'" />';

						}else{

							$html .= '<input type="checkbox" name="syounin[]" value="henkou_'.$kikan.'" />';

						}

						$html .= '</div>';
						$html .= '<br class="clear" />';
						$html .= '</div>';

						$data[$k] = $html;

					}else{

						$data_empty_flg = true;

					}

					if($data_empty_flg == false){

						$k++;

					}else{

						$data_empty_flg = false;

					}

				}

			}else{

				echo "error!(get_attendance_list_data_2)";
				exit();

			}

		}

	}

	return $data;
	exit();

}

function PHP_get_date_select_frm($now, $u_nowTime){
	$hour = date('H', $u_nowTime);
	if($hour<6){
		$index = -1;
	} else {
		$index = 0;
	}

	$html = '<select name="day" id="reserv_date" required="">';
	//1週間分取得
	for($i=$index; $i<=7; $i++) {
		$selected = "";
		$date_val = date('Y-m-d', strtotime($i.' day', $u_nowTime));
		if($date_val == $now) $selected = " selected";
		$html .= '<option value="' . $date_val . '"' . $selected . '>' . date('Y/m/d', strtotime($i.' day', $u_nowTime)) . '</option>';
	}
	$html.= '</select>';

	return $html;
}
?>
