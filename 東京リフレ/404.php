<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	$not_found_flg = true;
	
	$params['not_found_flg'] = $not_found_flg;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		$smarty->assign( 'content_tpl', 'pc/404.tpl' );
		$smarty->display( 'pc/template.tpl' );
	
	}else if($access_type=="sp"){
	
		$smarty->assign( 'content_tpl', 'sp/404.tpl' );
		$smarty->display( 'sp/template.tpl' );
	
	}else if($access_type=="m"){
	
		$smarty->assign( 'content_tpl', 'm/404.tpl' );
		$smarty->display( 'm/template.tpl' );
	
	}

?>