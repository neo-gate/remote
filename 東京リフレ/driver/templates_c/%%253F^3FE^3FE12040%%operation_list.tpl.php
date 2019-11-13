<?php /* Smarty version 2.6.26, created on 2017-01-31 21:38:14
         compiled from sp/operation_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'number_format', 'sp/operation_list.tpl', 30, false),array('function', 'math', 'sp/operation_list.tpl', 68, false),)), $this); ?>

<div id="top_name_disp">
	<?php echo $this->_tpl_vars['params']['staff_name']; ?>
さん
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu_beta.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page_operation_list">

<div class="title_bar">
<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/title_bar_4.gif" width="120" />
</div>

<div class="content_1">
<div>
	<input type="hidden" name="area" value="<?php echo $this->_tpl_vars['params']['area']; ?>
" id="hi_area" />
	<input type="hidden" name="ch" value="<?php echo $this->_tpl_vars['params']['ch']; ?>
" id="hi_ch" />
	<input type="hidden" name="staff_id" value="<?php echo $this->_tpl_vars['params']['staff_id']; ?>
" id="hi_staff_id" />
</div>
<div>
<?php echo $this->_tpl_vars['params']['month_select_frm']; ?>

</div>
</div>

<div class="content_2">
<div class="left_1">
報酬(月間)
</div>
<div class="left_2">
<?php echo ((is_array($_tmp=$this->_tpl_vars['params']['remuneration_all'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円
</div>
<br class="clear" />
</div>

<?php if ($this->_tpl_vars['params']['this_month_flg'] == true): ?>

<div class="content_3">
<div class="left_1">
次回振込額(週)
</div>
<div class="left_2">
<?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_price_jikai'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円
</div>
<br class="clear" />
</div>

<div class="content_4">
<div class="left_1">
前回振込額(週)
</div>
<div class="left_2">
<?php echo ((is_array($_tmp=$this->_tpl_vars['params']['furikomi_price_zenkai'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円
</div>
<br class="clear" />
</div>

<?php endif; ?>

<div class="content_5">
<a href="shift_regist.php?id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
&area=<?php echo $this->_tpl_vars['params']['area']; ?>
">
<img src="<?php echo $this->_tpl_vars['params']['url_root_site']; ?>
img/driver/btn_5.gif" width="140" />
</a>
</div>

<div class="content_6">
<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['params']['sale_data']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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

<?php echo smarty_function_math(array('equation' => "a%b",'a' => $this->_sections['cnt']['index'],'b' => 2,'assign' => 'kekka'), $this);?>


<?php if ($this->_tpl_vars['kekka'] == '1'): ?>
<div class="gyou">
<?php else: ?>
<div class="gyou2">
<?php endif; ?>

<a href="operation_detail.php?area=<?php echo $this->_tpl_vars['params']['area']; ?>
&ch=<?php echo $this->_tpl_vars['params']['ch']; ?>
&id=<?php echo $this->_tpl_vars['params']['staff_id']; ?>
&year=<?php echo $this->_tpl_vars['params']['sale_data'][$this->_sections['cnt']['index']]['year']; ?>
&month=<?php echo $this->_tpl_vars['params']['sale_data'][$this->_sections['cnt']['index']]['month']; ?>
&day=<?php echo $this->_tpl_vars['params']['sale_data'][$this->_sections['cnt']['index']]['day']; ?>
">
<?php echo $this->_tpl_vars['params']['sale_data'][$this->_sections['cnt']['index']]['month_disp']; ?>
/<?php echo $this->_tpl_vars['params']['sale_data'][$this->_sections['cnt']['index']]['day_disp']; ?>
(<?php echo $this->_tpl_vars['params']['sale_data'][$this->_sections['cnt']['index']]['week_name']; ?>
)　　　
<?php if ($this->_tpl_vars['params']['sale_data'][$this->_sections['cnt']['index']]['lowest_guarantee_flg'] == true): ?>※<?php endif; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['params']['sale_data'][$this->_sections['cnt']['index']]['remuneration'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

円
</a>

<?php if ($this->_tpl_vars['kekka'] == '1'): ?>
</div>
<?php else: ?>
</div>
<?php endif; ?>

<?php endfor; endif; ?>
</div>

</div>
