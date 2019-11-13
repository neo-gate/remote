<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	$error_page_flg = true;
	$params['error_page_flg'] = $error_page_flg;
	
	$pankuzu = "　＞　エラーページ";
	
	$params['pankuzu'] = $pankuzu;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		$smarty->assign( 'content_tpl', 'pc/error.tpl' );
		$smarty->display( 'pc/template.tpl' );
	
	}else if($access_type=="sp"){
	
		$smarty->assign( 'content_tpl', 'sp/error.tpl' );
		$smarty->display( 'sp/template.tpl' );
	
	}else if($access_type=="m"){
	
		$smarty->assign( 'content_tpl', 'm/error.tpl' );
		$smarty->display( 'm/template.tpl' );
	
	}

?>