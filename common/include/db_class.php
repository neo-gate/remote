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
		$this->Db_con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		mysqli_select_db($this->Db_con, DB_NAME);
		mysqli_query($this->Db_con, "SET NAMES utf8");
		return $this->Db_con;
	}

	function Query($u_sql, $u_line, $u_src = "") {
		$cls_Ret = mysqli_query($this->Db_con, $u_sql);
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
		return mysqli_fetch_assoc($u_obj);
	}

	function getNums($u_obj) {
		return mysqli_num_rows($u_obj);
	}

	function getArray($u_obj) {
		$R = 0;
		while($cls_Rec = mysqli_fetch_assoc($u_obj)) {
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
	$res = mysqli_query(DbCon, $sql);
	if($res == false) {
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(PHP_simpleQuery)";
		header("Location: " . WWW_URL . "error.php");
		exit();
	}
	$row = mysqli_fetch_assoc($res);
	return $row;
}

$Db = new DbSql();
define("DbCon", $Db->Connect());		//データベース接続 in local
?>
