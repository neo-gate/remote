<?php

include("../include/common.php");



$error = "";

$car_type = $_SESSION["d_f_car_type"];
$car_color = $_SESSION["d_f_car_color"];
$car_number = $_SESSION["d_f_car_number"];
$tel = $_SESSION["d_f_tel"];
$area = $_SESSION["d_f_area"];
$staff_id = $_SESSION["d_f_staff_id"];
$ch = $_SESSION["d_f_ch"];
$car_image_url = $_SESSION["d_f_car_image_url"];
$etc_number = $_SESSION["d_f_etc_number"];

if( isset($_POST["send"]) == true ){
	
	update_staff_tmp_common($staff_id,$car_type,$car_color,$car_number,$tel,$car_image_url,$area);

	header("Location: complete.php");
	exit();

}else if( isset($_POST["back"])==true ){
	
	header("Location: input.php?back=true");
	exit();
	
}

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);

$page_title = "ドライバー情報編集(確認)";



$params['top_url'] = $top_url;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;

$params['car_type'] = $car_type;
$params['car_color'] = $car_color;
$params['car_number'] = $car_number;
$params['tel'] = $tel;
$params['car_image_url'] = $car_image_url;
$params['etc_number'] = $etc_number;

$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['ch'] = $ch;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/driver_edit/confirm.tpl' );
$smarty->display( 'sp/template.tpl' );

?>