<?php

include("include/common.php");



$result = check_auto_login_bbs();

if( $result == true ){
	
	// インデックスページにリダイレクト
	header("Location: ".WWW_URL."index.php");
	exit();
	
}

if( isset($_POST["send"]) == true ){
	
	$tel = $_POST["tel"];
	$error = "";
	$result = false;
	
	if( $tel == "" ){
		
		$error = "ログインに失敗しました。";
		
	}
	
	if( $error == "" ){
	
		//ログイン実行
		$result = login_action_bbs($tel);
		
		if( $result == false ){
			
			$error = "ログインに失敗しました。";
			
		}else{
			
			$bbs_staff_type = $_SESSION["bbs_staff_type"];
			$page_area = $_SESSION["bbs_staff_area"];
			$bbs_staff_id = $_SESSION["bbs_staff_id"];
			$bbs_boss_flg = $_SESSION["bbs_boss_flg"];
			$therapist_flg = $_SESSION["bbs_therapist_flg"];
			
			if( $bbs_staff_type == "honbu" ){
				
				$page_area = "all";
				
			}
			
			if( $therapist_flg != true ){
			
				$result = check_today_attendance_data_driver($bbs_staff_id,$bbs_staff_type,$bbs_boss_flg,$page_area);
					
				if( $result == false ){
				
					$error = "本日出勤ではありません";
				
				}
				
				//スタッフのエリアを、出勤データのエリアに、上書き
				//check_today_attendance_data_driver関数内でセッション変数上書き
				$page_area = $_SESSION["bbs_staff_area"];
				
				if( ($bbs_staff_type=="driver") && ($page_area=="tokyo") ){
						
					$error = "ログインに失敗しました。";
						
				}
			
			}
			
			if( $error == "" ){
				
				//echo "login_success!";exit();
			
				$url = sprintf("%sindex.php?page_area=%s",WWW_URL,$page_area);
				
				if( $bbs_staff_id == "122" ){
					
					//echo $url;exit();
					
				}
				
				// インデックスページにリダイレクト
				header("Location: ".$url);
				exit();
			
			}else{
				
				reset_login_session();
				
			}
			
		}
	
	}
	
}

$params['page_title'] = "業務連絡BBS(ログイン)";
$params['error'] = $error;

$smarty->assign( 'params', $params );

if( ( $access_type == "pc" ) || ( $access_type == "sp" ) ){

	$smarty->assign( 'content_tpl', 'sp/login.tpl' );
	$smarty->display( 'sp/template.tpl' );

}else if( $access_type == "m" ){

	$smarty->assign( 'content_tpl', 'm/login.tpl' );
	$smarty->display( 'm/template.tpl' );

}

?>