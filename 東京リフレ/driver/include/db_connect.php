<?php

	$con = mysql_connect(DB_HOST, DB_USER, DB_PASS);
	mysql_select_db(DB_NAME, $con);
	mysql_query("SET NAMES utf8");

?>