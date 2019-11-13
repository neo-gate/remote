<?php

define('DB_HOST', 'refle-mysql.ciocmiv88pzc.ap-northeast-1.rds.amazonaws.com');
define('DB_USER', 'refle');
define('DB_PASSWORD', 'qfmpgxehf');
define('DB_NAME', 'refle');

error_reporting(E_ALL & ~E_NOTICE);

try {
    $pdo = new PDO('mysql:host='.DB_HOST.';charset=utf8;dbname='.DB_NAME,DB_USER,DB_PASSWORD,array(PDO::ATTR_EMULATE_PREPARES => false));
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}

?>