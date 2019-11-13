<?php /* Smarty version 2.6.26, created on 2018-11-22 14:59:19
         compiled from sp/operation_detail.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'number_format', 'sp/operation_detail.tpl', 10, false),)), $this); ?>
<div id="top_name_disp">
	<?php echo $this->_tpl_vars['params']['staff_name']; ?>
さん
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu_beta.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="page_operation_detail">
	<div class="title_bar"><img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/title_bar_5.gif" width="120" /></div>
	<div class="content_1"><?php echo $this->_tpl_vars['params']['month_disp']; ?>
/<?php echo $this->_tpl_vars['params']['day_disp']; ?>
（<?php echo $this->_tpl_vars['params']['week_name']; ?>
）</div>
	<div class="content_2" style="border-bottom:dashed 1px #4a7ebb;padding-bottom:10px;margin-bottom:10px;">
		<div class="left_1">総支給額</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_data']['furikomi_price'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">報酬</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_data']['remuneration'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<?php if ($this->_tpl_vars['params']['remuneration_type'] != 2): ?>
	<div class="content_2">
		<div class="left_1">インセンティブ</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_data']['car_distance_over_allowance'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">インセンティブ２</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_data']['gasoline_value'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<?php else: ?>
	<div class="content_2">
		<div class="left_1">走行距離/時間</div>
		<div class="left_2"><?php echo $this->_tpl_vars['params']['distance_ave']; ?>
&nbsp;km/h</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">報酬単価</div>
		<div class="left_2"><?php echo $this->_tpl_vars['params']['unit_price']; ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<?php endif; ?>
	<div class="content_2">
		<div class="left_1">高速代</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_data']['highway'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">駐車場代</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_data']['parking'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2" style="border-bottom:dashed 1px #4a7ebb;padding-bottom:10px;margin-bottom:10px;">
		<div class="left_1">清算済み</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_data']['pay_finish'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">出勤</div>
		<div class="left_2"><?php echo $this->_tpl_vars['params']['start_time']; ?>
</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">退勤</div>
		<div class="left_2"><?php echo $this->_tpl_vars['params']['end_time']; ?>
</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">勤務時間</div>
		<div class="left_2"><?php echo $this->_tpl_vars['params']['furikomi_data']['work_time']; ?>
&nbsp;時間</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">手当</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_data']['allowance'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<div class="content_2">
		<div class="left_1">走行距離</div>
		<div class="left_2"><?php echo $this->_tpl_vars['params']['furikomi_data']['car_distance']; ?>
&nbsp;km</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<?php if ($this->_tpl_vars['params']['remuneration_type'] != 2): ?>
	<div class="content_2">
		<div class="left_1">リッター単価</div>
		<div class="left_2"><?php echo ((is_array($_tmp=$this->_tpl_vars['params']['settings_gasoline'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
&nbsp;円</div>
		<div class="left_3"></div>
		<br class="clear" />
	</div>
	<?php endif; ?>
</div>