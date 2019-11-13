<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	
	$pankuzu = "　＞　会社概要";
	
	$params['pankuzu'] = $pankuzu;
	
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
		
		$smarty->assign( 'content_tpl', 'pc/company.tpl' );
		$smarty->display( 'pc/template.tpl' );
		
	}else if($access_type=="sp"){
		
		$smarty->assign( 'content_tpl', 'sp/company.tpl' );
		$smarty->display( 'sp/template.tpl' );
		
	}else if($access_type=="m"){
		
		$smarty->assign( 'content_tpl', 'm/company.tpl' );
		$smarty->display( 'm/template.tpl' );
		
	}


?>