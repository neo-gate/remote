
<div style="text-align:center;padding-top:10px;">
ドライバー：{{$params.staff_name}}さん
</div>

<div style="padding:20px 0px 0px 0px;">
	<div style="float:left;">
		{{$params.page_title}}
	</div>
	<div style="float:right;padding:0px 0px 0px 0px;">
		{{include file="to_top_2.tpl"}}
	</div>
	<br class="clear" />
</div>

{{if $params.error != ""}}
<div style="margin:10px 0px 0px 0px;">{{$params.error}}</div>
{{/if}}

<form action="" method="post" enctype="multipart/form-data">

<input type="hidden" name="area" value="{{$params.area}}" />
<input type="hidden" name="staff_id" value="{{$params.staff_id}}" />
<input type="hidden" name="ch" value="{{$params.ch}}" />
<input type="hidden" name="car_image_url" value="{{$params.car_image_url}}" />
<input type="hidden" name="etc_number" value="{{$params.etc_number}}" />

<div id="driver_edit">

<div class="one">
<div class="left_1">
車種
</div>
<div class="left_2">
<input type="text" name="car_type" value="{{$params.car_type}}" />
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
色
</div>
<div class="left_2">
<input type="text" name="car_color" value="{{$params.car_color}}" />
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
ナンバー
</div>
<div class="left_2">
<input type="text" name="car_number" value="{{$params.car_number}}" />
</div>
<br class="clear" />
</div>

<div class="one">

<div class="left_1">
車の画像
</div>

<div class="left_2">

<div>
<input type="file" name="pic" />
</div>

{{if $params.car_image_url != ""}}
<div style="margin:5px 0px 0px 0px;">
<img src="{{$smarty.const.S3_URL}}{{$params.car_image_url}}" width="120" />
</div>
{{/if}}

</div>

<br class="clear" />

</div>

<div class="one">
<div class="left_1">
携帯番号
</div>
<div class="left_2">
<input type="text" name="tel" value="{{$params.tel}}" />
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
ETCカード
</div>
<div class="left_2">
{{$params.etc_number}}
</div>
<br class="clear" />
</div>

<div class="btn">
<input type="submit" name="send" value="確認" style="padding:10px;" />
</div>

</div>

</form>

