<?php
//直接のページ遷移を阻止
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;
//DBへの接続
include('db_con.php');

$day = $_POST['day'];
$day = str_replace('/', '', $day);
$time = $_POST['time'];
$course = $_POST['course'];

if(($day)&&($time == '-1')&&($course = '-1')){
    $stmt = $pdo -> prepare("SELECT tn.id AS therapist_id, tn.name_site AS name FROM (SELECT * FROM attendance_new WHERE (year*10000+month*100+day)=:day AND kekkin_flg=0 AND area='tokyo') AS an LEFT JOIN (SELECT * FROM therapist_new WHERE delete_flg=0 AND area='tokyo') AS tn ON an.therapist_id=tn.id LEFT JOIN (SELECT * FROM therapist_page WHERE delete_flg=0 AND area='tokyo') AS tp ON tn.id=tp.therapist_id ORDER BY tp.area,tn.wait_select DESC,tn.rank=4 DESC,tn.rank,tn.order_num DESC,tn.id DESC");
}elseif(($day)&&($time)&&($course == '-1')){
    $stmt = $pdo -> prepare("SELECT tn.id AS therapist_id, tn.name_site AS name FROM (SELECT * FROM attendance_new WHERE (year*10000+month*100+day)=:day AND :time BETWEEN start_time AND end_time AND kekkin_flg=0 AND area='tokyo') AS an LEFT JOIN (SELECT * FROM therapist_new WHERE delete_flg=0 AND area='tokyo') AS tn ON an.therapist_id=tn.id LEFT JOIN (SELECT * FROM therapist_page WHERE delete_flg=0 AND area='tokyo') AS tp ON tn.id=tp.therapist_id ORDER BY tp.area,tn.wait_select DESC,tn.rank=4 DESC,tn.rank,tn.order_num DESC,tn.id DESC");
}elseif(($day)&&($time)&&($course)){
    $stmt = $pdo -> prepare("SELECT tn.id AS therapist_id, tn.name_site AS name FROM (SELECT * FROM attendance_new WHERE (year*10000+month*100+day)=:day AND :time BETWEEN start_time AND end_time AND kekkin_flg=0 AND area='tokyo') AS an LEFT JOIN (SELECT * FROM therapist_new WHERE delete_flg=0 AND area='tokyo') AS tn ON an.therapist_id=tn.id LEFT JOIN (SELECT * FROM therapist_page WHERE delete_flg=0 AND area='tokyo') AS tp ON tn.id=tp.therapist_id ORDER BY tp.area,tn.wait_select DESC,tn.rank=4 DESC,tn.rank,tn.order_num DESC,tn.id DESC");
}

$stmt->bindValue(':day', $day ,PDO::PARAM_INT);
$stmt->bindValue(':time', $time ,PDO::PARAM_INT);
$stmt->bindValue(':course', $course ,PDO::PARAM_INT);
$stmt->bindParam(':area', $area, PDO::PARAM_STR);
$stmt->execute();

//抽出された値を $therapist_list配列 に格納
$therapist_list = array("セラピスト指名なし");
while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
$therapist_list[]= $row['name'];
}
header('Content-Type: application/json');
//json形式で index.php へバックする
echo json_encode($therapist_list);
?>