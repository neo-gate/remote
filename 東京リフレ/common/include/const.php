<?php
/* =================================================================================
		Script Name	const.php(定数)
		Author		aida
		Create Date	2017/11/16
		Update Date	2018/02/02
		Description	共通処理
================================================================================= */

const CONST_charUnSet = "**未設定**";
const CONST_limitTime = 9;
const MAIL_mainUSER = "info@neo-gate.jp";
const S3_URL = "http://s3-refle.s3-website-ap-northeast-1.amazonaws.com/";
const DEFAULT_therapist_img = "img/therapist_page/tokyo/14794612031.jpg";
const DEFAULT_therapist_img_m = "img/ssl/vip/calendar/icon_sample.gif";

const CARD_commission1 = 325;		//カード手数料率1
const CARD_commission2 = 375;		//カード手数料率2

const CONST_system_BeginYmd		= 20140101;		//システム開始日
const CONST_shimei_value		= 1000;	//指名料
const CONST_transportation		= 500;	//交通費(自走手当)
const CONST_incentive_repeater	= 5000;	//リピーター　インセンティブ
const CONST_insurance_price		= 50;	//保険料

//$ARRAY_flesh_ShareTime = array(0, 100);
//$ARRAY_flesh_ShareRate = array(35, 40);
const CONST_flesh_ShareRate_Shimei = 50;
const CONST_ShareRate_Shimei = 10;

const CONST_point_Operation = 1;	//施術ポイント
const CONST_point_Repeat = 5;		//リピータポイント
const CONST_point_Shimei = 3;		//指名ポイント

define("BBS_url", "http://" . $_SERVER["SERVER_NAME"] . "/bbs/common/");

$ARRAY_Week = array("0" => "日", "1" => "月", "2" => "火", "3" => "水", "4" => "木", "5" => "金", "6" => "土");

$ARRAY_PayMethod = array(array("id" => -1, "name" => "変更なし", "selected" => ""), array("id" => 0, "name" => "現金", "selected" => ""), array("id" => 1, "name" => "カード", "selected" => ""));

$ARRAY_type = array("h" => array("type" => "honbu", "name" => "本部", "max_id" => 31)
	, "d" => array("type" => "driver", "name" => "ドライバー", "max_id" => 31)
	, "t" => array("type" => "therapist", "name" => "セラピスト", "max_id" => 23, "kensyuu_max_id" => 13)
	);

$ARRAY_mode = array("U" => "編集", "I" => "登録", "D" => "削除");

$ARRAY_sex = array("1" => "男性", "2" => "女性");

$GL_changeDate4card = 20150417;		//カード手数料切替日

$GL_changeDate4yokohama = 20150831;	//横浜店長報酬変更日

function PHP_get_skill_data() {

	$skill_data = array(
		"1"=>"アロマ",
		"2"=>"バリ式",
		"3"=>"セルライト",
		"4"=>"カイロ",
		"5"=>"フェイシャル",
		"6"=>"ヘッド",
		"7"=>"小顔",
		"8"=>"骨盤矯正",
		"9"=>"ロミロミ",
		"10"=>"リンパ",
		"11"=>"マタニティー",
		"12"=>"リフレ",
		"13"=>"整体",
		"14"=>"指圧(ボディケア)",
		"15"=>"ストレッチ",
		"16"=>"スウェディッシュ",
		"17"=>"タイ古式",
		"18"=>"強もみ",
		"19"=>"眼精疲労ケア",
		"20"=>"痩身"
	);

	return $skill_data;
}

function PHP_get_skill_font() {

	$skill_class = array(
		"1"=>"24",
		"2"=>"24",
		"3"=>"24",
		"4"=>"24",
		"5"=>"20",
		"6"=>"24",
		"7"=>"24",
		"8"=>"24",
		"9"=>"24",
		"10"=>"24",
		"11"=>"20",
		"12"=>"24",
		"13"=>"24",
		"14"=>"16",
		"15"=>"24",
		"16"=>"16",
		"17"=>"24",
		"18"=>"24",
		"19"=>"16",
		"20"=>"24"
	);
	return $skill_class;
}

