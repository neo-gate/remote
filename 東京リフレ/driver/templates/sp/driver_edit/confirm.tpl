
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


<form action="" method="post">

<input type="hidden" name="area" value="{{$params.area}}" />
<input type="hidden" name="staff_id" value="{{$params.staff_id}}" />
<input type="hidden" name="ch" value="{{$params.ch}}" />
<input type="hidden" name="car_type" value="{{$params.car_type}}" />
<input type="hidden" name="car_color" value="{{$params.car_color}}" />
<input type="hidden" name="car_number" value="{{$params.car_number}}" />
<input type="hidden" name="car_image_url" value="{{$params.car_image_url}}" />

<div id="driver_edit">

<div class="one">
<div class="left_1">
車種
</div>
<div class="left_2">
{{$params.car_type}}
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
色
</div>
<div class="left_2">
{{$params.car_color}}
</div>
<br class="clear" />
</div>

<div class="one">
<div class="left_1">
ナンバー
</div>
<div class="left_2">
{{$params.car_number}}
</div>
<br class="clear" />
</div>

{{if $params.car_image_url != ""}}
<div class="one">
<div class="left_1">
車の画像
</div>
<div class="left_2">
<img src="{{$smarty.const.S3_URL}}{{$params.car_image_url}}" width="120" />
</div>
<br class="clear" />
</div>
{{/if}}

<div class="one">
<div class="left_1">
携帯番号
</div>
<div class="left_2">
{{$params.tel}}
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

