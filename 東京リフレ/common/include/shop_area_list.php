<?php
/* =================================================================================
		Script Name	shop_area_list.php( エリア店舗関係 )
		Update Date	2018/02/22	全面的に整理 by aida
================================================================================= */

//echo "shop_area_list.php line:" . __LINE__ . "<br />";
$ARRAY_Pref = array(
	"北海道",
	"青森県","岩手県","宮城県","秋田県","山形県","福島県",
	"茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
	"新潟県",
	"富山県","石川県","福井県",
	"山梨県",
	"長野県","岐阜県","静岡県","愛知県","三重県",
	"滋賀県","京都府","大阪府","兵庫県","奈良県","和歌山県",
	"鳥取県","島根県","岡山県","広島県","山口県",
	"徳島県","香川県","愛媛県","高知県",
	"福岡県","佐賀県","長崎県","熊本県","大分県","宮崎県","鹿児島県",
	"沖縄県"
);

$ARRAY_areaKu = array(
	"tokyo" => array("千代田区", "中央区", "港区", "品川区", "目黒区", "渋谷区", "新宿区", "豊島区", "文京区", "台東区", "墨田区","世田谷区", "中野区", "荒川区", "江東区","大田区", "杉並区", "練馬区", "板橋区", "北区", "足立区", "葛飾区", "江戸川区"),
	"sapporo" => array("中央区", "北区", "東区", "豊平区", "西区"),
	"yokohama" => array("青葉区","旭区", "泉区", "磯子区", "神奈川区", "金沢区","川崎区","港南区","港北区","幸区","栄区","瀬谷区","都筑区","中原区","鶴見区","戸塚区","中区","西区","保土ヶ谷区","緑区","南区"),
	"fukuoka" => array("中央区", "博多区", "南区", "城南区")
);

//----店舗クラス
class Shop {

	var $ArRec;
	var $ArIds;
	var $nums;
	var $ArArea;
	var $ArAreaNums;
	var $ArList;
	var $ArListNums;
	var $ArGpList;
	var $ArGpListNums;

	function set($u_ArShopArea, $u_ym = 0) {

		$this->ArArea = $u_ArShopArea;
		$this->ArAreaNums = count($this->ArArea);

		if($u_ym) {
			$sql = "select * from shop where delete_flg=0";
			$sql .= " and (closym=0 or closym>=" . $u_ym . ") and openym<=" . $u_ym;
			$sql .= " order by dsordr,id";
		} else {
			$sql = "select * from shop where delete_flg=0 order by dsordr,id";
		}
		$res = mysql_query($sql, DbCon);
		if($res == false) {
			return;
		}
		$ws_strHistb = "";
		$n = 0;

		while($ws_Row = mysql_fetch_assoc($res)) {
			$this->ArRec[$n] = $ws_Row;
			$this->ArIds[$n] = $ws_Row["id"];
			if($shop_name == "lymph_tokyo") {
				$this->ArRec[$n]["short_name"] = "リンパ東京";
			} elseif($shop_name == "osaka_aroma") {
				$this->ArRec[$n]["short_name"] = "大阪AS";
			} else {
				$this->ArRec[$n]["short_name"] = $ws_Row["name"];
			}
			if(!$this->ArRec[$n]["bgcolr"]) $this->ArRec[$n]["bgcolr"] = "ffffff";
			if(!$this->ArRec[$n]["ftcolr"]) $this->ArRec[$n]["ftcolr"] = "000000";
			$this->ArRec[$n]["area_name"] = "";
			$this->ArRec[$n]["ex_name"] = "";
			for($i=0; $i<$this->ArAreaNums; $i++) {
				if($this->ArArea[$i]["area"] == $this->ArRec[$n]["area"]) {
					$this->ArRec[$n]["area_name"] = $this->ArArea[$i]["area_name"];
					$this->ArRec[$n]["ex_name"] = $this->ArArea[$i]["ex_name"];
					break;
				}
			}
			if($u_ym) {
				if($this->ArRec[$n]["raitym"] > $u_ym) {
					if($ws_strHistb) $ws_strHistb .= ",";
					$ws_strHistb .= $this->ArRec[$n]["id"];
				}
			}
			$n++;
		}
		$this->nums = $n;

		if($u_ym && $ws_strHistb) {
			//----指定年月時の店舗係数履歴で上書き
			$sql = "select A.* from shop_rait A";
			$sql .= " left outer join (select shop_id,max(raitym) as YM from shop_rait where shop_id in(" . $ws_strHistb . ") and raitym<=" . $u_ym . " group by shop_id) B on A.shop_id=B.shop_id and A.raitym=B.YM";
			$sql .= " where A.shop_id in(" . $ws_strHistb . ") order by A.shop_id";
			$res = mysql_query($sql, DbCon);
			if($res == false) {
				return;
			}
			while($ws_Row = mysql_fetch_assoc($res)) {
				$n = array_search($ws_Row["shop_id"], $this->ArIds);
				if($n === false) continue;
				$this->ArRec[$n]["ptstb1"] = $ws_Row["ptstb1"];
				$this->ArRec[$n]["ptstb2"] = $ws_Row["ptstb2"];
				$this->ArRec[$n]["shrtb1"] = $ws_Row["shrtb1"];
				$this->ArRec[$n]["shrtb2"] = $ws_Row["shrtb2"];
				$this->ArRec[$n]["lwgrnt"] = $ws_Row["lwgrnt"];
				$this->ArRec[$n]["extnsn"] = $ws_Row["extnsn"];
				$this->ArRec[$n]["bosrat"] = $ws_Row["bosrat"];
				$this->ArRec[$n]["simeik"] = $ws_Row["simeik"];
				$this->ArRec[$n]["insntv"] = $ws_Row["insntv"];
			}
		}
	}

	//----店舗情報配列取得
	function getArData() {
		return $this->ArRec;
	}

	//----店舗ID配列取得
	function getArIds() {
		return $this->ArIds;
	}

	//----店舗配列データ数取得
	function getNums() {
		return $this->nums;
	}

	//----店舗配列Index取得
	function getIndex($u_id) {
		return array_search($u_id, $this->ArIds);
	}

	//----店舗配列Index取得
	function getIndexByName($u_name) {
		for($n=0; $n<$this->nums; $n++) {
			if($this->ArRec[$n]["name"] == $u_name) {
				return $n;
			}
		}
		return -1;
	}

	//----指定店舗情報取得
	function getData($u_id) {
		$ws_Idx = $this->getIndex($u_id);
		if($ws_Idx === false) {
			return false;
		} else {
			return $this->ArRec[$n];
		}
	}

	//----指定店舗名取得
	function getName($u_id) {
		$ws_Idx = $this->getIndex($u_id);
		if($ws_Idx === false) {
			return "";
		} else {
			return $this->ArRec[$n]["name"];
		}
	}
	//----指定店舗名取得(エリアより)
	function getNameByArea($u_area) {
		for($n=0; $n<$this->nums; $n++) {
			if($this->ArRec[$n]["area"] == $u_area) {
				return $this->ArRec[$n]["name"];
				break;
			}
		}
		return "";
	}
	//----指定店舗データ取得(エリアより)
	function getDataByArea($u_area) {
		for($n=0; $n<$this->nums; $n++) {
			if($this->ArRec[$n]["area"] == $u_area) {
				return $this->ArRec[$n];
				break;
			}
		}
		return "";
	}

	//----メール会員管理対象店舗配列取得
	function getShopMailList() {
		$i = 0;
		for($n=0; $n<$this->nums; $n++) {
			if($this->ArRec[$n]["mailfg"]) {
				$ws_ArRec[$i] = $this->ArRec[$n];
				$i++;
			}
		}
		return $ws_ArRec;
	}

	//----店舗リスト取得
	function getShopList($u_ym = 0) {

		$this->ArList = array();
		$i = 0;

		for($n=0; $n<$this->nums; $n++) {
			if($this->ArRec[$n]["delete_flg"] == 1) continue;
			if($u_ym > 0) {
				if( !(($this->ArRec[$n]["closym"] == 0 || $this->ArRec[$n]["closym"] >= $u_ym) && $this->ArRec[$n]["openym"] <= $u_ym ) ) continue;
			}

			$short_name_S = $this->ArRec[$n]["area_name"];

			switch($this->ArRec[$n]["id"]) {
			case "2":
			case "11":
				$name = $this->ArRec[$n]["area"] . "_aroma";
				break;
			case "4":
				$name = "lymph_tokyo";
				break;
			case "12":
			case "13":
				//$short_name_S = str_replace("東京", "", $this->ArRec[$n]["name"]);
				$short_name_S = $this->ArRec[$n]["name"];
				$name = $this->ArRec[$n]["area"];
				break;
			case "14":
				$name = $this->ArRec[$n]["area"] . "_refle";
				$short_name = $this->ArRec[$n]["name"];
				$short_name_S = "横浜新規";
				break;
			case "15":
				$name = $this->ArRec[$n]["area"] . "_new";
				$short_name = "リフレッシュ横浜";
				$short_name_S = "横浜新人";
				break;
			case "16":
				$name = $this->ArRec[$n]["area"];
				$short_name_S = "リラクHD";
				break;
			case "17":
				$name = "tokyo_new";
				$short_name = "リフレッシュ東京";
				$short_name_S = "東京新人";
				break;
			default:
				$name = $this->ArRec[$n]["area"] . "_refle";
			}
			if($name == "lymph_tokyo") {
				$short_name = "リンパ東京";
			} elseif($name == "osaka_aroma") {
				$short_name = "大阪AS";
			} else {
				$short_name = $this->ArRec[$n]["name"];
			}
			$this->ArList[$i] = array("name" => $name, "id" => $this->ArRec[$n]["id"], "ja" => $this->ArRec[$n]["name"], "area" => $this->ArRec[$n]["area"], "short_name" => $short_name, "short_name_S" => $short_name_S, "gropno" => $this->ArRec[$n]["gropno"], "domain" => $this->ArRec[$n]["domain"], "area_name" => $this->ArRec[$n]["area_name"], "ex_name" => $this->ArRec[$n]["ex_name"], "bgcolr" => $this->ArRec[$n]["bgcolr"], "ftcolr" => $this->ArRec[$n]["ftcolr"], "ptstb1" => $this->ArRec[$n]["ptstb1"], "ptstb2" => $this->ArRec[$n]["ptstb2"], "shrtb1" => $this->ArRec[$n]["shrtb1"], "shrtb2" => $this->ArRec[$n]["shrtb2"], "shrcls" => $this->ArRec[$n]["shrcls"], "office" => $this->ArRec[$n]["office"]);
			$i++;
		}
		$this->ArListNums = $i;
		return $this->ArList;
	}

	//----店舗リスト数取得
	function getShopListNums() {
		return $this->ArListNums;
	}

	//----エリアリスト取得
	function getAreaList($u_mode) {
		if(!$u_mode) return $this->ArArea;
		$ws_ArArea = array();
		for($n=0; $n<$this->ArAreaNums; $n++) {
			if($this->ArArea[$n]["delete_flg"] == 1) continue;
			array_push($ws_ArArea, $this->ArArea[$n]);
		}
		return $ws_ArArea;
	}

	//----エリア名リスト取得
	function getAreaNameList($u_mode) {
		$ws_ArGropNm = array();
		for($n=0; $n<$this->ArAreaNums; $n++) {
			if($u_mode && $this->ArArea[$n]["delete_flg"] == 1) continue;
			array_push($ws_ArGropNm, $this->ArArea[$n]["area"]);
		}
		return $ws_ArGropNm;
	}

