<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");
include(ROOT_PATH."include/define.php");



$most_start_time = 23;
$most_end_time = 1;
$under6_flag = false;

$now_hour = intval(date('H'));
$now_day = intval(date('d'));

if($now_hour <= 6){

	//昨日の日付
	$now_year = intval(date('Y', strtotime('-1 day')));
	$now_month = intval(date('m', strtotime('-1 day')));
	$now_day = intval(date('d', strtotime('-1 day')));

	$year = $now_year;
	$month = $now_month;
	$day = $now_day;

	$under6_flag = true;

}else{

	$now_year = intval(date('Y'));
	$now_month = intval(date('m'));
	$now_day = intval(date('d'));

	$year = $now_year;
	$month = $now_month;
	$day = $now_day;

}

if(isset($_GET["year"])==true){
	$year = $_GET["year"];
}

if(isset($_GET["month"])==true){
	$month = $_GET["month"];
}

if(isset($_GET["day"])==true){
	$day = $_GET["day"];
}

$today_flag = false;

if( ($year == $now_year) && ($month == $now_month) && ($day == $now_day) ){
	$today_flag = true;
}

$attendance_data = array();

//出勤情報と対応可否情報の取得
$all_data = get_attendance_data($year,$month,$day,$today_flag,$under6_flag);

$year = $year;
$month = $month;
$day = $day;
$day_array = $day_array;
$week_array = $week_array;

$attendance_data = $all_data["attendance_data"];
$free_therapist_state = $all_data["free_therapist_state"];
$now_day = $now_day;
$today_flag = $today_flag;
$under6_flag = $under6_flag;

$attendance_data_num = count($attendance_data);

?>



<link href="<?php echo $url_root;?>css/sp/style_calendar.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $url_root;?>js/jquery-1.7.2.js"></script>
<script src="<?php echo $url_root;?>js/jquery.exscrollevent.js"></script>
<script src="<?php echo $url_root;?>js/vip_therapist_calendar_sp.js"></script>



<?php

