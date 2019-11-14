<?php

	$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	mysqli_select_db($con, DB_NAME);
	mysqli_query($con, "SET NAMES utf8");

?>