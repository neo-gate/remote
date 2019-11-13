
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>{{$params.page_title}}</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" type="text/css" href="{{$smarty.const.WWW_URL}}css/sp/style.css" />
<script type="text/javascript" src="{{$smarty.const.WWW_URL}}js/script.js"></script>
</head>
<body>
	<div id="wrapper">
		{{include file=$content_tpl params=$params}}
	</div>
</body>
</html>
