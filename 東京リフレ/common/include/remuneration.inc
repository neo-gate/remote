<?php
/* =================================================================================
		Script Name	remuneration.inc(報酬関係)
		Author		aida
		Create Date	2018/04/25
		Update Date	----/--/--
		Description	報酬関係処理
================================================================================= */

//----出勤データを取得（報酬計算目的）
class RemunerationData {

	var $ArAttend;
	var $ArKeyAtd;		//出勤データID配列
	var $ArKeyStaffYmd;
	var $strWhereAtend;

	var $ArStaff;		//スタッフ(セラピスト)データ配列
	var $ArKeyStaff;	//スタッフ(セラピスト)データID配列
	var $strWhereStaff;

	var $strWhereNonFixStaff;	//
	var $ArNonFixStaff;	//指定期間以前に固定ポイントになっていないスタッフID配列

	var $strWhereFreshStaffId;	//フレッシュ用
	var $ArFreshStaffId;

	var $shrcls;		//シェア率判定区分

	function set_one($u_attendance_id, $u_staff_type, $u_flagSyounin ,$boss_tp_id) {
	global $ARRAY_shopGroup;

		$this->setArray();

		if( $u_staff_type == "therapist" ) {
			//----セラピスト order_num,rank_order_numを追記 murase
			$ws_SQL = "select A.*,B.jitaku_taiki_flg as jitaku_taiki_flg,therapist_id as staff_id,(A.year*10000+A.month*100+A.day) as Ymd,B.area as B_area,B.name,name_furi,jisou_flg,point_ref,point_fix,from_unixtime(point_fix_start_time,'%Y%m%d') as point_fix_ymd,guarantee_rule,bank_info,furikomi_commission,furikomi_type,chief_allowance,from_unixtime(chief_allowance_start_time,'%Y%m%d') as chief_allowance_ymd,transport_cost,transfer_commission,therapist_type,payment_span,order_num,rank_order_num from attendance_new A";
			$ws_SQL .= " left outer join therapist_new B on A.therapist_id=B.id";
			$ws_SQL .= " where A.id=" . $u_attendance_id . " and A.today_absence='0' and A.kekkin_flg='0' and COALESCE(0,payment_span)!=1";
			if($u_flagSyounin != "all") {
				if($u_flagSyounin == "no") $ws_SQL .= " and A.syounin_state=0"; else $ws_SQL .= " and A.syounin_state=1";
			}
		} else {
			//----ドライバー及び本部スタッフ
			$ws_SQL = "select A.*,(A.year*10000+A.month*100+A.day) as Ymd,B.area as B_area,B.name,name_furi,bank_info,furikomi_commission,furikomi_type,chief_allowance,from_unixtime(chief_allowance_start_time,'%Y%m%d') as chief_allowance_ymd,B.area as B_area,B.type as B_type,pay_hour,B.pay_fix as B_pay_fix,B.fuel as B_fuel from attendance_staff_new A";
			$ws_SQL .= " left outer join staff_new_new B on A.staff_id=B.id";
			$ws_SQL .= " where A.id=" . $u_attendance_id . " and A.today_absence='0' and A.kekkin_flg='0'";
			if($u_flagSyounin != "all") {
				if($u_flagSyounin == "no") $ws_SQL .= " and A.syounin_state=0"; else $ws_SQL .= " and A.syounin_state=1";
			}
		}
		$sql = $ws_SQL;
		$res = mysql_query($sql, DbCon);
		//echo $res . "/" . $sql . "<br />";
		if( $res == false ) {
			echo "error!(RemunerationData->set_one) " . $sql;
			exit();
		}
		if($row = mysql_fetch_assoc($res)) {
			$this->setStaffData($u_staff_type, 0, $row);
			$this->shrcls = 0;
			for($n=0; $n<count($ARRAY_shopGroup); $n++) {
				if($ARRAY_shopGroup[$n]["area"] == $row["B_area"]) {
					$this->shrcls = $ARRAY_shopGroup[$n]["shrcls"];
					break;
				}
			}
			$ws_share_fix = 0;
			if($this->shrcls == 1) {		//シェア率判定区分
			//if($row["B_area"] == "sendai") {	//test
				//----フレッシュ用
				if($row["point_fix"] > 0 && ($row["point_fix_ymd"] <= $row["Ymd"] && $row["point_fix_ymd"] >= CONST_system_BeginYmd)) {
					$ws_share_fix = $row["point_fix"];	//固定シェア率
				} else {
					$this->strWhereFreshStaffId = $row["staff_id"];
					$this->ArFreshStaffId[0] = $row["staff_id"];		//スタッフID配列
				}
			} else {
				if($row["point_fix"] > 0 && ($row["point_fix_ymd"] <= $row["Ymd"] && $row["point_fix_ymd"] >= CONST_system_BeginYmd)) {
					$ws_share_fix = $row["point_fix"];	//固定シェア率
				}
			}
			$this->setData(0, $row, $ws_share_fix, $this->shrcls);
		}
	}

	function set($u_area, $u_staff_type, $u_FromTo, $u_staff_id, $u_flagSyounin) {
	global $ARRAY_shopGroup;

		$this->setArray();

		if( $u_staff_type == "therapist" ) {
			//----セラピスト
			$this->shrcls = 0;
			for($n=0; $n<count($ARRAY_shopGroup); $n++) {
				if($ARRAY_shopGroup[$n]["area"] == $u_area) {
					$this->shrcls = $ARRAY_shopGroup[$n]["shrcls"];
					break;
				}
			}
			// order_num,rank_order_numを追記 murase
			$ws_SQL = "select A.*,B.jitaku_taiki_flg as jitaku_taiki_flg,therapist_id as staff_id,(A.year*10000+A.month*100+A.day) as Ymd,B.area as B_area,B.name,name_furi,jisou_flg,point_ref,point_fix,from_unixtime(point_fix_start_time,'%Y%m%d') as point_fix_ymd,guarantee_rule,bank_info,furikomi_commission,furikomi_type,chief_allowance,from_unixtime(chief_allowance_start_time,'%Y%m%d') as chief_allowance_ymd,transport_cost,transfer_commission,therapist_type,payment_span,order_num,rank_order_num from attendance_new A";
			$ws_SQL .= " left outer join therapist_new B on A.therapist_id=B.id";
			$ws_SQL .= " where (year*10000+month*100+day)>=" . $u_FromTo["from"] . " and (year*10000+month*100+day)<=" . $u_FromTo["to"] . " and A.today_absence='0' and A.kekkin_flg='0'";
			if($u_flagSyounin != "all") {
				if($u_flagSyounin == "no") $ws_SQL .= " and A.syounin_state=0"; else $ws_SQL .= " and A.syounin_state=1";
			}
			$ws_SQL .= " and B.area='" . $u_area . "'";
			if($u_staff_id) $ws_SQL .= " and A.therapist_id=" . $u_staff_id;
			$ws_SQL .= " order by A.therapist_id,(year*10000+month*100+day)";
		} else {
			//----ドライバー及び本部スタッフ
			$ws_SQL = "select A.*,(A.year*10000+A.month*100+A.day) as Ymd,B.area as B_area,B.name,name_furi,bank_info,furikomi_commission,furikomi_type,chief_allowance,from_unixtime(chief_allowance_start_time,'%Y%m%d') as chief_allowance_ymd,B.area as B_area,B.type as B_type,pay_hour,B.pay_fix as B_pay_fix,B.fuel as B_fuel from attendance_staff_new A";
			$ws_SQL .= " left outer join staff_new_new B on A.staff_id=B.id";
			$ws_SQL .= " where (year*10000+month*100+day)>=" . $u_FromTo["from"] . " and (year*10000+month*100+day)<=" . $u_FromTo["to"] . " and A.today_absence='0' and A.kekkin_flg='0'";
			if($u_flagSyounin != "all") {
				if($u_flagSyounin == "no") $ws_SQL .= " and A.syounin_state=0"; else $ws_SQL .= " and A.syounin_state=1";
			}
			$ws_SQL .= " and B.area='" . $u_area . "' and shayousha_flg!=1 and B.type='" . $u_staff_type . "'";
			if($u_staff_id) $ws_SQL .= " and A.staff_id=" . $u_staff_id;
			$ws_SQL .= " order by A.staff_id,(year*10000+month*100+day)";
		}
		//$sql = sprintf($ws_SQL, $u_FromTo["from"], $u_FromTo["to"], $u_area);
		$sql = $ws_SQL;
		$res = mysql_query($sql, DbCon);
		//echo $res . "/" . $sql . "<br />";
		if( $res == false ) {
			echo "error!(RemunerationData->set) " . $sql;
			exit();
		}
		$ws_qId = -1;
		$n = 0;
		$s = 0;
		$sx = 0;
		$sf = 0;
		while($row = mysql_fetch_assoc($res)) {
			if( $u_staff_type == "therapist" ) {
				if($row["payment_span"] == 1) continue;		//null値データが有る為
			}
			//$this->ArAttend[$n] = $row;
			if($row["staff_id"] != $ws_qId) {
				$ws_qId = $row["staff_id"];
				$this->setStaffData($u_staff_type, $s, $row);
				$ws_share_fix = 0;
				if($this->shrcls == 1) {		//シェア率判定区分
				//if($row["B_area"] == "sendai") {	//test
					//----フレッシュ用
					if($row["point_fix"] > 0 && ($row["point_fix_ymd"] <= $u_FromTo["from"] && $row["point_fix_ymd"] >= CONST_system_BeginYmd)) {
						$ws_share_fix = $row["point_fix"];
					} else {
						if($this->strWhereFreshStaffId) $this->strWhereFreshStaffId .= ",";
						$this->strWhereFreshStaffId .= $ws_qId;
						$this->ArFreshStaffId[$sf] = $ws_qId;		//スタッフID配列
						$sf++;
					}
				} else {
					if($row["point_fix"] > 0 && ($row["point_fix_ymd"] <= $u_FromTo["from"] && $row["point_fix_ymd"] >= CONST_system_BeginYmd)) {
						$ws_share_fix = $row["point_fix"];	//固定シェア率
					}
				}
				$s++;
			}
			$this->setData($n, $row, $ws_share_fix, $this->shrcls);	//固定シェア率
			$n++;
		}
	}