	//----エリア名取得
	function getAreaName($u_area, $u_name) {
		$ws_AreaName = $u_name;
		for($n=0; $n<$this->ArAreaNums; $n++) {
			if($this->ArArea[$n]["area"] == $u_area) {
				$ws_AreaName = $this->ArArea[$n]["area_name"];
				break;
			}
		}
		return $ws_AreaName;
	}
	//----エリア名取得
	function getAreaNameById($u_Id, $u_name) {
		$ws_AreaName = $u_name;
		for($n=0; $n<$this->ArAreaNums; $n++) {
			if($this->ArArea[$n]["id"] == $u_Id) {
				$ws_AreaName = $this->ArArea[$n]["area_name"];
				break;
			}
		}
		return $ws_AreaName;
	}

	//----店舗グループリスト取得
	function getShopGroupList($u_list = 0) {
		$this->ArGpList = array();
		$ws_Group = "";
		for($n=0; $n<$this->ArListNums; $n++) {
			$ws_flag = false;
			for($x=0; $x<count($this->ArGpList); $x++) {
				if($this->ArGpList[$x]["area"] == $this->ArList[$n]["area"]) {
					$ws_flag = true;
					break;
				}
			}
			if($ws_flag) continue;
			array_push($this->ArGpList, $this->ArList[$n]);
		}
		$this->ArGpListNums = count($this->ArGpList);
		return $this->ArGpList;
	}

	//----店舗グループリスト数取得
	function getShopGroupListNums() {
		return $this->ArGpListNums;
	}

	//----店舗グループデータ取得
	function getShopGroupValue($u_area) {

		for($n=0; $n<$this->ArGpListNums; $n++) {
			if($this->ArGpList[$n]["area"] == $u_area) {
				$ws_retArray = $this->ArGpList[$n];
				break;
			}
		}
		return $ws_retArray;
	}

	//----店舗グループ名取得
	function getShopGroupName($u_area) {
		$ws_retName = "";
		for($n=0; $n<$this->ArGpListNums; $n++) {
			if($this->ArGpList[$n]["area"] == $u_area) {
				$ws_retName = $this->ArGpList[$n]["short_name_S"];
				break;
			}
		}
		return $ws_retName;
	}

	//----店舗グループ№取得
	function getShopGroupNo($u_area) {
		$ws_retNo = -1;
		for($n=0; $n<$this->ArGpListNums; $n++) {
			if($this->ArGpList[$n]["area"] == $u_area) {
				$ws_retNo = $this->ArGpList[$n]["gropno"];
				break;
			}
		}
		return $ws_retNo;
	}
}

//----店舗グループリスト取得(指定店舗リストより)
function PHP_getShopGroupListEx($u_list) {
	$ws_ArGpList = array();
	$ws_Group = "";
	for($n=0; $n<count($u_list); $n++) {
		$ws_flag = false;
		for($x=0; $x<count($ws_ArGpList); $x++) {
			if($ws_ArGpList[$x]["area"] == $u_list[$n]["area"]) {
				$ws_flag = true;
				break;
			}
		}
		if($ws_flag) continue;
		array_push($ws_ArGpList, $u_list[$n]);
	}
	return $ws_ArGpList;
}

//----店舗グループ№取得
function PHP_getAreaGroup($u_area, $obj_Shop, $u_Ymd) {

	$ws_GroupNo = $obj_Shop->getShopGroupNo($u_area);

	if($ws_GroupNo == -1) {
		echo "error!";
		exit();
	}
	$ws_SelGroupNo = floor($ws_GroupNo / 10);

	//----該当月の店舗グループリスト取得
	if($u_Ymd) $ws_desYm = floor($u_Ymd / 100); else $ws_desYm = date("Ym");
	$shop_listEx = $obj_Shop->getShopList($ws_desYm);		//全件(削除データ無し)
	$AR_shopGroup = PHP_getShopGroupListEx($shop_listEx);	//店舗グループリスト取得(指定店舗リストより)

	$x = 0;
	$AR_AreaGp = array();

	for($n=0; $n<count($AR_shopGroup); $n++) {
		if( floor($AR_shopGroup[$n]["gropno"] / 10) != $ws_SelGroupNo) continue;
		$AR_AreaGp[$x] = $AR_shopGroup[$n];
		$x++;
	}
	return $AR_AreaGp;
}

//----追加エリア取得
function PHP_getAddArea($u_area) {
global $ARRAY_ShopArea;

	$ws_ShopParty = "";
	for($n=0; $n<count($ARRAY_ShopArea); $n++) {
		if($ARRAY_ShopArea[$n]["area"] == $u_area) {
			$ws_ShopParty = $ARRAY_ShopArea[$n]["party"];
			break;
		}
	}
	return $ws_ShopParty;
}

//----追加エリア対応条件用文字列取得
function PHP_getStrAddAreaWhere($u_area) {

	$ws_AddArea = PHP_getAddArea($u_area);	//追加エリア取得

	if($ws_AddArea) {
		$ws_AddArea = PHP_addQuot($ws_AddArea, ",");		//文字列の両端にシングルクォーテーションを付加
		$ws_whereArea = " in ('" . $u_area . "'," . $ws_AddArea . ")";
	} else {
		$ws_whereArea = "='" . $u_area . "'";
	}

	return $ws_whereArea;
}

//----区から派遣費を返す
function PHP_getTransportation($ward, $u_area){
	$transportation = 1000;
	if($u_area == "yokohama" && ($ward=="中原区")||($ward=="都筑区")||($ward=="青葉区")){
		$transportation = 2000;
	} elseif($u_area == "tokyo" && ($ward=="足立区" || $ward=="板橋区" || $ward=="江戸川区" || $ward=="大田区" || $ward=="葛飾区" || $ward=="北区" || $ward=="杉並区" || $ward=="練馬区") ) {
		$transportation = 3000;
	} elseif($u_area == "tokyo" && ($ward=="世田谷区" || $ward=="中野区" || $ward=="荒川区" || $ward=="江東区") ) {
		$transportation = 2000;
	}
	return $transportation;
}

//----ホテル用エリアコードを通常のエリアに変換する
function PHP_getHotelArea2Area($u_id) {
global $ARRAY_ShopArea;

	//----area_new用
	$ws_ArIds = array(-1, 1, -1, -1, -1, 4, 3, 2, 5, 6, 7);

	$ws_Id = $ws_ArIds[(int)$u_id];

	$area1_name = "不明";
	for($n=0; $n<count($ARRAY_ShopArea); $n++) {
		if($ARRAY_ShopArea[$n]["id"] == $u_id) {
			$area1_name = $ARRAY_ShopArea[$n]["ex_name"];
			break;
		}
	}
	$ws_Ret = array("id" => $ws_Id, "name" => $area1_name);

	return $ws_Ret;
}

//----ホテル用エリアコードを通常のエリアに変換する
function PHP_getHotelArea2AreaList($u_id) {
global $ARRAY_ShopArea;

	//----area2_deri用
	$ws_ArIds = array(-1, 1, 7, 6, 5, 8, 9, 10, -1, -1, -1);

	$ws_Id = $ws_ArIds[(int)$u_id];

	$area1_name = "不明";
	for($n=0; $n<count($ARRAY_ShopArea); $n++) {
		if($ARRAY_ShopArea[$n]["id"] == $ws_Id) {
			$area1_name = $ARRAY_ShopArea[$n]["ex_name"];
			break;
		}
	}
	$ws_Ret = array("id" => $ws_Id, "name" => $area1_name);

	return $ws_Ret;
}

//----ベースエリア情報取得
function PHP_getAreaBase($u_area) {
global $ARRAY_ArShop;

	$x = -1;
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		if($ARRAY_ArShop[$n]["area"] == $u_area) {
			$x = $n;
			break;
		}
	}
	if($x < 0) return;

	$ws_groupNo = floor($ARRAY_ArShop[$x]["gropno"] / 10) * 10;

	$x = -1;
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		if($ARRAY_ArShop[$n]["gropno"] == $ws_groupNo) {
			$x = $n;
			break;
		}
	}

	return $ARRAY_ArShop[$x];
}

if(!$_DecisionYm) {
	$nowTime = PHP_getNowTime();		//本日日付取得 in common/include/today.inc
	$_DecisionYm = date("Ym", $nowTime);
}
$obj_Shop = new Shop();
$obj_Shop->set($ARRAY_ShopArea, $_DecisionYm);
$ARRAY_ArShop = $obj_Shop->getArData();		//店舗情報配列取得
$shop_id_data = $obj_Shop->getArIds();
//print_r($ARRAY_ArShop);
//exit;
//----コース別料金情報配列の取得
$ARRAY_courseFee = PHP_getArrayCourse();

//$ARRAY_Area = array("tokyo" => "東京", "yokohama" => "横浜", "sapporo" => "札幌", "sendai" => "仙台", "osaka" => "大阪", "fukuoka" => "福岡", "tokyo_reraku" => "東京リラク", "tokyo_bigao" => "BIGAO");
$ARRAY_Area = $obj_Shop->getAreaList(false);	//エリア名リスト取得(削除データ含む)
$ARRAY_Area2 = $obj_Shop->getAreaList(true);	//エリア名リスト取得(削除データ無し)
//print_r($ARRAY_Area2);
//exit;

//----get_reservation_for_board_by_forget_therapist_thanks_commonで使用
/*
$area_data = array(
	"0"=>"tokyo",
	"1"=>"yokohama",
	"2"=>"sapporo",
	"3"=>"sendai",
	"4"=>"osaka"
);
$w_data = $obj_Shop->getAreaList(true);
print_r($w_data);

$area_list = array(
		0=>"tokyo",
		1=>"yokohama",
		2=>"sapporo",
		3=>"sendai",
		4=>"osaka",
		5=>"tokyo_reraku",
		6=>"tokyo_bigao"
);
*/
$area_data = $obj_Shop->getAreaNameList(true);	//エリアリスト取得

/*
$shop_list = array(
		0=>array("name"=>"tokyo_refle","id"=>"1","ja"=>"東京リフレ","area"=>"tokyo"),
		1=>array("name"=>"tokyo_aroma","id"=>"2","ja"=>"東京アロマ","area"=>"tokyo"),
		2=>array("name"=>"lymph_tokyo","id"=>"4","ja"=>"リンパマッサージ東京","area"=>"tokyo"),
		3=>array("name"=>"yokohama_refle","id"=>"7","ja"=>"横浜リフレ","area"=>"yokohama"),
		4=>array("name"=>"sapporo_refle","id"=>"6","ja"=>"札幌リフレ","area"=>"sapporo"),
		5=>array("name"=>"sendai_refle","id"=>"8","ja"=>"仙台リフレ","area"=>"sendai"),
		6=>array("name"=>"osaka_refle","id"=>"10","ja"=>"大阪リフレ","area"=>"osaka"),
		7=>array("name"=>"osaka_aroma","id"=>"11","ja"=>"大阪アロマスタイル","area"=>"osaka"),
		8=>array("name"=>"tokyo_reraku","id"=>"12","ja"=>"東京リラク","area"=>"tokyo_reraku"),
		9=>array("name"=>"tokyo_bigao","id"=>"13","ja"=>"BIGAO","area"=>"tokyo_bigao")
);
*/
$shop_list = $obj_Shop->getShopList(0);		//全件(削除データ無し)
$shop_list_num = count($shop_list);