//----変更日チェック
function PHP_checkChangeDate($u_mode, $u_Y, $u_m, $u_d) {
global $GL_changeDate4card, $GL_changeDate4yokohama;

	$ws_Ymd = $u_Y * 10000 + $u_m * 100 + $u_d;

	$ws_Ret = false;

	if($u_mode == "yokohama") {
		if( $ws_Ymd > $GL_changeDate4yokohama ) $ws_Ret = true;
	} elseif($u_mode == "card") {
		if( $ws_Ymd > $GL_changeDate4card ) $ws_Ret = true;
	}

	return $ws_Ret;
}

$ARRAY_Course = array(
    "1" => "90",
	"2" => "120",
	"3" => "150",
	"4" => "180",
	"5" => "210",
	"6" => "240",
	"7" => "270",
	"8" => "300",
	"9" => "330",
	"10" => "360",
	"11" => "390",
	"12" => "420",
	"13" => "450",
	"14" => "480"
);

$ARRAY_Extension = array(
    "0" => "30",
	"1" => "60",
	"2" => "90",
	"3" => "120",
	"4" => "150",
	"5" => "180",
	"6" => "210",
	"7" => "240",
	"8" => "270",
	"9" => "300",
	"10" => "330",
	"11" => "360",
	"12" => "390",
	"13" => "420"
);

$ARRAY_Transportation = array(
    "0" => "なし",
	"1000" => "1,000円",
	"2000" => "2,000円",
	"3000" => "3,000円",
	"4000" => "4,000円",
	"5000" => "5,000円"
);
//==================================================================================

function toNull($u_str) {
	if(strlen(trim($u_str)) == 0) {
		$ws_ret = "null";
	} else {
		$ws_ret = "'" . $u_str . "'";
	}
	return $ws_ret;
}

function toZero($u_str) {
	if(strlen(trim($u_str)) == 0) {
		$ws_ret = "0";
	} else {
		$ws_ret = $u_str;
	}
	return $ws_ret;
}

//----カレンダー小窓HTML作成
function PHP_get_calendar($u_y, $u_m, $u_url) {

	$html = "";

	$html .= sprintf('<input type="hidden" name="calendar_year" value="%s" id="calendar_year" />', $u_y);
	$html .= sprintf('<input type="hidden" name="calendar_month" value="%s" id="calendar_month" />', $u_m);

	$ws_time = mktime(0, 0, 0, $u_m, 1, $u_y);
	$week_value = date("w", $ws_time);
	if( $week_value == "0" ) $pre_disp_num =  7; else $pre_disp_num =  $week_value;

	$last_day_now = date("t", $ws_time);
	$last_day_pre = date('t', mktime(0, 0, 0, ($u_m-1), 1, $u_y));

	$pre_disp_start_day = ($last_day_pre - $pre_disp_num) + 1;

	$html .= '<div id="calendar_disp_top">';
		$type = "kako";
		$html .= '<div class="left1"><img src="' . REFLE_WWW_URL . 'img/calendar/calendar_left_pc.jpg" onclick="calendar_move(' . $u_y . ',' . $u_m . ',\'' . $type . '\',\'' . $u_url .'\');" /></div>';
		$html .= '<div class="left2">' . sprintf('%s年%s月',$u_y,$u_m) . '</div>';

		$type = "mirai";
		$html .= '<div class="left3"><img src="' . REFLE_WWW_URL . 'img/calendar/calendar_right_pc.jpg" onclick="calendar_move(' . $u_y . ',' . $u_m . ',\'' . $type . '\',\'' . $u_url .'\');" /></div>';

		$html .= '<br class="clear" />';
	$html .= '</div>';

	$html .= '<div id="calendar_disp">';
		$html .= '<div class="title">日</div>';
		$html .= '<div class="title">月</div>';
		$html .= '<div class="title">火</div>';
		$html .= '<div class="title">水</div>';
		$html .= '<div class="title">木</div>';
		$html .= '<div class="title">金</div>';
		$html .= '<div class="title">土</div>';
		for( $i=$pre_disp_start_day; $i<=$last_day_pre; $i++ ){
			$html .= '<div class="out">'.$i.'</div>';
		}
		for( $d=1; $d<=$last_day_now; $d++ ){
			$html .= sprintf('<a href="%syear=%s&month=%s&day=%s" class="in">%s</a>', $u_url, $u_y, $u_m, $d, $d);
		}
		$ws_time = mktime(0, 0, 0, $u_m, $last_day_now, $u_y);
		$week_value = date("w", $ws_time);

		$next_disp_num = -1 * ($week_value - 6);
		if($next_disp_num == 0 ) $next_disp_num = 7;

		for( $i=1; $i<=$next_disp_num; $i++ ){
			$html .= '<div class="out">'.$i.'</div>';
		}
	$html .= '</div>';
	$html .= '<br class="clear" />';

	$html .= '<div id="calendar_bottom">';
	$html .= '<div id="calendar_disp_close" onclick="calendar_close();">×&nbsp;閉じる</div>';
	$html .= "</div>";

	return $html;
}

