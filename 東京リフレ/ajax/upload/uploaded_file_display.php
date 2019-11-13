<?php

// 定数定義
include("../../../include/define.php");

session_start();



$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");

$domain = $_SERVER["SERVER_NAME"];
$url_root = "http://".$domain."/";
define("WWW_URL", $url_root);



$file_name = $_POST["file_name"];

$image_path = ROOT_PATH.'img/upload/'.$file_name;

$image_url = WWW_URL.'img/upload/'.$file_name;

if($file_name=="-1"){
	
	$_SESSION["display_image_url"] = null;
	
	echo "";
	exit();
	
}else{

	$_SESSION["display_image_url"] = $image_path;
	
	echo '<div><img src="'.$image_url.'" width="200" /></div>';
	echo '<div style="padding:10px 0px 0px 0px;">'.$image_url.'</div>';
	exit();

}
?>