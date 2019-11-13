<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$namae = $_POST["namae"];
$mail = $_POST["mail"];
$gender = $_POST["gender"];
$today_month = $_POST["today_month"];
$today_day = $_POST["today_day"];
$today_year = $_POST["today_year"];
$time = $_POST["time"];
$course = $_POST["course"];
$hotel_or_home = $_POST["hotel_or_home"];
$hotel_name = $_POST["hotel_name"];
$room_number = $_POST["room_number"];
$home_address = $_POST["home_address"];
$cash_or_credit = $_POST["cash_or_credit"];
$any_request = $_POST["any_request"];

if( $namae == "" ){

	$error .= '<li>Name is empty</li>';

}

if( $mail == "" ){

	$error .= '<li>Your E-mail is empty</li>';

}

if( $time == "-1" ){

	$error .= '<li>Time of use is not selected</li>';

}

if( $course == "-1" ){

	$error .= '<li>Massage time length is not selected</li>';

}

if( $hotel_or_home == "" ){

	$error .= '<li>Hotel or Home is not selected</li>';

}else{

	if( $hotel_or_home == "hotel" ){
			
		if( $hotel_name == "" ){

			$error .= '<li>Hotel name is empty</li>';

		}
			
	}else if( $hotel_or_home == "home" ){

		if( $home_address == "" ){

			$error .= '<li>Home address is empty</li>';

		}

	}

}

if( $cash_or_credit == "" ){

	$error .= '<li>How to pay is not selected</li>';

}

if( $error != "" ){

	$error = '<ul id="error_booking_form">'.$error.'</ul>';
	
	echo $error;
	exit();

}else{

	$area = "tokyo";
	
	//メール送信
	$result = send_booking_form_ch_common(
$namae,$mail,$gender,$today_month,$today_day,$today_year,
$time,$course,$hotel_or_home,$hotel_name,$room_number,
$home_address,$cash_or_credit,$any_request,$area
	);

	if( $result == false ){

		$error = '<ul id="error_booking_form"><li>Mail action is failure.Try again,please.</li></ul>';
		
		echo $error;
		exit();

	}else{

/*
$data =<<<EOT
<ul id="success_booking_form">
<li>Thank you very much for your booking.</li>
<li>We sent e-mail to you.</li>
<li>Confirm it,please.</li>
</ul>
EOT;
echo $data;
exit();
*/

		echo "ok";
		exit();

	}

}












?>