function PHP_get_area_name($u_area, &$ARRAY_Area) {

	switch($u_area) {
		case "tokyo_reraku":
		case "tokyo_bigao":
			$ws_area_name = "東京";
			break;
		case "chiba":
			$ws_area_name = "千葉";
			break;
		default:
			$ws_flag = false;
			foreach($ARRAY_Area as $ws_Key => $ws_val) {
				if($u_area == $ws_Key) {
					$ws_area_name = $ws_val;
					$ws_flag = true;
					break;
				}
			}
			if(!$ws_flag) $ws_area_name = "不明";
	}
	return $ws_area_name;
}

//----エラーログ出力
function PHP_error_log($u_messe, $u_line) {

	$now = date("Y-m-d H:i:s", time());
	$ws_message = $now . " " . $_SERVER["SCRIPT_NAME"];
	if($u_line) $ws_message .= " line:" . $u_line;
	$ws_message .= " " . $u_messe;

	$fp = fopen(MAN_ROOT_PATH . "error.txt", "a");
	fwrite($fp, $ws_message);
	fclose($fp);
}

//----Checked文字列取得
function PHP_getCheckedText($u_Data) {
	if($u_Data == "1") return " checked"; else return "";
}
//----Checked表示名取得
function PHP_getCheckedName($u_Data) {
	if($u_Data == "1") return "あり"; else return "なし";
}

//----メール送信
function PHP_sendMail($u_MailTo, $u_Subject, $u_Content) {
	mb_language("ja");
	mb_internal_encoding("UTF-8");

	$ws_header = "From: info@neo-gate.jp\n";
	$ws_header .= "Bcc: info@neo-gate.jp";
	return mb_send_mail($u_MailTo, $u_Subject, $u_Content, $ws_header, MAIL_PARAMETER);
}

//----n月加減日計算
function PHP_getNextMonthDay($u_Ymd = 0, $u_AddMonth = 1) {
	$ws_thisTime = time();
	if(site_SERVER_name == "stg") $ws_thisTime = PHP_getNowTime();	//insert by aida at 20180802
	$ws_H = date("H", $ws_thisTime);
	$ws_i = date("i", $ws_thisTime);
	$ws_s = date("s", $ws_thisTime);

	if($u_Ymd) {
		$ws_thisTime = mktime(0, 0, 0, (floor($u_Ymd/100)%100), ($u_Ymd%100), floor($u_Ymd/10000));
	}
	$ws_StrAdd = $u_AddMonth . " months";
	$ws_thisTime = strtotime($ws_StrAdd, $ws_thisTime);
	return $ws_thisTime;
}

//----本日取得
function PHP_getThisDay($u_Ymd = 0) {

	$ws_thisTime = time();
	$ws_H = date("H", $ws_thisTime);
	$ws_i = date("i", $ws_thisTime);
	$ws_s = date("s", $ws_thisTime);
	if($u_Ymd) {
		$ws_thisTime = mktime($ws_H, $ws_i, $ws_s, (floor($u_Ymd/100)%100), ($u_Ymd%100), floor($u_Ymd/10000));
		$ws_PrevTime = mktime($ws_H, $ws_i, $ws_s, (floor($u_Ymd/100)%100), ($u_Ymd%100 - 1), floor($u_Ymd/10000));
	} else {
		$ws_PrevTime = strtotime("-1 day");
	}

	if($ws_H <= 6){
		//昨日の日付
		$year = intval(date('Y', $ws_PrevTime));
		$month = intval(date('m', $ws_PrevTime));
		$day = intval(date('d', $ws_PrevTime));
		$week = intval(date('w', $ws_PrevTime));
	} else {
		$year = intval(date('Y', $ws_thisTime));
		$month = intval(date('m', $ws_thisTime));
		$day = intval(date('d', $ws_thisTime));
		$week = intval(date('w', $ws_thisTime));
	}
	$ws_Ymd = $year * 10000 + $month * 100 + $day;
	$ws_ArRet = array("year" => $year, "month" => $month, "day" => $day, "week" => $week, "now_hour" => $ws_H, "Ymd" => $ws_Ymd, "time" => $ws_thisTime);
	return $ws_ArRet;
}

