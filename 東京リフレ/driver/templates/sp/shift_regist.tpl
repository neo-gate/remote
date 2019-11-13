
<div id="top_name_disp">
	{{$params.staff_name}}さん
</div>

{{include file="top_menu_beta.tpl"}}

{{if $params.error_list != ""}}
<div style="text-align:center;padding-top:5px;">{{$params.error_list}}</div>
{{/if}}

<div style="padding:0px 0px 50px 0px;">

	<div class="title_bar2">
	
		{{if $params.today_month == $params.month}}
			<span style="font-size:10px;">{{$params.today_month}}月シフト</span>
		{{else}}
			<a href="shift_regist.php?area={{$params.area}}&id={{$params.staff_id}}&year={{$params.today_year}}&month={{$params.today_month}}&ch={{$params.ch}}" style="font-size:10px;">
				{{$params.today_month}}月シフト
			</a>
		{{/if}}
		&nbsp;｜&nbsp;
		{{if $params.next_month == $params.month}}
			<span style="font-size:10px;">{{$params.next_month}}月シフト</span>
		{{else}}
			<a href="shift_regist.php?area={{$params.area}}&id={{$params.staff_id}}&year={{$params.next_year}}&month={{$params.next_month}}&ch={{$params.ch}}" style="font-size:10px;">
				{{$params.next_month}}月シフト
			</a>
		{{/if}}
		&nbsp;｜&nbsp;
		{{if $params.month_3 == $params.month}}
			<span style="font-size:10px;">{{$params.month_3}}月シフト</span>
		{{else}}
			<a href="shift_regist.php?area={{$params.area}}&id={{$params.staff_id}}&year={{$params.year_3}}&month={{$params.month_3}}&ch={{$params.ch}}" style="font-size:10px;">
				{{$params.month_3}}月シフト
			</a>
		{{/if}}
		
	</div>
	
	<form action="" method="post" id="edit_frm">
	
	<input type="hidden" name="staff_id" value="{{$params.staff_id}}" />
	<input type="hidden" name="area" value="{{$params.area}}" />
	<input type="hidden" name="year" value="{{$params.year}}" />
	<input type="hidden" name="month" value="{{$params.month}}" />
	<input type="hidden" name="ch" value="{{$params.ch}}" />
	
	<div id="shift_regist_check_all">
		<div class="content_1">以下の時間帯をチェックした日付にコピー</div>
		<div class="content_2">
			
			<div class="left_1">
			<select name="start_time_check_all">
			{{$params.start_time_check_all_option}}
			</select>
			</div>
			<div class="left_2">
			<select name="end_time_check_all">
			{{$params.end_time_check_all_option}}
			</select>
			</div>
			<br class="clear" />
		</div>
	</div>
	
	<div>
	
		{{section name=cnt loop=$params.list_data}}
			<div style="border-top:dotted 1px #000;padding:15px 10px 15px 10px;">
				{{$params.list_data[cnt]}}
			</div>
		{{/section}}
		
		<div style="border-top:dotted 1px #000;"></div>
		
		<div style="text-align:center;padding:30px 0px 0px 0px;">
			<input type="submit" name="send" value="登録" style="padding:10px 10px 10px 10px;" />
		</div>
		
	</div>
	
	</form>
	
</div>

