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

/**
 * Class KinoarhivModelCountry
 *
 * @since  3.0
 */
class KinoarhivModelCountry extends JModelForm
{
	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   3.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_kinoarhiv.country', 'country', array('control' => 'form', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   3.0
	 */
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_kinoarhiv.countries.' . JFactory::getUser()->id . '.edit_data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem()
	{
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$_id = $app->input->get('id', array(), 'array');
		$id = !empty($_id) ? $_id[0] : $app->input->get('id', null, 'int');
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('id', 'name', 'code', 'language', 'state')))
			->from($db->quoteName('#__ka_countries'))
			->where($db->quoteName('id') . ' = ' . (int) $id);

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}

	public function publish($isUnpublish)
	{
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$ids = $app->input->get('id', array(), 'array');
		$state = $isUnpublish ? 0 : 1;
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__ka_countries'))
			->set($db->quoteName('state') . ' = ' . (int) $state)
			->where($db->quoteName('id') . ' IN (' . implode(',', $ids) . ')');

		$db->setQuery($query);

		try
		{
			$db->execute();

			return true;
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
	}

	public function remove()
	{
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$ids = $app->input->get('id', array(), 'array');
		$query = $db->getQuery(true);

		$query->delete($db->quoteName('#__ka_countries'))
			->where($db->quoteName('id') . ' IN (' . implode(',', $ids) . ')');

		$db->setQuery($query);

		try
		{
			$db->execute();

			return true;
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.0
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$user = JFactory::getUser();
		$id = $app->input->post->get('id', null, 'int');
		$name = trim($data['name']);

		if (empty($name))
		{
			$this->setError(JText::_('COM_KA_REQUIRED'));

			$app->setUserState('com_kinoarhiv.countries.' . $user->id . '.data',
				array(
					'success' => false,
					'message' => JText::_('COM_KA_REQUIRED')
				)
			);

			return false;
		}

		if (empty($id))
		{
			// Check if country with this name allready exists
			$query = $db->getQuery(true);

			$query->select('COUNT(id)')
				->from($db->quoteName('#__ka_countries'))
				->where($db->quoteName('name') . " = '" . $db->escape($name) . "'");

			$db->setQuery($query);
			$count = $db->loadResult();

			if ($count > 0)
			{
				$this->setError(JText::_('COM_KA_COUNTRY_EXISTS'));

				$app->setUserState('com_kinoarhiv.countries.' . $user->id . '.data',
					array(
						'success' => false,
						'message' => JText::_('COM_KA_COUNTRY_EXISTS')
					)
				);

				return false;
			}

			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__ka_countries'))
				->columns($db->quoteName(array('id', 'name', 'code', 'language', 'state')))
				->values("'','" . $db->escape($name) . "','" . $db->escape($data['code']) . "','" . $db->escape($data['language']) . "','" . $data['state'] . "'");
		}
		else
		{
			$query = $db->getQuery(true);

			$query->update($db->quoteName('#__ka_countries'))
				->set($db->quoteName('name') . " = '" . $db->escape($name) . "'")
				->set($db->quoteName('code') . " = '" . $db->escape($data['code']) . "'")
				->set($db->quoteName('language') . " = '" . $db->escape($data['language']) . "'")
				->set($db->quoteName('state') . " = '" . $data['state'] . "'")
				->where($db->quoteName('id') . ' = ' . (int) $id);
		}

		try
		{
			$db->setQuery($query);
			$db->execute();

			if (empty($id))
			{
				$id = $db->insertid();
			}

			$app->setUserState('com_kinoarhiv.countries.' . $user->id . '.data',
				array(
					'success' => true,
					'message' => JText::_('COM_KA_ITEMS_SAVE_SUCCESS'),
					'data'    => array('id' => $id, 'name' => $name)
				)
			);

			return true;
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			$app->setUserState('com_kinoarhiv.countries.' . $user->id . '.data',
				array(
					'success' => false,
					'message' => JText::_('JERROR_AN_ERROR_HAS_OCCURRED')
				)
			);

			return false;
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
	public function validate($form, $data, $group = null)
	{
		// Filter and validate the form data.
		$data = $form->filter($data);
		$return = $form->validate($data, $group);

		// Check for an error.
		if ($return instanceof Exception)
		{
			$this->setError($return->getMessage());

			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message)
			{
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}
}
