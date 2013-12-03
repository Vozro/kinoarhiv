<?php defined('_JEXEC') or die;
$input = JFactory::getApplication()->input;
$section = $input->get('section', '', 'word');
$type = $input->get('type', '', 'word');
$video_ext = 'h264,mp4,mp4v,mpg4,mpeg,mpg,ogv,qt,mov,wmv,avi,mpd';
$subtitle_ext = 'vtt,sub,sup,txt,xml,mks,cue';
?>
<link type="text/css" rel="stylesheet" href="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/css/mediamanager.css"/>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_kinoarhiv/assets/js/ui.aurora.min.js"></script>
<script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/jquery-ui-1.10.3.custom.min.js"></script>

<!-- Uncomment line below to load Browser+ from YDN -->
<!-- <script src="http://bp.yahooapis.com/2.4.21/browserplus-min.js" type="text/javascript"></script> -->
<!-- Comment line below if load Browser+ from YDN -->
<script src="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/browserplus-min.js" type="text/javascript"></script>

<script src="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.full.js" type="text/javascript"></script>
<script src="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/i18n/mediamanager/<?php echo substr(JFactory::getLanguage()->getTag(), 0, 2); ?>.js" type="text/javascript"></script>
<script src="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/jquery.plupload.queue.js" type="text/javascript"></script>
<script src="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/jquery.ui.plupload.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/jquery.ui.tooltip.min.js"></script>
<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function($){
		function showMsg(selector, text) {
			$(selector).aurora({
				text: text,
				placement: 'before',
				button: 'close',
				button_title: '[<?php echo JText::_('COM_KA_CLOSE'); ?>]'
			});
		}

		$('.hasTip, .hasTooltip').tooltip({
			show: null,
			position: {
				my: 'left top',
				at: 'left bottom'
			},
			open: function(event, ui){
				ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, 'fast');
			},
			content: function(){
				var parts = $(this).attr('title').split('::', 2),
					title = '';

				if (parts.length == 2) {
					if (parts[0] != '') {
						title += '<div style="text-align: center; border-bottom: 1px solid #EEEEEE;">' + parts[0] + '</div>' + parts[1];
					} else {
						title += parts[1];
					}
				} else {
					title += $(this).attr('title');
				}

				return title;
			}
		});

		$('#accordion').accordion({
			collapsible: true,
			heightStyle: 'content',
			active: <?php echo ($this->form->getValue('embed_code') != '') ? 'false' : 0; ?>
		});

		$('#video_uploader').pluploadQueue({
			runtimes: 'html5,gears,flash,silverlight,browserplus,html4',
			url: 'index.php?option=com_kinoarhiv&controller=mediamanager&task=upload&format=raw&section=<?php echo $input->get('section', '', 'word'); ?>&type=<?php echo $input->get('type', '', 'word'); ?>&id=<?php echo $input->get('id', 0, 'int'); ?>&<?php echo JSession::getFormToken(); ?>=1',
			max_file_size: '<?php echo $this->params->get('upload_limit'); ?>',
			<?php if ($this->params->get('upload_chunk') == 1): ?>chunk_size: '<?php echo $this->params->get('upload_chunk_size'); ?>',<?php endif; ?>
			unique_names: false,
			filters: [
				{title: 'Video files', extensions: '<?php echo $video_ext; ?>'}
			],
			flash_swf_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.flash.swf',
			silverlight_xap_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.silverlight.xap',
			init: {
				StateChanged: function(up){
					if (up.state == plupload.STARTED) {
						// Block 'Save' and 'Close' buttons
						$('.ui-dialog-titlebar .ui-button').button('disable');
						$('#tr_save, #tr_cancel').button('disable');
					} else if (up.state == plupload.STOPPED) {
						// Unblock 'Save' and 'Close' buttons
						$('.ui-dialog-titlebar .ui-button').button('enable');
						$('#tr_save, #tr_cancel').button('enable');
					}
				}
			}
		});

		$('#subtl_uploader').pluploadQueue({
			runtimes: 'html5,gears,flash,silverlight,browserplus,html4',
			url: 'index.php?option=com_kinoarhiv&controller=mediamanager&task=upload&format=raw&section=<?php echo $input->get('section', '', 'word'); ?>&type=<?php echo $input->get('type', '', 'word'); ?>&id=<?php echo $input->get('id', 0, 'int'); ?>&<?php echo JSession::getFormToken(); ?>=1',
			max_file_size: '<?php echo $this->params->get('upload_limit'); ?>',
			<?php if ($this->params->get('upload_chunk') == 1): ?>chunk_size: '<?php echo $this->params->get('upload_chunk_size'); ?>',<?php endif; ?>
			unique_names: false,
			filters: [
				{title: 'Captions', extensions: '<?php echo $subtitle_ext; ?>'}
			],
			flash_swf_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.flash.swf',
			silverlight_xap_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.silverlight.xap',
			init: {
				StateChanged: function(up){
					if (up.state == plupload.STARTED) {
						// Block 'Save' and 'Close' buttons
						$('.ui-dialog-titlebar .ui-button').button('disable');
						$('#tr_save, #tr_cancel').button('disable');
					} else if (up.state == plupload.STOPPED) {
						// Unblock 'Save' and 'Close' buttons
						$('.ui-dialog-titlebar .ui-button').button('enable');
						$('#tr_save, #tr_cancel').button('enable');
					}
				}
			}
		});

		$('#v_sortable').sortable({
			placeholder: 'ui-state-highlight',
			cursor: 'move',
			update: function(e, ui){
				$.post('index.php?option=com_kinoarhiv&controller=mediamanager&task=saveOrderTrailerVideofile&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&format=json', $('#v_sortable input').serialize()+'&<?php echo JSession::getFormToken(); ?>=1', function(response){
					if (response.success) {
						$.each($('#v_sortable input'), function(i, el){
							$(el).val(i);
							$(el).next().find('.ord_numbering').text(i);
						});
					} else {
						showMsg('#system-message-container', response.message);
					}
				}).fail(function(xhr, status, error){
					showMsg('#system-message-container', error);
				});
			}
		});
		$('#v_sortable').disableSelection();
		$('a.file-remove').click(function(e){
			e.preventDefault();
			var _this = $(this);

			$.post($(this).attr('href'), {'<?php echo JSession::getFormToken(); ?>': 1}, function(response){
				if (response.success) {
					_this.closest('li').remove();

					$.each($('#v_sortable input'), function(i, el){
						$(el).val(i);
						$(el).next().find('.ord_numbering').text(i);
					});
				} else {
					showMsg('#system-message-container', response.message);
				}
			});
		});
	});
