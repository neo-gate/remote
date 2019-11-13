<?php

$site_sub_folder_flg = true;

if( PHP_OS == "WINNT" ){
	define("SETUP_PATH", "C:/svn/neogate/sites/common/include/");
}else{
	define("SETUP_PATH", "/home/neogate/sites/common/include/");
}
include(SETUP_PATH."setup.php");



$domain = $_SERVER["SERVER_NAME"];
$url_root = "http://".$domain."/bbs/common/";

define("WWW_URL", $url_root);
define("SESSION_TICKET", "ticket_bbs");

?>