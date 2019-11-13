<?php

include("include/common.php");
include("include/auth.php");

$error_update = "";
$error_add = "";

$shop_id = "1";
$type = "course";

$discount_course_int_add = "-1";
$discount_type_add = "1";

if( isset($_POST["send_update"]) == true ){
	
	$shop_id = $_POST["shop_id"];
	$data_id = $_POST["data_id"];
	$type = $_POST["type"];
	$name = $_POST["name"];
	$order_num = $_POST["order_num"];
	$discount_value = $_POST["discount_value"];
	
	$course_int = $_POST["course_int"];
	$kihon_price = $_POST["kihon_price"];
	
	$discount_course_int = $_POST["discount_course_int"];
	$discount_type = $_POST["discount_type"];
	
	update_course_and_discount(
$data_id,$type,$name,$order_num,$discount_value,$course_int,$kihon_price,$discount_course_int,$discount_type);
	
	$error_update = '<div style="color:blue;">更新しました</div>';
	
}else if( isset($_POST["send_delete"]) == true ){
	
	$shop_id = $_POST["shop_id"];
	$data_id = $_POST["data_id"];
	$type = $_POST["type"];
	
	delete_course_and_discount($data_id,$type);
	
	$error_update = '<div style="color:blue;">削除しました</div>';
	
}else if( isset($_POST["send_add"]) == true ){
	
	$shop_id = $_POST["shop_id"];
	$type = $_POST["type"];
	$name_add = $_POST["name"];
	$order_num_add = $_POST["order_num"];
	$discount_value_add = $_POST["discount_value"];
	
	$course_int_add = $_POST["course_int"];
	$kihon_price_add = $_POST["kihon_price"];
	
	$discount_course_int_add = $_POST["discount_course_int"];
	$discount_type_add = $_POST["discount_type"];
	
	if( $name_add == "" ){
		
		$error_add = '<div style="color:red;">未入力です</div>';
		
	}
	
	if( $error_add == "" ){
	
		insert_course_and_discount($shop_id,$type,$name_add,$order_num_add,$discount_value_add,$course_int_add,
$kihon_price_add,$discount_course_int_add,$discount_type_add);
		
		$error_add = '<div style="color:blue;">追加しました</div>';
		
		$name_add = "";
		$order_num_add = "";
		$discount_value_add = "";
		
		$course_int_add = "";
		$kihon_price_add = "";
		
		$discount_course_int_add = "-1";
		$discount_type_add = "1";
	
	}
	
}else{
	
	if( isset($_GET["id"]) == true ){
		
		$shop_id = $_GET["id"];
		
		if( isset($_GET["type"]) == true ){
		
			$type = $_GET["type"];
		
		}
		
	}
	
}

$area = "all";
$shop_data = get_shop_data($area);
$shop_data_num = count($shop_data);

$list_data = get_course_or_discount($shop_id,$type);
$list_data_num = count($list_data);

$shop_name = get_shop_name_by_shop_id($shop_id);

if($type == "course"){
	$type_name = "コース";
}else{
	$type_name = "割引";
}

