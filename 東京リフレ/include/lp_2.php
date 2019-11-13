<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
require_once(ROOT_PATH."include/common.php");

$error = "";

$area = "tokyo";

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
exit();
*/

if( $_SESSION["lp_mail_complete_flg"] == true ){
	
	$error = '<div style="color:blue;margin:20px;">予約を送信しました</div>';
	
	unset($_SESSION["lp_mail_complete_flg"]);
	
}

if( isset($_POST["send"]) == true ){
	
	$time = $_POST["time"];
	$course = $_POST["course"];
	$onamae = $_POST["onamae"];
	$mail = $_POST["mail"];
	$mail_confirm = $_POST["mail_confirm"];
	$address = $_POST["address"];
	$tel = $_POST["tel"];
	$renraku = $_POST["renraku"];
	$reservation_day = $_POST["reservation_day"];
	
	$pieces = explode("_", $reservation_day);
	
	$year = $pieces[0];
	$month = $pieces[1];
	$day = $pieces[2];
	
	$error = "";

	if($time=="-99"){
		$error .= "<li>"."ご利用開始時間が未選択です"."</li>";
	}

	if($course=="-1"){
		$error .= "<li>"."ご利用予定コースが未選択です"."</li>";
	}

	if($onamae==""){
		$error .= "<li>"."お名前が未入力です"."</li>";
	}

	if($mail==""){
		$error .= "<li>"."メールアドレスが未入力です"."</li>";
	}else if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
		$error .= "<li>メールアドレスの形式が正しくありません</li>";
	}else{
	
		if( $access_type == "pc" ){
			
			if( $mail != $mail_confirm ){
				
				$error .= "<li>メールアドレス(確認)と一致していません</li>";
				
			}
			
		}
		
	}

	if($address==""){
		$error .= "<li>"."住所が未入力です"."</li>";
	}

	if($tel==""){
		$error .= "<li>"."電話番号が未入力です"."</li>";
	}else if(!preg_match("/^[0-9]+$/", $tel)){
		$error .= "<li>"."電話番号の入力値が不正です"."</li>";
	}


	if($error != ""){
		
		$error = '<ul style="padding:10px;">'.$error.'</ul>';
		
	}else{
		
send_reservation_mail_front_2_common(
$time,$course,$onamae,$mail,$address,$tel,$renraku,$reservation_day);
		
		$_SESSION["lp_mail_complete_flg"] = true;
		
		header('Location: index.php');
		exit();

	}

}else if( ($_GET["year"] != "") && ($_GET["month"] != "") && ($_GET["day"] != "") ){
	
	$year = $_GET["year"];
	$month = $_GET["month"];
	$day = $_GET["day"];
	
	$reservation_day = sprintf("%s_%s_%s",$year,$month,$day);
	
}else{
	
	$data = get_today_year_month_day_common();
	
	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];
	
}

$day_select_frm = get_day_select_frm_for_reservation_mail_frm_common($reservation_day);

$time_select_option = get_time_array_select_option_vip_common($time);
//$time_select_option = get_time_array_select_option_vip_2_common($time);

$select_course = get_select_course_common($course);

?>