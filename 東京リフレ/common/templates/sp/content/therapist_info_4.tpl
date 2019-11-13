<div id="therapist_info">
	
	<div class="title_bar">
		セラピスト紹介
	</div>
	
	<div id="therapist_list">
	
		{{section name=cnt loop=$params.therapist_data}}
	
			<div class="title_therapist_info_content">
				<div class="top_area">
					<div class="name_img">
						<p class="therapist_name">
							{{$params.therapist_data[cnt].therapist_name}}（{{$params.therapist_data[cnt].age}}）
						</p>
						<p class="img">
							<img src="{{$smarty.const.S3_URL}}{{$params.therapist_data[cnt].img_url}}" alt="セラピスト{{$params.therapist_data[cnt].therapist_name}}" width="70" />
						</p>
						<p class="info">
							{{$params.therapist_data[cnt].hometown}}出身<br />
							セラピスト歴{{$params.therapist_data[cnt].history}}
						</p>
						
					</div>
					<div class="tokui">
						{{if $params.therapist_data[cnt].skill_2_exist_flg == true}}
						<div class="one" style="margin-bottom:15px;">
							<div class="title_1">
								○一押しメニュー
							</div>
							<div class="content_sp">
							{{section name=cnt2 loop=$params.therapist_data[cnt].skill_2_data}}
							
								{{assign var="x" value=$params.therapist_data[cnt].skill_2_data[cnt2]}}
								
								<div class="left">
									<img src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" alt="{{$params.skill_data[$x]}}" width="80" />
								</div>
								
							{{/section}}
							<br class="clear" />
							</div>
						</div>
						{{/if}}
						<div class="one">
							<div class="title_2">
								○施術可能メニュー
							</div>
							<div class="content_sp">
							{{section name=cnt2 loop=$params.therapist_data[cnt].skill_data}}
							
								{{assign var="x" value=$params.therapist_data[cnt].skill_data[cnt2]}}
								
								<div class="left">
									<img src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" alt="{{$params.skill_data[$x]}}" width="80" />
								</div>
								
							{{/section}}
							<br class="clear" />
							</div>
						</div>
					</div>
					<br class="clear" />
				</div>
				
				<div><img src="{{$params.refle_url_root}}img/sp/201503/kugiri.jpg" alt="区切り" width="310" /></div>
				
				<p class="exp">
					{{$params.therapist_data[cnt].pr_new|nl2br}}
					
					{{if $params.therapist_data[cnt].shikaku != ""}}
						<br />
						【保有資格】<br />
						{{$params.therapist_data[cnt].shikaku}}<br />
					{{/if}}
				</p>
				
				<div><img src="{{$params.refle_url_root}}img/sp/201503/kugiri.jpg" alt="区切り" width="310" /></div>
				
<div style="margin:10px 0px 0px 0px;">
<div style="float:left;padding-left:10px;">
<a onclick="goog_report_conversion('tel:{{$params.shop_tel}}')" href="#">
<img src="{{$params.refle_url_root}}img/sp/201503/yoyaku_tel.jpg" alt="電話予約" width="130" />
</a>
</div>
<div style="float:left;padding-left:30px;">
<a href="{{$params.url_root}}mail/reservation/input.php">
<img src="{{$params.refle_url_root}}img/sp/201503/yoyaku_web.jpg" alt="WEB予約" width="130" />
</a>
</div>
<br class="clear" />
</div>
				
			</div>
						
		{{sectionelse}}
		
			<div style="text-align:center;">セラピストは登録されていません。</div>
		
		{{/section}}
		
		
	</div>
</div>