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

$user		= JFactory::getUser();
$input 		= JFactory::getApplication()->input;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$sortFields = $this->getSortFields();

KAComponentHelper::loadMediamanagerAssets();
JHtml::_('stylesheet', JUri::root() . 'components/com_kinoarhiv/assets/themes/component/' . $this->params->get('ka_theme') . '/css/select.css');
JHtml::_('script', JUri::root() . 'components/com_kinoarhiv/assets/js/select2.min.js');
KAComponentHelper::getScriptLanguage('select2_locale_', true, 'select', true);
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
		var dirn;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	};

	jQuery(document).ready(function($){
		var bootstrapTooltip = $.fn.tooltip.noConflict();
		$.fn.bootstrapTlp = bootstrapTooltip;
		var bootstrapButton = $.fn.button.noConflict();
		$.fn.bootstrapBtn = bootstrapButton;

		var tooltip_img = $('a.tooltip-img');

		tooltip_img.hover(function(e){
			$(this).next('img').stop().hide().fadeIn();
		}, function(e){
			$(this).next('img').stop().fadeOut();
		});
		tooltip_img.colorbox({ maxHeight: '95%', maxWidth: '95%', fixed: true });

		// Reload page if files was uploaded
		$('#imgModalUpload').on('hidden', function() {
			if (parseInt($('input[name="file_uploaded"]').val(), 10) == 1) {
				document.location.reload();
			}
		});

		<?php if ($input->get('tab', 0, 'int') == 3): ?>
		$('.cmd-fp_off, .cmd-fp_on').click(function(){
			var boxchecked = $('input[name="boxchecked"]');

			$(this).closest('tr').find(':checkbox').prop('checked', true);
			boxchecked.val(parseInt(boxchecked.val(), 10) + 1);

			if ($(this).hasClass('cmd-fp_off')) {
				$('input[name="task"]').val('fpOff');
				$('form').submit();
			} else if ($(this).hasClass('cmd-fp_on')) {
				$('input[name="task"]').val('fpOn');
				$('form').submit();
			}
		});
		<?php endif; ?>

		Joomla.submitbutton = function(task) {
			if (task == 'upload') {
				$('#image_uploader').pluploadQueue({
					runtimes: 'html5,gears,flash,silverlight,browserplus,html4',
					url: 'index.php?option=com_kinoarhiv&controller=mediamanager&task=upload&format=raw&section=<?php echo $input->get('section', '', 'word'); ?>&type=<?php echo $input->get('type', '', 'word'); ?>&tab=<?php echo $input->get('tab', 0, 'int'); ?>&id=<?php echo $input->get('id', 0, 'int'); ?>',
					multipart_params: {
						'<?php echo JSession::getFormToken(); ?>': 1
					},
					max_file_size: '<?php echo $this->params->get('upload_limit'); ?>',
					unique_names: false,
					multiple_queues: true,
					filters: [{title: 'Image', extensions: '<?php echo $this->params->get('upload_mime_images'); ?>'}],
					flash_swf_url: '<?php echo JUri::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/Moxie.swf',
					silverlight_xap_url: '<?php echo JUri::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/Moxie.xap',
					preinit: {
						init: function(up, info){
							$('#image_uploader').find('.plupload_buttons a:last').after('<a class="plupload_button plupload_clear_all" href="#"><?php echo JText::_('JCLEAR'); ?></a>');
							$('#image_uploader .plupload_clear_all').click(function(e){
								e.preventDefault();
								up.splice();
								$.each(up.files, function(i, file){
									up.removeFile(file);
								});
							});
						},
						UploadComplete: function(up, files){
							$('input[name="file_uploaded"]').val(1);
						}
					},
					init: {
						PostInit: function () {
							$('#image_uploader_container').removeAttr('title', '');
						}
					}
				});

				$('#imgModalUpload').modal();

				return false;
			} else if (task == 'copyfrom') {
				var dialog = $('<div id="dialog-copy" title="<?php echo JText::_('JTOOLBAR_COPYFROM'); ?>"><p class="ajax-loading"><?php echo JText::_('COM_KA_LOADING'); ?></p></div>');

				dialog.dialog({
					dialogClass: 'copy-dlg',
					modal: true,
					width: 600,
					height: 300,
					close: function(event, ui){
						$('#item_id').select2('destroy');
						dialog.remove();
					},
					buttons: [
						{
							text: '<?php echo JText::_('JTOOLBAR_COPY'); ?>',
							id: 'copy-apply',
							click: function(){
								if ($('#item_id', this).select2('val') == 0 || $('#item_id', this).select2('val') == '') {
									return false;
								}

								blockUI('show');
								$('#copy-apply').button('disable');
								var $this = $(this);

								$.ajax({
									type: 'POST',
									url: $('#form_copyfrom', this).attr('action'),
									data: '&id=' + $('#id', this).val() + '&item_id=' + $('#item_id', this).select2('val') + '&item_subtype=' + $('#item_subtype', this).val() + '&item_type=' + $('#item_type', this).val() + '&section=' + $('#section', this).val() + '&<?php echo JSession::getFormToken(); ?>=1'
								}).done(function(response){
									blockUI();
									if (response.success) {
										$this.dialog('close');
										document.location.reload(true);
									} else {
										showMsg('.copy-dlg #id', response.message);
									}
									$('#copy-apply').button('enable');
								}).fail(function(xhr, status, error){
									showMsg('.copy-dlg #id', error);
									$('#copy-apply').button('enable');
									blockUI();
								});
							}
						},
						{
							text: '<?php echo JText::_('JTOOLBAR_CLOSE'); ?>',
							click: function(){
								$(this).dialog('close');
							}
						}
					]
				});
				dialog.load('index.php?option=com_kinoarhiv&task=loadTemplate&template=copyfrom&model=mediamanager&view=mediamanager&format=raw&id=<?php echo $input->get('id', 0, 'int'); ?>&item_type=<?php echo $input->get('type', '', 'word'); ?>&section=<?php echo $input->get('section', '', 'word'); ?>');

				return false;
			}

			Joomla.submitform(task);
		}
	});
