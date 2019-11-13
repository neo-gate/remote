
$(function() {
	
	$("#after_work_check").click(function(){
		
		if($("#after_work_check").prop('checked')) {
			//alert("チェックされています。");
			$('#kensyuu_work_time').show();
		}
		else {
			//alert("チェックされていません。");
			$('#kensyuu_work_time').hide();
		}
		
	});
	
	$("#operation_list_month_select").live("change", function(){
		
		var month_select = $("#operation_list_month_select").val();
		var data_array = month_select.split("_");
		var year = data_array[0];
		var month = data_array[1];
		
		var area = $("#hi_area").val();
		var ch = $("#hi_ch").val();
		var staff_id = $("#hi_staff_id").val();
		
		location.href="operation_list.php?year="+year+"&month="+month+"&area="+area+"&ch="+ch+"&id="+staff_id;
		exit;
		
	});
	
	$("#point_list_month_select").live("change", function(){
		
		var month_select = $("#point_list_month_select").val();
		var data_array = month_select.split("_");
		var year = data_array[0];
		var month = data_array[1];
		
		var area = $("#hi_area").val();
		var ch = $("#hi_ch").val();
		var therapist_id = $("#hi_therapist_id").val();
		
		location.href="point_list.php?year="+year+"&month="+month+"&area="+area+"&ch="+ch+"&id="+therapist_id;
		exit;
		
	});
	
});

function reservation_start(id){
	
	//alert("reservation_start");
	
	if(window.confirm('スタートにしてよろしいですか？')){
		
		$('#start_frm_'+id).submit();
	    exit;
    
	}
	
}

function reservation_end(id){
	
	//alert("reservation_start");
	
	//if(window.confirm('エンドにしてよろしいですか？')){
		
		$('#end_frm_'+id).submit();
	    exit;
    
	//}
	
}

function delete_movement_cost_edit(){
	
	if(window.confirm('削除してよろしいですか？')){
		
		var obj = document.forms["movement_cost_edit_frm"];
		
		o=document.createElement('input');
		o.setAttribute('type', 'hidden');
		o.name ='send_delete';
		o.value='1';
		
		obj.appendChild(o);
		obj.submit();
		
		exit;
	
	}
	
}

function move_shift_edit_page(staff_id,area,year,month,day,start_time,end_time,ch){
	
	var obj = document.forms["edit_frm"];
	
	obj.action = "edit.php";
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='send_list_edit';
	o.value='1';
	
	obj.appendChild(o);
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='staff_id';
	o.value=staff_id;
	
	obj.appendChild(o);
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='area';
	o.value=area;
	
	obj.appendChild(o);
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='year';
	o.value=year;
	
	obj.appendChild(o);
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='month';
	o.value=month;
	
	obj.appendChild(o);
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='day';
	o.value=day;
	
	obj.appendChild(o);
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='start_time';
	o.value=start_time;
	
	obj.appendChild(o);
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='end_time';
	o.value=end_time;
	
	obj.appendChild(o);
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='ch';
	o.value=ch;
	
	obj.appendChild(o);
	
	obj.submit();
	
	exit;
	
}

function submit_work_start_send(){
	
	if(window.confirm('送信してよろしいですか？')){
		
		var obj = document.forms["edit_frm"];
		
		o=document.createElement('input');
		o.setAttribute('type', 'hidden');
		o.name ='send';
		o.value='1';
		
		obj.appendChild(o);
		obj.submit();
		
		exit;
	
	}
	
}

function submit_work_start_send_back(){
	
	var obj = document.forms["edit_frm"];
	
	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name ='send_back';
	o.value='1';
	
	obj.appendChild(o);
	obj.submit();
	
	exit;
	
}









