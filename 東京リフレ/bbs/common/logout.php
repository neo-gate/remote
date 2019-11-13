<?php

include("include/common.php");



// セッションクッキーを無効化
setcookie(session_name(), "", 0);

// セッションを無効化
session_destroy();

//オートログイン用クッキーの削除
setcookie("login_cookie_bbs", '', time() - 60);

// ログインページにリダイレクト
header("Location: ".WWW_URL."login.php");
exit();

?>