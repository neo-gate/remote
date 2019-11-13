<?php

include("include/common.php");



$error_page_message = $_SESSION["error_page_message"];

if( $error_page_message == "" ){
	
	$error_page_message = "システムエラーが発生しました。";
	
}

$params['page_title'] = "業務連絡BBS(エラーページ)";
$params['error_page_message'] = $error_page_message;

$smarty->assign( 'params', $params );

if( ( $access_type == "pc" ) || ( $access_type == "sp" ) ){

	$smarty->assign( 'content_tpl', 'sp/error.tpl' );
	$smarty->display( 'sp/template.tpl' );

}else if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/error.tpl' );
	$smarty->display( 'm/template.tpl' );

}

?>