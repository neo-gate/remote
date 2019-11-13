<?php
/* =================================================================================
		Script Name	db_class.php(データベース関連)
		Author		aida
		Create Date	2018/02/04
		Update Date	----/--/--
		Description	データベース関連
================================================================================= */
//----データベース接続クラス
class DbSql {

	var $Db_con;
	var $Rec;
	var $msg;

	function Connect() {
		$this->Db_con = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_NAME, $this->Db_con);
		mysql_query("SET NAMES utf8");
		return $this->Db_con;
	}

	function Query($u_sql, $u_line, $u_src = "") {
		$cls_Ret = mysql_query($u_sql, $this->Db_con);
		if($cls_Ret) {
			$this->msg = "";
		} else {
			$this->msg = "error:" . $u_src;
			if($u_line) $this->msg .= " line:" . $u_line;
			$this->msg .= " " . $u_sql;
		}
		return $cls_Ret;
	}

	function getErrMsg() {
		return $this->msg;
	}

	function fetch($u_obj) {
		return mysql_fetch_assoc($u_obj);
	}

	function getNums($u_obj) {
		return mysql_num_rows($u_obj);
	}

	function getArray($u_obj) {
		$R = 0;
		while($cls_Rec = mysql_fetch_assoc($u_obj)) {
			$ws_rec[$R] = $cls_Rec;
			$R++;
		}
		return $ws_rec;
	}
}

//----シンプルSQL検索
function PHP_simpleQuery($u_tblname, $u_keyword, $u_keyval, $u_delflg) {

	$ws_SQL = "select * from " . $u_tblname . " where %s='%s'";
	if($u_delflg) $ws_SQL .= " and delete_flg=0";
	$sql = sprintf($ws_SQL, $u_keyword, $u_keyval);
	$res = mysql_query($sql, DbCon);
	if($res == false) {
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(PHP_simpleQuery)";
		header("Location: " . WWW_URL . "error.php");
		exit();
	}
	$row = mysql_fetch_assoc($res);
	return $row;
}

$Db = new DbSql();
define("DbCon", $Db->Connect());		//データベース接続 in local
?>
