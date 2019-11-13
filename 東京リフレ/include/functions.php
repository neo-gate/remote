<?php
function furiwake(){

	$header = getallheaders();
	$agent = $header["User-Agent"];
	$ua=$_SERVER['HTTP_USER_AGENT'];

	$type = "pc";

	//スマートフォンの振替処理
	if((strpos($ua,'iPhone')!==false)||(strpos($ua,'iPod')!==false)||(strpos($ua,'Android')!==false)) {

		$type = "sp";

	//携帯電話の振替処理
	}else if((preg_match("/DoCoMo/",$agent)) || (preg_match("/^UP.Browser|^KDDI/", $agent)) || (preg_match("/^J-PHONE|^Vodafone|^SoftBank/", $agent))){

		$type = "m";

	}

	return $type;
	exit();

}

function get_blog_data(){

	// DBに接続
	//include(INC_PATH."/db_connect.php");

	$con = mysql_connect("localhost", "neogate", "neogate");
	mysql_select_db("staffblog", $con);
	mysql_query("SET NAMES utf8");

	//ブログ最新データ
	$sql = "select ID,post_title from wp_blog_posts where post_status='publish' order by post_date desc limit 0,3";
	$res = mysql_query($sql, $con);
	if($res == false){
	}
	$i=0;
	$blog_data = array();
	while($row = mysql_fetch_assoc($res)){
		$blog_data[$i++] = $row;
	}

	return $blog_data;
	exit();

}

//タイトルブロックのランダム表示を取得
function get_random_title_block(){

	$file_array = array(
		1=>"title_about_refle.tpl",
		2=>"title_area.tpl",
		3=>"title_aromade.tpl",
		4=>"title_ashigatsuka.tpl",
		5=>"title_eikokushiki.tpl",
		6=>"title_therapist_quo.tpl",
		7=>"title_goriyou.tpl",
		8=>"title_hitorihitori.tpl",
		9=>"title_hotel_to_customer.tpl",
		10=>"title_katayakoshi.tpl",
		11=>"title_menu.tpl",
		12=>"title_menu4.tpl",
		13=>"title_quality.tpl",
		14=>"title_relax.tpl",
		15=>"title_ryoukin.tpl",
		16=>"title_shitsumon.tpl",
		17=>"title_standard.tpl",
		18=>"title_syuttyou.tpl",
		19=>"title_therapist_info.tpl",
		20=>"title_therapist_quo.tpl"
	);

	$rand_num_1 = 0;
	$rand_num_2 = 0;
	$rand_num_3 = 0;

	do{

		$rand_num_1 = mt_rand(1,20);

	}while( $rand_num_1 == "1" );

	do{

		$rand_num_2 = mt_rand(1,20);

	}while( ( $rand_num_1 == $rand_num_2 ) || ( $rand_num_2 == "1" ) );

	do{

		$rand_num_3=mt_rand(1,20);

	}while( ( $rand_num_1 == $rand_num_3 ) || ( $rand_num_2 == $rand_num_3 ) || ( $rand_num_3 == "1" ) );

	$data = array();

	$data["file1"] = "pc/content/".$file_array[$rand_num_1];
	$data["file2"] = "pc/content/".$file_array[$rand_num_2];
	$data["file3"] = "pc/content/".$file_array[$rand_num_3];

	return $data;
	exit();

}

//時間行のデータを取得
function get_time_line_data($today_time_num){

	$time_line_data = array(
					    "1" => "18:00",
						"2" => "18:30",
						"3" => "19:00",
						"4" => "19:30",
						"5" => "20:00",
						"6" => "20:30",
						"7" => "21:00",
						"8" => "21:30",
						"9" => "22:00",
						"10" => "22:30",
						"11" => "23:00",
						"12" => "23:30",
						"13" => "0:00",
						"14" => "0:30",
						"15" => "1:00",
						"16" => "1:30",
						"17" => "2:00",
						"18" => "2:30",
						"19" => "3:00",
						"20" => "3:30",
						"21" => "4:00",
						"22" => "4:30",
						"23" => "5:00",
						"24" => "5:30",
						"25" => "6:00"
	);

	/*
	if( $today_time_num == false ){
		return $time_line_data;
		exit();
	}else{
		for($i=1;$i<=23;$i++){
			if( $i < $today_time_num ){
				$time_line_data[$i] = "×";
			}
		}
		return $time_line_data;
		exit();
	}
	*/

	return $time_line_data;
	exit();

}

function get_time_line_data_2(){

	$time_line_data = array(
			"1" => "18時",
			"2" => "18:30",
			"3" => "19時",
			"4" => "19:30",
			"5" => "20時",
			"6" => "20:30",
			"7" => "21時",
			"8" => "21:30",
			"9" => "22時",
			"10" => "22:30",
			"11" => "23時",
			"12" => "23:30",
			"13" => "0時",
			"14" => "0:30",
			"15" => "1時",
			"16" => "1:30",
			"17" => "2時",
			"18" => "2:30",
			"19" => "3時",
			"20" => "3:30",
			"21" => "4時",
			"22" => "4:30",
			"23" => "5時",
			"24" => "5:30",
			"25" => "6時"

	);

	return $time_line_data;
	exit();

}

//本日時間を表す1～23の数値を取得
function get_today_time_num($now_hour,$now_minute){

	/*
	echo $now_hour;
	echo "<br />";
	echo $now_minute;
	exit();
	*/

	$time_array = array(
				    "1" => array("hour" => 18,"minute" => 0),
					"2" => array("hour" => 18,"minute" => 30),
					"3" => array("hour" => 19,"minute" => 0),
					"4" => array("hour" => 19,"minute" => 30),
					"5" => array("hour" => 20,"minute" => 0),
					"6" => array("hour" => 20,"minute" => 30),
					"7" => array("hour" => 21,"minute" => 0),
					"8" => array("hour" => 21,"minute" => 30),
					"9" => array("hour" => 22,"minute" => 0),
					"10" => array("hour" => 22,"minute" => 30),
					"11" => array("hour" => 23,"minute" => 0),
					"12" => array("hour" => 23,"minute" => 30),
					"13" => array("hour" => 0,"minute" => 0),
					"14" => array("hour" => 0,"minute" => 30),
					"15" => array("hour" => 1,"minute" => 0),
					"16" => array("hour" => 1,"minute" => 30),
					"17" => array("hour" => 2,"minute" => 0),
					"18" => array("hour" => 2,"minute" => 30),
					"19" => array("hour" => 3,"minute" => 0),
					"20" => array("hour" => 3,"minute" => 30),
					"21" => array("hour" => 4,"minute" => 0),
					"22" => array("hour" => 4,"minute" => 30),
					"23" => array("hour" => 5,"minute" => 0),
					"24" => array("hour" => 5,"minute" => 30),
					"25" => array("hour" => 6,"minute" => 0)
	);

	if( $now_minute > 30 ){

		$minute = 0;
		$hour = $now_hour + 1;

		if( $hour > 25 ){

			$hour = 0;

		}

	}else if( $now_minute == 0 ){

		$minute = $now_minute;
		$hour = $now_hour;

	}else{

		$minute = 30;
		$hour = $now_hour;

	}

	/*
	echo $hour;
	echo "<br />";
	echo $minute;
	exit();
	*/

	for($i=1;$i<=27;$i++){

		$hour_tmp = $time_array[$i]["hour"];
		$minute_tmp = $time_array[$i]["minute"];

		if( ( $hour_tmp == $hour ) && ( $minute_tmp == $minute ) ){

			return $i;
			exit();

		}

	}

	return false;
	exit();

}

function past_time($time){

	$time_array = array(
			    "1" => array("hour" => 18,"minute" => 0),
				"2" => array("hour" => 18,"minute" => 30),
				"3" => array("hour" => 19,"minute" => 0),
				"4" => array("hour" => 19,"minute" => 30),
				"5" => array("hour" => 20,"minute" => 0),
				"6" => array("hour" => 20,"minute" => 30),
				"7" => array("hour" => 21,"minute" => 0),
				"8" => array("hour" => 21,"minute" => 30),
				"9" => array("hour" => 22,"minute" => 0),
				"10" => array("hour" => 22,"minute" => 30),
				"11" => array("hour" => 23,"minute" => 0),
				"12" => array("hour" => 23,"minute" => 30),
				"13" => array("hour" => 0,"minute" => 0),
				"14" => array("hour" => 0,"minute" => 30),
				"15" => array("hour" => 1,"minute" => 0),
				"16" => array("hour" => 1,"minute" => 30),
				"17" => array("hour" => 2,"minute" => 0),
				"18" => array("hour" => 2,"minute" => 30),
				"19" => array("hour" => 3,"minute" => 0),
				"20" => array("hour" => 3,"minute" => 30),
				"21" => array("hour" => 4,"minute" => 0),
				"22" => array("hour" => 4,"minute" => 30),
				"23" => array("hour" => 5,"minute" => 0),
				"24" => array("hour" => 5,"minute" => 30),
				"25" => array("hour" => 6,"minute" => 0)
	);

	$now_time = time();

	$now_year = intval(date('Y'));
	$now_month = intval(date('m'));
	$now_day = intval(date('d'));
	$now_hour = intval(date('H'));

	$select_hour = $time_array[$time]["hour"];
	$select_minute = $time_array[$time]["minute"];

	//echo $now_hour;

	if($select_hour <= 6){

		if($now_hour > $select_hour){

			if( ($now_hour >= 0) && ($now_hour <= 6) ){
				return true;
			}else{
				return false;
			}
		}

	}

	$select_time = mktime($select_hour,$select_minute,0,$now_month,$now_day,$now_year);//時・分・秒・月・日・年、の順に入力

	if($now_time > $select_time){

		return true;

	}else{

		return false;

	}

}

