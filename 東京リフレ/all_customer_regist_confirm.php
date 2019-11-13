<?php
header("Content-type: text/html; charset=UTF-8");

include("include/common.php");
include("include/auth.php");

$onamae = $_SESSION["onamae"];
$mail = $_SESSION["mail"];
$tel = $_SESSION["tel"];
$shop_id = $_SESSION["shop_id"];

// 店の名前を取得するためのSQL文
$sql = "select name from shop where id=".$shop_id;
$res = mysql_query($sql, $con);
if($res == false){
	echo "クエリ実行に失敗しました";
	exit();
}

$row = mysql_fetch_assoc($res);

$shop_name = $row["name"];


if(isset($_POST["send"])==true){
	
	$onamae = $_SESSION["onamae"];
	$mail = $_SESSION["mail"];
	$tel = $_SESSION["tel"];
	$shop_id = $_SESSION["shop_id"];
	
	//電話番号はハイフンを外してDBに格納する
	$tel = str_replace("-","",$tel);
	
	// 会員情報を登録するSQL文
	$sql = sprintf("insert into customer(shop_id,name,mail,tel) values('%s','%s','%s','%s')",$shop_id,$onamae,$mail,$tel);
	$res = mysql_query($sql, $con);
	
	if( $res == false ){
		
		echo "クエリ実行に失敗しました";
		exit();
	
	}else{
	
		include("include/csv_update.php");
		
	}
	
	header("Location: all_customer_regist_complete.php");
	exit();
	
}else if(isset($_POST["back"])==true){
	
	header("Location: all_customer_regist_input.php?back=true");
	exit();
	
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>管理ページ・顧客新規登録確認ページ</title>
<script type="text/javascript" src="../js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="main.js"></script>

<style>

table tr td{
	padding:5px 0px 5px 5px;
}

</style>

</head>

<body>

<div style="width:800px;margin:0px auto 0px auto;">
	<div>
		<a href="index.php">トップ</a> &gt; <a href="all_customer_list.php">顧客一覧</a> &gt; 顧客新規登録入力 &gt; 顧客新規登録確認
	</div>
	<div style="padding:60px 0px 0px 0px;">
		<form action="" method="post">
			<table border="1">
				<tr>
					<td width="150">
						登録店舗
					</td>
					<td width="500">
						<?php echo $shop_name;?>
					</td>
				</tr>
				<tr>
					<td>
						顧客名
					</td>
					<td>
						<?php echo $onamae;?>
					</td>
				</tr>
				<tr>
					<td>
						メールアドレス
					</td>
					<td>
						<?php echo $mail;?>
					</td>
				</tr>
				<tr>
					<td>
						電話番号
					</td>
					<td>
						<?php echo $tel;?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="padding:10px 0px 10px 0px;text-align:center;">
							<input type="submit" value="戻る" name="back" />&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="submit" value="登録" name="send" />
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

</body>
</html>