$html = "";

	$html .= '<div id="calendar_display_left">';
	
		$html .= '<div id="calendar_display_therapist_name_title">';
			$html .= 'セラピスト';
		$html .= '</div>';
		
		$html .= '<div id="calendar_display_therapist_name">';
		
		for($i=0;$i<$attendance_data_num;$i++){
		
			$gyou_type = ($i%2);
			
			if($gyou_type==1){
		
				$html .= '<div class="ichiretsu1_new">';
				
			}else{
				
				$html .= '<div class="ichiretsu2_new">';
			
			}
					
					$html .= $attendance_data[$i]["name_site"];
			
			
			if($gyou_type==1){
			
				$html .= '</div>';
			
			}else{
			
				$html .= '</div>';
					
			}
			
		}
		
		if($gyou_type==1){
		
			$html .= '<div class="ichiretsu1_new">';
		
		}else{
		
			$html .= '<div class="ichiretsu2_new">';
				
		}
				$html .= '指定なし';
		
		if($gyou_type==1){
		
			$html .= '</div>';
		
		}else{
		
			$html .= '</div>';
		
		}
		
		$html .= '</div>';
		
	$html .= '</div>';
	$html .= '<div id="calendar_display_right">';
	
		$html .= '<div id="gyou_nichiji">';
		
			$html .= '<div class="table_title">';
				$html .= '18:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '18:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '19:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '19:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '20:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '20:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '21:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '21:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '22:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '22:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '23:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '23:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '0:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '0:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '1:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '1:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '2:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '2:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '3:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '3:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '4:00';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '4:30';
			$html .= '</div>';
			$html .= '<div class="table_title">';
				$html .= '5:00';
			$html .= '</div>';
			$html .= '<br class="clear" >';
			
		$html .= '</div>';
		
		$html .= '<div id="calendar_display_content">';
		
		for($i=0;$i<$attendance_data_num;$i++){
		
			$gyou_type = ($i%2);
		
			$html .= '<div class="gyou_new">';
				
				$num = 24;
			
				for($j=1;$j<$num;$j++){
					
					$start_time = $attendance_data[$i]["start_time"];
					$end_time = $attendance_data[$i]["end_time"]-1;
					$attendance_id = $attendance_data[$i]["attendance_id"];
					$therapist_id = $attendance_data[$i]["therapist_id"];
					$year = $attendance_data[$i]["year"];
					$month = $attendance_data[$i]["month"];
					$day = $attendance_data[$i]["day"];
					$time = $j;
					$reservation_flag = false;
					$time_num = $attendance_data[$i]["time_num"];
					
					for($k=0;$k<$time_num;$k++){
						
						$tmp_time = $attendance_data[$i]["time"][$k];
						
						if( $tmp_time==$time ){
						
							$reservation_flag = true;
						
						}
						
					}
					
					if($today_flag==false){
						
						$past_flag = false;
					
					}else if( ($under6_flag == true) && ($j <= 12) && ($today_flag==true) ){
						
						$past_flag = true;
					
					}else if( $day > $now_day){
						
						$past_flag = false;
					
					}else{
						
						$past_flag = past_time($j);
					
					}
					
					if( ($j >= $start_time) && ($j <= $end_time) ){
					
						$html .= '<div>';
							
							if($reservation_flag == true){
								
								if($gyou_type==1){
								
									$html .= '<div id="'.$attendance_id.'_'.$j.'" class="batsu1">×</div>';
								
								}else{
								
									$html .= '<div id="'.$attendance_id.'_'.$j.'" class="batsu2">×</div>';
										
								}
								
							}else if($past_flag == true){
								
								if($gyou_type==1){
								
									$html .= '<div id="'.$attendance_id.'_'.$j.'" class="batsu1">×</div>';
								
								}else{
								
									$html .= '<div id="'.$attendance_id.'_'.$j.'" class="batsu2">×</div>';
								
								}
							
							}else{
								
								if($gyou_type==1){
								
									$html .= '<div id="'.$attendance_id.'_'.$j.'" class="maru1">';
										$html .= '<a href="reservation/input.php?therapist_id='.$attendance_data[$i]["therapist_id"].'&year='.$attendance_data[$i]["year"].'&month='.$attendance_data[$i]["month"].'&day='.$attendance_data[$i]["day"].'&time='.$j.'">〇</a>';
									$html .= '</div>';
								
								}else{
								
									$html .= '<div id="'.$attendance_id.'_'.$j.'" class="maru2">';
										$html .= '<a href="reservation/input.php?therapist_id='.$attendance_data[$i]["therapist_id"].'&year='.$attendance_data[$i]["year"].'&month='.$attendance_data[$i]["month"].'&day='.$attendance_data[$i]["day"].'&time='.$j.'">〇</a>';
									$html .= '</div>';
								
								}
							
							}
						
						$html .= '</div>';
					
					}else{
						
						if($gyou_type==1){
						
							$html .= '<div class="kuuran1">&nbsp;</div>';
						
						}else{
						
							$html .= '<div class="kuuran2">&nbsp;</div>';
						
						}
					
					}
				
				}
				
				$html .= '<br class="clear" >';
			
			$html .= '</div>';
		
		}
		
		$html .= '<div class="gyou_new">';
		
			$num = 24;
			
			for($i=1;$i<$num;$i++){
				
				if($gyou_type==1){
				
					$html .= '<div class="maru1">';
				
				}else{
				
					$html .= '<div class="maru2">';
				
				}
					
					if( $free_therapist_state[$i] == 0 ){
						
						$html .= '×';
					
					}else{
						
						$html .= '<a href="reservation/input.php?therapist_id=-1&year='.$year.'&month='.$month.'&day='.$day.'&time='.$i.'">〇</a>';
					
					}
				
				if($gyou_type==1){
				
					$html .= '</div>';
				
				}else{
				
					$html .= '</div>';
				
				}
					
			}
			
			$html .= '<br class="clear" >';
			
		$html .= '</div>';
		
		$html .= '</div>';
		
	$html .= '</div>';
	$html .= '<br class="clear" >';
	
	echo $html;
	exit();

?>