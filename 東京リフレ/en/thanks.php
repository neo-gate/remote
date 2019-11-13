<?php

	// 各ページ共通の処理
	require_once("../include/common.php");
	
	
	
	$tokyo_refle_flg = true;
	$params['tokyo_refle_flg'] = $tokyo_refle_flg;
	$conversion_thanks_path_web_reservation_tokyo_refle = sprintf("file:%sadwords/tokyo-refle/conversion_thanks_web_reservation.tpl",COMMON_TEMPLATES);
	$smarty->assign( 'conversion_thanks_path_web_reservation_tokyo_refle', $conversion_thanks_path_web_reservation_tokyo_refle );
	
	
	
	$page_title = "Thanks | Visit Relaxation Massage Tokyo Refle";
	$page_keywords = "Thanks,massage,relaxation,tokyo,visit,hotel,home";
	$page_description = "Tokyo Refle is visit relaxation massage service with the no.1 results in Tokyo. We provide professional massage by Japanese female therapist at hotels or home in Tokyo.";
	
	$params['page_title'] = $page_title;
	$params['page_keywords'] = $page_keywords;
	$params['page_description'] = $page_description;
	
	$smarty->assign( 'params', $params );
	
	if( $access_type == "sp" ){
		
		$smarty->assign( 'content_tpl', 'sp/en/thanks.tpl' );
		$smarty->display( 'sp/template_en.tpl' );
		
	}else{
		
		$smarty->assign( 'content_tpl', 'pc/en/thanks.tpl' );
		$smarty->display( 'pc/template_en.tpl' );
		
	}

?>