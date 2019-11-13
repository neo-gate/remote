
<div style="text-align:center;padding:10px 0px 5px 0px;border-bottom:solid 1px blue;">
	ドライバー：{{$params.staff_name}}さん
</div>

<div class="title_bar">
	<a href="{{$params.url_communication}}">送迎情報</a>　　<a href="{{$params.url_history}}">通信履歴</a>
</div>

<div id="driver_history">

<div class="content_1">
{{section name=cnt loop=$params.list_top}}

{{if $params.list_top[cnt].type == "1"}}
<div class="one_2">
{{else}}
<div class="one">
{{/if}}

<div class="left_1">
{{$params.list_top[cnt].created|date_format:"%m/%d %H:%M"}}
</div>
<div class="left_2">
{{$params.list_top[cnt].staff_name_small}}
</div>
<div class="left_3">
{{$params.list_top[cnt].content}}
</div>
<br class="clear" />

{{if $params.list_top[cnt].type == "1"}}
</div>
{{else}}
</div>
{{/if}}

{{/section}}
</div>

<div class="content_form">
<form action="" method="post">
<div>{{$params.error}}</div>
<div class="text_a">
<textarea name="content" style="width:280px;height:100px;"></textarea>
</div>
<div class="btn">
<input type="submit" name="send" value="送信" style="padding:5px 10px 5px 10px;" />
</div>
</form>
</div>

<div class="content_1">
{{section name=cnt loop=$params.list_bottom}}

{{if $params.list_bottom[cnt].type == "1"}}
<div class="one_2">
{{else}}
<div class="one">
{{/if}}

<div class="left_1">
{{$params.list_bottom[cnt].created|date_format:"%m/%d %H:%M"}}
</div>
<div class="left_2">
{{$params.list_bottom[cnt].staff_name_small}}
</div>
<div class="left_3">
{{$params.list_bottom[cnt].content}}
</div>
<br class="clear" />

{{if $params.list_bottom[cnt].type == "1"}}
</div>
{{else}}
</div>
{{/if}}

{{/section}}
</div>

</div>