	function set_all($u_staff_type, $u_FromTo, $u_staff_id, $u_flagSyounin) {
	global $ARRAY_shopGroup;

		$this->setArray();

		if( $u_staff_type == "therapist" ) {
			//----セラピスト order_num,rank_order_numを追記 murase
			$ws_SQL = "select A.*,B.jitaku_taiki_flg as jitaku_taiki_flg,therapist_id as staff_id,(A.year*10000+A.month*100+A.day) as Ymd,B.area as B_area,B.name,name_furi,jisou_flg,point_ref,point_fix,from_unixtime(point_fix_start_time,'%Y%m%d') as point_fix_ymd,guarantee_rule,bank_info,furikomi_commission,furikomi_type,chief_allowance,from_unixtime(chief_allowance_start_time,'%Y%m%d') as chief_allowance_ymd,transport_cost,transfer_commission,therapist_type,payment_span,order_num,rank_order_num from attendance_new A";
			$ws_SQL .= " left outer join therapist_new B on A.therapist_id=B.id";
			$ws_SQL .= " where (year*10000+month*100+day)>=" . $u_FromTo["from"] . " and (year*10000+month*100+day)<=" . $u_FromTo["to"] . " and A.today_absence='0' and A.kekkin_flg='0'";
			if($u_flagSyounin != "all") {
				if($u_flagSyounin == "no") $ws_SQL .= " and A.syounin_state=0"; else $ws_SQL .= " and A.syounin_state=1";
			}
			if($u_staff_id) $ws_SQL .= " and A.therapist_id=" . $u_staff_id;
			$ws_SQL .= " order by A.therapist_id,(year*10000+month*100+day)";
		} else {
			//----ドライバー及び本部スタッフ
			$ws_SQL = "select A.*,(A.year*10000+A.month*100+A.day) as Ymd,B.area as B_area,B.name,name_furi,bank_info,furikomi_commission,furikomi_type,chief_allowance,from_unixtime(chief_allowance_start_time,'%Y%m%d') as chief_allowance_ymd,B.area as B_area,B.type as B_type,pay_hour,B.pay_fix as B_pay_fix,B.fuel as B_fuel from attendance_staff_new A";
			$ws_SQL .= " left outer join staff_new_new B on A.staff_id=B.id";
			$ws_SQL .= " where (year*10000+month*100+day)>=" . $u_FromTo["from"] . " and (year*10000+month*100+day)<=" . $u_FromTo["to"] . " and A.today_absence='0' and A.kekkin_flg='0'";
			if($u_flagSyounin != "all") {
				if($u_flagSyounin == "no") $ws_SQL .= " and A.syounin_state=0"; else $ws_SQL .= " and A.syounin_state=1";
			}
			$ws_SQL .= " and shayousha_flg!=1 and B.type='" . $u_staff_type . "'";
			if($u_staff_id) $ws_SQL .= " and A.staff_id=" . $u_staff_id;
			$ws_SQL .= " order by A.staff_id,(year*10000+month*100+day)";
		}
		//$sql = sprintf($ws_SQL, $u_FromTo["from"], $u_FromTo["to"], $u_area);
		$sql = $ws_SQL;
		$res = mysql_query($sql, DbCon);
		//echo $res . "/" . $sql . "<br />";

		if( $res == false ) {
			echo "error!(RemunerationData->set) " . $sql;
			exit();
		}
		$ws_qId = -1;
		$n = 0;
		$s = 0;
		$sx = 0;
		$sf = 0;
		while($row = mysql_fetch_assoc($res)) {
			if( $u_staff_type == "therapist" ) {
				if($row["payment_span"] == 1) continue;		//null値データが有る為
			}
			//$this->ArAttend[$n] = $row;
			if($row["staff_id"] != $ws_qId) {
				$ws_qId = $row["staff_id"];
				$this->setStaffData($u_staff_type, $s, $row);
				$ws_share_fix = 0;
				$ws_shrcls = 0;
				for($a=0; $a<count($ARRAY_shopGroup); $a++) {
					if($ARRAY_shopGroup[$a]["area"] == $row["area"]) {
						$ws_shrcls = $ARRAY_shopGroup[$a]["shrcls"];
						break;
					}
				}
				if($ws_shrcls == 1) {		//シェア率判定区分
				//if($row["B_area"] == "sendai") {	//test
					//----フレッシュ用
					if($row["point_fix"] > 0 && ($row["point_fix_ymd"] <= $u_FromTo["from"] && $row["point_fix_ymd"] >= CONST_system_BeginYmd)) {
						$ws_share_fix = $row["point_fix"];
					} else {
						if($this->strWhereFreshStaffId) $this->strWhereFreshStaffId .= ",";
						$this->strWhereFreshStaffId .= $ws_qId;
						$this->ArFreshStaffId[$sf] = $ws_qId;		//スタッフID配列
						$sf++;
					}
				} else {
					if($row["point_fix"] > 0 && ($row["point_fix_ymd"] <= $u_FromTo["from"] && $row["point_fix_ymd"] >= CONST_system_BeginYmd)) {
						$ws_share_fix = $row["point_fix"];	//固定シェア率
					}
				}
				$s++;
			}
			$this->setData($n, $row, $ws_share_fix, $ws_shrcls);	//固定シェア率
			$n++;
		}
	}

	function setArray() {
		$this->ArAttend = array();
		$this->ArKeyAtd = array();		//出勤データID配列
		$this->ArKeyStaffYmd = array();
		$this->strWhereAtend = "";

		$this->ArStaff = array();		//スタッフ(セラピスト)データ配列
		$this->ArKeyStaff = array();	//スタッフ(セラピスト)データID配列
		$this->strWhereStaff = "";

		$this->strWhereNonFixStaff = "";	//
		$this->ArNonFixStaff = array();	//指定期間以前に固定ポイントになっていないスタッフID配列

		$this->strWhereFreshId = "";	//フレッシュ用
		$this->ArFreshStaffId = array();
	}

	function setData($n, $row, $u_share_fix, $u_shrcls) {
		$this->ArAttend[$n] = $row;
		$this->ArKeyStaffYmd[$n] = substr("000000" . $row["staff_id"], -6) . $row["year"] . substr("00" . $row["month"], -2) . substr("00" . $row["day"], -2);
		$this->ArKeyAtd[$n] = $row["id"];		//出勤データID配列
		if($this->strWhereAtend) $this->strWhereAtend .= ",";
		$this->strWhereAtend .= $row["id"];
		$this->ArAttend[$n]["price"] = 0;
		$this->ArAttend[$n]["transportation"] = 0;
		$this->ArAttend[$n]["repeater_cnt"] = 0;
		$this->ArAttend[$n]["incentive_repeater"] = 0;
		$this->ArAttend[$n]["board_num"] = 0;
		$this->ArAttend[$n]["allowance_jisou"] = 0;
		$this->ArAttend[$n]["shimei_cnt"] = 0;
		$this->ArAttend[$n]["remuneration"] = 0;
		$this->ArAttend[$n]["chief_allowance_yuko"] = 0;
		$this->ArAttend[$n]["share_fix"] = $u_share_fix;	//固定シェア率
		$this->ArAttend[$n]["shrcls"] = $u_shrcls;			//シェア率判定区分
		$this->ArAttend[$n]["coupon90"] = 0;
		$this->ArAttend[$n]["couponex"] = 0;
	}

