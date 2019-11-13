
var url_root = "http://"+location.hostname+"/";

	$(function () {
		
		// アップロード年月を変更すると非同期でアップロードファイルの内容が変化する処理
		$("#uploaded_time").live("change",function(){
			
			$.ajax({
				type:'post',
				url:url_root+'ajax/upload/uploaded_file_select_form.php',
				data:{
					'folder_name':$("#uploaded_time").val()
				},
				success:function(data){
					$("#uploaded_file").html(data);
				}
			});
		});
		
		// アップロードファイルを変更すると非同期で表示画像の内容が変化する処理
		$("#select_uploaded_file").live("change",function(){
			
			$.ajax({
				type:'post',
				url:url_root+'ajax/upload/uploaded_file_display.php',
				data:{
					'file_name':$("#select_uploaded_file").val()
				},
				success:function(data){
					$("#image_display").html(data);
					
					$.ajax({
						type:'post',
						url:url_root+'ajax/upload/image_delete_btn.php',
						data:{
						},
						success:function(data){
							
							$("#image_delete_btn").html(data);
							
						}
					});
				}
			});
		});
		
	});
	
	
	function uploadFile() {
		
		$("#action_result").html("");
		var pic_val = $('#pic').val();
		
		if(pic_val != ""){
	        if(window.confirm('画像UPしてよろしいですか？')){
	        	$('#pic').upload(
	        		url_root+'ajax/upload/upload.php', 
	                {type:'0'},
	                function (res) {
	                    
	                    $('#pic').val('');
	                    
	                    if(res=="extension_error"){
	                    	
	                    	$("#action_result").html('<div style="color:red;padding:5px 0px 0px 5px;">ファイルの拡張子が不正です。</div>');
	                    	
	                    }else{
	                    	
	                    	if(res=="success"){
	                    		
		                    	$("#action_result").html('<div style="color:blue;padding:5px 0px 0px 5px;">画像UP完了</div>');
		                    	
	                    	}else{
	                    		
	                    		$("#action_result").html('<div style="color:red;padding:5px 0px 0px 5px;">画像UP失敗</div>');
	                    		
	                    	}
	                    	
	                    	pic_form1_reset();
		                    pic_form2_reset();
	                    	now_uploaded_file_display();
	                    	
	                    }
	                    
	                },
	                'text'
	            );
			}else{
				$('#pic').val('');
			}
		}
		
    }
	
	function now_uploaded_file_display(){
		$.ajax({
			type:'post',
			url:url_root+'ajax/upload/now_uploaded_file_display.php',
			data:{
			},
			success:function(data){
				$("#image_display").html(data);
				
				$.ajax({
					type:'post',
					url:url_root+'ajax/upload/image_delete_btn.php',
					data:{
					},
					success:function(data){
						
						$("#image_delete_btn").html(data);
						
					}
				});
			}
		});
	}
	
	function pic_form1_reset(){
		
		$.ajax({
			type:'post',
			url:url_root+'ajax/upload/pic_form1_reset.php',
			data:{
			},
			success:function(data){
				
				$("#uploaded_time_area").html(data);
				
			}
		});
	}
	
	function pic_form2_reset(){
		
		$.ajax({
			type:'post',
			url:url_root+'ajax/upload/pic_form2_reset.php',
			data:{
			},
			success:function(data){
				$("#uploaded_file").html(data);
			}
		});
	}
	
	function pic_form1_delete_reset(){
		
		$.ajax({
			type:'post',
			url:url_root+'ajax/upload/pic_form1_delete_reset.php',
			data:{
			},
			success:function(data){
				$("#uploaded_time_area").html(data);
			}
		});
	}
	
	function pic_form2_delete_reset(){
		$.ajax({
			type:'post',
			url:url_root+'ajax/upload/pic_form2_delete_reset.php',
			data:{
			},
			success:function(data){
				$("#uploaded_file").html(data);
			}
		});
	}
	
	function deleteFile(x) {
		
		$("#action_result").html("");
		if(x==""){
			return false;
		}else{
			if(window.confirm('以下を削除してよろしいですか？\n'+x)){
				$.ajax({
					type:'post',
					url:url_root+'ajax/upload/image_delete.php',
					data:{
					},
					success:function(data){
						
						$("#action_result").html(data);
						
						$("#image_display").html("");
						
						$.ajax({
							type:'post',
							url:url_root+'ajax/upload/image_delete_btn.php',
							data:{
							},
							success:function(data){
								
								$("#image_delete_btn").html(data);
								
							}
						});
						
						pic_form1_delete_reset();
	                    pic_form2_delete_reset();
					}
				});
			}else{
				return false;
			}
		}
    }
	
	function brand_big_preview(){
		
		var naiyou = document.brand_big_frm.naiyou.value;
		
		$("#preview_area").html(naiyou);
		
		exit;
		
	}
	
	
