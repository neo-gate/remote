<table width="650" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<!--content start-->
			<div id="content">
				<div class="box_out">
					<div class="box_in">
						<h2>
							セラピスト応募フォーム<span></span>
						</h2>
						<div style="font-size:14px;">
							下記入力の上、内容確認ボタンを押してください。
							(<span class="chui">*</span>は入力必須項目です)</br >
							折り返し担当者からメールにてご連絡いたします</br >
						</div>
						<div style="font-size:14px;">{{$params.error}}</div>
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
												第一候補
												<select name="mensetsu_day_1">
													{{$params.day_option_1}}
												</select>
												<select name="mensetsu_time_1">
													{{$params.time_option_1}}
												</select>
												<br />
												<br />
												第二候補
												<select name="mensetsu_day_2">
													{{$params.day_option_2}}
												</select>
												<select name="mensetsu_time_2">
													{{$params.time_option_2}}
												</select>
												<br />
												<br />
												第三候補
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
												<input type="text" name="onamae" size="50" value="{{$params.onamae}}" />
											</td>
										</tr>
										
										<tr>
											<td class="td_head">
												メールアドレス<span class="chui">*</span>
											</td>
											<td class="td_odd">
												<input name="mail" type="text" size="50" value="{{$params.mail}}" />
											</td>
										</tr>
										
										<tr>
											<td class="td_head">
												電話番号<span class="chui">*</span>
											</td>
											<td class="td_odd">
												<input type="text" name="tel" size="30" value="{{$params.tel}}" />
											</td>
										</tr>
										
										<tr>
											<td class="td_head">
												その他お問い合わせ
											</td>
											<td class="td_odd">
												<textarea name="sonota" cols="50" rows="7">{{$params.sonota}}</textarea>
											</td>
										</tr>
									</tbody>
								</table>
								<div align="center" style="padding-top:30px;">
									<input type="submit" value=" 　内容確認　 " name="send" style="height:30px;width:100px;" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>