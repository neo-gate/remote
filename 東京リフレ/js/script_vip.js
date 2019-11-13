/*
var url_root = "http://"+location.hostname+"/";
if(window.location.port == 443) {
	var url_root_ssl = "https://"+location.hostname+"/";
} else {
	var url_root_ssl = "http://"+location.hostname+"/";
}
*/
var evaluation_skill = 0;
var evaluation_service = 0;
var evaluation_therapist_publish = 1;

var voice_publish_allow_therapist = 1;
var voice_publish_allow_site = 0;

$(function() {

	var url_root = document.location.protocol + "//"+location.hostname+"/";
	var url_root_ssl = url_root;

	$("#reserv_day").change(function(){

		var indicator_url = url_root+"img/indicator.gif";
		var indicator_img = '<img src="'+indicator_url+'" />';
		$("#free_therapist").html(indicator_img);

		$.ajax({
			type:'post',
			url:url_root_ssl+'ajax/vip/free_therapist_2.php',
			data:{
				'access_type':$("#access_type").val(),
				'customer_id':$("#customer_id").val(),
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
			url:url_root_ssl+'ajax/vip/free_therapist_2.php',
			data:{
				'customer_id':$("#customer_id").val(),
				'day':$("#reserv_day").val(),
				'time':$("#reserv_time").val(),
				'year':$("#reserv_year").val()
			},
			success:function(data){
				$("#free_therapist").html(data);
			}
		});

	});

});



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

function skill_click(num){

	var url_root = document.location.protocol + "//"+location.hostname+"/";

	evaluation_skill = num;

	var i;

	for(i=1;i<=10;i++){

		var data = '<img src="'+url_root+'img/ssl/vip/evaluation/star_off.gif" width="40" />';
		$("#skill_"+i).html(data);

	}

	for(i=1;i<=num;i++){

		var data = '<img src="'+url_root+'img/ssl/vip/evaluation/star_on.gif" width="40" />';
		$("#skill_"+i).html(data);

	}

	exit;

}

function service_click(num){

	var url_root = document.location.protocol + "//"+location.hostname+"/";

	evaluation_service = num;

	var i;

	for(i=1;i<=10;i++){

		var data = '<img src="'+url_root+'img/ssl/vip/evaluation/star_off.gif" width="40" />';
		$("#service_"+i).html(data);

	}

	for(i=1;i<=num;i++){

		var data = '<img src="'+url_root+'img/ssl/vip/evaluation/star_on.gif" width="40" />';
		$("#service_"+i).html(data);

	}

	exit;

}

function click_submit_evaluation(){

	if( (evaluation_skill == "0") || (evaluation_service == "0") ){

		alert("未選択です");
		exit;

	}

	var obj = document.forms["edit_frm"];

	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = "send";
	o.value='1';
	obj.appendChild(o);

	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = "skill";
	o.value=evaluation_skill;
	obj.appendChild(o);

	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = "service";
	o.value=evaluation_service;
	obj.appendChild(o);

	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = "publish_allow_therapist";
	o.value=evaluation_therapist_publish;
	obj.appendChild(o);

	obj.submit();

	exit;

}

function action_evaluation_therapist_publish(num){

	var url_root = document.location.protocol + "//"+location.hostname+"/";

	var data;

	if( num == "1" ){

		data = '<img src="'+url_root+'img/ssl/vip/evaluation/check_on.gif" onclick="action_evaluation_therapist_publish(0);" width="20" />';
		$("#evaluation_therapist_publish").html(data);

	}else{

		num = 0;

		data = '<img src="'+url_root+'img/ssl/vip/evaluation/check_off.gif" onclick="action_evaluation_therapist_publish(1);" width="20" />';
		$("#evaluation_therapist_publish").html(data);

	}

	evaluation_therapist_publish = num;

	exit;

}

function action_history_clip(id,selected_num,type,loginCustomId){

	var url_root = document.location.protocol + "//"+location.hostname+"/";
	var url_root_ssl = url_root;

	var indicator_url = url_root+"img/indicator.gif";
	var indicator_img = '<img src="'+indicator_url+'" width="20" />';
	$("#clip_action_"+id).html(indicator_img);

	$.ajax({
		type:'post',
		url:url_root_ssl+'ajax/ssl/vip/action_history_clip.php',
		data:{
			'id':id,
			'selected_num':selected_num,
			'type':type,
			'loginCustomId':loginCustomId
		},
		success:function(data){
			var url = url_root_ssl+"ssl/tokyo/vip/history.php?selected_num="+selected_num;
			location.href = url;
			exit;
		}
	});

}

function action_voice_publish_allow_therapist(num){

	var url_root = document.location.protocol + "//"+location.hostname+"/";

	var data;

	if( num == "1" ){

		data = '<img src="'+url_root+'img/ssl/vip/evaluation/check_on.gif" onclick="action_voice_publish_allow_therapist(0);" width="20" />';
		$("#voice_publish_allow_therapist").html(data);

	}else{

		num = 0;

		data = '<img src="'+url_root+'img/ssl/vip/evaluation/check_off.gif" onclick="action_voice_publish_allow_therapist(1);" width="20" />';
		$("#voice_publish_allow_therapist").html(data);

	}

	voice_publish_allow_therapist = num;

	exit;

}

function action_voice_publish_allow_site(num){

	var url_root = document.location.protocol + "//"+location.hostname+"/";

	var data;

	if( num == "1" ){

		data = '<img src="'+url_root+'img/ssl/vip/evaluation/check_on.gif" onclick="action_voice_publish_allow_site(0);" width="20" />';
		$("#voice_publish_allow_site").html(data);

	}else{

		num = 0;

		data = '<img src="'+url_root+'img/ssl/vip/evaluation/check_off.gif" onclick="action_voice_publish_allow_site(1);" width="20" />';
		$("#voice_publish_allow_site").html(data);

	}

	voice_publish_allow_site = num;

	exit;

}

function click_submit_voice_regist(){

	var obj = document.forms["edit_frm"];

	var voice_content = obj.voice_content.value;
	var voice_content_length = obj.voice_content.value.length;

	var message;

	if( voice_content == "" ){

		message = "未入力です";

		alert(message);
		exit;

	}else{

		if( voice_content_length > 5000 ){

			message = "入力は5000字以内です";

			alert(message);
			exit;

		}

	}

	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = "send";
	o.value='1';
	obj.appendChild(o);

	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = "publish_allow_therapist";
	o.value=voice_publish_allow_therapist;
	obj.appendChild(o);

	o=document.createElement('input');
	o.setAttribute('type', 'hidden');
	o.name = "publish_allow_site";
	o.value=voice_publish_allow_site;
	obj.appendChild(o);

	obj.submit();

	exit;

}
