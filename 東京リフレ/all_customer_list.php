<?php

header("Content-type: text/html; charset=UTF-8");

include("include/common.php");
include("include/auth.php");



$search = "";

if( isset($_GET["search"]) == true ){
	
	$search = $_GET["search"];
	
}

if(isset($_GET["shop_id"])==true && $_GET["shop_id"] != ""){
	
	$shop_id = $_GET["shop_id"];
	
	$_SESSION["shop_id"] = $shop_id;
	
}else{
	
	$_SESSION["shop_id"] = "";
	
}

$all_data_num = get_customer_num($shop_id,$search);

$paging_selected_num=1;

if(isset($_GET["paging_selected_num"])==true){

	$paging_selected_num = $_GET["paging_selected_num"];

	$start_num = (($paging_selected_num-1)*100)+1;
	$temp_num = $start_num+99;

	if($all_data_num < $temp_num){

		$end_num = $all_data_num;

	}else{

		$end_num = $temp_num;

	}

}else{

	$start_num = 1;
	$end_num = 100;

}

$paging_max_num = ceil($all_data_num/100);

$customer_data = get_customer_data($shop_id,$paging_selected_num,$search);

$customer_data_num = count($customer_data);

//echo "<pre>";
//print_r($customer_data);
//echo "</pre>";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>管理ページ・顧客一覧</title>
<script type="text/javascript" src="../js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="../js/jquery.upload-1.0.2.js"></script>
<script type="text/javascript" src="main.js"></script>
<link href="style.css" rel="stylesheet" type="text/css" />

<style>

table tr th{
	background-color:#c9caca;
	font-size:12px;
}

table tr td{
	padding:5px 0px 5px 5px;
	font-size:12px;
}

</style>

