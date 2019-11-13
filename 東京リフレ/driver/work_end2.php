<?php
include("include/common.php");
$w_time =strtotime("25/Oct/2018:18:10:14 +0000");
echo date("H:i:s", $w_time) . "<br />";
echo intval(date('H', $w_time)) . " " . intval(date('i', $w_time));
$now_hour = hour_plus_24_common(3);
$data = Xget_hour_minute_unit_10_common($now_hour,10);
print_r($data);
exit;

//----n時前は24時を加算
function XPHP_hour_change_over_24_common($hour, $u_limit) {
	if( $hour < $u_limit ) $hour += 24;
	return $hour;
}

//----10時前は24時を加算
function Xhour_plus_24_common($hour){
	return XPHP_hour_change_over_24_common($hour, 10);	//n時前は24時を加算
}

function Xget_now_hour_minute_unit_10_24_common(){

	$now = time();
	$now_hour = intval(date('H', $now));
	$now_minute = intval(date('i', $now));
	$now_hour = hour_plus_24_common($now_hour);
	$data = Xget_hour_minute_unit_10_common($now_hour,$now_minute);

	return $data;
	exit();
}

function Xget_hour_minute_unit_10_common($hour,$minute){

	if( $minute > 50 ){
		$hour = $hour + 1;
		$minute = 0;
	}else if( $minute > 40 ){
		$minute = 50;
	}else if( $minute > 30 ){
		$minute = 40;
	}else if( $minute > 20 ){
		$minute = 30;
	}else if( $minute > 10 ){
		$minute = 20;
	}else if( $minute > 0 ){
		$minute = 10;
	}

	$data["hour"] = $hour;
	$data["minute"] = $minute;

	return $data;
	exit();
}

function Xupdate_work_meter_end($attendance_staff_new_id,$work_meter_start,$work_meter_end){
	
	$tmp = Xget_now_hour_minute_unit_10_24_common();
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

	$sql = sprintf("update attendance_staff_2 set work_meter_end='%s' where delete_flg='0' and attendance_staff_new_id='%s'",$work_meter_end,$attendance_staff_new_id);
	$res = mysql_query($sql, $con);
	if($res == false){
		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, $con );
			
		echo "error!(update_work_meter_end)";
		exit();
	}
	
	$sql = sprintf("update attendance_staff_new set car_distance='%s',end_hour='%s',end_minute='%s' where id='%s'",$car_distance,$now_hour,$now_minute,$attendance_staff_new_id);
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


$ch = $_GET["ch"];
$staff_id = $_GET["id"];
$area = $_GET["area"];

$result = staff_shift_page_access_check($staff_id,$ch);
if( $result == false ){
	echo "error!";
	exit();
}

$error = "";

$work_start_flg = true;

