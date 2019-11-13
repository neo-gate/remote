
var url_root = "http://"+location.hostname+"/";

$(function() {
	
	$("#calendar_day").change(function(){
		
		var calendar_day = $("#calendar_day").val();
		var calendar_array = calendar_day.split("_");
		var year = calendar_array[0];
		var month = calendar_array[1];
		var day = calendar_array[2];
		
		location.href=url_root+"vip/therapist_calendar.php?year="+year+"&month="+month+"&day="+day;
		exit;
		
	});
	
	$("#site_logo_right_menu").click(function(){
		
		$('#site_logo_right_menu_list').css("display", "block");
		
	});
	
	$("#site_logo_right_menu_close").click(function(){
		
		$('#site_logo_right_menu_list').css("display", "none");
		
	});
	
	
	
var logbox = $('#logbox');
var target = $('#calendar_display_content');
target.exScrollEvent(function(evt , param){
var txt='';

var p_top = param.scroll.top;
var p_left = param.scroll.left;

var therapist_name_top;
var nichiji_left;

//X方向にスクロール？
if(param.scrollX){
txt += 'X ';

nichiji_left = ((p_left)*-1)+70;

$('#gyou_nichiji').css("left", nichiji_left+"px");

}
//Y方向にスクロール？
if(param.scrollY){
txt += 'Y ';

therapist_name_top = ((p_top)*-1)+30;

$('#calendar_display_therapist_name').css("top", therapist_name_top+"px");

}

//スクロール開始
if(param.status == 1){
txt += '<span style="color:pink">scroll start</span>';
}
//スクロール終了
if(param.status == 0){
txt += '<span style="color:pink">scroll end</span>';
}
//スクロール中
if(param.status == 2){
txt += 'scroll now';
}
//スクロール位置
txt = '  ( top:'+param.scroll.top+' , left:'+param.scroll.left+' )';

logbox.html(txt);
});

$('#clear').click(function(){
logbox.html('');
});



});



function sleep(time) {
	var d1 = new Date().getTime();
	var d2 = new Date().getTime();
	while (d2 < d1 + time) {
		d2 = new Date().getTime();
	}
	return;
}

function open_therapist_info_sp(therapist_id){
	
	window.open(url_root+"therapist_one.php?id="+therapist_id, "", "width=320,height=420,top=20,left=0,scrollbars=yes");
	exit();
	
}

function open_therapist_info_pc(therapist_id){
	
	window.open(url_root+"therapist_one.php?id="+therapist_id, "", "width=420,height=580,top=20,left=0,scrollbars=yes");
	exit();
	
}



























