<?php
/* =================================================================================
		Script Name	func_reserv.php( 横浜のinclude/functions.phpのコピー )
		Update Date	2018/03/27	by aida
================================================================================= */

//----仮予約データ登録
function PHP_insertReservationPre($u_data) {
global $access_type;

	$ws_ymd = toZero($u_data["ymd"]);
	$ws_shop_id = toZero($u_data['shop_id']);
	$ws_name = toNull($u_data['customer_name']);
	$ws_tel = toNull($u_data['customer_tel']);
	$ws_customer_id = toZero($u_data['customer_id']);
	if(ctype_digit($u_data['course'])) {
		$ws_course_tm = toZero($u_data['course']);
	} else {
		$ws_course_tm = -1;
	}
	$ws_mail = toNull($u_data['mail']);
	$ws_ward = toNull($u_data['ward']);
	$ws_address1 = toNull($u_data['address1']);
	$ws_address2 = toNull($u_data['address2']);
	$ws_otherwise = toNull($u_data['otherwise']);
	$ws_Name_db = toNull($u_data["Name_db"]);
	$ws_hotel_id = toZero($u_data["hotel_id"]);
	$ws_therapist_id = toZero($u_data['therapist_id']);
	$ws_attendance_id = toZero($u_data['attendance_id']);
	$ws_old_reservId = toZero($u_data['old_attendance_id']);
	$ws_vipflg = toZero($u_data['vipflg']);		//VIPフラグ

	$ws_acstyp = 0;
	if($access_type == "m") {
		$ws_acstyp = 2;
	} elseif($access_type == "sp") {
		$ws_acstyp = 1;
	}

	$ws_simei_flg = 0;
	if($u_data['therapist_id'] > 0) $ws_simei_flg = 1;

	$ws_tm = toZero(str_replace(":", "", $u_data['start']));

	$ws_Nums = 0;
	$sql = sprintf("select count(*) as nums from reservation_pre where rsvymd=%s and rsv_tm=%s and customer_tel=%s and corstm=%s", $ws_ymd, $ws_tm, $ws_tel, $ws_course_tm);
	$res = mysql_query($sql, DbCon);
	//echo $res . "/" . $sql . "<br />";
	if($res) {
		if($row = mysql_fetch_assoc($res)) {
			$ws_Nums = $row["nums"];
		}
	}

	if($ws_Nums == 0) {
		$ws_SQL = "insert into reservation_pre(crtime,rsvymd,rsv_tm,customer_name,customer_tel,customer_id,customer_ml,at_ku,at_adrs,hotlid,hotlnm,shop_id,corstm,shimei_flg,therapist_id,attendance_id,vipflg,memo,db_nam,db_old,acstyp)";
		$ws_SQL .= " values(now(),%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)";
		$sql = sprintf($ws_SQL, $ws_ymd, $ws_tm, $ws_name, $ws_tel, $ws_customer_id, $ws_mail, $ws_ward, $ws_address1, $ws_hotel_id, $ws_address2, $ws_shop_id, $ws_course_tm, $ws_simei_flg, $ws_therapist_id, $ws_attendance_id, $ws_vipflg, $ws_otherwise, $ws_Name_db, $ws_old_reservId, $ws_acstyp);
		$res = mysql_query($sql, DbCon);
		//echo $res . "/" . $sql . "<br />" . mysql_affected_rows() . "**<br />";
		if($res) {
			return "ok";
		} else {
			return $sql;
		}
	} else {
		return "ok";
	}
}

    function PHP_checkDuplicate($area,$day,$start,$course,$therapist_id) {

        //重複登録をチェック
        if(empty($area)||empty($day)||empty($start)||empty($course)||empty($therapist_id)){
           return true;
        }

        if($therapist_id=="-1"){
            return true;
        }
        $check_flg = true;
        $course = intval(str_replace("分","",str_replace("+リフレ","",$course)));
        $arr1 = explode('-',$day);
        $arr2 = explode(':',$start);
        $hour = intval($arr2[0]);
        $minute = intval($arr2[1]);

        $arr3 = PHP_getTimeFromCourse($hour,$minute,$course,true); //in local

        if($hour>=24){
            $hour -= 24;
        }

        if($minute<30) {$time=$hour.":00";}else{$time=$hour.":30";}
        $time_index = get_index_by_time($time);
        //出勤チェック
		/*
        $sql = sprintf("
            select id from attendance_new where
            today_absence='0' and
            kekkin_flg='0' and
            area='%s' and
            year='%s' and
            month='%s' and
            day='%s' and
            start_time<=%s and
            end_time>=%s and
            therapist_id=%s",
            $area,$arr1[0],$arr1[1],$arr1[2],$time_index,$time_index,$therapist_id);
		*/
        $sql = sprintf("select id from attendance_new where today_absence='0' and kekkin_flg='0' and year='%s' and month='%s' and day='%s' and start_time<=%s and end_time>=%s and therapist_id=%s",$arr1[0],$arr1[1],$arr1[2],$time_index,$time_index,$therapist_id);
        $res = mysql_query($sql, DbCon);
        $row = mysql_fetch_assoc($res);

        if(empty($row)){
            return false;
        }
        $attendance_id = $row['id'];

        //予約チェック
        $sql = sprintf("select * from reservation_for_board where year='%s' and month='%s' and day='%s' and delete_flg=0 and attendance_id='%s'",$arr1[0],$arr1[1],$arr1[2],$attendance_id);
        $res = mysql_query($sql, DbCon);

		$check_start = $arr3['start_hour'] * 60 + $arr3['start_minute'];
		$check_end = $arr3['end_hour'] * 60 + $arr3['end_minute'];

        while($row = mysql_fetch_assoc($res)){
            $r_start = $row['start_hour'] * 60 + $row['start_minute'];
            $r_end = $row['end_hour'] * 60 + $row['end_minute'];

            if($r_start < $check_start){
                if($check_start < $r_end){
                    $check_flg = false;
                }
            }
            else{
                if($check_end > $r_start){
                    $check_flg = false;
                }
            }
//echo $row['start_hour'] . ":" . $row['start_minute'] . " -> " . $row['end_hour'] . ":" . $row['end_minute'] . "<br />";
//echo $r_start . " - " . $r_end . " " . $check_start . " - " . $check_end . " ==> " . $check_flg . "***<br />";
        }

        return $check_flg;
        exit();

    }

    /****************************/
    /*         予約登録         */
    /* [reservation_new]        */
    /* [reservation_for_board   */
    /* [sale_history]           */
    /****************************/

    function PHP_exeReservation($params){

        $now = time();
        $attendance_id = "-1";
        $customer_id = "-1";
        $new_flg = "1";
        $repeat_flg = "0";
        $tel = str_replace("-","",$params['tel']);
        //予約番号生成
        //$sql = "select max(reservation_no) as no from reservation_for_board";
        $sql = "select max(reservation_no) as no from reservation_for_board where reservation_no<100000000";
        $res = mysql_query($sql, DbCon);
        $row = mysql_fetch_assoc($res);
		/*
        if($row['no']<=100000000){
            $reservation_no = $row['no'] + 1;
        }
        else{
            $reservation_no = 100000000;
        }
		*/
		$reservation_no = $row['no'] + 1;

        //リーピート・常連チェック
        //新規：new_flg =1, repeat_flg=0
        //リピーター：new_flg =0, repeat_flg=1
        //常連：new_flg =0, repeat_flg=0
        $sql = sprintf("select count(customer_tel) as cnt from reservation_for_board where customer_tel='%s' and shop_name='%s' and complete_flg =1 and delete_flg=0",$tel,$params['shop_name']);
        $res = mysql_query($sql, DbCon);
		//echo $res . "/" . $sql . "<br />";
        $row = mysql_fetch_assoc($res);
        if(!empty($row)){
            if($row['cnt'] == 1){
                $new_flg = "0";
                $repeat_flg = "1";
            }
            else if($row['cnt'] > 1){
                $new_flg = "0";
            }
        }

        //その他パラメタ生成
        $shimei_flg = 0;
        $course = intval(str_replace("分","",str_replace("+リフレ","",$params['course'])));

        //$discount = getDiscountByCourse($course);
		$discount = PHP_getDiscountNameByCourse($course, $params['ArCorse']);
        $arr1 = explode('-',$params['day']);
        $arr2 = explode(':',$params['start']);
        $arr3 = PHP_getTimeFromCourse(intval($arr2[0]),intval($arr2[1]),$course,false);

        $year = intval($arr1[0]);
        $month = intval($arr1[1]);
        $day = intval($arr1[2]);

        if(!empty($params['therapist_id']) && ($params['therapist_id'] != "-1")){
            $shimei_flg = "1";
            $attendance_id = PHP_get_attendance_id($params['therapist_id'],$year,$month,$day,$params['area']);
        } else{
            $params['therapist_id'] = "-1";
        }

        $time_len = PHP_get_time_len($arr3['start_hour'],$arr3['start_minute'],$arr3['end_hour'],$arr3['end_minute']);

        /******************/
        /*   INSERT開始   */
        /******************/
        mysql_query("SET AUTOCOMMIT = 0", DbCon); //オートコミット無効
        mysql_query("begin", DbCon); //トランザクション開始

        //【テーブル】sale_history
        $sql = sprintf("insert into sale_history(created,updated,reservation_no,therapist_id,area,eigyou_year,eigyou_month,eigyou_day,price) values('%s','%s','%s','%s','%s','%s','%s','%s','%s')",$now,$now,$reservation_no,$params['therapist_id'],$params['area'],$year,$month,$day,$params['total_charge']);
            $res = mysql_query($sql, DbCon);
			//echo "line:" . __LINE__ . " " . $res . "/" . $sql . "<br />";
            if($res == false){
                mysql_query("rollback", DbCon);
                return false;
            }

        //【テーブル】reservation_for_board
        $sql = sprintf("
            insert into reservation_for_board(
            attendance_id,year,month,day,start_hour,start_minute,end_hour,end_minute,
            time_len,shop_name,customer_name,area_name,orders_route,shop_area,course,course_var,transportation,discount,
            new_flg,shimei_flg,reservation_no,customer_tel,repeat_flg,otherwise)
            values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
            $attendance_id,$year,$month,$day,$arr3['start_hour'],$arr3['start_minute'],$arr3['end_hour'],$arr3['end_minute'],
            $time_len,$params['shop_name'],$params['name'],$params['ward_address'],$params['orders_route'],$params['area'],$course,$params['course'],$params['transportation'],
            $discount,$new_flg,$shimei_flg,$reservation_no,$params['tel'],$repeat_flg,$params['otherwise']);
            $res = mysql_query($sql, DbCon);
			//echo "line:" . __LINE__ . " " . $res . "/" . $sql . "<br />";

            if($res == false){
                mysql_query("rollback", DbCon);
                return false;
            }

        //状況ボードＩＤ取得
        $reservation_for_board_id = mysqli_insert_id();

        //【テーブル】reservation_new
        $start_hour = intval($arr3['start_hour']);
        $start_minute = intval($arr3['start_minute']);
        $end_hour = intval($arr3['end_hour']);
        $end_minute = intval($arr3['end_minute']);
        if( $start_minute >= 30 ){
                $start_minute = 30;
        }else{
                $start_minute = 0;
        }
        if( $end_minute >= 30 ){
                $end_minute = 30;
        }else{
                $end_minute = 0;
        }
        if( $start_hour >= 24 ){
                $start_hour = $start_hour - 24;
        }
        if( $end_hour >= 24 ){
                $end_hour = $end_hour - 24;
        }
        if( $start_minute < 10 ){
                $start_minute = "0".$start_minute;
        }
        if( $end_minute < 10 ){
                $end_minute = "0".$end_minute;
        }
        $start = $start_hour.":".$start_minute;
        $end = $end_hour.":".$end_minute;
        $from_time = get_index_by_time($start) - 1;//30分前
        $to_time = get_index_by_time($end);

        $reservation_for_board_id = mysql_insert_id();
        for( $j = $from_time; $j <= $to_time; $j++){
            $sql = sprintf("insert into reservation_new(attendance_id,area,time,reservation_for_board_id,auto_flg) values('%s','%s','%s','%s','%s')",$attendance_id,$params['area'],$j,$reservation_for_board_id,1);
            $res = mysql_query($sql, DbCon);
			//echo "line:" . __LINE__ . " " . $res . "/" . $sql . "<br />";
            if($res == false){
                mysql_query("rollback", DbCon);
                return false;
            }

        }
        mysql_query("commit", DbCon);

        return true;
        exit();
    }
