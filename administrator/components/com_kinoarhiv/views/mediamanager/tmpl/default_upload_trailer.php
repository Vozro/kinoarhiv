<?php defined('_JEXEC') or die;
$input = JFactory::getApplication()->input;
$section = $input->get('section', '', 'word');
$type = $input->get('type', '', 'word');
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
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_kinoarhiv/assets/js/jquery.colorbox-min.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_kinoarhiv/assets/js/i18n/colorbox/jquery.colorbox-<?php echo substr(JFactory::getLanguage()->getTag(), 0, 2); ?>.js" type="text/javascript"></script>
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

		function blockUI(action) {
			if (action == 'show') {
				$('<div class="ui-widget-overlay" id="blockui" style="z-index: 10001;"></div>').appendTo('body').show();
			} else {
				$('#blockui').remove();
			}
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
			url: 'index.php?option=com_kinoarhiv&controller=mediamanager&task=upload&format=raw&section=<?php echo $input->get('section', '', 'word'); ?>&type=<?php echo $input->get('type', '', 'word'); ?>&upload=video&id=<?php echo $input->get('id', 0, 'int'); ?>&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>',
			multipart_params: {
				'<?php echo JSession::getFormToken(); ?>': 1
			},
			max_file_size: '<?php echo $this->params->get('upload_limit'); ?>',
			<?php if ($this->params->get('upload_chunk') == 1): ?>chunk_size: '<?php echo $this->params->get('upload_chunk_size'); ?>',<?php endif; ?>
			unique_names: false,
			filters: [
				{title: 'Video files', extensions: '<?php echo $this->params->get('upload_mime_video'); ?>'}
			],
			flash_swf_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.flash.swf',
			silverlight_xap_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.silverlight.xap',
			preinit: {
				init: function(up, info){
					$('#video_uploader').find('.plupload_buttons a:last').after('<a class="plupload_button plupload_clear_all" href="#"><?php echo JText::_('JCLEAR'); ?></a>');
					$('#video_uploader .plupload_clear_all').click(function(e){
						e.preventDefault();
						up.splice();
						$.each(up.files, function(i, file){
							up.removeFile(file);
						});
					});
				},
				UploadComplete: function(up, files){
					$('#video_uploader').find('.plupload_buttons').show();
					$('.t-video').trigger('click');
				}
			},
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
			url: 'index.php?option=com_kinoarhiv&controller=mediamanager&task=upload&format=raw&section=<?php echo $input->get('section', '', 'word'); ?>&type=<?php echo $input->get('type', '', 'word'); ?>&upload=subtitles&id=<?php echo $input->get('id', 0, 'int'); ?>&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>',
			multipart_params: {
				'<?php echo JSession::getFormToken(); ?>': 1
			},
			max_file_size: '<?php echo $this->params->get('upload_limit'); ?>',
			filters: [{title: 'Subtitles', extensions: '<?php echo $this->params->get('upload_mime_subtitles'); ?>'}],
			flash_swf_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.flash.swf',
			silverlight_xap_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.silverlight.xap',
			unique_names: false,
			preinit: {
				init: function(up, info){
					$('#subtl_uploader').find('.plupload_buttons a:last').after('<a class="plupload_button plupload_clear_all" href="#"><?php echo JText::_('JCLEAR'); ?></a>');
					$('#subtl_uploader .plupload_clear_all').click(function(e){
						e.preventDefault();
						up.splice();
						$.each(up.files, function(i, file){
							up.removeFile(file);
						});
					});
				},
				UploadComplete: function(up, files){
					$('#subtl_uploader').find('.plupload_buttons').show();
					$('.t-subtitles').trigger('click');
				}
			},
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

		$('#chap_uploader').pluploadQueue({
			runtimes: 'html5,gears,flash,silverlight,browserplus,html4',
			url: 'index.php?option=com_kinoarhiv&controller=mediamanager&task=upload&format=raw&section=<?php echo $input->get('section', '', 'word'); ?>&type=<?php echo $input->get('type', '', 'word'); ?>&upload=chapters&id=<?php echo $input->get('id', 0, 'int'); ?>&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>',
			multipart_params: {
				'<?php echo JSession::getFormToken(); ?>': 1
			},
			max_file_size: '<?php echo $this->params->get('upload_limit'); ?>',
			unique_names: false,
			filters: [{title: 'Chapters', extensions: '<?php echo $this->params->get('upload_mime_chapters'); ?>'}],
			flash_swf_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.flash.swf',
			silverlight_xap_url: '<?php echo JURI::base(); ?>components/com_kinoarhiv/assets/js/mediamanager/plupload.silverlight.xap',
			preinit: {
				init: function(up, info){
					$('#chap_uploader').find('.plupload_buttons a:last').after('<a class="plupload_button plupload_clear_all" href="#"><?php echo JText::_('JCLEAR'); ?></a>');
					$('#chap_uploader .plupload_clear_all').click(function(e){
						e.preventDefault();
						up.splice();
						$.each(up.files, function(i, file){
							up.removeFile(file);
						});
					});
				},
				UploadComplete: function(up, files){
					$('#chap_uploader').find('.plupload_buttons').show();
					$('.t-chapters').trigger('click');
				}
			},
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
						showMsg('#v_sortable', '<?php echo JText::_('COM_KA_SAVED'); ?>');
					} else {
						showMsg('#v_sortable', response.message);
					}
				}).fail(function(xhr, status, error){
					showMsg('#system-message-container', error);
				});
			}
		});
		$('#sub_sortable').sortable({
			placeholder: 'ui-state-highlight',
			cursor: 'move',
			update: function(e, ui){
				$.post('index.php?option=com_kinoarhiv&controller=mediamanager&task=saveOrderTrailerSubtitlefile&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&format=json', $('#sub_sortable input').serialize()+'&<?php echo JSession::getFormToken(); ?>=1', function(response){
					if (response.success) {
						$.each($('#sub_sortable input:hidden'), function(n, el){
							$(el).val(n);
							$(el).next().find('.ord_numbering').text(n);
						});
						showMsg('#sub_sortable', '<?php echo JText::_('COM_KA_SAVED'); ?>');
					} else {
						showMsg('#sub_sortable', response.message);
					}
				}).fail(function(xhr, status, error){
					showMsg('#system-message-container', error);
				});
			}
		});
		$('#v_sortable, #sub_sortable').disableSelection();

		$('#sub_sortable').on('click', 'input:radio', function(){
			var _this = $(this);

			$.post('index.php?option=com_kinoarhiv&controller=mediamanager&task=saveDefaultTrailerSubtitlefile&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&format=json', '&default='+_this.closest('li').find(':hidden').eq(0).val()+'&<?php echo JSession::getFormToken(); ?>=1', function(response){
				if (response.success) {
					showMsg('#sub_sortable', '<?php echo JText::_('COM_KA_SAVED'); ?>');
				} else {
					showMsg('#sub_sortable', response.message);
				}
			}).fail(function(xhr, status, error){
				showMsg('#system-message-container', error);
			});
		});

		$('#filelist').on('click', 'a.cmd-file-remove', function(e){
			e.preventDefault();
			var _this = $(this);

			if (_this.hasClass('all')) {
				if (!confirm('<?php echo JText::_('COM_KA_DELETE_ALL'); ?>')) {
					return false;
				}
			}

			blockUI('show');
			$.post($(this).attr('href'), {'<?php echo JSession::getFormToken(); ?>': 1}, function(response){
				if (response.success) {
					if (_this.hasClass('video')) {
						_this.closest('li').remove();

						$.each(_this.closest('ul').find('input:hidden'), function(i, el){
							$(el).val(i);
							$(el).next().find('.ord_numbering').text(i);
						});
					} else if (_this.hasClass('subtitle')) {
						if (_this.hasClass('all')) {
							$('#sub_sortable').children('li').remove();
						} else {
							_this.closest('li').remove();

							$.each(_this.closest('ul').find('input:hidden'), function(i, el){
								$(el).val(i);
								$(el).next().find('.ord_numbering').text(i);
							});
						}
					} else if (_this.hasClass('scrimage')) {
						
					}

					showMsg(_this.closest('ul'), '<?php echo JText::_('COM_KA_FILE_DELETED_SUCCESS'); ?>');
				} else {
					showMsg(_this.closest('ul'), response.message);
				}
				blockUI();
			}).fail(function(xhr, status, error){
				showMsg('#system-message-container', '<strong>'+status+'</strong>: '+error);
				blockUI();
			});
		});

		$('#sub_sortable').on('click', 'a.lang-edit', function(e){
			e.preventDefault();
			var _this = $(this);
			var dlg = $('<div style="display: none;" class="dialog" title="<?php echo JText::_('COM_KA_TRAILERS_HEADING_SUBTITLES_LANG_EDIT'); ?>"><p class="ajax-loading"></p></div>').appendTo('body');

			dlg.dialog({
				buttons: {
					'<?php echo JText::_('JAPPLY'); ?>': function(){
						$.post('index.php?option=com_kinoarhiv&controller=mediamanager&task=saveSubtitles&trailer_id=<?php echo $input->get('item_id', 0, 'int'); ?>&format=raw', {
							'subtitle_id': _this.closest('li').find('input').eq(0).val(),
							'language': $('#subtl_edit_form #jform_language_subtl option:selected').val(),
							'desc': $('#subtl_edit_form #jform_desc').val(),
							'default': $('#subtl_edit_form #jform_default option:selected').val(),
							'movie_id': <?php echo $this->item->movie_id; ?>,
							'<?php echo JSession::getFormToken(); ?>': 1
						}, function(response){
							if (response) {
								$('.t-subtitles').trigger('click');
							} else {
								showMsg('#subtl_edit_form .message', '<?php echo JText::_('JERROR'); ?>');
							}
						}).fail(function(xhr, status, error){
							showMsg('#subtl_edit_form .message', status+': '+error);
						});
					},
					'<?php echo JText::_('JCANCEL'); ?>': function(){
						dlg.remove();
					}
				},
				resizable: false,
				modal: true,
				height: 300,
				width: 450,
				close: function(e, ui){
					dlg.remove();
				}
			});

			dlg.load(_this.attr('href'));
		});

		$('.cmd-refresh-filelist').click(function(e){
			e.preventDefault();
			var _this = $(this);

			blockUI('show');
			$('body').aurora.destroy({indexes:'all'});
			$.get(_this.attr('href'), function(response){
				if (_this.hasClass('t-video')) {
					if (typeof response != 'object') {
						showMsg('#v_sortable', response);
						blockUI('hide');
						return false;
					}

					_this.closest('h3').next('ul').children('li').remove();

					var html = '';
					$.each(response, function(k, object){
						html += '<li>'
							+ '<input type="hidden" name="ord[]" value="'+ k +'" />'
							+ '<div style="float: left;"><span class="ord_numbering">'+ k +'</span>. '+ object.src +'</div>'
							+ '<div style="float: right;"><a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerFiles&type=video&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file='+ object.src +'&id=<?php echo $this->item->movie_id; ?>&format=json" class="cmd-file-remove video"><span class="icon-delete"></span></a></div>'
						+ '</li>';
					});

					$('#v_sortable').append(html);
					blockUI('hide');
				} else if (_this.hasClass('t-subtitles')) {
					if (typeof response != 'object') {
						showMsg('#sub_sortable', response);
						blockUI('hide');
						return false;
					}

					_this.closest('h3').next('ul').children('li').remove();

					var html = '';
					$.each(response, function(i, obj){
						var checked = obj.default ? ' checked="checked"' : '';

						html += '<li>'
							+ '<input type="hidden" name="cord[]" value="'+ i +'" />'
							+ '<div style="float: left;"><span class="ord_numbering">'+ i +'</span>. '+ obj.file +' ('+ obj.lang_code +', '+ obj.lang +' <a href="index.php?option=com_kinoarhiv&task=loadTemplate&template=upload_subtitles_lang_edit&model=mediamanager&view=mediamanager&format=raw&trailer_id=<?php echo $this->item->id; ?>&subtitle_id='+ i +'" class="lang-edit"><img src="components/com_kinoarhiv/assets/images/icons/table_edit.png" border="0" /></a>)</div>'
								+ '<div style="float: right;"><input type="radio" name="sub_default" title="<?php echo JText::_('JDEFAULT'); ?>" class="hasTooltip" style="margin: 0px 4px 4px 0px;" autocomplete="off"'+ checked +' /> <a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerFiles&type=subtitle&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file='+ obj.file +'&id=<?php echo $this->item->movie_id; ?>&format=json" class="cmd-file-remove subtitle"><span class="icon-delete"></span></a></div>'
						+ '</li>';
					});

					$('#sub_sortable').append(html);
					blockUI('hide');
				} else if (_this.hasClass('t-chapters')) {
					if (!response.file) {
						if (typeof response != 'object') {
							showMsg('#chap_sortable', response);
						}
						blockUI('hide');
						return false;
					}

					_this.closest('h3').next('ul').children('li').remove();

					var html = '<li>'
						+ '<div style="float: left;">'+response.file+'</div>'
						+ '<div style="float: right;"><a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerFiles&type=chapter&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file='+response.file+'&id=<?php echo $this->item->movie_id; ?>&format=json" class="cmd-file-remove chapter"><span class="icon-delete"></span></a></div>'
					+ '</li>';

					$('#chap_sortable').append(html);
					blockUI('hide');
				}
			}).fail(function(xhr, status, error){
				showMsg(_this.closest('h3').find('ul'), status+': '+error);
				blockUI('hide');
			});
		});

		$('a.tooltip-img').colorbox({ maxHeight: '95%', maxWidth: '95%', fixed: true });

		$('a.file-create-scr').click(function(e){
			e.preventDefault();
			var _this = $(this);
			var dlg = $('<div style="display: none;" class="dialog" title="<?php echo JText::_('COM_KA_TRAILERS_VIDEO_SCREENSHOT_CREATE_TITLE'); ?>"><p><label for="time"><?php echo JText::_('COM_KA_TRAILERS_VIDEO_SCREENSHOT_CREATE_TIME_DESC'); ?></label><br /><input type="text" name="time" id="time" value="00:02:00.000" required="required" size="16" maxlength="12" placeholder="00:00:00.000" /></p></div>').appendTo('body');

			dlg.dialog({
				buttons: {
					'<?php echo JText::_('JTOOLBAR_NEW'); ?>': function(){
						blockUI('show');
						$.post(_this.attr('href'), {'time': $('#time').val(), '<?php echo JSession::getFormToken(); ?>': 1}, function(response){
							dlg.dialog('option', {
								height: parseInt($(window).height() - 100),
								width: parseInt($(window).width() - 100)
							});
							
							$('p', dlg).html(response);
							blockUI();
						}).fail(function(xhr, status, error){
							showMsg('#system-message-container', '<strong>'+status+'</strong>: '+error);
							blockUI();
							dlg.remove();
						});
					},
					'<?php echo JText::_('JCANCEL'); ?>': function(){
						dlg.remove();
					}
				},
				modal: true,
				height: 300,
				width: 450,
				close: function(e, ui){
					dlg.remove();
				}
			});
		});
	});
