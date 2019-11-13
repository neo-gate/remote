<?php

	$tmp = str_replace("index.php","",$_SERVER['HTTP_REFERER']);

	if( VIP_LOGIN_PAGE != $tmp ){

		// ログイン状態かどうかのチェック
		if ( $_SESSION["ticket"] != VIP_TICKET_WORD.$_SESSION["customer_name"] ){
			
			/*
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
			echo "login-error!";echo "<br />";
			echo "HTTP_REFERER:".$_SERVER['HTTP_REFERER'];echo "<br />";
			echo "VIP_LOGIN_PAGE:".VIP_LOGIN_PAGE;echo "<br />";
			echo "ticket:".$_SESSION["ticket"];echo "<br />";
			echo "VIP_TICKET_WORD:".VIP_TICKET_WORD;echo "<br />";
			echo "customer_name:".$_SESSION["customer_name"];echo "<br />";
			exit();
			*/
			
			echo $tmp;
			echo "<br />";
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
			exit();
	
			// セッションクッキーを無効化
			setcookie(session_name(), "", 0);
			
			// セッションを無効化
			session_destroy();
			
			// ログインページにリダイレクト
			header("Location: ".VIP_LOGIN_PAGE);
			exit();
			
		}
	
	}
	
?>