	function setStaffData($u_staff_type, $s, $row) {
		if($this->strWhereStaff) $this->strWhereStaff .= ",";
		$this->strWhereStaff .= $row["staff_id"];
		$this->ArKeyStaff[$s] = $row["staff_id"];	//スタッフ(セラピスト)IDデータID配列
		$this->ArStaff[$s] = array("staff_id" => $row["staff_id"], "area" => $row["B_area"], "name" => $row["name"], "name_furi" => $row["name_furi"], "bank_info" => $row["bank_info"], "furikomi_commission" => $row["furikomi_commission"], "furikomi_type" => $row["furikomi_type"], "chief_allowance" => $row["chief_allowance"], "chief_allowance_ymd" => $row["chief_allowance_ymd"]);
		if( $u_staff_type == "therapist" ) {
			$this->ArStaff[$s]["staff_id"] = $row["staff_id"];
			$this->ArStaff[$s]["jisou_flg"] = $row["jisou_flg"];
			$this->ArStaff[$s]["point_ref"] = $row["point_ref"];
			$this->ArStaff[$s]["point_fix"] = $row["point_fix"];
			$this->ArStaff[$s]["point_fix_ymd"] = $row["point_fix_ymd"];
			$this->ArStaff[$s]["transport_cost"] = $row["transport_cost"];
			$this->ArStaff[$s]["transfer_commission"] = $row["transfer_commission"];
			$this->ArStaff[$s]["payment_span"] = $row["payment_span"];
			if($row["point_fix_ymd"] >= $u_FromTo["from"] || $row["point_fix_ymd"] <= CONST_system_BeginYmd || $row["point_fix"] == 0) {
				//----指定期間以前に固定ポイントになっていないリスト作成
				$this->ArNonFixStaff[$sx] = $row["staff_id"];
				if($this->strWhereNonFixStaff) $this->strWhereNonFixStaff .= ",";
				$this->strWhereNonFixStaff .= $row["staff_id"];
				$sx++;
			}
		}
	}

	//----出勤データ配列取得
	function getArAttend() {
		return $this->ArAttend;
	}

	//----出勤データID配列取得
	function getArAttendKey() {
		return $this->ArKeyAtd;
	}

	//----出勤データスタッフID＋年月日キー配列取得
	function getArKeyStaffYmd() {
		return $this->ArKeyStaffYmd;
	}

	//----出勤データID文字列取得
	function getStrWhereAtend() {
		return $this->strWhereAtend;
	}

	//----スタッフデータ配列取得
	function getArStaff() {
		return $this->ArStaff;
	}

	//----スタッフデータID配列取得
	function getArKeyStaff() {
		return $this->ArKeyStaff;
	}

	//----スタッフデータID文字列取得
	function getStrWhereStaff() {
		return $this->strWhereStaff;
	}

	//----指定期間以前に固定ポイントになっていないスタッフID配列取得
	function getArNonFixStaff() {
		return $this->ArNonFixStaff;
	}

	//----指定期間以前に固定ポイントになっていないスタッフID文字列取得
	function getStrWhereNonFixStaff() {
		return $this->strWhereNonFixStaff;
	}

	//----スタッフデータID配列取得（フレッシュ用）
	function getArFreshStaffId() {
		return $this->ArFreshStaffId;
	}

	//----スタッフデータID文字列取得（フレッシュ用）
	function getStrWhereFreshId() {
		return $this->strWhereFreshStaffId;
	}
}

//----予約状況データ配列情報を出勤データに追加
function PHP_addReserv2Attend(&$u_ArReservData, &$u_ArKeyAtd, &$u_attend, $u_ArTotalPoint) {

	$ws_ret["attend"] = $u_attend;
	$ws_ret["reserv"] = $u_ArReservData;

	for($r=0; $r<count($ws_ret["reserv"]); $r++) {
		$n = array_search($ws_ret["reserv"][$r]["attendance_id"], $u_ArKeyAtd);
		$ws_ret["reserv"][$r]["therapist_id"] = -1;
		if($n === false) continue;
		$ws_ret["reserv"][$r]["therapist_id"] = $ws_ret["attend"][$n]["therapist_id"];
		$ws_ret["reserv"][$r]["share_fix"]	= $ws_ret["attend"][$n]["share_fix"];	//固定シェア率
		$ws_ret["reserv"][$r]["shrcls"]		= $ws_ret["attend"][$n]["shrcls"];		//シェア率判定区分
		//if($ws_ret["reserv"][$r]["therapist_id"] == 621) echo "line:" . __LINE__ . " " . date("H:i:s") . " " . $ws_ret["reserv"][$r]["attendance_id"] . "<br />";
		$ws_ret["attend"][$n]["board_num"]++;
		if($ws_ret["attend"][$n]["jisou_flg"] == 1 && $allowance < CONST_transportation) $allowance = CONST_transportation;
		$ws_ret["attend"][$n]["price"] += $ws_ret["reserv"][$r]["price"];	//売上
		$ws_ret["attend"][$n]["transportation"] += $ws_ret["reserv"][$r]["transportation"];	//交通費
		if($ws_ret["reserv"][$r]["shimei_flg"] == 1) {
			$ws_ret["attend"][$n]["shimei_cnt"]++;
			$ws_ret["attend"][$n]["incentive_shimei"] += CONST_shimei_value;		//指名インセンティブ
		}

		$result = check_last_day_for_remuneration_common($ws_ret["attend"][$n]["year"], $ws_ret["attend"][$n]["month"], $ws_ret["attend"][$n]["day"]);	//前日チェック(報酬計算用)(過去であればTRUE) in ^common/include/functions.php

		if($result) {
			//----固定シェア率の取得
			if($ws_ret["attend"][$n]["area"] == "tokyo_reraku" || $ws_ret["attend"][$n]["area"] == "tokyo_bigao") {
				$share_rate = $ws_ret["attend"][$n]["share_rate"] * 100;
			} else {
				//----時期(2015年以降か)による固定ポイントチェック
				//$share_rate = get_share_rate_by_point_fix_common($ws_ret["attend"][$n]["point_fix"],$ws_ret["attend"][$n]["point_fix_start_time"],$ws_ret["attend"][$n]["year"], $ws_ret["attend"][$n]["month"], $ws_ret["attend"][$n]["day"]);	//時期(2015年以降か)による固定ポイントチェック in ^common/include/functions.php
				$share_rate = $ws_ret["attend"][$n]["share_fix"];

				if(!$share_rate) {
					//----固定シェア率がない場合
					$total_point = 0;
					if($n == 0) {
						if($u_ArTotalPoint[0]["Ymd"] < $ws_ret["attend"][0]["Ymd"]) {
							$total_point = $u_ArTotalPoint[0]["value"];
						}
					}
					//----該当日のトータルポイントの登録がある場合
					for($i=0; $i<count($u_ArTotalPoint); $i++) {
						if($u_ArTotalPoint[$i]["therapist_id"] == $ws_ret["attend"][$n]["therapist_id"] && $u_ArTotalPoint[$i]["year"] == $ws_ret["attend"][$n]["year"] && $u_ArTotalPoint[$i]["month"] == $ws_ret["attend"][$n]["month"] && $u_ArTotalPoint[$i]["day"] == $ws_ret["attend"][$n]["day"]) {
							$total_point = $u_ArTotalPoint[$i]["value"];
							break;
						}
					}
				}
			}
			$ws_ret["reserv"][$r]["share_rate"] = $share_rate;
			$ws_ret["reserv"][$r]["total_point"] = $total_point;
			$ws_ret["attend"][$n]["share_rate"] = $share_rate;
			$ws_ret["attend"][$n]["total_point"] = $total_point;
		}
	}


	return $ws_ret;
}

