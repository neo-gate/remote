<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	$page_title = "出張エリア | 出張マッサージ東京リフレ";
	$page_keywords = "出張エリア,出張マッサージ,東京,リフレクソロジー,アロママッサージ,リンパマッサージ";
	$page_description = "出張エリアのページです。出張マッサージをお探しなら【東京リフレ】高技術を持った女性セラピストが都内23区のご自宅やホテルへ出張します。";
	
	$params['page_title'] = $page_title;
	$params['page_keywords'] = $page_keywords;
	$params['page_description'] = $page_description;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		header("Location: ".$url_root);
		exit();
	
	}else if($access_type=="sp"){
	
		$smarty->assign( 'content_tpl', 'sp/area2.tpl' );
		$smarty->display( 'sp/template.tpl' );
	
	}else if($access_type=="m"){
	
		$smarty->assign( 'content_tpl', 'm/area2.tpl' );
		$smarty->display( 'm/template.tpl' );
	
	}

?>