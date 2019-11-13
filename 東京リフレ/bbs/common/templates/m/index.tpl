
<div style="margin:20px 0px 0px 13px;">

	<div>
		{{if $params.page_area_menu == "all"}}
		<div style="float:left;">
		<form action="" method="get">
		<input type="hidden" name="page_area" value="all" />
		<input type="submit" name="send_menu" value="全体" class="top_menu_btn" />
		</form>
		</div>
		{{/if}}
		
		{{if ($params.page_area_menu == "all") || ($params.page_area_menu == "tokyo")}}
		<div style="float:left;padding-left:20px;">
		<form action="" method="get">
		<input type="hidden" name="page_area" value="tokyo" />
		<input type="submit" name="send_menu" value="東京" class="top_menu_btn" />
		</form>
		</div>
		{{/if}}
		
		{{if ($params.page_area_menu == "all") || ($params.page_area_menu == "yokohama")}}
		<div style="float:left;padding-left:20px;">
		<form action="" method="get">
		<input type="hidden" name="page_area" value="yokohama" />
		<input type="submit" name="send_menu" value="横浜" class="top_menu_btn" />
		</form>
		</div>
		{{/if}}
		<br class="clear" />
	</div>
	
	<div style="margin-top:10px;">
		{{if ($params.page_area_menu == "all") || ($params.page_area_menu == "sapporo")}}
		<div style="float:left;">
		<form action="" method="get">
		<input type="hidden" name="page_area" value="sapporo" />
		<input type="submit" name="send_menu" value="札幌" class="top_menu_btn" />
		</form>
		</div>
		{{/if}}
		
		{{if ($params.page_area_menu == "all") || ($params.page_area_menu == "sendai")}}
		<div style="float:left;padding-left:20px;">
		<form action="" method="get">
		<input type="hidden" name="page_area" value="sendai" />
		<input type="submit" name="send_menu" value="仙台" class="top_menu_btn" />
		</form>
		</div>
		{{/if}}
		
		{{if ($params.page_area_menu == "all") || ($params.page_area_menu == "osaka")}}
		<div style="float:left;padding-left:20px;">
		<form action="" method="get">
		<input type="hidden" name="page_area" value="osaka" />
		<input type="submit" name="send_menu" value="大阪" class="top_menu_btn" />
		</form>
		</div>
		{{/if}}
		<br class="clear" />
	</div>

</div>

{{if ($params.page_area == "all") || ($params.therapist_flg == true)}}
<div style="text-align:center;padding:30px 0px 0px 0px;">
&nbsp;
</div>
{{else}}
<div style="text-align:center;padding:30px 0px 30px 0px;">
<a href="regist.php?area={{$params.page_area}}">新規メッセージ投稿</a>
</div>
{{/if}}

<div id="top_message_disp">
	{{section name=cnt loop=$params.shift_message_data}}
		<div style="position:relative;margin:0px 0px 20px 0px;">
			{{if $params.shift_message_data[cnt].message_type == "mail_start"}}
				<div class="mail_start_box">
					<div>
						<div style="float:left;font-size:10px;">{{$params.shift_message_data[cnt].time_disp}}</div>
						<div style="float:left;padding-left:10px;font-size:10px;">
							{{$params.shift_message_data[cnt].from_to_disp}}
						</div>
						<br class="clear" />
					</div>
					<div>
						<div style="float:left;width:60px;font-size:10px;">スタート連絡：</div>
						<div style="float:left;width:170px;">
							{{$params.shift_message_data[cnt].content}}
						</div>
						<br class="clear" />
					</div>
					<div style="text-align:right;">
{{if $params.therapist_flg != true}}
<a href="response.php?area={{$params.shift_message_data[cnt].area}}&staff_id={{$params.shift_message_data[cnt].from_staff_id}}&therapist_id={{$params.shift_message_data[cnt].therapist_id}}">
	Res.
