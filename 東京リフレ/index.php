<?php
	// 各ページ共通の処理
	require_once("include/common.php");

	$top_flg = true;
	$params['top_flg'] = $top_flg;

	$yahoo_retargeting_flg = true;
	$params['yahoo_retargeting_flg'] = $yahoo_retargeting_flg;

	$now_hour = intval(date('H'));

	if($now_hour <= 10){

		//昨日の日付
		$year = intval(date('Y', strtotime('-1 day')));
		$month = intval(date('m', strtotime('-1 day')));
		$day = intval(date('d', strtotime('-1 day')));
		$week = date("w", strtotime('-1 day'));

	}else{

		$year = intval(date('Y'));
		$month = intval(date('m'));
		$day = intval(date('d'));
		$week = date("w");

	}

	$page_name = "top";

	$about_title = "";

	$shop_type = "refle";

	if( $access_type == "sp" ){

		$num = 3;

		//お客様の声データ取得
		$voice_list = get_voice_list_front_top($shop_type,$num);
		$params['voice_list'] = $voice_list;

	}else if( $access_type == "pc" ){

		$num = 5;

		//お客様の声データ取得
		$voice_list = get_voice_list_front_top($shop_type,$num);
		$params['voice_list'] = $voice_list;

	}

	$head_page_name = "head_index_page";
	$head_add = get_page_html_2_content_by_page_name_common($head_page_name);
	$params['head_add'] = $head_add;

	$page_title = "東京で出張マッサージならご利用実績No.1｜東京リフレ";
	$page_description = "東京で出張マッサージならご利用実績No.1の東京リフレへ！実績を積んだセラピストが多数在籍しリフレクソロジー、アロママッサージ、タイ古式マッサージなどの施術を高いレベルでご提供。おかげさまでリピート率82％！14000円～新宿、品川、渋谷など東京23区のご自宅やホテルへ出張致します。";
	$link_cpt = "<link rel='contents' href='http://www.tokyo-refle.com/concept.php'>";
	$link_tp = "<link rel='contents' href='http://www.tokyo-refle.com/therapist.php'>";
	$link_menu = "<link rel='contents' href='http://www.tokyo-refle.com/menu.php'>";
	$link_gdnc = "<link rel='contents' href='http://www.tokyo-refle.com/guidance.php'>";
	$link_voice = "<link rel='contents' href='http://www.tokyo-refle.com/voice.php'>";
	$link_recruit = "<link rel='contents' href='http://www.tokyo-refle.com/recruit.php'>";
	$link_vip = "<link rel='contents' href='https://www.tokyo-refle.com/ssl/tokyo/vip/'>";
	$canonical = "<link rel='canonical' href='http://www.tokyo-refle.com'>";

	$params['page_title'] = $page_title;
	$params['page_keywords'] = $page_keywords;
	$params['page_description'] = $page_description;
	$params['link_cpt'] = $link_cpt;
	$params['link_tp'] = $link_tp;
	$params['link_menu'] = $link_menu;
	$params['link_gdnc'] = $link_gdnc;
	$params['link_voice'] = $link_voice;
	$params['link_recruit'] = $link_recruit;
	$params['link_vip'] = $link_vip;
	$params['canonical'] = $canonical;
	$params['year'] = $year;
	$params['month'] = $month;
	$params['day'] = $day;
	$params['page_name'] = $page_name;

	$smarty->assign( 'params', $params );

	if($access_type=="pc"){

		$smarty->assign( 'content_tpl', 'pc/index.tpl' );
		$smarty->display( 'pc/template.tpl' );

	}else if($access_type=="sp"){

		$smarty->assign( 'content_tpl', 'sp/index.tpl' );
		$smarty->display( 'sp/template.tpl' );

	}else if($access_type=="m"){

		$smarty->assign( 'content_tpl', 'm/index.tpl' );
		$smarty->display( 'm/template.tpl' );

	}


?>
