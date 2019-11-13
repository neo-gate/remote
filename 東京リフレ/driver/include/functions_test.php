<?php

function get_attendance_data(){

	include(INC_PATH."/db_connect.php");

	$under6_flag = false;

	$now_hour = intval(date('H'));

	if($now_hour <= 6){

		//昨日の日付
		$year = intval(date('Y', strtotime('-1 day')));
		$month = intval(date('m', strtotime('-1 day')));
		$day = intval(date('d', strtotime('-1 day')));

		$under6_flag = true;

	}else{

		$year = intval(date('Y'));
		$month = intval(date('m'));
		$day = intval(date('d'));

	}

	$today_flag = true;

	// 出勤しているセラピスト情報を取得するためのSQL文
	$sql = sprintf("select *,attendance.id as attendance_id from attendance
left join therapist on attendance.therapist_id=therapist.id
where therapist.delete_flag=0 and year='%s' and month='%s' and day='%s'",
$year,$month,$day);
	$res = mysql_query($sql, $con);
	if($res == false){
		echo "クエリ実行に失敗しました(get_attendance_data)";
		exit();
	}

	$attendance_data = array();

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;
		$i++;

	}

	return $attendance_data;
	exit();

}

//スタッフデータ登録(typeは1:セラピスト,2:ドライバー,3:本部メンバー)
function regist_staff_data($name,$mail,$tel,$type){

	include(INC_PATH."/db_connect.php");

	$shop_id = "-1";

	if( $type == "1" ){

		//セラピストの場合は札幌リフレ所属
		$shop_id = "6";

	}

	$sql = sprintf("insert into staff(shop_id,type,name,mail,tel) values('%s','%s','%s','%s','%s')",
					$shop_id,$type,$name,$mail,$tel);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_staff_data)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}else{

		return true;
		exit();

	}

}

//スタッフデータの取得
function get_staff_data($type){

	include(INC_PATH."/db_connect.php");

	$sql = "select * from staff where delete_flg=0 and type=".$type;
	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_staff_data)";
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

//スタッフデータの取得(汎用)
function get_staff_data_hanyou($type){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from staff where delete_flg=0 and type='%s' and area='%s'",$type,SHOP_AREA);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "error!(get_staff_data)";
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

//スタッフデータの取得(1つ)
function get_staff_data_one($id){

	include(INC_PATH."/db_connect.php");

	$sql = "select * from staff where id=".$id;
	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_staff_data_one)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();

}

//スタッフデータ更新
function update_staff_data($name,$mail,$tel,$staff_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("update staff set name='%s',mail='%s',tel='%s' where id='%s'",
					$name,$mail,$tel,$staff_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_staff_data)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	return true;
	exit();

}

//スタッフデータ削除
function delete_staff_data($staff_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("update staff set delete_flg='1' where id='%s'",$staff_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(delete_staff_data)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	return true;
	exit();

}

//ログイン実行
function login_action($tel){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from staff where tel='%s'",$tel);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(login_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num_rows = mysql_num_rows($res);

	if($num_rows == 0){

		return false;
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$_SESSION["name"] = $row["name"];
	$_SESSION["ticket"] = md5(TICKET_WORD.$row["name"]);
	$_SESSION["staff_id"] = $row["id"];
	$_SESSION["staff_type"] = $row["type"];

	$cookie_value = md5(time()."-".$row["id"]);

	//クッキーの値を保存
	$sql = sprintf("update staff set login_cookie='%s' where id='%s'",$cookie_value,$row["id"]);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(login_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}else{

		//クッキー(有効期限1年)
		setcookie ("AutoLogin", $cookie_value, time()+(60*60*24*30*12));

	}

	return true;
	exit();

}

//ログイン実行(東京リフレ)
function login_action_refle($tel){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from staff where tel='%s'",$tel);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(login_action_refle)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num_rows = mysql_num_rows($res);

	if($num_rows == 0){

		return false;
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$_SESSION["name"] = $row["name"];
	$_SESSION["ticket_refle"] = md5(TICKET_WORD.$row["name"]);
	$_SESSION["staff_id"] = $row["id"];
	$_SESSION["staff_type"] = $row["type"];
	$_SESSION["staff_id_refle"] = $row["id"];
	$_SESSION["staff_type_refle"] = $row["type"];

	$cookie_value = md5(time()."-".$row["id"]);

	//クッキーの値を保存
	$sql = sprintf("update staff set login_cookie_refle='%s' where id='%s'",$cookie_value,$row["id"]);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(login_action_refle)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}else{

		//クッキー(有効期限1年)
		setcookie ("AutoLoginRefle", $cookie_value, time()+(60*60*24*30*12));

	}

	return true;
	exit();

}

//ログイン実行(汎用)
function login_action_hanyou($tel){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from staff where tel='%s' and (area='%s' or type='3')",$tel,SHOP_AREA);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "error!(login_action_hanyou:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num_rows = mysql_num_rows($res);

	if($num_rows == 0){

		return false;
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$_SESSION["name"] = $row["name"];
	$_SESSION[SESSION_TICKET] = md5(TICKET_WORD.$row["name"]);
	$_SESSION["staff_id"] = $row["id"];
	$_SESSION["staff_type"] = $row["type"];
	$_SESSION[SESSION_STAFF_ID] = $row["id"];
	$_SESSION[SESSION_STAFF_TYPE] = $row["type"];

	$cookie_value = md5(time()."-".$row["id"]);

	//クッキーの値を保存
	$sql = sprintf("update staff set %s='%s' where id='%s'",TBL_COOKIE_COLUMN,$cookie_value,$row["id"]);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "error!(login_action_hanyou:2)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}else{

		//クッキー(有効期限1年)
		setcookie (AutoLoginArea, $cookie_value, time()+(60*60*24*30*12));

	}

	return true;
	exit();

}

//ログイン実行(管理ページ)
function login_action_man($tel){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from staff where type='3' and tel='%s'",$tel);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(login_action_man)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num_rows = mysql_num_rows($res);

	if($num_rows == 0){

		return false;
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$_SESSION["man_name"] = $row["name"];
	$_SESSION["man_ticket"] = md5(TICKET_WORD.$row["name"]);
	$_SESSION["man_staff_id"] = $row["id"];

	$cookie_value = md5(time()."-".$row["id"]);

	//クッキーの値を保存
	$sql = sprintf("update staff set login_cookie_man='%s' where id='%s'",$cookie_value,$row["id"]);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(login_action_man)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}else{

		//クッキー(有効期限1年)
		setcookie ("AutoLoginMan", $cookie_value, time()+(60*60*24*30*12));

	}

	return true;
	exit();

}

//オートログインをチェック
function check_auto_login(){

	include(INC_PATH."/db_connect.php");

	if(isset($_COOKIE["AutoLogin"])==true){

		$auto_login_key = $_COOKIE["AutoLogin"];

		$sql = "select * from staff where delete_flg=0 and login_cookie='".$auto_login_key."'";

		$res = mysql_query($sql, $con);

		if($res == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_auto_login)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$data_num = mysql_num_rows($res);

		if($data_num==1){

			$row = mysql_fetch_array($res);

			$_SESSION["name"] = $row["name"];
			$_SESSION["ticket"] = md5(TICKET_WORD.$row["name"]);
			$_SESSION["staff_id"] = $row["id"];
			$_SESSION["staff_type"] = $row["type"];

			return true;
			exit();

		}else{

			//オートログイン用クッキーの削除
			setcookie('AutoLogin', '', time() - 60);

			return false;
			exit();

		}

	}else{

		return false;
		exit();

	}

}

//オートログインをチェック(東京リフレ)
function check_auto_login_refle(){

	include(INC_PATH."/db_connect.php");

	if(isset($_COOKIE["AutoLoginRefle"])==true){

		$auto_login_key = $_COOKIE["AutoLoginRefle"];

		$sql = "select * from staff where delete_flg=0 and login_cookie_refle='".$auto_login_key."'";

		$res = mysql_query($sql, $con);

		if($res == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_auto_login_refle)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$data_num = mysql_num_rows($res);

		if($data_num==1){

			$row = mysql_fetch_array($res);

			$_SESSION["name"] = $row["name"];
			$_SESSION["ticket_refle"] = md5(TICKET_WORD.$row["name"]);
			$_SESSION["staff_id"] = $row["id"];
			$_SESSION["staff_type"] = $row["type"];
			$_SESSION["staff_id_refle"] = $row["id"];
			$_SESSION["staff_type_refle"] = $row["type"];

			return true;
			exit();

		}else{

			//オートログイン用クッキーの削除
			setcookie('AutoLoginRefle', '', time() - 60);

			return false;
			exit();

		}

	}else{

		return false;
		exit();

	}

}

//オートログインをチェック(汎用)
function check_auto_login_hanyou(){

	include(INC_PATH."/db_connect.php");

	if( isset($_COOKIE[AutoLoginArea]) == true ){

		$auto_login_key = $_COOKIE[AutoLoginArea];

$sql = sprintf("
select * from staff where delete_flg=0 and %s='%s' and (area='%s' or type='3')",
TBL_COOKIE_COLUMN,$auto_login_key,SHOP_AREA);

		$res = mysql_query($sql, $con);

		if($res == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_auto_login_hanyou)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$data_num = mysql_num_rows($res);

		if($data_num==1){

			$row = mysql_fetch_array($res);

			$_SESSION["name"] = $row["name"];
			$_SESSION[SESSION_TICKET] = md5(TICKET_WORD.$row["name"]);
			$_SESSION["staff_id"] = $row["id"];
			$_SESSION["staff_type"] = $row["type"];
			$_SESSION[SESSION_STAFF_ID] = $row["id"];
			$_SESSION[SESSION_STAFF_TYPE] = $row["type"];

			return true;
			exit();

		}else{

			//オートログイン用クッキーの削除
			setcookie(AutoLoginArea, '', time() - 60);

			return false;
			exit();

		}

	}else{

		return false;
		exit();

	}

}

//オートログインをチェック(管理ページ)
function check_auto_login_man(){

	include(INC_PATH."/db_connect.php");

	if(isset($_COOKIE["AutoLoginMan"])==true){

		$auto_login_key = $_COOKIE["AutoLoginMan"];

		$sql = "select * from staff where delete_flg=0 and login_cookie_man='".$auto_login_key."'";

		$res = mysql_query($sql, $con);

		if($res == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_auto_login_man)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$data_num = mysql_num_rows($res);

		if($data_num==1){

			$row = mysql_fetch_array($res);

			$_SESSION["man_name"] = $row["name"];
			$_SESSION["man_ticket"] = md5(TICKET_WORD_MAN.$row["name"]);
			$_SESSION["man_staff_id"] = $row["id"];

			return true;
			exit();

		}else{

			//オートログイン用クッキーの削除
			setcookie('AutoLoginMan', '', time() - 60);

			return false;
			exit();

		}

	}else{

		return false;
		exit();

	}

}

//曜日取得
function get_today_week_name(){

	$week = intval(date('w'));
	$name = "";

	if( $week == "0" ){

		$name = "日";

	}else if( $week == "1" ){

		$name = "月";

	}else if( $week == "2" ){

		$name = "火";

	}else if( $week == "3" ){

		$name = "水";

	}else if( $week == "4" ){

		$name = "木";

	}else if( $week == "5" ){

		$name = "金";

	}else if( $week == "6" ){

		$name = "土";

	}

	return $name;
	exit();

}

//曜日取得(引数あり)
function get_today_week_name2($week){

	$name = "";

	if( $week == "0" ){

		$name = "日";

	}else if( $week == "1" ){

		$name = "月";

	}else if( $week == "2" ){

		$name = "火";

	}else if( $week == "3" ){

		$name = "水";

	}else if( $week == "4" ){

		$name = "木";

	}else if( $week == "5" ){

		$name = "金";

	}else if( $week == "6" ){

		$name = "土";

	}

	return $name;
	exit();

}

//配列データをDB格納用に変更
function get_insert_array_data($array_data){

	if($array_data == null){

		$insert_array_data = "";

	}else{

		$array_data_num = count($array_data);

		for($i=0;$i<$array_data_num;$i++){

			if($i==($array_data_num-1)){

				$insert_array_data .= $array_data[$i];

			}else{

				$insert_array_data .= $array_data[$i].",";

			}
		}
	}

	return $insert_array_data;
	exit();

}

//出勤データ格納
function regist_today_work_data($therapist,$driver){

	include(INC_PATH."/db_connect.php");

	$year = intval(date('Y'));
	$month = intval(date('m'));
	$day = intval(date('d'));

	//本日のデータがあるかどうかのチェック(無ければインサート)
	check_today_work_data($year,$month,$day);

	$insert_therapist = get_insert_array_data($therapist);
	$insert_driver = get_insert_array_data($driver);

	$sql = sprintf("update work_today set therapist='%s',driver='%s'
					where delete_flg=0 and year='%s' and month='%s' and day='%s'",
					$insert_therapist,$insert_driver,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_today_work_data)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	return true;
	exit();

}

//本日のデータがあるかどうかのチェック(無ければインサート)
function check_today_work_data($year,$month,$day){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select id from work_today where delete_flg=0 and year='%s' and month='%s' and day='%s'",
					$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_today_work_data)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$data_num = mysql_num_rows($res);

	if( $data_num == 0 ){

		//データをインサート
		$sql = sprintf("insert into work_today(year,month,day) values('%s','%s','%s')",
						$year,$month,$day);

		$res = mysql_query($sql, $con);

		if($res == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_today_work_data)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

	}

	return true;
	exit();

}

//出勤データの取得
function get_today_work_data(){

	include(INC_PATH."/db_connect.php");

	$year = intval(date('Y'));
	$month = intval(date('m'));
	$day = intval(date('d'));

	$sql = sprintf("select * from work_today where delete_flg=0 and year='%s' and month='%s' and day='%s'",
					$year,$month,$day);

	$res = mysql_query($sql, $con);

	$data_num = mysql_num_rows($res);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_today_work_data)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$data = array();

	$data["therapist"] = explode(",",$row["therapist"]);
	$data["driver"] = explode(",",$row["driver"]);

	return $data;
	exit();

}

//出勤スタッフデータ取得
function get_today_staff($type){

	$data = get_today_work_data();

	if($type=="1"){

		$staff = $data["therapist"];

	}else if($type=="2"){

		$staff = $data["driver"];

	}else{

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_today_staff)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}


	$staff_num = count($staff);

	$data = array();
	$x = 0;

	for($i=0;$i<$staff_num;$i++){

		if($staff[$i] != ""){

			$data[$x]["id"] = $staff[$i];
			$data[$x]["name"] = get_staff_name($staff[$i]);
			$x++;

		}

	}

	return $data;
	exit();

}

//スタッフの名前を取得
function get_staff_name($id){

	include(INC_PATH."/db_connect.php");

	$sql = "select name from staff where id=".$id;

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_staff_name)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["name"];
	exit();

}

//メール送信処理(顧客情報)
function send_customer_info_mail($year,$month,$day,$namae,$course,$age,$bunrui,$operation,$other,$staff_name){

	$operation_num = count($operation);
	$operation_name = "";

	if($operation_num==0){

		$operation_name = "未選択";

	}else{

		for($i=0;$i<$operation_num;$i++){

			$tmp = $operation[$i];

			if( $tmp == "1" ){

				$operation_name .= "アロマトリートメント　";

			}else if( $tmp == "2" ){

				$operation_name .= "リンパドレナージュ　";

			}else if( $tmp == "3" ){

				$operation_name .= "パウダーリフレクソロジー　";

			}else if( $tmp == "4" ){

				$operation_name .= "オイルリフレクソロジー　";

			}else if( $tmp == "5" ){

				$operation_name .= "指圧　";

			}else if( $tmp == "6" ){

				$operation_name .= "ソフト整体　";

			}else if( $tmp == "7" ){

				$operation_name .= "ヘッドマッサージ　";

			}else if( $tmp == "8" ){

				$operation_name .= "ストレッチ　";

			}

		}

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	$title = "[業務連絡BBS]顧客データ";
	$content = "
業務連絡BBSから以下の顧客データが送信されました。\n
-----------------------------------------------------------------\n
送信者:".$staff_name."\n
日にち:".$year."年".$month."月".$day."日\n
顧客名:".$namae."\n
コース:".$course."\n
年代:".$age."\n
分類:".$bunrui."\n
施術:".$operation_name."\n
特記事項:\n".$other."\n";

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(send_customer_info_mail)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	return true;
	exit();

}

//投稿データの登録
function regist_contribute($naiyou,$from_staff_id,$from_staff_name,$from_staff_type,$to_staff_id,$to_staff_mail,$to_staff_name,$base_url){

	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	if($from_staff_type == "3"){

		$from_staff_id = $from_staff_id;
		$to_staff_id = $to_staff_id;

	}else{

		$from_staff_id = $from_staff_id;
		$to_staff_id = $from_staff_id;

	}

	//threadに登録
	$sql = sprintf("insert into thread(created,updated,from_staff_id,to_staff_id) values('%s','%s','%s','%s')",
					$now,$now,$from_staff_id,$to_staff_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_contribute)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	// インサートしたデータのIDを取得
	$thread_id = mysql_insert_id();

	//thread_commentに登録
	$sql = sprintf("insert into thread_comment(created,thread_id,staff_id,staff_type,content) values('%s','%s','%s','%s','%s')",
					$now,$thread_id,$from_staff_id,$from_staff_type,$naiyou);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_contribute)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$staff_id = $to_staff_id;
	$from_staff_name = $from_staff_name;
	$to_staff_name = $to_staff_name;
	$staff_type = $from_staff_type;
	$base_url = $base_url;
	if( $staff_type == "3" ){

		$mailto = $to_staff_mail;

	}else{

		$mailto = "info@neo-gate.jp";

	}
	$naiyou = $naiyou;

	$result = send_contribute_mail($staff_id,$from_staff_name,$to_staff_name,$staff_type,$base_url,$mailto,$naiyou);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_contribute)";
		header("Location: ".WWW_URL."error.php");
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

//投稿メール
function send_contribute_mail($staff_id,$from_staff_name,$to_staff_name,$staff_type,$base_url,$mailto,$naiyou){

	$year = intval(date('Y'));
	$month = intval(date('m'));
	$day = intval(date('d'));
	$hour = intval(date('H'));
	$minute = intval(date('i'));

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");

	if( $staff_type == "3" ){

		$url = $base_url."index.php?staff_id=".$staff_id;

		$mailto = $mailto;
		$title = "[業務連絡BBS]新着のコメント";
		$content = "
受信日時:".$year."年".$month."月".$day."日".$hour."時".$minute."分\n
".$to_staff_name."さん\n
新着のコメントがあります。\n
確認してください。\n
".$url."\n";

		$header = "From: info@neo-gate.jp\n";
		$header .= "Cc: info@neo-gate.jp\n";
		//$header .= "Bcc: minamikawa@neo-gate.jp";

	}else{

		$url = $base_url."index.php?staff_id=".$staff_id;

		$mailto = $mailto;
		$title = "[業務連絡BBS]投稿のお知らせ";
		$content = "
業務連絡BBSに以下の内容が投稿されました。\n
-----------------------------------------------------------------\n
投稿者:".$from_staff_name."\n
投稿日時:".$year."年".$month."月".$day."日".$hour."時".$minute."分\n
URL:".$url."\n
投稿内容:\n".$naiyou."\n";

		$header = "From: info@neo-gate.jp\n";
		//$header .= "Bcc: minamikawa@neo-gate.jp";

	}

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return $result;
	exit();

}

//スタッフのメールアドレスを取得
function get_staff_mail($id){

	include(INC_PATH."/db_connect.php");

	$sql = "select mail from staff where id=".$id;

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_staff_mail)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["mail"];
	exit();

}

//スレッドのデータを取得
function get_thread_data($data_id,$data_type){

	include(INC_PATH."/db_connect.php");

	if( ($data_id == "") || ($data_id == "-1") ){

		$sql = sprintf("select *,thread.id as th_id from thread left join staff on staff.id=thread.to_staff_id where staff.type='%s' and thread.delete_flg=0 order by thread.updated desc limit 0,30",$data_type);

	}else{

		$sql = sprintf("select *,thread.id as th_id from thread left join staff on staff.id=thread.to_staff_id where staff.type='%s' and thread.to_staff_id='%s' and thread.delete_flg=0 order by thread.updated desc limit 0,30",$data_type,$data_id);

	}

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_thread_data)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$id = $row["th_id"];

		//コメントデータの取得
		$comment = get_thread_comment_data($id);
		$comment_num = count($comment);

		$list_data[$i] = $row;

		$list_data[$i]["comment"] = $comment;
		$list_data[$i]["comment_num"] = $comment_num;

		$i++;
	}

	return $list_data;
	exit();

}

//スレッドのデータを取得(汎用)
function get_thread_data_hanyou($data_id,$data_type){

	include(INC_PATH."/db_connect.php");

	if( ($data_id == "") || ($data_id == "-1") ){

$sql = sprintf("
select *,thread.id as th_id from thread
left join staff on staff.id=thread.to_staff_id
where staff.type='%s' and thread.delete_flg=0 and staff.area='%s' and staff.delete_flg=0
order by thread.updated desc limit 0,30",
$data_type,SHOP_AREA);

	}else{

$sql = sprintf("
select *,thread.id as th_id from thread
left join staff on staff.id=thread.to_staff_id
where staff.type='%s' and thread.to_staff_id='%s' and thread.delete_flg=0 and staff.area='%s' and staff.delete_flg=0
order by thread.updated desc limit 0,30",
$data_type,$data_id,SHOP_AREA);

	}

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "error!(get_thread_data_hanyou)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$id = $row["th_id"];

		//コメントデータの取得
		$comment = get_thread_comment_data($id);
		$comment_num = count($comment);

		$list_data[$i] = $row;

		$list_data[$i]["comment"] = $comment;
		$list_data[$i]["comment_num"] = $comment_num;

		$i++;
	}

	return $list_data;
	exit();

}

