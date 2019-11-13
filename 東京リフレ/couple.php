<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	$pankuzu = "　＞　お得なカップルプラン";
	
	$params['pankuzu'] = $pankuzu;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		$smarty->assign( 'content_tpl', 'pc/couple.tpl' );
		$smarty->display( 'pc/template.tpl' );
	
	}else if($access_type=="sp"){
	
		$smarty->assign( 'content_tpl', 'sp/couple.tpl' );
		$smarty->display( 'sp/template.tpl' );
	
	}

?>