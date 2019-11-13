
<div style="text-align:center;padding:10px 0px 5px 0px;border-bottom:solid 1px blue;">
	ドライバー：{{$params.staff_name}}さん
</div>

{{include file="top_menu.tpl"}}

<div class="title_bar">
	業務開始・締め処理(確認)
</div>

<form action="" method="post">

<div id="day_report">

<div class="one">
<div class="left_1">
開始：
</div>
<div class="left_2">
{{$params.start_time}}
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
終了：
</div>
<div class="left_2">
{{$params.end_time}}
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
距離：
</div>
<div class="left_2">
{{$params.car_distance}}km
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
高速代：
</div>
<div class="left_2">
{{$params.highway|number_format}}円
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
駐車場代：
</div>
<div class="left_2">
{{$params.parking|number_format}}円
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
精算済み：
</div>
<div class="left_2">
{{$params.pay_finish|number_format}}円
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
特記事項：
</div>
<div class="left_2">
{{$params.comment|nl2br}}
</div>
<br class="clear" />
</div>

<div class="btn_2">

<div class="left_1">
<input type="submit" name="back" value="戻る" style="padding:10px;" />
</div>

<div class="left_2">
<input type="submit" name="send" value="送信" style="padding:10px;" />
</div>

<br class="clear" />

</div>

</div>

</form>



