<?php defined('_JEXEC') or die;

class KinoarhivViewName extends JViewLegacy {
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $filters = null;
	protected $tab;

	public function display($tpl = null) {
		$app = JFactory::getApplication();
		$this->tab = $app->input->get('tab', '', 'cmd');
		$this->itemid = $app->input->get('Itemid');

		switch ($this->tab) {
			case 'wallpp': $this->wallpp(); break;
			case 'photo': $this->photo(); break;
			case 'awards': $this->awards(); break;
			default: $this->info($tpl); break;
		}
	}

	/**
	 * Method to get and show person info.
	 */
	protected function info($tpl) {
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		$item = $this->get('Data');

		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$params = $app->getParams('com_kinoarhiv');

		// Prepare the data
		// Build title string
		$item->title = '';
		if ($item->name != '') {
			$item->title .= $item->name;
		}
		if ($item->name != '' && $item->latin_name != '') {
			$item->title .= ' / ';
		}
		if ($item->latin_name != '') {
			$item->title .= $item->latin_name;
		}

		// Build date string
		$item->dates = '';
		if ($item->date_of_birth != '0000') {
			$item->dates .= ' ('.$item->date_of_birth;
		}
		if ($item->date_of_death != '0000') {
			$item->dates .= ' - '.$item->date_of_death;
		}
		$item->dates .= ')';

		if (empty($item->filename)) {
			if ($item->gender == 0) { // Female
				$no_cover = 'no_name_cover_f';
			} else {
				$no_cover = 'no_name_cover_m';
			}
			$item->poster = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/'.$no_cover.'.png';
			$item->y_poster = '';
		} else {
			$item->poster = JURI::base().$params->get('media_actor_photo_root_www').'/'.JString::substr($item->alias, 0, 1).'/'.$item->id.'/photo/thumb_'.$item->filename;
			$item->y_poster = ' y-poster';
		}

		$lc_offset = JFactory::getConfig()->get('offset');
		$date_of_birth_1 = new DateTime($item->date_of_birth_raw.' '.date('H:i:s'), new DateTimeZone($lc_offset));
		$date_of_birth_2 = new DateTime('now', new DateTimeZone($lc_offset));
		$_interval = $date_of_birth_1->diff($date_of_birth_2);
		$interval = ($_interval->y > 100) ? substr($_interval->y, -2) : $_interval->y;
		$str_age = '';

		if ($interval >= 5 && $interval <= 14) {
			$str_age = JText::_('COM_KA_NAMES_AGE_01');
		} else {
			$interval = substr($_interval->y, -1);

			if ($interval == 0 || ($interval >= 5 && $interval <= 9)) $str_age = JText::_('COM_KA_NAMES_AGE_01');
			if ($interval == 1) $str_age = JText::_('COM_KA_NAMES_AGE_02');
			if ($interval >= 2 && $interval <= 4) $str_age = JText::_('COM_KA_NAMES_AGE_03');
		}
		$item->date_of_birth_interval_str = $_interval->y.' '.$str_age;

		if (!empty($item->desc)) {
			$item->desc = str_replace("\n", "<br />", $item->desc);
		}

		$this->params = &$params;
		$this->item = &$item;
		$this->user = &$user;

		$this->_prepareDocument();
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->title, JRoute::_('index.php?option=com_kinoarhiv&view=name&id='.$this->item->id.'&Itemid='.$this->itemid));