//コメントデータの取得
function get_thread_comment_data($id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from thread_comment where delete_flg=0 and thread_id='%s' order by created desc",$id);

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_thread_comment_data)";
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

//整形された日付情報取得
function get_seikei_time($date){

	$month = date('m',$date);
	$day = date('d',$date);
	$hour = date('H',$date);
	$minute = date('i',$date);
	$week = date('w',$date);
	$week_name = get_today_week_name2($week);

	$data = $month."/".$day."（".$week_name."）".$hour."：".$minute;

	return $data;
	exit();

}

//整形された日付情報取得(秒まで)
function get_seikei_time2($date){

	$hour = date('H',$date);
	$minute = date('i',$date);
	$s = date('s',$date);

	$data = $hour."：".$minute."：".$s;

	return $data;
	exit();

}

//投稿データの更新
function update_contribute($thread_id,$naiyou,$staff_id,$staff_type,$staff_name,$base_url){

	include(INC_PATH."/db_connect.php");

	//to_staff_idの取得
	$to_staff_id = get_to_staff_id($thread_id);

	//スタッフのメールアドレスを取得
	$to_staff_mail = get_staff_mail($to_staff_id);

	//スタッフの名前を取得
	$to_staff_name = get_staff_name($to_staff_id);

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	//threadのupdatedを更新する
	$sql = sprintf("update thread set updated='%s' where id='%s'",$now,$thread_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_contribute)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	//thread_commentに登録
	$sql = sprintf("insert into thread_comment(created,thread_id,staff_id,staff_type,content) values('%s','%s','%s','%s','%s')",
					$now,$thread_id,$staff_id,$staff_type,$naiyou);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_contribute)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$staff_id = $to_staff_id;
	$from_staff_name = $staff_name;
	$to_staff_name = $to_staff_name;
	$staff_type = $staff_type;
	$base_url = $base_url;
	if( $staff_type == "3" ){

		$mailto = $to_staff_mail;

	}else{

		$mailto = "info@neo-gate.jp";

	}
	$naiyou = $naiyou;

	$result = send_contribute_mail($staff_id,$from_staff_name,$to_staff_name,$staff_type,$base_url,$mailto,$naiyou);



	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_contribute)";
		header("Location: ".WWW_URL."error.php");
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

//to_staff_idの取得
function get_to_staff_id($id){

	include(INC_PATH."/db_connect.php");

	$sql = "select to_staff_id from thread where id=".$id;

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_to_staff_id)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["to_staff_id"];
	exit();

}

//振り分け
function furiwake(){

	//echo "furiwake";exit();

	$header = getallheaders();
	$agent = $header["User-Agent"] ;
	$ua=$_SERVER['HTTP_USER_AGENT'];


	//携帯電話の振替処理
	if((preg_match("/DoCoMo/",$agent)) || (preg_match("/^UP.Browser|^KDDI/", $agent)) || (preg_match("/^J-PHONE|^Vodafone|^SoftBank/", $agent))){

		$type = "m";

	}else{

		$type = "sp";

	}

	return $type;
	exit();

}

//セラピスト名取得
function get_therapist_name_by_therapist_id($therapist_id,$area){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	if( $area=="tokyo" ){

		$sql = sprintf("select name_refle from therapist where id='%s'",$therapist_id);

	}else if( $area=="sapporo" ){

		$sql = sprintf("select name_sapporo from therapist_sapporo where id='%s'",$therapist_id);

	}else{

		$sql = sprintf("select name_site from therapist_new where id='%s'",$therapist_id);

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

	}else{

		$name = $row["name_site"];

	}

	return $name;
	exit();

}

function get_staff_name_by_staff_id($staff_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select name from staff_new_new where id='%s'",$staff_id);

	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$name = $row["name"];

	return $name;
	exit();

}

//セラピスト名取得(本名)
function get_therapist_name_by_therapist_id_honmyou_new($therapist_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select name from therapist_new where id='%s'",$therapist_id);

	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$name = $row["name"];

	return $name;
	exit();

}

//セラピストのメールアドレス
function get_therapist_mail_by_therapist_id($therapist_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select mail from therapist_new where id='%s'",$therapist_id);

	$res = mysql_query($sql, $con);
	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["mail"];
	exit();

}

function get_staff_month_attendance_data($staff_id, $year, $month){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select * from attendance_staff_new where staff_id='%s' and year='%s' and month='%s' order by day asc",
$staff_id, $year, $month);

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_staff_month_attendance_data)";
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

function get_therapist_month_attendance_data_kensyuu($therapist_id, $year, $month){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select * from attendance_kensyuu where therapist_id='%s' and year='%s' and month='%s' order by day asc",
$therapist_id, $year, $month);

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_month_attendance_data_kensyuu)";
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

function get_max_day($year,$month){

	$uru_flag = false;

	//うるう年判定
	if(($year%4)==0){
		if(($year%100)==0){
			if(($year%400)==0){
				$uru_flag = true;
			}else{
				$uru_flag = false;
			}
		}else{
			$uru_flag = true;
		}
	}else{
		$uru_flag = false;
	}
	if($month==2){
		if($uru_flag==true){
			$day = 29;
		}else{
			$day = 28;
		}
	}else{
		if(($month==4)||($month==6)||($month==9)||($month==11)){
			$day = 30;
		}else{
			$day = 31;
		}
	}

	return $day;
	exit();

}

function get_day_data($year,$month,$max_day){

	$data = array();

	for($i=0;$i<$max_day;$i++){

		$data[$i]["year"] = $year;
		$data[$i]["month"] = $month;
		$day = $i+1;
		$data[$i]["day"] = $day;
		$w = intval(date("w", mktime(0, 0, 0, $month, $day, $year)));
		$data[$i]["week"] = get_ja_week($w);
	}

	return $data;
	exit();

}

function get_ja_week($w){

	$word = "";

	if($w==0){

		$word = "日";

	}else if($w==1){

		$word = "月";

	}else if($w==2){

		$word = "火";

	}else if($w==3){

		$word = "水";

	}else if($w==4){

		$word = "木";

	}else if($w==5){

		$word = "金";

	}else if($w==6){

		$word = "土";

	}

	return $word;
	exit();

}

function test(){

	return "test!";
	exit;

}

function get_attendance_list_data_staff($attendance_data,$day_data,$staff_id,$area,$ch){

	include(INC_PATH."/time_array.php");

	$data = array();

	$k=0;

	$day_data_num = count($day_data);
	$attendance_data_num = count($attendance_data);

	for($i=0;$i<$day_data_num;$i++){

		$year = $day_data[$i]["year"];
		$month = $day_data[$i]["month"];
		$day = $day_data[$i]["day"];
		$week = $day_data[$i]["week"];

		$shop_holiday_flg = false;

		if( ( ($year=="2014") && ($month=="12") && ($day=="31") ) || ( ($year=="2015") && ($month=="1") && ($day=="1") ) ){

			$shop_holiday_flg = true;

		}

		$past_flg = today_past_check($year,$month,$day);

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
					$kekkin_flg = $attendance_data[$j]["kekkin_flg"];

					if( ($year==$year_a) && ($month==$month_a) && ($day==$day_a) ){

						$match_flg = true;

					}

				}

			}

			$html = "";

			if( $match_flg == true ){

				$start_time_ta = $time_array[$start_time]["minute"];
				$end_time_ta = $time_array[$end_time]["minute"];

				if($start_time_ta=="0"){

					$start_time_ta = "0".$start_time_ta;

				}

				if($end_time_ta=="0"){

					$end_time_ta = "0".$end_time_ta;

				}

				$html .= '<form action="edit.php" method="post">';
				$html .= '<input type="hidden" name="staff_id" value="'.$staff_id.'" />';
				$html .= '<input type="hidden" name="area" value="'.$area.'" />';
				$html .= '<input type="hidden" name="year" value="'.$year.'" />';
				$html .= '<input type="hidden" name="month" value="'.$month.'" />';
				$html .= '<input type="hidden" name="day" value="'.$day.'" />';
				$html .= '<input type="hidden" name="start_time" value="'.$start_time.'" />';
				$html .= '<input type="hidden" name="end_time" value="'.$end_time.'" />';
				$html .= '<input type="hidden" name="ch" value="'.$ch.'" />';

				$html .= '<div class="shift_list_time">';

				$html .= $day."(".$week.")";
				$html .= "　";
				$html .= $time_array[$start_time]["hour"]."時".$start_time_ta."分";
				$html .= "／";
				$html .= $time_array[$end_time]["hour"]."時".$end_time_ta."分";
				$html .= "　";

				$html .= '</div>';

				$html .= '<div class="shift_list_right_area_1">';

				if($kekkin_flg=="1"){

					$html .= '<div class="shift_list_right_area_kekkin">欠勤</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= '<input type="submit" value="編集" name="send_list_edit" />';
					$html .= '</div>';

				}else if($syounin_state=="1"){

					$html .= '<div class="shift_list_right_area_kakutei"><span style="color:blue;">確</span></div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= '<input type="submit" value="編集" name="send_list_edit" />';
					$html .= '</div>';

				}else if($syounin_state=="2"){

					$html .= '<div class="shift_list_right_area_fusyounin">不承認</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= '<input type="submit" value="編集" name="send_list_edit" />';
					$html .= '</div>';

				}else if($syounin_state=="3"){

					$html .= '<div class="shift_list_right_area_shimekiri">締切</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= '&nbsp;';
					$html .= '</div>';

				}else{

					$html .= '<div class="shift_list_right_area_kari">仮</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= '<input type="submit" value="編集" name="send_list_edit" />';
					$html .= '</div>';

				}

				$html .= '<br class="clear" />';

				$html .= '</div>';

				$html .= '<br class="clear" />';

				$html .= '</form>';

				$data[$k] = $html;

			}else{

				$html .= '<form action="add.php" method="post">';
				$html .= '<input type="hidden" name="staff_id" value="'.$staff_id.'" />';
				$html .= '<input type="hidden" name="area" value="'.$area.'" />';
				$html .= '<input type="hidden" name="year" value="'.$year.'" />';
				$html .= '<input type="hidden" name="month" value="'.$month.'" />';
				$html .= '<input type="hidden" name="day" value="'.$day.'" />';
				$html .= '<input type="hidden" name="ch" value="'.$ch.'" />';
				$html .= '<div class="shift_list_time">';
				$html .= $day."(".$week.")";

				$html .= '</div>';
				$html .= '<div class="shift_list_right_area_2">';

				if( $shop_holiday_flg == false ){

					$html .= '<input type="submit" value="追加" name="send_list_add" />';

				}else{

					$html .= '店休';

				}

				$html .= '</div>';

				$html .= '<br class="clear" />';

				$html .= '</form>';

				$data[$k] = $html;

			}

			$k++;

		}

	}

	return $data;
	exit();

}

function get_next_month($month){

	$month = $month + 1;

	if($month==13){

		$month = 1;

	}

	return $month;
	exit();

}

function escape_for_db($data){

	include(INC_PATH."/db_connect.php");

	$data = mysql_real_escape_string($data);

	return $data;
	exit();

}

function get_next_year($year,$month){

	$month = $month+1;

	if($month==13){

		$year = $year + 1;

	}

	return $year;
	exit();

}

//shift_historyを取得
function get_shift_history($area,$therapist_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select shift_history.created,shift_history.staff_type,shift_history.message,therapist_new.name
from shift_history
left join therapist_new on therapist_new.id=shift_history.therapist_id
where
shift_history.delete_flg=0 and
shift_history.therapist_id='%s' and
shift_history.area='%s'
order by shift_history.created desc limit 0,5",
$therapist_id,$area);
	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_shift_history)";
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

function get_shift_common_comment_data($year,$month,$day,$area){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select content from shift_common_comment where delete_flg=0 and area='%s' and year='%s' and month='%s' and day='%s'",
$area,$year,$month,$day);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_shift_common_comment_data)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["content"];
	exit();

}

function creat_period_select_option($therapist_id,$today_year,$today_month,$next_year,$next_month,$area){

	$today_zenhan_flg = false;
	$today_kouhan_flg = false;
	$next_zenhan_flg = false;
	$next_kouhan_flg = false;

	$type = "zenhan";
	$today_zenhan_flg = check_attendance_data_exist($type,$therapist_id,$today_year,$today_month,$area);

	$type = "kouhan";
	$today_kouhan_flg = check_attendance_data_exist($type,$therapist_id,$today_year,$today_month,$area);

	$type = "zenhan";
	$next_zenhan_flg = check_attendance_data_exist($type,$therapist_id,$next_year,$next_month,$area);

	$type = "kouhan";
	$next_kouhan_flg = check_attendance_data_exist($type,$therapist_id,$next_year,$next_month,$area);

	$html = "";

	if($today_zenhan_flg==false){

		$html .= '<option value="'.$today_year.'_'.$today_month.'_zenhan">'.$today_month.'月前半</option>';

	}

	if($today_kouhan_flg==false){

		$html .= '<option value="'.$today_year.'_'.$today_month.'_kouhan">'.$today_month.'月後半</option>';

	}

	if($next_zenhan_flg==false){

		$html .= '<option value="'.$next_year.'_'.$next_month.'_zenhan">'.$next_month.'月前半</option>';

	}

	if($next_kouhan_flg==false){

		$html .= '<option value="'.$next_year.'_'.$next_month.'_kouhan">'.$next_month.'月後半</option>';

	}

	return $html;
	exit();

}

