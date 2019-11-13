
<div id="mail">

<div class="title_bar">
	WEB予約(送信完了)
</div>

<table width="300" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td>
		<!--content start-->
		<div id="mail_content">
			<div class="box_out">
				<div class="box_in">
					<div style="font-size:14px;padding:30px 30px 0px 30px;">
						メールを送信しました。
					</div>
					<div style="padding:30px 30px 30px 30px;line-height:160%;">
						ご予約ありがとうございました。<br />
						ご入力いただいたメールアドレスに確認メールをお届けしておりますので、<br />
						内容のご確認をお願い致します。
					</div>
					
				</div>
			</div>
		</div>

	</td>
	</tr>
</table>

</div>

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



