
var url_root = "http://"+location.hostname+"/";

$(function() {
	
	$("#site_logo_right_menu").click(function(){
		
		$('#site_logo_right_menu_list').css("display", "block");
		
	});
	
	$("#site_logo_right_menu_close").click(function(){
		
		$('#site_logo_right_menu_list').css("display", "none");
		
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