		parent::display($tpl);
	}

	protected function wallpp() {
		$app = JFactory::getApplication();

		$item = $this->get('NameData');
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$params = $app->getParams('com_kinoarhiv');

		// Build title string
		$item->title = '';
		if ($item->name != '') {
			$item->title .= $item->name;
		}
		if ($item->name != '' && $item->latin_name != '') {
			$item->title .= ' / ';
		}
		if ($item->latin_name != '') {
			$item->title .= $item->latin_name;
		}

		// Check for files
		foreach ($items as $key=>$_item) {
			$file_path = $params->get('media_actor_wallpapers_root_www').DIRECTORY_SEPARATOR.JString::substr($item->alias, 0, 1).DIRECTORY_SEPARATOR.$item->id.DIRECTORY_SEPARATOR.'wallpapers'.DIRECTORY_SEPARATOR;

			if (!file_exists($file_path.$_item->filename)) {
				$items[$key]->image = 'javascript:void(0);';
				$items[$key]->th_image = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/no_wp.png';
			} else {
				$items[$key]->image = JURI::base().$params->get('media_actor_wallpapers_root_www').'/'.JString::substr($item->alias, 0, 1).'/'.$item->id.'/wallpapers/'.$_item->filename;
				$size = @getimagesize($file_path.DIRECTORY_SEPARATOR.'thumb_'.$_item->filename);

				if ($size !== false) {
					$items[$key]->th_image = JURI::base().$params->get('media_actor_wallpapers_root_www').'/'.JString::substr($item->alias, 0, 1).'/'.$item->id.'/wallpapers/thumb_'.$_item->filename;
				} else {
					$items[$key]->th_image = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/no_wp.png';
				}
			}
		}

		$this->params = &$params;
		$this->item = &$item;
		$this->items = &$items;
		$this->filters = $this->getDimensionList();
		$this->pagination = &$pagination;

		$this->_prepareDocument();
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->title, JRoute::_('index.php?option=com_kinoarhiv&view=name&id='.$this->item->id.'&Itemid='.$this->itemid));
		$pathway->addItem(JText::_('COM_KA_MOVIE_TAB_WALLPP'), JRoute::_('index.php?option=com_kinoarhiv&view=name&tab=wallpp&id='.$this->item->id.'&Itemid='.$this->itemid));

		parent::display('wallpp');
	}

	/*protected function posters() {
		$app = JFactory::getApplication();

		$item = $this->get('MovieData');
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$params = $app->getParams('com_kinoarhiv');

		if ($item->year != '0000') {
			$item->year_str = '&nbsp;('.$item->year.')';
		}

		// Check for files
		foreach ($items as $key=>$_item) {
			$file_path = $params->get('media_posters_root').DIRECTORY_SEPARATOR.JString::substr($item->alias, 0, 1).DIRECTORY_SEPARATOR.$item->id.DIRECTORY_SEPARATOR.'posters'.DIRECTORY_SEPARATOR;

			if (!file_exists($file_path.$_item->filename)) {
				$items[$key]->image = 'javascript:void(0);';
				$items[$key]->th_image = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/no_movie_cover.png';
			} else {
				$items[$key]->image = JURI::base().$params->get('media_posters_root').'/'.JString::substr($item->alias, 0, 1).'/'.$item->id.'/posters/'.$_item->filename;
				$size = @getimagesize($file_path.DIRECTORY_SEPARATOR.'thumb_'.$_item->filename);

				if ($size !== false) {
					$items[$key]->th_image = JURI::base().$params->get('media_posters_root').'/'.JString::substr($item->alias, 0, 1).'/'.$item->id.'/posters/thumb_'.$_item->filename;
				} else {
					$items[$key]->th_image = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/no_movie_cover.png';
				}
			}
		}

		$this->params = &$params;
		$this->item = &$item;
		$this->items = &$items;
		$this->pagination = &$pagination;

		$this->_prepareDocument();
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->title, JRoute::_('index.php?option=com_kinoarhiv&view=movie&id='.$this->item->id.'&Itemid='.$this->itemid));
		$pathway->addItem(JText::_('COM_KA_MOVIE_TAB_POSTERS'), JRoute::_('index.php?option=com_kinoarhiv&view=movie&tab=posters&id='.$this->item->id.'&Itemid='.$this->itemid));

		parent::display('posters');
	}*/

	protected function photo() {
		$app = JFactory::getApplication();

		$item = $this->get('NameData');
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$params = $app->getParams('com_kinoarhiv');

		// Build title string
		$item->title = '';
		if ($item->name != '') {
			$item->title .= $item->name;
		}
		if ($item->name != '' && $item->latin_name != '') {
			$item->title .= ' / ';
		}
		if ($item->latin_name != '') {
			$item->title .= $item->latin_name;
		}

		// Check for files
		foreach ($items as $key=>$_item) {
			$file_path = $params->get('media_actor_photo_root_www').DIRECTORY_SEPARATOR.JString::substr($item->alias, 0, 1).DIRECTORY_SEPARATOR.$item->id.DIRECTORY_SEPARATOR.'photo'.DIRECTORY_SEPARATOR;

			if (!file_exists($file_path.$_item->filename)) {
				$items[$key]->image = 'javascript:void(0);';
				if ($_item->gender == 1) {
					$items[$key]->th_image = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/no_name_cover_m.png';
				} else {
					$items[$key]->th_image = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/no_name_cover_f.png';
				}
			} else {
				$items[$key]->image = JURI::base().$params->get('media_actor_photo_root_www').'/'.JString::substr($item->alias, 0, 1).'/'.$item->id.'/photo/'.$_item->filename;
				$size = @getimagesize($file_path.DIRECTORY_SEPARATOR.'thumb_'.$_item->filename);

				if ($size !== false) {
					$items[$key]->th_image = JURI::base().$params->get('media_actor_photo_root_www').'/'.JString::substr($item->alias, 0, 1).'/'.$item->id.'/photo/thumb_'.$_item->filename;
				} else {
					if ($_item->gender == 1) {
						$items[$key]->th_image = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/no_name_cover_m.png';
					} else {
						$items[$key]->th_image = JURI::base().'components/com_kinoarhiv/assets/themes/component/'.$params->get('ka_theme').'/images/no_name_cover_f.png';
					}
				}
			}
		}

		$this->params = &$params;
		$this->item = &$item;
		$this->items = &$items;
		$this->pagination = &$pagination;

		$this->_prepareDocument();
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->title, JRoute::_('index.php?option=com_kinoarhiv&view=name&id='.$this->item->id.'&Itemid='.$this->itemid));
		$pathway->addItem(JText::_('COM_KA_NAMES_TAB_PHOTO'), JRoute::_('index.php?option=com_kinoarhiv&view=name&tab=posters&id='.$this->item->id.'&Itemid='.$this->itemid));

		parent::display('photo');
	}

	protected function awards() {
		$app = JFactory::getApplication();

		$items = $this->get('Awards');

		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$params = $app->getParams('com_kinoarhiv');

		// Prepare the data
		// Build title string
		$items->title = '';
		if ($items->name != '') {
			$items->title .= $items->name;
		}
		if ($items->name != '' && $items->latin_name != '') {
			$items->title .= ' / ';
		}
		if ($items->latin_name != '') {
			$items->title .= $items->latin_name;
		}

		$this->params = &$params;
		$this->item = &$items;

		$this->_prepareDocument();
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->title, JRoute::_('index.php?option=com_kinoarhiv&view=name&id='.$this->item->id.'&Itemid='.$this->itemid));
		$pathway->addItem(JText::_('COM_KA_NAMES_TAB_AWARDS'), JRoute::_('index.php?option=com_kinoarhiv&view=name&tab=awards&id='.$this->item->id.'&Itemid='.$this->itemid));

		parent::display('awards');
	}

	protected function getDimensionList() {
		$app = JFactory::getApplication();
		$active = $app->input->get('dim_filter', '0', 'string');
		$dimensions = $this->get('DimensionFilters');
		array_push($dimensions, array('width'=>'0', 'title'=>JText::_('COM_KA_FILTERS_DIMENSION_NOSORT')));

		// Build select
		$list = '<label for="dim_filter">'.JText::_('COM_KA_FILTERS_DIMENSION').'</label>
		<select name="dim_filter" id="dim_filter" class="inputbox" onchange="this.form.submit()" autocomplete="off">';
			foreach ($dimensions as $dimension) {
				$selected = ($dimension['width'] == $active) ? ' selected="selected"' : '';
				$list .= '<option value="'.$dimension['width'].'"'.$selected.'>'.$dimension['title'].'</option>';
			}
		$list .= '</select>';

		return array('dimensions.list' => $list);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$pathway = $app->getPathway();

		// Create a new pathway object
		$path = (object)array(
			'name' => JText::_('COM_KA_PERSONS'),
			'link' => 'index.php?option=com_kinoarhiv&view=names&Itemid='.$this->itemid
		);

		$pathway->setPathway(array($path));
		$this->document->setTitle($this->item->title);

		if ($this->item->metadesc != '') {
			$this->document->setDescription($this->item->metadesc);
		} else {
			$this->document->setDescription($menu->params->get('menu-meta_description'));
		}

		if ($this->item->metakey != '') {
			$this->document->setMetadata('keywords', $this->item->metakey);
		} else {
			$this->document->setMetadata('keywords', $menu->params->get('menu-meta_keywords'));
		}

		if ($menu->params->get('robots') != '') {
			$this->document->setMetadata('robots', $menu->params->get('robots'));
		} else {
			$this->document->setMetadata('robots', $this->item->metadata);
		}

		if ($this->params->get('generator') == 'none') {
			$this->document->setGenerator('');
		} elseif ($this->params->get('generator') == 'site') {
			$this->document->setGenerator($this->document->getGenerator());
		} else {
			$this->document->setGenerator($this->params->get('generator'));
		}
	}
}
