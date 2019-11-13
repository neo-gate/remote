<div id="top_name_disp">
	{{$params.staff_name}}さん
</div>
{{include file="top_menu_beta.tpl"}}
<div id="page_operation_detail">
	<div class="title_bar"><img src="{{$params.url_root_site}}img/driver/title_bar_5.gif" width="120" /></div>
	<div class="content_1">{{$params.month_disp}}/{{$params.day_disp}}（{{$params.week_name}}）</div>
	<div class="content_2" style="border-bottom:dashed 1px #4a7ebb;padding-bottom:10px;margin-bottom:10px;">
		<div class="left_1">総支給額</div>
		<div class="left_2">{{$params.furikomi_data.furikomi_price|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">報酬</div>
		<div class="left_2">{{$params.furikomi_data.remuneration|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	{{if $params.remuneration_type !=2 }}
	<div class="content_2">
		<div class="left_1">インセンティブ</div>
		<div class="left_2">{{$params.furikomi_data.car_distance_over_allowance|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">インセンティブ２</div>
		<div class="left_2">{{$params.furikomi_data.gasoline_value|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	{{else}}
	<div class="content_2">
		<div class="left_1">走行距離/時間</div>
		<div class="left_2">{{$params.distance_ave}}&nbsp;km/h</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">報酬単価</div>
		<div class="left_2">{{$params.unit_price}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	{{/if}}
	<div class="content_2">
		<div class="left_1">高速代</div>
		<div class="left_2">{{$params.furikomi_data.highway|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">駐車場代</div>
		<div class="left_2">{{$params.furikomi_data.parking|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2" style="border-bottom:dashed 1px #4a7ebb;padding-bottom:10px;margin-bottom:10px;">
		<div class="left_1">清算済み</div>
		<div class="left_2">{{$params.furikomi_data.pay_finish|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">出勤</div>
		<div class="left_2">{{$params.start_time}}</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">退勤</div>
		<div class="left_2">{{$params.end_time}}</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">勤務時間</div>
		<div class="left_2">{{$params.furikomi_data.work_time}}&nbsp;時間</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">手当</div>
		<div class="left_2">{{$params.furikomi_data.allowance|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">走行距離</div>
		<div class="left_2">{{$params.furikomi_data.car_distance}}&nbsp;km</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	{{if $params.remuneration_type !=2 }}
	<div class="content_2">
		<div class="left_1">リッター単価</div>
		<div class="left_2">{{$params.settings_gasoline|number_format}}&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	{{/if}}
</div>