//]]>
</script>
<form action="index.php" method="post" style="margin: 0;" name="adminForm" id="adminForm" class="admform-upload-trailers">
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
					<div class="small"><?php echo JText::sprintf('COM_KA_TRAILERS_EDIT_UPLOAD_FILENAME_CONVERT', $this->params->get('upload_mime_video'), $this->params->get('upload_mime_subtitles'), $this->params->get('upload_mime_chapters')); ?></div>
					<div id="accordion" class="uploader">
						<h3><?php echo JText::_('COM_KA_TRAILERS_HEADING_UPLOAD_FILES_VIDEO'); ?></h3>
						<div>
							<div id="video_uploader" class="tr-uploader">
								<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
							</div>
						</div>

						<h3><?php echo JText::_('COM_KA_TRAILERS_HEADING_UPLOAD_FILES_SUBTL'); ?></h3>
						<div><span class="small red" style="margin: 0 5px;"><?php echo JText::_('COM_KA_TRAILERS_HEADING_SUBTITLES_WARN'); ?></span>
							<div id="subtl_uploader" class="tr-uploader">
								<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
							</div>
						</div>

						<h3><?php echo JText::_('COM_KA_TRAILERS_HEADING_UPLOAD_FILES_CHAPTERS'); ?></h3>
						<div id="chap_uploader" class="tr-uploader">
							<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
						</div>
					</div>
				</div>
				<div class="span6" id="filelist">
					<h3 class="ui-widget ui-widget-content"><?php echo JText::_('COM_KA_TRAILERS_HEADING_UPLOAD_FILES_VIDEO'); ?><span class="btn-small hasTooltip icon-help" title="<?php echo JText::_('COM_KA_TRAILERS_HEADING_SORT_VIDEOFILES_DESC'); ?>"></span>
						<a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=refresh_filelist&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&type=video&format=json" class="cmd-refresh-filelist t-video" title="<?php echo JText::_('JTOOLBAR_REFRESH'); ?>"><img src="components/com_kinoarhiv/assets/images/icons/arrow_refresh_small.png" border="0" /></a>
					</h3>
					<ul id="v_sortable">
						<?php $files = json_decode($this->item->filename);
						if (count($files) > 0):
							foreach ($files as $key=>$item): ?>
								<li>
									<input type="hidden" name="ord[]" value="<?php echo (int)$key; ?>" />
									<div style="float: left;"><span class="ord_numbering"><?php echo (int)$key; ?></span>. <?php echo $item->src; ?></div>
									<div style="float: right;"><a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerFiles&type=video&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file=<?php echo $item->src; ?>&id=<?php echo $this->item->movie_id; ?>&format=json" class="cmd-file-remove video"><span class="icon-delete"></span></a></div>
								</li>
							<?php endforeach;
						endif; ?>
					</ul>
					<div class="video_screenshot">
						<div style="float: left;">
							<?php if (file_exists($this->item->screenshot_path)): ?>
								<a href="<?php echo $this->item->screenshot_path_www; ?>" class="tooltip-img" id="screenshot_file"><?php echo $this->item->screenshot; ?></a>
							<?php endif; ?>
						</div>
						<div style="float: right;">
							<a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=create_screenshot&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&id=<?php echo $this->item->movie_id; ?>&format=raw" class="file-create-scr hasTip" title="<?php echo JText::_('COM_KA_TRAILERS_VIDEO_SCREENSHOT_CREATE_TITLE'); ?>"><span class="icon-refresh"></span></a>
							<a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerFiles&type=image&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file=<?php echo $this->item->screenshot; ?>&id=<?php echo $this->item->movie_id; ?>&format=json" class="cmd-file-remove scrimage"><span class="icon-delete"></span></a>
						</div>
					</div><br />

					<h3 class="ui-widget ui-widget-content"><?php echo JText::_('COM_KA_TRAILERS_HEADING_SUBTITLES'); ?><span class="btn-small hasTooltip icon-help" title="<?php echo JText::_('COM_KA_TRAILERS_HEADING_SORT_VIDEOFILES_DESC'); ?>"></span>
						<a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerFiles&type=subtitles&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&all=1&id=<?php echo $input->get('id', 0, 'int'); ?>&format=json" class="cmd-file-remove all subtitle" title="<?php echo JText::_('COM_KA_DELETE_ALL'); ?>"><img src="components/com_kinoarhiv/assets/images/icons/mediamanager/delete.png" border="0" /></a>
						<a href="index.php?option=com_kinoarhiv&task=ajaxData&element=trailer_files&id=<?php echo $input->get('item_id', 0, 'int'); ?>&type=subtitles&format=json" class="cmd-refresh-filelist t-subtitles" title="<?php echo JText::_('JTOOLBAR_REFRESH'); ?>"><img src="components/com_kinoarhiv/assets/images/icons/arrow_refresh_small.png" border="0" /></a>
					</h3>
					<ul id="sub_sortable">
						<?php $subtitles = json_decode($this->item->_subtitles);
						if (count($subtitles) > 0):
							foreach ($subtitles as $k=>$sub_data): ?>
								<li>
									<input type="hidden" name="cord[]" value="<?php echo (int)$k; ?>" />
									<div style="float: left;"><span class="ord_numbering"><?php echo $k; ?></span>. <?php echo $sub_data->file; ?> (<?php echo $sub_data->lang_code; ?>, <?php echo $sub_data->lang; ?> <a href="index.php?option=com_kinoarhiv&task=loadTemplate&template=upload_subtitles_lang_edit&model=mediamanager&view=mediamanager&format=raw&trailer_id=<?php echo $this->item->id; ?>&subtitle_id=<?php echo (int)$k; ?>" class="lang-edit"><img src="components/com_kinoarhiv/assets/images/icons/table_edit.png" border="0" /></a>)</div>
									<div style="float: right;"><input type="radio" name="sub_default" title="<?php echo JText::_('JDEFAULT'); ?>" class="hasTooltip" style="margin: 0px 4px 4px 0px;" autocomplete="off"<?php echo $sub_data->default ? ' checked="checked"' : ''; ?> /> <a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerFiles&type=subtitle&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file=<?php echo $sub_data->file; ?>&id=<?php echo $this->item->movie_id; ?>&format=json" class="cmd-file-remove subtitle"><span class="icon-delete"></span></a></div>
								</li>
							<?php endforeach;
						endif; ?>
					</ul>

					<h3 class="ui-widget ui-widget-content"><?php echo JText::_('COM_KA_TRAILERS_HEADING_CHAPTERS'); ?>
						<a href="index.php?option=com_kinoarhiv&task=ajaxData&element=trailer_files&id=<?php echo $input->get('item_id', 0, 'int'); ?>&type=chapters&format=json" class="cmd-refresh-filelist t-chapters" title="<?php echo JText::_('JTOOLBAR_REFRESH'); ?>"><img src="components/com_kinoarhiv/assets/images/icons/arrow_refresh_small.png" border="0" /></a>
					</h3>
					<ul id="chap_sortable">
						<?php $chapters = json_decode($this->item->_chapters);
						if (count($chapters) > 0):
							foreach ($chapters as $chapter): ?>
								<li>
									<div style="float: left;"><?php echo $chapter; ?></div>
									<div style="float: right;"><a href="index.php?option=com_kinoarhiv&controller=mediamanager&task=removeTrailerFiles&type=chapter&item_id=<?php echo $input->get('item_id', 0, 'int'); ?>&file=<?php echo $chapter; ?>&id=<?php echo $this->item->movie_id; ?>&format=json" class="cmd-file-remove chapter"><span class="icon-delete"></span></a></div>
								</li>
							<?php endforeach;
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