//出勤情報の取得
function get_attendance_data($year,$month,$day,$today_flag,$under6_flag){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$area = "tokyo";

	// 出勤しているセラピスト情報を取得するためのSQL文
	$sql = sprintf("
select *,attendance_new.id as attendance_id
from attendance_new
left join therapist_new on attendance_new.therapist_id=therapist_new.id
where
therapist_new.delete_flg=0 and
therapist_new.test_flg=0 and
year='%s' and
month='%s' and
day='%s' and
therapist_new.area='%s' and
attendance_new.today_absence='0' and
attendance_new.syounin_state='1'
order by therapist_new.order_num desc",
$year,$month,$day,$area);

	$res = mysql_query($sql, $con);
	if($res == false){
		echo "クエリ実行に失敗しました";
		exit();
	}

	$attendance_data = array();

	//出勤しているセラピストのidを取得
	$therapist_array = array();

	$most_start_time = 25;
	$most_end_time = 1;

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;
		$attendance_id = $attendance_data[$i]["attendance_id"];
		$therapist_array[$i] = $attendance_data[$i]["therapist_id"];

		if($most_start_time > $attendance_data[$i]["start_time"]){
			$most_start_time = $attendance_data[$i]["start_time"];
		}

		if($most_end_time < $attendance_data[$i]["end_time"]){
			$most_end_time = $attendance_data[$i]["end_time"];
		}

		$sql = sprintf("
select time from reservation_new where attendance_id='%s'",
$attendance_id);

		$res2 = mysql_query($sql, $con);
		if($res2 == false){
			echo "クエリ実行に失敗しました";
			exit();
		}
		$j=0;
		while($row2 = mysql_fetch_assoc($res2)){

			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;

		}
		if($j==0){

			$attendance_data[$i]["time_num"]=0;

		}

		$i++;
	}

	//移動時間もあるため、開始時間ぴったりの予約には対応できない
	$most_start_time = $most_start_time + 1;

	$therapist_array_num = count($therapist_array);

	//対応可能:1,対応不可:0

	$free_therapist_state = array(
	    "1" => 0,
		"2" => 0,
		"3" => 0,
		"4" => 0,
		"5" => 0,
		"6" => 0,
		"7" => 0,
		"8" => 0,
		"9" => 0,
		"10" => 0,
	    "11" => 0,
		"12" => 0,
		"13" => 0,
		"14" => 0,
		"15" => 0,
		"16" => 0,
		"17" => 0,
		"18" => 0,
		"19" => 0,
		"20" => 0,
		"21" => 0,
		"22" => 0,
		"23" => 0
	);

	for($i=1;$i<=23;$i++){

		if( ($under6_flag==true) && ($i<=12) && ($today_flag==true) ){

			$free_therapist_state[$i] = 0;

		}else if($i<$most_start_time){

			$free_therapist_state[$i] = 0;

		}else if($i>$most_end_time){

			$free_therapist_state[$i] = 0;

		}else if( ($today_flag == true) && (past_time($i)) ){

			$free_therapist_state[$i] = 0;

		}else{

			$kettei_flag=false;
			for($j=0;$j<$therapist_array_num;$j++){
				if($kettei_flag==false){
					$therapist_id = $therapist_array[$j];

$sql = sprintf("
select
therapist_id,start_time,end_time,time
from attendance_new
left join reservation_new on attendance_new.id=reservation_new.attendance_id
where
therapist_id='%s' and
year='%s' and
month='%s' and
day='%s'",
$therapist_id,$year,$month,$day);

					$res = mysql_query($sql, $con);
					if($res == false){
						echo "クエリ実行に失敗しました";
						exit();
					}
					$tmp_end_flag=false;
					while($row = mysql_fetch_assoc($res)){
						if($tmp_end_flag==false){
							if($row["start_time"] > $i){
								$tmp_end_flag=true;

							}else if($row["end_time"] < $i){
								$tmp_end_flag=true;

							}else{
								$tmp_start_time = $row["start_time"];
								$tmp_end_time = $row["end_time"]-1;
								$tmp_time = $row["time"];
								if($tmp_time==$i){
									$free_therapist_state[$i] = 0;
									$tmp_end_flag=true;


								}else{
									$for_end_flag = false;
									for( $k=$tmp_start_time;$k<=$tmp_end_time;$k++ ){
										if($for_end_flag==false){
											if( $k == $i ){

												$free_therapist_state[$i] = 1;
												$for_end_flag = true;

											}
										}
									}
								}
							}
						}
					}
					if($free_therapist_state[$i]==1){
						$kettei_flag=true;
					}
				}
			}
		}


	}

	$data = array();

	$data["attendance_data"] = $attendance_data;
	$data["free_therapist_state"] = $free_therapist_state;

	return $data;
	exit();

}

//出勤情報の取得
function get_attendance_data_bk201405281559($year,$month,$day,$today_flag,$under6_flag){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	// 出勤しているセラピスト情報を取得するためのSQL文
	$sql = sprintf("select *,attendance.id as attendance_id from attendance
	left join therapist on attendance.therapist_id=therapist.id
	where therapist.delete_flag=0 and year='%s' and month='%s' and day='%s' and attendance.today_absence='0'",
	$year,$month,$day);
	$res = mysql_query($sql, $con);
	if($res == false){
		echo "クエリ実行に失敗しました";
		exit();
	}

	$attendance_data = array();

	//出勤しているセラピストのidを取得
	$therapist_array = array();

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;
		$attendance_id = $attendance_data[$i]["attendance_id"];
		$therapist_array[$i] = $attendance_data[$i]["therapist_id"];

		if($most_start_time > $attendance_data[$i]["start_time"]){
			$most_start_time = $attendance_data[$i]["start_time"];
		}

		if($most_end_time < $attendance_data[$i]["end_time"]){
			$most_end_time = $attendance_data[$i]["end_time"];
		}

		$sql = sprintf("select time from reservation where attendance_id='%s'",$attendance_id);
		$res2 = mysql_query($sql, $con);
		if($res2 == false){
			echo "クエリ実行に失敗しました";
			exit();
		}
		$j=0;
		while($row2 = mysql_fetch_assoc($res2)){

			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;

		}
		if($j==0){

			$attendance_data[$i]["time_num"]=0;

		}

		$i++;
	}

	$therapist_array_num = count($therapist_array);

	//対応可能:1,対応不可:0

	$free_therapist_state = array(
	    "1" => 0,
		"2" => 0,
		"3" => 0,
		"4" => 0,
		"5" => 0,
		"6" => 0,
		"7" => 0,
		"8" => 0,
		"9" => 0,
		"10" => 0,
	    "11" => 0,
		"12" => 0,
		"13" => 0,
		"14" => 0,
		"15" => 0,
		"16" => 0,
		"17" => 0,
		"18" => 0,
		"19" => 0,
		"20" => 0,
		"21" => 0,
		"22" => 0,
		"23" => 0
	);

	for($i=1;$i<=23;$i++){

		if( ($under6_flag==true) && ($i<=12) && ($today_flag==true) ){

			$free_therapist_state[$i] = 0;

		}else if($i<$most_start_time){

			$free_therapist_state[$i] = 0;

		}else if($i>$most_end_time){

			$free_therapist_state[$i] = 0;

		}else if( ($today_flag == true) && (past_time($i)) ){

			$free_therapist_state[$i] = 0;

		}else{

			$kettei_flag=false;
			for($j=0;$j<$therapist_array_num;$j++){
				if($kettei_flag==false){
					$therapist_id = $therapist_array[$j];
					$sql = sprintf("select therapist_id,start_time,end_time,time from attendance
					left join reservation on attendance.id=reservation.attendance_id
					where therapist_id='%s' and year='%s' and month='%s' and day='%s'",
					$therapist_id,$year,$month,$day);
					$res = mysql_query($sql, $con);
					if($res == false){
						echo "クエリ実行に失敗しました";
						exit();
					}
					$tmp_end_flag=false;
					while($row = mysql_fetch_assoc($res)){
						if($tmp_end_flag==false){
							if($row["start_time"] > $i){
								$tmp_end_flag=true;

							}else if($row["end_time"] < $i){
								$tmp_end_flag=true;

							}else{
								$tmp_start_time = $row["start_time"];
								$tmp_end_time = $row["end_time"]-1;
								$tmp_time = $row["time"];
								if($tmp_time==$i){
									$free_therapist_state[$i] = 0;
									$tmp_end_flag=true;


								}else{
									$for_end_flag = false;
									for( $k=$tmp_start_time;$k<=$tmp_end_time;$k++ ){
										if($for_end_flag==false){
											if($k==$i){

												$free_therapist_state[$i] = 1;
												$for_end_flag = true;

											}
										}
									}
								}
							}
						}
					}
					if($free_therapist_state[$i]==1){
						$kettei_flag=true;
					}
				}
			}
		}


	}

	$data = array();

	$data["attendance_data"] = $attendance_data;
	$data["free_therapist_state"] = $free_therapist_state;

	return $data;
	exit();

}

//お客様の声、登録
function regist_voice($name,$age,$gender,$satisfaction,$content,$shop_type){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$now = time();

	$mail_content = $content;

	$name = mysql_real_escape_string($name);
	$age = mysql_real_escape_string($age);
	$gender = mysql_real_escape_string($gender);
	$satisfaction = mysql_real_escape_string($satisfaction);
	$content = mysql_real_escape_string($content);

	if( $name=="" ){

		$name = "匿名";

	}

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$sql = sprintf("insert into voice(created,updated,shop_type,name,age,gender,satisfaction,content)
					values('%s','%s','%s','%s','%s','%s','%s','%s')",
	$now,$now,$shop_type,$name,$age,$gender,$satisfaction,$content);

	$res = mysql_query($sql, $con);
	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		return false;
		exit();

	}

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

	if( $shop_type=="refle" ){

		$shop_name = "東京リフレ";
		$mailto = "info@tokyo-refle.com";
		$header = "From: info@tokyo-refle.com";

	}else if( $shop_type=="sapporo" ){

		$shop_name = "札幌リフレ";
		$mailto = "info@sapporo-refle.com";
		$header = "From: info@sapporo-refle.com";

	}else if( $shop_type=="makuhari" ){

		$shop_name = "幕張リフレ";
		$mailto = "info@makuhari-refle.com";
		$header = "From: info@makuhari-refle.com";

	}else if( $shop_type=="yokohama" ){

		$shop_name = "横浜リフレ";
		$mailto = "info@yokohama-refle.com";
		$header = "From: info@yokohama-refle.com";

	}

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

	$res = mb_send_mail($mailto,$title,$content,$header);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
		return false;
		exit();

	}

	//コミット
	$sql = "commit";
	mysql_query( $sql, $con );

	//MySQL切断
	mysql_close( $con );

	return true;
	exit();

}

