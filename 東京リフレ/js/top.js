var url_root = "http://"+location.hostname+"/";

$(function() {
	
	// オプションの一括指定
	var options = {
		imglist: [
			"img/photo/0.png",
			"img/photo/1.png",
			"img/photo/2.png",
			"img/photo/3.png",
		],
		width: 1200,
		height: 896,
		minWidth: 600,
		minHeight: 400,
		callback: function() {
			var self = this;
			// ローディング表示を終了
			$("#loading").hide();
			// 末尾の要素をフェードイン
			self.find("img").eq(-1).fadeIn(function() {
				// フェード切り替え開始
				self.fadechanger({ selector: "img" });
			});
		}
	};
	
	// loader
	$("#imgContainer").imgloader(options);
	
	wait_time_update();
	
});

function wait_time_update(){
	
	$.ajax({
		type:'post',
		url:url_root+'ajax/wait_time.php',
		data:{
			'type':'pc'
		},
		success:function(data){
			
			$("#top_wait_time").html(data);
			
		}
	});
	
}

