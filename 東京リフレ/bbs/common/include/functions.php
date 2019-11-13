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

//ログイン実行
function login_action_bbs_therapist($tel){

	include(INC_PATH."/db_connect.php");

	$area = "tokyo";

	$sql = sprintf("
select * from therapist_new where delete_flg=0 and leave_flg=0 and test_flg=0 and area<>'%s'",
$area);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "error!(login_action_bbs_therapist:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$match_flg = false;

	while($row = mysql_fetch_assoc($res)){

		$staff_tel = $row["tel"];

		$result = check_two_tel_match($tel,$staff_tel);

		if( $result == true ){

			$staff_name = $row["name"];
			$staff_id = $row["id"];
			$staff_type = $row["type"];
			$staff_area = $row["area"];
			$boss_flg = $row["boss_flg"];

			$match_flg = true;

		}

	}

	if( $match_flg == false ){

		return false;
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$_SESSION[SESSION_TICKET] = md5(TICKET_WORD.$staff_name);

	$_SESSION["bbs_staff_name"] = $staff_name;
	$_SESSION["bbs_staff_id"] = $staff_id;
	$_SESSION["bbs_staff_type"] = $staff_type;
	$_SESSION["bbs_staff_area"] = $staff_area;
	$_SESSION["bbs_boss_flg"] = $boss_flg;
	$_SESSION["bbs_therapist_flg"] = true;

	//セラピスト用は末尾に「x」追加
	$login_cookie_bbs = md5(time()."-".$staff_id."x");

	//クッキーの値を保存
	$sql = sprintf("
update therapist_new set login_cookie_bbs='%s' where id='%s'",
$login_cookie_bbs,$staff_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "error!(login_action_bbs_therapist:2)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}else{

		//クッキー(有効期限1年)
		setcookie("login_cookie_bbs", $login_cookie_bbs, time()+(60*60*24*30*12));

	}

	return true;
	exit();

}

//ログイン実行
function login_action_bbs_staff($tel){

	include(INC_PATH."/db_connect.php");

	$sql = "select * from staff_new_new where delete_flg=0 and leave_flg=0";

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "error!(login_action_bbs_staff:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$match_flg = false;

	while($row = mysql_fetch_assoc($res)){

		$staff_tel = $row["tel"];

		$result = check_two_tel_match($tel,$staff_tel);

		if( $result == true ){

			$staff_name = $row["name"];
			$staff_id = $row["id"];
			$staff_type = $row["type"];
			$staff_area = $row["area"];
			$boss_flg = $row["boss_flg"];

			$match_flg = true;

		}

	}

	if( $match_flg == false ){

		return false;
		exit();

	}

	$row = mysql_fetch_assoc($res);

	$_SESSION[SESSION_TICKET] = md5(TICKET_WORD.$staff_name);

	$_SESSION["bbs_staff_name"] = $staff_name;
	$_SESSION["bbs_staff_id"] = $staff_id;
	$_SESSION["bbs_staff_type"] = $staff_type;
	$_SESSION["bbs_staff_area"] = $staff_area;
	$_SESSION["bbs_boss_flg"] = $boss_flg;

	$login_cookie_bbs = md5(time()."-".$staff_id);

	//クッキーの値を保存
	$sql = sprintf("
update staff_new_new set login_cookie_bbs='%s' where id='%s'",
$login_cookie_bbs,$staff_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "error!(login_action_bbs_staff:2)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}else{

		//クッキー(有効期限1年)
		setcookie("login_cookie_bbs", $login_cookie_bbs, time()+(60*60*24*30*12));

	}

	return true;
	exit();

}

//ログイン実行
function login_action_bbs($tel){

	$result1 = login_action_bbs_staff($tel);

	if( $result1 == false ){

		$result2 = login_action_bbs_therapist($tel);

	}

	if( ($result1==false) && ($result2==false) ){

		return false;
		exit();

	}else{

		return true;
		exit();

	}

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
function check_auto_login_bbs_therapist(){

	include(INC_PATH."/db_connect.php");

	if( isset($_COOKIE["login_cookie_bbs"]) == true ){

		$login_cookie_bbs = $_COOKIE["login_cookie_bbs"];

		$sql = sprintf("
select * from therapist_new where delete_flg=0 and login_cookie_bbs='%s'",
$login_cookie_bbs);

		$res = mysql_query($sql, $con);

		if($res == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_auto_login_bbs_therapist)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$data_num = mysql_num_rows($res);

		if( $data_num == 1 ){

			$row = mysql_fetch_array($res);

			$_SESSION[SESSION_TICKET] = md5(TICKET_WORD.$row["name"]);

			$staff_name = $row["name"];
			$staff_id = $row["id"];
			$staff_type = $row["type"];
			$staff_area = $row["area"];
			$boss_flg = $row["boss_flg"];

			$_SESSION["bbs_staff_name"] = $staff_name;
			$_SESSION["bbs_staff_id"] = $staff_id;
			$_SESSION["bbs_staff_type"] = $staff_type;
			$_SESSION["bbs_staff_area"] = $staff_area;
			$_SESSION["bbs_boss_flg"] = $boss_flg;
			$_SESSION["bbs_therapist_flg"] = true;

			return true;
			exit();

		}else{

			return false;
			exit();

		}

	}else{

		return false;
		exit();

	}

}

//オートログインをチェック
function check_auto_login_bbs_staff(){

	include(INC_PATH."/db_connect.php");

	if( isset($_COOKIE["login_cookie_bbs"]) == true ){

		$login_cookie_bbs = $_COOKIE["login_cookie_bbs"];

		$sql = sprintf("
select * from staff_new_new where delete_flg=0 and login_cookie_bbs='%s'",
$login_cookie_bbs);

		$res = mysql_query($sql, $con);

		if($res == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_auto_login_bbs_staff)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$data_num = mysql_num_rows($res);

		if( $data_num == 1 ){

			$row = mysql_fetch_array($res);

			$_SESSION[SESSION_TICKET] = md5(TICKET_WORD.$row["name"]);

			$staff_name = $row["name"];
			$staff_id = $row["id"];
			$staff_type = $row["type"];
			$staff_area = $row["area"];
			$boss_flg = $row["boss_flg"];

			$_SESSION["bbs_staff_name"] = $staff_name;
			$_SESSION["bbs_staff_id"] = $staff_id;
			$_SESSION["bbs_staff_type"] = $staff_type;
			$_SESSION["bbs_staff_area"] = $staff_area;
			$_SESSION["bbs_boss_flg"] = $boss_flg;

			return true;
			exit();

		}else{

			return false;
			exit();

		}

	}else{

		return false;
		exit();

	}

}

//オートログインをチェック
function check_auto_login_bbs(){

	$result1 = check_auto_login_bbs_staff();

	if( $result1 == false ){

		$result2 = check_auto_login_bbs_therapist();

	}

	if( ($result1 == false) && ($result2 == false) ){

		return false;
		exit();

	}else{

		return true;
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

	$result = mb_send_mail($mailto,$title,$content,$header);

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

	$result = mb_send_mail($mailto,$title,$content,$header);

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

	$header = getallheaders();
	$agent = $header["User-Agent"] ;
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

function get_shift_message_by_order_num_bbs($order_num,$area){

	include(INC_PATH."/db_connect.php");

	if( $area == "all" ){

		$sql = sprintf("
select *
from shift_message
where
delete_flg=0 and
bbs_not_disp_flg=0
order by created desc
limit 0,%s",
$order_num);

}elseif( $area == "yokohama" ){

	$area = '%'.$area.'%';

	$sql = sprintf("
select *
from shift_message
where
delete_flg=0 and
bbs_not_disp_flg=0 and
area like '%s'
order by created desc
limit 0,%s",
$area,$order_num);

	}else{

	$sql = sprintf("
select *
from shift_message
where
delete_flg=0 and
bbs_not_disp_flg=0 and
area='%s'
order by created desc
limit 0,%s",
$area,$order_num);

	}

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_shift_message_by_order_num_bbs)";
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
		$week_name = get_week_name_common($week);

		if( $minute < 10){

			$minute = "0".$minute;

		}

		$time_disp = sprintf("%s/%s（%s）%s：%s",$month,$day,$week_name,$hour,$minute);

		$list_data[$i]["time_disp"] = $time_disp;

		$therapist_id = $row["therapist_id"];
		$area = $row["area"];
		$from_staff_id = $row["from_staff_id"];
		$to_staff_id = $row["to_staff_id"];
		$message_type = $row["message_type"];
		$type = $row["type"];

		$area_name = get_area_name_by_area_common($area);

		if( $therapist_id != "-1" ){
			$therapist_name = get_therapist_name_by_therapist_id_honmyou_common($therapist_id);
		}else{
			$therapist_name = "";
		}

		if( $from_staff_id != "-1" ){
			$from_staff_name = get_staff_name_by_id_common($from_staff_id);
			$from_staff_boss_flg = get_staff_boss_flg_by_id_common($from_staff_id);
		}else{
			$from_staff_name = "";
		}

		if( $to_staff_id != "-1" ){
			$to_staff_name = get_staff_name_by_id_common($to_staff_id);
			$to_staff_boss_flg = get_staff_boss_flg_by_id_common($to_staff_id);
			$to_staff_type = get_staff_type_by_id_common($to_staff_id);
		}else{
			$to_staff_name = "";
		}

		if( $area == "all" ){
			$message_to_type = "all";
		}else if( $to_staff_name == "" ){
			$message_to_type = "therapist";
		}else{
			$message_to_type = "staff";
		}

		$from_to_disp = "";

		if( ($therapist_id=="-1") && ($to_staff_id=="-1") ){
			$message_to_type = "staff_new_message";
		}

		if( ($message_type=="mail_start") || ($message_type=="mail_end") || ($message_type=="mail_change") ){

			$from_to_disp = sprintf("【%s】セラピスト%s",$area_name,$therapist_name);

		}else{

			if( ($from_staff_id=="-1") && ($to_staff_id=="-1") ){
				$from_to_disp = $therapist_name;
			}else if( $message_to_type == "staff_new_message" ){
				if( $type == "1" ){
					$from_to_disp = "本部";
				}else{
					if($from_staff_boss_flg=="1"){
						$from_to_disp = sprintf("%s",$from_staff_name);
					}else{
						$from_to_disp = sprintf("【%s】ドライバー%s",$area_name,$from_staff_name);
					}
				}
			}else if( $area == "all" ){
				if( $type == "1" ){
					$from_to_disp = "本部";
				}else{
					if($from_staff_boss_flg=="1"){
						$from_to_disp = sprintf("%s",$from_staff_name);
					}else{
						$from_to_disp = sprintf("【%s】ドライバー%s",$area_name,$from_staff_name);
					}
				}
			}else if( $to_staff_name == "" ){
				if( $type == "1" ){
					$from_to_disp = sprintf("本部　→　【%s】セラピスト%s",$area_name,$therapist_name);
				}else if($from_staff_boss_flg=="1"){
					$from_to_disp = sprintf("%s　→　【%s】セラピスト%s",$from_staff_name,$area_name,$therapist_name);
				}else{
					$from_to_disp = sprintf("ドライバー%s　→　【%s】セラピスト%s",$from_staff_name,$area_name,$therapist_name);
				}
			}else{
				if( $to_staff_type == "honbu" ){
					if($from_staff_boss_flg=="1"){
						$from_to_disp = sprintf("%s　→　本部",$from_staff_name);
					}else{
						$from_to_disp = sprintf("【%s】ドライバー%s　→　本部",$area_name,$from_staff_name);
					}
				}else{
					if( $type == "1" ){
						if( $to_staff_boss_flg == "1" ){
							$from_to_disp = sprintf("本部　→　%s",$to_staff_name);
						}else{
							$from_to_disp = sprintf("本部　→　【%s】ドライバー%s",$area_name,$to_staff_name);
						}
					}else if($from_staff_boss_flg=="1"){
						$from_to_disp = sprintf("%s　→　【%s】ドライバー%s",$from_staff_name,$area_name,$to_staff_name);
					}else{
						if( $to_staff_boss_flg == "1" ){
							$from_to_disp = sprintf("【%s】ドライバー%s　→　%s",$area_name,$from_staff_name,$to_staff_name);
						}else{
							$from_to_disp = sprintf("【%s】ドライバー%s　→　【%s】ドライバー%s",$area_name,$from_staff_name,$area_name,$to_staff_name);
						}
					}
				}
			}

		}

		$list_data[$i]["area_name"] = $area_name;
		$list_data[$i]["therapist_name"] = $therapist_name;
		$list_data[$i]["from_staff_name"] = $from_staff_name;
		$list_data[$i]["to_staff_name"] = $to_staff_name;
		$list_data[$i]["message_to_type"] = $message_to_type;
		$list_data[$i]["from_to_disp"] = $from_to_disp;

		$i++;

	}

	return $list_data;
	exit();

}

function insert_shift_message_regist_bbs($staff_id,$staff_type,$area,$naiyou){

	$message_type = "new";

	$mail_title = get_mail_title_bbs($staff_id,$staff_type,$area,$message_type);

	$send_user_name = get_send_user_name_for_mail($staff_id,$staff_type);

	$to_staff_id = "-1";
	$to_therapist_id = "-1";

	$bcc_add = get_bcc_add_bbs($staff_id,$staff_type,$area,$message_type,$to_staff_id,$to_therapist_id);

	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	if( $staff_type == "honbu" ){

		$type = "1";

	}else{

		$type = "11";

	}

	$therapist_id = "-1";

	$now = time();

	$sql = sprintf("
insert into shift_message(created,therapist_id,type,content,area,from_staff_id)
values('%s','%s','%s','%s','%s','%s')",
$now,$therapist_id,$type,$naiyou,$area,$staff_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(insert_shift_message_regist_bbs:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}



	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";

	$title = "[業務連絡BBS]投稿のお知らせ";

	$url = WWW_URL;

	$content =<<<EOT
新しいメッセージがあります。確認してください。

{$url}

EOT;

	$header = "From: info@neo-gate.jp\n";
	//$header .= "Bcc: minamikawa@neo-gate.jp";

	if( $bcc_add != "" ){

		$header .= ",";
		$header .= $bcc_add;

	}

	$parameter="-f info@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,$parameter);

	if($result==false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(insert_shift_message_regist_bbs:2)";
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

function insert_shift_message_response_bbs($staff_id,$staff_type,$area,$naiyou,$to_staff_id,$to_therapist_id){

	if( $to_therapist_id != "-1" ){

		$now = time();
		$now_hour = intval(date('H', $now));
		$now_minute = intval(date('i', $now));
		$now_hour_mail = hour_plus_24_common($now_hour);
		$now_minute_mail = minute_zero_add_common($now_minute);

		$check_url = get_check_url_shift_front_common($area,$to_therapist_id);
		$shop_area_name = get_area_name_by_area_common($area);
		$therapist_name = get_therapist_name_by_therapist_id_honmyou_common($to_therapist_id);

	}

	$bbs_common_url = get_bbs_common_url_common();

	$message_type = "res";

	$mail_title = get_mail_title_bbs($staff_id,$staff_type,$area,$message_type);

	$send_user_name = get_send_user_name_for_mail($staff_id,$staff_type);

	$receive_user_name = get_receive_user_name_for_mail($to_staff_id,$to_therapist_id);

	//$bcc_add = get_bcc_add_bbs($staff_id,$staff_type,$area,$message_type,$to_staff_id,$to_therapist_id);

	$area_boss_mail = get_area_boss_mail($area);

	$receive_user_mail = get_receive_user_mail_common($to_staff_id,$to_therapist_id);

	include(INC_PATH."/db_connect.php");

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, $con );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, $con );

	if( $staff_type == "honbu" ){

		$type = "1";

	}else{

		$type = "11";

	}

	if( $to_therapist_id == "" ){

		$to_therapist_id = "-1";

	}

	if( $to_staff_id == "" ){

		$to_staff_id = "-1";

	}

	if( $to_staff_id != "-1" ){

		$to_therapist_id = "-1";

	}

	$now = time();

	$sql = sprintf("
insert into shift_message(created,therapist_id,type,content,area,from_staff_id,to_staff_id)
values('%s','%s','%s','%s','%s','%s','%s')",
$now,$to_therapist_id,$type,$naiyou,$area,$staff_id,$to_staff_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(insert_shift_message_response_bbs:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	//メール送信
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@neo-gate.jp";

	if( $receive_user_mail != "" ){

		if( $to_therapist_id != "-1" ){

			$send_user_name = str_replace("さん", "", $send_user_name);
			$title = sprintf("施術連絡：%s：%s　%s：%s",
			$shop_area_name,$therapist_name,$now_hour_mail,$now_minute_mail);

$content =<<<EOT
{$send_user_name}　→　{$receive_user_name}

{$naiyou}

{$check_url}

EOT;

		}else{

			$title = "[業務連絡BBS]投稿のお知らせ";

$content =<<<EOT
新しいメッセージがあります。確認してください。

{$bbs_common_url}

EOT;

		}

		$header = "From: info@neo-gate.jp\n";
		//$header .= "Bcc: minamikawa@neo-gate.jp";

		$header .= ",";
		$header .= $receive_user_mail;

		$parameter="-f info@neo-gate.jp";

		$result = mb_send_mail($mailto,$title,$content,$header,$parameter);

		if($result==false){

			//二回メールを送るので、トランザクションの対象とはしない

		}

	}

	if( $area_boss_mail != "" ){

		//あらためてメール送信

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

	//コミット
	$sql = "commit";
	mysql_query( $sql, $con );

	//MySQL切断
	mysql_close( $con );

	return true;
	exit();

}

function get_today_work_driver_mail_str($area){

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

	$sql = sprintf("
select staff_new_new.mail from attendance_staff_new
left join staff_new_new on staff_new_new.id=attendance_staff_new.staff_id
where
attendance_staff_new.area='%s' and
attendance_staff_new.year='%s' and
attendance_staff_new.month='%s' and
attendance_staff_new.day='%s' and
staff_new_new.boss_flg=0",
$area,$year,$month,$day);

	//echo $sql;echo "<br />";

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_today_work_driver_mail_str)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$mail_string = "";
	while($row = mysql_fetch_assoc($res)){

		$mail = $row["mail"];

		if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)) {

			if( $i > 0 ){

				$mail_string .= ",".$mail;

			}else{

				$mail_string .= $mail;

			}

			$i++;

		}

	}

	return $mail_string;
	exit();

}

function get_area_boss_mail($area){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("select mail from staff_new_new where area='%s' and boss_flg=1",$area);

	//echo $sql;echo "<br />";

	$res = mysql_query($sql, $con);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_area_boss_mail)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row["mail"];
	exit();

}

function get_mail_title_bbs($staff_id,$staff_type,$area,$message_type){

	$area_name = get_area_name_by_area_common($area);

	$now = time();
	$now_hour = intval(date('H', $now));
	$now_minute = intval(date('i', $now));
	$now_hour_mail = hour_plus_24_common($now_hour);
	$now_minute_mail = minute_zero_add_common($now_minute);

	if( $staff_type == "honbu" ){

		$send_user_name = "本部";

	}else{

		$send_user_name = get_staff_name_by_id_common($staff_id);

	}

	if( $message_type == "res" ){

		$message_type_name = "返信";

	}else if( $message_type == "new" ){

		$message_type_name = "新規";

	}else{

		$message_type_name = "不明";

	}

	$title = sprintf("
%sメッセージ：%s：%s　%s：%s",
$message_type_name,$area_name,$send_user_name,$now_hour_mail,$now_minute_mail);

	return $title;
	exit();

}

function get_send_user_name_for_mail($staff_id,$staff_type){

	if( $staff_type == "honbu" ){

		$send_user_name = "本部";

	}else{

		$send_user_name = get_staff_name_by_id_common($staff_id);
		$send_user_name = $send_user_name."さん";

	}

	return $send_user_name;
	exit();

}

function get_bcc_add_bbs($staff_id,$staff_type,$area,$message_type,$to_staff_id,$to_therapist_id){

	$bcc_add = "";

	$staff_boss_flg = get_staff_boss_flg_by_id_common($staff_id);
	$area_boss_mail = get_area_boss_mail($area);
	$driver_mail_str = get_today_work_driver_mail_str($area);

	if( $message_type == "new" ){

		if( $staff_type == "honbu" ){

			//本部の場合は、店長、ドライバー

			if( $area_boss_mail != "" ){

				$bcc_add = $area_boss_mail;

			}

			if( $driver_mail_str != "" ){

				if( $bcc_add == "" ){

					$bcc_add = $driver_mail_str;

				}else{

					$bcc_add = $bcc_add.",".$driver_mail_str;

				}

			}

		}else if( $staff_boss_flg == "1" ){

			//店長の場合は、店長、ドライバー

			if( $area_boss_mail != "" ){

				$bcc_add = $area_boss_mail;

			}

			if( $driver_mail_str != "" ){

				if( $bcc_add == "" ){

					$bcc_add = $driver_mail_str;

				}else{

					$bcc_add = $bcc_add.",".$driver_mail_str;

				}

			}

		}else{

			//ドライバーの場合は、店長

			if( $area_boss_mail != "" ){

				$bcc_add = $area_boss_mail;

			}

		}

	}else if( $message_type == "res" ){

		//エリア店長、返信先スタッフまたは返信先セラピスト

		if( $area_boss_mail != "" ){

			$bcc_add = $area_boss_mail;

		}

		$to_staff_type = "";

		if( $to_staff_id != "-1" ){

			$to_staff_type = get_staff_type_by_id_common($to_staff_id);

		}

		//本部でない場合
		if( $to_staff_type != "honbu" ){

			$res_mail = get_res_mail_staff_or_therapist($to_staff_id,$to_therapist_id);

			//店長があて先ではない場合
			if( $res_mail != $area_boss_mail ){

				if( $res_mail != "" ){

					if( $bcc_add == "" ){

						$bcc_add = $res_mail;

					}else{

						$bcc_add = $bcc_add.",".$res_mail;

					}

				}

			}

		}

	}

	//echo $bcc_add;exit();

	return $bcc_add;
	exit();

}

//本日出勤でないドライバーの場合
function check_today_attendance_data_driver($staff_id,$staff_type,$boss_flg,$staff_area){

	if( ( $staff_type == "honbu" ) || ( $boss_flg == "1" ) ){

		return true;
		exit();

	}

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

/*
$sql = sprintf("
select id from attendance_staff_new
where
area='%s' and
year='%s' and
month='%s' and
day='%s' and
staff_id='%s'",
$staff_area,$year,$month,$day,$staff_id);
*/

$sql = sprintf("
select id,area from attendance_staff_new
where
year='%s' and
month='%s' and
day='%s' and
staff_id='%s'",
$year,$month,$day,$staff_id);

	//echo $sql;exit();

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(check_today_attendance_data_driver)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){

		return false;
		exit();

	}else{

		//スタッフのエリアを、出勤データのエリアに、上書き
		$area = $row["area"];
		$_SESSION["bbs_staff_area"] = $area;

		return true;
		exit();

	}

}

function get_res_mail_staff_or_therapist($staff_id,$therapist_id){

	if( $staff_id != "-1" ){

		$mail = get_staff_mail_common($staff_id);

	}else if( $therapist_id != "-1" ){

		$mail = get_therapist_mail_common($therapist_id);

	}

	$result = mail_keishiki_check_common($mail);

	if( $result == false ){

		$mail = "";

	}

	return $mail;
	exit();

}

function get_receive_user_name_for_mail($staff_id,$therapist_id){

	$name = "";

	if( $staff_id != "-1" ){

		$staff_type = get_staff_type_by_id_common($staff_id);

		if( $staff_type == "honbu" ){

			$name = "本部";

		}else{

			$name = get_staff_name_by_id_common($staff_id);

		}

	}else if( $therapist_id != "-1" ){

		$name = get_therapist_name_by_therapist_id_honmyou_common($therapist_id);

	}

	return $name;
	exit();

}

function check_two_tel_match($tel_1,$tel_2){

	$tel_1 = str_replace("-","",$tel_1);
	$tel_2 = str_replace("-","",$tel_2);
	$tel_1 = trim($tel_1);
	$tel_2 = trim($tel_2);

	if( $tel_1 == $tel_2 ){

		return true;
		exit();

	}else{

		return false;
		exit();

	}

}

function logout_action(){

	reset_login_session();

	header("Location: ".WWW_URL."login.php");
	exit();

}

function reset_login_session(){

	setcookie(session_name(), "", 0);
	session_destroy();
	setcookie("login_cookie_bbs", '', time() - 60);

	return true;
	exit();

}

function delete_shift_message_by_id($message_id){

	include(INC_PATH."/db_connect.php");

	$sql = sprintf("update shift_message set delete_flg='1' where id='%s'",
$message_id);

	$res = mysql_query($sql, $con);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(delete_shift_message_by_id)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	return true;
	exit();

}














?>
