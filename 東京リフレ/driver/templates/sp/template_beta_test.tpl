
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<title>{{$params.page_title}}</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="{{$smarty.const.WWW_URL}}css/sp/style_beta.css" />
<script type="text/javascript" src="{{$smarty.const.WWW_URL}}js/jquery-1.6.2.js"></script>
<script type="text/javascript" src="{{$smarty.const.WWW_URL}}js/main.js"></script>

<meta name="robots" content="noindex,nofollow" />

</head>
<body>
	<div id="wrapper">
	
		{{include file=$content_tpl params=$params}}
		
		<div id="footer">
Copyright(C)&nbsp;出張マッサージ東京リフレ<br />
All&nbsp;Rights&nbsp;Reserved.
		</div>
		
	</div>
</body>
</html>
