
$(function() {
	
	
	
});

function delete_confirm_action(id){
	
	if(window.confirm('削除してよろしいですか？')){
		
		var obj = document.forms["edit_frm_"+id];
		
		o=document.createElement('input');
		o.setAttribute('type', 'hidden');
		o.name ='send_delete';
		o.value='1';
		
		obj.appendChild(o);
		obj.submit();
		
		exit;
    	
	}
	
	exit;
	
}

