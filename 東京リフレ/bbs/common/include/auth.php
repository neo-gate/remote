<?php

// ログイン状態かどうかのチェック
if ($_SESSION[SESSION_TICKET] != md5(TICKET_WORD.$_SESSION["name"])) {
	
	$result = false;
	
	$result = check_auto_login_bbs();
	
	if( $result == false ){
		
		//echo "auto_login_failure!";exit();
	
		// セッションクッキーを無効化
		setcookie(session_name(), "", 0);
		
		// セッションを無効化
		session_destroy();
		
		//オートログイン用クッキーの削除
		setcookie("login_cookie_bbs", '', time() - 60);
		
		// ログインページにリダイレクト
		header("Location: ".WWW_URL."login.php");
		exit();
	
	}else{
		
		//echo "auto_login_success!";exit();
		
	}
	
}

$staff_name = $_SESSION["bbs_staff_name"];
$staff_id = $_SESSION["bbs_staff_id"];
$staff_type = $_SESSION["bbs_staff_type"];
$staff_area = $_SESSION["bbs_staff_area"];
$boss_flg = $_SESSION["bbs_boss_flg"];
$therapist_flg = $_SESSION["bbs_therapist_flg"];

if( $staff_type == "honbu" ){

	$page_area = "all";

}else{
	
	$page_area = $staff_area;
	
}

//echo $page_area;echo "<br />";

$today_attendance_flg = true;

if( $therapist_flg != true ){

	//本日出勤でないドライバーの場合
	$today_attendance_flg = check_today_attendance_data_driver($staff_id,$staff_type,$boss_flg,$staff_area);
	
	if( $staff_type != "honbu" ){
		
		//スタッフのエリアを、出勤データのエリアに、上書き
		//check_today_attendance_data_driver関数内でセッション変数上書き
		$page_area = $_SESSION["bbs_staff_area"];
	
	}

}

if( ($page_area == "") || ($today_attendance_flg == false) ){

	header("Location: ".WWW_URL."logout.php");
	exit();

}

if( $staff_id == "122" ){

	echo $page_area;echo "<br />";

}


?>