function get_voice_list_front($paging_num,$display_num,$shop_type){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$start_num = $display_num*($paging_num-1);

	$sql = sprintf("select * from voice where publish_flg='1' and delete_flg=0 and shop_type='%s' order by created desc limit %s,%s",$shop_type,$start_num,$display_num);
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();

}

function get_voice_list_front_top($shop_type,$num){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from voice where publish_flg='1' and delete_flg=0 and shop_type='%s' order by created desc limit 0,%s",$shop_type,$num);
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();

}

//ページングの最大値を取得
function get_paging_max_num_voice_front($display_num,$shop_type){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select id from voice where publish_flg='1' and shop_type='%s' and delete_flg='0'",$shop_type);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num_rows = mysql_num_rows($res);

	$max_num = ceil($num_rows/$display_num);

	return $max_num;
	exit();

}

//駅ページのデータを取得
function get_station_page_data($page_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	//無害化
	$page_id = mysql_real_escape_string($page_id);

	//url_nameかどうかのチェック
	$result = check_station_url_name($page_id);

	if( $result == true ){

		if($access_type == "pc"){

			$sql = "select station.id,station.name,station.hotel_id,ku.idou_time,ku.idou_cost from station left join ku on ku.id=station.ku_id where station.url_name='".$page_id."'";

		}else{

			$sql = "select station.id,station.name,station.distance,station.hotel_id,ku.idou_time,ku.idou_cost from station left join ku on ku.id=station.ku_id where station.url_name='".$page_id."'";

		}

	}else{

		if($access_type == "pc"){

			$sql = "select station.id,station.name,station.hotel_id,ku.idou_time,ku.idou_cost from station left join ku on ku.id=station.ku_id where station.id=".$page_id;

		}else{

			$sql = "select station.id,station.name,station.distance,station.hotel_id,ku.idou_time,ku.idou_cost from station left join ku on ku.id=station.ku_id where station.id=".$page_id;

		}

	}

	$res = mysql_query($sql, $con);
	if($res === false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$i=0;
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();

}

//url_nameかどうかのチェック
function check_station_url_name($page_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select id from station where url_name='%s'",$page_id);

	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){

		return true;
		exit();

	}else{

		//数値かどうか
		if (!preg_match("/^[0-9]+$/", $page_id)) {

			header("Location: ".WWW_URL."404.php");
			exit();

		}

		return false;
		exit();

	}

}

function get_therapist_page_data($area){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select
therapist_page.*
from therapist_page
left join therapist_new on therapist_new.id=therapist_page.therapist_id
where
therapist_new.leave_flg=0 and
therapist_new.test_flg=0 and
therapist_new.delete_flg=0 and
therapist_page.delete_flg=0 and
therapist_page.area='%s'
order by therapist_page.order_value desc",
$area);

	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$skill = $row["skill"];
		$therapist_id = $row["therapist_id"];
		$skill_data = explode(",",$skill);
		$area = "tokyo";
		$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);

		$list_data[$i] = $row;
		$list_data[$i]["skill_data"] = $skill_data;
		$list_data[$i]["therapist_name"] = $therapist_name;

		$i++;
	}

	return $list_data;
	exit();

}

function get_therapist_page_data_bk201405281512($area){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select therapist_page.* from therapist_page
left join therapist on therapist.id=therapist_page.therapist_id
where therapist.leave_flg=0 and therapist_page.delete_flg=0 and therapist_page.area='%s' order by therapist_page.order_value desc",
$area);
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$skill = $row["skill"];
		$therapist_id = $row["therapist_id"];
		$skill_data = explode(",",$skill);
		$area = "tokyo";
		$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);

		$list_data[$i] = $row;
		$list_data[$i]["skill_data"] = $skill_data;
		$list_data[$i]["therapist_name"] = $therapist_name;

		$i++;
	}

	return $list_data;
	exit();

}

//セラピスト名取得
function get_therapist_name_by_therapist_id($therapist_id,$area){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select name_site from therapist_new where id='%s'",$therapist_id);

	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$name = $row["name_site"];

	return $name;
	exit();

}

//セラピスト名取得
function get_therapist_name_by_therapist_id_bk201405281515($therapist_id,$area){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	if( $area=="tokyo" ){

		$sql = sprintf("select name_refle from therapist where id='%s'",$therapist_id);

	}else if( $area=="sapporo" ){

		$sql = sprintf("select name_sapporo from therapist_sapporo where id='%s'",$therapist_id);

	}
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $area=="tokyo" ){

		$name = $row["name_refle"];

	}else if( $area=="sapporo" ){

		$name = $row["name_sapporo"];

	}

	return $name;
	exit();

}

function test(){

	$data = "test";

	return $data;
	exit();

}

function get_therapist_page_data_attendance($area,$year,$month,$day,$type){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select
therapist_page.*
from therapist_page
left join therapist_new on therapist_new.id=therapist_page.therapist_id
where
therapist_new.leave_flg=0 and
therapist_new.test_flg=0 and
therapist_new.delete_flg=0 and
therapist_page.delete_flg=0 and
therapist_page.area='%s'
order by therapist_page.order_value desc",
$area);

	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		//本日出勤かどうかのチェック
		$result = therapist_attendance_check($therapist_id,$year,$month,$day);

		if( $type == "1" ){

			if( $result == true ){

				$skill = $row["skill"];
				$skill_data = explode(",",$skill);
				$area = "tokyo";
				$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$i++;

			}

		}else if( $type == "2" ){

			if( $result == false ){

				$skill = $row["skill"];
				$skill_data = explode(",",$skill);
				$area = "tokyo";
				$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$i++;

			}

		}else{

			header("Location: ".WWW_URL."error.php");
			exit();

		}

	}

	return $list_data;
	exit();

}

function get_therapist_page_data_attendance_bk201405281519($area,$year,$month,$day,$type){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select therapist_page.* from therapist_page
left join therapist on therapist.id=therapist_page.therapist_id
where
therapist.leave_flg=0 and
therapist_page.delete_flg=0 and
therapist_page.area='%s'
order by therapist_page.order_value desc",
	$area);
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		//本日出勤かどうかのチェック
		$result = therapist_attendance_check($therapist_id,$year,$month,$day);

		if( $type == "1" ){

			if( $result == true ){

				$skill = $row["skill"];
				$skill_data = explode(",",$skill);
				$area = "tokyo";
				$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$i++;

			}

		}else if( $type == "2" ){

			if( $result == false ){

				$skill = $row["skill"];
				$skill_data = explode(",",$skill);
				$area = "tokyo";
				$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$i++;

			}

		}else{

			header("Location: ".WWW_URL."error.php");
			exit();

		}

	}

	return $list_data;
	exit();

}

//横浜リフレセラピストの兼務に対応し改修中だったが改修を一旦中止したため、
//未完でとりあえず保存しておいている関数。VIPページ対応など、兼務対応のための改修範囲が広すぎるので
function get_therapist_page_data_attendance_totyuu($area,$year,$month,$day,$type){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select therapist_page.* from therapist_page
left join therapist on therapist.id=therapist_page.therapist_id
where therapist.leave_flg=0 and therapist_page.delete_flg=0 and therapist_page.area='%s' order by therapist_page.order_value desc",
$area);
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}


	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$list_data[$i] = $row;
		$i++;

	}

	$kenmu = "tokyo";
	$staff_area = "yokohama";

	$sql = sprintf("
select therapist_page.* from therapist_page
left join therapist_new on therapist_new.id=therapist_page.therapist_id
where
therapist_new.leave_flg=0 and
therapist_new.kenmu='%s' and
therapist_page.delete_flg=0 and
therapist_page.area='%s'
order by therapist_page.order_value desc",
$kenmu,$staff_area);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	while($row = mysql_fetch_assoc($res)){

		$list_data[$i] = $row;
		$i++;

	}

	$data = $list_data;

	$list_data = array();

	$data_num = count($data);

	for($i=0;$i<$data_num;$i++){

		$therapist_id = $data[$i]["therapist_id"];

		//本日出勤かどうかのチェック
		$result = therapist_attendance_check($therapist_id,$year,$month,$day);

		if( $type == "1" ){

			if( $result == true ){

				$skill = $data[$i]["skill"];
				$skill_data = explode(",",$skill);
				$area = "tokyo";
				$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);
				$list_data[$i] = $data[$i];
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;

			}

		}else if( $type == "2" ){

			if( $result == false ){

				$skill = $data[$i]["skill"];
				$skill_data = explode(",",$skill);
				$area = "tokyo";
				$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);
				$list_data[$i] = $data[$i];
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;

			}

		}else{

			header("Location: ".WWW_URL."error.php");
			exit();

		}

	}

	/*
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		//本日出勤かどうかのチェック
		$result = therapist_attendance_check($therapist_id,$year,$month,$day);

		if( $type == "1" ){

			if( $result == true ){

				$skill = $row["skill"];
				$skill_data = explode(",",$skill);
				$area = "tokyo";
				$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$i++;

			}

		}else if( $type == "2" ){

			if( $result == false ){

				$skill = $row["skill"];
				$skill_data = explode(",",$skill);
				$area = "tokyo";
				$therapist_name = get_therapist_name_by_therapist_id($therapist_id,$area);
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$i++;

			}

		}else{

			header("Location: ".WWW_URL."error.php");
			exit();

		}

	}
	*/

	return $list_data;
	exit();

}

