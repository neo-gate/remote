
<div style="text-align:center;padding:10px 0px 5px 0px;border-bottom:solid 1px blue;">
	ドライバー：{{$params.staff_name}}さん
</div>

{{include file="top_menu.tpl"}}

<div class="title_bar">
	報酬一覧
</div>

<div style="padding:5px 0px 5px 0px;margin:0px 0px 0px 0px;">

<div id="operation_list_top">

<div class="block">
<div class="left">
{{$params.month}}月の報酬
</div>
<div class="right">
：　{{$params.remuneration_all|number_format}}円
</div>
<br class="clear" />
</div>

{{if $params.this_month_flg == true}}
<div class="block">
<div class="left">
次回振込額
</div>
<div class="right">
：　{{$params.furikomi_price_jikai|number_format}}円
</div>
<br class="clear" />
</div>
{{/if}}

{{if $params.this_month_flg == true}}
<div class="block">
<div class="left">
前回振込額
</div>
<div class="right">
：　{{$params.furikomi_price_zenkai|number_format}}円
</div>
<br class="clear" />
</div>
{{/if}}

</div>

<div id="operation_list_content">
{{section name=cnt loop=$params.sale_data}}
<div class="gyou">
<a href="operation_detail.php?area={{$params.area}}&ch={{$params.ch}}&id={{$params.staff_id}}&year={{$params.sale_data[cnt].year}}&month={{$params.sale_data[cnt].month}}&day={{$params.sale_data[cnt].day}}">
{{$params.sale_data[cnt].month_disp}}/{{$params.sale_data[cnt].day_disp}}({{$params.sale_data[cnt].week_name}})　　　
{{if $params.sale_data[cnt].lowest_guarantee_flg == true}}※{{/if}}
{{$params.sale_data[cnt].remuneration|number_format}}
円
</a>
</div>
{{/section}}
</div>

<div id="operation_list_frm">
<div>
	<input type="hidden" name="area" value="{{$params.area}}" id="hi_area" />
	<input type="hidden" name="ch" value="{{$params.ch}}" id="hi_ch" />
	<input type="hidden" name="staff_id" value="{{$params.staff_id}}" id="hi_staff_id" />
</div>
<div>
{{$params.month_select_frm}}
</div>
</div>

</div>



