<?php

	// 各ページ共通の処理
	require_once("../include/common.php");
	
	
	
	$tokyo_refle_flg = true;
	$params['tokyo_refle_flg'] = $tokyo_refle_flg;
	$conversion_thanks_path_web_reservation_tokyo_refle = sprintf("file:%sadwords/tokyo-refle/conversion_thanks_web_reservation.tpl",COMMON_TEMPLATES);
	$smarty->assign( 'conversion_thanks_path_web_reservation_tokyo_refle', $conversion_thanks_path_web_reservation_tokyo_refle );
	
	
	
	$page_title = "Thanks | 派遣按摩【东京reflex】";
	$page_keywords = "Thanks,派遣按摩,东京reflex";
	$page_description = "本店【东京reflex】专为处于东京中心地带的宾馆，或在自己住处的客人提供按摩服务，按摩师均   为技术熟练的日本女性。";
	
	$params['page_title'] = $page_title;
	$params['page_keywords'] = $page_keywords;
	$params['page_description'] = $page_description;
	
	$smarty->assign( 'params', $params );
	
	if( $access_type == "sp" ){
		
		$smarty->assign( 'content_tpl', 'sp/ch/thanks.tpl' );
		$smarty->display( 'sp/template_ch.tpl' );
		
	}else{
		
		$smarty->assign( 'content_tpl', 'pc/ch/thanks.tpl' );
		$smarty->display( 'pc/template_ch.tpl' );
		
	}

?>