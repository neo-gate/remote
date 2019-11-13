
<div style="text-align:center;padding:10px 0px 5px 0px;border-bottom:solid 1px blue;">
	ドライバー：{{$params.staff_name}}さん
</div>

{{include file="top_menu.tpl"}}

<div class="title_bar">
	報酬詳細
</div>

<div style="padding:5px 0px 5px 0px;margin:0px 0px 0px 0px;">

<div id="operation_detail_top">

<div class="time">
□{{$params.month_disp}}月{{$params.day_disp}}日（{{$params.week_name}}）
</div>

<div class="block">
<div class="left">
振込金額　：
</div>
<div class="right">
{{$params.furikomi_data.furikomi_price|number_format}}円
</div>
<br class="clear" />
</div>

{{if $params.furikomi_data.furikomi_price > 0}}

<div class="block">
<div class="left">
勤務　：
</div>
<div class="right">
{{$params.furikomi_data.start_hour}}時{{$params.furikomi_data.start_minute}}分～{{$params.furikomi_data.end_hour}}時{{$params.furikomi_data.end_minute}}分
</div>
<br class="clear" />
</div>

<div class="block">
<div class="left">
勤務時間　：
</div>
<div class="right">
{{$params.furikomi_data.work_time}}
</div>
<br class="clear" />
</div>

<div class="block">
<div class="left">
時給　：
</div>
<div class="right">
{{$params.furikomi_data.pay_hour|number_format}}円
</div>
<br class="clear" />
</div>

{{if $params.furikomi_data.settings_gasoline_value > 0}}

<div class="block">
<div class="left">
走行距離　：
</div>
<div class="right">
{{$params.furikomi_data.car_distance}}km
</div>
<br class="clear" />
</div>

<div class="block">
<div class="left">
ガソリン価格　：
</div>
<div class="right">
{{$params.furikomi_data.settings_gasoline_value|number_format}}円/L
</div>
<br class="clear" />
</div>

<div class="block">
<div class="left">
ガソリン代　：
</div>
<div class="right">
{{$params.furikomi_data.gasoline_value|number_format}}円
</div>
<br class="clear" />
</div>

{{/if}}

{{if $params.furikomi_data.car_distance_over_allowance > 0}}
<div class="block">
<div class="left">
<span style="font-size:10px;">超過手当(走行距離)</span>　：
</div>
<div class="right">
{{$params.furikomi_data.car_distance_over_allowance|number_format}}円
</div>
<br class="clear" />
</div>
{{/if}}

{{if $params.furikomi_data.chief_allowance > 0}}
<div class="block">
<div class="left">
チーフ手当　：
</div>
<div class="right">
{{$params.furikomi_data.chief_allowance|number_format}}円
</div>
<br class="clear" />
</div>
{{/if}}

{{if $params.furikomi_data.sonota > 0}}
<div class="block">
<div class="left">
その他　：
</div>
<div class="right">
{{$params.furikomi_data.sonota|number_format}}円
</div>
<br class="clear" />
</div>
{{/if}}

{{if $params.furikomi_data.pay_finish > 0}}
<div class="block">
<div class="left">
清算済み　：
</div>
<div class="right">
{{$params.furikomi_data.pay_finish|number_format}}円
</div>
<br class="clear" />
</div>
{{/if}}

{{/if}}

</div>

</div>



