<?php defined('_JEXEC') or die;
$input = JFactory::getApplication()->input;
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
$movies_field = JFormHelper::loadFieldType('movies');
$params = JComponentHelper::getParams('com_kinoarhiv');
?>
<style type="text/css">
	@import url("<?php echo JURI::root(); ?>components/com_kinoarhiv/assets/themes/component/<?php echo $params->get('ka_theme'); ?>/css/select.css");
</style>
<script src="<?php echo JURI::root(); ?>components/com_kinoarhiv/assets/js/select2.min.js" type="text/javascript"></script>
<?php GlobalHelper::getScriptLanguage('select2_locale_', false, 'select', true); ?>
<script src="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/utils.js" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function($){
		$('#copy-apply').button('disable');

		$('#copy_item').select2({
			placeholder: '<?php echo JText::_('COM_KA_SEARCH_AJAX'); ?>',
			quietMillis: 200,
			allowClear: true,
			minimumInputLength: 1,
			maximumSelectionSize: 1,
			ajax: {
				cache: true,
				url: 'index.php?option=com_kinoarhiv&task=ajaxData&element=movies&format=json&ignore[]=<?php echo $input->get('id', 0, 'int'); ?>',
				data: function(term, page){
					return {
						term: term,
						showAll: 0
					}
				},
				results: function(data, page){
					return {results: data};
				}
			},
			formatResult: function(data){
				if (data.year == '0000') return data.title;
				return data.title+' ('+data.year+')';
			},
			formatSelection: function(data){
				if (data.year == '0000') return data.title;
				return data.title+' ('+data.year+')';
			},
			escapeMarkup: function(m) { return m; }
		}).change(function(val, added, removed){
			if (parseInt(val.val) != parseInt($('#id').val())) {
				$('.item_type').toggle();
			} else {
				$('#copy-apply').button('disable');
				showMsg('.copy-dlg #id', '<?php echo JText::_('COM_KA_MOVIES_GALLERY_COPYFROM_ITEMTYPE_ERROR', true); ?>');
			}
		});

		$('#copy_from_type').select2().change(function(val, added, removed){
			if (typeof val.val != 'undefined') {
				$('#copy-apply').button('enable');
			}
		}).trigger('change');
	});
//]]>
</script>
<div class="row-fluid">
	<div class="span12">
		<form action="index.php?option=com_kinoarhiv&controller=mediamanager&task=copyfrom&format=json" id="form_copyfrom">
			<fieldset class="form-horizontal copy">
				<div class="control-group">
					<div class="control-label">
						<?php echo $movies_field->getLabel('copy_item', 'COM_KA_MOVIES_GALLERY_COPYFROM_LABEL', 'COM_KA_MOVIES_GALLERY_COPYFROM_DESC', 'required'); ?>
					</div>
					<div class="controls copy-from">
						<?php echo $movies_field->getInput('copy_item', 100, '', 0, 'span12 required'); ?>
					</div>
				</div>
				<div class="control-group item_type" style="display: none;">
					<div class="control-label">
						<label class="required" for="copy_from_type"><?php echo JText::_('COM_KA_MOVIES_GALLERY_COPYFROM_ITEMTYPE_LABEL'); ?></label>
					</div>
					<div class="controls copy-from">
						<select name="copy_from_type" id="copy_from_type" class="span7 required">
							<option value="" selected>---</option>
							<option value="1"><?php echo JText::_('COM_KA_MOVIES_WALLPP'); ?></option>
							<option value="2"><?php echo JText::_('COM_KA_MOVIES_POSTERS'); ?></option>
							<option value="3"><?php echo JText::_('COM_KA_MOVIES_SCRSHOTS'); ?></option>
						</select>
					</div>
				</div>
			</fieldset>
			<input type="hidden" name="id" id="id" value="<?php echo $input->get('id', 0, 'int'); ?>" />
		</form>
	</div>
</div>