<?php
/* =================================================================================
		Script Name	coupon.php(回数券関係)
		Author		aida
		Create Date	2018/08/28
		Update Date	----/--/--
		Description	回数券関係共通処理
================================================================================= */

//----回数券(90分券)の金額配列取得
function PHP_getPrice4Coupon90($u_shop_id = 0) {
	if($u_shop_id) {
		$sql = "select * from shop_fee where corstm=90 and shop_id=" . $u_shop_id;
	} else {
		$sql = "select * from shop_fee where corstm=90 order by shop_id";
	}
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql;
	if($res == false){
		echo "error!(get_sale_shop_record_common)";
		exit();
	}
	$i = 0;
	while($row = mysql_fetch_assoc($res)) {
		$ws_ArShopFee[$i] = $row;
		$i++;
	}

	return $ws_ArShopFee;
}

//----回数券の交通費分金額取得
function PHP_getTransportation4Coupon90() {
global $ARRAY_Transportation;

	$ws_Transportation = 0;
	$n = 0;
	foreach($ARRAY_Transportation as $w_key => $w_val) {
		if($n == 1) {
			$ws_Transportation = intval($w_key);
			break;
		}
		$n++;
	}
	return $ws_Transportation;
}
?>
