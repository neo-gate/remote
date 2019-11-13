
<div id="top_name_disp">
	{{$params.staff_name}}さん
</div>

{{include file="top_menu_beta.tpl"}}

<div id="page_operation_list">

<div class="title_bar">
<img src="{{$params.url_root_site}}img/driver/title_bar_4.gif" width="120" />
</div>

<div class="content_1">
<div>
	<input type="hidden" name="area" value="{{$params.area}}" id="hi_area" />
	<input type="hidden" name="ch" value="{{$params.ch}}" id="hi_ch" />
	<input type="hidden" name="staff_id" value="{{$params.staff_id}}" id="hi_staff_id" />
</div>
<div>
{{$params.month_select_frm}}
</div>
</div>

<div class="content_2">
<div class="left_1">
報酬(月間)
</div>
<div class="left_2">
{{$params.remuneration_all|number_format}}円
</div>
<br class="clear" />
</div>

{{if $params.this_month_flg == true}}

<div class="content_3">
<div class="left_1">
次回振込額(週)
</div>
<div class="left_2">
{{$params.furikomi_price_jikai|number_format}}円
</div>
<br class="clear" />
</div>

<div class="content_4">
<div class="left_1">
前回振込額(週)
</div>
<div class="left_2">
{{$params.furikomi_price_zenkai|number_format}}円
</div>
<br class="clear" />
</div>

{{/if}}

<div class="content_5">
<a href="shift_regist.php?id={{$params.staff_id}}&ch={{$params.ch}}&area={{$params.area}}">
<img src="{{$params.url_root_site}}img/driver/btn_5.gif" width="140" />
</a>
</div>

<div class="content_6">
{{section name=cnt loop=$params.sale_data}}

{{math equation=a%b a=$smarty.section.cnt.index b=2 assign=kekka}}

{{if $kekka == "1"}}
<div class="gyou">
{{else}}
<div class="gyou2">
{{/if}}

<a href="operation_detail.php?area={{$params.area}}&ch={{$params.ch}}&id={{$params.staff_id}}&year={{$params.sale_data[cnt].year}}&month={{$params.sale_data[cnt].month}}&day={{$params.sale_data[cnt].day}}">
{{$params.sale_data[cnt].month_disp}}/{{$params.sale_data[cnt].day_disp}}({{$params.sale_data[cnt].week_name}})　　　
{{if $params.sale_data[cnt].lowest_guarantee_flg == true}}※{{/if}}
{{$params.sale_data[cnt].remuneration|number_format}}
円
</a>

{{if $kekka == "1"}}
</div>
{{else}}
</div>
{{/if}}

{{/section}}
</div>

</div>

