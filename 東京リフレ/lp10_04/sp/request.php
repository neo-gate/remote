<?php
    header('Content-type: text/plain; charset= UTF-8');
    if(isset($_POST['name']) && isset($_POST['age'])){

        include('../module/common/function.php');

        $img = $_POST['img'];
        $name = $_POST['name'];
        $age = $_POST['age'];
        $history = $_POST['history'];
        $main_skills = explode(",",$_POST['main_skill']);
        $skills = explode(",",$_POST['skill']);
        $pr = $_POST['pr'];

        $str = "<div class='result_box_1 clearfix'>";
        $str .= "<img src='http://s3-refle.s3-website-ap-northeast-1.amazonaws.com/$img' alt='セラピストアイコン'>";
        $str .= "<p>$name<span>($age)</span></p>";
        $str .= "<p>セラピスト歴$history</p>";
        $str .= "</div>";
        $str .= "<div class='result_box_2 clearfix'>";
        $str .= "<div class='menu_box_2 clearfix'>";
        $str .= "<p>一押しメニュー</p>";
        $i = 0;
        foreach ($main_skills as $main_skill) {
            $main_skill_data[$i] = mb_strimwidth( $skill_data[$main_skill], 0, 10, "...", "UTF-8" );
            $str .= "<p class='menu_2'>$main_skill_data[$i]</p>";
            $i++;
        }
        $str .= "</div>";
        $str .= "</div>";
        $str .= "<div class='result_box_3 clearfix'>";
        $str .= "<div class='menu_box_3 clearfix'>";
        $str .= "<p>施術可能メニュー</p>";
        $j = 0;
        foreach ($skills as $skill) {
            $skill_data[$j] = mb_strimwidth( $skill_data[$skill], 0, 10, "...", "UTF-8" );
            $str .= "<p class='menu_3'>$skill_data[$j]</p>";
            $j++;
        }
        $str .= "</div>";
        $str .= "</div>";
        $str .= "<div class='result_box_4 clearfix'>";
        $str .= "<p>$pr</p>";
        $str .= "</div>";

        $result = nl2br($str);
        echo $result;
    }else{
        echo '失敗';
    }
?>