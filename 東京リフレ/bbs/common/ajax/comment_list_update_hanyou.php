<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/bbs/common/");
include(ROOT_PATH."include/define.php");



$data_id = $_POST["data_id"];

$thread_type = $_SESSION[THREAD_TYPE_AREA];

$thread_type_name = "";

$thread_type_name = SITE_NAME."・セラピストBBS";

//スレッドのデータを取得(汎用)
$thread_data = get_thread_data_hanyou($data_id,$thread_type);
$thread_data_num = count($thread_data);

$html = "";

$html = "<div style='padding:5px 0px 5px 0px;font-size:12px;'>更新時間：".get_seikei_time2(time())."</div>";

$html .= "<hr />";

for($i=0;$i<$thread_data_num;$i++){
	
	$thread_id = $thread_data[$i]["th_id"];
	$to_staff_id = $thread_data[$i]["to_staff_id"];
	$to_staff_name = get_staff_name($to_staff_id);
	$comment = $thread_data[$i]["comment"];
	$comment_num = $thread_data[$i]["comment_num"];
	
	for($j=0;$j<$comment_num;$j++){
		
		$created = $comment[$j]["created"];
		$staff_id = $comment[$j]["staff_id"];
		$staff_type = $comment[$j]["staff_type"];
		$content = $comment[$j]["content"];
		$time = get_seikei_time($created);
		$name = get_staff_name($staff_id);
		
		if($staff_type=="3"){
			
			$content = $to_staff_name."さん<br />".$content;
			$content = "<span style='color:red;'>".$content."</span>";
			
			$name = "■".$name;
			
		}else{
			
			$name = "□".$name;
			
		}
		
		$html .= "<div class='time'>".$time."</div>";
		$html .= "<div class='name'>".$name."</div>";
		$html .= "<div class='content'>".$content."</div>";
		
		
		if( ( $_SESSION[SESSION_STAFF_ID] == $to_staff_id ) || ( $_SESSION[SESSION_STAFF_TYPE] == "3" ) ){

			if( $thread_type == "1" ){
			
				$html .= '<div style="line-height:160%;font-size:12px;">';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'">返信する</a>　｜　';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=1">了解です</a>　｜　';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=2">現着です</a><br />';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=3">スタートします</a>　｜　';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=4">終了しました</a>　｜　';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=5">事務所着です</a>';
				$html .= '</div>';
			
			}else if( $thread_type == "2" ){
			
				$html .= '<div style="line-height:160%;font-size:12px;">';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'">返信する</a>　｜　';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=1">了解です</a>　｜　';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=2">現着です</a><br />';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=3">合流しました</a>　｜　';
				$html .= '<a href="reply.php?thread_id='.$thread_id.'&messe=4">事務所着です</a>';
				$html .= '</div>';
			
			}
		

		}
	
	}
	$html .= "<br />";
	$html .= "<hr />";
	
}

echo $html;
exit();

?>