<?php /* Smarty version 2.6.26, created on 2017-01-31 14:17:42
         compiled from sp/template.tpl */ ?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<title><?php echo $this->_tpl_vars['params']['page_title']; ?>
</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="<?php echo @WWW_URL; ?>
css/sp/style.css" />
<script type="text/javascript" src="<?php echo @WWW_URL; ?>
js/jquery-1.6.2.js"></script>
<script type="text/javascript" src="<?php echo @WWW_URL; ?>
js/main.js"></script>

<meta name="robots" content="noindex,nofollow" />

</head>
<body>
	<div id="wrapper">
		<div><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['content_tpl'], 'smarty_include_vars' => array('params' => $this->_tpl_vars['params'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
		
		<?php if ($this->_tpl_vars['params']['top_flg'] != true): ?>
		<div style="text-align:center;padding:50px 0px 100px 0px;">
			<a href="<?php echo $this->_tpl_vars['params']['top_url']; ?>
">トップページへ戻る</a>
		</div>
		<?php endif; ?>
		
	</div>
</body>
</html>