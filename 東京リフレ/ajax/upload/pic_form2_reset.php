<?php

	session_start();
	header("Content-type: text/html; charset=UTF-8");
	
	
	
	$document_root = $_SERVER['DOCUMENT_ROOT'];
	define("ROOT_PATH", $document_root."/");
	
	$domain = $_SERVER["SERVER_NAME"];
	$url_root = "http://".$domain."/";
	define("WWW_URL", $url_root);
	
	
	
	// 現在日時のフォルダ名を代入
	$folder_name = $_SESSION["year_month"];
	
	$last_uploaded_file = $_SESSION["last_uploaded_file"];
	
	$uploaded_files = array();
	
	//ディレクトリ・ハンドルをオープン
	$res_dir = opendir( ROOT_PATH.'/img/upload/'.$folder_name );
	
	$file_num = 0;
		
	//ディレクトリ内のファイル名を１つずつを取得
	while( $file_name = readdir( $res_dir ) ){
		//取得したファイル名を表示
		if ($file_name != "." && $file_name != "..") {
				
			$uploaded_files[$file_num] = $file_name;
				
			$file_num++;
			
		}
	}
	
	//ディレクトリ・ハンドルをクローズ
	closedir( $res_dir );
	
	$array_num = count($uploaded_files);
	
	// 以下、セレクトフォームを作成して変数に代入
	$html  = '<select name="uploaded_file" id="select_uploaded_file">';
	$html .= '<option value="-1" selected>ファイル選択</option>';
	for($i=0;$i<$array_num;$i++){
		
		if($last_uploaded_file == $uploaded_files[$i]){
			$html .= '<option value="'.$folder_name.'/'.$uploaded_files[$i].'" selected>'.$uploaded_files[$i].'</option>';
		}else{
			$html .= '<option value="'.$folder_name.'/'.$uploaded_files[$i].'">'.$uploaded_files[$i].'</option>';
		}
	}
	$html .= '</select>';
	
	// セレクトフォームが代入された変数を出力
	echo $html;
	exit();

?>