//本日出勤かどうかのチェック
function therapist_attendance_check($therapist_id,$year,$month,$day){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from attendance_new
where therapist_id='%s' and
year='%s' and
month='%s' and
day='%s' and
today_absence='0' and
syounin_state='1'",
$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

//本日出勤かどうかのチェック
function therapist_attendance_check_bk201405281524($therapist_id,$year,$month,$day){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select id from attendance where therapist_id='%s' and year='%s' and month='%s' and day='%s' and today_absence='0'",
					$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

//出勤データを取得
function get_therapist_attendance_data($therapist_id,$year,$month,$day){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select start_time,end_time
from attendance_new
where
therapist_id='%s' and
year='%s' and
month='%s' and
day='%s' and
today_absence='0' and
kekkin_flg='0' and
syounin_state='1'",
$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();

}

//出勤データを取得
function get_therapist_attendance_data_bk201405281537($therapist_id,$year,$month,$day){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select start_time,end_time from attendance where therapist_id='%s' and year='%s' and month='%s' and day='%s' and today_absence='0'",
					$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num = mysql_num_rows($res);

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();

}

function get_page_content_page_html($page_type,$site_type,$data_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select content from page_html where page_type='%s' and site_type='%s' and data_id='%s'",
	$page_type,$site_type,$data_id);
	$res = mysql_query($sql, $con);
	if($res == false){
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["content"];
	exit();

}

function get_data_id_by_url_name($page_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select id from station where url_name='%s'",$page_id);

	$res = mysql_query($sql, $con);
	if($res == false){
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();

}

function station_page_redirect($page_id){

	if( $page_id == "15" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ikebukuro/" );

	}else if( $page_id == "11" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinjuku/" );

	}else if( $page_id == "8" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shibuya/" );

	}else if( $page_id == "3" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinagawa/" );

	}else if( $page_id == "382" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ginza/" );

	}else if( $page_id == "470" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/akasaka/" );

	}else if( $page_id == "568" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shiodome/" );

	}else if( $page_id == "23" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ueno/" );

	}else if( $page_id == "2" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinbashi/" );

	}else if( $page_id == "26" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kanda/" );

	}else if( $page_id == "5" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/gotanda/" );

	}else if( $page_id == "53" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakano/" );

	}else if( $page_id == "42" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ochanomizu/" );

	}else if( $page_id == "25" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/akihabara/" );

	}else if( $page_id == "38" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yotsuya/" );

	}else if( $page_id == "9" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/harajuku/" );

	}else if( $page_id == "56" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ogikubo/" );

	}else if( $page_id == "10" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yoyogi/" );

	}else if( $page_id == "76" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ryougoku/" );

	}else if( $page_id == "435" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/roppongi/" );

	}else if( $page_id == "437" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ebisu/" );

	}else if( $page_id == "43" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/suidobashi/" );

	}else if( $page_id == "44" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/iidabashi/" );

	}else if( $page_id == "13" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takadanobaba/" );

	}else if( $page_id == "436" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hiroo/" );

	}else if( $page_id == "281" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakameguro/" );

	}else if( $page_id == "299" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sangenjaya/" );

	}else if( $page_id == "318" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/futakotamagawa/" );

	}else if( $page_id == "277" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/seijyougakuenmae/" );

	}else if( $page_id == "301" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sakurashinmachi/" );

	}else if( $page_id == "187" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nerima/" );

	}else if( $page_id == "472" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/omotesandou/" );

	}else if( $page_id == "280" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/daikanyama/" );

	}else if( $page_id == "531" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shirokanetakanawa/" );

	}else if( $page_id == "93" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/minamisenjyu/" );

	}else if( $page_id == "427" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kayabacho/" );

	}else if( $page_id == "380" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nihonbashi/" );

	}else if( $page_id == "398" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/otemachi/" );

	}else if( $page_id == "28" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yurakucho/" );

	}else if( $page_id == "161" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/asakusa/" );

	}else if( $page_id == "30" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hamamatsucho/" );

	}else if( $page_id == "443" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kagurazaka/" );

	}else if( $page_id == "288" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/meguro/" );

	}else if( $page_id == "451" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kiba/" );

	}else if( $page_id == "452" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toyocho/" );

	}else if( $page_id == "21" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nippori/" );

	}else if( $page_id == "78" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kameido/" );

	}else if( $page_id == "282" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yuutenji/" );

	}else if( $page_id == "285" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/jiyuugaoka/" );

	}else if( $page_id == "450" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/monzennakacho/" );

	}else if( $page_id == "20" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishinippori/" );

	}else if( $page_id == "571" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/azabujuban/" );

	}else if( $page_id == "286" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/denenchofu/" );

	}else if( $page_id == "1" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tokyo/" );

	}else if( $page_id == "4" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/osaki/" );

	}else if( $page_id == "12" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinokubo/" );

	}else if( $page_id == "14" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/mejiro/" );

	}else if( $page_id == "16" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/otsuka/" );

	}else if( $page_id == "18" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/komagome/" );

	}else if( $page_id == "19" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tabata/" );

	}else if( $page_id == "22" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/uguisudani/" );

	}else if( $page_id == "24" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/okachimachi/" );

	}else if( $page_id == "31" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tamachi/" );

	}else if( $page_id == "36" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishiooi/" );

	}else if( $page_id == "45" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ichigaya/" );

	}else if( $page_id == "47" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinanomachi/" );

	}else if( $page_id == "48" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sendagaya/" );

	}else if( $page_id == "51" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/okubo/" );

	}else if( $page_id == "52" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashinakano/" );

	}else if( $page_id == "54" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/koenji/" );

	}else if( $page_id == "55" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/asagaya/" );

	}else if( $page_id == "57" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishiogikubo/" );

	}else if( $page_id == "75" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/asakusabashi/" );

	}else if( $page_id == "79" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hirai/" );

	}else if( $page_id == "80" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinkoiwa/" );

	}else if( $page_id == "81" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/koiwa/" );

	}else if( $page_id == "83" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinnihonbashi/" );

	}else if( $page_id == "84" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/bakurocho/" );

	}else if( $page_id == "85" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kinshicho/" );

	}else if( $page_id == "88" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oku/" );

	}else if( $page_id == "89" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/akabane/" );

	}else if( $page_id == "92" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/mikawashima/" );

	}else if( $page_id == "94" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kitasenju/" );

	}else if( $page_id == "95" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ayase/" );

	}else if( $page_id == "96" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kameari/" );

	}else if( $page_id == "97" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kanamati/" );

	}else if( $page_id == "103" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/itabashi/" );

	}else if( $page_id == "104" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/jujo/" );

	}else if( $page_id == "106" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kitaakabane/" );

	}else if( $page_id == "107" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ukimafunado/" );

	}else if( $page_id == "112" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hatchobori/" );

	}else if( $page_id == "113" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ecchujima/" );

	}else if( $page_id == "114" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shiomi/" );

	}else if( $page_id == "115" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinkiba/" );

	}else if( $page_id == "116" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kasairinkaikouen/" );

	}else if( $page_id == "123" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashijujo/" );

	}else if( $page_id == "125" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kaminakazato/" );

	}else if( $page_id == "141" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/omori/" );

	}else if( $page_id == "142" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kamata/" );

	}else if( $page_id == "151" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kitaikebukuro/" );

	}else if( $page_id == "152" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimoitabashi/" );

	}else if( $page_id == "153" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ooyama/" );

	}else if( $page_id == "154" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakaitabashi/" );

	}else if( $page_id == "155" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tokiwadai/" );

	}else if( $page_id == "156" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kamiitabashi/" );

	}else if( $page_id == "157" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tobunerima/" );

	}else if( $page_id == "158" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimoakatsuka/" );

	}else if( $page_id == "159" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/narimasu/" );

	}else if( $page_id == "162" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tokyoskytree/" );

	}else if( $page_id == "163" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oshiage/" );

	}else if( $page_id == "164" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hikifune/" );

	}else if( $page_id == "165" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashimukoujima/" );

	}else if( $page_id == "166" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kanegafuchi/" );

	}else if( $page_id == "167" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/horikiri/" );

	}else if( $page_id == "168" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ushida/" );

	}else if( $page_id == "170" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kosuge/" );

	}else if( $page_id == "171" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/gotanno/" );

	}else if( $page_id == "172" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/umejima/" );

	}else if( $page_id == "173" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishiarai/" );

	}else if( $page_id == "174" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takenotsuka/" );

	}else if( $page_id == "176" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/omurai/" );

	}else if( $page_id == "177" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashiazuma/" );

	}else if( $page_id == "178" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kameidosuijin/" );

	}else if( $page_id == "181" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/daishimae/" );

	}else if( $page_id == "183" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shiinamachi/" );

	}else if( $page_id == "184" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashinagasaki/" );

	}else if( $page_id == "185" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ekoda/" );

	}else if( $page_id == "186" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sakuradai/" );

	}else if( $page_id == "188" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakamurabashi/" );

	}else if( $page_id == "189" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/fujimidai/" );

	}else if( $page_id == "190" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nerimatakanodai/" );

	}else if( $page_id == "191" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/syakujiikouen/" );

	}else if( $page_id == "192" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ooizumigakuen/" );

	}else if( $page_id == "193" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kotakemukaihara/" );

	}else if( $page_id == "194" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinsakuradai/" );

	}else if( $page_id == "197" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toshimaen/" );

	}else if( $page_id == "198" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/seibushinjuku/" );

	}else if( $page_id == "200" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimoochiai/" );

	}else if( $page_id == "201" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakai/" );

	}else if( $page_id == "202" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/araiyakushimae/" );

	}else if( $page_id == "203" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/numabukuro/" );

	}else if( $page_id == "204" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nogata/" );

	}else if( $page_id == "205" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toritsukasei/" );

	}else if( $page_id == "206" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/saginomiya/" );

	}else if( $page_id == "207" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimoigusa/" );

	}else if( $page_id == "208" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/iogi/" );

	}else if( $page_id == "209" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kamiigusa/" );

	}else if( $page_id == "210" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kamisyakujii/" );

	}else if( $page_id == "211" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/musashiseki/" );

	}else if( $page_id == "212" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/keiseiueno/" );

	}else if( $page_id == "214" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinmikawashima/" );

	}else if( $page_id == "215" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/machiya/" );

	}else if( $page_id == "216" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/senjuoohashi/" );

	}else if( $page_id == "217" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/keiseisekiya/" );

	}else if( $page_id == "218" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/horikirisyoubuen/" );

	}else if( $page_id == "219" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ohanajaya/" );

	}else if( $page_id == "220" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/aoto/" );

	}else if( $page_id == "221" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/keiseitakasago/" );

	}else if( $page_id == "222" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/keiseikoiwa/" );

	}else if( $page_id == "223" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/edogawa/" );

	}else if( $page_id == "225" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/keiseihikifune/" );

	}else if( $page_id == "226" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yahiro/" );

	}else if( $page_id == "227" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yotsugi/" );

	}else if( $page_id == "228" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/keiseitateishi/" );

	}else if( $page_id == "232" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shibamata/" );

	}else if( $page_id == "233" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/keiseikanamachi/" );

	}else if( $page_id == "239" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hatsudai/" );

	}else if( $page_id == "240" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hatagaya/" );

	}else if( $page_id == "241" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sasazuka/" );

	}else if( $page_id == "242" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/daitabashi/" );

	}else if( $page_id == "244" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimotakaido/" );

	}else if( $page_id == "245" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sakurazyousui/" );

	}else if( $page_id == "246" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kamikitazawa/" );

	}else if( $page_id == "247" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hachimanyama/" );

	}else if( $page_id == "248" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/rukakouen/" );

	}else if( $page_id == "249" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/chitosekarasuyama/" );

	}else if( $page_id == "251" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinsen/" );

	}else if( $page_id == "252" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/komabatoudaimae/" );

	}else if( $page_id == "253" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ikenoue/" );

	}else if( $page_id == "254" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimokitazawa/" );

	}else if( $page_id == "255" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shindaita/" );

	}else if( $page_id == "256" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashimatsubara/" );

	}else if( $page_id == "257" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/meidaimae/" );

	}else if( $page_id == "258" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/eifukuchou/" );

	}else if( $page_id == "259" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishieifuku/" );

	}else if( $page_id == "260" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hamadayama/" );

	}else if( $page_id == "261" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takaido/" );

	}else if( $page_id == "262" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/fujimigaoka/" );

	}else if( $page_id == "263" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kugayama/" );

	}else if( $page_id == "265" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/minamishinjuku/" );

	}else if( $page_id == "266" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sanguubashi/" );

	}else if( $page_id == "267" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yoyogihachiman/" );

	}else if( $page_id == "268" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yoyogiuehara/" );

	}else if( $page_id == "269" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashikitazawa/" );

	}else if( $page_id == "271" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/setagayadaita/" );

	}else if( $page_id == "272" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/umegaoka/" );

	}else if( $page_id == "273" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/goutokuji/" );

	}else if( $page_id == "274" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kyodo/" );

	}else if( $page_id == "275" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/chitosefunabashi/" );

	}else if( $page_id == "276" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/soshigayaookura/" );

	}else if( $page_id == "278" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kitami/" );

	}else if( $page_id == "283" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/gakugeidaigaku/" );

	}else if( $page_id == "284" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toritsudaigaku/" );

	}else if( $page_id == "287" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tamagawa/" );

	}else if( $page_id == "289" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/fudoumae/" );

	}else if( $page_id == "290" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/musashikoyama/" );

	}else if( $page_id == "291" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishikoyama/" );

	}else if( $page_id == "292" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/senzoku/" );

	}else if( $page_id == "293" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oookayama/" );

	}else if( $page_id == "294" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/okusawa/" );

	}else if( $page_id == "298" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ikejirioohashi/" );

	}else if( $page_id == "300" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/komazawadaigaku/" );

	}else if( $page_id == "302" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/youga/" );

	}else if( $page_id == "304" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ooimachi/" );

	}else if( $page_id == "305" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimoshinmei/" );

	}else if( $page_id == "306" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/togoshikouen/" );

	}else if( $page_id == "307" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakanobu/" );

	}else if( $page_id == "308" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ebaramachi/" );

	}else if( $page_id == "309" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hatanodai/" );

	}else if( $page_id == "310" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kitasenzoku/" );

	}else if( $page_id == "312" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/midorigaoka/" );

	}else if( $page_id == "314" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kuhonbutsu/" );

	}else if( $page_id == "315" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oyamadai/" );

	}else if( $page_id == "316" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/todoroki/" );

	}else if( $page_id == "317" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kaminoge/" );

	}else if( $page_id == "320" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oosakihirokouji/" );

	}else if( $page_id == "321" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/togoshiginza/" );

	}else if( $page_id == "322" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ebaranakanobu/" );

	}else if( $page_id == "324" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nagahara/" );

	}else if( $page_id == "325" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/senzokuike/" );

	}else if( $page_id == "326" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ishikawadai/" );

	}else if( $page_id == "327" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yukigayaootsuka/" );

	}else if( $page_id == "328" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ontakesan/" );

	}else if( $page_id == "329" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kugahara/" );

	}else if( $page_id == "330" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/chidoricho/" );

	}else if( $page_id == "331" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ikegami/" );

	}else if( $page_id == "332" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hasunuma/" );

	}else if( $page_id == "335" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/numabe/" );

	}else if( $page_id == "336" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/unoki/" );

	}else if( $page_id == "337" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimomaruko/" );

	}else if( $page_id == "338" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/musashinitta/" );

	}else if( $page_id == "339" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/eguchinowatashi/" );

	}else if( $page_id == "342" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishitaishidou/" );

	}else if( $page_id == "343" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/wakabayashi/" );

	}else if( $page_id == "344" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/syouinjinja/" );

	}else if( $page_id == "345" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/setagaya/" );

	}else if( $page_id == "346" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kamimachi/" );

	}else if( $page_id == "347" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/miyanosaka/" );

	}else if( $page_id == "348" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yamashita/" );

	}else if( $page_id == "349" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/matsubara/" );

	}else if( $page_id == "351" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sengakuji/" );

	}else if( $page_id == "353" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kitashinagawa/" );

	}else if( $page_id == "354" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinbanba/" );

	}else if( $page_id == "355" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/aomonoyokocho/" );

	}else if( $page_id == "356" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/samezu/" );

	}else if( $page_id == "357" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tachiaigawa/" );

	}else if( $page_id == "358" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oomorikaigan/" );

	}else if( $page_id == "359" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/heiwajima/" );

	}else if( $page_id == "360" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oomorimachi/" );

	}else if( $page_id == "361" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/umeyashiki/" );

	}else if( $page_id == "362" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/keikyuukamata/" );

	}else if( $page_id == "363" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/zoushiki/" );

	}else if( $page_id == "364" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/rokugoudote/" );

	}else if( $page_id == "366" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/koujiya/" );

	}else if( $page_id == "367" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ootorii/" );

	}else if( $page_id == "368" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/anamoriinari/" );

	}else if( $page_id == "369" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tenkuubashi/" );

	}else if( $page_id == "370" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hanedakuukoukokusaisenterminal/" );

	}else if( $page_id == "371" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hanedakuukou/" );

	}else if( $page_id == "373" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tawaramachi/" );

	}else if( $page_id == "374" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/inaricho/" );

	}else if( $page_id == "376" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/uenohirokouji/" );

	}else if( $page_id == "377" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/suehirocho/" );

	}else if( $page_id == "379" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/mitsukoshimae/" );

	}else if( $page_id == "381" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kyobashi/" );

	}else if( $page_id == "384" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toranomon/" );

	}else if( $page_id == "385" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tameikesanno/" );

	}else if( $page_id == "386" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/akasakamitsuke/" );

	}else if( $page_id == "387" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/aoyamaitchome/" );

	}else if( $page_id == "388" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/gaiemmae/" );

	}else if( $page_id == "392" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinotsuka/" );

	}else if( $page_id == "393" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/myougadani/" );

	}else if( $page_id == "394" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kourakuen/" );

	}else if( $page_id == "395" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hongosanchome/" );

	}else if( $page_id == "397" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/awajicho/" );

	}else if( $page_id == "401" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kasumigaseki/" );

	}else if( $page_id == "402" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kokkaigijidomae/" );

	}else if( $page_id == "405" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yotsuyasanchome/" );

	}else if( $page_id == "406" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinjukugyoenmae/" );

	}else if( $page_id == "407" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinjukusanchome/" );

	}else if( $page_id == "408" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishishinjuku/" );

	}else if( $page_id == "410" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinnakano/" );

	}else if( $page_id == "411" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashikouenji/" );

	}else if( $page_id == "412" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinkouenji/" );

	}else if( $page_id == "413" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/minamiasagaya/" );

	}else if( $page_id == "415" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakanoshinbashi/" );

	}else if( $page_id == "416" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakanofujimicho/" );

	}else if( $page_id == "417" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hounancho/" );

	}else if( $page_id == "420" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/minowa/" );

	}else if( $page_id == "421" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/iriya/" );

	}else if( $page_id == "423" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakaokachimachi/" );

	}else if( $page_id == "425" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kodenmacho/" );

	}else if( $page_id == "426" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ningyoucho/" );

	}else if( $page_id == "429" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tsukiji/" );

	}else if( $page_id == "430" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashiginza/" );

	}else if( $page_id == "432" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hibiya/" );

	}else if( $page_id == "434" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kamiyacho/" );

	}else if( $page_id == "440" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ochiai/" );

	}else if( $page_id == "442" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/waseda/" );

	}else if( $page_id == "445" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kudanshita/" );

	}else if( $page_id == "446" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takebashi/" );

	}else if( $page_id == "453" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/minamisunamachi/" );

	}else if( $page_id == "454" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishikasai/" );

	}else if( $page_id == "455" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kasai/" );

	}else if( $page_id == "456" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kitaayase/" );

	}else if( $page_id == "461" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sendagi/" );

	}else if( $page_id == "462" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nezu/" );

	}else if( $page_id == "464" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinochanomizu/" );

	}else if( $page_id == "466" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nijubashimae/" );

	}else if( $page_id == "471" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nogizaka/" );

	}else if( $page_id == "474" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yoyogikouen/" );

	}else if( $page_id == "476" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/chikatetsunarimasu/" );

	}else if( $page_id == "477" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/chikatetsuakatsuka/" );

	}else if( $page_id == "478" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/heiwadai/" );

	}else if( $page_id == "479" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hikawadai/" );

	}else if( $page_id == "481" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/senkawa/" );

	}else if( $page_id == "482" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kanamecho/" );

	}else if( $page_id == "484" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashiikebukuro/" );

	}else if( $page_id == "485" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/gokokuji/" );

	}else if( $page_id == "486" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/edogawabashi/" );

	}else if( $page_id == "489" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/koujimachi/" );

	}else if( $page_id == "490" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nagatacho/" );

	}else if( $page_id == "491" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sakuradamon/" );

	}else if( $page_id == "493" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ginzaitchome/" );

	}else if( $page_id == "494" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shintomicho/" );

	}else if( $page_id == "495" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tsukishima/" );

	}else if( $page_id == "497" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tatsumi/" );

	}else if( $page_id == "499" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinsenikebukuro/" );

	}else if( $page_id == "505" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hanzoumon/" );

	}else if( $page_id == "507" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/jinbocho/" );

	}else if( $page_id == "509" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/mitsukoshimae/" );

	}else if( $page_id == "510" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/suitengumae/" );

	}else if( $page_id == "511" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kiyosumishirakawa/" );

	}else if( $page_id == "512" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sumiyoshi/" );

	}else if( $page_id == "515" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/akabaneiwabuchi/" );

	}else if( $page_id == "516" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimo/" );

	}else if( $page_id == "517" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oujikamiya/" );

	}else if( $page_id == "518" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ouji/" );

	}else if( $page_id == "519" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishigahara/" );

	}else if( $page_id == "521" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/honkomagome/" );

	}else if( $page_id == "522" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toudaimae/" );

	}else if( $page_id == "528" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tameikesannou/" );

	}else if( $page_id == "529" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/roppongiitchome/" );

	}else if( $page_id == "535" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/chikatetsuakatsuka/" );

	}else if( $page_id == "542" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/zoushigaya/" );

	}else if( $page_id == "543" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishiwaseda/" );

	}else if( $page_id == "544" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashishinjuku/" );

	}else if( $page_id == "546" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kitasandou/" );

	}else if( $page_id == "549" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tochoumae/" );

	}else if( $page_id == "550" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinjukunishiguchi/" );

	}else if( $page_id == "552" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/wakamatsukawada/" );

	}else if( $page_id == "553" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ushigomeyanagicho/" );

	}else if( $page_id == "554" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ushigomekagurazaka/" );

	}else if( $page_id == "556" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kasuga/" );

	}else if( $page_id == "558" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/uenookachimachi/" );

	}else if( $page_id == "559" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinokachimachi/" );

	}else if( $page_id == "560" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kuramae/" );

	}else if( $page_id == "562" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/morishita/" );

	}else if( $page_id == "563" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kiyosumishirakawa/" );

	}else if( $page_id == "566" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kachidoki/" );

	}else if( $page_id == "567" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tsukijishijou/" );

	}else if( $page_id == "569" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/daimon/" );

	}else if( $page_id == "570" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/akabanebashi/" );

	}else if( $page_id == "574" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kokuritsukyougijou/" );

	}else if( $page_id == "577" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishishinjukugochome/" );

	}else if( $page_id == "578" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakanosakaue/" );

	}else if( $page_id == "581" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ochiaiminaminagasaki/" );

	}else if( $page_id == "582" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinegota/" );

	}else if( $page_id == "584" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toshimaen/" );

	}else if( $page_id == "585" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nerimakasugacho/" );

	}else if( $page_id == "586" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hikarigaoka/" );

	}else if( $page_id == "587" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishimagome/" );

	}else if( $page_id == "588" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/magome/" );

	}else if( $page_id == "589" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nakanobu/" );

	}else if( $page_id == "590" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/togoshi/" );

	}else if( $page_id == "592" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takanawadai/" );

	}else if( $page_id == "593" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sengakuji/" );

	}else if( $page_id == "594" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/mita/" );

	}else if( $page_id == "598" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takaracho/" );

	}else if( $page_id == "601" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashinihonbashi/" );

	}else if( $page_id == "605" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/honjoazumabashi/" );

	}else if( $page_id == "608" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shirokanedai/" );

	}else if( $page_id == "611" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shibakouen/" );

	}else if( $page_id == "612" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/onarimon/" );

	}else if( $page_id == "613" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/uchisaiwaicho/" );

	}else if( $page_id == "614" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hibiya/" );

	}else if( $page_id == "619" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hakusan/" );

	}else if( $page_id == "620" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sengoku/" );

	}else if( $page_id == "621" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sugamo/" );

	}else if( $page_id == "622" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishisugamo/" );

	}else if( $page_id == "623" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinitabashi/" );

	}else if( $page_id == "624" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/itabashikuyakusyomae/" );

	}else if( $page_id == "625" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/itabashihoncho/" );

	}else if( $page_id == "626" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/motohasunuma/" );

	}else if( $page_id == "627" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimurasakaue/" );

	}else if( $page_id == "628" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shimurasanchome/" );

	}else if( $page_id == "629" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hasune/" );

	}else if( $page_id == "630" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishidai/" );

	}else if( $page_id == "631" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takashimadaira/" );

	}else if( $page_id == "632" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shintakashimadaira/" );

	}else if( $page_id == "633" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishitakashimadaira/" );

	}else if( $page_id == "636" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/akebonobashi/" );

	}else if( $page_id == "638" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kudanshita/" );

	}else if( $page_id == "640" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ogawamachi/" );

	}else if( $page_id == "641" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/iwamotocho/" );

	}else if( $page_id == "642" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/bakuroyokoyama/" );

	}else if( $page_id == "643" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hamacho/" );

	}else if( $page_id == "645" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kikukawa/" );

	}else if( $page_id == "646" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sumiyoshi/" );

	}else if( $page_id == "647" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishioojima/" );

	}else if( $page_id == "648" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oojima/" );

	}else if( $page_id == "649" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashioojima/" );

	}else if( $page_id == "650" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/funabori/" );

	}else if( $page_id == "651" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ichinoe/" );

	}else if( $page_id == "652" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/mizue/" );

	}else if( $page_id == "653" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinozaki/" );

	}else if( $page_id == "654" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/minowabashi/" );

	}else if( $page_id == "655" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/arakawaicchumae/" );

	}else if( $page_id == "656" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/arakawakuyakusyomae/" );

	}else if( $page_id == "657" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/arakawanichome/" );

	}else if( $page_id == "658" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/arakawananachome/" );

	}else if( $page_id == "659" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/machiyaekimae/" );

	}else if( $page_id == "660" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/machiyanichome/" );

	}else if( $page_id == "661" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashiogusanchome/" );

	}else if( $page_id == "662" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kumanomae/" );

	}else if( $page_id == "663" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/miyanomae/" );

	}else if( $page_id == "664" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/odai/" );

	}else if( $page_id == "665" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/arakawayuuenchimae/" );

	}else if( $page_id == "666" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/arakawasyakomae/" );

	}else if( $page_id == "667" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kajiwara/" );

	}else if( $page_id == "668" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sakaecho/" );

	}else if( $page_id == "669" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oujiekimae/" );

	}else if( $page_id == "670" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/asukayama/" );

	}else if( $page_id == "671" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takinogawaicchome/" );

	}else if( $page_id == "672" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishigaharayonchome/" );

	}else if( $page_id == "673" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinkoushinzuka/" );

	}else if( $page_id == "674" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/koushinzuka/" );

	}else if( $page_id == "675" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/sugamoshinden/" );

	}else if( $page_id == "676" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ootsukaekimae/" );

	}else if( $page_id == "677" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/mukaihara/" );

	}else if( $page_id == "678" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/higashiikebukuroyonchome/" );

	}else if( $page_id == "679" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/todenzoushigaya/" );

	}else if( $page_id == "680" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kishibojinmae/" );

	}else if( $page_id == "681" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/gakusyuuinshita/" );

	}else if( $page_id == "682" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/omokagebashi/" );

	}else if( $page_id == "690" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/aoi/" );

	}else if( $page_id == "691" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/rokucho/" );

	}else if( $page_id == "694" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/takeshiba/" );

	}else if( $page_id == "695" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hinode/" );

	}else if( $page_id == "696" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shibaurafutou/" );

	}else if( $page_id == "697" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/odaibakaihinkouen/" );

	}else if( $page_id == "698" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/daiba/" );

	}else if( $page_id == "699" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/funenokagakukan/" );

	}else if( $page_id == "700" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/telecomcenter/" );

	}else if( $page_id == "701" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/aomi/" );

	}else if( $page_id == "702" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kokusaitenjizyouseimon/" );

	}else if( $page_id == "703" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ariake/" );

	}else if( $page_id == "704" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ariaketenisunomori/" );

	}else if( $page_id == "705" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shijoumae/" );

	}else if( $page_id == "706" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shintoyosu/" );

	}else if( $page_id == "707" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toyosu/" );

	}else if( $page_id == "709" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tennouzuairu/" );

	}else if( $page_id == "710" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ooikeibajoumae/" );

	}else if( $page_id == "711" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/ryuutsuusentaa/" );

	}else if( $page_id == "712" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/syouwajima/" );

	}else if( $page_id == "713" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/seibijou/" );

	}else if( $page_id == "714" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tenkuubashi/" );

	}else if( $page_id == "715" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hanedakuukoukokusaisenbiru/" );

	}else if( $page_id == "716" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinseibijou/" );

	}else if( $page_id == "717" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hanedakuukoudaiichibiru/" );

	}else if( $page_id == "718" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/hanedakuukoudainibiru/" );

	}else if( $page_id == "720" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinonome/" );

	}else if( $page_id == "721" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kokusaitenjijou/" );

	}else if( $page_id == "722" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tokyoterepooto/" );

	}else if( $page_id == "724" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinagawashiisaido/" );

	}else if( $page_id == "728" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/shinshibamata/" );

	}else if( $page_id == "731" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/akadosyougakkoumae/" );

	}else if( $page_id == "733" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/adachiodai/" );

	}else if( $page_id == "734" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/oogioohashi/" );

	}else if( $page_id == "735" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kouya/" );

	}else if( $page_id == "736" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/kouhoku/" );

	}else if( $page_id == "737" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/nishiaraidaishinishi/" );

	}else if( $page_id == "738" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/yazaike/" );

	}else if( $page_id == "739" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/tonerikouen/" );

	}else if( $page_id == "740" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/toneri/" );

	}else if( $page_id == "741" ){

		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".WWW_URL."station/minumadaishinsuikouen/" );

	}

	return true;
	exit();

}

function send_booking_form(
$namae,$mail,$gender,$today_month,$today_day,$today_year,
$time,$course,$hotel_or_home,$hotel_name,$room_number,
$home_address,$cash_or_credit,$any_request){

	$hotel_or_home_content = "";

	if( $gender == "" ){

		$gender = "empty";

	}

	if( $room_number == "" ){

		$room_number = "empty";

	}

	if( $any_request == "" ){

		$any_request = "empty";

	}

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
	$title = "Booking form【Tokyo Refle】";

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

Tokyo Refle
TEL：03-5206-5134
MAIL：order@tokyo-refle.com
URL：http://www.tokyo-refle.com/en/

EOT;

	$header = "From: order@tokyo-refle.com\n";
	$header .= "Bcc: order@tokyo-refle.com";

	$res = mb_send_mail($mailto,$title,$content,$header);

	if($res == false){

		return false;
		exit();

	}

	return true;
	exit();

}

//追記データ取得
function get_station_add_data($site_type,$data_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$page_type = "station_add";

	$sql = sprintf("select content from page_html where page_type='%s' and site_type='%s' and data_id='%s'",$page_type,$site_type,$data_id);
	$res = mysql_query($sql, $con);
	if($res === false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["content"];
	exit();

}

//本日出勤セラピストのID取得
function get_today_work_therapist_data_wait_time(){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$now_hour = intval(date('H'));

	if($now_hour <= 6){

		//昨日の日付
		$year = intval(date('Y', strtotime('-1 day')));
		$month = intval(date('m', strtotime('-1 day')));
		$day = intval(date('d', strtotime('-1 day')));

	}else{

		$year = intval(date('Y'));
		$month = intval(date('m'));
		$day = intval(date('d'));

	}

	// 出勤しているセラピスト情報を取得するためのSQL文
	$sql = sprintf("
select
therapist_new.id,
therapist_new.name_site,
attendance_new.id as attendance_id,
attendance_new.start_time,
attendance_new.end_time
from attendance_new
left join therapist_new on attendance_new.therapist_id=therapist_new.id
where
therapist_new.delete_flg=0 and
therapist_new.test_flg=0 and
attendance_new.year='%s' and
attendance_new.month='%s' and
attendance_new.day='%s' and
attendance_new.today_absence='0' and
attendance_new.syounin_state='1'",
$year,$month,$day);

	$res = mysql_query($sql, $con);
	if($res == false){
		echo "error!(get_today_work_therapist_id)";
		exit();
	}

	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();

}

//本日出勤セラピストのID取得
function get_today_work_therapist_data_wait_time_bk201405281319(){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$now_hour = intval(date('H'));

	if($now_hour <= 6){

		//昨日の日付
		$year = intval(date('Y', strtotime('-1 day')));
		$month = intval(date('m', strtotime('-1 day')));
		$day = intval(date('d', strtotime('-1 day')));

	}else{

		$year = intval(date('Y'));
		$month = intval(date('m'));
		$day = intval(date('d'));

	}

	// 出勤しているセラピスト情報を取得するためのSQL文
	$sql = sprintf("
select therapist.id,therapist.name_refle,attendance.id as attendance_id,attendance.start_time,attendance.end_time
from attendance
left join therapist on attendance.therapist_id=therapist.id
where therapist.delete_flag=0 and attendance.year='%s' and attendance.month='%s' and
attendance.day='%s' and attendance.today_absence='0'",
$year,$month,$day);
	$res = mysql_query($sql, $con);
	if($res == false){
		echo "error!(get_today_work_therapist_id)";
		exit();
	}

	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();

}

function change_attendance_num_wait_time($hour,$minute,$time_array){

	$time_array_num = count($time_array);

	for($i=1;$i<=$time_array_num;$i++){

		$tmp_hour = $time_array[$i]["hour"];
		$tmp_minute = $time_array[$i]["minute"];

		if( ($tmp_hour==$hour) && ($tmp_minute==$minute) ){

			return $i;
			exit();

		}

	}

	return false;
	exit();

}

function check_wait_time_start_time($now_attendance_num,$start_time){

	$limit_num = $now_attendance_num+0;

	//echo $start_time;
	//exit();

	if($limit_num >= $start_time){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

function check_wait_time_end_time($now_attendance_num,$end_time){

	$limit_num = $now_attendance_num+0;

	//$end_time="1";

	$end_time = $end_time - 1;

	if($limit_num <= $end_time){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

function check_wait_time_reservation($now_attendance_num,$therapist_id,$attendance_id,$start_time,$end_time){

	$limit_num_start = $now_attendance_num + 0;
	$limit_num_end = $now_attendance_num + 0;

	//echo $limit_num_start;
	//echo $limit_num_end;
	//echo $start_time;
	//echo $end_time;
	//echo $attendance_id;
	//echo $therapist_id;
	//exit();

	for($i=$limit_num_start;$i<=$limit_num_end;$i++){

		$exist_flg = check_reservation_data_exist($attendance_id,$i);

		if($exist_flg==true){

			return false;
			exit();

		}

	}

	return true;
	exit();

}

function check_reservation_data_exist($attendance_id,$i){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from reservation_new where attendance_id='%s' and time='%s'",
$attendance_id,$i);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(check_reservation_data_exist)";
		exit();

	}

	//echo $sql."<br />";
	//exit();

	$num = mysql_num_rows($res);

	if( $num > 0 ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

function check_reservation_data_exist_bk201405281339($attendance_id,$i){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select id from reservation where attendance_id='%s' and time='%s'",$attendance_id,$i);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(check_reservation_data_exist)";
		exit();

	}

	//echo $sql."<br />";
	//exit();

	$num = mysql_num_rows($res);

	if( $num > 0 ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

//追記データ取得
function get_ku_add_data($site_type,$data_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$page_type = "area_en";

	$sql = sprintf("select content from page_html where page_type='%s' and site_type='%s' and data_id='%s'",$page_type,$site_type,$data_id);

	//echo $sql;
	//exit();

	$res = mysql_query($sql, $con);
	if($res === false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["content"];
	exit();

}

//追記データ取得
function get_add_data_en($site_type,$page_type,$data_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select content from page_html where page_type='%s' and site_type='%s' and data_id='%s'",$page_type,$site_type,$data_id);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);
	if($res === false){

		header("Location: ".WWW_URL."en/");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["content"];
	exit();

}

//追記データ取得
function get_ku_add_data_ja($site_type,$data_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$page_type = "ku_add";

	$sql = sprintf("select content from page_html where page_type='%s' and site_type='%s' and data_id='%s'",$page_type,$site_type,$data_id);

	//echo $sql;
	//exit();

	$res = mysql_query($sql, $con);
	if($res === false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["content"];
	exit();

}

function get_ku_id_by_url_name($url_name){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select id from ku where url_name='%s'",$url_name);

	//echo $sql;
	//exit();

	$res = mysql_query($sql, $con);
	if($res === false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();

}

function get_time_array_select_option_vip($time){
	/*
	global $time_array;

	if( $time == "" ){

		$time = "-99";

	}
	//include(COMMON_INC."time_array.php");

	$html = "";

	$start_num = 1;

	for($i=$start_num; $i<24; $i++) {

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

		$s_time = sprintf("%s:%s",$hour,$minute);
		$s_time_stamp = strtotime($s_time);
		$now_stamp = strtotime(now);

		if($s_time_stamp >= $now_stamp){
			if( $i == $time ){

				$html .= sprintf("<option value='%s' selected>%s</option>",$i,$time_disp);

			}else{

				$html .= sprintf("<option value='%s'>%s</option>",$i,$time_disp);

			}
		}
	}
	*/

	$t = strtotime('18:00');
	$j = 1;
	for ($i = 0; $i <= 30 * 2 * 11; $i += 30) {
			$time_stamp = strtotime("+{$i} minutes", $t);
			$time = date('H:i', $time_stamp);
			$now_stamp = strtotime(now);
			if($time_stamp >= $now_stamp){
				if($time < '18:00'){
					$html .= sprintf("<option value='%s'>翌%s</option>",$j,$time);
				}else{
					$html .= sprintf("<option value='%s'>%s</option>",$j,$time);
				}
			}
			$j++;
		}

	return $html;
	exit();

}

function get_time_disp($time){
global $time_array;
	//include(INC_PATH."/define.php");

	$hour = $time_array[$time]["hour"];
	$minute = $time_array[$time]["minute"];

	if( $minute == "0" ){

		$minute = "0".$minute;

	}

	if( $hour < 10 ){

		$hour = $hour+24;

	}

	$html = sprintf("%s:%s",$hour,$minute);

	return $html;
	exit();

}

function get_day_option_for_therapist_recruit($mensetsu_day){

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
		$data[$i]["week"] = get_week_name($week);

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

function get_time_option_for_therapist_recruit($mensetsu_time){

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

function get_experience_option_for_therapist_recruit($experience){

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

function get_week_name($week){

	$data = "";

	if($week=="0"){

		$data = "日";

	}else if($week=="1"){

		$data = "月";

	}else if($week=="2"){

		$data = "火";

	}else if($week=="3"){

		$data = "水";

	}else if($week=="4"){

		$data = "木";

	}else if($week=="5"){

		$data = "金";

	}else if($week=="6"){

		$data = "土";

	}else{

		$data = "不明";

	}

	return $data;
	exit();

}

function get_age_option_for_therapist_recruit($age){

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

//選択されたセラピストの空いている一番最初の時間を取得
function get_therapist_first_free_time_bk201405291449($year,$month,$day,$therapist_id,$therapist_start_time){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select
attendance_new.start_time,
attendance_new.end_time,
reservation_new.time
from reservation_new
left join attendance_new on attendance_new.id=reservation_new.attendance_id
where
attendance_new.year='%s' and
attendance_new.month='%s' and
attendance_new.day='%s' and
attendance_new.therapist_id='%s'
order by reservation_new.time asc",
	$year,$month,$day,$therapist_id);

	$res = mysql_query($sql, $con);

	if( $res == false ){

		echo "error!(get_therapist_first_free_time)";
		exit();

	}

	$first_flag = false;
	$end_flag = false;
	$time_match_flag = false;

	$i=0;

	while( $row = mysql_fetch_assoc($res) ){

		if( $end_flag == false ){

			if( $first_flag == false ){

				$tmp_start_time = $row["start_time"];
				$tmp_end_time = $row["end_time"];
				$first_flag = true;

			}

			if( $tmp_start_time !=  $row["time"] ){

				$time = $tmp_start_time;
				$end_flag = true;
				$time_match_flag = true;

			}

			$tmp_start_time++;

		}

		$i++;

	}

	if( ( $time_match_flag == false ) && ( $tmp_start_time < $tmp_end_time) ){

		$time = $tmp_start_time;

	}

	if( $i == 0 ){

		$time = $therapist_start_time;

	}

	return $time;
	exit();

}

function mail_check_for_vip_page($customer_id,$shop_id,$mail){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select mail from customer where delete_flag=0 and customer_id<>'%s' and shop_id='%s'",
$customer_id,$shop_id);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(mail_check_for_vip_page)";
		exit();

	}

	$match_num = 0;

	while($row = mysql_fetch_assoc($res)){

		if($mail == $row["mail"]){

			$match_num++;

		}

	}

	if($match_num > 0){

		return true;
		exit();

	}

	return false;
	exit();

}

function get_customer_mail_for_vip_page($customer_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	//ログインしている顧客のメールアドレスを取得する処理
	//選択されたセラピストの開始時間を取得してそれを変数timeに代入する
	$sql = sprintf("
select mail from customer where delete_flag=0 and customer_id='%s'",
$customer_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(get_customer_mail_for_vip_page)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["mail"];
	exit();

}

function get_start_time_for_vip_page($year,$month,$day,$therapist_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	//選択されたセラピストの開始時間を取得してそれを変数timeに代入する
	$sql = sprintf("
select start_time
from attendance_new
left join therapist_new on attendance_new.therapist_id=therapist_new.id
where
year='%s' and month='%s' and day='%s' and
therapist_new.id='%s'",
$year,$month,$day,$therapist_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(get_start_time_for_vip_page)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["start_time"];
	exit();

}

function get_attendance_data_for_vip_page($shop_area,$year,$month,$day,$time){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	// 出勤しているセラピスト情報を取得するためのSQL文
	$sql = sprintf("
select *,attendance_new.id as attendance_id
from attendance_new
left join therapist_new on attendance_new.therapist_id=therapist_new.id
where
therapist_new.area='%s' and
therapist_new.test_flg='0' and
attendance_new.today_absence='0' and
attendance_new.attendance_adjustment='0' and
attendance_new.year='%s' and
attendance_new.month='%s' and
attendance_new.day='%s' and
attendance_new.syounin_state='1'",
$shop_area,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(get_attendance_data_for_vip_page:1)";
		exit();

	}

	$attendance_data = array();

	$i=0;

	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;

		$attendance_id = $attendance_data[$i]["attendance_id"];

		$sql = sprintf("
select time from reservation_new where attendance_id='%s'",
$attendance_id);

		$res2 = mysql_query($sql, $con);

		if($res2 == false){

			echo "error!(get_attendance_data_for_vip_page:2)";
			exit();

		}

		$j=0;

		while( $row2 = mysql_fetch_assoc($res2) ){

			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;

		}
		if($j==0){

			$attendance_data[$i]["time_num"] = 0;

		}

		$i++;
	}

	$attendance_data_num = count($attendance_data);

	$temp_data = array();

	$k = 0;

	$match_flag = false;

	for( $i=0; $i<$attendance_data_num; $i++ ){

		if( $attendance_data[$i]["time_num"] > 0 ){

			$time_num = $attendance_data[$i]["time_num"];

			$match_flag2 = false;

			for( $j=0; $j<$time_num; $j++ ){

				if( $attendance_data[$i]["time"][$j] == $time ){

					$match_flag2 = true;

				}
			}

			$start_time = $attendance_data[$i]["start_time"];
			$end_time = $attendance_data[$i]["end_time"] - 1;

			//移動時間を考慮すると、ぴったりの開始時間での予約は不可
			$start_time = $start_time + 1;

			if( ( $time < $start_time ) || ( $time > $end_time ) ){

				$match_flag = true;

			}else{

				$match_flag = false;
			}

			if( ( $match_flag == true ) || ( $match_flag2 == true ) ){

			}else{

				$temp_data[$k] = $attendance_data[$i];
				$k++;

			}

		}else{

			$start_time = $attendance_data[$i]["start_time"];
			$end_time = $attendance_data[$i]["end_time"];

			//移動時間を考慮すると、ぴったりの開始時間での予約は不可
			$start_time = $start_time + 1;

			if( ( $time < $start_time ) || ( $time > $end_time ) ){

				$match_flag = true;

			}else{

				$match_flag = false;

			}

			if( $match_flag != true ){

				$temp_data[$k] = $attendance_data[$i];
				$k++;

			}
		}
	}

	return $temp_data;
	exit();

}

//顧客の電話番号を取得
function get_customer_tel_for_vip_page($customer_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select tel from customer where customer_id='%s'",
$customer_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["tel"];
	exit();

}

function vip_page_confirm_action(
$mail,$customer_id,$time_array,$time,$access_type,$customer_name,$year,$month,$day,$week_array,
$week,$mail_hour,$minute,$course,$therapist_name,$customer_tel,$free){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	// 会員情報を更新するSQL文(メールアドレスのみ)
	$sql = sprintf("
update customer set mail='%s' where customer_id='%s'",
$mail,$customer_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		return false;
		exit();

	}

	$mail_hour = $time_array[$time]["hour"];

	if( $mail_hour < 10 ){

		$mail_hour = $mail_hour + 24;

	}

	if( $time_array[$time]["minute"] == '0' ){

		$minute = "00";

	}else{

		$minute = $time_array[$time]["minute"];

	}

	if( $access_type == "sp" ){

		$title = "VIP会員用ご予約フォーム(東京リフレ)【SP】";

	}else{

		$title = "VIP会員用ご予約フォーム(東京リフレ)【PC】";

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "order@tokyo-refle.com";
	$content =<<<EOT

[東京リフレ]VIP会員用ご予約フォームから以下の内容の予約がありました。

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

	$header = "From: order@tokyo-refle.com\n";

	$result = mb_send_mail($mailto,$title,$content,$header);

	if( $result == false ){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		return false;
		exit();

	}

	//コミット
	$sql = "commit";
	mysql_query( $sql, $con );

	//MySQL切断
	mysql_close( $con );

	return true;
	exit();

}

function get_station_id_by_url_name_for_en($url_name){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	//無害化
	$url_name = mysql_real_escape_string($url_name);

	$sql = sprintf("select id from station where en_flg=1 and delete_flg=0 and url_name='%s'",$url_name);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."en/");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();

}

//出勤セラピストの数を取得
function get_therapist_attendance_num_new($year,$month,$day,$area){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select DISTINCT attendance_new.therapist_id from attendance_new
left join therapist_new on therapist_new.id=attendance_new.therapist_id
where
therapist_new.delete_flg='0' and
therapist_new.leave_flg='0' and
therapist_new.test_flg='0' and
attendance_new.kekkin_flg='0' and
attendance_new.today_absence='0' and
attendance_new.year='%s' and
attendance_new.month='%s' and
attendance_new.day='%s' and
attendance_new.area='%s' and
therapist_new.rank<>'19'",
$year,$month,$day,$area);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num = mysql_num_rows($res);

	return $num;
	exit();

}

function get_reservation_message($hour,$area){

	$hour = intval($hour);

	$data = "WEB予約の受付中です";

	if( ($hour < 5) || ($hour > 17) ){

		$result = get_attendance_today_reservation_flg_common($area);

		if( $result == true ){

			$data = "15分～30分で到着できます";

		}else{

			$data = "すぐにご案内できます";

		}

	}else if( $hour > 11 ){

		$data = "ご予約の受付中です";

	}

	return $data;
	exit();

}

function get_day_select_frm_for_reservation_mail_frm($reservation_day){

	$data = get_today_year_month_day_common();

	$year_today = $data["year"];
	$month_today = $data["month"];
	$day_today = $data["day"];

	$html = "";

	$html .= '<select name="reservation_day" id="reservation_day_rev_mail">';

	for( $i=0; $i<30; $i++ ){

		$data = get_mirai_day_common($year_today,$month_today,$day_today,$i);

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

function get_therapist_select_frm_for_reservation_mail_frm($area,$therapist_id,$year,$month,$day){

	$therapisit_data = get_today_attendance_therapisit_data_common($area,$year,$month,$day);

	$therapisit_data_num = count($therapisit_data);

	$html = "";

	$html .= '<select name="therapist_id">';

	$html .= '<option value="-1">指定セラピストなし</option>';

	for( $i=0; $i<$therapisit_data_num; $i++ ){

		$option_value = $therapisit_data[$i]["id"];
		$option_text = $therapisit_data[$i]["name_site"];

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




















?>
