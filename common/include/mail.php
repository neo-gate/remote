<?php
/* =================================================================================
		Script Name	mail.php( メール関係 )
		Update Date	2018/02/22	全面的に整理 by aida
================================================================================= */

//----お礼メッセージの未記入セラピスト  ※注意 by aida
function get_mail_content_for_remind_therapist_thanks_common($data,$year,$month,$day){

	//update by aida at 20180220 from
	//$area_data = array(
	//	"0"=>"tokyo",
	//	"1"=>"yokohama",
	//	"2"=>"sapporo",
	//	"3"=>"sendai",
	//	"4"=>"osaka"
	//);
	global $area_data;
	//update by aida at 20180220 to

	$reservation_for_board_num = get_reservation_for_board_num_by_day_common($year, $month, $day);

	$therapist_thanks_num = get_therapist_thanks_num_by_day_common($year,$month,$day);

	$not_regist_num = $reservation_for_board_num - $therapist_thanks_num;

$title =<<<EOT
{$month}/{$day}お礼メッセージの未記入セラピスト
EOT;

	$area_data_num = count($area_data);

	$content = "";

$content .=<<<EOT

{$month}月{$day}日営業日のお礼メッセージ未記入セラピストは以下の通りです。

{$reservation_for_board_num}件中　{$therapist_thanks_num}件登録済み　{$not_regist_num}件未登録


EOT;

	for( $i=0; $i<$area_data_num; $i++ ){

		$area_name = get_area_name_by_area_common($area_data[$i]);		//エリア名取得 in common/include/shop_area_list.php

$content .=<<<EOT
【{$area_name}】

EOT;

		$tmp = $data[$area_data[$i]];

		$tmp_num = count($tmp);

		for( $x=0; $x<$tmp_num; $x++ ){

			$therapist_id = $tmp[$x]["id"];
			$therapist_name = $tmp[$x]["name"];
			$board_data = $tmp[$x]["board_data"];

			$content_therapist = "";

$content .=<<<EOT
・{$therapist_name}

EOT;

			$board_data_num = count($board_data);

			for( $y=0; $y<$board_data_num; $y++ ){

				$customer_name = $board_data[$y]["customer_name"];
				$start_hour = $board_data[$y]["start_hour"];

$content .=<<<EOT
　{$start_hour}時　{$customer_name}

EOT;

$content_therapist .=<<<EOT
{$start_hour}時　{$customer_name}

EOT;

			}

$content .=<<<EOT


EOT;

			//セラピストにメール
			send_mail_remind_therapist_thanks_common($year,$month,$day,$therapist_id,$therapist_name,$content_therapist);
			sleep(1);

		}

	}

	$return_data["title"] = $title;
	$return_data["content"] = $content;

	return $return_data;
	exit();
}

//お客様の声、登録
function regist_voice_common($name,$age,$gender,$satisfaction,$content,$shop_type){
global $ARRAY_age;

	$now = time();

	$mail_content = $content;

	$name = mysqli_real_escape_string($name);
	$age = mysqli_real_escape_string($age);
	$gender = mysqli_real_escape_string($gender);
	$satisfaction = mysqli_real_escape_string($satisfaction);
	$content = mysqli_real_escape_string($content);

	if( $name == "" ) $name = "匿名";

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysqli_query(DbCon, $sql);

	//トランザクション開始
	$sql = "begin";
	mysqli_query(DbCon, $sql);

	$sql = sprintf("insert into voice(created,updated,shop_type,name,age,gender,satisfaction,content) values('%s','%s','%s','%s','%s','%s','%s','%s')",$now,$now,$shop_type,$name,$age,$gender,$satisfaction,$content);
	$res = mysqli_query(DbCon, $sql);
	if($res == false){
		mysql_query( "rollback", DbCon );	//ロールバック

		return false;
		echo "mail.php regist_voice_common line:" . __LINE__ . "<br />"; exit();
	}
/*
	$age_disp = "";
	foreach($ARRAY_age as $ws_Key => $ws_Val) {
		if($ws_Key == $age) {
			$age_disp = $ws_Val;
			break;
		}
	}
	if(!$age_disp) $age_disp = "不明";

	$gender_disp = "";
	foreach($ARRAY_sex as $ws_Key => $ws_Val) {
		if($ws_Key == $age) {
			$gender_disp = $ws_Val;
			break;
		}
	}
	if(!$gender_disp) $gender_dispp = "不明";
*/
$age_disp = "";

if($age=="1"){

	$age_disp = "10代";

}else if($age=="2"){

	$age_disp = "20代";

}else if($age=="3"){

	$age_disp = "30代";

}else if($age=="4"){

	$age_disp = "40代";

}else if($age=="5"){

	$age_disp = "50代";

}else if($age=="6"){

	$age_disp = "60代以上";

}else{

	$age_disp = "不明";

}

$gender_disp = "";

if($gender=="1"){

	$gender_disp = "男性";

}else if($gender=="2"){

	$gender_disp = "女性";

}else{

	$gender_disp = "不明";

}
	$satisfaction_disp = "";

	if($satisfaction=="1"){
		$satisfaction_disp = "★";
	}else if($satisfaction=="2"){
		$satisfaction_disp = "★★";
	}else if($satisfaction=="3"){
		$satisfaction_disp = "★★★";
	}else if($satisfaction=="4"){
		$satisfaction_disp = "★★★★";
	}else if($satisfaction=="5"){
		$satisfaction_disp = "★★★★★";
	}else{
		$satisfaction_disp = "不明";
	}

	$shop_name = "";

	//----店舗情報取得
	$ws_ArShop = PHP_getShopInfo_common($shop_type, "info");	//update by aida at 20180215
	$shop_name = $ws_ArShop["shop_name"];
	$mailto = $ws_ArShop["mailto"];
	$header = $ws_ArShop["header"];

	$parameter = "-f ".$mailto;

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$site_url = WWW_URL;

	$title = "お客様の声【".$shop_name."】";
	$content =<<<EOT
{$shop_name}の客様の声フォームから以下の投稿がありました(管理ページで公開設定必要)

お名前：{$name}
年代：{$age_disp}
性別：{$gender_disp}
満足度：{$satisfaction_disp}
内容
{$mail_content}

以上です。
EOT;

	$res = mb_send_mail($mailto,$title,$content,$header,$parameter);
	//echo $mailto . ", " . $title . ", " . $header . ", " . $parameter . "<br />" . $content . "<br />";

	if($res == false){
		mysql_query( "rollback", DbCon );	//ロールバック
		return false;
		//echo "mail.php regist_voice_common line:" . __LINE__ . "<br />"; exit();
	}

	//コミット
	$sql = "commit";
	mysqli_query(DbCon, $sql);

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}

//----
function send_booking_form_common(
$namae,$mail,$gender,$today_month,$today_day,$today_year,
$time,$course,$hotel_or_home,$hotel_name,$room_number,
$home_address,$cash_or_credit,$any_request,$area){

	$hotel_or_home_content = "";

	if( $gender == "" ) $gender = "empty";

	if( $room_number == "" ) $room_number = "empty";

	if( $any_request == "" ) $any_request = "empty";

	if( $hotel_or_home == "hotel" ){

		$hotel_or_home_content =<<<EOT
Hotel name：{$hotel_name}
Room number：{$room_number}
EOT;

	}else if( $hotel_or_home == "home" ){

		$hotel_or_home_content =<<<EOT
Home address：{$home_address}
EOT;

	}else{
		return false;
		exit();
	}

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $mail;

	//----エリア情報取得
	$ws_areaData = PHP_getAreaInfo_common($area);
	$site_name = $ws_areaData["site_name"];
	$site_tel = $ws_areaData["site_tel"];
	$site_mail = $ws_areaData["site_mail"];
	$site_url = $ws_areaData["site_url"];
	$title = $ws_areaData["title"];

	$content =<<<EOT

Thank you very much for your booking.
We'll check if the time you hope is available to send therapist and
we'll mail you back soon for confirmation.

Please wait for a while.

Hereinafter,The contents of your booking.

Name：{$namae}
Your E-mail：{$mail}
Sex：{$gender}
Date of use：{$today_month}/{$today_day}/{$today_year}
Time of use：{$time}
Massage time length：{$course}
Hotel or Home：{$hotel_or_home}
{$hotel_or_home_content}
How to pay：{$cash_or_credit}
Any request：
{$any_request}

Hereinafter,Our shop's information.

Relaxation Massage {$site_name}
TEL：{$site_tel}
MAIL：{$site_mail}
URL：{$site_url}

EOT;

	$header = "From: ".$site_mail."\n";
	$header .= "Bcc: ".$site_mail;

	$res = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($res == false){
		return false;
		exit();
	}

	return true;
	exit();

}