//----店舗グループリスト取得
$ARRAY_shopGroup = $obj_Shop->getShopGroupList();
$GP_shopGroupNums = $obj_Shop->getShopGroupListNums();
//print_r($ARRAY_shopGroup);
for($n=0; $n<$GP_shopGroupNums; $n++) {
	$area_list[$n] = $ARRAY_shopGroup[$n]["area"];
}
$area_list_num = count($area_list);
//print_r($area_list);
//exit;

//man/include/functions.phpより転載
function get_therapist_page_area_name($area){
global $ARRAY_Area2;

	$area_name = "";

	//if($area=="tokyo"){
	//	$area_name = "東京";
	//}else if($area=="yokohama"){
	//	$area_name = "横浜";
	//}else if($area=="sapporo"){
	//	$area_name = "札幌";
	//}else if($area=="sendai"){
	//	$area_name = "仙台";
	//}else if($area=="fukuoka"){
	//	$area_name = "福岡";
	//}else{
	//	echo "クエリー実行で失敗しました(get_therapist_page_area_name)";
	//	exit();
	//}
	foreach($ARRAY_Area2 as $ws_Key => $ws_Val) {
		if($area == $ws_Key) {
			$area_name = $ws_Val;
			break;
		}
	}

	return $area_name;
	exit();
}

//man/include/functions.phpより転載
function get_area_name_by_page_area($area){
global $ARRAY_Area2;

	$data = "不明";

	if( $area == "all" ){
		$data = "すべて";
	//}else if( $area == "tokyo" ){
	//	$data = "東京";
	//}else if( $area == "yokohama" ){
	//	$data = "横浜";
	//}else if( $area == "sapporo" ){
	//	$data = "札幌";
	//}else if( $area == "sendai" ){
	//	$data = "仙台";
	//}else if( $area == "osaka" ){
	//	$data = "大阪";
	} else {
		for($n=0; $n<count($ARRAY_Area2); $n++) {
			if($area == $ARRAY_Area2[$n]["area"]) {
				$data = $ARRAY_Area2[$n]["area_name"];
				break;
			}
		}
	}

	return $data;
	exit();
}

//man/include/functions.phpより転載
function get_shop_data_for_dashboard_support(){
global $shop_list;
	/*
	$shop_data = array(
		"0" => "東京エリア",
		"1" => "東京リフレ",
		"2" => "東京アロマ",
		"3" => "リンパ東京",
		"4" => "横浜",
		"5" => "札幌",
		"6" => "仙台",
		"7" => "大阪リフレ",
		"8" => "大阪ＡＳ",
		"9" => "全エリア"
	);
	*/
	$shop_data = array();
	$shop_data[0] = "東京エリア";
	$i = 0;
	for($n=0; $n<count($shop_list); $n++) {
		if(strpos($shop_list[$n]["area"], "_") !== false) continue;
		$shop_data[($i+1)] = $shop_list[$n]["short_name"];
		$i++;
	}
	$shop_data[($i+1)] = "全エリア";

	return $shop_data;
	exit();
}
//$w_data = get_shop_data_for_dashboard_support();
//print_r($w_data);
//exit;

//man/include/functions.phpより転載
function get_reservation_for_board_for_dashboard_support($selected_time,$shop_type){
global $shop_list;

	$data = get_time_for_frikomi_list($selected_time);

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$sql_where = "";
	/*
	if( $shop_type == "東京エリア" ){
		$tmp = "tokyo";
		$sql_where = sprintf("shop_area='%s'",$tmp);
	}else if( $shop_type == "東京リフレ" ){
		$sql_where = sprintf("shop_name='%s'",$shop_type);
	}else if( $shop_type == "東京アロマ" ){
		$sql_where = sprintf("shop_name='%s'",$shop_type);
	}else if( $shop_type == "リンパ東京" ){
		$tmp = "リンパマッサージ東京";
		$sql_where = sprintf("shop_name='%s'",$tmp);
	}else if( $shop_type == "横浜" ){
		$tmp = "yokohama";
		$sql_where = sprintf("shop_area='%s'",$tmp);
	}else if( $shop_type == "札幌" ){
		$tmp = "sapporo";
		$sql_where = sprintf("shop_area='%s'",$tmp);
	}else if( $shop_type == "仙台" ){
		$tmp = "sendai";
		$sql_where = sprintf("shop_area='%s'",$tmp);
	}else if( $shop_type == "大阪リフレ" ){
		$sql_where = sprintf("shop_name='%s'",$shop_type);
	}else if( $shop_type == "大阪ＡＳ" ){
		$tmp = "大阪アロマスタイル";
		$sql_where = sprintf("shop_name='%s'",$tmp);
	}else if( $shop_type == "全エリア" ){
		$sql_where = "1=1";
	}else{
		echo "error!(get_reservation_for_board_for_dashboard_support)";
		exit();
	}
	*/
	if( $shop_type == "東京エリア" ){
		$tmp = "tokyo";
		$sql_where = sprintf("shop_area='%s'",$tmp);
	}else if( $shop_type == "全エリア" ){
		$sql_where = "1=1";
	} else {
		for($n=0; $n<count($shop_list); $n++) {
			if($shop_li[$n]["short_name"] == $shop_type) {
				$sql_where = sprintf("shop_name='%s'",$shop_type);
				break;
			}
		}
		if(!$sql_where) {
			for($n=0; $n<count($shop_list); $n++) {
				if($shop_li[$n]["area_name"] == $shop_type) {
					$sql_where = sprintf("shop_area='%s'",$shop_type);
					break;
				}
			}
		}
	}
	if(!$sql_where) {
		echo "error!(get_reservation_for_board_for_dashboard_support)";
		exit();
	}

	$sql = sprintf("select * from reservation_for_board where delete_flg='0' and complete_flg='1' and (%s) and year='%s' and month='%s' and day='%s'",$sql_where,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_for_board_for_dashboard_support)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----予約メッセージ取得
function PHP_get_reservation_message_common($hour, $u_area) {
global $ARRAY_Area2;

	$data = "WEB予約の受付中です";

	$a = -1;
	for($n=0; $n<count($ARRAY_Area2); $n++) {
		if($ARRAY_Area2[$n]["area"] == $u_area) {
			$a = $n;
			break;
		}
	}
	if($a == -1) return $data;

	$ws_Hour = $hour;
	if($ws_Hour < $ARRAY_Area2[$a]["staH"]) $ws_Hour += 24;		//検査時刻が開始時刻以前の場合は24時を加算

	if( $ws_Hour >= $ARRAY_Area2[$a]["staH"] && $ws_Hour <= $ARRAY_Area2[$a]["endH"] ) {
		//----時間内
		if($ARRAY_Area2[$a]["ckAtt"] == 1) {
			//----出勤チェックを行うエリア
			//if($u_area == "tokyo") {
			//	$result = get_attendance_today_reservation_flg_tokyo_common();	//本日予約と出勤チェック
			//} else {
				$result = get_attendance_today_reservation_flg_common($u_area);	//本日予約と出勤チェック
			//}
			if($result) {
				$data = "15分～30分で到着できます";
			} else {
				if($ARRAY_Area2[$a]["annaiH"] == -1) {
					$data = "すぐにご案内できます";
				} elseif( $ws_Hour >= $ARRAY_Area2[$a]["annaiH"]) {
					$data = "すぐにご案内できます";
				} else {
					$data = "ご予約の受付中です";
				}
			}
		} else {
			//----出勤チェックを行なわないエリア
			$data = "ご予約の受付中です";
		}
	} else {
		//----時間外
		if( $ws_Hour >= $ARRAY_Area2[$a]["outH"] ) $data = "ご予約の受付中です";
	}
	//echo $ws_Hour . "ggg" .$ARRAY_Area2[$a]["staH"] . "->" . $ARRAY_Area2[$a]["endH"] . " " . $u_area . " " . $data; exit;
	return $data;
}

//man/include/functions.phpより転載
function get_attendance_data_for_dashboard_support($selected_time,$shop_type){

	$data = get_time_for_frikomi_list($selected_time);

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$sql_where = "";

	/*
	if( $shop_type == "東京エリア" ){
		$sql_where = sprintf("area='%s'",$shop_type);
	}else if( $shop_type == "東京リフレ" ){
		$tmp = "tokyo";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "東京アロマ" ){
		$tmp = "tokyo";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "リンパ東京" ){
		$tmp = "tokyo";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "横浜" ){
		$tmp = "yokohama";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "札幌" ){
		$tmp = "sapporo";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "仙台" ){
		$tmp = "sendai";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "大阪リフレ" ){
		$tmp = "osaka";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "大阪ＡＳ" ){
		$tmp = "osaka";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "全エリア" ){
		$sql_where = "1=1";
	}else{
		echo "error!(get_attendance_data_for_dashboard_support)";
		exit();
	}
	*/
	if( $shop_type == "東京エリア" ){
		$tmp = "tokyo";
		$sql_where = sprintf("area='%s'",$tmp);
	}else if( $shop_type == "全エリア" ){
		$sql_where = "1=1";
	} else {
		for($n=0; $n<count($shop_list); $n++) {
			if($shop_li[$n]["short_name"] == $shop_type) {
				$sql_where = sprintf("area='%s'",$shop_type);
				break;
			}
		}
		if(!$sql_where) {
			for($n=0; $n<count($shop_list); $n++) {
				if($shop_li[$n]["area_name"] == $shop_type) {
					$sql_where = sprintf("area='%s'",$shop_type);
					break;
				}
			}
		}
	}
	if(!$sql_where) {
		echo "error!(get_attendance_data_for_dashboard_support)";
		exit();
	}

	$sql = sprintf("select * from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and (%s) and year='%s' and month='%s' and day='%s'",$sql_where,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_data_for_dashboard_support)" . $sql;
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

function PHP_getShopTypeClass_maninc($shop_name) {
//----man/include/functions.php get_board_disp_data_html用

	if(($shop_name == "東京リフレ")||($shop_name == "横浜リフレ") ){
		$shop_type_class = " refle";
	}else if( $shop_name == "東京アロマ" ){
		$shop_type_class = " aroma";
	}else if( $shop_name == "リンパマッサージ東京" ){
		$shop_type_class = " lymph";
	}else if( $shop_name == "大阪リフレ" ){
		$shop_type_class = " aroma";	//?? バグ？by aida
	}else if( $shop_name == "大阪アロマスタイル" ){
		$shop_type_class = " refle";	//?? バグ？by aida
	}else if( $shop_name == "東京リラク" ){
		$shop_type_class = " relax";
        }else if( $shop_name == "BIGAO" ){
		$shop_type_class = " bigao";
	}

	if( $out_time_flg == true ){
		$shop_type_class = " out_time";
	}
	return $shop_type_class;
}

//----店舗メニューHTML取得
function PHP_getShopMenue($u_area, $u_url, $url_para, $u_mode = 0, $year, $month, $day) {
global $ARRAY_shopGroup;
	//2018/11/7 村瀬 追加
	$data = get_today_year_month_day_common();

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];
	//952~956まで
	$ws_Html = "";
	for($z=0; $z<count($ARRAY_shopGroup); $z++) {
		if($u_mode == 1) {
			if($ARRAY_shopGroup[$z]["area"] == "yokohama2") continue;
			if(strpos($ARRAY_shopGroup[$z]["area"], "_") !== false) continue;
		}
		if($ARRAY_shopGroup[$z]["area"] == $u_area) {
			$ws_Html .= '<b>' . $ARRAY_shopGroup[$z]["short_name_S"] . '</b>　';
		} else {
			//'&year=' . $year . '&month=' . $month . '&day=' . $day . '部分追加 村瀬
			$ws_Html .= '<a href="' . $u_url . '?area=' .  $ARRAY_shopGroup[$z]["area"] . $url_para . '&year=' . $year . '&month=' . $month . '&day=' . $day . '">' . $ARRAY_shopGroup[$z]["short_name_S"] . '</a>　';
		}
	}
	return $ws_Html;
}

//----超過料金取得
function PHP_getExtension_common($u_shop_name, $u_extension) {
global $ARRAY_ArShop;

	if( $u_extension >= 30 ){

		$ws_extension = intval($u_extension / 30);
		/*
		switch($u_shop_name) {
		case "札幌リフレ":
		case "仙台リフレ":
		case "大阪リフレ":
		case "福岡リフレ":
		case "大阪アロマスタイル":
			$ws_ret = 4000 * $ws_extension;
			break;
		default:
			$ws_ret = 5000 * $ws_extension;
		}
		*/
		for($n=0; $n<count($ARRAY_ArShop); $n++) {
			if($ARRAY_ArShop[$n]["name"] == $u_shop_name) {
				$ws_ret = $ARRAY_ArShop[$n]["extnsn"] * $ws_extension;
				break;
			}
		}
	}
	return $ws_ret;
}

//----エリア名取得
function get_area_name_by_area_common($area){
global $ARRAY_Area;

	$ws_area = $area;

	$ws_pos = strpos($area, "_");

	if($ws_pos !== false) {
		$ws_area = substr($ws_area, 0, $ws_pos);
	}
	if($ws_area == "yokohama2") $ws_area = "yokohama";

	for($n=0; $n<count($ARRAY_Area); $n++) {
		if($ARRAY_Area[$n]["area"] == $ws_area) {
			$area_name = $ARRAY_Area[$n]["area_name"];
			break;
		}
	}
	if(!$area_name) $area_name = "不明";

	return $area_name;
}

//----店舗グループ名取得
function PHP_get_shopGroupName($u_area) {
global $ARRAY_shopGroup;

	for($n=0; $n<count($ARRAY_shopGroup); $n++) {
		if($ARRAY_shopGroup[$n]["area"] == $u_area) {
			$area_name = $ARRAY_shopGroup[$n]["short_name"];
			break;
		}
	}
	if(!$area_name) $area_name = "不明";

	return $area_name;
}

//----店舗情報取得
function PHP_getShopInfo_common($shop_type, $u_name) {
global $shop_list;

	$shop_name = "";
	$domain = "tokyo-refle.com";

	if($shop_type == "refle") {
		$shop_name = "東京リフレ";
	} else {
		for($n=0; $n<count($shop_list); $n++) {
			if($shop_list[$n]["area"] == $shop_type) {
				$shop_name = $shop_list[$n]["short_name"];
				$domain = $shop_list[$n]["domain"];
				break;
			}
		}
	}
	if($shop_type == "sendai") {
		$shop_name = "出張マッサージ仙台リフレ";
	}

	if($shop_name) {
		$mailto = $u_name . "@" . $domain;
		$header = "From: " . $u_name . "@" . $domain . "\n";
	}

	$ws_ArRet = array("shop_name" => $shop_name, "mailto" => $mailto, "header" => $header);
	return $ws_ArRet;
}

//----エリア情報取得
function PHP_getAreaInfo_common($area) {
global $ARRAY_ArShop;
	/*
	switch($area) {
	case "yokohama":
		$site_name = "Yokohama Refle";
		$site_tel = "0120-916-796";
		$site_mail = "order@yokohama-refle.com";
		$site_url = "http://www.yokohama-refle.com/en/";
		$title = "Booking form【Relaxation Massage Yokohama Refle】";
		$title_ch = "Booking form【Relaxation Massage Yokohama Refle】";
		break;
	case "sapporo":
		$site_name = "Sapporo Refle";
		$site_tel = "0120-978-950";
		$site_mail = "order@sapporo-refle.com";
		$site_url = "http://www.sapporo-refle.com/en/";
		$title = "Booking form【Relaxation Massage Sapporo Refle】";
		$title_ch = "Booking form【Relaxation Massage Sapporo Refle】";
		break;
	case "sendai":
		$site_name = "Sendai Refle";
		$site_tel = "0120-910-220";
		$site_mail = "order@sendai-refle.com";
		$site_url = "http://www.sendai-refle.com/en/";
		$title = "Booking form【Relaxation Massage Sendai Refle】";
		$title_ch = "Booking form【Relaxation Massage Sendai Refle】";
		break;
	case "fukuoka":
		$site_name = "Fukuoka Refle";
		$site_tel = "0120-xxx-xxx";
		$site_mail = "order@fukuoka-refle.com";
		$site_url = "http://www.fukuoka-refle.com/en/";
		$title = "Booking form【Relaxation Massage Fukuoka Refle】";
		$title_ch = "Booking form【Relaxation Massage Fukuoka Refle】";
		break;
	case "osaka":
		$site_name = "Osaka Refle";
		$site_tel = "0120-910-706";
		$site_mail = "order@osaka-refle.com";
		$site_url = "http://www.osaka-refle.com/en/";
		$title = "Booking form【Relaxation Massage Osaka Refle】";
		$title_ch = "Booking form【Relaxation Massage Osaka Refle】";
		break;
	case "tokyo":
		$site_name = "Tokyo Refle";
		$site_tel = "03-5206-5134";
		$site_mail = "order@tokyo-refle.com";
		$site_url = "http://www.tokyo-refle.com/en/";
		$title = "Booking form【Relaxation Massage Tokyo Refle】";
		$title_ch = "Booking form【Relaxation Massage Tokyo Refle】";
		break;
	default:
		$site_name = "";
		$site_tel = "";
		$site_mail = "";
		$site_url = "";
		$title = "Booking form";
		$title_ch = "Booking form";
	}
	*/
	$site_name = "";
	$site_tel = "";
	$site_mail = "";
	$site_url = "";
	$title = "Booking form";
	$title_ch = "Booking form";
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		if($ARRAY_ArShop[$n]["area"] == $area) {
			$site_name = $ARRAY_ArShop[$n]["sitnam"];
			$site_tel = $ARRAY_ArShop[$n]["sittel"];
			$site_mail = "order@" . $ARRAY_ArShop[$n]["domain"];
			$site_url = "http://www." . $ARRAY_ArShop[$n]["domain"] . "/en/";
			$title = "Booking form【Relaxation Massage " . $site_name . "】";
			$title_ch = "Booking form【Relaxation Massage " . $site_name . "】";
			break;
		}
	}

	$ws_ArRet = array("site_name" => $site_name, "site_tel" => $site_tel, "site_mail" => $site_mail, "site_url" => $site_url, "title" => $title, "title_ch" => $title_ch);
	return $ws_ArRet;
}

