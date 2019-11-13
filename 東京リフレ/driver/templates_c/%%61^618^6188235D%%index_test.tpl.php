<?php /* Smarty version 2.6.26, created on 2018-10-10 11:36:04
         compiled from sp/index_test.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'sp/index_test.tpl', 21, false),)), $this); ?>
<div id="top_name_disp"><?php echo $this->_tpl_vars['params']['staff_name']; ?>
さん</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu_beta_test.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="page_index">
	<?php if ($this->_tpl_vars['params']['message_board_data_num'] != '0'): ?>
	<div>
		<div class="title">
			<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/shift/title/02_heading.gif" width="75" />
		</div>
		<div>
			<?php if ($this->_tpl_vars['params']['shift_notice_flg'] == true): ?>
			<div class="top_shift_request">
				<div class="left_1">
					<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/shift/icon/02_icon.gif" width="30" />
				</div>
				<div class="left_2">シフトの入力をお願いします</div>
				<br class="clear" />
			</div>
			<?php endif; ?>
			<div id="top_message_board_box">
			<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['params']['message_board_data']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
			<?php echo smarty_function_math(array('equation' => "a%b",'a' => $this->_sections['cnt']['index'],'b' => 2,'assign' => 'gyou_type'), $this);?>

			<?php if ($this->_tpl_vars['gyou_type'] == '1'): ?><div class="one_2"><?php else: ?><div class="one_1"><?php endif; ?>
				<div style="font-size:12px;"><?php echo $this->_tpl_vars['params']['message_board_data'][$this->_sections['cnt']['index']]['day_disp']; ?>
</div>
				<div style="padding-top:3px;">
					<a href="message_board_test.php?area=<?php echo $this->_tpl_vars['params']['area']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
&id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
#page_<?php echo $this->_tpl_vars['params']['message_board_data'][$this->_sections['cnt']['index']]['id']; ?>
"><?php echo $this->_tpl_vars['params']['message_board_data'][$this->_sections['cnt']['index']]['title']; ?>
</a>
				</div>
			<?php if ($this->_tpl_vars['gyou_type'] == '1'): ?></div><?php else: ?></div><?php endif; ?>
			<?php endfor; endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="title">
		<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/title_bar_3.gif" width="160" />
	</div>
	<div class="content_1">
		<div class="left_1">
			<a href="work_start.php?id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
&area=<?php echo $this->_tpl_vars['params']['area']; ?>
">
			<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/btn_3.gif" width="130" />
		</a>
		</div>
		<div class="left_2">
			<a href="work_end.php?id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
&area=<?php echo $this->_tpl_vars['params']['area']; ?>
">
			<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/btn_4.gif" width="130" />
		</a>
		</div>
		<br class="clear" />
	</div>
	<div class="title_2">
		<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/title_bar_6.gif" width="160" />
	</div>
	<div class="content_2">
		<a href="shift_regist.php?id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
&area=<?php echo $this->_tpl_vars['params']['area']; ?>
">
		<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/btn_5.gif" width="140" /></a>
	</div>
</div>