<?php

// 各ページ共通の処理
require_once("include/common.php");



header( "HTTP/1.1 301 Moved Permanently" );
header( "Location: ".WWW_URL );

$hotel_flg = true;

if(isset($_GET["id"])==true){
	$page_id = $_GET["id"];
}else{
	header("Location: ".$url_root);
	exit();
}

//$random_file = array();

//タイトルブロックのランダム表示を取得
//$random_file = get_random_title_block();

//無害化
$page_id = mysqli_real_escape_string($page_id, $page_i);

$sql = "select *,hotel.name as hotel_name from hotel left join ku on ku.id=hotel.ku_id where hotel.delete_flg=0 and hotel.id=".$page_id;
$res = mysqli_query($con, $sql);
if($res == false){
}
$i=0;
$row = mysqli_fetch_assoc($res);

$hotel_name = $row["hotel_name"];
$idou_time = $row["idou_time"];
$idou_cost = $row["idou_cost"];
$address = "〒".$row["post_no"]."&nbsp;".$row["address1"].$row["address2"];
$tel = $row["tel"];
$url = $row["hotel_url"];
$map_url = $row["map_url"];
$image_url = $row["image_url"];
$access = $row["access"];
$exp = $row["special"];
$ku_id = $row["ku_id"];

if($ku_id=="-1" || $ku_id==""){
	//header("Location: ".$url_root);
	header("Location: ".$url_root."404.php");
	exit();
}

//$image_url="";

if( $image_url == "" ){
	
	$image_url = $url_root."img/hotel/noimage.jpg";
	
}

$pankuzu = "　＞　".$name;

$page_title = $hotel_name." | 出張マッサージ東京リフレ";
$page_keywords = $hotel_name.",出張マッサージ,東京,リフレクソロジー,アロママッサージ,リンパマッサージ";
$page_description = $hotel_name."で出張マッサージをお探しなら【東京リフレ】高技術を持った女性セラピストが都内23区のご自宅やホテルへ出張します。";
$h1 = $hotel_name."に出張マッサージ";
$h1_p = "女性セラピストが".$hotel_name."などの東京23区のホテルやご自宅にスピード出張します。";

$params['page_title'] = $page_title;
$params['page_keywords'] = $page_keywords;
$params['page_description'] = $page_description;
$params['h1'] = $h1;
$params['h1_p'] = $h1_p;

$params['hotel_name'] = $hotel_name;
$params['idou_time'] = $idou_time;
$params['idou_cost'] = $idou_cost;
$params['address'] = $address;
$params['tel'] = $tel;
$params['url'] = $url;
$params['map_url'] = $map_url;
$params['image_url'] = $image_url;
$params['access'] = $access;
$params['exp'] = $exp;
$params['ku_id'] = $ku_id;

//$params['rand_file_1'] = $random_file["file1"];
//$params['rand_file_2'] = $random_file["file2"];
//$params['rand_file_3'] = $random_file["file3"];

$params['hotel_flg'] = $hotel_flg;
$params['page_id'] = $page_id;

$smarty->assign( 'params', $params );

if($access_type=="pc"){

	$smarty->assign( 'content_tpl', 'pc/hotel.tpl' );
	$smarty->display( 'pc/template.tpl' );

}else if($access_type=="sp"){

	$smarty->assign( 'content_tpl', 'sp/hotel.tpl' );
	$smarty->display( 'sp/template.tpl' );

}else if($access_type=="m"){

	$smarty->assign( 'content_tpl', 'm/hotel.tpl' );
	$smarty->display( 'm/template.tpl' );

}

?>