</a>
{{/if}}
					</div>
				</div>
			{{elseif $params.shift_message_data[cnt].message_type == "mail_end"}}
				<div class="mail_end_box">
					<div>
						<div style="float:left;font-size:10px;">{{$params.shift_message_data[cnt].time_disp}}</div>
						<div style="float:left;padding-left:10px;font-size:10px;">
							{{$params.shift_message_data[cnt].from_to_disp}}
						</div>
						<br class="clear" />
					</div>
					<div>
						<div style="float:left;width:60px;font-size:10px;">終了連絡：</div>
						<div style="float:left;width:170px;">
							{{$params.shift_message_data[cnt].content}}
						</div>
						<br class="clear" />
					</div>
					<div style="text-align:right;">
{{if $params.therapist_flg != true}}
<a href="response.php?area={{$params.shift_message_data[cnt].area}}&staff_id={{$params.shift_message_data[cnt].from_staff_id}}&therapist_id={{$params.shift_message_data[cnt].therapist_id}}">
	Res.
</a>
{{/if}}
					</div>
				</div>
			{{elseif $params.shift_message_data[cnt].message_type == "mail_change"}}
				<div class="mail_change_box">
					<div>
						<div style="float:left;font-size:10px;">{{$params.shift_message_data[cnt].time_disp}}</div>
						<div style="float:left;padding-left:10px;font-size:10px;">
							{{$params.shift_message_data[cnt].from_to_disp}}
						</div>
						<br class="clear" />
					</div>
					<div>
						<div style="float:left;width:60px;font-size:10px;">コース変更：</div>
						<div style="float:left;width:170px;">
							{{$params.shift_message_data[cnt].content}}
						</div>
						<br class="clear" />
					</div>
					<div style="text-align:right;">
{{if $params.therapist_flg != true}}
<a href="response.php?area={{$params.shift_message_data[cnt].area}}&staff_id={{$params.shift_message_data[cnt].from_staff_id}}&therapist_id={{$params.shift_message_data[cnt].therapist_id}}">
	Res.
</a>
{{/if}}
					</div>
				</div>
			{{elseif $params.shift_message_data[cnt].type == "1"}}
				<div class="honbu_message_fuki">
					<img src="{{$smarty.const.REFLE_WWW_URL}}img/shift/fuki2.gif" width="10" />
				</div>
				<div class="honbu_message_box">
					<div>
						<div>
							<div style="font-size:10px;">{{$params.shift_message_data[cnt].time_disp}}</div>
							<div style="font-size:10px;">
								{{$params.shift_message_data[cnt].from_to_disp}}
							</div>
						</div>
						<div>{{$params.shift_message_data[cnt].content}}</div>
					</div>
					<div style="text-align:right;">
{{if $params.therapist_flg != true}}
<a href="response.php?area={{$params.shift_message_data[cnt].area}}&staff_id={{$params.shift_message_data[cnt].from_staff_id}}&therapist_id={{$params.shift_message_data[cnt].therapist_id}}">
	Res.
</a>
{{/if}}
					</div>
				</div>
			{{else}}
				<div class="therapist_message_fuki">
					<img src="{{$smarty.const.REFLE_WWW_URL}}img/shift/fuki1.gif" width="10" />
				</div>
				<div class="therapist_message_box">
					<div>
						<div>
							<div style="font-size:10px;">{{$params.shift_message_data[cnt].time_disp}}</div>
							<div style="font-size:10px;">
								{{$params.shift_message_data[cnt].from_to_disp}}
							</div>
						</div>
						<div>{{$params.shift_message_data[cnt].content}}</div>
					</div>
					<div style="text-align:right;">
{{if $params.therapist_flg != true}}
<a href="response.php?area={{$params.shift_message_data[cnt].area}}&staff_id={{$params.shift_message_data[cnt].from_staff_id}}&therapist_id={{$params.shift_message_data[cnt].therapist_id}}">
	Res.
</a>
{{/if}}
					</div>
				</div>
			{{/if}}
			<br class="clear" />
		</div>
	{{sectionelse}}
		<div style="text-align:center;padding:30px 0px 30px 0px;">
			メッセージはありません
		</div>
	{{/section}}
</div>










<div style="text-align:center;padding:20px 0px 50px 0px;">
<a href="{{$smarty.const.WWW_URL}}logout.php">ログアウト</a>
</div>