//ok
    /****************/
    /*    共通用    */
    /****************/
/*
    function PHP_get_date_select_frm($now){
        $hour = date('H');
        if($hour<6){
            $index = -1;
        }
        else{
            $index = 0;
        }
        $html = '<select name="day" id="reserv_date" required="">';
        //1週間分取得
        for($i=$index;$i<=7;$i++){
            $selected = "";
            $date_val = date('Y-m-d', strtotime($i.' day'));
            if($date_val == $now){
                $selected = " selected";
            }
            $html.='<option value="' . $date_val . '"' . $selected . '>' . date('Y/m/d', strtotime($i.' day')) . '</option>';
        }
        $html.= '</select>';

        return $html;
    }
*/
    function get_time(){
        $ret = array(
            "1" => "18:00",
            "2" => "18:30",
            "3" => "19:00",
            "4" => "19:30",
            "5" => "20:00",
            "6" => "20:30",
            "7" => "21:00",
            "8" => "21:30",
            "9" => "22:00",
            "10" => "22:30",
            "11" => "23:00",
            "12" => "23:30",
            "13" => "0:00",
            "14" => "0:30",
            "15" => "1:00",
            "16" => "1:30",
            "17" => "2:00",
            "18" => "2:30",
            "19" => "3:00",
            "20" => "3:30",
            "21" => "4:00",
            "22" => "4:30",
            "23" => "5:00"
        );
        return $ret;
	}

    function get_time_by_index($index){
        $ret = array(
            "-11" => "12:00",
            "-10" => "12:30",
            "-9" => "13:00",
            "-8" => "13:30",
            "-7" => "14:00",
            "-6" => "14:30",
            "-5" => "15:00",
            "-4" => "15:30",
            "-3" => "16:00",
            "-2" => "16:30",
            "-1" => "17:00",
            "0" => "17:30",
            "1" => "18:00",
            "2" => "18:30",
            "3" => "19:00",
            "4" => "19:30",
            "5" => "20:00",
            "6" => "20:30",
            "7" => "21:00",
            "8" => "21:30",
            "9" => "22:00",
            "10" => "22:30",
            "11" => "23:00",
            "12" => "23:30",
            "13" => "0:00",
            "14" => "0:30",
            "15" => "1:00",
            "16" => "1:30",
            "17" => "2:00",
            "18" => "2:30",
            "19" => "3:00",
            "20" => "3:30",
            "21" => "4:00",
            "22" => "4:30",
            "23" => "5:00",
            "24" => "5:30"
        );
        return $ret[$index];
	}

	function get_index_by_time($time){
        $ret = array(
            "12:00" => "-11",
            "12:30" => "-10",
            "13:00" => "-9",
            "13:30" => "-8",
            "14:00" => "-7",
            "14:30" => "-6",
            "15:00" => "-5",
            "15:30" => "-4",
            "16:00" => "-3",
            "16:30" => "-2",
            "17:00" => "-1",
            "17:30" => "0",
            "18:00" => "1",
            "18:30" => "2",
            "19:00" => "3",
            "19:30" => "4",
            "20:00" => "5",
            "20:30" => "6",
            "21:00" => "7",
            "21:30" => "8",
            "22:00" => "9",
            "22:30" => "10",
            "23:00" => "11",
            "23:30" => "12",
            "0:00" => "13",
            "0:30" => "14",
            "1:00" => "15",
            "1:30" => "16",
            "2:00" => "17",
            "2:30" => "18",
            "3:00" => "19",
            "3:30" => "20",
            "4:00" => "21",
            "4:30" => "22",
            "5:00" => "23",
            "5:30" => "24",
        );
        return $ret[$time];
	}

    function PHP_getTimeFromCourse($start_hour,$start_minute,$course,$spare){
        //開始時間から時刻補正、終了時間取得
        $ret = array();
        if($start_hour<=5){
            $start_hour += 24;
        }
        $end_hour = $start_hour + floor(intval($course)/60);
        $end_minute = $start_minute + intval($course)%60;
        if($end_minute>=60){
            $end_hour += 1;
            $end_minute -= 60;
        }
        if($spare){
            //移動時間有（前後30分）
            if($start_minute>=30){
                $start_minute -= 30;
            }
            else{
                $start_hour -= 1;
                $start_minute += 30;
            }
            if($end_minute<=30){
                $end_minute += 30;
            }
            else{
                $end_minute -= 30;
                $end_hour += 1;
            }
        }
        $ret['start_hour'] = $start_hour;
        $ret['start_minute'] = $start_minute;
        $ret['end_hour'] = $end_hour;
        $ret['end_minute'] = $end_minute;

        return $ret;
    }

    function PHP_get_attendance_id($therapist_id,$year,$month,$day,$area){

        $sql = sprintf("select id from attendance_new where therapist_id='%s' and year='%s' and month='%s' and day='%s' and area='%s'",$therapist_id,$year,$month,$day,$area);

        $res = mysql_query($sql, DbCon);
        if($res == false){
                return false;
                exit();
        }
        $row = mysql_fetch_assoc($res);
        if(!empty($row)){
            return $row["id"];
        }
        else{
            return "-1";
        }
        exit();
    }

    function PHP_get_time_len($start_hour,$start_minute,$end_hour,$end_minute){
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

    //出勤中のスタッフ情報を取得する
    function PHP_get_therapist_attendance($day,$area){

		//----insert by aida at 20180318 from
		$ws_cellName = "pr_" . $area;

		$ws_strWhere = "";
		if(Domain_ShopParty) {
			$ws_strWhere = " in('" . $area . "'," . PHP_addQuot(Domain_ShopParty, ",") . ")";
		} else {
			$ws_strWhere = "='" . $area . "'";
		}
		//----insert by aida at 20180318 to

        $ret = array();
        $arr1 = explode('-',$day); //yyyy-MM-dd
        $sql = sprintf("
            select an.id,an.therapist_id,tn.name_site as name,tn.rank,an.start_time,an.end_time,tp.age,tp.img_url,tp.skill,tp.skill_2,tp.hometown,tp.history,tp.pr_new,tp.%s from attendance_new as an
            left join therapist_new as tn on an.therapist_id = tn.id
            left join therapist_page as tp on tp.therapist_id = tn.id
            where
            an.today_absence='0' and
            an.kekkin_flg='0' and
            an.area%s and
            an.year='%s' and
            an.month='%s' and
            an.day='%s' and
            tp.delete_flg=0 and
            tn.delete_flg=0
            GROUP BY name
            order by tp.order_value desc",
            $ws_cellName, $ws_strWhere,$arr1[0], $arr1[1], $arr1[2]);	//update by aida at 20180318
        $res = mysql_query($sql, DbCon);	//$con);	update by aida at 20180318
        while($row = mysql_fetch_assoc($res)){
            $ret[] = $row;
        }
        return $ret;
    }

    //スタッフすべての予約可能時間
    function PHP_get_all_freetime_html($params, $date){

        $time_table = array(
            "1" => "18:00",
            "2" => "18:30",
            "3" => "19:00",
            "4" => "19:30",
            "5" => "20:00",
            "6" => "20:30",
            "7" => "21:00",
            "8" => "21:30",
            "9" => "22:00",
            "10" => "22:30",
            "11" => "23:00",
            "12" => "23:30",
            "13" => "0:00",
            "14" => "0:30",
            "15" => "1:00",
            "16" => "1:30",
            "17" => "2:00",
            "18" => "2:30",
            "19" => "3:00",
            "20" => "3:30",
            "21" => "4:00",
            "22" => "4:30",
            "23" => "5:00",
            "24" => "5:30",
            "25" => "6:00",
            "26" => "6:30",
            "27" => "7:00",
            "28" => "7:30"
        );

        $tmp_time_table = $time_table;
        //$arr_where = array();
        //$arr_where2 = array();
        $min_start_time = 28;
        $max_end_time = 0;

		$where = "";
		//$where2 = "";
        foreach($params as $val){
            //$arr_where[] = sprintf("(attendance_id = %s)",$val['id']);
            //$arr_where2[] = sprintf("(id = %s)",$val['id']);
			if($where) $where .= ",";
			$where .= $val['id'];

            if($min_start_time >= intval($val['start_time'])){
                $min_start_time = intval($val['start_time']);
            }
            if($max_end_time <= intval($val['end_time'])){
                $max_end_time = intval($val['end_time']);
            }
        }
        //$where = implode(" or ", $arr_where);
        //$where2 = implode(" or ", $arr_where2);

		for($n=0; $n<count($time_table); $n++) {
			$ws_ArWork[($n + 1)] = 0;
		}

        $sql = "SELECT time, count(distinct time,attendance_id) as cnt from reservation_new where attendance_id in(" . $where . ") group by time";
        $res = mysql_query($sql, DbCon);

        while($row = mysql_fetch_assoc($res)){
			$ws_ArWork[$row["time"]] = $row["cnt"];

            if(count($params) <= intval($row['cnt'])){
                unset($tmp_time_table[intval($row['time'])]);
                unset($ws_ArWork[intval($row['time'])]);
			}
        }

        foreach($tmp_time_table as $key => $val){
			//$sql = "select (select count(distinct(time)) from reservation_new where ({$where}) and time = {$key}) as cnt_ng,";
			//$sql .= " count(id) as cnt_ok from attendance_new where start_time <= {$key} and ({$where2})";
			$sql = "select (select count(distinct(time)) from reservation_new where attendance_id in(" . $where . ") and time = {$key}) as cnt_ng,";
			$sql .= " count(id) as cnt_ok from attendance_new where start_time <= {$key} and id in(" . $where . ")";

            $res = mysql_query($sql, DbCon);
			//echo $res . "/" . $sql . "<br />";
            $row = mysql_fetch_assoc($res);
            if($row['cnt_ok']<=$row['cnt_ng']){
                unset($tmp_time_table[intval($key)]);
            }
        }

        $html_top = '';
        $html_bottom = '';
        $lst = '';
        $jpg = '';
        $flg_has_maru = false;
        foreach($time_table as $key => $val){
            $nxt_key = $key+1;
            $key_60 = $key+2;
            $key_90 = $key+3;
            if(array_key_exists($key,$tmp_time_table)){
                if(array_key_exists($nxt_key,$tmp_time_table)){
                    if(array_key_exists($key_60,$tmp_time_table)){
                        if(intval($key) > intval($max_end_time)){
                            if($flg_has_maru == false){
                                $lst = "batu";
                            }
                            else{
                                $lst = "bar";
                            }
                        }
                        else{
                            $lst = "maru";
                            $flg_has_maru = true;
                        }
                    }
                    else{
                        if($flg_has_maru == false){
                            $lst = "batu";
                        }
                        else{
                            $lst = "bar";
                        }
                    }
                }
                else{
                    if($flg_has_maru == false){
                        $lst = "batu";
                    }
                    else{
                        $lst = "arrow";
                    }
                }
            }
            else{
                $lst = "batu";
            }

            if($lst == "maru"){
                if(($key=="1")||($key=="15")){
                    $jpg = "mark_maru_left.jpg";
                }
                else if($key=="14"){
                    $jpg = "mark_maru_right.jpg";
                }
                else if(intval($key)%2!=0){
                    $jpg = "mark_maru_00.jpg";
                }
                else{
                    $jpg = "mark_maru_30.jpg";
                }
            }
            else if($lst == "bar"){
                if($key=="15"){
                    $jpg = "mark_bar_left.jpg";
                }
                else if($key=="14"){
                    $jpg = "mark_bar_right.jpg";
                }
                else if(intval($key)%2!=0){
                    $jpg = "mark_bar_00.jpg";
                }
                else{
                    $jpg = "mark_bar_30.jpg";
                }
            }
            else if($lst == "arrow"){
                if($key=="15"){
                    $jpg = "mark_bar_left.jpg";
                }
                else if(($key=="14")||($key=="28")){
                    $jpg = "mark_arrow_right.jpg";
                }
                else if(intval($key)%2!=0){
                    $jpg = "mark_arrow_00.jpg";
                }
                else{
                    $jpg = "mark_arrow_30.jpg";
                }
            }
            else{
                if($key=="1"){
                    $jpg = "mark_batu_left.jpg";
                }
                else if($key=="28"){
                    $jpg = "mark_batu_right.jpg";
                }
                else if(intval($key)%2!=0){
                    $jpg = "mark_batu_00.jpg";
                }
                else{
                    $jpg = "mark_batu_30.jpg";
                }
            }

            $tmp = "";
            if($lst == "maru"){
                $tmp = '<p class="ox"><a href="form.php?tid='. $therapist_id . '&t='. $key . '&date='. $date . '" target="_blank"><img src="img/'.$jpg.'"></a></p>';
            }
            else{
                $tmp = '<p class="ox"><img src="img/'.$jpg.'"></p>';
            }

            if(intval($key) <= 14){
                $html_top .= $tmp;
            }
            else{
                $html_bottom .= $tmp;
            }
        }

        $html = '
          <div class="reservation_middle">
            <img src="img/time_01.jpg">
          </div>
          <div class="reservation_bottom">';
        $html .= $html_top;
        $html .= '
          </div>
          <div class="reservation_middle">
            <img src="img/time_02.jpg">
          </div>
          <div class="reservation_bottom">';
        $html .= $html_bottom;
        $html .= '</div>';

        return $html;
        exit();
    }


    function PHP_get_freetime_html($therapist,$now){

        //時間
        $time_table = array(
            "1" => "18:00",
            "2" => "18:30",
            "3" => "19:00",
            "4" => "19:30",
            "5" => "20:00",
            "6" => "20:30",
            "7" => "21:00",
            "8" => "21:30",
            "9" => "22:00",
            "10" => "22:30",
            "11" => "23:00",
            "12" => "23:30",
            "13" => "0:00",
            "14" => "0:30",
            "15" => "1:00",
            "16" => "1:30",
            "17" => "2:00",
            "18" => "2:30",
            "19" => "3:00",
            "20" => "3:30",
            "21" => "4:00",
            "22" => "4:30",
            "23" => "5:00",
            "24" => "5:30",
            "25" => "6:00",
            "26" => "6:30",
            "27" => "7:00",
            "28" => "7:30"
        );

        $date = $now;
        $tmp_time_table = $time_table;

        //スタッフ情報の取得
        $attendance_id = $therapist['id'];
        $therapist_id = $therapist['therapist_id'];
        $start_time = $therapist['start_time'];
        $end_time = $therapist['end_time'];

        $arr = array();//初期化
        if($therapist_id!="-1"){
            $sql = sprintf("select time from reservation_new where attendance_id='%s'", $attendance_id);
            $res = mysql_query($sql, DbCon);
            while($row = mysql_fetch_assoc($res)){
                unset($tmp_time_table[$row['time']]);
            }
            foreach($tmp_time_table as $key => $val){
                //開始時間以前、終了時間以後を削除
                if(intval($key) < intval($start_time)){
                    unset($tmp_time_table[$key]);
                }
            }
        }
        $html_top = '';
        $html_bottom = '';
        $lst = '';
        $jpg = '';
        $flg_has_maru = false;
        foreach($time_table as $key => $val){
            $nxt_key = $key+1;
            $key_60 = $key+2;
            $key_90 = $key+3;
            if(array_key_exists($key,$tmp_time_table)){
                if(array_key_exists($nxt_key,$tmp_time_table)){
                    if(array_key_exists($key_60,$tmp_time_table)){
                        if(intval($key) > intval($end_time)){
                            if($flg_has_maru == false){
                                $lst = "batu";
                            }
                            else{
                                $lst = "bar";
                            }
                        }
                        else{
                            $lst = "maru";
                            $flg_has_maru = true;
                        }
                    }
                    else{
                        if($flg_has_maru == false){
                            $lst = "batu";
                        }
                        else{
                            $lst = "bar";
                        }
                    }
                }
                else{
                    if($flg_has_maru == false){
                        $lst = "batu";
                    }
                    else{
                        $lst = "arrow";
                    }
                }
            }
            else{
                $lst = "batu";
            }

            if($lst == "maru"){
                if(($key=="1")||($key=="15")){
                    $jpg = "mark_maru_left.jpg";
                }
                else if($key=="14"){
                    $jpg = "mark_maru_right.jpg";
                }
                else if(intval($key)%2!=0){
                    $jpg = "mark_maru_00.jpg";
                }
                else{
                    $jpg = "mark_maru_30.jpg";
                }
            }
            else if($lst == "bar"){
                if($key=="15"){
                    $jpg = "mark_bar_left.jpg";
                }
                else if($key=="14"){
                    $jpg = "mark_bar_right.jpg";
                }
                else if(intval($key)%2!=0){
                    $jpg = "mark_bar_00.jpg";
                }
                else{
                    $jpg = "mark_bar_30.jpg";
                }
            }
            else if($lst == "arrow"){
                if($key=="15"){
                    $jpg = "mark_bar_left.jpg";
                }
                else if(($key=="14")||($key=="28")){
                    $jpg = "mark_arrow_right.jpg";
                }
                else if(intval($key)%2!=0){
                    $jpg = "mark_arrow_00.jpg";
                }
                else{
                    $jpg = "mark_arrow_30.jpg";
                }
            }
            else{
                if($key=="1"){
                    $jpg = "mark_batu_left.jpg";
                }
                else if($key=="28"){
                    $jpg = "mark_batu_right.jpg";
                }
                else if(intval($key)%2!=0){
                    $jpg = "mark_batu_00.jpg";
                }
                else{
                    $jpg = "mark_batu_30.jpg";
                }
            }

            $tmp = "";
            if($lst == "maru"){
                $tmp = '<p class="ox"><a href="form.php?tid='. $therapist_id . '&t='. $key .'&date='. $date.'"  target="_blank"><img src="img/'.$jpg.'" class="aimg"></a></p>';
            }
            else{
                $tmp = '<p class="ox"><img src="img/'.$jpg.'"></p>';
            }

            if(intval($key) <= 14){
                $html_top .= $tmp;
            }
            else{
                $html_bottom .= $tmp;
            }
        }

        $html = '
          <div class="reservation_middle">
            <img src="img/time_01.jpg">
          </div>
          <div class="reservation_bottom">';
        $html .= $html_top;
        $html .= '
          </div>
          <div class="reservation_middle">
            <img src="img/time_02.jpg">
          </div>
          <div class="reservation_bottom">';
        $html .= $html_bottom;
        $html .= '</div>';

        return $html;
        exit();

    }

    //スタッフすべての出勤日(Weekly)
    function PHP_get_therapist_width_weekly_attendance($date){
		//----insert by aida at 20180318 from
        //include("db_connect.php");

		$ws_strWhere = "";
		if(Domain_ShopParty) {
			$ws_strWhere = " in('" . Domain_Area . "'," . PHP_addQuot(Domain_ShopParty, ",") . ")";
		} else {
			$ws_strWhere = "='" . Domain_Area . "'";
		}
		//----insert by aida at 20180318 to

        $result = array();
        $arr_count = array();
        $arr_org = explode('-',$date);
        $arr_count[] = "count((year={$arr_org[0]} and month={$arr_org[1]} and day={$arr_org[2]}) or null) as w0";
        $arr_date[] = "'{$date}' as d0";
        $target_date = new DateTime($date);
        for($i=1;$i<7;$i++){
            $target_date->add(new DateInterval('P1D'));
            $str_date = date_format($target_date, 'Y-m-d');
            $arr = explode('-',$str_date);
            $arr_count[] = "count((year={$arr[0]} and month={$arr[1]} and day={$arr[2]}) or null) as w{$i}";
            $arr_date[] = "'{$str_date}' as d{$i}";
        }

        $sql = "SELECT tp.*,tn.rank,tn.name_site as name," . implode(" , ", $arr_count) . "," . implode(" , ", $arr_date) . " FROM (attendance_new as an";
		$sql .= " LEFT JOIN therapist_new as tn on tn.id=an.therapist_id)";
		$sql .= " LEFT JOIN therapist_page as tp on tn.id=tp.therapist_id";
 		$sql .= " WHERE an.kekkin_flg = 0";
		$sql .= " AND tn.area" . $ws_strWhere . " AND tn.delete_flg=0 AND tn.leave_flg=0 AND tn.test_flg=0 AND tp.delete_flg=0 group by an.therapist_id";	//update by aida at 20180318
        $res = mysql_query($sql, DbCon);	//$con);	update by aida at 20180318
        while($row = mysql_fetch_assoc($res)){
            $result[] = $row;
        }
        return $result;
        exit();
    }

    //本日から6日取得
    function PHP_get_week($date){
        $arr_week = array();
        $week = array("日", "月", "火", "水", "木", "金", "土");
        $target_date = new DateTime($date);
        $w = (int)$target_date->format('w');
        $arr_week[$w] = date_format($target_date,'j')."日(". $week[$w].")";
        for($i=1;$i<7;$i++){
            $target_date->add(new DateInterval('P1D'));
            $w = (int)$target_date->format('w');
            $arr_week[$w] = date_format($target_date,'j')."日(". $week[$w].")";
        }

        return $arr_week;

    }


    function PHP_get_paging_max_num_voice_front_2($display_num,$shop_type){

        $sql = sprintf("select id from voice where publish_flg='1' and shop_type='%s' and delete_flg='0'",$shop_type);

        $res = mysql_query($sql, DbCon);

        if($res == false){

            exit();

        }

        $num_rows = mysql_num_rows($res);

        $max_num = ceil($num_rows/$display_num);

        return $max_num;

    }

?>
