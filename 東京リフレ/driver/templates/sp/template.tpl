
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<title>{{$params.page_title}}</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="{{$smarty.const.WWW_URL}}css/sp/style.css" />
<script type="text/javascript" src="{{$smarty.const.WWW_URL}}js/jquery-1.6.2.js"></script>
<script type="text/javascript" src="{{$smarty.const.WWW_URL}}js/main.js"></script>

<meta name="robots" content="noindex,nofollow" />

</head>
<body>
	<div id="wrapper">
		<div>{{include file=$content_tpl params=$params}}</div>
		
		{{if $params.top_flg != true}}
		<div style="text-align:center;padding:50px 0px 100px 0px;">
			<a href="{{$params.top_url}}">トップページへ戻る</a>
		</div>
		{{/if}}
		
	</div>
</body>
</html>
