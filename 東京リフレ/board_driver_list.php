<?php

include("include/common.php");

$id = $_GET["id"];

$type = "";
$reservation_for_board_id = "";

if (preg_match("/mukae_driver_/",$id)) {
		
	$type = "mukae";
	$reservation_for_board_id = trim(str_replace("mukae_driver_", "", $id));
		
}else if (preg_match("/okuri_driver_/",$id)) {
		
	$type = "okuri";
	$reservation_for_board_id = trim(str_replace("okuri_driver_", "", $id));
	
}else{
	
	echo "error!";
	exit();
	
}

if( $reservation_for_board_id != "" ){
	
	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);
	
	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];
	$shop_area = $data["shop_area"];
	$okuri_driver_id = $data["okuri_driver_id"];
	$mukae_driver_id = $data["mukae_driver_id"];
	
	$driver_data = get_driver_data_for_board($year,$month,$day,$shop_area);
	
	$driver_data_num = count($driver_data);
	
	if( $type == "mukae" ){
		
		$recent_driver_name = get_driver_name_for_board($mukae_driver_id);
		$recent_driver_id = $mukae_driver_id;
		
	}else if( $type == "okuri" ){
		
		$recent_driver_name = get_driver_name_for_board($okuri_driver_id);
		$recent_driver_id = $okuri_driver_id;
		
	}else{
		
		echo "error!";
		exit();
		
	}
	
}else{
	
	echo "error!";
	exit();
	
}

/*
echo "<pre>";
print_r($driver_data);
echo "</pre>";
*/

?>

<!DOCTYPE html>
<html lang="ja">
<head>
		
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
		
<title>状況ボード・ドライバーリスト</title>
<meta name="Keywords" content="状況ボード・ドライバーリスト" />
<meta name="Description" content="状況ボード・ドライバーリスト" />

<meta name="robots" content="noindex,nofollow" />

<script src="js/jquery-1.10.2.js"></script>

<script>

var url_root = "http://"+location.hostname+"/";

function reservation_driver_set(driver_id,reservation_for_board_id,type,area){

	if(window.confirm('セットしてよろしいですか？')){

		var indicator = '<div><img src="'+url_root+'img/indicator.gif" width="60" /></div>';
		$("#board_driver_list_wrapper").html(indicator);
		
		$.ajax({
			type:'post',
			url:'ajax/okuri_mukae_driver_set.php',
			data:{
				'driver_id':driver_id,
				'reservation_id':reservation_for_board_id,
				'type':type,
				'driver_area':area
			},
			success:function(data){

				//親ウィンドウリロード
				window.opener.location.reload();
				window.close();
				
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){

				var message = "error!";
				$("#board_driver_list_wrapper").html(message);
				
			}
		});
		
	}

}

function reservation_driver_delete(reservation_for_board_id,type){

	if(window.confirm('解除してよろしいですか？')){

		var indicator = '<div><img src="'+url_root+'img/indicator.gif" width="60" /></div>';
		$("#board_driver_list_wrapper").html(indicator);
		
		$.ajax({
			type:'post',
			url:'ajax/okuri_mukae_driver_delete.php',
			data:{
				'reservation_id':reservation_for_board_id,
				'type':type
			},
			success:function(data){

				//親ウィンドウリロード
				window.opener.location.reload();
				window.close();
				
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){

				var message = "error!";
				$("#board_driver_list_wrapper").html(message);
				
			}
		});
		
	}
	
}

</script>

</head>
<body>

<div id="board_driver_list_wrapper">

<form>

<div style="padding:0px 0px 20px 0px;">
	<div>
		現在のドライバー
	</div>
	<div style="padding:10px 0px 0px 0px;">
		<div style="float:left;font-size:10px;padding:10px 0px 0px 0px;">
			<?php echo $recent_driver_name;?>
		</div>
		<div style="float:left;font-size:10px;padding:0px 0px 0px 10px;">
<?php if( $recent_driver_name != "なし" ){ ?>
			<input type="button" value="解除" style="padding:5px" onclick="reservation_driver_delete('<?php echo $reservation_for_board_id;?>','<?php echo $type;?>');" />
<?php } ?>
		</div>
		<br style="clear:both;">
	</div>
</div>

<div>ドライバーリスト</div>
<div>
<div>
<?php
for($i=0;$i<$driver_data_num;$i++){
	$driver_id = $driver_data[$i]["id"];
	$driver_name = $driver_data[$i]["name"];
?>

<div style="padding:10px 0px 0px 0px;">
<input type="button" value="<?php echo $driver_name;?>" style="padding:5px" onclick="reservation_driver_set('<?php echo $driver_id;?>','<?php echo $reservation_for_board_id;?>','<?php echo $type;?>','<?php echo $shop_area;?>');" />
</div>

<?php
}
?>

<div style="padding:10px 0px 0px 0px;">
<input type="button" value="TAXI" style="padding:5px" onclick="reservation_driver_set('-2','<?php echo $reservation_for_board_id;?>','<?php echo $type;?>','<?php echo $shop_area;?>');" />
</div>

<div style="padding:10px 0px 0px 0px;">
<input type="button" value="本部" style="padding:5px" onclick="reservation_driver_set('-3','<?php echo $reservation_for_board_id;?>','<?php echo $type;?>','<?php echo $shop_area;?>');" />
</div>

</div>
<div style="padding:20px 0px 50px 0px;">
<input type="button" value="閉じる" style="padding:5px" onclick="window.close();" />
</div>
</div>

</form>

</div>

</body>
</html>


