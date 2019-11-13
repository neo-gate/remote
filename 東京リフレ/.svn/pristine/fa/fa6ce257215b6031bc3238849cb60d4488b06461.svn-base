//var url_root = "http://"+location.hostname+"/";

$(function() {
	var url_root = document.location.protocol + "//"+location.hostname+"/";
	
	$("#reserv_day").change(function(){
		
		var indicator_url = url_root+"img/indicator.gif";
		var indicator_img = '<img src="'+indicator_url+'" />';
		$("#free_therapist").html(indicator_img);
		
		$.ajax({
			type:'post',
			url:url_root+'ajax/vip/free_therapist.php',
			data:{
				'day':$("#reserv_day").val(),
				'time':$("#reserv_time").val(),
				'year':$("#reserv_year").val()
			},
			success:function(data){
				$("#free_therapist").html(data);
			}
		});
		
	});
	
	$("#reserv_time").change(function(){
		
		var indicator_url = url_root+"img/indicator.gif";
		var indicator_img = '<img src="'+indicator_url+'" />';
		$("#free_therapist").html(indicator_img);
		
		$.ajax({
			type:'post',
			url:url_root+'ajax/vip/free_therapist.php',
			data:{
				'day':$("#reserv_day").val(),
				'time':$("#reserv_time").val(),
				'year':$("#reserv_year").val()
			},
			success:function(data){
				$("#free_therapist").html(data);
			}
		});
		
	});
	
	$("#reservation_day_rev_mail").change(function(){
		
		var indicator_url = url_root+"img/indicator.gif";
		var indicator_img = '<img src="'+indicator_url+'" width="30" />';

		$("#therapist_rev_mail").html(indicator_img);
		
		$.ajax({
			type:'post',
			url:url_root+'ajax/update_therapist_rev_mail.php',
			data:{
				'reservation_day':$("#reservation_day_rev_mail").val(),
				'area':$("#area_rev_mail").val()
			},
			success:function(data){
				$("#therapist_rev_mail").html(data);
			}
		});
		
	});
	
	$("#vip_tel_button").hover(
	  function () {
		  $('#vip_tel_button').css("background-color", "#88bfbf");
	  },
	  function () {
		  $('#vip_tel_button').css("background-color", "#4f81bd");
	  }
	);
	
	$("#vip_tel_button").click(function(){
		
		$('#vip_tel_button').css("background-color", "#bbdbf3");
		document.tel_input_form.submit();
		
	});
	
	$("#calendar_day").change(function(){
		
		var indicator_url = url_root+"img/indicator.gif";
		var indicator_img = '<div style="text-align:center;"><img src="'+indicator_url+'" /></div>';
		$("#calendar_display_new").html(indicator_img);
		
		var calendar_day = $("#calendar_day").val();
		var calendar_array = calendar_day.split("_");
		var year = calendar_array[0];
		var month = calendar_array[1];
		var day = calendar_array[2];
		
		$.ajax({
			type:'get',
			//url:'../ajax/calendar_update.php',
			url:'../ajax/calendar_update_sp.php',
			data:{
				'year':year,
				'month':month,
				'day':day
			},
			success:function(data){
				//$("#calendar_display_disp").html(data);
				$("#calendar_display_new").html(data);
			}
		});
		
	});
	
	$("#therapist_attendance_day").change(function(){
		
		var indicator = '<div class="therapist_page_indicator"><img src="img/indicator.gif" /></div>';
		
		$("#attendance").html(indicator);
		
		var therapist_attendance_day = $("#therapist_attendance_day").val();
		var day_array = therapist_attendance_day.split("_");
		var year = day_array[0];
		var month = day_array[1];
		var day = day_array[2];
		
		$.ajax({
			type:'post',
			url:url_root+'ajax/therapist_page_attendance_update_sp.php',
			data:{
				'year':year,
				'month':month,
				'day':day
			},
			success:function(data){
				
				$("#attendance").html(data);
				
				$.ajax({
					type:'post',
					url:url_root+'ajax/update_therapist_page_attendance_title.php',
					data:{
						'year':year,
						'month':month,
						'day':day
					},
					success:function(data){
						
						$("#therapist_page_attendance_title").html(data);
						
					}
				});
				
			}
		});
		
	});
	
	$("#site_logo_right_menu").click(function(){
		
		$('#site_logo_right_menu_list').css("display", "block");
		
	});
	
	$("#site_logo_right_menu_close").click(function(){
		
		$('#site_logo_right_menu_list').css("display", "none");
		
	});
	
	intervalId = setInterval("wait_time_update()",(1000*60*10));
	
});