function check_attendance_data_exist($type,$therapist_id,$year,$month,$area){

	include(INC_PATH."/db_connect.php");

	if($type=="zenhan"){

		$sql = sprintf("
select id from attendance_new where therapist_id='%s' and year='%s' and month='%s' and (day>=1) and (day<=15)",
$therapist_id,$year,$month,$day);

	}else if($type=="kouhan"){

		$sql = sprintf("
select id from attendance_new where therapist_id='%s' and year='%s' and month='%s' and (day>=16) and (day<=31)",
$therapist_id,$year,$month,$day);

	}else{

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_attendance_data_exist):1";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_attendance_data_exist):2";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$num = mysql_num_rows($res);

	if($num > 0){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

function today_past_check($year,$month,$day){

	$today_year = intval(date("Y"));
	$today_month = intval(date("m"));
	$today_day = intval(date("d"));

	$hour = 12;
	$minute = 0;
	$second = 0;

	$today_timestamp = mktime($hour, $minute, $second, $today_month, $today_day, $today_year);

	$hikisuu_timestamp = mktime($hour, $minute, $second, $month, $day, $year);

	if( $today_timestamp > $hikisuu_timestamp ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

function get_kikan_name($kikan){

	if($kikan=="zenhan"){

		$data = "前半";

	}else if($kikan=="kouhan"){

		$data = "後半";

	}

	return $data;
	exit();

}

function get_shift_regist_list_data($day_data,$kikan,$therapist_id,$shift_time_data,$att_area_select_disp_flg,$area){

	include(INC_PATH."/time_array.php");

	//echo "xxx";exit();

	$day_data_num = count($day_data);

	$jitaku_taiki_flg = get_jitaku_taiki_flg_by_therapist_id($therapist_id);

	$data = array();

	$x = 0;

	for($i=0;$i<$day_data_num;$i++){

		$year = $day_data[$i]["year"];
		$month = $day_data[$i]["month"];
		$day = $day_data[$i]["day"];
		$week = $day_data[$i]["week"];

		$shop_holiday_flg = false;

		if( ( ($year=="2014") && ($month=="12") && ($day=="31") ) || ( ($year=="2015") && ($month=="1") && ($day=="1") ) ){

			$shop_holiday_flg = true;

		}

		if( $kikan == "zenhan" ){

			if( $i <= 14 ){

				$num = $x+1;

				$html = "";
				$html .= '<div class="shift_regist_time">';
				$html .= $day."(".$week.")";
				$html .= '</div>';

				$html .= '<div style="float:left;">';

				if( $shop_holiday_flg == false ){

				$html .= '<div class="shift_regist_select_1">';
				$html .= '<select name="start_time_'.$num.'">';
				$html .= '<option value="-1">未選択</option>';

				$type = "start";
				$html .= get_shift_time_option_for_selected($type,$shift_time_data,$num);

				$html .= '</select>';
				$html .= '</div>';
				$html .= '<div class="shift_regist_select_2">';
				$html .= '<select name="end_time_'.$num.'">';
				$html .= '<option value="-1">未選択</option>';

				$type = "end";
				$html .= get_shift_time_option_for_selected($type,$shift_time_data,$num);

				$html .= '</select>';

				$html .= '</div>';
				$html .= '<br class="clear" />';

				if( $jitaku_taiki_flg == "1" ){

					$html .= '<div style="padding:10px 0px 0px 10px;">';
					$html .= get_shift_time_checkbox_for_jitaku_taiki_flg($type,$shift_time_data,$num);
					$html .= '</div>';

				}

if( $att_area_select_disp_flg == true ){
$att_area_html = get_shift_time_radio_for_att_area($shift_time_data,$num,$area);
$html .=<<<EOT
<div style="padding:10px 0px 0px 10px;">
<div>
担当エリア
</div>
<div style="padding:5px 0px 0px 0px;">
{$att_area_html}
</div>
</div>
EOT;
}

				}else{

					$html .= '　店休';

				}


				$html .= '</div>';

				$html .= '<br class="clear" />';

				$data[$x] = $html;

				$x++;

			}

		}else if( $kikan == "kouhan" ){

			if( $i >= 15 ){

				$num = $x+1;

				$html = "";
				$html .= '<div class="shift_regist_time">';
				$html .= $day."(".$week.")";
				$html .= '</div>';

				$html .= '<div style="float:left;">';

				if( $shop_holiday_flg == false ){

				$html .= '<div class="shift_regist_select_1">';
				$html .= '<select name="start_time_'.$num.'">';
				$html .= '<option value="-1">未選択</option>';

				$type = "start";
				$html .= get_shift_time_option_for_selected($type,$shift_time_data,$num);

				$html .= '</select>';
				$html .= '</div>';
				$html .= '<div class="shift_regist_select_2">';
				$html .= '<select name="end_time_'.$num.'">';
				$html .= '<option value="-1">未選択</option>';

				$type = "end";
				$html .= get_shift_time_option_for_selected($type,$shift_time_data,$num);

				$html .= '</select>';
				$html .= '</div>';
				$html .= '<br class="clear" />';

				if( $jitaku_taiki_flg == "1" ){

					$html .= '<div style="padding:10px 0px 0px 10px;">';
					$html .= get_shift_time_checkbox_for_jitaku_taiki_flg($type,$shift_time_data,$num);
					$html .= '</div>';

				}

if( $att_area_select_disp_flg == true ){
$att_area_html = get_shift_time_radio_for_att_area($shift_time_data,$num,$area);
$html .=<<<EOT
<div style="padding:10px 0px 0px 10px;">
<div>
担当エリア
</div>
<div style="padding:5px 0px 0px 0px;">
{$att_area_html}
</div>
</div>
EOT;
}

				}else{

					$html .= '　店休';

				}

				$html .= '</div>';

				$html .= '<br class="clear" />';

				$data[$x] = $html;

				$x++;

			}

		}

	}

	return $data;
	exit();

}

function get_shift_time_radio_for_att_area($shift_time_data,$num,$area){

	$att_area = $shift_time_data[$num]["att_area"];

	$html = "";

	if( $att_area == "tokyo" ){

		$html .= '<input type="radio" name="att_area_'.$num.'" value="tokyo" checked>東京';
		$html .= '<input type="radio" name="att_area_'.$num.'" value="yokohama">横浜';

	}else if( $att_area == "yokohama" ){

		$html .= '<input type="radio" name="att_area_'.$num.'" value="tokyo">東京';
		$html .= '<input type="radio" name="att_area_'.$num.'" value="yokohama" checked>横浜';

	}else{

		if( $area == "tokyo" ){

			$html .= '<input type="radio" name="att_area_'.$num.'" value="tokyo" checked>東京';
			$html .= '<input type="radio" name="att_area_'.$num.'" value="yokohama">横浜';

		}else if( $area == "yokohama" ){

			$html .= '<input type="radio" name="att_area_'.$num.'" value="tokyo">東京';
			$html .= '<input type="radio" name="att_area_'.$num.'" value="yokohama" checked>横浜';

		}else{

			$html .= '<input type="radio" name="att_area_'.$num.'" value="tokyo">東京';
			$html .= '<input type="radio" name="att_area_'.$num.'" value="yokohama">横浜';

		}

	}

	return $html;
	exit();

}

function get_shift_time_option_for_selected($type,$shift_time_data,$num){

	include(INC_PATH."/time_array.php");

	$start_time = $shift_time_data[$num]["start_time"];
	$end_time = $shift_time_data[$num]["end_time"];

	//echo $start_time;

	$html = "";

	if( $type == "start" ){

		for($z=1;$z<=13;$z++){
			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];
			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($start_time=="-1") || ($start_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $start_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}else if( $type == "end" ){

		for($z=9;$z<=23;$z++){
			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];
			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($end_time=="-1") || ($end_time=="") ){

				if($z=="23"){
					$html .= '<option value="'.$z.'">ラスト('.$hour.':'.$minute.')</option>';
				}else{
					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';
				}

			}else{

				if( $z == $end_time ){

					if($z=="23"){
						$html .= '<option value="'.$z.'" selected>ラスト('.$hour.':'.$minute.')</option>';
					}else{
						$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';
					}

				}else{

				if($z=="23"){
					$html .= '<option value="'.$z.'">ラスト('.$hour.':'.$minute.')</option>';
				}else{
					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';
				}

				}

			}

		}

	}


	return $html;
	exit();

}

function get_shift_time_option_for_selected_2($type,$start_time,$end_time){

	include(INC_PATH."/time_array.php");

	$html = "";

	if( $type == "start" ){

		for($z=1;$z<=13;$z++){
			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];
			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($start_time=="-1") || ($start_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $start_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}else if( $type == "end" ){

		for($z=9;$z<=23;$z++){
			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];
			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($end_time=="-1") || ($end_time=="") ){

				if($z=="23"){
					$html .= '<option value="'.$z.'">ラスト('.$hour.':'.$minute.')</option>';
				}else{
					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';
				}

			}else{

				if( $z == $end_time ){

					if($z=="23"){
						$html .= '<option value="'.$z.'" selected>ラスト('.$hour.':'.$minute.')</option>';
					}else{
						$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';
					}

				}else{

					if($z=="23"){
						$html .= '<option value="'.$z.'">ラスト('.$hour.':'.$minute.')</option>';
					}else{
						$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';
					}

				}

			}

		}

	}


	return $html;
	exit();

}

function get_shift_time_option_for_selected_3($type,$start_time,$end_time){

	include(INC_PATH."/time_array.php");

	$html = "";

	$html .= '<option value="-1">未選択</option>';

	if( $type == "start" ){

		for($z=1;$z<=13;$z++){

			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($start_time=="-1") || ($start_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $start_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}else if( $type == "end" ){

		for($z=9;$z<=23;$z++){

			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($end_time=="-1") || ($end_time=="") ){

				if($z=="23"){
					$html .= '<option value="'.$z.'">ラスト('.$hour.':'.$minute.')</option>';
				}else{
					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';
				}

			}else{

				if( $z == $end_time ){

					if($z=="23"){
						$html .= '<option value="'.$z.'" selected>ラスト('.$hour.':'.$minute.')</option>';
					}else{
						$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';
					}

				}else{

					if($z=="23"){
						$html .= '<option value="'.$z.'">ラスト('.$hour.':'.$minute.')</option>';
					}else{
						$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';
					}

				}

			}

		}

	}


	return $html;
	exit();

}

function get_shift_time_option_for_selected_add($type,$start_time,$end_time){

	include(INC_PATH."/time_array.php");

	$html = "";

	if( $type == "start" ){

		for($z=1;$z<=15;$z++){

			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( $z == $start_time ){

				$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

			}else{

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}

		}

	}else if( $type == "end" ){

		for( $z=23; $z>=3; $z-- ){

			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($end_time=="-1") || ($end_time=="") ){

				if($z=="23"){
					$html .= '<option value="'.$z.'" selected>ラスト('.$hour.':'.$minute.')</option>';
				}else{
					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';
				}

			}else{

				if( $z == $end_time ){

					if($z=="23"){
						$html .= '<option value="'.$z.'" selected>ラスト('.$hour.':'.$minute.')</option>';
					}else{
						$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';
					}

				}else{

					if($z=="23"){
						$html .= '<option value="'.$z.'">ラスト('.$hour.':'.$minute.')</option>';
					}else{
						$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';
					}

				}

			}

		}

	}


	return $html;
	exit();

}

function regist_shift_data_by_therapist($therapist_id,$area,$year,$month,$kikan,$shift_time_data){

	include(INC_PATH."/db_connect.php");

	$therapist_name = get_therapist_name_by_therapist_id_honmyou($therapist_id, $area);

	$check_url = get_check_url_shift_man($area,$therapist_id);

	$att_area_select_disp_flg = get_att_area_select_disp_flg($therapist_id);

	$kikan_name = get_kikan_name($kikan);

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	/*
	echo "<pre>";
	print_r($shift_time_data);
	echo "</pre>";
	exit();
	*/

	$max_day = get_max_day($year,$month);

	$now = time();

	$syori_basyo = "shift_regist";

	if($kikan=="zenhan"){

		for($i=1;$i<=15;$i++){

			$day = $i;

			$week = get_week_value($year,$month,$day);

			$shift_time_data;

			$start_time = $shift_time_data[$i]["start_time"];
			$end_time = $shift_time_data[$i]["end_time"];
			$jitaku_taiki_flg = $shift_time_data[$i]["jitaku_taiki_flg"];

			$att_area = $shift_time_data[$i]["att_area"];

			if( $att_area_select_disp_flg == false ){

				$att_area = $area;

			}

			$nothing_flg = false;

			if( ($start_time=="-1") || ($end_time=="-1") ){

				$nothing_flg = true;

			}

			$check_result = check_attendance_new_exist_by_therapist_id_common($year,$month,$day,$therapist_id);

			if( ($nothing_flg == false) && ($check_result == false) ){

				$sql = sprintf("
insert into attendance_new(
therapist_id,year,month,day,week,start_time,end_time,area,jitaku_taiki_flg,created,updated,syori_basyo)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$year,$month,$day,$week,$start_time,$end_time,$att_area,$jitaku_taiki_flg,$now,$now,$syori_basyo);

				$res = mysql_query($sql, $con);

				if($res == false){

					//ロールバック
					$sql = "rollback";
					mysql_query( $sql, $con );
					$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_therapist)";
					header("Location: ".WWW_URL."error.php");
					exit();

				}

				$sql = sprintf("
insert into attendance_new_small(
therapist_id,year,month,day,week,start_time,end_time,area,jitaku_taiki_flg,created,updated,syori_basyo)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$year,$month,$day,$week,$start_time,$end_time,$att_area,$jitaku_taiki_flg,$now,$now,$syori_basyo);

				$res = mysql_query($sql, $con);

				if($res == false){

					//ロールバック
					$sql = "rollback";
					mysql_query( $sql, $con );
					$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_therapist)";
					header("Location: ".WWW_URL."error.php");
					exit();

				}

			}

		}


	}else if($kikan=="kouhan"){

		for($i=16;$i<=$max_day;$i++){

			$day = $i;

			$week = get_week_value($year,$month,$day);

			$shift_time_data;

			$x = $i - 15;

			$start_time = $shift_time_data[$x]["start_time"];
			$end_time = $shift_time_data[$x]["end_time"];
			$jitaku_taiki_flg = $shift_time_data[$x]["jitaku_taiki_flg"];

			$att_area = $shift_time_data[$x]["att_area"];

			if( $att_area_select_disp_flg == false ){

				$att_area = $area;

			}

			$nothing_flg = false;

			if( ($start_time=="-1") || ($end_time=="-1") ){

				$nothing_flg = true;

			}

			$check_result = check_attendance_new_exist_by_therapist_id_common($year,$month,$day,$therapist_id);

			if( ($nothing_flg == false) && ($check_result == false) ){

				$sql = sprintf("
insert into attendance_new(therapist_id,year,month,day,week,start_time,end_time,area,jitaku_taiki_flg,created,updated,syori_basyo)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$year,$month,$day,$week,$start_time,$end_time,$att_area,$jitaku_taiki_flg,$now,$now,$syori_basyo);

				//echo $sql;echo "<br />";

				$res = mysql_query($sql, $con);

				if($res == false){

					//ロールバック
					$sql = "rollback";
					mysql_query( $sql, $con );
					$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_therapist)";
					header("Location: ".WWW_URL."error.php");
					exit();

				}

				$sql = sprintf("
insert into attendance_new_small(therapist_id,year,month,day,week,start_time,end_time,area,jitaku_taiki_flg,created,updated,syori_basyo)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$year,$month,$day,$week,$start_time,$end_time,$att_area,$jitaku_taiki_flg,$now,$now,$syori_basyo);

				//echo $sql;echo "<br />";

				$res = mysql_query($sql, $con);

				if($res == false){

					//ロールバック
					$sql = "rollback";
					mysql_query( $sql, $con );
					$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_therapist)";
					header("Location: ".WWW_URL."error.php");
					exit();

				}

			}

		}

	}else{

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_therapist)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$now = time();
	$staff_type = "2";
	$message = $month."月".$kikan_name."シフト登録しました";

	$sql = sprintf("
insert into shift_history(created,area,therapist_id,staff_type,message)
values('%s','%s','%s','%s','%s')",
$now,$area,$therapist_id,$staff_type,$message);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_therapist)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minamikawa@neo-gate.jp";


	//$title = $month."月".$kikan_name."シフト登録【".$therapist_name."】";
	$title = sprintf("【シフト登録】%s月%sシフト登録[%s]",$month,$kikan_name,$therapist_name);

	$content =<<<EOT
{$therapist_name}さんより、{$month}月{$kikan_name}のシフト登録がありました。

{$check_url}
EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_therapist)";
		header("Location: ".WWW_URL."error.php");
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

function get_week_value($year,$month,$day){

	$hour = 12;
	$minute = 0;
	$second = 0;
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

	$week = date('w', $timestamp);

	return $week;
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

function shift_kekkin_action($therapist_id,$area,$year,$month,$day,$week_name){

	include(INC_PATH."/db_connect.php");

	$therapist_name = get_therapist_name_by_therapist_id_honmyou($therapist_id, $area);

	$check_url = get_check_url_shift_man($area,$therapist_id);

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	$now_year = intval(date('Y'));
	$now_month = intval(date('m'));
	$now_day = intval(date('d'));

	if( ($now_year==$year) && ($now_month==$month) && ($now_day==$day) ){

		$today_absence = 1;

	}else{

		$today_absence = 0;

	}

	$kekkin_flg = 1;
	$shift_change_flg = 0;
	$syounin_state = 0;

	$sql = sprintf("
update attendance_new set
kekkin_flg='%s',
shift_change_flg='%s',
syounin_state='%s',
today_absence='%s',
updated='%s',
absence_connection_time='%s'
where therapist_id='%s' and year='%s' and month='%s' and day='%s'",
$kekkin_flg,$shift_change_flg,$syounin_state,
$today_absence,$now,$now,$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);
	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_kekkin_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$sql = sprintf("
update attendance_new_small set
kekkin_flg='%s',
shift_change_flg='%s',
syounin_state='%s',
today_absence='%s',
updated='%s',
absence_connection_time='%s'
where therapist_id='%s' and year='%s' and month='%s' and day='%s'",
$kekkin_flg,$shift_change_flg,$syounin_state,
$today_absence,$now,$now,$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);
	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_kekkin_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$now = time();
	$staff_type = "2";
	$message = sprintf("%s/%s(%s)を欠勤に変更",$month,$day,$week_name);

	$sql = sprintf("
insert into shift_history(created,area,therapist_id,staff_type,message)
values('%s','%s','%s','%s','%s')",
$now,$area,$therapist_id,$staff_type,$message);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_therapist)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minamikawa@neo-gate.jp";

	//$title = "欠勤連絡".$month."月".$day."日【".$therapist_name."】";
	$title = sprintf("【シフト登録】%s月%s日欠勤連絡[%s]",$month,$day,$therapist_name);

	$content =<<<EOT
{$therapist_name}さんより、{$month}月{$day}日の欠勤連絡がありました。

{$check_url}
EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_kekkin_action)";
		header("Location: ".WWW_URL."error.php");
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