//man/include/functions.phpより転載
function get_staff_type_menu_for_furikomi_list(){

	$menu = array(
		"0"=>array(
			"value"=>"tokyo_therapist",
			"ja"=>"東京(セラピスト)"
		),
		"1"=>array(
			"value"=>"tokyo_driver",
			"ja"=>"東京(ドライバー)"
		),
		"2"=>array(
			"value"=>"tokyo_honbu",
			"ja"=>"東京(内勤スタッフ)"
		),
		"3"=>array(
			"value"=>"yokohama_therapist",
			"ja"=>"横浜(セラピスト)"
		),
		"4"=>array(
			"value"=>"yokohama_driver",
			"ja"=>"横浜(ドライバー)"
		),
		"5"=>array(
			"value"=>"sapporo_therapist",
			"ja"=>"札幌(セラピスト)"
		),
		"6"=>array(
			"value"=>"sapporo_driver",
			"ja"=>"札幌(ドライバー)"
		),
		"7"=>array(
			"value"=>"sendai_therapist",
			"ja"=>"仙台(セラピスト)"
		),
		"8"=>array(
			"value"=>"sendai_driver",
			"ja"=>"仙台(ドライバー)"
		),
		"9"=>array(
			"value"=>"osaka_therapist",
			"ja"=>"大阪(セラピスト)"
		),
		"10"=>array(
			"value"=>"osaka_driver",
			"ja"=>"大阪(ドライバー)"
		),
		"11"=>array(
			"value"=>"tokyo_reraku_therapist",
			"ja"=>"東京リラク(セラピスト)"
		),
		"12"=>array(
			"value"=>"tokyo_bigao_therapist",
			"ja"=>"BIGAO(セラピスト)"
		)
	);

	return $menu;
	exit();
}

//man/include/functions.phpより転載
function get_shop_id_by_shop_name_en($shop_name){
global $shop_list;

	$shop_id = "-1";
	/*
	if( $shop_name == "tokyo_refle" ){
		$shop_id = "1";
	}else if( $shop_name == "tokyo_aroma" ){
		$shop_id = "2";
	}else if( $shop_name == "lymph_tokyo" ){
		$shop_id = "4";
	}else if( $shop_name == "yokohama_refle" ){
		$shop_id = "7";
	}else if( $shop_name == "sapporo_refle" ){
		$shop_id = "6";
	}else if( $shop_name == "sendai_refle" ){
		$shop_id = "8";
	}else if( $shop_name == "osaka_refle" ){
		$shop_id = "10";
	}else if( $shop_name == "osaka_aroma" ){
		$shop_id = "11";
	}else{
		echo "error!(get_shop_id_by_shop_name_en)";
		exit();
	}
	*/
	for($n=0; $n<count($shop_list); $n++) {
		if($shop_list[$n]["name"] == $shop_name) {
			$shop_id = $shop_list[$n]["id"];
			break;
		}
	}

	return $shop_id;
	exit();
}

//速報データの取得	man/include/functions.phpより転載
function get_sokuhou_data($year,$month,$day){
global $ARRAY_ArShop;

	/*
	$area = "tokyo";
	$price_tokyo = get_sokuhou_price_area($year,$month,$day,$area);
	$honsuu_tokyo = get_sokuhou_honsuu_area($year,$month,$day,$area);

	$area = "yokohama";
	$price_yokohama = get_sokuhou_price_area($year,$month,$day,$area);
	$honsuu_yokohama = get_sokuhou_honsuu_area($year,$month,$day,$area);

	$area = "sapporo";
	$price_sapporo = get_sokuhou_price_area($year,$month,$day,$area);
	$honsuu_sapporo = get_sokuhou_honsuu_area($year,$month,$day,$area);

	$area = "sendai";
	$price_sendai = get_sokuhou_price_area($year,$month,$day,$area);
	$honsuu_sendai = get_sokuhou_honsuu_area($year,$month,$day,$area);

	$area = "osaka";
	$price_osaka = get_sokuhou_price_area($year,$month,$day,$area);
	$honsuu_osaka = get_sokuhou_honsuu_area($year,$month,$day,$area);

	$area = "tokyo_reraku";
	$price_tokyo_reraku = get_sokuhou_price_area($year,$month,$day,$area);
	$honsuu_tokyo_reraku = get_sokuhou_honsuu_area($year,$month,$day,$area);

	$area = "tokyo_bigao";
	$price_tokyo_bigao = get_sokuhou_price_area($year,$month,$day,$area);
	$honsuu_tokyo_bigao = get_sokuhou_honsuu_area($year,$month,$day,$area);

	$price_total = $price_tokyo + $price_yokohama + $price_sapporo + $price_sendai + $price_osaka + $price_tokyo_reraku + $price_tokyo_bigao;
	$honsuu_total = $honsuu_tokyo + $honsuu_yokohama + $honsuu_sapporo + $honsuu_sendai + $honsuu_osaka + $honsuu_tokyo_reraku + $honsuu_tokyo_bigao;

	$data["price_tokyo"] = number_format($price_tokyo)."円";
	$data["honsuu_tokyo"] = $honsuu_tokyo."本";

	$data["price_yokohama"] = number_format($price_yokohama)."円";
	$data["honsuu_yokohama"] = $honsuu_yokohama."本";

	$data["price_sapporo"] = number_format($price_sapporo)."円";
	$data["honsuu_sapporo"] = $honsuu_sapporo."本";

	$data["price_sendai"] = number_format($price_sendai)."円";
	$data["honsuu_sendai"] = $honsuu_sendai."本";

	$data["price_osaka"] = number_format($price_osaka)."円";
	$data["honsuu_osaka"] = $honsuu_osaka."本";

	$data["price_total"] = number_format($price_total)."円";
	$data["honsuu_total"] = $honsuu_total."本";

	$data["price_tokyo_reraku"] = number_format($price_tokyo_reraku)."円";
	$data["honsuu_tokyo_reraku"] = $honsuu_tokyo_reraku."本";

	$data["price_tokyo_bigao"] = number_format($price_tokyo_bigao)."円";
	$data["honsuu_tokyo_bigao"] = $honsuu_tokyo_bigao."本";
	*/
	$price_total	= 0;
	$honsuu_total	= 0;
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		$ws_ARprice[$n]		= get_sokuhou_price_area($year, $month, $day, $ARRAY_ArShop[$n]["area"]);
		$ws_ARhonsuu[$n]	= get_sokuhou_honsuu_area($year, $month, $day, $ARRAY_ArShop[$n]["area"]);
		$price_total	+= $ws_ARprice[$n];
		$honsuu_total	+= $ws_ARhonsuu[$n];
		$ws_cellName = "price_" . $ARRAY_ArShop[$n]["area"];
		$data[$ws_cellName] = number_format($ws_ARprice[$n]) . "円";
		$ws_cellName = "honsuu_" . $ARRAY_ArShop[$n]["area"];
		$data[$ws_cellName] = $ws_ARhonsuu[$n] . "本";
	}

	return $data;
	exit();

}

