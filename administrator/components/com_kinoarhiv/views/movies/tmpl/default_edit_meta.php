<?php defined('_JEXEC') or die; ?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#form_tags').select2({
			placeholder: '<?php echo JText::_('COM_KA_SEARCH_AJAX'); ?>',
			quietMillis: 100,
			minimumInputLength: 1,
			maximumSelectionSize: 10,
			multiple: true,
			ajax: {
				cache: true,
				url: 'index.php?option=com_kinoarhiv&task=ajaxData&element=tags&format=json',
				data: function(term, page){
					return { term: term, showAll: 0 }
				},
				results: function(data, page){
					return { results: data };
				}
			},
			initSelection: function(element, callback){
				var data = <?php echo json_encode($this->items['tags']['data']); ?>;
				callback(data);
			},
			formatResult: function(data){
				return data.title;
			},
			formatSelection: function(data, container){
				return data.title;
			},
			escapeMarkup: function(m) { return m; }
		}).select2('container').find('ul.select2-choices').sortable({
			containment: 'parent',
			start: function() { $("#form_tags").select2('onSortStart'); },
			update: function() { $("#form_tags").select2('onSortEnd'); }
		});
	});
</script>
<div class="row-fluid">
	<div class="span6">
		<fieldset class="form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('metakey', $this->form_group); ?></div>
				<div class="controls"><?php echo $this->form->getInput('metakey', $this->form_group); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('metadesc', $this->form_group); ?></div>
				<div class="controls"><?php echo $this->form->getInput('metadesc', $this->form_group); ?></div>
			</div>
		</fieldset>
	</div>
	<div class="span6">
		<fieldset class="form-horizontal">
			<div class="control-group">
				<div class="control-label">
					<label id="form_tags-lbl" for="form_tags" class="hasTip" title="<?php echo JText::_('JTAG_DESC'); ?>"><?php echo JText::_('JTAG'); ?></label>
				</div>
				<div class="controls">
					<input type="hidden" name="form[tags]" id="form_tags" value="<?php echo implode(',', $this->items['tags']['ids']); ?>" class="span11 autocomplete" data-ac-type="tags" />
					<span class="rel-link"><a href="<?php echo JRoute::_('index.php?option=com_tags'); ?>" class="hasTip" title="::<?php echo JText::_('COM_KA_COM_TAGS'); ?>" target="_blank"><img src="components/com_kinoarhiv/assets/images/icons/tag_edit.png" border="0" /></a></span>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('robots', $this->form_group); ?></div>
				<div class="controls"><?php echo $this->form->getInput('robots', $this->form_group); ?></div>
			</div>
		</fieldset>
	</div>
</div>
