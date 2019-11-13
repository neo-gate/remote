<div>
	<a id="today_attendance"></a>
</div>
<div class="title_block">
	<h2 id="therapist_page_attendance_title">本日の出勤セラピスト</h2>
	<div>
		<div id="therapist_page_day_disp">
			{{section name=cnt loop=$params.day_array}}
			{{if $smarty.section.cnt.index < 7}}
			{{if $smarty.section.cnt.index != "0"}} <span style='padding-left:10px;'>|</span> {{/if}} {{if ( $params.month == $params.day_array[cnt].month ) && ( $params.day == $params.day_array[cnt].day )}} <span style="padding-left:10px;">{{$params.day_array[cnt].month}}/{{$params.day_array[cnt].day}}({{$params.day_array[cnt].week_name}})</span> {{else}} <span style="padding-left:10px;"><span class="therapist_page_day_list" onclick="therapistpage_day_change({{$params.day_array[cnt].year}},{{$params.day_array[cnt].month}},{{$params.day_array[cnt].day}});">{{$params.day_array[cnt].month}}/{{$params.day_array[cnt].day}}({{$params.day_array[cnt].week_name}})</span></span> {{/if}} {{/if}} {{/section}}
		</div>
		<div id="attendance">
			<div>
				{{section name=cnt loop=$params.attendance_therapist_data}}
				<div class="title_therapist_info_content">
					<div class="top_area">
						<div class="name_img">
							<p class="therapist_name">{{$params.attendance_therapist_data[cnt].therapist_name}}（{{$params.attendance_therapist_data[cnt].age}}）</p>
							<p class="img"><img alt="セラピスト{{$params.attendance_therapist_data[cnt].therapist_name}}" src="{{$smarty.const.S3_URL}}{{$params.attendance_therapist_data[cnt].img_url}}" width="100"></p>
							<p class="info">{{$params.attendance_therapist_data[cnt].hometown}}出身｜セラピスト歴{{$params.attendance_therapist_data[cnt].history}}</p>
							<p style="padding:5px 0px 0px 60px;"><input onclick="open_attendance_schedule({{$params.attendance_therapist_data[cnt].therapist_id}});" style="padding:10px;" type="button" value="出勤予定表"></p>
						</div>
						<div class="tokui">
							{{if $params.attendance_therapist_data[cnt].skill_2_exist_flg == true}}
							<div class="one" style="margin-bottom:15px;">
								<div class="title_1">
									○一押しメニュー
								</div>
								<div class="content">
									{{section name=cnt2 loop=$params.attendance_therapist_data[cnt].skill_2_data}} {{assign var="x" value=$params.attendance_therapist_data[cnt].skill_2_data[cnt2]}}
									<div class="left"><img alt="{{$params.skill_data[$x]}}" src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" width="105"></div>{{/section}}<br class="clear">
								</div>
							</div>{{/if}}
							<div class="one">
								<div class="title_2">
									○施術可能メニュー
								</div>
								<div class="content">
									{{section name=cnt2 loop=$params.attendance_therapist_data[cnt].skill_data}} {{assign var="x" value=$params.attendance_therapist_data[cnt].skill_data[cnt2]}} {{if $x!=''}}
									<div class="left"><img alt="{{$params.skill_data[$x]}}" src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" width="105"></div>{{/if}} {{/section}}<br class="clear">
								</div>
							</div>
						</div><br class="clear">
					</div>
					<div class="separator"><img alt="セパレーター" src="{{$smarty.const.REFLE_WWW_URL}}img/lp/pc/separator.jpg" width="600"></div>
					<p class="exp">{{$params.attendance_therapist_data[cnt].pr_content|nl2br}} {{if $params.attendance_therapist_data[cnt].shikaku != ""}}</p>
					<p class="exp">【保有資格】</p>
					<p class="exp">{{$params.attendance_therapist_data[cnt].shikaku}}
					{{/if}}</p>
					<div class="separator"><img alt="セパレーター" src="{{$smarty.const.REFLE_WWW_URL}}img/lp/pc/separator.jpg" width="600"></div>{{$params.attendance_therapist_data[cnt].uketsuke_state_html}}
				</div>{{/section}}
			</div>
			<div style="padding:20px 0px 0px 0px;">
				<h2>セラピスト一覧</h2>
				<div id="therapist_list">
					{{section name=cnt loop=$params.not_attendance_therapist_data}}
					<div class="title_therapist_info_content">
						<div class="top_area">
							<div class="name_img">
								<p class="therapist_name">{{$params.not_attendance_therapist_data[cnt].therapist_name}}（{{$params.not_attendance_therapist_data[cnt].age}}）</p>
								<p class="img"><img alt="セラピスト{{$params.not_attendance_therapist_data[cnt].therapist_name}}" src="{{$smarty.const.S3_URL}}{{$params.not_attendance_therapist_data[cnt].img_url}}" width="100"></p>
								<p class="info">{{$params.not_attendance_therapist_data[cnt].hometown}}出身｜セラピスト歴{{$params.not_attendance_therapist_data[cnt].history}}</p>
								<p style="padding:5px 0px 0px 60px;"><input onclick="open_attendance_schedule({{$params.not_attendance_therapist_data[cnt].therapist_id}});" style="padding:10px;" type="button" value="出勤予定表"></p>
							</div>
							<div class="tokui">
								{{if $params.not_attendance_therapist_data[cnt].skill_2_exist_flg == true}}
								<div class="one" style="margin-bottom:15px;">
									<div class="title_1">
										○一押しメニュー
									</div>
									<div class="content">
										{{section name=cnt2 loop=$params.not_attendance_therapist_data[cnt].skill_2_data}} {{assign var="x" value=$params.not_attendance_therapist_data[cnt].skill_2_data[cnt2]}} {{if $x != ""}}
										<div class="left"><img alt="{{$params.skill_data[$x]}}" src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" width="105"></div>{{/if}} {{/section}}<br class="clear">
									</div>
								</div>{{/if}}
								<div class="one">
									<div class="title_2">
										○施術可能メニュー
									</div>
									<div class="content">
										{{section name=cnt2 loop=$params.not_attendance_therapist_data[cnt].skill_data}} {{assign var="x" value=$params.not_attendance_therapist_data[cnt].skill_data[cnt2]}} {{if $x!=''}}
										<div class="left"><img alt="{{$params.skill_data[$x]}}" src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" width="105"></div>{{/if}} {{/section}}<br class="clear">
									</div>
								</div>
							</div><br class="clear">
						</div>
						<div class="separator"><img alt="セパレーター" src="{{$smarty.const.REFLE_WWW_URL}}img/lp/pc/separator.jpg" width="600"></div>
						<p class="exp2">{{$params.not_attendance_therapist_data[cnt].pr_content|nl2br}} {{if $params.not_attendance_therapist_data[cnt].shikaku != ""}}</p>
						<p class="exp2">【保有資格】</p>
						<p class="exp2">{{$params.not_attendance_therapist_data[cnt].shikaku}}
						{{/if}}</p>
					</div>{{/section}}
				</div>
			</div>
		</div>
	</div>
</div>