//----
function send_booking_form_ch_common(
$namae,$mail,$gender,$today_month,$today_day,$today_year,
$time,$course,$hotel_or_home,$hotel_name,$room_number,
$home_address,$cash_or_credit,$any_request,$area){

	$hotel_or_home_content = "";

	if( $gender == "" ){
		//$gender = "empty";
	}

	if( $room_number == "" ){
		//$room_number = "empty";
	}

	if( $any_request == "" ){
		//$any_request = "empty";
	}

	if( $hotel_or_home == "??" ){

		$hotel_or_home_content =<<<EOT
??的全称：{$hotel_name}
房?号?：{$room_number}
EOT;

	}else if( $hotel_or_home == "自己的住宅" ){

		$hotel_or_home_content =<<<EOT
自己住宅的地址：{$home_address}
EOT;

	}else{

		return false;
		exit();

	}

	//メール送信
	mb_language("zh");
	mb_internal_encoding("UTF-8");
	$mailto = $mail;

	//----エリア情報取得
	$ws_areaData = PHP_getAreaInfo_common($area);
	$site_name = $ws_areaData["site_name"];
	$site_tel = $ws_areaData["site_tel"];
	$site_mail = $ws_areaData["site_mail"];
	$site_url = $ws_areaData["site_url"];
	$title = $ws_areaData["title_ch"];

	$content =<<<EOT

Thank you very much for your booking.
We'll check if the time you hope is available to send therapist and
we'll mail you back soon for confirmation.

Please wait for a while.

Hereinafter,The contents of your booking.

姓名：{$namae}
?子信箱号?：{$mail}
性?：{$gender}
??日：{$today_month}/{$today_day}/{$today_year}
????：{$time}
?????的??：{$course}
??或自己的住宅：{$hotel_or_home}
{$hotel_or_home_content}
支付方法：{$cash_or_credit}
是否?有其他要求：
{$any_request}

Hereinafter,Our shop's information.

派遣按摩 {$site_name}
TEL：{$site_tel}
MAIL：{$site_mail}
URL：{$site_url}

EOT;

$header = "From: ".$site_mail."\n";
$header .= "Bcc: ".$site_mail;

$res = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

if($res == false){
	return false;
	exit();
}

return true;
exit();
}

