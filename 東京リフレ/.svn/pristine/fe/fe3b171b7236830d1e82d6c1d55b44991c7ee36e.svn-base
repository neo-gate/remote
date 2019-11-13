<div id="top_name_disp">{{$params.staff_name}}さん</div>
{{include file="top_menu_beta_test.tpl"}}
<div id="page_index">
	{{if $params.message_board_data_num != "0"}}
	<div>
		<div class="title">
			<img src="{{$params.url_root_site}}img/shift/title/02_heading.gif" width="75" />
		</div>
		<div>
			{{if $params.shift_notice_flg == true}}
			<div class="top_shift_request">
				<div class="left_1">
					<img src="{{$params.url_root_site}}img/shift/icon/02_icon.gif" width="30" />
				</div>
				<div class="left_2">シフトの入力をお願いします</div>
				<br class="clear" />
			</div>
			{{/if}}
			<div id="top_message_board_box">
			{{section name=cnt loop=$params.message_board_data}}
			{{math equation=a%b a=$smarty.section.cnt.index b=2 assign=gyou_type}}
			{{if $gyou_type == "1"}}<div class="one_2">{{else}}<div class="one_1">{{/if}}
				<div style="font-size:12px;">{{$params.message_board_data[cnt].day_disp}}</div>
				<div style="padding-top:3px;">
					<a href="message_board_test.php?area={{$params.area}}&ch={{$params.ch}}&id={{$params.staff_id}}#page_{{$params.message_board_data[cnt].id}}">{{$params.message_board_data[cnt].title}}</a>
				</div>
			{{if $gyou_type == "1"}}</div>{{else}}</div>{{/if}}
			{{/section}}
			</div>
		</div>
	</div>
	{{/if}}
	<div class="title">
		<img src="{{$params.url_root_site}}img/driver/title_bar_3.gif" width="160" />
	</div>
	<div class="content_1">
		<div class="left_1">
			<a href="work_start.php?id={{$params.staff_id}}&ch={{$params.ch}}&area={{$params.area}}">
			<img src="{{$params.url_root_site}}img/driver/btn_3.gif" width="130" />
		</a>
		</div>
		<div class="left_2">
			<a href="work_end.php?id={{$params.staff_id}}&ch={{$params.ch}}&area={{$params.area}}">
			<img src="{{$params.url_root_site}}img/driver/btn_4.gif" width="130" />
		</a>
		</div>
		<br class="clear" />
	</div>
	<div class="title_2">
		<img src="{{$params.url_root_site}}img/driver/title_bar_6.gif" width="160" />
	</div>
	<div class="content_2">
		<a href="shift_regist.php?id={{$params.staff_id}}&ch={{$params.ch}}&area={{$params.area}}">
		<img src="{{$params.url_root_site}}img/driver/btn_5.gif" width="140" /></a>
	</div>
</div>
