<?php

include("include/common.php");
include("include/auth.php");


if(isset($_POST["send"])==true){
	
	$shop_id = $_POST["shop_id"];
	$onamae = $_POST["onamae"];
	$mail = $_POST["mail"];
	$tel = $_POST["tel"];
	
	$error = "";
	
	if($tel==""){
		$error .= "<li>"."電話番号が未入力です"."</li>";
	}else if (!preg_match("/^[0-9]{2,4}[-]*[0-9]{2,4}[-]*[0-9]{3,4}$/", $tel)) {
		$error .= "<li>"."電話番号の形式が不正です(半角数字とハイフンのみ)"."</li>";
	}else{
		
		//電話番号重複チェック
		$sql = "select tel from customer where delete_flag=0 and shop_id=".$shop_id;
		$res = mysqli_query($con, $sql);
		if($res == false){
			echo "クエリ実行に失敗しました。";
			exit();
		}
		$match_num = 0;
		$tel_not_haifun = str_replace("-","",$tel);
		while($row = mysqli_fetch_assoc($res)){
			$db_tel = str_replace("-","",$row["tel"]);
			if($tel_not_haifun == $db_tel){
				$match_num++;
			}
		}
		if($match_num > 0){
			$error .= "<li>"."登録済みの電話番号です"."</li>";
		}
		
	}
	
	if($onamae==""){
		$error .= "<li>"."顧客名が未入力です"."</li>";
	}
	
	if($mail==""){
		
		//$error .= "<li>"."メールアドレスが未入力です"."</li>";
		
	}else if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
		
		$error .= "<li>メールアドレスの形式が正しくありません</li>";
		
	}else{
		
		//メールアドレス重複チェック
		$sql = "select mail from customer where delete_flag=0 and shop_id=".$shop_id;
		$res = mysqli_query($con, $sql);
		if($res == false){
			echo "クエリ実行に失敗しました。";
			exit();
		}
		$match_num = 0;
		while($row = mysqli_fetch_assoc($res)){
			if($mail == $row["mail"]){
				$match_num++;
			}
		}
		if($match_num > 0){
			$error .= "<li>"."登録済みのメールアドレスです"."</li>";
		}
		
		
	}
	
	
	
	if($error != ""){
		$error = "<ul style='color:red;'>".$error."</ul>";
	}else{
		
		$_SESSION["onamae"] = $onamae;
		$_SESSION["mail"] = $mail;
		$_SESSION["tel"] = $tel;
		$_SESSION["shop_id"] = $shop_id;
		
		header('Location: all_customer_regist_confirm.php');
		exit();
		
	}
	
	
}else if(isset($_POST["back"])==true){
	
	header("Location: all_customer_list.php");
	exit();
	
}else if(isset($_GET["back"])==true){
	
	$shop_id = $_SESSION["shop_id"];
	$onamae = $_SESSION["onamae"];
	$mail = $_SESSION["mail"];
	$tel = $_SESSION["tel"];
	
}


// 店情報を取得するためのSQL文
$sql = "select * from shop where delete_flg=0";
$res = mysqli_query($con, $sql);
if($res == false){
	echo "クエリ実行に失敗しました";
	exit();
}

$shop_data = array();

// 一覧に表示される顧客データを変数に格納する処理
$i=0;
while($row = mysqli_fetch_assoc($res)){
	$shop_data[$i++] = $row;
}

$shop_data_num = count($shop_data);

/*
echo "<pre>";
print_r($tel_data);
echo "</pre>";
*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>管理ページ・顧客新規登録入力ページ</title>
<script type="text/javascript" src="../js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="main.js"></script>

<style>

table tr td{
	padding:5px 0px 5px 5px;
}

li {
	list-style-type: none;
}

</style>

</head>

<body>

<div style="width:800px;margin:0px auto 0px auto;">
	<div>
		<a href="index.php">トップ</a> &gt; <a href="all_customer_list.php">顧客一覧</a> &gt; 顧客新規登録入力
	</div>
	<div style="padding:60px 0px 0px 0px;">
		<div><?php echo $error;?></div>
		<form action="" method="post">
			<table border="1">
				<tr>
					<td width="150">
						登録店舗
					</td>
					<td width="500">
						<select name="shop_id">
							<?php
								for($i=0;$i<$shop_data_num;$i++){
							?>
									<?php if($shop_id==$shop_data[$i]['id']){?>
										<option value="<?php echo $shop_data[$i]['id'];?>" selected="selected">
									<?php }else{?>
										<option value="<?php echo $shop_data[$i]['id'];?>">
									<?php } ?>
										<?php echo $shop_data[$i]['name'];?>
									</option>
							<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						顧客名
					</td>
					<td>
						<input type="text" name="onamae" value="<?php echo $onamae;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						メールアドレス
					</td>
					<td>
						<input type="text" name="mail" value="<?php echo $mail;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号
					</td>
					<td>
						<input type="text" name="tel" value="<?php echo $tel;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="padding:10px 0px 10px 0px;text-align:center;">
							<input type="submit" value="戻る" name="back" />&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="submit" value="内容確認" name="send" />
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

</body>
</html>