//----日付配列取得
function PHP_getArDates($u_maxDay, &$u_ArThisDate) {
global $ARRAY_Week;

	$day_array = array();
	$uru_flag = false;

	$year = $u_ArThisDate["year"];
	$month = $u_ArThisDate["month"];
	$day = $u_ArThisDate["day"];
	$week = $u_ArThisDate["week"];

	for($i=0; $i<$u_maxDay; $i++){
		$day_array[$i]["year"] = $year;
		$day_array[$i]["month"] = $month;
		$day_array[$i]["day"] = $day;
		$day_array[$i]["week"] = $week;
		$day_array[$i]["week_name"] = $ARRAY_Week[$week];
		$day_array[$i]["Ymd"] = $year * 10000 + $month * 100 + $day;
		$day_array[$i]["Y_m_d"] = $year . "/" . substr("00" . $month, -2) . "/" . substr("00" . $day, -2);
		$day_array[$i]["ymdId"] = $year . "_" . $month . "_" . $day;
		//うるう年判定
		if(($year%4)==0){
			if(($year%100)==0){
				if(($year%400)==0){
					$uru_flag = true;
				}else{
					$uru_flag = false;
				}
			}else{
				$uru_flag = true;
			}
		}else{
			$uru_flag = false;
		}
		if($month==2){
			if($day==28){
				if($uru_flag==true){
					$day = 29;
				}else{
					$day = 1;
					$month++;
				}
			}else if($day==29){
				$day = 1;
				$month++;
			}else{
				$day++;
			}
		}else{
			if(($month==4)||($month==6)||($month==9)||($month==11)){
				if($day==30){
					$day = 1;
					$month++;
				}else{
					$day++;
				}
			}else{
				if($day==31){
					$day = 1;
					if($month==12){
						$month = 1;
						$year++;
					}else{
						$month++;
					}
				}else{
					$day++;
				}
			}
		}
		if($week==6){
			$week = 0;
		}else{
			$week++;
		}
	}

	return $day_array;
}

//----ドライバー名取得
function PHP_get_driver_name($u_id, $u_name) {

	$ret = "";
	if( $u_id == "-1" || $u_id == "" ) {
		$ret = "なし";
	} elseif( $u_id == "-2" ){
		$ret = "TAXI";
	}elseif( $u_id == "-3" ){
		$ret = "本部";
	} else {
		$ret = $u_name;
	}
	return $ret;
}

//----メニュー編集
function PHP_get_menu($login_auth_type, $u_area, $u_area_name, &$u_ArAuth) {
global $ARRAY_allowUrl;

	$page_data = $ARRAY_allowUrl;

	//print_r($page_data);

	/* test
	$u_ArAuth["all_page"] = 0;
	$u_ArAuth["shift_page"] = 1;
	$u_ArAuth["shift_edit"] = 1;
	$u_ArAuth["board_page"] = 1;
	$u_ArAuth["board_car"] = 1;
	$u_ArAuth["shop_sale"] = 1;
	$u_ArAuth["shop_sale_2"] = 1;
	$u_ArAuth["operation_sale"] = 1;
	$u_ArAuth["move_cost"] = 1;
	$u_ArAuth["shift_training"] = 1;
	$login_auth_type = 2;
	//print_r($u_ArAuth);
	*/

	$html = "";

	if( $u_ArAuth["all_page"] == "1" ) {
		return "";
	} else {
		$html .= PHP_getHtmlMenu("shift_page", $u_ArAuth, $page_data, $u_area, $u_area_name);
		//$html .= PHP_getHtmlMenu("shift_edit", $u_ArAuth, $page_data, $u_area, $u_area_name);
		$html .= PHP_getHtmlMenu("board_page", $u_ArAuth, $page_data, $u_area, $u_area_name);
		//$html .= PHP_getHtmlMenu("board_car", $u_ArAuth, $page_data, $u_area, $u_area_name);
		$html .= PHP_getHtmlMenu("shop_sale", $u_ArAuth, $page_data, $u_area, $u_area_name);
		$html .= PHP_getHtmlMenu("shop_sale_2", $u_ArAuth, $page_data, $u_area, $u_area_name);
		$html .= PHP_getHtmlMenu("operation_sale", $u_ArAuth, $page_data, $u_area, $u_area_name);
		$html .= PHP_getHtmlMenu("move_cost", $u_ArAuth, $page_data, $u_area, $u_area_name);
		//$html .= PHP_getHtmlMenu("shift_training", $u_ArAuth, $page_data, $u_area, $u_area_name);

		if( $login_auth_type == "2" ){
			$html .= "<a href=\"" . $man_url . "fixtures/index.php\">備品状況</a>　　";
		}

		$html .= "<a href=\"" . $man_url . "logout.php\">ログアウト</a>";
	}
	$html = "<div id=\"staff_man_menu\">" . $html . "</div>";

	return $html;
}

