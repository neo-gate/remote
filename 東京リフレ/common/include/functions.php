<?php
/* =================================================================================
		Script Name	functions.php( 共通？ファンクション群 )
		Update Date	2018/02/22	全面的に整理 by aida
================================================================================= */
include_once(COMMON_INC . "const.php");			//in ^common/include/
include_once(COMMON_INC . "db_class.php");		//in ^common/include/
include_once(COMMON_INC . "mail.php");			//in ^common/include/
include_once(COMMON_INC . "shop_area_list.php");
include_once(COMMON_INC . "time_array.php");
include_once(COMMON_INC . "html_common.php");
include_once(COMMON_INC . "func_sub.inc");
include_once(COMMON_INC . "func_reserv.inc");
include_once(COMMON_INC . "team_data.php");
//===================================================================================
//----予約状況データ配列取得 ※注意 by aida
function get_reservation_for_board_by_forget_therapist_thanks_common($year,$month,$day){

	//update by aida at 20180220 from
	//$area_data = array(
	//	"0"=>"tokyo",
	//	"1"=>"yokohama",
	//	"2"=>"sapporo",
	//	"3"=>"sendai",
	//	"4"=>"osaka"
	//);

	$return_data = array();

	//$x["tokyo"] = 0;
	//$x["yokohama"] = 0;
	//$x["sapporo"] = 0;
	//$x["sendai"] = 0;
	//$x["osaka"] = 0;
global $area_data;
	foreach($area_data as $ws_Key => $ws_Val) {
		$return_data[$ws_Val] = 0;
	}
	//update by aida at 20180220 to

	$data = get_reservation_for_board_data_by_day_common($year,$month,$day);	//予約データ配列取得

	//リフレ以外は除外
	$data = select_only_refle_for_customer_voice_common($data);		//配列コピー 意味不明 by aida

	$data_num = count($data);

	for( $i=0; $i<$data_num; $i++ ){

		$reservation_for_board_id = $data[$i]["id"];
		$shop_area = $data[$i]["shop_area"];
		$attendance_id = $data[$i]["attendance_id"];
		$customer_id = $data[$i]["customer_id"];

		$therapist_id = get_therapist_id_by_attendance_id_common($attendance_id);	//出勤データIDからセラピストIDを取得

		$result1 = check_exist_therapist_thanks_by_reservation_for_board_id_common($reservation_for_board_id);	//セラピスト感謝データ有無

		$result2 = check_exist_black_list_common($therapist_id, $customer_id);		//ブラックリストデータ有無

		if( ( $result1 == false ) && ( $result2 == false ) ){

			$tmp = $x[$shop_area];
			$return_data[$shop_area][$tmp] = $data[$i];
			$return_data[$shop_area][$tmp]["therapist_id"] = $therapist_id;
			$x[$shop_area] = $x[$shop_area] + 1;

		}

	}

	$area_data_num = count($area_data);

	for( $i=0; $i<$area_data_num; $i++ ){

		$area_tmp = $area_data[$i];

		$tmp = $return_data[$area_tmp];

		$tmp_num = count($tmp);

		$therapist_data = array();

		for( $x=0; $x<$tmp_num; $x++ ){

			$therapist_id = $tmp[$x]["therapist_id"];

			$result = check_exist_therapist_data_common($therapist_data,$therapist_id);		//セラピストID有無

			if( $result == false ){

				$therapist_data_num = count($therapist_data);

				$therapist_name = get_therapist_name_real_by_therapist_id_common($therapist_id);	//セラピスト名取得(本名)

				$therapist_data[$therapist_data_num]["id"] = $therapist_id;
				$therapist_data[$therapist_data_num]["name"] = $therapist_name;
				$therapist_data[$therapist_data_num]["board_data"][0] = $tmp[$x];
			}else{
				$therapist_data = add_board_data_in_therapist_data_by_therapist_id_common($therapist_data,$therapist_id,$tmp[$x]);	//予約状況データ取得
			}
		}

		$return_data[$area_tmp] = $therapist_data;
	}

	return $return_data;
	exit();
}

function test_common(){
	echo "test";
	exit();
}

//----スタッフ情報取得（スタッフ）
function get_driver_data_for_sale_shop_common($year,$month,$day,$area){

	$page_area = $area;

	$type = "driver";

	$sql = sprintf("select * from staff_new_new where delete_flg='0' and shayousha_flg='0' and type='%s' and area='%s'",$type,$page_area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_driver_data_for_sale_shop_common:1)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$staff_id = $row["id"];

		$result = check_staff_attendance_exist_syounin_common($staff_id,$year,$month,$day);	//出席データが登録済みであるかどうか(登録済み：true,未登録:false)

		if( $result == true ){

			$list_data[$i] = $row;
			$list_data[$i]["area"] = $page_area;
			$i++;

		}

	}

	if( $area == "yokohama" ){

		$area_tokyo = "tokyo";

		$where_kenmu = "kenmu like '%".$page_area."%'";

		$sql = sprintf("select * from staff_new_new where delete_flg='0' and type='%s' and (%s) and area='%s'",$type,$where_kenmu,$area_tokyo);
		$res = mysql_query($sql, DbCon);
		if($res == false){

			echo "error!(get_driver_data_for_sale_shop_common:2)";
			exit();

		}

		while($row = mysql_fetch_assoc($res)){

			$staff_id = $row["id"];

			$result = check_staff_attendance_exist_syounin_common($staff_id,$year,$month,$day);	//出席データが登録済みであるかどうか(登録済み：true,未登録:false)

			if( $result == true ){
				$list_data[$i] = $row;
				$list_data[$i]["area"] = $area_tokyo;
				$i++;
			}
		}
	}

	return $list_data;
	exit();
}

//----スタッフ情報配列取得（スタッフ）
function get_attendance_staff_new_month_common($year,$month){

	$sql = sprintf("select * from attendance_staff_new where year='%s' and month='%s'",$year,$month);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_staff_new_month_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----出勤データ取得（スタッフ）承認済
function get_attendance_staff_new_day_and_staff_id_common($year,$month,$day,$staff_id){

	$sql = sprintf("select * from attendance_staff_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and staff_id='%s' and year='%s' and month='%s' and day='%s'",$staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_staff_new_day_and_staff_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データ取得（スタッフ）
function get_attendance_staff_new_day_and_staff_id_2_common($year,$month,$day,$staff_id){

	$sql = sprintf("select * from attendance_staff_new where staff_id='%s' and year='%s' and month='%s' and day='%s'",$staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_staff_new_day_and_staff_id_2_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データ取得（スタッフ）承認済
function get_staff_attendance_data_by_time_syounin_common($staff_id,$year,$month,$day){

	$sql = sprintf("select * from attendance_staff_new where syounin_state='1' and staff_id='%s' and year='%s' and month='%s' and day='%s' and kekkin_flg='0' and today_absence=0 and attendance_adjustment=0",$staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_staff_attendance_data_by_time_syounin_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データ取得(スタッフ)
function get_staff_month_attendance_data_common($staff_id){

	$today_year = intval(date("Y"));
	$today_month = intval(date("m"));
	$today_day = intval(date("d"));

	$next_month_year = intval(date('Y', strtotime(date('Y-m-1').' +1 month')));
	$next_month_month = intval(date('m', strtotime(date('Y-m-1').' +1 month')));

	$where_part = sprintf("day>=%s",$today_day);

	$sql = sprintf("select * from attendance_staff_new where staff_id='%s' and year='%s' and month='%s' and (%s) order by day asc",$staff_id, $today_year, $today_month, $where_part);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_staff_month_attendance_data_common)";
		exit();
	}

	$i=0;

	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	$sql = sprintf("select * from attendance_staff_new where staff_id='%s' and year='%s' and month='%s' order by day asc",$staff_id, $next_month_year, $next_month_month);
	$res = mysql_query($sql, DbCon);
	if($res == false){

		echo "error!(get_staff_month_attendance_data_common)";
		exit();

	}

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----出勤データ配列取得(スタッフ)
function get_attendance_staff_new_day_common($year,$month,$day){

	$sql = sprintf("select * from attendance_staff_new where today_absence='0' and year='%s' and month='%s' and day='%s'",$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_staff_new_day_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----出勤データ配列取得（スタッフ）
function get_driver_data_syounin_common($year,$month,$day,$area){

	$type = "driver";

	if($area == "all") {
		$sql = sprintf("select * from staff_new_new where delete_flg='0' and shayousha_flg='0' and type='%s'",$type);
	} else {
		$sql = sprintf("select * from staff_new_new where delete_flg='0' and shayousha_flg='0' and type='%s' and area='%s'",$type,$area);
	}

	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_driver_data_syounin_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$staff_id = $row["id"];

		$result = check_staff_attendance_exist_syounin_common($staff_id,$year,$month,$day);	//出席データが登録済みであるかどうか(登録済み：true,未登録:false)

		if( $result == true ){
			$list_data[$i] = $row;
			$list_data[$i]["area"] = $area;
			$i++;
		}
	}

	return $list_data;
	exit();
}

//----出勤データ取得（スタッフ）属性
function get_staff_type_by_attendance_id_common($attendance_id){

	$sql = sprintf("select type from attendance_staff_new where id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_staff_type_by_attendance_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["type"];
	exit();
}

//----出勤データ取得（スタッフ）ID
function get_attendance_id_staff_common($staff_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_staff_new where staff_id='%s' and year='%s' and month='%s' and day='%s'", $staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_id_staff_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();
}

//----同一エリア内の出勤情報（スタッフ）配列取得
function get_cost_data_day_from_attendance_staff_new_common($year,$month,$day,$area){

	$sql = sprintf("select * from attendance_staff_new where area='%s' and year='%s' and month='%s' and day='%s' and today_absence=0 and attendance_adjustment=0",$area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(get_cost_day_from_attendance_staff_new_common)";
		exit();
	}

	$gasoline_all = 0;
	$highway_all = 0;
	$parking_all = 0;
	$pay_finish_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$gasoline = $row["gasoline"];
		$highway = $row["highway"];
		$parking = $row["parking"];
		$pay_finish = $row["pay_finish"];

		$gasoline_all = $gasoline_all + $gasoline;
		$highway_all = $highway_all + $highway;
		$parking_all = $parking_all + $parking;
		$pay_finish_all = $pay_finish_all + $pay_finish;

	}

	$data["gasoline"] = $gasoline_all;
	$data["highway"] = $highway_all;
	$data["parking"] = $parking_all;
	$data["pay_finish"] = $pay_finish_all;

	return $data;
	exit();
}

//----スタッフ情報配列取得
function get_staff_data_by_day_common($year,$month,$day,$area){

	$page_area = $area;

	$type = "driver";

	$sql = sprintf("select * from staff_new_new where delete_flg='0' and type='%s' and area='%s' and shayousha_flg='0'",$type,$page_area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_staff_data_by_day_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$staff_id = $row["id"];

		$result = check_staff_attendance_exist_syounin_common($staff_id,$year,$month,$day);	//出席データが登録済みであるかどうか(登録済み：true,未登録:false)

		if( $result == true ){
			$list_data[$i] = $row;
			$list_data[$i]["area"] = $page_area;
			$i++;
		}
	}

	if( $page_area == "yokohama" ){

		$area_tokyo = "tokyo";

		$where_kenmu = "kenmu like '%".$page_area."%'";

		$sql = sprintf("select * from staff_new_new where delete_flg='0' and type='%s' and (%s) and area='%s' and shayousha_flg='0'",$type,$where_kenmu,$area_tokyo);
		$res = mysql_query($sql, DbCon);
		if($res == false){
			echo "error!(get_staff_data_by_day_common)";
			exit();
		}

		while($row = mysql_fetch_assoc($res)){

			$staff_id = $row["id"];

			$result = check_staff_attendance_exist_syounin_common($staff_id,$year,$month,$day);	//出席データが登録済みであるかどうか(登録済み：true,未登録:false)

			if( $result == true ){
				$list_data[$i] = $row;
				$list_data[$i]["area"] = $area_tokyo;
				$i++;
			}
		}
	}

	return $list_data;
	exit();
}

//----スタッフデータ更新
function update_staff_new_new_from_man_driver_common($staff_id,$car_type,$car_color,$car_number,$tel,$car_image_url){

	$sql = sprintf("update staff_new_new set car_type='%s',car_color='%s',car_number='%s',tel='%s',car_image_url='%s' where delete_flg=0 and id='%s'",$car_type,$car_color,$car_number,$tel,$car_image_url,$staff_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(update_staff_new_new_from_man_driver_common)";
		exit();
	}

	return true;
	exit();
}

//----出勤データ更新（スタッフ）状況ボード非表示フラグ
function update_not_board_display_flg_common($attendance_id,$not_board_display_flg){

	$sql = sprintf("update attendance_staff_new set not_board_display_flg='%s' where id='%s'",$not_board_display_flg,$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(update_not_board_display_flg_common)";
		exit();
	}

	return true;
	exit();
}

//----出勤データ（スタッフ）配列より指定年月のデータ取得
function get_attendance_data_from_month_data_staff_common($year,$month,$day,$staff_id,$month_data){

	$month_data_num = count($month_data);

	for( $i=0; $i<$month_data_num; $i++ ){

		$staff_id_tmp = $month_data[$i]["staff_id"];
		$year_tmp = $month_data[$i]["year"];
		$month_tmp = $month_data[$i]["month"];
		$day_tmp = $month_data[$i]["day"];

		if( ($staff_id_tmp==$staff_id) && ($year_tmp==$year) && ($month_tmp==$month) && ($day_tmp==$day) ){
			return $month_data[$i];
			exit();
		}
	}

	return false;
	exit();
}

//----出勤データ有無（スタッフ）
function check_week_attendance_exist_by_staff_id_common($week_data,$staff_id){

	$week_data_num = count($week_data);

	$where_day = "";

	$first_flg = true;

	for($i=0;$i<$week_data_num;$i++){

		$year = $week_data[$i]["year"];
		$month = $week_data[$i]["month"];
		$day = $week_data[$i]["day"];

		if( $first_flg == true ){
			$where_day .= sprintf("(year='%s' and month='%s' and day='%s')",$year,$month,$day);
			$first_flg = false;
		}else{
			$where_day .= sprintf(" or (year='%s' and month='%s' and day='%s')",$year,$month,$day);
		}
	}

	$sql = sprintf("select id from attendance_staff_new where (%s) and today_absence='0' and attendance_adjustment='0' and staff_id='%s'",$where_day,$staff_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_week_attendance_exist_by_staff_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----ドライバー日払い集計 ※注意 by aida
function get_pay_day_driver_common($year,$month,$day,$shop_name,$shop_area){

	$pay_day_all = 0;

	if( ($shop_area == "tokyo") && ($shop_name != "東京リフレ") ){
		return $pay_day_all;
		exit();
	}

	$sql = sprintf("select pay_day from attendance_staff_new where area='%s' and year='%s' and month='%s' and day='%s' and type='driver' and today_absence=0 and attendance_adjustment=0",$shop_area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(get_pay_day_driver_common)";
		exit();
	}

	while($row = mysql_fetch_assoc($res)){

		$pay_day = $row["pay_day"];

		$pay_day_all = $pay_day_all + $pay_day;
	}

	return $pay_day_all;
	exit();
}

//----出勤データ取得（スタッフ）その他費用
function get_driver_sonota_day_common($year,$month,$day,$shop_area){

	$sql = sprintf("select * from attendance_staff_new where area='%s' and year='%s' and month='%s' and day='%s' and today_absence=0 and attendance_adjustment=0",$shop_area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_driver_sonota_day_common)";
		exit();
	}

	$sonota_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$repair = $row["repair"];
		$insurance = $row["insurance"];
		$sonota = $row["sonota"];

		$sonota_all = $sonota_all + ( $repair + $insurance + $sonota );

	}

	return $sonota_all;
	exit();

}

//----同一エリア内のスタッフ情報配列取得
function PHP_getArrayStaff_area_common($u_area, $u_type) {

	$sql = sprintf("select * from staff_new_new where type='%s' and delete_flg='0' and area='%s'", $u_type, $u_area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_driver_data_by_area_common)";
		exit();
	}

	$i = 0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}
	return $list_data;
}

//----出勤データ取得（セラピスト）
function PHP_get_attendance_data_one_common($u_mode, $therapist_id, $year, $month, $day) {

	switch($u_mode) {
	case "syounin":
		//----承認済
		$sql = sprintf("select * from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
		break;
	default:
		//----シンプル
		$sql = sprintf("select * from attendance_new where therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	}
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql;
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_one_common)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	return $row;
}

//----出勤データ取得（セラピスト）
function get_today_attendance_id_by_therapist_id_common($therapist_id){

	$data = get_today_year_month_day_common();		//本日の年月日取得
	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_today_attendance_id_by_therapist_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();
}

//----出勤データ取得（セラピスト）
function get_attendance_data_by_day_and_area_common($year,$month,$day,$area){

	$sql = sprintf("select * from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and area='%s' and year='%s' and month='%s' and day='%s'",$area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_by_day_and_area_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----出勤データ取得（セラピスト）
function get_attendance_new_data_day_therapist_id_common($year,$month,$day,$therapist_id){

	$sql = sprintf("select * from attendance_new where year='%s' and month='%s' and day='%s' and therapist_id='%s'",$year,$month,$day,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_attendance_new_data_day_therapist_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データ取得（セラピスト）
function get_attendance_new_data_day_common($year,$month,$day){

	$sql = sprintf("select * from attendance_new where year='%s' and month='%s' and day='%s'", $year,$month,$day);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql;
	if( $res == false ){
		//echo "error!(get_attendance_new_data_day_common) " .$sql;
		//exit();
		return false;
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----出勤データ取得（セラピスト）
function get_attendance_new_month_common($year,$month){

	$sql = sprintf("select * from attendance_new where year='%s' and month='%s'", $year,$month);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_new_month_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----出勤データ取得（セラピスト）
function get_attendance_new_month_2_common($year,$month){

	$sql = sprintf("select * from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and year='%s' and month='%s'",$year,$month);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_new_month_2_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----出勤データ取得（セラピスト）
function get_attendance_data_by_where_common($where){

	$sql = sprintf("select * from attendance_new where %s",$where);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_data_by_where_common)";
		exit();
	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----出勤データ（セラピスト）配列より指定年月のデータ取得
function get_attendance_data_from_month_data_therapist_common($year,$month,$day,$therapist_id,$month_data){

	$month_data_num = count($month_data);

	for( $i=0; $i<$month_data_num; $i++ ){

		$therapist_id_tmp = $month_data[$i]["therapist_id"];
		$year_tmp = $month_data[$i]["year"];
		$month_tmp = $month_data[$i]["month"];
		$day_tmp = $month_data[$i]["day"];

		if( ($therapist_id_tmp==$therapist_id) && ($year_tmp==$year) && ($month_tmp==$month) && ($day_tmp==$day) ){
			return $month_data[$i];
			exit();
		}
	}

	return false;
	exit();
}

//----出勤データ取得（セラピスト）
function get_attendance_new_by_day_and_therapist_id_common($therapist_id, $year, $month, $day){
	return PHP_get_attendance_data_one_common("", $therapist_id, $year, $month, $day);	//出勤データ取得（セラピスト）
}

//----出勤データ数取得（セラピスト）
function get_attendance_num_month_therapist_id_common($year,$month,$day,$therapist_id){

	$sql = sprintf("select A.id from attendance_new as A left join attendance_2 as B on A.id=B.attendance_new_id where today_absence='0' and kekkin_flg='0' and syounin_state='1' and year='%s' and month='%s' and work_ts_start is not null and therapist_id='%s'",$year,$month,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(get_attendance_num_month_therapist_id_common)";
		exit();
	}
	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----セラピスト出勤データ削除
function delete_therapist_attendance_data_by_id_2_common($therapist_id,$year,$month,$day){

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, DbCon );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, DbCon );

	$sql = sprintf("delete from attendance_new where therapist_id='%s' and year='%s' and month='%s' and day='%s'", $therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, DbCon );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(delete_therapist_attendance_data_by_id_2_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$sql = sprintf("delete from attendance_new_small where therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, DbCon );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(delete_therapist_attendance_data_by_id_2_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	//コミット
	$sql = "commit";
	mysql_query( $sql, DbCon );

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}

//----セラピスト出勤データ（小）配列取得
function get_attendance_new_small_for_delete_common(){

	$sql = "select id,year,month,day from attendance_new_small order by id asc limit 0,1000";
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_new_small_for_delete_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----セラピスト出勤データ（小）有無
function delete_attendance_new_small_by_id_common($id){

	$sql = sprintf("delete from attendance_new_small where id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(delete_attendance_new_small_by_id_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピスト出勤データ（小）のエリア取得
function get_attendance_area_2_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select area from attendance_new_small where therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);

	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_area_2_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["area"];
	exit();
}

//----セラピスト出勤データ（小）のエリア取得3
function get_attendance_area_3_common($therapist_id,$year,$month,$day,$attendance_data){

	$area = "";

	$attendance_data_num = count($attendance_data);

	for( $i=0; $i<$attendance_data_num; $i++ ){

		$therapist_id_tmp = $attendance_data[$i]["therapist_id"];
		$year_tmp = $attendance_data[$i]["year"];
		$month_tmp = $attendance_data[$i]["month"];
		$day_tmp = $attendance_data[$i]["day"];
		$area_tmp = $attendance_data[$i]["area"];

		if( ($therapist_id_tmp==$therapist_id) && ($year_tmp==$year) && ($month_tmp==$month) && ($day_tmp==$day) ){

			$area = $area_tmp;

			return $area;
			exit();
		}
	}

	return $area;
	exit();
}

//----予約状況データ取得
function PHP_get_reservation_for_board_data_by_id_common($id, $u_del_flg) {

	if($u_del_flg) {
		$sql = sprintf("select * from reservation_for_board where delete_flg=0 and id='%s'",$id);
	} else {
		$sql = sprintf("select * from reservation_for_board where id='%s'",$id);
	}
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql;
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_reservation_for_board_data_by_id_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----予約状況データ取得
function get_reservation_for_board_data_by_therapist_id_for_shift_customer_voice_common($therapist_id,$ymd){

	$ws_SQL = "select A.*,C.id AS th_id, C.content AS th_content, D.content AS ctm_voice, E.publish_allow_therapist AS tp_allow, E.skill AS tp_skill, E.service AS tp_service from reservation_for_board A";
	$ws_SQL .= " left join attendance_new B on B.id=A.attendance_id";
	$ws_SQL .= " left join therapist_thanks C on C.reservation_for_board_id=A.id";
	$ws_SQL .= " left join customer_voice D on D.reservation_for_board_id=A.id";
	$ws_SQL .= " left join customer_evaluation E on E.reservation_for_board_id=A.id";
	$ws_SQL .= " where A.delete_flg='0' and A.complete_flg='1' and B.therapist_id='%s'";
	$ws_SQL .= " and A.year*10000+A.month*100+A.day>='%s'";
	$ws_SQL .= " order by A.id desc";
	$sql = sprintf($ws_SQL, $therapist_id, $ymd);

	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_for_board_data_by_therapist_id_for_shift_customer_voice_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----予約状況データ取得
function get_sale_data_month_therapist_id_common($year,$month,$therapist_id){

	$ws_SQL = "select B.price,A.shimei_flg,A.transportation from reservation_for_board A";
	$ws_SQL .= " left join sale_history B on B.reservation_no=A.reservation_no";
	$ws_SQL .= " where A.year='%s' and A.month='%s' and A.delete_flg=0 and A.complete_flg='1'";
	$ws_SQL .= " and B.delete_flg=0 and B.eigyou_year='%s' and B.eigyou_month='%s' and B.therapist_id='%s'";
	$sql = sprintf($ws_SQL, $year,$month,$year,$month,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_sale_data_month_therapist_id_common)";
		exit();
	}

	$total = 0;

	$price_all = 0;
	$price_shimei = 0;
	$price_all_bigao = 0;
	$price_shimei_bigao = 0;

	while($row = mysql_fetch_assoc($res)){

		$price = $row["price"];
		$shimei_flg = $row["shimei_flg"];
		$transportation = $row["transportation"];

		$price_all = $price_all + $price;
		$tmp = $price - $transportation;

		if( $shimei_flg == "1" ){

			$price_shimei = $price_shimei + $price;
			$tmp = $tmp - 1000;
			$price_shimei_bigao = $price_shimei_bigao + $tmp;
		}

		$price_all_bigao = $price_all_bigao + $tmp;

	}

	$data["price_all"] = $price_all;
	$data["price_shimei"] = $price_shimei;
	$data["price_all_bigao"] = $price_all_bigao;
	$data["price_shimei_bigao"] = $price_shimei_bigao;

	return $data;
	exit();
}

//----予約状況データ取得（電話番号より）
function get_reservation_for_board_data_by_customer_tel_for_shift_customer_voice_common($customer_tel){

	$ws_SQL = "select A.* from reservation_for_board A";
	$ws_SQL .= " left join attendance_new B on B.id=A.attendance_id";
	$ws_SQL .= " where A.delete_flg='0' and A.complete_flg='1' and A.customer_tel='%s'";
	$ws_SQL .= " order by A.id desc";
	$sql = sprintf($ws_SQL, $customer_tel);

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(get_reservation_for_board_data_by_customer_tel_for_shift_customer_voice_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----予約状況データ配列取得（顧客IDより）
function get_reservation_for_board_data_by_customer_id_for_vip_history_common($customer_id){

	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and customer_id='%s' order by id desc",$customer_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_for_board_data_by_customer_id_for_vip_history_common)";
		exit();
	}
	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----予約状況データ配列取得（電話番号より）
function get_reservation_for_board_data_by_customer_id_for_vip_history_2_common($customer_tel){

	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and customer_tel='%s' order by id desc",$customer_tel);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_for_board_data_by_customer_id_for_vip_history_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//2018/11/13 murase insert from
//----予約状況データ配列取得（電話番号及び顧客IDより）
function ctm_tel_list($customer_tel){
	$sql = sprintf("select * from customer where delete_flag='0' and (tel='%s' or tel_2='%s' or tel_3='%s' or tel_4='%s' or tel_5='%s' or tel_6='%s' or tel_7='%s' or tel_8='%s' or tel_9='%s' or tel_10='%s' )", $customer_tel, $customer_tel, $customer_tel, $customer_tel, $customer_tel, $customer_tel, $customer_tel, $customer_tel, $customer_tel, $customer_tel);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(ctm_tel_list)";
		exit();
	}

	$rows = mysql_fetch_assoc($res);

  return $rows;

}
//2018/11/13 murase insert to

//----予約状況データ配列取得（電話番号及び顧客IDより）
function PHP_get_reservation_for_board_data_by_customer_id_for_vip_history_common($customer_id){

	//$ws_tel2 =str_replace("-", "", $customer_tel);
	$ws_Ymd = date("Ymd");

	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and customer_id='%s'and (year*10000+month*100+day)<=%s order by id desc",$customer_id, $ws_Ymd);
	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(PHP_get_reservation_for_board_data_by_customer_id_for_vip_history_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
}

//----予約状況データ更新（顧客ID）
function update_customer_id_at_reservation_for_board_common($reservation_for_board_id,$customer_id){

	$sql = sprintf("update reservation_for_board set customer_id='%s' where id='%s'",$customer_id,$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(update_customer_id_at_reservation_for_board_common)";
		exit();
	}

	return true;
	exit();
}

//----予約状況データ数取得
function get_pt_operation_by_attendance_id_common($attendance_id){
	//$ws_data = PHP_get_reservation_for_board_data_by_id_common($id, false);

	$sql = sprintf("select id from reservation_for_board where delete_flg=0 and attendance_id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_pt_operation_by_attendance_id_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----指名ポイント取得（予約状況データ）
function get_pt_shimei_by_attendance_id_common($attendance_id){

	$sql = sprintf("select shimei_flg from reservation_for_board where delete_flg=0 and attendance_id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_pt_shimei_by_attendance_id_common)";
		exit();
	}

	$shimei_point = 0;

	while($row = mysql_fetch_assoc($res)){

		$shimei_flg = $row["shimei_flg"];

		if( $shimei_flg == "1" ){
			$shimei_point = $shimei_point + 3;
		}
	}

	return $shimei_point;
	exit();
}

//----店舗情報配列取得
function get_shop_data_all_common(){

	$sql = "select * from shop where delete_flg=0";
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_shop_data_all_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$list_data[$i++] = $row;

	}

	return $list_data;
	exit();
}

//----店舗のエリア取得
function get_area_by_shop_name_common($shop_name){

	$sql = sprintf("select area from shop where name='%s'",$shop_name);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_area_by_shop_name_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row["area"];
	exit();
}

//----店舗のID取得
function get_shop_id_by_shop_name_common($shop_name){

	$shop_name = trim($shop_name);

	$sql = sprintf("select id from shop where name='%s'",$shop_name);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_shop_id_by_shop_name_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();
}

//----フリーセラピスト状態配列取得56
function PHP_setFreeTherapistState_56($most_start_time, $therapist_array) {

	//----公開フラグ＝１の該当日出勤のセラピスト情報取得
	$attendance_free = get_attendance_new_for_free_therapist_state_common($year,$month,$day);

	//移動時間もあるため、開始時間ぴったりの予約には対応できない
	$most_start_time = $most_start_time + 1;

	$therapist_array_num = count($therapist_array);

	//対応可能:1,対応不可:0

	$free_therapist_state = array(
			"1" => 0,
			"2" => 0,
			"3" => 0,
			"4" => 0,
			"5" => 0,
			"6" => 0,
			"7" => 0,
			"8" => 0,
			"9" => 0,
			"10" => 0,
			"11" => 0,
			"12" => 0,
			"13" => 0,
			"14" => 0,
			"15" => 0,
			"16" => 0,
			"17" => 0,
			"18" => 0,
			"19" => 0,
			"20" => 0,
			"21" => 0,
			"22" => 0,
			"23" => 0,
			"24" => 0,
			"25" => 0,
			"26" => 0,
			"27" => 0,
			"28" => 0,
			"29" => 0,
			"30" => 0,
			"31" => 0,
			"32" => 0,
			"33" => 0,
			"34" => 0,
			"35" => 0
	);

	for($i=1; $i<=35; $i++) {

		$i_tmp = $i - 12;

		if( ($under6_flag==true) && ($i<=24) && ($today_flag==true) ){

			//0時すぎは、前日分はすべてバツ

			$free_therapist_state[$i] = 0;

		}else if( $i_tmp<$most_start_time ){

			//開始時間ぴったりの予約はできない
			$free_therapist_state[$i] = 0;

		}else if( $i_tmp>$most_end_time ){

			$free_therapist_state[$i] = 0;

		}else if( ($today_flag == true) && (past_time_common($i)) ){

			$free_therapist_state[$i] = 0;

		}else{

			$kettei_flag=false;
			for($j=0;$j<$therapist_array_num;$j++){
				if($kettei_flag==false){
					$therapist_id = $therapist_array[$j];

					//----セラピスト情報配列より指定セラピストの情報取得
					$attendance_tmp = get_attendance_new_from_attendance_free_common($attendance_free,$therapist_id);
					$attendance_tmp_num = count($attendance_tmp);

					$tmp_end_flag = false;

					for( $l=0; $l<$attendance_tmp_num; $l++ ){

						$tmp = $attendance_tmp[$l];

						$end_time = $tmp["end_time"]+1;

						if( $tmp_end_flag == false ){

							if( $tmp["start_time"] > $i_tmp ){

								$tmp_end_flag = true;

							}else if( $end_time < $i_tmp ){

								$tmp_end_flag = true;

							}else{

								$tmp_start_time = $tmp["start_time"];
								$tmp_end_time = $end_time - 1;//5時までだからマイナス1？
								$tmp_time = $tmp["time"];

								//移動時間があるので勤務開始時間ぴったりの予約には対応できない
								$tmp_start_time = $tmp_start_time + 1;

								if( $tmp_time == $i_tmp ){

									$free_therapist_state[$i] = 0;
									$tmp_end_flag=true;

								}else{

									$for_end_flag = false;

									for( $k=$tmp_start_time; $k<=$tmp_end_time; $k++ ){

										if( $for_end_flag == false ){

											if( $k == $i_tmp ){
												$free_therapist_state[$i] = 1;
												$for_end_flag = true;
											}
										}
									}
								}
							}
						}
					}
					if( $free_therapist_state[$i] == 1 ) $kettei_flag = true;
				}
			}
		}
	}

	return $free_therapist_state;
}

//----フリーセラピスト状態配列取得234
function PHP_setFreeTherapistState_234($u_mode, $most_start_time, &$therapist_array, $year, $month, $day) {

	//----公開フラグ＝１の該当日出勤のセラピスト情報取得
	$attendance_free = get_attendance_new_for_free_therapist_state_common($year,$month,$day);

	//移動時間もあるため、開始時間ぴったりの予約には対応できない
	$most_start_time = $most_start_time + 1;

	$therapist_array_num = count($therapist_array);

	//対応可能:1,対応不可:0

	$free_therapist_state = array(
			"1" => 0,
			"2" => 0,
			"3" => 0,
			"4" => 0,
			"5" => 0,
			"6" => 0,
			"7" => 0,
			"8" => 0,
			"9" => 0,
			"10" => 0,
			"11" => 0,
			"12" => 0,
			"13" => 0,
			"14" => 0,
			"15" => 0,
			"16" => 0,
			"17" => 0,
			"18" => 0,
			"19" => 0,
			"20" => 0,
			"21" => 0,
			"22" => 0,
			"23" => 0,
			"24" => 0,
			"25" => 0
	);

	if($u_mode == 4) $ws_max = 23; else $ws_max = 25;

	//for($i=1;$i<=23;$i++){
	for($i=1; $i<=$ws_max; $i++) {		//???? 4は23、2or3は25



			$kettei_flag=false;
			for($j=0;$j<$therapist_array_num;$j++){
				if($kettei_flag==false){
					$therapist_id = $therapist_array[$j];

					//----セラピスト情報配列より指定セラピストの情報取得
					$attendance_tmp = get_attendance_new_from_attendance_free_common($attendance_free,$therapist_id);
					$attendance_tmp_num = count($attendance_tmp);

					$tmp_end_flag=false;

					for( $l=0; $l<$attendance_tmp_num; $l++ ){

						$tmp = $attendance_tmp[$l];

						if($u_mode == 2) $end_time = $tmp["end_time"]; else $end_time = $tmp["end_time"] + 1;		//?????  2は＋１しない

						if( $tmp_end_flag == false ){

							if($tmp["start_time"] > $i) {
								$tmp_end_flag = true;
							} else if( $end_time < $i ) {
								$tmp_end_flag=true;
							} else {

								$tmp_start_time = $tmp["start_time"];
								$tmp_end_time = $end_time - 1;//5時までだからマイナス1？
								$tmp_time = $tmp["time"];

								//移動時間があるので勤務開始時間ぴったりの予約には対応できない
								$tmp_start_time = $tmp_start_time + 1;

								if( $tmp_time == $i ){
									$free_therapist_state[$i] = 0;
									$tmp_end_flag=true;
								}else{

									$for_end_flag = false;

									for( $k=$tmp_start_time; $k<=$tmp_end_time; $k++ ){

										if( $for_end_flag == false ){
											if( $k == $i ){
												$free_therapist_state[$i] = 1;
												$for_end_flag = true;
											}
										}
									}
								}
							}
						}
					}

					if( $free_therapist_state[$i] == 1 ) $kettei_flag = true;
				}
			}

	}

	return $free_therapist_state;
}

function furiwake_common(){

	$type = "pc";

	$ua = $_SERVER['HTTP_USER_AGENT'];

	if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)) {
		// スマートフォンからアクセスされた場合
		$type = "sp";

	} elseif ((strpos($ua, 'Android') !== false) || (strpos($ua, 'iPad') !== false)) {
		// タブレットからアクセスされた場合
		$type = "pc";

	} elseif ((strpos($ua, 'DoCoMo') !== false) || (strpos($ua, 'KDDI') !== false) || (strpos($ua, 'SoftBank') !== false) || (strpos($ua, 'Vodafone') !== false) || (strpos($ua, 'J-PHONE') !== false)) {
		// 携帯からアクセスされた場合
		$type = "m";

	}

	return $type;
	exit();

}

//本日出勤セラピストのID取得
function get_today_work_therapist_data_wait_time_common($area){

	$ws_date = PHP_get_today_ymd_common(6);		//本日の年月日取得
	$year = $ws_date["year"];
	$month = $ws_date["month"];
	$day = $ws_date["day"];

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select B.id,B.name_site,B.rank,A.id as attendance_id,A.start_time,A.end_time from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where B.delete_flg=0 and B.leave_flg=0 and B.test_flg=0";
	$ws_SQL .= " and A.year='%s' and A.month='%s' and A.day='%s' and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.syounin_state='1'";
	$sql = sprintf($ws_SQL,$year,$month,$day,$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_today_work_therapist_id)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//本日出勤セラピストのID取得(for_bot)
function get_today_work_therapist_data_wait_time_for_bot_common($area){

	$ws_date = PHP_get_today_ymd_common(6);		//本日の年月日取得
	$year = $ws_date["year"];
	$month = $ws_date["month"];
	$day = $ws_date["day"];

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select B.id,B.name_site,B.rank,A.id as attendance_id,A.start_time,A.end_time from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where B.delete_flg=0 and B.leave_flg=0 and B.test_flg=0";
	$ws_SQL .= "  and A.year='%s' and A.month='%s' and A.day='%s' and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.publish_flg='1' and A.syounin_state='1'";
	$ws_SQL .= "  order by B.rank asc";
	$sql = sprintf($ws_SQL, $year,$month,$day,$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_today_work_therapist_data_wait_time_for_bot_common)";
		exit();
	}

	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//出勤情報の取得
function get_attendance_data_common($year,$month,$day,$today_flag,$under6_flag,$area) {

	$attendance_data = array();

	//出勤しているセラピストのidを取得
	$therapist_array = array();

	$most_start_time = 25;		//注意
	$most_end_time = 1;

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,A.id as attendance_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where B.delete_flg=0 and B.test_flg=0 and B.leave_flg=0 and A.year='%s' and A.month='%s' and A.day='%s'";
	$ws_SQL .= " and (B.area='%s' or B.kenmu like '%s') and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.syounin_state='1'";
	$ws_SQL .= " order by B.order_num desc";
	$sql = sprintf($ws_SQL, $year,$month,$day,$shop_area,$where_kenmu,$shop_area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;
		$attendance_id = $attendance_data[$i]["attendance_id"];
		$therapist_array[$i] = $attendance_data[$i]["therapist_id"];

		if($most_start_time > $attendance_data[$i]["start_time"]){
			$most_start_time = $attendance_data[$i]["start_time"];
		}

		if($most_end_time < $attendance_data[$i]["end_time"]){
			$most_end_time = $attendance_data[$i]["end_time"];
		}

		$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);
		$res2 = mysql_query($sql, DbCon);
		if($res2 == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}
		$j=0;
		while($row2 = mysql_fetch_assoc($res2)){

			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;

		}
		if($j==0) $attendance_data[$i]["time_num"]=0;

		$i++;
	}

	//移動時間もあるため、開始時間ぴったりの予約には対応できない
	$most_start_time = $most_start_time + 1;

	$therapist_array_num = count($therapist_array);

	//対応可能:1,対応不可:0

	$free_therapist_state = array(
	    "1" => 0,
		"2" => 0,
		"3" => 0,
		"4" => 0,
		"5" => 0,
		"6" => 0,
		"7" => 0,
		"8" => 0,
		"9" => 0,
		"10" => 0,
	    "11" => 0,
		"12" => 0,
		"13" => 0,
		"14" => 0,
		"15" => 0,
		"16" => 0,
		"17" => 0,
		"18" => 0,
		"19" => 0,
		"20" => 0,
		"21" => 0,
		"22" => 0,
		"23" => 0,
		"24" => 0,
		"25" => 0
	);

	for($i=1;$i<=25;$i++){

		if( ($under6_flag==true) && ($i<=12) && ($today_flag==true) ){

			$free_therapist_state[$i] = 0;

		}else if($i<$most_start_time){

			$free_therapist_state[$i] = 0;

		}else if($i>$most_end_time){

			$free_therapist_state[$i] = 0;

		}else if( ($today_flag == true) && (past_time($i)) ){

			$free_therapist_state[$i] = 0;

		}else{

			$kettei_flag=false;
			for($j=0;$j<$therapist_array_num;$j++){
				if($kettei_flag==false){
					$therapist_id = $therapist_array[$j];

					$ws_SQL = "select A.therapist_id,A.start_time,A.end_time,B.time from attendance_new A";
					$ws_SQL .= " left join reservation_new B on A.id=B.attendance_id";
					$ws_SQL .= " where A.publish_flg='1' and A.therapist_id='%s' and A.year='%s' and A.month='%s' and A.day='%s'";
					$sql = sprintf($ws_SQL, $therapist_id,$year,$month,$day);
					$res = mysql_query($sql, DbCon);
					if($res == false){
						$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_common:3)";
						header("Location: ".WWW_URL."error.php");
						exit();
					}

					$tmp_end_flag=false;

					while($row = mysql_fetch_assoc($res)){

						//echo $i;echo "<br />";

						if( $tmp_end_flag == false ){

							if($row["start_time"] > $i){

								$tmp_end_flag=true;

								//echo "111";echo "<br />";

							}else if($row["end_time"] < $i){

								$tmp_end_flag=true;

								//echo "222";echo "<br />";

							}else{

								//echo "333";echo "<br />";

								$tmp_start_time = $row["start_time"];
								$tmp_end_time = $row["end_time"]-1;
								$tmp_time = $row["time"];

								//移動時間があるので勤務開始時間ぴったりの予約には対応できない
								$tmp_start_time = $tmp_start_time + 1;

								//echo "tmp_time:".$tmp_time;echo "<br />";

								if( $tmp_time == $i ){

									//echo "444";echo "<br />";

									$free_therapist_state[$i] = 0;
									$tmp_end_flag=true;

								}else{

									//echo "555";echo "<br />";

									$for_end_flag = false;

									for( $k=$tmp_start_time; $k<=$tmp_end_time; $k++ ){

										if( $for_end_flag == false ){

											if( $k == $i ){

												$free_therapist_state[$i] = 1;
												$for_end_flag = true;
											}
										}
									}
								}
							}
						}
					}
					if($free_therapist_state[$i]==1){
						$kettei_flag=true;
					}
				}
			}
		}
	}

	$data = array();

	$data["attendance_data"] = $attendance_data;
	$data["free_therapist_state"] = $free_therapist_state;

	return $data;
	exit();
}

//出勤情報の取得4
function get_attendance_data_4_common($year,$month,$day,$today_flag,$under6_flag,$area,$customer_type,$shop_id,$access_type){

	$ws_whereArea = PHP_getStrAddAreaWhere($area);	//insert by aida at 20180322 追加エリア対応条件用文字列取得(横浜リフレ新規対応)

	$attendance_data = array();

	//出勤しているセラピストのidを取得
	$therapist_array = array();

	$most_start_time = 25;		//注意
	$most_end_time = 1;

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	$where_publish_flg = "";

	if( $customer_type != "1" ) $where_publish_flg = "A.publish_flg='1'";

	$ws_SQL = "B.delete_flg=0 and B.test_flg=0 and B.leave_flg=0 and B.therapist_type<2 and A.year='%s' and A.month='%s' and A.day='%s'";
	$ws_SQL .= " and (B.area%s or B.kenmu like '%s') and A.area%s and A.today_absence='0' and A.kekkin_flg='0' and A.syounin_state='1'";
	$where_sql = sprintf($ws_SQL, $year,$month,$day,$ws_whereArea,$where_kenmu,$ws_whereArea);		//update by aida at 20180320

	if( $where_publish_flg != "" ) $where_sql = $where_sql." and ".$where_publish_flg;

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,A.id as attendance_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " left join therapist_page C on A.therapist_id=C.therapist_id";
	$ws_SQL .= " where %s";
	$ws_SQL .= " GROUP BY name";	//1月にorder by order_num descに戻す
	$ws_SQL .= " order by case vip_order_key when '0' then 1 else 2 end, case vip_order_key when '1' then 1 else 2 end, order_value desc";
	$sql = sprintf($ws_SQL, $where_sql);

	$res = mysql_query($sql, DbCon);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_4_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;

		$therapist_id = $row["therapist_id"];
		$name_site = $row["name_site"];

		$img_url_m = get_img_url_m_by_therapist_id_common($therapist_id);	//セラピスト頁情報から携帯用の画像URL取得

		$attendance_data[$i]["img_url_m"] = $img_url_m;
		$attendance_data[$i]["name_site"] = $name_site;

		$attendance_id = $attendance_data[$i]["attendance_id"];
		$therapist_array[$i] = $attendance_data[$i]["therapist_id"];

		if($most_start_time > $attendance_data[$i]["start_time"]){
			$most_start_time = $attendance_data[$i]["start_time"];
		}

		if($most_end_time < $attendance_data[$i]["end_time"]){
			$most_end_time = $attendance_data[$i]["end_time"];
		}

		$sql = sprintf("
				select time from reservation_new where attendance_id='%s'",
				$attendance_id);

		$res2 = mysql_query($sql, DbCon);

		if($res2 == false){
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_4_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}

		$j=0;

		while($row2 = mysql_fetch_assoc($res2)){
			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;
		}

		if($j==0) $attendance_data[$i]["time_num"]=0;

		$i++;
	}

	//----フリーセラピスト状態配列取得
	$free_therapist_state = PHP_setFreeTherapistState_234(4, $most_start_time, $therapist_array, $year, $month, $day);

	$data = array();

	$data["attendance_data"] = $attendance_data;
	$data["free_therapist_state"] = $free_therapist_state;

	return $data;
	exit();
}

//出勤情報の取得5
function get_attendance_data_5_common($year,$month,$day,$today_flag,$under6_flag,$area,$customer_type,$shop_id,$access_type){

	$attendance_data = array();

	//出勤しているセラピストのidを取得
	$therapist_array = array();

	$most_start_time = 23;		//注意
	$most_end_time = 1;

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	$where_publish_flg = "";

	if( $customer_type != "1" ) $where_publish_flg = "A.publish_flg='1'";

	$ws_SQL = "B.delete_flg=0 and B.test_flg=0 and B.leave_flg=0 and B.therapist_type<2 and A.year='%s' and A.month='%s' and A.day='%s'";
	$ws_SQL .= " and (B.area='%s' or B.kenmu like '%s') and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.syounin_state='1'";
	$where_sql = sprintf($ws_SQL, $year,$month,$day,$shop_area,$where_kenmu,$shop_area);

	if( $where_publish_flg != "" ) $where_sql = $where_sql." and ".$where_publish_flg;

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,A.id as attendance_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where %s order by B.order_num desc";
	$sql = sprintf($ws_SQL, $where_sql);
	$res = mysql_query($sql, DbCon);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_5_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;

		$therapist_id = $row["therapist_id"];
		$name_site = $row["name_site"];

		$img_url_m = get_img_url_m_by_therapist_id_common($therapist_id);	//セラピスト頁情報から携帯用の画像URL取得

		$attendance_data[$i]["img_url_m"] = $img_url_m;
		$attendance_data[$i]["name_site"] = $name_site;

		$attendance_id = $attendance_data[$i]["attendance_id"];
		$therapist_array[$i] = $attendance_data[$i]["therapist_id"];

		if($most_start_time > $attendance_data[$i]["start_time"]){
			$most_start_time = $attendance_data[$i]["start_time"];
		}

		if($most_end_time < $attendance_data[$i]["end_time"]){
			$most_end_time = $attendance_data[$i]["end_time"];
		}

		$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);
		$res2 = mysql_query($sql, DbCon);
		if($res2 == false){
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_5_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}

		$j=0;

		while($row2 = mysql_fetch_assoc($res2)){

			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;
		}

		if($j==0) $attendance_data[$i]["time_num"]=0;

		$i++;
	}

	//----フリーセラピスト状態配列取得
	PHP_setFreeTherapistState_56($most_start_time, $therapist_array);

	$data = array();

	$data["attendance_data"] = $attendance_data;
	$data["free_therapist_state"] = $free_therapist_state;

	return $data;
	exit();
}

//出勤情報の取得6
function get_attendance_data_6_common($year, $month, $day, $today_flag, $under6_flag, $area, $customer_type){

	$attendance_data = array();

	//出勤しているセラピストのidを取得
	$therapist_array = array();

	$most_start_time = 23;		//注意
	$most_end_time = 1;

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	$where_publish_flg = "";

	if( $customer_type != "1" ) $where_publish_flg = "A.publish_flg='1'";

	$ws_SQL = "B.delete_flg=0 and B.test_flg=0 and B.leave_flg=0 and A.year='%s' and A.month='%s' and A.day='%s'";
	$ws_SQL .= " and (B.area='%s' or B.kenmu like '%s') and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.syounin_state='1'";
	$where_sql = sprintf($ws_SQL, $year, $month, $day, $shop_area, $where_kenmu, $shop_area);

	if( $where_publish_flg != "" ) $where_sql = $where_sql." and ".$where_publish_flg;

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,A.id as attendance_id,C.time from (attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " left join reservation_new C on A.id=C.attendance_id";
	$ws_SQL .= " where %s order by B.order_num desc";
	$sql = sprintf($ws_SQL, $where_sql);
	$res = mysql_query($sql, DbCon);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_6_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;

		$therapist_id = $row["therapist_id"];
		$name_site = $row["name_site"];

		$img_url_m = get_img_url_m_by_therapist_id_common($therapist_id);	//セラピスト頁情報から携帯用の画像URL取得

		$attendance_data[$i]["img_url_m"] = $img_url_m;
		$attendance_data[$i]["name_site"] = $name_site;

		$attendance_id = $attendance_data[$i]["attendance_id"];
		$therapist_array[$i] = $attendance_data[$i]["therapist_id"];

		if($most_start_time > $attendance_data[$i]["start_time"]){
			$most_start_time = $attendance_data[$i]["start_time"];
		}

		if($most_end_time < $attendance_data[$i]["end_time"]){
			$most_end_time = $attendance_data[$i]["end_time"];
		}

		$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);
		$res2 = mysql_query($sql, DbCon);
		if($res2 == false){
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_6_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}

		$j=0;

		while($row2 = mysql_fetch_assoc($res2)){
			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;
		}

		$j=0;

		while($row2 = mysql_fetch_assoc($res2)){

			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;

		}

		if($j==0) $attendance_data[$i]["time_num"]=0;

		$i++;

	}

	//----フリーセラピスト状態配列取得
	$free_therapist_state = PHP_setFreeTherapistState_56($most_start_time, $therapist_array);

	$data = array();

	$data["attendance_data"] = $attendance_data;
	$data["free_therapist_state"] = $free_therapist_state;

	return $data;
	exit();

}

//----セラピスト出勤データ取得
function get_attendance_data_for_vip_page_common($shop_area,$year,$month,$day,$time){

	$where_kenmu = "%".$shop_area."%";

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,A.id as attendance_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where (B.area='%s' or B.kenmu like '%s') and B.test_flg='0' and B.leave_flg='0'";
	$ws_SQL .= " and A.area='%s' and A.publish_flg='1' and A.today_absence='0' and A.kekkin_flg='0' and A.attendance_adjustment='0' and A.year='%s' and A.month='%s' and A.day='%s' and A.syounin_state='1'";
	$sql = sprintf($ws_SQL, $shop_area,$where_kenmu,$shop_area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_for_vip_page:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$attendance_data = array();

	$i=0;

	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;

		$attendance_id = $attendance_data[$i]["attendance_id"];

		$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);
		$res2 = mysql_query($sql, DbCon);

		if($res2 == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_for_vip_page:2)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$j=0;

		while( $row2 = mysql_fetch_assoc($res2) ){
			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;

		}
		if($j==0) $attendance_data[$i]["time_num"] = 0;

		$i++;
	}

	$attendance_data_num = count($attendance_data);

	$temp_data = array();

	$k = 0;

	$match_flag = false;

	for( $i=0; $i<$attendance_data_num; $i++ ){

		if( $attendance_data[$i]["time_num"] > 0 ){

			$time_num = $attendance_data[$i]["time_num"];

			$match_flag2 = false;

			for( $j=0; $j<$time_num; $j++ ){
				if( $attendance_data[$i]["time"][$j] == $time ) $match_flag2 = true;
			}

			$start_time = $attendance_data[$i]["start_time"];
			$end_time = $attendance_data[$i]["end_time"] - 1;

			//移動時間を考慮すると、ぴったりの開始時間での予約は不可
			$start_time = $start_time + 1;

			if( ( $time < $start_time ) || ( $time > $end_time ) ){
				$match_flag = true;
			}else{
				$match_flag = false;
			}

			if( ( $match_flag == true ) || ( $match_flag2 == true ) ){
			}else{
				$temp_data[$k] = $attendance_data[$i];
				$k++;
			}
		}else{
			$start_time = $attendance_data[$i]["start_time"];
			$end_time = $attendance_data[$i]["end_time"];

			//移動時間を考慮すると、ぴったりの開始時間での予約は不可
			$start_time = $start_time + 1;

			if( ( $time < $start_time ) || ( $time > $end_time ) ){
				$match_flag = true;
			}else{
				$match_flag = false;
			}

			if( $match_flag != true ){
				$temp_data[$k] = $attendance_data[$i];
				$k++;
			}
		}
	}

	return $temp_data;
	exit();
}
//----セラピスト出勤データ取得
function get_attendance_data_for_vip_page_2_common($shop_area,$year,$month,$day,$time,$customer_type){

	$where_kenmu = "%".$shop_area."%";

	$where_publish_flg = "";

	if( $customer_type != "1" ) $where_publish_flg = "A.publish_flg='1'";

	$ws_SQL = "(B.area='%s' or B.kenmu like '%s') and B.test_flg='0' and B.leave_flg='0'";
	$ws_SQL .= " and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.attendance_adjustment='0' and A.year='%s' and A.month='%s' and A.day='%s' and A.syounin_state='1'";
	$where_sql = sprintf($ws_SQL, $shop_area,$where_kenmu,$shop_area,$year,$month,$day);
	if( $where_publish_flg != "" ) $where_sql = $where_sql." and ".$where_publish_flg;

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,A.id as attendance_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where %s";
	$sql = sprintf($ws_SQL, $where_sql);
	$res = mysql_query($sql, DbCon);

	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_for_vip_page_2_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$attendance_data = array();

	$i=0;

	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;

		$attendance_id = $attendance_data[$i]["attendance_id"];

		$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);
		$res2 = mysql_query($sql, DbCon);
		if($res2 == false){

			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_for_vip_page_2_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();

		}

		$j=0;

		while( $row2 = mysql_fetch_assoc($res2) ){
			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;
		}
		if($j==0){
			$attendance_data[$i]["time_num"] = 0;
		}

		$i++;
	}

	$attendance_data_num = count($attendance_data);

	$temp_data = array();

	$k = 0;

	$match_flag = false;

	for( $i=0; $i<$attendance_data_num; $i++ ){

		if( $attendance_data[$i]["time_num"] > 0 ){

			$time_num = $attendance_data[$i]["time_num"];

			$match_flag2 = false;

			for( $j=0; $j<$time_num; $j++ ){

				if( $attendance_data[$i]["time"][$j] == $time ){
					$match_flag2 = true;
				}
			}

			$start_time = $attendance_data[$i]["start_time"];
			//$end_time = $attendance_data[$i]["end_time"] - 1;
			$end_time = $attendance_data[$i]["end_time"];

			//移動時間を考慮すると、ぴったりの開始時間での予約は不可
			$start_time = $start_time + 1;

			if( ( $time < $start_time ) || ( $time > $end_time ) ){
				$match_flag = true;
			}else{
				$match_flag = false;
			}

			if( ( $match_flag == true ) || ( $match_flag2 == true ) ){
			}else{
				$temp_data[$k] = $attendance_data[$i];
				$k++;
			}

		}else{

			$start_time = $attendance_data[$i]["start_time"];
			$end_time = $attendance_data[$i]["end_time"];

			//移動時間を考慮すると、ぴったりの開始時間での予約は不可
			$start_time = $start_time + 1;

			if( ( $time < $start_time ) || ( $time > $end_time ) ){
				$match_flag = true;
			}else{
				$match_flag = false;
			}

			if( $match_flag != true ){
				$temp_data[$k] = $attendance_data[$i];
				$k++;
			}
		}
	}

	return $temp_data;
	exit();
}


//----セラピスト出勤データ取得3
function get_attendance_data_for_vip_page_3_common($shop_area,$year,$month,$day,$time,$customer_type,$customer_id){

	$ws_whereArea = PHP_getStrAddAreaWhere($shop_area);	//insert by aida at 20180329 追加エリア対応条件用文字列取得(横浜リフレ新規対応)

	$where_kenmu = "%".$shop_area."%";

	$where_publish_flg = "";

	if( $customer_type != "1" ){

		$where_publish_flg = "attendance_new.publish_flg='1'";

	}

	$where_sql = sprintf("
			(therapist_new.area%s or therapist_new.kenmu like '%s') and
			therapist_new.test_flg='0' and
			therapist_new.leave_flg='0' and
			therapist_new.therapist_type<2 and
			attendance_new.area%s and
			attendance_new.today_absence='0' and
			attendance_new.kekkin_flg='0' and
			attendance_new.attendance_adjustment='0' and
			attendance_new.year='%s' and
			attendance_new.month='%s' and
			attendance_new.day='%s' and
			attendance_new.syounin_state='1'",
			$ws_whereArea,$where_kenmu,$ws_whereArea,$year,$month,$day);

	if( $where_publish_flg != "" ) $where_sql = $where_sql." and ".$where_publish_flg;

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,attendance_new.id as attendance_id from attendance_new";
	$ws_SQL .= " left join therapist_new on attendance_new.therapist_id=therapist_new.id";
	$ws_SQL .= " where %s";
	$sql = sprintf($ws_SQL, $where_sql);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql;
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_for_vip_page_2_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$attendance_data = array();

	$i=0;

	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		$result = check_exist_black_list_common($therapist_id,$customer_id);	//ブラックリストデータ有無

		if( $result == false ){

			$attendance_data[$i] = $row;

			$attendance_id = $attendance_data[$i]["attendance_id"];

			$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);
			$res2 = mysql_query($sql, DbCon);
			if($res2 == false){
				$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_for_vip_page_2_common:2)";
				header("Location: ".WWW_URL."error.php");
				exit();
			}

			$j=0;

			while( $row2 = mysql_fetch_assoc($res2) ){
				$attendance_data[$i]["time"][$j] = $row2["time"];
				$time_num = count($attendance_data[$i]["time"]);
				$attendance_data[$i]["time_num"] = $time_num;
				$j++;
			}
			if($j==0){
				$attendance_data[$i]["time_num"] = 0;
			}
			$i++;
		}
	}

	$attendance_data_num = count($attendance_data);

	$temp_data = array();

	$k = 0;

	$match_flag = false;

	for( $i=0; $i<$attendance_data_num; $i++ ){

		if( $attendance_data[$i]["time_num"] > 0 ){

			$time_num = $attendance_data[$i]["time_num"];

			$match_flag2 = false;

			for( $j=0; $j<$time_num; $j++ ){
				if( $attendance_data[$i]["time"][$j] == $time ){
					$match_flag2 = true;
				}
			}

			$start_time = $attendance_data[$i]["start_time"];
			//$end_time = $attendance_data[$i]["end_time"] - 1;
			$end_time = $attendance_data[$i]["end_time"];

			//移動時間を考慮すると、ぴったりの開始時間での予約は不可
			$start_time = $start_time + 1;

			if( ( $time < $start_time ) || ( $time > $end_time ) ){
				$match_flag = true;
			}else{
				$match_flag = false;
			}

			if( ( $match_flag == true ) || ( $match_flag2 == true ) ){
			}else{
				$temp_data[$k] = $attendance_data[$i];
				$k++;
			}
		}else{
			$start_time = $attendance_data[$i]["start_time"];
			$end_time = $attendance_data[$i]["end_time"];

			//移動時間を考慮すると、ぴったりの開始時間での予約は不可
			$start_time = $start_time + 1;

			if( ( $time < $start_time ) || ( $time > $end_time ) ){
				$match_flag = true;
			}else{
				$match_flag = false;
			}

			if( $match_flag != true ){
				$temp_data[$k] = $attendance_data[$i];
				$k++;
			}
		}
	}
	return $temp_data;
	exit();
}

function get_voice_list_front_common($paging_num,$display_num,$shop_type){

	$start_num = $display_num*($paging_num-1);

	$sql = sprintf("select * from voice where publish_flg='1' and publish_allow_site='1' and delete_flg=0 and shop_type='%s' order by created desc limit %s,%s",$shop_type,$start_num,$display_num);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_voice_list_front_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----顧客情報取得
function vip_login_check_common($tel){

	//shop_id:1→東京リフレ
	//shop_id:6→札幌リフレ
	//shop_id:7→横浜リフレ
	//shop_id:8→仙台リフレ
	//shop_id:9→福岡リフレ
	//shop_id:10→大阪リフレ

//	$sql = sprintf("		//update by aida at 2-180216
//select id,name,type from customer where
//delete_flag=0 and
//(shop_id=1 or shop_id=6 or shop_id=7 or shop_id=8 or shop_id=9 or shop_id=10) and
//(tel='%s' or tel_2='%s' or tel_3='%s' or tel_4='%s' or tel_5='%s' or tel_6='%s' or tel_7='%s' or tel_8='%s' or tel_9='%s' or tel_10='%s')",
	$ws_SQL = "select id,name,type from customer where delete_flag=0";
	$ws_SQL .= " and (tel='%s' or tel_2='%s' or tel_3='%s' or tel_4='%s' or tel_5='%s' or tel_6='%s' or tel_7='%s' or tel_8='%s' or tel_9='%s' or tel_10='%s')";
	$sql = sprintf($ws_SQL, $tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel);
	$res = mysql_query($sql, DbCon);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(vip_login_check_common)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}

	return $row;
	exit();
}

//----顧客情報取得
function vip_login_check_2_common($tel){

	//shop_id:1→東京リフレ
	//shop_id:6→札幌リフレ
	//shop_id:7→横浜リフレ
	//shop_id:8→仙台リフレ
	//shop_id:9→福岡リフレ
	//shop_id:10→大阪リフレ

	$ws_SQL = "select * from customer where delete_flag=0";
	$ws_SQL .= " and (shop_id=1 or shop_id=6 or shop_id=7 or shop_id=8 or shop_id=9 or shop_id=10 or shop_id=14)";
	$ws_SQL .= " and (tel='%s' or tel_2='%s' or tel_3='%s' or tel_4='%s' or tel_5='%s' or tel_6='%s' or tel_7='%s' or tel_8='%s' or tel_9='%s' or tel_10='%s')";
	$sql = sprintf($ws_SQL, $tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(vip_login_check_2_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}

	return $row;
	exit();
}

//----顧客情報取得
function vip_login_check_lymph_common($tel){

	//shop_id:4→リンパマッサージ東京

	$ws_SQL = "select id,name,type from customer where delete_flag=0 and shop_id='4'";
	$ws_SQL .= " and (tel='%s' or tel_2='%s' or tel_3='%s' or tel_4='%s' or tel_5='%s' or tel_6='%s' or tel_7='%s' or tel_8='%s' or tel_9='%s' or tel_10='%s')";
	$sql = sprintf($ws_SQL, $tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel);
	$res = mysql_query($sql, DbCon);

	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(vip_login_check_lymph_common)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){

		return false;
		exit();

	}

	return $row;
	exit();

}

//----顧客のメールアドレス取得
function mail_check_for_vip_page_common($customer_id,$shop_id,$mail){

	$sql = sprintf("select mail from customer where delete_flag=0 and customer_id<>'%s' and shop_id='%s'",$customer_id,$shop_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(mail_check_for_vip_page)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$match_num = 0;

	while($row = mysql_fetch_assoc($res)){
		if($mail == $row["mail"]){
			$match_num++;
		}
	}

	if($match_num > 0){
		return true;
		exit();
	}

	return false;
	exit();
}

//----顧客名取得
function get_customer_name_by_id_common($customer_id){

	$sql = sprintf("select name from customer where delete_flag=0 and customer_id='%s'",$customer_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_customer_name_by_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["name"];
	exit();
}

//----顧客名取得
function get_customer_name_by_tel_common($customer_tel){

	$customer_tel = str_replace(array('-', 'ー', '−', '―', '‐'), '', $customer_tel);
	$sql = sprintf("select name from customer where delete_flag=0 and tel='%s'",$customer_tel);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_customer_name_by_tel_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["name"];
	exit();
}

//----顧客データ有無
function check_exist_tel_customer_common($tel){

	if( $tel == "" ){
		return false;
		exit();
	}

	$sql = sprintf("select id from customer where delete_flag=0 and tel='%s'", $tel);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_tel_customer_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----顧客情報登録
function insert_customer_common($customer_name,$customer_id,$customer_tel,$address_1,$shop_id){

	$sql = sprintf('insert into customer(name,customer_id,
	tel,address_1,shop_id) values("%s","%s","%s","%s","%s")',$customer_name,$customer_id,$customer_tel,$address_1,$shop_id);
	$res = mysql_query($sql, DbCon);
	if($res === false){

		echo "error!(insert_customer_common)";
		exit();

	}

	return true;
	exit();

}

//----顧客データ有無
function check_exist_tel_customer_all_common($tel){

	if( $tel == "" ){
		return false;
		exit();
	}

	$sql = sprintf("select id from customer where delete_flag=0 and (tel='%s' or tel_2='%s' or tel_3='%s' or tel_4='%s' or tel_5='%s' or tel_6='%s' or tel_7='%s' or tel_8='%s' or tel_9='%s' or tel_10='%s')",$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel,$tel);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_tel_customer_all_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//セラピスト名取得
function get_therapist_name_by_therapist_id_common($therapist_id) {
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["name_site"];
}

//セラピスト名取得
function get_therapist_name_site_by_therapist_id_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["name_site"];
}

//セラピスト名(アロマ)取得
function get_therapist_name_by_therapist_id_aroma_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["name_aroma"];
}

//----エリア情報取得
function get_area_list_data_common($area){

	$sql = sprintf("select * from area_new where delete_flg=0 and type='%s'",$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_area_list_data_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----予約の開始終了時刻取得
function change_to_reservation_time_common($start_hour,$start_minute,$end_hour,$end_minute){

	$time_array = get_time_array_common();	//時刻配列取得

	$start_hour = hour_change_over_24_common($start_hour);
	$end_hour = hour_change_over_24_common($end_hour);

	$time_array_num = count($time_array);

	if( $start_minute >= 30 ){
		$start_minute = 30;
	}else{
		$start_minute = 0;
	}

	if( $end_minute == 0 ){
		$end_hour = $end_hour - 1;
		$end_minute = 30;
	}else if( $end_minute <= 30 ){
		$end_minute = 0;
	}else{
		$end_minute = 30;
	}

	$start_hour = hour_change_not_over_24_common($start_hour);
	$end_hour = hour_change_not_over_24_common($end_hour);

	$data = array();

	for( $i=1;$i<=$time_array_num;$i++ ){

		$tmp_hour = $time_array[$i]["hour"];
		$tmp_minute = $time_array[$i]["minute"];

		if( ($tmp_hour==$start_hour) && ($tmp_minute==$start_minute) ) $data["start_time"] = $i;

		if( ($tmp_hour==$end_hour) && ($tmp_minute==$end_minute) ) $data["end_time"] = $i;
	}

	return $data;
	exit();
}

//----最近の終了時刻取得
function get_before_latest_end_time_common($attendance_id,$time,$shop_area){

	//出勤のスタート時間

	$sql = sprintf("select start_time from attendance_new where id='%s' and area='%s'",$attendance_id,$shop_area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_before_latest_end_time_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$row = mysql_fetch_assoc($res);

	$att_start_time = $row["start_time"];

	if( $att_start_time == 1 ) $att_start_time = 0;

	//直近の予約タイム
	$sql = sprintf("select time from reservation_new where area='%s' and attendance_id='%s' and (time < %s) order by time desc",$shop_area,$attendance_id,$time);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_before_latest_end_time_common:2)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$latest_to_time = $row["time"];

	if($latest_to_time=="") $latest_to_time = 0;

	$latest_end_time = "";

	if($att_start_time <= $latest_to_time){
		$latest_end_time = $latest_to_time;
	}else{
		$latest_end_time = $att_start_time;
	}

	return $latest_end_time;
	exit();
}

//----直後の予約スタートタイム取得
function get_after_first_start_time_common($attendance_id,$time,$shop_area){

	//直後の予約スタートタイム

	$sql = sprintf("select time from reservation_new where area='%s' and attendance_id='%s' and (time > %s) order by time asc",$shop_area,$attendance_id,$time);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_after_first_start_time_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$after_start_time = $row["time"];

	if($after_start_time=="") $after_start_time = -1;

	return $after_start_time;
	exit();
}

//----トータル時間取得
function get_time_len_new_common($start_hour,$start_minute,$end_hour,$end_minute){

	$start_hour = hour_plus_24_common($start_hour);
	$end_hour = hour_plus_24_common($end_hour);

	$start_hour++;

	$hour_sa = $end_hour - $start_hour;

	$start_minute_sa = 60 - $start_minute;

	$minute_goukei = $start_minute_sa + $end_minute;

	$minute_num = intval( $minute_goukei / 5 );

	$hour_sa_num = $hour_sa * 12;

	$total_num = $hour_sa_num + $minute_num;

	return $total_num;
	exit();
}

//----予約状況データ取得
function get_reservation_for_board_data_by_id_common($id){
	return PHP_get_reservation_for_board_data_by_id_common($id, true);		//予約状況データ取得
}

//----予約状況データがあるかどうかチェック(ある：true,ない：false)
function check_reservation_for_board_exist_common($area,$year,$month){

	if( $area == "all" ){
		$sql = sprintf("select id from reservation_for_board where year='%s' and month='%s' and delete_flg='0'",$year,$month);
	}else{
		$sql = sprintf("select id from reservation_for_board where shop_area='%s' and year='%s' and month='%s' and delete_flg='0'",$area,$year,$month);
	}
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(check_reservation_for_board_exist_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----予約状況データ有無
function check_exist_reservation_for_board_data_by_therapist_id_common($therapist_id){

	$sql = sprintf("select A.id from reservation_for_board A left join attendance_new B on B.id=A.attendance_id where A.complete_flg='1' and A.delete_flg='0' and B.therapist_id='%s'",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_reservation_for_board_data_by_therapist_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----予約状況データ有無
function check_reservation_for_board_id_for_vip_common($reservation_for_board_id,$customer_id){

	if( ($reservation_for_board_id=="") || ($customer_id=="") ){
		return false;
		exit();
	}

	if( ($reservation_for_board_id=="-1") || ($customer_id=="-1") ){
		return false;
		exit();
	}

	$tmp = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得

	$customer_id_tmp = $tmp["customer_id"];

	//if( $customer_id != $customer_id_tmp ){
	//if( $customer_id != $customer_id_tmp && $customer_id_tmp != -1 ){	//update by aida at 20180319 予約状況データの顧客IDが-1の時は不問にする
	//	return false;
	//	exit();
	//}

	return true;
	exit();
}

//----移動費用取得
function get_movement_cost_by_id_common($id){

	$sql = sprintf("select * from movement_cost where delete_flg=0 and id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_movement_cost_by_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----移動費用取得
function get_movement_cost_by_id_2_common($id){

	$sql = sprintf("select * from movement_cost where id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_movement_cost_by_id_2_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----移動費用の削除更新
function delete_movement_cost_by_id_common($movement_cost_id){

	$sql = sprintf("update movement_cost set delete_flg='1' where id='%s'",$movement_cost_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(delete_movement_cost_by_id_common)";
		exit();
	}

	return true;
	exit();
}

//セラピスト名取得(本名)
function get_therapist_name_by_therapist_id_honmyou_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["name"];
}

//セラピスト名取得(本名)
function get_therapist_name_real_by_therapist_id_common($therapist_id){
	$name = get_therapist_name_by_therapist_id_honmyou_common($therapist_id);	//セラピスト名取得(本名)
	return $name;
}

//スタッフ名取得
function get_staff_name_by_id_common($id){
	$ws_data = get_staff_data_by_id_common($id);	//スタッフ情報取得
	return $ws_data["name"];
}

//店長かどうか
function get_staff_boss_flg_by_id_common($id){
	$ws_data = get_staff_data_by_id_common($id);	//スタッフ情報取得
	return $ws_data["boss_flg"];
}

//本部かどうか
function get_staff_type_by_id_common($id){
	$ws_data = get_staff_data_by_id_common($id);	//スタッフ情報取得
	return $ws_data["type"];
}

//----予約番号取得
function get_reservation_no_by_reservation_for_board_id_common($reservation_for_board_id){
	$ws_data = PHP_get_reservation_for_board_data_by_id_common($reservation_for_board_id, false);	//予約状況データ取得
	return $ws_data["reservation_no"];
}

//----料金取得(売上履歴データより)
function get_price_by_reservation_no_common($reservation_no){

	$sql = sprintf("select price from sale_history where delete_flg=0 and reservation_no='%s'",$reservation_no);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_price_by_reservation_no_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["price"];
	exit();
}

//----料金取得（超過料金含む）
function get_change_price_extension_common($shop_name,$extension_old,$extension_new,$price){

	$plus_flg = false;

	$extension_sa = 0;

	if( $extension_new > $extension_old ){
		$extension_sa = $extension_new - $extension_old;
		$plus_flg = true;
	}else{
		$extension_sa = $extension_old - $extension_new;
	}

	$change_value = 0;
	$shop_name = trim($shop_name);

	//----超過料金取得
	$change_value = PHP_getExtension_common($shop_name, $extension_sa);		//update by aida at 20180215

	if( $plus_flg == true ){
		$price = $price + $change_value;
	}else{
		$price = $price - $change_value;
	}

	if( $price < 0 ) $price = 0;

	return $price;
	exit();
}

//----料金取得
function get_change_price_course_common($shop_name,$course_var,$reservation_for_board_id,$discount_new){

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得

	$transportation = $data["transportation"];
	$shimei_flg = $data["shimei_flg"];
	$extension = $data["extension"];
	$shop_name = $data["shop_name"];

	$extension_price = get_extension_price_common($extension,$shop_name);	//超過料金取得

	$discount_value = get_discount_value_by_discount_common($discount_new,$shop_name);

	$kihon_price = get_kihon_price_common($shop_name,$course_var);

	$price = $kihon_price + $transportation + $extension_price;
	$price = $price - $discount_value;

	if( $shimei_flg == "1" ) $price = $price + 1000;

	if( $price < 0 ) $price = 0;

	return $price;
	exit();
}

//----料金取得
function get_price_by_reservation_for_board_id_common($reservation_for_board_id){

	$reservation_no = get_reservation_no_by_reservation_for_board_id_common($reservation_for_board_id);		//予約番号取得
	$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)

	return $price;
	exit();
}

//----割引額取得
function get_discount_by_reservation_for_board_id_common($reservation_for_board_id){
	$ws_data = PHP_get_reservation_for_board_data_by_id_common($reservation_for_board_id, true);	//予約状況データ取得
	return $ws_data["discount"];
}

//----交通費取得
function get_transportation_by_reservation_for_board_id_common($reservation_for_board_id){
	$ws_data = PHP_get_reservation_for_board_data_by_id_common($reservation_for_board_id, true);	//予約状況データ取得
	return $ws_data["transportation"];
}

//----割引額取得
function get_discount_value_by_discount_common($discount,$shop_name){

	$discount = trim($discount);
	$shop_name = trim($shop_name);

	$shop_id = get_shop_id_by_shop_name_common($shop_name);

	$discount_value = get_discount_value_by_shop_id_and_discount_common($shop_id,$discount);

	return $discount_value;
	exit();

}

//----基本料金取得
function get_kihon_price_common($shop_name,$course_var){

	$course_var = trim($course_var);

	$shop_id = get_shop_id_by_shop_name_common($shop_name);

	$kihon_price = get_kihon_price_by_shop_id_and_course_var_common($shop_id,$course_var);

	return $kihon_price;
	exit();
}

//----超過料金取得
function get_extension_price_common($extension,$shop_name){

	$extension = trim($extension);
	$shop_name = trim($shop_name);

	$price = 0;
	$extension_num = 0;

	//----超過料金取得
	$price = PHP_getExtension_common($shop_name, $extension);		//update by aida at 20180215

	return $price;
	exit();
}

//札幌リフレその他、割引が「なし」ではない場合、コースの変更に応じて割引も変化しなければいけない
function get_discount_new($discount,$shop_name,$course_var){

	$discount_new = get_discount_common($discount,$shop_name,$course_var);

	return $discount_new;
	exit();
}

//札幌リフレその他、割引が「なし」ではない場合、コースの変更に応じて割引も変化しなければいけない
function get_discount_common($discount,$shop_name,$course_var){

	$discount = trim($discount);
	$shop_name = trim($shop_name);
	$course_var = trim($course_var);

	$shop_id = get_shop_id_by_shop_name_common($shop_name);

	$course_int = get_course_int_by_shop_id_and_course_common($shop_id,$course_var);	//コースの時間取得

	$shop_discount_data = get_shop_discount_data_by_shop_id_and_discount_common($shop_id,$discount);	//割引情報取得

	$discount_course_int = $shop_discount_data["course_int"];
	$discount_type = $shop_discount_data["type"];

	if( $discount_course_int == "-1" ){

		$discount_new = $discount;

	}else{

		$discount_new = get_discount_name_by_shop_id_and_course_int_and_type_common($shop_id,$course_int,$discount_type);	//割引種別名取得

		if( $discount_new == "" ) $discount_new = $discount;
	}

	return $discount_new;
	exit();
}

//----セラピスト頁情報から年齢取得
function get_therapist_age_by_id_common($id){

	$sql = sprintf("select age from therapist_page where delete_flg=0 and therapist_id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_age_by_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["age"];
	exit();
}

//----セラピスト頁情報から画像URL取得
function get_img_url_by_therapist_id_common($therapist_id){

	$sql = sprintf("select img_url from therapist_page where delete_flg=0 and therapist_id='%s'",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_img_url_by_therapist_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row["img_url"];
	exit();
}

//----セラピスト頁情報から携帯用の画像URL取得
function get_img_url_m_by_therapist_id_common($therapist_id){

	$sql = sprintf("select img_url_m from therapist_page where delete_flg=0 and therapist_id='%s'",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_img_url_m_by_therapist_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["img_url_m"];
	exit();
}

//----セラピスト頁情報取得	※注意 by aida
function get_therapist_page_data_attendance_common($area,$year,$month,$day,$type){

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	$ws_SQL = "select A.* from therapist_page A";
	$ws_SQL .= " left join therapist_new B on B.id=A.therapist_id";
	$ws_SQL .= " where B.leave_flg=0 and B.test_flg=0 and B.delete_flg=0 and A.delete_flg=0 and (A.area='%s' or B.kenmu like '%s')";
	$ws_SQL .= " order by A.order_value desc";
	$sql = sprintf($ws_SQL, $shop_area,$where_kenmu);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_attendance_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		$area = $row["area"];

		$pr_content = "";

		//if( $area == "tokyo" ){
		//	$pr_content = $row["pr_refle"];
		//}else if( $area == "sapporo" ){
		//	$pr_content = $row["pr_sapporo"];
		//}else{
		//	$pr_content = $row["pr_new"];
		//}
		$ws_cellName = get_pr_name_common($area);		//update by aida at 20180220
		$pr_content = $row[$ws_cellName];

		//出勤のエリアを取得
		$att_area = get_attendance_area_common($therapist_id,$year,$month,$day);	//出勤データのエリア取得

		if( $shop_area == $att_area ){
			//本日出勤かどうかのチェック
			$result = therapist_attendance_check_common($therapist_id,$year,$month,$day);	//本日出勤かどうかのチェック
		}else{
			$result = false;
		}

		if( $type == "1" ){

			if( $result == true ){
				$skill = $row["skill"];
				$skill_data = explode(",",$skill);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$i++;
			}

		}else if( $type == "2" ){

			if( $result == false ){
				$skill = $row["skill"];
				$skill_data = explode(",",$skill);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$i++;
			}

		}else{
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_attendance_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}

	return $list_data;
	exit();
}

//----セラピスト頁情報取得(大阪)
function get_therapist_page_data_osaka_aroma_style_common(){

	$area = "osaka";

	$data = get_today_year_month_day_2_common();	//本日の年月日取得2

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$ws_SQL = "select A.* from therapist_page A";
	$ws_SQL .= " left join therapist_new B on B.id=A.therapist_id";
	$ws_SQL .= " where B.leave_flg=0 and B.test_flg=0 and B.delete_flg=0 and A.delete_flg=0 and A.area='%s'";
	$ws_SQL .= " order by A.order_value desc";
	$sql = sprintf($ws_SQL, $area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_osaka_aroma_style_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$skill = $row["skill"];
		$therapist_id = $row["therapist_id"];
		$skill_data = explode(",",$skill);
		$therapist_name = get_therapist_name_by_therapist_id_aroma_common($therapist_id);		//セラピスト名(アロマ)取得

		$attendance_flg = check_attendance_new_exist_by_therapist_id_common($year,$month,$day,$therapist_id);	//データがあるかどうかチェック(ある：true,ない：false)

		$list_data[$i] = $row;
		$list_data[$i]["skill_data"] = $skill_data;
		$list_data[$i]["therapist_name"] = $therapist_name;
		$list_data[$i]["attendance_flg"] = $attendance_flg;

		$i++;
	}

	return $list_data;
	exit();
}

//----セラピスト頁情報取得2
function get_therapist_page_data_attendance_2_common($area,$year,$month,$day,$type,$site_url,$access_type){

	//return array();exit();

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	$ws_SQL = "select A.* from therapist_page A";
	$ws_SQL .= " left join therapist_new B on B.id=A.therapist_id";
	$ws_SQL .= " where B.leave_flg=0 and B.test_flg=0 and B.delete_flg=0 and A.delete_flg=0 and (A.area='%s' or B.kenmu like '%s')";
	$ws_SQL .= " order by A.order_value desc";
	$sql = sprintf($ws_SQL, $shop_area,$where_kenmu);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_attendance_2_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		$area = $row["area"];

		$pr_content = "";

		//if( $area == "tokyo" ){
		//	$pr_content = $row["pr_refle"];
		//}else if( $area == "sapporo" ){
		//	$pr_content = $row["pr_sapporo"];
		//}else{
		//	$pr_content = $row["pr_new"];
		//}
		$ws_cellName = get_pr_name_common($area);		//update by aida at 20180220
		$pr_content = $row[$ws_cellName];

		//出勤のエリアを取得
		//これは2秒くらい
		//$att_area = get_attendance_area_common($therapist_id,$year,$month,$day);
		//以下でスピードアップ
		$att_area = get_attendance_area_2_common($therapist_id,$year,$month,$day);	//セラピスト出勤データ（小）のエリア取得

		if( $shop_area == $att_area ){

			//本日出勤かどうかのチェック
			//これに3秒かかってる
			//$result = therapist_attendance_check_common($therapist_id,$year,$month,$day);
			//以下でスピードアップ
			$result = therapist_attendance_check_2_common($therapist_id,$year,$month,$day);		//本日出勤かどうかのチェック2

		}else{

			$result = false;

		}

		if( $type == "1" ){

			if( $result == true ){

				//----受付状態HTML取得 in common/include/html_common.php
				$uketsuke_state_html = get_uketsuke_state_html_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area);

				$skill = $row["skill"];
				$skill_2 = $row["skill_2"];

				$skill_2_exist_flg = false;
				if( $skill_2 != "" ){
					$skill_2_exist_flg = true;
				}

				$skill_data = explode(",",$skill);
				$skill_2_data = explode(",",$skill_2);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["skill_2_data"] = $skill_2_data;
				$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$list_data[$i]["uketsuke_state_html"] = $uketsuke_state_html;
				$i++;

			}

		}else if( $type == "2" ){

			if( $result == false ){

				$skill = $row["skill"];
				$skill_2 = $row["skill_2"];

				$skill_2_exist_flg = false;
				if( $skill_2 != "" ){
					$skill_2_exist_flg = true;
				}

				$skill_data = explode(",",$skill);
				$skill_2_data = explode(",",$skill_2);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["skill_2_data"] = $skill_2_data;
				$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$i++;

			}

		}else if( $type == "3" ){

			if( $result == true ){

				$area = $area."_lp";

				//----受付状態HTML取得 in common/include/html_common.php
				$uketsuke_state_html = get_uketsuke_state_html_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area);

				$skill = $row["skill"];
				$skill_2 = $row["skill_2"];

				$skill_2_exist_flg = false;
				if( $skill_2 != "" ){
					$skill_2_exist_flg = true;
				}

				$skill_data = explode(",",$skill);
				$skill_2_data = explode(",",$skill_2);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["skill_2_data"] = $skill_2_data;
				$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$list_data[$i]["uketsuke_state_html"] = $uketsuke_state_html;
				$i++;

			}

		}else if( $type == "4" ){

			if( $result == true ){

				//----受付状態HTML取得2 in common/include/html_common.php
				$uketsuke_state_html = get_uketsuke_state_html_2_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area);

				$skill = $row["skill"];
				$skill_2 = $row["skill_2"];

				$skill_2_exist_flg = false;
				if( $skill_2 != "" ){
					$skill_2_exist_flg = true;
				}

				$skill_data = explode(",",$skill);
				$skill_2_data = explode(",",$skill_2);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["skill_2_data"] = $skill_2_data;
				$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$list_data[$i]["uketsuke_state_html"] = $uketsuke_state_html;
				$i++;

			}

		}else{
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_attendance_2_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}

	}

	return $list_data;
	exit();
}

//----セラピスト頁情報取得3 ※注意 by aida
function get_therapist_page_data_attendance_3_common($area,$year,$month,$day,$type,$site_url,$access_type,$attendance_all,$reservation_all){

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	$ws_SQL = "select A.* from therapist_page A";
	$ws_SQL .= " left join therapist_new B on B.id=A.therapist_id";
	$ws_SQL .= " where B.leave_flg=0 and B.test_flg=0 and B.delete_flg=0 and A.delete_flg=0 and (A.area='%s' or B.kenmu like '%s')";
	$ws_SQL .= " order by A.order_value desc";
	$sql = sprintf($ws_SQL, $shop_area,$where_kenmu);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_attendance_3_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		$area = $row["area"];

		$pr_content = "";

		//if( $area == "tokyo" ){
		//	$pr_content = $row["pr_refle"];
		//}else if( $area == "sapporo" ){
		//	$pr_content = $row["pr_sapporo"];
		//}else{
		//	$pr_content = $row["pr_new"];
		//}
		$ws_cellName = get_pr_name_common($area);		//update by aida at 20180220
		$pr_content = $row[$ws_cellName];

		//出勤のエリアを取得
		//これは2秒くらい
		//$att_area = get_attendance_area_common($therapist_id,$year,$month,$day);
		//以下でスピードアップ
		//$att_area = get_attendance_area_2_common($therapist_id,$year,$month,$day);
		//さらにスピードアップ
		$att_area = get_attendance_area_3_common($therapist_id,$year,$month,$day,$attendance_all);	//セラピスト出勤データ（小）のエリア取得3

		if( $shop_area == $att_area ){

			//本日出勤かどうかのチェック
			//これに3秒かかってる
			//$result = therapist_attendance_check_common($therapist_id,$year,$month,$day);
			//以下でスピードアップ
			//$result = therapist_attendance_check_2_common($therapist_id,$year,$month,$day);
			//さらにスピードアップ
			$result = therapist_attendance_check_3_common($therapist_id,$year,$month,$day,$attendance_all);		//本日出勤かどうかのチェック3

		}else{

			$result = false;

		}

		if( $type == "1" ){

			if( $result == true ){

/*
$uketsuke_state_html = get_uketsuke_state_html_3_common(
$therapist_id,$year,$month,$day,$site_url,$access_type,$area,$attendance_all,$reservation_all);
*/
				//----受付状態HTML取得4 in common/include/html_common.php
				$uketsuke_state_html = get_uketsuke_state_html_4_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area,$attendance_all,$reservation_all);

				$skill = $row["skill"];
				$skill_2 = $row["skill_2"];

				$skill_2_exist_flg = false;
				if( $skill_2 != "" ){
					$skill_2_exist_flg = true;
				}

				$skill_data = explode(",",$skill);
				$skill_2_data = explode(",",$skill_2);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["skill_2_data"] = $skill_2_data;
				$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$list_data[$i]["uketsuke_state_html"] = $uketsuke_state_html;
				$i++;

			}

		}else if( $type == "2" ){

			if( $result == false ){

				$skill = $row["skill"];
				$skill_2 = $row["skill_2"];

				$skill_2_exist_flg = false;
				if( $skill_2 != "" ){
					$skill_2_exist_flg = true;
				}

				$skill_data = explode(",",$skill);
				$skill_2_data = explode(",",$skill_2);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["skill_2_data"] = $skill_2_data;
				$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$i++;

			}

		}else if( $type == "3" ){

			if( $result == true ){

				$area = $area."_lp";

				//----受付状態HTML取得 in common/include/html_common.php
				$uketsuke_state_html = get_uketsuke_state_html_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area);

				$skill = $row["skill"];
				$skill_2 = $row["skill_2"];

				$skill_2_exist_flg = false;
				if( $skill_2 != "" ){
					$skill_2_exist_flg = true;
				}

				$skill_data = explode(",",$skill);
				$skill_2_data = explode(",",$skill_2);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["skill_2_data"] = $skill_2_data;
				$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$list_data[$i]["uketsuke_state_html"] = $uketsuke_state_html;
				$i++;

			}

		}else if( $type == "4" ){

			if( $result == true ){
				//----受付状態HTML取得2 in common/include/html_common.php
				$uketsuke_state_html = get_uketsuke_state_html_2_common($therapist_id,$year,$month,$day,$site_url,$access_type,$area);

				$skill = $row["skill"];
				$skill_2 = $row["skill_2"];

				$skill_2_exist_flg = false;
				if( $skill_2 != "" ){
					$skill_2_exist_flg = true;
				}

				$skill_data = explode(",",$skill);
				$skill_2_data = explode(",",$skill_2);
				$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
				$list_data[$i] = $row;
				$list_data[$i]["skill_data"] = $skill_data;
				$list_data[$i]["skill_2_data"] = $skill_2_data;
				$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
				$list_data[$i]["therapist_name"] = $therapist_name;
				$list_data[$i]["pr_content"] = $pr_content;
				$list_data[$i]["uketsuke_state_html"] = $uketsuke_state_html;
				$i++;
			}
		}else{
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_attendance_2_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}
	}

	return $list_data;
	exit();
}

//本日出勤かどうかのチェック
function therapist_attendance_check_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_new where therapist_id='%s' and year='%s' and month='%s' and day='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(therapist_attendance_check_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----セラピスト頁情報取得	※注意 by aida
function get_therapist_page_data_common($area){

	$ws_SQL = "select A.* from therapist_page A";
	$ws_SQL .= " left join therapist_new B on B.id=A.therapist_id";
	$ws_SQL .= " where B.leave_flg=0 and B.test_flg=0 and B.delete_flg=0 and A.delete_flg=0 and A.area='%s'";
	$ws_SQL .= " order by A.order_value desc";
	$sql = sprintf($ws_SQL, $area);
	$res = mysql_query($sql, DbCon);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_common)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$skill = $row["skill"];
		$skill_2 = $row["skill_2"];

		$skill_2_exist_flg = false;
		if( $skill_2 != "" ){
			$skill_2_exist_flg = true;
		}

		$therapist_id = $row["therapist_id"];
		$skill_data = explode(",",$skill);
		$skill_2_data = explode(",",$skill_2);
		$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得

		$pr_content = "";

		//if( $area == "tokyo" ){
		//	$pr_content = $row["pr_refle"];
		//}else if( $area == "sapporo" ){
		//	$pr_content = $row["pr_sapporo"];
		//}else{
		//	$pr_content = $row["pr_new"];
		//}
		$ws_cellName = get_pr_name_common($area);		//update by aida at 20180220
		$pr_content = $row[$ws_cellName];

		$list_data[$i] = $row;
		$list_data[$i]["skill_data"] = $skill_data;
		$list_data[$i]["skill_2_data"] = $skill_2_data;
		$list_data[$i]["skill_2_exist_flg"] = $skill_2_exist_flg;
		$list_data[$i]["therapist_name"] = $therapist_name;
		$list_data[$i]["pr_content"] = $pr_content;

		$i++;
	}

	return $list_data;
	exit();

}

//出席データの取得
function get_attendance_data_by_attendance_id_common($attendance_id){

	$ws_SQL = "select B.id as therapist_id, B.area as therapist_area,B.name_site,";
	$ws_SQL .= "A.area,A.start_time,A.end_time,A.year,A.month,A.day,A.week,A.today_absence,A.attendance_adjustment,A.charge_area,A.syounin_state,A.publish_flg,A.comment";
	$ws_SQL .= " from attendance_new A";
	$ws_SQL .= " left join therapist_new B on B.id=A.therapist_id";
	$ws_SQL .= " where A.id='%s'";
	$sql = sprintf($ws_SQL, $attendance_id);
	$res = mysql_query($sql, DbCon);

	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_by_attendance_id_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データID配列取得(セラピスト)
function get_day_attendance_id_common($year,$month,$day){

	$sql = sprintf("select id from attendance_new where year='%s' and month='%s' and day='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1'",$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_day_attendance_id_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row["id"];
	}

	return $list_data;
	exit();
}

//----出席データの取得(セラピスト)
function get_attendance_data_one_by_attendance_id_common($attendance_id){

	$sql = sprintf("select * from attendance_new where id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_data_one_by_attendance_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データ有無(セラピスト)
function check_effective_attendance_new_common($attendance_id){

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_effective_attendance_new_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		//common_error_log($sql);

		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----出勤データ有無(セラピスト)
function check_exist_attendance_new_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_attendance_new_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----出勤データの取得(スタッフ)
function get_attendance_staff_data_one_by_attendance_id_common($attendance_id){

	$sql = sprintf("select * from attendance_staff_new where id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_staff_data_one_by_attendance_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データ（スタッフ）
function get_staff_id_by_attendance_id_common($attendance_id){
	$ws_data = get_attendance_staff_data_one_by_attendance_id_common($attendance_id);		//出勤データの取得(スタッフ)
	return $ws_data["staff_id"];
}

//----出勤データ（スタッフ）取得
function get_attendance_staff_new_data_by_attendance_id_common($attendance_id){
	return get_attendance_staff_data_one_by_attendance_id_common($attendance_id);		//出勤データの取得(スタッフ)
}

//----出勤データ取得（スタッフ）
function get_staff_attendance_data_by_time_common($staff_id,$year,$month,$day){

	$sql = sprintf("select * from attendance_staff_new where staff_id='%s' and year='%s' and month='%s' and day='%s' and today_absence=0 and attendance_adjustment=0",$staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(get_staff_attendance_data_by_time_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}
//----出勤データ数取得（スタッフ）
function get_attendance_num_by_day_common($year,$month,$day){

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and year='%s' and month='%s' and day='%s'",$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(get_attendance_num_by_day_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----エリア別出勤データ数取得（スタッフ）
function get_attendance_num_by_day_and_area_common($year,$month,$day,$area){

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and year='%s' and month='%s' and day='%s' and area='%s'",$year,$month,$day,$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(get_attendance_num_by_day_and_area_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----出勤データIDからセラピストIDを取得
function get_therapist_id_by_attendance_id_common($attendance_id){
	$ws_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)
	return $ws_data["therapist_id"];
}

//----出勤データ削除（セラピスト）
function delete_therapist_attendance_data_by_id_common($attendance_id){

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, DbCon );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, DbCon );

	$sql = sprintf("delete from attendance_new where id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, DbCon );
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(delete_therapist_attendance_data_by_id_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	//コミット
	$sql = "commit";
	mysql_query( $sql, DbCon );

	//MySQL切断
	//mysql_close( DbCon );

	return true;
	exit();
}

//----アロマのコース取得
function get_course_int_for_aroma_style_common($course){

	$course = trim($course);

	$shop_id = "11";

	$course_int = get_course_int_by_shop_id_and_course_common($shop_id,$course);	//コースの時間取得

	return $course_int;
	exit();

}

//----出勤データ取得(シンプル)
function get_attendance_data_one_common($therapist_id,$year,$month,$day){
	return PHP_get_attendance_data_one_common("", $therapist_id, $year, $month, $day);		//出勤データ取得
}

//----出勤データ取得(承認済シンプル)
function get_attendance_data_work_common($therapist_id,$year,$month,$day){
	return PHP_get_attendance_data_one_common("syounin", $therapist_id, $year, $month, $day);		//出勤データ取得
}

//----出勤データのエリア取得
function get_att_area_common($therapist_id,$year,$month,$day){

	$ws_data = get_attendance_data_one_common($therapist_id,$year,$month,$day);		//出勤データ取得

	return $ws_data["area"];
}

//----シフトメッセージのエリア取得
function get_shift_message_area_common($therapist_id,$eigyou_year,$eigyou_month,$eigyou_day){

	$attendance_data = get_attendance_data_one_common($therapist_id,$eigyou_year,$eigyou_month,$eigyou_day);	//出勤データ取得

	$att_area = $attendance_data["area"];

	if( $att_area == "" ){
		$shift_message_area = get_therapist_area_by_therapist_id_common($therapist_id);		//セラピストのエリア取得
	}else{
		$shift_message_area = $att_area;
	}

	return $shift_message_area;
	exit();

}

//----セラピストのエリア取得
function get_therapist_area_by_therapist_id_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["area"];
}

//----スタッフのメールアドレス取得
function get_staff_mail_common($id){
	$ws_data = get_staff_data_by_id_common($id);	//スタッフ情報取得
	return $ws_data["mail"];
}

//----セラピストのメールアドレス取得
function get_therapist_mail_common($id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["mail"];
}

//----メールアドレス形式チェック
function mail_keishiki_check_common($mail){

	if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----店長のメールアドレス取得
function get_area_boss_mail_common($area){

	$sql = sprintf("select mail from staff_new_new where area='%s' and boss_flg=1",$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_area_boss_mail_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["mail"];
	exit();
}

//----スタッフデータ配列取得
function get_honbu_data_for_sale_common($year,$month,$day,$area){

	$type = "honbu";

	$sql = sprintf("select * from staff_new_new where delete_flg='0' and type='%s' and area='%s'",$type,$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_honbu_data_for_sale_common)";
		exit();
	}

	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$staff_id = $row["id"];

		$pay_hour = $row["pay_hour"];
		$pay_fix = $row["pay_fix"];

		if( !((($pay_hour=="0") || ($pay_hour=="-1")) && ($pay_fix=="0")) ){

			$result = check_staff_attendance_exist_board_common($staff_id, $year, $month, $day,$area);

			if( $result == true ){
				$list_data[$i] = $row;
				$list_data[$i]["area"] = $area;
				$i++;
			}
		}
	}

	return $list_data;
	exit();
}

//----指定エリアのスタッフのメールリスト文字列取得
function get_today_work_driver_mail_str_common($area){

	$eigyou_day_data = get_eigyou_day_common();	//営業年月日取得
	$year = $eigyou_day_data["year"];
	$month = $eigyou_day_data["month"];
	$day = $eigyou_day_data["day"];

	return PHP_get_today_work_driver_mail_str_common($area, $year,$month,$day);
}
//----指定エリアのスタッフのメールリスト文字列取得
function PHP_get_today_work_driver_mail_str_common($area, $year,$month,$day){

	$ws_SQL = "select B.mail from attendance_staff_new A";
	$ws_SQL .= " left join staff_new_new B on B.id=A.staff_id";
	$ws_SQL .= " where A.area='%s' and A.year='%s' and A.month='%s' and A.day='%s' and B.boss_flg=0";
	$sql = sprintf($ws_SQL, $area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_today_work_driver_mail_str)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$i=0;
	$mail_string = "";
	while($row = mysql_fetch_assoc($res)){

		$mail = $row["mail"];

		$result = mail_keishiki_check_common($mail);	//メールアドレス形式チェック

		if ( $result == true ){

			if( $i > 0 ){
				$mail_string .= ",".$mail;
			}else{
				$mail_string .= $mail;
			}

			$i++;
		}
	}

	return $mail_string;
}

//----BCCのメールリスト文字列取得
function get_bcc_add_shift_common($area){

	$mail_str = "";

	$driver_mail_str = get_today_work_driver_mail_str_common($area);	//指定エリアのスタッフのメールリスト文字列取得

	$area_boss_mail = get_area_boss_mail_common($area);		//店長のメールアドレス取得

	if( $area_boss_mail != "" ) $mail_str = $area_boss_mail;

	if( $driver_mail_str != "" ){
		if( $mail_str == "" ){
			$mail_str = $driver_mail_str;
		}else{
			$mail_str .= ",".$driver_mail_str;
		}
	}

	return $mail_str;
	exit();
}

//----BCCのメールリスト文字列取得
function PHP_get_bcc_add_shift_common($area, $year,$month,$day){

	$mail_str = "";

	$driver_mail_str = PHP_get_today_work_driver_mail_str_common($area, $year,$month,$day);	//指定エリアのスタッフのメールリスト文字列取得

	$area_boss_mail = get_area_boss_mail_common($area);		//店長のメールアドレス取得

	if( $area_boss_mail != "" ) $mail_str = $area_boss_mail;

	if( $driver_mail_str != "" ){
		if( $mail_str == "" ){
			$mail_str = $driver_mail_str;
		}else{
			$mail_str .= ",".$driver_mail_str;
		}
	}

	return $mail_str;
	exit();
}

//----BBSのURL取得
function get_bbs_common_url_common(){

	$domain = $_SERVER["SERVER_NAME"];

	$url_root = "http://".$domain."/";

	$data = $url_root."bbs/common/";

	return $data;
	exit();

}

//----シフト用URL取得
function get_check_url_shift_front_common($area,$therapist_id){

	$ch = get_therapist_for_kobetsu_url_common($therapist_id);	//個別URL用文字取得

	$domain = $_SERVER["SERVER_NAME"];
	$url_root = "http://".$domain."/";
	$check_url = sprintf("%sshift/index.php?area=%s&id=%s&ch=%s",$url_root,$area,$therapist_id,$ch);

	return $check_url;
	exit();
}

//----シフト頁URL取得
function get_therapist_shift_page_url_common($therapist_id,$file_name){

	$for_kobetsu_url = get_therapist_for_kobetsu_url_by_therapist_id_common($therapist_id);

	$area = get_therapist_area_by_therapist_id_common($therapist_id);

	$url = sprintf("%sshift/%s?area=%s&id=%s&ch=%s",REFLE_WWW_URL,$file_name,$area,$therapist_id,$for_kobetsu_url);

	return $url;
	exit();
}

//----個別URL用文字取得
function get_therapist_for_kobetsu_url_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["for_kobetsu_url"];
}

//----メールアドレス取得
function get_receive_user_mail_common($staff_id,$therapist_id){

	$mail = "";

	if( $staff_id != "-1" ){
		$staff_type = get_staff_type_by_id_common($staff_id);			//本部かどうか
		$staff_boss_flg = get_staff_boss_flg_by_id_common($staff_id);	//店長かどうか

		if( ($staff_type != "honbu") && ($staff_boss_flg != "1") ){
			$mail = get_staff_mail_common($staff_id);		//スタッフのメールアドレス取得
		}
	}else if( $therapist_id != "-1" ){
		$mail = get_therapist_mail_common($therapist_id);	//セラピストのメールアドレス取得
	}

	return $mail;
	exit();
}

//----出勤データを取得
function get_therapist_attendance_data_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select start_time,end_time from attendance_new where therapist_id='%s' and year='%s' and month='%s' and day='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_attendance_data_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----セラピスト情報取得
function get_therapist_data_by_id_common($id){

	$sql = sprintf("select * from therapist_new where id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_data_by_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----スタッフ名取得
function get_staff_name_by_staff_id_common($staff_id){
	$ws_data = get_staff_data_by_id_common($staff_id);	//スタッフ情報取得
	return $ws_data["name"];
}

//----勤怠開始時刻の取得
function get_attendance_start_time_common($attendance_id){
	$ws_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)
	return $ws_data["start_time"];
}

//----勤怠、開始及び終了時刻の取得
function get_attendance_time_common($attendance_id){
	$ws_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)
	$ws_ret["start_time"] = $ws_data["start_time"];
	$ws_ret["end_time"] = $ws_data["end_time"];
	return $ws_ret;
}

//----勤怠終了時刻の取得
function get_attendance_end_time_common($attendance_id){
	$ws_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)
	return $ws_data["end_time"];
}

//----スタッフのエリア取得
function get_area_by_staff_id_common($staff_id){
	$ws_data = get_staff_data_by_id_common($staff_id);	//スタッフ情報取得
	return $ws_data["area"];
}

//----セラピスト保険料取得
function get_therapist_insurance_by_id_common($id){
	$ws_data = get_therapist_data_by_id_common($id);	//セラピスト情報取得
	return $ws_data["insurance"];
}

//----予約番号から予約状況データ取得
function get_reservation_for_board_data_by_reservation_no_common($reservation_no){

	//直後の予約スタートタイム
	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and reservation_no='%s'",$reservation_no);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		return false;
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----シェア率取得
function get_share_rate_by_id_common($therapist_id){

	$therapist_data = get_therapist_data_by_id_common($therapist_id);		//セラピスト情報取得

	if( $therapist_data == false ){
		return false;
		exit();
	}

	$point_ref = $therapist_data["point_ref"];
	$point_fix = $therapist_data["point_fix"];
	$total_point = $therapist_data["total_point"];
	$area = $therapist_data["area"];

	if(($therapist_data["area"]!="tokyo_reraku")&&($therapist_data["area"]!="tokyo_bigao")){
		$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$area);	//シェア率取得 in common/include/shop_area_list.php
	} else {
		$share_rate = $therapist_data["share_rate"]*100;
	}

	return $share_rate;
	exit();
}

//----シェア率取得
function get_share_rate_by_attendance_data_common($attendance_data, $attendance_data_for_total_point){

	$attendance_id = $attendance_data["id"];

	if( $attendance_id == "" ){
		return "-1";
		exit();
	}

	$therapist_id = $attendance_data["therapist_id"];

	$therapist_data = get_therapist_data_by_id_common($therapist_id);

	//----update by aida at 20180305 from
	$share_rate = PHP_get_share_rate_by_attendance_data_common($attendance_data, $attendance_data_for_total_point, $therapist_data);

	/*
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];

	$point_ref = $therapist_data["point_ref"];
	$therapist_area = $therapist_data["area"];

	$point_fix = $therapist_data["point_fix"];
	$point_fix_start_time = $therapist_data["point_fix_start_time"];

	$share_rate = get_share_rate_by_point_fix_common($point_fix,$point_fix_start_time,$year,$month,$day);	//時期(2015年以降か)による固定ポイントチェック

	if( $share_rate == false ){

		//----update by aida at 20180305 from
		//$total_point = get_total_point_by_therapist_id_2_common($therapist_id,$year,$month,$day, $attendance_data_for_total_point);	//トータルポイント取得2
		if($attendance_data_for_total_point) {
			$total_point = get_total_point_by_therapist_id_2_common($therapist_id,$year,$month,$day, $attendance_data_for_total_point);	//トータルポイント取得2
		} else {
			$total_point = get_total_point_by_therapist_id_2_common($therapist_id,$year,$month,$day,$attendance_data_for_total_point);	//トータルポイント取得2
		}
		//----update by aida at 20180305 to

		$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$therapist_area);	//シェア率取得 in common/include/shop_area_list.php
	}

	if(($therapist_area=="tokyo_reraku")||($therapist_area=="tokyo_bigao")){	//注意 by aida
		$share_rate = $therapist_data["share_rate"]*100;
	}
	*/
	//----update by aida at 20180305 to

	return $share_rate;
	exit();
}


//----シェア率取得
function PHP_get_share_rate_by_attendance_data_common($attendance_data, $attendance_data_for_total_point, $therapist_data){

	$attendance_id = $attendance_data["id"];

	if( $attendance_id == "" ){
		return "-1";
		exit();
	}

	$therapist_id = $attendance_data["therapist_id"];
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];

	//$therapist_data = get_therapist_data_by_id_common($therapist_id);

	$point_ref = $therapist_data["point_ref"];
	$therapist_area = $therapist_data["area"];

	$point_fix = $therapist_data["point_fix"];
	$point_fix_start_time = $therapist_data["point_fix_start_time"];

	$share_rate = get_share_rate_by_point_fix_common($point_fix,$point_fix_start_time,$year,$month,$day);	//時期(2015年以降か)による固定ポイントチェック

	if( $share_rate == false ){

		//----update by aida at 20180305 from
		//$total_point = get_total_point_by_therapist_id_2_common($therapist_id,$year,$month,$day, $attendance_data_for_total_point);	//トータルポイント取得2
		if($attendance_data_for_total_point) {
			$total_point = get_total_point_by_therapist_id_2_common($therapist_id,$year,$month,$day, $attendance_data_for_total_point);	//トータルポイント取得2
		} else {
			$total_point = get_total_point_by_therapist_id_2_common($therapist_id,$year,$month,$day,$attendance_data_for_total_point);	//トータルポイント取得2
		}
		//----update by aida at 20180305 to

		$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$therapist_area);	//シェア率取得 in common/include/shop_area_list.php
		//if($therapist_id ==180) echo "line:" . __LINE__ . " " . $share_rate . " " . $point_ref . "," . $point_fix . "," . $total_point . "," . $therapist_area . "***<br />";
	}

	if(($therapist_area=="tokyo_reraku")||($therapist_area=="tokyo_bigao")){	//注意 by aida
		$share_rate = $therapist_data["share_rate"]*100;
	}

	return $share_rate;
	exit();
}

//----施術料取得
function get_price_shijutsu_day_common($year,$month,$day,$therapist_id){

	$therapist_data = get_therapist_data_by_id_common($therapist_id);

	if( $therapist_data == false ){
		return false;
		exit();
	}

	$point_ref = $therapist_data["point_ref"];
	$point_fix = $therapist_data["point_fix"];
	$total_point = $therapist_data["total_point"];
	$area = $therapist_data["area"];

	if(($area!="tokyo_reraku")&&($area!="tokyo_bigao")){
		$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$area);	//シェア率取得 in common/include/shop_area_list.php
	} else {
		$share_rate = $therapist_data["share_rate"]*100;
	}

	$sql = sprintf("select * from sale_history where delete_flg=0 and therapist_id='%s' and eigyou_year='%s' and eigyou_month='%s' and eigyou_day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		return false;
		exit();
	}

	$i=0;
	$price_shijutsu_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$reservation_no = $row["reservation_no"];
		$price = $row["price"];

		$reservation_data = get_reservation_for_board_data_by_reservation_no_common($reservation_no);

		if( $reservation_data == false ){
			return false;
			exit();
		}

		$shimei_flg = $reservation_data["shimei_flg"];
		$transportation = $reservation_data["transportation"];

		if( $shimei_flg == "1" ){
			$shimei_value = 1000;
		}else{
			$shimei_value = 0;
		}

		$price_shijutsu = $price - $shimei_value - $transportation;

		$price_shijutsu_all = $price_shijutsu_all + $price_shijutsu;
	}

	return $price_shijutsu_all;
	exit();
}

//----報酬合計取得
function get_remuneration_day_common($year,$month,$day,$therapist_id){

	$therapist_data = get_therapist_data_by_id_common($therapist_id);

	if( $therapist_data == false ){
		return false;
		exit();
	}

	$point_ref = $therapist_data["point_ref"];
	$point_fix = $therapist_data["point_fix"];
	$total_point = $therapist_data["total_point"];
	$area = $therapist_data["area"];

	if(($area!="tokyo_reraku")&&($area!="tokyo_bigao")){
		$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$area);	//シェア率取得 in common/include/shop_area_list.php
	} else {
		$share_rate = $therapist_data["share_rate"]*100;
	}

	$sql = sprintf("select * from sale_history where delete_flg=0 and therapist_id='%s' and eigyou_year='%s' and eigyou_month='%s' and eigyou_day='%s'", $therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		return false;
		exit();
	}

	$i=0;
	$remuneration_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$reservation_no = $row["reservation_no"];
		$price = $row["price"];

		$reservation_data = get_reservation_for_board_data_by_reservation_no_common($reservation_no);

		if( $reservation_data == false ){
			return false;
			exit();
		}

		$shimei_flg = $reservation_data["shimei_flg"];
		$transportation = $reservation_data["transportation"];

		if(($area!="tokyo_reraku")&&($area!="tokyo_bigao")){
			if( $shimei_flg == "1" ){
				$shimei_value = 1000;
			}else{
				$shimei_value = 0;
			}

			$price_shijutsu = $price - $shimei_value - $transportation;

			$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$area);	//シェア率取得 in common/include/shop_area_list.php
		} else {
			$price_shijutsu = $price - $transportation;
			if( $shimei_flg == "1" ){
				$share_rate = 60;
			}
		}

		if( $share_rate == "-1" ){
			$remuneration = 0;
		}else{
			$remuneration = $price_shijutsu * ($share_rate / 100);
		}

		$remuneration_all = $remuneration_all + $remuneration;

	}

	return $remuneration_all;
	exit();
}

//----指定日の指定セラピストの出勤ID取得
function get_attendance_id_by_time_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_new where therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_attendance_id_by_time_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();
}

//----獲得ポイント取得
function get_kakutoku_point_day_common($year,$month,$day,$attendance_id){

	$sql = sprintf("select shimei_flg from reservation_for_board where delete_flg=0 and year='%s' and month='%s' and day='%s' and attendance_id='%s'",$year,$month,$day,$attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		return false;
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	$list_data_num = count($list_data);

	$shijutsu_point = 0;
	$shimei_point = 0;

	for($i=0;$i<$list_data_num;$i++){

		$shimei_flg = $list_data[$i]["shimei_flg"];

		if( $shimei_flg == "1" ){
			$shimei_point = $shimei_point + 3;
		}

		$shijutsu_point++;
	}

	$repeat_point_data = get_repeat_point_data_by_attendance_id_common($attendance_id);		//リピータポイント取得
	$repeater_point = $repeat_point_data["value"];

	$kakutoku_point = $shijutsu_point + $shimei_point + $repeater_point;

	return $kakutoku_point;
	exit();
}

//----獲得ポイント取得
function get_kakutoku_point_by_attendance_id_common($attendance_id){

	$data = get_therapist_point_by_attendance_id_common($attendance_id);	//指定出勤データのポイント数取得

	$pt_repeat = $data["pt_repeat"];
	$pt_operation = $data["pt_operation"];
	$pt_shimei = $data["pt_shimei"];

	$kakutoku_point = $pt_repeat + $pt_operation + $pt_shimei;

	return $kakutoku_point;
	exit();
}

//----獲得ポイント取得2
function get_kakutoku_point_by_attendance_id_2_common($attendance_id,$attendance_data_for_total_point){

	//$data = get_therapist_point_by_attendance_id_common($attendance_id);		//指定出勤データのポイント数取得
	$data = get_therapist_point_by_attendance_id_2_common($attendance_id,$attendance_data_for_total_point);	//指定出勤データのポイント数取得2

	$pt_repeat = $data["pt_repeat"];
	$pt_operation = $data["pt_operation"];
	$pt_shimei = $data["pt_shimei"];

	$kakutoku_point = $pt_repeat + $pt_operation + $pt_shimei;

	return $kakutoku_point;
	exit();
}

//----報酬取得
function get_remuneration_value_common($share_rate,$price,$shimei_flg,$transportation){

	$price_shijutsu = get_price_shijutsu_value_common($price,$shimei_flg,$transportation);

	if( $share_rate == "-1" ){
		$remuneration = 0;
	}else{
		$remuneration = $price_shijutsu * ($share_rate / 100);
	}

	return $remuneration;
	exit();
}

//----施術料取得
function get_price_shijutsu_value_common($price,$shimei_flg,$transportation){

	if( $shimei_flg == "1" ){
		$shimei_value = 1000;
	}else{
		$shimei_value = 0;
	}

	$price_shijutsu = $price - $shimei_value - $transportation;

	return $price_shijutsu;
	exit();
}

//----指名ポイント取得
function get_point_value_common($shimei_flg){

	$point = 1;		//??? 指名が無くても？　by aida

	if( $shimei_flg == "1" ){
		$point = $point + 3;
	}

	return $point;
	exit();
}

//----指定出勤データのセラピスト名取得
function get_therapist_name_by_attendance_id_common($attendance_id){

	$sql = sprintf("select * from attendance_new A left join therapist_new B on B.id=A.therapist_id where A.id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		return false;
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["name"];
	exit();
}

//----料金再計算
function get_price_recalculation_common($reservation_for_board_id,$shop_name,$lost_cost,$course_var,$extension,$shimei_flg,$transportation){

	//基本料金＋延長料金＋交通費＋指名料ー割引料金ー売上損金

	$kihon_price = get_kihon_price_common($shop_name,$course_var);

	$extension_price = get_extension_price_common($extension,$shop_name);	//超過料金取得

	$shimei_price = 0;

	if( $shimei_flg == "1" ){

		$shimei_price = 1000;

	}

	$discount = get_discount_by_reservation_for_board_id_common($reservation_for_board_id);

	$discount_price = get_discount_value_by_discount_common($discount,$shop_name);

	$kihon_price = intval($kihon_price);
	$extension_price = intval($extension_price);
	$transportation = intval($transportation);
	$shimei_price = intval($shimei_price);
	$discount_price = intval($discount_price);
	$lost_cost = intval($lost_cost);

	$price = ( $kihon_price + $extension_price + $transportation + $shimei_price ) - $discount_price - $lost_cost;

	return $price;
	exit();
}

//----出勤データのエリア取得
function get_attendance_area_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select area from attendance_new where therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_area_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["area"];
	exit();
}

//----セラピスト頁情報取得
function get_therapist_page_data_by_therapist_id_common($therapist_id){

	$sql = sprintf("select * from therapist_page where therapist_id='%s' and delete_flg=0",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_page_data_by_therapist_id_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----報酬計算(一般)
function get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate){

	$remuneration = PHP_getRemuneration("", $price, $shimei_flg, $transportation, $share_rate, 0, 0, 0);	//報酬計算 in common/include/shop_area_list.php

	return $remuneration;
	exit();
}

//----報酬計算(東京リラク)
function get_remuneration_one_reraku_common($price,$shimei_flg,$transportation,$share_rate){

	$remuneration = PHP_getRemuneration("tokyo_reraku", $price, $shimei_flg, $transportation, $share_rate, 0, 0, 0);	//報酬計算 in common/include/shop_area_list.php

	return $remuneration;
	exit();
}

//----報酬計算(BIGAO)
function get_remuneration_one_bigao_common($price,$shimei_flg,$new_flg,$repeat_flg,$transportation,$share_rate,$attendance_id){

	$insentive = 0;
	$data = get_repeat_point_data_by_attendance_id_common($attendance_id);		//リピータポイント取得
	if($data["num"]>0) $insentive += 5000;

	$remuneration = PHP_getRemuneration("tokyo_bigao", $price, $shimei_flg, $transportation, $share_rate, $new_flg, $repeat_flg, $attendance_id);	//報酬計算 in common/include/shop_area_list.php

	return $remuneration;
	exit();
}

//----報酬取得
function get_therapist_remuneration_by_attendance_id_common($attendance_id){

	/*
	$data["remuneration"] = 0;
	$data["chief_allowance"] = 0;
	$data["lowest_guarantee"] = 0;
	$data["lowest_guarantee_flg"] = 0;
	return $data;exit();
	*/

	//$start_time = microtime(true);

	$chief_allowance = get_chief_allowance_by_attendance_id_common($attendance_id);

	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and complete_flg='1' and attendance_id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_therapist_remuneration_by_attendance_id_common)";
		exit();
	}

	$remuneration_all = 0;
	$incentive_shimei = 0;
	$incentive_repeater = 0;
	$shimei_cnt = 0;
	$repeater_cnt = 0;

	while($row = mysql_fetch_assoc($res)){

		$reservation_no = $row["reservation_no"];
		$shimei_flg = $row["shimei_flg"];
		$new_flg = $row["new_flg"];
		$repeat_flg = $row["repeat_flg"];
		$transportation = $row["transportation"];
		//---from get_therapist_remuneration_by_attendance_id_inc_insurance_price_commonには無い
		if($shimei_flg=="1"){
		    $shimei_cnt++;
		    $incentive_shimei+=1000;
		}
		//リピーター
		$data = get_repeat_point_data_by_attendance_id_common($attendance_id);		//リピータポイント取得
		if($data["num"]>0){
		    $repeater_cnt++;
		    $incentive_repeater+=5000;
		}
		//----to

		$year = $row["year"];
		$month = $row["month"];
		$day = $row["day"];

		$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

		if( $result != false ){

			//$reservation_noから、売上金額を取得
			$price = get_price_by_reservation_no_common($reservation_no);		//料金取得(売上履歴データより)

			//$attendance_idから$share_rateを取得
			$share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

			//以下の記述を関数化
			if($row["shop_area"]=="tokyo_reraku"){
				$remuneration = get_remuneration_one_reraku_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(東京リラク)
			} else if($row["shop_area"]=="tokyo_bigao"){
				$remuneration = get_remuneration_one_bigao_common($price,$shimei_flg,$new_flg,$repeat_flg,$transportation,$share_rate,$attendance_id);	//報酬計算(BIGAO)
			} else{
				$remuneration = get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(一般)
				//if($attendance_id == 78439 || $attendance_id == 78524) echo $remuneration . " = get_remuneration_one_common(" . $price . "-" . $shimei_flg . "-" . $transportation . "-" . $share_rate . ")<br />";
			}

			$remuneration_all = $remuneration_all + $remuneration;
		}
	}

	$lowest_guarantee = 0;

	$lowest_guarantee_flg = false;

	if( ($attendance_data["area"]=="tokyo") || ($attendance_data["area"]=="tokyo_reraku") || ($attendance_data["area"]=="tokyo_refresh") || ($attendance_data["area"]=="yokohama") || ($attendance_data["area"]=="yokohama2") || ($attendance_data["area"]=="yokohama2_refres") || ($attendance_data["area"]=="sendai") ) {    //仙台？    by aida
		$ws_limit_remuneration = 5000;
	} else {
		$ws_limit_remuneration = 3000;
	}

	if( $remuneration_all <= $ws_limit_remuneration ) {
		if(empty($share_rate)) $share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

		if($share_rate!=0) $lowest_guarantee = lowest_guarantee_common($attendance_id);

		if( $lowest_guarantee > 0 ) $lowest_guarantee_flg = true;

		$remuneration_all = $lowest_guarantee;
	}

	$remuneration_all += $chief_allowance;

	$data["remuneration"] = $remuneration_all;
	$data["chief_allowance"] = $chief_allowance;
	$data["lowest_guarantee"] = $lowest_guarantee;
	$data["lowest_guarantee_flg"] = $lowest_guarantee_flg;
	//---from get_therapist_remuneration_by_attendance_id_inc_insurance_price_commonには無い
	$data["share_rate"] = $share_rate;
	$data["shimei_cnt"] = $shimei_cnt;
	$data["repeater_cnt"] = $repeater_cnt;
	$data["incentive_shimei"] = $incentive_shimei;
	$data["incentive_repeater"] = $incentive_repeater;
	//----to

	return $data;
	exit();
}

//----報酬取得
function tp_rem_by_atd_id_common($attendance_id, $id_list){

	/*
	$data["remuneration"] = 0;
	$data["chief_allowance"] = 0;
	$data["lowest_guarantee"] = 0;
	$data["lowest_guarantee_flg"] = 0;
	return $data;exit();
	*/

	//$start_time = microtime(true);

	$chief_allowance = get_chief_allowance_by_attendance_id_common($attendance_id);

	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and complete_flg='1' and attendance_id in (%s)",$id_list);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		$sql = sprintf("select * from reservation_for_board where delete_flg=0 and complete_flg='1' and attendance_id='%s'",$attendance_id);
		$res = mysql_query($sql, DbCon);
		if( $res == false ){
			echo "error!(tp_rem_by_atd_id_common)";
			exit();
		}
	}

	$remuneration_all = 0;
	$incentive_shimei = 0;
	$incentive_repeater = 0;
	$shimei_cnt = 0;
	$repeater_cnt = 0;

	while($row = mysql_fetch_assoc($res)){
		$attendance_id = $row["attendance_id"];
		$reservation_no = $row["reservation_no"];
		$shimei_flg = $row["shimei_flg"];
		$new_flg = $row["new_flg"];
		$repeat_flg = $row["repeat_flg"];
		$transportation = $row["transportation"];
		//---from get_therapist_remuneration_by_attendance_id_inc_insurance_price_commonには無い
		if($shimei_flg=="1"){
		    $shimei_cnt++;
		    $incentive_shimei+=1000;
		}

		//リピーター
		$data = get_repeat_point_data_by_attendance_id_common($attendance_id);		//リピータポイント取得
		if($data["num"]>0){
		    $repeater_cnt++;
		    $incentive_repeater+=5000;
		}
		//----to

		$year = $row["year"];
		$month = $row["month"];
		$day = $row["day"];

		$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

		if( $result != false ){

			//$reservation_noから、売上金額を取得
			$price = get_price_by_reservation_no_common($reservation_no);		//料金取得(売上履歴データより)

			//$attendance_idから$share_rateを取得
			$share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得 遅い原因?

			//以下の記述を関数化
			if($row["shop_area"]=="tokyo_reraku"){
				$remuneration = get_remuneration_one_reraku_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(東京リラク)
			} else if($row["shop_area"]=="tokyo_bigao"){
				$remuneration = get_remuneration_one_bigao_common($price,$shimei_flg,$new_flg,$repeat_flg,$transportation,$share_rate,$attendance_id);	//報酬計算(BIGAO)
			} else{
				$remuneration = get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(一般)
				//if($attendance_id == 78439 || $attendance_id == 78524) echo $remuneration . " = get_remuneration_one_common(" . $price . "-" . $shimei_flg . "-" . $transportation . "-" . $share_rate . ")<br />";
			}

			$remuneration_all = $remuneration_all + $remuneration;
		}

	}

	$lowest_guarantee = 0;

	$lowest_guarantee_flg = false;

	if( ($attendance_data["area"]=="tokyo") || ($attendance_data["area"]=="tokyo_reraku") || ($attendance_data["area"]=="tokyo_refresh") || ($attendance_data["area"]=="yokohama") || ($attendance_data["area"]=="yokohama2") || ($attendance_data["area"]=="yokohama2_refres") || ($attendance_data["area"]=="sendai") ) {    //仙台？    by aida
		$ws_limit_remuneration = 5000;
	} else {
		$ws_limit_remuneration = 3000;
	}

	if( $remuneration_all <= $ws_limit_remuneration ) {
		if(empty($share_rate)) $share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

		if($share_rate!=0) $lowest_guarantee = lowest_guarantee_common($attendance_id);

		if( $lowest_guarantee > 0 ) $lowest_guarantee_flg = true;

		$remuneration_all = $lowest_guarantee;
	}

	$remuneration_all += $chief_allowance;

	$data["remuneration"] = $remuneration_all;
	$data["chief_allowance"] = $chief_allowance;
	$data["lowest_guarantee"] = $lowest_guarantee;
	$data["lowest_guarantee_flg"] = $lowest_guarantee_flg;
	//---from get_therapist_remuneration_by_attendance_id_inc_insurance_price_commonには無い
	$data["share_rate"] = $share_rate;
	$data["shimei_cnt"] = $shimei_cnt;
	$data["repeater_cnt"] = $repeater_cnt;
	$data["incentive_shimei"] = $incentive_shimei;
	$data["incentive_repeater"] = $incentive_repeater;
	//----to

	return $data;
	exit();
}

//----セラピスト報酬料等取得
function get_therapist_remuneration_by_attendance_id_inc_insurance_price_common($id_list){

	$chief_allowance = get_chief_allowance_by_attendance_id_common($id_list);

	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and complete_flg='1' and attendance_id in (%s)", $id_list);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_therapist_remuneration_by_attendance_id_inc_insurance_price_common)";
		exit();
	}

	$remuneration_all = 0;

	$attendance_id = $id_list;		//insert by aida at 20181026
	while($row = mysql_fetch_assoc($res)){

		//$attendance_id = $row["attendance_id"];		//delete by aida at 20181026
		$reservation_no = $row["reservation_no"];
		$shimei_flg = $row["shimei_flg"];
		$new_flg = $row["new_flg"];
		$repeat_flg = $row["repeat_flg"];
		$transportation = $row["transportation"];

		$year = $row["year"];
		$month = $row["month"];
		$day = $row["day"];

		$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

		if( $result != false ){

			//$reservation_noから、売上金額を取得
			$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)

			//$attendance_idから$share_rateを取得
			$share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

			//以下の記述を関数化
			if($row["shop_area"]=="tokyo_reraku"){
				$remuneration = get_remuneration_one_reraku_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(東京リラク)
			} else if($row["shop_area"]=="tokyo_bigao"){
				$remuneration = get_remuneration_one_bigao_common($price,$shimei_flg,$new_flg,$repeat_flg,$transportation,$share_rate,$attendance_id);	//報酬計算(BIGAO)
			} else{
				$remuneration = get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(一般)
			}

			$remuneration_all = $remuneration_all + $remuneration;
		}
	}

	$lowest_guarantee = 0;

	$lowest_guarantee_flg = false;

	if( ($row["shop_area"]=="tokyo") || ($row["shop_area"]=="yokohama") || ($row["shop_area"]=="sendai") ) {		//仙台？ by aida
		$ws_limit_remuneration = 5000;
	} else {
		$ws_limit_remuneration = 3000;
	}

	if( $remuneration_all <= $ws_limit_remuneration ) {

		if(empty($share_rate)) $share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

		if($share_rate!=0) $lowest_guarantee = lowest_guarantee_common($attendance_id);

		if( $lowest_guarantee > 0 ) $lowest_guarantee_flg = true;

		$remuneration_all = $lowest_guarantee;
	}

	$remuneration_all += $chief_allowance;

	if( $remuneration_all > 0 ){
		$insurance_price = get_insurance_price_by_attendance_id_common($attendance_id);
		$remuneration_all = $remuneration_all - $insurance_price;
	}

	$data["remuneration"] = $remuneration_all;
	$data["chief_allowance"] = $chief_allowance;
	$data["lowest_guarantee"] = $lowest_guarantee;
	$data["lowest_guarantee_flg"] = $lowest_guarantee_flg;

	return $data;
	exit();
}

//----セラピスト報酬料等取得
function tp_rem_by_atd_id_inc_insuranc_price_common($attendance_id){

	$chief_allowance = get_chief_allowance_by_attendance_id_common($attendance_id);

	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and complete_flg='1' and attendance_id='%s'", $attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_therapist_remuneration_by_attendance_id_inc_insurance_price_common)";
		exit();
	}

	$remuneration_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$reservation_no = $row["reservation_no"];
		$shimei_flg = $row["shimei_flg"];
		$new_flg = $row["new_flg"];
		$repeat_flg = $row["repeat_flg"];
		$transportation = $row["transportation"];

		$year = $row["year"];
		$month = $row["month"];
		$day = $row["day"];

		$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

		if( $result != false ){

			//$reservation_noから、売上金額を取得
			$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)

			//$attendance_idから$share_rateを取得
			$share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

			//以下の記述を関数化
			if($row["shop_area"]=="tokyo_reraku"){
				$remuneration = get_remuneration_one_reraku_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(東京リラク)
			} else if($row["shop_area"]=="tokyo_bigao"){
				$remuneration = get_remuneration_one_bigao_common($price,$shimei_flg,$new_flg,$repeat_flg,$transportation,$share_rate,$attendance_id);	//報酬計算(BIGAO)
			} else{
				$remuneration = get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(一般)
			}

			$remuneration_all = $remuneration_all + $remuneration;
		}
	}

	$lowest_guarantee = 0;

	$lowest_guarantee_flg = false;

	if( ($row["shop_area"]=="tokyo") || ($row["shop_area"]=="yokohama") || ($area=="sendai") ) {		//仙台？ by aida
		$ws_limit_remuneration = 5000;
	} else {
		$ws_limit_remuneration = 3000;
	}

	if( $remuneration_all <= $ws_limit_remuneration ) {

		if(empty($share_rate)) $share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

		if($share_rate!=0) $lowest_guarantee = lowest_guarantee_common($attendance_id);

		if( $lowest_guarantee > 0 ) $lowest_guarantee_flg = true;

		$remuneration_all = $lowest_guarantee;
	}

	$remuneration_all += $chief_allowance;

	if( $remuneration_all > 0 ){
		$insurance_price = get_insurance_price_by_attendance_id_common($attendance_id);
		$remuneration_all = $remuneration_all - $insurance_price;
	}

	$data["remuneration"] = $remuneration_all;
	$data["chief_allowance"] = $chief_allowance;
	$data["lowest_guarantee"] = $lowest_guarantee;
	$data["lowest_guarantee_flg"] = $lowest_guarantee_flg;

	return $data;
	exit();
}

//----セラピスト報酬料等取得
function get_therapist_remuneration_by_attendance_data_common($attendance_data,$attendance_data_for_total_point) {

	//echo "get_therapist_remuneration_by_attendance_data_common";exit();

	$attendance_id = $attendance_data["id"];

	//$start_time = microtime(true);

	//$chief_allowance = get_chief_allowance_by_attendance_id_common($attendance_id);
	$chief_allowance = get_chief_allowance_by_attendance_data_common($attendance_data);		//チーフ手当取得

	$sql = sprintf("select * from reservation_for_board where delete_flg=0 and complete_flg='1' and attendance_id='%s'", $attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_therapist_remuneration_by_attendance_id_common)";
		exit();
	}

	$remuneration_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$reservation_no = $row["reservation_no"];
		$shimei_flg = $row["shimei_flg"];
		$new_flg = $row["new_flg"];
		$repeat_flg = $row["repeat_flg"];
		$transportation = $row["transportation"];

		$year = $row["year"];
		$month = $row["month"];
		$day = $row["day"];

		$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

		if( $result != false ){

			//$reservation_noから、売上金額を取得
			$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)

			//$attendance_idから$share_rateを取得
			//$share_rate = get_share_rate_by_attendance_id_common($attendance_id);
			$share_rate = get_share_rate_by_attendance_data_common($attendance_data,$attendance_data_for_total_point);

			if($row["shop_area"]=="tokyo_reraku"){
				$remuneration = get_remuneration_one_reraku_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(東京リラク)
			} else if($row["shop_area"]=="tokyo_bigao"){
				$remuneration = get_remuneration_one_bigao_common($price,$shimei_flg,$new_flg,$repeat_flg,$transportation,$share_rate,$attendance_id);	//報酬計算(BIGAO)
			} else{
				$remuneration = get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(一般)
			}

			$remuneration_all = $remuneration_all + $remuneration;
		}

	}

	$lowest_guarantee = 0;

	$lowest_guarantee_flg = false;

	if( ($row["shop_area"]=="tokyo") || ($row["shop_area"]=="yokohama") ) {
		$ws_limit_remuneration = 5000;
	} else {
		$ws_limit_remuneration = 3000;
	}
	if( $remuneration_all <= $ws_limit_remuneration ) {

		if(empty($share_rate)) $share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

		if($share_rate!=0) $lowest_guarantee = lowest_guarantee_common($attendance_id);

		if( $lowest_guarantee > 0 ) $lowest_guarantee_flg = true;

		$remuneration_all = $lowest_guarantee;
	}

	$remuneration_all = $remuneration_all + $chief_allowance;
//*/
	$data["remuneration"] = $remuneration_all;
	$data["chief_allowance"] = $chief_allowance;
	$data["lowest_guarantee"] = $lowest_guarantee;
	$data["lowest_guarantee_flg"] = $lowest_guarantee_flg;

	return $data;
	exit();
}

//----リピータポイント取得
function get_repeat_point_data_by_attendance_id_common($attendance_id){

	$sql = sprintf("select * from repeater where delete_flg=0 and attendance_id='%s'", $attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_repeat_point_data_by_attendance_id_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	$data["value"] = $num * 5;
	$data["num"] = $num;

	return $data;
	exit();
}

//----シェア率取得
function get_share_rate_by_attendance_id_common($attendance_id){

	//$start_time = microtime(true);

	if( $attendance_id == "-1" ){
		return "-1";
		exit();
	}

	$attendance_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)

	$share_rate = get_share_rate_by_attendance_data_common($attendance_data, 0);	//シェア率取得 update by aida at 20180305

	/*
	//$therapist_id = $attendance_data["therapist_id"];
	//$year = $attendance_data["year"];
	//$month = $attendance_data["month"];
	//$day = $attendance_data["day"];

	//$therapist_data = get_therapist_data_by_id_common($therapist_id);

	//$point_ref = $therapist_data["point_ref"];
	//$therapist_area = $therapist_data["area"];

	//$point_fix = $therapist_data["point_fix"];
	//$point_fix_start_time = $therapist_data["point_fix_start_time"];

	//if($therapist_area == "tokyo_reraku"){
	//	//■東京リラク
	//	$share_rate = $therapist_data['share_rate']*100;
	//}
	//else if($therapist_area == "tokyo_bigao"){
	//	//■BIGAO
	//	$share_rate = $therapist_data['share_rate']*100;
	//}
	//else{
		//■その他
		//$share_rate = get_share_rate_by_point_fix_common($point_fix,$point_fix_start_time,$year,$month,$day);	//時期(2015年以降か)による固定ポイントチェック

		//if( $share_rate == false ){

			//$total_point = get_total_point_by_therapist_id_common($therapist_id,$year,$month,$day);		//トータルポイント取得

			//$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$therapist_area);	//シェア率取得 in common/include/shop_area_list.php

		//}
	//}
	*/
	return $share_rate;
	exit();
}

//----最低保証取得
function lowest_guarantee_common($attendance_id){

	$attendance_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)

	$area = $attendance_data["area"];
	$start_time = $attendance_data["start_time"];
	$end_time = $attendance_data["end_time"];
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];

	$lowest_guarantee = $attendance_data["lowest_guarantee"];

	$jitaku_taiki_flg = $attendance_data["jitaku_taiki_flg"];

	if( $lowest_guarantee == "-1" ){

		$lowest_guarantee = 0;

		if( $jitaku_taiki_flg != "1" ){

			//前日チェック
			$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

			if( $result == false ){

				return $lowest_guarantee;
				exit();

			}

			$lowest_guarantee = get_lowest_guarantee_common($area,$start_time,$end_time);	//最低保証取得 in shop_area_list.php

		}

	}

	return $lowest_guarantee;
	exit();

}

//----指定エリアの出勤データ配列取得(承認済)
function get_attendance_data_work_by_area_common($area,$year,$month,$day){

	$sql = sprintf("select * from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and area='%s' and year='%s' and month='%s' and day='%s'",$area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_work_by_area_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		//休職、退職したセラピストのデータも対象
		//$result = check_therapist_exist_by_id_common($therapist_id);
		$result = true;

		if( $result == true ){
			$list_data[$i++] = $row;
		}
	}

	return $list_data;
	exit();
}

//----予約IDから出勤データID取得
function get_attendance_id_by_reservation_for_board_id_common($reservation_for_board_id){
	$ws_data = PHP_get_reservation_for_board_data_by_id_common($id, false);		//予約状況データ取得
	return $ws_data["attendance_id"];
}

//----出勤データ取得（スタッフ）
function get_attendance_id_work_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_id_work_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();
}

//予約データがあるかどうか
// 2018/11/13 murase update from
function check_reservation_for_board_exist_by_attendance_id_common($attendance_id){
	$ws_data = PHP_get_reservation_for_board_data_by_id_common($attendance_id, true);		//予約状況データ取得
	if( $ws_data["id"] == "" ){
		return false;
	}else{
		return true;
	}
}
// 2018/11/13 murase update to

//----交通費取得
function get_allowance_therapist_common($year,$month,$day,$attendance_id){

	$sql = sprintf("select transportation from reservation_for_board where delete_flg=0 and year='%s' and month='%s' and day='%s' and attendance_id='%s'", $year,$month,$day,$attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_allowance_therapist_common)";
		exit();
	}

	$allowance = 0;

	while($row = mysql_fetch_assoc($res)){

		$transportation = $row["transportation"];

		if( $transportation == "0" ){
			$allowance = $allowance + 500;
		}else{
			$allowance = $allowance + $transportation;
		}
	}

	return $allowance;
	exit();
}

//----シェア率取得
function get_share_rate_by_therapist_id_and_time_common($therapist_id,$year,$month,$day){

	$total_point = get_total_point_by_therapist_id_common($therapist_id,$year,$month,$day);	//トータルポイント取得

	$therapist_data = get_therapist_data_by_id_common($therapist_id);

	$point_ref = $therapist_data["point_ref"];
	$point_fix = $therapist_data["point_fix"];
	$therapist_area = $therapist_data["area"];

	$share_rate = get_share_rate_common($point_ref,$point_fix,$total_point,$therapist_area);	//シェア率取得 in common/include/shop_area_list.php

	return $share_rate;
	exit();

}

//----シェア率取得
function get_share_rate_by_therapist_id_and_now_common($therapist_id){

	$ws_date = PHP_get_today_ymd_common(1);		//本日の年月日取得
	$year = $ws_date["year"];
	$month = $ws_date["month"];
	$day = $ws_date["day"];

	$share_rate = get_share_rate_by_therapist_id_and_time_common($therapist_id,$year,$month,$day);		//シェア率取得

	return $share_rate;
	exit();
}

//データがあるかどうかチェック(ある：true,ない：false)
function check_attendance_new_exist_common($area,$year,$month){

	$sql = sprintf("select id from attendance_new where area='%s' and year='%s' and month='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1'", $area,$year,$month);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(check_attendance_new_exist_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//データがあるかどうかチェック(ある：true,ない：false)
function check_attendance_staff_new_exist_common($area,$year,$month){

	$type = "driver";

	$sql = sprintf("select id from attendance_staff_new where type='%s' and area='%s' and year='%s' and month='%s'",$type,$area,$year,$month);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(check_attendance_staff_new_exist_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----出勤データがあるかどうかチェック(ある：true,ない：false)
function check_attendance_staff_new_exist_honbu_common($area,$year,$month){

	$type = "honbu";

	$sql = sprintf("select id from attendance_staff_new where type='%s' and area='%s' and year='%s' and month='%s'",$type,$area,$year,$month);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(check_attendance_staff_new_exist_honbu_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----最小年月取得
function get_most_small_year_and_month_common($area,$type){

	$min_year = "2014";

	if( $type == "therapist" ){
		$sql = sprintf("select year from attendance_new where year>%s and area='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1' order by year asc",$min_year,$area);
	}else if( ($type == "driver") || ($type == "honbu") ){
		$sql = sprintf("select year from attendance_staff_new where type='%s' and year>%s and area='%s' order by year asc", $type,$min_year,$area);
	}else if( $type == "shop" ){
		if( $area == "all" ){
			$sql = sprintf("select year from reservation_for_board where year>%s and delete_flg='0' order by year asc",$min_year);
		}else{
			$sql = sprintf("select year from reservation_for_board where year>%s and shop_area='%s' and delete_flg='0' order by year asc",$min_year,$area);
		}
	}else{
		echo "error!(get_most_small_year_and_month_common)";
		exit();
	}
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql;
	if($res == false){
		echo "クエリー実行で失敗しました(get_most_small_year_and_month_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	$year = $row["year"];

	if( $type == "therapist" ){
		$sql = sprintf("select month from attendance_new where area='%s' and year='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1' order by month asc",$area,$year);
	}else if( ($type == "driver") || ($type == "honbu") ){
		$sql = sprintf("select month from attendance_staff_new where type='%s' and area='%s' and year='%s' order by month asc",$type,$area,$year);
	}else if( $type == "shop" ){
		if( $area == "all" ){
			$sql = sprintf("select month from reservation_for_board where year='%s' and delete_flg='0' order by month asc", $year);
		}else{
			$sql = sprintf("select month from reservation_for_board where shop_area='%s' and year='%s' and delete_flg='0' order by month asc", $area,$year);
		}
	}else{
		echo "error!(get_most_small_year_and_month_common)";
		exit();
	}
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(get_most_small_year_and_month_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	$month = $row["month"];

	$data["year"] = $year;
	$data["month"] = $month;

	return $data;
	exit();
}

//----店舗休日情報取得
function get_shop_holiday_data_common($type){

	$sql = sprintf("select * from shop_holiday where name='%s'",$type);
	$res = mysql_query($sql, DbCon);

	if( $res == false ){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_shop_holiday_data_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----セラピストのトータルポイント取得
function get_therapist_total_point_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["total_point"];
}

//データがあるかどうかチェック(ある：true,ない：false)
function check_attendance_new_exist_by_day_common($year,$month,$day){

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and year='%s' and month='%s' and day='%s'", $year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(check_attendance_new_exist_by_day_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----予約データ配列取得
function get_reservation_for_board_data_by_day_common($year,$month,$day){

	$sql = sprintf("select * from reservation_for_board where delete_flg='0' and complete_flg='1' and year='%s' and month='%s' and day='%s'",$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_for_board_data_by_day_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----予約データ数取得(年月日単位)
function get_reservation_for_board_num_by_shop_name_common($year,$month,$day,$shop_name){

	if($day == 0) {
		$sql = sprintf("select id from reservation_for_board where delete_flg='0' and complete_flg='1' and year='%s' and month='%s' and shop_name='%s'",$year,$month,$shop_name);
	} else {
		$sql = sprintf("select id from reservation_for_board where delete_flg='0' and complete_flg='1' and year='%s' and month='%s' and day='%s' and shop_name='%s'",$year,$month,$day,$shop_name);
	}
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(get_reservation_for_board_num_by_shop_name_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----予約データ数取得(年月単位)
function get_reservation_for_board_num_month_by_shop_name_common($year,$month,$shop_name){
	return get_reservation_for_board_num_by_shop_name_common($year,$month,0,$shop_name);	//予約データ数取得
}

//----予約データ数取得(年月日単位)
function get_reservation_for_board_num_by_day_common($year,$month,$day){

	$sql = sprintf("select id from reservation_for_board where delete_flg='0' and year='%s' and month='%s' and day='%s'",$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(get_reservation_for_board_num_by_day_common)";
		exit();
	}
	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----エリア別予約データ数取得(年月日単位)
function get_reservation_for_board_num_by_shop_area_common($year,$month,$day,$shop_area){

	$sql = sprintf("select id from reservation_for_board where delete_flg='0' and complete_flg='1' and year='%s' and month='%s' and day='%s' and shop_area='%s'",$year,$month,$day,$shop_area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "クエリー実行で失敗しました(get_reservation_for_board_num_by_shop_area_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----予約データ数取得（出勤データ別）
function get_reservation_for_board_data_num_by_attendance_id_common($attendance_id){

	$sql = sprintf("select * from reservation_for_board where delete_flg='0' and complete_flg='1' and attendance_id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_for_board_data_num_by_attendance_id_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----予約データ数取得（出勤データ別）
function get_reservation_for_board_num_by_attendance_id_common($attendance_id){
	return get_reservation_for_board_data_num_by_attendance_id_common($attendance_id);	//予約データ数取得（出勤データ別）
}

//----予約データ数取得（店舗別種別別）
function get_reservation_for_board_num_by_shop_name_and_type_common($year,$month,$day,$shop_name,$type){

	if( $type == "new" ){
		$sql = sprintf("select id from reservation_for_board where delete_flg='0' and complete_flg='1' and new_flg='1' and year='%s' and month='%s' and day='%s' and shop_name='%s'",$year,$month,$day,$shop_name);
	}else if( $type == "repeat" ){
		$sql = sprintf("select id from reservation_for_board where delete_flg='0' and complete_flg='1' and repeat_flg='1' and year='%s' and month='%s' and day='%s' and shop_name='%s'",$year,$month,$day,$shop_name);
	}else{
		echo "error!(get_reservation_for_board_num_by_shop_name_and_type_common)";
		exit();
	}
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_for_board_num_by_shop_name_and_type_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//order_value並べ替えの処理(カタカナ)
function order_value_sort_kana_common($data,$hairetsu_name){

	foreach ($data as $key => $val){

		$kana = mb_convert_kana($val[$hairetsu_name], "c", "UTF-8");

		$order_value[$key] = $kana;
		//$order_value[$key] = $val[$hairetsu_name];

	}
	array_multisort($order_value, SORT_ASC, $data);

	return $data;
	exit();

}

//----出勤データ配列取得（セラピスト）
function get_attendance_data_day_common($year,$month,$day){

	$sql = sprintf("select * from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and year='%s' and month='%s' and day='%s'",$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_day_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----振込額取得
function get_furikomi_price_by_week_data_common($week_data,$therapist_id){

	$furikomi_price = 0;

	$therapist_data = get_therapist_data_by_id_common($therapist_id);
	$jisou_flg = $therapist_data["jisou_flg"];
	//$insurance = $therapist_data["insurance"];

	$week_data_num = count($week_data);

	for( $i=0; $i<$week_data_num; $i++ ){

		$year = $week_data[$i]["year"];
		$month = $week_data[$i]["month"];
		$day = $week_data[$i]["day"];

		if( $year > 2017 ){

			$attendance_data = get_attendance_data_work_common($therapist_id,$year,$month,$day);

			$attendance_id = $attendance_data["id"];
			$pay_day = $attendance_data["pay_day"];

			$pay_another = $attendance_data["pay_another"];
			$pay_finish = $attendance_data["pay_finish"];

			$allowance = $attendance_data["allowance"];

			$allowance_jisou = 0;

			if( $attendance_id != "" ){

				$insurance = get_insurance_common($therapist_id,$year,$month,$day);		//設定保険料データより設定値取得

				//echo $insurance;exit();

				$insurance_price = 0;

				if( $insurance == "2" ){
					$board_num = get_reservation_for_board_num_by_attendance_id_common($attendance_id);
					$insurance_price = 50 * $board_num;
				}

				if( $jisou_flg == "1" ){

					$allowance_jisou = get_allowance_therapist_common($year,$month,$day,$attendance_id);	//交通費取得

				}

				$allowance = $allowance + $allowance_jisou;

				$remuneration_data = get_therapist_remuneration_by_attendance_id_common($attendance_id);	//報酬取得
				$remuneration = $remuneration_data["remuneration"];

				$furikomi_price = $furikomi_price + $remuneration - $pay_day + $allowance + $pay_another - $pay_finish;

				$furikomi_price = $furikomi_price - $insurance_price;

				/*
				echo "remuneration:".$remuneration;echo "<br />";
				echo "pay_day:".$pay_day;echo "<br />";
				echo "allowance:".$allowance;echo "<br />";
				echo "pay_another:".$pay_another;echo "<br />";
				echo "pay_finish:".$pay_finish;echo "<br />";
				*/

			}

		}

	}

	if( $furikomi_price < 0 ){

		$furikomi_price = 0;

	}

	return $furikomi_price;
	exit();

}

//----振込手数料取得
function get_furikomi_commission_by_furikomi_type_common($furikomi_type,$price){

	$furikomi_commission = "-1";

	if( $price == "0" ){
		$furikomi_commission = "-1";
	}else if( $furikomi_type == "1" ){
		$furikomi_commission = "54";
	}else if( $furikomi_type == "2" ){
		if( $price >= 30000 ){
			$furikomi_commission = "270";
		}else{
			$furikomi_commission = "172";
		}
	}else if( $furikomi_type == "3" ){
		$furikomi_commission = "0";
	}else if( $furikomi_type == "4" ){
		if( $price >= 50000 ){
			$furikomi_commission = "432";
		}else{
			$furikomi_commission = "216";
		}
	}

	return $furikomi_commission;
	exit();
}

//----振込金額集計
function get_furikomi_price_staff_by_week_data_common($week_data,$staff_id){

	$week_data_num = count($week_data);

	$furikomi_price_all = 0;

	for( $i=0; $i<$week_data_num; $i++ ){

		$year = $week_data[$i]["year"];
		$month = $week_data[$i]["month"];
		$day = $week_data[$i]["day"];

		$data = get_furikomi_price_driver_by_day_2_common($staff_id,$year,$month,$day);

		$furikomi_price = $data["furikomi_price"];

		$furikomi_price_all = $furikomi_price_all + $furikomi_price;

	}

	return $furikomi_price_all;
	exit();
}

//----同一エリアのドライバー情報配列取得
function get_driver_data_by_area_common($area){
	$list_data = PHP_getArrayStaff_area_common($area, 'driver');		//同一エリア内のスタッフ情報配列取得
	return $list_data;
}

//----同一本部情報配列取得
function get_honbu_data_by_area_common($area){
	return PHP_getArrayStaff_area_common($area, 'honbu');		//同一エリア内のスタッフ情報配列取得
}

//----ガソリン代取得
function get_gasoline_value_from_settings_common($area){

	$sql = sprintf("select value from settings where name='gasoline' and area='%s'",$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_gasoline_value_from_settings_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["value"];
	exit();
}

//----スタッフ情報取得
function get_driver_data_by_id_common($id){
	$ws_data = get_staff_data_by_id_common($id);	//スタッフ情報取得
	return $ws_data;
}

//----振込金額集計
function get_furikomi_price_driver_by_week_data_common($week_data,$staff_id){

	$week_data_num = count($week_data);

	$furikomi_price_all = 0;

	for( $i=0; $i<$week_data_num; $i++ ){

		$year = $week_data[$i]["year"];
		$month = $week_data[$i]["month"];
		$day = $week_data[$i]["day"];

		$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

		if( $result == true ){

			$furikomi_price = 0;

			if( $year > 2013 ){
				$furikomi_price = get_furikomi_price_driver_by_day_common($staff_id,$year,$month,$day);
			}

			$furikomi_price_all = $furikomi_price_all + $furikomi_price;
		}
	}

	return $furikomi_price_all;
	exit();
}

//----振込金額計算
function get_furikomi_price_driver_by_day_common($staff_id,$year,$month,$day){

	$furikomi_price = 0;

	$driver_data = get_driver_data_by_id_common($staff_id);
	$area = $driver_data["area"];
	$fuel = $driver_data["fuel"];
	$pay_hour = $driver_data["pay_hour"];
	$pay_fix = $driver_data["pay_fix"];

	$gasoline_value = get_gasoline_value_from_settings_2_common($area,$year,$month,$day);		//ガソリン代設定値取得

	$data = get_staff_attendance_data_by_time_common($staff_id,$year,$month,$day);

	$attendance_id = $data["id"];
	$start_time = $data["start_time"];
	$end_time = $data["end_time"];
	$allowance = $data["allowance"];
	$car_distance = $data["car_distance"];
	$highway = $data["highway"];
	$parking = $data["parking"];
	$pay_finish = $data["pay_finish"];
	$start_hour = $data["start_hour"];
	$start_minute = $data["start_minute"];
	$end_hour = $data["end_hour"];
	$end_minute = $data["end_minute"];

	$pay_day = $data["pay_day"];

	if( ($fuel != "0") && ($fuel != "-1") ){

		$value_tmp = $car_distance * $gasoline_value;
//echo $value_tmp . " = " . $car_distance . " * " . $gasoline_value . "<br />";

		if( $value_tmp == "0" ){

			$gasoline_value_disp = 0;

		}else{

			$gasoline_value_disp = intval($value_tmp/$fuel);

		}

	}else{

		$gasoline_value_disp = 0;

	}

	if( $attendance_id != "" ){

		//データのアップデート(最初のアクセス時だけ)と、データの取得
		$data = get_and_update_driver_work_time_common($attendance_id,$start_hour,$start_minute,$end_hour,$end_minute,$start_time,$end_time);	//データのアップデート(最初のアクセス時だけ)と、データの取得

		$start_hour = $data["start_hour"];
		$start_minute = $data["start_minute"];
		$end_hour = $data["end_hour"];
		$end_minute = $data["end_minute"];

		$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);	//時間集計

		$remuneration = get_remuneration_driver_common($pay_hour,$pay_fix,$work_time,$car_distance);

		//報酬+ガソリン代+高速代+駐車場代+手当-清算済み
		$furikomi_price = ( $remuneration + $gasoline_value_disp + $highway + $parking + $allowance ) - $pay_finish - $pay_day;

	}

	return $furikomi_price;
	exit();
}

//データのアップデート(最初のアクセス時だけ)と、データの取得
function get_and_update_driver_work_time_common($attendance_id,$start_hour,$start_minute,$end_hour,$end_minute,$start_time,$end_time){

	if( ($start_hour == "-1") || ($start_minute == "-1") || ($end_hour == "-1") || ($end_minute == "-1") ){

		$staff_type = get_staff_type_by_attendance_id_common($attendance_id);

		if( $staff_type == "honbu" ){
			$time_array = get_time_array_honbu_common();	//本部スタッフ用時刻配列取得
		}else{
			$time_array = get_time_array_driver_common();	//ドライバー用時刻配列取得
		}

		$time_data = get_time_value_for_sale_driver_one_common($time_array,$start_time,$end_time);	//開始及び終了時刻取得

		$start_hour = $time_data["start_hour"];
		$start_minute = $time_data["start_minute"];
		$end_hour = $time_data["end_hour"];
		$end_minute = $time_data["end_minute"];

		//売上金額の更新
		$sql = sprintf("update attendance_staff_new set start_hour='%s',start_minute='%s',end_hour='%s',end_minute='%s' where id='%s'",$start_hour,$start_minute,$end_hour,$end_minute,$attendance_id);
		$res = mysql_query($sql, DbCon);
		if($res == false){
			echo "error!(get_and_update_driver_work_time_common)";
			exit();
		}
	}

	$data["start_hour"] = $start_hour;
	$data["start_minute"] = $start_minute;
	$data["end_hour"] = $end_hour;
	$data["end_minute"] = $end_minute;

	return $data;
	exit();
}

//----前日の指定セラピストの出勤データID取得
function get_pre_day_attendance_id_by_therapist_id_common($therapist_id){

	$data = get_pre_day_for_repeater_stock_common();	//前日の年月日取得

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$attendance_id = get_attendance_id_by_time_for_pre_check_common($therapist_id,$year,$month,$day);	//指定日の指定セラピストの出勤データID取得

	if( $attendance_id == "" ) $attendance_id = "-1";

	return $attendance_id;
	exit();
}

//----指定日前日の年月日取得
function get_pre_day_common($year,$month,$day){
	return get_old_day_common($year, $month, $day, 1);		//指定日前日の指定日数前の年月日取得
}

//----指定日前日の指定日数前の年月日取得
function get_old_day_common($year,$month,$day,$num){

	$data = array();

	$data["year"] = intval(date("Y", mktime(0, 0, 0, $month, $day-$num, $year)));
	$data["month"] = intval(date("m", mktime(0, 0, 0, $month, $day-$num, $year)));
	$data["day"] = intval(date("d", mktime(0, 0, 0, $month, $day-$num, $year)));

	return $data;
	exit();
}

//----指定日前日の指定日数後の年月日取得
function get_next_day_common($year,$month,$day,$num){
	$ws_num = -1 * $num;
	return get_old_day_common($year, $month, $day, $ws_num);		//指定日前日の指定日数前の年月日取得
}

//----指定日の指定セラピストの出勤データID取得
function get_attendance_id_by_time_for_pre_check_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_attendance_id_by_time_for_pre_check_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();
}

//----指定出勤データのポイント数取得
function get_therapist_point_by_attendance_id_common($attendance_id){

	$attendance_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)

	$therapist_id = $attendance_data["therapist_id"];
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];
	$pt_repeat = $attendance_data["pt_repeat"];
	$pt_operation = $attendance_data["pt_operation"];
	$pt_shimei = $attendance_data["pt_shimei"];

	if( $pt_repeat == "-1" ){
		$repeat_point_data = get_repeat_point_data_by_attendance_id_common($attendance_id);		//リピータポイント取得
		$pt_repeat = $repeat_point_data["value"];
	}

	if( $pt_operation == "-1" ) $pt_operation = get_pt_operation_by_attendance_id_common($attendance_id);

	if( $pt_shimei == "-1" ) $pt_shimei = get_pt_shimei_by_attendance_id_common($attendance_id);

	$data["pt_repeat"] = $pt_repeat;
	$data["pt_operation"] = $pt_operation;
	$data["pt_shimei"] = $pt_shimei;

	return $data;
	exit();
}

//----指定出勤データのポイント数取得2
function get_therapist_point_by_attendance_id_2_common($attendance_id,$attendance_data_for_total_point){

	//$attendance_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		出席データの取得(セラピスト)
	$attendance_data = get_attendance_data_one_from_attendance_data_for_total_point_common($attendance_id,$attendance_data_for_total_point);

	if( $attendance_data == false ){

		$pt_repeat = "-1";
		$pt_operation = "-1";
		$pt_shimei = "-1";

	} else {
		$therapist_id = $attendance_data["therapist_id"];
		$year = $attendance_data["year"];
		$month = $attendance_data["month"];
		$day = $attendance_data["day"];
		$pt_repeat = $attendance_data["pt_repeat"];
		$pt_operation = $attendance_data["pt_operation"];
		$pt_shimei = $attendance_data["pt_shimei"];
	}
	//if($attendance_id == 77944) echo $pt_operation . "#####<br />";
	if( $pt_repeat == "-1" ){
		$repeat_point_data = get_repeat_point_data_by_attendance_id_common($attendance_id);		//リピータポイント取得
		$pt_repeat = $repeat_point_data["value"];
	}

	if( $pt_operation == "-1" ){
		$pt_operation = get_pt_operation_by_attendance_id_common($attendance_id);	//予約状況データ数取得
	}

	if( $pt_shimei == "-1" ){
		$pt_shimei = get_pt_shimei_by_attendance_id_common($attendance_id);		//指名ポイント取得（予約状況データ）
	}

	$data["pt_repeat"] = $pt_repeat;
	$data["pt_operation"] = $pt_operation;
	$data["pt_shimei"] = $pt_shimei;

	return $data;
	exit();
}

//----基本料金取得
function get_kihon_price_by_shop_id_and_course_var_common($shop_id,$course_var){

	$sql = sprintf("select kihon_price from shop_course where delete_flg='0' and shop_id='%s' and name='%s'",$shop_id,$course_var);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_kihon_price_by_shop_id_and_course_var_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	$kihon_price = $row["kihon_price"];

	if( $kihon_price == "" ) $kihon_price = 0;

	return $kihon_price;
	exit();
}

//----割引額取得
function get_discount_value_by_shop_id_and_discount_common($shop_id,$discount){

	$sql = sprintf("select discount_value from shop_discount where delete_flg='0' and shop_id='%s' and name='%s'",$shop_id,$discount);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_discount_value_by_shop_id_and_discount_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$discount_value = $row["discount_value"];

	if( $discount_value == "" ) $discount_value = 0;

	return $discount_value;
	exit();
}

//----コースの時間取得
function get_course_int_by_shop_id_and_course_common($shop_id,$course){

	$sql = sprintf("select course_int from shop_course where delete_flg='0' and shop_id='%s' and name='%s'",$shop_id,$course);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_course_int_by_shop_id_and_course_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$course_int = $row["course_int"];

	if( $course_int == "" ) $course_int = 0;

	return $course_int;
	exit();
}

//----割引種別名取得
function get_discount_name_by_shop_id_and_course_var_common($shop_id,$course_var) {
	$ws_data = get_shop_discount_data_by_shop_id_and_discount_common($shop_id, $course_var);	//割引情報取得
	$name = $ws_data["name"];

	if( $name == "" ) $name = "hogehoge";

	return $name;
	exit();
}

//----割引情報取得
function get_shop_discount_data_by_shop_id_and_discount_common($shop_id,$discount){

	$sql = sprintf("select * from shop_discount where delete_flg='0' and shop_id='%s' and name='%s'",$shop_id,$discount);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_shop_discount_data_by_shop_id_and_discount_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----割引種別名取得
function get_discount_name_by_shop_id_and_course_int_and_type_common($shop_id,$course_int,$discount_type){

	$course_int_where = "%-".$course_int."-%";

	$sql = sprintf("select name from shop_discount where delete_flg='0' and shop_id='%s' and type='%s' and (course_int like '%s')",$shop_id,$discount_type,$course_int_where);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_discount_name_by_shop_id_and_course_int_and_type_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	$name = $row["name"];

	return $name;
	exit();
}

//----店舗コース情報取得
function get_shop_course_by_shop_id_common($shop_id){

	$sql = sprintf("select * from shop_course where delete_flg='0' and shop_id='%s' order by order_num asc",$shop_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_shop_course_by_shop_id_common)";
		exit();
	}

	$i = 0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row["name"];
	}

	return $list_data;
	exit();
}

//----報酬取得
function get_remuneration_driver_common($pay_hour,$pay_fix,$work_time,$car_distance){

	$remuneration = 0;

	if( ($pay_hour=="0") || ($pay_hour=="-1") ){
		if( $pay_fix != "0" ) $remuneration = $pay_fix;
	}else{
		//$remuneration = $pay_hour * $work_time + $car_distance * 5;
		$remuneration = $pay_hour * $work_time;
	}

	//給料の小数点以下は四捨五入
	$remuneration = round($remuneration);

	return $remuneration;
	exit();
}

//----距離別料金取得2
function get_remuneration_driver_2_common($car_distance){

	$unit_price = get_unit_price_by_car_distance_common($car_distance);		//距離別単価取得

	$car_distance = floor($car_distance);

	$price = $unit_price * $car_distance;

	return $price;
	exit();
}

//----距離別料金取得3
function get_remuneration_driver_3_common($car_distance,$distance_ave,$year,$month,$day,$area){
	//$unit_price = get_unit_price_by_car_distance_common($car_distance);
	$unit_price = get_unit_price_by_car_distance_2_common($distance_ave,$year,$month,$day,$area);

	$car_distance = floor($car_distance);

	$price = $unit_price * $car_distance;

	return $price;
	exit();
}

//----日給料金取得
function get_remuneration_driver_4_common($staff_id){

	//$pay_fix = get_pay_fix_common($staff_id);		//距離別単価取得
	$pay_fix="10000";
	$pay_fix = floor($pay_fix);

	$price = $pay_fix;

	return $price;
	exit();
}

//データのアップデート(最初のアクセス時だけ)と、データの取得
function get_and_update_honbu_work_time_common($attendance_id,$start_hour,$start_minute,$end_hour,$end_minute,$start_time,$end_time){

	if( ($start_hour == "-1") or ($start_minute == "-1") or ($end_hour == "-1") or ($end_minute == "-1") ){

		$time_array = get_time_array_honbu_common();	//本部スタッフ用時刻配列取得

		$time_data = get_time_value_for_sale_driver_one_common($time_array,$start_time,$end_time);	//開始及び終了時刻取得

		$start_hour = $time_data["start_hour"];
		$start_minute = $time_data["start_minute"];
		$end_hour = $time_data["end_hour"];
		$end_minute = $time_data["end_minute"];

		//売上金額の更新
		$sql = sprintf("update attendance_staff_new set start_hour='%s',start_minute='%s',end_hour='%s',end_minute='%s' where id='%s'",$start_hour,$start_minute,$end_hour,$end_minute,$attendance_id);
		$res = mysql_query($sql, DbCon);
		if($res == false){
			echo "error!(get_and_update_honbu_work_time_common)";
			exit();
		}
	}

	$data["start_hour"] = $start_hour;
	$data["start_minute"] = $start_minute;
	$data["end_hour"] = $end_hour;
	$data["end_minute"] = $end_minute;

	return $data;
	exit();
}

//----チーフ手当取得
function get_chief_allowance_by_therapist_id_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	$chief_allowance = $ws_data["chief_allowance"];

	if( $chief_allowance == "-1" ) $chief_allowance = 0;

	return $chief_allowance;
}

//----チーフ手当取得
function get_chief_allowance_by_attendance_id_common($id_list){

	$attendance_data = get_attendance_data_one_by_attendance_id_common($id_list);		//出席データの取得(セラピスト)

	$therapist_id = $attendance_data["therapist_id"];
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];

	//前日チェック
	$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

	if( $result == false ){
		$chief_allowance = 0;
	}else{
		$chief_allowance = get_chief_allowance_by_therapist_id_and_day_common($therapist_id,$year,$month,$day);	//チーフ手当取得
	}

	return $chief_allowance;
	exit();
}

//----スタッフ情報取得
function get_staff_data_by_id_common($staff_id){

	$sql = sprintf("select * from staff_new_new where id='%s'",$staff_id);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(get_staff_data_by_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----報酬取得
function get_remuneration_staff_by_attendance_id_common($attendance_id,$distance_ave){

	$remuneration = 0;

	//出席データ取得
	$attendance_data = get_attendance_staff_new_data_by_attendance_id_common($attendance_id);	//出勤データ（スタッフ）取得

	$staff_id = $attendance_data["staff_id"];
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];
	$area = $attendance_data["area"];
	$start_time = $attendance_data["start_time"];
	$end_time = $attendance_data["end_time"];

	$start_hour = $attendance_data["start_hour"];
	$start_minute = $attendance_data["start_minute"];
	$end_hour = $attendance_data["end_hour"];
	$end_minute = $attendance_data["end_minute"];

	$car_distance = $attendance_data["car_distance"];

	//当日欠勤の場合はゼロ
	$today_absence = $attendance_data["today_absence"];

	$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

	if( ($today_absence == "1") || ($result == false) ){

		return $remuneration;
		exit();

	}

	//データのアップデート(最初のアクセス時だけ)と、データの取得
	$data = get_and_update_driver_work_time_common($attendance_id,$start_hour,$start_minute,$end_hour,$end_minute,$start_time,$end_time);	//データのアップデート(最初のアクセス時だけ)と、データの取得

	$start_hour = $data["start_hour"];
	$start_minute = $data["start_minute"];
	$end_hour = $data["end_hour"];
	$end_minute = $data["end_minute"];

	$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);	//時間集計

	//スタッフデータ取得
	$staff_data = get_staff_data_by_id_common($staff_id);	//スタッフ情報取得

	$pay_hour = $staff_data["pay_hour"];
	$pay_fix = $staff_data["pay_fix"];

	$staff_type = $staff_data["type"];

	$switching_result = check_switching_driver_remuneration_common($year,$month,$day,$staff_id);

	if( $staff_type != "driver" ){

		$switching_result = false;

	}

	if( $switching_result == true ){

		$remuneration_type = get_remuneration_type_common($staff_id,$year,$month,$day);	//設定報酬データの設定値取得

		if( $remuneration_type == "1" ){
			//時給
			$remuneration = get_remuneration_driver_common($pay_hour,$pay_fix,$work_time,$car_distance);	//報酬取得

		}elseif( $remuneration_type == "2" ){
			//距離制
			//$remuneration = get_remuneration_driver_2_common($car_distance);
			$remuneration = get_remuneration_driver_3_common($car_distance,$distance_ave,$year,$month,$day,$area);

		}else{
			//日給
			$remuneration = get_remuneration_driver_4_common($staff_id);
			//$remuneration = get_remuneration_driver_3_common($car_distance,$distance_ave,$year,$month,$day,$area);
		}

	}else{

		$remuneration = get_remuneration_driver_common($pay_hour,$pay_fix,$work_time,$car_distance);	//報酬取得

	}

	return $remuneration;
	exit();

}

//----全ドライバー情報配列取得
function get_driver_data_all_common(){

	$sql = "select * from staff_new_new where type='driver' and delete_flg='0'";
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_driver_data_all_common)";
		exit();
	}

	$i = 0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----振込金額明細データ取得
function get_furikomi_data_staff_by_day_for_cron_common($staff_id,$year,$month,$day){

	$furikomi_price = 0;
	$remuneration = 0;
	$gasoline_value_disp = 0;
	$highway = 0;
	$parking = 0;
	$allowance = 0;
	$pay_finish = 0;
	$car_distance = 0;
	$sonota = 0;

	$driver_data = get_driver_data_by_id_common($staff_id);		//スタッフ情報取得
	$area = $driver_data["area"];
	$fuel = $driver_data["fuel"];
	$pay_hour = $driver_data["pay_hour"];
	$pay_fix = $driver_data["pay_fix"];

	$settings_gasoline_value = get_gasoline_value_from_settings_2_common($area,$year,$month,$day);		//ガソリン代設定値取得

	$data = get_staff_attendance_data_by_time_common($staff_id,$year,$month,$day);		//出勤データ取得（スタッフ）

	$attendance_id = $data["id"];
	$start_time = $data["start_time"];
	$end_time = $data["end_time"];
	$allowance = $data["allowance"];
	$car_distance = $data["car_distance"];
	$highway = $data["highway"];
	$parking = $data["parking"];
	$pay_finish = $data["pay_finish"];
	$start_hour = $data["start_hour"];
	$start_minute = $data["start_minute"];
	$end_hour = $data["end_hour"];
	$end_minute = $data["end_minute"];
	$pay_day = $data["pay_day"];

	$gasoline_value = get_gasoline_value_by_id_and_time_common($attendance_id);		//ガソリン代取得

	if( ($fuel != "0") && ($fuel != "-1") ){

		$value_tmp = $car_distance * $gasoline_value;

		if( $value_tmp == "0" ){

			$gasoline_value_disp = 0;

		}else{

			$gasoline_value_disp = intval($value_tmp/$fuel);

		}

	}else{

		$gasoline_value_disp = 0;

	}

	if( $attendance_id != "" ){

		//データのアップデート(最初のアクセス時だけ)と、データの取得
		$data = get_and_update_driver_work_time_common($attendance_id,$start_hour,$start_minute,$end_hour,$end_minute,$start_time,$end_time);	//データのアップデート(最初のアクセス時だけ)と、データの取得

		$start_hour = $data["start_hour"];
		$start_minute = $data["start_minute"];
		$end_hour = $data["end_hour"];
		$end_minute = $data["end_minute"];

		$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);	//時間集計

		$remuneration = get_remuneration_driver_common($pay_hour,$pay_fix,$work_time,$car_distance);	//報酬取得

		//報酬+ガソリン代+高速代+駐車場代+手当-清算済み
		$furikomi_price = ( $remuneration + $gasoline_value_disp + $highway + $parking + $allowance ) - $pay_finish - $pay_day;

		$sonota = $highway + $parking + $allowance;

	}

	$return_data["furikomi_price"] = $furikomi_price;
	$return_data["remuneration"] = $remuneration;
	$return_data["gasoline_value"] = $gasoline_value_disp;
	$return_data["highway"] = if_null_is_zero_common($highway);
	$return_data["parking"] = if_null_is_zero_common($parking);
	$return_data["allowance"] = if_null_is_zero_common($allowance);
	$return_data["pay_finish"] = if_null_is_zero_common($pay_finish);
	$return_data["car_distance"] = if_null_is_zero_common($car_distance);
	$return_data["sonota"] = if_null_is_zero_common($sonota);
	$return_data["settings_gasoline_value"] = $settings_gasoline_value;

	return $return_data;
	exit();

}

//----指定セラピストの有無
function check_therapist_exist_by_id_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	if( $row["id"] == "" ){
		return false;
	}else{
		if($ws_data["delete_flg"] == 0 && $ws_data["leave_flg"] == 0) {
			return true;
		} else {
			return false;
		}
	}
}

//----ガソリン代設定値取得
function get_gasoline_value_from_settings_2_common($area,$year,$month,$day){

	$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$sql = sprintf("select value from settings_gasoline where delete_flg='0' and (period_start<='%s') and (period_end>='%s') and area='%s'",$timestamp,$timestamp,$area);
	$res = mysql_query($sql, DbCon);
//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(get_gasoline_value_from_settings_2_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$value = $row["value"];

	if( $value == "" ) $value = 0;

	return $value;
	exit();
}

//出勤情報の取得
function get_attendance_data_2_common($year,$month,$day,$today_flag,$under6_flag,$area,$customer_type){

	$attendance_data = array();

	//出勤しているセラピストのidを取得
	$therapist_array = array();

	$most_start_time = 23;		//注意
	$most_end_time = 1;

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	$where_publish_flg = "";

	if( $customer_type != "1" ) $where_publish_flg = "attendance_new.publish_flg='1'";

	$ws_SQL = "B.delete_flg=0 and B.test_flg=0 and B.leave_flg=0 and A.year='%s' and A.month='%s' and A.day='%s'";
	$ws_SQL .= " and (B.area='%s' or B.kenmu like '%s') and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.syounin_state='1'";
	$where_sql = sprintf($ws_SQL, $year,$month,$day,$shop_area,$where_kenmu,$shop_area);

	if( $where_publish_flg != "" ) $where_sql = $where_sql." and ".$where_publish_flg;

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,A.id as attendance_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where %s order by B.order_num desc";
	$sql = sprintf($ws_SQL, $where_sql);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_2_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;
		$attendance_id = $attendance_data[$i]["attendance_id"];
		$therapist_array[$i] = $attendance_data[$i]["therapist_id"];

		if($most_start_time > $attendance_data[$i]["start_time"]){
			$most_start_time = $attendance_data[$i]["start_time"];
		}

		if($most_end_time < $attendance_data[$i]["end_time"]){
			$most_end_time = $attendance_data[$i]["end_time"];
		}

		$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);
		$res2 = mysql_query($sql, DbCon);
		if($res2 == false){
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_2_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}

		$j=0;

		while($row2 = mysql_fetch_assoc($res2)){

			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;

		}

		if($j==0) $attendance_data[$i]["time_num"]=0;

		$i++;

	}

	//----フリーセラピスト状態配列取得
	$free_therapist_state = PHP_setFreeTherapistState_234(2, $most_start_time, $therapist_array);		//update

	$data = array();

	$data["attendance_data"] = $attendance_data;
	$data["free_therapist_state"] = $free_therapist_state;

	return $data;
	exit();

}

//----トータルポイント取得
function get_total_point_by_therapist_id_common($therapist_id,$year_to,$month_to,$day_to){

	$past_num = "12";

	if( ($therapist_id == "-1") || ($therapist_id == "") ){
		return "0";
		exit();
	}

	$total_point = get_total_point_from_total_point_day_common($therapist_id,$year_to,$month_to,$day_to);	//トータルポイント取得(指定日のDBテーブルのtotal_point)

	if( $total_point != "" ){
		return $total_point;
		exit();
	}

	$date_to = $year_to * 10000 + $month_to * 100 + $day_to;

	$data = get_total_point_from_total_point_common($therapist_id);		//最新のトータルポイント取得（ただしDBテーブルのtotal_point）

	$therapist_total_point = $data["value"];

	$date_from = $data["year"] * 10000 + $data["month"] * 100 + $data["day"];

	//施術と指名のポイント獲得のため
	//$ws_SQL = "select id from attendance_new where year>='2015'";
	//$ws_SQL .= " and ((year>%s) or ((year=%s) and (month>%s)) or ((year=%s) and (month=%s) and (day>=%s)))";
	//$ws_SQL .= " and ((year<%s) or ((year=%s) and (month<%s)) or ((year=%s) and (month=%s) and (day<%s)))";
	//$ws_SQL .= " and therapist_id='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1'";
	//$sql = sprintf($ws_SQL, $year_from,$year_from,$month_from,$year_from,$month_from,$day_from, $year_to,$year_to,$month_to,$year_to,$month_to,$day_to, $therapist_id);
	$ws_SQL = "select id from attendance_new where year>='2015'";
	$ws_SQL .= " and (year*10000+month*100+day)>=%s and (year*10000+month*100+day)<%s";
	$ws_SQL .= " and therapist_id='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1'";
	$sql = sprintf($ws_SQL, $date_from, $date_to, $therapist_id);
	$res = mysql_query($sql, DbCon);
	//echo "shift/point_list.php " . $res . "/" . $sql . "<br />";
	if( $res == false ){
		echo "error!(get_total_point_by_therapist_id_common)" . $sql;
		exit();
	}

	$total_point = 0;

	while($row = mysql_fetch_assoc($res)){

		$attendance_id = $row["id"];

		$kakutoku_point = get_kakutoku_point_by_attendance_id_common($attendance_id);

		$total_point = $total_point + $kakutoku_point;

	}

	$total_point = $total_point + $therapist_total_point;

	return $total_point;
	exit();
}

//----トータルポイント取得2
function get_total_point_by_therapist_id_2_common($therapist_id,$year_to,$month_to,$day_to,$attendance_data_for_total_point){

	$past_num = "12";

	if( ($therapist_id == "-1") || ($therapist_id == "") ){

		return "0";
		exit();

	}

	//----DBテーブルのtotal_pointがある場合
	$total_point = get_total_point_from_total_point_day_common($therapist_id,$year_to,$month_to,$day_to);	//トータルポイント取得(指定日のDBテーブルのtotal_point)

	if( $total_point != "" ){

		return $total_point;
		exit();

	}

	//----DBテーブルのtotal_pointが無い場合
	$data = get_total_point_from_total_point_common($therapist_id);		//最新のトータルポイント取得（ただしDBテーブルのtotal_point）

	$therapist_total_point = $data["value"];
	$year_from = $data["year"];
	$month_from = $data["month"];
	$day_from = $data["day"];

	if( $therapist_total_point == "" ){

		$therapist_total_point = get_therapist_total_point_common($therapist_id);		//セラピストのトータルポイント取得
		$year_now = intval(date('Y'));
		$month_now = intval(date('m'));
		$day_now = intval(date('d'));

		$data = get_kako_day_common($year_now,$month_now,$day_now,$past_num);	//指定した日にち分、過去の日付取得
		$year_from = $data["year"];
		$month_from = $data["month"];
		$day_from = $data["day"];

	}
//*
	$start_year = "2015";

	//施術と指名のポイント獲得のため
	$ws_SQL = "select id from attendance_new where year>='%s' and ((year>%s) or ((year=%s) and (month>%s)) or ((year=%s) and (month=%s) and (day>=%s)))";
	$ws_SQL .= " and ((year<%s) or ((year=%s) and (month<%s)) or ((year=%s) and (month=%s) and (day<%s)))";
	$ws_SQL .= " and therapist_id='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1'";
	$sql = sprintf($ws_SQL, $start_year,$year_from,$year_from,$month_from,$year_from,$month_from,$day_from,$year_to,$year_to,$month_to,$year_to,$month_to,$day_to,$therapist_id);
	$res = mysql_query($sql, DbCon);
	//echo "sale_therapist_one.php " . $res . "/" . $sql . "<br />";
	if( $res == false ){
		echo "error!(get_total_point_by_therapist_id_common)";
		exit();
	}

	$total_point = 0;

	while($row = mysql_fetch_assoc($res)){

		$attendance_id = $row["id"];

		//$kakutoku_point = get_kakutoku_point_by_attendance_id_common($attendance_id);
		$kakutoku_point = get_kakutoku_point_by_attendance_id_2_common($attendance_id,$attendance_data_for_total_point);	//獲得ポイント取得2

		$total_point = $total_point + $kakutoku_point;
		//if($therapist_id == 615) echo $total_point . "/" . $kakutoku_point . " " . $attendance_id . "@@@@@@@@@@@@@@@@@@<br />";

	}

	$total_point = $total_point + $therapist_total_point;
//*/
	return $total_point;
	exit();
}

//----トータルポイント取得(指定日のDBテーブルのtotal_point)
function get_total_point_from_total_point_day_common($therapist_id,$year,$month,$day){
	//----更新が10日サイクルなので古いデータとなる
	$sql = sprintf("select value from total_point where delete_flg=0 and therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_total_point_from_total_point_day_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);
	//echo $row["value"] . "/" . $sql . "<br />";
	return $row["value"];
	exit();
}

//----最新のトータルポイント取得（ただしDBテーブルのtotal_point）
function get_total_point_from_total_point_common($therapist_id){

	$sql = sprintf("select value,year,month,day from total_point where delete_flg=0 and therapist_id='%s' order by attendance_ts desc limit 0,1",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_total_point_from_total_point_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤時刻チェック
function check_wait_time_start_time_common($now_attendance_num,$start_time){

	$limit_num = $now_attendance_num+0;

	if($limit_num >= $start_time){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----出勤時刻チェック
function check_wait_time_end_time_common($now_attendance_num,$end_time){

	$limit_num = $now_attendance_num+0;

	$end_time = $end_time - 1;

	if($limit_num <= $end_time){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----出勤時刻チェック
function check_wait_time_reservation_common($now_attendance_num,$therapist_id,$attendance_id,$start_time,$end_time){

	$limit_num_start = $now_attendance_num + 0;
	$limit_num_end = $now_attendance_num + 0;

	for($i=$limit_num_start;$i<=$limit_num_end;$i++){

		$exist_flg = check_reservation_data_exist_common($attendance_id,$i);	//出勤データ有無(セラピスト)

		if( $exist_flg == true ){
			return false;
			exit();
		}
	}

	return true;
	exit();
}

//----予約データ取得
function get_reservation_new_by_attendance_id_common($attendance_id){

	$sql = sprintf("select * from reservation_new where attendance_id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_new_by_attendance_id_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$list_data[$i++] = $row;

	}

	return $list_data;
	exit();
}

//----予約データ有無
function check_reservation_data_exist_common($attendance_id,$i){

	$sql = sprintf("select id from reservation_new where attendance_id='%s' and time='%s'",$attendance_id,$i);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_reservation_data_exist)";
		exit();
	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----予約データ有無２
function check_reservation_data_exist_2_common($attendance_id,$num1,$num2){

	$sql = sprintf("select id from reservation_new where attendance_id='%s' and (time='%s' or time='%s')",$attendance_id,$num1,$num2);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_reservation_data_exist_2_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----予約データ有無３
function check_reservation_data_exist_3_common($attendance_id,$start_time,$end_time){

	$sql_or = "";

	for( $i=$start_time; $i<=$end_time; $i++ ){

		if( $i == $start_time ){
			$sql_or .= sprintf("time='%s'",$i);
		}else{
			$sql_or .= sprintf(" or time='%s'",$i);
		}
	}

	$sql = sprintf("select id from reservation_new where attendance_id='%s' and (%s)",$attendance_id,$sql_or);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_reservation_data_exist_3_common)";

	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----予約状況データ配列取得
function get_reservation_new_for_delete_common(){

	$sql = "select A.id,B.year,B.month,B.day from reservation_new A";
	$sql .= " left join attendance_new B on B.id=A.attendance_id";
	$sql .= " order by A.id asc limit 0,1000";
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_new_for_delete_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----予約状況データ削除
function delete_reservation_new_by_id_common($id){

	$sql = sprintf("delete from reservation_new where id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(delete_reservation_new_by_id_common)";
		exit();
	}

	return true;
	exit();
}

//----本日予約と出勤チェック
function get_attendance_today_reservation_flg_common($area){

	$time_array = get_time_array_common();		//時刻配列取得

	//echo "get_attendance_today_reservation_flg_common";exit();

	//今の時間
	$now_hour = intval(date("H"));
	$now_minute = intval(date("i"));

	if( ($now_minute > 0) && ($now_minute < 30) ){

		$now_minute = 30;

	}else if( $now_minute > 30 ){

		if($now_hour == '4'){
			$now_minute = 30;
		}else{

			$now_minute = 0;
			$now_hour = $now_hour + 1;

			if($now_hour=="24") $now_hour = 0;
		}
	}

	//----update by aida at 20180220 from
	//$now_attendance_num = change_attendance_num_wait_time_common($now_hour,$now_minute,$time_array);	//時分を時刻IDに変換
	if($area == "tokyo") {
		$now_attendance_num = change_attendance_num_wait_time_common($now_hour,$now_minute,$time_array);	//時分を時刻IDに変換
	} else {
		$now_attendance_num = change_attendance_num_wait_time_2_common($now_hour,$now_minute,$time_array,$area);	//時分を時刻IDに変換2
	}
	//----update by aida at 20180220 to
	if( $now_attendance_num == false ){
		//echo "";
		//exit();
		return false;
	}

	$therapist_data_arr = get_today_work_therapist_data_wait_time_common($area);	//本日出勤セラピストのID取得
	$therapist_data_arr_num = count($therapist_data_arr);

	for($i=0;$i<$therapist_data_arr_num;$i++){

		$start_time_flg = false;
		$end_time_flg = false;
		$reservation_flg = false;

		$therapist_id = $therapist_data_arr[$i]["id"];
		$attendance_id = $therapist_data_arr[$i]["attendance_id"];
		$start_time = $therapist_data_arr[$i]["start_time"];
		$end_time = $therapist_data_arr[$i]["end_time"];

		$start_time_flg = check_wait_time_start_time_common($now_attendance_num,$start_time);	//出勤時刻チェック

		if($start_time_flg==true){

			$end_time_flg = check_wait_time_end_time_common($now_attendance_num,$end_time);		//出勤時刻チェック

			if($end_time_flg==true){

				$reservation_flg = check_wait_time_reservation_common($now_attendance_num,$therapist_id,$attendance_id,$start_time,$end_time);	//出勤時刻チェック

				if( $reservation_flg == true ){
					return true;
					exit();
				}
			}
		}
	}

	return false;
	exit();
}

//----本日予約と出勤チェック(東京用)
function get_attendance_today_reservation_flg_tokyo_common(){

	$area = "tokyo";
	return get_attendance_today_reservation_flg_common($area);		//本日予約と出勤チェック update by aida
}

//----振込明細データ取得
function get_furikomi_price_by_week_data_2_common($week_data,$therapist_id){

	$therapist_data = get_therapist_data_by_id_common($therapist_id);
	$jisou_flg = $therapist_data["jisou_flg"];
	$transport_cost = $therapist_data["transport_cost"];

	$transfer_commission = $therapist_data["transfer_commission"];
	$transfer_commission_price = 0;
	$transfer_commission_price_disp = 0;
	if( $transfer_commission == "2" ){
		$transfer_commission_price = 270;
		$transfer_commission_price_disp = $transfer_commission_price*(-1);
	}

	$week_data_num = count($week_data);

	$furikomi_price = 0;
	$remuneration_all = 0;
	$pay_another_all = 0;

	for( $i=0; $i<$week_data_num; $i++ ){

		$year = $week_data[$i]["year"];
		$month = $week_data[$i]["month"];
		$day = $week_data[$i]["day"];

		if( $year > 2013 ){

			$remuneration = 0;
			$allowance_jisou = 0;
			$allowance = 0;
			$pay_another = 0;
			$pay_finish = 0;
			$pay_day = 0;

			$result = check_last_day_common($year,$month,$day);		//前日チェック

			if( $result == true ){

				$insurance_price = get_insurance_price_day_therapist_id_common($year,$month,$day,$therapist_id);

				//remuneration_therapistから取得
				$data_tmp = get_remuneration_therapist_by_day_common($therapist_id,$year,$month,$day);

				if( $data_tmp["id"] != "" ){
					$remuneration = $data_tmp["remuneration"];
					$allowance_jisou = $data_tmp["allowance_jisou"];
					$allowance = $data_tmp["allowance"];
					$pay_another = $data_tmp["pay_another"];
					$pay_finish = $data_tmp["pay_finish"];
					$pay_day = $data_tmp["pay_day"];

				}else{
					$attendance_data = get_attendance_data_work_common($therapist_id,$year,$month,$day);
					$attendance_id = $attendance_data["id"];

					if( $attendance_id != "" ){
						$pay_day = $attendance_data["pay_day"];
						$pay_another = $attendance_data["pay_another"];
						$pay_finish = $attendance_data["pay_finish"];
						$allowance = $attendance_data["allowance"];

						if( $pay_another == "0" ){
							$pay_another = $transport_cost;
						}

						$allowance_jisou = 0;

						if( $jisou_flg == "1" ){
							$allowance_jisou = get_allowance_therapist_common($year,$month,$day,$attendance_id);	//交通費取得
						}

						$remuneration_data = get_therapist_remuneration_by_attendance_id_common($attendance_id);	//報酬取得
						$remuneration = $remuneration_data["remuneration"];

					}

				}

				$remuneration = $remuneration - $insurance_price;

			}

			$allowance = $allowance + $allowance_jisou;
			$furikomi_price = $furikomi_price + $remuneration - $pay_day + $allowance + $pay_another - $pay_finish;
			$remuneration_all = $remuneration_all + $remuneration - $pay_day + $allowance - $pay_finish;
			$pay_another_all = $pay_another_all + $pay_another;

		}
	}

	$furikomi_price = $furikomi_price - $transfer_commission_price;

	$data["furikomi_price"] = $furikomi_price;
	$data["remuneration"] = $remuneration_all;

	//立替金(固定支給交通費、含む)
	$data["other_price"] = $pay_another_all;

	$data["transfer_commission_price"] = $transfer_commission_price;
	$data["transfer_commission_price_disp"] = $transfer_commission_price_disp;

	return $data;
	exit();

}

//----振込額取得
function get_furikomi_price_driver_by_week_data_2_common($week_data,$staff_id){

	$week_data_num = count($week_data);

	$furikomi_price_all = 0;
	$remuneration_all = 0;
	$gasoline_all = 0;

	for( $i=0; $i<$week_data_num; $i++ ){

		$year = $week_data[$i]["year"];
		$month = $week_data[$i]["month"];
		$day = $week_data[$i]["day"];

		$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

		if( $result == true ){

			$furikomi_price = 0;

			if( $year > 2013 ){

				$data = get_furikomi_price_driver_by_day_2_common($staff_id,$year,$month,$day);

				$furikomi_price = $data["furikomi_price"];
				$remuneration = $data["remuneration_return"];
				$gasoline = $data["gasoline_return"];
				$car_distance_over_allowance = $data["car_distance_over_allowance"];
				$chief_allowance = $data["chief_allowance"];

			}else{

				$furikomi_price = 0;
				$remuneration = 0;
				$gasoline = 0;
				$car_distance_over_allowance = 0;
				$chief_allowance = 0;

			}

			$furikomi_price_all = $furikomi_price_all + $furikomi_price;
			$remuneration_all = $remuneration_all + $remuneration;

			$gasoline_all = $gasoline_all + $gasoline;

			//超過手当をガソリン代にプラス
			$gasoline_all = $gasoline_all + $car_distance_over_allowance;

			if( $remuneration > 0 ){
				//チーフ手当を報酬にプラス
				$remuneration_all = $remuneration_all + $chief_allowance;
			}
		}
	}

	$data["furikomi_price"] = $furikomi_price_all;
	$data["remuneration"] = $remuneration_all;
	$data["other_price"] = $gasoline_all;

	return $data;
	exit();
}

//----振込明細取得
function get_furikomi_price_driver_by_day_2_common($staff_id,$year,$month,$day){

	$staff_type = get_staff_type_by_id_common($staff_id);

	$furikomi_price = 0;
	$remuneration_return = 0;
	$gasoline_return = 0;
	$remuneration = 0;
	$gasoline_value_disp = 0;
	$highway = 0;
	$parking = 0;
	$allowance = 0;
	$pay_finish = 0;
	$car_distance = 0;
	$sonota = 0;
	$car_distance_over_allowance = 0;
	$pay_day = 0;
	$settings_gasoline_value = 0;

	$data_tmp = get_remuneration_staff_by_day_common($staff_id,$year,$month,$day);	//スタッフ報酬データ取得

	if( $data_tmp["id"] != "" ){

		$area = $data_tmp["area"];
		$pay_hour = $data_tmp["pay_hour"];
		$pay_fix = $data_tmp["pay_fix"];
		$fuel = $data_tmp["fuel"];
		$type = $data_tmp["type"];
		$chief_allowance = $data_tmp["chief_allowance"];
		$attendance_id = $data_tmp["attendance_id"];
		$start_time = $data_tmp["start_time"];
		$end_time = $data_tmp["end_time"];
		$allowance = $data_tmp["allowance"];
		$car_distance = $data_tmp["car_distance"];
		$highway = $data_tmp["highway"];
		$parking = $data_tmp["parking"];
		$pay_finish = $data_tmp["pay_finish"];
		$start_hour = $data_tmp["start_hour"];
		$start_minute = $data_tmp["start_minute"];
		$end_hour = $data_tmp["end_hour"];
		$end_minute = $data_tmp["end_minute"];
		$pay_day = $data_tmp["pay_day"];
		$gasoline = $data_tmp["gasoline"];
		$work_time = $data_tmp["work_time"];
		$remuneration = $data_tmp["remuneration"];
		$car_distance_over_allowance = $data_tmp["car_distance_over_allowance"];
		$sum_price = $data_tmp["sum_price"];

		$gasoline_value_disp = $gasoline;

		$settings_gasoline_value = get_gasoline_value_from_settings_2_common($area,$year,$month,$day);		//ガソリン代設定値取得

	}else{

		$data = get_staff_attendance_data_by_time_syounin_common($staff_id,$year,$month,$day);

		$attendance_id = $data["id"];

		if( $attendance_id != "" ){

			$start_time = $data["start_time"];
			$end_time = $data["end_time"];
			$allowance = $data["allowance"];
			$car_distance = $data["car_distance"];
			$highway = $data["highway"];
			$parking = $data["parking"];
			$pay_finish = $data["pay_finish"];
			$start_hour = $data["start_hour"];
			$start_minute = $data["start_minute"];
			$end_hour = $data["end_hour"];
			$end_minute = $data["end_minute"];
			$pay_day = $data["pay_day"];
			$driver_data = get_driver_data_by_id_common($staff_id);
			$area = $driver_data["area"];
			$fuel = $driver_data["fuel"];
			$pay_hour = $driver_data["pay_hour"];
			$pay_fix = $driver_data["pay_fix"];
			$chief_allowance = $driver_data["chief_allowance"];
			$chief_allowance_start_time = $driver_data["chief_allowance_start_time"];
			$chief_allowance = get_chief_allowance_staff_common($year,$month,$day,$chief_allowance,$chief_allowance_start_time);	//店長報酬が店長就任前の時はゼロにする
			$staff_type = $driver_data["type"];

			$staff_area = $driver_data["area"];

			$settings_gasoline_value = get_gasoline_value_from_settings_2_common($area,$year,$month,$day);		//ガソリン代設定値取得
			$gasoline_value = get_gasoline_value_by_id_and_time_common($attendance_id);		//ガソリン代取得
			if( ($fuel != "0") && ($fuel != "-1") ){
				$value_tmp = $car_distance * $gasoline_value;
				if( $value_tmp == "0" ){
					$gasoline_value_disp = 0;
				}else{
					$gasoline_value_disp = intval($value_tmp/$fuel);
				}
			}else{
				$gasoline_value_disp = 0;
			}
			//データのアップデート(最初のアクセス時だけ)と、データの取得
			$data = get_and_update_driver_work_time_common($attendance_id,$start_hour,$start_minute,$end_hour,$end_minute,$start_time,$end_time);	//データのアップデート(最初のアクセス時だけ)と、データの取得
			$start_hour = $data["start_hour"];
			$start_minute = $data["start_minute"];
			$end_hour = $data["end_hour"];
			$end_minute = $data["end_minute"];
			$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);	//時間集計
			//走行距離/時間,超過距離/時間,超過距離/日,超過手当
			$data_tmp = get_car_distance_allowance_data_common($car_distance,$work_time,$staff_type,$year,$month,$day);		//カー距離別報酬データ取得
			$car_distance_over_allowance = $data_tmp["car_distance_over_allowance"];

			if( $staff_area == "tokyo" ){

				//超過手当はゼロ
				$car_distance_over_allowance = 0;

			}
			$distance_ave = round($car_distance/$work_time, 1);
			$remuneration = get_remuneration_staff_by_attendance_id_common($attendance_id,$distance_ave);	//報酬取得

		}

	}

	$switching_result = check_switching_driver_remuneration_common($year,$month,$day,$staff_id);

	if( $staff_type != "driver" ){

		$switching_result = false;

	}

	$remuneration_type = get_remuneration_type_common($staff_id,$year,$month,$day);	//設定報酬データの設定値取得

	if( $staff_type == "driver" ){

		//インセンティブは、超過手当に加算
		$incentive = get_incentive_common($car_distance,$year,$month,$day);
		$car_distance_over_allowance = $car_distance_over_allowance + $incentive;

	}

	if( ($switching_result == true) && ($remuneration_type != "1") ){

		//報酬+高速代+駐車場代-清算済み
		$furikomi_price = ( $remuneration + $highway + $parking ) - $pay_finish - $pay_day;

		$remuneration_return = ( $remuneration + $highway + $parking ) - $pay_finish - $pay_day;
		$gasoline_return = 0;
		$sonota = $highway + $parking;

		$gasoline_return = 0;
		//$car_distance_over_allowance = 0;
		$chief_allowance = 0;
		$gasoline_value_disp = 0;

	}else{

		//報酬+ガソリン代+高速代+駐車場代+手当-清算済み
		$furikomi_price = ( $remuneration + $gasoline_value_disp + $highway + $parking + $allowance ) - $pay_finish - $pay_day;
		//振込金額に超過手当を加算
		$furikomi_price = $furikomi_price + $car_distance_over_allowance;
		if( $remuneration > 0 ){
			//振込金額にチーフ手当を加算
			$furikomi_price = $furikomi_price + $chief_allowance;
		}
		$remuneration_return = ( $remuneration + $highway + $parking + $allowance ) - $pay_finish - $pay_day;
		$gasoline_return = $gasoline_value_disp;
		$sonota = $highway + $parking + $allowance;

	}

	//$sum_priceが記録済みの場合は、$furikomi_priceは、$sum_priceに
	if( $sum_price > 0 ){
		$furikomi_price = $sum_price;
	}

	$data["furikomi_price"] = $furikomi_price;
	$data["remuneration_return"] = $remuneration_return;
	$data["gasoline_return"] = $gasoline_return;
	$data["car_distance_over_allowance"] = $car_distance_over_allowance;
	$data["work_time"] = $work_time;
	$data["remuneration"] = $remuneration;
	$data["gasoline_value"] = $gasoline_value_disp;
	$data["highway"] = if_null_is_zero_common($highway);
	$data["parking"] = if_null_is_zero_common($parking);
	$data["allowance"] = if_null_is_zero_common($allowance);
	$data["pay_finish"] = if_null_is_zero_common($pay_finish);
	$data["car_distance"] = if_null_is_zero_common($car_distance);
	$data["sonota"] = if_null_is_zero_common($sonota);
	$data["pay_day"] = if_null_is_zero_common($pay_day);
	$data["settings_gasoline_value"] = $settings_gasoline_value;

	$data["chief_allowance"] = $chief_allowance;
	if(!empty($pay_fix)){
	    $data["pay_per_hour_flg"] = false;
	}
	else{
	    $data["pay_per_hour_flg"] = true;
	}
	return $data;
	exit();

}

//----指名料取得
function get_shimei_price_common($price,$transportation){

	$shimei_value = 1000;

	$price_shijutsu = $price - $shimei_value - $transportation;

	$shimei_add_value = intval($price_shijutsu/10);

	$return_value = $shimei_value + $shimei_add_value;

	return $return_value;
	exit();
}

//出席データが登録済みであるかどうか(登録済み：attendance_id,未登録:false)
function check_staff_attendance_exist_common($staff_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_staff_new where today_absence='0' and staff_id='%s' and year='%s' and month='%s' and day='%s'",$staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_staff_attendance_exist_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	$id = $row["id"];

	if( $id == "" ){
		return false;
		exit();
	}else{
		return $id;
		exit();
	}
}

//出席データが登録済みであるかどうか(登録済み：true,未登録:false)
function check_staff_attendance_exist_2_common($staff_id,$year,$month,$day,$page_area){

	$sql = sprintf("select id from attendance_staff_new where area='%s' and staff_id='%s' and year='%s' and month='%s' and day='%s' and today_absence=0 and attendance_adjustment=0",$page_area,$staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_staff_attendance_exist_2_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----ガソリン代取得
function get_gasoline_value_by_id_and_time_common($attendance_id){

	$data = get_attendance_staff_data_one_by_attendance_id_common($attendance_id);	//出勤データの取得(スタッフ)

	$staff_id = $data["staff_id"];
	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$area = get_staff_area_by_id_common($staff_id);		//スタッフのエリア取得

	$value = get_gasoline_value_from_settings_2_common($area,$year,$month,$day);		//ガソリン代設定値取得

	return $value;
	exit();

}

//出勤情報の取得
function get_attendance_data_3_common($year,$month,$day,$today_flag,$under6_flag,$area,$customer_type,$shop_id,$access_type){

	$attendance_data = array();

	//出勤しているセラピストのidを取得
	$therapist_array = array();

	$most_start_time = 23;		//注意
	$most_end_time = 1;

	$shop_area = $area;

	$where_kenmu = "%".$shop_area."%";

	$where_publish_flg = "";

	if( $customer_type != "1" ) $where_publish_flg = "A.publish_flg='1'";

	$ws_SQL = "B.delete_flg=0 and B.test_flg=0 and B.leave_flg=0 and A.year='%s' and A.month='%s' and A.day='%s'";
	$ws_SQL .= " and (B.area='%s' or B.kenmu like '%s') and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.syounin_state='1'";
	$where_sql = sprintf($ws_SQL, $year,$month,$day,$shop_area,$where_kenmu,$shop_area);

	if( $where_publish_flg != "" ) $where_sql = $where_sql." and ".$where_publish_flg;

	// 出勤しているセラピスト情報を取得するためのSQL文
	$ws_SQL = "select *,A.id as attendance_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where %s order by B.order_num desc";
	$sql = sprintf($ws_SQL, $where_sql);
	$res = mysql_query($sql, DbCon);
	if($res == false){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_3_common:1)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	// 一覧に表示される顧客データを変数に格納する処理
	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$attendance_data[$i] = $row;



		if(
			(
			($shop_id == "1") ||
			($shop_id == "6") ||
			($shop_id == "7") ||
			($shop_id == "8") ||
			($shop_id == "10")
			)
			&&
			($access_type=="sp") ){

						$therapist_id = $row["therapist_id"];
						$name_site = $row["name_site"];

			$name_site = sprintf(
			'<span onclick="open_therapist_info_sp(%s);" class="open_therapist_info_sp_span">%s</span>',
			$therapist_id,$name_site);

			$attendance_data[$i]["name_site"] = $name_site;

		}else if(
			(
			($shop_id == "1") ||
			($shop_id == "6") ||
			($shop_id == "7") ||
			($shop_id == "8") ||
			($shop_id == "10")
			)
			&&
			($access_type=="pc") ){

						$therapist_id = $row["therapist_id"];
						$name_site = $row["name_site"];

			$name_site = sprintf('<span onclick="open_therapist_info_pc(%s);" class="open_therapist_info_pc_span">%s</span>',$therapist_id,$name_site);

			$attendance_data[$i]["name_site"] = $name_site;

		}

		$attendance_id = $attendance_data[$i]["attendance_id"];
		$therapist_array[$i] = $attendance_data[$i]["therapist_id"];

		if($most_start_time > $attendance_data[$i]["start_time"]){
			$most_start_time = $attendance_data[$i]["start_time"];
		}

		if($most_end_time < $attendance_data[$i]["end_time"]){
			$most_end_time = $attendance_data[$i]["end_time"];
		}

		$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);
		$res2 = mysql_query($sql, DbCon);
		if($res2 == false){
			$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_data_3_common:2)";
			header("Location: ".WWW_URL."error.php");
			exit();
		}

		$j=0;

		while($row2 = mysql_fetch_assoc($res2)){
			$attendance_data[$i]["time"][$j] = $row2["time"];
			$time_num = count($attendance_data[$i]["time"]);
			$attendance_data[$i]["time_num"] = $time_num;
			$j++;
		}

		if($j==0) $attendance_data[$i]["time_num"]=0;

		$i++;
	}

	//----フリーセラピスト状態配列取得
	$free_therapist_state = PHP_setFreeTherapistState_234(3, $most_start_time, &$therapist_array);

	$data = array();

	$data["attendance_data"] = $attendance_data;
	$data["free_therapist_state"] = $free_therapist_state;

	return $data;
	exit();
}

//----セラピスト頁取得
function get_therapist_page_data_attendance_one_common($therapist_id){

	$therapist_id = mysql_real_escape_string($therapist_id);

	$area = get_therapist_area_by_therapist_id_common($therapist_id);	//セラピストのエリア取得

	$sql = sprintf("select * from therapist_page where therapist_id='%s' and delete_flg=0",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_page_data_attendance_one_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if($row) {
		if(!$row["img_url"]) $row["img_url"] = DEFAULT_therapist_img;	//insert by aida at 20180319
	}
	//$pr_refle = $row["pr_refle"];
	//$pr_sapporo = $row["pr_sapporo"];
	//$pr_new = $row["pr_new"];
	//
	//if( $area == "tokyo" ){
	//	$pr_content = $pr_refle;
	//}else if( $area == "sapporo" ){
	//	$pr_content = $pr_sapporo;
	//}else{
	//	$pr_content = $pr_new;
	//}
	$ws_cellName = get_pr_name_common($area);		//update by aida at 20180220
	$pr_content = $row[$ws_cellName];

	$skill = $row["skill"];
	$skill_data = explode(",",$skill);
	$therapist_name = get_therapist_name_by_therapist_id_common($therapist_id);	//セラピスト名取得
	$data = $row;
	$data["skill_data"] = $skill_data;
	$data["therapist_name"] = $therapist_name;
	$data["pr_content"] = $pr_content;

	return $data;
	exit();
}

//----スタッフのエリア取得
function get_staff_area_by_id_common($staff_id){
	return get_area_by_staff_id_common($staff_id);		//スタッフのエリア取得
}

//----出勤データ取得(セラピスト)
function get_today_attendance_therapisit_data_common($area,$year,$month,$day){

	$where_kenmu = "%".$area."%";

	$ws_SQL = "(B.area='%s' or B.kenmu like '%s') and B.test_flg='0' and B.leave_flg='0'";
	$ws_SQL .= " and A.publish_flg='1' and A.area='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.attendance_adjustment='0' and A.year='%s' and A.month='%s' and A.day='%s' and A.syounin_state='1'";
	$where_sql = sprintf($ws_SQL, $area,$where_kenmu,$area,$year,$month,$day);

	// 出勤しているセラピスト情報を取得するためのSQL文
	$sql = sprintf("select B.* from attendance_new A left join therapist_new B on A.therapist_id=B.id where %s",$where_sql);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_today_attendance_therapisit_data_common)";
		exit();
	}

	$i = 0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//====( カード関係 )======================================================================================

//----カード手数料取得
function get_card_commission_free_common($card_commission_type,$card_price_free){

	$card_commission_free = 0;

	if( $card_commission_type == "1" ){
		$card_commission_free = round(($card_price_free * CARD_commission1) / 10000);
	}else if( $card_commission_type == "2" ){
		$card_commission_free = round(($card_price_free * CARD_commission2) / 10000);
	}

	return $card_commission_free;
	exit();
}

//----カード手数料取得
function get_card_commission_free_common_2($card_commission_type,$card_price_free){

	$card_commission_free = 0;

	if( $card_commission_type == "2" ){
		$card_commission_free = round(($card_price_free * CARD_commission1) / 10000);
	}else if( $card_commission_type == "1" ){
		$card_commission_free = round(($card_price_free * CARD_commission2) / 10000);
	}

	return $card_commission_free;
	exit();
}

//----カード手数料取得
function get_card_commission_free_for_sale_shop_common($card_commission_type,$card_price_free,$card_flg,$price_tmp,$card_commission_free_tmp){

	$card_commission_free = 0;

	if( $card_flg == "1" ){

		//カードフラグON
		if( $card_commission_free_tmp == "0" ){
			$card_commission_free = get_card_commission_free_common($card_commission_type,$price_tmp);	//カード手数料取得
		}else{
			$card_commission_free = $card_commission_free_tmp;
		}
	}else{

		//カードフラグOFF
		if( $card_commission_free_tmp == "0" ){
			$card_commission_free = 0;
		}else{
			$card_commission_free = $card_commission_free_tmp;
		}
	}

	return $card_commission_free;
	exit();
}

//----カード手数料取得2
function get_card_commission_free_for_sale_shop_2_common($card_commission_type,$price_card,$card_commission_free_tmp){

	$card_commission_free = 0;

	if( $price_card != "0" ){

		if( $card_commission_free_tmp == "0" ){
			$card_commission_free = get_card_commission_free_common($card_commission_type,$price_card);	//カード手数料取得
		}else{
			$card_commission_free = $card_commission_free_tmp;
		}
	}

	return $card_commission_free;
	exit();
}

//----現金またはカード金額取得
function get_price_genkin_or_card_common($card_flg,$price_tmp,$card_price_free){

	$price = 0;

	if( $card_flg == "1" ){
		//カードフラグONの場合
		if( $card_price_free == "0" ){
			$price = $price_tmp;
		}else{
			$price = $card_price_free;
		}
	}else{
		//カードフラグOFFの場合
		if( $card_price_free == "0" ){
			$price = $price_tmp;
		}else{
			$price = $price_tmp - $card_price_free;
		}
	}

	return $price;
	exit();
}

//----現金またはカード金額取得
function get_price_genkin_or_card_2_common($price_tmp,$card_price_free,$type,$card_flg){

	$price = 0;

	if( $type == "card" ){
		//カード
		if( $card_flg == "1" ){
			if( $card_price_free == "0" ){
				$price = $price_tmp;
			}else{
				$price = $card_price_free;
			}
		}else{
			$price = $card_price_free;
		}
	}else{
		//現金
		if( $card_flg == "1" ){

			if( $card_price_free != "0" ){
				$price = $price_tmp - $card_price_free;
			}
		}else{

			if( $card_price_free == "0" ){
				$price = $price_tmp;
			}else{
				$price = $price_tmp - $card_price_free;
			}
		}
	}

	return $price;
	exit();
}

//----カード情報取得
function get_card_info_for_update_reservation_data_common($reservation_for_board_id,$card_flg,$price){

	$data = get_reservation_for_board_data_by_id_common($reservation_for_board_id);		//予約状況データ取得

	$card_flg_db = $data["card_flg"];

	$card_price_free = $data["card_price_free"];
	$card_commission_free = $data["card_commission_free"];
	$card_commission_type = $data["card_commission_type"];

	if( $card_flg_db != $card_flg ){

		if( $card_flg == "1" ){

			$card_commission_type = "1";
			$card_price_free = $price;
			$card_commission_free = get_card_commission_free_common($card_commission_type,$card_price_free);

		}else{

			$card_commission_type = "1";
			$card_price_free = "0";
			$card_commission_free = "0";

		}

	}

	$data["card_price_free"] = $card_price_free;
	$data["card_commission_free"] = $card_commission_free;
	$data["card_commission_type"] = $card_commission_type;

	return $data;
	exit();

}
//====( xxxx )======================================================================================

//----チーフ手当取得
function get_chief_allowance_by_therapist_id_and_day_common($therapist_id,$year,$month,$day){

	$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得

	$chief_allowance = $ws_data["chief_allowance"];
	$chief_allowance_start_time = $ws_data["chief_allowance_start_time"];

	if( $chief_allowance == "-1" ){
		$chief_allowance = 0;
	}else{
		if( ($timestamp < $chief_allowance_start_time) || ($chief_allowance_start_time=="0") ) $chief_allowance = 0;
	}

	return $chief_allowance;
	exit();
}

//----出勤しているセラピスト情報を取得
function get_therapist_data_for_sale_therapist_common($year,$month,$day,$area){

	// 出勤しているセラピスト情報を取得するためのSQL文

	$ws_SQL = "select *,A.id as attendance_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on A.therapist_id=B.id";
	$ws_SQL .= " where B.delete_flg=0 and B.test_flg=0 and year='%s' and month='%s' and day='%s' and A.today_absence='0' and A.kekkin_flg='0' and A.area='%s'";
	$ws_SQL .= " order by B.rank_order_num asc,B.order_num desc";
	$sql = sprintf($ws_SQL, $year,$month,$day,$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_data_for_sale_therapist_common:1)";
		exit();
	}

	$data = array();

	$i=0;
	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		//therapist_id重複チェック
		$result = check_duplication_therapist_id_in_attendance_common($data,$therapist_id);

		if( $result == false ){

			$data[$i] = $row;

			$attendance_id = $data[$i]["attendance_id"];

			$data[$i]["area"] = $area;

			$sql = sprintf("select time from reservation_new where attendance_id='%s'",$attendance_id);

			$res2 = mysql_query($sql, DbCon);
			if($res == false){
				echo "error!(get_therapist_data_for_sale_therapist_common:2)";
				exit();
			}

			$j=0;
			while($row2 = mysql_fetch_assoc($res2)){
				$data[$i]["time"][$j] = $row2["time"];
				$time_num = count($data[$i]["time"]);
				$data[$i]["time_num"] = $time_num;
				$j++;
			}
			if($j==0){
				$data[$i]["time_num"]=0;
			}

			$i++;

		}
	}

	return $data;
	exit();
}

//therapist_id重複チェック
function check_duplication_therapist_id_in_attendance_common($data,$therapist_id){

	$data_num = count($data);

	for($i=0;$i<$data_num;$i++){

		$therapist_id_tmp = $data[$i]["therapist_id"];

		if( $therapist_id_tmp == $therapist_id ){

			return true;
			exit();
		}

	}

	return false;
	exit();
}

//----店舗名取得
function get_shop_name_by_reservation_for_board_id_common($id){
	$ws_data = PHP_get_reservation_for_board_data_by_id_common($id, true);		//予約状況データ取得
	return $row["shop_name"];
}

//データがあるかどうかチェック(ある：true,ない：false)
function check_attendance_new_exist_by_therapist_id_common($year,$month,$day,$therapist_id){

	$sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and therapist_id='%s' and year='%s' and month='%s' and day='%s'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_attendance_new_exist_by_therapist_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----移動費用更新
function update_movement_cost_common($movement_cost_id,$cost_value,$movement_method,$area_name,$other){

	$sql = sprintf("update movement_cost set cost_value='%s',movement_method='%s',area_name='%s',other='%s' where id='%s'",$cost_value,$movement_method,$area_name,$other,$movement_cost_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(update_movement_cost_common)";
		exit();
	}

	return true;
	exit();
}

//----予約メッセージ取得
function get_reservation_message_common($hour,$area){
	return PHP_get_reservation_message_common($hour, $area);	//予約メッセージ取得 in ^common/include/shop_area_list.php
}

//----予約メッセージ取得(東京)
function get_reservation_message_tokyo_common($hour){
	return PHP_get_reservation_message_common($hour, "tokyo");	//予約メッセージ取得 in ^common/include/shop_area_list.php
}

//出勤セラピストの数を取得
function get_therapist_attendance_num_common($year,$month,$day,$area){

	$file_name = basename($_SERVER['PHP_SELF']);
	if( $file_name == "error.php" ){
		return false;
		exit();
	}

	$ws_SQL = "select DISTINCT A.therapist_id from attendance_new A";
	$ws_SQL .= " left join therapist_new B on B.id=A.therapist_id";
	$ws_SQL .= " where B.delete_flg='0' and B.leave_flg='0' and B.test_flg='0'";
	$ws_SQL .= " and A.kekkin_flg='0' and A.today_absence='0' and A.syounin_state='1' and A.year='%s' and A.month='%s' and A.day='%s' and A.area='%s'";
	$ws_SQL .= " and B.rank<>'19'";
	$sql = sprintf($ws_SQL, $year,$month,$day,$area);
	$res = mysql_query($sql, DbCon);
	//echo $re . "/" . $sql . "<br />";
	if($res == false){
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----駅名(ローマ字)取得
function get_url_name_by_station_id_common($id){

	$sql = sprintf("select url_name from station_new where id='%s'",$id);
	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(get_url_name_by_station_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);
	return $row["url_name"];
	exit();
}

//----駅名取得
function get_station_name_by_url_name_common($url_name,$shop_area){

	$url_name = mysql_real_escape_string($url_name);

	$sql = sprintf("select name from station_new where url_name='%s' and shop_area='%s' and delete_flg=0",$url_name,$shop_area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_station_name_by_url_name_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	$station_name = $row["name"];

	return $station_name;
	exit();
}

//----リピート数取得
function get_repeater_num_kikan_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area){

	$year_name = "year";
	$month_name = "month";
	$day_name = "day";
	$where_kikan = get_where_kikan_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$year_name,$month_name,$day_name);	//SQL文用期間条件編集

	if( $type == "all" ){
		$sql = sprintf("select A.id from repeater A left join attendance_new B on B.id=A.attendance_id where A.delete_flg='0' and A.shop_area='%s' and (%s)",$area,$where_kikan);
	}else if( $type == "therapist" ){
		$sql = sprintf("select A.id from repeater A left join attendance_new B on B.id=A.attendance_id where A.delete_flg='0' and A.therapist_id='%s' and (%s)",$therapist_id,$where_kikan);
	}else{
		echo "error!(get_repeater_num_kikan_common)";
		exit();
	}
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res == false){
		echo "error!(get_repeater_num_kikan_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	return $num;
	exit();
}

//----SQL文用期間条件編集
function get_where_kikan_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$year_name,$month_name,$day_name){

$where_kikan = sprintf("
(
	(%s>%s)
	or
	(
		(%s=%s)
		and
		(%s>%s)
	)
	or
	(
		(%s=%s)
		and
		(%s=%s)
		and
		(%s>=%s)
	)
)
and
(
	(%s<%s)
	or
	(
		(%s=%s)
		and
		(%s<%s)
	)
	or
	(
		(%s=%s)
		and
		(%s=%s)
		and
		(%s<=%s)
	)
)",
$year_name,$year_old,$year_name,$year_old,$month_name,$month_old,$year_name,$year_old,$month_name,$month_old,$day_name,$day_old,
$year_name,$year_now,$year_name,$year_now,$month_name,$month_now,$year_name,$year_now,$month_name,$month_now,$day_name,$day_now
);

	return $where_kikan;
	exit();
}

//----指名数等集計値取得
function get_all_data_for_reportcard_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area){

	$year_name = "reservation_for_board.year";
	$month_name = "reservation_for_board.month";
	$day_name = "reservation_for_board.day";
	$where_kikan = get_where_kikan_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$year_name,$month_name,$day_name);	//SQL文用期間条件編集

	if( $type == "all" ){
		$sql = sprintf("select new_flg,shimei_flg from reservation_for_board where delete_flg='0' and shop_area='%s' and (%s)",$area,$where_kikan);
	}else if( $type == "therapist" ){
		$ws_SQL = "select reservation_for_board.new_flg,reservation_for_board.shimei_flg from reservation_for_board";
		$ws_SQL .= " left join attendance_new on attendance_new.id=reservation_for_board.attendance_id";
		$ws_SQL .= " where reservation_for_board.delete_flg='0' and attendance_new.therapist_id='%s' and (%s)";
		$sql = sprintf($ws_SQL, $therapist_id,$where_kikan);
	}else{

		echo "error!(get_all_data_for_reportcard_common)";
		exit();

	}
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_all_data_for_reportcard_common)";
		exit();
	}

	$all_num = 0;
	$shimei_num = 0;
	$new_num = 0;

	while($row = mysql_fetch_assoc($res)){

		$new_flg = $row["new_flg"];
		$shimei_flg = $row["shimei_flg"];

		if( $new_flg == "1" ) $new_num++;

		if( $shimei_flg == "1" ) $shimei_num++;

		$all_num++;

	}

	$data["all_num"] = $all_num;
	$data["new_num"] = $new_num;
	$data["shimei_num"] = $shimei_num;

	return $data;
	exit();

}

//----個別URL用文字取得
function get_therapist_for_kobetsu_url_by_therapist_id_common($therapist_id){
	//----個別URL用文字取得
	return get_therapist_for_kobetsu_url_common($therapist_id);
}

//----出勤データ重複チェック
function get_attendance_id_not_duplicate_common($data){

	$id_data = array();

	$data_num = count($data);

	$x = 0;

	for($i=0;$i<$data_num;$i++){

		$attendance_id = $data[$i]["attendance_id"];

		$result = check_data_exist_array_common($id_data,$attendance_id);	//配列検索

		if( $result == false ){
			$id_data[$x] = $attendance_id;
			$x++;
		}
	}

	return $id_data;
	exit();
}

//----セラピスト報酬データ有無
function check_therapist_remuneration_common($attendance_id){

	$sql = sprintf("select id from therapist_remuneration where delete_flg='0' and attendance_id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_therapist_remuneration_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----セラピスト報酬データ有無
function check_data_exist_therapist_remuneration_common($attendance_id){
	return check_therapist_remuneration_common($attendance_id);		//セラピスト報酬データ有無
}

//----セラピスト報酬登録
function insert_therapist_remuneration_common($year,$month,$day,$attendance_id,$remuneration,$lowest_guarantee,$lowest_guarantee_flg,$chief_allowance){

	$sql = sprintf("
insert into therapist_remuneration(year,month,day,attendance_id,remuneration,lowest_guarantee,lowest_guarantee_flg,chief_allowance)
values('%s','%s','%s','%s','%s','%s','%s','%s')",
$year,$month,$day,$attendance_id,$remuneration,$lowest_guarantee,$lowest_guarantee_flg,$chief_allowance);

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(insert_therapist_remuneration_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピスト報酬登録2
function insert_therapist_remuneration_2_common(
$attendance_id,$remuneration,$chief_allowance,$lowest_guarantee,$lowest_guarantee_flg,$share_rate,$year,$month,$day){

	$sql = sprintf("
insert into therapist_remuneration(attendance_id,remuneration,chief_allowance,lowest_guarantee,lowest_guarantee_flg,share_rate,year,month,day)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$attendance_id,$remuneration,$chief_allowance,$lowest_guarantee,$lowest_guarantee_flg,$share_rate,$year,$month,$day);

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(insert_therapist_remuneration_2_common)";
		exit();
	}

	return true;
	exit();
}

//----報酬情報取得
function get_remuneration_for_sale_shop_common($year,$month,$day,$shop_name){

	$ws_SQL = "select B.price,B.therapist_id,A.attendance_id,A.shop_area,A.shimei_flg,A.new_flg,A.repeat_flg,A.transportation from reservation_for_board A";
	$ws_SQL .= " left join sale_history B on B.reservation_no=A.reservation_no";
	$ws_SQL .= " where A.shop_name='%s' and A.year='%s' and A.month='%s' and A.day='%s' and A.delete_flg=0";
	$ws_SQL .= " and B.delete_flg=0 and B.eigyou_year='%s' and B.eigyou_month='%s' and B.eigyou_day='%s'";
	$sql = sprintf($ws_SQL, $shop_name,$year,$month,$day,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_remuneration_for_sale_shop_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	$list_data_num = count($list_data);

	$remuneration_all = 0;

	for($i=0;$i<$list_data_num;$i++){

		$price = $list_data[$i]["price"];
		$shimei_flg = $list_data[$i]["shimei_flg"];
		$new_flg = $list_data[$i]["new_flg"];
		$repeat_flg = $list_data[$i]["repeat_flg"];
		$transportation = $list_data[$i]["transportation"];
		$attendance_id = $list_data[$i]["attendance_id"];
		$therapist_id = $list_data[$i]["therapist_id"];

		$result = check_effective_attendance_new_common($attendance_id);

		if( $result == false ){

			$remuneration = "0";

		}else{

			$share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

			if($list_data[$i]["shop_area"]=="tokyo_reraku"){
				$remuneration = get_remuneration_one_reraku_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(東京リラク)
			} else if($list_data[$i]["shop_area"]=="tokyo_bigao"){
				$remuneration = get_remuneration_one_bigao_common($price,$shimei_flg,$new_flg,$repeat_flg,$transportation,$share_rate,$attendance_id);	//報酬計算(BIGAO)
			}else{
				$remuneration = get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(一般)
			}

		}

		$remuneration_all = $remuneration_all + $remuneration;

	}

	//$chief_allowance = get_chief_allowance_by_shop_name_common($year,$month,$day,$shop_name);

	return $remuneration_all;
	exit();
}

//----報酬合計取得
function get_lowest_guarantee_by_area_common($area,$year,$month,$day){

	//$start_time = microtime(true);

	$attendance_data = get_attendance_data_work_by_area_common($area,$year,$month,$day);	//指定エリアの出勤データ配列取得(承認済)
	$attendance_data_num = count($attendance_data);

	$lowest_guarantee_all = 0;

	for($i=0;$i<$attendance_data_num;$i++){

		$attendance_id = $attendance_data[$i]["id"];

		$remuneration_data = get_therapist_remuneration_by_attendance_id_common($attendance_id);	//報酬取得
		$lowest_guarantee = $remuneration_data["lowest_guarantee"];

		$lowest_guarantee_all = $lowest_guarantee_all + $lowest_guarantee;

	}

	/*
	$end_time = microtime(true);
	$sa = $end_time-$start_time;
	echo $sa;exit();
	*/

	return $lowest_guarantee_all;
	exit();
}

//----店舗別報酬データの有無
function check_for_sale_shop_common($year,$month,$day,$shop_id){

	$sql = sprintf("select id from for_sale_shop where delete_flg='0' and year='%s' and month='%s' and day='%s' and shop_id='%s'",$year,$month,$day,$shop_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_for_sale_shop_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----店舗別報酬データ登録
function insert_for_sale_shop_common($shop_id,$year,$month,$day,$remuneration,$lowest_guarantee,$chief_allowance){

	$sql = sprintf("
insert into for_sale_shop(shop_id,year,month,day,remuneration,lowest_guarantee,chief_allowance)
values('%s','%s','%s','%s','%s','%s','%s')",
$shop_id,$year,$month,$day,$remuneration,$lowest_guarantee,$chief_allowance);

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(insert_for_sale_shop_common)";
		exit();
	}

	return true;
	exit();
}

//----店舗別報酬データ取得
function get_for_sale_shop_data_common($shop_id,$year,$month,$day){

	$sql = sprintf("select * from for_sale_shop where delete_flg=0 and shop_id='%s' and year='%s' and month='%s' and day='%s'",$shop_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_for_sale_shop_data_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//出勤セラピストのサイト名を取得
function get_therapist_attendance_site_name_common($year,$month,$day,$area){

	$ws_SQL = "select B.name_site,B.name_aroma from attendance_new A";
	$ws_SQL .= " left join therapist_new B on B.id=A.therapist_id";
	$ws_SQL .= " where B.delete_flg='0' and B.leave_flg='0' and B.test_flg='0'";
	$ws_SQL .= " and A.kekkin_flg='0' and A.today_absence='0' and A.syounin_state='1' and A.year='%s' and A.month='%s' and A.day='%s' and A.area='%s'";
	$ws_SQL .= " and B.rank<>'19'";
	$sql = sprintf($ws_SQL, $year,$month,$day,$area);

	$res = mysql_query($sql, DbCon);

	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_therapist_attendance_site_name_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----アロマ源氏名またはサイト名取得
function get_therapist_name_by_therapist_id_aroma_2_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得

	$name = $ws_data["name_aroma"];
	if( $name == "" ) $name = $ws_data["name_site"];

	return $name;
	exit();
}

//----セラピストページ(仮)にインサートしスキル、資格を返す
function get_therapist_page_shift_hp_common($therapist_id,$area){

	$skill = "";
	$skill_2 = "";
	$shikaku = "";
	$pr = "";

	//セラピストページ(仮)から取得
	$data = get_therapist_page_tmp_common($therapist_id);

	if( $data["id"] != "" ){

		$skill = $data["skill"];
		$skill_2 = $data["skill_2"];
		$shikaku = $data["shikaku"];
		$pr = $data["pr"];

	}else{

		//セラピストページから取得
		$data = get_therapist_page_data_by_therapist_id_common($therapist_id);	//セラピスト頁情報取得

		if( $data["id"] != "" ){

			$skill = $data["skill"];
			$skill_2 = $data["skill_2"];
			$shikaku = $data["shikaku"];

			//if( $area == "tokyo" ){
			//	$pr = $data["pr_refle"];
			//}else if( $area == "sapporo" ){
			//	$pr = $data["pr_sapporo"];
			//}else{
			//	$pr = $data["pr_new"];
			//}
			$ws_cellName = get_pr_name_common($area);		//update by aida at 20180220
			$pr_content = $row[$ws_cellName];

			//セラピストページ(仮)にインサート
			insert_therapist_page_tmp_common($therapist_id,$skill,$skill_2,$shikaku,$pr);
		}

	}

	$return_data["skill"] = $skill;
	$return_data["skill_2"] = $skill_2;
	$return_data["shikaku"] = $shikaku;
	$return_data["pr"] = $pr;

	return $return_data;
	exit();
}

//セラピストページ(仮)から取得
function get_therapist_page_tmp_common($therapist_id){

	$sql = sprintf("select * from therapist_page_tmp where delete_flg=0 and therapist_id='%s'",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_page_tmp_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//セラピストページ(仮)から取得２
function get_therapist_page_tmp_2_common($therapist_id){

	$sql = sprintf("select * from therapist_page_tmp where approval_flg=0 and delete_flg=0 and therapist_id='%s'",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_page_tmp_2_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//セラピストページ(仮)にインサート
function insert_therapist_page_tmp_common($therapist_id,$skill,$skill_2,$shikaku,$pr){

	$sql = sprintf("insert into therapist_page_tmp(therapist_id,skill,skill_2,shikaku,pr) values('%s','%s','%s','%s','%s')",$therapist_id,$skill,$skill_2,$shikaku,$pr);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(insert_therapist_page_tmp_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピストページ更新
function update_therapist_page_tmp_common($therapist_id,$skill_string,$skill_2_string,$shikaku,$pr){

	$approval_flg = "0";

	$sql = sprintf("update therapist_page_tmp set skill='%s',skill_2='%s',shikaku='%s',pr='%s',approval_flg='%s' where delete_flg=0 and therapist_id='%s'",$skill_string,$skill_2_string,$shikaku,$pr,$approval_flg,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(update_therapist_page_tmp_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピスト頁承認処理 2018/11/13 murase update from
function approval_therapist_page_tmp_common($therapist_id,$area){

	$data = get_therapist_page_tmp_common($therapist_id);	//セラピストページ(仮)から取得
	$therapist_name = get_therapist_name_by_therapist_id_honmyou($therapist_id, $area);
	$therapist_mail = get_therapist_mail_by_therapist_id($therapist_id);
	$check_url = get_check_url_shift_front($area,$therapist_id);
	$area_name = get_area_name_by_area($area);
	$site_name = $area_name."リフレ";

	$skill = $data["skill"];
	$skill_2 = $data["skill_2"];
	$shikaku = $data["shikaku"];
	$pr = $data["pr"];

	$pr_name = "";

	//if( $area == "tokyo" ){
	//	$pr_name = "pr_refle";
	//}else if( $area == "sapporo" ){
	//	$pr_name = "pr_sapporo";
	//}else{
	//	$pr_name = "pr_new";
	//}
	$pr_name = get_pr_name_common($area);		//update by aida at 20180220
	//$pr_content = $row[$ws_cellName];

	//トランザクションをはじめる準備
	$sql = "set autocommit = 0";
	mysql_query( $sql, DbCon );

	//トランザクション開始
	$sql = "begin";
	mysql_query( $sql, DbCon );

	//セラピストページを更新

	//$sql = sprintf("update therapist_page set skill='%s',skill_2='%s',shikaku='%s',%s='%s' where delete_flg=0 and therapist_id='%s'",$skill,$skill_2,$shikaku,$pr_name,$pr,$therapist_id);
	$sql = sprintf("update therapist_page set skill='%s',skill_2='%s',shikaku='%s',%s='%s' where delete_flg=0 and therapist_id='%s'",$skill,$skill_2,$shikaku,$pr_name,$pr,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, DbCon );

		echo "error!(approval_therapist_page_tmp_common 1)";
		exit();
	}

	//approval_flgを1に

	$sql = sprintf("update therapist_page_tmp set approval_flg='1' where delete_flg=0 and therapist_id='%s'",$therapist_id);
	$res = mysql_query($sql, DbCon);

	mb_language("ja");
	mb_internal_encoding("UTF-8");
	$mailto = $therapist_mail;
	//$mailto = 'murase@skipboat.jp';

	$title = sprintf("【HP変更】HP変更を承認しました。[%s]",$therapist_name);

	$content =<<<EOT
{$therapist_name}さん

HP変更が承認されましたのでご報告いたします。

{$check_url}
EOT;

	$header = "From: info@neo-gate.jp\n";
	$header .= "Bcc: info@neo-gate.jp";
	//$header .= ",";
	//$header .= "minamikawa@neo-gate.jp";

	$result = mb_send_mail($mailto,$title,$content,$header,MAIL_PARAMETER);

	if( $res == false ){
		//ロールバック
		$sql = "rollback";
		mysql_query( $sql, DbCon );

		echo "error!(approval_therapist_page_tmp_common 2)";
		exit();
	}

	//コミット
	$sql = "commit";
	mysql_query( $sql, DbCon );

	//MySQL切断
	//mysql_close( $con );

	return true;
	exit();
}
//2018/11/13 murase update to

//----カー距離別報酬データ取得
function get_car_distance_allowance_data_common($car_distance,$work_time,$staff_type,$year,$month,$day){

	//走行距離/時間,超過距離/時間,超過距離/日,超過手当

	$car_distance_hour = "0";

	if( ($car_distance != "0") && ($work_time != "0") ){

		//「走行距離」?「勤務時間」　＊小数点一位まで
		$tmp = $car_distance/$work_time;
		$car_distance_hour = round($tmp,1);

	}

	$car_distance_over_hour = "0";

	if($car_distance_hour != "0"){

		//15km/時間を基準に、±を表記
		$car_distance_over_hour_value = $car_distance_hour - 15;

		if( $car_distance_over_hour_value > 0 ){

			$car_distance_over_hour = "+".$car_distance_over_hour_value;

		}else{

			$car_distance_over_hour = $car_distance_over_hour_value;

		}

	}

	$car_distance_over_day = "0";

	if($car_distance_hour != "0"){

		//「超過距離/時間」×勤務時間
		$car_distance_over_day_value = $car_distance_over_hour_value * $work_time;

		$car_distance_over_day_value = round($car_distance_over_day_value,1);

		if( $car_distance_over_day_value > 0 ){

			$car_distance_over_day = "+".$car_distance_over_day_value;

		}else{

			$car_distance_over_day = $car_distance_over_day_value;

		}

	}

	$car_distance_over_allowance = "0";

	if($car_distance_hour != "0"){

		//「超過距離/日」×３０円

		if( $car_distance_over_day_value > 0 ){

			$car_distance_over_allowance = $car_distance_over_day_value * 30 * 0;

		}

		//小数点以下、四捨五入
		$car_distance_over_allowance = round($car_distance_over_allowance);

	}

	//from 2015-9-10
	$result = check_over_time_common($year,$month,$day);

	if( ($staff_type != "driver") || ($result == false) ){

		$car_distance_hour = "0";
		$car_distance_over_hour = "0";
		$car_distance_over_day = "0";
		$car_distance_over_allowance = "0";
		$car_distance_over_hour_value = "0";
		$car_distance_over_day_value = "0";

	}

	$data["car_distance_hour"] = if_null_is_zero_common($car_distance_hour);
	$data["car_distance_over_hour"] = if_null_is_zero_common($car_distance_over_hour);
	$data["car_distance_over_day"] = if_null_is_zero_common($car_distance_over_day);
	$data["car_distance_over_allowance"] = if_null_is_zero_common($car_distance_over_allowance);
	$data["car_distance_over_hour_value"] = if_null_is_zero_common($car_distance_over_hour_value);
	$data["car_distance_over_day_value"] = if_null_is_zero_common($car_distance_over_day_value);

	return $data;
	exit();

}

//----カード手数料切替日チェック
function check_kirikae_card_commission_calculation_common($year,$month,$day){
	/*
	$ts = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$year_kirikae = "2015";
	$month_kirikae = "4";
	$day_kirikae = "17";

	$ts_kirikae = get_timestamp_by_year_month_day_common($year_kirikae,$month_kirikae,$day_kirikae);	//指定年月日をタイムスタンプ形式に変換

	if( $ts < $ts_kirikae ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
	*/
	return PHP_checkChangeDate("card", $year, $month, $day);	//変更日チェック in common/include/const.php
}

//----旧料金取得（現金、カード）
function get_genkin_price_card_price_card_commission_old_common($year,$month,$day,$shop_name){

	$card_flg=0;
	$genkin_price = get_sale_price_by_shop_name_2_common($year,$month,$day,$shop_name,$card_flg);	//店舗別売上集計

	$card_flg=1;
	$card_price = get_sale_price_by_shop_name_2_common($year,$month,$day,$shop_name,$card_flg);		//店舗別売上集計

	if( $card_price == "0" ){

		$card_commission = 0;

	}else{

		$card_commission = ( $card_price * 324 ) / 10000;

		//四捨五入
		$card_commission = round($card_commission);

	}

	$data["genkin_price"] = $genkin_price;
	$data["card_price"] = $card_price;
	$data["card_commission"] = $card_commission;

	return $data;
	exit();

}

//----新料金取得（現金、カード）
function get_genkin_price_card_price_card_commission_new_common($year,$month,$day,$shop_name){

	$data = get_sale_price_by_shop_name_4_common($year,$month,$day,$shop_name);	//店舗別売上集計4

	$price_genkin = $data["price_genkin"];
	$price_card = $data["price_card"];
	$card_commission_free = $data["card_commission_free"];

	$data["genkin_price"] = $price_genkin;
	$data["card_price"] = $price_card;
	$data["card_commission"] = $card_commission_free;

	return $data;
	exit();

}

//----店舗別売上集計2
function get_sale_price_by_shop_name_2_common($year,$month,$day,$shop_name,$card_flg){

	$ws_SQL = "select B.price,A.extension,A.card_state_extension from reservation_for_board A";
	$ws_SQL .= " left join sale_history B on B.reservation_no=A.reservation_no";
	$ws_SQL .= " where A.shop_name='%s' and A.card_flg='%s' and A.year='%s' and A.month='%s' and A.day='%s' and A.delete_flg=0";
	$ws_SQL .= " and B.delete_flg=0 and B.eigyou_year='%s' and B.eigyou_month='%s' and B.eigyou_day='%s'";
	$sql = sprintf($ws_SQL, $shop_name,$card_flg,$year,$month,$day,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){

		echo "error!(get_sale_price_by_shop_name_2_common)";
		exit();

	}

	$total = 0;

	while($row = mysql_fetch_assoc($res)){

		$price = $row["price"];
		$extension = $row["extension"];
		$card_state_extension = $row["card_state_extension"];

		$extension_price = 0;

		if( $extension != "0" ){

			$extension_price = get_extension_price_common($extension,$shop_name);	//超過料金取得

			if( $card_flg == "0" ){

				//延長支払い、カードの場合
				if( $card_state_extension == "1" ){
					$price = $price - $extension_price;
				}

			}else if( $card_flg == "1" ){

				//延長支払い、現金の場合
				if( $card_state_extension == "0" ){
					$price = $price - $extension_price;
				}
			}
		}

		$total = $total + $price;
	}

	if( $card_flg == "0" ){
		$card_flg_2 = "1";
	}else{
		$card_flg_2 = "0";
	}

	$extension_price = get_extension_price_for_shop_name_common($year,$month,$day,$shop_name,$card_flg_2);	//予約データより延長料金集計

	$total = $total + $extension_price;

	return $total;
	exit();
}

//----店舗別売上集計4
function get_sale_price_by_shop_name_4_common($year,$month,$day,$shop_name){

	$ws_SQL = "select B.price,A.card_flg,A.card_price_free,A.card_commission_free,A.card_commission_type from reservation_for_board A";
	$ws_SQL .= " left join sale_history B on B.reservation_no=A.reservation_no";
	$ws_SQL .= " where A.shop_name='%s' and A.year='%s' and A.month='%s' and A.day='%s' and A.delete_flg=0";
	$ws_SQL .= " and B.delete_flg=0 and B.eigyou_year='%s' and B.eigyou_month='%s' and B.eigyou_day='%s'";
	$sql = sprintf($ws_SQL, $shop_name,$year,$month,$day,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_sale_price_by_shop_name_4_common)";
		exit();
	}

	$price_genkin_all = 0;
	$price_card_all = 0;
	$card_commission_free_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$price_tmp = $row["price"];
		$card_price_free = $row["card_price_free"];
		$card_commission_free_tmp = $row["card_commission_free"];
		$card_commission_type = $row["card_commission_type"];
		$card_flg = $row["card_flg"];

		$type = "genkin";
		$price_genkin = get_price_genkin_or_card_2_common($price_tmp,$card_price_free,$type,$card_flg);

		$type = "card";
		$price_card = get_price_genkin_or_card_2_common($price_tmp,$card_price_free,$type,$card_flg);

		$card_commission_free = get_card_commission_free_for_sale_shop_2_common($card_commission_type,$price_card,$card_commission_free_tmp);

		$price_genkin_all = $price_genkin_all + $price_genkin;
		$price_card_all = $price_card_all + $price_card;

		$card_commission_free_all = $card_commission_free_all + $card_commission_free;

	}

	$data["price_genkin"] = $price_genkin_all;
	$data["price_card"] = $price_card_all;
	$data["card_commission_free"] = $card_commission_free_all;

	return $data;
	exit();
}

//----予約データより延長料金集計
function get_extension_price_for_shop_name_common($year,$month,$day,$shop_name,$card_flg){

	$sql = sprintf("select extension,card_state_extension from reservation_for_board where shop_name='%s' and card_flg='%s' and year='%s' and month='%s' and day='%s' and delete_flg=0",$shop_name,$card_flg,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_extension_price_for_shop_name_common)";
		exit();
	}

	$extension_price_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$extension = $row["extension"];
		$card_state_extension = $row["card_state_extension"];

		$extension_price = 0;

		if( $extension != "0" ){

			$extension_price = get_extension_price_common($extension,$shop_name);	//超過料金取得

			if( $card_flg == "0" ){

				//延長支払い、カードの場合
				if( $card_state_extension == "1" ){
					$extension_price_all = $extension_price_all + $extension_price;
				}
			}else if( $card_flg == "1" ){

				//延長支払い、現金の場合
				if( $card_state_extension == "0" ){
					$extension_price_all = $extension_price_all + $extension_price;
				}
			}
		}
	}

	return $extension_price_all;
	exit();

}

//----報酬総額集計
function get_remuneration_at_sale_shop_2_common($year,$month,$day,$shop_name){

	//for_sale_shopにデータがあれば、それを返す
	$shop_id = get_shop_id_by_shop_name_common($shop_name);		//店舗のID取得

	$data = get_for_sale_shop_data_common($shop_id,$year,$month,$day);	//店舗別報酬データ取得

	if( $data["id"] != "" ){

		$remuneration = $data["remuneration"];
		$chief_allowance = $data["chief_allowance"];

		$remuneration = $remuneration + $chief_allowance;

		return $remuneration;
		exit();

	}

	$remuneration_all = 0;

	$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

	if( $result == false ){

		return $remuneration_all;
		exit();

	}

	$ws_SQL = "select B.price, B.therapist_id,A.attendance_id,A.shop_area,A.shimei_flg,A.new_flg,A.repeat_flg,A.transportation from reservation_for_board A";
	$ws_SQL .= " left join sale_history B on B.reservation_no=A.reservation_no";
	$ws_SQL .= " where A.shop_name='%s' and A.year='%s' and A.month='%s' and A.day='%s' and A.delete_flg=0 and B.delete_flg=0 and B.eigyou_year='%s' and B.eigyou_month='%s' and B.eigyou_day='%s'";
	$sql = sprintf($ws_SQL, $shop_name,$year,$month,$day,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_remuneration_at_sale_shop_2_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$list_data[$i++] = $row;

	}

	$list_data_num = count($list_data);

	for($i=0;$i<$list_data_num;$i++){

		$price = $list_data[$i]["price"];
		$shimei_flg = $list_data[$i]["shimei_flg"];
		$new_flg = $list_data[$i]["new_flg"];
		$repeat_flg = $list_data[$i]["repeat_flg"];
		$transportation = $list_data[$i]["transportation"];
		$attendance_id = $list_data[$i]["attendance_id"];

		$therapist_id = $list_data[$i]["therapist_id"];

		$result = check_effective_attendance_new_common($attendance_id);

		if( $result == false ){

			$remuneration = "0";

		}else{

			$share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

			if($list_data[$i]["shop_area"]=="tokyo_reraku"){
				$remuneration = get_remuneration_one_reraku_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(東京リラク)
			}
			else if($list_data[$i]["shop_area"]=="tokyo_bigao"){
				$remuneration = get_remuneration_one_bigao_common($price,$shimei_flg,$new_flg,$repeat_flg,$transportation,$share_rate,$attendance_id);	//報酬計算(BIGAO)
			}
			else{
				$remuneration = get_remuneration_one_common($price,$shimei_flg,$transportation,$share_rate);	//報酬計算(一般)
			}

		}

		$remuneration_all = $remuneration_all + $remuneration;

	}

	$chief_allowance = get_chief_allowance_by_shop_name_common($year,$month,$day,$shop_name);

	$remuneration_all = $remuneration_all + $chief_allowance;

	return $remuneration_all;
	exit();

}

//日払い報酬取得
function get_pay_day_therapist_common($year,$month,$day,$shop_name,$shop_area){

	$pay_day_all = 0;

	if( ($shop_area == "tokyo") && ($shop_name != "東京リフレ") ){

		return $pay_day_all;
		exit();

	}

	$sql = sprintf("select pay_day from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and area='%s' and year='%s' and month='%s' and day='%s'",$shop_area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_pay_day_therapist_common)";
		exit();
	}

	while($row = mysql_fetch_assoc($res)){

		$pay_day = $row["pay_day"];

		$pay_day_all = $pay_day_all + $pay_day;

	}

	return $pay_day_all;
	exit();
}

//----セラピスト自走手当取得
function get_allowance_jisou_therapist_day_common($year,$month,$day,$shop_name,$shop_area){

	$allowance = 0;

	if( ($shop_area == "tokyo") && ($shop_name != "東京リフレ") ){

		return $allowance;
		exit();

	}

	$sql = sprintf("select transportation,attendance_id from reservation_for_board where delete_flg=0 and year='%s' and month='%s' and day='%s' and shop_area='%s'",$year,$month,$day,$shop_area);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_allowance_jisou_therapist_day_common)";
		exit();
	}

	while($row = mysql_fetch_assoc($res)){

		$attendance_id = $row["attendance_id"];
		$transportation = $row["transportation"];

		$therapist_id = get_therapist_id_by_attendance_id_common($attendance_id);	//出勤データIDからセラピストIDを取得
		$therapist_data = get_therapist_data_by_id_common($therapist_id);
		$jisou_flg = $therapist_data["jisou_flg"];

		if( $jisou_flg == "1" ){

			if( $transportation == "0" ){
				$allowance = $allowance + 500;
			}else{
				$allowance = $allowance + $transportation;
			}
		}
	}

	return $allowance;
	exit();

}

//----セラピスト手当取得
function get_allowance_therapist_day_common($year,$month,$day,$shop_name,$shop_area){

	$allowance_all = 0;

	if( ($shop_area == "tokyo") && ($shop_name != "東京リフレ") ){

		return $allowance_all;
		exit();

	}

	$sql = sprintf("select allowance from attendance_new where today_absence='0' and kekkin_flg='0' and syounin_state='1' and area='%s' and year='%s' and month='%s' and day='%s'",$shop_area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){

		echo "error!(get_allowance_therapist_day_common)";
		exit();

	}

	while($row = mysql_fetch_assoc($res)){

		$allowance = $row["allowance"];

		$allowance_all = $allowance_all + $allowance;

	}

	return $allowance_all;
	exit();

}

//----事務所支払い
function get_office_payment_for_sale_shop_common($year,$month,$day,$area){

	$driver_data = get_honbu_data_for_sale_common($year,$month,$day,$area);	//スタッフデータ配列取得
	$driver_data_num = count($driver_data);

	$remuneration_all = 0;
	$gasoline_value_disp_all = 0;
	$allowance_all = 0;

	for($i=0;$i<$driver_data_num;$i++){

		$driver_id = $driver_data[$i]["id"];
		$pay_hour = $driver_data[$i]["pay_hour"];
		$pay_fix = $driver_data[$i]["pay_fix"];
		$fuel = $driver_data[$i]["fuel"];

		if( $pay_hour == "-1" ){

			$pay_hour = 0;

		}

		$data = get_staff_attendance_data_by_time_common($driver_id,$year,$month,$day);	//出勤データ取得（スタッフ）

		$start_time = $data["start_time"];
		$end_time = $data["end_time"];

		$car_distance = $data["car_distance"];
		$allowance = $data["allowance"];

		$start_hour = $data["start_hour"];
		$start_minute = $data["start_minute"];
		$end_hour = $data["end_hour"];
		$end_minute = $data["end_minute"];

		$driver_area = get_staff_area_by_id_common($driver_id);		//スタッフのエリア取得
		$gasoline_value = get_gasoline_value_from_settings_2_common($driver_area,$year,$month,$day);		//ガソリン代設定値取得

		if( ($fuel != "0") && ($fuel != "-1") ){

			$gasoline_value_disp = intval(($car_distance*$gasoline_value)/$fuel);

		}else{

			$gasoline_value_disp = 0;

		}

		if( ( $start_hour == "-1" ) || ( $start_minute == "-1" ) || ( $end_hour == "-1" ) || ( $end_minute == "-1" ) ){

			$work_time = get_work_time_common($start_time,$end_time);

		}else{

			$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);	//時間集計

		}

		$remuneration = get_remuneration_driver_common($pay_hour,$pay_fix,$work_time,$car_distance);	//報酬取得

		$remuneration_all = $remuneration_all + $remuneration;
		$gasoline_value_disp_all = $gasoline_value_disp_all + $gasoline_value_disp;

		$allowance_all = $allowance_all + $allowance;

	}

	$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

	if( $result == false ){

		$remuneration_all = 0;

	}

	$data["remuneration"] = $remuneration_all;
	$data["gasoline"] = $gasoline_value_disp_all;
	$data["allowance"] = $allowance_all;

	return $data;
	exit();
}

//出席データが登録済みであるかどうか(登録済み：true,未登録:false)
function check_staff_attendance_exist_board_common($staff_id,$day_year,$day_month,$day_day,$area){

	$sql = sprintf("select id from attendance_staff_new where staff_id='%s' and year='%s' and month='%s' and day='%s' and today_absence=0 and attendance_adjustment=0",$staff_id,$day_year,$day_month,$day_day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_staff_attendance_exist_board_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----ドライバー支払い明細取得
function get_driver_payment_for_sale_shop_2_common($year,$month,$day,$area){

	$driver_data = get_driver_data_for_sale_shop_common($year,$month,$day,$area);		//スタッフ情報取得

	$driver_data_num = count($driver_data);

	$remuneration_all = 0;
	$gasoline_value_disp_all = 0;
	$allowance_all = 0;

	$car_distance_over_allowance_all = 0;

	$chief_allowance_all = 0;

	$incentive_all = 0;

	for($i=0;$i<$driver_data_num;$i++){

		$driver_id = $driver_data[$i]["id"];
		$pay_hour = $driver_data[$i]["pay_hour"];
		$pay_fix = $driver_data[$i]["pay_fix"];
		$fuel = $driver_data[$i]["fuel"];
		$staff_type = $driver_data[$i]["type"];

		$chief_allowance = $driver_data[$i]["chief_allowance"];
		$chief_allowance_start_time = $driver_data[$i]["chief_allowance_start_time"];

		$staff_area = $driver_data[$i]["area"];

		$chief_allowance = get_chief_allowance_staff_common($year,$month,$day,$chief_allowance,$chief_allowance_start_time);	//店長報酬が店長就任前の時はゼロにする

		if( $pay_hour == "-1" ){

			$pay_hour = 0;

		}

		$data = get_staff_attendance_data_by_time_common($driver_id,$year,$month,$day);

		$attendance_id = $data["id"];

		$start_time = $data["start_time"];
		$end_time = $data["end_time"];

		$car_distance = $data["car_distance"];
		$allowance = $data["allowance"];

		$start_hour = $data["start_hour"];
		$start_minute = $data["start_minute"];
		$end_hour = $data["end_hour"];
		$end_minute = $data["end_minute"];

		$incentive = get_incentive_common($car_distance,$year,$month,$day);
		$incentive_all = $incentive_all + $incentive;

		$driver_area = get_staff_area_by_id_common($driver_id);		//スタッフのエリア取得

		$staff_id = $driver_id;
		$remuneration_type = get_remuneration_type_common($staff_id,$year,$month,$day);	//設定報酬データの設定値取得

		if( ($driver_area == "tokyo") && ($remuneration_type != "1") ){

			$gasoline_value = 0;
			$gasoline_value_disp = 0;

		}else{

			$gasoline_value = get_gasoline_value_from_settings_2_common($driver_area,$year,$month,$day);		//ガソリン代設定値取得

			if( ($fuel != "0") && ($fuel != "-1") ){
				$gasoline_value_disp = intval(($car_distance*$gasoline_value)/$fuel);
			}else{
				$gasoline_value_disp = 0;
			}

		}

		/*
		if( ($fuel != "0") && ($fuel != "-1") ){
			$gasoline_value_disp = intval(($car_distance*$gasoline_value)/$fuel);
		}else{
			$gasoline_value_disp = 0;
		}
		*/

		if( ( $start_hour == "-1" ) || ( $start_minute == "-1" ) || ( $end_hour == "-1" ) || ( $end_minute == "-1" ) ){

			$work_time = get_work_time_common($start_time,$end_time);

		}else{

			$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);	//時間集計

		}

		//$remuneration = get_remuneration_driver_common($pay_hour,$pay_fix,$work_time,$car_distance);
		$remuneration = get_remuneration_staff_by_attendance_id_common($attendance_id);		//報酬取得

		$remuneration_all = $remuneration_all + $remuneration;
		$gasoline_value_disp_all = $gasoline_value_disp_all + $gasoline_value_disp;

		$allowance_all = $allowance_all + $allowance;

		//走行距離/時間,超過距離/時間,超過距離/日,超過手当
		$data = get_car_distance_allowance_data_common($car_distance,$work_time,$staff_type,$year,$month,$day);		//カー距離別報酬データ取得
		$car_distance_over_allowance = $data["car_distance_over_allowance"];

		if( $staff_area == "tokyo" ){

			//超過手当はゼロ
			$car_distance_over_allowance = 0;

		}

		$car_distance_over_allowance_all = $car_distance_over_allowance_all + $car_distance_over_allowance;

		//チーフ手当
		$chief_allowance_all = $chief_allowance_all + $chief_allowance;

	}

	$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

	if( $result == false ){

		$remuneration_all = 0;

	}

	$cost_data = get_cost_data_day_from_attendance_staff_new_common($year,$month,$day,$area);	//同一エリア内の出勤情報（スタッフ）配列取得

	$gasoline = $cost_data["gasoline"];
	$highway = $cost_data["highway"];
	$parking = $cost_data["parking"];
	$pay_finish = $cost_data["pay_finish"];

	$gasoline_value_disp_all = $gasoline_value_disp_all + $gasoline;
	$highway_all = $highway;
	$parking_all = $parking;
	$pay_finish_all = $pay_finish;

	//超過手当をガソリン代に含ませる
	$gasoline_value_disp_all = $gasoline_value_disp_all + $car_distance_over_allowance_all;

	//インセンティブをガソリン代に含ませる
	$gasoline_value_disp_all = $gasoline_value_disp_all + $incentive_all;

	//チーフ手当を報酬に含ませる
	$remuneration_all = $remuneration_all + $chief_allowance_all;

	$data["remuneration"] = $remuneration_all;
	$data["gasoline"] = $gasoline_value_disp_all;
	$data["parking"] = $parking_all;
	$data["allowance"] = $allowance_all;
	$data["highway"] = $highway_all;
	$data["pay_finish"] = $pay_finish_all;

	return $data;
	exit();
}

//----移動費用取得
function get_movement_cost_for_sale_shop_common($area,$year,$month,$day){

	$movement_cost = get_movement_cost_value_for_sale_shop_2_common($area,$year,$month,$day);		//移動費用取得

	return $movement_cost;
	exit();

}

//----移動費用取得
function get_movement_cost_value_for_sale_shop_2_common($area,$year,$month,$day){

	$movement_cost_value = get_movement_cost_value_for_sale_shop_common($area,$year,$month,$day);	//移動費用取得

	if( $movement_cost_value > 0 ){

		return $movement_cost_value;
		exit();

	}

	$sql = sprintf("select value from sale_cost where delete_flg=0 and name='movement_cost' and year='%s' and month='%s' and day='%s' and area='%s'",$year,$month,$day,$area);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_movement_cost_value_for_sale_shop_2_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["value"] == "" ){
		return "0";
		exit();
	}else{
		return $row["value"];
		exit();
	}
}

//----移動費用取得
function get_movement_cost_value_for_sale_shop_common($area,$year,$month,$day){

	$sql = sprintf("select cost_value from movement_cost where delete_flg='0' and area='%s' and year='%s' and month='%s' and day='%s'",$area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_movement_cost_value_for_sale_shop_common)";
		exit();
	}

	$cost_value_all = 0;

	while($row = mysql_fetch_assoc($res)){

		$cost_value = $row["cost_value"];

		$cost_value_all = $cost_value_all + $cost_value;

	}

	return $cost_value_all;
	exit();
}

//----粗利計算
function get_gross_profit_for_sale_shop_common(
$genkin_price,$card_price,$therapist_remuneration_sum,$driver_remuneration_sum,$movement_cost,$driver_gasoline,
$driver_parking,$driver_highway,$card_commission,$shop_boss_remuneration,$driver_sonota,$driver_pay_finish){

	//「売上」-「報酬合計(セラピスト)」-「報酬合計(ドライバー)」-「移動実費」-「ガソリン代」-「駐車場代」-「高速代」-
	//「カード手数料」-「店長報酬」-「その他(ドライバー)」+「清算済み(ドライバー)」
	//=(粗利益)

	$sale_price = $genkin_price + $card_price;

	$gross_profit = $sale_price - $therapist_remuneration_sum - $driver_remuneration_sum - $movement_cost -
	$driver_gasoline - $driver_parking - $driver_highway - $card_commission - $shop_boss_remuneration - $driver_sonota +
	$driver_pay_finish;

	return $gross_profit;
	exit();

}

//----売上店舗記録データ有無
function check_data_exist_sale_shop_record_common($year,$month,$day,$shop_id){

	$sql = sprintf("select id from sale_shop_record where delete_flg='0' and year='%s' and month='%s' and day='%s' and shop_id='%s'",$year,$month,$day,$shop_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_data_exist_sale_shop_record_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----売上店舗記録データ登録
function insert_sale_shop_record_common($data,$year,$month,$day,$shop_id){

	$sql = sprintf("
insert into sale_shop_record(
genkin_price,
card_price,
card_commission,
therapist_remuneration,
pay_day_therapist,
therapist_remuneration_mibarai,
allowance_jisou,
allowance_therapist,
driver_remuneration,
driver_gasoline,
driver_parking,
driver_allowance,
driver_highway,
driver_pay_finish,
lowest_guarantee,
therapist_remuneration_sum,
pay_day_driver,
driver_remuneration_mibarai,
driver_remuneration_sum,
movement_cost,
shop_boss_remuneration,
driver_sonota,
gross_profit,
office_remuneration_mibarai,
pay_day_office,
office_allowance,
office_remuneration_sum,
year,
month,
day,
shop_id
)
values(
'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$data["genkin_price"],
$data["card_price"],
$data["card_commission"],
$data["therapist_remuneration"],
$data["pay_day_therapist"],
$data["therapist_remuneration_mibarai"],
$data["allowance_jisou"],
$data["allowance_therapist"],
$data["driver_remuneration"],
$data["driver_gasoline"],
$data["driver_parking"],
$data["driver_allowance"],
$data["driver_highway"],
$data["driver_pay_finish"],
$data["lowest_guarantee"],
$data["therapist_remuneration_sum"],
$data["pay_day_driver"],
$data["driver_remuneration_mibarai"],
$data["driver_remuneration_sum"],
$data["movement_cost"],
$data["shop_boss_remuneration"],
$data["driver_sonota"],
$data["gross_profit"],
$data["office_remuneration_mibarai"],
$data["pay_day_office"],
$data["office_allowance"],
$data["office_remuneration_sum"],
$year,
$month,
$day,
$shop_id
);

	$res = mysql_query($sql, DbCon);

	if($res == false){

		echo "error!(insert_sale_shop_record_common)";
		exit();

	}

	return true;
	exit();
}

//----店舗売上記録データ更新
function update_sale_shop_record_common($data,$year,$month,$day,$shop_id){

	$sql = sprintf("
update sale_shop_record set
genkin_price='%s',
card_price='%s',
card_commission='%s',
therapist_remuneration='%s',
pay_day_therapist='%s',
therapist_remuneration_mibarai='%s',
allowance_jisou='%s',
allowance_therapist='%s',
driver_remuneration='%s',
driver_gasoline='%s',
driver_parking='%s',
driver_allowance='%s',
driver_highway='%s',
driver_pay_finish='%s',
lowest_guarantee='%s',
therapist_remuneration_sum='%s',
pay_day_driver='%s',
driver_remuneration_mibarai='%s',
driver_remuneration_sum='%s',
movement_cost='%s',
shop_boss_remuneration='%s',
driver_sonota='%s',
gross_profit='%s',
office_remuneration_mibarai='%s',
pay_day_office='%s',
office_allowance='%s',
office_remuneration_sum='%s'
where
delete_flg=0 and
year='%s' and
month='%s' and
day='%s' and
shop_id='%s'",
$data["genkin_price"],
$data["card_price"],
$data["card_commission"],
$data["therapist_remuneration"],
$data["pay_day_therapist"],
$data["therapist_remuneration_mibarai"],
$data["allowance_jisou"],
$data["allowance_therapist"],
$data["driver_remuneration"],
$data["driver_gasoline"],
$data["driver_parking"],
$data["driver_allowance"],
$data["driver_highway"],
$data["driver_pay_finish"],
$data["lowest_guarantee"],
$data["therapist_remuneration_sum"],
$data["pay_day_driver"],
$data["driver_remuneration_mibarai"],
$data["driver_remuneration_sum"],
$data["movement_cost"],
$data["shop_boss_remuneration"],
$data["driver_sonota"],
$data["gross_profit"],
$data["office_remuneration_mibarai"],
$data["pay_day_office"],
$data["office_allowance"],
$data["office_remuneration_sum"],
$year,
$month,
$day,
$shop_id);

	//echo $sql;exit();

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(update_sale_shop_record_common)";
		exit();
	}

	return true;
	exit();
}

//----売上店舗記録データ取得
function get_sale_shop_record_common($year,$month,$day,$shop_id){

	$sql = sprintf("select * from sale_shop_record where delete_flg='0' and year='%s' and month='%s' and day='%s' and shop_id='%s'",$year,$month,$day,$shop_id);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql;
	if($res == false){
		echo "error!(get_sale_shop_record_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----意味不明 by aida
function check_sale_not_edit_day_common($year,$month,$day){

	$data = get_today_year_month_day_common();		//本日の年月日取得

	$year_now = $data["year"];
	$month_now = $data["month"];
	$day_now = $data["day"];

	$num = "3";
	$data = get_old_day_common($year_now,$month_now,$day_now,$num);		//指定日前日の指定日数前の年月日取得

	$year_old = $data["year"];
	$month_old = $data["month"];
	$day_old = $data["day"];

	$ts = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$ts_old = get_timestamp_by_year_month_day_common($year_old,$month_old,$day_old);	//指定年月日をタイムスタンプ形式に変換

	if( $ts > $ts_old ){
		$result = true;
	}else{
		$result = false;
	}

	return $result;
	exit();
}

//----店長報酬が店長就任前の時はゼロにする
function get_chief_allowance_staff_common($year,$month,$day,$chief_allowance,$chief_allowance_start_time){

	$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	if( $chief_allowance != "0" ){

		if( ($timestamp < $chief_allowance_start_time) || ($chief_allowance_start_time=="0") ){
			$chief_allowance = 0;
		}

	}

	/*
	echo "timestamp:".$timestamp;echo "<br />";
	echo "chief_allowance_start_time:".$chief_allowance_start_time;echo "<br />";
	echo "chief_allowance:".$chief_allowance;echo "<br />";
	exit();
	*/

	return $chief_allowance;
	exit();
}

//----同一エリアのセラピスト情報配列取得
function get_therapist_list_by_area_common($area){

	$sql = sprintf("select * from therapist_new where leave_flg=0 and test_flg=0 and delete_flg='0' and area='%s'",$area);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_list_by_area_common)";
		exit();
	}

	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----セラピストリポートデータ取得
function get_therapist_report_data_common($therapist_id,$area){

	$data = get_eigyou_day_common();	//営業年月日取得
	$year_now = $data["year"];
	$month_now = $data["month"];
	$day_now = $data["day"];

	$num = 90;
	$data = get_old_day_common($year_now,$month_now,$day_now,$num);		//指定日前日の指定日数前の年月日取得
	$year_old = $data["year"];
	$month_old = $data["month"];
	$day_old = $data["day"];

	$timestamp_old = get_timestamp_by_year_month_day_2_common($year_old,$month_old,$day_old);	//指定年月日をタイムスタンプ形式に変換(0)

	$timestamp_now = get_timestamp_by_year_month_day_3_common($year_now,$month_now,$day_now);	//指定年月日をタイムスタンプ形式に変換(23)

	//リピーター数の取得(セラピスト)
	$type = "therapist";
	$repeater_num = get_repeater_num_kikan_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area);	//リピート数取得

	//施術数、新規数、指名数、取得(セラピスト)
	$type = "therapist";
	$data = get_all_data_for_reportcard_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area);	//指名数等集計値取得

	$all_num = $data["all_num"];
	$new_num = $data["new_num"];
	$shimei_num = $data["shimei_num"];

	$data = get_rate_for_reportcard_common($repeater_num,$new_num,$shimei_num,$all_num);	//リピータ、指名係数等取得

	$repeater_rate = $data["repeater_rate"];
	$shimei_rate = $data["shimei_rate"];

	$repeater_rate_ranking = $repeater_rate * 10;
	$shimei_rate_ranking = $shimei_rate * 10;

	$data["repeater_num"] = $repeater_num;
	$data["all_num"] = $all_num;
	$data["new_num"] = $new_num;
	$data["shimei_num"] = $shimei_num;
	$data["repeater_rate"] = $repeater_rate;
	$data["shimei_rate"] = $shimei_rate;

	$data["repeater_rate_ranking"] = $repeater_rate_ranking;
	$data["shimei_rate_ranking"] = $shimei_rate_ranking;

	return $data;
	exit();
}

//----セラピストリポートデータ取得
function get_therapist_report_data_2_common($therapist_id,$area,$day_num){

	$data = get_eigyou_day_common();	//営業年月日取得
	$year_now = $data["year"];
	$month_now = $data["month"];
	$day_now = $data["day"];

	$num = $day_num;
	$data = get_old_day_common($year_now,$month_now,$day_now,$num);
	$year_old = $data["year"];
	$month_old = $data["month"];
	$day_old = $data["day"];

	$timestamp_old = get_timestamp_by_year_month_day_2_common($year_old,$month_old,$day_old);	//指定年月日をタイムスタンプ形式に変換(0)

	$timestamp_now = get_timestamp_by_year_month_day_3_common($year_now,$month_now,$day_now);	//指定年月日をタイムスタンプ形式に変換(23)

	//リピーター数の取得(セラピスト)
	$type = "therapist";
	$repeater_num = get_repeater_num_kikan_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area);	//リピート数取得

	//施術数、新規数、指名数、取得(セラピスト)
	$type = "therapist";
	$data = get_all_data_for_reportcard_common(
			$year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area);

	$all_num = $data["all_num"];
	$new_num = $data["new_num"];
	$shimei_num = $data["shimei_num"];

	$data = get_rate_for_reportcard_common($repeater_num,$new_num,$shimei_num,$all_num);	//リピータ、指名係数等取得

	$repeater_rate = $data["repeater_rate"];
	$shimei_rate = $data["shimei_rate"];

	$repeater_rate_ranking = $repeater_rate * 10;
	$shimei_rate_ranking = $shimei_rate * 10;

	$data["repeater_num"] = $repeater_num;
	$data["all_num"] = $all_num;
	$data["new_num"] = $new_num;
	$data["shimei_num"] = $shimei_num;
	$data["repeater_rate"] = $repeater_rate;
	$data["shimei_rate"] = $shimei_rate;

	$data["repeater_rate_ranking"] = $repeater_rate_ranking;
	$data["shimei_rate_ranking"] = $shimei_rate_ranking;

	return $data;
	exit();
}

//----リピータ、指名係数等取得
function get_rate_for_reportcard_common($repeater_num,$new_num,$shimei_num,$all_num){

	$repeater_rate = ($repeater_num/$new_num)*100;
	$repeater_rate = kirisute_common($repeater_rate,1);		//切り捨て処理

	$shimei_rate = ($shimei_num/$all_num)*100;
	$shimei_rate = kirisute_common($shimei_rate,1);			//切り捨て処理

	$repeater_rate = add_shousuu_ten_zero_common($repeater_rate);	//小数点以下がなければ　.0を付加
	$shimei_rate = add_shousuu_ten_zero_common($shimei_rate);		//小数点以下がなければ　.0を付加

	$data["repeater_rate"] = $repeater_rate;
	$data["shimei_rate"] = $shimei_rate;
	$data["all_num"] = $all_num;
	$data["new_num"] = $new_num;
	$data["repeater_num"] = $repeater_num;
	$data["shimei_num"] = $shimei_num;

	return $data;
	exit();

}

//----セラピストリポートデータ取得（ランキング）
function get_therapist_report_ranking_common($therapist_list,$type){

	$therapist_list_num = count($therapist_list);

	for($i=0;$i<$therapist_list_num;$i++){

		$therapist_id = $therapist_list[$i]["id"];
		$area = $therapist_list[$i]["area"];

		$data = get_therapist_report_data_common($therapist_id,$area);

		$therapist_list[$i]["repeater_rate"] = $data["repeater_rate"];
		$therapist_list[$i]["shimei_rate"] = $data["shimei_rate"];
		$therapist_list[$i]["repeater_num"] = $data["repeater_num"];
		$therapist_list[$i]["all_num"] = $data["all_num"];
		$therapist_list[$i]["new_num"] = $data["new_num"];
		$therapist_list[$i]["shimei_num"] = $data["shimei_num"];
		$therapist_list[$i]["repeater_rate_ranking"] = $data["repeater_rate_ranking"];
		$therapist_list[$i]["shimei_rate_ranking"] = $data["shimei_rate_ranking"];

	}

	$ranking_name = PHP_get_type_id_name_therapist_report_common($type);	//項目名取得(記号) insert by aida

	foreach($therapist_list as $key => $row){
		$key_data[$key] = $row[$ranking_name];
	}
	array_multisort($key_data,SORT_DESC,$therapist_list);

	return $therapist_list;
	exit();
}

//----セラピストリポートデータ取得２（ランキング）
function get_therapist_report_ranking_2_common($therapist_list,$type,$day_num){

	$therapist_list_num = count($therapist_list);

	for($i=0;$i<$therapist_list_num;$i++){

		$therapist_id = $therapist_list[$i]["id"];
		$area = $therapist_list[$i]["area"];

		$data = get_therapist_report_data_2_common($therapist_id,$area,$day_num);

		$therapist_list[$i]["repeater_rate"] = $data["repeater_rate"];
		$therapist_list[$i]["shimei_rate"] = $data["shimei_rate"];
		$therapist_list[$i]["repeater_num"] = $data["repeater_num"];
		$therapist_list[$i]["all_num"] = $data["all_num"];
		$therapist_list[$i]["new_num"] = $data["new_num"];
		$therapist_list[$i]["shimei_num"] = $data["shimei_num"];
		$therapist_list[$i]["repeater_rate_ranking"] = $data["repeater_rate_ranking"];
		$therapist_list[$i]["shimei_rate_ranking"] = $data["shimei_rate_ranking"];

	}

	$ranking_name = PHP_get_type_id_name_therapist_report_common($type);	//項目名取得(記号)

	foreach($therapist_list as $key => $row){
		$key_data[$key] = $row[$ranking_name];
	}
	array_multisort($key_data,SORT_DESC,$therapist_list);

	return $therapist_list;
	exit();
}

//----セラピストリポートデータ取得
function get_therapist_report_all_common($area){

	$data = get_eigyou_day_common();	//営業年月日取得
	$year_now = $data["year"];
	$month_now = $data["month"];
	$day_now = $data["day"];

	$num = 90;
	$data = get_old_day_common($year_now,$month_now,$day_now,$num);		//指定日前日の指定日数前の年月日取得
	$year_old = $data["year"];
	$month_old = $data["month"];
	$day_old = $data["day"];

	$timestamp_old = get_timestamp_by_year_month_day_2_common($year_old,$month_old,$day_old);	//指定年月日をタイムスタンプ形式に変換(0)

	$timestamp_now = get_timestamp_by_year_month_day_3_common($year_now,$month_now,$day_now);	//指定年月日をタイムスタンプ形式に変換(23)

	//リピーター数の取得(すべて)
	$type = "all";
	$repeater_num = get_repeater_num_kikan_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area);	//リピート数取得

	//施術数、新規数、指名数、取得(すべて)
	$type = "all";
	$data = get_all_data_for_reportcard_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area);

	$all_num = $data["all_num"];
	$new_num = $data["new_num"];
	$shimei_num = $data["shimei_num"];

	$data = get_rate_for_reportcard_common($repeater_num,$new_num,$shimei_num,$all_num);	//リピータ、指名係数等取得

	//$repeater_rate = $data["repeater_rate"];
	//$shimei_rate = $data["shimei_rate"];

	return $data;
	exit();
}

//----セラピストリポートデータ取得２
function get_therapist_report_all_2_common($area,$day_num){

	$data = get_eigyou_day_common();	//営業年月日取得
	$year_now = $data["year"];
	$month_now = $data["month"];
	$day_now = $data["day"];

	$num = $day_num;
	$data = get_old_day_common($year_now,$month_now,$day_now,$num);		//指定日前日の指定日数前の年月日取得
	$year_old = $data["year"];
	$month_old = $data["month"];
	$day_old = $data["day"];

	$timestamp_old = get_timestamp_by_year_month_day_2_common($year_old,$month_old,$day_old);	//指定年月日をタイムスタンプ形式に変換(0)

	$timestamp_now = get_timestamp_by_year_month_day_3_common($year_now,$month_now,$day_now);	//指定年月日をタイムスタンプ形式に変換(23)

	//リピーター数の取得(すべて)
	$type = "all";
	$repeater_num = get_repeater_num_kikan_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area);	//リピート数取得

	//施術数、新規数、指名数、取得(すべて)
	$type = "all";
	$data = get_all_data_for_reportcard_common($year_old,$month_old,$day_old,$year_now,$month_now,$day_now,$type,$therapist_id,$area);	//指名数等集計値取得

	$all_num = $data["all_num"];
	$new_num = $data["new_num"];
	$shimei_num = $data["shimei_num"];

	$data = get_rate_for_reportcard_common($repeater_num,$new_num,$shimei_num,$all_num);	//リピータ、指名係数等取得

	//$repeater_rate = $data["repeater_rate"];
	//$shimei_rate = $data["shimei_rate"];

	return $data;
	exit();
}

//----料金集計
function get_sale_data_day_common($year,$month,$day,$shop_name){

	$ws_SQL = "select B.price,A.new_flg from reservation_for_board A";
	$ws_SQL .= " left join sale_history B on B.reservation_no=A.reservation_no";
	$ws_SQL .= " where A.shop_name='%s' and A.year='%s' and A.month='%s' and A.day='%s' and A.delete_flg=0 and A.complete_flg='1'";
	$ws_SQL .= " and B.delete_flg=0 and B.eigyou_year='%s' and B.eigyou_month='%s' and B.eigyou_day='%s'";
	$sql = sprintf($ws_SQL, $shop_name,$year,$month,$day,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_sale_data_day_common)";
		exit();
	}

	$total = 0;

	$operation_num = 0;
	$operation_num_new = 0;

	while($row = mysql_fetch_assoc($res)){

		$price = $row["price"];
		$new_flg = $row["new_flg"];

		$total = $total + $price;

		if( $new_flg == "1" ){

			$operation_num_new++;

		}

		$operation_num++;

	}

	$data["price"] = $total;
	$data["operation_num"] = $operation_num;
	$data["operation_num_new"] = $operation_num_new;

	return $data;
	exit();
}

//----セラピスト報酬データ有無
function check_data_exist_remuneration_therapist_common($attendance_id){

	$sql = sprintf("select id from remuneration_therapist where delete_flg='0' and attendance_id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_data_exist_remuneration_therapist_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----セラピスト報酬データ取得
function get_remuneration_therapist_by_attendance_id_common($attendance_id){

	$attendance_data = get_attendance_data_one_by_attendance_id_common($attendance_id);		//出席データの取得(セラピスト)

	$therapist_id = $attendance_data["therapist_id"];
	$pay_another = $attendance_data["pay_another"];
	$pay_finish = $attendance_data["pay_finish"];
	$pay_day = $attendance_data["pay_day"];
	$allowance = $attendance_data["allowance"];
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];
	$area = $attendance_data["area"];

	$allowance_jisou = 0;

	$therapist_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得

	$jisou_flg = $therapist_data["jisou_flg"];
	$transport_cost = $therapist_data["transport_cost"];

	if( ( $pay_another == "0" ) && ( $transport_cost != "0" ) ){

		$pay_another = $transport_cost;

		//pay_anotherの更新
		update_pay_another_by_attendance_id_common($pay_another,$attendance_id);	//pay_anotherの更新
	}

	if( $jisou_flg == "1" ){
		$allowance_jisou = get_allowance_therapist_common($year,$month,$day,$attendance_id);	//交通費取得
	}

	$total_point = get_total_point_by_therapist_id_common($therapist_id,$year,$month,$day);		//トータルポイント取得

	$point_data = get_therapist_point_by_attendance_id_common($attendance_id);		//指定出勤データのポイント数取得

	$pt_repeat = $point_data["pt_repeat"];
	$pt_operation = $point_data["pt_operation"];
	$pt_shimei = $point_data["pt_shimei"];

	$kakutoku_point = $pt_repeat + $pt_operation + $pt_shimei;

	$share_rate = get_share_rate_by_attendance_id_common($attendance_id);	//シェア率取得

	$remuneration_data = get_therapist_remuneration_by_attendance_id_common($attendance_id);	//報酬取得

	$remuneration = $remuneration_data["remuneration"];
	$chief_allowance = $remuneration_data["chief_allowance"];
	$lowest_guarantee = $remuneration_data["lowest_guarantee"];
	$lowest_guarantee_flg = $remuneration_data["lowest_guarantee_flg"];

	$sale_price = get_sokuhou_price_area_by_therapist_id_common($year,$month,$day,$area,$therapist_id);			//売上集計（売上履歴データ）

	$sale_price_shijutsu = get_sale_price_shijutsu_common($year,$month,$day,$area,$attendance_id,$sale_price);	//施術料取得（予約状況データ）

	$data["therapist_id"] = $therapist_id;
	$data["attendance_id"] = $attendance_id;
	$data["year"] = $year;
	$data["month"] = $month;
	$data["day"] = $day;
	$data["sale_price"] = $sale_price;
	$data["sale_price_shijutsu"] = $sale_price_shijutsu;
	$data["remuneration"] = $remuneration;
	$data["allowance_jisou"] = $allowance_jisou;
	$data["allowance"] = $allowance;
	$data["pay_another"] = $pay_another;
	$data["pay_finish"] = $pay_finish;
	$data["pay_day"] = $pay_day;
	$data["share_rate"] = $share_rate;
	$data["total_point"] = $total_point;
	$data["kakutoku_point"] = $kakutoku_point;
	$data["pt_operation"] = $pt_operation;
	$data["pt_shimei"] = $pt_shimei;
	$data["pt_repeat"] = $pt_repeat;
	$data["chief_allowance"] = $chief_allowance;
	$data["lowest_guarantee"] = $lowest_guarantee;
	$data["lowest_guarantee_flg"] = $lowest_guarantee_flg;

	return $data;
	exit();
}

//----セラピスト報酬データ登録
function insert_remuneration_therapist_common($data){

	$sql = sprintf("
insert into remuneration_therapist(
therapist_id,attendance_id,year,month,day,
sale_price,sale_price_shijutsu,remuneration,allowance_jisou,allowance,
pay_another,pay_finish,pay_day,share_rate,total_point,
kakutoku_point,pt_operation,pt_shimei,pt_repeat,chief_allowance,
lowest_guarantee,lowest_guarantee_flg)
values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$data["therapist_id"],$data["attendance_id"],$data["year"],$data["month"],$data["day"],
$data["sale_price"],$data["sale_price_shijutsu"],$data["remuneration"],$data["allowance_jisou"],$data["allowance"],
$data["pay_another"],$data["pay_finish"],$data["pay_day"],$data["share_rate"],$data["total_point"],
$data["kakutoku_point"],$data["pt_operation"],$data["pt_shimei"],$data["pt_repeat"],$data["chief_allowance"],
$data["lowest_guarantee"],$data["lowest_guarantee_flg"]);

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(insert_remuneration_therapist_common)";
		exit();
	}

	return true;
	exit();
}

//pay_anotherの更新
function update_pay_another_by_attendance_id_common($pay_another,$attendance_id){

	$sql = sprintf("update attendance_new set pay_another='%s' where id='%s'",$pay_another,$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(update_pay_another_by_attendance_id_common)";
		exit();
	}

	return true;
	exit();
}

//----売上集計（売上履歴データ）
function get_sokuhou_price_area_by_therapist_id_common($year,$month,$day,$area,$therapist_id){

	$sql = sprintf("select price from sale_history where delete_flg=0 and eigyou_year='%s' and eigyou_month='%s' and eigyou_day='%s' and area='%s' and therapist_id='%s'",$year,$month,$day,$area,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_sokuhou_price_area_by_therapist_id_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	$list_data_num = count($list_data);

	$total = 0;

	for($i=0;$i<$list_data_num;$i++){

		$price = $list_data[$i]["price"];

		$total = $total + $price;

	}

	return $total;
	exit();

}

//----施術料取得（予約状況データ）
function get_sale_price_shijutsu_common($year,$month,$day,$area,$attendance_id,$sale_price){

	$sql = sprintf("select shimei_flg,transportation from reservation_for_board where delete_flg=0 and year='%s' and month='%s' and day='%s' and shop_area='%s' and attendance_id='%s'",$year,$month,$day,$area,$attendance_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_sale_price_shijutsu_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$list_data[$i++] = $row;

	}

	$list_data_num = count($list_data);

	$sale_price_shijutsu = $sale_price;

	for($i=0;$i<$list_data_num;$i++){

		$shimei_flg = $list_data[$i]["shimei_flg"];
		$transportation = $list_data[$i]["transportation"];

		if( $shimei_flg == "1" ){
			$shimei_value = 1000;
		}else{
			$shimei_value = 0;
		}

		$sale_price_shijutsu = $sale_price_shijutsu - $shimei_value - $transportation;

	}

	return $sale_price_shijutsu;
	exit();
}

//----セラピスト報酬データ取得
function get_remuneration_therapist_by_day_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select * from remuneration_therapist where delete_flg=0 and year='%s' and month='%s' and day='%s' and therapist_id='%s'",$year,$month,$day,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_remuneration_therapist_by_day_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----スタッフ報酬データ有無
function check_data_exist_remuneration_staff_common($attendance_id){

	$sql = sprintf("select id from remuneration_staff where delete_flg='0' and attendance_id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_data_exist_remuneration_staff_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----報酬額明細取得
function get_remuneration_staff_all_by_attendance_id_common($attendance_id){

	//これは全部取ってる
	$attendance_data = get_attendance_staff_new_data_by_attendance_id_common($attendance_id);	//出勤データ（スタッフ）取得

	$staff_id = $attendance_data["staff_id"];
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];
	$area = $attendance_data["area"];
	$type = $attendance_data["type"];
	$start_time = $attendance_data["start_time"];
	$end_time = $attendance_data["end_time"];
	$start_hour = $attendance_data["start_hour"];
	$start_minute = $attendance_data["start_minute"];
	$end_hour = $attendance_data["end_hour"];
	$end_minute = $attendance_data["end_minute"];
	$allowance = $attendance_data["allowance"];
	$pay_day = $attendance_data["pay_day"];
	$car_distance = $attendance_data["car_distance"];
	$highway = $attendance_data["highway"];
	$parking = $attendance_data["parking"];
	$pay_finish = $attendance_data["pay_finish"];

	$staff_data = get_staff_data_by_id_common($staff_id);	//スタッフ情報取得

	$chief_allowance = $staff_data["chief_allowance"];
	$chief_allowance_start_time = $staff_data["chief_allowance_start_time"];

	$chief_allowance = get_chief_allowance_staff_common($year,$month,$day,$chief_allowance,$chief_allowance_start_time);	//店長報酬が店長就任前の時はゼロにする

	//データのアップデート(最初のアクセス時だけ)と、データの取得
	$data = get_and_update_driver_work_time_common($attendance_id,$start_hour,$start_minute,$end_hour,$end_minute,$start_time,$end_time);	//データのアップデート(最初のアクセス時だけ)と、データの取得

	$start_hour = $data["start_hour"];
	$start_minute = $data["start_minute"];
	$end_hour = $data["end_hour"];
	$end_minute = $data["end_minute"];

	$work_time = get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute);	//時間集計

	//これも全部取ってる
	$staff_data = get_staff_data_by_id_common($staff_id);	//スタッフ情報取得

	$pay_hour = $staff_data["pay_hour"];
	$pay_fix = $staff_data["pay_fix"];
	$fuel = $staff_data["fuel"];

	$gasoline_value = get_gasoline_value_by_id_and_time_common($attendance_id);		//ガソリン代取得
	if( ($fuel != "0") && ($fuel != "-1") ){
		$gasoline_value = intval(($car_distance*$gasoline_value)/$fuel);
	}else{
		$gasoline_value = 0;
	}
	$gasoline_value_disp = $gasoline_value;

	$remuneration = get_remuneration_staff_by_attendance_id_common($attendance_id);	//報酬取得

	$staff_type = $type;

	//超過手当
	$data = get_car_distance_allowance_data_common($car_distance,$work_time,$staff_type,$year,$month,$day);		//カー距離別報酬データ取得
	$car_distance_over_allowance = $data["car_distance_over_allowance"];

	if( $remuneration != "0" ){

		$other_data["car_distance"] = $car_distance;

		//----報酬金額集計
		$tmp = get_remuneration_and_sum_price_2_common($remuneration,$chief_allowance,$gasoline_value_disp,$highway,$parking,$allowance,$pay_finish,
			$pay_day,$car_distance_over_allowance,$staff_id,$year,$month,$day,$other_data);

		$remuneration = $tmp["remuneration"];
		$sum_price = $tmp["sum_price"];

	}

	$data["staff_id"] = $staff_id;
	$data["attendance_id"] = $attendance_id;
	$data["year"] = $year;
	$data["month"] = $month;
	$data["day"] = $day;
	$data["area"] = $area;
	$data["type"] = $type;
	$data["pay_hour"] = $pay_hour;
	$data["pay_fix"] = $pay_fix;
	$data["start_time"] = $start_time;
	$data["end_time"] = $end_time;
	$data["start_hour"] = $start_hour;
	$data["start_minute"] = $start_minute;
	$data["end_hour"] = $end_hour;
	$data["end_minute"] = $end_minute;
	$data["work_time"] = $work_time;
	$data["allowance"] = $allowance;
	$data["remuneration"] = $remuneration;
	$data["chief_allowance"] = $chief_allowance;
	$data["pay_day"] = $pay_day;
	$data["car_distance"] = $car_distance;
	$data["fuel"] = $fuel;
	$data["gasoline"] = $gasoline_value;
	$data["highway"] = $highway;
	$data["parking"] = $parking;
	$data["pay_finish"] = $pay_finish;
	$data["car_distance_over_allowance"] = $car_distance_over_allowance;
	$data["sum_price"] = $sum_price;

	return $data;
	exit();
}

//----スタッフ報酬データ登録
function insert_remuneration_staff_common($data){

	$sql = sprintf("
insert into remuneration_staff(
staff_id,
attendance_id,
year,
month,
day,
area,
type,
pay_hour,
pay_fix,
start_time,
end_time,
start_hour,
start_minute,
end_hour,
end_minute,
work_time,
allowance,
remuneration,
chief_allowance,
pay_day,
car_distance,
fuel,
gasoline,
highway,
parking,
pay_finish,
car_distance_over_allowance,
sum_price
)
values(
'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
'%s','%s','%s','%s','%s','%s','%s','%s')",
$data["staff_id"],
$data["attendance_id"],
$data["year"],
$data["month"],
$data["day"],
$data["area"],
$data["type"],
$data["pay_hour"],
$data["pay_fix"],
$data["start_time"],
$data["end_time"],
$data["start_hour"],
$data["start_minute"],
$data["end_hour"],
$data["end_minute"],
$data["work_time"],
$data["allowance"],
$data["remuneration"],
$data["chief_allowance"],
$data["pay_day"],
$data["car_distance"],
$data["fuel"],
$data["gasoline"],
$data["highway"],
$data["parking"],
$data["pay_finish"],
$data["car_distance_over_allowance"],
$data["sum_price"]);

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(insert_remuneration_staff_common)";
		exit();
	}

	return true;
	exit();
}

//----スタッフ報酬データ取得
function get_remuneration_staff_by_day_common($staff_id,$year,$month,$day){

	$sql = sprintf("select * from remuneration_staff where delete_flg=0 and year='%s' and month='%s' and day='%s' and staff_id='%s'",$year,$month,$day,$staff_id);

	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_remuneration_staff_by_day_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----店舗売上記録データ更新
function update_sale_shop_record_at_sale_shop_common($year,$month,$day){

	$shop_data = get_shop_data_all_common();	//店舗情報配列取得
	$shop_data_num = count($shop_data);

	for($z=0;$z<$shop_data_num;$z++){

		$shop_id = $shop_data[$z]["id"];
		$shop_name = $shop_data[$z]["name"];
		$shop_area = $shop_data[$z]["area"];

		$data = get_sale_shop_data_day_2_common($year,$month,$day,$shop_name,$shop_area);	//売上店舗記録データ取得サブ

		update_sale_shop_record_common($data,$year,$month,$day,$shop_id);		//店舗売上記録データ更新

	}

	return true;
	exit();
}

//----売上店舗記録データ取得
function get_sale_shop_data_day_common($year,$month,$day,$shop_name,$shop_area){

	$shop_id = get_shop_id_by_shop_name_common($shop_name);		//店舗のID取得

	$data = get_sale_shop_record_common($year,$month,$day,$shop_id);	//売上店舗記録データ取得
	if( $data["id"] != "" ){
		return $data;
		exit();
	}
	/*echo "<pre>";
	print_r($data);
	echo "</pre>";
	//exit(); */

	$data = get_sale_shop_data_day_2_common($year,$month,$day,$shop_name,$shop_area);	//売上店舗記録データ取得サブ in common/include/shop_area_list.php
	/*
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	exit();
	*/

	return $data;
	exit();
}

//本日出勤かどうかのチェック
function therapist_attendance_check_2_common($therapist_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_new_small where therapist_id='%s' and year='%s' and month='%s' and day='%s' and today_absence='0' and kekkin_flg='0' and syounin_state='1'",$therapist_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(therapist_attendance_check_2_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----顧客パスワード取得
function get_customer_password_by_id_common($customer_id){

	$sql = sprintf("select password from customer where delete_flag=0 and customer_id='%s'",$customer_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_customer_password_by_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["password"];
	exit();
}

//----顧客パスワード更新
function update_customer_password_common($pw,$customer_id){

	$pw = mysql_real_escape_string($pw);
	$customer_id = mysql_real_escape_string($customer_id);

	$sql = sprintf("update customer set password='%s' where customer_id='%s'",$pw,$customer_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(update_customer_password_common)";
		exit();
	}

	return true;
	exit();
}

//----顧客認証機能
function check_match_customer_password_common($pw,$customer_id){

	$pw = mysql_real_escape_string($pw);
	$customer_id = mysql_real_escape_string($customer_id);

	$sql = sprintf("select id from customer where delete_flag=0 and password='%s' and customer_id='%s'",$pw,$customer_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_match_customer_password_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----顧客ID取得（TELより）
function get_customer_id_by_tel_common($tel){

	$tel = str_replace("-","",$tel);

	$sql = sprintf("select customer_id from customer where delete_flag=0 and tel='%s'",$tel);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_customer_id_by_tel_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();
}

function get_disp_data_for_vip_history_by_list_data_common($selected_num,$disp_num,$data){

	$data_num = count($data);

	$start_num = ($selected_num-1)*$disp_num;

	$end_num = $start_num + $disp_num;

	if( $end_num > $data_num ){
		$end_num = $data_num;
	}

	$return_data = array();
	$x = 0;

	for( $i=$start_num; $i<$end_num; $i++ ){

		$reservation_for_board_id = $data[$i]["id"];
		$attendance_id = $data[$i]["attendance_id"];

		$year = $data[$i]["year"];
		$month = $data[$i]["month"];
		$day = $data[$i]["day"];
		$rsv_date = $year*10000+$month*100+$day;
		$start_hour = $data[$i]["start_hour"];
		$reservation_no = $data[$i]["reservation_no"];
		$customer_id = $data[$i]["customer_id"];

		$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)

		if( $start_hour >= 24 ){

			$start_hour = $start_hour - 24;

			$tmp = get_mirai_day_common($year, $month, $day, 1);	//指定した日にち分、未来の日付取得

			$year = $tmp["year"];
			$month = $tmp["month"];
			$day = $tmp["day"];
		}

		$week_name = get_week_name_by_time_common($year, $month, $day);	//日付より曜日取得

		$disp_day = sprintf("%s年%s月%s日(%s)　%s時",$year,$month,$day,$week_name,$start_hour);

		$disp_day_2 = sprintf("%s月%s日(%s)　%s時",$month,$day,$week_name,$start_hour);

		$tmp = get_therapist_data_by_attendance_id_common($attendance_id);		//出勤データのセラピスト情報取得

		$therapist_name = $tmp["name_site"];
		$therapist_id = $tmp["id"];

		$therapist_img_url = get_img_url_by_therapist_id_common($therapist_id);	//セラピスト頁情報から画像URL取得

		$result = check_customer_clip_common($customer_id,$therapist_id);		//顧客クリップデータ有無

		if( $result == true ){

			$clip_exist_flg = 1;

		}else{

			$clip_exist_flg = 0;

		}

		$result = check_exist_black_list_common($therapist_id, $customer_id);	//ブラックリストデータ有無

		if( $result == true ){
			$black_flg = 1;
		}else{
			$black_flg = 0;
		}

		$customer_evaluation = get_customer_evaluation_common($reservation_for_board_id);	//顧客評価情報取得
		$customer_voice = get_customer_voice_common($reservation_for_board_id);				//感想
		$therapist_thanks = get_therapist_thanks_common($reservation_for_board_id);			//セラピスト感謝データ取得

		$return_data[$x] = $data[$i];
		$return_data[$x]["rsv_date"] = $rsv_date;
		$return_data[$x]["therapist_name"] = $therapist_name;
		$return_data[$x]["therapist_id"] = $therapist_id;
		$return_data[$x]["disp_day"] = $disp_day;
		$return_data[$x]["disp_day_2"] = $disp_day_2;
		$return_data[$x]["price"] = $price;
		$return_data[$x]["clip_exist_flg"] = $clip_exist_flg;
		$return_data[$x]["customer_evaluation"] = $customer_evaluation;
		$return_data[$x]["customer_voice"] = $customer_voice;
		$return_data[$x]["therapist_thanks"] = $therapist_thanks;
		$return_data[$x]["therapist_img_url"] = $therapist_img_url;
		$return_data[$x]["black_flg"] = $black_flg;

		$x++;

	}

	return $return_data;
	exit();
}

//----出勤データのセラピスト情報取得
function get_therapist_data_by_attendance_id_common($attendance_id){

	$therapist_id = get_therapist_id_by_attendance_id_common($attendance_id);	//出勤データIDからセラピストIDを取得

	$data = get_therapist_data_by_id_common($therapist_id);		//セラピスト情報取得

	return $data;
	exit();
}

//----顧客クリップデータ取得
function get_therapist_data_for_vip_clip_common($customer_id){

	$sql = sprintf("select * from customer_clip where delete_flg=0 and customer_id='%s'",$customer_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_data_for_vip_clip_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$therapist_id = $row["therapist_id"];

		$result = check_exist_black_list_common($therapist_id, $customer_id);	//ブラックリストデータ有無

		if( $result == true ){

			$black_flg = "1";
		}else{
			$black_flg = "0";
		}

		$list_data[$i] = $row;
		$list_data[$i]["black_flg"] = $black_flg;

		$i++;
	}

	return $list_data;
	exit();
}

//----顧客クリップデータ登録
function insert_customer_clip_common($customer_id,$therapist_id){

	$sql = sprintf("insert into customer_clip(customer_id,therapist_id) values('%s','%s')",$customer_id,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res === false){
		echo "error!(insert_customer_clip_common)";
		exit();
	}
//echo "function.php line:" . __LINE__ . " " . $res . "/" . $sql . "<br />";
//exit;
	return true;
	exit();
}

//----顧客クリップデータ更新
function delete_customer_clip_common($customer_id,$therapist_id){

	$sql = sprintf("update customer_clip set delete_flg='1' where customer_id='%s' and therapist_id='%s'",$customer_id,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(delete_customer_clip_common)";
		exit();
	}

	return true;
	exit();
}

//----顧客クリップデータ有無
function check_customer_clip_common($customer_id,$therapist_id){

	$sql = sprintf("select id from customer_clip where delete_flg=0 and customer_id='%s' and therapist_id='%s'",$customer_id,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_customer_clip_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----顧客クリップデータ有無
function check_exist_customer_clip_common($customer_id,$therapist_id){
	return check_customer_clip_common($customer_id,$therapist_id);	//顧客クリップデータ有無
}

//----顧客評価情報取得
function get_customer_evaluation_common($reservation_for_board_id){

	$sql = sprintf("select * from customer_evaluation where delete_flg=0 and reservation_for_board_id='%s'", $reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_customer_evaluation_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);
	return $row;
	exit();
}

//----顧客評価情報有無
function check_exist_customer_evaluation_common($customer_id,$reservation_for_board_id){

	$sql = sprintf("select id from customer_evaluation where delete_flg=0 and customer_id='%s' and reservation_for_board_id='%s'",$customer_id,$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_customer_evaluation_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----顧客評価情報登録
function insert_customer_evaluation_common($customer_id,$reservation_for_board_id,$skill,$service,$publish_allow_therapist){

	$customer_id = mysql_real_escape_string($customer_id);
	$reservation_for_board_id = mysql_real_escape_string($reservation_for_board_id);
	$skill = mysql_real_escape_string($skill);
	$service = mysql_real_escape_string($service);
	$publish_allow_therapist = mysql_real_escape_string($publish_allow_therapist);

	$ws_SQL = "insert into customer_evaluation(customer_id,reservation_for_board_id,skill,service,publish_allow_therapist)";
	$ws_SQL .= " values('%s','%s','%s','%s','%s')";
	$sql = sprintf($ws_SQL, $customer_id,$reservation_for_board_id,$skill,$service,$publish_allow_therapist);
	$res = mysql_query($sql, DbCon);
	if($res === false){
		echo "error!(insert_customer_evaluation_common)";
		exit();
	}

	return true;
	exit();
}

//----顧客の声データ取得
function get_customer_voice_common($reservation_for_board_id){

	$sql = sprintf("select * from customer_voice where delete_flg=0 and reservation_for_board_id='%s'",$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_customer_voice_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----顧客の声データ有無
function check_exist_customer_voice_common($customer_id,$reservation_for_board_id){

	$sql = sprintf("select id from customer_voice where delete_flg=0 and customer_id='%s' and reservation_for_board_id='%s'",$customer_id,$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_customer_voice_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----顧客の声データ登録
function insert_customer_voice_common($reservation_for_board_id,$content,$publish_allow_therapist,$publish_allow_site,$customer_id){

	$reservation_for_board_id = mysql_real_escape_string($reservation_for_board_id);
	$voice_content = mysql_real_escape_string($voice_content);
	$publish_allow_therapist = mysql_real_escape_string($publish_allow_therapist);
	$publish_allow_site = mysql_real_escape_string($publish_allow_site);
	$customer_id = mysql_real_escape_string($customer_id);

	$ws_SQL = "insert into customer_voice(reservation_for_board_id,content,publish_allow_therapist,publish_allow_site,customer_id)";
	$ws_SQL .= " values('%s','%s','%s','%s','%s')";
	$sql = sprintf($ws_SQL, $reservation_for_board_id,$content,$publish_allow_therapist,$publish_allow_site,$customer_id);
	$res = mysql_query($sql, DbCon);
	if($res === false){
		echo "error!(insert_customer_voice_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピスト感謝データ取得
function get_therapist_thanks_common($reservation_for_board_id){

	$sql = sprintf("select * from therapist_thanks where delete_flg=0 and reservation_for_board_id='%s'",$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_therapist_thanks_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----セラピスト感謝データ有無
function check_exist_therapist_thanks_by_reservation_for_board_id_common($reservation_for_board_id){

	$sql = sprintf("select id from therapist_thanks where delete_flg=0 and reservation_for_board_id='%s'", $reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_therapist_thanks_by_reservation_for_board_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----セラピスト感謝データ登録
function insert_therapist_thanks_common($reservation_for_board_id,$content,$therapist_id){

	$sql = sprintf("insert into therapist_thanks(reservation_for_board_id,content,therapist_id) values('%s','%s','%s')",$reservation_for_board_id,$content,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res === false){
		echo "error!(insert_therapist_thanks_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピスト感謝データ変更
function update_therapist_thanks_common($thanks_content,$therapist_thanks_id){

	$sql = sprintf("update therapist_thanks set content='%s' where id='%s'",$thanks_content,$therapist_thanks_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(update_therapist_thanks_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピストヒアリングデータ取得
function get_therapist_hearing_common($reservation_for_board_id){

	$sql = sprintf("select * from therapist_hearing where delete_flg=0 and reservation_for_board_id='%s'", $reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_therapist_hearing_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----セラピストヒアリングデータ取得
function get_customer_history_hearing_common($reservation_for_board_id){
	return get_therapist_hearing_common($reservation_for_board_id);		//セラピストヒアリングデータ取得
}

//----セラピストヒアリングデータ有無
function check_exist_therapist_hearing_common($reservation_for_board_id,$therapist_id){

	$sql = sprintf("select id from therapist_hearing where delete_flg=0 and reservation_for_board_id='%s' and therapist_id='%s'", $reservation_for_board_id,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_therapist_hearing_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----セラピストヒアリングデータ登録
function insert_therapist_hearing_common($reservation_for_board_id,$content,$therapist_id,$customer_tel,$customer_id){

	$sql = sprintf("insert into therapist_hearing(reservation_for_board_id,content,therapist_id,customer_tel,customer_id) values('%s','%s','%s','%s','%s')",$reservation_for_board_id,$content,$therapist_id,$customer_tel,$customer_id);
	$res = mysql_query($sql, DbCon);
	if($res === false){
		echo "error!(insert_therapist_hearing_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピストヒアリングデータ更新
function update_therapist_hearing_common($thanks_content,$customer_id,$therapist_thanks_id){

	$sql = sprintf("update therapist_hearing set content='%s', customer_id='%s' where id='%s'",$thanks_content,$customer_id,$therapist_thanks_id);

	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(update_therapist_hearing_common)";
		exit();
	}

	return true;
	exit();
}

//----お客様の声表示用データ取得
function get_disp_data_for_vip_voice_common($data){

	$attendance_id = $data["attendance_id"];
	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];
	$start_hour = $data["start_hour"];
	$start_minute = $data["start_minute"];
	$end_hour = $data["end_hour"];
	$end_minute = $data["end_minute"];
	$reservation_no = $data["reservation_no"];

	$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)
	/*
	if( $start_hour >= 24 ){

		$start_hour = $start_hour - 24;

		$tmp = get_mirai_day_common($year, $month, $day, 1);	//指定した日にち分、未来の日付取得

		$year = $tmp["year"];
		$month = $tmp["month"];
		$day = $tmp["day"];

	}
	*/
	$week_name = get_week_name_by_time_common($year, $month, $day);	//日付より曜日取得

	$disp_day = sprintf("%s年%s月%s日(%s)　%s:%02d-%s:%02d",$year,$month,$day,$week_name,$start_hour,$start_minute,$end_hour,$end_minute);

	$tmp = get_therapist_data_by_attendance_id_common($attendance_id);	//出勤データのセラピスト情報取得

	$therapist_name = $tmp["name_site"];

	$return_data = $data;
	$return_data["disp_day"] = $disp_day;
	$return_data["price"] = $price;
	$return_data["therapist_name"] = $therapist_name;

	return $return_data;
	exit();
}

//----該当出勤データのセラピスト名取得
function get_therapist_name_real_by_attendance_id_common($attendance_id){

	$sql = sprintf("select therapist_new.name from attendance_new left join therapist_new on therapist_new.id=attendance_new.therapist_id where attendance_new.id='%s'",$attendance_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_name_real_by_attendance_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["name"];
	exit();
}

//----シフト用顧客の声URL取得
function get_therapist_check_url_for_vip_voice_common($therapist_id,$therapist_area,$for_kobetsu_url){

	$check_url = sprintf("%sshift/customer_voice.php?area=%s&id=%s&ch=%s",REFLE_WWW_URL,$therapist_area,$therapist_id,$for_kobetsu_url);

	return $check_url;
	exit();
}

//----シフト用顧客の声ヘッダ用データ取得
function get_top_disp_for_shift_customer_voice_common($data){

	$data_num = count($data);

	$all = 0;
	$all_num = 0;
	$skill_all = 0;
	$service_all = 0;

	for( $i=0; $i<$data_num; $i++ ){

		$reservation_for_board_id = $data[$i]["id"];

		$tmp = get_customer_evaluation_common($reservation_for_board_id);	//顧客評価情報取得

		$skill = $tmp["skill"];
		$service = $tmp["service"];

		if( $skill != "" ){

			$skill = if_mainasu_is_zero_common($skill);			//-1の時ゼロを返す
			$service = if_mainasu_is_zero_common($service);		//-1の時ゼロを返す

			$all_num++;

			$skill_all = $skill_all + $skill;
			$service_all = $service_all + $service;
		}
	}

	if( $all_num >= 3 ){
		$skill_rate = get_rate_value_common($all_num,$skill_all);		//率計算
		$service_rate = get_rate_value_common($all_num,$service_all);	//率計算
	}else{
		$skill_rate = -1;
		$service_rate = -1;
	}

	$return_data["skill_rate"] = $skill_rate;
	$return_data["service_rate"] = $service_rate;
	$return_data["all_num"] = $all_num;

	return $return_data;
	exit();
}

//----セラピストヒアリングデータ有無
function check_exist_therapist_thanks_common($reservation_for_board_id,$therapist_id){

	$sql = sprintf("select id from therapist_thanks where delete_flg=0 and reservation_for_board_id='%s' and therapist_id='%s'", $reservation_for_board_id,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_therapist_thanks_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----顧客クリップ用データ取得
function get_disp_data_for_vip_clip_common($selected_num,$disp_num,$data,$customer_id){

	$data_num = count($data);

	$start_num = ($selected_num-1)*$disp_num;

	$end_num = $start_num + $disp_num;

	if( $end_num > $data_num ){

		$end_num = $data_num;

	}

	$return_data = array();
	$x = 0;

	$tmp = get_today_year_month_day_2_common();		//本日の年月日取得2

	$year = $tmp["year"];
	$month = $tmp["month"];
	$day = $tmp["day"];

	$evaluation_data_all = get_customer_evaluation_by_customer_id_common($customer_id);		//顧客評価取得

	$evaluation_data_all = add_therapist_id_customer_evaluation_data_common($evaluation_data_all);	//顧客評価情報からからセラピストID取得

	/*
	echo "<pre>";
	print_r($evaluation_data);
	echo "</pre>";
	exit();
	*/


	for( $i=$start_num; $i<$end_num; $i++ ){

		$clip_id = $data[$i]["id"];
		$customer_id = $data[$i]["customer_id"];
		$therapist_id = $data[$i]["therapist_id"];

		$therapist_img_url = get_img_url_m_by_therapist_id_common($therapist_id);		//セラピスト頁情報から携帯用の画像URL取得

		$therapist_name = get_therapist_name_site_by_therapist_id_common($therapist_id);	//セラピスト名取得

		$result = check_exist_attendance_new_common($therapist_id,$year,$month,$day);	//出勤データ有無(セラピスト)

		if( $result == false ){

			$attendance_flg = "0";

		}else{

			$attendance_flg = "1";

		}

		$evaluation_data = get_evaluation_data_for_vip_clip_disp_one_common($therapist_id,$evaluation_data_all);	//顧客評価データ配列から指定セラピスト分抜き出し

		$return_data[$x] = $data[$i];
		$return_data[$x]["therapist_img_url"] = $therapist_img_url;
		$return_data[$x]["therapist_name"] = $therapist_name;
		$return_data[$x]["attendance_flg"] = $attendance_flg;
		$return_data[$x]["evaluation_data"] = $evaluation_data;

		$x++;
	}

	return $return_data;
	exit();
}

//----顧客評価取得
function get_customer_evaluation_by_customer_id_common($customer_id){

	$sql = sprintf("select * from customer_evaluation where delete_flg=0 and customer_id='%s'",$customer_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_customer_evaluation_by_customer_id_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----顧客評価情報からからセラピストID取得
function add_therapist_id_customer_evaluation_data_common($data){

	$data_num = count($data);

	for( $i=0; $i<$data_num; $i++){

		$reservation_for_board_id = $data[$i]["reservation_for_board_id"];

		if( $reservation_for_board_id == "" ){

			$therapist_id = "-1";

		}else{

			$therapist_id = get_therapist_id_by_reservation_for_board_id_common($reservation_for_board_id);	//予約状況IDからセラピストID取得

		}

		$data[$i]["therapist_id"] = $therapist_id;

	}

	return $data;
	exit();
}

//----予約状況IDからセラピストID取得
function get_therapist_id_by_reservation_for_board_id_common($reservation_for_board_id){

	$attendance_id = get_attendance_id_by_reservation_for_board_id_common($reservation_for_board_id);	//予約IDから出勤データID取得

	$therapist_id = get_therapist_id_by_attendance_id_common($attendance_id);	//出勤データIDからセラピストIDを取得

	return $therapist_id;
	exit();

}

//----顧客評価データ配列から指定セラピスト分抜き出し
function get_evaluation_data_for_vip_clip_disp_one_common($therapist_id,$data){

	$data_num = count($data);

	for( $i=0; $i<$data_num; $i++){

		$therapist_id_tmp = $data[$i]["therapist_id"];

		if( $therapist_id_tmp == $therapist_id ){
			return $data[$i];
			exit();
		}
	}

	return false;
	exit();
}

//----出勤データ取得
function get_attendance_data_for_clip_calendar_common($therapist_id){

	$tmp = get_today_year_month_day_2_common();		//本日の年月日取得2

	$year_now = $tmp["year"];
	$month_now = $tmp["month"];
	$day_now = $tmp["day"];

	$return_data = array();
	$x = 0;

	for( $i=0; $i<30; $i++ ){

		$tmp = get_mirai_day_common($year_now, $month_now, $day_now, $i);	//指定した日にち分、未来の日付取得

		$year_tmp = $tmp["year"];
		$month_tmp = $tmp["month"];
		$day_tmp = $tmp["day"];

		$week_name = get_week_name_by_time_common($year_tmp, $month_tmp, $day_tmp);	//日付より曜日取得

		$attendance_data = get_attendance_data_work_common($therapist_id, $year_tmp, $month_tmp, $day_tmp);		//出勤データ取得(承認済シンプル)

		if( $attendance_data["id"] != "" ){

			$attendance_id = $attendance_data["id"];

			$return_data[$x] = $attendance_data;
			$return_data[$x]["week_name"] = $week_name;

			$reservation = get_reservation_new_by_attendance_id_common($attendance_id);	//予約データ取得
			$reservation_num = count($reservation);

			for( $y=0; $y<$reservation_num; $y++ ){
				$return_data[$x]["time"][$y] = $reservation[$y]["time"];
			}

			$time_num = count($return_data[$x]["time"]);
			$return_data[$x]["time_num"] = $time_num;

			$x++;
		}
	}

	return $return_data;
	exit();
}

//----顧客クリップ有無フラグ配列取得
function add_clip_flg_for_vip_therapist_calendar_common($data,$customer_id){

	$attendance_data = $data["attendance_data"];

	$attendance_data_num = count($attendance_data);

	for( $i=0; $i<$attendance_data_num; $i++ ){

		$therapist_id = $attendance_data[$i]["therapist_id"];

		$result = check_exist_customer_clip_common($customer_id, $therapist_id);	//顧客クリップデータ有無

		if( $result == true ){
			$clip_flg = "1";
		}else{
			$clip_flg = "0";
		}
		$attendance_data[$i]["clip_flg"] = $clip_flg;
	}

	$data["attendance_data"] = $attendance_data;

	return $data;
	exit();
}

//----全件セラピスト情報配列取得
function get_therapist_data_all_common(){

	$sql = "select * from therapist_new where leave_flg='0' and test_flg='0' and delete_flg='0'";
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_therapist_data_all_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----ブラックリストデータ有無
function check_exist_black_list_common($therapist_id,$customer_id){

	$sql = sprintf("select id from black_list where delete_flg=0 and customer_id='%s' and therapist_id='%s'", $customer_id,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_black_list_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----ブラックリストでない顧客の出勤データ配列取得
function if_black_is_not_disp_for_vip_therapist_calendar_common($data,$customer_id){

	$attendance_data = $data["attendance_data"];

	$attendance_data_num = count($attendance_data);

	$return_data = array();

	$x = 0;

	for( $i=0; $i<$attendance_data_num; $i++ ){

		$therapist_id = $attendance_data[$i]["therapist_id"];

		$result = check_exist_black_list_common($therapist_id, $customer_id);	//ブラックリストデータ有無

		if( $result != true ) $return_data[$x++] = $attendance_data[$i];
	}

	$data["attendance_data"] = $return_data;

	return $data;
	exit();
}

//----予約データの日付が指定年月日以前のデータのみの配列取得
function get_reservation_for_board_from_up_day_for_vip_history_common($year,$month,$day,$data){

	$ts_from = get_timestamp_by_year_month_day_common($year, $month, $day);	//指定年月日をタイムスタンプ形式に変換

	$data_num = count($data);

	$return_data = array();
	$x=0;

	for( $i=0; $i<$data_num; $i++ ){

		$year = $data[$i]["year"];
		$month = $data[$i]["month"];
		$day = $data[$i]["day"];

		$ts = get_timestamp_by_year_month_day_common($year, $month, $day);	//指定年月日をタイムスタンプ形式に変換

		if( $ts_from <= $ts ){
			$return_data[$x] = $data[$i];
			$x++;
		}
	}

	return $return_data;
	exit();
}

//----ボイスデータ登録
function insert_voice_from_vip_voice_common($shop_area,$content,$name){

	$content = mysql_real_escape_string($content);
	$name = mysql_real_escape_string($name);

	$now = time();

	if( $shop_area == "tokyo" ) {
		$shop_type = "refle";
	}else{
		$shop_type = $shop_area;
	}

	if( $name == "" ) $name = "匿名";

	$sql = sprintf("insert into voice(created,updated,shop_type,name,content) values('%s','%s','%s','%s','%s')",$now,$now,$shop_type,$name,$content);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(insert_voice_from_vip_voice_common)";
		exit();
	}

	return true;
	exit();
}

//----ボイスデータ登録
function insert_voice_from_vip_voice_2_common($shop_area,$content,$name,$publish_allow_site,$reservation_for_board_id){

	if( $publish_allow_site == "" ) $publish_allow_site = 0;

	$content = mysql_real_escape_string($content);
	$name = mysql_real_escape_string($name);

	$now = time();

	if( $shop_area == "tokyo" ){
		$shop_type = "refle";
	}else{
		$shop_type = $shop_area;
	}

	if( $name == "" ) $name = "匿名";

	$ws_SQL = "insert into voice(created,updated,shop_type,name,content,publish_allow_site,reservation_for_board_id)";
	$ws_SQL .= " values('%s','%s','%s','%s','%s','%s','%s')";
	$sql = sprintf($ws_SQL, $now,$now,$shop_type,$name,$content,$publish_allow_site,$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(insert_voice_from_vip_voice_2_common)";
		exit();
	}

	return true;
	exit();
}

//----セラピストID有無
function check_exist_therapist_data_common($therapist_data,$therapist_id){

	$therapist_data_num = count($therapist_data);

	for( $i=0; $i<$therapist_data_num; $i++ ){

		$therapist_id_tmp = $therapist_data[$i]["id"];

		if( $therapist_id == $therapist_id_tmp ){
			return true;
			exit();
		}
	}

	return false;
	exit();
}

//----予約状況データ取得
function add_board_data_in_therapist_data_by_therapist_id_common($therapist_data,$therapist_id,$board_data_hiki){

	$therapist_data_num = count($therapist_data);

	for( $i=0; $i<$therapist_data_num; $i++ ){

		$id = $therapist_data[$i]["id"];
		$board_data = $therapist_data[$i]["board_data"];

		$board_data_num = count($board_data);

		if( $id == $therapist_id ){

			$board_data[$board_data_num] = $board_data_hiki;

			$therapist_data[$i]["board_data"] = $board_data;
		}
	}

	return $therapist_data;
	exit();
}

//----セラピスト感謝データを持つ予約データ数取得
function get_therapist_thanks_num_by_day_common($year,$month,$day){

	$data = get_reservation_for_board_data_by_day_common($year,$month,$day);	//予約データ配列取得

	$data_num = count($data);

	$num = 0;

	for( $i=0; $i<$data_num; $i++ ){

		$reservation_for_board_id = $data[$i]["id"];

		$result = check_exist_therapist_thanks_by_reservation_for_board_id_common($reservation_for_board_id);	//セラピスト感謝データ有無

		if( $result == true ){
			$num++;
		}
	}

	return $num;
	exit();
}

//登録済みでないかチェック
function check_attendance_staff_new_data_exist_common($staff_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_staff_new where staff_id='%s' and year='%s' and month='%s' and day='%s'",$staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_attendance_staff_new_data_exist_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----配列コピー　意味不明　by aida
function select_only_refle_for_customer_voice_common($data){

	$data_num = count($data);

	$return_data = array();

	$x = 0;

	for( $i=0; $i<$data_num; $i++ ){
			$return_data[$x] = $data[$i];
			$x++;
	}

	return $return_data;
	exit();
}
//
function get_disp_data_for_vip_history_by_list_data_2_common($selected_num,$disp_num,$data){
	$data_num = count($data);
	$start_num = ($selected_num-1)*$disp_num;
	$end_num = $start_num + $disp_num;
	if( $end_num > $data_num ){
		$end_num = $data_num;
	}
	$return_data = array();
	$x = 0;
	for( $i=$start_num; $i<$end_num; $i++ ){
		$reservation_for_board_id = $data[$i]["id"];
		$attendance_id = $data[$i]["attendance_id"];
		$year = $data[$i]["year"];
		$month = $data[$i]["month"];
		$day = $data[$i]["day"];
		$start_hour = $data[$i]["start_hour"];
		$reservation_no = $data[$i]["reservation_no"];
		$customer_id = $data[$i]["customer_id"];
		if( $start_hour >= 24 ){
			$start_hour = $start_hour - 24;
			$tmp = get_mirai_day_common($year, $month, $day, 1);	//指定した日にち分、未来の日付取得
			$year = $tmp["year"];
			$month = $tmp["month"];
			$day = $tmp["day"];
		}
		$week_name = get_week_name_by_time_common($year, $month, $day);	//日付より曜日取得
		$disp_day = sprintf("%s年%s月%s日(%s)　%s時",$year,$month,$day,$week_name,$start_hour);
		$disp_day_2 = sprintf("%s月%s日(%s)　%s時",$month,$day,$week_name,$start_hour);
		$therapist_thanks = $data[$i]["th_content"];
		$therapist_thanks_id = $data[$i]["th_id"];
		$return_data[$x] = $data[$i];
		$return_data[$x]["disp_day"] = $disp_day;
		$return_data[$x]["disp_day_2"] = $disp_day_2;
		$return_data[$x]["therapist_thanks"] = $therapist_thanks;
		$return_data[$x]["therapist_thanks_id"] = $therapist_thanks_id;
		$x++;
	}
	return $return_data;
	exit();
}

//----セラピストヒアリングデータ取得
function history_hearing_by_list_data_2_common($selected_num,$disp_num,$data){

	$data_num = count($data);

	$start_num = ($selected_num-1)*$disp_num;

	$end_num = $start_num + $disp_num;

	if( $end_num > $data_num ){

		$end_num = $data_num;

	}

	$return_data = array();
	$x = 0;

	for( $i=$start_num; $i<$end_num; $i++ ){

		$reservation_for_board_id = $data[$i]["id"];
		$attendance_id = $data[$i]["attendance_id"];

		$year = $data[$i]["year"];
		$month = $data[$i]["month"];
		$day = $data[$i]["day"];
		$start_hour = $data[$i]["start_hour"];
		$reservation_no = $data[$i]["reservation_no"];
		$customer_id = $data[$i]["customer_id"];
		$customer_tel = $data[$i]["customer_tel"];

		$price = get_price_by_reservation_no_common($reservation_no);	//料金取得(売上履歴データより)

		if( $start_hour >= 24 ){

			$start_hour = $start_hour - 24;

			$tmp = get_mirai_day_common($year, $month, $day, 1);	//指定した日にち分、未来の日付取得

			$year = $tmp["year"];
			$month = $tmp["month"];
			$day = $tmp["day"];

		}

		$week_name = get_week_name_by_time_common($year, $month, $day);	//日付より曜日取得

		$disp_day = sprintf("%s年%s月%s日(%s)　%s時",$year,$month,$day,$week_name,$start_hour);

		$disp_day_2 = sprintf("%s月%s日(%s)　%s時",$month,$day,$week_name,$start_hour);

		$tmp = get_therapist_data_by_attendance_id_common($attendance_id);	//出勤データのセラピスト情報取得

		$therapist_name = $tmp["name_site"];
		$therapist_id = $tmp["id"];

		$therapist_img_url = get_img_url_by_therapist_id_common($therapist_id);	//セラピスト頁情報から画像URL取得

		$result = check_customer_clip_common($customer_id,$therapist_id);		//顧客クリップデータ有無

		if( $result == true ){
			$clip_exist_flg = 1;
		}else{
			$clip_exist_flg = 0;
		}

		$result = check_exist_black_list_common($therapist_id, $customer_id);	//ブラックリストデータ有無

		if( $result == true ){
			$black_flg = 1;
		}else{
			$black_flg = 0;
		}

		$customer_evaluation = get_customer_evaluation_common($reservation_for_board_id);	//顧客評価情報取得
		$customer_voice = get_customer_voice_2_common($reservation_for_board_id);			//ボイス情報取得
		$therapist_thanks = get_therapist_thanks_common($reservation_for_board_id);			//セラピスト感謝データ取得
		$therapist_hearing = get_customer_history_hearing_common($reservation_for_board_id);	//セラピストヒアリングデータ取得

		$return_data[$x] = $data[$i];
		$return_data[$x]["therapist_name"] = $therapist_name;
		$return_data[$x]["therapist_id"] = $therapist_id;
		$return_data[$x]["disp_day"] = $disp_day;
		$return_data[$x]["disp_day_2"] = $disp_day_2;
		$return_data[$x]["price"] = $price;
		$return_data[$x]["clip_exist_flg"] = $clip_exist_flg;
		$return_data[$x]["customer_evaluation"] = $customer_evaluation;
		$return_data[$x]["customer_voice"] = $customer_voice;
		$return_data[$x]["therapist_thanks"] = $therapist_thanks;
		$return_data[$x]["therapist_hearing"] = $therapist_hearing;
		$return_data[$x]["therapist_img_url"] = $therapist_img_url;
		$return_data[$x]["black_flg"] = $black_flg;

		$x++;
	}

	return $return_data;
	exit();
}

//----ボイス情報取得
function get_customer_voice_2_common($reservation_for_board_id){

	$sql = sprintf("select * from voice where delete_flg=0 and publish_flg=1 and reservation_for_board_id='%s'", $reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_customer_voice_2_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----ドライバー専用ページURL取得
function get_check_url_driver_front_common($area,$staff_id){

	$ch = get_staff_for_kobetsu_url_common($staff_id);

	$domain = $_SERVER["SERVER_NAME"];

	$url_root = "http://".$domain."/";

	$check_url = sprintf("%sdriver/index.php?area=%s&id=%s&ch=%s",$url_root,$area,$staff_id,$ch);

	return $check_url;
	exit();

}

//----スタッフの個別情報URL取得
function get_staff_for_kobetsu_url_common($staff_id){
	$ws_data = get_staff_data_by_id_common($id);	//スタッフ情報取得
	return $ws_data["for_kobetsu_url"];
}

//出席データが登録済みであるかどうか(登録済み：true,未登録:false)
function check_staff_attendance_exist_syounin_common($staff_id,$year,$month,$day){

	$sql = sprintf("select id from attendance_staff_new where syounin_state='1' and staff_id='%s' and year='%s' and month='%s' and day='%s' and today_absence=0 and kekkin_flg=0 and attendance_adjustment=0",$staff_id,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(check_staff_attendance_exist_syounin_common)";
		exit();
	}

	$num = mysql_num_rows($res);

	if( $num > 0 ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//画像のリサイズ
function writer_image_upload_resize_common($image_path){

	// 画像の情報を取得

	$size = getimagesize($image_path);

	// ファイルから画像の作成。画像のタイプによって関数を使い分ける

	switch($size[2]) {

		case IMAGETYPE_GIF:
			$image = imagecreatefromgif($image_path);
			break;
		case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($image_path);
			break;
		case IMAGETYPE_PNG:
			$image = imagecreatefrompng($image_path);
			break;
		default:
			return false;
			exit();

	}

	// 指定したサイズ以上のものを縮小

	$width = $size[0];
	$height = $size[1];

	if( $width > $height ){

		$hiritsu = $width / 100;

	}else{

		$hiritsu = $height / 100;

	}

	$new_width = floor($width / $hiritsu);
	$new_height = floor($height / $hiritsu);

	// 新規画像の作成

	$new_image = imagecreatetruecolor($new_width, $new_height);

	if( $new_image == false ){

		return false;
		exit();

	}

	if($size[2]==IMAGETYPE_PNG){

		//--背景が黒くなるので追加
		ImageAlphaBlending($new_image, false);
		ImageSaveAlpha($new_image, true);
		$fillcolor = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
		imagefill($new_image, 0, 0, $fillcolor);
		//背景が黒くなるので追加--

	}

	// リサンプル
	$result = imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

	if( $result == false ){

		return false;
		exit();

	}

	$new_fname = $image_path;

	switch($size[2]) {

		case IMAGETYPE_GIF:

			$result = imagegif($new_image, $new_fname);
			break;

		case IMAGETYPE_JPEG:

			//画質、最低：0、最高：100、デフォルト:75
			$quality = 75;

			$result = imagejpeg($new_image, $new_fname, $quality);
			break;

		case IMAGETYPE_PNG:

			//圧縮レベル。0 (圧縮しない) から 9 までの値です。
			$quality = 1;

			$result = imagepng($new_image, $new_fname, $quality);
			break;

		default:
			return false;
			exit();

	}

	if( $result == false ){

		return false;
		exit();

	}

	return true;
	exit();

}

//----スタッフTMPデータ有無
function check_exist_staff_tmp_common($staff_id){

	$sql = sprintf("select id from staff_tmp where delete_flg=0 and staff_id='%s'",$staff_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_staff_tmp_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----スタッフ名取得
function get_staff_tmp_from_d_f_common($staff_id){

	$result = check_exist_staff_tmp_common($staff_id);		//スタッフTMPデータ有無

	if( $result == true ){

		$data = get_staff_tmp_by_id_common($staff_id);		//スタッフTMPデータ取得

		$data["etc_number"] = get_etc_number_by_id_common($staff_id);	//ETC番号取得

	}else{

		$data = get_staff_data_by_id_common($staff_id);	//スタッフ情報取得

	}

	return $data;
	exit();
}

//----スタッフTMPデータ取得
function get_staff_tmp_by_id_common($staff_id){

	$sql = sprintf("select * from staff_tmp where delete_flg=0 and staff_id='%s'",$staff_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_staff_tmp_by_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----ETC番号取得
function get_etc_number_by_id_common($staff_id){
	$ws_data = get_staff_data_by_id_common($staff_id);	//スタッフ情報取得
	return $ws_data["etc_number"];
}

function update_staff_data_from_man_driver_common($staff_id){

	//仮データ取得
	$data = get_staff_tmp_by_id_common($staff_id);		//スタッフTMPデータ取得

	$id = $data["id"];
	$car_type = $data["car_type"];
	$car_color = $data["car_color"];
	$car_number = $data["car_number"];
	$tel = $data["tel"];
	$car_image_url = $data["car_image_url"];

	if( $id == "" ){

		return true;
		exit();

	}

	//仮データ更新
	$syounin_state = "1";
	update_staff_tmp_from_man_driver_common($staff_id,$syounin_state);	//スタッフTMP削除更新

	//スタッフデータ更新
	update_staff_new_new_from_man_driver_common($staff_id,$car_type,$car_color,$car_number,$tel,$car_image_url);	//スタッフデータ更新

	//メール送信

	return true;
	exit();

}

//----スタッフTMP削除更新
function update_staff_tmp_from_man_driver_common($staff_id,$syounin_state){

	$sql = sprintf("update staff_tmp set delete_flg='1',syounin_state='%s' where delete_flg=0 and staff_id='%s'",$syounin_state,$staff_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(update_staff_tmp_from_man_driver_common)";
		exit();
	}

	return true;
	exit();
}

//----予約の送迎ドライバーID取得
function get_staff_id_for_driver_instruction_common($reservation_for_board_id,$type){

	if( $type == "okuri_reservation" ){
		$column_name = "okuri_driver_id";
	}else if( $type == "mukae_reservation" ){
		$column_name = "mukae_driver_id";
	}else{
		return "-1";
		exit();
	}

	$sql = sprintf("select %s from reservation_for_board where id='%s'",$column_name,$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_staff_id_for_driver_instruction_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row[$column_name];
	exit();
}

//----ボードメッセージ歴データ登録
function insert_board_message_history_common($type,$staff_id,$content){

	$now = time();

	$sql = sprintf("insert into board_message_history(created,type,staff_id,content) values('%s','%s','%s','%s')",$now,$type,$staff_id,$content);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(insert_board_message_history_common)";
		exit();
	}

	if( $type == "2" ){

		update_top_disp_flg_common($staff_id);	//ボードメッセージ歴データ更新

	}

	return true;
	exit();
}

//----ボードメッセージ歴データ登録
function insert_board_message_history_2_common($type,$staff_id,$content,$title){

	$now = time();

	$sql = sprintf("insert into board_message_history(created,type,staff_id,content,title) values('%s','%s','%s','%s','%s')",$now,$type,$staff_id,$content,$title);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(insert_board_message_history_2_common)";
		exit();
	}

	if( $type == "2" ){
		update_top_disp_flg_common($staff_id);
	}

	return true;
	exit();
}

//----ボードメッセージ歴データ取得
function get_board_message_history_by_id_common($id){

	$sql = sprintf("select * from board_message_history where id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_board_message_history_by_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----ボードメッセージ歴データ配列取得
function get_board_message_history_by_staff_id_common($staff_id,$num){

	$sql = sprintf("select * from board_message_history where delete_flg='0' and staff_id='%s' order by created desc limit 0,%s",$staff_id,$num);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_board_message_history_by_staff_id_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$list_data[$i] = $row;
		$i++;

	}

	return $list_data;
	exit();
}

//---ボードメッセージ歴データ配列取得
function get_board_message_history_for_driver_history_common($staff_id,$num){

	$sql = sprintf("select * from board_message_history where delete_flg='0' and staff_id='%s' order by created desc limit 0,%s", $staff_id,$num);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_board_message_history_for_driver_history_common)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){

		$type = $row["type"];
		$staff_id = $row["staff_id"];

		$staff_name = get_staff_name_by_staff_id_common($staff_id);

		if( $type == "1" ){

			$staff_name_small = "本部";

		}else{

			$staff_name_small = mb_substr($staff_name,0,3,"UTF-8");

		}

		$list_data[$i] = $row;
		$list_data[$i]["staff_name"] = $staff_name;
		$list_data[$i]["staff_name_small"] = $staff_name_small;
		$i++;
	}

	return $list_data;
	exit();
}

//----ボードメッセージ歴データ更新
function update_top_disp_flg_common($staff_id){

	$id = get_board_message_history_top_id_common($staff_id);

	$sql = sprintf("update board_message_history set top_disp_flg='0' where id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(update_top_disp_flg_common)";
		exit();
	}

	return true;
	exit();
}

//----ボードメッセージ歴データ取得　ID
function get_board_message_history_top_id_common($staff_id){

	$sql = sprintf("select id from board_message_history where delete_flg='0' and type='1' and staff_id='%s' order by created desc limit 0,1",$staff_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_board_message_history_by_staff_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["id"];
	exit();
}

//----ボードメッセージ歴データ取得　Top表示用のみ
function get_top_message_board_message_history_common($staff_id){

	$sql = sprintf("select * from board_message_history where delete_flg='0' and type='1' and staff_id='%s' order by created desc limit 0,1",$staff_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_board_message_history_by_staff_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$top_disp_flg = $row["top_disp_flg"];

	$data = array();

	if( $top_disp_flg == "1" ) $data = $row;

	return $data;
	exit();
}

//----予約データ２取得
function get_reservation_board_2_by_reservation_for_board_id_common($reservation_for_board_id){

	$sql = sprintf("select * from reservation_board_2 where delete_flg='0' and reservation_for_board_id='%s'",$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_board_2_by_reservation_for_board_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----予約データ２登録
function update_reservation_board_2_state_common($reservation_for_board_id,$hour,$minute,$type,$state){

	$result = check_exist_reservation_board_2_by_reservation_for_board_id_common($reservation_for_board_id);

	if( $result == true ){
		//更新
		if( $type == "okuri" ){
			$sql = sprintf("update reservation_board_2 set okuri_hour='%s',okuri_minute='%s',okuri_driver_state='%s' where delete_flg=0 and reservation_for_board_id='%s'",$hour,$minute,$state,$reservation_for_board_id);
		}else if( $type == "mukae" ){
			$sql = sprintf("update reservation_board_2 set mukae_hour='%s',mukae_minute='%s',mukae_driver_state='%s' where delete_flg=0 and reservation_for_board_id='%s'",$hour,$minute,$state,$reservation_for_board_id);
		}else{
			echo "error!(update_reservation_board_2_state_common)";
			exit();
		}
	}else{
		//インサート
		if( $type == "okuri" ){
			$sql = sprintf("insert into reservation_board_2(okuri_hour,okuri_minute,okuri_driver_state,reservation_for_board_id) values('%s','%s','%s','%s')",$hour,$minute,$state,$reservation_for_board_id);
		}else if( $type == "mukae" ){
			$sql = sprintf("insert into reservation_board_2(mukae_hour,mukae_minute,mukae_driver_state,reservation_for_board_id) values('%s','%s','%s','%s')",$hour,$minute,$state,$reservation_for_board_id);
		}else{
			echo "error!(update_reservation_board_2_state_common)";
			exit();
		}
	}
	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(update_reservation_board_2_state_common)";
		exit();
	}

	return true;
	exit();
}

//----予約データ２有無
function check_exist_reservation_board_2_by_reservation_for_board_id_common($reservation_for_board_id){

	$sql = sprintf("select id from reservation_board_2 where delete_flg='0' and reservation_for_board_id='%s'",$reservation_for_board_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_reservation_board_2_by_reservation_for_board_id_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----予約データ２登録
function update_reservation_board_2_comment_common($reservation_for_board_id,$type,$comment){

	$result = check_exist_reservation_board_2_by_reservation_for_board_id_common($reservation_for_board_id);	//予約データ２有無

	if( $result == true ){
		//更新
		if( $type == "okuri" ){
			$sql = sprintf("update reservation_board_2 set okuri_driver_comment='%s' where delete_flg=0 and reservation_for_board_id='%s'",$comment,$reservation_for_board_id);
		}else if( $type == "mukae" ){
			$sql = sprintf("update reservation_board_2 set mukae_driver_comment='%s' where delete_flg=0 and reservation_for_board_id='%s'",$comment,$reservation_for_board_id);
		}else{
			echo "error!(update_reservation_board_2_comment_common)";
			exit();
		}
	}else{
		//インサート
		if( $type == "okuri" ){
			$sql = sprintf("insert into reservation_board_2(okuri_driver_comment,reservation_for_board_id) values('%s','%s')",$comment,$reservation_for_board_id);
		}else if( $type == "mukae" ){
			$sql = sprintf("insert into reservation_board_2(mukae_driver_comment,reservation_for_board_id) values('%s','%s')",$comment,$reservation_for_board_id);
		}else{
			echo "error!(update_reservation_board_2_comment_common)";
			exit();
		}
	}

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(update_reservation_board_2_comment_common)";
		exit();
	}

	return true;
	exit();
}

//予約ボードデータを取得
function get_reservation_for_board_data_today_for_driver_common($staff_id){

	$data = get_today_year_month_day_common();		//本日の年月日取得

	$year = $data["year"];
	$month = $data["month"];
	$day = $data["day"];

	$ws_SQL = "select * from reservation_for_board where delete_flg=0 and attendance_tmp_flg=0 and year='%s' and month='%s' and day='%s'";
	$ws_SQL .= " and (okuri_driver_id='%s' or mukae_driver_id='%s')";
	$ws_SQL .= " order by id desc";
	$sql = sprintf($ws_SQL, $year,$month,$day,$staff_id,$staff_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_reservation_for_board_data_today_for_driver_common)";
		exit();
	}
	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$attendance_id = $row["attendance_id"];

		$therapist_name = get_therapist_name_by_attendance_id_common($attendance_id);	//指定出勤データのセラピスト名取得

		$list_data[$i] = $row;
		$list_data[$i]["therapist_name"] = $therapist_name;

		$i++;
	}

	return $list_data;
	exit();
}

//----ドライバー状態データ取得
function board_data_divide_for_instruction_common($data,$staff_id){

	$return_data = array();

	$x = 0;

	$data_num = count($data);

	for( $i=0; $i<$data_num; $i++ ){

		$okuri_driver_id = $data[$i]["okuri_driver_id"];
		$mukae_driver_id = $data[$i]["mukae_driver_id"];

		$reservation_for_board_id = $data[$i]["id"];

		$tmp = get_reservation_board_2_by_reservation_for_board_id_common($reservation_for_board_id);	//予約データ２取得

		$okuri_hour = $tmp["okuri_hour"];
		$okuri_minute = $tmp["okuri_minute"];
		$mukae_hour = $tmp["mukae_hour"];
		$mukae_minute = $tmp["mukae_minute"];

		$okuri_driver_comment = $tmp["okuri_driver_comment"];
		$mukae_driver_comment = $tmp["mukae_driver_comment"];

		$okuri_driver_state = $tmp["okuri_driver_state"];
		$mukae_driver_state = $tmp["mukae_driver_state"];

		$okuri_time_disp = get_time_disp_for_instruction_common($okuri_hour,$okuri_minute);	//時分編集
		$mukae_time_disp = get_time_disp_for_instruction_common($mukae_hour,$mukae_minute);	//時分編集

		if( $okuri_driver_id == $staff_id ){

			$data_type = "okuri";

			$driver_state = $okuri_driver_state;
			$driver_state_disp = get_driver_state_disp_common($data_type,$driver_state);	//ドライバー状態取得

			$return_data[$x] = $data[$i];
			$return_data[$x]["data_type"] = $data_type;

			$return_data[$x]["okuri_time_disp"] = $okuri_time_disp;
			$return_data[$x]["mukae_time_disp"] = $mukae_time_disp;

			$return_data[$x]["okuri_driver_comment"] = $okuri_driver_comment;
			$return_data[$x]["mukae_driver_comment"] = $mukae_driver_comment;

			$return_data[$x]["driver_state_disp"] = $driver_state_disp;

			$x++;

		}

		if( $mukae_driver_id == $staff_id ){

			$data_type = "mukae";

			$driver_state = $mukae_driver_state;
			$driver_state_disp = get_driver_state_disp_common($data_type,$driver_state);	//ドライバー状態取得

			$return_data[$x] = $data[$i];
			$return_data[$x]["data_type"] = $data_type;

			$return_data[$x]["okuri_time_disp"] = $okuri_time_disp;
			$return_data[$x]["mukae_time_disp"] = $mukae_time_disp;

			$return_data[$x]["okuri_driver_comment"] = $okuri_driver_comment;
			$return_data[$x]["mukae_driver_comment"] = $mukae_driver_comment;

			$return_data[$x]["driver_state_disp"] = $driver_state_disp;

			$x++;

		}

	}

	return $return_data;
	exit();

}
//----出勤データ２取得
function get_attendance_staff_2_by_id_common($id){

	$sql = sprintf("select * from attendance_staff_2 where id='%s'",$id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_staff_2_by_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データ２取得
function get_attendance_staff_2_by_attendance_staff_new_id_common($attendance_staff_new_id){

	$sql = sprintf("select * from attendance_staff_2 where delete_flg=0 and attendance_staff_new_id='%s'",$attendance_staff_new_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_attendance_staff_2_by_attendance_staff_new_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----出勤データ２有無
function check_exist_attendance_staff_2_by_attendance_staff_new_id_common($attendance_staff_new_id){
	$ws_data = get_attendance_staff_2_by_attendance_staff_new_id_common($attendance_staff_new_id);	//出勤データ２取得
	if( $ws_data["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----出勤データ２取得
function get_driver_day_report_for_man_by_attendance_staff_new_id_common($attendance_staff_new_id){

	$sql = sprintf("select * from attendance_staff_2 where delete_flg=0 and report_delete_flg=0 and attendance_staff_new_id='%s'",$attendance_staff_new_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_driver_day_report_for_man_by_attendance_staff_new_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	return $row;
	exit();
}

//----ドライバー頁URL取得
function get_check_url_driver_front_communication_common($staff_id,$area){

	$ch = get_staff_for_kobetsu_url_common($staff_id);

	$url_root = WWW_URL_TOKYO;

	$check_url = sprintf("%sdriver/communication.php?area=%s&id=%s&ch=%s",$url_root,$area,$staff_id,$ch);

	return $check_url;
	exit();
}


//----ドライバー切替日以降かどうか
function check_switching_driver_remuneration_common($year,$month,$day,$staff_id){

	$area = get_staff_area_by_id_common($staff_id);		//スタッフのエリア取得
	/*
	if( $area != "tokyo" ){
		return false;
		exit();
	}
	*/
	return true;
	exit();

	$data = get_driver_switching_year_month_day_common();	//ドライバー切替日取得

	$year_switching = $data["year"];
	$month_switching = $data["month"];
	$day_switching = $data["day"];

	$timestamp = get_timestamp_by_year_month_day_common($year, $month, $day);	//指定年月日をタイムスタンプ形式に変換

	$timestamp_switching = get_timestamp_by_year_month_day_common($year_switching, $month_switching, $day_switching);	//指定年月日をタイムスタンプ形式に変換

	if( $timestamp >= $timestamp_switching ){
		return true;
		exit();
	}

	return false;
	exit();
}

//----距離別単価取得
function get_unit_price_by_car_distance_common($car_distance){

	$value = "80";

	$car_distance = floor($car_distance);

	if( $car_distance >= 250 ){
		$value = "100";
	}else if( $car_distance >= 200 ){
		$value = "95";
	}else if( $car_distance >= 150 ){
		$value = "90";
	}else if( $car_distance >= 100 ){
		$value = "85";
	}

	return $value;
}
//2018/12/10 murase insert from
//----距離別単価取得
function get_unit_price_by_distance_ave_common($distance_ave,$year,$month,$day,$area){

	//$distance_ave = round($distance_ave, 1);
	$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$sql = sprintf("select unit_price from unit_price_setting where delete_flg='0' and area='%s' and (period_start <='%s') and (period_end >='%s') and (ave_distance_le <='%s') order by unit_price desc limit 1",$area,$timestamp,$timestamp,$distance_ave);

	$res = mysql_query($sql, DbCon);

	if($res == false){
		echo "error!(get_unit_price_by_distance_ave_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$unit_price = $row["unit_price"];

	if( $unit_price == "" ) $unit_price = 100;

	return $unit_price;
}
//2018/12/10 murase insert to
//----ドライバー報酬取得
function get_remuneration_and_sum_price_common(
$remuneration,$chief_allowance,$gasoline_value_disp,$highway,$parking,$allowance,$pay_finish,
$pay_day,$car_distance_over_allowance,$staff_id,$year,$month,$day){

	$staff_type = get_staff_type_by_id_common($staff_id);

	$sum_price = 0;

	$switching_result = check_switching_driver_remuneration_common($year,$month,$day,$staff_id);	//ドライバー切替日以降かどうか

	if( $staff_type != "driver" ){

		$switching_result = false;

	}

	$remuneration_type = get_remuneration_type_common($staff_id,$year,$month,$day);	//設定報酬データの設定値取得

	if( ($switching_result == true) && ($remuneration_type != "1") ){

		//報酬+高速代+駐車場代-清算済み
		$sum_price = ( $remuneration + $highway + $parking ) - $pay_finish - $pay_day;

	}else{

		//チーフ手当を足す
		$remuneration = $remuneration + $chief_allowance;

		//報酬+ガソリン代+高速代+駐車場代+手当-清算済み
		$sum_price = ( $remuneration + $gasoline_value_disp + $highway + $parking + $allowance ) - $pay_finish - $pay_day;

		$sum_price = $sum_price + $car_distance_over_allowance;

	}

	$data["remuneration"] = $remuneration;
	$data["sum_price"] = $sum_price;

	return $data;
	exit();

}

//----ドライバー切替日取得
function get_driver_switching_year_month_day_common(){

	$name = "driver_switching_year";
	$year = get_settings_value_by_name_common($name);	//設定情報取得（設定値）

	$name = "driver_switching_month";
	$month = get_settings_value_by_name_common($name);	//設定情報取得（設定値）

	$name = "driver_switching_day";
	$day = get_settings_value_by_name_common($name);	//設定情報取得（設定値）

	$data["year"] = $year;
	$data["month"] = $month;
	$data["day"] = $day;

	return $data;
	exit();

}

//----インセンティブ切替日
function get_incentive_switching_year_month_day_common(){

	$name = "incentive_switching_year";
	$year = get_settings_value_by_name_common($name);	//設定情報取得（設定値）

	$name = "incentive_switching_month";
	$month = get_settings_value_by_name_common($name);	//設定情報取得（設定値）

	$name = "incentive_switching_day";
	$day = get_settings_value_by_name_common($name);	//設定情報取得（設定値）

	$data["year"] = $year;
	$data["month"] = $month;
	$data["day"] = $day;

	return $data;
	exit();
}

//----インセンティブ切替日以降か
function check_switching_incentive_common($year,$month,$day){

	$data = get_incentive_switching_year_month_day_common();	//インセンティブ切替日

	$year_switching = $data["year"];
	$month_switching = $data["month"];
	$day_switching = $data["day"];

	$timestamp = get_timestamp_by_year_month_day_common($year, $month, $day);	//指定年月日をタイムスタンプ形式に変換

	$timestamp_switching = get_timestamp_by_year_month_day_common($year_switching, $month_switching, $day_switching);	//指定年月日をタイムスタンプ形式に変換

	if( $timestamp >= $timestamp_switching ){
		return true;
		exit();
	}

	return false;
	exit();
}

//----インセンティブ取得
function get_incentive_common($car_distance,$year,$month,$day,$remuneration_type){

	$result = check_switching_incentive_common($year,$month,$day);	//インセンティブ切替日以降か

	if(( $result == true )&&( $remuneration_type != '2'  )){
		$incentive = $car_distance * 5;
	}else{
		$incentive = 0;
	}

	return $incentive;
	exit();
}

//----設定情報取得（設定値）
function get_settings_value_by_name_common($name){

	$sql = sprintf("select value from settings where delete_flg=0 and name='%s'",$name);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_settings_value_by_name_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["value"];
	exit();
}

//----ドライバー切替日更新
function update_driver_switching_year_month_day_common($year,$month,$day){

	$name = "driver_switching_year";
	$value = $year;
	update_settings_value_by_name_common($name,$value);	//設定データ更新

	$name = "driver_switching_month";
	$value = $month;
	update_settings_value_by_name_common($name,$value);	//設定データ更新

	$name = "driver_switching_day";
	$value = $day;
	update_settings_value_by_name_common($name,$value);	//設定データ更新

	return true;
	exit();
}

//----インセンティブ切替日更新
function update_incentive_switching_year_month_day_common($year,$month,$day){

	$name = "incentive_switching_year";
	$value = $year;
	update_settings_value_by_name_common($name,$value);	//設定データ更新

	$name = "incentive_switching_month";
	$value = $month;
	update_settings_value_by_name_common($name,$value);	//設定データ更新

	$name = "incentive_switching_day";
	$value = $day;
	update_settings_value_by_name_common($name,$value);	//設定データ更新

	return true;
	exit();
}

//----設定データ更新
function update_settings_value_by_name_common($name,$value){

	$sql = sprintf("update settings set value='%s' where delete_flg=0 and name='%s'",$value,$name);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(update_settings_value_by_name_common)";
		exit();
	}

	return true;
	exit();
}

//本日出勤かどうかのチェック
function therapist_attendance_check_3_common($therapist_id,$year,$month,$day,$attendance_data){

	$attendance_data_num = count($attendance_data);

	for( $i=0; $i<$attendance_data_num; $i++ ){

		$therapist_id_tmp = $attendance_data[$i]["therapist_id"];
		$year_tmp = $attendance_data[$i]["year"];
		$month_tmp = $attendance_data[$i]["month"];
		$day_tmp = $attendance_data[$i]["day"];

		if( ($therapist_id_tmp==$therapist_id) && ($year_tmp==$year) && ($month_tmp==$month) && ($day_tmp==$day) ){
			return true;
			exit();
		}
	}

	return false;
	exit();
}

//----指定日かつ指定セラピストIDの出勤データを出勤データ配列より取得
function get_attendance_id_from_attendance_all_by_time_common($therapist_id,$year,$month,$day,$attendance_data){

	$attendance_id = "";

	$attendance_data_num = count($attendance_data);

	for( $i=0; $i<$attendance_data_num; $i++ ){

		$attendance_id_tmp = $attendance_data[$i]["id"];
		$therapist_id_tmp = $attendance_data[$i]["therapist_id"];
		$year_tmp = $attendance_data[$i]["year"];
		$month_tmp = $attendance_data[$i]["month"];
		$day_tmp = $attendance_data[$i]["day"];

		if( ($therapist_id_tmp==$therapist_id) && ($year_tmp==$year) && ($month_tmp==$month) && ($day_tmp==$day) ){

			$attendance_id = $attendance_id_tmp;

			return $attendance_id;
			exit();
		}
	}

	return $attendance_id;
	exit();
}

//----出勤IDを出勤データ配列より取得
function get_attendance_data_one_from_attendance_all_by_attendance_id_common($attendance_id,$attendance_data){

	$data = array();

	$attendance_data_num = count($attendance_data);

	for( $i=0; $i<$attendance_data_num; $i++ ){

		$attendance_id_tmp = $attendance_data[$i]["id"];

		if( $attendance_id_tmp == $attendance_id ){

			$data = $attendance_data[$i];

			return $data;
			exit();
		}
	}

	return $data;
	exit();
}

//----予約newデータ配列取得
function get_reservation_new_by_day_and_area_common($year,$month,$day,$area){

	$ws_SQL = "select A.* from reservation_new A";
	$ws_SQL .= " left join attendance_new B on B.id=A.attendance_id";
	$ws_SQL .= " where B.today_absence='0' and B.kekkin_flg='0' and B.syounin_state='1' and B.area='%s' and B.year='%s' and B.month='%s' and B.day='%s'";
	$sql = sprintf($ws_SQL, $area,$year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_reservation_new_by_day_and_area_common)";
		header("Location: ".WWW_URL."error.php");
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i++] = $row;
	}

	return $list_data;
	exit();
}

//----予約データの有無を予約データ配列より判断
function check_reservation_data_exist_by_reservation_all_common($attendance_id,$time_1,$time_2,$reservation_data){

	$reservation_data_num = count($reservation_data);

	for( $i=0; $i<$reservation_data_num; $i++ ){

		$attendance_id_tmp = $reservation_data[$i]["attendance_id"];
		$time_tmp = $reservation_data[$i]["time"];

		if( $attendance_id_tmp==$attendance_id ){

			if( ($time_tmp==$time_1) || ($time_tmp==$time_2) ){
				return true;
				exit();
			}
		}
	}

	return false;
	exit();
}

//----出勤データ２有無
function check_exist_attendance_2_by_attendance_new_id_common($attendance_new_id){

	$sql = sprintf("select id from attendance_2 where delete_flg=0 and attendance_new_id='%s'",$attendance_new_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(check_exist_attendance_2_by_attendance_new_id_common)";
		exit();
	}
	$row = mysql_fetch_assoc($res);

	if( $row["id"] == "" ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----公開フラグ＝１の該当日出勤のセラピスト情報配列取得
function get_attendance_new_for_free_therapist_state_common($year,$month,$day){

	$ws_SQL = "select A.therapist_id,A.start_time,A.end_time,B.time from attendance_new A";
	$ws_SQL .= " left join reservation_new B on A.id=B.attendance_id";
	$ws_SQL .= " where A.publish_flg='1' and A.year='%s' and A.month='%s' and A.day='%s'";
	$sql = sprintf($ws_SQL, $year,$month,$day);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){

		$_SESSION["error_page_message"] = "クエリ実行に失敗しました(get_attendance_new_for_free_therapist_state_common)";
		header("Location: ".WWW_URL."error.php");
		exit();

	}

	$i=0;
	$list_data = array();
	while($row = mysql_fetch_assoc($res)){

		$list_data[$i++] = $row;

	}

	return $list_data;
	exit();
}

//----セラピスト情報配列より指定セラピストの情報取得
function get_attendance_new_from_attendance_free_common($attendance_data,$therapist_id){

	$attendance_data_num = count($attendance_data);

	$data = array();
	$x = 0;

	for( $i=0; $i<$attendance_data_num; $i++ ){

		$therapist_id_tmp = $attendance_data[$i]["therapist_id"];

		if( $therapist_id_tmp == $therapist_id ){
			$data[$x] = $attendance_data[$i];
			$x++;
		}
	}

	return $data;
	exit();
}

//----保険料取得
function get_insurance_price_day_therapist_id_common($year,$month,$day,$therapist_id){

	$price = 0;

	//$insurance = get_therapist_insurance_by_id_common($therapist_id);
	$insurance = get_insurance_common($therapist_id,$year,$month,$day);		//設定保険料データより設定値取得

	if( $insurance == "2" ){

		$attendance_data = get_attendance_new_data_day_therapist_id_common($year,$month,$day,$therapist_id);	//出勤データ取得（セラピスト）

		$attendance_id = $attendance_data["id"];

		if( $attendance_id != "" ){

			$board_num = get_reservation_for_board_data_num_by_attendance_id_common($attendance_id);	//予約データ数取得（出勤データ別）

			$price = 50 * $board_num;
		}
	}

	return $price;
	exit();
}

//----保険料取得
function get_insurance_price_by_attendance_id_common($attendance_id){

	$tmp = get_attendance_data_by_attendance_id_common($attendance_id);		//出席データの取得

	$therapist_id = $tmp["therapist_id"];
	$year = $tmp["year"];
	$month = $tmp["month"];
	$day = $tmp["day"];

	$insurance_price = 0;
	$insurance = get_insurance_common($therapist_id,$year,$month,$day);		//設定保険料データより設定値取得
	if( $insurance == "2" ){
		$board_num = get_reservation_for_board_num_by_attendance_id_common($attendance_id);	//予約データ数取得（出勤データ別）
		$insurance_price = 50 * $board_num;
	}

	return $insurance_price;
	exit();
}

//----指定ページURL取得
function get_page_html_2_content_by_page_name_common($page_name){

	$sql = sprintf("select content from page_html_2 where delete_flg=0 and name='%s'",$page_name);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_page_html_2_content_by_page_name_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	return $row["content"];
	exit();
}

//----設定保険料データより設定値取得
function get_insurance_common($therapist_id,$year,$month,$day){

	$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$sql = sprintf("select value from settings_insurance where delete_flg='0' and (period_start<='%s') and (period_end>='%s') and therapist_id='%s'",$timestamp,$timestamp,$therapist_id);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_insurance_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$value = $row["value"];

	if( $value == "" ){
		$value = 0;
	}

	return $value;
	exit();
}

//----チーフ手当取得
function get_chief_allowance_by_attendance_data_common($attendance_data){

	$therapist_id = $attendance_data["therapist_id"];
	$year = $attendance_data["year"];
	$month = $attendance_data["month"];
	$day = $attendance_data["day"];

	//前日チェック
	$result = check_last_day_for_remuneration_common($year,$month,$day);	//前日チェック(報酬計算用)(過去であればTRUE)

	if( $result == false ){
		$chief_allowance = 0;
	}else{
		$chief_allowance = get_chief_allowance_by_therapist_id_and_day_common($therapist_id,$year,$month,$day);	//チーフ手当取得
	}

	return $chief_allowance;
	exit();
}

//----出勤データ取得(出勤データ配列より)
function get_attendance_data_one_from_attendance_data_for_total_point_common($attendance_id,$attendance_data_for_total_point){

	$attendance_data = $attendance_data_for_total_point;
	$attendance_data_num = count($attendance_data);

	for($i=0;$i<$attendance_data_num;$i++){

		$attendance_id_tmp = $attendance_data[$i]["id"];

		if( $attendance_id == $attendance_id_tmp ){
			return $attendance_data[$i];
			exit();
		}
	}

	return false;
	exit();
}

//----ユニット料金取得
function get_unit_price_by_car_distance_2_common($distance_ave,$year,$month,$day,$area){

	$unit_price = get_unit_price_by_distance_ave_common($distance_ave,$year,$month,$day,$area);

	//$control_value = get_settings_gasoline_control_value_common($year,$month,$day);		//ガソリン制御データ取得（設定値）

	//$unit_price = $unit_price+($control_value);

	if( $unit_price < 0 ) $unit_price = 0;

	return $unit_price;
	exit();
}

//----ガソリン制御データ取得（設定値）
function get_settings_gasoline_control_value_common($year,$month,$day){

	$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$sql = sprintf("select value from settings_gasoline_control where delete_flg='0' and (period_start<='%s') and (period_end>='%s')",$timestamp,$timestamp);
	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_settings_gasoline_control_value_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$value = $row["value"];

	if( $value == "" ) $value = 0;

	return $value;
	exit();
}

//----設定報酬データの設定値取得
function get_remuneration_type_common($staff_id,$year,$month,$day){

	$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$sql = sprintf("select value from settings_remuneration where delete_flg='0' and (period_start<='%s') and (period_end>='%s') and staff_id='%s'",$timestamp,$timestamp,$staff_id);

 	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_remuneration_type_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$value = $row["value"];

	if( $value == "" ) $value = 0;

	return $value;
	exit();
}

//----設定報酬データの設定値取得
function get_remuneration_type_2_common($staff_id,$u_fromYmd,$u_toYmd){

	$ws_fromYear = floor($u_fromYmd / 10000);
	$ws_fromMonth = floor($u_fromYmd / 100) % 100;
	$ws_fromDay = $u_fromYmd % 100;
	$ws_toYear = floor($u_toYmd / 10000);
	$ws_toMonth = floor($u_toYmd / 100) % 100;
	$ws_toDay = $u_toYmd % 100;
	$ws_startPeriod = get_timestamp_by_year_month_day_common($ws_fromYear, $ws_fromMonth, $ws_fromDay);	//指定年月日をタイムスタンプ形式に変換 in ^common/include/functions.php
	$ws_endPeriod = get_timestamp_by_year_month_day_common($ws_toYear, $ws_toMonth, $ws_toDay);			//指定年月日をタイムスタンプ形式に変換 in ^common/include/functions.php

	$sql = sprintf("select value from settings_remuneration where delete_flg='0' and (period_start<='%s') and (period_end>='%s') and staff_id='%s'",$ws_endPeriod,$ws_startPeriod,$staff_id);

 	$res = mysql_query($sql, DbCon);
	if($res == false){
		echo "error!(get_remuneration_type_common)";
		exit();
	}

	$row = mysql_fetch_assoc($res);

	$value = $row["value"];

	if( $value == "" ) $value = 0;

	return $value;
	exit();
}

//----同一エリアのセラピスト情報配列取得
function get_therapist_data_by_area_common($area){
	return get_therapist_list_by_area_common($area);
}


//----報酬金額集計
function get_remuneration_and_sum_price_2_common($remuneration,$chief_allowance,$gasoline_value_disp,$highway,$parking,$allowance,$pay_finish,$pay_day,$car_distance_over_allowance,$staff_id,$year,$month,$day,$other_data){

	$car_distance = $other_data["car_distance"];

	$incentive = get_incentive_common($car_distance,$year,$month,$day);

	$staff_type = get_staff_type_by_id_common($staff_id);

	$sum_price = 0;

	$switching_result = check_switching_driver_remuneration_common($year,$month,$day,$staff_id);

	if( $staff_type != "driver" ) $switching_result = false;

	$remuneration_type = get_remuneration_type_common($staff_id,$year,$month,$day);	//設定報酬データの設定値取得

	if( ($switching_result == true) && ($remuneration_type == "2") ){
		//報酬+高速代+駐車場代-清算済み
		$remuneration = $remuneration + $chief_allowance;
		$sum_price = ( $remuneration + $highway + $parking ) - $pay_finish - $pay_day - $incentive;
	}else{
		//チーフ手当を足す
		$remuneration = $remuneration + $chief_allowance;

		//報酬+ガソリン代+高速代+駐車場代+手当-清算済み
		$sum_price = ( $remuneration + $gasoline_value_disp + $highway + $parking + $allowance ) - $pay_finish - $pay_day;

		$sum_price = $sum_price + $car_distance_over_allowance;
	}

	$sum_price = $sum_price + $incentive;

	$data["remuneration"] = $remuneration;
	$data["sum_price"] = $sum_price;

	return $data;
	exit();
}

//----セラピスト待機場所データ配列取得
function get_therapist_meeting_place_data_common($therapist_id){

	$sql = sprintf("select * from therapist_meeting_place where delete_flg='0' and therapist_id='%s'",$therapist_id);
	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_therapist_meeting_place_data)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----セラピスト待機場所データ配列取得
function get_therapist_meeting_place_data_common_new($therapist_id){

	$sql = sprintf("select therapist_meeting_place content from therapist_new where delete_flg='0' and id='%s'",$therapist_id);

	$res = mysql_query($sql, DbCon);
	if( $res == false ){
		echo "error!(get_therapist_meeting_place_data)";
		exit();
	}

	$i=0;
	$list_data = array();

	while($row = mysql_fetch_assoc($res)){
		$list_data[$i] = $row;
		$i++;
	}

	return $list_data;
	exit();
}

//----シェア率更新
function update_therapist_share_rate_common($area,$year,$month){

	//シェア率計算
	if($area == "tokyo_bigao"){
		//■BIGAO
		//セラピストデータを取得
		$sql = "select id,share_rate from therapist_new where leave_flg='0' and delete_flg='0' and area = '".$area."'";
		$res_list = mysql_query($sql, DbCon);
		if($res_list == false){
			echo "error!(get_therapist_data_all_common)";
			exit();
		}

		while($list = mysql_fetch_assoc($res_list)){
		    $therapist_id = $list['id'];
		    $old_share_rate = $list['share_rate'];
			//前月指名売上に応じて変化
			$total_sales = 0;
			$share_rate = 0.4;
			$tmp_array = array();

			$ws_SQL = "select reservation_no from reservation_for_board as rfb";
			$ws_SQL .= " join attendance_new as an on an.id = rfb.attendance_id";
			$ws_SQL .= " where rfb.delete_flg=0 and rfb.shimei_flg=1 and complete_flg='1' and an.year='%s' and an.month='%s' and an.therapist_id='%s'";
			$sql =sprintf($ws_SQL, $year,$month,$therapist_id);
			$res = mysql_query($sql, DbCon);
			if( $res == false ){
				echo "error!(update_therapist_share_rate_common)";
				exit();
			}
			while($row = mysql_fetch_assoc($res)){
			    $tmp_array[] = "reservation_no = '" . $row['reservation_no'] . "'";
			}
			if(!empty($tmp_array)){
			    $where = join(" or ",$tmp_array);
				$sql = sprintf("select sum(price) as total_sales from sale_history where %s",$where);

				$res = mysql_query($sql, DbCon);
				$row = mysql_fetch_assoc($res);
				if(!empty($row)){
				    $total_sales = $row['total_sales'];
				}
			}

			if($total_sales >= 1000000){
			    $share_rate = 0.5;
			} else if($total_sales >= 500000){
			    $share_rate = 0.45;
			}

			if($old_share_rate != $share_rate){
				// 会員情報を更新するSQL文(メールアドレスのみ)
				$sql = sprintf("update therapist_new set share_rate='%s' where id='%s'",$share_rate,$therapist_id);
				$res = mysql_query($sql, DbCon);
				if($res == false){
					return false;
					exit();
				}
			}
		}
	}
	return true;
	exit();
}

//----セラピスト支払いスパン取得
function get_payment_span_by_therapist_id_common($therapist_id){
	$ws_data = get_therapist_data_by_id_common($therapist_id);	//セラピスト情報取得
	return $ws_data["payment_span"];
}

//====( 一般関数 )======================================================================================

//---エラーログ出力
function common_error_log($messe){

	$now = date("Y-m-d H:i:s",time());

	$fp = fopen("error.txt", "a");
	fwrite($fp, "\n".$messe."(".$now.")");
	fclose($fp);
}

//----SSLサイトチェック
function is_ssl_common(){

	$server_name = $_SERVER["SERVER_NAME"];

	if( (PHP_OS == "WINNT") || ($server_name == "test.tokyo-refle.com") ){

		if( $_SERVER['HTTPS'] == 'on' ){
			return true;
			exit();
		}
	}else{

		//「https」or「http」
		$tmp = $_SERVER["HTTP_X_FORWARDED_PROTO"];

		if( $tmp == "https" ){
			return true;
			exit();
		}

	}

	return false;
	exit();
}

//----本サイトか否か
function get_env_type_common(){

	$type = "honban";

	if( PHP_OS == "WINNT" ) $type = "local";

	return $type;
	exit();
}

//----東京リフレURL取得
function get_refle_url_common(){

	if ( PHP_OS == "WIN32" || PHP_OS == "WINNT" ){
		$url = "http://refle-aws.localhost/";
	}else{
		$url = "http://www.tokyo-refle.com/";
	}

	return $url;
}

//----nullをゼロに変換
function if_null_is_zero_common($data){

	if( $data == "" ) $data = 0;
	return $data;
}

//----数値が１桁の時前ゼロを付加し２ケタ文字列にする
function add_zero_when_under_ten_common($data){

	if( $data < 10 ) $data = "0".$data;

	return $data;
}

//----小数点以下がなければ　.0を付加
function add_shousuu_ten_zero_common($data){

	if (!strstr($data, '.')) $data = $data.".0";

	return $data;
	exit();
}

//----数値が１桁の時前ゼロを付加し２ケタ文字列にする
function minute_zero_add_common($minute){
	return add_zero_when_under_ten_common($minute);	//数値が１桁の時前ゼロを付加し２ケタ文字列にする
}

//----半角チェック
function check_hankaku_value_common($value){

	if (preg_match("/^[a-zA-Z0-9]+$/", $value)) {
		return true;
		exit();
	} else {
		return false;
		exit();
	}
}

//----半角数字チェック
function check_hankaku_num_value_common($value){

	if (preg_match("/^[0-9]+$/", $value)) {
		return true;
		exit();
	} else {
		return false;
		exit();
	}
}

//----数値チェック
function check_car_distance_value_common($value){
	return check_hankaku_num_value_common($value);	//半角数字チェック
}

//----ファイル名書式チェック
function check_upload_file_name_common($file_name){

	$file_name = str_replace(".", "", $file_name);
	$file_name = str_replace("_", "", $file_name);
	$file_name = str_replace("-", "", $file_name);
	$result = check_hankaku_value_common($file_name);	//半角チェック

	if( $result == false ){
		return false;
		exit();
	}

	return true;
	exit();
}

//----マイクロタイム差計算
function get_microtime_sa_common($time1){

	$time2 = microtime(true);

	$sa = $time2-$time1;

	return $sa;
	exit();
}

//----開始時刻から終了時刻間の時間取得
function get_work_minute_driver_common($start_hour,$start_minute,$end_hour,$end_minute){

	$start_time = ($start_hour*60)+$start_minute;
	$end_time = ($end_hour*60)+$end_minute;

	$sa = $end_time - $start_time;

	return $sa;
	exit();

}

//----開始時刻から終了時刻間の時間取得
function get_work_time_common($start_time,$end_time){

	$work_time = round( (($end_time-$start_time)/2) , 1 );

	return $work_time;
	exit();

}

//----開始及び終了時刻の表示編集
function work_time_disp_for_driver_instruction_common($data){

	$start_hour = $data["start_hour"];
	$start_minute = add_zero_when_under_ten_common($data["start_minute"]);	//数値が１桁の時前ゼロを付加し２ケタ文字列にする
	$end_hour = $data["end_hour"];
	$end_minute = add_zero_when_under_ten_common($data["end_minute"]);		//数値が１桁の時前ゼロを付加し２ケタ文字列にする

	$start_time = $data["start_time"];
	$end_time = $data["end_time"];

	if( $start_hour == "-1" ){

		$time_array = get_time_array_driver_common();	//ドライバー用時刻配列取得

		$time_data = get_time_value_for_sale_driver_one_common($time_array,$start_time,$end_time);		//開始及び終了時刻取得

		$start_hour = $time_data["start_hour"];
		$start_minute = add_zero_when_under_ten_common($time_data["start_minute"]);	//数値が１桁の時前ゼロを付加し２ケタ文字列にする
		$end_hour = $time_data["end_hour"];
		$end_minute = add_zero_when_under_ten_common($time_data["end_minute"]);		//数値が１桁の時前ゼロを付加し２ケタ文字列にする
	}

	$disp = sprintf("%s:%s　-　%s:%s",$start_hour,$start_minute,$end_hour,$end_minute);

	return $disp;
	exit();
}

//----時間オーバーチェック
function check_past_time_common($year,$month,$day,$time){

	$result = check_today_common($year,$month,$day);	//本日チェック

	if($result != true){
		return false;
		exit();
	}else{

		$now_hour = intval(date('H'));

		$time_array_num = count($time_array);

		for( $i=1; $i<=$time_array_num; $i++ ){

			$hour = $time_array[$i]["hour"];

			if( $hour == $now_hour ){

				if( $i > $time ){
					return true;
					exit();
				}else{
					return false;
					exit();
				}
			}
		}
	}

	return false;
	exit();
}

//----時間オーバーチェック２
function check_past_time_2_common($year,$month,$day,$time){

	$time_array = get_time_array_common();		//時刻配列取得

	$hour = $time_array[$time]["hour"];
	$minute = $time_array[$time]["minute"];

	if( $hour < 9 ){

		$num = 1;
		$tmp = get_mirai_day_common($year, $month, $day, $num);		//指定した日にち分、未来の日付取得

		$year = $tmp["year"];
		$month = $tmp["month"];
		$day = $tmp["day"];

	}

	$year_now = intval(date('Y'));
	$month_now = intval(date('m'));
	$day_now = intval(date('d'));
	$hour_now = intval(date('H'));
	$minute_now = intval(date('i'));

	$ts_now = mktime($hour_now,$minute_now,0,$month_now,$day_now,$year_now);//時・分・秒・月・日・年、の順に入力

	$ts = mktime($hour,$minute,0,$month,$day,$year);//時・分・秒・月・日・年、の順に入力

	if( $ts_now > $ts ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----タイムスタンプを年月日に変換
function get_day_by_timestamp_common($timestamp){

	$data["year"] = intval(date('Y', $timestamp));
	$data["month"] = intval(date('m', $timestamp));
	$data["day"] = intval(date('d', $timestamp));

	return $data;
	exit();
}

//----タイムスタンプを年月日に変換
function get_year_month_day_from_timestamp_common($timestamp){
	return get_day_by_timestamp_common($timestamp);
}

//----時分を時刻IDに変換
function change_attendance_num_wait_time_common($hour, $minute, $time_array){

	$time_array_num = count($time_array);

	for($i=1; $i<=$time_array_num; $i++){

		$tmp_hour = $time_array[$i]["hour"];
		$tmp_minute = $time_array[$i]["minute"];

		if( ($tmp_hour==$hour) && ($tmp_minute==$minute) ){
			return $i;
			exit();
		}
	}

	return false;
	exit();
}

//----時分を時刻IDに変換2
function change_attendance_num_wait_time_2_common($hour, $minute, $time_array, $area) {

	$time_array_num = count($time_array);

	$start_num = -1;

	if( $area == "tokyo" ) $start_num = -11;

	for($i=$start_num; $i<=$time_array_num; $i++) {

		$tmp_hour = $time_array[$i]["hour"];
		$tmp_minute = $time_array[$i]["minute"];

		if( ($tmp_hour==$hour) && ($tmp_minute==$minute) ){
			return $i;
			exit();
		}
	}
	return false;
	exit();
}

//----時分編集
function get_minute_to_hour_common($data){

	$hour = intval($data/60);

	$minute = $data - ($hour*60);

	$html = sprintf("%s時間%s分",$hour,$minute);

	return $html;
	exit();
}

//----年月日編集
function get_day_disp_by_reservation_day_common($reservation_day){

	$pieces = explode("_", $reservation_day);

	$year = $pieces[0];
	$month = $pieces[1];
	$day = $pieces[2];

	$week_name = get_week_name_by_time_common($year, $month, $day);		//日付より曜日取得

	$day_disp = sprintf('%s年　%s月%s日(%s)',$year,$month,$day,$week_name);

	return $day_disp;
	exit();
}

//----時分編集
function get_time_disp_common($time){

	$time_array = get_time_array_common();		//時刻配列取得

	$hour = $time_array[$time]["hour"];
	$minute = $time_array[$time]["minute"];

	if( $minute == "0" ) $minute = "0".$minute;

	if( $hour < 10 ) $hour += 24;

	$html = sprintf("%s:%s",$hour,$minute);

	return $html;
	exit();
}

//----時分編集
function get_time_disp_for_instruction_common($hour,$minute){

	$html = "";

	if( ($hour == "") || ($hour == "-1") || ($minute == "") || ($minute == "-1") ){
		$html = "時間未選択";
	}else{
		$minute = add_zero_when_under_ten_common($minute);
		$html = sprintf("%s時%s分",$hour,$minute);
	}

	return $html;
	exit();
}

//----n時前は24時を加算
function PHP_hour_change_over_24_common($hour, $u_limit) {
	if( $hour < $u_limit ) $hour += 24;
	return $hour;
}

//----10時前は24時を加算
function hour_change_over_24_common($hour){
	return PHP_hour_change_over_24_common($hour, 10);	//n時前は24時を加算
}

//----10時前は24時を加算
function hour_plus_24_common($hour){
	return PHP_hour_change_over_24_common($hour, 10);	//n時前は24時を加算
}

//----24時以降は24時を減算
function hour_change_not_over_24_common($hour){

	if( $hour >= 24 ) $hour = $hour - 24;

	return $hour;
	exit();
}

//----本日の年月日取得
function PHP_get_today_ymd_common($u_limit) {

	$ws_Now = PHP_getNowTime();		//現在時時刻取得 in common/include/today.inc

	$now_hour = intval(date('H', $ws_Now));

	if($now_hour < $u_limit){
		//昨日の日付
		$year = intval(date('Y', strtotime('-1 day', $ws_Now)));
		$month = intval(date('m', strtotime('-1 day', $ws_Now)));
		$day = intval(date('d', strtotime('-1 day', $ws_Now)));
	}else{
		$year = intval(date('Y', $ws_Now));
		$month = intval(date('m', $ws_Now));
		$day = intval(date('d', $ws_Now));
	}

	$data["year"] = $year;
	$data["month"] = $month;
	$data["day"] = $day;

	return $data;
}

//----営業年月日取得
function get_eigyou_day_common(){
	return PHP_get_today_ymd_common(6);		//本日の年月日取得
}

//----本日の年月日取得
function get_today_year_month_day_common(){
	return PHP_get_today_ymd_common(9);
}

//----本日の年月日取得2
function get_today_year_month_day_2_common(){
	return PHP_get_today_ymd_common(6);
}

//----本日フラグ取得
function get_today_flg_common($year,$month,$day){

	$flg = false;

	$ws_date = PHP_get_today_ymd_common(6);		//本日の年月日取得
	$now_year = $ws_date["year"];
	$now_month = $ws_date["month"];
	$now_day = $ws_date["day"];

	if( ( $year == $now_year ) && ( $month == $now_month ) && ( $day == $now_day ) ){
		$flg = true;
	}

	return $flg;
	exit();
}

//指定した日にち分、未来の日付取得
function get_mirai_day_common($year,$month,$day,$num){

	$data = array();

	$data["year"] = intval(date("Y", mktime(0, 0, 0, $month, $day+$num, $year)));
	$data["month"] = intval(date("m", mktime(0, 0, 0, $month, $day+$num, $year)));
	$data["day"] = intval(date("d", mktime(0, 0, 0, $month, $day+$num, $year)));

	return $data;
	exit();
}

//指定した日にち分、過去の日付取得
function get_kako_day_common($year,$month,$day,$num){

	$data = array();

	$data["year"] = intval(date("Y", mktime(0, 0, 0, $month, $day-$num, $year)));
	$data["month"] = intval(date("m", mktime(0, 0, 0, $month, $day-$num, $year)));
	$data["day"] = intval(date("d", mktime(0, 0, 0, $month, $day-$num, $year)));

	return $data;
	exit();
}

//----指定した月分、未来の日付取得
function get_mirai_month_common($year,$month,$num){

	$data = array();

	$day = 1;

	$data["year"] = intval(date("Y", mktime(0, 0, 0, $month+$num, $day, $year)));
	$data["month"] = intval(date("m", mktime(0, 0, 0, $month+$num, $day, $year)));
	$data["day"] = intval(date("d", mktime(0, 0, 0, $month+$num, $day, $year)));

	return $data;
	exit();
}

//----指定した月分、過去の日付取得
function get_kako_month_common($year,$month,$num){

	$data = array();

	$day = 1;

	$data["year"] = intval(date("Y", mktime(0, 0, 0, $month-$num, $day, $year)));
	$data["month"] = intval(date("m", mktime(0, 0, 0, $month-$num, $day, $year)));
	$data["day"] = intval(date("d", mktime(0, 0, 0, $month-$num, $day, $year)));

	return $data;
	exit();
}

//----本日チェック
function check_today_common($year,$month,$day){

	$data = get_today_year_month_day_common();		//本日の年月日取得

	if( ($data["year"] == $year) && ($data["month"] == $month) && ($data["day"] == $day) ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----指定日が未来か
function check_mirai_common($year,$month,$day){

	$ws_date = PHP_get_today_ymd_common(10);		//本日の年月日取得
	$now_year = $ws_date["year"];
	$now_month = $ws_date["month"];
	$now_day = $ws_date["day"];

	$ts_now = get_timestamp_by_year_month_day_common($now_year, $now_month, $now_day);	//指定年月日をタイムスタンプ形式に変換

	$ts = get_timestamp_by_year_month_day_common($year, $month, $day);	//指定年月日をタイムスタンプ形式に変換

	if( $ts_now < $ts ){
		return true;
		exit();
	}

	return false;
	exit();
}

//前日チェック
function check_last_day_for_lowest_guarantee_common($year,$month,$day){
	$ws_date = PHP_get_today_ymd_common(6);		//本日の年月日取得
	$now_year = $ws_date["year"];
	$now_month = $ws_date["month"];
	$now_day = $ws_date["day"];

	$hour = 12;
	$minute = 0;
	$second = 0;
	$tp_now = mktime($hour, $minute, $second, $now_month, $now_day, $now_year);

	$hour = 12;
	$minute = 0;
	$second = 0;
	$tp = mktime($hour, $minute, $second, $month, $day, $year);

	if( $tp < $tp_now ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----翌月取得
function get_next_month_common($year,$month){

	$day = 15;

	$data = array();

	$data["year"] = intval(date("Y", mktime(0, 0, 0, $month+1, $day, $year)));
	$data["month"] = intval(date("m", mktime(0, 0, 0, $month+1, $day, $year)));

	return $data;
	exit();
}

//前月データの取得
function get_pre_month_common($year,$month,$day){

	$data = array();

	$data["year"] = intval(date("Y", mktime(0, 0, 0, $month-1, $day, $year)));
	$data["month"] = intval(date("m", mktime(0, 0, 0, $month-1, $day, $year)));
	$data["day"] = intval(date("d", mktime(0, 0, 0, $month-1, $day, $year)));

	return $data;
	exit();
}

//----2015年9月10日以降か否か
function check_over_time_common($year,$month,$day){

	$year_tmp = 2015;
	$month_tmp = 9;
	$day_tmp = 10;

	$ts = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	$ts_tmp = get_timestamp_by_year_month_day_common($year_tmp,$month_tmp,$day_tmp);	//指定年月日をタイムスタンプ形式に変換

	if( $ts >= $ts_tmp ){

		$result = true;

	}else{

		$result = false;

	}

	return $result;
	exit();
}

//----時刻配列位置取得
function get_time_array_value_common($hour,$minute){

	$hour = intval($hour);
	$minute = intval($minute);

	if( $hour >= 30 ){
		return "25";
		exit();
	}

	$time_array = get_time_array_common();		//時刻配列取得

	$time_array_num = count($time_array);

	for( $i=1; $i<=$time_array_num; $i++ ){

		$hour_tmp = $time_array[$i]["hour"];
		$minute_tmp = $time_array[$i]["minute"];

		if( $hour_tmp < 7 ){
			$hour_tmp = $hour_tmp + 24;
		}

		if( $minute >= 30 ){
			$minute = 30;
		}else{
			$minute = 0;
		}

		if( ($hour == $hour_tmp) && ($minute == $minute_tmp) ){
			return $i;
			exit();
		}
	}

	return "-1";
	exit();
}

//----時刻配列取得
function get_time_array_common(){
global $time_array;

	return $time_array;
	exit();
}

//----ドライバー用時刻配列取得
function get_time_array_driver_common(){
global $time_array_driver;

	return $time_array_driver;
	exit();
}

//----本部スタッフ用時刻配列取得
function get_time_array_honbu_common(){
global $time_array_honbu;

	return $time_array_honbu;
	exit();
}

//----曜日番号取得
function get_week_value_common($year,$month,$day){

	$hour = 12;
	$minute = 0;
	$second = 0;
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

	$week = date('w', $timestamp);

	return $week;
	exit();

}

//----日付より曜日取得
function get_week_name_by_time_common($year, $month, $day){
	$week = get_week_value_common($year, $month, $day);
	return get_week_name_common($week);		//曜日取得
}

//----指定年月の１月前年月取得
function get_last_month_common($year,$month){

	$day = 15;

	$data["year"] = intval(date("Y", mktime(0, 0, 0, $month-1, $day, $year)));
	$data["month"] = intval(date("m", mktime(0, 0, 0, $month-1, $day, $year)));

	return $data;
	exit();
}

//----文字列長チェック
function check_string_num_over_common($data,$num){

	$data_len = mb_strlen($data,"UTF-8");

	//echo $data_len;exit();

	if( $data_len > $num ){
		return false;
		exit();
	}else{
		return true;
		exit();
	}
}

//----切り捨て処理
function kirisute_common($value,$num){

	$data = 0;

	if( ($value=="0") || ($value=="") || ($num=="0") || ($num=="") ){
		//返り値は「0」
	}else{
		$data = $value/$num;
		$data = sprintf("%.2f", $data);
		$data = intval($data*10);
		$data = $data/10;
	}

	return $data;
	exit();
}

//----　-1の時ゼロを返す
function if_mainasu_is_zero_common($data){

	if( $data == "-1" ) $data = 0;
	return $data;
}

//----率計算
function get_rate_value_common($all_num,$value_all){

	$rate_value = $value_all/$all_num;
	$rate_value = kirisute_common($rate_value,1);			//切り捨て処理
	$rate_value = add_shousuu_ten_zero_common($rate_value);	//小数点以下がなければ　.0を付加

	return $rate_value;
	exit();
}

//----割合計算
function get_int_per_common($value,$value_base){

	$data = 0;

	if( ( $value == "0" ) || ( $value_base == "0" ) || ( $value == "" ) || ( $value_base == "" ) ){
		return $data;
		exit();
	}

	$data = intval(($value/$value_base)*100);

	return $data;
	exit();
}

//----配列検索
function check_data_exist_array_common($data,$value){

	$data_num = count($data);

	$match_flg = false;

	for($i=0;$i<$data_num;$i++){

		$tmp = $data[$i];

		if( $tmp == $value ){

			$match_flg = true;		//?? breakが無いが良いか？ by aida

		}

	}

	return $match_flg;
	exit();

}

//----開始及び終了時刻取得
function change_from_time_to_hour_minute_driver_common($start_time,$end_time){
global $time_array_driver;

	$time_array = $time_array_driver;

	$time_data = get_time_value_for_sale_driver_one_common($time_array,$start_time,$end_time);	//開始及び終了時刻取得

	return $time_data;
	exit();
}

//----開始及び終了時刻取得
function get_time_value_for_sale_driver_one_common($time_array,$start_time,$end_time){

	$start_hour = hour_plus_24_common($time_array[$start_time]["hour"]);
	$start_minute = $time_array[$start_time]["minute"];

	$end_hour = hour_plus_24_common($time_array[$end_time]["hour"]);
	$end_minute = $time_array[$end_time]["minute"];

	$data["start_hour"] = $start_hour;
	$data["start_minute"] = $start_minute;
	$data["end_hour"] = $end_hour;
	$data["end_minute"] = $end_minute;

	return $data;
	exit();
}

//----時間集計
function get_work_time_driver_common($start_hour,$start_minute,$end_hour,$end_minute){

	$data = 0;

	$start_time = ($start_hour*60)+$start_minute;
	$end_time = ($end_hour*60)+$end_minute;

	$sa = $end_time - $start_time;

	if( $sa < 60 ){
		$data = 0;

		return $data;
		exit();

	}else{

		$value_ten = intval($sa/60);
		$value_ichi = $sa % 60;

		if( $value_ichi == "0" ){
			$num_small = "0";
		}else if( $value_ichi == "10" ){
			$num_small = "0.17";
		}else if( $value_ichi == "20" ){
			$num_small = "0.33";
		}else if( $value_ichi == "30" ){
			$num_small = "0.5";
		}else if( $value_ichi == "40" ){
			$num_small = "0.67";
		}else if( $value_ichi == "50" ){
			$num_small = "0.83";
		}else{
			$data = 0;

			return $data;
			exit();
		}

		$data = $value_ten + $num_small;
	}

	return $data;
	exit();
}

//----指定年月日をタイムスタンプ形式に変換
function PHP_getTimeStamp($u_mode, $year, $month, $day) {
	$hour = $u_mode;
	$minute = 0;
	$second = 0;
	if($u_mode == 23) {
		$minute = 59;
		$second = 59;
	}
	return $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
}

//----本日のタイムスタンプ取得
function get_today_ts_common(){

	$year = intval(date('Y'));
	$month = intval(date('m'));
	$day = intval(date('d'));

	$ts = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	return $ts;
	exit();
}

//----指定年月日をタイムスタンプ形式に変換
function get_timestamp_by_year_month_day_common($year,$month,$day){
	return PHP_getTimeStamp(12, $year, $month, $day);		//指定年月日をタイムスタンプ形式に変換
}

//----指定年月日をタイムスタンプ形式に変換
function get_timestamp_by_year_month_day_2_common($year,$month,$day){
	return PHP_getTimeStamp(0, $year, $month, $day);		//指定年月日をタイムスタンプ形式に変換
}

//----指定年月日をタイムスタンプ形式に変換
function get_timestamp_by_year_month_day_3_common($year,$month,$day){
	return PHP_getTimeStamp(23, $year, $month, $day);		//指定年月日をタイムスタンプ形式に変換
}

//----営業日をタイムスタンプ形式で返す
function get_timestamp_now_common(){

	$ws_date = get_eigyou_day_common();		//営業年月日取得

	//$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換
	$timestamp = PHP_getTimeStamp(12, $ws_date["year"], $ws_date["month"], $ws_date["day"]);		//指定年月日をタイムスタンプ形式に変換

	return $timestamp;
	exit();
}

//----開始及び終了時刻チェック
function check_start_end_hour_minute_common($start_hour,$start_minute,$end_hour,$end_minute){

	$result = true;

	if( $start_hour > $end_hour ){
		$result = false;
	}else if( $start_hour == $end_hour ){
		if( $start_minute >= $end_minute ){
			$result = false;
		}
	}

	return $result;
	exit();
}

//前日チェック(報酬計算用)(過去であればTRUE)
function check_last_day_for_remuneration_common($year,$month,$day){

	$ws_date = PHP_get_today_ymd_common(1);		//本日の年月日取得
	$now_year = $ws_date["year"];
	$now_month = $ws_date["month"];
	$now_day = $ws_date["day"];

	$hour = 12;
	$minute = 0;
	$second = 0;
	$tp_now = mktime($hour, $minute, $second, $now_month, $now_day, $now_year);

	$hour = 12;
	$minute = 0;
	$second = 0;
	$tp = mktime($hour, $minute, $second, $month, $day, $year);

	if( $tp < $tp_now ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

function get_hour_minute_unit_10_common($hour,$minute){

	if( $minute > 50 ){
		$hour = $hour + 1;
		$minute = 0;
	}else if( $minute > 40 ){
		$minute = 50;
	}else if( $minute > 30 ){
		$minute = 40;
	}else if( $minute > 20 ){
		$minute = 30;
	}else if( $minute > 10 ){
		$minute = 20;
	}else if( $minute > 0 ){
		$minute = 10;
	}

	$data["hour"] = $hour;
	$data["minute"] = $minute;

	return $data;
	exit();
}

function get_now_hour_minute_unit_10_24_common(){

	$now = time();
	$now_hour = intval(date('H', $now));
	$now_minute = intval(date('i', $now));
	$now_hour = hour_plus_24_common($now_hour);
	$data = get_hour_minute_unit_10_common($now_hour,$now_minute);

	return $data;
	exit();
}

//前日チェック(報酬計算用)(過去であればTRUE)
function check_past_day_for_remuneration_common($year,$month,$day){

	$ws_date = PHP_get_today_ymd_common(6);		//本日の年月日取得
	$now_year = $ws_date["year"];
	$now_month = $ws_date["month"];
	$now_day = $ws_date["day"];

	$hour = 12;
	$minute = 0;
	$second = 0;
	$tp_now = mktime($hour, $minute, $second, $now_month, $now_day, $now_year);

	$hour = 12;
	$minute = 0;
	$second = 0;
	$tp = mktime($hour, $minute, $second, $month, $day, $year);

	if( $tp < $tp_now ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----本日が指定日より大きいか
function today_past_check_common($year,$month,$day){

	$today_year = intval(date("Y"));
	$today_month = intval(date("m"));
	$today_day = intval(date("d"));

	$hour = 12;
	$minute = 0;
	$second = 0;

	$today_timestamp = mktime($hour, $minute, $second, $today_month, $today_day, $today_year);

	$hikisuu_timestamp = mktime($hour, $minute, $second, $month, $day, $year);

	if( $today_timestamp > $hikisuu_timestamp ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//前日チェック
function check_last_day_common($year,$month,$day){

	$ws_date = PHP_get_today_ymd_common(6);		//本日の年月日取得
	$now_year = $ws_date["year"];
	$now_month = $ws_date["month"];
	$now_day = $ws_date["day"];

	$hour = 12;
	$minute = 0;
	$second = 0;
	$tp_now = mktime($hour, $minute, $second, $now_month, $now_day, $now_year);

	$hour = 12;
	$minute = 0;
	$second = 0;
	$tp = mktime($hour, $minute, $second, $month, $day, $year);

	if( $tp < $tp_now ){
		return true;
		exit();
	}else{
		return false;
		exit();
	}
}

//----時期(2015年以降か)による固定ポイントチェック
function get_share_rate_by_point_fix_common($point_fix,$point_fix_start_time,$year,$month,$day){

	$timestamp = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換

	if( $point_fix == "0" ){
		return false;
		exit();
	}else{

		if( $point_fix_start_time == "0" ){
			$year = 2015;
			$month = 1;
			$day = 1;

			$point_fix_start_time = get_timestamp_by_year_month_day_common($year,$month,$day);	//指定年月日をタイムスタンプ形式に変換
		}
	}

	if( $timestamp < $point_fix_start_time ){
			return false;
			exit();
	}

	return $point_fix;
	exit();
}

//----2ヶ月分の日付配列取得
function get_day_data_two_month_common($year,$month,$max_day,$next_month_year,$next_month_month,$next_month_max_day){

	$data = array();

	for($i=0;$i<$max_day;$i++){

		$data[$i]["year"] = $year;
		$data[$i]["month"] = $month;
		$day = $i+1;
		$data[$i]["day"] = $day;
		$w = intval(date("w", mktime(0, 0, 0, $month, $day, $year)));
		$data[$i]["week"] = get_ja_week_common($w);	//曜日の取得

	}

	$data_num = count($data);

	$max_sum_num = $max_day + $next_month_max_day;

	$x = 0;

	for($i=$data_num;$i<$max_sum_num;$i++){

		$data[$i]["year"] = $next_month_year;
		$data[$i]["month"] = $next_month_month;
		$day = $x+1;
		$data[$i]["day"] = $day;
		$w = intval(date("w", mktime(0, 0, 0, $next_month_month, $day, $next_month_year)));
		$data[$i]["week"] = get_ja_week_common($w);	//曜日の取得

		$x++;

	}

	return $data;
	exit();
}

//----曜日の取得
function get_ja_week_common($w){
global $ARRAY_Week;

	return $ARRAY_Week[$w];
}

//----曜日取得
function get_week_name_common($week){
global $ARRAY_Week;

	if($week >= 0 && $week <= 6) {
		$data = $ARRAY_Week[$week];
	} else {
		$data = "不明";
	}

	return $data;
}

//----前日の年月日取得
function get_pre_day_for_repeater_stock_common(){

	$ws_date = get_eigyou_day_common();		//営業年月日取得

	$data = get_pre_day_common($ws_date["year"], $ws_date["month"], $ws_date["day"]);		//指定日前日の年月日取得

	return $data;
}

//----指定年月の末日取得
function get_month_last_day_common($year,$month){

	$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

	return $last_day;
	exit();
}

//----前月末日取得
function get_pre_month_last_day_common($year,$month){

	$day = "10";

	$data = get_pre_month_common($year,$month,$day);	//前月データの取得

	$year = $data["year"];
	$month = $data["month"];

	$last_day = get_last_day_common($year,$month);		//指定年月の末日取得

	return $last_day;
	exit();
}

//----指定年月の末日取得
function get_last_day_common($year,$month){
	return get_month_last_day_common($year,$month);		//指定年月の末日取得
}

//----指定年月の末日取得
function get_max_day_common($year,$month){

	$uru_flag = false;

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
		if($uru_flag==true){
			$day = 29;
		}else{
			$day = 28;
		}
	}else{
		if(($month==4)||($month==6)||($month==9)||($month==11)){
			$day = 30;
		}else{
			$day = 31;
		}
	}

	return $day;
	exit();
}

//----表示日付配列取得
function get_day_array_for_disp_common($data){

	$tmp = get_eigyou_day_common();	//営業年月日取得

	$year_now = $tmp["year"];
	$month_now = $tmp["month"];
	$day_now = $tmp["day"];

	$data_num = count($data);

	for($i=0;$i<$data_num;$i++){

		$year = $data[$i]["year"];
		$month = $data[$i]["month"];
		$day = $data[$i]["day"];
		$week_name = $data[$i]["week_name"];

		if( ($year==$year_now) && ($month==$month_now) && ($day==$day_now) ){
			$disp = sprintf("本日【%s/%s(%s)】",$month,$day,$week_name);
		}else{
			$disp = sprintf("%s/%s(%s)",$month,$day,$week_name);
		}

		$data[$i]["disp"] = $disp;

	}

	return $data;
	exit();
}

//----振込日数取得
function get_kikan_data_for_furikomi_day_common($year,$month,$day){

	$week = get_week_value_common($year,$month,$day);	//曜日番号取得

	if( $week == "0" ){
		$num_kako = 3;
		$num_mirai = 3;
	}else if( $week == "1" ){
		$num_kako = 4;
		$num_mirai = 2;
	}else if( $week == "2" ){
		$num_kako = 5;
		$num_mirai = 1;
	}else if( $week == "3" ){
		//end suiyou
		$num_kako = 6;
		$num_mirai = 0;
	}else if( $week == "4" ){
		//start mokuyou
		$num_kako = 0;
		$num_mirai = 6;
	}else if( $week == "5" ){
		$num_kako = 1;
		$num_mirai = 5;
	}else if( $week == "6" ){
		$num_kako = 2;
		$num_mirai = 4;
	}else{
		echo "error!(get_kikan_data_for_furikomi_day_common)";
		exit();
	}

	$kako_day = get_kako_day_common($year,$month,$day,$num_kako);		//指定した日にち分、過去の日付取得
	$mirai_day = get_mirai_day_common($year,$month,$day,$num_mirai);	//指定した日にち分、未来の日付取得

	$data["kako_day"] = $kako_day;
	$data["mirai_day"] = $mirai_day;

	return $data;
	exit();
}

//----振替日配列取得
function get_week_data_for_furikae_list_common($kako_day){

	$year = $kako_day["year"];
	$month = $kako_day["month"];
	$day = $kako_day["day"];

	$data = array();

	for($i=0;$i<7;$i++){
		$data[$i]["year"] = $year;
		$data[$i]["month"] = $month;
		$data[$i]["day"] = $day;

		$mirai_day = get_mirai_day_common($year,$month,$day,1);		//指定した日にち分、未来の日付取得

		$year = $mirai_day["year"];
		$month = $mirai_day["month"];
		$day = $mirai_day["day"];
	}

	return $data;
	exit();
}

//----スキル文字列編集
function disp_skill_string_common($skill){

	include(COMMON_INC."skill_data.php");

	$html = "";

	$skill_data_num = count($skill_data);

	$skill_arr = explode(",",$skill);

	$skill_arr_num = count($skill_arr);

	for($i=0;$i<$skill_arr_num;$i++){

		$html .= $skill_data[$skill_arr[$i]]."　";

	}

	return $html;
	exit();

}

//----スキルチェック
function check_skill_exist_common($value,$skill){

	$skill_num = count($skill);

	for($i=0;$i<$skill_num;$i++){

		if($value==$skill[$i]){
			return true;
			exit();
		}
	}

	return false;
	exit();
}



//----ドライバー状態取得
function get_driver_state_disp_common($type,$state){

	$html = "";

	if( $type == "okuri" ){

		if( $state == "1" ){

			$html = "確認";

		}else if( $state == "2" ){

			$html = "着予定";

		}else if( $state == "3" ){

			$html = "到着待機";

		}else if( $state == "9" ){

			$html = "降車";

		}else{

			$html = "未選択";

		}

	}else if( $type == "mukae" ){

		if( $state == "1" ){

			$html = "確認";

		}else if( $state == "2" ){

			$html = "着予定";

		}else if( $state == "3" ){

			$html = "到着待機";

		}else if( $state == "4" ){

			$html = "合流";

		}else if( $state == "9" ){

			$html = "降車";

		}else{

			$html = "未選択";

		}

	}else{

		$html = "未選択";

	}

	return $html;
	exit();

}

//----項目名取得
function get_type_name_therapist_report_common($type){

	$data = "不明";

	if($type=="1"){
		$data = "施術数";
	}else if($type=="2"){
		$data = "新規数";
	}else if($type=="3"){
		$data = "指名数";
	}else if($type=="4"){
		$data = "リピーター数";
	}else if($type=="5"){
		$data = "リピーター獲得率";
	}else if($type=="6"){
		$data = "指名率";
	}

	return $data;
}

//----項目名取得(記号) insert by aida
function PHP_get_type_id_name_therapist_report_common($type){

	$ranking_name = "repeater_rate_ranking";

	if($type=="1"){
		//施術数
		$ranking_name = "all_num";
	}else if($type=="2"){
		//新規数
		$ranking_name = "new_num";
	}else if($type=="3"){
		//指名数
		$ranking_name = "shimei_num";
	}else if($type=="4"){
		//リピーター数
		$ranking_name = "repeater_num";
	}else if($type=="5"){
		//リピーター獲得率
		$ranking_name = "repeater_rate_ranking";
	}else if($type=="6"){
		//指名率
		$ranking_name = "shimei_rate_ranking";
	}

	return $ranking_name;
}

function format_phone_number($input, $strict = false) {
    $groups = array(
        5 =>
        array (
            '01564' => 1,
            '01558' => 1,
            '01586' => 1,
            '01587' => 1,
            '01634' => 1,
            '01632' => 1,
            '01547' => 1,
            '05769' => 1,
            '04992' => 1,
            '04994' => 1,
            '01456' => 1,
            '01457' => 1,
            '01466' => 1,
            '01635' => 1,
            '09496' => 1,
            '08477' => 1,
            '08512' => 1,
            '08396' => 1,
            '08388' => 1,
            '08387' => 1,
            '08514' => 1,
            '07468' => 1,
            '01655' => 1,
            '01648' => 1,
            '01656' => 1,
            '01658' => 1,
            '05979' => 1,
            '04996' => 1,
            '01654' => 1,
            '01372' => 1,
            '01374' => 1,
            '09969' => 1,
            '09802' => 1,
            '09912' => 1,
            '09913' => 1,
            '01398' => 1,
            '01377' => 1,
            '01267' => 1,
            '04998' => 1,
            '01397' => 1,
            '01392' => 1,
        ),
        4 =>
        array (
            '0768' => 2,
            '0770' => 2,
            '0772' => 2,
            '0774' => 2,
            '0773' => 2,
            '0767' => 2,
            '0771' => 2,
            '0765' => 2,
            '0748' => 2,
            '0747' => 2,
            '0746' => 2,
            '0826' => 2,
            '0749' => 2,
            '0776' => 2,
            '0763' => 2,
            '0761' => 2,
            '0766' => 2,
            '0778' => 2,
            '0824' => 2,
            '0797' => 2,
            '0796' => 2,
            '0555' => 2,
            '0823' => 2,
            '0798' => 2,
            '0554' => 2,
            '0820' => 2,
            '0795' => 2,
            '0556' => 2,
            '0791' => 2,
            '0790' => 2,
            '0779' => 2,
            '0558' => 2,
            '0745' => 2,
            '0794' => 2,
            '0557' => 2,
            '0799' => 2,
            '0738' => 2,
            '0567' => 2,
            '0568' => 2,
            '0585' => 2,
            '0586' => 2,
            '0566' => 2,
            '0564' => 2,
            '0565' => 2,
            '0587' => 2,
            '0584' => 2,
            '0581' => 2,
            '0572' => 2,
            '0574' => 2,
            '0573' => 2,
            '0575' => 2,
            '0576' => 2,
            '0578' => 2,
            '0577' => 2,
            '0569' => 2,
            '0594' => 2,
            '0827' => 2,
            '0736' => 2,
            '0735' => 2,
            '0725' => 2,
            '0737' => 2,
            '0739' => 2,
            '0743' => 2,
            '0742' => 2,
            '0740' => 2,
            '0721' => 2,
            '0599' => 2,
            '0561' => 2,
            '0562' => 2,
            '0563' => 2,
            '0595' => 2,
            '0596' => 2,
            '0598' => 2,
            '0597' => 2,
            '0744' => 2,
            '0852' => 2,
            '0956' => 2,
            '0955' => 2,
            '0954' => 2,
            '0952' => 2,
            '0957' => 2,
            '0959' => 2,
            '0966' => 2,
            '0965' => 2,
            '0964' => 2,
            '0950' => 2,
            '0949' => 2,
            '0942' => 2,
            '0940' => 2,
            '0930' => 2,
            '0943' => 2,
            '0944' => 2,
            '0948' => 2,
            '0947' => 2,
            '0946' => 2,
            '0967' => 2,
            '0968' => 2,
            '0987' => 2,
            '0986' => 2,
            '0985' => 2,
            '0984' => 2,
            '0993' => 2,
            '0994' => 2,
            '0997' => 2,
            '0996' => 2,
            '0995' => 2,
            '0983' => 2,
            '0982' => 2,
            '0973' => 2,
            '0972' => 2,
            '0969' => 2,
            '0974' => 2,
            '0977' => 2,
            '0980' => 2,
            '0979' => 2,
            '0978' => 2,
            '0920' => 2,
            '0898' => 2,
            '0855' => 2,
            '0854' => 2,
            '0853' => 2,
            '0553' => 2,
            '0856' => 2,
            '0857' => 2,
            '0863' => 2,
            '0859' => 2,
            '0858' => 2,
            '0848' => 2,
            '0847' => 2,
            '0835' => 2,
            '0834' => 2,
            '0833' => 2,
            '0836' => 2,
            '0837' => 2,
            '0846' => 2,
            '0845' => 2,
            '0838' => 2,
            '0865' => 2,
            '0866' => 2,
            '0892' => 2,
            '0889' => 2,
            '0887' => 2,
            '0893' => 2,
            '0894' => 2,
            '0897' => 2,
            '0896' => 2,
            '0895' => 2,
            '0885' => 2,
            '0884' => 2,
            '0869' => 2,
            '0868' => 2,
            '0867' => 2,
            '0875' => 2,
            '0877' => 2,
            '0883' => 2,
            '0880' => 2,
            '0879' => 2,
            '0829' => 2,
            '0550' => 2,
            '0228' => 2,
            '0226' => 2,
            '0225' => 2,
            '0224' => 2,
            '0229' => 2,
            '0233' => 2,
            '0237' => 2,
            '0235' => 2,
            '0234' => 2,
            '0223' => 2,
            '0220' => 2,
            '0192' => 2,
            '0191' => 2,
            '0187' => 2,
            '0193' => 2,
            '0194' => 2,
            '0198' => 2,
            '0197' => 2,
            '0195' => 2,
            '0238' => 2,
            '0240' => 2,
            '0260' => 2,
            '0259' => 2,
            '0258' => 2,
            '0257' => 2,
            '0261' => 2,
            '0263' => 2,
            '0266' => 2,
            '0265' => 2,
            '0264' => 2,
            '0256' => 2,
            '0255' => 2,
            '0243' => 2,
            '0242' => 2,
            '0241' => 2,
            '0244' => 2,
            '0246' => 2,
            '0254' => 2,
            '0248' => 2,
            '0247' => 2,
            '0186' => 2,
            '0185' => 2,
            '0144' => 2,
            '0143' => 2,
            '0142' => 2,
            '0139' => 2,
            '0145' => 2,
            '0146' => 2,
            '0154' => 2,
            '0153' => 2,
            '0152' => 2,
            '0138' => 2,
            '0137' => 2,
            '0125' => 2,
            '0124' => 2,
            '0123' => 2,
            '0126' => 2,
            '0133' => 2,
            '0136' => 2,
            '0135' => 2,
            '0134' => 2,
            '0155' => 2,
            '0156' => 2,
            '0176' => 2,
            '0175' => 2,
            '0174' => 2,
            '0178' => 2,
            '0179' => 2,
            '0184' => 2,
            '0183' => 2,
            '0182' => 2,
            '0173' => 2,
            '0172' => 2,
            '0162' => 2,
            '0158' => 2,
            '0157' => 2,
            '0163' => 2,
            '0164' => 2,
            '0167' => 2,
            '0166' => 2,
            '0165' => 2,
            '0267' => 2,
            '0250' => 2,
            '0533' => 2,
            '0422' => 2,
            '0532' => 2,
            '0531' => 2,
            '0436' => 2,
            '0428' => 2,
            '0536' => 2,
            '0299' => 2,
            '0294' => 2,
            '0293' => 2,
            '0475' => 2,
            '0295' => 2,
            '0297' => 2,
            '0296' => 2,
            '0495' => 2,
            '0438' => 2,
            '0466' => 2,
            '0465' => 2,
            '0467' => 2,
            '0478' => 2,
            '0476' => 2,
            '0470' => 2,
            '0463' => 2,
            '0479' => 2,
            '0493' => 2,
            '0494' => 2,
            '0439' => 2,
            '0268' => 2,
            '0480' => 2,
            '0460' => 2,
            '0538' => 2,
            '0537' => 2,
            '0539' => 2,
            '0279' => 2,
            '0548' => 2,
            '0280' => 2,
            '0282' => 2,
            '0278' => 2,
            '0277' => 2,
            '0269' => 2,
            '0270' => 2,
            '0274' => 2,
            '0276' => 2,
            '0283' => 2,
            '0551' => 2,
            '0289' => 2,
            '0287' => 2,
            '0547' => 2,
            '0288' => 2,
            '0544' => 2,
            '0545' => 2,
            '0284' => 2,
            '0291' => 2,
            '0285' => 2,
            '0120' => 3,
            '0570' => 3,
            '0800' => 3,
            '0990' => 3,
        ),
        3 =>
        array (
            '099' => 3,
            '054' => 3,
            '058' => 3,
            '098' => 3,
            '095' => 3,
            '097' => 3,
            '052' => 3,
            '053' => 3,
            '011' => 3,
            '096' => 3,
            '049' => 3,
            '015' => 3,
            '048' => 3,
            '072' => 3,
            '084' => 3,
            '028' => 3,
            '024' => 3,
            '076' => 3,
            '023' => 3,
            '047' => 3,
            '029' => 3,
            '075' => 3,
            '025' => 3,
            '055' => 3,
            '026' => 3,
            '079' => 3,
            '082' => 3,
            '027' => 3,
            '078' => 3,
            '077' => 3,
            '083' => 3,
            '022' => 3,
            '086' => 3,
            '089' => 3,
            '045' => 3,
            '044' => 3,
            '092' => 3,
            '046' => 3,
            '017' => 3,
            '093' => 3,
            '059' => 3,
            '073' => 3,
            '019' => 3,
            '087' => 3,
            '042' => 3,
            '018' => 3,
            '043' => 3,
            '088' => 3,
            '050' => 4,
        ),
        2 =>
        array (
            '04' => 4,
            '03' => 4,
            '06' => 4,
        ),
    );
    $groups[3] +=
        $strict ?
        array(
            '020' => 3,
            '070' => 3,
            '080' => 3,
            '090' => 3,
        ) :
        array(
            '020' => 4,
            '070' => 4,
            '080' => 4,
            '090' => 4,
        )
    ;
    $number = preg_replace('/[^\d]++/', '', $input);
    foreach ($groups as $len => $group) {
        $area = substr($number, 0, $len);
        if (isset($group[$area])) {
            $formatted = implode('-', array(
                $area,
                substr($number, $len, $group[$area]),
                substr($number, $len + $group[$area])
            ));
            return strrchr($formatted, '-') !== '-' ? $formatted : $input;
        }
    }
    $pattern = '/\A(00(?:[013-8]|2\d|91[02-9])\d)(\d++)\z/';
    if (preg_match($pattern, $number, $matches)) {
        return $matches[1] . '-' . $matches[2];
    }
    return $input;
}
?>
