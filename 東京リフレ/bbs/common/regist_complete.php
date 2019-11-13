<?php

include("include/common.php");
include(INC_PATH."/auth.php");



unset($_SESSION["bbs_regist_area"]);
unset($_SESSION["bbs_regist_naiyou"]);



$params['page_title'] = "業務連絡BBS(新規メッセージ投稿・完了)";

$smarty->assign( 'params', $params );

if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/regist_complete.tpl' );
	$smarty->display( 'm/template.tpl' );

}else{
	
	$smarty->assign( 'content_tpl', 'sp/regist_complete.tpl' );
	$smarty->display( 'sp/template.tpl' );
	
}

?>