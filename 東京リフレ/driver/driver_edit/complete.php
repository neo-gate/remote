<?php

include("../include/common.php");



$area = $_SESSION["d_f_area"];
$staff_id = $_SESSION["d_f_staff_id"];
$ch = $_SESSION["d_f_ch"];

unset($_SESSION["d_f_car_type"]);
unset($_SESSION["d_f_car_color"]);
unset($_SESSION["d_f_car_number"]);
unset($_SESSION["d_f_tel"]);

unset($_SESSION["d_f_car_image_url"]);

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);

$page_title = "ドライバー情報編集(完了)";



$params['top_url'] = $top_url;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/driver_edit/complete.tpl' );
$smarty->display( 'sp/template.tpl' );

?>