function shift_edit_action(
$therapist_id,$area,$att_area,$att_area_flg,$year,$month,$day,$start_time,$end_time,
$start_start_time,$start_end_time,$week_name,$jitaku_taiki_flg){

	$syounin_state = get_attendance_new_syounin_state_by_time($therapist_id,$year,$month,$day);

	include(INC_PATH."/db_connect.php");

	if( $jitaku_taiki_flg == "" ){

		$jitaku_taiki_flg = 0;

	}

	if( $att_area_flg == true ){

		$att_area_old = get_att_area_common($therapist_id,$year,$month,$day);
		$att_area_new = $att_area;
		$att_area_old_name = get_area_name_by_area_common($att_area_old);
		$att_area_new_name = get_area_name_by_area_common($att_area_new);

	}

	$therapist_name = get_therapist_name_by_therapist_id_honmyou($therapist_id, $area);

	$check_url = get_check_url_shift_man($area,$therapist_id);

	$start_time_hour = get_time_ja_hour($start_time);
	$start_time_minute = get_time_ja_minute($start_time);

	$end_time_hour = get_time_ja_hour($end_time);
	$end_time_minute = get_time_ja_minute($end_time);

	$start_start_time_hour = get_time_ja_hour($start_start_time);
	$start_start_time_minute = get_time_ja_minute($start_start_time);

	$start_end_time_hour = get_time_ja_hour($start_end_time);
	$start_end_time_minute = get_time_ja_minute($start_end_time);

	$sql = sprintf("
select id from shift_change_record where delete_flg=0 and therapist_id='%s' and area='%s' and year='%s' and month='%s' and day='%s'",
$therapist_id,$area,$year,$month,$day);

	//登録済みであるかどうかのチェック(汎用)
	$record_data_exist = data_exist_check_hanyou($sql);

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	$shift_change_flg = 1;
	$today_absence = 0;
	$kekkin_flg = 0;

	if( $syounin_state != "1" ){

		$syounin_state = 0;

	}

	$sql = sprintf("
update attendance_new set
shift_change_flg='%s',
syounin_state='%s',
start_time='%s',
end_time='%s',
area='%s',
today_absence='%s',
kekkin_flg='%s',
jitaku_taiki_flg='%s',
updated='%s'
where therapist_id='%s' and year='%s' and month='%s' and day='%s'",
$shift_change_flg,$syounin_state,$start_time,$end_time,$att_area,
$today_absence,$kekkin_flg,$jitaku_taiki_flg,$now,$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$sql = sprintf("
update attendance_new_small set
shift_change_flg='%s',
syounin_state='%s',
start_time='%s',
end_time='%s',
area='%s',
today_absence='%s',
kekkin_flg='%s',
jitaku_taiki_flg='%s',
updated='%s'
where therapist_id='%s' and year='%s' and month='%s' and day='%s'",
$shift_change_flg,$syounin_state,$start_time,$end_time,$att_area,
$today_absence,$kekkin_flg,$jitaku_taiki_flg,$now,$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	if($record_data_exist != true){

		$sql = sprintf("
insert into shift_change_record(therapist_id,area,year,month,day,start_time,end_time,start_time_update,end_time_update)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$area,$year,$month,$day,$start_start_time,$start_end_time,$start_time,$end_time);

	}else{

		$sql = sprintf("
update shift_change_record set start_time='%s',end_time='%s',start_time_update='%s',end_time_update='%s'
where delete_flg='0' and therapist_id='%s' and area='%s' and year='%s' and month='%s' and day='%s'",
$start_start_time,$start_end_time,$start_time,$end_time,$therapist_id,$area,$year,$month,$day);

	}

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$now = time();
	$staff_type = "2";
	$message = sprintf("
%s/%s(%s)を%s:%s-%s:%sから%s:%s-%s:%sに変更",
$month,$day,$week_name,$start_start_time_hour,$start_start_time_minute,$start_end_time_hour,$start_end_time_minute,
$start_time_hour,$start_time_minute,$end_time_hour,$end_time_minute);


	$sql = sprintf("
insert into shift_history(created,area,therapist_id,staff_type,message)
values('%s','%s','%s','%s','%s')",
$now,$area,$therapist_id,$staff_type,$message);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$mail_add = "";

	if( $jitaku_taiki_flg == "1" ){

$mail_add .=<<<EOT
自宅待機に変更のチェックあり
EOT;

	}

	if( $att_area_flg == true ){

$mail_add .=<<<EOT
担当エリア：{$att_area_old_name}→{$att_area_new_name}
EOT;
	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minamikawa@neo-gate.jp";

	if( $syounin_state == "1" ){

		$title_in_name = "シフト変更通知";

		//URLは消す
		$check_url = "";

	}else{

		$title_in_name = "シフト登録";

	}

	$title = sprintf("【%s】%s月%s日シフト変更[%s]",$title_in_name,$month,$day,$therapist_name);

	$content =<<<EOT
{$therapist_name}さんより、{$month}月{$day}日({$week_name})のシフト変更依頼がありました。

出勤：{$start_start_time_hour}:{$start_start_time_minute}→{$start_time_hour}:{$start_time_minute}
退勤：{$start_end_time_hour}:{$start_end_time_minute}→{$end_time_hour}:{$end_time_minute}
{$mail_add}

{$check_url}
EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action)";
		header("Location: ".WWW_URL."error.php");
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

function get_time_ja_hour($time){

	include(INC_PATH."/time_array.php");

	$hour = $time_array[$time]["hour"];

	return $hour;
	exit();

}

function get_time_ja_minute($time){

	include(INC_PATH."/time_array.php");

	$minute = $time_array[$time]["minute"];

	if( $minute == "0" ){

		$minute = "0".$minute;

	}

	return $minute;
	exit();

}

function shift_add_action($therapist_id,$area,$att_area,$att_area_flg,$year,$month,$day,$start_time,$end_time,$week_name,$jitaku_taiki_flg){

	include(INC_PATH."/db_connect.php");

	$mail_add = "";

	if( $att_area_flg == true ){

		$att_area_name = get_area_name_by_area_common($att_area);

		$mail_add = "担当エリア：".$att_area_name;

	}

	$therapist_name = get_therapist_name_by_therapist_id_honmyou($therapist_id, $area);

	$check_url = get_check_url_shift_man($area,$therapist_id);

	$week = get_week_value($year, $month, $day);

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	$syori_basyo = "shift_add";

	$sql = sprintf("
insert into attendance_new(
therapist_id,year,month,day,week,start_time,end_time,area,jitaku_taiki_flg,created,updated,syori_basyo)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$year,$month,$day,$week,$start_time,$end_time,$att_area,$jitaku_taiki_flg,$now,$now,$syori_basyo);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_add_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$sql = sprintf("
insert into attendance_new_small(
therapist_id,year,month,day,week,start_time,end_time,area,jitaku_taiki_flg,created,updated,syori_basyo)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$year,$month,$day,$week,$start_time,$end_time,$att_area,$jitaku_taiki_flg,$now,$now,$syori_basyo);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_add_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$now = time();
	$staff_type = "2";
	$message = sprintf("%s/%s(%s)シフト追加しました",$month,$day,$week_name);

	$sql = sprintf("
insert into shift_history(created,area,therapist_id,staff_type,message)
values('%s','%s','%s','%s','%s')",
$now,$area,$therapist_id,$staff_type,$message);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_add_action)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minamikawa@neo-gate.jp";

	//$title = "シフト追加".$month."月".$day."日【".$therapist_name."】";
	$title = sprintf("【シフト登録】%s月%s日シフト追加[%s]",$month,$day,$therapist_name);

	$content =<<<EOT
{$therapist_name}さんより、{$month}月{$day}日のシフト追加がありました。
{$mail_add}

{$check_url}
EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_add_action)";
		header("Location: ".WWW_URL."error.php");
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

//登録済みであるかどうかのチェック(汎用)
function data_exist_check_hanyou($sql){

	include(INC_PATH."/db_connect.php");

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(data_exist_check_hanyou)";
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

function get_check_url_shift_man($area,$therapist_id){

	$check_url = sprintf("%sman/shift/index.php?area=%s&id=%s",WWW_URL_SITE,$area,$therapist_id);

	return $check_url;
	exit();

}

function get_check_url_driver_man($area,$staff_id){

	$check_url = sprintf("%sman/driver/index.php?area=%s&id=%s",WWW_URL_SITE,$area,$staff_id);

	return $check_url;
	exit();

}

function get_jitaku_taiki_flg_by_therapist_id($therapist_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select jitaku_taiki_flg from therapist_new where id='%s'",$therapist_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["jitaku_taiki_flg"];
	exit();

}

function get_shift_time_checkbox_for_jitaku_taiki_flg($type,$shift_time_data,$num){

	include(INC_PATH."/time_array.php");

	$jitaku_taiki_flg = $shift_time_data[$num]["jitaku_taiki_flg"];

	$html = "";

	if( $jitaku_taiki_flg == "1" ){

		$html = '<input type="checkbox" name="jitaku_taiki_flg_'.$num.'" value="1" checked />自宅待機';

	}else{

		$html = '<input type="checkbox" name="jitaku_taiki_flg_'.$num.'" value="1" />自宅待機';

	}

	return $html;
	exit();

}

function get_shift_time_checkbox_for_jitaku_taiki_flg_one($jitaku_taiki_flg){

	$html = "";

	if( $jitaku_taiki_flg == "1" ){

		$html = '<input type="checkbox" name="jitaku_taiki_flg" value="1" checked />自宅待機';

	}else{

		$html = '<input type="checkbox" name="jitaku_taiki_flg" value="1" />自宅待機';

	}

	return $html;
	exit();

}

function get_shift_time_checkbox_for_jitaku_taiki_flg_one_2($jitaku_taiki_flg){

	$html = "";

	if( $jitaku_taiki_flg == "1" ){

		$html = '<input type="checkbox" name="jitaku_taiki_flg" value="1" checked />自宅待機に変更する';

	}else{

		$html = '<input type="checkbox" name="jitaku_taiki_flg" value="1" />自宅待機に変更する';

	}

	return $html;
	exit();

}

function staff_shift_page_access_check($staff_id,$for_kobetsu_url){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from staff_new_new where delete_flg=0 and id='%s' and for_kobetsu_url='%s'",
$staff_id,$for_kobetsu_url);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$id = $row["id"];

	if( $id == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

function get_attendance_request_state($year,$month,$day,$area){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from attendance_request
where
delete_flg=0 and
year='%s' and
month='%s' and
day='%s' and
area='%s'",
$year,$month,$day,$area);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$id = $row["id"];

	if( $id == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

//研修生かどうか
function get_kensyuu_flg_by_therapist_id($therapist_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select kensyuu_flg from therapist_new where id='%s'",
$therapist_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["kensyuu_flg"];
	exit();

}

function get_kensyuu_result_for_shift_index($therapist_id,$year,$month,$day){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from attendance_kensyuu where
therapist_id='%s' and year='%s' and month='%s' and day='%s'",
$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

function get_attendance_list_data_kensyuu($attendance_data,$day_data,$therapist_id,$ch,$area){

	include(INC_PATH."/time_array_kensyuu.php");

	$data = array();

	$k=0;

	$day_data_num = count($day_data);
	$attendance_data_num = count($attendance_data);

	//echo $attendance_data_num;exit();

	for($i=0;$i<$day_data_num;$i++){

		$year = $day_data[$i]["year"];
		$month = $day_data[$i]["month"];
		$day = $day_data[$i]["day"];
		$week = $day_data[$i]["week"];

		$past_flg = today_past_check($year,$month,$day);

		//echo $past_flg;echo "<br />";

		if( $past_flg == false ){

			$match_flg = false;

			for($j=0;$j<$attendance_data_num;$j++){

				if($match_flg == false){

					$year_a = $attendance_data[$j]["year"];
					$month_a = $attendance_data[$j]["month"];
					$day_a = $attendance_data[$j]["day"];
					$start_time = $attendance_data[$j]["start_time"];
					$end_time = $attendance_data[$j]["end_time"];
					$work_flg = $attendance_data[$j]["work_flg"];

					if( ($year==$year_a) && ($month==$month_a) && ($day==$day_a) ){

						$match_flg = true;

					}

				}

			}



			$attendance_kensyuu_all_data = get_attendance_kensyuu_all_data_one($year,$month,$day);
			$start_time_tmp = $attendance_kensyuu_all_data["start_time"];
			$end_time_tmp = $attendance_kensyuu_all_data["end_time"];
			$start_time_hour_disp_all = $time_array_kensyuu[$start_time_tmp]["hour"];
			$end_time_hour_disp_all = $time_array_kensyuu[$end_time_tmp]["hour"];
			$start_time_minute_disp_all = $time_array_kensyuu[$start_time_tmp]["minute"];
			$end_time_minute_disp_all = $time_array_kensyuu[$end_time_tmp]["minute"];
			if($start_time_minute_disp_all=="0"){
				$start_time_minute_disp_all = "0".$start_time_minute_disp_all;
			}
			if($end_time_minute_disp_all=="0"){
				$end_time_minute_disp_all = "0".$end_time_minute_disp_all;
			}
			$time_disp_all = sprintf(
'(%s時%s分／%s時%s分)',
$start_time_hour_disp_all,$start_time_minute_disp_all,$end_time_hour_disp_all,$end_time_minute_disp_all);



			$html = "";

			if( $match_flg == true ){

				$start_time_ta = $time_array_kensyuu[$start_time]["minute"];
				$end_time_ta = $time_array_kensyuu[$end_time]["minute"];

				if($start_time_ta=="0"){

					$start_time_ta = "0".$start_time_ta;

				}

				if($end_time_ta=="0"){

					$end_time_ta = "0".$end_time_ta;

				}

				$html .= '<form action="edit_kensyuu.php" method="post">';
				$html .= '<input type="hidden" name="therapist_id" value="'.$therapist_id.'" />';
				$html .= '<input type="hidden" name="year" value="'.$year.'" />';
				$html .= '<input type="hidden" name="month" value="'.$month.'" />';
				$html .= '<input type="hidden" name="day" value="'.$day.'" />';
				$html .= '<input type="hidden" name="start_time" value="'.$start_time.'" />';
				$html .= '<input type="hidden" name="end_time" value="'.$end_time.'" />';
				$html .= '<input type="hidden" name="ch" value="'.$ch.'" />';
				$html .= '<input type="hidden" name="area" value="'.$area.'" />';
				$html .= '<input type="hidden" name="work_flg" value="'.$work_flg.'" />';

				$html .= '<div class="shift_list_time">';

				$html .= $day."(".$week.")";
				$html .= "　";
				$html .= $time_array_kensyuu[$start_time]["hour"]."時".$start_time_ta."分";
				$html .= "／";
				$html .= $time_array_kensyuu[$end_time]["hour"]."時".$end_time_ta."分";
				$html .= "　";
				$html .= "<br />";
				$html .= '<span style="color:#999;padding-left:40px;font-size:12px;">'.$time_disp_all.'</span>';

				$html .= '</div>';

				$html .= '<div class="shift_list_right_area_1">';

				$html .= '<div class="shift_list_right_area_kakutei"><span style="color:blue;">○</span></div>';
				$html .= '<div class="shift_list_right_area_btn">';
				$html .= '<input type="submit" value="編集" name="send_list_edit" />';
				$html .= '</div>';

				$html .= '<br class="clear" />';

				$html .= '</div>';

				$html .= '<br class="clear" />';

				$html .= '</form>';

				$data[$k] = $html;

			}else{

				$html .= '<form action="add_kensyuu.php" method="post">';
				$html .= '<input type="hidden" name="therapist_id" value="'.$therapist_id.'" />';
				$html .= '<input type="hidden" name="year" value="'.$year.'" />';
				$html .= '<input type="hidden" name="month" value="'.$month.'" />';
				$html .= '<input type="hidden" name="day" value="'.$day.'" />';
				$html .= '<input type="hidden" name="ch" value="'.$ch.'" />';
				$html .= '<input type="hidden" name="area" value="'.$area.'" />';

				$html .= '<div class="shift_list_time">';
				$html .= $day."(".$week.")";

				$html .= '<span style="color:#999;padding-left:10px;font-size:12px;">'.$time_disp_all.'</span>';

				$html .= '</div>';
				$html .= '<div class="shift_list_right_area_2">';
				$html .= '<input type="submit" value="参加" name="send_list_add" />';
				$html .= '</div>';

				$html .= '<br class="clear" />';

				$html .= '</form>';

				$data[$k] = $html;

			}

			$k++;

		}

	}

	return $data;
	exit();

}

function get_day_data_kensyuu($year,$month,$max_day){

	$data = array();

	$x = 0;

	for($i=0;$i<$max_day;$i++){

		$day = $i+1;

		$sql = sprintf("
select id from attendance_kensyuu_all where delete_flg=0 and year='%s' and month='%s' and day='%s'",
$year,$month,$day);
		$result = data_exist_check_hanyou($sql);

		if( $result == true ){

			$data[$x]["year"] = $year;
			$data[$x]["month"] = $month;
			$data[$x]["day"] = $day;

			$w = intval(date("w", mktime(0, 0, 0, $month, $day, $year)));
			$data[$x]["week"] = get_ja_week($w);

			$x++;

		}
	}

	return $data;
	exit();

}

function get_attendance_kensyuu_all_data_one($year,$month,$day){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select * from attendance_kensyuu_all where delete_flg=0 and year='%s' and month='%s' and day='%s'",
$year,$month,$day);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(get_attendance_kensyuu_all_data_one)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();

}

function get_shift_time_option_for_selected_add_kensyuu($type,$start_time,$end_time,$year,$month,$day){

	$data = get_attendance_kensyuu_all_data_one($year,$month,$day);

	$start_time_all = $data["start_time"];
	$end_time_all = $data["end_time"];

	include(INC_PATH."/time_array_kensyuu.php");

	$html = "";

	if( $type == "start" ){

		$for_max_num = 0;

		if( $end_time_all > 9 ){

			$for_max_num = 9;

		}else{

			$for_max_num = $end_time_all;

		}

		for($z=$start_time_all;$z<=$for_max_num;$z++){

			$minute = $time_array_kensyuu[$z]["minute"];
			$hour = $time_array_kensyuu[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( $z == $start_time ){

				$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

			}else{

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}

		}

	}else if( $type == "end" ){

		$for_min_num = 0;

		if( $start_time_all < -7 ){

			$for_min_num = -7;

		}else{

			$for_min_num = $start_time_all;

		}

		for( $z=$end_time_all; $z>=$for_min_num; $z-- ){

			$minute = $time_array_kensyuu[$z]["minute"];
			$hour = $time_array_kensyuu[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($end_time=="-999") || ($end_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $end_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}


	return $html;
	exit();

}

function shift_add_action_kensyuu(
$therapist_id,$area,$year,$month,$day,$start_time,$end_time,$week_name,$work_flg,$start_time_work,$end_time_work){

	$therapist_name = get_therapist_name_by_therapist_id_honmyou_new($therapist_id);
	$week = get_week_value($year,$month,$day);

	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	if( ($start_time_work != "-1") && ($end_time_work != "-1") && ($work_flg == "1") ){

		$result = attendance_data_exist_check($therapist_id,$year,$month,$day);

		$syounin_state = "0";

		if( $result == false ){

			$syori_basyo = "add_kensyuu_insert";

			// 出勤情報を登録するSQL文
			$sql = sprintf("
insert into attendance_new(
therapist_id,year,month,day,week,start_time,end_time,area,syounin_state,created,updated,syori_basyo)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,
$year,
$month,
$day,
$week,
$start_time_work,
$end_time_work,
$area,
$syounin_state,
$now,$now,
$syori_basyo);

		}else{

			$syori_basyo = "add_kensyuu_update";

			$sql = sprintf("
update attendance_new set
start_time='%s',
end_time='%s',
area='%s',
syounin_state='%s',
updated='%s',
syori_basyo='%s'
where
therapist_id='%s' and
year='%s' and
month='%s' and
day='%s'",
$start_time_work,$end_time_work,$area,$syounin_state,
$now,$syori_basyo,$therapist_id,$year,$month,$day);

		}

		$res = mysql_query($sql, $con);

		if($res == false){

			//ロールバック
			$sql = "rollback";
			mysql_query( $sql, $con );

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_add_action_kensyuu:1)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$check_url = get_check_url_shift_man($area,$therapist_id);

		$mail_add =<<<EOT
{$therapist_name}さんより、研修後の勤務時間の登録がありました

{$check_url}
EOT;

	}

	$sql = sprintf("
insert into attendance_kensyuu(
therapist_id,year,month,day,start_time,end_time,area,work_flg)
values('%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$year,$month,$day,$start_time,$end_time,$area,$work_flg);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_add_action_kensyuu:2)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minata.user@gmail.com";

	//$title = "シフト追加".$month."月".$day."日【".$therapist_name."】";
	$title = sprintf("【シフト登録】%s月%s日研修参加[%s]",$month,$day,$therapist_name);

	$content =<<<EOT
{$therapist_name}さんより、{$month}月{$day}日({$week_name})の研修参加の連絡がありました。

http://www.tokyo-refle.com/man/staff_calendar_kensyuu.php

{$mail_add}

EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_add_action_kensyuu)";
		header("Location: ".WWW_URL."error.php");
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

function shift_kekkin_action_kensyuu($therapist_id,$area,$year,$month,$day,$week_name){

	include(INC_PATH."/db_connect.php");

	$therapist_name = get_therapist_name_by_therapist_id_honmyou($therapist_id, $area);


	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	$sql = sprintf("
delete from attendance_kensyuu where therapist_id='%s' and year='%s' and month='%s' and day='%s'",
$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);
	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_kekkin_action_kensyuu)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minata.user@gmail.com";

	//$title = "欠勤連絡".$month."月".$day."日【".$therapist_name."】";
	$title = sprintf("【シフト登録】%s月%s日研修欠勤連絡[%s]",$month,$day,$therapist_name);

	$content =<<<EOT
{$therapist_name}さんより、{$month}月{$day}日({$week_name})の研修欠勤の連絡がありました。

http://www.tokyo-refle.com/man/staff_calendar_kensyuu.php

EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_kekkin_action_kensyuu)";
		header("Location: ".WWW_URL."error.php");
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

function shift_edit_action_kensyuu(
$therapist_id,$area,$year,$month,$day,$start_time,$end_time,$start_start_time,$start_end_time,$week_name,$work_flg,
$work_time_change_flg,$start_time_work,$end_time_work){

	$therapist_name = get_therapist_name_by_therapist_id_honmyou($therapist_id, $area);
	$week = get_week_value($year,$month,$day);

	$check_url = get_check_url_shift_man($area,$therapist_id);

	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	if( ($start_time_work != "-1") && ($end_time_work != "-1") && ($work_flg == "1") ){

		$result = attendance_data_exist_check($therapist_id,$year,$month,$day);

		$syounin_state = "0";

		if( $result == false ){

			$syori_basyo = "edit_kensyuu_insert";

			// 出勤情報を登録するSQL文
			$sql = sprintf("
insert into attendance_new(
therapist_id,year,month,day,week,start_time,end_time,area,syounin_state,created,updated,syori_basyo)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,
$year,
$month,
$day,
$week,
$start_time_work,
$end_time_work,
$area,
$syounin_state,
$now,$now,
$syori_basyo);

			$res = mysql_query($sql, $con);

			if($res == false){

				//ロールバック
				$sql = "rollback";
				mysql_query( $sql, $con );

				echo "error!(shift_edit_action_kensyuu:1)";
				exit();

			}

			$mail_add =<<<EOT
{$therapist_name}さんより、研修後の勤務時間の登録がありました

{$check_url}
EOT;

		}else{

			if( $work_time_change_flg == true ){

				$syori_basyo = "edit_kensyuu_update";

				$sql = sprintf("
update attendance_new set
start_time='%s',
end_time='%s',
area='%s',
syounin_state='%s',
updated='%s',
syori_basyo='%s'
where
therapist_id='%s' and
year='%s' and
month='%s' and
day='%s'",
$start_time_work,$end_time_work,$area,$syounin_state,
$now,$syori_basyo,$therapist_id,$year,$month,$day);

				$res = mysql_query($sql, $con);
				if($res == false){
					//ロールバック
					$sql = "rollback";
					mysql_query( $sql, $con );

					echo "error!(shift_edit_action_kensyuu:2)";
					exit();

				}

				$mail_add =<<<EOT
{$therapist_name}さんより、研修後の勤務時間の変更がありました

{$check_url}
EOT;

			}

		}

	}

	$sql = sprintf("
update attendance_kensyuu set
start_time='%s',
end_time='%s',
work_flg='%s'
where therapist_id='%s' and year='%s' and month='%s' and day='%s'",
$start_time,$end_time,$work_flg,$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action_kensyuu:3)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}



	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minata.user@gmail.com";

	$title = sprintf("【シフト登録】%s月%s日研修時間の変更[%s]",$month,$day,$therapist_name);

	$content =<<<EOT
{$therapist_name}さんより、{$month}月{$day}日({$week_name})の研修時間の変更の連絡がありました。

http://www.tokyo-refle.com/man/staff_calendar_kensyuu.php

{$mail_add}

EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action_kensyuu:4)";
		header("Location: ".WWW_URL."error.php");
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

function get_time_ja_hour_kensyuu($time){

	include(INC_PATH."/time_array_kensyuu.php");

	$hour = $time_array_kensyuu[$time]["hour"];

	return $hour;
	exit();

}

function get_time_ja_minute_kensyuu($time){

	include(INC_PATH."/time_array_kensyuu.php");

	$minute = $time_array_kensyuu[$time]["minute"];

	if( $minute == "0" ){

		$minute = "0".$minute;

	}

	return $minute;
	exit();

}

function get_today_driver_data($year,$month,$day,$area){

	include(INC_PATH."/db_connect.php");

	$type = "driver";

	$sql = sprintf("select staff_new_new.* from attendance_staff_new
left join staff_new_new on staff_new_new.id=attendance_staff_new.staff_id
where
staff_new_new.delete_flg=0 and
staff_new_new.shayousha_flg=0 and
attendance_staff_new.year='%s' and
attendance_staff_new.month='%s' and
attendance_staff_new.day='%s' and
attendance_staff_new.area='%s' and
staff_new_new.type='%s'",
$year,$month,$day,$area,$type);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(get_today_driver_data)";
		exit();

	}

	$data = array();

	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$data[$i] = $row;
		$i++;

	}

	return $data;
	exit();

}

function get_top_url($area,$staff_id,$ch){

	$top_url = sprintf("%sindex.php?area=%s&id=%s&ch=%s",WWW_URL,$area,$staff_id,$ch);

	$_SESSION["top_url"] = $top_url;

	return $top_url;
	exit();

}

function get_driver_url($area,$staff_id,$ch,$file_name){

	$page_url = sprintf("%s%s?area=%s&id=%s&ch=%s",WWW_URL,$file_name,$area,$staff_id,$ch);

	return $page_url;
	exit();

}

function regist_shift_message($therapist_id,$message_content,$staff_type,$area){

	$bbs_common_url = get_bbs_common_url_common();

	$check_url = get_check_url_shift_man($area,$therapist_id);
	$therapist_name = get_therapist_name_by_therapist_id_honmyou_common($therapist_id);

	$shop_area_name = get_area_name_by_area_common($area);

	$area_boss_mail = get_area_boss_mail_common($area);

	$now = time();
	$now_hour = intval(date('H', $now));
	$now_minute = intval(date('i', $now));
	$now_hour_mail = hour_plus_24_common($now_hour);
	$now_minute_mail = minute_zero_add_common($now_minute);

	$eigyou_day_data = get_eigyou_day_common();
	$eigyou_year = $eigyou_day_data["year"];
	$eigyou_month = $eigyou_day_data["month"];
	$eigyou_day = $eigyou_day_data["day"];

	$shift_message_area = get_shift_message_area_common($therapist_id,$eigyou_year,$eigyou_month,$eigyou_day);



	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	$sql = sprintf("
insert into shift_message(created,therapist_id,type,content,area) values('%s','%s','%s','%s','%s')",
$now,$therapist_id,$staff_type,$message_content,$shift_message_area);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_message:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";

	$title = sprintf("施術連絡：%s：%s　%s：%s",
$shop_area_name,$therapist_name,$now_hour_mail,$now_minute_mail);

	$content =<<<EOT
{$message_content}

{$check_url}

EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$parameter="-f info@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,$parameter);

	if( $result == false ){

		//二回メールを送るので、トランザクションの対象とはしない

	}

	if( ($area=="yokohama") || ($area=="sapporo") || ($area=="osaka") ){

		if( $area_boss_mail != "" ){

			//あらためてメール送信

			$mailto = "info@neo-gate.jp";

			$title = "[業務連絡BBS]投稿のお知らせ";

$content =<<<EOT
新しいメッセージがあります。確認してください。

{$bbs_common_url}

EOT;

			$header = "From: info@neo-gate.jp\n";
			//$header .= "Bcc: minamikawa@neo-gate.jp";

			$header .= ",";
			$header .= $area_boss_mail;

			$parameter="-f info@neo-gate.jp";

			$result = mb_send_mail($mailto,$title,$content,$header,$parameter);

			if( $result == false ){

				//二回メールを送るので、トランザクションの対象とはしない

			}

		}

	}

	//コミット
	$sql = "commit";
	mysql_query( $sql, $con );

	//MySQL切断
	mysql_close( $con );

	return true;
	exit();

}

function get_shift_message_by_order_num($order_num,$therapist_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select * from shift_message where delete_flg=0 and therapist_id='%s' order by created desc limit 0,%s",$therapist_id,$order_num);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_shift_message_by_order_num)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$list_data[$i] = $row;

		$created = $row["created"];

		$month = intval(date("m", $created));
		$day = intval(date("d", $created));
		$week = intval(date("w", $created));
		$hour = intval(date("H", $created));
		$minute = intval(date("i", $created));
		$week_name = get_week_name($week);

		if( $minute < 10){

			$minute = "0".$minute;

		}

		$time_disp = sprintf("%s/%s（%s）%s：%s",$month,$day,$week_name,$hour,$minute);

		$list_data[$i]["time_disp"] = $time_disp;

		$i++;
	}

	return $list_data;
	exit();

}

//予約ボードデータを取得
function get_reservation_for_board_data_today($therapist_id){

	include(INC_PATH."/db_connect.php");

	$data = get_today_year_month_day_common();

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$sql = sprintf("
select reservation_for_board.* from reservation_for_board
left join attendance_new on attendance_new.id=reservation_for_board.attendance_id
where
reservation_for_board.delete_flg=0 and
reservation_for_board.attendance_tmp_flg=0 and
reservation_for_board.year='%s' and
reservation_for_board.month='%s' and
reservation_for_board.day='%s' and
attendance_new.therapist_id='%s' order by reservation_for_board.id desc",
$year,$month,$day,$therapist_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_reservation_for_board_data_today)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$list_data[$i] = $row;

		$reservation_no = $row["reservation_no"];
		$okuri_driver_id = $row["okuri_driver_id"];
		$mukae_driver_id = $row["mukae_driver_id"];
		$course_var = $row["course_var"];
		$customer_name = $row["customer_name"];

		$customer_name = preg_replace("/[\(（].*/u","",$customer_name);

		if (preg_match("/リフレ/",$course_var)) {
			$course_add = 30;
		}else{
			$course_add = 0;
		}

		$start_minute = minute_zero_check($row["start_minute"]);
		$end_minute = minute_zero_check($row["end_minute"]);

		$okuri_driver_name = get_driver_name($okuri_driver_id);
		$mukae_driver_name = get_driver_name($mukae_driver_id);
		$price = get_price_by_reservation_no($reservation_no);

		$list_data[$i]["okuri_driver_name"] = $okuri_driver_name;
		$list_data[$i]["mukae_driver_name"] = $mukae_driver_name;
		$list_data[$i]["price"] = $price;
		$list_data[$i]["start_minute"] = $start_minute;
		$list_data[$i]["end_minute"] = $end_minute;
		$list_data[$i]["course_add"] = $course_add;
		$list_data[$i]["customer_name"] = $customer_name;

		$i++;

	}

	return $list_data;
	exit();

}

function get_driver_name($driver_id){

	$name = "";

	if( $driver_id == "-1" ){

		$name = "未定";

	}else if( $driver_id == "-2" ){

		$name = "TAXI";

	}else if( $driver_id == "-3" ){

		$name = "本部";

	}else{

		$name = get_staff_name_new($driver_id);

		$name = mb_substr($name, 0, 3, 'UTF-8');

	}

	return $name;
	exit();

}

//スタッフの名前を取得
function get_staff_name_new($id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select name from staff_new_new where delete_flg=0 and id='%s'",$id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_staff_name_new)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["name"];
	exit();

}

function get_price_by_reservation_no($reservation_no){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select price from sale_history where delete_flg=0 and reservation_no='%s'",$reservation_no);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_price_by_reservation_no)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["price"];
	exit();

}

function minute_zero_check($minute){

	if( $minute < 10 ){

		$minute = "0".$minute;

	}

	return $minute;
	exit();

}

function reservation_start($reservation_for_board_id,$course,$course_var,$therapist_id,$area,$extension){

	if (preg_match("/リフレ/",$course_var)) {

		$course_add = 30;

	}else{

		$course_add = 0;

	}

	$start_real_time = time();

	$now_hour = intval(date('H'));
	$now_minute = intval(date('i'));

	if( $now_hour < 10 ){

		$now_hour = $now_hour + 24;

	}

	//繰り上げ処理
	$zyuunokurai = floor($now_minute/10);
	$amari = $now_minute % 10;
	if( ($amari == 0) || ($amari == 5) ){
		//変更なし
	}else if( $now_minute > 50 ){
		//51～59
		if( $amari < 5 ){
			//51～54
			$now_minute = 55;
		}else{
			//5より大きい場合
			//56～59
			$now_minute = 0;
			$now_hour = $now_hour + 1;
		}
	}else{
		//1～49(10,20,30,40は無し)
		if( $amari < 5 ){
			//1～4
			$now_minute = ($zyuunokurai*10)+5;
		}else{
			//6～9
			$now_minute = ($zyuunokurai+1)*10;
		}
	}

	$start_hour = $now_hour;
	$start_minute = $now_minute;

	$start = ( $now_hour * 60 ) + $now_minute;
	$end = $start + $course + $course_add + $extension;
	$end_hour = intval( $end / 60 );
	$end_minute = $end % 60;

	//こちらで予約ボードと、予約状況一覧のデータを書き換える

	//引数は、すべて$reservation_for_board_idから取得可能

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);

	/*
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	exit();
	*/

	$attendance_id = $data["attendance_id"];
	$shop_area = $data["shop_area"];
	$shop_name = $data["shop_name"];
	$area_name = $data["area_name"];
	$customer_name = $data["customer_name"];
	$extension = $data["extension"];
	$card_flg = $data["card_flg"];
	$card_confirm_flg = $data["card_confirm_flg"];
	$complete_flg = $data["complete_flg"];
	$start_real_time = $data["start_real_time"];
	$start_flg = "1";

	if( $start_real_time != "0" ){

		return false;
		exit();

	}

	$start_real_time_flg = true;

	$mail_flg = true;
	$mail_type = "start";

	$check_url = get_check_url_shift_man($area,$therapist_id);

	$course_change_flg = false;
	$extension_change_flg = false;

	update_reservation_data_transaction_common(
$start_hour,$start_minute,$end_hour,$end_minute,$reservation_for_board_id,$therapist_id,$attendance_id,
$shop_area,$area_name,$course,$extension,$card_flg,$card_confirm_flg,$complete_flg,$start_flg,
$start_real_time_flg,$mail_flg,$mail_type,$customer_name,$check_url,
$shop_name,$course_old,$course_new,$extension_old,$extension_new,$course_change_flg,$extension_change_flg,
$course_var);

	return true;
	exit();

}

function reservation_end($id,$therapist_id,$area){

	$bbs_common_url = get_bbs_common_url_common();

	$therapist_name = get_therapist_name_by_therapist_id_honmyou_common($therapist_id);

	$therapist_mail = get_therapist_mail_common($therapist_id);

	$check_url = get_check_url_shift_man($area,$therapist_id);

	$price = get_price_by_reservation_for_board_id_common($id);

	$data = get_reservation_for_board_data_by_id_common($id);

	$area_name = $data["area_name"];
	$customer_name = $data["customer_name"];
	$course = $data["course"];
	$course_var = $data["course_var"];
	$shop_area = $data["shop_area"];
	$extension = $data["extension"];
	$card_flg = $data["card_flg"];

	$month_mail = $data["month"];
	$day_mail = $data["day"];

	$start_hour_mail = add_zero_when_under_ten_common($data["start_hour"]);
	$start_minute_mail = add_zero_when_under_ten_common($data["start_minute"]);

	$customer_name = preg_replace("/[\(（].*/u","",$customer_name);
	$now = time();
	$now_hour = intval(date('H', $now));
	$now_minute = intval(date('i', $now));
	$now_hour = hour_plus_24_common($now_hour);
	$now_minute = minute_zero_add_common($now_minute);

	$shop_area_name = get_area_name_by_area_common($shop_area);

	$eigyou_day_data = get_eigyou_day_common();
	$eigyou_year = $eigyou_day_data["year"];
	$eigyou_month = $eigyou_day_data["month"];
	$eigyou_day = $eigyou_day_data["day"];

	$shift_message_area = get_shift_message_area_common($therapist_id,$eigyou_year,$eigyou_month,$eigyou_day);

	$bcc_add = get_bcc_add_shift_common($area);

	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$end_real_time = $now;

	$sql = sprintf("update reservation_for_board set end_real_time='%s',complete_flg='1' where id='%s'",$end_real_time,$id);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(reservation_end:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	if( $extension == 0 ){

		$course_disp = $course_var."コース";

	}else{

		$course_disp = $course_var."＋".$extension."分コース";

	}

	if( $card_flg == "1" ){

		$pay_type = "カード支払い";

	}else{

		$pay_type = "現金支払い";

	}

	$price_disp = number_format($price)."円";

	$message_content .= sprintf("%s様(%s)%s　終了しました",$customer_name,$area_name,$course_disp);

	$message_type = "mail_end";

	//メッセージのインサート
	$now = time();
	$staff_type = "2";

	$sql = sprintf("
insert into shift_message(created,therapist_id,type,message_type,content,area) values('%s','%s','%s','%s','%s','%s')",
$now,$therapist_id,$staff_type,$message_type,$message_content,$shift_message_area);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(reservation_end:2)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}



	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";

	$title = sprintf("
終了連絡：%s：%s　%s：%s",
$shop_area_name,$therapist_name,$now_hour,$now_minute);

	$content =<<<EOT
{$customer_name}様（{$area_name}）

{$course_disp}/{$price_disp}/{$pay_type}
終了しました

{$check_url}

EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if( $result == false ){

		//複数回メールを送るので、トランザクションの対象とはしない

	}

	if( ($area=="yokohama") || ($area=="sapporo") || ($area=="osaka") ){

		if( $bcc_add != "" ){

			//あらためてメール送信

			$mailto = "info@neo-gate.jp";

			$title = "[業務連絡BBS]投稿のお知らせ";

$content =<<<EOT
新しいメッセージがあります。確認してください。

{$bbs_common_url}

EOT;

			$header = "From: info@neo-gate.jp\n";
			$header .= "Bcc: ".$bcc_add;

			/*
			$header .= "Bcc: minamikawa@neo-gate.jp";
			$header .= ",";
			$header .= $bcc_add;
			*/

			$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

			if( $result == false ){

				//複数回メールを送るので、トランザクションの対象とはしない

			}

		}

	}

	//ヒアリングのメール送信

	$mailto = $therapist_mail;

$title = sprintf("ヒアリング：%s【%s】%s/%s %s:%s：%s様",
$shop_area_name,$therapist_name,$month_mail,$day_mail,$start_hour_mail,$start_minute_mail,$customer_name);

$content =<<<EOT

下記の内容を簡潔にまとめて返信して下さい。

・お客様のカルテ、施術内容、リクエスト
　(例：左肩が異常に凝っている、腰の張りが酷いためストレッチ入れた など)
・お客様の環境、情報、特徴(部屋の状況や環境)
　(例：部屋が狭い、体が大きい為施術困難、タバコの臭い嫌い など)

※件名は変更しないでください。(先頭に「Re:」はついても大丈夫です。)
※予約データの顧客名と異なる場合などは、正しい情報に訂正して記載して下さい。

EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if( $result == false ){

		//複数回メールを送るので、トランザクションの対象とはしない

	}

	//コミット
	$sql = "commit";
	mysql_query( $sql, $con );

	//MySQL切断
	mysql_close( $con );

	return true;
	exit();

}

function update_reservation_by_therapist(
$course_var,$course_add,$card_flg,$reservation_for_board_id,$area,$therapist_id,$course_old,$shop_name){

	if( $shop_name == "大阪アロマスタイル" ){

		$course = get_course_int_for_aroma_style_common($course_var);

		$course_add = 0;

	}else{

		$course = trim(str_replace("分+リフレ","",$course_var));
		$course = intval(trim(str_replace("分","",$course)));

		if (preg_match("/リフレ/",$course_var)) {

			$course_add = 30;

		}else{

			$course_add = 0;

		}

	}

	$check_url = get_check_url_shift_man($area,$therapist_id);

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);

	$attendance_id = $data["attendance_id"];
	$start_hour = $data["start_hour"];
	$start_minute = $data["start_minute"];
	$attendance_id = $data["attendance_id"];
	$shop_area = $data["shop_area"];
	$area_name = $data["area_name"];
	$customer_name = $data["customer_name"];
	$extension = $data["extension"];
	$card_confirm_flg = $data["card_confirm_flg"];
	$complete_flg = $data["complete_flg"];
	$start_flg = $data["start_flg"];
	$start_real_time_flg = false;

	$shop_name = trim($data["shop_name"]);

	$start = ( $start_hour * 60 ) + $start_minute;
	$end = $start + $course + $course_add + $extension;
	$end_hour = intval( $end / 60 );
	$end_minute = $end % 60;

	$mail_flg = true;
	$mail_type = "change";

	$course_change_flg = true;
	$extension_change_flg = false;

	update_reservation_data_transaction_common(
$start_hour,$start_minute,$end_hour,$end_minute,$reservation_for_board_id,$therapist_id,$attendance_id,
$shop_area,$area_name,$course,$extension,$card_flg,$card_confirm_flg,$complete_flg,$start_flg,
$start_real_time_flg,$mail_flg,$mail_type,$customer_name,$check_url,
$shop_name,$course_old,$course_new,$extension_old,$extension_new,$course_change_flg,$extension_change_flg,
$course_var);

	return true;
	exit();

}

function attendance_data_exist_check($therapist_id,$year,$month,$day){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from attendance_new where
therapist_id='%s' and
year='%s' and
month='%s' and
day='%s'",
$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(attendance_data_exist_check)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$id = $row["id"];

	if( $id == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

//出席データの取得(日にちから)
function get_attendance_id_by_time($therapist_id,$year,$month,$day){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select * from attendance_new where
therapist_id='%s' and
year='%s' and
month='%s' and
day='%s'",
$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if( $res == false ){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_id_by_time)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();

}

function check_time_change_edit_kensyuu($start_time_work,$end_time_work,$start_time_work_old,$end_time_work_old){

	if( ($start_time_work != "-1") && ($end_time_work != "-1") && ($start_time_work_old != "-1") && ($end_time_work_old != "-1") ){

		if( ($start_time_work==$start_time_work_old) && ($end_time_work==$end_time_work_old) ){

			return false;
			exit();

		}else{

			return true;
			exit();

		}

	}else{

		return false;
		exit();

	}

}

function get_help_page(){

	include(INC_PATH."/db_connect.php");

	$sql = "select * from help_page where delete_flg=0 order by order_value desc";
	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_help_page)";
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

function get_message_board($limit_num){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from message_board where delete_flg=0 order by created desc limit 0,%s",$limit_num);
	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_message_board)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$created = $row["created"];

		$month = date('m',$created);
		$day = date('d',$created);
		$hour = date('h',$created);
		$minute = date('i',$created);
		$week = date('w',$created);

		$week_name = get_week_name_common($week);

$day_disp =<<<EOT
{$month}/{$day}（{$week_name}）{$hour}：{$minute}
EOT;

		$list_data[$i] = $row;
		$list_data[$i]["day_disp"] = $day_disp;

		$i++;

	}

	return $list_data;
	exit();

}

function get_message_board_driver_2($limit_num,$area){

	include(INC_PATH."/db_connect.php");

	$where_area = sprintf('((area="all") or (area="%s"))',$area);

	$sql = sprintf("
select * from message_board_driver where
delete_flg=0 and %s
order by created desc limit 0,%s",
$where_area,$limit_num);

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_message_board_driver_2)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$created = $row["created"];

		$month = date('m',$created);
		$day = date('d',$created);
		$hour = date('h',$created);
		$minute = date('i',$created);
		$week = date('w',$created);

		$week_name = get_week_name_common($week);

$day_disp =<<<EOT
{$month}/{$day}（{$week_name}）{$hour}：{$minute}
EOT;

		$list_data[$i] = $row;
		$list_data[$i]["day_disp"] = $day_disp;

		$i++;

	}

	return $list_data;
	exit();

}

function get_att_area_select_disp_flg($therapist_id){

	// DBに接続
	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from therapist_new where id='%s'",$therapist_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_att_area_select_disp_flg)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$area = $row["area"];
	$kenmu = $row["kenmu"];
	$kensyuu_flg = $row["kensyuu_flg"];

	if( $kenmu != "" ){

		return true;
		exit();

	}else if( ($area=="yokohama") && ($kensyuu_flg=="1") ){

		return true;
		exit();

	}else{


		return false;
		exit();

	}

}

function get_radio_html_for_att_area($att_area,$shop_area){

	$html = "";

	$space = "&nbsp;&nbsp;";

	if( $att_area == "tokyo" ){

		$html .= '<input type="radio" name="att_area" value="tokyo" checked>東京';
		$html .= $space;
		$html .= '<input type="radio" name="att_area" value="yokohama">横浜';

	}else if( $att_area == "yokohama" ){

		$html .= '<input type="radio" name="att_area" value="tokyo">東京';
		$html .= $space;
		$html .= '<input type="radio" name="att_area" value="yokohama" checked>横浜';

	}else{

		if( $shop_area == "tokyo" ){

			$html .= '<input type="radio" name="att_area" value="tokyo" checked>東京';
			$html .= $space;
			$html .= '<input type="radio" name="att_area" value="yokohama">横浜';

		}else if( $shop_area == "yokohama" ){

			$html .= '<input type="radio" name="att_area" value="tokyo">東京';
			$html .= $space;
			$html .= '<input type="radio" name="att_area" value="yokohama" checked>横浜';

		}else{

			$html .= '<input type="radio" name="att_area" value="tokyo">東京';
			$html .= $space;
			$html .= '<input type="radio" name="att_area" value="yokohama">横浜';

		}

	}

	return $html;
	exit();

}

function get_remuneration_month($year,$month,$therapist_id){

	$therapist_data = get_therapist_data_by_id_common($therapist_id);

	if( $therapist_data == false ){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_remuneration_month)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$point_ref = $therapist_data["point_ref"];
	$point_fix = $therapist_data["point_fix"];
	$total_point = $therapist_data["total_point"];
	$area = $therapist_data["area"];

	$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$area);

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select * from sale_history
where
delete_flg=0 and
therapist_id='%s' and
eigyou_year='%s' and
eigyou_month='%s'",
$therapist_id,$year,$month);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_remuneration_month)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$i=0;
	$remuneration_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$reservation_no = $row["reservation_no"];
		$price = $row["price"];

		$reservation_data = get_reservation_for_board_data_by_reservation_no_common($reservation_no);

		if( $reservation_data == false ){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_remuneration_month)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$shimei_flg = $reservation_data["shimei_flg"];
		$transportation = $reservation_data["transportation"];

		if( $shimei_flg == "1" ){
			$shimei_value = 1000;
		}else{
			$shimei_value = 0;
		}

		$price_shijutsu = $price - $shimei_value - $transportation;

		if( $share_rate == "-1" ){

			$remuneration = 0;

		}else{

			$remuneration = $price_shijutsu * ($share_rate / 100);

		}

		$remuneration_all = $remuneration_all + $remuneration;

	}

	return $remuneration_all;
	exit();

}

