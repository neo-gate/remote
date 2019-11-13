<?php

include("../include/common.php");

include(MAN_INC."AWSSDKforPHP/aws.phar");

use Aws\S3\S3Client;
use Aws\Common\Enum\Region;
use Aws\S3\Exception\S3Exception;
use Guzzle\Http\EntityBody;

$error = "";

if( isset($_POST["send"]) == true ){

	$car_type = $_POST["car_type"];
	$car_color = $_POST["car_color"];
	$car_number = $_POST["car_number"];
	$tel = $_POST["tel"];
	$area = $_POST["area"];
	$staff_id = $_POST["staff_id"];
	$ch = $_POST["ch"];
	$car_image_url = $_POST["car_image_url"];
	$etc_number = $_POST["etc_number"];
	
	$file_name = $_FILES["pic"]["name"];
	$tmp_pic_url = $_FILES["pic"]["tmp_name"];
	$tmp_pic_type = $_FILES['pic']["type"];
	
	if( $file_name != "" ){
		$result = check_upload_file_name_common($file_name);
		if( $result == false ){
			$error .= "<li>ファイル名は半角英数字のみです</li>";
		}
	}
	
	if( $car_type == "" ){
		$error .= "<li>車種が未入力です</li>";
	}else{
		$num = mb_strlen( $car_type );
		if( $num > 30 ){
			$error .= "<li>車種は30文字までです</li>";
		}
	}
	
	if( $car_color == "" ){
		$error .= "<li>色が未入力です</li>";
	}else{
		$num = mb_strlen( $car_color );
		if( $num > 30 ){
			$error .= "<li>色は30文字までです</li>";
		}
	}
	
	if( $car_number == "" ){
		$error .= "<li>ナンバーが未入力です</li>";
	}else{
		$num = mb_strlen( $car_number );
		if( $num > 30 ){
			$error .= "<li>ナンバーは30文字までです</li>";
		}
	}
	
	$tel_tmp = str_replace("-","",$tel);
	if( $tel_tmp == "" ){
		$error .= "<li>電話番号が未入力です</li>";
	}else if( !preg_match("/^[0-9]+$/", $tel_tmp) ){
		$error .= "<li>電話番号の入力値が不正です</li>";
	}else{
		$num = mb_strlen( $tel );
		if( $num > 30 ){
			$error .= "<li>電話番号は30文字までです</li>";
		}
	}
	
	if( $error == "" ){
		
		if ( is_uploaded_file($tmp_pic_url) == TRUE ) {
		
			if( $file_name != "" ){
		
				$upload_file_name = $staff_id."_".time()."_".$file_name;
				$img_url = "img/driver/driver/".$upload_file_name;
				
				$car_image_url = $img_url;
		
				//S3へ、アップ
				$client = S3Client::factory(array(
					"key" => AWS_KEY,
					"secret" => AWS_SECRET,
					"region" => Region::AP_NORTHEAST_1 // AP_NORTHEAST_1はtokyo region
				));
				
				//$result = writer_image_upload_resize_common($tmp_pic_url);
				$result = true;
				
				if( $result == false ){
					
					$error .= "<li>ファイルのリサイズに失敗しました</li>";
					
				}else{
		
					$tmpfile = $tmp_pic_url;
					$tempFileType = $tmp_pic_type;
			
					// バケット名
					$bucket = "s3-refle";
					// アップロードファイル名
					$key = $img_url;
			
					try {
						$result = $client->putObject(array(
							'Bucket' => $bucket,
							'Key' => $key,
							'Body' => EntityBody::factory(fopen($tmpfile, 'r')),
							'ContentType' => $tempFileType
						));
							
					} catch (S3Exception $exc) {
						
						$error .= "<li>ファイルのアップに失敗しました</li>";
						
					}
				
				}
		
			}
		
		}
		
	}
	
	if( $error != "" ){
	
		$error = "<ul style='color:red;'>".$error."</ul>";
	
	}else{
	
		$_SESSION["d_f_car_type"] = $car_type;
		$_SESSION["d_f_car_color"] = $car_color;
		$_SESSION["d_f_car_number"] = $car_number;
		$_SESSION["d_f_tel"] = $tel;
		$_SESSION["d_f_area"] = $area;
		$_SESSION["d_f_staff_id"] = $staff_id;
		$_SESSION["d_f_ch"] = $ch;
		$_SESSION["d_f_car_image_url"] = $car_image_url;
		$_SESSION["d_f_etc_number"] = $etc_number;
	
		header('Location: confirm.php');
		exit();
	
	}


}else if(isset($_GET["back"])==true){
	
	$car_type = $_SESSION["d_f_car_type"];
	$car_color = $_SESSION["d_f_car_color"];
	$car_number = $_SESSION["d_f_car_number"];
	$tel = $_SESSION["d_f_tel"];
	$area = $_SESSION["d_f_area"];
	$staff_id = $_SESSION["d_f_staff_id"];
	$ch = $_SESSION["d_f_ch"];
	$car_image_url = $_SESSION["d_f_car_image_url"];
	$etc_number = $_SESSION["d_f_etc_number"];
	
}else if( ($_GET["area"] != "") && ($_GET["id"] != "") ){

	$area = $_GET["area"];
	$staff_id = $_GET["id"];
	$ch = $_GET["ch"];
	
	$data = get_staff_tmp_from_d_f_common($staff_id);
	
	$car_type = $data["car_type"];
	$car_color = $data["car_color"];
	$car_number = $data["car_number"];
	$tel = $data["tel"];
	$car_image_url = $data["car_image_url"];
	$etc_number = $data["etc_number"];

}else{

	echo "error!";
	exit();

}

/*
echo "<pre>";
print_r($data);
echo "</pre>";
exit();
*/

$staff_name = get_staff_name_by_staff_id($staff_id);

$top_url = get_top_url($area,$staff_id,$ch);

$page_title = "ドライバー情報編集(入力)";



$params['top_url'] = $top_url;
$params['staff_name'] = $staff_name;
$params['page_title'] = $page_title;

$params['car_type'] = $car_type;
$params['car_color'] = $car_color;
$params['car_number'] = $car_number;
$params['tel'] = $tel;
$params['car_image_url'] = $car_image_url;
$params['etc_number'] = $etc_number;

$params['area'] = $area;
$params['staff_id'] = $staff_id;
$params['ch'] = $ch;

$params['error'] = $error;



$smarty->assign( 'params', $params );

$smarty->assign( 'content_tpl', 'sp/driver_edit/input.tpl' );
$smarty->display( 'sp/template.tpl' );

?>