</head>
<body>

	<div style="margin:10px 30px 0px 30px;">
		<div>
			<div style="float:left;"><a href="index.php">トップ</a> &gt; 顧客一覧</div>
			<div style="float:right;"><a href="logout.php">ログアウト</a></div>
			<br style="clear:both" />
		</div>
		<div style="width:300px;margin:30px auto 0px auto;padding:5px 0px 5px 0px;background-color:#4f81bd;color:#fff;border:solid 2px #385d8a;text-align:center;" id="allcustomer_regist">
			顧客新規登録
		</div>
		<div style="padding:20px 0px 0px 200px;">
			<form action="" method="post" enctype="multipart/form-data" name="csv_upload_form">
				<div>
					<div style="float:left;padding-top:5px">
						CSVファイル選択
					</div>
					<div style="float:left">
						<input type="file" name="pic" id="pic" />
					</div>
					<div style="float:left">
						<input type="button" value="アップロード" name="send" onclick="csvUpload();" />
					</div>
					<div style="float:left;padding-top:5px;" id="action_result">
						&nbsp;
					</div>
					<br style="clear:both" />
				</div>
			</form>
			<div style="font-weight:bold;">
				※ログインは不要になりました。アップロードと同時に反映されます。
			</div>
		</div>
		<div style="padding:20px 0px 0px 0px;">
			<div id="all_customer_list_menu">
				<a href="all_customer_list.php">全店舗</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="all_customer_list.php?shop_id=1">東京リフレ</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="all_customer_list.php?shop_id=2">東京アロマ</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="all_customer_list.php?shop_id=4">リンパマッサージ東京</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="all_customer_list.php?shop_id=7">横浜リフレ</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="all_customer_list.php?shop_id=6">札幌リフレ</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="all_customer_list.php?shop_id=8">仙台リフレ</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="all_customer_list.php?shop_id=10">大阪リフレ</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			</div>
			<div style="padding:10px 0px 0px 0px;">
				<div style="float:left;">
					該当件数：<?php echo $all_data_num;?>件(<?php echo $start_num;?>～<?php echo $end_num;?>件)
				</div>
				<div style="float:right">
					
				<?php
					if($paging_max_num != 0){
						if($paging_max_num == 1){
						}else{
							if($paging_selected_num != 1){
				
				?>
								<span class="paging_num_forward"><a href="all_customer_list.php?shop_id=<?php echo ($shop_id);?>&paging_selected_num=<?php echo ($paging_selected_num-1);?>&search=<?php echo $search;?>">前へ</a></span>
								<span class="paging_num_box"><a href="all_customer_list.php?shop_id=<?php echo ($shop_id);?>&paging_selected_num=1&search=<?php echo $search;?>">1</a></span>
				<?php
							}else{
				?>			
								<span class="selected_paging_num_box"><a href="all_customer_list.php?shop_id=<?php echo ($shop_id);?>&paging_selected_num=1&search=<?php echo $search;?>">1</a></span>
				<?php
							}
							
							
							
							if(($paging_selected_num-2) > 2){
				?>			
							
								<span class="paging_num_dod">...</span>
				<?php				
							}
							
							
							for($i=2;$i<$paging_max_num;$i++){
							
								if((($paging_selected_num-2) > $i) || (($paging_selected_num+2) < $i)){
								
								}else{
									
									if($paging_selected_num == $i){
				?>					
										<span class="selected_paging_num_box"><a href="all_customer_list.php?shop_id=<?php echo ($shop_id);?>&paging_selected_num=<?php echo $i;?>&search=<?php echo $search;?>"><?php echo $i;?></a></span>
				<?php					
									}else{
				?>					
										<span class="paging_num_box"><a href="all_customer_list.php?shop_id=<?php echo ($shop_id);?>&paging_selected_num=<?php echo $i;?>&search=<?php echo $search;?>"><?php echo $i;?></a></span>
				<?php						
									}
								
								}
							
							
							
							}
														
							if($paging_selected_num+3 < $paging_max_num){
				?>			
								<span class="paging_num_dod">...</span>
				<?php				
							}
														
							if($paging_max_num != 1){
							
								if($paging_selected_num == $paging_max_num){
				?>				
									<span class="selected_paging_num_box"><a href="all_customer_list.php?shop_id=<?php echo ($shop_id);?>&paging_selected_num=<?php echo $paging_max_num;?>&search=<?php echo $search;?>"><?php echo $paging_max_num;?></a></span>
								
				<?php				
								}else{
				?>				
									<span class="paging_num_box"><a href="all_customer_list.php?shop_id=<?php echo ($shop_id);?>&paging_selected_num=<?php echo $paging_max_num;?>&search=<?php echo $search;?>"><?php echo $paging_max_num;?></a></span>
									<span class="paging_num_next"><a href="all_customer_list.php?shop_id=<?php echo ($shop_id);?>&paging_selected_num=<?php echo ($paging_selected_num+1);?>&search=<?php echo $search;?>">次へ</a></span>
				<?php				
								}
							
							}
					
						}
					}
				?>	
					
				</div>
				<br style="clear:both" />
			</div>
			
			<div>
				<form action="" method="get">
					<input type="hidden" name="shop_id" value="<?php echo $shop_id;?>" />
					<input type="text" name="search" value="<?php echo $search;?>" />
					<input type="submit" name="send" value="検索" />
					<span style="font-size:10px;">※電話番号、顧客名</span>
				</form>
			</div>
			
			<div style="padding:10px 0px 100px 0px;">
				<table border="1">
					<tr>
						<th width="150" align="left">店舗名</th>
						<th width="150" align="left">顧客名</th>
						<th width="200" align="left">メールアドレス</th>
						<th width="120" align="left">電話番号</th>
						<th width="60" align="left">会員ページ閲覧制限</th>
						<th width="60" align="left">&nbsp;</th>
						<th width="60" align="left">&nbsp;</th>
					</tr>
					<?php
						for($i=0;$i<$customer_data_num;$i++){
							
							$tel_data = $customer_data[$i]['tel_data'];
							$tel_data_num = count($tel_data);
							
					?>
							<tr>
								<form action="all_customer_edit_input.php" method="post" name="allcustomer_edit_form">
									<input type="hidden" name="customer_id" value="<?php echo $customer_data[$i]['customer_id'];?>" />
									<td>
										<?php echo $customer_data[$i]['shop_name'];?></td>
										<td>
											<?php echo $customer_data[$i]['customer_name'];?><br />
											(<?php echo $customer_data[$i]['customer_name_kana'];?>)
										</td>
										<td><a href="mailto:<?php echo $customer_data[$i]['customer_mail'];?>"><?php echo $customer_data[$i]['customer_mail'];?></a></td>
										<td>
<?php
for( $x=0; $x<$tel_data_num; $x++ ){
	
	echo $tel_data[$x];
	echo "<br />";
	
}
?>
										</td>
										<td>
<?php
if( $customer_data[$i]['type'] == "11" ){
	echo "あり";
}else{
	echo "なし";
}
?>
										</td>
										<td><input type="submit" value="編集" name="edit_send" /></td>
										<td><input type="button" value="削除" onclick="all_customer_delete('<?php echo $customer_data[$i]['customer_id'];?>','<?php echo $customer_data[$i]['shop_id'];?>');" />
									</td>
								</form>
							</tr>
					<?php
						}
					?>
					
					
				</table>
			</div>
		</div>
	</div>

</body>
</html>