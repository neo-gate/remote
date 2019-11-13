
<div style="text-align:center;padding:10px 0px 5px 0px;border-bottom:solid 1px blue;">
	ドライバー：{{$params.staff_name}}さん
</div>

{{include file="top_menu.tpl"}}

<div class="title_bar">
	業務開始・締め処理(入力)
</div>

{{if $params.attendance_staff_new_id == ""}}

本日は出勤日ではありません。

{{else}}

<form action="" method="post">

<input type="hidden" name="attendance_staff_new_id" value="{{$params.attendance_staff_new_id}}" />

{{if $params.error != ""}}
<div style="color:red;margin:0px 0px 10px 0px;">{{$params.error}}</div>
{{/if}}

<div id="day_report">

<div class="one">
<div class="left_1">
開始：
</div>
<div class="left_2">
{{$params.select_frm_work_time_start}}
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
終了：
</div>
<div class="left_2">
{{$params.select_frm_work_time_end}}
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
距離：
</div>
<div class="left_2">
<input type="text" name="car_distance" value="{{$params.car_distance}}" />
km
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
高速代：
</div>
<div class="left_2">
<input type="text" name="highway" value="{{$params.highway}}" />
円
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
駐車場代：
</div>
<div class="left_2">
<input type="text" name="parking" value="{{$params.parking}}" />
円
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
精算済み：
</div>
<div class="left_2">
<input type="text" name="pay_finish" value="{{$params.pay_finish}}" />
円
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
特記事項：
</div>
<div class="left_2">
<textarea name="comment" style="width:200px;height:80px;">{{$params.comment}}</textarea>
</div>
<br class="clear" />
</div>

<div class="btn">
<input type="submit" name="send" value="確認" style="padding:10px;" />
</div>

</div>

</form>

{{/if}}