//----シェア率取得
function get_share_rate_common($point_ref,$point_fix,$total_point,$area){
global $ARRAY_ArShop;

	if( ($point_fix != "0") && ($point_fix != "-1") ){
		return $point_fix;
		exit();
	}

	$return_value = "-1";

	if( $point_ref == "1" ){
		/*
		if( $area == "tokyo" || $area == "yokohama" || $area == "osaka"){
			if( $total_point >= 450 ){
				$return_value = "50";
			}else if( $total_point >= 400 ){
				$return_value = "49";
			}else if( $total_point >= 350 ){
				$return_value = "48";
			}else if( $total_point >= 300 ){
				$return_value = "47";
			}else if( $total_point >= 250 ){
				$return_value = "46";
			}else if( $total_point >= 200 ){
				$return_value = "45";
			}else if( $total_point >= 150 ){
				$return_value = "44";
			}else if( $total_point >= 100 ){
				$return_value = "43";
			}else if( $total_point >= 50 ){
				$return_value = "42";
			}else if( $total_point >= 30 ){
				$return_value = "41";
			}else{
				$return_value = "40";
			}
		} else {
			if( $total_point >= 450 ){
				$return_value = "45";
			}else if( $total_point >= 400 ){
				$return_value = "44";
			}else if( $total_point >= 350 ){
				$return_value = "43";
			}else if( $total_point >= 300 ){
				$return_value = "42";
			}else if( $total_point >= 250 ){
				$return_value = "41";
			}else if( $total_point >= 200 ){
				$return_value = "40";
			}else if( $total_point >= 150 ){
				$return_value = "39";
			}else if( $total_point >= 100 ){
				$return_value = "38";
			}else if( $total_point >= 50 ){
				$return_value = "37";
			}else if( $total_point >= 30 ){
				$return_value = "36";
			}else{
				$return_value = "35";
			}
		}
		*/
		for($n=0; $n<count($ARRAY_ArShop); $n++) {
			if($ARRAY_ArShop[$n]["area"] == $area) {
				if(strlen($ARRAY_ArShop[$n]["ptstb1"]) == 0) continue;
				if($ARRAY_ArShop[$n]["ptstb1"] == "0") {
					$return_value = $ARRAY_ArShop[$n]["shrtb1"];
				} else {
					$ws_ArPoints = explode(",", $ARRAY_ArShop[$n]["ptstb1"]);
					$ws_ArSharRt = explode(",", $ARRAY_ArShop[$n]["shrtb1"]);
					for($i=0; $i<count($ws_ArPoints); $i++) {
						$ws_ArPoints[$i] = intval($ws_ArPoints[$i]);
						$ws_ArSharRt[$i] = intval($ws_ArSharRt[$i]);
					}
					if($ARRAY_ArShop[$n]["shrcls"] == 1) {
						if($total_point >= $ws_ArPoints[1]) $return_value = $ws_ArSharRt[1]; else $return_value = $ws_ArSharRt[0];
					} else {
						for($r=0; $r<count($ws_ArPoints); $r++) {
							if( $total_point >= $ws_ArPoints[$r]){
								$return_value = $ws_ArSharRt[$r];
								break;
							}
						}
					}
				}
				break;
			}
		}

	}else if( $point_ref == "2" ){
		/*
		if( $area == "sapporo" || $area == "sendai"){
			if( $total_point >= 250 ){
				$return_value = "45";
			}else if( $total_point >= 200 ){
				$return_value = "44";
			}else if( $total_point >= 150 ){
				$return_value = "43";
			}else if( $total_point >= 100 ){
				$return_value = "42";
			}else if( $total_point >= 50 ){
				$return_value = "41";
			}else{
				$return_value = "40";
			}
		}
		*/
		for($n=0; $n<count($ARRAY_ArShop); $n++) {
			if($ARRAY_ArShop[$n]["area"] == $area) {
				if(strlen($ARRAY_ArShop[$n]["ptstb2"]) == 0) continue;
				if($ARRAY_ArShop[$n]["ptstb1"] == "0") {
					$return_value = $ARRAY_ArShop[$n]["shrtb1"];
				} else {
					$ws_ArPoints = explode(",", $ARRAY_ArShop[$n]["ptstb2"]);
					$ws_ArSharRt = explode(",", $ARRAY_ArShop[$n]["shrtb2"]);
					for($r=0; $r<count($ws_ArPoints); $r++) {
						if( $total_point >= $ws_ArPoints[$r]){
							$return_value = $ws_ArSharRt[$r];
							break;
						}
					}
				}
				break;
			}
		}
	} else {
		for($n=0; $n<count($ARRAY_ArShop); $n++) {
			if($ARRAY_ArShop[$n]["area"] == $area) {
				if(strlen($ARRAY_ArShop[$n]["ptstb1"]) == 0) continue;
				if($ARRAY_ArShop[$n]["ptstb1"] == "0") {
					$return_value = $ARRAY_ArShop[$n]["shrtb1"];
				}
				break;
			}
		}
		//echo $return_value . "wwwww<br />";
	}
	/*
	if( $area == "tokyo_reraku" ){
		//シェア率は固定
		$return_value = "40";
	}

	if( $area == "tokyo_bigao" ){
		//シェア率は固定(テーブルから取得するため)
		$return_value = "40";
	}
	*/

	return $return_value;
	exit();
}

//man/include.php/functions.phpより転載
function get_share_rate($point_ref,$point_fix,$total_point,$area){
	return get_share_rate_common($point_ref,$point_fix,$total_point,$area);		//シェア率取得
}

//----シェア率からポイント下限値取得
function get_point_be_share_rate_common($area,$point_ref,$share_rate){
global $ARRAY_shopGroup;

	for($n=0; $n<count($ARRAY_shopGroup); $n++) {
		if($ARRAY_shopGroup[$n]["area"] == $area) {
			$ws_ArGroup = $ARRAY_shopGroup[$n];
			break;
		}
	}

	if( $ws_ArGroup["shrcls"] == 1 || $point_ref == "1" ){
		/*
		if( $area == "tokyo" || $area == "yokohama" || $area == "osaka"){
			if( $share_rate == "50" ){
				$data = "450";
			}else if( $share_rate == "49" ){
				$data = "400";
			}else if( $share_rate == "48" ){
				$data = "350";
			}else if( $share_rate == "47" ){
				$data = "300";
			}else if( $share_rate == "46" ){
				$data = "250";
			}else if( $share_rate == "45" ){
				$data = "200";
			}else if( $share_rate == "44" ){
				$data = "150";
			}else if( $share_rate == "43" ){
				$data = "100";
			}else if( $share_rate == "42" ){
				$data = "50";
			}else if( $share_rate == "41" ){
				$data = "30";
			}else if( $share_rate == "40" ){
				$data = "0";
			}else{
				echo "error!(get_point_be_share_rate_common)";
				exit();
			}
		} else {
			if( $share_rate == "45" ){
				$data = "450";
			}else if( $share_rate == "44" ){
				$data = "400";
			}else if( $share_rate == "43" ){
				$data = "350";
			}else if( $share_rate == "42" ){
				$data = "300";
			}else if( $share_rate == "41" ){
				$data = "250";
			}else if( $share_rate == "40" ){
				$data = "200";
			}else if( $share_rate == "39" ){
				$data = "150";
			}else if( $share_rate == "38" ){
				$data = "100";
			}else if( $share_rate == "37" ){
				$data = "50";
			}else if( $share_rate == "36" ){
				$data = "30";
			}else if( $share_rate == "35" ){
				$data = "0";
			}else{
				echo "error!(get_point_be_share_rate_common)";
				exit();
			}
		}
		*/
		if($ws_ArGroup["ptstb1"] == "0") {
			$data = $ws_ArGroup["ptstb1"];
		} else {
			$ws_ArPoints = explode(",", $ws_ArGroup["ptstb1"]);
			$ws_ArSharRt = explode(",", $ws_ArGroup["shrtb1"]);
			for($r=0; $r<count($ws_ArPoints); $r++) {
				if( $share_rate == $ws_ArSharRt[$r]){
					$data = $ws_ArPoints[$r];
					break;
				}
			}
		}
	}else if( $point_ref == "2" ){
		/*
		if( $area == "sapporo" || $area == "sendai"){
			if( $share_rate == "45" ){
				$data = "250";
			}else if( $share_rate == "44" ){
				$data = "200";
			}else if( $share_rate == "43" ){
				$data = "150";
			}else if( $share_rate == "42" ){
				$data = "100";
			}else if( $share_rate == "41" ){
				$data = "50";
			}else if( $share_rate == "40" ){
				$data = "0";
			}else{
				echo "error!(get_point_be_share_rate_common)";
				exit();
			}
		} else {
			echo "error!(get_point_be_share_rate_common)";
			exit();
		}
		*/
		if($ws_ArGroup["ptstb1"] == "0") {
			$data = $ws_ArGroup["ptstb1"];
		} else {
			$ws_ArPoints = explode(",", $ws_ArGroup["ptstb2"]);
			$ws_ArSharRt = explode(",", $ws_ArGroup["shrtb2"]);
			for($r=0; $r<count($ws_ArPoints); $r++) {
				if( $share_rate == $ws_ArSharRt[$r]){
					$data = $ws_ArPoints[$r];
					break;
				}
			}
		}
	} else {
		echo "error!(get_point_be_share_rate_common)";
		exit();
	}

	return $data;
	exit();
}

