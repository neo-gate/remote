<?php /* Smarty version 2.6.26, created on 2017-01-31 14:19:00
         compiled from sp/edit_complete.tpl */ ?>

<div style="text-align:center;padding-top:10px;">
	ドライバー：<?php echo $this->_tpl_vars['params']['staff_name']; ?>
さん
</div>

<div style="padding:40px 0px 0px 30px;line-height:180%;">
<?php if ($this->_tpl_vars['params']['kekkin'] == '1'): ?>
シフト欠勤を受け付けました。<br />
<?php else: ?>
シフト修正を受け付けました。<br />
<?php endif; ?>
本部でチェック後、シフト確定致します。<br />

<!--
<br />
シフトの編集はTOPページの「シフト編集」から行ってください
-->

</div>