//----保険料の追加
function PHP_addInsurance($u_week_data, &$u_strWhereStaff, &$u_attend, $u_ArKeyStaffYmd) {

	$ws_attend = $u_attend;

	$ws_ArInsurance = PHP_getInsurance($u_week_data, $u_strWhereStaff);	//保険料データ取得 in local

	for($i=0; $i<count($ws_ArInsurance); $i++) {
		$ws_key = substr("000000" . $ws_ArInsurance[$i]["therapist_id"], -6) . $u_week_data["year"] . substr("00" . $u_week_data["month"], -2) . substr("00" . $u_week_data["day"], -2);
		$n = array_search($ws_key, $u_ArKeyStaffYmd);
		//if($row["therapist_id"] == 621) echo $n . "/" . $ws_key . "<br />";
		if($n === false) continue;
		$ws_attend[$n]["insurance"] = $ws_ArInsurance[$i]["value"];
		$ws_attend[$n]["insurance_price"] = 0;

		if( $ws_attend[$n]["insurance"] == "2" ){
			//$ws_attend[$n]["board_num"] = get_reservation_for_board_num_by_attendance_id_common($ws_attend[$n]["id"]);
			$ws_attend[$n]["insurance_price"] = CONST_insurance_price * $ws_attend[$n]["board_num"];
		}
		//if($row["therapist_id"] == 630) echo "line:" . __LINE__ . " " . date("H:i:s") . " " . $ws_key . " " . $ws_attend[$n]["furikomi_price"] . " -= " . $ws_attend[$n]["insurance_price"] . " ( " . $ws_attend[$n]["board_num"] . " )<br />";
	}

	return $ws_attend;
}

