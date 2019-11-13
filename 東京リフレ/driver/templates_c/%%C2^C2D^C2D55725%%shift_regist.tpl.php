<?php /* Smarty version 2.6.26, created on 2017-01-31 12:21:24
         compiled from sp/shift_regist.tpl */ ?>

<div id="top_name_disp">
	<?php echo $this->_tpl_vars['params']['staff_name']; ?>
さん
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu_beta.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['params']['error_list'] != ""): ?>
<div style="text-align:center;padding-top:5px;"><?php echo $this->_tpl_vars['params']['error_list']; ?>
</div>
<?php endif; ?>

<div style="padding:0px 0px 50px 0px;">

	<div class="title_bar2">
	
		<?php if ($this->_tpl_vars['params']['today_month'] == $this->_tpl_vars['params']['month']): ?>
			<span style="font-size:10px;"><?php echo $this->_tpl_vars['params']['today_month']; ?>
月シフト</span>
		<?php else: ?>
			<a href="shift_regist.php?area=<?php echo $this->_tpl_vars['params']['area']; ?>
&id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
&year=<?php echo $this->_tpl_vars['params']['today_year']; ?>
&month=<?php echo $this->_tpl_vars['params']['today_month']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
" style="font-size:10px;">
				<?php echo $this->_tpl_vars['params']['today_month']; ?>
月シフト
			</a>
		<?php endif; ?>
		&nbsp;｜&nbsp;
		<?php if ($this->_tpl_vars['params']['next_month'] == $this->_tpl_vars['params']['month']): ?>
			<span style="font-size:10px;"><?php echo $this->_tpl_vars['params']['next_month']; ?>
月シフト</span>
		<?php else: ?>
			<a href="shift_regist.php?area=<?php echo $this->_tpl_vars['params']['area']; ?>
&id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
&year=<?php echo $this->_tpl_vars['params']['next_year']; ?>
&month=<?php echo $this->_tpl_vars['params']['next_month']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
" style="font-size:10px;">
				<?php echo $this->_tpl_vars['params']['next_month']; ?>
月シフト
			</a>
		<?php endif; ?>
		&nbsp;｜&nbsp;
		<?php if ($this->_tpl_vars['params']['month_3'] == $this->_tpl_vars['params']['month']): ?>
			<span style="font-size:10px;"><?php echo $this->_tpl_vars['params']['month_3']; ?>
月シフト</span>
		<?php else: ?>
			<a href="shift_regist.php?area=<?php echo $this->_tpl_vars['params']['area']; ?>
&id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
&year=<?php echo $this->_tpl_vars['params']['year_3']; ?>
&month=<?php echo $this->_tpl_vars['params']['month_3']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
" style="font-size:10px;">
				<?php echo $this->_tpl_vars['params']['month_3']; ?>
月シフト
			</a>
		<?php endif; ?>
		
	</div>
	
	<form action="" method="post" id="edit_frm">
	
	<input type="hidden" name="staff_id" value="<?php echo $this->_tpl_vars['params']['staff_id']; ?>
" />
	<input type="hidden" name="area" value="<?php echo $this->_tpl_vars['params']['area']; ?>
" />
	<input type="hidden" name="year" value="<?php echo $this->_tpl_vars['params']['year']; ?>
" />
	<input type="hidden" name="month" value="<?php echo $this->_tpl_vars['params']['month']; ?>
" />
	<input type="hidden" name="ch" value="<?php echo $this->_tpl_vars['params']['ch']; ?>
" />
	
	<div id="shift_regist_check_all">
		<div class="content_1">以下の時間帯をチェックした日付にコピー</div>
		<div class="content_2">
			
			<div class="left_1">
			<select name="start_time_check_all">
			<?php echo $this->_tpl_vars['params']['start_time_check_all_option']; ?>

			</select>
			</div>
			<div class="left_2">
			<select name="end_time_check_all">
			<?php echo $this->_tpl_vars['params']['end_time_check_all_option']; ?>

			</select>
			</div>
			<br class="clear" />
		</div>
	</div>
	
	<div>
	
		<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['params']['list_data']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['step'] = 1;
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = $this->_sections['cnt']['loop'];
    if ($this->_sections['cnt']['total'] == 0)
        $this->_sections['cnt']['show'] = false;
} else
    $this->_sections['cnt']['total'] = 0;
if ($this->_sections['cnt']['show']):

            for ($this->_sections['cnt']['index'] = $this->_sections['cnt']['start'], $this->_sections['cnt']['iteration'] = 1;
                 $this->_sections['cnt']['iteration'] <= $this->_sections['cnt']['total'];
                 $this->_sections['cnt']['index'] += $this->_sections['cnt']['step'], $this->_sections['cnt']['iteration']++):
$this->_sections['cnt']['rownum'] = $this->_sections['cnt']['iteration'];
$this->_sections['cnt']['index_prev'] = $this->_sections['cnt']['index'] - $this->_sections['cnt']['step'];
$this->_sections['cnt']['index_next'] = $this->_sections['cnt']['index'] + $this->_sections['cnt']['step'];
$this->_sections['cnt']['first']      = ($this->_sections['cnt']['iteration'] == 1);
$this->_sections['cnt']['last']       = ($this->_sections['cnt']['iteration'] == $this->_sections['cnt']['total']);
?>
			<div style="border-top:dotted 1px #000;padding:15px 10px 15px 10px;">
				<?php echo $this->_tpl_vars['params']['list_data'][$this->_sections['cnt']['index']]; ?>

			</div>
		<?php endfor; endif; ?>
		
		<div style="border-top:dotted 1px #000;"></div>
		
		<div style="text-align:center;padding:30px 0px 0px 0px;">
			<input type="submit" name="send" value="登録" style="padding:10px 10px 10px 10px;" />
		</div>
		
	</div>
	
	</form>
	
</div>
