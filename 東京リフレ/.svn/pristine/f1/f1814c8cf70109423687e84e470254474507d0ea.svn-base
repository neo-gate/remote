<div id="content">
			
	<div style="margin:20px auto 0px auto;width:320px;">
		<hr />
	</div>
	
	<div style="margin:20px auto 0px auto;width:320px;">
		<div style="padding:0px 30px 0px 30px;">
			<div style="color:red;">{{$params.error}}</div>
			<form action="" method="post">
				<div>
					以下の通りの内容で宜しいでしょうか？
				</div>
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
					{{if $params.therapist_id != "-1"}}
					
						<div style="padding:0px 0px 0px 30px;color:red;">
							※セラピストを指定されますと指名料1,000円を頂戴いたしております。
						</div>
					
					{{/if}}
				</div>
				<div style="padding:10px 0px 0px 0px;">
					<div>
						■ご要望、ご連絡
					</div>
					<div style="padding:5px 0px 0px 30px;">
						{{if $params.free == ""}}
							ご記入なし
						{{else}}
							{{$params.free|nl2br}}
						{{/if}}
					</div>
				</div>
				<div style="padding:10px 0px 0px 0px;">
					<div>
						■メールアドレス
					</div>
					<div style="padding:5px 0px 0px 30px;">
						{{$params.mail}}
					</div>
				</div>
				<div style="padding:30px 0px 50px 40px;">
					<span>
						<input type="submit" value="戻る" name="back" style="width:80px;height:30px;" />
					</span>
					<span style="padding-left:30px;">
						<input type="submit" value="送信" name="send" style="width:80px;height:30px;" />
					</span>
				</div>
			</form>
		</div>
	</div>
	
</div>

