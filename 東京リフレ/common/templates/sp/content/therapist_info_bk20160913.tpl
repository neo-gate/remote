
<div id="therapist_info">

<div class="title_bar">
	セラピスト紹介
</div>
<div class="content">
	<div class="image">
		<div><img src="{{$smarty.const.REFLE_WWW_URL}}img/sp/woman3.jpg" alt="女性" width="130" /></div>
		
	</div>
	<div class="exp">

		当店のセラピストは資格保有者を始め、全員がキャリア豊富な20代～30代の女性セラピストです。<br />
		<br />
		セラピストのクオリティに絶対の自信を持っております。

	</div>
	<br class="clear" />
</div>

<div style="margin:10px 0px 10px 0px;">
{{include file="sp/content/reservation.tpl"}}
</div>
	
<div>
<a name="today_attendance"></a>
</div>
<div class="title_block">
	<div class="title_bar" id="therapist_page_attendance_title">
		本日の出勤セラピスト
	</div>
	<div>
		<div id="therapist_page_day_disp">
			
			<div style="font-size:12px;padding:10px 0px 0px 0px;line-height:160%;">
				<div style="float:left;padding-top:3px;">
					日付選択：
				</div>
				<div style="float:left;padding-left:5px;">
					<form>
						<select name="therapist_attendance_day" id="therapist_attendance_day">
							
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
				<br class="clear" />
			</div>
			
		</div>
		
		<div id="attendance">
		
			<div>
				{{section name=cnt loop=$params.attendance_therapist_data}}
					
					<div class="title_therapist_info_content">
						
						<div class="top_area">
							<div class="name_img">
								<p class="therapist_name">
									{{$params.attendance_therapist_data[cnt].therapist_name}}（{{$params.attendance_therapist_data[cnt].age}}）
								</p>
								<p class="img">
									<img src="{{$smarty.const.S3_URL}}{{$params.attendance_therapist_data[cnt].img_url}}" alt="セラピスト{{$params.attendance_therapist_data[cnt].therapist_name}}" width="70" />
								</p>
								<p class="info">
									{{$params.attendance_therapist_data[cnt].hometown}}出身<br />
									セラピスト歴{{$params.attendance_therapist_data[cnt].history}}
								</p>
								<div class="attendance_btn_block">
									<div style="float:left;">
										<input type="button" value="出勤予定表" style="padding:5px;" onclick="open_attendance_schedule_sp({{$params.attendance_therapist_data[cnt].therapist_id}});" />
									</div>
									<div class="therapist_attendance_yotei_disp" id="yotei_disp_{{$params.attendance_therapist_data[cnt].therapist_id}}">
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx
									</div>
									<br class="clear" />
								</div>
							</div>
							<div class="tokui">
								{{if $params.attendance_therapist_data[cnt].skill_2_exist_flg == true}}
								<div class="one" style="margin-bottom:15px;">
									<div class="title_1">
										○一押しメニュー
									</div>
									<div class="content_sp">
									{{section name=cnt2 loop=$params.attendance_therapist_data[cnt].skill_2_data}}
									
										{{assign var="x" value=$params.attendance_therapist_data[cnt].skill_2_data[cnt2]}}
										
										<div class="left">
											<img src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" alt="{{$params.skill_data[$x]}}" width="80" />
										</div>
										
									{{/section}}
									<br class="clear" />
									</div>
								</div>
								{{/if}}
								<div class="one">
									<div class="title_2">
										○施術可能メニュー
									</div>
									<div class="content_sp">
									{{section name=cnt2 loop=$params.attendance_therapist_data[cnt].skill_data}}
									
										{{assign var="x" value=$params.attendance_therapist_data[cnt].skill_data[cnt2]}}
										
										<div class="left">
											<img src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" alt="{{$params.skill_data[$x]}}" width="80" />
										</div>
										
									{{/section}}
									<br class="clear" />
									</div>
								</div>
							</div>
							<br class="clear" />
						</div>
						
						<div><img src="{{$smarty.const.REFLE_WWW_URL}}img/sp/201503/kugiri.jpg" alt="区切り" width="310" /></div>
						
						<p class="exp">
							{{$params.attendance_therapist_data[cnt].pr_content|nl2br}}
							
							{{if $params.attendance_therapist_data[cnt].shikaku != ""}}
								<br />
								【保有資格】<br />
								{{$params.attendance_therapist_data[cnt].shikaku}}<br />
							{{/if}}
						</p>
						
						<div><img src="{{$smarty.const.REFLE_WWW_URL}}img/sp/201503/kugiri.jpg" alt="区切り" width="310" /></div>
						
						{{$params.attendance_therapist_data[cnt].uketsuke_state_html}}
						
					</div>
					
				{{/section}}
				
			</div>
			
			<div style="padding:10px 0px 0px 0px;">
			
				<div class="title_bar">
					セラピスト一覧
				</div>
				
				<div id="therapist_list">
					
					{{section name=cnt loop=$params.not_attendance_therapist_data}}
					
						<div class="title_therapist_info_content">
							<div class="top_area">
								<div class="name_img">
									<p class="therapist_name">
										{{$params.not_attendance_therapist_data[cnt].therapist_name}}（{{$params.not_attendance_therapist_data[cnt].age}}）
									</p>
									<p class="img">
										<img src="{{$smarty.const.S3_URL}}{{$params.not_attendance_therapist_data[cnt].img_url}}" alt="セラピスト{{$params.not_attendance_therapist_data[cnt].therapist_name}}" width="70" />
									</p>
									<p class="info">
										{{$params.not_attendance_therapist_data[cnt].hometown}}出身<br />
										セラピスト歴{{$params.not_attendance_therapist_data[cnt].history}}
									</p>
									<div class="attendance_btn_block">
										<div style="float:left;">
											<input type="button" value="出勤予定表" style="padding:5px;" onclick="open_attendance_schedule_sp({{$params.not_attendance_therapist_data[cnt].therapist_id}});" />
										</div>
										<div class="therapist_attendance_yotei_disp" id="yotei_disp_{{$params.not_attendance_therapist_data[cnt].therapist_id}}">
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx
										</div>
										<br class="clear" />
									</div>
								</div>
								<div class="tokui">
									{{if $params.not_attendance_therapist_data[cnt].skill_2_exist_flg == true}}
									<div class="one" style="margin-bottom:15px;">
										<div class="title_1">
											○一押しメニュー
										</div>
										<div class="content_sp">
										{{section name=cnt2 loop=$params.not_attendance_therapist_data[cnt].skill_2_data}}
										
											{{assign var="x" value=$params.not_attendance_therapist_data[cnt].skill_2_data[cnt2]}}
											
											<div class="left">
												<img src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" alt="{{$params.skill_data[$x]}}" width="80" />
											</div>
											
										{{/section}}
										<br class="clear" />
										</div>
									</div>
									{{/if}}
									<div class="one">
										<div class="title_2">
											○施術可能メニュー
										</div>
										<div class="content_sp">
										{{section name=cnt2 loop=$params.not_attendance_therapist_data[cnt].skill_data}}
										
											{{assign var="x" value=$params.not_attendance_therapist_data[cnt].skill_data[cnt2]}}
											
											<div class="left">
												<img src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" alt="{{$params.skill_data[$x]}}" width="80" />
											</div>
											
										{{/section}}
										<br class="clear" />
										</div>
									</div>
								</div>
								<br class="clear" />
							</div>
							
							<div><img src="{{$smarty.const.REFLE_WWW_URL}}img/sp/201503/kugiri.jpg" alt="区切り" width="310" /></div>
							
							<p class="exp">
								{{$params.not_attendance_therapist_data[cnt].pr_content|nl2br}}
								
								{{if $params.not_attendance_therapist_data[cnt].shikaku != ""}}
									<br />
									【保有資格】<br />
									{{$params.not_attendance_therapist_data[cnt].shikaku}}<br />
								{{/if}}
							</p>
							
						</div>
					
					{{/section}}
					
				</div>
			</div>
			
			
			
		</div>
	</div>
</div>

</div>