function get_sale_history_month($year,$month,$therapist_id){

	include(INC_PATH."/db_connect.php");

$sql = sprintf("
select * from sale_history
where
delete_flg=0 and
therapist_id='%s' and
eigyou_year='%s' and
eigyou_month='%s'",
$therapist_id,$year,$month);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_sale_history_month)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$month_disp = add_zero_when_under_ten_common($row["eigyou_month"]);
		$day_disp = add_zero_when_under_ten_common($row["eigyou_day"]);
		$day = $row["eigyou_day"];

		$week_name = get_week_name_by_time_common($year, $month, $day);

		$list_data[$i] = $row;
		$list_data[$i]["week_name"] = $week_name;
		$list_data[$i]["month_disp"] = $month_disp;
		$list_data[$i]["day_disp"] = $day_disp;

		$i++;

	}

	return $list_data;
	exit();

}

function get_sale_history_price_day($year,$month,$day,$therapist_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select * from sale_history
where
delete_flg=0 and
therapist_id='%s' and
eigyou_year='%s' and
eigyou_month='%s' and
eigyou_day='%s'",
$therapist_id,$year,$month,$day);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_sale_history_day)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$price_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$price = $row["price"];

		$price_all = $price_all + $price;

	}

	return $price_all;
	exit();

}

function get_sale_history_data_for_operation_list_staff($year,$month,$staff_id){

	$data = array();
	$x = 0;

	for($i=31;$i>=1;$i--){

		$day = $i;

		$tmp = get_furikomi_price_driver_by_day_2_common($staff_id,$year,$month,$day);

		$furikomi_price = $tmp["furikomi_price"];

		if( $furikomi_price > 0 ){

			$week_name = get_week_name_by_time_common($year, $month, $day);
			$month_disp = add_zero_when_under_ten_common($month);
			$day_disp = add_zero_when_under_ten_common($day);

			$data[$x]["remuneration"] = $furikomi_price;
			$data[$x]["year"] = $year;
			$data[$x]["month"] = $month;
			$data[$x]["day"] = $day;
			$data[$x]["week_name"] = $week_name;
			$data[$x]["month_disp"] = $month_disp;
			$data[$x]["day_disp"] = $day_disp;

			$x++;

		}

	}

	return $data;
	exit();

}

function get_operation_list_month_select_frm($year_hiki,$month_hiki,$staff_id){

	$data = get_most_small_year_and_month_for_operation_list($staff_id);

	$most_small_year = $data["year"];
	$most_small_month = $data["month"];

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
			$result = check_attendance_staff_new_exist_for_operation_list($staff_id,$year,$month);

			if( $result == true ){

				$data[$z]["year"] = $year;
				$data[$z]["month"] = $month;

				$z++;

			}

		}

	}

	/*
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	exit();
	*/

	$data_num = count($data);

	$html = '<select name="month" id="operation_list_month_select">';

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

function get_most_small_year_and_month_for_operation_list($staff_id){

	include(INC_PATH."/db_connect.php");

	$min_year = "2014";

	$sql = sprintf("
select year from attendance_staff_new
where
year>%s and
staff_id='%s' and
today_absence='0' and
kekkin_flg='0' and
syounin_state='1'
order by year asc",
$min_year,$staff_id);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "クエリー実行で失敗しました(get_most_small_year_and_month_for_operation_list:1)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$year = $row["year"];

	$sql = sprintf("
select month from attendance_staff_new
where
staff_id='%s' and
year='%s' and
today_absence='0' and
kekkin_flg='0' and
syounin_state='1'
order by month asc",
$staff_id,$year);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "クエリー実行で失敗しました(get_most_small_year_and_month_for_operation_list:2)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$month = $row["month"];

	$data["year"] = $year;
	$data["month"] = $month;

	return $data;
	exit();

}

//データがあるかどうかチェック(ある：true,ない：false)
function check_attendance_new_exist_for_operation_list($therapist_id,$year,$month){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from attendance_new
where
therapist_id='%s' and
year='%s' and
month='%s' and
today_absence='0' and
kekkin_flg='0' and
syounin_state='1'",
$therapist_id,$year,$month);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "クエリー実行で失敗しました(check_attendance_new_exist_for_operation_list)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

