<?php

	session_start();
	
	
	
	$document_root = $_SERVER['DOCUMENT_ROOT'];
	define("ROOT_PATH", $document_root."/");
	
	$domain = $_SERVER["SERVER_NAME"];
	$url_root = "http://".$domain."/";
	define("WWW_URL", $url_root);
	
	
	
	$type = $_POST['type'];
	$temp_pic_url = null;
	
	$year_month = $_SESSION["year_month"];
	
	$file_name = $_FILES["pic"]["name"];
	
	if(preg_match('/\.gif$|\.png$|\.jpg$|\.jpeg$|\.bmp$/i', $file_name)){
		
		$_SESSION["last_uploaded_file"] = $file_name;
		
		if($type=='0'){
		
			$temp_pic_url = $_FILES["pic"]["tmp_name"];
			$pic_name = $file_name;
			
			$pic_url = ROOT_PATH."img/upload/".$year_month."/".$pic_name;
			
			if(move_uploaded_file($temp_pic_url,$pic_url)){
				
				$_SESSION["selected_folder_name"] = $year_month;
			
				echo 'success';
				exit;
			
			}else{
				
				echo 'failure';
				exit;
				
			}
		
		}
		
	}else{
		
		echo 'extension_error';
		exit;
		
	}
	
?>