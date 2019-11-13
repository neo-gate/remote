<?php

$onamae = $_POST["onamae"];
$tel = $_POST["tel"];
$mail = $_POST["mail"];
$error = "";


if($onamae==""){
	$error .= "<li>"."お名前が未入力です"."</li>";
}

if($mail==""){
	$error .= "<li>"."メールアドレスが未入力です"."</li>";
}else if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
	$error .= "<li>メールアドレスの形式が正しくありません</li>";
}

if($tel==""){
	$error .= "<li>"."電話番号が未入力です"."</li>";
}

if($error != ""){
	$error = '<ul style="color:red">'.$error.'</ul>';
	echo $error;
	exit();
}else{
	
	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = "info@tokyo-refle.com";
	$title = "[東京リフレ]メールVIP会員様ご登録フォーム";
	$content = "
		東京リフレのメールVIP会員様ご登録フォームに以下の入力が有りました。\n
		-----------------------------------------------------------------\n
		お名前:".$onamae."\n
		メールアドレス:".$mail."\n
		電話番号:".$tel."\n";
	
	$result = mb_send_mail($mailto,$title,$content);
	
	if($result==false){
		echo '<span style="color:red">登録失敗しました。お手数で恐縮ですが再度の登録をお願い致します。</span>';
		exit();
	}else{
		echo '<span style="color:blue">ありがとうございます。登録完了致しました。</span>';
		exit();
	}
	
	echo '<span style="color:blue">エラーはありません。</span>';
	exit();
}




?>