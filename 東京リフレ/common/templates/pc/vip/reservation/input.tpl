
<div style="padding:20px 0px 0px 100px;">
	<div>{{$params.error}}</div>
	<form action="" method="post">
	
		<input type="hidden" name="year" value="{{$params.year}}" id="reserv_year" />
		
		<div>
			<div>
				〇日時
			</div>
			<div style="padding:0px 0px 0px 30px;line-height:200%;">
				<div style="padding:5px 0px 0px 0px;">
					<select name="day" id="reserv_day">
						{{section name=cnt start=0 step=1 loop=7}}
							{{if ($params.month == $params.day_array[cnt].month) && ($params.day == $params.day_array[cnt].day)}}
								{{assign var="hoge" value=$params.day_array[cnt].week}}
								<option value="{{$params.day_array[cnt].month}}_{{$params.day_array[cnt].day}}" selected>{{$params.day_array[cnt].month}}/{{$params.day_array[cnt].day}}({{$params.week_array[$hoge]}})</option>
							{{else}}
								{{assign var="hoge" value=$params.day_array[cnt].week}}
								<option value="{{$params.day_array[cnt].month}}_{{$params.day_array[cnt].day}}">{{$params.day_array[cnt].month}}/{{$params.day_array[cnt].day}}({{$params.week_array[$hoge]}})</option>
							{{/if}}
						{{/section}}
					</select>
				</div>
				<div style="padding:5px 0px 0px 0px;">
					<select name="time" id="reserv_time">
						{{$params.time_select_option}}
					</select>
					スタート
				</div>
			</div>
		</div>
		<div style="padding:15px 0px 0px 0px;">
			<div>
				〇担当セラピスト
			</div>
			<div style="padding:5px 0px 0px 30px;line-height:200%;">
				<div>
					<div id="free_therapist">
						{{if $params.attendance_data_num == "0"}}
							<span style="color:red;font-weight:bold;">申し訳ありません。この時間帯に対応可能なセラピストはおりません。</span>
							<input type="hidden" name="notherapist" value="true" />
						{{else}}
							
							{{section name=cnt start=0 step=1 loop=$params.attendance_data_num}}
								{{if $params.therapist_id == $params.attendance_data[cnt].therapist_id}}
									<input type="radio" name="therapist" value="{{$params.attendance_data[cnt].therapist_id}}" checked>{{$params.attendance_data[cnt].name_site}}<br />
								{{else}}
									<input type="radio" name="therapist" value="{{$params.attendance_data[cnt].therapist_id}}">{{$params.attendance_data[cnt].name_site}}<br />
								{{/if}}
								
							{{/section}}
							
							{{if $params.therapist_id == "-1"}}
								<input type="radio" name="therapist" value="-1" checked>特に指定しない<br />
							{{else}}
								<input type="radio" name="therapist" value="-1">特に指定しない<br />
							{{/if}}
						
						{{/if}}
					</div>
					
				</div>
				<div style="padding:0px 0px 0px 20px;">
					※セラピストを指定しますと指名料が別途1,000円掛かります。
				</div>
			</div>
		</div>
		<div style="padding:10px 0px 0px 0px;">
			<div>
				〇ご希望コース
			</div>
			<div style="padding:0px 0px 0px 30px;">
				
				{{section name=cnt start=0 step=1 loop=8}}
					{{if $params.course == $params.course_array[cnt]}}
						<input type="radio" name="course" value="{{$params.course_array[cnt]}}" checked>{{$params.course_array[cnt]}}分コース<br />
					{{else}}
						<input type="radio" name="course" value="{{$params.course_array[cnt]}}">{{$params.course_array[cnt]}}分コース<br />
					{{/if}}
				{{/section}}
				
				{{if $params.course == "-1"}}
					<input type="radio" name="course" value="-1" checked>未定<br />
				{{else}}
					<input type="radio" name="course" value="-1">未定<br />
				{{/if}}
				
				
			</div>
		</div>
		<div style="padding:10px 0px 0px 0px;">
			<div>
				〇ご要望欄、ご連絡欄(ホテルでご利用の場合はホテル名とお部屋番号をご記入ください)
			</div>
			<div style="padding:0px 0px 0px 30px;">
				<textarea name="free" style="width:600px;height:150px;">{{$params.free}}</textarea>
			</div>
		</div>
		<div style="padding:10px 0px 0px 0px;">
			<div>
				〇メールアドレス
			</div>
			<div style="padding:0px 0px 0px 30px;">
				<input type="text"  name="mail" value="{{$params.mail}}" style="width:400px;height:20px;" />
			</div>
		</div>
		<div style="padding:30px 0px 50px 200px;">
			<span>
				<input type="submit" value="戻る" name="back" style="width:80px;height:30px;" />
			</span>
			<span style="padding-left:30px;">
				<input type="submit" value="内容確認" name="send" style="width:80px;height:30px;" />
			</span>
		</div>
	</form>
</div>