/*
echo "<pre>";
print_r($list_data);
echo "</pre>";
exit();
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>管理ページ・コース、割引</title>
<script type="text/javascript" src="../js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="main.js"></script>
<link href="style.css" rel="stylesheet" type="text/css" />

<style>

.td_padding{

	padding:10px;
	
}

</style>

</head>

<body>

<div style="width:1200px;margin:0px auto 0px auto;">
	<div>
		<a href="index.php">管理トップ</a> &gt; <a href="page_edit.php">ページ編集</a> &gt; コース、割引
	</div>
	<div style="padding:0px 0px 100px 0px;">
	
		<?php include("include/page_edit_menu.php");?>
	
		<div>
<?php

$html = "";

for($i=0;$i<$shop_data_num;$i++){
	
	$shop_id_tmp = $shop_data[$i]["id"];
	$shop_name_tmp = $shop_data[$i]["name"];
	
$html .=<<<EOT
<a href="course_and_discount.php?id={$shop_id_tmp}">{$shop_name_tmp}</a>　
EOT;

}

echo $html;

?>
		</div>
		
		<div style="padding:10px 0px 0px 0px;">
			<a href="course_and_discount.php?id=<?php echo $shop_id;?>&type=course">コース</a>　
			<a href="course_and_discount.php?id=<?php echo $shop_id;?>&type=discount">割引</a>
		</div>
		
		<div style="padding:30px 0px 0px 0px;">
			<div><?php echo $error_add;?></div>
			<div><?php echo $shop_name;?>(<?php echo $type_name;?>)</div>
			<form action="" method="post">
				<input type="hidden" name="shop_id" value="<?php echo $shop_id;?>" />
				<input type="hidden" name="type" value="<?php echo $type;?>" />
				<table border="1">
					
					<tr>
					<th>名前</th>
					
<?php
if($type == "course"){
	echo '<th>分</th>';
	echo '<th>基本料金</th>';
	echo '<th>表示順</th>';
}else{
	echo '<th>割引金額</th>';
	echo '<th>コース長さ</th>';
	echo '<th>タイプ</th>';
}
?>
					
					<th>&nbsp;</th>
					</tr>
					
					<tr>
						<td class="td_padding">
							<input type="text" name="name" value="<?php echo $name_add;?>" style="width:240px;" />
						</td>
						
<?php
if($type == "course"){
	echo '<td class="td_padding"><input type="text" name="course_int" value="'.$course_int_add.'" style="width:60px;" />分</td>';
	echo '<td class="td_padding"><input type="text" name="kihon_price" value="'.$kihon_price_add.'" style="width:100px;" />円</td>';
	echo '<td class="td_padding"><input type="text" name="order_num" value="'.$order_num_add.'" style="width:60px;" /></td>';
}else{
	echo '<td class="td_padding"><input type="text" name="discount_value" value="'.$discount_value_add.'" style="width:60px;" />円</td>';
	echo '<td class="td_padding"><input type="text" name="discount_course_int" value="'.$discount_course_int_add.'" style="width:60px;" /></td>';
	echo '<td class="td_padding"><input type="text" name="discount_type" value="'.$discount_type_add.'" style="width:60px;" /></td>';
}
?>
						
						<td class="td_padding">
							<input type="submit" name="send_add" value="追加" style="padding:5px;" />
						</td>
					</tr>
				</table>
			</form>
		</div>
		
		<div style="padding:30px 0px 0px 0px;">

<div><?php echo $error_update;?></div>
<div><?php echo $shop_name;?>(<?php echo $type_name;?>)</div>
<table border="1">

<tr>
<th>名前</th>

<?php
if($type == "course"){
	echo '<th>分</th>';
	echo '<th>基本料金</th>';
	echo '<th>表示順</th>';
}else{
	echo '<th>割引金額</th>';
	echo '<th>コース長さ</th>';
	echo '<th>タイプ</th>';
}
?>

<th>&nbsp;</th>
<th>&nbsp;</th>
</tr>

<?php
for($i=0;$i<$list_data_num;$i++){
	$data_id = $list_data[$i]["id"];
	$shop_id = $list_data[$i]["shop_id"];
	$name = $list_data[$i]["name"];
	$order_num = $list_data[$i]["order_num"];
	$discount_value = $list_data[$i]["discount_value"];
	
	$course_int = $list_data[$i]["course_int"];
	$kihon_price = $list_data[$i]["kihon_price"];
	
	$discount_course_int = $list_data[$i]["course_int"];
	$discount_type = $list_data[$i]["type"];
	
?>

<form action="" method="post" id="course_and_discount_update_form_<?php echo $data_id;?>">
<input type="hidden" name="data_id" value="<?php echo $data_id;?>" />
<input type="hidden" name="shop_id" value="<?php echo $shop_id;?>" />
<input type="hidden" name="type" value="<?php echo $type;?>" />
<tr>
<td class="td_padding">
<input type="text" name="name" value="<?php echo $name;?>" style="width:240px;" />
</td>

<?php
if($type == "course"){
	echo '<td class="td_padding"><input type="text" name="course_int" value="'.$course_int.'" style="width:60px;" />分</td>';
	echo '<td class="td_padding"><input type="text" name="kihon_price" value="'.$kihon_price.'" style="width:100px;" />円</td>';
	echo '<td class="td_padding"><input type="text" name="order_num" value="'.$order_num.'" style="width:60px;" /></td>';
}else{
	echo '<td class="td_padding"><input type="text" name="discount_value" value="'.$discount_value.'" style="width:60px;" />円</td>';
	echo '<td class="td_padding"><input type="text" name="discount_course_int" value="'.$discount_course_int.'" style="width:280px;" /></td>';
	echo '<td class="td_padding"><input type="text" name="discount_type" value="'.$discount_type.'" style="width:60px;" /></td>';
}
?>

<td class="td_padding">
<input type="submit" name="send_update" value="更新" style="padding:5px;" />
</td>

<td class="td_padding">
<input type="button" name="send_delete" value="削除" style="padding:5px;" onclick="delete_course_and_discount('<?php echo $data_id;?>');" />
</td>

</tr>
</form>
	
<?php
}
?>

</table>

		</div>
		
		
		
		

	</div>
</div>

</body>
</html>