<?php

function now_tp($now, $area){

include('db_con.php');

$stmt = $pdo -> prepare("SELECT  tn.id AS therapist_id, tp.img_url AS img, tn.name_site AS name, tp.age AS age, tp.history AS history, tp.skill_2 AS main_skill, tp.skill AS skill, tp.pr_refle AS pr FROM (SELECT * FROM attendance_new WHERE (year*10000+month*100+day)=:now AND kekkin_flg=0 AND area='tokyo') AS an LEFT JOIN (SELECT * FROM therapist_new WHERE delete_flg=0 AND area='tokyo') AS tn ON an.therapist_id=tn.id LEFT JOIN (SELECT * FROM therapist_page WHERE delete_flg=0 AND area='tokyo') AS tp ON tn.id=tp.therapist_id ORDER BY tp.area,tn.wait_select DESC,tn.rank=4 DESC,tn.rank,tn.order_num DESC,tn.id DESC");

$stmt->bindValue(':now', $now ,PDO::PARAM_INT);
$stmt->bindParam(':area', $area, PDO::PARAM_STR);
$stmt->execute();

$rows = $stmt->fetchAll();

return $rows;

}

$skill_data = array(
    "1"=>"アロマ",
    "2"=>"バリ式",
    "3"=>"セルライト",
    "4"=>"カイロ",
    "5"=>"フェイシャル",
    "6"=>"ヘッド",
    "7"=>"小顔",
    "8"=>"骨盤矯正",
    "9"=>"ロミロミ",
    "10"=>"リンパ",
    "11"=>"マタニティー",
    "12"=>"リフレ",
    "13"=>"整体",
    "14"=>"指圧",
    "15"=>"ストレッチ",
    "16"=>"スウェディッシュ",
    "17"=>"タイ古式",
    "18"=>"強もみ",
    "19"=>"眼精疲労",
    "20"=>"痩身"
);

$time_array = array(

    "1" => array("hour" => 18,"minute" => 0),
    "2" => array("hour" => 18,"minute" => 30),
    "3" => array("hour" => 19,"minute" => 0),
    "4" => array("hour" => 19,"minute" => 30),
    "5" => array("hour" => 20,"minute" => 0),
    "6" => array("hour" => 20,"minute" => 30),
    "7" => array("hour" => 21,"minute" => 0),
    "8" => array("hour" => 21,"minute" => 30),
    "9" => array("hour" => 22,"minute" => 0),
    "10" => array("hour" => 22,"minute" => 30),
    "11" => array("hour" => 23,"minute" => 0),
    "12" => array("hour" => 23,"minute" => 30),
    "13" => array("hour" => 0,"minute" => 0),
    "14" => array("hour" => 0,"minute" => 30),
    "15" => array("hour" => 1,"minute" => 0),
    "16" => array("hour" => 1,"minute" => 30),
    "17" => array("hour" => 2,"minute" => 0),
    "18" => array("hour" => 2,"minute" => 30),
    "19" => array("hour" => 3,"minute" => 0),
    "20" => array("hour" => 3,"minute" => 30),
    "21" => array("hour" => 4,"minute" => 0),
    "22" => array("hour" => 4,"minute" => 30),
    "23" => array("hour" => 5,"minute" => 0),
    "24" => array("hour" => 5,"minute" => 30),
    "25" => array("hour" => 6,"minute" => 0)

);

?>