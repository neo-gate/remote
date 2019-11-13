<table width="650" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<!--content start-->
			<div id="content">
				<div class="box_out">
					<div class="box_in">
						<h2>セラピスト応募フォーム<span></span></h2>
						<div>
						以下の内容でよろしいですか？
						</div>
						<div style="color:red;">{{$params.error}}</div>
						<div id="form">
							<div>
								<table class="form">
								<tbody>
									<tr>
										<td class="td_head">
											施術経験
										</td>
										<td class="td_odd">
											{{$params.experience}}
										</td>
									</tr>
									<tr>
										<td class="td_head">
											年齢
										</td>
										<td class="td_odd">
											{{$params.age}}
										</td>
									</tr>
									<tr>
										<td class="td_head">
											面接希望日時(第一候補)
										</td>
										<td class="td_odd">
											{{$params.mensetsu_day_1}}　{{$params.mensetsu_time_1}}
										</td>
									</tr>
									<tr>
										<td class="td_head">
											面接希望日時(第二候補)
										</td>
										<td class="td_odd">
											{{$params.mensetsu_day_2}}　{{$params.mensetsu_time_2}}
										</td>
									</tr>
									<tr>
										<td class="td_head">
											面接希望日時(第三候補)
										</td>
										<td class="td_odd">
											{{$params.mensetsu_day_3}}　{{$params.mensetsu_time_3}}
										</td>
									</tr>
									<tr>
										<td class="td_head">
											お名前
										</td>
										<td class="td_odd">
											{{$params.onamae}}
										</td>
									</tr>
									
									<tr>
										<td class="td_head">
											メールアドレス
										</td>
										<td class="td_odd">
											{{$params.mail}}
										</td>
									</tr>
									
									<tr>
										<td class="td_head">
											電話番号
										</td>
										<td class="td_odd">
											{{$params.tel}}
										</td>
									</tr>
									
									<tr>
										<td class="td_head">
											その他お問い合わせ
										</td>
										<td class="td_odd">
											{{$params.sonota|nl2br}}
										</td>
									</tr>
								</tbody>
								</table>
							</div>
							<div style="padding:30px 0px 30px 210px;">
								<form action="" method="post">
									<div style="float:left;">
										<input type="submit" value="戻る" name="back" style="height:30px;width:60px;"/>
									</div>
									<div style="float:left;padding-left:50px;">
										<input type="submit" value="送信する" name="send" style="height:30px;width:60px;" />
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