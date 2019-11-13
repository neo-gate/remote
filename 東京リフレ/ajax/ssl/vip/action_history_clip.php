<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$reservation_for_board_id = $_POST["id"];
$selected_num = $_POST["selected_num"];
$type = $_POST["type"];

$tmp = get_reservation_for_board_data_by_id_common($reservation_for_board_id);
$attendance_id = $tmp["attendance_id"];
$customer_id = $tmp["customer_id"];
if(($customer_id == "-1")||($customer_id != $_POST["loginCustomId"])) $customer_id = $_POST["loginCustomId"];		//insert by aida at 20180319 �\���󋵃f�[�^�̌ڋqID���|�P�΍�

$therapist_id = get_therapist_id_by_attendance_id_common($attendance_id);

if( ($customer_id=="") || ($therapist_id=="") ){

	echo "NG";
	exit();

}

if( ($customer_id=="-1") || ($therapist_id=="-1") ){

	echo "NG";
	exit();

}

$exist_flg = false;

$result = check_exist_customer_clip_common($customer_id,$therapist_id);

if( $result == true ){

	$exist_flg = true;

}

if( $type == "on" ){

	if( $exist_flg == false ){

		insert_customer_clip_common($customer_id,$therapist_id);

	}

}else if( $type == "off" ){

	delete_customer_clip_common($customer_id,$therapist_id);

}else{

	echo "NG";
	exit();

}

echo "OK";
exit();

?>
