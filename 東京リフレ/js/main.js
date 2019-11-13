var url_root = "http://"+location.hostname+"/";

$(function() {
	
	$("#regist_button").mousedown(function(){
		$('#regist_button').css("background-color", "blue");
	});
	
	$("#regist_button").mouseup(function(){
		$('#regist_button').css("background-color", "#4f81bd");
	});
	
	$("#regist_button").hover(
	  function () {
		  $('#regist_button').css("background-color", "#88bfbf");
	  },
	  function () {
		  $('#regist_button').css("background-color", "#4f81bd");
	  }
	);
	
	$("#regist_button").click(function(){
		
		var onamae = document.mail_regist_form.onamae.value;
		var tel = document.mail_regist_form.tel.value;
		var mail = document.mail_regist_form.mail.value;
		
		$.ajax({
			type:'post',
			url:'ajax/mail_regist.php',
			data:{
				'onamae':onamae,
				'tel':tel,
				'mail':mail
			},
			success:function(data){
				$("#result_message").html(data);
			}
		});
		
	});
	
	$("#vip_yoyaku").click(function(){
		
		location.href=url_root+"vip/index.php";
		exit;
		
	});
	
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
		
		document.tel_input_form.submit();
		
	});
	
	intervalId = setInterval("wait_time_update()",(1000*60*10));
	
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

function openwin1() {
	window.open(url_root+"mail/reservation/input.php", "", "width=700,height=600,scrollbars=yes");
}

function openwin2() {
	window.open(url_root+"mail/therapist/input.php", "", "width=700,height=600,scrollbars=yes");
}

function openwin3() {
	window.open(url_root+"mail/driver/input.php", "", "width=700,height=600,scrollbars=yes");
}

function openwin4() {
	window.open(url_root+"mail/voice/input.php", "", "width=700,height=600,scrollbars=yes");
}

function openwin5() {
	window.open(url_root+"mail/office/input.php", "", "width=700,height=600,scrollbars=yes");
}

function therapistpage_day_change(year,month,day){
	
	var indicator = '<div class="therapist_page_indicator"><img src="img/indicator.gif" /></div>';
	
	$("#attendance").html(indicator);
	
	$.ajax({
		type:'post',
		url:url_root+'ajax/therapist_page_attendance_update.php',
		data:{
			'year':year,
			'month':month,
			'day':day
		},
		success:function(data){
			
			$("#attendance").html(data);
			
			$.ajax({
				type:'post',
				url:url_root+'ajax/update_therapist_page_day_disp.php',
				data:{
					'year':year,
					'month':month,
					'day':day
				},
				success:function(data){
					
					$("#therapist_page_day_disp").html(data);
					
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
			
		}
	});
	
}

function to_member_login_page(){
	
	location.href=url_root+"vip/index.php";
	exit;
	
}

function mail_starting(){
	
	location.href="mailto:order@tokyo-refle.com?subject=MailToRefle&amp;body=";
	exit;
	
}

function open_attendance_schedule(therapist_id){
	
	window.open(url_root+"therapist_attendance_schedule.php?id="+therapist_id, "", "width=370,height=430,top=50,left=450,scrollbars=yes");
	
}

function booking_frm_submit(){
	
	var indicator_url = url_root+"img/indicator.gif";
	var indicator_img = '<img src="'+indicator_url+'" />';
	
	$("#booking_frm_message").html(indicator_img);
	
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
	
	var indicator_url = url_root+"img/indicator.gif";
	var indicator_img = '<img src="'+indicator_url+'" />';
	
	$("#booking_frm_message").html(indicator_img);
	
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








