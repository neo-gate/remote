<div id="therapist_info">
	<div>
		<a id="today_attendance"></a>
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
							<select id="therapist_attendance_day" neme="therapist_attendance_day">
							{{section name=cnt loop=$params.day_array}}
								{{if $smarty.section.cnt.index < 7}}
									{{if ( $params.month == $params.day_array[cnt].month ) && ( $params.day == $params.day_array[cnt].day )}}
									<option selected value="{{$params.day_array[cnt].year}}_{{$params.day_array[cnt].month}}_{{$params.day_array[cnt].day}}">
										{{$params.day_array[cnt].month}}/{{$params.day_array[cnt].day}}({{$params.day_array[cnt].week_name}})
									</option>
									{{else}}
									<option value="{{$params.day_array[cnt].year}}_{{$params.day_array[cnt].month}}_{{$params.day_array[cnt].day}}">
										{{$params.day_array[cnt].month}}/{{$params.day_array[cnt].day}}({{$params.day_array[cnt].week_name}})
									</option>
									{{/if}}
								{{/if}}
							{{/section}}
							</select>
						</form>
					</div><br class="clear">
				</div>
			</div>
			<div id="attendance">
				<div>
					{{section name=cnt loop=$params.attendance_therapist_data}}
					<div class="title_therapist_info_content">
						<div class="top_area">
							<div class="name_img">
								<p class="therapist_name">{{$params.attendance_therapist_data[cnt].therapist_name}}（{{$params.attendance_therapist_data[cnt].age}}）</p>
								<p class="img"><img alt="セラピスト{{$params.attendance_therapist_data[cnt].therapist_name}}" src="{{$smarty.const.S3_URL}}{{$params.attendance_therapist_data[cnt].img_url}}" width="70"></p>
								<p class="info">{{$params.attendance_therapist_data[cnt].hometown}}出身
								セラピスト歴{{$params.attendance_therapist_data[cnt].history}}</p>
								<div class="attendance_btn_block">
									<div style="float:left;">
										<input onclick="open_attendance_schedule_sp({{$params.attendance_therapist_data[cnt].therapist_id}});" style="padding:5px;" type="button" value="出勤予定表">
									</div>
									<div class="therapist_attendance_yotei_disp" id="yotei_disp_{{$params.attendance_therapist_data[cnt].therapist_id}}">
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx
										xxxxxxxxxxxxxxxxxxxxxxxxxxxx
									</div><br class="clear">
								</div>
							</div>
							<div class="tokui">
								{{if $params.attendance_therapist_data[cnt].skill_2_exist_flg == true}}
								<div class="one" style="margin-bottom:15px;">
									<div class="title_1">
										○一押しメニュー
									</div>
									<div class="content_sp">
										{{section name=cnt2 loop=$params.attendance_therapist_data[cnt].skill_2_data}} {{assign var="x" value=$params.attendance_therapist_data[cnt].skill_2_data[cnt2]}}
										<div class="left"><img alt="{{$params.skill_data[$x]}}" src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" width="80"></div>{{/section}}<br class="clear">
									</div>
								</div>{{/if}}
								<div class="one">
									<div class="title_2">
										○施術可能メニュー
									</div>
									<div class="content_sp">
										{{section name=cnt2 loop=$params.attendance_therapist_data[cnt].skill_data}} {{assign var="x" value=$params.attendance_therapist_data[cnt].skill_data[cnt2]}}
										<div class="left"><img alt="{{$params.skill_data[$x]}}" src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" width="80"></div>{{/section}}<br class="clear">
									</div>
								</div>
							</div><br class="clear">
						</div>
						<div><img alt="区切り" src="{{$smarty.const.REFLE_WWW_URL}}img/sp/201503/kugiri.jpg" width="310"></div>
						<p class="exp">{{$params.attendance_therapist_data[cnt].pr_content|nl2br}} {{if $params.attendance_therapist_data[cnt].shikaku != ""}}
						【保有資格】
						{{$params.attendance_therapist_data[cnt].shikaku}}
						{{/if}}</p>
						<div><img alt="区切り" src="{{$smarty.const.REFLE_WWW_URL}}img/sp/201503/kugiri.jpg" width="310"></div>{{$params.attendance_therapist_data[cnt].uketsuke_state_html}}
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
									<p class="therapist_name">{{$params.not_attendance_therapist_data[cnt].therapist_name}}（{{$params.not_attendance_therapist_data[cnt].age}}）</p>
									<p class="img"><img alt="セラピスト{{$params.not_attendance_therapist_data[cnt].therapist_name}}" src="{{$smarty.const.S3_URL}}{{$params.not_attendance_therapist_data[cnt].img_url}}" width="70"></p>
									<p class="info">{{$params.not_attendance_therapist_data[cnt].hometown}}出身
									セラピスト歴{{$params.not_attendance_therapist_data[cnt].history}}</p>
									<div class="attendance_btn_block">
										<div style="float:left;">
											<input onclick="open_attendance_schedule_sp({{$params.not_attendance_therapist_data[cnt].therapist_id}});" style="padding:5px;" type="button" value="出勤予定表">
										</div>
										<div class="therapist_attendance_yotei_disp" id="yotei_disp_{{$params.not_attendance_therapist_data[cnt].therapist_id}}">
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx
											xxxxxxxxxxxxxxxxxxxxxxxxxxxx
										</div><br class="clear">
									</div>
								</div>
								<div class="tokui">
									{{if $params.not_attendance_therapist_data[cnt].skill_2_exist_flg == true}}
									<div class="one" style="margin-bottom:15px;">
										<div class="title_1">
											○一押しメニュー
										</div>
										<div class="content_sp">
											{{section name=cnt2 loop=$params.not_attendance_therapist_data[cnt].skill_2_data}} {{assign var="x" value=$params.not_attendance_therapist_data[cnt].skill_2_data[cnt2]}}
											<div class="left"><img alt="{{$params.skill_data[$x]}}" src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" width="80"></div>{{/section}}<br class="clear">
										</div>
									</div>{{/if}}
									<div class="one">
										<div class="title_2">
											○施術可能メニュー
										</div>
										<div class="content_sp">
											{{section name=cnt2 loop=$params.not_attendance_therapist_data[cnt].skill_data}} {{assign var="x" value=$params.not_attendance_therapist_data[cnt].skill_data[cnt2]}}
											<div class="left"><img alt="{{$params.skill_data[$x]}}" src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" width="80"></div>{{/section}}<br class="clear">
										</div>
									</div>
								</div><br class="clear">
							</div>
							<div><img alt="区切り" src="{{$smarty.const.REFLE_WWW_URL}}img/sp/201503/kugiri.jpg" width="310"></div>
							<p class="exp">{{$params.not_attendance_therapist_data[cnt].pr_content|nl2br}} {{if $params.not_attendance_therapist_data[cnt].shikaku != ""}}
							【保有資格】
							{{$params.not_attendance_therapist_data[cnt].shikaku}}
							{{/if}}</p>
						</div>
						{{/section}}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>