//----スタッフ報酬データ取得
function PHP_getRenamurationData4Staff($u_staff_type, &$u_strWhereAtend, &$u_ArKeyAtd, &$u_attend, $u_DateSwitchDriverYmd, $u_ArGasoline, $remuneration_type) {

	$ws_attend = $u_attend;

	$sql = sprintf("select * from remuneration_staff where delete_flg=0 and attendance_id in(%s) order by staff_id,day", $u_strWhereAtend);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if( $res == false ){
		echo "error!(PHP_getRenamurationData4Staff) " . $sql;
		exit();
	}
	while($row = mysql_fetch_assoc($res)) {
		$n = array_search($row["attendance_id"], $u_ArKeyAtd);
		if($n === false) continue;
		$ws_attend[$n]["pay_hour"]		= $row["pay_hour"];
		$ws_attend[$n]["pay_fix"]		= $row["pay_fix"];
		$ws_attend[$n]["fuel"]			= $row["fuel"];
		$ws_attend[$n]["type"]			= $row["type"];
		$ws_attend[$n]["chief_allowance"] = $row["chief_allowance"];
		$ws_attend[$n]["rem_Flag"]		= $row["attendance_id"];
		$ws_attend[$n]["start_time"]	= $row["start_time"];
		$ws_attend[$n]["end_time"]		= $row["end_time"];
		$ws_attend[$n]["allowance"]		= $row["allowance"];
		$ws_attend[$n]["car_distance"]	= $row["car_distance"];
		$ws_attend[$n]["highway"]		= $row["highway"];
		$ws_attend[$n]["parking"]		= $row["parking"];
		$ws_attend[$n]["pay_finish"]	= $row["pay_finish"];
		$ws_attend[$n]["start_hour"]	= $row["start_hour"];
		$ws_attend[$n]["start_minute"]	= $row["start_minute"];
		$ws_attend[$n]["end_hour"]		= $row["end_hour"];
		$ws_attend[$n]["end_minute"]	= $row["end_minute"];
		$ws_attend[$n]["pay_day"]		= $row["pay_day"];
		$ws_attend[$n]["gasoline"]		= $row["gasoline"];
		$ws_attend[$n]["work_time"]		= $row["work_time"];
		$ws_attend[$n]["remuneration"]	= $row["remuneration"];
		$ws_attend[$n]["car_distance_over_allowance"] = $row["car_distance_over_allowance"];
		$ws_attend[$n]["sum_price"]		= $row["sum_price"];

		$gasoline_value_disp = $ws_attend[$n]["gasoline"];

		//$settings_gasoline_value = get_gasoline_value_from_settings_2_common($area,$year,$month,$day);		//ガソリン代設定値取得
		//----ガソリン代取得
		$settings_gasoline_value = PHP_getGasolineFee($ws_attend[$n]["year"], $ws_attend[$n]["month"], $ws_attend[$n][$n]["day"], $u_ArGasoline);	//in local
	}

//echo "<pre>"; print_r($u_ArGasoline);	echo "</pre>";
	for($n=0; $n<count($ws_attend); $n++) {

		$switching_result = false;
		if( $u_staff_type == "driver" ) {
			//----ドライバーの場合、該当日がドライバー切替日以降かチェックする
			if($ws_attend[$n]["Ymd"] >= $u_DateSwitchDriverYmd) $switching_result = true;
		}

		if( !$ws_attend[$n]["rem_Flag"] ) {
			//----スタッフ報酬データが無かった場合
			$ws_attend[$n]["type"] = $ws_attend[$n]["B_type"];
			$ws_attend[$n]["chief_allowance"] = $ws_attend[$n]["chief_allowance"];
			$ws_attend[$n]["chief_allowance_start_time"] = $driver_data["chief_allowance_start_time"];
			$ws_attend[$n]["chief_allowance"] = get_chief_allowance_staff_common($ws_attend[$n]["year"], $ws_attend[$n]["month"], $ws_attend[$n]["day"], $ws_attend[$n]["chief_allowance"],$ws_attend[$n]["chief_allowance_start_time"]);	//店長報酬が店長就任前の時はゼロにする

			//$gasoline_value = get_gasoline_value_by_id_and_time_common($attendance_id);		//ガソリン代取得
			//----ガソリン代取得
			$settings_gasoline_value = PHP_getGasolineFeeEx($ws_attend[$n]["area"], $ws_attend[$n]["year"], $ws_attend[$n]["month"], $ws_attend[$n]["day"], $u_ArGasoline);	//in local
			$ws_attend[$n]["settings_gasoline_value"] = $settings_gasoline_value;
			if( ($ws_attend[$n]["fuel"] != "0") && ($ws_attend[$n]["fuel"] != "-1") ) {
				$value_tmp = $ws_attend[$n]["car_distance"] * $settings_gasoline_value;
				//echo $value_tmp . " = " . $ws_attend[$n]["car_distance"] . " * " .  $settings_gasoline_value . "<br />";
				if( $value_tmp == "0" ){
					$ws_attend[$n]["gasoline_value_disp"] = 0;
				} else {
					$ws_attend[$n]["gasoline_value_disp"] = intval($value_tmp / $ws_attend[$n]["B_fuel"]);
//echo $ws_attend[$n]["gasoline_value_disp"] . " = intval(" . $value_tmp . " / " . $ws_attend[$n]["B_fuel"] . ")<br />";
				}
			} else {
				$ws_attend[$n]["gasoline_value_disp"] = 0;
			}
			//if($ws_attend[$n]["staff_id"] == 108) echo $ws_attend[$n]["car_distance"] . " " . $settings_gasoline_value . " ==> " . $value_tmp . " " . $ws_attend[$n]["B_fuel"] . " " . $ws_attend[$n]["gasoline_value_disp"] . "???????????<br />";

			//データのアップデート(最初のアクセス時だけ)と、データの取得
			$data = PHP_get_and_update_driver_work_time_common($u_staff_type, $ws_attend[$n]["id"], $ws_attend[$n]["start_hour"],$ws_attend[$n]["start_minute"], $ws_attend[$n]["end_hour"], $ws_attend[$n]["end_minute"], $ws_attend[$n]["start_time"], $ws_attend[$n]["end_time"]);	//データのアップデート(最初のアクセス時だけ)と、データの取得
			//echo $data["start_hour"] . " != " . $ws_attend[$n]["start_hour"] . " || " . $data["start_minute"] . " != " . $ws_attend[$n]["start_minute"] . " || " . $data["end_hour"] . " != " . $ws_attend[$n]["end_hour"] . " || " . $data["end_minute"] . " != " . $ws_attend[$n]["end_minute"] . "<br />";
			if($data["start_hour"] != $ws_attend[$n]["start_hour"] || $data["start_minute"] != $ws_attend[$n]["start_minute"] || $data["end_hour"] != $ws_attend[$n]["end_hour"] || $data["end_minute"] != $ws_attend[$n]["end_minute"]) {
				$ws_attend[$n]["msg_unUpdate"] = "売上未更新";
			}
			$start_hour = $data["start_hour"];
			$start_minute = $data["start_minute"];
			$end_hour = $data["end_hour"];
			$end_minute = $data["end_minute"];

			$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);	//時間集計 in ^common/include/functions.php
			//走行距離/時間,超過距離/時間,超過距離/日,超過手当
			$data_tmp = get_car_distance_allowance_data_common($ws_attend[$n]["car_distance"],$work_time,$u_staff_type,$ws_attend[$n]["year"], $ws_attend[$n]["month"], $ws_attend[$n]["day"]);	//カー距離別報酬データ取得 in ^common/include/functions.php
			//if($ws_attend[$n]["staff_id"] == 9) echo "line:" . __LINE__ . " " . date("H:i:s") . "<pre>"; print_r($data_tmp); echo "</pre>";
			$ws_attend[$n]["car_distance_over_allowance"] = $data_tmp["car_distance_over_allowance"];
			$ws_attend[$n]["car_distance_hour"] = $data_tmp["car_distance_hour"];

			if( $staff_area == "tokyo" ) $ws_attend[$n]["car_distance_over_allowance"] = 0;	//東京は超過手当はゼロ

			if( $switching_result == true ){
				//----ドライバー
				$remuneration_type = get_remuneration_type_common($ws_attend[$n]["staff_id"],$ws_attend[$n]["year"], $ws_attend[$n]["month"], $ws_attend[$n]["day"]);	//設定報酬データの設定値取得
				if( $remuneration_type == "1" ){
					//----時給
					$ws_attend[$n]["remuneration"] = get_remuneration_driver_common($ws_attend[$n]["pay_hour"],$ws_attend[$n]["pay_fix"],$work_time,$ws_attend[$n]["car_distance"]);	//報酬取得
				} else if( $remuneration_type == "2" ) {
					//----距離別料金取得3 (注意)東京(tokyo)のドライバーのみ有り得る
					$ws_attend[$n]["remuneration"] = get_remuneration_driver_3_common($ws_attend[$n]["car_distance"],$ws_attend[$n]["car_distance_hour"],$ws_attend[$n]["year"], $ws_attend[$n]["month"], $ws_attend[$n]["day"],$ws_attend[$n]["area"]);
				//if($ws_attend[$n]["staff_id"] == 208) echo "**line:" . __LINE__ . " " . date("H:i:s") . " " . $ws_attend[$n]["remuneration"] . " = " . $ws_attend[$n]["car_distance"] . "***@@@<br />";
				} else {
					//----距離別料金取得3 (注意)東京(tokyo)のドライバーのみ有り得る
					$ws_attend[$n]["remuneration"] = get_remuneration_driver_4_common($ws_attend[$n]["staff_id"]);
				//if($ws_attend[$n]["staff_id"] == 208) echo "**line:" . __LINE__ . " " . date("H:i:s") . " " . $ws_attend[$n]["remuneration"] . " = " . $ws_attend[$n]["car_distance"] . "***@@@<br />";
				}
			} else {
				//----本部スタッフ
				$ws_attend[$n]["remuneration"] = get_remuneration_driver_common($ws_attend[$n]["pay_hour"],$ws_attend[$n]["B_pay_fix"],$work_time,$ws_attend[$n]["car_distance"]);	//報酬取得 in ^common/include/functions.php
				//if($ws_attend[$n]["staff_id"] == 208) echo "**line:" . __LINE__ . " " . date("H:i:s") . " " . $ws_attend[$n]["remuneration"] . " = " . $ws_attend[$n]["pay_hour"] . "," . $ws_attend[$n]["B_pay_fix"] . "," . $work_time . "," . $ws_attend[$n]["car_distance"] . "***@@@<br />";
			}
		}

		if( $u_staff_type == "driver" ){
			//インセンティブは、超過手当に加算
			$ws_attend[$n]["incentive"] = get_incentive_common($ws_attend[$n]["car_distance"], $ws_attend[$n]["year"], $ws_attend[$n]["month"], $ws_attend[$n]["day"]);	//インセンティブ取得 in ^common/include/functions.php
			$ws_attend[$n]["car_distance_over_allowance"] += $ws_attend[$n]["incentive"];
			//if($ws_attend[$n]["staff_id"] == 108) echo "line:" . __LINE__ . " " . date("H:i:s") . " " . $ws_attend[$n]["car_distance_over_allowance"] . " += " . $ws_attend[$n]["incentive"] . "<br />";
		}
/*
		if( ($switching_result == true) && ($remuneration_type != "1") ){
			//報酬+高速代+駐車場代-清算済み
			$ws_attend[$n]["furikomi_price"] = ( $ws_attend[$n]["remuneration"] + $ws_attend[$n]["highway"] + $ws_attend[$n]["parking"] ) - $ws_attend[$n]["pay_finish"] - $ws_attend[$n]["pay_day"];

			$ws_attend[$n]["remuneration_return"] = ( $ws_attend[$n]["remuneration"] + $ws_attend[$n]["highway"] + $ws_attend[$n]["parking"] ) - $ws_attend[$n]["pay_finish"] - $ws_attend[$n]["pay_day"];
			$ws_attend[$n]["gasoline_return"] = 0;
			$ws_attend[$n]["sonota"] = $ws_attend[$n]["highway"] + $ws_attend[$n]["parking"];

			$ws_attend[$n]["chief_allowance"] = 0;
			$ws_attend[$n]["gasoline_value_disp"] = 0;
		} else { */
			//報酬+ガソリン代+高速代+駐車場代+手当-清算済み
			$ws_attend[$n]["remuneration"] = ( $ws_attend[$n]["remuneration"] + $ws_attend[$n]["highway"] + $ws_attend[$n]["parking"] + $ws_attend[$n]["allowance"] ) - $ws_attend[$n]["pay_finish"] - $ws_attend[$n]["pay_day"];
			//if($ws_attend[$n]["staff_id"] == 208) echo "**line:" . __LINE__ . " " . $ws_attend[$n]["remuneration"] . " + " . $ws_attend[$n]["highway"] . " + " . $ws_attend[$n]["parking"] . " + " . $ws_attend[$n]["allowance"] . " ) - " . $ws_attend[$n]["pay_finish"] . " - " . $ws_attend[$n]["pay_day"] . "<br />";
			//振込金額に超過手当を加算
			$ws_attend[$n]["other_price"] += $ws_attend[$n]["car_distance_over_allowance"];

			if( $ws_attend[$n]["remuneration"] > 0 ) $ws_attend[$n]["remuneration"] += $chief_allowance;	//振込金額にチーフ手当を加算
			if($remuneration_type == "2"){
				$ws_attend[$n]["furikomi_price"] = $ws_attend[$n]["remuneration"];
			}else{
				$ws_attend[$n]["furikomi_price"] = $ws_attend[$n]["remuneration"] + $ws_attend[$n]["gasoline_value_disp"] + $ws_attend[$n]["car_distance_over_allowance"];
			}
			$ws_attend[$n]["remuneration_return"] = ( $ws_attend[$n]["remuneration"] + $ws_attend[$n]["highway"] + $ws_attend[$n]["parking"] + $ws_attend[$n]["allowance"] ) - $ws_attend[$n]["pay_finish"] - $ws_attend[$n]["pay_day"];
			$ws_attend[$n]["gasoline_return"] = $ws_attend[$n]["gasoline_value_disp"];
			$ws_attend[$n]["other_price"] += $ws_attend[$n]["gasoline_value_disp"];
			$ws_attend[$n]["sonota_cost"] = $ws_attend[$n]["highway"] + $ws_attend[$n]["parking"] + $ws_attend[$n]["allowance"];
		//}

		//$sum_priceが記録済みの場合は、$furikomi_priceは、$sum_priceに
		if( $ws_attend[$n]["sum_price"] > 0 ) $ws_attend[$n]["furikomi_price"] = $ws_attend[$n]["sum_price"];
		$remuneration_type_disp = get_remuneration_type_disp($remuneration_type);//in ^man/include/functions.php
		$ws_attend[$n]["remuneration_type_disp"] = $remuneration_type_disp;
	}

	return $ws_attend;
}

