var url_root = "http://"+location.hostname+"/";

$(function() {
	
	wait_time_update();
	
});

function wait_time_update(){
	
	$.ajax({
		type:'post',
		url:url_root+'ajax/wait_time.php',
		data:{
			'type':'sp'
		},
		success:function(data){
			
			$("#top_wait_time").html(data);
			
		}
	});
	
}

