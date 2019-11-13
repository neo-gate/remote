<?php

require_once(ROOT_PATH."Smarty2/Smarty.class.php");
$smarty = new Smarty();
$smarty->template_dir = ROOT_PATH."templates";
$smarty->compile_dir = ROOT_PATH."templates_c";

$smarty->left_delimiter = '{{';
$smarty->right_delimiter = '}}';

?>