//----指定期間内ガソリン代設定値の取得
function PHP_getSettingGasoline($u_area, $u_fromYmd, $u_toYmd) {

	$ws_fromYear = floor($u_fromYmd / 10000);
	$ws_fromMonth = floor($u_fromYmd / 100) % 100;
	$ws_fromDay = $u_fromYmd % 100;
	$ws_toYear = floor($u_toYmd / 10000);
	$ws_toMonth = floor($u_toYmd / 100) % 100;
	$ws_toDay = $u_toYmd % 100;
	$ws_startPeriod = get_timestamp_by_year_month_day_common($ws_fromYear, $ws_fromMonth, $ws_fromDay);	//指定年月日をタイムスタンプ形式に変換 in ^common/include/functions.php
	$ws_endPeriod = get_timestamp_by_year_month_day_common($ws_toYear, $ws_toMonth, $ws_toDay);			//指定年月日をタイムスタンプ形式に変換 in ^common/include/functions.php

	if($u_area == "all" || !$u_area) {
		$sql = "select *,from_unixtime(period_start,'%Y%m%d') as fromYmd,from_unixtime(period_end,'%Y%m%d') as toYmd from settings_gasoline where delete_flg='0' and (period_start<=" . $ws_endPeriod . ") and (period_end>=" . $ws_startPeriod . ")";
	} else {
		$sql = "select *,from_unixtime(period_start,'%Y%m%d') as fromYmd,from_unixtime(period_end,'%Y%m%d') as toYmd from settings_gasoline where delete_flg='0' and (period_start<=" . $ws_endPeriod . ") and (period_end>=" . $ws_startPeriod . ") and area='" . $u_area . "'";
	}
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if( $res == false ){
		echo "error!(PHP_getSettingGasoline) " . $sql;
		return false;
	}
	$g = 0;
	while($row = mysql_fetch_assoc($res)) {
		$ws_ArGasoline[$g] = $row;
		$g++;
	}
	return $ws_ArGasoline;
}

//----ガソリン代取得
function PHP_getGasolineFee($u_year, $u_month, $u_day, &$u_ArGasoline) {

	$ws_ret = 0;

	$ts = get_timestamp_by_year_month_day_common($u_year, $u_month, $u_day);	//指定年月日をタイムスタンプ形式に変換 in ^common/include/functions.php
	$settings_gasoline_value = 0;
	for($g=0; $g<count($u_ArGasoline); $g++) {
		//echo $ts . " | " . $u_ArGasoline[$g]["period_start"] . " | " . $u_ArGasoline[$g]["period_end"] . "========<br />";
		if($ts >= $u_ArGasoline[$g]["period_start"] && $ts <= $u_ArGasoline[$g]["period_end"]) {
			$ws_ret = $u_ArGasoline[$g]["value"];
			break;
		}
	}
	return $ws_ret;
}

//----ガソリン代取得(エリア)
function PHP_getGasolineFeeEx($u_area, $u_year, $u_month, $u_day, &$u_ArGasoline) {

	$ws_ret = 0;

	$ts = get_timestamp_by_year_month_day_common($u_year, $u_month, $u_day);	//指定年月日をタイムスタンプ形式に変換 in ^common/include/functions.php
	$settings_gasoline_value = 0;
	for($g=0; $g<count($u_ArGasoline); $g++) {
		//echo $ts . " | " . $u_ArGasoline[$g]["period_start"] . " | " . $u_ArGasoline[$g]["period_end"] . "========<br />";
		if($u_ArGasoline[$g]["area"] == $u_area && $ts >= $u_ArGasoline[$g]["period_start"] && $ts <= $u_ArGasoline[$g]["period_end"]) {
			$ws_ret = $u_ArGasoline[$g]["value"];
			break;
		}
	}
	return $ws_ret;
}

//データのアップデート(最初のアクセス時だけ)と、データの取得
function PHP_get_and_update_driver_work_time_common($u_staff_type, $attendance_id, $start_hour, $start_minute, $end_hour, $end_minute, $start_time, $end_time) {

	if( ($start_hour == "-1") || ($start_minute == "-1") || ($end_hour == "-1") || ($end_minute == "-1") ){

		if( $u_staff_type == "honbu" ){
			$time_array = get_time_array_honbu_common();	//本部スタッフ用時刻配列取得 in ^common/include/functions.php
		}else{
			$time_array = get_time_array_driver_common();	//ドライバー用時刻配列取得 in ^common/include/functions.php
		}

		$time_data = get_time_value_for_sale_driver_one_common($time_array,$start_time,$end_time);	//開始及び終了時刻取得 in ^common/include/functions.php

		$start_hour = $time_data["start_hour"];
		$start_minute = $time_data["start_minute"];
		$end_hour = $time_data["end_hour"];
		$end_minute = $time_data["end_minute"];

		//売上金額の更新
		//$sql = sprintf("update attendance_staff_new set start_hour='%s',start_minute='%s',end_hour='%s',end_minute='%s' where id='%s'",$start_hour,$start_minute,$end_hour,$end_minute,$attendance_id);
		//$res = mysql_query($sql, DbCon);
		//if($res == false){
		//	echo "error!(get_and_update_driver_work_time_common)";
		//	exit();
		//}
	}

	$data["start_hour"] = $start_hour;
	$data["start_minute"] = $start_minute;
	$data["end_hour"] = $end_hour;
	$data["end_minute"] = $end_minute;

	return $data;
	exit();
}

//----トータルポイント
function PHP_getTotalPoint($u_strWhereNonFixStaff, $u_FromYmd, $u_ToYmd) {

	//----期間内のトータルポイント取得
	$ws_strWhereTp = "";
	$n = 0;
	$sql = sprintf("select *,(year*10000+month*100+day) as Ymd from total_point where delete_flg=0 and therapist_id in(%s) and (year*10000+month*100+day)>=%s and (year*10000+month*100+day)<=%s order by therapist_id,(year*10000+month*100+day) desc", $u_strWhereNonFixStaff, $u_FromYmd, $u_ToYmd);
	$res = mysql_query($sql, DbCon);
//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(PHP_getTotalPoint)";
		exit();
	}

	while($row = mysql_fetch_assoc($res)) {
		$ws_ArTotalPoint[$n] = $row;
		if($ws_strWhereTp) $ws_strWhereTp .= ",";
		$ws_strWhereTp .= $row["id"];
		$ws_ArTpId[$n] = $row["id"];
		$n++;
	}

//echo "line:" . __LINE__ . " " . date("H:i:s") . "<br />";
	//----期間以前の最新トータルポイントの取得
	$ws_SQL = "select A.*,(A.year*10000+A.month*100+A.day) as Ymd from total_point A";
	$ws_SQL .= " left outer join (select therapist_id,max(year*10000+month*100+day) as Ymd from total_point where delete_flg=0 group by therapist_id) B on A.therapist_id=B.therapist_id and (A.year*10000+A.month*100+A.day)=B.Ymd";
	$ws_SQL .= " where A.delete_flg=0 and A.therapist_id in(%s) and (A.year*10000+A.month*100+A.day)<%s and B.therapist_id is not null";
	if($ws_strWhereTp) $ws_SQL .= " and id not in(%s)";
	$ws_SQL .= " order by A.therapist_id,(A.year*10000+A.month*100+A.day)";
	$sql = sprintf($ws_SQL, $u_strWhereNonFixStaff, $u_FromYmd, $ws_strWhereTp);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(PHP_getTotalPoint) " . $sql;
		exit();
	}
	while($row = mysql_fetch_assoc($res)) {
		$ws_ArTotalPoint[$n] = $row;
//if($row["therapist_id"] == 543) echo "line:" . __LINE__ . " " . date("H:i:s") . " " . $row["year"] . "-" . $row["month"] . "-" . $row["day"] . " " . $row["value"] . "<br />";
		$n++;
	}
//echo "line:" . __LINE__ . " " . date("H:i:s") . " total_point counts:" . count($ws_ArTotalPoint) . "<br />";

	return $ws_ArTotalPoint;
}

//----リピーターポイントの取得
function PHP_getRepeatPoint($u_strWhereAtend) {
	$sql = sprintf("select * from repeater where delete_flg=0 and attendance_id in(%s)", $u_strWhereAtend);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(PHP_getRepeatPoint)";
		exit();
	}
	$n = 0;
	while($row = mysql_fetch_assoc($res)) {
		$ws_ArRet[$n] = $row;
		$n++;
	}

	return $ws_ArRet;
}