//----予約変更処理
function vip_page_confirm_action_common(
$mail,$customer_id,$time_array,$time,$access_type,$customer_name,$year,$month,$day,$week_array,
$week,$mail_hour,$minute,$course,$therapist_name,$customer_tel,$free,$shop_area){

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysqli_query(DbCon, $sql);

	//トランザクション開始
	$sql = "begin";
	mysqli_query(DbCon, $sql);

	// 会員情報を更新するSQL文(メールアドレスのみ)
	$sql = sprintf("update customer set mail='%s' where customer_id='%s'",$mail,$customer_id);
	$res = mysqli_query(DbCon, $sql);

	if($res == false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		return false;
		exit();
	}

	$mail_hour = $time_array[$time]["hour"];

	if( $mail_hour < 10 ) $mail_hour = $mail_hour + 24;

	if( $time_array[$time]["minute"] == '0' ){
		$minute = "00";
	}else{
		$minute = $time_array[$time]["minute"];
	}

	//----店舗情報取得
	$ws_ArShop = PHP_getShopInfo_common($shop_area, "order");	//update by aida at 20180215
	$shop_name = $ws_ArShop["shop_name"];
	$mailto = $ws_ArShop["mailto"];
	$header = $ws_ArShop["header"];

	if(!$shop_name) {
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		return false;
		exit();
	}

	$parameter="-f ".$mailto;

	$title = sprintf("VIP会員用ご予約フォーム(%s)",$shop_name);

	if( $access_type == "sp" ){
		$title = $title."【SP】";
	}else{
		$title = $title."【PC】";
	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$content =<<<EOT

[{$shop_name}]VIP会員用ご予約フォームから以下の内容の予約がありました。

ご予約者名:{$customer_name}
ご利用日:{$year}年{$month}月{$day}日({$week_array[$week]})
ご利用開始時間:{$mail_hour}:{$minute}
ご利用予定コース:{$course}分コース
ご指定セラピスト:{$therapist_name}
電話番号:{$customer_tel}
メールアドレス:{$mail}
【ご意見、ご要望】
{$free}

以上
EOT;

	$result = mb_send_mail($mailto,$title,$content,$header,$parameter);

	if( $result == false ){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		return false;
		exit();
	}

	//コミット
	$sql = "commit";
	mysqli_query(DbCon, $sql);

	//MySQL切断
	//mysql_close( DbCon );

	return true;
	exit();
}

//----WEB予約メール発信
function PHP_web_reserv_mail_common(
$mail,$customer_id,$time_array,$time,$access_type,$customer_name,$year,$month,$day,$week_array,
$week,$mail_hour,$minute,$course,$therapist_name,$customer_tel,$free,$shop_area, $u_ward_address){


	$mail_hour = $time_array[$time]["hour"];

	if( $mail_hour < 10 ) $mail_hour = $mail_hour + 24;

	if( $time_array[$time]["minute"] == '0' ){
		$minute = "00";
	}else{
		$minute = $time_array[$time]["minute"];
	}

	//----店舗情報取得
	$ws_ArShop = PHP_getShopInfo_common($shop_area, "order");	//update by aida at 20180215
	$shop_name = $ws_ArShop["shop_name"];
	$mailto = $ws_ArShop["mailto"];
	$header = $ws_ArShop["header"];

	if(!$shop_name) {
		//ロールバック
		mysql_query( "rollback", DbCon );
		echo "vip_page_confirm_action_2_common line:" . __LINE__ . "<br />";

		return false;
		exit();
	}

	$parameter="-f ".$mailto;

	$title = sprintf("WEB予約用ご予約フォーム(%s)",$shop_name);

	if( $access_type == "sp" ){

		$title = $title."【SP】";

	}else{

		$title = $title."【PC】";

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$content =<<<EOT

[{$shop_name}]WEB予約用ご予約フォームから以下の内容の予約がありました。

ご予約者名:{$customer_name}
ご利用日:{$year}年{$month}月{$day}日({$week_array[$week]})
ご利用開始時間:{$mail_hour}:{$minute}
ご利用予定コース:{$course}
ご指定セラピスト:{$therapist_name}
電話番号:{$customer_tel}
派遣先住所：{$u_ward_address}
メールアドレス:{$mail}
【ご意見、ご要望】
{$free}

以上
EOT;

	$result = mb_send_mail($mailto,$title,$content,$header,$parameter);
	//echo $mailto . ", " . $title . ", " . $header . ", " . $parameter . "<br />" . $content . "<br />";

	return $result;
}

//----予約変更処理２
function vip_page_confirm_action_2_common(
$mail,$customer_id,$time_array,$time,$access_type,$customer_name,$year,$month,$day,$week_array,
$week,$mail_hour,$minute,$course,$therapist_name,$customer_tel,$free,$shop_area){

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysqli_query(DbCon, $sql);

	//トランザクション開始
	$sql = "begin";
	mysqli_query(DbCon, $sql);

	// 会員情報を更新するSQL文(メールアドレスのみ)
	if($customer_id > 0 && $mail) {
		$sql = sprintf("update customer set mail='%s' where customer_id='%s'",$mail,$customer_id);
		$res = mysqli_query(DbCon, $sql);
		if($res == false){
			//ロールバック
			mysql_query( "rollback", DbCon );
			echo "vip_page_confirm_action_2_common line:" . __LINE__ . "<br />";

			return false;
			exit();
		}
	}

	$mail_hour = $time_array[$time]["hour"];

	if( $mail_hour < 10 ) $mail_hour = $mail_hour + 24;

	if( $time_array[$time]["minute"] == '0' ){
		$minute = "00";
	}else{
		$minute = $time_array[$time]["minute"];
	}

	//----店舗情報取得
	$ws_ArShop = PHP_getShopInfo_common($shop_area, "order");	//update by aida at 20180215
	$shop_name = $ws_ArShop["shop_name"];
	$mailto = $ws_ArShop["mailto"];
	$header = $ws_ArShop["header"];

	if(!$shop_name) {
		//ロールバック
		mysql_query( "rollback", DbCon );
		echo "vip_page_confirm_action_2_common line:" . __LINE__ . "<br />";

		return false;
		exit();
	}

	$parameter="-f ".$mailto;

	$title = sprintf("VIP会員用ご予約フォーム(%s)",$shop_name);

	if( $access_type == "sp" ){

		$title = $title."【SP】";

	}else{

		$title = $title."【PC】";

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$content =<<<EOT

[{$shop_name}]VIP会員用ご予約フォームから以下の内容の予約がありました。

ご予約者名:{$customer_name}
ご利用日:{$year}年{$month}月{$day}日({$week_array[$week]})
ご利用開始時間:{$mail_hour}:{$minute}
ご利用予定コース:{$course}
ご指定セラピスト:{$therapist_name}
電話番号:{$customer_tel}
メールアドレス:{$mail}
【ご意見、ご要望】
{$free}

以上
EOT;

	$result = mb_send_mail($mailto,$title,$content,$header,$parameter);
	//echo $mailto . ", " . $title . ", " . $header . ", " . $parameter . "<br />" . $content . "<br />";

	if( $result == false ){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);
		echo "vip_page_confirm_action_2_common line:" . __LINE__ . "<br />";

		return false;
		exit();
	}

	//コミット
	$sql = "commit";
	mysqli_query(DbCon, $sql);

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}

//----予約登録
function update_reservation_data_transaction_common(
$start_hour,$start_minute,$end_hour,$end_minute,$reservation_for_board_id,$therapist_id,$attendance_id,
$shop_area,$place_name,$course,$extension,$card_flg,$card_confirm_flg,$complete_flg,$start_flg,
$start_real_time_flg,$mail_flg,$mail_type,$customer_name,$check_url,
$shop_name,$course_old,$course_new,$extension_old,$extension_new,$course_change_flg,$extension_change_flg,
$course_var){

	$bbs_common_url = get_bbs_common_url_common();	//BBSのURL取得

	$therapist_name = get_therapist_name_by_therapist_id_honmyou_common($therapist_id);	//セラピスト名取得(本名)
	$now = time();
	$now_hour = intval(date('H', $now));
	$now_minute = intval(date('i', $now));
	$now_hour_mail = hour_plus_24_common($now_hour);
	$now_minute_mail = minute_zero_add_common($now_minute);
	$area_name = $place_name;
	$customer_name = preg_replace("/[\(（].*/u","",$customer_name);

	$reservation_no = get_reservation_no_by_reservation_for_board_id_common($reservation_for_board_id);

	$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)

	if( $extension_change_flg == true ){

		$price = get_change_price_extension_common($shop_name,$extension_old,$extension_new,$price);

		$extension = $extension_new;

	}

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得

	$discount = $data["discount"];
	$card_flg_old = $data["card_flg"];
	$course_var_old = $data["course_var"];
	$start_real_time_old = $data["start_real_time"];
	$year_re = $data["year"];
	$month_re = $data["month"];
	$day_re = $data["day"];

	$price_old = get_price_by_reservation_for_board_id_common($reservation_for_board_id);	//料金取得

	//札幌リフレその他、割引が「なし」ではない場合、コースの変更に応じて割引も変化しなければいけない
	$discount_new = get_discount_new($discount,$shop_name,$course_var);

	if( $course_change_flg == true ){

		$price = get_change_price_course_common($shop_name,$course_var,$reservation_for_board_id,$discount_new);

	}

	$bcc_add = get_bcc_add_shift_common($shop_area);	//BCCのメールリスト文字列取得

	$data = get_card_info_for_update_reservation_data_common($reservation_for_board_id,$card_flg,$price);

	$card_price_free = $data["card_price_free"];
	$card_commission_free = $data["card_commission_free"];
	$card_commission_type = $data["card_commission_type"];

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysqli_query(DbCon, $sql);

	//トランザクション開始
	$sql = "begin";
	mysqli_query(DbCon, $sql);

	if( ($course_change_flg == true) || ($extension_change_flg == true) ){

		//金額変更処理
		$sql = sprintf("update sale_history set price='%s' where reservation_no='%s'",$price,$reservation_no);

		$res = mysqli_query(DbCon, $sql);

		if($res == false){
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:0)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}

	$reservation_time_data = change_to_reservation_time_common($start_hour,$start_minute,$end_hour,$end_minute);
	$update_start_time = $reservation_time_data["start_time"];
	$update_end_time = $reservation_time_data["end_time"];

	$sql = sprintf("delete from reservation_new where reservation_for_board_id='%s'",$reservation_for_board_id);

	$res = mysqli_query(DbCon, $sql);
	if($res == false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	if( $therapist_id != "-1" ){

		//そして改めて登録

		$update_start_time = $update_start_time - 0;
		$update_end_time = $update_end_time + 1;

		for( $i = $update_start_time; $i <= $update_end_time; $i++ ){

			if( ($i == $update_start_time) || ($i == $update_end_time) ){
				$sql = sprintf("insert into reservation_new(attendance_id,time,reservation_for_board_id,auto_flg,area) values('%s','%s','%s','%s','%s')",$attendance_id,$i,$reservation_for_board_id,1,$shop_area);
			}else{
				$sql = sprintf("insert into reservation_new(attendance_id,time,reservation_for_board_id,area) values('%s','%s','%s','%s')",$attendance_id,$i,$reservation_for_board_id,$shop_area);
			}

			$res = mysqli_query(DbCon, $sql);
			if($res == false){
				//ロールバック
				$sql = "rollback";
				mysqli_query(DbCon, $sql);

				$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:2)";
				header("Location: ".WWW_URL."error.php");
				exit();
			}
		}

		//前後が二時間以内の場合、そこも予約
		$before_latest_end_time = get_before_latest_end_time_common($attendance_id,$update_start_time,$shop_area);
		$after_first_start_time = get_after_first_start_time_common($attendance_id,$update_end_time,$shop_area);
		$sa = $update_start_time - $before_latest_end_time;

		if( $sa < 5 ){
			for( $x = ( $before_latest_end_time + 0 ); $x < $update_start_time; $x++ ){

				$sql = sprintf("insert into reservation_new(attendance_id,time,reservation_for_board_id,auto_flg,area) values('%s','%s','%s','%s','%s')",$attendance_id,$x,$reservation_for_board_id,1,$shop_area);

				$res = mysqli_query(DbCon, $sql);
				if($res == false){
					//ロールバック
					$sql = "rollback";
					mysqli_query(DbCon, $sql);

					$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:3)";
					header("Location: ".WWW_URL."error.php");
					exit();
				}
			}
		}

		if( $after_first_start_time != "-1" ){
			$sa = $after_first_start_time - $update_end_time;
			if( $sa < 5 ){
				for( $x = ( $update_end_time + 1 ); $x < $after_first_start_time; $x++ ){

					$sql = sprintf("insert into reservation_new(attendance_id,time,reservation_for_board_id,auto_flg,area)values('%s','%s','%s','%s','%s')",$attendance_id,$x,$reservation_for_board_id,1,$shop_area);

					$res = mysqli_query(DbCon, $sql);
					if($res == false){
						//ロールバック
						$sql = "rollback";
						mysqli_query(DbCon, $sql);

						$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:4)";
						header("Location: ".WWW_URL."error.php");
						exit();
					}
				}
			}
		}

	}

	$start_hour = hour_change_over_24_common($start_hour);
	$end_hour = hour_change_over_24_common($end_hour);

	$time_len = get_time_len_new_common($start_hour,$start_minute,$end_hour,$end_minute);

	if( $therapist_id == "-1" ){

		$sql = sprintf("
update reservation_for_board set
start_hour='%s',start_minute='%s',end_hour='%s',end_minute='%s',time_len='%s',area_name='%s',
attendance_id='-1',course='%s',extension='%s',card_flg='%s',card_confirm_flg='%s',complete_flg='%s',start_flg='%s'
where id='%s'",
$start_hour,$start_minute,$end_hour,$end_minute,$time_len,$place_name,
$course,$extension,$card_flg,$card_confirm_flg,$complete_flg,$start_flg,$reservation_for_board_id);

	}else{
		//course_var,discountも更新
		$sql = sprintf("
update reservation_for_board set
start_hour='%s',start_minute='%s',end_hour='%s',end_minute='%s',time_len='%s',area_name='%s',
attendance_id='%s',course='%s',course_var='%s',extension='%s',card_flg='%s',card_confirm_flg='%s',
complete_flg='%s',start_flg='%s',discount='%s' where id='%s'",
$start_hour,$start_minute,$end_hour,$end_minute,$time_len,$place_name,$attendance_id,
$course,$course_var,$extension,$card_flg,$card_confirm_flg,$complete_flg,$start_flg,$discount_new,$reservation_for_board_id);
	}

	$res = mysqli_query(DbCon, $sql);

	if($res == false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:5)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$sql = sprintf("
update reservation_for_board set
card_price_free='%s',
card_commission_free='%s',
card_commission_type='%s'
where id='%s'",$card_price_free,$card_commission_free,$card_commission_type,$reservation_for_board_id);

	$res = mysqli_query(DbCon, $sql);

	if($res == false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:5_2)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	if( $start_real_time_flg == true ){

		$start_real_time = time();

		$sql = sprintf("update reservation_for_board set start_real_time='%s' where id='%s'",$start_real_time,$reservation_for_board_id);

		$res = mysqli_query(DbCon, $sql);

		if($res == false){
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:6)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}else{
		if( ($start_flg == "1") && ($start_real_time_old == "0") ){

			$year = $year_re;
			$month = $month_re;
			$day = $day_re;
			$hour = $start_hour;
			$minute = $start_minute;
			$second = 0;
			$start_real_time = mktime($hour, $minute, $second, $month, $day, $year);

			$sql = sprintf("update reservation_for_board set start_real_time='%s',start_push_user='honbu' where id='%s'",$start_real_time,$reservation_for_board_id);

			$res = mysqli_query(DbCon, $sql);

			if($res == false){
				//ロールバック
				$sql = "rollback";
				mysqli_query(DbCon, $sql);

				$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:6_2)";
				header("Location: ".WWW_URL."error.php");
				exit();
			}
		}
	}

	if( $mail_flg == true ){

		$shop_area_name = get_area_name_by_area_common($shop_area);		//エリア名取得 in common/include/shop_area_list.php

		$message_content = "";
		$message_type = "";

		if( $card_flg == "1" ) $pay_type = "カード支払い"; else $pay_type = "現金支払い";

		$price_disp = number_format($price)."円";
		if( $extension == 0 ){
			$course_disp = $course_var."コース";
		}else{
			$course_disp = $course_var."＋".$extension."分コース";
		}

		if( $mail_type == "change" ){

			//変更の場合

			$title_name = "変更";
			if( $card_flg_old == "1" ) $pay_type_old = "カード支払い"; else $pay_type_old = "現金支払い";

			$price_disp_old = number_format($price_old)."円";
			if( $extension == 0 ){
				$course_disp_old = $course_var_old."コース";
			}else{
				$course_disp_old = $course_var_old."＋".$extension."分コース";
			}

$action_type =<<<EOT
{$course_disp_old}/{$price_disp_old}/{$pay_type_old}
から
{$course_disp}/{$price_disp}/{$pay_type}
に変更します
EOT;

			if( $course_var != $course_var_old ){
				$message_content .= $course_var."に変更になりました。";
			}else{
				$message_content .= $course_var."のまま変更はありません。";
			}

			$message_content .= "<br />";

			if( $card_flg_old != $card_flg ){
				$message_content .= $pay_type."に変更になりました。";
			}else{
				$message_content .= $pay_type."のまま変更はありません。";
			}

			$message_type = "mail_change";

		}else{
			//スタートの場合
			$title_name = "スタート";

$action_type =<<<EOT
{$course_disp}/{$price_disp}/{$pay_type}
スタートします
EOT;

			$message_content .= sprintf("%s様(%s)%s　スタートします",$customer_name,$area_name,$course_disp);

			$message_type = "mail_start";
		}

		$eigyou_day_data = get_eigyou_day_common();	//営業年月日取得
		$eigyou_year = $eigyou_day_data["year"];
		$eigyou_month = $eigyou_day_data["month"];
		$eigyou_day = $eigyou_day_data["day"];

		$shift_message_area = get_shift_message_area_common($therapist_id,$eigyou_year,$eigyou_month,$eigyou_day);

		$now = time();
		$staff_type = "2";

		$sql = sprintf("insert into shift_message(created,therapist_id,type,message_type,content,area) values('%s','%s','%s','%s','%s','%s')",$now,$therapist_id,$staff_type,$message_type,$message_content,$shift_message_area);
		$res = mysqli_query(DbCon, $sql);
		if($res == false){
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_common:7)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}

		//メール送信
		mb_language("ja");
		mb_internal_encoding("UTF-8");
		$mailto = MAIL_mainUSER;
		//$mailto = "minamikawa@neo-gate.jp";

		$title = sprintf("%s連絡：%s：%s　%s：%s",$title_name,$shop_area_name,$therapist_name,$now_hour_mail,$now_minute_mail);

		$content =<<<EOT
{$customer_name}様（{$area_name}）

