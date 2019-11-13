
<div id="top_name_disp">
	{{$params.staff_name}}さん
</div>

{{include file="top_menu_beta.tpl"}}

<div id="page_operation_detail">

<div class="title_bar">
<img src="{{$params.url_root_site}}img/driver/title_bar_5.gif" width="120" />
</div>

<div class="content_1">
{{$params.month_disp}}/{{$params.day_disp}}（{{$params.week_name}}）
</div>

{{if $params.switching_result == true}}

<div class="content_2">
<div class="left_1">
報酬
</div>
<div class="left_2">
{{$params.furikomi_data.furikomi_price|number_format}}
</div>
<div class="left_3">
円
</div>
<br class="clear" />
</div>

<div class="content_2">
<div class="left_1">
稼働距離
</div>
<div class="left_2">
{{$params.furikomi_data.car_distance}}
</div>
<div class="left_3">
km
</div>
<br class="clear" />
</div>

<div class="content_2">
<div class="left_1">
稼働単価
</div>
<div class="left_2">
{{$params.furikomi_data.unit_price}}
</div>
<div class="left_3">
円
</div>
<br class="clear" />
</div>

{{if $params.furikomi_data.highway > 0}}
<div class="content_2">
<div class="left_1">
高速代
</div>
<div class="left_2">
{{$params.furikomi_data.highway|number_format}}
</div>
<div class="left_3">
円
</div>
<br class="clear" />
</div>
{{/if}}

{{if $params.furikomi_data.parking > 0}}
<div class="content_2">
<div class="left_1">
駐車場代
</div>
<div class="left_2">
{{$params.furikomi_data.parking|number_format}}
</div>
<div class="left_3">
円
</div>
<br class="clear" />
</div>
{{/if}}

{{if $params.furikomi_data.pay_finish > 0}}
<div class="content_2">
<div class="left_1">
清算済み
</div>
<div class="left_2">
{{$params.furikomi_data.pay_finish|number_format}}
</div>
<div class="left_3">
円
</div>
<br class="clear" />
</div>
{{/if}}

{{if $params.furikomi_data.pay_day > 0}}
<div class="content_2">
<div class="left_1">
日払い
</div>
<div class="left_2">
{{$params.furikomi_data.pay_day|number_format}}
</div>
<div class="left_3">
円
</div>
<br class="clear" />
</div>
{{/if}}

{{else}}

<div id="operation_detail_top">

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

{{/if}}

</div>

