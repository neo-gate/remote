<?php

include("include/common.php");
include("include/auth.php");

if(isset($_POST["edit_send"])==true){
	
	$customer_id = $_POST["customer_id"];
	
	$data = get_customer_data_one($customer_id);
	
	$shop_id = $data["shop_id"];
	$onamae = $data["name"];
	$name_kana = $data["name_kana"];
	$mail = $data["mail"];
	$tel = $data["tel"];
	$type = $data["type"];
	
	$tel_2 = $data["tel_2"];
	$tel_3 = $data["tel_3"];
	$tel_4 = $data["tel_4"];
	$tel_5 = $data["tel_5"];
	$tel_6 = $data["tel_6"];
	$tel_7 = $data["tel_7"];
	$tel_8 = $data["tel_8"];
	$tel_9 = $data["tel_9"];
	$tel_10 = $data["tel_10"];

}else if(isset($_POST["send"])==true){
	
	$shop_id = $_POST["shop_id"];
	$onamae = $_POST["onamae"];
	$name_kana = $_POST["name_kana"];
	$mail = $_POST["mail"];
	$tel = $_POST["tel"];
	$customer_id = $_POST["customer_id"];
	$type = $_POST["type"];
	
	$tel_2 = $_POST["tel_2"];
	$tel_3 = $_POST["tel_3"];
	$tel_4 = $_POST["tel_4"];
	$tel_5 = $_POST["tel_5"];
	$tel_6 = $_POST["tel_6"];
	$tel_7 = $_POST["tel_7"];
	$tel_8 = $_POST["tel_8"];
	$tel_9 = $_POST["tel_9"];
	$tel_10 = $_POST["tel_10"];
	
	$tel_data["0"] = $tel_2;
	$tel_data["1"] = $tel_3;
	$tel_data["2"] = $tel_4;
	$tel_data["3"] = $tel_5;
	$tel_data["4"] = $tel_6;
	$tel_data["5"] = $tel_7;
	$tel_data["6"] = $tel_8;
	$tel_data["7"] = $tel_9;
	$tel_data["8"] = $tel_10;
	
	$tel_data_num = count($tel_data);
	
	$error = "";
	
	if( $tel == "" ){
		$error .= "<li>"."電話番号が未入力です"."</li>";
	}else{
		$result = check_tel_keishiki($tel);
		if( $result == false ){
			$error .= "<li>"."電話番号の形式が不正です(半角数字とハイフンのみ)"."</li>";
		}else{
			$result = check_tel_duplicate_for_customer_edit($tel,$customer_id,$shop_id);
			if( $result == false ){
				$error .= "<li>"."登録済みの電話番号です"."</li>";
			}
		}
	}
	
	for( $i=0; $i<$tel_data_num; $i++ ){
		
		$tel_tmp = $tel_data[$i];
		
		if( $tel_tmp != "" ){
			
			$result = check_tel_keishiki($tel_tmp);
			if( $result == false ){
				$error .= "<li>"."電話番号の形式が不正です(半角数字とハイフンのみ)"."</li>";
			}else{
				$result = check_tel_duplicate_for_customer_edit($tel_tmp,$customer_id,$shop_id);
				if( $result == false ){
					$error .= "<li>"."登録済みの電話番号です"."</li>";
				}
			}
			
		}
		
	}
	
	$result = check_tel_duplicate_all_for_customer_edit($tel,$tel_data);
	
	if( $result == false ){
		
		$error .= "<li>"."同じ電話番号があります"."</li>";
		
	}
	
	if( $onamae == "" ){
		
		$error .= "<li>"."顧客名が未入力です"."</li>";
		
	}
	
	if( $mail != "" ){
		
		$result = check_mail_keishiki($mail);
		
		if( $result == false ){
		
			$error .= "<li>メールアドレスの形式が正しくありません</li>";
		
		}else{
			
			$result = check_mail_duplicate_for_customer_edit($mail,$customer_id,$shop_id);
			
			if( $result == false ){
			
				$error .= "<li>"."登録済みのメールアドレスです"."</li>";
			
			}
			
		}
		
	}
	
	if( $error != "" ){
		
		$error = "<ul style='color:red;'>".$error."</ul>";
		
	}else{
		
		update_customer_data_one(
$shop_id,$onamae,$mail,$tel,$customer_id,$name_kana,$tel_2,$tel_3,$tel_4,$tel_5,$tel_6,$tel_7,$tel_8,$tel_9,$tel_10,$type);
		
		header('Location: all_customer_edit_complete.php');
		exit();
		
	}
	
	
}else if( isset($_POST["back"]) == true ){
	
	header("Location: all_customer_list.php");
	exit();
	
}else{
	
	header("Location: all_customer_list.php");
	exit();
	
}

$area = "all";
$shop_data = get_shop_data($area);

$shop_data_num = count($shop_data);

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>顧客情報編集入力 | 管理ページ</title>
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

<div style="width:800px;margin:0px auto 50px auto;">
	<div>
		<a href="index.php">トップ</a> &gt; <a href="all_customer_list.php">顧客一覧</a> &gt; 顧客情報編集入力
	</div>
	<div style="padding:60px 0px 0px 0px;">
		<div><?php echo $error;?></div>
		<form action="" method="post">
			<input type="hidden" name="customer_id" value="<?php echo $customer_id;?>" />
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
						顧客名(カナ)
					</td>
					<td>
						<input type="text" name="name_kana" value="<?php echo $name_kana;?>" style="width:450px;" />
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
						会員ページ閲覧制限
					</td>
					<td>
<?php
if( $type == "11" ){
	echo '<input type="radio" name="type" value="1">なし　　';
	echo '<input type="radio" name="type" value="11" checked="checked">あり';
}else{
	echo '<input type="radio" name="type" value="1" checked="checked">なし　　';
	echo '<input type="radio" name="type" value="11">あり';
}
?>
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
					<td>
						電話番号(2)
					</td>
					<td>
						<input type="text" name="tel_2" value="<?php echo $tel_2;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号(3)
					</td>
					<td>
						<input type="text" name="tel_3" value="<?php echo $tel_3;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号(4)
					</td>
					<td>
						<input type="text" name="tel_4" value="<?php echo $tel_4;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号(5)
					</td>
					<td>
						<input type="text" name="tel_5" value="<?php echo $tel_5;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号(6)
					</td>
					<td>
						<input type="text" name="tel_6" value="<?php echo $tel_6;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号(7)
					</td>
					<td>
						<input type="text" name="tel_7" value="<?php echo $tel_7;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号(8)
					</td>
					<td>
						<input type="text" name="tel_8" value="<?php echo $tel_8;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号(9)
					</td>
					<td>
						<input type="text" name="tel_9" value="<?php echo $tel_9;?>" style="width:450px;" />
					</td>
				</tr>
				<tr>
					<td>
						電話番号(10)
					</td>
					<td>
						<input type="text" name="tel_10" value="<?php echo $tel_10;?>" style="width:450px;" />
					</td>
				</tr>
				
				<tr>
					<td colspan="2">
						<div style="padding:10px 0px 10px 0px;text-align:center;">
							<input type="submit" value="戻る" name="back" />&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="submit" value="編集" name="send" />
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

</body>
</html>
