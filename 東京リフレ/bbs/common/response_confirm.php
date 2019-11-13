<?php

include("include/common.php");
include(INC_PATH."/auth.php");



$area = $_SESSION["bbs_response_area"];
$naiyou = $_SESSION["bbs_response_naiyou"];
$to_staff_id = $_SESSION["bbs_response_to_staff_id"];
$to_therapist_id = $_SESSION["bbs_response_to_therapist_id"];



if( isset($_POST["back"]) == true ){

	header('Location: response.php?back=true');
	exit();

}else if(isset($_POST["send"])==true){

	insert_shift_message_response_bbs($staff_id,$staff_type,$area,$naiyou,$to_staff_id,$to_therapist_id);
	
	header('Location: response_complete.php');
	exit();

}


$params['page_title'] = "業務連絡BBS(返信・確認)";

$params['naiyou'] = $naiyou;

$smarty->assign( 'params', $params );

if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/response_confirm.tpl' );
	$smarty->display( 'm/template.tpl' );

}else{
	
	$smarty->assign( 'content_tpl', 'sp/response_confirm.tpl' );
	$smarty->display( 'sp/template.tpl' );
	
}

?>