<div id="content">
		
	<div style="margin:20px auto 0px auto;width:320px;">
		<hr />
	</div>
	
	<div style="margin:30px auto 50px auto;width:320px;">
		<div style="padding:0px 30px 0px 30px;">
			<form action="" method="post">
				<div style="line-height:160%;">
					以下の通りご予約を承りました。<br />
					ご登録頂いたメールアドレスもしくはお電話番号に確認のご連絡をさせて頂く場合がございます。
				</div>
				<div style="padding:0px 20px 0px 20px;">
					<div style="padding:30px 0px 0px 0px;">
						<div>
							■ご予約者名
						</div>
						<div style="padding:5px 0px 0px 30px;">
							{{$params.customer_name}}様
						</div>
					</div>
					<div style="padding:10px 0px 0px 0px;">
						<div>
							■日時
						</div>
						<div style="padding:5px 0px 0px 30px;">
							{{$params.nichizi}}
						</div>
					</div>
					<div style="padding:10px 0px 0px 0px;">
						<div>
							■コース
						</div>
						<div style="padding:5px 0px 0px 30px;">
							{{$params.course}}分コース
						</div>
					</div>
					<div style="padding:10px 0px 0px 0px;">
						<div>
							■セラピスト
						</div>
						<div style="padding:5px 0px 0px 30px;">
							{{$params.therapist_name}}
							{{if $params.therapist_id ne "-1"}}
								(ご指名)
							{{/if}}
						</div>
					</div>
				</div>
				<div style="text-align:center;padding-top:30px;">
					ご予約誠にありがとうございました。
				</div>
				<div style="text-align:center;padding-top:20px;">
					<a href="{{$params.url_root}}vip/therapist_calendar.php">セラピスト出勤カレンダーに戻る</a>
				</div>
			</form>
		</div>
	</div>
	
</div>

