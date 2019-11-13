<?php

define("COMMON_FOLDER_NAME", "common");

if( PHP_OS == "WINNT" ){
	define("SETUP_PATH", "C:/svn/neogate/sites/".COMMON_FOLDER_NAME."/include/");
}else{
	define("SETUP_PATH", "/home/neogate/sites/".COMMON_FOLDER_NAME."/include/");
}
include(SETUP_PATH."setup.php");

?>