//----メニュー編集サブ
function PHP_getHtmlMenu($u_KW, &$u_ArAuth, &$page_data, $u_area, $u_area_name) {
	$ws_Html = "";
	if( $u_ArAuth[$u_KW] == "1" ){
		$tmp_data = $page_data[$u_KW];
		$tmp_data_num = count($tmp_data);
		for( $i=0; $i<$tmp_data_num; $i++ ) {
			$url = $tmp_data[$i]["url"];
			$name = $tmp_data[$i]["name"];
			$menu_flg = $tmp_data[$i]["menu_flg"];
			if($menu_flg=="1") {
				$ws_Html = "<a href=\"" . $url . "?area=" . $u_area . "\">" . $name . "(" . $u_area_name . ")</a>　　";
			}
		}
	}
	return $ws_Html;
}

//----データベース接続クラス
/*
class DbSql {

	var $Db_con;
	var $Rec;

	function Connect() {
		$this->Db_con = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_NAME, $this->Db_con);
		mysql_query("SET NAMES utf8");
		return $this->Db_con;
	}

	function Query($u_sql) {
		$cls_Ret = mysql_query($u_sql, $Db_con);
		if($cls_Ret) {
			$cls_nums = mysql_num_rows($cls_Ret);
			switch($cls_nums) {
				case 0:
					return;
				default:
					$R = 0;
					while($cls_Rec = mysql_fetch_assoc($cls_Ret)) {
						$Rec[$R] = $cls_Rec;
						$R++;
					}
			}
		} else {
			return;
		}
	}
}
*/
//----時刻クラス
class refleTime {

	var $ArTime;

	function set() {

		$this->ArTime = array();

		for($n=0; $n<48; $n++) {
			$ws_Id = $n - 15;
			$ws_temp = $n + 20;
			$ws_H_ex = floor($ws_temp / 2);
			if($ws_H_ex >= 24) {
				$ws_H = $ws_H_ex - 24;
				//$ws_H_ex = $ws_H;
			} else {
				$ws_H = $ws_H_ex;
			}
			$ws_i = ($ws_temp % 2) * 30;
			$ws_Id_honbu = $ws_Id + 6;
			$ws_dspHi = $ws_H . "時" . $ws_i . "分";
			$ws_dspHi_ex = $ws_H_ex . "時" . $ws_i . "分";
			$ws_hm = $ws_H . ":" . substr("00" . $ws_i, -2);
			$ws_Hm = $ws_H_ex . ":" . substr("00" . $ws_i, -2);
			$this->ArTime[$n] = array("id" => $ws_Id, "hour" => $ws_H, "hour_ex" => $ws_H_ex, "minute" => $ws_i, "honbu_id" => $ws_Id_honbu, "dspHi" => $ws_dspHi, "dspHi_ex" => $ws_dspHi_ex, "hm" => $ws_hm, "Hm" => $ws_Hm);
		}
		return;
	}

	function getByIndex($u_Index) {
		return $this->ArTime[$u_Index];
	}

	function getById($u_Id, $u_type) {
		if($u_type == "h") $ws_cellName = "honbu_id"; else $ws_cellName = "id";
		$ws_Index = -1;
		for($n=0; $n<count($this->ArTime); $n++) {
			if($this->ArTime[$n][$ws_cellName] == $u_Id) {
				$ws_Index = $n;
				break;
			}
		}
		if($ws_Index == -1) {
			return;
		}
		return $this->ArTime[$ws_Index];
	}

