<?php
/* =================================================================================
		Script Name	func_sub.inc( 共通ファンクション群 )
		Update Date	2018/03/22	by aida
================================================================================= */

//----文字列の両端にシングルクォーテーションを付加
function PHP_addQuot($u_str, $u_delimit) {
	$ws_ArStr = explode(",", $u_str);
	//if(count($ws_ArStr) == 1) {
	//	return "'" . $u_str "'";
	//}
	$ws_retStr = "";
	foreach($ws_ArStr as $val) {
		if($ws_retStr) $ws_retStr .= $u_delimit;
		$ws_retStr .= "'" . $val . "'";
	}
	return $ws_retStr;
}
?>
