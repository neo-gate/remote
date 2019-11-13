
<div id="mail">

<div class="title_bar">
	WEB予約(ご入力)
</div>

<table width="300" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td>
		<!--content start-->
		<div id="mail_content">
			<div class="box_out">
				<div class="box_in">
					<div style="font-size:10px;text-align:center;padding-bottom:5px;">
						下記入力の上、内容確認ボタンを押してください。<br />
						(<span class="chui">*</span>は入力必須項目です)
					</div>
					<div>{{$params.error}}</div>
					<div id="form">
						<form method="post" action="">
							<input type="hidden" name="area" value="{{$params.area}}" id="area_rev_mail" />
							<table class="form" summary="問合せフォーム">
							<tbody>
								<tr>
									<td class="td_head">ご利用日<span class="chui">*</span></td>
									<td class="td_odd">
										{{$params.day_select_frm}}
									</td>
								</tr>
								<tr>
									<td class="td_head">ご利用開始時間<span class="chui">*</span></td>
									<td class="td_odd"><span class="style1">
									<select name="time">
										
										<option value="-1">選択してください</option>
										{{$params.time_select_option}}
										
									</select>
									</span></td>
								</tr>
								<tr>
									<td class="td_head">ご利用予定<br />コース<span class="chui">*</span></td>
									<td class="td_odd">
										<select name="course">
											{{$params.select_course}}
										</select>
										
									</td>
								</tr>
								
								{{if $params.therapist_not_disp_flg != true}}
								<tr>
									<td class="td_head">ご指名セラピスト</td>
									<td class="td_odd">
										<div id="therapist_rev_mail">
											{{$params.therapist_select_frm}}
										</div>
										<div style="font-size:10px;">
											※セラピスト指定すると指名料1000円が掛かります
										</div>
									</td>
								</tr>
								{{/if}}
								
								<tr>
									<td class="td_head">お名前<span class="chui">*</span></td>
									<td class="td_odd">
										<input type="text" name="onamae" size="26" value="{{$params.onamae}}" />
									</td>
								</tr>
								<tr>
									<td class="td_head">メールアドレス<span class="chui">*</span></td>
									<td class="td_odd">
										<input type="text" name="mail" size="26" value="{{$params.mail}}" />
									</td>
								</tr>
								
								<tr>
									<td class="td_head">電話番号<span class="chui">*</span></td>
									<td class="td_odd">
										<input type="text" name="tel" size="26" value="{{$params.tel}}" /><br />
										<span class="small_f">※ハイフン(-)なしで入力してください。</span>
									</td>
								</tr>
								
								<tr>
									<td class="td_head">住所<span class="chui">*</span></td>
									<td class="td_odd">
										
										<div style="padding-top:10px;">
											<textarea name="address" rows="3" cols="26">{{$params.address}}</textarea>
										</div>
										<div style="padding-top:10px;font-size:10px;">
											※ホテルの場合はホテル名とお部屋番号をご記入ください。<br />
											※お部屋番号が未定の場合は当日にご連絡ください。
										</div>
									</td>
								</tr>
								
								<tr>
									<td class="td_head">ご要望等</td>
									<td class="td_odd">
										<textarea name="renraku" rows="5" cols="26">{{$params.renraku}}</textarea>
									</td>
								</tr>
							</tbody>
							</table>
							<div style="text-align:center;padding:30px 0px 30px 0px;">
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


</div>