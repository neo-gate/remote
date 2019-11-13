<?php

	// 各ページ共通の処理
	require_once("include/common.php");
	
	if(isset($_GET["id"])==true){
		$page_id = $_GET["id"];
	}else{
		header("Location: ".$url_root);
		exit();
	}
	
	//無害化
	$page_id = mysql_real_escape_string($page_id);
	
	$sql = "select * from ku where id=".$page_id;
	$res = mysql_query($sql, $con);
	if($res == false){
	}
	$i=0;
	$row = mysql_fetch_assoc($res);
	
	$ku_name = $row["name"];
	$idou_time = $row["idou_time"];
	$idou_cost = $row["idou_cost"];
	
	//ホテルデータ
	$sql = "select * from hotel where delete_flg=0 and ku_id=".$page_id;
	$res = mysql_query($sql, $con);
	if($res == false){
	}
	$i=0;
	$hotel_data = array();
	while($row = mysql_fetch_assoc($res)){
		$hotel_data[$i++] = $row;
	}
	
	//区の駅
	$sql = "select * from station where delete_flg=0 and ku_id=".$page_id;
	$res = mysql_query($sql, $con);
	if($res == false){
	}
	$i=0;
	$station_data = array();
	while($row = mysql_fetch_assoc($res)){
		
		$name = $row["name"];
		$url_name = $row["url_name"];
		
		if($url_name == ""){
			$url_name = $row["id"];
		}
		
		$station_data[$i]["name"] = $name;
		$station_data[$i]["url_name"] = $url_name;
		
		$i++;
		
	}
	
	if($access_type=="pc"){
		
		$site_type = "tokyo-refle";
		
		//追記データ取得
		$add_data = get_ku_add_data_ja($site_type,$page_id);
		$params['add_data'] = $add_data;
		
	}
	
	$page_title = $ku_name." | 出張マッサージ東京リフレ";
	$page_keywords = $ku_name.",出張マッサージ,東京,リフレクソロジー,アロママッサージ,リンパマッサージ";
	$page_description = $ku_name."で出張マッサージをお探しなら【東京リフレ】高技術を持った女性セラピストが都内23区のご自宅やホテルへ出張します。";
	
	$h1 = $ku_name."に出張マッサージ";
	$h1_p = "女性セラピストが".$ku_name."などの東京23区にスピード出張します。";
	
	$params['page_title'] = $page_title;
	$params['page_keywords'] = $page_keywords;
	$params['page_description'] = $page_description;
	
	$params['h1'] = $h1;
	$params['h1_p'] = $h1_p;
	
	$params['ku_name'] = $ku_name;
	$params['idou_time'] = $idou_time;
	$params['idou_cost'] = $idou_cost;
	$params['hotel_data'] = $hotel_data;
	$params['station_data'] = $station_data;
	
	$smarty->assign( 'params', $params );
	
	if($access_type=="pc"){
	
		$smarty->assign( 'content_tpl', 'pc/area.tpl' );
		$smarty->display( 'pc/template.tpl' );
	
	}else if($access_type=="sp"){
	
		$smarty->assign( 'content_tpl', 'sp/area.tpl' );
		$smarty->display( 'sp/template.tpl' );
	
	}else if($access_type=="m"){
	
		$smarty->assign( 'content_tpl', 'm/area.tpl' );
		$smarty->display( 'm/template.tpl' );
	
	}

?>