	function getByHourMin($u_H, $u_I) {
		$ws_Index = -1;
		for($n=0; $n<count($this->ArTime); $n++) {
			if($this->ArTime[$n]["hour"] == $u_H && $this->ArTime[$n]["minute"] == $u_I) {
				$ws_Index = $n;
				break;
			}
		}
		if($ws_Index == -1) {
			return;
		}
		return $this->ArTime[$ws_Index];
	}

	function getAll() {
		$ws_retArray = array("all" => array(), "honbu" => array(), "driver" => array(), "therapist" => array(), "all_num" => 0, "honbu_num" => 0, "driver_num" => 0, "therapist_num" => 0, "honbu_maxId" => 0, "driver_maxId" => 0, "therapist_maxId" => 0);

		$i = 0;
		for($n=0; $n<count($this->ArTime); $n++) {
			if($this->ArTime[$n]["honbu_id"] < -9) continue;
			$ws_retArray["honbu"][$i] = $this->ArTime[$n];
			if($this->ArTime[$n]["honbu_id"] >= 37) break;
			$i++;
		}
		$ws_retArray["honbu_num"] = count($ws_retArray["honbu"]);
		$ws_retArray["honbu_maxId"] = $ws_retArray["honbu"][($ws_retArray["honbu_num"] - 1)]["id"] + 6;

		$i = 0;
		for($n=0; $n<count($this->ArTime); $n++) {
			if($this->ArTime[$n]["id"] < 1) continue;
			$ws_retArray["driver"][$i] = $this->ArTime[$n];
			if($this->ArTime[$n]["id"] >= 31) break;
			$i++;
		}
		$ws_retArray["driver_num"] = count($ws_retArray["driver"]);
		$ws_retArray["driver_maxId"] = $ws_retArray["driver"][($ws_retArray["driver_num"] - 1)]["id"];

		$i = 0;
		for($n=0; $n<count($this->ArTime); $n++) {
			if($this->ArTime[$n]["id"] < 1) continue;
			$ws_retArray["therapist"][$i] = $this->ArTime[$n];
			if($this->ArTime[$n]["id"] >= 23) break;
			$i++;
		}
		$ws_retArray["therapist_num"] = count($ws_retArray["therapist"]);
		$ws_retArray["therapist_maxId"] = $ws_retArray["therapist"][($ws_retArray["therapist_num"] - 1)]["id"];

		$ws_retArray["all"] = $this->ArTime;
		$ws_retArray["all_num"] = count($ws_retArray["all"]);

		return $ws_retArray;
	}
}

//----週クラス
class refleWeek {
	var $ArWeek;

	function set() {
		global $ARRAY_Week;

		$this->ArWeek = $ARRAY_Week;
	}

	function getWeek($u_year, $u_month, $u_day) {
		$ws_time = mktime(0, 0, 0, $u_month, $u_day, $u_year);
		$ws_week = (int)date("w", $ws_time);
		return $ws_week;
	}

	function getWeekName($u_year, $u_month, $u_day) {
		$ws_week = $this->getWeek($u_year, $u_month, $u_day);
		$ws_weekName = $this->ArWeek[$ws_week];
		return $ws_weekName;
	}

	function getDispWeekName($u_year, $u_month, $u_day) {
		$ws_week = $this->getWeek($u_year, $u_month, $u_day);
		if($ws_week == 0) {
			$ws_weekName = "<font color='red'>" . $this->ArWeek[$ws_week] . "</font>";
		} elseif($ws_week == 6) {
			$ws_weekName = "<font color='blue'>" . $this->ArWeek[$ws_week] . "</font>";
		} else {
			$ws_weekName = $this->ArWeek[$ws_week];
		}
		return $ws_weekName;
	}
}

//----本日クラス
class refleToday {

	var $ArToday;

