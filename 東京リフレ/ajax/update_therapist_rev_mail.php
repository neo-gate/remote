<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$reservation_day = $_POST["reservation_day"];
$area = $_POST["area"];

$pieces = explode("_", $reservation_day);

$year = $pieces[0];
$month = $pieces[1];
$day = $pieces[2];

$therapist_id = "-1";

$therapist_select_frm = get_therapist_select_frm_for_reservation_mail_frm_common($area,$therapist_id,$year,$month,$day);

echo $therapist_select_frm;
exit();

?>