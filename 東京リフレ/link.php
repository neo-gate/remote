<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	$standard_title = "";
	
	$pankuzu = "　＞　リンク集";
	
	$params['pankuzu'] = $pankuzu;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		$smarty->assign( 'content_tpl', 'pc/link.tpl' );
		$smarty->display( 'pc/template.tpl' );
	
	}else if($access_type=="sp"){
	
		header("Location: ".$url_root);
  		exit();
	
	}else if($access_type=="m"){
	
		header("Location: ".$url_root);
  		exit();
	
	}

?>