	//----本日取得
	function set($u_Ymd = 0, $u_limitH = 6) {
		global $ARRAY_Week;

		$ws_thisTime = time();
		if(site_SERVER_name == "stg") $ws_thisTime = PHP_getNowTime();	//insert by aida at 20180802
		$ws_H = date("H", $ws_thisTime);
		$ws_i = date("i", $ws_thisTime);
		$ws_s = date("s", $ws_thisTime);
		$ws_Ymd = (int)date("Ymd", $ws_thisTime);
		if($u_Ymd && $u_Ymd < $ws_Ymd) {
			$ws_thisTime = mktime($ws_H, $ws_i, $ws_s, (floor($u_Ymd/100)%100), ($u_Ymd%100), floor($u_Ymd/10000));
			$ws_PrevTime = mktime($ws_H, $ws_i, $ws_s, (floor($u_Ymd/100)%100), ($u_Ymd%100 - 1), floor($u_Ymd/10000));
		} else {
			$ws_PrevTime = strtotime("-1 day", $ws_thisTime);
		}

		if(($u_Ymd == 0 || $u_Ymd >= $ws_Ymd) && $ws_H <= $u_limitH) {
			//----本日の場合かつ時刻が$u_limitH以前の時は昨日の日付
			$year = intval(date('Y', $ws_PrevTime));
			$month = intval(date('m', $ws_PrevTime));
			$day = intval(date('d', $ws_PrevTime));
			$week = intval(date('w', $ws_PrevTime));
		} else {
			$year = intval(date('Y', $ws_thisTime));
			$month = intval(date('m', $ws_thisTime));
			$day = intval(date('d', $ws_thisTime));
			$week = intval(date('w', $ws_thisTime));
		}
		$ws_Ymd = $year * 10000 + $month * 100 + $day;
		$ws_ymd = floor($ws_Ymd % 1000000);
		$ws_fYmd = $year . "-" . substr("00" . $month, -2) . "-" . substr("00" . $day, -2);
		$this->ArToday = array("year" => $year, "month" => $month, "day" => $day, "week" => $week, "week_name" => $ARRAY_Week[$week], "hour" => $ws_H, "minute" => $ws_i, "second" => $ws_s, "Ymd" => $ws_Ymd, "ymd" => $ws_ymd, "Y-m-d" => $ws_fYmd, "time" => date("Y-m-d H:i:s", $ws_thisTime), "now" => $ws_thisTime);
	}

	function get($u_Id) {
		switch($u_Id) {
			case "year":
				return $this->ArToday["year"];
			case "month":
				return $this->ArToday["month"];
			case "day":
				return $this->ArToday["day"];
			case "week":
				return $this->ArToday["week"];
			case "week_name":
				return $this->ArToday["week_name"];
			case "now_hour":
				return $this->ArToday["hour"];
			case "hour":
				return $this->ArToday["hour"];
			case "minute":
				return $this->ArToday["minute"];
			case "second":
				return $this->ArToday["second"];
			case "Ymd":
				return $this->ArToday["Ymd"];
			case "ymd":
				return $this->ArToday["ymd"];
			case "Y-m-d":
				return $this->ArToday["Y-m-d"];
			case "time":
				return $this->ArToday["time"];
			case "now":
				return $this->ArToday["now"];
			default:
				return $this->ArToday;
		}
	}
}

//----日付配列クラス
class refleDate {

	var $day_array;
	var $day_array_num;

