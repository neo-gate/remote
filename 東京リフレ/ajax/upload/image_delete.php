<?php
session_start();
header("Content-type: text/html; charset=UTF-8");

$display_image_url = $_SESSION["display_image_url"];

if(unlink($display_image_url)){
	
	$_SESSION["display_image_url"] = "";
	
	echo '<div style="color:blue;padding:5px 0px 0px 5px;">画像削除完了</div>';
	exit();
	
}else{
	
	echo '<div style="color:red;padding:5px 0px 0px 5px;">画像削除失敗：'.$display_image_url."</div>";
	exit();
}

?>