{$action_type}

{$check_url}

EOT;

		$header = "From: " . MAIL_mainUSER . "\n";
		//$header .= "Bcc: minamikawa@neo-gate.jp";

		$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);
		if( $result == false ){
			//二回メールを送るので、トランザクションの対象とはしない
		}

		if( ($shop_name=="yokohama") || ($shop_name=="sapporo") || ($shop_name=="osaka") ){

			if( $bcc_add != "" ){

				//あらためてメール送信

				$mailto = MAIL_mainUSER;

				$title = "[業務連絡BBS]投稿のお知らせ";

$content =<<<EOT
新しいメッセージがあります。確認してください。

{$bbs_common_url}

EOT;

				$header = "From: " . MAIL_mainUSER . "\n";
				//$header .= "Bcc: minamikawa@neo-gate.jp";

				$header .= ",";
				$header .= $bcc_add;

				$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

				if( $result == false ){
					//二回メールを送るので、トランザクションの対象とはしない
				}
			}
		}
	}

	//コミット
	$sql = "commit";
	mysqli_query(DbCon, $sql);

	//戻す
	$sql = "set autocommit = 1";
	mysqli_query(DbCon, $sql);

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}

//----予約登録
function update_reservation_data_transaction_2_common(
$start_hour,$start_minute,$end_hour,$end_minute,$reservation_for_board_id,$therapist_id,$attendance_id,
$shop_area,$place_name,$course,$extension,$card_flg,$card_confirm_flg,$complete_flg,$start_flg,
$start_real_time_flg,$mail_flg,$mail_type,$customer_name,$check_url,
$shop_name,$course_old,$course_new,$extension_old,$extension_new,$course_change_flg,$extension_change_flg,
$course_var,$other_data,$shimei_flg,$shimei_change_flg){

	$card_price_free = $other_data["card_price_free"];
	$card_commission_free = $other_data["card_commission_free"];
	$card_commission_type = $other_data["card_commission_type"];

	$bbs_common_url = get_bbs_common_url_common();	//BBSのURL取得

	if( $attendance_id != "-1" ){
	    $therapist_id = get_therapist_id_by_attendance_id_common($attendance_id);	//出勤データIDからセラピストIDを取得
	}
	else{
	    $therapist_id = "-1";
	}

	$therapist_name = get_therapist_name_by_therapist_id_honmyou_common($therapist_id);		//セラピスト名取得(本名)
	$now = time();
	$now_hour = intval(date('H', $now));
	$now_minute = intval(date('i', $now));
	$now_hour_mail = hour_plus_24_common($now_hour);
	$now_minute_mail = minute_zero_add_common($now_minute);
	$area_name = $place_name;
	$customer_name = preg_replace("/[\(（].*/u","",$customer_name);

	$reservation_no = get_reservation_no_by_reservation_for_board_id_common($reservation_for_board_id);		//予約番号取得

	$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)

	if( $extension_change_flg == true ){

		$price = get_change_price_extension_common($shop_name,$extension_old,$extension_new,$price);	//料金取得（超過料金含む）

		$extension = $extension_new;

	}

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得

	$discount = $data["discount"];
	$card_flg_old = $data["card_flg"];
	$course_var_old = $data["course_var"];
	$start_real_time_old = $data["start_real_time"];
	$year_re = $data["year"];
	$month_re = $data["month"];
	$day_re = $data["day"];

	$price_old = get_price_by_reservation_for_board_id_common($reservation_for_board_id);	//料金取得

	//札幌リフレその他、割引が「なし」ではない場合、コースの変更に応じて割引も変化しなければいけない
	$discount_new = get_discount_new($discount,$shop_name,$course_var);

	if( $course_change_flg == true ){

		$price = get_change_price_course_common($shop_name,$course_var,$reservation_for_board_id,$discount_new);	//料金取得

	}

	$bcc_add = get_bcc_add_shift_common($shop_area);	//BCCのメールリスト文字列取得

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysqli_query(DbCon, $sql);

	//トランザクション開始
	$sql = "begin";
	mysqli_query(DbCon, $sql);

    //売り上げ情報変更
	if($shimei_change_flg == true){
        if($shimei_flg=="1"){
            $price += 1000;
        }else{
            $price -=1000;
        }
    }

	$sql = sprintf("update sale_history set price='%s',therapist_id='%s' where reservation_no='%s'",$price,$therapist_id,$reservation_no);
	$res = mysqli_query(DbCon, $sql);
	if($res == false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:0)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$reservation_time_data = change_to_reservation_time_common($start_hour,$start_minute,$end_hour,$end_minute);
	$update_start_time = $reservation_time_data["start_time"];
	$update_end_time = $reservation_time_data["end_time"];

	$sql = sprintf("delete from reservation_new where reservation_for_board_id='%s'",$reservation_for_board_id);
	$res = mysqli_query(DbCon, $sql);
	if($res == false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	if( $attendance_id != "-1" ){

		//そして改めて登録

		$update_start_time = $update_start_time - 1;
		$update_end_time = $update_end_time + 1;

		for( $i = $update_start_time; $i <= $update_end_time; $i++ ){

			if( ($i == $update_start_time) || ($i == $update_end_time) ){

				$sql = sprintf("
insert into reservation_new(attendance_id,time,reservation_for_board_id,auto_flg,area)
values('%s','%s','%s','%s','%s')",
$attendance_id,$i,$reservation_for_board_id,1,$shop_area);

			}else{

				$sql = sprintf("
insert into reservation_new(attendance_id,time,reservation_for_board_id,area)
values('%s','%s','%s','%s')",
$attendance_id,$i,$reservation_for_board_id,$shop_area);

			}

			$res = mysqli_query(DbCon, $sql);
			if($res == false){
				//ロールバック
				$sql = "rollback";
				mysqli_query(DbCon, $sql);

				$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:2)";
				header("Location: ".WWW_URL."error.php");
				exit();
			}
		}

		//前後が二時間以内の場合、そこも予約
		$before_latest_end_time = get_before_latest_end_time_common($attendance_id,$update_start_time,$shop_area);
		$after_first_start_time = get_after_first_start_time_common($attendance_id,$update_end_time,$shop_area);
		$sa = $update_start_time - $before_latest_end_time;

		if( $sa < 5 ){
			for( $x = ( $before_latest_end_time + 0 ); $x < $update_start_time; $x++ ){

				$sql = sprintf("
insert into reservation_new(attendance_id,time,reservation_for_board_id,auto_flg,area)
values('%s','%s','%s','%s','%s')",
$attendance_id,$x,$reservation_for_board_id,1,$shop_area);

				$res = mysqli_query(DbCon, $sql);
				if($res == false){
					//ロールバック
					$sql = "rollback";
					mysqli_query(DbCon, $sql);

					$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:3)";
					header("Location: ".WWW_URL."error.php");
					exit();
				}
			}
		}

		if( $after_first_start_time != "-1" ){
			$sa = $after_first_start_time - $update_end_time;
			if( $sa < 5 ){
				for( $x = ( $update_end_time + 1 ); $x < $after_first_start_time; $x++ ){

					$sql = sprintf("
insert into reservation_new(attendance_id,time,reservation_for_board_id,auto_flg,area)
values('%s','%s','%s','%s','%s')",
$attendance_id,$x,$reservation_for_board_id,1,$shop_area);

					$res = mysqli_query(DbCon, $sql);
					if($res == false){
						//ロールバック
						$sql = "rollback";
						mysqli_query(DbCon, $sql);

						$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:4)";
						header("Location: ".WWW_URL."error.php");
						exit();
					}
				}
			}
		}

	}

	$start_hour = hour_change_over_24_common($start_hour);
	$end_hour = hour_change_over_24_common($end_hour);

	$time_len = get_time_len_new_common($start_hour,$start_minute,$end_hour,$end_minute);

	if( $therapist_id == "-1" ){

		$sql = sprintf("
update reservation_for_board set
start_hour='%s',start_minute='%s',end_hour='%s',end_minute='%s',time_len='%s',area_name='%s',
attendance_id='-1',course='%s',extension='%s',card_flg='%s',card_confirm_flg='%s',
complete_flg='%s',start_flg='%s',card_price_free='%s',card_commission_free='%s',card_commission_type='%s',shimei_flg='%s'
where id='%s'",
$start_hour,$start_minute,$end_hour,$end_minute,$time_len,$place_name,
$course,$extension,$card_flg,$card_confirm_flg,
$complete_flg,$start_flg,$card_price_free,$card_commission_free,$card_commission_type,$shimei_flg,
$reservation_for_board_id);

	}else{

		//course_var,discountも更新

		$sql = sprintf("
update reservation_for_board set
start_hour='%s',start_minute='%s',end_hour='%s',end_minute='%s',time_len='%s',area_name='%s',
attendance_id='%s',course='%s',course_var='%s',extension='%s',card_flg='%s',card_confirm_flg='%s',complete_flg='%s',
start_flg='%s',discount='%s',card_price_free='%s',card_commission_free='%s',card_commission_type='%s',shimei_flg='%s'
where id='%s'",
$start_hour,$start_minute,$end_hour,$end_minute,$time_len,$place_name,$attendance_id,
$course,$course_var,$extension,$card_flg,$card_confirm_flg,$complete_flg,$start_flg,
$discount_new,$card_price_free,$card_commission_free,$card_commission_type,$shimei_flg,
$reservation_for_board_id);

	}

	$res = mysqli_query(DbCon, $sql);

	if($res == false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:5)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	if( $start_real_time_flg == true ){

		$start_real_time = time();

		$sql = sprintf("update reservation_for_board set start_real_time='%s' where id='%s'",$start_real_time,$reservation_for_board_id);
		$res = mysqli_query(DbCon, $sql);

		if($res == false){
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:6)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}

	}else{

		if( ($start_flg == "1") && ($start_real_time_old == "0") ){

			$year = $year_re;
			$month = $month_re;
			$day = $day_re;
			$hour = $start_hour;
			$minute = $start_minute;
			$second = 0;
			$start_real_time = mktime($hour, $minute, $second, $month, $day, $year);

			$sql = sprintf("update reservation_for_board set start_real_time='%s',start_push_user='honbu' where id='%s'",$start_real_time,$reservation_for_board_id);
			$res = mysqli_query(DbCon, $sql);
			if($res == false){

				//ロールバック
				$sql = "rollback";
				mysqli_query(DbCon, $sql);

				$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:6_2)";
				header("Location: ".WWW_URL."error.php");
				exit();

			}
		}
	}

	if( $mail_flg == true ){

		$shop_area_name = get_area_name_by_area_common($shop_area);		//エリア名取得 in common/include/shop_area_list.php

		$message_content = "";
		$message_type = "";

		if( $card_flg == "1" ){
			$pay_type = "カード支払い";
		}else{
			$pay_type = "現金支払い";
		}
		$price_disp = number_format($price)."円";
		if( $extension == 0 ){
			$course_disp = $course_var."コース";
		}else{
			$course_disp = $course_var."＋".$extension."分コース";
		}

		if( $mail_type == "change" ){

			//変更の場合

			$title_name = "変更";
			if( $card_flg_old == "1" ){
				$pay_type_old = "カード支払い";
			}else{
				$pay_type_old = "現金支払い";
			}
			$price_disp_old = number_format($price_old)."円";
			if( $extension == 0 ){
				$course_disp_old = $course_var_old."コース";
			}else{
				$course_disp_old = $course_var_old."＋".$extension."分コース";
			}

$action_type =<<<EOT
{$course_disp_old}/{$price_disp_old}/{$pay_type_old}
から
{$course_disp}/{$price_disp}/{$pay_type}
に変更します
EOT;

			if( $course_var != $course_var_old ){

				$message_content .= $course_var."に変更になりました。";

			}else{

				$message_content .= $course_var."のまま変更はありません。";

			}

			$message_content .= "<br />";

			if( $card_flg_old != $card_flg ){

				$message_content .= $pay_type."に変更になりました。";

			}else{

				$message_content .= $pay_type."のまま変更はありません。";

			}

			$message_type = "mail_change";

		}else{

			//スタートの場合

			$title_name = "スタート";

$action_type =<<<EOT
{$course_disp}/{$price_disp}/{$pay_type}
スタートします
EOT;

			$message_content .= sprintf("%s様(%s)%s　スタートします",$customer_name,$area_name,$course_disp);

			$message_type = "mail_start";
		}

		$eigyou_day_data = get_eigyou_day_common();	//営業年月日取得
		$eigyou_year = $eigyou_day_data["year"];
		$eigyou_month = $eigyou_day_data["month"];
		$eigyou_day = $eigyou_day_data["day"];

		$shift_message_area = get_shift_message_area_common($therapist_id,$eigyou_year,$eigyou_month,$eigyou_day);

		$now = time();
		$staff_type = "2";

		$sql = sprintf("
insert into shift_message(created,therapist_id,type,message_type,content,area) values('%s','%s','%s','%s','%s','%s')",
$now,$therapist_id,$staff_type,$message_type,$message_content,$shift_message_area);

		$res = mysqli_query(DbCon, $sql);

		if($res == false){

			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_data_transaction_2_common:7)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		//メール送信
		mb_language("ja");
		mb_internal_encoding("UTF-8");
		$mailto = MAIL_mainUSER;

$title = sprintf("
%s連絡：%s：%s　%s：%s",
$title_name,$shop_area_name,$therapist_name,$now_hour_mail,$now_minute_mail);

$content =<<<EOT
{$customer_name}様（{$area_name}）

{$action_type}

{$check_url}

EOT;

		$header = "From: " . MAIL_mainUSER . "\n";
		//$header .= "Bcc: minamikawa@neo-gate.jp";

		$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

		if( $result == false ){

			//二回メールを送るので、トランザクションの対象とはしない

		}

		if( ($shop_name=="yokohama") || ($shop_name=="sapporo") || ($shop_name=="osaka") ){

			if( $bcc_add != "" ){

				//あらためてメール送信

				$mailto = MAIL_mainUSER;

				$title = "[業務連絡BBS]投稿のお知らせ";

$content =<<<EOT
新しいメッセージがあります。確認してください。

{$bbs_common_url}

EOT;

				$header = "From: " . MAIL_mainUSER . "\n";
				//$header .= "Bcc: minamikawa@neo-gate.jp";

				$header .= ",";
				$header .= $bcc_add;

				$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

				if( $result == false ){
					//二回メールを送るので、トランザクションの対象とはしない
				}
			}
		}
	}

	//コミット
	$sql = "commit";
	mysqli_query(DbCon, $sql);

	//戻す
	$sql = "set autocommit = 1";
	mysqli_query(DbCon, $sql);

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}

