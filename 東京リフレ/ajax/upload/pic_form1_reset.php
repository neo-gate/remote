<?php

session_start();
header("Content-type: text/html; charset=UTF-8");



$document_root = $_SERVER['DOCUMENT_ROOT'];
define("ROOT_PATH", $document_root."/");

$domain = $_SERVER["SERVER_NAME"];
$url_root = "http://".$domain."/";
define("WWW_URL", $url_root);



$now_year_month = $_SESSION["year_month"];

$upload_files = array();

//ディレクトリ・ハンドルをオープン
$res_dir1 = opendir( ROOT_PATH.'img/upload' );

$folder_num = 0;

$num = 0;

$flag = false;

//ディレクトリ内のフォルダ名を１つずつを取得
while( $folder_name = readdir( $res_dir1 ) ){
	if ($folder_name != "." && $folder_name != "..") {
			
		$folder_num++;
			
		$year = substr($folder_name,0,4)."年";
		$month = substr($folder_name,4,2)."月";
			
		$year_month = $year.$month;
			
		$upload_files[$num]=array();
			
		//ディレクトリ・ハンドルをオープン
		$res_dir2 = opendir( ROOT_PATH.'img/upload/'.$folder_name );
			
		$file_num = 0;
			
		//ディレクトリ内のファイル名を１つずつを取得
		while( $file_name = readdir( $res_dir2 ) ){
			//取得したファイル名を表示
			if ($file_name != "." && $file_name != "..") {
					
				$upload_files[$num][$file_num]=$file_name;
					
				$file_num++;
					
				$flag=true;
			}
		}
			
		$upload_files[$num]['num'] = $file_num;
			
		$upload_files[$num]['time'] = $year_month;
			
		$upload_files[$num]['folder_name'] = $folder_name;
			
		//ディレクトリ・ハンドルをクローズ
		closedir( $res_dir2 );
			
	}

	if($flag == true){
		$num++;
		$flag = false;
	}

}

//ディレクトリ・ハンドルをクローズ
closedir( $res_dir1 );


$array_num = count($upload_files);

			
echo '<select name="uploaded_time" id="uploaded_time">';

echo '<option value="-1">年月選択</option>';

for($i=($array_num-1);$i>=0;$i--){
	
	
	if($now_year_month == $upload_files[$i]["folder_name"]){
		echo '<option value="'.$upload_files[$i]["folder_name"].'" selected>'.$upload_files[$i]["time"].'('.$upload_files[$i]["num"].')</option>';
	}else{
		echo '<option value="'.$upload_files[$i]["folder_name"].'">'.$upload_files[$i]["time"].'('.$upload_files[$i]["num"].')</option>';
	}
}

echo '</select>';
exit();

?>