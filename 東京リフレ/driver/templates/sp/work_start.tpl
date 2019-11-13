
<form action="" enctype="multipart/form-data" method="post" id="edit_frm">

<input type="hidden" name="attendance_staff_new_id" value="{{$params.attendance_staff_new_id}}" />

<div id="top_name_disp">
	{{$params.staff_name}}さん
</div>

{{include file="top_menu_beta.tpl"}}

<div id="page_work_start">

<div class="title_bar">
<img src="{{$params.url_root_site}}img/driver/title_bar_1.gif" width="160" />
</div>

{{if $params.attendance_staff_new_id == ""}}

<div class="no_attendance">
本日は出勤日ではありません。
</div>

{{else}}

<div id="mail_content">

{{if $params.error != ""}}
<div class="error">
{{$params.error}}
</div>
{{/if}}

<div class="content_1">
業務開始
</div>

<div class="content_2">
<div class="left_1">
開始時メーター
</div>
<div class="left_2">
<input type="text" name="meter" value="{{$params.meter}}" style="width:120px;" />&nbsp;km
</div>
<br class="clear" />
</div>

<div class="content_3">
写真を添付
</div>

<div class="content_5">
<input type="file" name="pic" />
</div>

<div class="content_4">
<div class="left_1">
<img src="{{$params.url_root_site}}img/driver/btn_2.gif" width="120" class="btn_image" onclick="submit_work_start_send_back();" />
</div>
<div class="left_2">
<img src="{{$params.url_root_site}}img/driver/btn_1.gif" width="120" class="btn_image" onclick="submit_work_start_send();" />
</div>
<br class="clear" />
</div>

</div>

{{/if}}

</div>

</form>

