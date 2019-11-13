<?php

	// 各ページ共通の処理
	require_once("../include/common.php");
	
	
	
	if( isset($_GET["id"]) == true ){
		
		$url_name = $_GET["id"];
		
	}else{
		
		header("Location: ".$url_root."en/");
		exit();
		
	}
	
	$station_id = get_station_id_by_url_name_for_en($url_name);
	
	if( $station_id == "" ){
		
		header("Location: ".$url_root."en/");
		exit();
		
	}
	
	
	
	$station_name = ucfirst($url_name);
	
	$page_title = $station_name." | Visit Relaxation Massage Tokyo Refle";
	$page_keywords = $station_name.",massage,relaxation,tokyo,visit,hotel,home";
	$page_description = "It is the Tokyo Refle if it is search about a visit relaxation massage in ".$station_name.". We provide professional massage by Japanese female therapist at hotels or home in ".$station_name.".";
	
	$params['page_title'] = $page_title;
	$params['page_keywords'] = $page_keywords;
	$params['page_description'] = $page_description;
	
	$params['station_name'] = $station_name;
	$params['url_name'] = $url_name;
	
	
	
	//以下、予約フォーム用の処理
	
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
	
	$today_year = date('Y');
	$today_month = date('n');
	$today_day = date('j');
	
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
	
	
	
	if( $access_type == "pc" ){
	
		$site_type = "tokyo-refle";
		$page_type = "station_en";
	
		//追記データ取得
		$add_data = get_add_data_en($site_type,$page_type,$station_id);
		$params['add_data'] = $add_data;
	
	}
	
	
	
	$smarty->assign( 'params', $params );
	
	if( $access_type == "sp" ){
	
		$smarty->assign( 'content_tpl', 'sp/en/station.tpl' );
		$smarty->display( 'sp/template_en.tpl' );
	
	}else{
	
		$smarty->assign( 'content_tpl', 'pc/en/station.tpl' );
		$smarty->display( 'pc/template_en.tpl' );
	
	}

?>