
<div>
<a name="page_top"></a>
</div>

<div style="text-align:center;padding:10px 0px 5px 0px;border-bottom:solid 1px blue;">
	ドライバー：{{$params.staff_name}}さん
</div>

{{include file="top_menu_beta_test.tpl"}}

{{section name=cnt loop=$params.help_page_data}}
<div>
<a name="page_{{$params.help_page_data[cnt].id}}"></a>
</div>
<div>
<div class="title_bar">
{{$params.help_page_data[cnt].created|date_format:"%m/%d"}}　{{$params.help_page_data[cnt].title}}
</div>
<div style="padding:0px 20px 0px 20px;line-height:160%;">
{{$params.help_page_data[cnt].content|nl2br}}
</div>
</div>
{{sectionelse}}
<div style="text-align:center;padding-top:30px;">伝言はありません。</div>
{{/section}}

<div style="text-align:right;padding:20px 0px 0px 0px;">
<a href="#page_top">
一番上へ
</a>
</div>
