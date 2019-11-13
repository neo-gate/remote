
<div style="text-align:center;padding-top:10px;">
ドライバー：{{$params.staff_name}}さん
</div>

<div style="padding:20px 0px 0px 0px;">
	<div style="float:left;">
		○シフト修正　{{$params.month}}月
	</div>
	<div style="float:right;padding:0px 0px 0px 0px;">
		{{include file="to_top.tpl"}}
	</div>
	<br class="clear" />
</div>

<div style="color:red;padding:10px 0px 0px 0px;">{{$params.error}}</div>
<div style="padding:10px 0px 0px 0px;">
	<form action="" method="post">
		<input type="hidden" name="staff_id" value="{{$params.staff_id}}" />
		<input type="hidden" name="area" value="{{$params.area}}" />
		<input type="hidden" name="year" value="{{$params.year}}" />
		<input type="hidden" name="month" value="{{$params.month}}" />
		<input type="hidden" name="day" value="{{$params.day}}" />
		<input type="hidden" name="week_name" value="{{$params.week_name}}" />
		<input type="hidden" name="start_start_time" value="{{$params.start_start_time}}" />
		<input type="hidden" name="start_end_time" value="{{$params.start_end_time}}" />
		<input type="hidden" name="ch" value="{{$params.ch}}" />
		<div style="border-top:dotted 1px #000;"></div>
		<div style="padding:20px 0px 0px 0px;">
			{{$params.day}}({{$params.week_name}})　
			<select name="start_time">
				{{$params.start_time_option}}
			</select>
			&nbsp;&nbsp;&nbsp;
			<select name="end_time">
				{{$params.end_time_option}}
			</select>
		</div>
				
		<br />
		
		<div style="border-top:dotted 1px #000;"></div>
		<div style="padding:20px 0px 0px 50px;">
			<div><input type="checkbox" name="kekkin" value="1" />欠勤に変更する</div>
			<div style="color:red;padding:5px 0px 0px 0px;font-size:12px;">
				当日欠勤は余程の事情がない限り控えてください
			</div>
		</div>
		<div style="text-align:center;padding:30px 0px 0px 0px;">
			<input type="submit" name="send" value="修正" style="padding:10px 10px 10px 10px;" />
		</div>
	</form>
</div>