//----エリア別最大シェア率取得
function get_max_share_rate_by_area_common($area,$point_ref){
global $ARRAY_shopGroup;

	if(( $area == "tokyo_reraku" )||( $area == "tokyo_bigao" )) return null;

	for($n=0; $n<count($ARRAY_shopGroup); $n++) {
		if($ARRAY_shopGroup[$n]["area"] == $area) {
			$ws_ArGroup = $ARRAY_shopGroup[$n];
			break;
		}
	}
	if(!$ws_ArGroup) {
		echo "error! (get_max_share_rate_by_area_common) line:" . __LINE__;
		exit;
	}

	if($ws_ArGroup["shrcls"] == 1 || $point_ref == "1" ){
		/*
		if( $area == "tokyo" || $area == "yokohama" || $area == "osaka"){
			$return_value = "50";
		}else if( $area == "sapporo" || $area == "sendai" ){
			$return_value = "45";
		}else{
			echo "error!(get_max_share_rate_by_area_common)";
			exit();
		}
		*/
		if($ws_ArGroup["ptstb1"] == "0") {
			$return_value = null;
		} else {
			$ws_ArPoints = explode(",", $ws_ArGroup["shrtb1"]);
			$return_value = intval($ws_ArPoints[0]);
		}
	}else if( $point_ref == "2" ){
		/*
		if( $area == "sapporo" || $area == "sendai"){
			$return_value = "45";
		}else{
			echo "error!(get_max_share_rate_by_area_common)";
			exit();
		}
		*/
		if($ws_ArGroup["ptstb1"] == "0") {
			$return_value = null;
		} else {
			$ws_ArPoints = explode(",", $ws_ArGroup["shrtb2"]);
			$return_value = intval($ws_ArPoints[0]);
		}
	}else{
		echo "error!(get_max_share_rate_by_area_common)";
		exit();
	}

	return $return_value;
	exit();
}

//----報酬計算
function PHP_getRemuneration($u_area, $price, $shimei_flg, $transportation, $share_rate, $new_flg, $repeat_flg, $insentive, $u_flagFresh) {
global $ARRAY_ArShop;

	//(料金)-(指名料)-(交通費)=(施術料金)
	//(施術料金)×(シェア率)=(セラピスト報酬)
	//(以下、指名の場合)
	//(セラピスト報酬)+((施術料金)×(10パーセント))=(セラピスト報酬)
	//
	//(チーフ手当てのプラスも必要)(この関数内ではチーフ手当てのプラスは、していない)
	//

	$remuneration = 0;
	$shimei_value = 0;

	if( $share_rate == "-1" ){
		return $remuneration;
		exit();
	}

	//【東京リラク報酬計算】
	//(料金)-(指名料)-(交通費)=(施術料金)
	//(施術料金)×(シェア率)=(セラピスト報酬)
	//(以下、指名の場合)
	//(施術料金)×60%=(セラピスト報酬)

	//【BIGAO報酬計算】
	//(料金) -(指名料)-(交通費)=(施術料金)
	//税抜→(料金)-(消費税)-(指名料)-(交通費)=(施術料金)
	//(施術料金)×(シェア率)=(セラピスト報酬)
	//リピーター

	//(料金)-(指名料)-(交通費)=(施術料金)
	//(施術料金)×(シェア率)=(セラピスト報酬)
	//(以下、指名の場合)
	//(セラピスト報酬)+((施術料金)×(10パーセント))=(セラピスト報酬)
	//
	//(チーフ手当てのプラスも必要)(この関数内ではチーフ手当てのプラスは、していない)

	$ws_Insentive = 0;
	$ws_SimeiKin = CONST_shimei_value;
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		if($ARRAY_ArShop[$n]["area"] == $u_area) {
			$ws_SimeiKin = $ARRAY_ArShop[$n]["simeik"];
			$ws_Insentive = $ARRAY_ArShop[$n]["insntv"];
			break;
		}
	}

	if( $shimei_flg == "1" ) $shimei_value = $ws_SimeiKin;

	$insentive = 0;
	$shimei_add_value = 0;

	$price_shijutsu = $price - $shimei_value - $transportation;		//(料金)-(指名料)-(交通費)=(施術料金)
	//echo $price_shijutsu . " = " . $price . " - " . $shimei_value . " - " . $transportation . "<br />";

	if( $shimei_flg == "1" ) {
		if($u_area == "tokyo_reraku") {
			$share_rate = 60;
		} elseif($u_area == "tokyo_bigao" ) {
			$insentive += $ws_Insentive;		//指名時はインセンティブに加算
		} else {
			if($u_flagFresh) {
				//----フレッシュ対応
				$shimei_add_value = $price_shijutsu * (CONST_flesh_ShareRate_Shimei - $share_rate) / 100;
				//echo $shimei_add_value . " = " . $price_shijutsu . " * (" . CONST_flesh_ShareRate_Shimei . " - " . $share_rate . ") / 100<br />";
			} else {
				$shimei_add_value = $price_shijutsu * CONST_ShareRate_Shimei / 100;
			}
		}
	}

	if($u_area == "tokyo_bigao" ) {
		$tmp = get_therapist_data_by_attendance_id_common($attendance_id);		//出勤データのセラピスト情報取得
		if($tmp["tax_flg"] == "1") $price_shijutsu = ceil($price_shijutsu / TAX_RATE);	//税抜
		//----何故BOGAOだけなのか？ by aida
	}

	$remuneration = $price_shijutsu * ($share_rate / 100) + $insentive + $shimei_add_value;
	//echo $u_area . "==== " . $remuneration . " = " . $price_shijutsu . " * (" . $share_rate . " / 100) + " . $insentive . " + " . $shimei_add_value . " | " . $price . " - " . $shimei_value . " - " . $transportation . "<br />";

	//給料の小数点以下は四捨五入
	$remuneration = round($remuneration);

	return $remuneration;
}

//----報酬計算
function PHP_getRemunerationEx($u_area, $price, $shimei_flg, $transportation, $share_rate, $new_flg, $repeat_flg, $insentive, $u_flagFresh) {
global $ARRAY_ArShop;

	//(料金)-(指名料)-(交通費)=(施術料金)
	//(施術料金)×(シェア率)=(セラピスト報酬)
	//(以下、指名の場合)
	//(セラピスト報酬)+((施術料金)×(10パーセント))=(セラピスト報酬)
	//
	//(チーフ手当てのプラスも必要)(この関数内ではチーフ手当てのプラスは、していない)
	//

	$remuneration = 0;
	$shimei_value = 0;

	if( $share_rate == "-1" ){
		return $remuneration;
		exit();
	}

	//【東京リラク報酬計算】
	//(料金)-(指名料)-(交通費)=(施術料金)
	//(施術料金)×(シェア率)=(セラピスト報酬)
	//(以下、指名の場合)
	//(施術料金)×60%=(セラピスト報酬)

	//【BIGAO報酬計算】
	//(料金) -(指名料)-(交通費)=(施術料金)
	//税抜→(料金)-(消費税)-(指名料)-(交通費)=(施術料金)
	//(施術料金)×(シェア率)=(セラピスト報酬)
	//リピーター

	//(料金)-(指名料)-(交通費)=(施術料金)
	//(施術料金)×(シェア率)=(セラピスト報酬)
	//(以下、指名の場合)
	//(セラピスト報酬)+((施術料金)×(10パーセント))=(セラピスト報酬)
	//
	//(チーフ手当てのプラスも必要)(この関数内ではチーフ手当てのプラスは、していない)

	$ws_Insentive = 0;
	$ws_SimeiKin = CONST_shimei_value;
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		if($ARRAY_ArShop[$n]["area"] == $u_area) {
			$ws_SimeiKin = $ARRAY_ArShop[$n]["simeik"];
			$ws_Insentive = $ARRAY_ArShop[$n]["insntv"];
			break;
		}
	}

	if( $shimei_flg == "1" ) $shimei_value = $ws_SimeiKin;

	$insentive = 0;
	$shimei_add_value = 0;

	$price_shijutsu = $price - $shimei_value - $transportation;		//(料金)-(指名料)-(交通費)=(施術料金)
	//echo $price_shijutsu . " = " . $price . " - " . $shimei_value . " - " . $transportation . "<br />";

	if( $shimei_flg == "1" ) {
		if($u_area == "tokyo_reraku") {
			$share_rate = 60;
		} elseif($u_area == "tokyo_bigao" ) {
			$insentive += $ws_Insentive;		//指名時はインセンティブに加算
		} else {
			if($u_flagFresh) {
				//----フレッシュ対応
				$shimei_add_value = $price_shijutsu * (CONST_flesh_ShareRate_Shimei - $share_rate) / 100;
				//echo $shimei_add_value . " = " . $price_shijutsu . " * (" . CONST_flesh_ShareRate_Shimei . " - " . $share_rate . ") / 100<br />";
			} else {
				$shimei_add_value = $price_shijutsu * CONST_ShareRate_Shimei / 100;
			}
		}
	}

	if($u_area == "tokyo_bigao" ) {
		$tmp = get_therapist_data_by_attendance_id_common($attendance_id);		//出勤データのセラピスト情報取得
		if($tmp["tax_flg"] == "1") $price_shijutsu = ceil($price_shijutsu / TAX_RATE);	//税抜
		//----何故BOGAOだけなのか？ by aida
	}

	$remuneration = $price_shijutsu * ($share_rate / 100) + $insentive + $shimei_add_value;
	//echo $u_area . "==== " . $remuneration . " = " . $price_shijutsu . " * (" . $share_rate . " / 100) + " . $insentive . " + " . $shimei_add_value . " | " . $price . " - " . $shimei_value . " - " . $transportation . "<br />";

	//給料の小数点以下は四捨五入
	$remuneration = round($remuneration);

	$ws_ArRet = array("remuneration" => $remuneration, "remuneration_shimei" => $shimei_add_value, "remuneration_insentive" => $insentive, "remuneration_base" => ($remuneration - $shimei_add_value - $insentive));

	return $ws_ArRet;
}

//----報酬取得（店長）
function get_shop_boss_remuneration_for_sale_shop_common(
$genkin_price,$card_price,$therapist_remuneration,
$driver_remuneration,$movement_cost,$driver_gasoline,$driver_parking,$card_commission,
$shop_area,$lowest_guarantee,$driver_highway){

global $ARRAY_ArShop;

	//セラピスト報酬に最低保証分も足す
	$therapist_remuneration = $therapist_remuneration + $lowest_guarantee;

	if( $shop_area == "tokyo" ){
		$shop_boss_remuneration = 0;		//東京には店長報酬は無い
		return $shop_boss_remuneration;
		exit();

	}

	//□各エリアの店長報酬の計算式
	//【札幌】【仙台】
	//売上-セラピスト報酬合計-ドライバー報酬合計-移動費(移動実費+ガソリン代+駐車場代+高速代）-カード手数料×20％

	//【大阪】
	//売上-セラピスト報酬合計-ドライバー報酬合計-移動費(移動実費+ガソリン代+駐車場代+高速代）-カード手数料×10％

	//【横浜】
	//売上×1％

	//超過手当もマイナス(超過手当はガソリン代に含まれている)

	$shop_boss_remuneration = 0;

	$sale_price = $genkin_price + $card_price;

	$movement_cost_all = $movement_cost + $driver_gasoline + $driver_parking + $driver_highway;

	/*
	if( ($shop_area=="sapporo") || ($shop_area=="sendai") ){

		$shop_boss_remuneration = intval(($sale_price-$therapist_remuneration-$driver_remuneration-$movement_cost_all-$card_commission)/5);

	}else if( $shop_area == "osaka" ){

		$shop_boss_remuneration = intval(($sale_price-$therapist_remuneration-$driver_remuneration-$movement_cost_all-$card_commission)/10);

	}else if( $shop_area == "yokohama" ){

		$shop_boss_remuneration = intval($sale_price/100);

	}
	*/

	$ws_Rate = 0;
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		if($ARRAY_ArShop[$n]["area"] == $shop_area) {
			$ws_Rate = 100 / $ARRAY_ArShop[$n]["bosrat"];
		}
	}

	if( $shop_area == "yokohama" ){
		$shop_boss_remuneration = intval($sale_price / 100);	//横浜店長報酬変更日チェックが無くても良いか？ by aida
	} else {
		$shop_boss_remuneration = intval(($sale_price-$therapist_remuneration-$driver_remuneration-$movement_cost_all-$card_commission) / $ws_Rate);
	}

	return $shop_boss_remuneration;
	exit();
}

