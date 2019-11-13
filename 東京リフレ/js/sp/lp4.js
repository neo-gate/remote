
var url_root = "http://"+location.hostname+"/";

$(function() {
	
	$("#therapist_attendance_day").change(function(){
		
		var therapist_attendance_day = $("#therapist_attendance_day").val();
		var day_array = therapist_attendance_day.split("_");
		var year = day_array[0];
		var month = day_array[1];
		var day = day_array[2];
		
		var url = url_root+"lp4/list.php?year="+year+"&month="+month+"&day="+day;
		location.href = url;
		exit;
		
	});
	
});

function voice_disp_change(type){
	
	if( type == "off" ){
		
		$('#lp4_voice').hide(500);
		
		$('#lp4_voice_title').css("display", "block");
		
	}else if( type == "on" ){
		
		$('#lp4_voice_title').css("display", "none");
		
		$('#lp4_voice').show(500);
		
	}
	
	exit;
	
}

function qa_disp_change(type){
	
	if( type == "off" ){
		
		$('#lp4_qa').hide(500);
		
		$('#lp4_qa_title').css("display", "block");
		
	}else if( type == "on" ){
		
		$('#lp4_qa_title').css("display", "none");
		
		$('#lp4_qa').show(500);
		
	}
	
	exit;
	
}

function click_submit(type){
	
	var obj = document.forms["edit_frm"];
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = type;
	o.value='1';
	
	obj.appendChild(o);
	obj.submit();
	
	exit;
	
}

function openwin1() {
	window.open(url_root+"mail/reservation/input.php", "", "width=700,height=600,scrollbars=yes");
}

function open_attendance_schedule(therapist_id){
	
	window.open(url_root+"therapist_attendance_schedule.php?id="+therapist_id, "", "width=370,height=430,top=50,left=450,scrollbars=yes");
	
}

function open_attendance_schedule_sp(therapist_id){
	
	var indicator = '<div><img src="../img/indicator.gif" /></div>';
	
	$("#yotei_disp_"+therapist_id).html(indicator);
	
	$("#yotei_disp_"+therapist_id).css("display", "block");
	
	$.ajax({
		type:'post',
		url:url_root+'ajax/therapist_attendance_schedule_sp.php',
		data:{
			'therapist_id':therapist_id
		},
		success:function(data){
			
			$("#yotei_disp_"+therapist_id).html(data);
			
		}
	});
	
}

function yotei_win_close(therapist_id){
	
	$("#yotei_disp_"+therapist_id).css("display", "none");
	
}

function openwin_therapist_list() {
	window.open(url_root+"lp4/list.php");
}






