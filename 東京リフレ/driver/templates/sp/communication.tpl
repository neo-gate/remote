
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<title>{{$params.page_title}}</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="{{$smarty.const.WWW_URL}}css/sp/communication.css" />
<script type="text/javascript" src="{{$smarty.const.WWW_URL}}js/jquery-1.6.2.js"></script>
<script type="text/javascript" src="{{$smarty.const.WWW_URL}}js/main.js"></script>

<meta name="robots" content="noindex,nofollow" />

</head>
<body>
<div id="wrapper">

{{if $params.error != ""}}
<div style="margin:10px 0px 0px 0px;">{{$params.error}}</div>
{{/if}}

<div class="title_bar">
	<a href="{{$params.url_communication}}">送迎情報</a>　　<a href="{{$params.url_history}}">通信履歴</a>
</div>

<div id="driver_communication">

{{if $params.message.content != ""}}

<div class="top">

<form action="" method="post">

<input type="hidden" name="staff_id" value="{{$params.staff_id}}" />
<input type="hidden" name="ch" value="{{$params.ch}}" />
<input type="hidden" name="area" value="{{$params.area}}" />
<input type="hidden" name="message_id" value="{{$params.message.id}}" />

<div class="content_1">
<div class="left_1">
指示者：
</div>
<div class="left_2">
本部
</div>
<br class="clear" />
</div>

<div class="content_2">
{{$params.message.content|mb_strimwidth:0:160:"...":'UTF-8'}}
</div>

<div class="content_3">
<div class="left_1">
<input type="submit" name="send_top_message_1" value="了解" class="driver_communication_btn" />
</div>
<div class="left_2">
<input type="submit" name="send_top_message_2" value="コメント" class="driver_communication_btn" />
</div>
<br class="clear" />
</div>

</form>

</div>

{{/if}}

<div class="content">
{{section name=cnt loop=$params.board_data}}

<form action="" method="post">

<input type="hidden" name="reservation_for_board_id" value="{{$params.board_data[cnt].id}}" />

<div class="one">

{{if $params.board_data[cnt].data_type == "okuri"}}

<div class="content_1">

<div class="left_1">
【送り】
</div>

<div class="left_2">
{{$params.board_data[cnt].therapist_name}}
</div>

<div class="left_3">
{{$params.board_data[cnt].start_hour}}:{{$params.board_data[cnt].start_minute}}
</div>

<div class="left_4">
開始予定
</div>

<br class="clear" />

</div>

<div class="content_2">
<input type="submit" name="send_okuri_1" value="確認" class="driver_communication_btn" />
</div>

<div class="content_3">

<div class="left_1">
出発　{{$params.board_data[cnt].select_frm_okuri_hour}}　{{$params.board_data[cnt].select_frm_okuri_minute}}
</div>

<div class="left_2">
<input type="submit" name="send_okuri_2" value="着予定" class="driver_communication_btn" />
</div>

<br class="clear" />

</div>

<div class="content_4">

<div class="left_1">
<input type="submit" name="send_okuri_3" value="到着待機" class="driver_communication_btn" />
</div>

<div class="left_2">
<input type="submit" name="send_okuri_4" value="降車" class="driver_communication_btn" />
</div>

<br class="clear" />

</div>

<div class="content_5">
コメント
</div>

<div class="content_6">
<textarea name="comment" style="width:260px;height:60px;">{{$params.board_data[cnt].okuri_driver_comment}}</textarea>
</div>

<div class="content_7">
<input type="submit" name="send_okuri_5" value="送信" class="driver_communication_btn" />
</div>

<hr />

{{elseif $params.board_data[cnt].data_type == "mukae"}}

<div class="content_1">

<div class="left_1">
【迎え】
</div>

<div class="left_2">
{{$params.board_data[cnt].therapist_name}}
</div>

<div class="left_3">
{{$params.board_data[cnt].end_hour}}:{{$params.board_data[cnt].end_minute}}
</div>

<div class="left_4">
終了予定
</div>

<br class="clear" />

</div>

<div class="content_2">
<input type="submit" name="send_mukae_1" value="確認" class="driver_communication_btn" />
</div>

<div class="content_3">

<div class="left_1">
到着　{{$params.board_data[cnt].select_frm_mukae_hour}}　{{$params.board_data[cnt].select_frm_mukae_minute}}
</div>

<div class="left_2">
<input type="submit" name="send_mukae_2" value="着予定" class="driver_communication_btn" />
</div>

<br class="clear" />

</div>

<div class="content_4">

<div class="left_3">
<input type="submit" name="send_mukae_3" value="到着待機" class="driver_communication_btn" />
</div>

<div class="left_4">
<input type="submit" name="send_mukae_4" value="合流" class="driver_communication_btn" />
</div>

<div class="left_5">
<input type="submit" name="send_mukae_5" value="降車" class="driver_communication_btn" />
</div>

<br class="clear" />

</div>

<div class="content_5">
コメント
</div>

<div class="content_6">
<textarea name="comment" style="width:260px;height:60px;">{{$params.board_data[cnt].mukae_driver_comment}}</textarea>
</div>

<div class="content_7">
<input type="submit" name="send_mukae_6" value="送信" class="driver_communication_btn" />
</div>

<hr />

{{/if}}

</div>

</form>

{{/section}}
</div>

<form action="" method="post">
<input type="hidden" name="attendance_staff_new_id" value="{{$params.attendance_staff_new_id}}" />

<div class="bottom">

<div class="content_1">【事務所戻り】</div>

{{if $params.attendance_staff_new_id != ""}}

<div class="content_2">

<div class="left_1">{{$params.select_frm_back_plans_state}}</div>

<div class="left_2">{{$params.select_frm_back_plans_hour}}</div>

<div class="left_3">{{$params.select_frm_back_plans_minute}}</div>

<div class="left_4">
<input type="submit" name="send_plans" value="戻り予定" class="driver_communication_btn" />
</div>

<br class="clear" />

</div>

<div class="content_3">

{{if $params.arrival_flg == true}}
<input type="submit" name="send_arrival" value="到着" disabled="disabled" class="driver_communication_btn" />
{{else}}
<input type="submit" name="send_arrival" value="到着" class="driver_communication_btn" />
{{/if}}

</div>

{{else}}
<div>
本日は出勤なし
</div>
{{/if}}

</div>

</form>

</div>



</div>
</body>
</html>