//----店長報酬取得
function get_shop_boss_remuneration_for_sale_shop_2_common(
$genkin_price,$card_price,$therapist_remuneration,
$driver_remuneration,$movement_cost,$driver_gasoline,$driver_parking,$card_commission,
$shop_area,$lowest_guarantee,$driver_highway,$year,$month,$day,$therapist_remuneration_sum,
$staff_remuneration_sum,$driver_sonota,$driver_pay_finish){

global $ARRAY_ArShop;

	//セラピスト報酬に最低保証分も足す
	$therapist_remuneration = $therapist_remuneration + $lowest_guarantee;

	if( $shop_area == "tokyo" ){
		$shop_boss_remuneration = 0;		//東京には店長報酬は無い
		return $shop_boss_remuneration;
		exit();
	}

	//□各エリアの店長報酬の計算式
	//【札幌】【仙台】
	//売上-セラピスト報酬合計-ドライバー報酬合計-移動費(移動実費+ガソリン代+駐車場代+高速代）-カード手数料×20パーセント

	//【大阪】
	//売上-セラピスト報酬合計-ドライバー報酬合計-移動費(移動実費+ガソリン代+駐車場代+高速代）-カード手数料×10パーセント

	//【横浜】
	//(旧計算式)売上×1パーセント
	//(新計算式)大阪と同じ

	//超過手当もマイナス(超過手当はガソリン代に含まれている)

	$shop_boss_remuneration = 0;

	$sale_price = $genkin_price + $card_price;

	$movement_cost_all = $movement_cost + $driver_gasoline + $driver_parking + $driver_highway;

	/*
	if( ($shop_area=="sapporo") || ($shop_area=="sendai") ){

		$shop_boss_remuneration = intval(($sale_price-$therapist_remuneration-$driver_remuneration-$movement_cost_all-$card_commission)/5);

	}else if( $shop_area == "osaka" ){

		$shop_boss_remuneration = intval(($sale_price-$therapist_remuneration-$driver_remuneration-$movement_cost_all-$card_commission)/10);

	}else if( $shop_area == "yokohama" ){
		global $ARRAY_changeDate;
		$year_change = $ARRAY_changeDate["y"];
		$month_change = $ARRAY_changeDate["m"];
		$day_change = $ARRAY_changeDate["d"];

		$ts = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換
		$ts_change = get_timestamp_by_year_month_day_common($year_change,$month_change,$day_change);	//指定年月日をタイムスタンプ形式に変換

		if( $ts > $ts_change ){
			//大阪と同じ
			$shop_boss_remuneration = intval(($sale_price-$therapist_remuneration-$driver_remuneration-$movement_cost_all-$card_commission)/10);
		}else{
			$shop_boss_remuneration = intval($sale_price/100);
		}
	}
	*/

	$ws_Rate = 0;
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		if($ARRAY_ArShop[$n]["area"] == $shop_area) {
			$ws_Rate = 100 / $ARRAY_ArShop[$n]["bosrat"];
		}
	}
	if( $shop_area == "yokohama" && PHP_checkChangeDate("yokohama", $year, $month, $day) == false ) {	//変更日チェック in common/include/const.php
		$shop_boss_remuneration = intval($sale_price/100);	//横浜の店長報酬切替日以前の場合
	} else {
		$shop_boss_remuneration = intval(($sale_price-$therapist_remuneration-$driver_remuneration-$movement_cost_all-$card_commission) / $ws_Rate);
	}

	return $shop_boss_remuneration;
	exit();
}

//----最低保証取得
function get_lowest_guarantee_common($area,$start_time,$end_time){
global $ARRAY_ArShop;

	$lowest_guarantee = 0;

	if($area == "tokyo"){
		if($start_time<="4"){$start_time = "5";}
		if($end_time>"23"){$end_time = "23";}
	}else{
		if($start_time<="4"){$start_time = "5";}
		if($end_time>"21"){$end_time = "21";}
	}
	

	$sa = $end_time - $start_time;
	
	/*
	if( ($area=="tokyo") || ($area=="yokohama") || ($area=="osaka") || ($area=="tokyo_reraku") || ($area=="tokyo_bigao") || ($area=="sendai") ){

		if( $sa >= 12 ){
			$lowest_guarantee = 5000;
		}
	}else if($area=="sapporo"){
		if( $sa >= 12 ){
			$lowest_guarantee = 3000;
		}
	}
	*/
	
	if( $sa >= 12 ) {
		for($n=0; $n<count($ARRAY_ArShop); $n++) {
			//echo $area;
			if($ARRAY_ArShop[$n]["area"] == $area) {
				$lowest_guarantee = $ARRAY_ArShop[$n]["lwgrnt"];
				break;
			}
		}
	}

	return $lowest_guarantee;
	exit();
}

//----最低保証取得2
function get_lowest_guarantee_2_common($shop_area,$shop_name,$year,$month,$day){

	//$start_time = microtime(true);

	$shop_id = get_shop_id_by_shop_name_common($shop_name);

	$data = get_for_sale_shop_data_common($shop_id,$year,$month,$day);

	if( $data["id"] != "" ){
		$lowest_guarantee = $data["lowest_guarantee"];
		return $lowest_guarantee;
		exit();
	}

	$value = 0;

	if( $shop_area == "tokyo" ){
		if( $shop_name == "東京リフレ" ){
			$value = get_lowest_guarantee_by_area_common($shop_area,$year,$month,$day);
		}
	}else if( $shop_area == "osaka" ){
		if( $shop_name == "大阪リフレ" ){
			$value = get_lowest_guarantee_by_area_common($shop_area,$year,$month,$day);
		}
	}else{
		$value = get_lowest_guarantee_by_area_common($shop_area,$year,$month,$day);
	}

	return $value;
	exit();
}

//----チーフ報酬取得
function get_chief_allowance_by_shop_name_common($year,$month,$day,$shop_name){

	//$start_time = microtime(true);

	$chief_allowance_all = 0;

	$area = get_area_by_shop_name_common($shop_name);	//店舗のエリア取得 in common/include/functions.php

	if( $area == "tokyo" ){

		if( $shop_name != "東京リフレ" ){
			return $chief_allowance_all;
			exit();
		}
	}else if( $area == "osaka" ){

		if( $shop_name != "大阪リフレ" ){
			return $chief_allowance_all;
			exit();
		}
	}

	$therapist_data = get_therapist_data_for_sale_therapist_common($year,$month,$day,$area);	//出勤しているセラピスト情報を取得 in common/include/functions.php
	$therapist_data_num = count($therapist_data);

	for($i=0;$i<$therapist_data_num;$i++){

		$chief_allowance = $therapist_data[$i]["chief_allowance"];
		$therapist_id = $therapist_data[$i]["therapist_id"];

		$chief_allowance = get_chief_allowance_by_therapist_id_and_day_common($therapist_id,$year,$month,$day);		//チーフ手当取得 in common/include/functions.php
		$chief_allowance_all = $chief_allowance_all + $chief_allowance;

	}

	return $chief_allowance_all;
	exit();
}

//----PRコメントフィールド名取得
function get_pr_name_common($area){

	if( $area == "tokyo" ){
		$pr_name = "pr_refle";
	}else if( $area == "sapporo" ){
		$pr_name = "pr_sapporo";
	}else{
		$pr_name = "pr_new";
	}

	return $pr_name;
	exit();
}