function get_sale_history_data_for_operation_detail($year,$month,$day,$therapist_id){

	$attendance_id = get_attendance_id_by_time_common($therapist_id,$year,$month,$day);

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select sale_history.* from sale_history
left join reservation_for_board on reservation_for_board.reservation_no=sale_history.reservation_no
where
sale_history.delete_flg=0 and
sale_history.therapist_id='%s' and
sale_history.eigyou_year='%s' and
sale_history.eigyou_month='%s' and
sale_history.eigyou_day='%s' order by reservation_for_board.start_hour desc",
$therapist_id,$year,$month,$day);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_sale_history_data_for_operation_detail)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$therapist_data = get_therapist_data_by_id_common($therapist_id);

	if( $therapist_data == false ){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_data_by_id_common)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$point_ref = $therapist_data["point_ref"];
	$point_fix = $therapist_data["point_fix"];
	$total_point = $therapist_data["total_point"];
	$area = $therapist_data["area"];

	$share_rate = get_share_rate_by_attendance_id_common($attendance_id);

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$reservation_no = $row["reservation_no"];
		$price = $row["price"];

		$reservation_data = get_reservation_for_board_data_by_reservation_no_common($reservation_no);

		if( $reservation_data == false ){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_reservation_for_board_data_by_reservation_no_common)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$start_hour = $reservation_data["start_hour"];
		$start_minute = add_zero_when_under_ten_common($reservation_data["start_minute"]);
		$end_hour = $reservation_data["end_hour"];
		$end_minute = add_zero_when_under_ten_common($reservation_data["end_minute"]);
		$shop_name = $reservation_data["shop_name"];
		$shimei_flg = $reservation_data["shimei_flg"];
		$customer_name = $reservation_data["customer_name"];
		$area_name = $reservation_data["area_name"];
		$course = $reservation_data["course"];
		$extension = $reservation_data["extension"];
		$transportation = $reservation_data["transportation"];

		$time_disp = sprintf("%s：%s　－　%s：%s",$start_hour,$start_minute,$end_hour,$end_minute);

		$cource_time = $course + $extension;

		$price_shijutsu = get_price_shijutsu_value_common($price,$shimei_flg,$transportation);

		$point = get_point_value_common($shimei_flg);

		$remuneration = get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate);

		if( $shimei_flg == "1" ){

			$customer_name = "【指名】".$customer_name;

		}

		$list_data[$i]["time_disp"] = $time_disp;
		$list_data[$i]["shop_name"] = $shop_name;
		$list_data[$i]["customer_name"] = $customer_name;
		$list_data[$i]["area_name"] = $area_name;
		$list_data[$i]["cource_time"] = $cource_time;
		$list_data[$i]["price_shijutsu"] = $price_shijutsu;
		$list_data[$i]["point"] = $point;
		$list_data[$i]["remuneration"] = $remuneration;

		$i++;

	}

	return $list_data;
	exit();

}

function get_point_list_month_select_frm($year_hiki,$month_hiki,$therapist_id){

	//operation_listと同じ
	$data = get_most_small_year_and_month_for_operation_list($therapist_id);

	$most_small_year = $data["year"];
	$most_small_month = $data["month"];

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
			//operation_listと同じ
			$result = check_attendance_new_exist_for_operation_list_new($therapist_id,$year,$month);

			if( $result == true ){

				$data[$z]["year"] = $year;
				$data[$z]["month"] = $month;

				$z++;

			}

		}

	}

	/*
	 echo "<pre>";
	print_r($data);
	echo "</pre>";
	exit();
	*/

	$data_num = count($data);

	$html = '<select name="month" id="point_list_month_select">';

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

function get_sale_data_for_point_list($year,$month,$therapist_id){

	$data = array();
	$x = 0;

	for($i=31;$i>=1;$i--){

		$day = $i;

		$point_data = get_point_day_new($year,$month,$day,$therapist_id);

		if( $point_data != "-1" ){

			$point_shijutsu = get_maru_num($point_data["point_shijutsu"]);
			$point_shimei = get_maru_num($point_data["point_shimei"]);
			$point_repeat = get_maru_num($point_data["point_repeat"]);
			$point_sum = $point_data["point_sum"];

			$week_name = get_week_name_by_time_common($year, $month, $day);

			$month_disp = add_zero_when_under_ten_common($month);
			$day_disp = add_zero_when_under_ten_common($day);

			$data[$x]["point_shijutsu"] = $point_shijutsu;
			$data[$x]["point_shimei"] = $point_shimei;
			$data[$x]["point_repeat"] = $point_repeat;
			$data[$x]["point_sum"] = $point_sum;
			$data[$x]["year"] = $year;
			$data[$x]["month"] = $month;
			$data[$x]["day"] = $day;
			$data[$x]["week_name"] = $week_name;
			$data[$x]["month_disp"] = $month_disp;
			$data[$x]["day_disp"] = $day_disp;

			$x++;

		}

	}

	return $data;
	exit();

}

function get_point_day($year,$month,$day,$therapist_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select reservation_for_board.shimei_flg from reservation_for_board
left join sale_history on sale_history.reservation_no=reservation_for_board.reservation_no
where
reservation_for_board.delete_flg=0 and
sale_history.therapist_id='%s' and
reservation_for_board.year='%s' and
reservation_for_board.month='%s' and
reservation_for_board.day='%s'",
$therapist_id,$year,$month,$day);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_point_day)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$point_shijutsu = 0;
	$point_shimei = 0;

	while($row = mysql_fetch_assoc($res)){

		$shimei_flg = $row["shimei_flg"];

		if( $shimei_flg == "1" ){

			$point_shimei = $point_shimei + 3;

		}

		$point_shijutsu++;

	}

	if( $point_shijutsu == "0" ){

		return "-1";
		exit();

	}

	//リピーターポイントの取得

	$attendance_id = get_attendance_id_by_time_common($therapist_id,$year,$month,$day);

	if( $attendance_id == false ){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_id_by_time_common)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$repeat_point_data = get_repeat_point_data_by_attendance_id_common($attendance_id);
	$repeat_point_value = $repeat_point_data["value"];

	$point_sum = $point_shijutsu + $point_shimei + $repeat_point_value;

	$data["point_shijutsu"] = $point_shijutsu;
	$data["point_shimei"] = $point_shimei;
	$data["point_repeat"] = $repeat_point_value;
	$data["point_sum"] = $point_sum;

	return $data;
	exit();

}

function get_point_day_new($year,$month,$day,$therapist_id){

	include(INC_PATH."/db_connect.php");

	$attendance_id = get_attendance_id_work_common($therapist_id,$year,$month,$day);

	if( $attendance_id == "" ){

		return "-1";
		exit();

	}

	$data = get_therapist_point_by_attendance_id_common($attendance_id);

	$pt_repeat = $data["pt_repeat"];
	$pt_operation = $data["pt_operation"];
	$pt_shimei = $data["pt_shimei"];

	if( ($pt_repeat == "0") && ($pt_operation == "0") && ($pt_shimei == "0") ){

		return "-1";
		exit();

	}

	$point_sum = $pt_repeat + $pt_operation + $pt_shimei;

	$data["point_shijutsu"] = $pt_operation;
	$data["point_shimei"] = $pt_shimei;
	$data["point_repeat"] = $pt_repeat;
	$data["point_sum"] = $point_sum;

	return $data;
	exit();

}

function get_maru_num($value){

	$data = $value;

	if( $value == "1" ){

		$data = "①";

	}else if( $value == "2" ){

		$data = "②";

	}else if( $value == "3" ){

		$data = "③";

	}else if( $value == "4" ){

		$data = "④";

	}else if( $value == "5" ){

		$data = "⑤";

	}else if( $value == "6" ){

		$data = "⑥";

	}else if( $value == "7" ){

		$data = "⑦";

	}else if( $value == "8" ){

		$data = "⑧";

	}else if( $value == "9" ){

		$data = "⑨";

	}else if( $value == "10" ){

		$data = "⑩";

	}else if( $value == "11" ){

		$data = "⑪";

	}else if( $value == "12" ){

		$data = "⑫";

	}else if( $value == "13" ){

		$data = "⑬";

	}else if( $value == "14" ){

		$data = "⑭";

	}else if( $value == "15" ){

		$data = "⑮";

	}else if( $value == "16" ){

		$data = "⑯";

	}else if( $value == "17" ){

		$data = "⑰";

	}else if( $value == "18" ){

		$data = "⑱";

	}else if( $value == "19" ){

		$data = "⑲";

	}else if( $value == "20" ){

		$data = "⑳";

	}

	if( $data == "0" ){

		$data = "&nbsp;";

	}

	return $data;
	exit();

}

function get_point_sum_for_point_list($year,$month,$therapist_id){

	$point_sum_all = 0;

	for($i=31;$i>=1;$i--){

		$day = $i;

		$point_data = get_point_day($year,$month,$day,$therapist_id);

		if( $point_data != "-1" ){

			$point_sum = $point_data["point_sum"];

			$point_sum_all = $point_sum_all + $point_sum;

		}

	}

	return $point_sum_all;
	exit();

}

function get_remuneration_month_new($year,$month,$therapist_id){

	$remuneration_all = 0;

	for($i=31;$i>=1;$i--){

		$day = $i;

		$attendance_id = get_attendance_id_work_common($therapist_id,$year,$month,$day);

		if( $attendance_id != "" ){

			$remuneration_data = get_therapist_remuneration_by_attendance_id_common($attendance_id);
			$remuneration = $remuneration_data["remuneration"];

			$remuneration_all = $remuneration_all + $remuneration;

		}

	}

	return $remuneration_all;
	exit();

}

//データがあるかどうかチェック(ある：true,ない：false)
function check_attendance_staff_new_exist_for_operation_list($staff_id,$year,$month){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from attendance_staff_new
where
staff_id='%s' and
year='%s' and
month='%s' and
today_absence='0' and
kekkin_flg='0' and
syounin_state='1'",
$staff_id,$year,$month);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "クエリー実行で失敗しました(check_attendance_staff_new_exist_for_operation_list)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

function get_top_disp_data_for_operation_list_staff($year,$month,$staff_id){

	$remuneration_all = 0;

	for($i=31;$i>=1;$i--){

		$day = $i;

		$data = get_furikomi_price_driver_by_day_2_common($staff_id,$year,$month,$day);

		$furikomi_price = $data["furikomi_price"];

		$remuneration_all = $remuneration_all + $furikomi_price;

	}

	$data["remuneration"] = $remuneration_all;

	return $data;
	exit();

}

function get_furikomi_week_data($type,$year,$month){

	$now_hour = intval(date('H'));

	if($now_hour <= 6){

		//昨日の日付
		$year_now = intval(date('Y', strtotime('-1 day')));
		$month_now = intval(date('m', strtotime('-1 day')));
		$day_now = intval(date('d', strtotime('-1 day')));
		$week_now = intval(date('w', strtotime('-1 day')));

	}else{

		$year_now = intval(date('Y'));
		$month_now = intval(date('m'));
		$day_now = intval(date('d'));
		$week_now = intval(date('w'));

	}

	if( ($year_now==$year) && ($month_now==$month) ){

		$mainasu_value = get_furikae_mainasu_value($type,$week_now);

		$week_data = get_week_data_for_operation_list($mainasu_value,$year_now,$month_now,$day_now,$type);

		/*
		echo "<pre>";
		print_r($week_data);
		echo "</pre>";
		exit();
		*/

	}else{

		return false;
		exit();

	}

	return $week_data;
	exit();

}

function get_week_data_for_operation_list($mainasu_value,$year,$month,$day,$type){

	if( $type == "jikai" ){

		$end_val = 0;

	}else if( $type == "zenkai" ){

		$end_val = $mainasu_value - 6;

	}else{

		echo "error!(get_week_data_for_operation_list)";
		exit();

	}

	$x = 0;

	$data = array();

	$day_now = $day;

	for( $i=$mainasu_value; $i>=$end_val; $i-- ){

		$day = $day_now - $i;

		$data[$x]["year"] = intval(date("Y", mktime(0, 0, 0, $month, $day, $year)));
		$data[$x]["month"] = intval(date("m", mktime(0, 0, 0, $month, $day, $year)));
		$data[$x]["day"] = intval(date("d", mktime(0, 0, 0, $month, $day, $year)));

		$x++;

	}

	return $data;
	exit();

}

function get_furikae_mainasu_value($type,$week){

	$data = "";

	if( $type == "jikai" ){

		if( $week == "0" ){

			$data = "3";

		}else if( $week == "1" ){

			$data = "4";

		}else if( $week == "2" ){

			$data = "5";

		}else if( $week == "3" ){

			$data = "6";

		}else if( $week == "4" ){

			$data = "0";

		}else if( $week == "5" ){

			$data = "1";

		}else if( $week == "6" ){

			$data = "2";

		}else{

			echo "error!(get_furikae_mainasu_value)";
			exit();

		}

	}else if( $type == "zenkai" ){

		if( $week == "0" ){

			$data = "10";

		}else if( $week == "1" ){

			$data = "11";

		}else if( $week == "2" ){

			$data = "12";

		}else if( $week == "3" ){

			$data = "13";

		}else if( $week == "4" ){

			$data = "7";

		}else if( $week == "5" ){

			$data = "8";

		}else if( $week == "6" ){

			$data = "9";

		}else{

			echo "error!(get_furikae_mainasu_value)";
			exit();

		}

	}else{

		echo "error!(get_furikae_mainasu_value)";
		exit();

	}

	return $data;
	exit();

}

function get_furikomi_price_for_operation_list($week_data,$therapist_id){

	$therapist_data = get_therapist_data_by_id_common($therapist_id);
	$jisou_flg = $therapist_data["jisou_flg"];

	$week_data_num = count($week_data);

	$furikomi_price = 0;

	for( $i=0; $i<$week_data_num; $i++ ){

		$year = $week_data[$i]["year"];
		$month = $week_data[$i]["month"];
		$day = $week_data[$i]["day"];

		if( $year > 2013 ){

			$attendance_data = get_attendance_data_work_common($therapist_id,$year,$month,$day);

			$attendance_id = $attendance_data["id"];
			$pay_day = $attendance_data["pay_day"];

			$pay_another = $attendance_data["pay_another"];
			$pay_finish = $attendance_data["pay_finish"];

			$allowance = 0;

			if( $attendance_id != "" ){

				if( $jisou_flg == "1" ){

					$allowance = get_allowance_therapist_common($year,$month,$day,$attendance_id);

				}

				$remuneration_data = get_therapist_remuneration_by_attendance_id_common($attendance_id);
				$remuneration = $remuneration_data["remuneration"];

				$furikomi_price = $furikomi_price + $remuneration - $pay_day + $allowance + $pay_another - $pay_finish;

			}

		}

	}

	return $furikomi_price;
	exit();

}

