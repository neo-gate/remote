<?php

session_start();



$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");

$domain = $_SERVER["SERVER_NAME"];
$url_root = "http://".$domain."/";
define("WWW_URL", $url_root);



$now_year_month = $_SESSION["year_month"];
$file_name = $_SESSION["last_uploaded_file"];

$image_path = ROOT_PATH.'img/upload/'.$now_year_month.'/'.$file_name;

$image_url = WWW_URL.'img/upload/'.$now_year_month.'/'.$file_name;

$_SESSION["display_image_url"] = $image_path;

echo '<img src="'.$image_url.'" width="200" />';
echo '<br />';
echo $image_url;
exit();

?>