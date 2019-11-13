<?php /* Smarty version 2.6.26, created on 2017-01-31 14:17:42
         compiled from sp/edit.tpl */ ?>

<div style="text-align:center;padding-top:10px;">
ドライバー：<?php echo $this->_tpl_vars['params']['staff_name']; ?>
さん
</div>

<div style="padding:20px 0px 0px 0px;">
	<div style="float:left;">
		○シフト修正　<?php echo $this->_tpl_vars['params']['month']; ?>
月
	</div>
	<div style="float:right;padding:0px 0px 0px 0px;">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "to_top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<br class="clear" />
</div>

<div style="color:red;padding:10px 0px 0px 0px;"><?php echo $this->_tpl_vars['params']['error']; ?>
</div>
<div style="padding:10px 0px 0px 0px;">
	<form action="" method="post">
		<input type="hidden" name="staff_id" value="<?php echo $this->_tpl_vars['params']['staff_id']; ?>
" />
		<input type="hidden" name="area" value="<?php echo $this->_tpl_vars['params']['area']; ?>
" />
		<input type="hidden" name="year" value="<?php echo $this->_tpl_vars['params']['year']; ?>
" />
		<input type="hidden" name="month" value="<?php echo $this->_tpl_vars['params']['month']; ?>
" />
		<input type="hidden" name="day" value="<?php echo $this->_tpl_vars['params']['day']; ?>
" />
		<input type="hidden" name="week_name" value="<?php echo $this->_tpl_vars['params']['week_name']; ?>
" />
		<input type="hidden" name="start_start_time" value="<?php echo $this->_tpl_vars['params']['start_start_time']; ?>
" />
		<input type="hidden" name="start_end_time" value="<?php echo $this->_tpl_vars['params']['start_end_time']; ?>
" />
		<input type="hidden" name="ch" value="<?php echo $this->_tpl_vars['params']['ch']; ?>
" />
		<div style="border-top:dotted 1px #000;"></div>
		<div style="padding:20px 0px 0px 0px;">
			<?php echo $this->_tpl_vars['params']['day']; ?>
(<?php echo $this->_tpl_vars['params']['week_name']; ?>
)　
			<select name="start_time">
				<?php echo $this->_tpl_vars['params']['start_time_option']; ?>

			</select>
			&nbsp;&nbsp;&nbsp;
			<select name="end_time">
				<?php echo $this->_tpl_vars['params']['end_time_option']; ?>

			</select>
		</div>
				
		<br />
		
		<div style="border-top:dotted 1px #000;"></div>
		<div style="padding:20px 0px 0px 50px;">
			<div><input type="checkbox" name="kekkin" value="1" />欠勤に変更する</div>
			<div style="color:red;padding:5px 0px 0px 0px;font-size:12px;">
				当日欠勤は余程の事情がない限り控えてください
			</div>
		</div>
		<div style="text-align:center;padding:30px 0px 0px 0px;">
			<input type="submit" name="send" value="修正" style="padding:10px 10px 10px 10px;" />
		</div>
	</form>
</div>