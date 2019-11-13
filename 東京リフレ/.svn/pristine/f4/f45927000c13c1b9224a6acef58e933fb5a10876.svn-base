<?php

include("include/common.php");



$error_page_message = $_SESSION["error_page_message"];

if( $error_page_message == "" ){
	
	$error_page_message = "システムエラーが発生しました。";
	
}

$params['top_url'] = $_SESSION["top_url"];

$params['page_title'] = "エラーページ";
$params['error_page_message'] = $error_page_message;

$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/error.tpl' );
$smarty->display( 'sp/template.tpl' );

?>