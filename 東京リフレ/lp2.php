<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	
	
	$error = "";
	
	$time_array = array(
	    "0" => "-1",
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
		"21" => "4:00"
	);
	
	$course_array = array(
	    "0" => "-1",
	    "1" => "90分コース(14,000円+交通費)",
		"2" => "120分コース(19,000円+交通費)",
		"3" => "150分コース(23,000円+交通費)",
		"4" => "180分コース(28,000円+交通費)",
		"5" => "210分コース(32,000円+交通費)",
		"6" => "240分コース(37,000円+交通費)"
	);
	
	
	$tokyo_array = array(
	    "0" => "-1",
	    "1" => "新宿区",
		"2" => "港区",
		"3" => "千代田区",
		"4" => "文京区",
		"5" => "中央区",
		"6" => "墨田区",
		"7" => "渋谷区",
		"8" => "豊島区",
		"9" => "台東区",
		"10" => "品川区",
	    "11" => "目黒区",
		"12" => "江東区",
		"13" => "荒川区",
		"14" => "世田谷区",
		"15" => "大田区",
		"16" => "中野区",
		"17" => "練馬区",
		"18" => "杉並区",
		"19" => "江戸川区",
		"20" => "板橋区",
		"21" => "足立区",
		"22" => "葛飾区",
		"23" => "北区"
	);
	
	
	if( isset($_POST["send"]) == true ){
	
		$time = $_POST["time"];
		$course = $_POST["course"];
		$onamae = $_POST["onamae"];
		$mail = $_POST["mail"];
		$mail_confirm = $_POST["mail_confirm"];
		$tokyo = $_POST["tokyo"];
		$address = $_POST["address"];
		$tel = $_POST["tel"];
		$renraku = $_POST["renraku"];
		
		$reservation_day = $_POST["reservation_day"];
		$therapist_id = $_POST["therapist_id"];
		
		$mail_confirm = $_POST["mail_confirm"];
		
		$pieces = explode("_", $reservation_day);
		
		$year = $pieces[0];
		$month = $pieces[1];
		$day = $pieces[2];
		
		$error = "";
	
		if($time=="-1"){
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
	
			$_SESSION["time"] = $_POST["time"];
			$_SESSION["course"] = $_POST["course"];
			$_SESSION["onamae"] = $_POST["onamae"];
			$_SESSION["mail"] = $_POST["mail"];
			$_SESSION["address"] = $_POST["address"];
			$_SESSION["tel"] = $_POST["tel"];
			$_SESSION["renraku"] = $_POST["renraku"];
			
			$_SESSION["reservation_day"] = $reservation_day;
			$_SESSION["therapist_id"] = $therapist_id;
			
			$_SESSION["mail_confirm"] = $_POST["mail_confirm"];
	
			header('Location: '.$url_root.'mail/reservation/confirm.php');
			exit();
	
		}
	
	}else{
		
		$data = get_today_year_month_day_common();
		
		$year = $data["year"];
		$month = $data["month"];
		$day = $data["day"];
		
	}
	
	$day_select_frm = get_day_select_frm_for_reservation_mail_frm($reservation_day);
	
	$area = "tokyo";
	$therapist_select_frm = get_therapist_select_frm_for_reservation_mail_frm($area,$therapist_id,$year,$month,$day);
	
	$select_time = "";
	if($time==null){
		$select_time .= '<option value="'.$time_array[0].'" selected="selected">選択してください</option>';
	}else{
		$select_time .= '<option value="'.$time_array[0].'">選択してください</option>';
	}
	
	$i=0;
	for($i=1;$i<22;$i++){
		if($time==$time_array[$i]){
			$select_time .= '<option value="'.$time_array[$i].'" selected="selected">'.$time_array[$i].'</option>';
		}else{
			$select_time .= '<option value="'.$time_array[$i].'">'.$time_array[$i].'</option>';
		}
	}
	
	$select_course = "";
	if($course==null){
		$select_course .= '<option value="'.$course_array[0].'" selected="selected">選択してください</option>';
	}else{
		$select_course .= '<option value="'.$course_array[0].'">選択してください</option>';
	}
	
	$select_course_sp = "";
	if($course==null){
		$select_course_sp .= '<option value="'.$course_array_sp[0].'" selected="selected">選択してください</option>';
	}else{
		$select_course_sp .= '<option value="'.$course_array_sp[0].'">選択してください</option>';
	}
	
	$i=0;
	for($i=1;$i<7;$i++){
		if($course==$course_array[$i]){
			$select_course .= '<option value="'.$course_array[$i].'" selected="selected">'.$course_array[$i].'</option>';
		}else{
			$select_course .= '<option value="'.$course_array[$i].'">'.$course_array[$i].'</option>';
		}
	}
	
	$i=0;
	for($i=1;$i<7;$i++){
		if($course==$course_array_sp[$i]){
			$select_course_sp .= '<option value="'.$course_array_sp[$i].'" selected="selected">'.$course_array_sp[$i].'</option>';
		}else{
			$select_course_sp .= '<option value="'.$course_array_sp[$i].'">'.$course_array_sp[$i].'</option>';
		}
	}
	
	$select_tokyo = "";
	if($tokyo==null){
		$select_tokyo .= '<option value="'.$tokyo_array[0].'" selected="selected">選択してください</option>';
	}else{
		$select_tokyo .= '<option value="'.$tokyo_array[0].'">選択してください</option>';
	}
	
	$i=0;
	for($i=1;$i<24;$i++){
		if($tokyo==$tokyo_array[$i]){
			$select_tokyo .= '<option value="'.$tokyo_array[$i].'" selected="selected">'.$tokyo_array[$i].'</option>';
		}else{
			$select_tokyo .= '<option value="'.$tokyo_array[$i].'">'.$tokyo_array[$i].'</option>';
		}
	}
	
	$time_select_option = get_time_array_select_option_vip($time);
	
	$now_hour = intval(date('H'));
	$now_minute = intval(date('i'));
	
	if( $now_minute < 10 ){
		
		$now_minute = "0".$now_minute;
		
	}
	
	if($now_hour <= 6){
		
		//昨日の日付
		$year_now = intval(date('Y', strtotime('-1 day')));
		$month_now = intval(date('m', strtotime('-1 day')));
		$day_now = intval(date('d', strtotime('-1 day')));
		$week_now = date("w", strtotime('-1 day'));
		
	}else{
		
		$year_now = intval(date('Y'));
		$month_now = intval(date('m'));
		$day_now = intval(date('d'));
		$week_now = date("w");
		
	}
	
	$week_name = "";
	
	if($week_now==0){
		
		$week_name = "日";
		
	}else if($week_now==1){
		
		$week_name = "月";
		
	}else if($week_now==2){
		
		$week_name = "火";
		
	}else if($week_now==3){
		
		$week_name = "水";
		
	}else if($week_now==4){
		
		$week_name = "木";
		
	}else if($week_now==5){
		
		$week_name = "金";
		
	}else if($week_now==6){
		
		$week_name = "土";
		
	}
	
	$area = "tokyo";
	$attendance_num = get_therapist_attendance_num_new($year_now,$month_now,$day_now,$area);
	
	$reservation_message = get_reservation_message($now_hour,$area);
	
	$params['year_now'] = $year_now;
	$params['month_now'] = $month_now;
	$params['day_now'] = $day_now;
	$params['week_name'] = $week_name;
	$params['now_hour'] = $now_hour;
	$params['now_minute'] = $now_minute;
	$params['attendance_num'] = $attendance_num;
	$params['reservation_message'] = $reservation_message;
	
	$params['select_time'] = $select_time;
	$params['select_course'] = $select_course;
	$params['onamae'] = $onamae;
	$params['mail'] = $mail;
	$params['address'] = $address;
	$params['tel'] = $tel;
	$params['renraku'] = $renraku;
	$params['error'] = $error;
	$params['time_select_option'] = $time_select_option;
	
	$params['day_select_frm'] = $day_select_frm;
	$params['therapist_select_frm'] = $therapist_select_frm;
	$params['area'] = $area;
	
	$params['mail_confirm'] = $mail_confirm;
	
	
	
	$smarty->assign( 'params', $params );
	
	if( $access_type == "sp" ){
		
		$smarty->display( 'sp/lp2.tpl' );
		
	}else{
		
		$smarty->display( 'pc/lp2.tpl' );
		
	}

?>