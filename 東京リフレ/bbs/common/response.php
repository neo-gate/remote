<?php

include("include/common.php");
include(INC_PATH."/auth.php");



if( isset($_POST["send"]) == true ){

	$area = $_POST["area"];
	$naiyou = $_POST["naiyou"];
	$to_staff_id = $_POST["to_staff_id"];
	$to_therapist_id = $_POST["to_therapist_id"];
	
	if( $naiyou == "" ){
	
		$error = "未入力です";
	
	}
	
	if( $error == "" ){
	
		$_SESSION["bbs_response_area"] = $area;
		$_SESSION["bbs_response_naiyou"] = $naiyou;
		$_SESSION["bbs_response_to_staff_id"] = $to_staff_id;
		$_SESSION["bbs_response_to_therapist_id"] = $to_therapist_id;
	
		header('Location: response_confirm.php');
		exit();
	
	}

}elseif( isset($_GET["area"]) == true ){

	$area = $_GET["area"];
	$to_staff_id = $_GET["staff_id"];
	$to_therapist_id = $_GET["therapist_id"];
	
	$_SESSION["bbs_response_area"] = "";
	$_SESSION["bbs_response_naiyou"] = "";
	$_SESSION["bbs_response_to_staff_id"] = "";
	$_SESSION["bbs_response_to_therapist_id"] = "";

}else if(isset($_GET["back"])==true){

	$area = $_SESSION["bbs_response_area"];
	$naiyou = $_SESSION["bbs_response_naiyou"];
	$to_staff_id = $_SESSION["bbs_response_to_staff_id"];
	$to_therapist_id = $_SESSION["bbs_response_to_therapist_id"];

}else{

	header('Location: error.php');
	exit();

}



$params['page_title'] = "業務連絡BBS(返信・入力)";

$params['area'] = $area;
$params['naiyou'] = $naiyou;
$params['to_staff_id'] = $to_staff_id;
$params['to_therapist_id'] = $to_therapist_id;

$smarty->assign( 'params', $params );

if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/response.tpl' );
	$smarty->display( 'm/template.tpl' );

}else{
	
	$smarty->assign( 'content_tpl', 'sp/response.tpl' );
	$smarty->display( 'sp/template.tpl' );
	
}

?>