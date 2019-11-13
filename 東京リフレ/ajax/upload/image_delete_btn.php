<?php
session_start();
header("Content-type: text/html; charset=UTF-8");

$display_image_url = $_SESSION["display_image_url"];

echo '<input type="button" value="画像削除" onclick="deleteFile(\''.$display_image_url.'\')">';
exit();

?>