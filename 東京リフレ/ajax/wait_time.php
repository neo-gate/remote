<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$type = $_POST["type"];

$hour = intval(date("H"));
$minute = intval(date("i"));

//本日出勤セラピストのデータ取得
$area = "tokyo";
$result = get_attendance_today_reservation_flg_common($area);

if( $result == true ){
	
	if( $type == "pc" ){
			
		echo "【只今の待ち時間】".$hour."：".$minute."更新　「移動時間だけですぐにご案内可能です」";
		exit();
			
	}else if( $type == "sp" ){
			
		echo "【只今の待ち時間】<br />&nbsp;".$hour."：".$minute."更新「移動時間だけですぐにご案内可能です」";
		exit();
			
	}
	
}

echo "";
exit();



?>