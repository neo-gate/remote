<?php

include("include/common.php");
include(INC_PATH."/auth.php");



unset($_SESSION["bbs_response_area"]);
unset($_SESSION["bbs_response_naiyou"]);
unset($_SESSION["bbs_response_to_staff_id"]);
unset($_SESSION["bbs_response_to_therapist_id"]);



$params['page_title'] = "業務連絡BBS(返信・完了)";

$smarty->assign( 'params', $params );

if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/response_complete.tpl' );
	$smarty->display( 'm/template.tpl' );

}else{
	
	$smarty->assign( 'content_tpl', 'sp/response_complete.tpl' );
	$smarty->display( 'sp/template.tpl' );
	
}

?>