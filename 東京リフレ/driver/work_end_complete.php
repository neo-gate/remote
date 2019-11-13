<?php

include("include/common.php");



$ch = $_GET["ch"];
$staff_id = $_GET["id"];
$area = $_GET["area"];

$result = staff_shift_page_access_check($staff_id,$ch);
if( $result == false ){
	echo "error!";
	exit();
}

$staff_name = get_staff_name_by_staff_id($staff_id);



/*
echo "<pre>";
print_r($attendance_data);
echo "</pre>";
exit();
*/



$params['page_title'] = $staff_name."さんのページ";
$params['staff_name'] = $staff_name;
$params['staff_id'] = $staff_id;
$params['ch'] = $ch;
$params['area'] = $area;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/work_end_complete.tpl' );
$smarty->display( 'sp/template_beta.tpl' );

?>