<?php
	// ログイン状態かどうかのチェック
	if ($_SESSION["ticket"] != VIP_TICKET_WORD.$_SESSION["customer_name"]) {

		// セッションクッキーを無効化
		setcookie(session_name(), "", 0);
		// セッションを無効化
		session_destroy();
		// ログインページにリダイレクト
		header("Location: ".$url_root."vip/index.php");
		exit();
		
	}
?>