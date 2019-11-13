
<div id="mail_2">
	<div class="title_bar">
		セラピスト応募フォーム
	</div>
</div>

<div style="font-size:12px;padding:10px 0px 10px 10px;">
	下記入力の上、内容確認ボタンを押してください。</br >
	(<span class="chui">*</span>は入力必須項目です)</br >
	折り返し担当者からメールにてご連絡いたします</br >
</div>

<div style="padding:0px 0px 30px 0px;">

<table width="320" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<!--content start-->
			<div id="mail_content">
				<div class="box_out">
					<div class="box_in">
						
						<div style="font-size:12px;padding:0px 0px 0px 10px;">{{$params.error}}</div>
						<div id="form">
							<form method="post" action="">
								<table class="form" summary="問合せフォーム">
									<tbody>
										<tr>
											<td class="td_head">
												施術経験<span class="chui">*</span>
											</td>
											<td class="td_odd">
												<select name="experience">
													{{$params.experience_option}}
												</select>
											</td>
										</tr>
										<tr>
											<td class="td_head">
												年齢<span class="chui">*</span>
											</td>
											<td class="td_odd">
												<select name="age">
													{{$params.age_option}}
												</select>
											</td>
										</tr>
										<tr>
											<td class="td_head">
												面接希望日時
											</td>
											<td class="td_odd">
												第一候補<br />
												<select name="mensetsu_day_1">
													{{$params.day_option_1}}
												</select>
												<select name="mensetsu_time_1">
													{{$params.time_option_1}}
												</select>
												<br />
												<br />
												第二候補<br />
												<select name="mensetsu_day_2">
													{{$params.day_option_2}}
												</select>
												<select name="mensetsu_time_2">
													{{$params.time_option_2}}
												</select>
												<br />
												<br />
												第三候補<br />
												<select name="mensetsu_day_3">
													{{$params.day_option_3}}
												</select>
												<select name="mensetsu_time_3">
													{{$params.time_option_3}}
												</select>
											</td>
										</tr>
										<tr>
											<td class="td_head">
												お名前<span class="chui">*</span>
											</td>
											<td class="td_odd">
												<input type="text" name="onamae" size="25" value="{{$params.onamae}}" />
											</td>
										</tr>
										<tr>
											<td class="td_head">
												メール<br />
												アドレス<span class="chui">*</span>
											</td>
											<td class="td_odd">
												<input name="mail" type="text" size="25" value="{{$params.mail}}" />
											</td>
										</tr>
										<tr>
											<td class="td_head">
												電話番号<span class="chui">*</span>
											</td>
											<td class="td_odd">
												<input type="text" name="tel" size="26" value="{{$params.tel}}" />
											</td>
										</tr>
										
										<tr>
											<td class="td_head">
												その他<br />
												お問い合わせ
											</td>
											<td class="td_odd">
												<textarea name="sonota" cols="25" rows="7">{{$params.sonota}}</textarea>
											</td>
										</tr>
									</tbody>
								</table>
								
								<div style="padding:30px 0px 0px 0px;">
									<div style="padding:0px 0px 0px 60px;float:left;">
										<input type="button" value="戻る" name="send" style="padding:10px;" onclick="history.back();" />
									</div>
									<div style="padding:0px 0px 0px 50px;float:left;">
										<input type="submit" value="内容確認" name="send" style="padding:10px;" />
									</div>
									<br class="clear" />
								</div>
								
							</form>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>

</div>

