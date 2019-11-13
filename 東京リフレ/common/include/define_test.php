<?php

define("DB_HOST", "localhost");
define("DB_USER", "refle");
define("DB_PASS", "refle");
define("DB_NAME", "refle_test");

if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {
	
	$tokyo_refle_url = "http://refle-aws.localhost/";
	$tokyo_refle_document_root = "C:/svn/neogate/sites/aws.tokyo-refle.com";
	
} else {
	
	$tokyo_refle_url = "http://test.tokyo-refle.com/";
	//$tokyo_refle_document_root = "/home/neogate/sites/test.tokyo-refle.com";
	$tokyo_refle_document_root = "/home/neogate/sites/aws.tokyo-refle.com";
	
}

include("define_common.php");

?>