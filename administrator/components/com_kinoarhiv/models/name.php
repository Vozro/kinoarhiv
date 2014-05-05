<?php defined('_JEXEC') or die;

JLoader::register('DatabaseHelper', JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'database.php');

class KinoarhivModelName extends JModelForm {
	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_kinoarhiv.name', 'name', array('control' => 'form', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}

		$input = JFactory::getApplication()->input;
		$ids = $input->get('id', array(), 'array');
		$id = (isset($id[0]) && !empty($id[0])) ? $id[0] : 0;
		$user = JFactory::getUser();

		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_kinoarhiv.name.' . (int) $id)) || ($id == 0 && !$user->authorise('core.edit.state', 'com_kinoarhiv'))) {
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');
		}

		return $form;
	}

	protected function loadFormData() {
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_kinoarhiv.edit.name.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null) {
		$app = JFactory::getApplication();
		$db = $this->getDBO();
		$tmpl = $app->input->get('template', '', 'string');
		$id = $app->input->get('id', array(), 'array');

		if ($tmpl == 'names_edit') {
			$movie_id = $app->input->get('movie_id', 0, 'int');
			$name_id = $app->input->get('name_id', 0, 'int');

			$db->setQuery("SELECT `name_id`, `role`, `dub_id`, `is_actors`, `voice_artists`, `is_directors`, `ordering` AS `r_ordering`, `desc` AS `r_desc`"
				. "\n FROM ".$db->quoteName('#__ka_rel_names')
				. "\n WHERE `name_id` = ".(int)$name_id." AND `movie_id` = ".(int)$movie_id);
			$result = $db->loadObject();
			
			if (!empty($result)) {
				$result->type = $app->input->get('career_id', 0, 'int');
			}
		} elseif ($tmpl == 'awards_edit') {
			$award_id = $app->input->get('award_id', 0, 'int');

			$db->setQuery("SELECT `id` AS `rel_aw_id`, `item_id`, `award_id`, `desc` AS `aw_desc`, `year` AS `aw_year`"
				. "\n FROM ".$db->quoteName('#__ka_rel_awards')
				. "\n WHERE `id` = ".(int)$award_id);
			$result = $db->loadObject();
		} elseif ($tmpl == 'premieres_edit') {
			$premiere_id = $app->input->get('premiere_id', 0, 'int');

			$db->setQuery("SELECT `id` AS `premiere_id`, `vendor_id` AS `p_vendor_id`, `premiere_date` AS `p_premiere_date`, `country_id` AS `p_country_id`, `info` AS `p_info`, `ordering` AS `p_ordering`"
				. "\n FROM ".$db->quoteName('#__ka_premieres')
				. "\n WHERE `id` = ".(int)$premiere_id);
			$result = $db->loadObject();
		} elseif ($tmpl == 'releases_edit') {
			$release_id = $app->input->get('release_id', 0, 'int');

			$db->setQuery("SELECT `id` AS `release_id`, `vendor_id` AS `r_vendor_id`, `release_date` AS `r_release_date`, `country_id` AS `r_country_id`, `media_type` AS `r_media_type`, `ordering` AS `r_ordering`"
				. "\n FROM ".$db->quoteName('#__ka_releases')
				. "\n WHERE `id` = ".(int)$release_id);
			$result = $db->loadObject();
		} else {
			$result = array('name'=>(object)array());
			if (count($id) == 0 || empty($id) || empty($id[0])) {
				return $result;
			}

			$db->setQuery("SELECT `n`.`id`, `n`.`asset_id`, `n`.`name`, `n`.`latin_name`, `n`.`alias`, `n`.`date_of_birth`, `n`.`date_of_death`, `n`.`birthplace`, `n`.`birthcountry`, `n`.`gender`, `n`.`height`, `n`.`desc`, `n`.`attribs`, `n`.`ordering`, `n`.`state`, `n`.`access`, `n`.`metakey`, `n`.`metadesc`, `n`.`metadata`, `n`.`language`, `l`.`title` AS `language_title`, `g`.`id` AS `gid`, `g`.`filename`"
				. "\n FROM ".$db->quoteName('#__ka_names')." AS `n`"
				. "\n LEFT JOIN ".$db->quoteName('#__languages')." AS `l` ON `l`.`lang_code` = `n`.`language`"
				. "\n LEFT JOIN ".$db->quoteName('#__ka_names_gallery')." AS `g` ON `g`.`name_id` = `n`.`id` AND `g`.`type` = 2 AND `g`.`photo_frontpage` = 1"
				. "\n WHERE `n`.`id` = ".(int)$id[0]);
			$result['name'] = $db->loadObject();

			$result['name']->genres = $this->getGenres();
			$result['name']->genres_orig = implode(',', $result['name']->genres['ids']);
			/*$result['name']->countries = $this->getCountries();
			$result['name']->countries_orig = implode(',', $result['name']->countries['ids']);
			$result['name']->tags = $this->getTags();
			$result['name']->tags_orig = !empty($result['name']->tags['ids']) ? implode(',', $result['name']->tags['ids']) : '';*/

			if (!empty($result['name']->attribs)) {
				$result['attribs'] = json_decode($result['name']->attribs);
			}
		}
//echo '<pre>';
//print_r($result['name']->genres);
		return $result;
	}

	protected function getGenres() {
		$app = JFactory::getApplication();
		$db = $this->getDBO();
		$id = $app->input->get('id', array(), 'array');
		$result = array('data'=>array(), 'ids'=>array());

		$db->setQuery("SELECT `g`.`id`, `g`.`name` AS `title`"
			. "\n FROM ".$db->quoteName('#__ka_genres')." AS `g`"
			. "\n WHERE `id` IN (SELECT `genre_id` FROM ".$db->quoteName('#__ka_rel_names_genres')." WHERE `name_id` = ".(int)$id[0].")");
		$result['data'] = $db->loadObjectList();

		foreach ($result['data'] as $value) {
			$result['ids'][] = $value->id;
		}

		return $result;
	}

	public function quickSave() {
		$app = JFactory::getApplication();
		$db = $this->getDBO();

		// We need set alias for quick save on movie page
		$name = 'n_name';
		$latin_name = 'n_latin_name';
		$date_of_birth = 'n_date_of_birth';
		$ordering = 'n_ordering';
		$language = 'n_language';

		$data = $app->input->getArray(array(
			'form'=>array(
				$name=>'string', $latin_name=>'string', $date_of_birth=>'string', $ordering=>'int', $language=>'string'
			)
		));
		$name = $data['form']['n_name'];
		$latin_name = $data['form']['n_latin_name'];
		$alias = $name != '' ? $name : $latin_name;
		$date_of_birth = (empty($data['form']['n_date_of_birth']) && $data['form']['n_date_of_birth'] == '0000-00-00') ? date('Y-m-d') : $data['form']['n_date_of_birth'];
		$ordering = empty($data['form']['n_ordering']) ? 0 : $data['form']['n_ordering'];
		$metadata = json_encode(array('tags'=>array(), 'robots'=>''));
		$language = empty($data['form']['n_language']) ? '*' : $data['form']['n_language'];

		if (empty($name) && empty($latin_name)) {
			return array('success'=>false, 'message'=>JText::_('COM_KA_REQUIRED'));
		}

		$db->setQuery("INSERT INTO ".$db->quoteName('#__ka_names')." (`id`, `asset_id`, `name`, `latin_name`, `alias`, `url_photo`, "
			. "\n `date_of_birth`, `date_of_death`, `birthplace`, `birthcountry`, `gender`, `height`, `desc`, `ordering`, `state`, "
			. "\n `access`, `metakey`, `metadesc`, `metadata`, `language`)"
			. "\n VALUES ('', '0', '".$db->escape($name)."', '".$db->escape($latin_name)."', '".JFilterOutput::stringURLSafe($alias)."', '', "
			. "\n '".$date_of_birth."', '', '', '', '', '', '', '".(int)$ordering."', '1', '1', '', '', '".$metadata."', '".$language."')");
		$query = $db->execute();

		if ($query !== true) {
			return array('success'=>false, 'message'=>JText::_('JERROR_AN_ERROR_HAS_OCCURRED'));
		} else {
			$insertid = $db->insertid();
			$rules = json_encode((object)array());

			$db->setQuery("SELECT MAX(`rgt`) + 1 FROM ".$db->quoteName('#__assets'));
			$lft = $db->loadResult();

			$db->setQuery("SELECT `id` FROM ".$db->quoteName('#__assets')." WHERE `name` = 'com_kinoarhiv' AND `parent_id` = 1 AND `level` = 1");
			$parent_id = $db->loadResult();

			$db->setQuery("INSERT INTO ".$db->quoteName('#__assets')." (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`)"
				. "\n VALUES ('', '".$parent_id."', '".$lft."', '".($lft+1)."', '1', 'com_kinoarhiv.name.".(int)$insertid."', '".$alias."', '".$rules."')");
			$assets_query = $db->execute();
			$assets_id = $db->insertid();

			$db->setQuery("UPDATE ".$db->quoteName('#__ka_names')." SET `asset_id` = '".$assets_id."' WHERE `id` = ".$insertid);
			$update_query = $db->execute();

			return array(
				'success'	=> true,
				'message'	=> JText::_('COM_KA_ITEMS_SAVE_SUCCESS'),
				'data'		=> array('id'=>$insertid, 'name'=>$name, 'latin_name'=>$latin_name)
			);
		}
	}

	public function saveNameAccessRules() {
		$app = JFactory::getApplication();
		$db = $this->getDBO();
		$data = $app->input->post->get('form', array(), 'array');
		$id = $app->input->get('id', null, 'int');
		$rules = array();

		if (empty($id)) {
			return array('success'=>false, 'message'=>'Error');
		}

		foreach ($data['name']['rules'] as $rule=>$groups) {
			foreach ($groups as $group=>$value) {
				if ($value != '') {
					$rules[$rule][$group] = (int)$value;
				} else {
					unset($data['rules'][$rule][$group]);
				}
			}
		}

		$rules = json_encode($rules);

		if (JFactory::getUser()->authorise('core.admin', 'com_kinoarhiv') && JFactory::getUser()->authorise('core.edit.access', 'com_kinoarhiv')) {
			// Get parent id
			$db->setQuery("SELECT `id` FROM ".$db->quoteName('#__assets')." WHERE `name` = 'com_kinoarhiv' AND `parent_id` = 1");
			$parent_id = $db->loadResult();

			$db->setQuery("UPDATE ".$db->quoteName('#__assets')
				. "\n SET `rules` = '".$rules."'"
				. "\n WHERE `name` = 'com_kinoarhiv.name.".(int)$id."' AND `level` = 2 AND `parent_id` = ".(int)$parent_id);

			try {
				$db->execute();
				return array('success'=>true);
			} catch(Exception $e) {
				return array('success'=>false, 'message'=>$e->getMessage());
			}
		} else {
			return array('success'=>false, 'message'=>JText::_('COM_KA_NO_ACCESS_RULES_SAVE'));
		}
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   12.2
	 */
	public function validate($form, $data, $group = null) {
		// Filter and validate the form data.
		$data = $form->filter($data);
		$return = $form->validate($data, $group);

		// Check for an error.
		if ($return instanceof Exception) {
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false) {
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}
}
