
<table width="650" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td>
		<!--content start-->
		<div id="content">
			<div class="box_out">
				<div class="box_in">

					<h2>予約フォーム</h2>
					<div style="font-size:16px;padding:30px 0px 30px 0px;text-align:center;">
						メールを送信しました。
					</div>
					<div>
						<div style="text-align:center;">
							ご予約ありがとうございました。<br />
						</div>
						<div style="text-align:left;padding:10px 120px 0px 120px;">
							ご入力いただいたメールアドレスに確認メールをお届けしておりますので、内容のご確認をお願い致します。
						</div>
					</div>
					<div style="padding:50px 0px 30px 0px;text-align:center;">
						<a href="#" onClick="window.close();">ウィンドウを閉じる</a>
					</div>
				</div>
			</div>
		</div>

	</td>
	</tr>
</table>

{{if $params.tokyo_refle_flg == true}}

	{{include file=$conversion_thanks_path_web_reservation_tokyo_refle}}

{{elseif $params.yokohama_refle_flg == true}}

	{{include file=$conversion_thanks_path_web_reservation_yokohama_refle}}

{{elseif $params.sapporo_refle_flg == true}}

	{{include file=$conversion_thanks_path_web_reservation_sapporo_refle}}

{{elseif $params.sendai_refle_flg == true}}

	{{include file=$conversion_thanks_path_web_reservation_sendai_refle}}

{{elseif $params.osaka_refle_flg == true}}

	{{include file=$conversion_thanks_path_web_reservation_osaka_refle}}

{{/if}}



