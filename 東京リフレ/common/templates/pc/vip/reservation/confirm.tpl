<div style="padding:30px 0px 0px 200px;">
	<div style="color:red;">{{$params.error}}</div>
	<form action="" method="post">
		<div>
			以下の通りの内容で宜しいでしょうか？
		</div>
		<div style="padding:30px 0px 0px 0px;">
			<div>
				■ご予約者名
			</div>
			<div style="padding:0px 0px 0px 30px;">
				{{$params.customer_name}}様
			</div>
		</div>
		<div style="padding:10px 0px 0px 0px;">
			<div>
				■日時
			</div>
			<div style="padding:0px 0px 0px 30px;">
				{{$params.nichizi}}
			</div>
		</div>
		<div style="padding:10px 0px 0px 0px;">
			<div>
				■コース
			</div>
			<div style="padding:0px 0px 0px 30px;">
				{{$params.course}}分コース
			</div>
		</div>
		<div style="padding:10px 0px 0px 0px;">
			<div>
				■セラピスト
			</div>
			<div style="padding:0px 0px 0px 30px;">
				{{$params.therapist_name}}
				{{if $params.therapist_id ne "-1"}}
					(ご指名)
				{{/if}}
			</div>
		</div>
		<div style="padding:10px 0px 0px 0px;">
			<div>
				■ご要望、ご連絡
			</div>
			<div style="padding:0px 0px 0px 30px;">
				{{$params.free|nl2br}}
			</div>
		</div>
		<div style="padding:10px 0px 0px 0px;">
			<div>
				■メールアドレス
			</div>
			<div style="padding:0px 0px 0px 30px;">
				{{$params.mail}}
			</div>
		</div>
		<div style="padding:40px 0px 50px 100px;">
			<span>
				<input type="submit" value="戻る" name="back" style="width:80px;height:30px;" />
			</span>
			<span style="padding-left:30px;">
				<input type="submit" value="送信" name="send" style="width:80px;height:30px;" />
			</span>
		</div>
	</form>
</div>