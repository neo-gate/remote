<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	$pankuzu = "　＞　ビジネスマン向け特別メニュー";
	
	$params['pankuzu'] = $pankuzu;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		$smarty->assign( 'content_tpl', 'pc/business.tpl' );
		$smarty->display( 'pc/template.tpl' );
	
	}else if($access_type=="sp"){
	
		$smarty->assign( 'content_tpl', 'sp/business.tpl' );
		$smarty->display( 'sp/template.tpl' );
	
	}

?>