	function set($u_maxDay = 62, $u_Today) {
		global $ARRAY_Week;

		$uru_flag = false;

		$year = $u_Today->get("year");
		$month = $u_Today->get("month");
		$day = $u_Today->get("day");
		$week = $u_Today->get("week");

		$this->day_array_num = $u_maxDay;

		for($i=0; $i<$u_maxDay; $i++){
			$this->day_array[$i]["year"] = $year;
			$this->day_array[$i]["month"] = $month;
			$this->day_array[$i]["day"] = $day;
			$this->day_array[$i]["week"] = $week;
			$this->day_array[$i]["week_name"] = $ARRAY_Week[$week];
			$this->day_array[$i]["Ymd"] = $year * 10000 + $month * 100 + $day;
			$this->day_array[$i]["Y_m_d"] = $year . "/" . substr("00" . $month, -2) . "/" . substr("00" . $day, -2);
			$this->day_array[$i]["ymdId"] = $year . "_" . $month . "_" . $day;
			if($week == 0) {
				$ws_tmpWeekName = "<font color='red'>" . $this->day_array[$i]["week_name"] . "</font>";
			} elseif($week == 6) {
				$ws_tmpWeekName = "<font color='blue'>" . $this->day_array[$i]["week_name"] . "</font>";
			} else {
				$ws_tmpWeekName = $this->day_array[$i]["week_name"];
			}
			$this->day_array[$i]["dspWeekName"] = $ws_tmpWeekName;
			$this->day_array[$i]["dspMd"] = $month . "/" . $day . "(" . $ws_tmpWeekName . ")";
			$this->day_array[$i]["flag"] = false;		//多目的追加フラグ
			//うるう年判定
			if(($year%4)==0){
				if(($year%100)==0){
					if(($year%400)==0){
						$uru_flag = true;
					}else{
						$uru_flag = false;
					}
				}else{
					$uru_flag = true;
				}
			}else{
				$uru_flag = false;
			}
			if($month==2){
				if($day==28){
					if($uru_flag==true){
						$day = 29;
					}else{
						$day = 1;
						$month++;
					}
				}else if($day==29){
					$day = 1;
					$month++;
				}else{
					$day++;
				}
			}else{
				if(($month==4)||($month==6)||($month==9)||($month==11)){
					if($day==30){
						$day = 1;
						$month++;
					}else{
						$day++;
					}
				}else{
					if($day==31){
						$day = 1;
						if($month==12){
							$month = 1;
							$year++;
						}else{
							$month++;
						}
					}else{
						$day++;
					}
				}
			}
			if($week==6){
				$week = 0;
			}else{
				$week++;
			}
		}
	}

	function get($u_Index) {
		if($u_Index == "all") {
			return $this->day_array;
		} elseif($u_Index == "last") {
			$ws_Last = count($this->day_array) - 1;
			return $this->day_array[$ws_Last];
		} else {
			return $this->day_array[$u_Index];
		}
	}

	function getVal($u_Index, $u_name) {
		if($u_Index == "last") {
			$ws_Index = count($this->day_array) - 1;
		} else {
			$ws_Index = $u_Index;
		}
		return $this->day_array[$ws_Index][$u_name];
	}

	function search($u_year, $u_month, $u_day) {
		$ws_Index = -1;
		for($n=0; $n<$this->day_array_num; $n++) {
			if($u_month && $u_day) {
				if($this->day_array[$n]["year"] == $u_year && $this->day_array[$n]["month"] == $u_month && $this->day_array[$n]["day"] == $u_day) {
					$ws_Index = $n;
					break;
				}
			} else {
				if($this->day_array[$n]["Ymd"] == $u_year) {
					$ws_Index = $n;
					break;
				}
			}
		}
		return $ws_Index;
	}

	function counts() {
		return count($this->day_array);
	}
}

//----休日クラス
class HolidayData {

	var $ArRec;
	var $nums;

	function set() {
		$sql = "select * from shop_holiday where delete_flg=0 order by id";
		$res = mysql_query($sql, DbCon);
		if( $res == false ) {
			return false;
		}
		$n = 0;
		while($row = mysql_fetch_assoc($res)) {
			$this->ArRec[$n] = $row;
			$n++;
		}
		if($n == 0) return false;
		$this->nums = $n;
		return true;
	}

	function getIndex($u_str) {
		$ws_idx = -1;
		for($n=0; $n<$this->nums; $n++) {
			if($this->ArRec[$n]["name"] == $u_str) {
				$ws_idx = $n;
				break;
			}
		}
		return $ws_idx;
	}

	function getContent($u_str) {
		$ws_idx = $this->getIndex($u_str);
		if($ws_idx < 0) {
			return "";
		}
		return $this->ArRec[$ws_idx]["content"];
	}

	function getDisplayFlag($u_str) {
		$ws_idx = $this->getIndex($u_str);
		if($ws_idx < 0) {
			return -1;
		}
		return $this->ArRec[$ws_idx]["display_flg"];
	}
}

//----スタッフクラス
class Staff {
	
	var $rec;

	function set($u_type, $u_id) {
		if($u_type == "t") $ws_Tbl = "therapist_new"; else $ws_Tbl = "staff_new_new";
		$sql = "select * from " . $ws_Tbl ." where id=" . $u_id;
		$res = mysql_query($sql, DbCon);
		if($res == false) {
			return;
		}
		$this->rec = mysql_fetch_assoc($res);
	}

	function get($u_cellName) {
		return $this->rec[$u_cellName];
	}
}
?>