//----予約状況データ読み込み
function PHP_getReservData($u_strWhereAtend, $u_flagUnComplete = false) {

	$ws_ArReservData = array();
	$ws_strWhereResvNo = "";
	$ws_ArResvNo = array();
	$r = 0;
	//2018/11/30 murase update from
	$sql = sprintf("select *,convert_tz(from_unixtime(start_real_time),'+00:00','+09:00') as real_start_tm,convert_tz(from_unixtime(end_real_time),'+00:00','+09:00') as real_end_tm from reservation_for_board where delete_flg='0' and attendance_id in(%s) order by year,month,day,start_real_time",$u_strWhereAtend);
	//2018/11/30 murase update to
	if(!$u_flagUnComplete) $sql .= " and complete_flg='1'";
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(PHP_getReservData)";
		exit();
	}
	while($row = mysql_fetch_assoc($res)) {
		$ws_ArReservData[$r] = $row;
		$ws_ArReservData[$r]["price"] = 0;
		$ws_ArReservData[$r]["Ymd"] = $row["year"] * 10000 + $row["month"] * 100 + $row["day"];
		if($ws_strWhereResvNo) $ws_strWhereResvNo .= ",";
		$ws_strWhereResvNo .= $row["reservation_no"];
		$ws_ArResvNo[$r] = $row["reservation_no"];
		$r++;
	}

	if($r > 0) {
		//----売上履歴データ取得し予約状況データ配列に売上追加
		$sql = sprintf("select * from sale_history where delete_flg=0 and reservation_no in(%s)",$ws_strWhereResvNo);
		$res = mysql_query($sql, DbCon);
		//echo $res . "/" . $sql . "<br />";
		if($res == false){
			echo "error!(PHP_getReservData)";
			exit();
		}
		while($row = mysql_fetch_assoc($res)) {
			$r = array_search($row["reservation_no"], $ws_ArResvNo);
			if($r === false) continue;
			$ws_ArReservData[$r]["price"] = $row["price"];
		}
	} else {
		return;
	}

	return $ws_ArReservData;
}

//----トータルポイント計算
function PHP_calTotalPoint($u_strWhereNonFixStaff, $u_FromYmd, $u_ToYmd, &$week_data) {

	$ws_tpArRec = array();
	$ws_tpArKey = array();
	$ws_tpArRp = array();
	$ws_tpArOp = array();
	$ws_tpWhereRp = "";
	$ws_tpWhereOp = "";
	$sR = 0;
	$sO = 0;
	$n = 0;

	$ws_SQL = "select A.*,(C.year*10000+C.month*100+C.day) as base_Ymd,C.value as total_point from (attendance_new A";
	$ws_SQL .= " left outer join (select therapist_id,max(year*10000+month*100+day) as base_Ymd from total_point where delete_flg=0 and (year*10000+month*100+day)<%s group by therapist_id) B on A.therapist_id=B.therapist_id)";
	$ws_SQL .= " left outer join total_point C on B.therapist_id=C.therapist_id and B.base_Ymd=(C.year*10000+C.month*100+C.day) and C.delete_flg=0";
	$ws_SQL .= " where A.therapist_id in(%s) and (A.year*10000+A.month*100+A.day)>=(case when B.base_Ymd is null then 0 else B.base_ymd end) and (A.year*10000+A.month*100+A.day)<=%s";
	$ws_SQL .= " order by A.therapist_id,(A.year*10000+A.month*100+A.day)";
	$sql = sprintf($ws_SQL, $u_FromYmd, $u_strWhereNonFixStaff, $u_ToYmd);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(PHP_calTotalPoint)";
		exit();
	}
	while($row = mysql_fetch_assoc($res)) {
		$ws_tpArRec[$n] = array("id" => $row["id"], "therapist_id" => $row["therapist_id"], "area" => $row["area"], "base_point" => $row["total_point"], "base_Ymd" => $row["base_Ymd"], "Ymd" => ($row["year"]*10000+$row["month"]*100+$row["day"]), "pt_repeat" => $row["pt_repeat"], "pt_operation" => $row["pt_operation"], "pt_shimei" => $row["pt_shimei"]);
		$ws_tpArKey[$n] = $row["id"];
		if($row["pt_repeat"] == -1) {
			if($ws_tpWhereRp) $ws_tpWhereRp .= ",";
			$ws_tpWhereRp .= $row["id"];
			$ws_tpArRp[$sR] = $row["id"];
			$sR++;
		}
//if($row["therapist_id"] == 543) echo "line:" . __LINE__ . " " . date("H:i:s") . " " . $row["pt_operation"] . " " . $row["pt_shimei"] . "@@@@@@@<br />";
		if($row["pt_operation"] == -1 || $row["pt_shimei"] == -1) {
			if($ws_tpWhereOp) $ws_tpWhereOp .= ",";
			$ws_tpWhereOp .= $row["id"];
			$ws_tpArRp[$sO] = $row["id"];
			$sO++;
		}
		$n++;
	}
	//echo "<pre>"; print_r($ws_tpArRec[9]); echo "</pre>";

//echo "line:" . __LINE__ . " " . date("H:i:s") . " " . $ws_tpWhereOp . "XXXXXXXXXXXXX<br />";
	if($ws_tpWhereRp) {
		//----リピーターポイント加算
		$sql = sprintf("select * from repeater where delete_flg=0 and attendance_id in(%s)", $ws_tpWhereRp);
		$res = mysql_query($sql, DbCon);
		//echo $res . "/" . $sql . "<br />";
		if($res == false){
			echo "error!(PHP_calTotalPoint)";
			exit();
		}
		while($row = mysql_fetch_assoc($res)) {
			$n = array_search($row["attendance_id"], $ws_tpArKey);
			if($n === false) continue;
			if($ws_tpArRec[$n]["pt_repeat"] == -1) $ws_tpArRec[$n]["pt_repeat"] = 0;
			$ws_tpArRec[$n]["pt_repeat"] += CONST_point_Repeat;
		}
	}
//echo "line:" . __LINE__ . " " . date("H:i:s") . " " . $ws_tpWhereOp . "<br />";
	if($ws_tpWhereOp) {
		//----施術及び指名ポイント加算
		$sql = sprintf("select * from reservation_for_board where delete_flg=0 and attendance_id in(%s)", $ws_tpWhereOp);
		$res = mysql_query($sql, DbCon);
		//echo $res . "/" . $sql . "<br />";
		if($res == false){
			echo "error!(PHP_calTotalPoint)";
			exit();
		}
		while($row = mysql_fetch_assoc($res)) {
			$n = array_search($row["attendance_id"], $ws_tpArKey);
			if($n === false) continue;
			if($ws_tpArRec[$n]["pt_operation"] == -1) $ws_tpArRec[$n]["pt_operation"] = 0;
			$ws_tpArRec[$n]["pt_operation"] += CONST_point_Operation;
			if($row["shimei_flg"]) {
				if($ws_tpArRec[$n]["pt_shimei"] == -1) $ws_tpArRec[$n]["pt_shimei"] = 0;
				$ws_tpArRec[$n]["pt_shimei"] += CONST_point_Shimei;
			}
		}
	}
	//echo "<pre>"; print_r($ws_tpArRec); echo "</pre>";

	//----トータルポイント配列生成
	$ws_ArTpStaff = array();
	$i = -1;
	$ws_qId = -1;
	for($n=0; $n<count($ws_tpArRec); $n++) {
		if($ws_tpArRec[$n]["therapist_id"] != $ws_qId) {
			$ws_qId = $ws_tpArRec[$n]["therapist_id"];
			$i++;
			$ws_ArTpStaff[$i] = array("therapist_id" => $ws_qId, "area" => $ws_tpArRec[$n]["area"], "base_point" => $ws_tpArRec[$n]["base_point"], "base_Ymd" => $ws_tpArRec[$n]["base_Ymd"], "point" => array(), "share_rate" => array(), "pt_repeat" => array(), "pt_operation" => array(), "pt_shimei" => array());
			$ws_ArTpStaff[$i]["point"][0] = 0;
			$ws_ArTpStaff[$i]["share_rate"][0] = 0;
			$ws_ArTpStaff[$i]["pt_repeat"][0] = 0;
			$ws_ArTpStaff[$i]["pt_operation"][0] = 0;
			$ws_ArTpStaff[$i]["pt_shimei"][0] = 0;
			for($d=0; $d<count($week_data); $d++) {
				$ws_ArTpStaff[$i]["point"][($d+1)] = 0;
				$ws_ArTpStaff[$i]["share_rate"][($d+1)] = 0;
				$ws_ArTpStaff[$i]["pt_repeat"][($d+1)] = 0;
				$ws_ArTpStaff[$i]["pt_operation"][($d+1)] = 0;
				$ws_ArTpStaff[$i]["pt_shimei"][($d+1)] = 0;
			}
		}
		$d = -1;
		if($ws_tpArRec[$n]["Ymd"] > $u_ToYmd) continue;
		if($ws_tpArRec[$n]["Ymd"] >= $u_FromYmd) {
			for($x=0; $x<count($week_data); $x++) {
				if( $week_data[$x]["Ymd"] == $ws_tpArRec[$n]["Ymd"]) {
					$d = $x;
					break;
				}
			}
		}
		$d++;
		if($ws_tpArRec[$n]["pt_repeat"] == -1) $ws_tpArRec[$n]["pt_repeat"] = 0;
		if($ws_tpArRec[$n]["pt_operation"] == -1) $ws_tpArRec[$n]["pt_operation"] = 0;
		if($ws_tpArRec[$n]["pt_shimei"] == -1) $ws_tpArRec[$n]["pt_shimei"] = 0;
		$ws_ArTpStaff[$i]["point"][$d] += $ws_tpArRec[$n]["pt_repeat"] + $ws_tpArRec[$n]["pt_operation"] + $ws_tpArRec[$n]["pt_shimei"];
		$ws_ArTpStaff[$i]["pt_repeat"][$d] += $ws_tpArRec[$n]["pt_repeat"];
		$ws_ArTpStaff[$i]["pt_operation"][$d] += $ws_tpArRec[$n]["pt_operation"];
		$ws_ArTpStaff[$i]["pt_shimei"][$d] += $ws_tpArRec[$n]["pt_shimei"];
		//if($ws_ArTpStaff[$i]["therapist_id"] == 630) echo $d . "/" . $ws_tpArRec[$n]["Ymd"] . "/" . $u_FromYmd . " " . $ws_tpArRec[$n]["pt_repeat"] . " + " .$ws_tpArRec[$n]["pt_operation"] . " + " .$ws_tpArRec[$n]["pt_shimei"] . "<br />";
		//if($ws_ArTpStaff[$i]["therapist_id"] == 630) echo $d . "<pre>"; print_r($ws_ArTpStaff[$i]["point"]); echo "</pre>";
	}

	return $ws_ArTpStaff;
}