function wait_time_update(){
	var url_root = document.location.protocol + "//"+location.hostname+"/";
	
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

function open_attendance_schedule_sp(therapist_id){
	
	var url_root = document.location.protocol + "//"+location.hostname+"/";

	var indicator = '<div><img src="img/indicator.gif" /></div>';
	
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

function booking_frm_submit(){
	var url_root = document.location.protocol + "//"+location.hostname+"/";
	
	var indicator_url = url_root+"img/indicator.gif";
	var indicator_img = '<img src="'+indicator_url+'" />';
	
	$("#booking_frm_message").html(indicator_img);
	//exit;
	
	var namae = document.booking_frm.namae.value;
	var mail = document.booking_frm.mail.value;
	var today_month = document.booking_frm.today_month.value;
	var today_day = document.booking_frm.today_day.value;
	var today_year = document.booking_frm.today_year.value;
	var time = document.booking_frm.time.value;
	var course = document.booking_frm.course.value;
	var hotel_name = document.booking_frm.hotel_name.value;
	var room_number = document.booking_frm.room_number.value;
	var home_address = document.booking_frm.home_address.value;
	var any_request = document.booking_frm.any_request.value;
	
	for (var i = 0; i < document.booking_frm.hotel_or_home.length; i++){
		if(document.booking_frm.hotel_or_home[i].checked == true){
			var hotel_or_home = document.booking_frm.hotel_or_home[i].value;
		}
	}
	
	for (var i = 0; i < document.booking_frm.gender.length; i++){
		if(document.booking_frm.gender[i].checked == true){
			var gender = document.booking_frm.gender[i].value;
		}
	}
	
	for (var i = 0; i < document.booking_frm.cash_or_credit.length; i++){
		if(document.booking_frm.cash_or_credit[i].checked == true){
			var cash_or_credit = document.booking_frm.cash_or_credit[i].value;
		}
	}
	
	//$("#booking_frm_message").html(cash_or_credit);
	//exit;
	
	$.ajax({
		type:'post',
		url:url_root+'ajax/booking_form.php',
		data:{
			'namae':namae,
			'mail':mail,
			'gender':gender,
			'today_month':today_month,
			'today_day':today_day,
			'today_year':today_year,
			'time':time,
			'course':course,
			'hotel_or_home':hotel_or_home,
			'hotel_name':hotel_name,
			'room_number':room_number,
			'home_address':home_address,
			'cash_or_credit':cash_or_credit,
			'any_request':any_request
		},
		success:function(data){
			
			if( data == "ok" ){
				
				location.href=url_root+"en/thanks.php";
				exit;
			
			}else{
			
				$("#booking_frm_message").html(data);
			
			}
			
		}
	});
	
}

function send_edit_frm(){
	
	var obj = document.forms["edit_frm"];
	
	o = document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = "send";
	o.value='1';
	
	obj.appendChild(o);
	obj.submit();
	
	exit;
	
}

function booking_frm_submit_ch(){
	var url_root = document.location.protocol + "//"+location.hostname+"/";
	
	var indicator_url = url_root+"img/indicator.gif";
	var indicator_img = '<img src="'+indicator_url+'" />';
	
	$("#booking_frm_message").html(indicator_img);
	//exit;
	
	var namae = document.booking_frm.namae.value;
	var mail = document.booking_frm.mail.value;
	var today_month = document.booking_frm.today_month.value;
	var today_day = document.booking_frm.today_day.value;
	var today_year = document.booking_frm.today_year.value;
	var time = document.booking_frm.time.value;
	var course = document.booking_frm.course.value;
	var hotel_name = document.booking_frm.hotel_name.value;
	var room_number = document.booking_frm.room_number.value;
	var home_address = document.booking_frm.home_address.value;
	var any_request = document.booking_frm.any_request.value;
	
	for (var i = 0; i < document.booking_frm.hotel_or_home.length; i++){
		if(document.booking_frm.hotel_or_home[i].checked == true){
			var hotel_or_home = document.booking_frm.hotel_or_home[i].value;
		}
	}
	
	for (var i = 0; i < document.booking_frm.gender.length; i++){
		if(document.booking_frm.gender[i].checked == true){
			var gender = document.booking_frm.gender[i].value;
		}
	}
	
	for (var i = 0; i < document.booking_frm.cash_or_credit.length; i++){
		if(document.booking_frm.cash_or_credit[i].checked == true){
			var cash_or_credit = document.booking_frm.cash_or_credit[i].value;
		}
	}
	
	//$("#booking_frm_message").html(cash_or_credit);
	//exit;
	
	$.ajax({
		type:'post',
		url:url_root+'ajax/booking_form_ch.php',
		data:{
			'namae':namae,
			'mail':mail,
			'gender':gender,
			'today_month':today_month,
			'today_day':today_day,
			'today_year':today_year,
			'time':time,
			'course':course,
			'hotel_or_home':hotel_or_home,
			'hotel_name':hotel_name,
			'room_number':room_number,
			'home_address':home_address,
			'cash_or_credit':cash_or_credit,
			'any_request':any_request
		},
		success:function(data){
			
			if( data == "ok" ){
				
				location.href=url_root+"ch/thanks.php";
				exit;
			
			}else{
			
				$("#booking_frm_message").html(data);
			
			}
			
		}
	});
	
}













