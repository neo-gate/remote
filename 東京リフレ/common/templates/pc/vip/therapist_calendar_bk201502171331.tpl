
<div style="margin:0px 30px 20px 30px;">

	<div style="padding:10px 0px 10px 0px;">
		<div style="font-size:16px;float:left;">
			セラピスト出勤カレンダー&予約フォーム
		</div>
		<div style="font-size:12px;padding:0px 0px 0px 0px;float:right;">
			<div style="float:left;padding-top:3px;">
				日付選択：
			</div>
			<div style="float:left;padding-left:5px;">
				<form>
					<select name="calendar_day" id="calendar_day">
						
						{{section name=cnt loop=$params.day_array}}
				
							{{if $smarty.section.cnt.index < 7}}
								
								{{if ( $params.month == $params.day_array[cnt].month ) && ( $params.day == $params.day_array[cnt].day )}}
									
									<option value="{{$params.day_array[cnt].year}}_{{$params.day_array[cnt].month}}_{{$params.day_array[cnt].day}}" selected>{{$params.day_array[cnt].month}}/{{$params.day_array[cnt].day}}({{$params.day_array[cnt].week_name}})</option>
									
								{{else}}
									
									<option value="{{$params.day_array[cnt].year}}_{{$params.day_array[cnt].month}}_{{$params.day_array[cnt].day}}">{{$params.day_array[cnt].month}}/{{$params.day_array[cnt].day}}({{$params.day_array[cnt].week_name}})</option>
								
								{{/if}}
							
							{{/if}}
							
						{{/section}}
						
					</select>
				</form>
			</div>
			<br style="clear:both" />
		</div>
		<br class="clear" />
	</div>
	
	<div style="margin:0px 0px 5px 0px;">
		<table>
			<tr>
				<td>
					<div style="width:200px;">
						縦スクロールでセラピスト移動<br />
						横スクロールで時間移動
					</div>
				</td>
				<td>
					○の時間帯のご予約が可能です<br />
					○をクリックするとご予約フォームに移動します
				</td>
			</tr>
		</table>
	</div>

	<div id="calendar_display_new">
	
		<div id="calendar_display_left">
		
			<div id="calendar_display_therapist_name_title">
				セラピスト
			</div>
			
			<div id="calendar_display_therapist_name">
			
				{{if $gyou_type == 1}}
					<div class="ichiretsu1_new">
				{{else}}
					<div class="ichiretsu2_new">
				{{/if}}
						指定なし
				{{if $gyou_type == 1}}
					</div>
				{{else}}
					</div>
				{{/if}}
			
				{{section name=cnt loop=$params.attendance_data}}
				{{math equation=a%b a=$smarty.section.cnt.index b=2 assign=gyou_type}}
				
					{{if $gyou_type == 1}}
						<div class="ichiretsu1_new">
					{{else}}
						<div class="ichiretsu2_new">
					{{/if}}
							{{$params.attendance_data[cnt].name_site}}
					{{if $gyou_type == 1}}
						</div>
					{{else}}
						</div>
					{{/if}}
					
				{{/section}}
				
			</div>
			
		</div>
		<div id="calendar_display_right">
		
			<div id="gyou_nichiji">
			
				<div class="table_title">
					{{$params.time_line_data[1]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[2]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[3]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[4]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[5]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[6]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[7]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[8]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[9]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[10]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[11]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[12]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[13]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[14]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[15]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[16]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[17]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[18]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[19]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[20]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[21]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[22]}}
				</div>
				<div class="table_title">
					{{$params.time_line_data[23]}}
				</div>
				<br class="clear" >
			</div>
			
			<div id="calendar_display_content">
			
				<div class="gyou_new">
				
					{{section name=cnt start=1 loop=24}}
						{{assign var="z" value=$smarty.section.cnt.index}}
						
					    {{if $gyou_type == 1}}
							<div class="maru1">
						{{else}}
							<div class="maru2">
						{{/if}}
						
							{{if $params.free_therapist_state[$z] == 0}}
								×
							{{else}}
								<a href="reservation/input.php?therapist_id=-1&year={{$params.year}}&month={{$params.month}}&day={{$params.day}}&time={{$z}}">〇</a>
							{{/if}}
						
						 {{if $gyou_type == 1}}
							</div>
						{{else}}
							</div>
						{{/if}}
							
					{{/section}}
					
					<br class="clear" >
					
				</div>
			
				{{section name=cnt loop=$params.attendance_data}}
				
				{{math equation=a%b a=$smarty.section.cnt.index b=2 assign=gyou_type}}
				
				<div class="gyou_new">
				
					{{section name=cnt2 start=1 loop=24}}
			
						{{assign var="j" value=$smarty.section.cnt2.index}}
						
						{{math equation=a+b a=$params.attendance_data[cnt].start_time b=1 assign=start_time}}
						{{math equation=a-b a=$params.attendance_data[cnt].end_time b=1 assign=end_time}}
						{{assign var="attendance_id" value=$params.attendance_data[cnt].attendance_id}}
						{{assign var="therapist_id" value=$params.attendance_data[cnt].therapist_id}}
						{{assign var="year" value=$params.attendance_data[cnt].year}}
						{{assign var="month" value=$params.attendance_data[cnt].month}}
						{{assign var="day" value=$params.attendance_data[cnt].day}}
						{{assign var="publish_flg" value=$params.attendance_data[cnt].publish_flg}}
						{{assign var="time" value=$j}}
						{{assign var="reservation_flag" value=false}}
						{{assign var="time_num" value=$params.attendance_data[cnt].time_num}}
						
						{{section name=cnt3 start=0 loop=$time_num}}
							
							{{assign var="tmp_time" value=$params.attendance_data[cnt].time[$smarty.section.cnt3.index]}}
							
							{{if $tmp_time == $time}}
							
								{{assign var="reservation_flag" value=true}}
							
							{{/if}}
						
						{{/section}}
						
						{{if $params.today_flag == false}}
						
							{{assign var="past_flag" value=false}}
						
						{{elseif ($params.under6_flag == true) && ($j <= 12) && ($params.today_flag == true)}}
						
							{{assign var="past_flag" value=true}}
						
						{{elseif $day > $params.now_day}}
						
							{{assign var="past_flag" value=false}}
						
						{{else}}
						
							{{php}}
				
								$this->_tpl_vars[past_flag] = past_time($this->_tpl_vars[j]);
								
							{{/php}}
						
						{{/if}}
						
						
						{{if ($j >= $start_time) && ($j <= $end_time)}}
						
							<div>
							
								{{if $reservation_flag == true}}
						
									{{if $gyou_type == 1}}
										<div id="{{$attendance_id}}_{{$j}}" class="batsu1">×</div>
									{{else}}
										<div id="{{$attendance_id}}_{{$j}}" class="batsu2">×</div>
									{{/if}}
								
								{{elseif $past_flag == true}}
								
									{{if $gyou_type == 1}}
										<div id="{{$attendance_id}}_{{$j}}" class="batsu1">×</div>
									{{else}}
										<div id="{{$attendance_id}}_{{$j}}" class="batsu2">×</div>
									{{/if}}
									
								{{elseif $publish_flg == "0"}}
								
									{{if $gyou_type == 1}}
										<div id="{{$attendance_id}}_{{$j}}" class="batsu1">×</div>
									{{else}}
										<div id="{{$attendance_id}}_{{$j}}" class="batsu2">×</div>
									{{/if}}
													
								{{else}}
								
									{{if $gyou_type == 1}}
									
										<div id="{{$attendance_id}}_{{$j}}" class="maru1">
											<a href="reservation/input.php?therapist_id={{$params.attendance_data[cnt].therapist_id}}&year={{$params.attendance_data[cnt].year}}&month={{$params.attendance_data[cnt].month}}&day={{$params.attendance_data[cnt].day}}&time={{$j}}">〇</a>
										</div>
								
									{{else}}
										<div id="{{$attendance_id}}_{{$j}}" class="maru2">
											<a href="reservation/input.php?therapist_id={{$params.attendance_data[cnt].therapist_id}}&year={{$params.attendance_data[cnt].year}}&month={{$params.attendance_data[cnt].month}}&day={{$params.attendance_data[cnt].day}}&time={{$j}}">〇</a>
										</div>
									{{/if}}
													
								{{/if}}
							
							</div>
							
						{{else}}
						
							{{if $gyou_type == 1}}
							
								<div class="kuuran1">&nbsp;</div>
								
							{{else}}
				
								<div class="kuuran2">&nbsp;</div>
								
							{{/if}}
							
						{{/if}}
						
					{{/section}}
					
					<br class="clear" >
				
				</div>
					
				{{/section}}
		
				<br class="clear" >
			
			</div>
		
		</div>
		
	</div>
	
</div>