if( isset($_POST["send"]) == true ){
	
	/*
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	exit();
	*/
	
	$data = get_today_year_month_day_common();
	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];
	
	$attendance_staff_new_id = $_POST["attendance_staff_new_id"];
	$meter = $_POST["meter"];
	$work_meter_start = $_POST["work_meter_start"];
	
	$temp_pic_url = $_FILES["pic"]["tmp_name"];
	$file_name = $_FILES["pic"]["name"];
	
	if( $file_name == "" ){
		
		$error = '<span style="color:red;">写真の添付がありません</span>';
		
	}else{
		
		if( $meter == "" ){
			
			$error = '<span style="color:red;">開始時メーターが未入力です</span>';
			
		}else{
			
			$result = check_hankaku_num_value_common($meter);
			
			if( $result == false ){
				
				$error = '<span style="color:red;">開始時メーターは半角数字のみです</span>';
				
			}else{
				
				if( $work_meter_start > $meter ){
					
					$error = '<span style="color:red;">終了メーターの数値が不正です</span>';
					
				}
				
			}
			
		}
		
	}
	
	if( $error == "" ){
		
		$staff_name = get_staff_name_by_id_common($staff_id);
		
		$work_meter_end = $meter;
		
		Xupdate_work_meter_end($attendance_staff_new_id,$work_meter_start,$work_meter_end);
	
		if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {
			
			$dir_copy = 'C:/tmp/files/';
		
		}else{
			
			$dir_copy = '/tmp/files/';
			
		}
		
		$file_name_copy = date("Ymd-His") . $file_name;
		
		$upload_ok_flg = false;
		
		if ( move_uploaded_file( $temp_pic_url, $dir_copy . $file_name_copy ) ) {
			
			//chmod("files/" . date("Ymd-His") . $_FILES["upfile"]["name"], 0644);
			
			$upload_ok_flg = true;
			
		}
		
		if( $upload_ok_flg == true ){
		
			/*----------------------------------------------------------
			 添付ファイル付きメールをmb_send_mail()関数で送信する
			----------------------------------------------------------*/
			// 宛て先アドレス
			//$mailTo      = 'minamikawa@neo-gate.jp';
			$mailTo      = 'info@neo-gate.jp';
			
			// メールのタイトル
			$mailSubject = sprintf('ドライバー終了時メーター　%s月%s日　%s',$month,$day,$staff_name);
			
			// メール本文
			$mailMessage = '';
			
			// 添付するファイル
			/*
			$dir = './path/';
			$file = 'sample.jpg';
			$fileName    = $dir.$file;
			*/
			
			/*
			$dir = 'C:/tmp/';
			$file = '4931004_0.jpg';
			$fileName    = $dir.$file;
			*/
			
			$dir = $dir_copy;
			$file = $file_name_copy;
			$fileName    = $dir.$file;
			
			// 差出人のメールアドレス
			$mailFrom    = 'info@neo-gate.jp';
			
			// Return-Pathに指定するメールアドレス
			$returnMail  = 'info@neo-gate.jp';
			
			// メールで日本語使用するための設定をします。
			mb_language("Ja") ;
			mb_internal_encoding("UTF-8");
			
			$header  = "From: $mailFrom\r\n";
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-Type: multipart/mixed; boundary=\"__PHPRECIPE__\"\r\n";
			$header .= "\r\n";
			
			$body  = "--__PHPRECIPE__\r\n";
			$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\r\n";
			$body .= "\r\n";
			$body .= $mailMessage . "\r\n";
			$body .= "--__PHPRECIPE__\r\n";
			
			// 添付ファイルへの処理をします。
			$handle = fopen($fileName, 'r');
			$attachFile = fread($handle, filesize($fileName));
			fclose($handle);
			$attachEncode = base64_encode($attachFile);
			
			$body .= "Content-Type: image/jpeg; name=\"$file\"\r\n";
			$body .= "Content-Transfer-Encoding: base64\r\n";
			$body .= "Content-Disposition: attachment; filename=\"$file\"\r\n";
			$body .= "\r\n";
			$body .= chunk_split($attachEncode) . "\r\n";
			$body .= "--__PHPRECIPE__--\r\n";
			
			// メールの送信と結果の判定をします。セーフモードがOnの場合は第5引数が使えません。
			if (ini_get('safe_mode')) {
				$result = mb_send_mail($mailTo, $mailSubject, $body, $header);
			} else {
				$result = mb_send_mail($mailTo, $mailSubject, $body, $header,'-f' . $returnMail);
			}
			
			unlink($fileName);
			
			if($result){
				
				//リダイレクト
				$url = sprintf("work_start_complete.php?area=%s&id=%s&ch=%s",$area,$staff_id,$ch);
				
				header("Location: ".$url);
				exit();
				
			}else{
				
				$error = '<span style="color:red;">処理が失敗しました</span>';
				
			}
			
		
		}
	
	}
	
}else if( isset($_POST["send_back"]) == true ){
	
	$url = sprintf("index.php?area=%s&id=%s&ch=%s",$area,$staff_id,$ch);
	
	header("Location: ".$url);
	exit();
	
}else{
	
	$data = get_today_year_month_day_common();
	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];
	$attendance_data = get_attendance_staff_new_day_and_staff_id_common($year,$month,$day,$staff_id);
	$attendance_staff_new_id = $attendance_data["id"];
	
	if( $attendance_staff_new_id != "" ){
		
		$data = get_attendance_staff_2_by_attendance_staff_new_id_common($attendance_staff_new_id);
		
		$work_meter_start = $data["work_meter_start"];
		$meter = $data["work_meter_end"];
		
		if( $meter == "-1" ){
			
			$meter = "";
			
		}
		
		if( ( $work_meter_start == "" ) || ( $work_meter_start == "-1" ) ){
			
			$work_start_flg = false;
			
		}
	
	}
	
}

$staff_name = get_staff_name_by_staff_id($staff_id);



/*
echo "<pre>";
print_r($attendance_data);
echo "</pre>";
exit();
*/



$params['page_title'] = $staff_name."さんのページ";
$params['staff_name'] = $staff_name;
$params['staff_id'] = $staff_id;
$params['ch'] = $ch;
$params['area'] = $area;
$params['attendance_staff_new_id'] = $attendance_staff_new_id;
$params['meter'] = $meter;
$params['work_start_flg'] = $work_start_flg;
$params['work_meter_start'] = $work_meter_start;
$params['error'] = $error;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/work_end2.tpl' );
$smarty->display( 'sp/template_beta.tpl' );

?>