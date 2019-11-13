<?php

	// 各ページ共通の処理
	require_once("../include/common.php");
	
	
	
	//以下、予約フォーム用の処理
	
	$error = "";
	
	$time_array = array(
			    "0" => "-1",
			    "1" => "6:00 pm",
				"2" => "6:30 pm",
				"3" => "7:00 pm",
				"4" => "7:30 pm",
				"5" => "8:00 pm",
				"6" => "8:30 pm",
				"7" => "9:00 pm",
				"8" => "9:30 pm",
				"9" => "10:00 pm",
				"10" => "10:30 pm",
			    "11" => "11:00 pm",
				"12" => "11:30 pm",
				"13" => "0:00 am",
				"14" => "0:30 am",
				"15" => "1:00 am",
				"16" => "1:30 am",
				"17" => "2:00 am",
				"18" => "2:30 am",
				"19" => "3:00 am",
				"20" => "3:30 am",
				"21" => "4:00 am",
				"22" => "4:30 am",
				"23" => "5:00 am"
	);
	
	$course_array = array(
			    "0" => "-1",
			    "1" => "_90min  15000yen",
				"2" => "120min  20000yen",
				"3" => "150min  24000yen",
				"4" => "180min  29000yen",
				"5" => "210min  33000yen",
				"6" => "240min  38000yen"
	);
	
	
	
	if( isset($_POST["send"]) == true ){
	
		/*
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		exit();
		*/
	
		$namae = $_POST["namae"];
		$mail = $_POST["mail"];
		$gender = $_POST["gender"];
		$today_month = $_POST["today_month"];
		$today_day = $_POST["today_day"];
		$today_year = $_POST["today_year"];
		$time = $_POST["time"];
		$course = $_POST["course"];
		$hotel_or_home = $_POST["hotel_or_home"];
		$hotel_name = $_POST["hotel_name"];
		$room_number = $_POST["room_number"];
		$home_address = $_POST["home_address"];
		$cash_or_credit = $_POST["cash_or_credit"];
		$any_request = $_POST["any_request"];
	
		if( $namae == "" ){
				
			$error .= '<li>Name is empty</li>';
				
		}
	
		if( $mail == "" ){
	
			$error .= '<li>Your E-mail is empty</li>';
	
		}
	
		if( $time == "-1" ){
	
			$error .= '<li>Time of use is not selected</li>';
	
		}
	
		if( $course == "-1" ){
	
			$error .= '<li>Massage time length is not selected</li>';
	
		}
	
		if( $hotel_or_home == "" ){
	
			$error .= '<li>Hotel or Home is not selected</li>';
	
		}else{
				
			if( $hotel_or_home == "hotel" ){
					
				if( $hotel_name == "" ){
	
					$error .= '<li>Hotel name is empty</li>';
	
				}
					
			}else if( $hotel_or_home == "home" ){
	
				if( $home_address == "" ){
	
					$error .= '<li>Home address is empty</li>';
	
				}
	
			}
				
		}
	
		if( $cash_or_credit == "" ){
	
			$error .= '<li>How to pay is not selected</li>';
	
		}
	
		if( $error != "" ){
	
			$error = '<ul id="error_booking_form">'.$error.'</ul>';
	
		}else{
				
			//メール送信
			$result = send_booking_form(
			$namae,$mail,$gender,$today_month,$today_day,$today_year,
			$time,$course,$hotel_or_home,$hotel_name,$room_number,
			$home_address,$cash_or_credit,$any_request
			);
				
			if( $result == false ){
	
				$error = '<ul id="error_booking_form"><li>Mail action is failure.Try again,please.</li></ul>';
	
			}else{
	
				header('Location: '.$url_root.'en/form_complete.php');
				exit();
	
			}
				
		}
	
	}else{
	
		$today_year = date('Y');
		$today_month = date('n');
		$today_day = date('j');
	
	}
	
	$select_year = "";
	for($i=2013;$i<2020;$i++){
		if($today_year==$i){
			$select_year .= "<option value='".$i."' selected>".$i."</option>";
		}else{
			$select_year .= "<option value='".$i."'>".$i."</option>";
		}
	}
	
	$select_month = "";
	for($i=1;$i<13;$i++){
		if($today_month==$i){
			$select_month .= "<option value='".$i."' selected>".$i."</option>";
		}else{
			$select_month .= "<option value='".$i."'>".$i."</option>";
		}
	}
	
	$select_day = "";
	for($i=1;$i<32;$i++){
		if($today_day==$i){
			$select_day .= "<option value='".$i."' selected>".$i."</option>";
		}else{
			$select_day .= "<option value='".$i."'>".$i."</option>";
		}
	}
	
	$select_time = "";
	if($time==null){
		$select_time .= '<option value="'.$time_array[0].'" selected="selected">select please</option>';
	}else{
		$select_time .= '<option value="'.$time_array[0].'">select please</option>';
	}
	
	$i=0;
	for($i=1;$i<24;$i++){
		if($time==$time_array[$i]){
			$select_time .= '<option value="'.$time_array[$i].'" selected="selected">'.$time_array[$i].'</option>';
		}else{
			$select_time .= '<option value="'.$time_array[$i].'">'.$time_array[$i].'</option>';
		}
	}
	
	$select_course = "";
	if($course==null){
		$select_course .= '<option value="'.$course_array[0].'" selected="selected">select please</option>';
	}else{
		$select_course .= '<option value="'.$course_array[0].'">select please</option>';
	}
	
	$i=0;
	for($i=1;$i<7;$i++){
		if($course==$course_array[$i]){
			$select_course .= '<option value="'.$course_array[$i].'" selected="selected">'.$course_array[$i].'</option>';
		}else{
			$select_course .= '<option value="'.$course_array[$i].'">'.$course_array[$i].'</option>';
		}
	}
	
	$params['select_year'] = $select_year;
	$params['select_month'] = $select_month;
	$params['select_day'] = $select_day;
	$params['select_time'] = $select_time;
	$params['select_course'] = $select_course;
	
	$params['namae'] = $namae;
	$params['mail'] = $mail;
	$params['gender'] = $gender;
	$params['hotel_or_home'] = $hotel_or_home;
	$params['hotel_name'] = $hotel_name;
	$params['room_number'] = $room_number;
	$params['home_address'] = $home_address;
	$params['cash_or_credit'] = $cash_or_credit;
	$params['any_request'] = $any_request;
	$params['error'] = $error;
	
	//以上、予約フォーム用の処理
	
	
	
	$now_hour = intval(date('H'));
	if($now_hour <= 10){
		//昨日の日付
		$year = intval(date('Y', strtotime('-1 day')));
		$month = date('M', strtotime('-1 day'));
		$day = intval(date('d', strtotime('-1 day')));
		$week = date("D", strtotime('-1 day'));
	}else{
		$year = intval(date('Y'));
		$month = date('M');
		$day = intval(date('d'));
		$week = date("D");
	}
	
	$params['year'] = $year;
	$params['month'] = $month;
	$params['day'] = $day;
	$params['week'] = $week;
	
	$page_title = "Visit Relaxation Massage Tokyo Refle";
	$page_keywords = "massage,relaxation,tokyo,visit,hotel,home";
	$page_description = "Tokyo Refle is visit relaxation massage service with the no.1 results in Tokyo. We provide professional massage by Japanese female therapist at hotels or home in Tokyo.";
	
	$params['page_title'] = $page_title;
	$params['page_keywords'] = $page_keywords;
	$params['page_description'] = $page_description;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
		
		$smarty->assign( 'content_tpl', 'pc/en/index.tpl' );
		$smarty->display( 'pc/template_en.tpl' );
		
	}else if($access_type=="sp"){
		
		$smarty->assign( 'content_tpl', 'sp/en/index.tpl' );
		$smarty->display( 'sp/template_en.tpl' );
		
	}else if($access_type=="m"){
		
		$smarty->assign( 'content_tpl', 'pc/en/index.tpl' );
		$smarty->display( 'pc/template_en.tpl' );
		
	}else{
		
		$smarty->assign( 'content_tpl', 'pc/en/index.tpl' );
		$smarty->display( 'pc/template_en.tpl' );
		
	}

?>