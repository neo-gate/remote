<?php

if(TRUE_site) {
	$result = is_ssl_common();

	if( $result == false ){

		header("Location: ".$site_url);
		exit();

	}

	//echo "auth_ssl";
}
?>