function get_this_month_flg($year,$month){

	$now_hour = intval(date('H'));

	if($now_hour <= 6){

		//昨日の日付
		$year_now = intval(date('Y', strtotime('-1 day')));
		$month_now = intval(date('m', strtotime('-1 day')));

	}else{

		$year_now = intval(date('Y'));
		$month_now = intval(date('m'));

	}

	if( ($year_now==$year) && ($month_now==$month) ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

function get_repeater_by_attendance_id_for_operation_detail($attendance_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select repeater.*,shop.name as shop_name from repeater
left join shop on shop.id=repeater.shop_id
where repeater.delete_flg=0 and repeater.attendance_id='%s' order by repeater.created desc",
$attendance_id);

	$res = mysql_query($sql, $con);

	if( $res == false ){

		echo "error!(get_repeater_by_attendance_id_for_operation_detail)";
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

function get_repeater_by_attendance_id_for_point_list($year,$month,$therapist_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select
repeater.*,
shop.name as shop_name,
attendance_new.year,
attendance_new.month,
attendance_new.day
from attendance_new
left join repeater on repeater.attendance_id=attendance_new.id
left join shop on shop.id=repeater.shop_id
where
attendance_new.today_absence='0' and
attendance_new.kekkin_flg='0' and
attendance_new.syounin_state='1' and
attendance_new.therapist_id='%s' and
attendance_new.year='%s' and
attendance_new.month='%s' and
repeater.delete_flg=0
order by repeater.created desc",
$therapist_id,$year,$month);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if( $res == false ){

		echo "error!(get_repeater_by_attendance_id_for_point_list)";
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

function get_point_list_this_month_data($therapist_id){

	$now_hour = intval(date('H'));

	if($now_hour < 1){

		//昨日の日付
		$year = intval(date('Y', strtotime('-1 day')));
		$month = intval(date('m', strtotime('-1 day')));
		$day = intval(date('d', strtotime('-1 day')));

	}else{

		$year = intval(date('Y'));
		$month = intval(date('m'));
		$day = intval(date('d'));

	}

	//前日の日付
	$year = intval(date("Y", mktime(0, 0, 0, $month, $day-1, $year)));
	$month = intval(date("m", mktime(0, 0, 0, $month, $day-1, $year)));
	$day = intval(date("d", mktime(0, 0, 0, $month, $day-1, $year)));

	$total_point = get_total_point_by_therapist_id_common($therapist_id,$year,$month,$day);

	$therapist_data = get_therapist_data_by_id_common($therapist_id);

	$point_ref = $therapist_data["point_ref"];
	$point_fix = $therapist_data["point_fix"];
	$therapist_area = $therapist_data["area"];

	/*
	echo $point_ref;echo "<br />";
	echo $point_fix;echo "<br />";
	echo $total_point;echo "<br />";
	echo $therapist_area;echo "<br />";
	*/

	$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$therapist_area);

	//echo $share_rate;echo "<br />";

	$need_point = get_need_point_for_next_stage_share_rate($total_point,$therapist_area,$point_ref,$share_rate);

	$graph_html = get_graph_html($total_point,$therapist_area,$point_ref,$share_rate);

	$data["total_point"] = $total_point;
	$data["share_rate"] = $share_rate;
	$data["need_point"] = $need_point;
	$data["graph_html"] = $graph_html;

	return $data;
	exit();

}

function get_need_point_for_next_stage_share_rate($total_point,$area,$point_ref,$share_rate){

	$value = "-1";

	$max_share_rate = get_max_share_rate_by_area_common($area,$point_ref);

	if( $share_rate < $max_share_rate ){

		$share_rate_next = $share_rate + 1;

		$point_next = get_point_be_share_rate_common($area,$point_ref,$share_rate_next);

		$value = $point_next - $total_point;

	}

	return $value;
	exit();

}

function get_graph_html($total_point,$area,$point_ref,$share_rate){

	$html = "";

	$max_share_rate = get_max_share_rate_by_area_common($area,$point_ref);

	if( $share_rate < $max_share_rate ){

		$share_rate_next = $share_rate + 1;

		$point_now = get_point_be_share_rate_common($area,$point_ref,$share_rate);
		$point_next = get_point_be_share_rate_common($area,$point_ref,$share_rate_next);

		$sa = $point_next - $point_now;

		$shinchoku = $total_point - $point_now;

		$width = 240*($shinchoku/$sa);

		/*
		echo "shinchoku:".$shinchoku;echo "<br />";
		echo "width:".$width;echo "<br />";
		echo "max_share_rate:".$max_share_rate;echo "<br />";
		echo "total_point:".$total_point;echo "<br />";
		echo "point_next:".$point_next;echo "<br />";
		echo "point_now:".$point_now;exit();
		*/

		if( $width < 0 ){
			$width = 0;
		}

		$html = sprintf(
'<div style="width:240px;border:solid 2px #bc763c;"><div style="background:#e17b34;width:%spx;">&nbsp;</div></div>',
$width);

	}

	return $html;
	exit();

}

function get_max_share_rate_flg($therapist_id){

	$therapist_data = get_therapist_data_by_id_common($therapist_id);

	$point_ref = $therapist_data["point_ref"];
	$area = $therapist_data["area"];

	$share_rate = get_share_rate_by_therapist_id_and_now_common($therapist_id);

	$max_share_rate = get_max_share_rate_by_area_common($area,$point_ref);

	if( $share_rate == $max_share_rate ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

//「start_real_time」がゼロではないかチェック
function check_start_real_time_not_zero_by_reservation_for_board_id($reservation_for_board_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select start_real_time from reservation_for_board where id='%s'",$reservation_for_board_id);

	$res = mysql_query($sql, $con);

	if( $res == false ){

		echo "error!(check_start_real_time_not_zero_by_reservation_for_board_id)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["start_real_time"] == "0" ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

function check_attendance_new_exist_for_shift_add($therapist_id,$year,$month,$day){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from attendance_new
where
therapist_id='%s' and
year='%s' and
month='%s' and
day='%s'",
$therapist_id,$year,$month,$day);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(check_attendance_new_exist_for_shift_add)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

function check_access_user_seigen_staff($staff_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from staff_new_new where
id='%s' and
delete_flg='0' and
leave_flg='0'",
$staff_id);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(check_access_user_seigen_staff)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

function get_movement_cost_for_edit($reservation_for_board_id,$movement_cost_id){

	if( $movement_cost_id == "" ){

		$movement_cost_id = get_movement_cost_id_by_reservation_for_board_id($reservation_for_board_id);

	}

	//echo $movement_cost_id;exit();

	if( $movement_cost_id != "" ){

		$data = get_movement_cost_by_id_common($movement_cost_id);

		$reservation_for_board_id = $data["reservation_for_board_id"];
		$year = $data["year"];
		$month = $data["month"];
		$day = $data["day"];
		$cost_value = $data["cost_value"];
		$movement_method = $data["movement_method"];
		$area_name = $data["area_name"];
		$other = $data["other"];
		$area = $data["area"];

	}else if( $reservation_for_board_id != "" ){

		$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);

		$reservation_for_board_id = $reservation_for_board_id;
		$year = $data["year"];
		$month = $data["month"];
		$day = $data["day"];
		$cost_value = 0;
		$movement_method = "-1";
		$area_name = $data["area_name"];
		$other = "";
		$area = $data["shop_area"];

	}else{

		echo "error!(get_movement_cost_for_edit)";
		exit();

	}

	$return_data["reservation_for_board_id"] = $reservation_for_board_id;
	$return_data["year"] = $year;
	$return_data["month"] = $month;
	$return_data["day"] = $day;
	$return_data["cost_value"] = $cost_value;
	$return_data["movement_method"] = $movement_method;
	$return_data["area_name"] = $area_name;
	$return_data["other"] = $other;
	$return_data["area"] = $area;

	return $return_data;
	exit();

}

function regist_update_movement_cost(
$movement_cost_id,$therapist_id,$reservation_for_board_id,$year,$month,$day,$cost_value,$movement_method,$area_name,
$other,$area){

	include(INC_PATH."/db_connect.php");

	if( $movement_cost_id == "" ){

		$movement_cost_id = get_movement_cost_id_by_reservation_for_board_id($reservation_for_board_id);

	}

	if( $movement_cost_id == "" ){

		//インサート文
		$sql = sprintf("
insert into movement_cost(
therapist_id,reservation_for_board_id,year,month,day,cost_value,movement_method,area_name,
other,area)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$therapist_id,$reservation_for_board_id,$year,$month,$day,$cost_value,$movement_method,$area_name,
$other,$area);

	}else{

		//アップデート文
		$sql = sprintf("
update movement_cost set
cost_value='%s',
movement_method='%s',
area_name='%s',
other='%s'
where id='%s'",
$cost_value,$movement_method,$area_name,$other,$movement_cost_id);

	}

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if( $res == false ){

		echo "error!(regist_update_movement_cost)";
		exit();

	}

	return true;
	exit();

}

function get_movement_cost_id_by_reservation_for_board_id($reservation_for_board_id){

	include(INC_PATH."/db_connect.php");

	$movement_cost_id = "";

	if( $reservation_for_board_id == "-1" ){

		return $movement_cost_id;
		exit();

	}

	$sql = sprintf("
select id from movement_cost where reservation_for_board_id='%s' and delete_flg='0'",$reservation_for_board_id);

	$res = mysql_query($sql, $con);

	if( $res == false ){

		echo "error!(get_movement_cost_id_by_reservation_for_board_id)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();

}

function get_movement_cost_data_today($therapist_id){

	include(INC_PATH."/db_connect.php");

	$data = get_today_year_month_day_common();

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$sql = sprintf("
select * from movement_cost
where
delete_flg=0 and
year='%s' and
month='%s' and
day='%s' and
therapist_id='%s'",
$year,$month,$day,$therapist_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_movement_cost_data_today)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$list_data[$i] = $row;
		$i++;

	}

	return $list_data;
	exit();

}

function delete_movement_cost($movement_cost_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("update movement_cost set delete_flg='1' where id='%s'",$movement_cost_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(delete_movement_cost)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	return true;
	exit();

}

function get_rate_for_reportcard($repeater_num,$new_num,$shimei_num,$all_num){

	$repeater_rate = ($repeater_num/$new_num)*100;
	$repeater_rate = kirisute_common($repeater_rate,1);

	$shimei_rate = ($shimei_num/$all_num)*100;
	$shimei_rate = kirisute_common($shimei_rate,1);

	$repeater_rate = add_shousuu_ten_zero($repeater_rate);
	$shimei_rate = add_shousuu_ten_zero($shimei_rate);

	$data["repeater_rate"] = $repeater_rate;
	$data["shimei_rate"] = $shimei_rate;

	return $data;
	exit();

}

function add_shousuu_ten_zero($data){

	if (!strstr($data, '.')) {

		$data = $data.".0";

	}

	return $data;
	exit();

}

function send_kekkin_mail_to_honbu($attendance_id){

	$therapist_name = get_therapist_name_by_attendance_id_common($attendance_id);

	$data = get_reservation_for_board_data_by_attendance_id($attendance_id);

	$data_num = count($data);

	/*
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	exit();
	*/

	for($i=0;$i<$data_num;$i++){

		$shop_area = $data[$i]["shop_area"];
		$year = $data[$i]["year"];
		$month = $data[$i]["month"];
		$day = $data[$i]["day"];
		$customer_name = $data[$i]["customer_name"];
		$start_hour = add_zero_when_under_ten_common($data[$i]["start_hour"]);
		$start_minute = add_zero_when_under_ten_common($data[$i]["start_minute"]);

		$week_name = get_week_name_by_time_common($year, $month, $day);

$check_url = sprintf("%sman/reservation_status_board.php?year=%s&month=%s&day=%s&area=%s",
WWW_URL_SITE,$year,$month,$day,$shop_area);

		mb_language("ja");
		mb_internal_encoding("UTF-8");
		$mailto = "info@neo-gate.jp";
		//$mailto = "minamikawa@neo-gate.jp";

		$title = "【シフト登録】【予約確認】担当セラピストが欠勤しました";

$content =<<<EOT
{$month}/{$day}({$week_name}){$start_hour}：{$start_minute}～　{$customer_name}
セラピスト：{$therapist_name}

{$check_url}
EOT;

		$header = "From: info@neo-gate.jp\n";
		//$header .= "Bcc: minamikawa@neo-gate.jp";

		mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	}

	return true;
	exit();

}

function get_reservation_for_board_data_by_attendance_id($attendance_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select * from reservation_for_board
where
delete_flg=0 and
attendance_id='%s'",
$attendance_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_reservation_for_board_data_by_attendance_id)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$list_data[$i] = $row;
		$i++;

	}

	return $list_data;
	exit();

}

function update_reservation_for_board_data_kekkin($attendance_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
update reservation_for_board set attendance_id='-1' where attendance_id='%s'",
$attendance_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(update_reservation_for_board_data_kekkin)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	return true;
	exit();

}

function get_attendance_new_syounin_state_by_time($therapist_id,$year,$month,$day){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select syounin_state from attendance_new where
therapist_id='%s' and
year='%s' and
month='%s' and
day='%s'",
$therapist_id,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if( $res == false ){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_new_syounin_state_by_time)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["syounin_state"];
	exit();

}

function get_furikomi_detail_staff($year,$month,$day,$staff_id){

	$check_result = check_last_day_for_remuneration_common($year,$month,$day);

	if( $check_result == true ){

		$furikomi_data = get_furikomi_price_driver_by_day_2_common($staff_id,$year,$month,$day);

		$furikomi_price = $furikomi_data["furikomi_price"];
		$remuneration = $furikomi_data["remuneration"];
		$gasoline_value = $furikomi_data["gasoline_value"];
		$highway = $furikomi_data["highway"];
		$parking = $furikomi_data["parking"];
		$allowance = $furikomi_data["allowance"];
		$pay_finish = $furikomi_data["pay_finish"];
		$car_distance = $furikomi_data["car_distance"];
		$sonota = $furikomi_data["sonota"];
		$pay_day = $furikomi_data["pay_day"];

		if( $furikomi_data["settings_gasoline_value"] > 0 ){

			$settings_gasoline_value = $furikomi_data["settings_gasoline_value"];

		}

		$car_distance_over_allowance = $furikomi_data["car_distance_over_allowance"];

		$chief_allowance = $furikomi_data["chief_allowance"];

		if( $remuneration == "0" ){

			$chief_allowance = 0;

		}

		$tmp = get_staff_attendance_data_by_time_common($staff_id,$year,$month,$day);

		$start_hour = $tmp["start_hour"];
		$start_minute = $tmp["start_minute"];
		$end_hour = $tmp["end_hour"];
		$end_minute = $tmp["end_minute"];

		$work_minute = get_work_minute_driver_common($start_hour,$start_minute,$end_hour,$end_minute);

		$tmp = get_staff_data_by_id_common($staff_id);
		$pay_hour = $tmp["pay_hour"];

		$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);

	}else{

		$furikomi_price = 0;
		$remuneration = 0;
		$gasoline_value = 0;
		$highway = 0;
		$parking = 0;
		$allowance = 0;
		$pay_finish = 0;
		$car_distance = 0;
		$sonota = 0;
		$start_hour = 0;
		$start_minute = 0;
		$end_hour = 0;
		$end_minute = 0;
		$work_minute = 0;
		$car_distance_over_allowance = 0;
		$chief_allowance = 0;
		$settings_gasoline_value = 0;
		$pay_hour = 0;
		$work_time = 0;
		$pay_day = 0;

	}

	//$unit_price = get_unit_price_by_car_distance_common($car_distance);
	$unit_price = get_unit_price_by_car_distance_2_common($car_distance,$year,$month,$day);

	$data["furikomi_price"] = $furikomi_price;
	$data["remuneration"] = $remuneration;
	$data["gasoline_value"] = $gasoline_value;
	$data["highway"] = $highway;
	$data["parking"] = $parking;
	$data["allowance"] = $allowance;
	$data["pay_finish"] = $pay_finish;
	$data["car_distance"] = $car_distance;
	$data["sonota"] = $sonota;
	$data["start_hour"] = $start_hour;
	$data["start_minute"] = $start_minute;
	$data["end_hour"] = $end_hour;
	$data["end_minute"] = $end_minute;
	$data["work_minute"] = $work_minute;
	$data["car_distance_over_allowance"] = $car_distance_over_allowance;
	$data["chief_allowance"] = $chief_allowance;
	$data["settings_gasoline_value"] = $settings_gasoline_value;
	$data["pay_hour"] = $pay_hour;
	$data["work_time"] = $work_time;
	$data["unit_price"] = $unit_price;
	$data["pay_day"] = $pay_day;

	return $data;
	exit();

}

function get_attendance_list_data_staff_2($attendance_data,$day_data,$staff_id,$area,$ch){

	include(INC_PATH."/time_array.php");

	$data = array();

	$k=0;

	$day_data_num = count($day_data);
	$attendance_data_num = count($attendance_data);

	for($i=0;$i<$day_data_num;$i++){

		$year = $day_data[$i]["year"];
		$month = $day_data[$i]["month"];
		$day = $day_data[$i]["day"];
		$week = $day_data[$i]["week"];

		$shop_holiday_flg = false;

		if( ( ($year=="2014") && ($month=="12") && ($day=="31") ) || ( ($year=="2015") && ($month=="1") && ($day=="1") ) ){

			$shop_holiday_flg = true;

		}

		$num = $day;

		$past_flg = today_past_check($year,$month,$day);

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
					$kekkin_flg = $attendance_data[$j]["kekkin_flg"];

					if( ($year==$year_a) && ($month==$month_a) && ($day==$day_a) ){

						$match_flg = true;

					}

				}

			}

			$html = "";

			if( $match_flg == true ){

				$start_time_ta = $time_array[$start_time]["minute"];
				$end_time_ta = $time_array[$end_time]["minute"];

				if($start_time_ta=="0"){

					$start_time_ta = "0".$start_time_ta;

				}

				if($end_time_ta=="0"){

					$end_time_ta = "0".$end_time_ta;

				}

				$html .= '<div class="shift_list_time">';

				$html .= $day."(".$week.")";
				$html .= "　";
				$html .= $time_array[$start_time]["hour"]."時".$start_time_ta."分";
				$html .= "／";
				$html .= $time_array[$end_time]["hour"]."時".$end_time_ta."分";
				$html .= "　";

				if( $jitaku_taiki_flg == "1" ){

					$html .= "<br />";
					$html .= "自宅待機";

				}

				$html .= '</div>';

				$html .= '<div class="shift_list_right_area_1">';

				$onclick_func=<<<EOT
onclick="move_shift_edit_page('{$staff_id}','{$area}','{$year}','{$month}','{$day}','{$start_time}','{$end_time}','{$ch}');"
EOT;

				if($kekkin_flg=="1"){

					$html .= '<div class="shift_list_right_area_kekkin">欠勤</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= sprintf('<input type="button" value="編集" name="send_list_edit" %s />',$onclick_func);
					$html .= '</div>';

				}else if($syounin_state=="1"){

					$html .= '<div class="shift_list_right_area_kakutei"><span style="color:blue;">確</span></div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= sprintf('<input type="button" value="編集" name="send_list_edit" %s />',$onclick_func);
					$html .= '</div>';

				}else if($syounin_state=="2"){

					$html .= '<div class="shift_list_right_area_fusyounin">不承認</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= sprintf('<input type="button" value="編集" name="send_list_edit" %s />',$onclick_func);
					$html .= '</div>';

				}else if($syounin_state=="3"){

					$html .= '<div class="shift_list_right_area_shimekiri">締切</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= '&nbsp;';
					$html .= '</div>';

				}else{

					$html .= '<div class="shift_list_right_area_kari">仮</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= sprintf('<input type="button" value="編集" name="send_list_edit" %s />',$onclick_func);
					$html .= '</div>';

				}

				$html .= '<br class="clear" />';

				$html .= '</div>';

				$html .= '<br class="clear" />';


				if( $kensyuu_result == true ){

					$html .= '<div class="shift_list_comment" style="color:#000;font-size:12px;">研修</div>';

				}

				if($comment != ""){

					$html .= '<div class="shift_list_comment">'.$comment.'</div>';

				}else if($common_comment != ""){

					$html .= '<div class="shift_list_comment">'.$common_comment.'</div>';

				}

				$data[$k] = $html;

			}else{

				$html = "";

				$html .= '<div class="shift_regist_check">';
				$html .= '<input type="checkbox" name="check_'.$num.'" value="1" />';
				$html .= '</div>';

				$html .= '<div class="shift_regist_time">';
				$html .= $day."(".$week.")";
				$html .= '</div>';

				$html .= '<div style="float:left;">';

				if( $shop_holiday_flg == false ){

					$html .= '<div class="shift_regist_select_1">';
					$html .= '<select name="start_time_'.$num.'">';
					$html .= '<option value="-1">未選択</option>';

					$type = "start";
					$html .= get_shift_time_option_for_selected($type,$shift_time_data,$num);

					$html .= '</select>';
					$html .= '</div>';

					$html .= '<div class="shift_regist_select_2">';
					$html .= '<select name="end_time_'.$num.'">';
					$html .= '<option value="-1">未選択</option>';

					$type = "end";
					$html .= get_shift_time_option_for_selected($type,$shift_time_data,$num);

					$html .= '</select>';

					$html .= '</div>';
					$html .= '<br class="clear" />';

					if( $therapist_jitaku_taiki_flg== "1" ){

						$html .= '<div style="padding:10px 0px 0px 10px;">';
						$html .= get_shift_time_checkbox_for_jitaku_taiki_flg($type,$shift_time_data,$num);
						$html .= '</div>';

					}

					if( $att_area_select_disp_flg == true ){
						$att_area_html = get_shift_time_radio_for_att_area($shift_time_data,$num,$area);

$html .=<<<EOT
<div style="padding:10px 0px 0px 10px;">
<div>
担当エリア
</div>
<div style="padding:5px 0px 0px 0px;">
{$att_area_html}
</div>
</div>
EOT;
					}

				}else{

					$html .= '　店休';

				}


				$html .= '</div>';

				$html .= '<br class="clear" />';

				$data[$k] = $html;

			}

			$k++;

		}

	}

	return $data;
	exit();

}

function regist_shift_data_by_staff(
$staff_id,$area,$year,$month,$shift_time_data,$start_time_check_all,$end_time_check_all){

	include(INC_PATH."/db_connect.php");

	$staff_name = get_staff_name_by_staff_id($staff_id);

	$check_url = get_check_url_driver_man($area,$staff_id);

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$max_day = get_max_day($year,$month);

	$now = time();

	for( $i=1; $i<=$max_day; $i++ ){

		$day = $i;

		$week = get_week_value($year,$month,$day);

		$start_time = $shift_time_data[$i]["start_time"];
		$end_time = $shift_time_data[$i]["end_time"];
		$check = $shift_time_data[$i]["check"];

		if( $check == "1" ){

			if( ( $start_time == "-1" ) || ( $end_time == "-1" ) ){

				$start_time = $start_time_check_all;
				$end_time = $end_time_check_all;

			}

		}

		if( ( $start_time == "-1" ) || ( $end_time == "-1" ) ){

			$start_time = "-1";
			$end_time = "-1";

		}

		if( ( $start_time == "" ) || ( $end_time == "" ) ){

			$start_time = "-1";
			$end_time = "-1";

		}

		$nothing_flg = false;

		if( ($start_time=="-1") || ($end_time=="-1") ){

			$nothing_flg = true;

		}

		$check_result = check_attendance_staff_new_data_exist_common($staff_id,$year,$month,$day);

		if( ($nothing_flg == false) && ($check_result == false) ){

			$type = "driver";
			$syounin_state = "0";

$sql = sprintf("
insert into attendance_staff_new(staff_id,type,year,month,day,start_time,end_time,area,syounin_state)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$staff_id,$type,$year,$month,$day,$start_time,$end_time,$area,$syounin_state);

			$res = mysql_query($sql, $con);

			if($res == false){

				//ロールバック
				$sql = "rollback";
				mysql_query( $sql, $con );
				$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_staff)";
				header("Location: ".WWW_URL."error.php");
				exit();

			}

		}

	}

	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$mailto = "info@neo-gate.jp";
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("【シフト登録】%s月シフト登録[%s]",$month,$staff_name);

$content =<<<EOT
{$staff_name}さんより、{$month}月のシフト登録がありました。

{$check_url}
EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(regist_shift_data_by_staff)";
		header("Location: ".WWW_URL."error.php");
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

function get_shift_time_option_for_selected_4($type,$start_time,$end_time){

	include(INC_PATH."/time_array.php");

	$html = "";

	$html .= '<option value="-1">未選択</option>';

	if( $type == "start" ){

		for($z=1;$z<=13;$z++){

			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($start_time=="-1") || ($start_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $start_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}else if( $type == "end" ){

		for($z=9;$z<=31;$z++){

			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($end_time=="-1") || ($end_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $end_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}


	return $html;
	exit();

}

function get_attendance_list_data_staff_3($attendance_data,$day_data,$staff_id,$area,$ch){

	include(INC_PATH."/time_array.php");

	$data = array();

	$k=0;

	$day_data_num = count($day_data);
	$attendance_data_num = count($attendance_data);

	for($i=0;$i<$day_data_num;$i++){

		$year = $day_data[$i]["year"];
		$month = $day_data[$i]["month"];
		$day = $day_data[$i]["day"];
		$week = $day_data[$i]["week"];

		$shop_holiday_flg = false;

		if( ( ($year=="2014") && ($month=="12") && ($day=="31") ) || ( ($year=="2015") && ($month=="1") && ($day=="1") ) ){

			$shop_holiday_flg = true;

		}

		$num = $day;

		$past_flg = today_past_check($year,$month,$day);

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
					$kekkin_flg = $attendance_data[$j]["kekkin_flg"];

					if( ($year==$year_a) && ($month==$month_a) && ($day==$day_a) ){

						$match_flg = true;

					}

				}

			}

			$html = "";

			if( $match_flg == true ){

				$start_time_ta = $time_array[$start_time]["minute"];
				$end_time_ta = $time_array[$end_time]["minute"];

				if($start_time_ta=="0"){

					$start_time_ta = "0".$start_time_ta;

				}

				if($end_time_ta=="0"){

					$end_time_ta = "0".$end_time_ta;

				}

				$html .= '<div class="shift_list_time">';

				$html .= $day."(".$week.")";
				$html .= "　";
				$html .= $time_array[$start_time]["hour"]."時".$start_time_ta."分";
				$html .= "／";
				$html .= $time_array[$end_time]["hour"]."時".$end_time_ta."分";
				$html .= "　";

				if( $jitaku_taiki_flg == "1" ){

					$html .= "<br />";
					$html .= "自宅待機";

				}

				$html .= '</div>';

				$html .= '<div class="shift_list_right_area_1">';

				$onclick_func=<<<EOT
onclick="move_shift_edit_page('{$staff_id}','{$area}','{$year}','{$month}','{$day}','{$start_time}','{$end_time}','{$ch}');"
EOT;

				if($kekkin_flg=="1"){

					if( $syounin_state == "1" ){

						$kekkin = "欠勤";

					}else{

						$kekkin = '<span style="font-size:10px;">欠勤(仮)</span>';

					}

					$html .= '<div class="shift_list_right_area_kekkin">'.$kekkin.'</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= sprintf('<input type="button" value="編集" name="send_list_edit" %s />',$onclick_func);
					$html .= '</div>';

				}else if($syounin_state=="1"){

					$html .= '<div class="shift_list_right_area_kakutei"><span style="color:blue;">確</span></div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= sprintf('<input type="button" value="編集" name="send_list_edit" %s />',$onclick_func);
					$html .= '</div>';

				}else if($syounin_state=="2"){

					$html .= '<div class="shift_list_right_area_fusyounin">不承認</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= sprintf('<input type="button" value="編集" name="send_list_edit" %s />',$onclick_func);
					$html .= '</div>';

				}else if($syounin_state=="3"){

					$html .= '<div class="shift_list_right_area_shimekiri">締切</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= '&nbsp;';
					$html .= '</div>';

				}else{

					$html .= '<div class="shift_list_right_area_kari">仮</div>';
					$html .= '<div class="shift_list_right_area_btn">';
					$html .= sprintf('<input type="button" value="編集" name="send_list_edit" %s />',$onclick_func);
					$html .= '</div>';

				}

				$html .= '<br class="clear" />';

				$html .= '</div>';

				$html .= '<br class="clear" />';


				if( $kensyuu_result == true ){

					$html .= '<div class="shift_list_comment" style="color:#000;font-size:12px;">研修</div>';

				}

				if($comment != ""){

					$html .= '<div class="shift_list_comment">'.$comment.'</div>';

				}else if($common_comment != ""){

					$html .= '<div class="shift_list_comment">'.$common_comment.'</div>';

				}

				$data[$k] = $html;

			}else{

				$html = "";

				$html .= '<div class="shift_regist_check">';
				$html .= '<input type="checkbox" name="check_'.$num.'" value="1" />';
				$html .= '</div>';

				$html .= '<div class="shift_regist_time">';
				$html .= $day."(".$week.")";
				$html .= '</div>';

				$html .= '<div style="float:left;">';

				if( $shop_holiday_flg == false ){

					$html .= '<div class="shift_regist_select_1">';
					$html .= '<select name="start_time_'.$num.'">';

					$type = "start";
					$html .= get_shift_time_option_for_selected_5($type,$shift_time_data,$num);

					$html .= '</select>';
					$html .= '</div>';

					$html .= '<div class="shift_regist_select_2">';
					$html .= '<select name="end_time_'.$num.'">';

					$type = "end";
					$html .= get_shift_time_option_for_selected_5($type,$shift_time_data,$num);

					$html .= '</select>';

					$html .= '</div>';
					$html .= '<br class="clear" />';

					if( $therapist_jitaku_taiki_flg== "1" ){

						$html .= '<div style="padding:10px 0px 0px 10px;">';
						$html .= get_shift_time_checkbox_for_jitaku_taiki_flg($type,$shift_time_data,$num);
						$html .= '</div>';

					}

					if( $att_area_select_disp_flg == true ){
						$att_area_html = get_shift_time_radio_for_att_area($shift_time_data,$num,$area);

$html .=<<<EOT
<div style="padding:10px 0px 0px 10px;">
<div>
担当エリア
</div>
<div style="padding:5px 0px 0px 0px;">
{$att_area_html}
</div>
</div>
EOT;
					}

				}else{

					$html .= '　店休';

				}


				$html .= '</div>';

				$html .= '<br class="clear" />';

				$data[$k] = $html;

			}

			$k++;

		}

	}

	return $data;
	exit();

}

function get_shift_time_option_for_selected_5($type,$shift_time_data,$num){

	include(INC_PATH."/time_array.php");

	$start_time = $shift_time_data[$num]["start_time"];
	$end_time = $shift_time_data[$num]["end_time"];

	$html = "";

	$html .= '<option value="-1">未選択</option>';

	if( $type == "start" ){

		for($z=1;$z<=13;$z++){
			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];
			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($start_time=="-1") || ($start_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $start_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}else if( $type == "end" ){

		for( $z=9; $z<=31; $z++ ){

			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];

			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ( $end_time == "-1" ) || ( $end_time == "" ) ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $end_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}

	return $html;
	exit();

}

function get_shift_time_option_for_selected_2_2($type,$start_time,$end_time){

	include(INC_PATH."/time_array.php");

	$html = "";

	if( $type == "start" ){

		for($z=1;$z<=13;$z++){
			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];
			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($start_time=="-1") || ($start_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $start_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}else if( $type == "end" ){

		for($z=9;$z<=31;$z++){
			$minute = $time_array[$z]["minute"];
			$hour = $time_array[$z]["hour"];
			if($minute=="0"){
				$minute = "0".$minute;
			}

			if( ($end_time=="-1") || ($end_time=="") ){

				$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

			}else{

				if( $z == $end_time ){

					$html .= '<option value="'.$z.'" selected>'.$hour.':'.$minute.'</option>';

				}else{

					$html .= '<option value="'.$z.'">'.$hour.':'.$minute.'</option>';

				}

			}

		}

	}

	return $html;
	exit();

}

function shift_edit_action_2(
$staff_id,$area,$year,$month,$day,$start_time,$end_time,$start_start_time,$start_end_time,$week_name){

	include(INC_PATH."/db_connect.php");

	$staff_name = get_staff_name_by_staff_id($staff_id);

	$check_url = get_check_url_driver_man($area,$staff_id);

	$start_time_hour = get_time_ja_hour($start_time);
	$start_time_minute = get_time_ja_minute($start_time);

	$end_time_hour = get_time_ja_hour($end_time);
	$end_time_minute = get_time_ja_minute($end_time);

	$start_start_time_hour = get_time_ja_hour($start_start_time);
	$start_start_time_minute = get_time_ja_minute($start_start_time);

	$start_end_time_hour = get_time_ja_hour($start_end_time);
	$start_end_time_minute = get_time_ja_minute($start_end_time);

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	$today_absence = 0;
	$kekkin_flg = 0;
	$syounin_state = 0;

	$shift_change_flg = 1;

$sql = sprintf("
update attendance_staff_new set
shift_change_flg='%s',
syounin_state='%s',
start_time='%s',
end_time='%s',
start_hour='%s',
start_minute='%s',
end_hour='%s',
end_minute='%s',
area='%s',
today_absence='%s',
kekkin_flg='%s'
where staff_id='%s' and year='%s' and month='%s' and day='%s'",
$shift_change_flg,$syounin_state,$start_time,$end_time,$start_time_hour,$start_time_minute,$end_time_hour,$end_time_minute,$area,
$today_absence,$kekkin_flg,$staff_id,$year,$month,$day);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action_2:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$now = time();
	$staff_type = "2";
	$message = sprintf("
%s/%s(%s)を%s:%s-%s:%sから%s:%s-%s:%sに変更",
$month,$day,$week_name,$start_start_time_hour,$start_start_time_minute,$start_end_time_hour,$start_end_time_minute,
$start_time_hour,$start_time_minute,$end_time_hour,$end_time_minute);

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minamikawa@neo-gate.jp";

	$title_in_name = "シフト変更通知";

	$title = sprintf("【%s】%s月%s日シフト変更[%s]",$title_in_name,$month,$day,$staff_name);

$content =<<<EOT
{$staff_name}さんより、{$month}月{$day}日({$week_name})のシフト変更依頼がありました。

出勤：{$start_start_time_hour}:{$start_start_time_minute}→{$start_time_hour}:{$start_time_minute}
退勤：{$start_end_time_hour}:{$start_end_time_minute}→{$end_time_hour}:{$end_time_minute}

{$check_url}
EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_edit_action_2:2)";
		header("Location: ".WWW_URL."error.php");
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

function shift_kekkin_action_2($staff_id,$area,$year,$month,$day,$week_name){

	include(INC_PATH."/db_connect.php");

	$staff_name = get_staff_name_by_staff_id($staff_id);

	$check_url = get_check_url_driver_man($area,$staff_id);

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$now = time();

	$now_year = intval(date('Y'));
	$now_month = intval(date('m'));
	$now_day = intval(date('d'));

	if( ($now_year==$year) && ($now_month==$month) && ($now_day==$day) ){

		$today_absence = 1;

	}else{

		$today_absence = 0;

	}

	$kekkin_flg = 1;
	$syounin_state = 0;
	$shift_change_flg = 1;

$sql = sprintf("
update attendance_staff_new set
shift_change_flg='%s',
kekkin_flg='%s',
syounin_state='%s',
today_absence='%s'
where staff_id='%s' and year='%s' and month='%s' and day='%s'",
$shift_change_flg,$kekkin_flg,$syounin_state,$today_absence,$staff_id,$year,$month,$day);

	$res = mysql_query($sql, $con);
	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_kekkin_action_2)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$now = time();
	$message = sprintf("%s/%s(%s)を欠勤に変更",$month,$day,$week_name);

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minamikawa@neo-gate.jp";

	$title = sprintf("【シフト登録】%s月%s日欠勤連絡[%s]",$month,$day,$staff_name);

$content =<<<EOT
{$staff_name}さんより、{$month}月{$day}日の欠勤連絡がありました。

{$check_url}
EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(shift_kekkin_action_2)";
		header("Location: ".WWW_URL."error.php");
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

function list_separate($list){

	$list_num = count($list);

	if( $list_num > 23 ){

		$list_num = 23;

	}

	$data_1 = array();

	for( $i=0; $i<3; $i++ ){

		$data_1[$i] = $list[$i];

	}

	$data_2 = array();

	$x = 0;

	for( $i=3; $i<$list_num; $i++ ){

		$data_2[$x] = $list[$i];
		$x++;

	}

	$return_data[0] = $data_1;
	$return_data[1] = $data_2;

	return $return_data;
	exit();

}

//予約ボードデータを取得
function get_reservation_for_board_data_today_for_driver($staff_id){

	include(INC_PATH."/db_connect.php");

	$data = get_today_year_month_day_common();

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$sql = sprintf("
select * from reservation_for_board
where
delete_flg=0 and
attendance_tmp_flg=0 and
year='%s' and
month='%s' and
day='%s' and
(okuri_driver_id='%s' or mukae_driver_id='%s')
order by id desc",
$year,$month,$day,$staff_id,$staff_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_reservation_for_board_data_today_for_driver)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$attendance_id = $row["attendance_id"];

		$therapist_name = get_therapist_name_by_attendance_id_common($attendance_id);

		$list_data[$i] = $row;
		$list_data[$i]["therapist_name"] = $therapist_name;

		$i++;

	}

	return $list_data;
	exit();

}

function board_data_divide_for_communication($data,$staff_id){

	$return_data = array();

	$x = 0;

	$data_num = count($data);

	for( $i=0; $i<$data_num; $i++ ){

		$okuri_driver_id = $data[$i]["okuri_driver_id"];
		$mukae_driver_id = $data[$i]["mukae_driver_id"];

		$reservation_for_board_id = $data[$i]["id"];

		$tmp = get_reservation_board_2_by_reservation_for_board_id_common($reservation_for_board_id);

		$okuri_hour = $tmp["okuri_hour"];
		$okuri_minute = $tmp["okuri_minute"];
		$mukae_hour = $tmp["mukae_hour"];
		$mukae_minute = $tmp["mukae_minute"];

		$okuri_driver_comment = $tmp["okuri_driver_comment"];
		$mukae_driver_comment = $tmp["mukae_driver_comment"];

		$select_name = "okuri_hour";
		$value = $okuri_hour;
		$select_frm_okuri_hour = get_select_frm_hour($value,$select_name);

		$select_name = "okuri_minute";
		$value = $okuri_minute;
		$select_frm_okuri_minute = get_select_frm_minute($value,$select_name);

		$select_name = "mukae_hour";
		$value = $mukae_hour;
		$select_frm_mukae_hour = get_select_frm_hour($value,$select_name);

		$select_name = "mukae_minute";
		$value = $mukae_minute;
		$select_frm_mukae_minute = get_select_frm_minute($value,$select_name);

		if( $okuri_driver_id == $staff_id ){

			$data_type = "okuri";
			$return_data[$x] = $data[$i];
			$return_data[$x]["data_type"] = $data_type;

			$return_data[$x]["select_frm_okuri_hour"] = $select_frm_okuri_hour;
			$return_data[$x]["select_frm_okuri_minute"] = $select_frm_okuri_minute;
			$return_data[$x]["select_frm_mukae_hour"] = $select_frm_mukae_hour;
			$return_data[$x]["select_frm_mukae_minute"] = $select_frm_mukae_minute;

			$return_data[$x]["okuri_driver_comment"] = $okuri_driver_comment;
			$return_data[$x]["mukae_driver_comment"] = $mukae_driver_comment;

			$x++;

		}

		if( $mukae_driver_id == $staff_id ){

			$data_type = "mukae";
			$return_data[$x] = $data[$i];
			$return_data[$x]["data_type"] = $data_type;

			$return_data[$x]["select_frm_okuri_hour"] = $select_frm_okuri_hour;
			$return_data[$x]["select_frm_okuri_minute"] = $select_frm_okuri_minute;
			$return_data[$x]["select_frm_mukae_hour"] = $select_frm_mukae_hour;
			$return_data[$x]["select_frm_mukae_minute"] = $select_frm_mukae_minute;

			$return_data[$x]["okuri_driver_comment"] = $okuri_driver_comment;
			$return_data[$x]["mukae_driver_comment"] = $mukae_driver_comment;

			$x++;

		}

	}

	return $return_data;
	exit();

}

function get_attendance_staff_2_by_attendance_staff_new_id($attendance_staff_new_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select * from attendance_staff_2 where delete_flg=0 and attendance_staff_new_id='%s'",$attendance_staff_new_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(get_attendance_staff_2_by_attendance_staff_new_id)";
		exit();

	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();

}

function get_select_frm_back_plans_state($back_plans_state){

	$html = "";

	if( $back_plans_state == "" ){

		$back_plans_state = "-1";

	}

	$html .= '<select name="back_plans_state">';
	$html .= '<option value="-1">未選択</option>';
	if( $back_plans_state == "1" ){
		$html .= '<option value="1" selected>市ヶ谷</option>';
	}else{
		$html .= '<option value="1">市ヶ谷</option>';
	}
	if( $back_plans_state == "2" ){
		$html .= '<option value="2" selected>渋谷</option>';
	}else{
		$html .= '<option value="2">渋谷</option>';
	}
	$html .= '</select>';

	return $html;
	exit();

}

function get_select_frm_back_plans_hour($back_plans_hour,$back_plans_state){

	if( ($back_plans_hour == "-1") || ($back_plans_hour == "") ){

		$back_plans_hour = intval(date('H'));

		if( $back_plans_hour < 10 ){

			$back_plans_hour = $back_plans_hour + 24;

		}

	}

	if( $back_plans_state == "9" ){

		$back_plans_hour = "-1";

	}

	$html = "";

	$html .= '<select name="back_plans_hour">';
	$html .= '<option value="-1">未選択</option>';

	for( $i=17; $i<35; $i++ ){

		if( $back_plans_hour == $i ){
			$html .= sprintf('<option value="%s" selected>%s</option>',$i,$i);
		}else{
			$html .= sprintf('<option value="%s">%s</option>',$i,$i);
		}

	}


	$html .= '</select>';

	return $html;
	exit();

}

function get_select_frm_back_plans_minute($back_plans_minute,$back_plans_state){

	if( $back_plans_minute == "" ){

		$back_plans_minute = intval(date('i'));

	}

	if( $back_plans_state == "9" ){

		$back_plans_minute = "-1";

	}

	$html = "";

	$html .= '<select name="back_plans_minute">';
	$html .= '<option value="-1">未選択</option>';

	for( $i=0; $i<60; $i++ ){

		if( $back_plans_minute == $i ){
			$html .= sprintf('<option value="%s" selected>%s</option>',$i,$i);
		}else{
			$html .= sprintf('<option value="%s">%s</option>',$i,$i);
		}

	}

	$html .= '</select>';

	return $html;
	exit();

}

function update_back_plans($attendance_staff_new_id,$back_plans_state,$back_plans_hour,$back_plans_minute){

	include(INC_PATH."/db_connect.php");

	$result = check_exist_attendance_staff_2_by_attendance_staff_new_id($attendance_staff_new_id);

	if( $result == true ){

$sql = sprintf("
update attendance_staff_2 set
back_plans_state='%s',
back_plans_hour='%s',
back_plans_minute='%s'
where delete_flg=0 and attendance_staff_new_id='%s'",
$back_plans_state,$back_plans_hour,$back_plans_minute,$attendance_staff_new_id);

	}else{

$sql = sprintf("
insert into attendance_staff_2(attendance_staff_new_id,back_plans_state,back_plans_hour,back_plans_minute)
values('%s','%s','%s','%s')",
$attendance_staff_new_id,$back_plans_state,$back_plans_hour,$back_plans_minute);

	}

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(update_back_plans)";
		exit();

	}

	return true;
	exit();

}

function check_exist_attendance_staff_2_by_attendance_staff_new_id($attendance_staff_new_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("
select id from attendance_staff_2 where
delete_flg=0 and attendance_staff_new_id='%s'",
$attendance_staff_new_id);

	$res = mysql_query($sql, $con);

	if( $res == false ){

		echo "error!(check_exist_attendance_staff_2_by_attendance_staff_new_id)";
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

}

function get_select_frm_hour($value,$select_name){

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

function get_select_frm_minute($value,$select_name){

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

function update_day_report(
$attendance_staff_new_id,$car_distance,$highway,$parking,$pay_finish,$comment,
$start_hour,$start_minute,$end_hour,$end_minute){

	include(INC_PATH."/db_connect.php");

	$result = check_exist_attendance_staff_2_by_attendance_staff_new_id_common($attendance_staff_new_id);

	if( $result == true ){

		$sql = sprintf("
update attendance_staff_2 set
report_delete_flg='0',
car_distance_d='%s',
highway_d='%s',
parking_d='%s',
pay_finish_d='%s',
comment_d='%s',
start_hour_d='%s',
start_minute_d='%s',
end_hour_d='%s',
end_minute_d='%s'
where delete_flg='0' and attendance_staff_new_id='%s'",
$car_distance,$highway,$parking,$pay_finish,$comment,$start_hour,$start_minute,$end_hour,$end_minute,
$attendance_staff_new_id);

	}else{

		$sql = sprintf("
insert into attendance_staff_2(
car_distance_d,
highway_d,
parking_d,
pay_finish_d,
comment_d,
start_hour_d,
start_minute_d,
end_hour_d,
end_minute_d,
attendance_staff_new_id)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$car_distance,$highway,$parking,$pay_finish,$comment,$start_hour,$start_minute,$end_hour,$end_minute,
$attendance_staff_new_id);

	}

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(update_day_report)";
		exit();

	}

	return true;
	exit();

}

function send_mail_front_day_report(
$attendance_staff_new_id,$car_distance,$highway,$parking,$pay_finish,$comment,
$start_hour,$start_minute,$end_hour,$end_minute,$staff_id,$area){

	$check_url = get_check_url_driver_man($area,$staff_id);

	$data = get_staff_data_by_id_common($staff_id);

	$staff_name = $data["name"];

	$start_minute = add_zero_when_under_ten_common($start_minute);
	$end_minute = add_zero_when_under_ten_common($end_minute);
	$highway = number_format($highway);
	$parking = number_format($parking);
	$pay_finish = number_format($pay_finish);

	$data = get_attendance_staff_new_data_by_attendance_id_common($attendance_staff_new_id);

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$title = sprintf("[業務開始・締め処理の送信][%sさん]",$staff_name);

$content =<<<EOT

{$staff_name}さんから以下の業務開始・締め処理が送信されました。

{$year}年{$month}月{$day}日

開始：{$start_hour}時{$start_minute}分
終了：{$end_hour}時{$end_minute}分
距離： {$car_distance}km
高速代：{$highway} 円
駐車場代： {$parking}円
精算済み： {$pay_finish}円
特記事項：
{$comment}

{$check_url}

EOT;

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";
	//$mailto = "minamikawa@neo-gate.jp";

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	return true;
	exit();

}

function update_work_meter_start($meter,$attendance_staff_new_id){

	include(INC_PATH."/db_connect.php");

	$result = check_exist_attendance_staff_2_by_attendance_staff_new_id_common($attendance_staff_new_id);

	if( $result == true ){

		$sql = sprintf("
update attendance_staff_2 set
work_meter_start='%s'
where delete_flg='0' and attendance_staff_new_id='%s'",
$meter,$attendance_staff_new_id);

	}else{

		$sql = sprintf("
insert into attendance_staff_2(
work_meter_start,attendance_staff_new_id)
values('%s','%s')",
$meter,$attendance_staff_new_id);

	}

	$res = mysql_query($sql, $con);

	if($res == false){

		echo "error!(update_work_meter_start)";
		exit();

	}

	return true;
	exit();

}

function update_work_meter_end($attendance_staff_new_id,$work_meter_start,$work_meter_end){

	$tmp = get_now_hour_minute_unit_10_24_common();
	$now_hour = $tmp["hour"];
	$now_minute = $tmp["minute"];

	$car_distance = $work_meter_end - $work_meter_start;

	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	$sql = sprintf("
update attendance_staff_2 set
work_meter_end='%s'
where delete_flg='0' and attendance_staff_new_id='%s'",
$work_meter_end,$attendance_staff_new_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		echo "error!(update_work_meter_end)";
		exit();

	}

	$sql = sprintf("
update attendance_staff_new set
car_distance='%s',end_hour='%s',end_minute='%s'
where id='%s'",
$car_distance,$now_hour,$now_minute,$attendance_staff_new_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		echo "error!(update_work_meter_end)";
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

function get_message_board_2($limit_num,$area){

	include(INC_PATH."/db_connect.php");

	if(($area != "tokyo_reraku")&&($area != "tokyo_bigao")){
		$where_area = sprintf('((area="all") or (area="%s"))',$area);
	}
	else{
		$where_area = sprintf('(area="%s")',$area);
	}

	$sql = sprintf("
select * from message_board where
delete_flg=0 and %s
order by created desc limit 0,%s",
$where_area,$limit_num);

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_message_board_2)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$created = $row["created"];

		$month = date('m',$created);
		$day = date('d',$created);
		$hour = date('h',$created);
		$minute = date('i',$created);
		$week = date('w',$created);

		$week_name = get_week_name_common($week);

$day_disp =<<<EOT
{$month}/{$day}（{$week_name}）{$hour}：{$minute}
EOT;

		$list_data[$i] = $row;
		$list_data[$i]["day_disp"] = $day_disp;

		$i++;

	}

	return $list_data;
	exit();

}

?>
