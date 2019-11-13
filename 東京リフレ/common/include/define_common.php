<?php

define("TOKYO_REFLE_URL", $tokyo_refle_url);
define("TOKYO_REFLE_DOCUMENT_ROOT", $tokyo_refle_document_root);

define("COMMON_PATH_COMMON", $tokyo_refle_document_root."/common/");
define("COMMON_INC_COMMON", $tokyo_refle_document_root."/common/include/");
define("COMMON_TEMPLATES_COMMON", $tokyo_refle_document_root."/common/templates/");

$keiretsu_banner_file_sp = COMMON_TEMPLATES_COMMON."sp/shop_banner.tpl";
$keiretsu_banner_file_sp_2 = COMMON_TEMPLATES_COMMON."sp/shop_banner_2.tpl";

define("KEIRETSU_BANNER_FILE_SP", $keiretsu_banner_file_sp);
define("KEIRETSU_BANNER_FILE_SP_2", $keiretsu_banner_file_sp_2);

define("AWS_KEY", "AKIAI5MDJWARKE7PTMHQ");
define("AWS_SECRET", "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");

define("S3_URL", "http://s3-refle.s3-website-ap-northeast-1.amazonaws.com/");

define("MAIL_PARAMETER", "-f info@neo-gate.jp");

$facebook_pixel_code = COMMON_TEMPLATES_COMMON."facebook/facebook_pixel_code.tpl";

define("FACEBOOK_PIXEL_CODE", $facebook_pixel_code);

//$www_ssl = "https://".$_SERVER["SERVER_NAME"]."/";
$www_ssl = SSL_sitePort . $_SERVER["SERVER_NAME"] . "/";		//in setup.php

define("WWW_URL_SSL", $www_ssl);

define("TAX_RATE", 1.08);

if( PHP_OS == "WINNT" ) {
	for($a=0; $a<count($ARRAY_ShopArea); $a++) {			//in setup.php
		if($a == 0) {
			$w_defineUrlval = "http://refle-aws.localhost/";
		} else {
			$w_defineUrlval = "http://" . $ARRAY_ShopArea[$a]["area"] . "-refle-aws.localhost/";
		}
		$w_defineUrlname = "WWW_URL_" . strtoupper($ARRAY_ShopArea[$a]["area"]);
		define($w_defineUrlname, $w_defineUrlval);
	}
	/*
	$www_url_yokohama = "http://yokohama-refle-aws.localhost/";
	$www_url_sapporo = "http://sapporo-refle-aws.localhost/";
	$www_url_sendai = "http://sendai-refle-aws.localhost/";
	$www_url_osaka = "http://osaka-refle-aws.localhost/";
	*/
	$vip_url_tokyo = "https://refle-aws.localhost/ssl/tokyo/vip/";
	$www_url_ssl_tokyo = "https://refle-aws.localhost/";
} else {
	for($a=0; $a<count($ARRAY_ShopArea); $a++) {			//in setup.php
		$w_defineUrlval = "http://" . site_SERVER_name . "." . $ARRAY_ShopArea[$a]["area"] . "-refle.com/";
		$w_defineUrlname = "WWW_URL_" . strtoupper($ARRAY_ShopArea[$a]["area"]);
		define($w_defineUrlname, $w_defineUrlval);
	}
	if(TRUE_site) {
		//----本サイト
		$vip_url_tokyo = "https://www.tokyo-refle.com/ssl/tokyo/vip/";
		$www_url_ssl_tokyo = "https://www.tokyo-refle.com/";
	} else {
		//----テストサイト
		$www_url_ssl_tokyo = SSL_sitePort .  site_SERVER_name . ".tokyo-refle.com/";
		$vip_url_tokyo = $www_url_ssl_tokyo . "ssl/tokyo/vip/";
	}
}

define("VIP_URL_TOKYO", $vip_url_tokyo);
define("WWW_URL_SSL_TOKYO", $www_url_ssl_tokyo);
/*
define("WWW_URL_TOKYO", $www_url_tokyo);
define("WWW_URL_YOKOHAMA", $www_url_yokohama);
define("WWW_URL_SAPPORO", $www_url_sapporo);
define("WWW_URL_SENDAI", $www_url_sendai);
define("WWW_URL_OSAKA", $www_url_osaka);
*/