//----セラピストページ登録しメールする
function update_therapist_page_tmp_shift_hp_common($therapist_id,$skill_string,$skill_2_string,$shikaku,$pr,$area){

	//セラピストページ(仮)から取得
	$data = get_therapist_page_tmp_common($therapist_id);

	if( $data["id"] == "" ){
		//インサート
		insert_therapist_page_tmp_common($therapist_id,$skill_string,$skill_2_string,$shikaku,$pr);
	}else{
		//アップデート
		update_therapist_page_tmp_common($therapist_id,$skill_string,$skill_2_string,$shikaku,$pr);	//セラピストページ更新
	}

	$check_url = sprintf("%sman/shift/index.php?area=%s&id=%s",REFLE_WWW_URL,$area,$therapist_id);

	$therapist_name = get_therapist_name_by_therapist_id_honmyou_common($therapist_id);		//セラピスト名取得(本名)

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("セラピストHP変更の連絡：%s",$therapist_name);

	$content =<<<EOT

{$therapist_name}さんのセラピストHPが変更されました。

{$check_url}

※管理ページで承認後、反映されます。

EOT;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();
}

//----顧客評価情報メール送信
function send_mail_customer_evaluation_common($customer_id,$reservation_for_board_id,$skill,$service,$publish_allow_therapist){

	$customer_name = get_customer_name_by_id_common($customer_id);	//顧客名取得
	$customer_name .= "様";

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得
	$data = get_disp_data_for_vip_voice_common($data);	//お客様の声表示用データ取得

	$disp_day = $data["disp_day"];
	$attendance_id = $data["attendance_id"];
	$shimei_flg = $data["shimei_flg"];

	$therapist_name = get_therapist_name_real_by_attendance_id_common($attendance_id);	//該当出勤データのセラピスト名取得

	$mail_disp_1 = sprintf("%s　%s",$disp_day,$customer_name);

	if( $shimei_flg == "1" ){

		$mail_disp_1 .= "　【指名】";

	}

	if( $publish_allow_therapist == "1" ){

		$mail_disp_2 = "◯評価を担当セラピストに開示する";

	}else{

		$mail_disp_2 = "×評価を担当セラピストに開示する";

	}

	$mail_title = sprintf("お客様より評価登録：%s",$therapist_name);

$mail_content=<<<EOT

{$mail_disp_1}
技術評価：{$skill}
接客評価：{$service}
ーーー
{$mail_disp_2}

EOT;

	$mail_parameter = MAIL_mainUSER;

	$parameter = "-f ".$mail_parameter;

	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$title = $mail_title;
	$content =$mail_content;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,$parameter);

	return true;
	exit();
}

