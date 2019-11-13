<?php

if(TRUE_site) {

	$result = is_ssl_common();

	if( $result == false ){

		$url = WWW_URL;

		header("Location: ".$url);
		exit();

	}

	//echo "auth_ssl";
}
?>