
<form action="" method="post">
	<div style="text-align:center;padding-top:30px;font-weight:bold;">業務連絡BBS(ログイン)</div>
	<div style="text-align:center;padding-top:30px;">電話番号を入力</div>
	<div style="color:red;">{{$params.error}}</div>
	<div style="text-align:center;padding-top:10px;">
		<input type="text" name="tel" value="" style="width:200px;"/>
	</div>
	<div style="text-align:center;padding:20px 0px 100px 0px;">
		<input type="submit" name="send" value="ログイン" style="padding:5px;" />
	</div>
</form>