//----売上店舗記録データ取得サブ
function get_sale_shop_data_day_2_common($year,$month,$day,$shop_name,$shop_area){

	$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

	if( $result == false ){

		$genkin_price = 0;
		$card_price = 0;
		$card_commission = 0;
		$therapist_remuneration = 0;
		$pay_day_therapist = 0;
		$therapist_remuneration_mibarai = 0;
		$allowance_jisou = 0;
		$allowance_therapist = 0;
		$driver_remuneration = 0;
		$driver_gasoline = 0;
		$driver_parking = 0;
		$driver_allowance = 0;
		$driver_highway = 0;
		$driver_pay_finish = 0;
		$lowest_guarantee = 0;
		$therapist_remuneration_sum = 0;
		$pay_day_driver = 0;
		$driver_remuneration_mibarai = 0;
		$driver_remuneration_sum = 0;
		$movement_cost = 0;
		$shop_boss_remuneration = 0;
		$driver_sonota = 0;
		$gross_profit = 0;
		$office_remuneration_mibarai = 0;
		$pay_day_office = 0;
		$office_allowance = 0;
		$office_remuneration_sum = 0;

	}else{

		$result = check_kirikae_card_commission_calculation_common($year,$month,$day);		//カード手数料切替日チェック

		if( $result == false ){

			$data = get_genkin_price_card_price_card_commission_old_common($year,$month,$day,$shop_name);	//旧料金取得（現金、カード）

			$genkin_price = $data["genkin_price"];
			$card_price = $data["card_price"];
			$card_commission = $data["card_commission"];
		}else{
			$data = get_genkin_price_card_price_card_commission_new_common($year,$month,$day,$shop_name);	//新料金取得（現金、カード）

			$genkin_price = $data["genkin_price"];
			$card_price = $data["card_price"];
			$card_commission = $data["card_commission"];
		}

		$therapist_remuneration = get_remuneration_at_sale_shop_2_common($year,$month,$day,$shop_name);		//報酬総額集計

		//日払い報酬取得
		$pay_day_therapist = get_pay_day_therapist_common($year,$month,$day,$shop_name,$shop_area);		//日払い報酬取得

		$therapist_remuneration_mibarai = $therapist_remuneration - $pay_day_therapist;

		//セラピスト自走手当
		$allowance_jisou = get_allowance_jisou_therapist_day_common($year,$month,$day,$shop_name,$shop_area);	//セラピスト自走手当取得

		//セラピスト手当
		$allowance_therapist = get_allowance_therapist_day_common($year,$month,$day,$shop_name,$shop_area);		//セラピスト手当取得

		$allowance_therapist = $allowance_therapist + $allowance_jisou;

		//=======内勤スタッフ====================
		$data = get_office_payment_for_sale_shop_common($year,$month,$day,$shop_area);		//事務所支払い

		$office_remuneration = $data["remuneration"];
		$office_gasoline = $data["gasoline"];
		$office_allowance = $data["allowance"];
		$pay_day_office = 0;
		$office_remuneration_mibarai = $office_remuneration - $pay_day_office;
		$office_remuneration_sum = $office_remuneration + $office_allowance;

		//=======ドライバー====================
		$data = get_driver_payment_for_sale_shop_2_common($year,$month,$day,$shop_area);	//ドライバー支払い明細取得

		$driver_remuneration = $data["remuneration"];
		$driver_gasoline = $data["gasoline"];
		$driver_parking = $data["parking"];
		$driver_allowance = $data["allowance"];
		$driver_highway = $data["highway"];
		$driver_pay_finish = $data["pay_finish"];

		//ドライバー日払い
		$pay_day_driver = get_pay_day_driver_common($year,$month,$day,$shop_name,$shop_area);	//ドライバー日払い集計

		//未払い報酬(ドライバー)
		$driver_remuneration_mibarai = $driver_remuneration - $pay_day_driver;
		//echo "shop_area line:" . __LINE__ . " " . $driver_remuneration_mibarai . " = " . $driver_remuneration . " - " . $pay_day_driver . "<br />";

		//報酬合計(ドライバー)
		$driver_remuneration_sum = $driver_remuneration + $driver_allowance;

		//これが重い ---> セラピストの報酬総額集計の処理でやるべきで改修予定とする！
		$lowest_guarantee = get_lowest_guarantee_2_common($shop_area,$shop_name,$year,$month,$day);	//最低保証取得2 in common/include/shop_area_list.php

		//セラピスト報酬合計
		$therapist_remuneration_sum = $therapist_remuneration + $lowest_guarantee + $allowance_therapist;

		$movement_cost = get_movement_cost_for_sale_shop_common($shop_area,$year,$month,$day);	//移動費用取得

		if(
				(($shop_area == "tokyo") && ($shop_name != "東京リフレ")) ||
				(($shop_area == "osaka") && ($shop_name != "大阪リフレ"))
		){

			$driver_remuneration_sum = 0;
			$driver_remuneration_mibarai = 0;
			$driver_remuneration = 0;
			$movement_cost = 0;
			$driver_gasoline = 0;
			$driver_parking = 0;
			$driver_allowance = 0;
			$driver_highway = 0;
			$driver_pay_finish = 0;
			$office_remuneration_mibarai = 0;
			$pay_day_office = 0;
			$office_allowance = 0;
			$office_remuneration_sum = 0;
			$office_gasoline = 0;

		}

		//ドライバー、社用車、内勤
		$driver_gasoline = $driver_gasoline + $office_gasoline;

		$staff_remuneration_sum = $driver_remuneration_sum + $office_remuneration_sum;

		$driver_sonota = get_driver_sonota_day_common($year,$month,$day,$shop_area);	//出勤データ取得（スタッフ）その他費用

		//----店長報酬取得
		$shop_boss_remuneration = get_shop_boss_remuneration_for_sale_shop_2_common(
$genkin_price,$card_price,$therapist_remuneration,
$driver_remuneration,$movement_cost,$driver_gasoline,$driver_parking,$card_commission,
$shop_area,$lowest_guarantee,$driver_highway,$year,$month,$day,$therapist_remuneration_sum,
$staff_remuneration_sum,$driver_sonota,$driver_pay_finish);

		$gross_profit = 0;

		//----粗利計算 in common/include/functions.php
		$gross_profit = get_gross_profit_for_sale_shop_common($genkin_price,$card_price,$therapist_remuneration_sum,
$staff_remuneration_sum,$movement_cost,$driver_gasoline,$driver_parking,$driver_highway,$card_commission,
$shop_boss_remuneration,$driver_sonota,$driver_pay_finish);

	}

	$data["genkin_price"] = $genkin_price;
	$data["card_price"] = $card_price;
	$data["card_commission"] = $card_commission;
	$data["therapist_remuneration"] = $therapist_remuneration;
	$data["pay_day_therapist"] = $pay_day_therapist;
	$data["therapist_remuneration_mibarai"] = $therapist_remuneration_mibarai;
	$data["allowance_jisou"] = $allowance_jisou;
	$data["allowance_therapist"] = $allowance_therapist;
	$data["driver_remuneration"] = $driver_remuneration;
	$data["driver_gasoline"] = $driver_gasoline;
	$data["driver_parking"] = $driver_parking;
	$data["driver_allowance"] = $driver_allowance;
	$data["driver_highway"] = $driver_highway;
	$data["driver_pay_finish"] = $driver_pay_finish;
	$data["lowest_guarantee"] = $lowest_guarantee;
	$data["therapist_remuneration_sum"] = $therapist_remuneration_sum;
	$data["pay_day_driver"] = $pay_day_driver;
	$data["driver_remuneration_mibarai"] = $driver_remuneration_mibarai;
	$data["driver_remuneration_sum"] = $driver_remuneration_sum;
	$data["movement_cost"] = $movement_cost;
	$data["shop_boss_remuneration"] = $shop_boss_remuneration;
	$data["driver_sonota"] = $driver_sonota;
	$data["gross_profit"] = $gross_profit;
	$data["office_remuneration_mibarai"] = $office_remuneration_mibarai;
	$data["pay_day_office"] = $pay_day_office;
	$data["office_allowance"] = $office_allowance;
	$data["office_remuneration_sum"] = $office_remuneration_sum;

	return $data;
	exit();
}

//----店長報酬取得(横浜)
function get_shop_boss_remuneration_yokohama_common($genkin_price,$card_price,$therapist_remuneration_sum,
$driver_remuneration_sum,$movement_cost,$driver_gasoline,$driver_parking,$driver_highway,$card_commission,
$driver_sonota,$driver_pay_finish){

	//「売上」-「報酬合計(セラピスト)」-「報酬合計(ドライバー)」-「移動実費」-「ガソリン代」-「駐車場代」-「高速代」-
	//「カード手数料」-「その他(ドライバー)」+「清算済み(ドライバー)」
	//=(店長報酬を含めない粗利益)

	$sale_price = $genkin_price + $card_price;

	$remuneration = $sale_price - $therapist_remuneration_sum - $driver_remuneration_sum - $movement_cost -
					$driver_gasoline - $driver_parking - $driver_highway - $card_commission - $driver_sonota + $driver_pay_finish;

	$remuneration = intval($remuneration/11);

	return $remuneration;
	exit();
}

function PHP_get_select_frm_area($area, $u_mode) {
global $ARRAY_Area2;

	if($u_mode) {
		if($area == -1) $ws_Selected = " selected"; else $ws_Selected = "";
		$html .= '<option value="-1"' .  $ws_Selected . '>未選択</option>';
	}
	for($a=0; $a<count($ARRAY_Area2); $a++) {
		if($ARRAY_Area2[$a] == $area) $ws_Selected = " selected"; else $ws_Selected = "";
		$html .= '<option value="' . $ARRAY_Area2[$a]["area"] . '"' .  $ws_Selected . '>' . $ARRAY_Area2[$a]["area_name"] . '</option>';
	}

	return $html;
}

//----エリア別コース種別配列取得
function get_course_array_by_area_common($area){

	if( $area == "tokyo" || $area == "yokohama"){

		$data = array(
			"0" => "90分コース(14,000円+交通費)",
			"1" => "120分コース(19,000円+交通費)",
			"2" => "150分コース(23,000円+交通費)",
			"3" => "180分コース(28,000円+交通費)",
			"4" => "210分コース(32,000円+交通費)",
			"5" => "240分コース(37,000円+交通費)",
			"6" => "未定"
		);

	}else if( $area == "sapporo" || $area == "sendai" || $area == "fukuoka"){

		$data = array(
			"0" => "90分コース(11,000円+交通費)",
			"1" => "120分コース(14,000円+交通費)",
			"2" => "150分コース(18,000円+交通費)",
			"3" => "180分コース(22,000円+交通費)",
			"4" => "210分コース(25,000円+交通費)",
			"5" => "240分コース(28,000円+交通費)",
			"6" => "未定"
		);

	}else if( $area == "osaka" ){

		$data = array(
			"0" => "90分コース(12,000円)",
			"1" => "120分コース(16,000円)",
			"2" => "150分コース(20,000円)",
			"3" => "180分コース(24,000円)",
			"4" => "210分コース(28,000円)",
			"5" => "240分コース(32,000円)",
			"6" => "未定"
		);

	}else{
		echo "error!(get_course_array_by_area_common)";
		exit();
	}

	return $data;
	exit();
}

//----コース別料金情報配列の取得
function PHP_getArrayCourse() {
global $ARRAY_ArShop;

	$sql = sprintf("select * from shop_fee where delete_flg=0 order by shop_id,corstm");

	$res = mysql_query($sql, DbCon);
	if($res == false) {
		return;
	}
	$ws_qShopId = -1;
	$t = 0;
	while($ws_Row = mysql_fetch_assoc($res)) {
		if($ws_Row["shop_id"] != $ws_qShopId) {
			$ws_qShopId = $ws_Row["shop_id"];
			$ws_area = "";
			for($a=0; $a<count($ARRAY_ArShop); $a++) {
				if($ARRAY_ArShop[$a]["id"] == $ws_qShopId) {
					$ws_area = $ARRAY_ArShop[$a]["area"];
					break;
				}
			}
			if(!$ws_area) continue;
			$t = 0;
		}
		$ws_kakaku = $ws_Row["price"] - $ws_Row["dscont"];
		$ws_ArRec[$ws_area][$t] = array("id" => $ws_Row["shop_id"], "corstm" => $ws_Row["corstm"], "name" => $ws_Row["name"], "price" => $ws_Row["price"], "dscont" => $ws_Row["dscont"], "dsordr" => $ws_Row["dsordr"], "kakaku" => $ws_kakaku);
		$t++;
	}
	return $ws_ArRec;
}

//----コース別料金情報配列の取得
function PHP_getArrayChargeEx($u_area) {
global $ARRAY_courseFee;
	return $ARRAY_courseFee[$u_area];
}

//----コース別料金情報配列の取得
function PHP_getArrayCharge($u_area) {
global $ARRAY_ArShop;

	$ws_id = -1;
	for($n=0; $n<count($ARRAY_ArShop); $n++) {
		if($ARRAY_ArShop[$n]["area"] == $u_area) {
			$ws_id = $ARRAY_ArShop[$n]["id"];
			break;
		}
	}

	if($ws_id > 0) {
		$sql = sprintf("select * from shop_fee where shop_id=%s order by corstm", $ws_id);
		$res = mysql_query($sql, DbCon);
		if($res == false) {
			return;
		}
		$n = 0;
		while($ws_Row = mysql_fetch_assoc($res)) {
			$ws_ArRec[$n] = $ws_Row;
			$n++;
		}
		return $ws_ArRec;
	}
	return false;
}

//----料金の取得
function PHP_getChargeByTime($u_time, &$u_ArCharge) {
	$ws_ret = -1;
	for($n=0; $n<count($u_ArCharge); $n++) {
		if($u_ArCharge[$n]["corstm"] == $u_time) {
			$ws_ret = $u_ArCharge[$n]["price"] - $u_ArCharge[$n]["dscont"];
			break;
		}
	}
	return $ws_ret;
}

//----コース名から料金を取得
function PHP_getChargeByCourse($course, &$u_ArCharge) {

	$ws_time = str_replace("分","",$course);
	return PHP_getChargeByTime($ws_time, &$u_ArCharge);

}

//----コース時間から料金名を取得
function PHP_getDiscountNameByCourse($u_time, &$u_ArCharge){

	$ws_ret = -1;
	for($n=0; $n<count($u_ArCharge); $n++) {
		if($u_ArCharge[$n]["corstm"] == $u_time) {
			$ws_ret = $u_ArCharge[$n]["name"];
			break;
		}
	}
	return $ws_ret;
}

//----コース名から料金を取得
function PHP_getDiscountByCourse($course, &$u_ArCharge){

	$ws_ret = -1;
	for($n=0; $n<count($u_ArCharge); $n++) {
		if($u_ArCharge[$n]["name"] == $course) {
			$ws_ret = $u_ArCharge[$n]["price"] - $u_ArCharge[$n]["dscont"];
			break;
		}
	}
	return $ws_ret;
}
?>
