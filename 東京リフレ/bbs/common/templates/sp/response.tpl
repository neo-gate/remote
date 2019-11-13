
<div style="padding:20px 0px 20px 0px;">
<a href="index.php">トップ</a>　
{{$params.page_title}}
</div>

<div style="color:red;">{{$params.error}}</div>
<div>
<form action="" method="post">
<input type="hidden" name="area" value="{{$params.area}}" />
<input type="hidden" name="to_staff_id" value="{{$params.to_staff_id}}" />
<input type="hidden" name="to_therapist_id" value="{{$params.to_therapist_id}}" />
<div style="text-align:center">
<textarea name="naiyou" style="width:260px;height:200px;">{{$params.naiyou}}</textarea>
</div>
<div style="margin:30px 0px 50px 0px;text-align:center;">
<input type="submit" name="send" value="確認" style="padding:5px;" />
</div>
</form>
</div>
