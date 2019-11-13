<?php

include("include/common.php");



$area = $_GET["area"];
$staff_id = $_GET["id"];
$ch = $_GET["ch"];
if( ($area == "") || ($staff_id == "") ){
	echo "error!";
	exit();
}

$error = "";

if( isset($_POST["send"])==true ){

	$content = $_POST["content"];
	
	if( $content == "" ){
	
		$error = '<span style="color:red;">未入力です</span>';
	
	}
	
	if( $error == "" ){
		
		$type = "2";
		insert_board_message_history_common($type,$staff_id,$content);
		
		send_mail_for_driver_page_2_common($staff_id,$content,$area);
		
		$www_url_tokyo = WWW_URL_TOKYO;
		
		$url = sprintf("%sdriver/history.php?area=%s&id=%s&ch=%s",$www_url_tokyo,$area,$staff_id,$ch);
		
		header('Location: '.$url);
		exit();
		
	}

}



$staff_name = get_staff_name_by_staff_id($staff_id);

$num = 30;
$list = get_board_message_history_for_driver_history_common($staff_id,$num);

$list_data = list_separate($list);

$list_top = $list_data[0];
$list_bottom = $list_data[1];

$top_url = get_top_url($area,$staff_id,$ch);
$params['top_url'] = $top_url;

$file_name = "communication.php";
$url_communication = get_driver_url($area,$staff_id,$ch,$file_name);

$file_name = "history.php";
$url_history = get_driver_url($area,$staff_id,$ch,$file_name);

$params['url_communication'] = $url_communication;
$params['url_history'] = $url_history;

/*
echo "<pre>";
print_r($sale_data);
echo "</pre>";
exit();
*/

$page_title = "通信履歴";

$params['ch'] = $ch;
$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;

$params['list_top'] = $list_top;
$params['list_bottom'] = $list_bottom;

$params['error'] = $error;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/history.tpl' );
$smarty->display( 'sp/template.tpl' );

?>