//----顧客の声メール送信
function send_mail_customer_voice_common($reservation_for_board_id,$voice_content,$publish_allow_therapist,$publish_allow_site,$customer_id){

	$customer_name = get_customer_name_by_id_common($customer_id);		//顧客名取得
	$customer_name .= "様";

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得
	$data = get_disp_data_for_vip_voice_common($data);	//お客様の声表示用データ取得

	$disp_day = $data["disp_day"];
	$attendance_id = $data["attendance_id"];
	$shimei_flg = $data["shimei_flg"];

	$tmp = get_therapist_data_by_attendance_id_common($attendance_id);	//出勤データのセラピスト情報取得

	$therapist_name = $tmp["name"];
	$therapist_mail = $tmp["mail"];
	$therapist_id = $tmp["id"];
	$therapist_area = $tmp["area"];
	$for_kobetsu_url = $tmp["for_kobetsu_url"];

	$therapist_check_url = get_therapist_check_url_for_vip_voice_common($therapist_id,$therapist_area,$for_kobetsu_url);

	$mail_disp_1 = sprintf("%s　%s",$disp_day,$customer_name);

	if( $shimei_flg == "1" ){

		$mail_disp_1 .= "　【指名】";

	}

	if( $publish_allow_site == "1" ){

		$mail_disp_3 = "◯感想をお客様の声にアップしても良い";

$mail_add=<<<EOT

※管理ページで公開、編集ができます

EOT;

	}else{

		$mail_disp_3 = "×感想をお客様の声にアップしても良い";

		$mail_add = "";

	}

	$mail_title = sprintf("お客様よりお礼メール：%s",$therapist_name);

$mail_content=<<<EOT

{$mail_disp_1}
{$voice_content}

ーーー
{$mail_disp_3}
{$mail_add}
EOT;

	$mail_parameter = MAIL_mainUSER;

	$parameter = "-f ".$mail_parameter;

	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$title = $mail_title;
	$content =$mail_content;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,$parameter);

	return true;
	exit();
}

//----顧客の声メール送信(セラピストよりお礼メール)
function send_mail_customer_voice_therapist_common($reservation_for_board_id,$thanks_content,$therapist_id){

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得
	$data = get_disp_data_for_vip_voice_common($data);	//お客様の声表示用データ取得

	$disp_day = $data["disp_day"];
	$attendance_id = $data["attendance_id"];
	$shimei_flg = $data["shimei_flg"];
	$customer_id = $data["customer_id"];

	if( $customer_id == "-1" ){

		return true;
		exit();

	}

	$tmp = get_therapist_data_by_attendance_id_common($attendance_id);	//出勤データのセラピスト情報取得

	$therapist_name = $tmp["name"];

	$customer_name = get_customer_name_by_id_common($customer_id);		//顧客名取得
	$customer_name .= "様";

	$mail_disp_1 = sprintf("%s　%s",$disp_day,$customer_name);

	if( $shimei_flg == "1" ){

		$mail_disp_1 .= "　【指名】";

	}

	$mail_title = sprintf("セラピストよりお礼メール：%s",$therapist_name);

$mail_content=<<<EOT

{$mail_disp_1}
{$thanks_content}

EOT;

	$mail_parameter = MAIL_mainUSER;

	$parameter = "-f ".$mail_parameter;

	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$title = $mail_title;
	$content =$mail_content;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,$parameter);

	return true;
	exit();
}

//----セラピストヒアリングメール送信
function send_mail_customer_hearing_therapist_common($reservation_for_board_id,$hearing_content,$therapist_id,$url){

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得
	$shop_name = $data["shop_name"];
	$data = get_disp_data_for_vip_voice_common($data);	//お客様の声表示用データ取得

	$disp_day = $data["disp_day"];
	$attendance_id = $data["attendance_id"];
	$shimei_flg = $data["shimei_flg"];
	$customer_id = $data["customer_id"];

	if( $customer_id == "-1" ){
		return true;
		exit();
	}

	$tmp = get_therapist_data_by_attendance_id_common($attendance_id);	//出勤データのセラピスト情報取得

	$therapist_name = $tmp["name"];

	$customer_name = get_customer_name_by_id_common($customer_id);	//顧客名取得
	//$customer_name = get_customer_name_by_tel_common($customer_tel);	//顧客名取得
	$customer_name .= "様";

	$mail_disp_1 = sprintf("%s　%s",$disp_day,$customer_name);

	if( $shimei_flg == "1" ){

		$mail_disp_1 .= "　【指名】";

	}

	//$mail_title = sprintf("セラピストよりヒアリングシート：%s",$therapist_name);
	$mail_title = sprintf("ヒアリング：%s【%s】%s：%s",$shop_name,$therapist_name,$disp_day,$customer_name);

$mail_content=<<<EOT

{$mail_disp_1}
{$hearing_content}
http://www.tokyo-refle.com{$url}

EOT;

	$mail_parameter = MAIL_mainUSER;

	$parameter = "-f ".$mail_parameter;

	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$title = $mail_title;
	$content =$mail_content;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,$parameter);

	return true;
	exit();

}

//----お礼メッセージメール送信
function send_mail_remind_therapist_thanks_common($year,$month,$day,$therapist_id,$therapist_name,$content_therapist){

	$therapist_mail = get_therapist_mail_common($therapist_id);

	$ch = get_therapist_for_kobetsu_url_common($therapist_id);

	$refle_url = get_refle_url_common();

	$check_url = sprintf("%sshift/customer_voice.php?area=%s&id=%s&ch=%s",$refle_url,$area,$therapist_id,$ch);

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $therapist_mail;
	//$mailto = "minamikawa@neo-gate.jp";

	$title = "";
	//$title = "(TEST)";

$title .=<<<EOT
{$month}/{$day}お礼メッセージが未記入です
EOT;

$content =<<<EOT

{$therapist_name}さん

お疲れ様です。
以下の施術のお礼メッセージが未記入ですので、至急登録をお願いします。

{$content_therapist}

{$check_url}

EOT;

	$header = "From: " . MAIL_mainUSER . "\n";
	$header .= "Bcc: " . MAIL_mainUSER;

	/*
	$header .= "Bcc: minamikawa@neo-gate.jp";
	$header .= ",";
	$header .= MAIL_mainUSER;
	*/

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();

}

