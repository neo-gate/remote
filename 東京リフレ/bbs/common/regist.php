<?php

include("include/common.php");
include(INC_PATH."/auth.php");

$error = "";

if( isset($_POST["send"]) == true ){
	
	$area = $_POST["area"];
	$naiyou = $_POST["naiyou"];
	
	if( $naiyou == "" ){
		
		$error = "未入力です";
		
	}
	
	if( $error == "" ){
	
		$_SESSION["bbs_regist_area"] = $area;
		$_SESSION["bbs_regist_naiyou"] = $naiyou;
		
		header('Location: regist_confirm.php');
		exit();
	
	}
	
}elseif( isset($_GET["area"]) == true ){
	
	$area = $_GET["area"];
	
	$_SESSION["bbs_regist_area"] = "";
	$_SESSION["bbs_regist_naiyou"] = "";
	
}else if(isset($_GET["back"])==true){
	
	$area = $_SESSION["bbs_regist_area"];
	$naiyou = $_SESSION["bbs_regist_naiyou"];
	
}else{
	
	header('Location: error.php');
	exit();
	
}


$params['page_title'] = "業務連絡BBS(新規メッセージ投稿・入力)";

$params['naiyou'] = $naiyou;
$params['area'] = $area;
$params['error'] = $error;

$smarty->assign( 'params', $params );

if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/regist.tpl' );
	$smarty->display( 'm/template.tpl' );

}else{
	
	$smarty->assign( 'content_tpl', 'sp/regist.tpl' );
	$smarty->display( 'sp/template.tpl' );
	
}

?>