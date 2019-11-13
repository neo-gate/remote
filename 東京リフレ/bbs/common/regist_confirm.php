<?php

include("include/common.php");
include(INC_PATH."/auth.php");



$area = $_SESSION["bbs_regist_area"];
$naiyou = $_SESSION["bbs_regist_naiyou"];



if( isset($_POST["back"]) == true ){

	header('Location: regist.php?back=true');
	exit();

}else if(isset($_POST["send"])==true){

	insert_shift_message_regist_bbs($staff_id,$staff_type,$area,$naiyou);
	
	header('Location: regist_complete.php');
	exit();

}


$params['page_title'] = "業務連絡BBS(新規メッセージ投稿・確認)";

$params['naiyou'] = $naiyou;

$smarty->assign( 'params', $params );

if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/regist_confirm.tpl' );
	$smarty->display( 'm/template.tpl' );

}else{
	
	$smarty->assign( 'content_tpl', 'sp/regist_confirm.tpl' );
	$smarty->display( 'sp/template.tpl' );
	
}

?>