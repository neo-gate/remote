<?php /* Smarty version 2.6.26, created on 2018-11-06 09:38:15
         compiled from sp/work_end2.tpl */ ?>

<form action="" enctype="multipart/form-data" method="post" id="edit_frm">

<input type="hidden" name="attendance_staff_new_id" value="<?php echo $this->_tpl_vars['params']['attendance_staff_new_id']; ?>
" />
<input type="hidden" name="work_meter_start" value="<?php echo $this->_tpl_vars['params']['work_meter_start']; ?>
" />

<div id="top_name_disp">
	<?php echo $this->_tpl_vars['params']['staff_name']; ?>
さん
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu_beta.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page_work_start">

<div class="title_bar">
<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/title_bar_2.gif" width="160" />
</div>
	<div id="mail_content">

	<?php if ($this->_tpl_vars['params']['error'] != ""): ?>
		<div class="error">
		<?php echo $this->_tpl_vars['params']['error']; ?>

		</div>
	<?php endif; ?>

	<div class="content_1">
	業務終了
	</div>

	<div class="content_2">
	<div class="left_1">
	終了時メーター
	</div>
	<div class="left_2">
	<input type="text" name="meter" value="<?php echo $this->_tpl_vars['params']['meter']; ?>
" style="width:120px;" />&nbsp;km
	</div>
	<br class="clear" />
	</div>

	<div class="content_3">
	写真を添付
	</div>

	<div class="content_5">
	<input type="file" name="pic" />
	</div>

	<div class="content_4">
	<div class="left_1">
	<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/btn_2.gif" width="120" class="btn_image" onclick="submit_work_start_send_back();" />
	</div>
	<div class="left_2">
	<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/btn_1.gif" width="120" class="btn_image" onclick="submit_work_start_send();" />
	</div>
	<br class="clear" />
	</div>

	</div>

</div>

</form>
