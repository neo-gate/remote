<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");

$access_type = "pc";

$area = "tokyo";

$year = $_POST["year"];
$month = $_POST["month"];
$day = $_POST["day"];

$html = get_therapist_page_attendance_update_html_common($area,$year,$month,$day,$access_type);

echo $html;
exit();

?>