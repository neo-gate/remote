
<div id="mail">

<div class="title_bar">
	WEB予約(ご確認)
</div>


<table width="300" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td>
		<!--content start-->
		<div id="mail_content" style="padding-left:10px;">
			<div class="box_out">
				<div class="box_in">
					<div style="text-align:center;padding:0px 0px 20px 0px;">
						以下の内容でよろしいですか？
					</div>
					<div style="color:red;">{{$params.error}}</div>
					<div id="form">
						<div>
							<table class="form" summary="問合せフォーム">
							<tbody>
								<tr>
									<td class="td_head">ご利用日<span class="chui">*</span></td>
									<td class="td_odd">
										{{$params.day_disp}}
									</td>
								</tr>
								<tr>
									<td class="td_head">ご利用開始時間<span class="chui">*</span></td>
									<td class="td_odd">
										{{$params.time_disp}}
									</td>
								</tr>
								<tr>
									<td class="td_head">ご利用予定<br />コース<span class="chui">*</span></td>
									<td class="td_odd">
										{{$params.course}}
									</td>
								</tr>
								
								{{if $params.therapist_not_disp_flg != true}}
								<tr>
									<td class="td_head">ご指名セラピスト</td>
									<td class="td_odd">
										{{$params.therapist_name}}
									</td>
								</tr>
								{{/if}}
								
								<tr>
									<td class="td_head">お名前<span class="chui">*</span></td>
									<td class="td_odd">
										{{$params.onamae}}
									</td>
								</tr>
								<tr>
									<td class="td_head">メールアドレス<span class="chui">*</span></td>
									<td class="td_odd">
										{{$params.mail}}
									</td>
								</tr>
								<tr>
									<td class="td_head">電話番号<span class="chui">*</span></td>
									<td class="td_odd">
										{{$params.tel}}
									</td>
								</tr>
								<tr>
									<td class="td_head">住所<span class="chui">*</span></td>
									<td class="td_odd">
										{{$params.address}}
									</td>
								</tr>
								
								<tr>
									<td class="td_head">ご要望等</td>
									<td class="td_odd">
										{{$params.renraku}}
									</td>
								</tr>
							</tbody>
							</table>
						</div>
						<div style="padding:30px 0px 30px 60px;">
							<form action="" method="post">
								<div style="float:left;">
									<input type="submit" value="戻る" name="back" style="height:30px;width:60px;"/>
								</div>
								<div style="float:left;padding-left:50px;">
									<input type="submit" value="送信する" name="send" style="height:30px;width:80px;" />
								</div>
								<br style="clear:both" />
							</form>
						</div>
						
					</div>
				</div>
			</div>
		</div>

	</td>
	</tr>
</table>

</div>
