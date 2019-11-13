<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	$pankuzu = "　＞　ご利用方法";
	
	$guidance_flg = true;
	
	$params['pankuzu'] = $pankuzu;
	
	$params['guidance_flg'] = $guidance_flg;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		$smarty->assign( 'content_tpl', 'pc/guidance.tpl' );
		$smarty->display( 'pc/template.tpl' );
	
	}else if($access_type=="sp"){
	
		$smarty->assign( 'content_tpl', 'sp/guidance.tpl' );
		$smarty->display( 'sp/template.tpl' );
	
	}else if($access_type=="m"){
	
		$smarty->assign( 'content_tpl', 'm/guidance.tpl' );
		$smarty->display( 'm/template.tpl' );
	
	}

?>