//----シェア率計算
function PHP_calShareRate($u_TherapistId, $u_share_rate, $u_Ymd, &$week_data, $u_ArTpStaff) {

	$ws_share_rate = $u_share_rate;

	if(!$ws_share_rate) {
		$d = -1;
		for($x=0; $x<count($week_data); $x++) {
			if($week_data[$x]["Ymd"] == $u_Ymd) {
				$d = $x;
				break;
			}
		}
		if($d == -1) continue;

		$ws_share_rate = -1;

		for($i=0; $i<count($u_ArTpStaff); $i++) {
			if($u_ArTpStaff[$i]["therapist_id"] == $u_TherapistId) {
				$ws_share_rate = $u_ArTpStaff[$i]["share_rate"][($d+1)];
				break;
			}
		}
		/*
		if($ws_share_rate == -1) {
			echo "line:" . __LINE__ . " share_rate = -1 therapist_id:" . $u_TherapistId . ", date:" . $u_Ymd . "<br />";
			continue;
		}
		*/
	}
	return $ws_share_rate;
}

//----ポイント取得
function PHP_getPoints($u_TherapistId, $u_Ymd, &$week_data, $u_ArTpStaff) {

	$ws_ArTpStaff = $u_ArTpStaff;

	$d = -1;
	for($x=0; $x<count($week_data); $x++) {
		if($week_data[$x]["Ymd"] == $u_Ymd) {
			$d = $x;
			break;
		}
	}
	if($d == -1) return false;

	$s = -1;

	for($i=0; $i<count($ws_ArTpStaff); $i++) {
		if($ws_ArTpStaff[$i]["therapist_id"] == $u_TherapistId) {
			$s = $i;
//echo $n . "<pre>"; print_r($ws_ArTpStaff[$s]["pt_operation"]); echo "</pre>";
			break;
		}
	}
	if($s == -1) {
		echo "line:" . __LINE__ . " No data(ArTpStaff) therapist_id:" . $u_TherapistId . ", date:" . $u_Ymd . "<br />";
		return false;
	}

	if($ws_ArTpStaff[$s]["pt_operation"][($d+1)] < 0) $ws_ArTpStaff[$s]["pt_operation"][($d+1)] = 0;
	if($ws_ArTpStaff[$s]["pt_shimei"][($d+1)] < 0) $ws_ArTpStaff[$s]["pt_shimei"][($d+1)] = 0;
	if($ws_ArTpStaff[$s]["pt_repeat"][($d+1)] < 0) $ws_ArTpStaff[$s]["pt_repeat"][($d+1)] = 0;
	$ws_ArRet = array("point" => $ws_ArTpStaff[$s]["point"][($d+1)], "pt_operation" => $ws_ArTpStaff[$s]["pt_operation"][($d+1)], "pt_shimei" => $ws_ArTpStaff[$s]["pt_shimei"][($d+1)], "pt_repeat" => $ws_ArTpStaff[$s]["pt_repeat"][($d+1)]);

	return $ws_ArRet;
}

//----保険料データ取得
function PHP_getInsurance($u_week_data, $u_strWhereStaff) {

	$timestamp = get_timestamp_by_year_month_day_common($u_week_data["year"], $u_week_data["month"], $u_week_data["day"]);	//指定年月日をタイムスタンプ形式に変換

	$sql = sprintf("select * from settings_insurance where delete_flg='0' and (period_start<='%s') and (period_end>='%s') and therapist_id in(%s)",$timestamp,$timestamp,$u_strWhereStaff);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(PHP_getInsurance) line:" . __LINE__;
		exit();
	}
	$n = 0;
	while($row = mysql_fetch_assoc($res)) {
		$ws_ArRet[$n] = $row;
		$n++;
	}
	return $ws_ArRet;
}

//----シェア率判定区分及びシェア率取得
function PHP_getShareClass($u_area) {
global $ARRAY_shopGroup;

	$ws_ArRet = array("shrcls" => 0, "point" => array(), "rate" => array());

	for($n=0; $n<count($ARRAY_shopGroup); $n++) {
		if($ARRAY_shopGroup[$n]["area"] == $u_area) {
			$ws_ArRet["shrcls"] = $ARRAY_shopGroup[$n]["shrcls"];
			$ws_ArPoints = explode(",", $ARRAY_shopGroup[$n]["ptstb1"]);
			$ws_ArSharRt = explode(",", $ARRAY_shopGroup[$n]["shrtb1"]);
			for($i=0; $i<count($ws_ArPoints); $i++) {
				$ws_ArRet["point"][$i] = intval($ws_ArPoints[$i]);
				$ws_ArRet["rate"][$i] = intval($ws_ArSharRt[$i]);
			}
			break;
		}
	}
	return $ws_ArRet;
}

//----シェア率判定区分及びシェア率取得
function PHP_getShareClassArray($u_area) {
global $ARRAY_shopGroup;

	$ws_ArRet = array();

	$r = 0;
	for($n=0; $n<count($ARRAY_shopGroup); $n++) {
		if($ARRAY_shopGroup[$n]["area"] == $u_area || $u_area = "all" || !$u_area) {
			$ws_ArRet[$r] = array("area" => $ARRAY_shopGroup[$n]["area"], "shrcls" => 0, "point" => array(), "rate" => array());
			$ws_ArRet[$r]["shrcls"] = $ARRAY_shopGroup[$n]["shrcls"];
			$ws_ArPoints = explode(",", $ARRAY_shopGroup[$n]["ptstb1"]);
			$ws_ArSharRt = explode(",", $ARRAY_shopGroup[$n]["shrtb1"]);
			for($i=0; $i<count($ws_ArPoints); $i++) {
				$ws_ArRet[$r]["point"][$i] = intval($ws_ArPoints[$i]);
				$ws_ArRet[$r]["rate"][$i] = intval($ws_ArSharRt[$i]);
			}
			$r++;
		}
	}
	return $ws_ArRet;
}

//----シェア率取得(フレッシュ用)
function PHP_getShareRate4Fresh($u_therapist_id, $u_d, &$u_ArSharRt, &$u_ArFreshWorkTimes) {

	$share_rate = $u_ArSharRt[0];

	for($i=0; $i<count($u_ArFreshWorkTimes); $i++) {
		if($u_ArFreshWorkTimes[$i]["therapist_id"] == $u_therapist_id) {
			if($u_ArFreshWorkTimes[$i]["d"] != -1 && $u_d >= $u_ArFreshWorkTimes[$i]["d"]) {
				$share_rate = $u_ArSharRt[1];
			}
		}
	}

	return $share_rate;
}
?>
