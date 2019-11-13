
<div class="title_block">
	<h2>セラピスト紹介</h2>
	
	<div id="therapist_list">
		
		{{section name=cnt loop=$params.therapist_data}}
		
			<div class="title_therapist_info_content">
				<div class="top_area">
					<div class="name_img">
						<p class="therapist_name">
							{{$params.therapist_data[cnt].therapist_name}}（{{$params.therapist_data[cnt].age}}）
						</p>
						<p class="img">
							<img src="{{$smarty.const.S3_URL}}{{$params.therapist_data[cnt].img_url}}" alt="セラピスト{{$params.therapist_data[cnt].therapist_name}}" width="100" />
						</p>
						<p class="info">
							{{$params.therapist_data[cnt].hometown}}出身｜セラピスト歴{{$params.therapist_data[cnt].history}}
						</p>
						
					</div>
					
					<div class="tokui">
						{{if $params.therapist_data[cnt].skill_2_exist_flg == true}}
						<div class="one" style="margin-bottom:15px;">
							<div class="title_1">
								○一押しメニュー
							</div>
							<div class="content">
							{{section name=cnt2 loop=$params.therapist_data[cnt].skill_2_data}}
							
								{{assign var="x" value=$params.therapist_data[cnt].skill_2_data[cnt2]}}
								
								<div class="left">
									<img src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" alt="{{$params.skill_data[$x]}}" width="105" />
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
							<div class="content">
							{{section name=cnt2 loop=$params.therapist_data[cnt].skill_data}}
							
								{{assign var="x" value=$params.therapist_data[cnt].skill_data[cnt2]}}
								
								<div class="left">
									<img src="{{$smarty.const.REFLE_WWW_URL}}img/skill/{{$x}}.png" alt="{{$params.skill_data[$x]}}" width="105" />
								</div>
								
							{{/section}}
							<br class="clear" />
							</div>
						</div>
					</div>
					
					<br class="clear" />
				</div>
				
				<div class="separator">
					<img src="{{$smarty.const.REFLE_WWW_URL}}img/lp/pc/separator.jpg" width="600" alt="セパレーター" />
				</div>
			
				<p class="exp2">
					{{$params.therapist_data[cnt].pr_content|nl2br}}
					
					{{if $params.therapist_data[cnt].shikaku != ""}}
						<br />
						【保有資格】<br />
						{{$params.therapist_data[cnt].shikaku}}<br />
					{{/if}}
					
				</p>
				
				<div class="separator2">
					<img src="{{$smarty.const.REFLE_WWW_URL}}img/lp/pc/separator.jpg" width="600" alt="セパレーター" />
				</div>
				
<div class="reservation_3">
<div class="btn">
<a href="javascript:openwin1();">
<img src="{{$smarty.const.REFLE_WWW_URL}}img/lp/pc/reservation_btn.png" alt="WEB予約" width="240" />
</a>
</div>
</div>

			</div>
			
		{{sectionelse}}
		
		<div style="padding:0px 0px 0px 30px;">セラピストが登録されていません。</div>
		
		{{/section}}
		

	</div>
</div>

