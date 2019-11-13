<?php /* Smarty version 2.6.26, created on 2018-10-10 11:40:18
         compiled from sp/message_board_test.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'sp/message_board_test.tpl', 18, false),array('modifier', 'nl2br', 'sp/message_board_test.tpl', 21, false),)), $this); ?>

<div>
<a name="page_top"></a>
</div>

<div style="text-align:center;padding:10px 0px 5px 0px;border-bottom:solid 1px blue;">
	ドライバー：<?php echo $this->_tpl_vars['params']['staff_name']; ?>
さん
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu_beta_test.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['params']['help_page_data']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<div>
<a name="page_<?php echo $this->_tpl_vars['params']['help_page_data'][$this->_sections['cnt']['index']]['id']; ?>
"></a>
</div>
<div>
<div class="title_bar">
<?php echo ((is_array($_tmp=$this->_tpl_vars['params']['help_page_data'][$this->_sections['cnt']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%m/%d") : smarty_modifier_date_format($_tmp, "%m/%d")); ?>
　<?php echo $this->_tpl_vars['params']['help_page_data'][$this->_sections['cnt']['index']]['title']; ?>

</div>
<div style="padding:0px 20px 0px 20px;line-height:160%;">
<?php echo ((is_array($_tmp=$this->_tpl_vars['params']['help_page_data'][$this->_sections['cnt']['index']]['content'])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

</div>
</div>
<?php endfor; else: ?>
<div style="text-align:center;padding-top:30px;">伝言はありません。</div>
<?php endif; ?>

<div style="text-align:right;padding:20px 0px 0px 0px;">
<a href="#page_top">
一番上へ
</a>
</div>