//]]>
</script>
<form action="index.php" method="post" style="margin: 0;" name="adminForm" id="adminForm">
	<!-- At this first hidden input we will remove autofocus -->
	<input type="hidden" autofocus="autofocus" />

	<div class="row-fluid">
		<div class="span12">
			<div class="row-fluid">
				<div class="span6">
					<fieldset class="form-horizontal">
						<?php foreach ($this->form->getFieldset('tr_edit') as $field): ?>
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
						<?php endforeach; ?>
					</fieldset>

					<div class="small red"><?php echo JText::_('COM_KA_TRAILERS_EDIT_UPLOAD_ONLY_ONE'); ?></div>
					<div class="small"><?php echo JText::sprintf('COM_KA_TRAILERS_EDIT_UPLOAD_FILENAME_CONVERT', $video_ext, $subtitle_ext); ?></div>
					<div id="accordion" class="uploader">
						<h3><?php echo JText::_('COM_KA_TRAILERS_HEADING_UPLOAD_FILES_VIDEO'); ?></h3>
						<div>
							<div id="video_uploader" class="tr-uploader">
								<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
							</div>
						</div>

						<h3><?php echo JText::_('COM_KA_TRAILERS_HEADING_UPLOAD_FILES_SUBTL'); ?></h3>
						<div id="subtl_uploader" class="tr-uploader">
							<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
						</div>
					</div>
				</div>
				<div class="span6">
					<h3><?php echo JText::_('COM_KA_TRAILERS_HEADING_UPLOAD_FILES_VIDEO'); ?><span class="btn-small hasTooltip icon-help" title="<?php echo JText::_('COM_KA_TRAILERS_HEADING_SORT_VIDEOFILES_DESC'); ?>"></span></h3>
					<ul id="v_sortable">
						<?php $files = json_decode($this->item->filename);
						if (count($files) > 0):
							foreach ($files as $key=>$item): ?>
								<li>
									<input type="hidden" name="ord[]" value="<?php echo (int)$key; ?>" />
									<div style="float: left;"><span class="ord_numbering"><?php echo (int)$key; ?></span>. <?php echo $item->src; ?></div>
									<div style="float: right;"><a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerVideofile&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file=<?php echo $item->src; ?>&id=<?php echo $this->item->movie_id; ?>&format=json" class="file-remove"><span class="icon-delete"></span></a></div>
								</li>
							<?php endforeach;
						endif; ?>
					</ul>

					<h3><?php echo JText::_('COM_KA_TRAILERS_HEADING_SUBTITLES'); ?></h3>
					<ul id="sub_sortable">
						<?php $subtitles = json_decode($this->item->_subtitles);
						if (count($subtitles) > 0):
							foreach ($subtitles as $lang_tag=>$subtl_array):
								foreach ($subtl_array as $key=>$val): ?>
									<li>
										<input type="hidden" name="ord[]" value="<?php echo (int)$key; ?>" />
										<div style="float: left;"><span class="ord_numbering"><?php echo (int)$key; ?></span>. <?php echo $val->file; ?></div>
										<div style="float: right;"><a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerVideofile&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file=<?php echo $val->file; ?>&id=<?php echo $this->item->movie_id; ?>&format=json" class="file-remove"><span class="icon-delete"></span></a></div>
									</li>
								<?php endforeach;
							endforeach;
						endif; ?>
					</ul>

					<h3><?php echo JText::_('COM_KA_TRAILERS_HEADING_CHAPTERS'); ?></h3>
					<ul id="chap_sortable">
						<?php $chapters = json_decode($this->item->_chapters);
						if (count($chapters) > 0):
							foreach ($chapters as $chap_array):
								foreach ($subtl_array as $key=>$val): ?>
									<li>
										<input type="hidden" name="ord[]" value="<?php echo (int)$key; ?>" />
										<div style="float: left;"><span class="ord_numbering"><?php echo (int)$key; ?></span>. <?php echo $val->file; ?></div>
										<div style="float: right;"><a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerVideofile&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file=<?php echo $val->file; ?>&id=<?php echo $this->item->movie_id; ?>&format=json" class="file-remove"><span class="icon-delete"></span></a></div>
									</li>
								<?php endforeach;
							endforeach;
						endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="option" value="com_kinoarhiv" />
	<input type="hidden" name="controller" value="mediamanager" />
	<input type="hidden" name="task" value="upload" />
	<input type="hidden" name="section" value="movie" />
	<input type="hidden" name="type" value="trailers" />
	<input type="hidden" name="id" value="<?php echo !empty($this->item->movie_id) ? $this->item->movie_id : 0; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