//----ドライバー承認処理
function update_man_driver_syounin_check_common($area,$staff_id,$syounin,$fusyounin,$shimekiri){

	$syounin_num = count($syounin);
	$fusyounin_num = count($fusyounin);
	$shimekiri_num = count($shimekiri);

	if( ($syounin_num == 1) && ($fusyounin_num == 0) && ($shimekiri_num == 0) ){

		update_driver_syounin_check_add_common($area,$staff_id,$syounin);	//出勤データ更新（スタッフ）承認処理

		return true;
		exit();
	}

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$staff_mail = get_staff_mail_common($staff_id);			//スタッフのメールアドレス取得

	$check_url = get_check_url_driver_front_common($area,$staff_id);	//ドライバー専用ページURL取得

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysqli_query(DbCon, $sql);

	//トランザクション開始
	$sql = "begin";
	mysqli_query(DbCon, $sql);

	$first_flg = false;
	$mail_month = "";
	$mail_day = "";

	for($i=0;$i<$syounin_num;$i++){

		$val = $syounin[$i];
		$pieces = explode("_", $val);
		$year = $pieces[0];
		$month = $pieces[1];
		$day = $pieces[2];

		if( $first_flg == false ){

			$mail_month = $month;
			$mail_day = $day;
			$first_flg = true;

		}

		$syounin_state = 1;

		$sql = sprintf("update attendance_staff_new set syounin_state='%s' where staff_id='%s' and year='%s' and month='%s' and day='%s'",$syounin_state,$staff_id,$year,$month,$day);
		$res = mysqli_query(DbCon, $sql);
		if($res == false){
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_man_driver_syounin_check_common)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}

	for($i=0;$i<$fusyounin_num;$i++){

		$val = $fusyounin[$i];
		$pieces = explode("_", $val);
		$year = $pieces[0];
		$month = $pieces[1];
		$day = $pieces[2];

		if( $first_flg == false ){

			$mail_month = $month;
			$mail_day = $day;
			$first_flg = true;

		}

		$syounin_state = 2;

		$sql = sprintf("update attendance_staff_new set syounin_state='%s' where staff_id='%s' and year='%s' and month='%s' and day='%s'",$syounin_state,$staff_id,$year,$month,$day);
		$res = mysqli_query(DbCon, $sql);
		if($res == false){
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_man_driver_syounin_check_common)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}

	for($i=0;$i<$shimekiri_num;$i++){

		$val = $shimekiri[$i];
		$pieces = explode("_", $val);
		$year = $pieces[0];
		$month = $pieces[1];
		$day = $pieces[2];

		if( $first_flg == false ){

			$mail_month = $month;
			$mail_day = $day;
			$first_flg = true;

		}

		$syounin_state = 3;

		$sql = sprintf("update attendance_staff_new set syounin_state='%s' where staff_id='%s' and year='%s' and month='%s' and day='%s'",$syounin_state,$staff_id,$year,$month,$day);
		$res = mysqli_query(DbCon, $sql);
		if($res == false){
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_man_driver_syounin_check_common)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $staff_mail;
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("【シフト登録】%s月%s日ほかシフト承認[%s]",$mail_month,$mail_day,$staff_name);

	$content =<<<EOT
{$staff_name}さん

{$mail_month}月{$mail_day}日ほかのシフト登録が確定しました。

{$check_url}
EOT;

	$header = "From: " . MAIL_mainUSER . "\n";
	$header .= "Bcc: " . MAIL_mainUSER;
	$header .= ",";
	//$header .= "minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_man_driver_syounin_check_common)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	//コミット
	$sql = "commit";
	mysqli_query(DbCon, $sql);

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}

//----ドライバー承認処理
function update_man_driver_syounin_check_edit_common($area,$staff_id,$syounin){

	$syounin_num = count($syounin);

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$staff_mail = get_staff_mail_common($staff_id);			//スタッフのメールアドレス取得

	$check_url = get_check_url_driver_front_common($area,$staff_id);	//ドライバー専用ページURL取得

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysqli_query(DbCon, $sql);

	//トランザクション開始
	$sql = "begin";
	mysqli_query(DbCon, $sql);

	$first_year = 0;
	$first_month = 0;
	$first_day = 0;

	for($i=0;$i<$syounin_num;$i++){

		$val = $syounin[$i];
		$pieces = explode("_", $val);
		$syori_type = $pieces[0];
		$year = $pieces[1];
		$month = $pieces[2];
		$day = $pieces[3];

		if( $i == 0 ){

			$first_year = $year;
			$first_month = $month;
			$first_day = $day;

		}

		if( $syori_type == "henkou" ){

			$syounin_state = 1;

			$sql = sprintf("update attendance_staff_new set syounin_state='%s',shift_change_flg='0' where staff_id='%s' and year='%s' and month='%s' and day='%s'",$syounin_state,$staff_id,$year,$month,$day);

			$res = mysqli_query(DbCon, $sql);
			if($res == false){
				//ロールバック
				$sql = "rollback";
				mysqli_query(DbCon, $sql);
				$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_man_driver_syounin_check_edit_common)";
				header("Location: ".WWW_URL."error.php");
				exit();
			}

		}else if( $syori_type == "kekkin" ){

			$syounin_state = 1;
			$kekkin_flg = 1;

			$sql = sprintf("update attendance_staff_new set syounin_state='%s',kekkin_flg='%s',shift_change_flg='0' where staff_id='%s' and year='%s' and month='%s' and day='%s'",$syounin_state,$kekkin_flg,$staff_id,$year,$month,$day);

			$res = mysqli_query(DbCon, $sql);
			if($res == false){
				//ロールバック
				$sql = "rollback";
				mysqli_query(DbCon, $sql);
				$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_man_driver_syounin_check_edit_common)";
				header("Location: ".WWW_URL."error.php");
				exit();
			}
		}else{
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_man_driver_syounin_check_edit_common)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $staff_mail;
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("【シフト登録】%s月%s日他シフト変更・欠勤承認[%s]",$first_month,$first_day,$staff_name);

$content =<<<EOT
{$staff_name}さん

{$first_month}月{$first_day}日他のシフト変更・欠勤を確認し了承いたしました。

{$check_url}
EOT;

	$header = "From: " . MAIL_mainUSER . "\n";
	$header .= "Bcc: " . MAIL_mainUSER;
	$header .= ",";
	//$header .= "minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_man_driver_syounin_check_edit_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	//コミット
	$sql = "commit";
	mysqli_query(DbCon, $sql);

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}