</script>
<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-left">
			<a href="index.php?option=com_kinoarhiv&view=mediamanager&section=name&type=gallery&tab=3&id=<?php echo $input->get('id', 0, 'int'); ?>" class="btn btn-small <?php echo ($input->get('tab', 0, 'int') == 3) ? 'btn-success' : ''; ?>"><span class="icon-picture icon-white"></span> <?php echo JText::_('COM_KA_NAMES_GALLERY_PHOTO'); ?></a>
			<a href="index.php?option=com_kinoarhiv&view=mediamanager&section=name&type=gallery&tab=2&id=<?php echo $input->get('id', 0, 'int'); ?>" class="btn btn-small <?php echo ($input->get('tab', 0, 'int') == 2) ? 'btn-success' : ''; ?>"><span class="icon-picture icon-white"></span> <?php echo JText::_('COM_KA_NAMES_GALLERY_POSTERS'); ?></a>
			<a href="index.php?option=com_kinoarhiv&view=mediamanager&section=name&type=gallery&tab=1&id=<?php echo $input->get('id', 0, 'int'); ?>" class="btn btn-small <?php echo ($input->get('tab', 0, 'int') == 1) ? 'btn-success' : ''; ?>"><span class="icon-picture icon-white"></span> <?php echo JText::_('COM_KA_NAMES_GALLERY_WALLPP'); ?></a>
		</div>
		<div class="btn-group pull-right">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div class="btn-group pull-right">
			<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
			<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
				<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
				<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');  ?></option>
			</select>
		</div>
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
			<select name="sortTable" id="sortTable" class="input-xlarge" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
				<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
			</select>
		</div>
	</div><br />
	<table class="table table-striped gallery-list" id="articleList">
		<thead>
			<tr>
				<th width="1%" class="center">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th><?php echo JText::_('COM_KA_MOVIES_GALLERY_HEADING_FILENAME'); ?></th>
				<th width="15%" class="nowrap center hidden-phone"><?php echo JText::_('COM_KA_MOVIES_GALLERY_HEADING_DIMENSION'); ?></th>
				<?php if ($input->get('tab', 0, 'int') == 3): ?>
					<th width="10%" style="min-width: 55px" class="nowrap center"><?php echo JText::_('COM_KA_MOVIES_GALLERY_HEADING_FRONTPAGE'); ?></th>
				<?php endif; ?>
				<th width="1%" style="min-width: 55px" class="nowrap center"><?php echo JText::_('JSTATUS'); ?></th>
				<th width="5%" class="nowrap center"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->items) == 0): ?>
				<tr>
					<td colspan="6" class="center"><?php echo JText::_('COM_KA_NO_ITEMS'); ?></td>
				</tr>
			<?php else:
				foreach ($this->items as $i => $item):
					$canEdit    = $user->authorise('core.edit',		  'com_kinoarhiv.name.' . $item->id);
					$canChange  = $user->authorise('core.edit.state', 'com_kinoarhiv.name.' . $item->id);
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id, false, '_id'); ?>
					</td>
					<td>
						<?php if (!empty($item->error)): ?><a href="#" class="hasTooltip error_image" title="<?php echo $item->error; ?>"></a><?php endif; ?>
						<a href="<?php echo $item->filepath; ?>" class="tooltip-img" rel="group_<?php echo $input->get('tab', 0, 'int'); ?>"><?php echo $item->filename; ?></a>
						<?php if ($item->th_filepath != ''): ?><img src="<?php echo $item->th_filepath; ?>" class="tooltip-img-content" /><?php endif; ?>
						<?php if ($item->folderpath != ''): ?> <span class="small gray">(<?php echo $item->folderpath; ?>)</span><?php endif; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo $item->dimension; ?>
					</td>
					<?php if ($input->get('tab', 0, 'int') == 3 && $canChange): ?>
					<td class="center">
						<?php if ($item->photo_frontpage == 0): ?>
							<a class="btn btn-micro active cmd-fp_off" href="javascript:void(0);"><i class="icon-unpublish"></i></a>
						<?php else: ?>
							<a class="btn btn-micro active cmd-fp_on" href="javascript:void(0);"><i class="icon-publish"></i></a>
						<?php endif; ?>
					</td>
					<?php endif; ?>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->state, $i, '', $canChange, 'cb'); ?>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach;
			endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="controller" value="mediamanager" />
	<input type="hidden" name="section" value="<?php echo $input->get('section', '', 'word'); ?>" />
	<input type="hidden" name="type" value="<?php echo $input->get('type', '', 'word'); ?>" />
	<input type="hidden" name="tab" value="<?php echo $input->get('tab', 0, 'int'); ?>" />
	<input type="hidden" name="id" value="<?php echo $input->get('id', 0, 'int'); ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<input type="hidden" name="file_uploaded" value="0" />
	<div class="pagination bottom">
		<?php echo $this->pagination->getListFooter(); ?><br />
		<?php echo $this->pagination->getResultsCounter(); ?>
	</div>
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php echo JLayoutHelper::render('layouts.edit.upload_image', array(), JPATH_COMPONENT);
