<?php

$site_sub_folder_flg = true;

define("COMMON_FOLDER_NAME", "common");

if( PHP_OS == "WINNT" ){
	define("SETUP_PATH", "C:/svn/neogate/sites/".COMMON_FOLDER_NAME."/include/");
}else{
	define("SETUP_PATH", "/home/neogate/sites/".COMMON_FOLDER_NAME."/include/");
}
include(SETUP_PATH."setup_test.php");



//ドメイン名取得
$domain = $_SERVER["SERVER_NAME"];
$url_root = "http://".$domain."/driver/";
$url_root_site = "http://".$domain."/";
$params['url_root_site'] = $url_root_site;
define("WWW_URL", $url_root);
define("WWW_URL_SITE", $url_root_site);

define("MAN_INC", REFLE_DOCUMENT_ROOT."/man/include/");

//退職・休職ユーザーチェック
if( isset($_GET["id"]) == true ){

	$therapist_id = $_GET["id"];

	$result = check_access_user_seigen_staff($therapist_id);

	if( $result == false ){

		echo "access check error!";
		exit();

	}

}

?>