//----出勤データ更新（スタッフ）承認処理
function update_driver_syounin_check_add_common($area,$staff_id,$syounin){

	$syounin_num = count($syounin);

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$staff_mail = get_staff_mail_common($staff_id);			//スタッフのメールアドレス取得

	$check_url = get_check_url_driver_front_common($area,$staff_id);	//ドライバー専用ページURL取得

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysqli_query(DbCon, $sql);

	//トランザクション開始
	$sql = "begin";
	mysqli_query(DbCon, $sql);

	for($i=0;$i<$syounin_num;$i++){

		$val = $syounin[$i];
		$pieces = explode("_", $val);
		$year = $pieces[0];
		$month = $pieces[1];
		$day = $pieces[2];

		$syounin_state = 1;

		$sql = sprintf("update attendance_staff_new set syounin_state='%s' where staff_id='%s' and year='%s' and month='%s' and day='%s'",$syounin_state,$staff_id,$year,$month,$day);
		$res = mysqli_query(DbCon, $sql);
		if($res == false){
			//ロールバック
			$sql = "rollback";
			mysqli_query(DbCon, $sql);
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_driver_syounin_check_add_common)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $staff_mail;
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("【シフト登録】%s月%s日シフト承認[%s]",$month,$day,$staff_name);

$content =<<<EOT
{$staff_name}さん

{$month}月{$day}日のシフト登録が確定しました。

{$check_url}
EOT;

	$header = "From: " . MAIL_mainUSER . "\n";
	$header .= "Bcc: " . MAIL_mainUSER;
	$header .= ",";
	//$header .= "minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){
		//ロールバック
		$sql = "rollback";
		mysqli_query(DbCon, $sql);
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_driver_syounin_check_add_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	//コミット
	$sql = "commit";
	mysqli_query(DbCon, $sql);

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}

//----スタッフTMPデータ更新
function update_staff_tmp_common($staff_id,$car_type,$car_color,$car_number,$tel,$car_image_url,$area){

	$check_url = get_check_url_driver_man($area,$staff_id);		//???? 存在確認出来ない by aida

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$result = check_exist_staff_tmp_common($staff_id);		//スタッフTMPデータ有無

	if( $result == true ){
		$sql = sprintf("update staff_tmp set car_type='%s',car_color='%s',car_number='%s',tel='%s',car_image_url='%s' where delete_flg=0 and staff_id='%s'",$car_type,$car_color,$car_number,$tel,$car_image_url,$staff_id);
	}else{
		$sql = sprintf("insert into staff_tmp(car_type,car_color,car_number,tel,car_image_url,staff_id) values('%s','%s','%s','%s','%s','%s')",$car_type,$car_color,$car_number,$tel,$car_image_url,$staff_id);
	}
	$res = mysqli_query(DbCon, $sql);

	if($res == false){
		echo "error!(update_staff_tmp_common)";
		exit();
	}

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("ドライバー情報変更の連絡：%s",$staff_name);

$content =<<<EOT

{$staff_name}さんのドライバー情報が変更されました。

{$check_url}

※管理ページで承認後、反映されます。

EOT;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();
}

//----ドライバーメール送信
function send_mail_for_driver_page_1_common($staff_id,$message_id,$ch,$area){

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$area_name = get_area_name_by_area_common($area);		//エリア名取得 in common/include/shop_area_list.php

	$tmp = get_board_message_history_by_id_common($message_id);	//ボードメッセージ歴データ取得

	$message_title = $tmp["title"];
	$message_content = $tmp["content"];

	$check_url = sprintf("%sman/driver/instruction.php?id=%s",WWW_URL_TOKYO,$staff_id);

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("[%s][%s]【了解】「%s」",$area_name,$staff_name,$message_title);

$content =<<<EOT

{$message_content}

{$check_url}

EOT;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();

}

//----ドライバーメール送信２
function send_mail_for_driver_page_2_common($staff_id,$message_content,$area){

	$hour = intval(date('H'));
	$minute = add_zero_when_under_ten_common(intval(date('i')));	//数値が１桁の時前ゼロを付加し２ケタ文字列にする

	$time_disp = sprintf("%s:%s",$hour,$minute);

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$area_name = get_area_name_by_area_common($area);		//エリア名取得 in common/include/shop_area_list.php

	$check_url = sprintf("%sman/driver/instruction.php?id=%s",WWW_URL_TOKYO,$staff_id);

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("[%s][%s]【コメント】　[%s]",$area_name,$staff_name,$time_disp);

$content =<<<EOT

{$message_content}

{$check_url}

EOT;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();

}

//----ドライバーメール送信３
function send_mail_for_driver_page_3_common(
$staff_id,$area,$type,$state,$reservation_for_board_id,$hour_hiki,$minute_hiki){

	$hour = intval(date('H'));
	$minute = add_zero_when_under_ten_common(intval(date('i')));	//数値が１桁の時前ゼロを付加し２ケタ文字列にする

	$time_disp = sprintf("%s:%s",$hour,$minute);

	$minute_hiki = add_zero_when_under_ten_common($minute_hiki);	//数値が１桁の時前ゼロを付加し２ケタ文字列にする

	$time_disp_hiki = sprintf("%s:%s",$hour_hiki,$minute_hiki);

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$area_name = get_area_name_by_area_common($area);		//エリア名取得 in common/include/shop_area_list.php

	$tmp = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得

	$attendance_id = $tmp["attendance_id"];
	$customer_name = $tmp["customer_name"];
	$start_hour = $tmp["start_hour"];
	$start_minute = add_zero_when_under_ten_common($tmp["start_minute"]);	//数値が１桁の時前ゼロを付加し２ケタ文字列にする
	$end_hour = $tmp["end_hour"];
	$end_minute = add_zero_when_under_ten_common($tmp["end_minute"]);		//数値が１桁の時前ゼロを付加し２ケタ文字列にする

	$yoyaku_time_disp = sprintf("%s時%s分～%s時%s分",$start_hour,$start_minute,$end_hour,$end_minute);

	$therapist_name = get_therapist_name_by_attendance_id_common($attendance_id);	//指定出勤データのセラピスト名取得

	$check_url = sprintf("%sman/driver/instruction.php?id=%s",WWW_URL_TOKYO,$staff_id);

	if( $type == "okuri" ){

		if( $state == "1" ){

			//確認

$title = sprintf("[%s][%s]【確認済み】　[送り]%s %s",$area_name,$staff_name,$therapist_name,$time_disp);

$content =<<<EOT

[送り]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else if( $state == "2" ){

			//着予定

$title = sprintf("[%s][%s]【着予定 %s】　[送り]%s %s",
$area_name,$staff_name,$time_disp_hiki,$therapist_name,$time_disp);

$content =<<<EOT

[送り]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else if( $state == "3" ){

			//到着待機

$title = sprintf("[%s][%s]【到着待機】　[送り]%s %s",
$area_name,$staff_name,$therapist_name,$time_disp);

$content =<<<EOT

[送り]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else if( $state == "9" ){

			//降車

$title = sprintf("[%s][%s]【降車】　[送り]%s %s",
$area_name,$staff_name,$therapist_name,$time_disp);

$content =<<<EOT

[送り]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else{

			echo "error!(send_mail_for_driver_page_3_common)";
			exit();

		}

	}else if( $type == "mukae" ){

		if( $state == "1" ){

			//確認

$title = sprintf("[%s][%s]【確認済み】　[迎え]%s %s",
$area_name,$staff_name,$therapist_name,$time_disp);

$content =<<<EOT

[迎え]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else if( $state == "2" ){

			//着予定

$title = sprintf("[%s][%s]【着予定 %s】　[迎え]%s %s",
$area_name,$staff_name,$time_disp_hiki,$therapist_name,$time_disp);

$content =<<<EOT

[迎え]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else if( $state == "3" ){

			//到着待機

$title = sprintf("[%s][%s]【到着待機】　[迎え]%s %s",
$area_name,$staff_name,$therapist_name,$time_disp);

$content =<<<EOT

[迎え]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else if( $state == "4" ){

			//合流

$title = sprintf("[%s][%s]【合流】　[迎え]%s %s",
$area_name,$staff_name,$therapist_name,$time_disp);

			$content =<<<EOT

[迎え]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else if( $state == "9" ){

			//降車

$title = sprintf("[%s][%s]【降車】　[迎え]%s %s",
$area_name,$staff_name,$therapist_name,$time_disp);

$content =<<<EOT

[迎え]
顧客名：{$customer_name}
セラピスト名：{$therapist_name}
時間：{$yoyaku_time_disp}

{$check_url}

EOT;

		}else{

			echo "error!(send_mail_for_driver_page_3_common)";
			exit();

		}

	}else{

		echo "error!(send_mail_for_driver_page_3_common)";
		exit();

	}

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();

}

//----ドライバーメール送信４
function send_mail_for_driver_page_4_common($staff_id,$area,$type,$comment){

	$hour = intval(date('H'));
	$minute = add_zero_when_under_ten_common(intval(date('i')));	//数値が１桁の時前ゼロを付加し２ケタ文字列にする

	$time_disp = sprintf("%s:%s",$hour,$minute);

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$area_name = get_area_name_by_area_common($area);		//エリア名取得 in common/include/shop_area_list.php

	$therapist_name = get_therapist_name_by_attendance_id_common($attendance_id);	//指定出勤データのセラピスト名取得

	$check_url = sprintf("%sman/driver/instruction.php?id=%s",WWW_URL_TOKYO,$staff_id);

	if( $type == "okuri" ){

		$title = sprintf("[%s][%s]【コメント】　[送り]%s %s",$area_name,$staff_name,$therapist_name,$time_disp);

	}else if( $type == "mukae" ){

		$title = sprintf("[%s][%s]【送信】　[迎え]%s %s",$area_name,$staff_name,$therapist_name,$time_disp);

	}else{

		echo "error!(send_mail_for_driver_page_4_common)";
		exit();

	}

$content =<<<EOT

{$comment}

{$check_url}

EOT;

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();

}

//----ドライバーメール送信５
function send_mail_for_driver_page_5_common($staff_id,$area,$type,$hour,$minute,$state){

	//state
	//1:市ヶ谷、2:渋谷

	if( $state == "1" ){

		$state_name = "市ヶ谷";

	}else if( $state == "2" ){

		$state_name = "渋谷";

	}else{

		$state_name = "不明";

	}

	$minute = add_zero_when_under_ten_common($minute);		//数値が１桁の時前ゼロを付加し２ケタ文字列にする

	$time_disp = sprintf("%s:%s",$hour,$minute);

	$staff_name = get_staff_name_by_id_common($staff_id);	//スタッフ名取得

	$area_name = get_area_name_by_area_common($area);		//エリア名取得 in common/include/shop_area_list.php

	$check_url = sprintf("%sman/driver/instruction.php?id=%s",WWW_URL_TOKYO,$staff_id);

	if( $type == "plans" ){

		//戻り予定

		$title = sprintf("[%s][%s]【戻り予定 %s】　[%s事務所]",$area_name,$staff_name,$time_disp,$state_name);

	}else if( $type == "arrival" ){

		//到着

		$title = sprintf("[%s][%s]【戻り済み】　[%s事務所]",$area_name,$staff_name,$state_name);

	}else{

		echo "error!(send_mail_for_driver_page_5_common)";
		exit();

	}

$content =<<<EOT

{$check_url}

EOT;

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = MAIL_mainUSER;
	//$mailto = "minamikawa@neo-gate.jp";

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();

}

//----ドライバー頁URLメール送信
function send_mail_for_man_driver_page_common($staff_id,$send_content,$send_title){

	$data = get_staff_data_by_id_common($staff_id);	//スタッフ情報取得

	$staff_area = $data["area"];
	$staff_mail = $data["mail"];
	$staff_name = $data["name"];

	$check_url = get_check_url_driver_front_communication_common($staff_id,$staff_area);	//ドライバー頁URL取得

	$area_name = get_area_name_by_area_common($staff_area);		//エリア名取得 in common/include/shop_area_list.php

	$title = sprintf("[%s][%sさん]%s",$area_name,$staff_name,$send_title);

$content =<<<EOT

{$send_content}

{$check_url}

EOT;

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $staff_mail;

	$header = "From: " . MAIL_mainUSER . "\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();

}

//----予約メール送信
function send_reservation_mail_front_common($time,$course,$onamae,$mail,$address,$tel,$renraku,$reservation_day,$therapist_id){

	$access_type = furiwake_common();

	$day_disp = get_day_disp_by_reservation_day_common($reservation_day);
	$day_disp_mail = str_replace("　","",$day_disp);
	if( ($therapist_id == "-1") || ($therapist_id == "") ){
		$therapist_name = "指定セラピストなし";
	}else{
		$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
	}

	$time_disp = get_time_disp_common($time);

	if( $access_type == "sp" ){

		$title = "ご予約フォーム(東京リフレ)【SP】";

	}else{

		$title = "ご予約フォーム(東京リフレ)【PC】";

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $mail;
$content =<<<EOT

ご予約ありがとうございます。東京リフレです。

ご希望頂いた条件にて予約状況を確認し、メールにてご連絡させていただきます。
そのメールにご返信を頂いてご予約完了となります。
メールが送れなかった場合、ご予約時間間際の場合はお電話にてご連絡する場合もございます。
ご予約頂いた情報は以下の通りです。

ご利用日:{$day_disp_mail}
ご利用開始時間:{$time_disp}
ご利用予定コース:{$course}
ご指名セラピスト:{$therapist_name}
お名前:{$onamae}
メールアドレス:{$mail}
電話番号:{$tel}
住所:{$address}
ご要望等:
{$renraku}

以上です。

今後とも出張マッサージ東京リフレを宜しくお願致します。
EOT;

	$header = "From: order@tokyo-refle.com\n";
	$header .= "Bcc: order@tokyo-refle.com";

	$parameter_mail = "order@tokyo-refle.com";
	$parameter="-f ".$parameter_mail;

	mb_send_mail($mailto,$title,$content,$header,$parameter);

	return true;
	exit();
}

//----予約メール送信２
function send_reservation_mail_front_2_common($time,$course,$onamae,$mail,$address,$tel,$renraku,$reservation_day){

	$access_type = furiwake_common();

	$day_disp = get_day_disp_by_reservation_day_common($reservation_day);
	$day_disp_mail = str_replace("　","",$day_disp);

	$time_disp = get_time_disp_common($time);

	if( $access_type == "sp" ){

		$title = "ご予約フォーム(東京リフレ)【SP】";

	}else{

		$title = "ご予約フォーム(東京リフレ)【PC】";

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $mail;
$content =<<<EOT

ご予約ありがとうございます。東京リフレです。

ご希望頂いた条件にて予約状況を確認し、メールにてご連絡させていただきます。
そのメールにご返信を頂いてご予約完了となります。
メールが送れなかった場合、ご予約時間間際の場合はお電話にてご連絡する場合もございます。
ご予約頂いた情報は以下の通りです。

ご利用日:{$day_disp_mail}
ご利用開始時間:{$time_disp}
ご利用予定コース:{$course}
お名前:{$onamae}
メールアドレス:{$mail}
電話番号:{$tel}
住所:{$address}
ご要望等:
{$renraku}

以上です。

今後とも出張マッサージ東京リフレを宜しくお願致します。
EOT;

	$header = "From: order@tokyo-refle.com\n";
	$header .= "Bcc: order@tokyo-refle.com";

	$parameter_mail = "order@tokyo-refle.com";
	$parameter="-f ".$parameter_mail;

	mb_send_mail($mailto,$title,$content,$header,$parameter);

	return true;
	exit();
}
?>
