<?php

include("include/common.php");
include(INC_PATH."/auth.php");



$error = '';

if( isset($_POST["send_delete"]) == true ){
	
	$message_id = $_POST["message_id"];
	
	delete_shift_message_by_id($message_id);
	
	$error = '<span style="color:blue">削除しました</span>';

}

if( isset($_GET["page_area"]) == true ){
	
	$page_area = $_GET["page_area"];
	
	if( ($_SESSION["bbs_staff_type"] == "driver") && ($page_area != $_SESSION["bbs_staff_area"]) ){
		
		logout_action();
		
	}
	
}

if( $page_area == "" ){
	
	logout_action();
	
}

$page_area_menu = $page_area;

//シフトメッセージを取得
$order_num = 100;
$shift_message_data = get_shift_message_by_order_num_bbs($order_num,$page_area);

/*
echo "<pre>";
print_r($shift_message_data);
echo "</pre>";
exit();
*/

if( $staff_type == "honbu" ){
	
	$page_area_menu = "all";
	
}

/*
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";
*/



$params['page_title'] = "業務連絡BBS(トップ)";

$params['staff_name'] = $staff_name;
$params['staff_id'] = $staff_id;
$params['staff_type'] = $staff_type;
$params['staff_area'] = $staff_area;
$params['boss_flg'] = $boss_flg;
$params['therapist_flg'] = $therapist_flg;
$params['page_area'] = $page_area;
$params['page_area_menu'] = $page_area_menu;
$params['shift_message_data'] = $shift_message_data;
$params['error'] = $error;

$smarty->assign( 'params', $params );

if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/index.tpl' );
	$smarty->display( 'm/template.tpl' );

}else{
	
	$smarty->assign( 'content_tpl', 'sp/index.tpl' );
	$smarty->display( 'sp/template.tpl' );
	
}

?>