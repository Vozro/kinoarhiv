<?php
/**
 * @package     Kinoarhiv.Administrator
 * @subpackage  com_kinoarhiv
 *
 * @copyright   Copyright (C) 2010 Libra.ms. All rights reserved.
 * @license     GNU General Public License version 2 or later
 * @url            http://киноархив.com/
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'apply' || task == 'save' || task == 'save2new') {
			if (jQuery('#form_movie_id').select2('val') == '' || jQuery('#form_vendor_id').select2('val') == '' || jQuery('#form_premiere_date').val() == '') {
				showMsg('#j-main-container', '<?php echo JText::_('COM_KA_REQUIRED'); ?>');
				return;
			}
		}
		Joomla.submitform(task);
	};
</script>
<form action="<?php echo JRoute::_('index.php?option=com_kinoarhiv'); ?>" method="post" name="adminForm" id="adminForm"
	class="form-validate" autocomplete="off">
	<div id="j-main-container">
		<fieldset class="form-horizontal">
			<?php
			foreach ($this->form->getFieldset('edit') as $field):
			?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php
			endforeach;
			?>
		</fieldset>
	</div>

	<input type="hidden" name="controller" value="premieres" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id[]" value="<?php echo $this->form->getValue('id') != 0 ? $this->form->getValue('id') : ''; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
