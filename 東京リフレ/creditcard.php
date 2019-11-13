<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	$pankuzu = "　＞　クレジットカードのご利用";
	
	$params['pankuzu'] = $pankuzu;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		$smarty->assign( 'content_tpl', 'pc/creditcard.tpl' );
		$smarty->display( 'pc/template.tpl' );
	
	}else if($access_type=="sp"){
	
		$smarty->assign( 'content_tpl', 'sp/creditcard.tpl' );
		$smarty->display( 'sp/template.tpl' );
	
	}else if($access_type=="m"){
	
		$smarty->assign( 'content_tpl', 'm/creditcard.tpl' );
		$smarty->display( 'm/template.tpl' );
	
	}

?>