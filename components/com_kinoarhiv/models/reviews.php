<?php
/**
 * @package     Kinoarhiv.Site
 * @subpackage  com_kinoarhiv
 *
 * @copyright   Copyright (C) 2010 Libra.ms. All rights reserved.
 * @license     GNU General Public License version 2 or later
 * @url            http://киноархив.com/
 */

defined('_JEXEC') or die;

use Joomla\String\StringHelper;

/**
 * Class KinoarhivModelReviews
 *
 * @since  3.0
 */
class KinoarhivModelReviews extends JModelForm
{
	/**
	 * Method to get the record form.
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
		$form = $this->loadForm('com_kinoarhiv.reviews', 'reviews', array('control' => 'form', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}


	/**
	 * Method to save review into DB
	 *
	 * @param   string  $data  A raw string from POST
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since  3.0
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams('com_kinoarhiv');
		$movie_id = $app->input->get('id', 0, 'int');
		$strip_tag = KAComponentHelper::cleanHTML($data['review'], null);

		if (StringHelper::strlen($strip_tag) < $params->get('reviews_length_min') || StringHelper::strlen($strip_tag) > $params->get('reviews_length_max'))
		{
			$this->setError(JText::sprintf(JText::_('COM_KA_EDITOR_EMPTY'), $params->get('reviews_length_min'), $params->get('reviews_length_max')));

			return false;
		}

		$cleaned_text = KAComponentHelper::cleanHTML($data['review']);
		$datetime = date('Y-m-d H:i:s');
		$state = $params->get('reviews_premod') == 1 ? 0 : 1;
		$ip = '';

		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip .= $_SERVER['HTTP_CLIENT_IP'] . ' ';
		}

		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip .= $_SERVER['HTTP_X_FORWARDED_FOR'] . ' ';
		}

		if (!empty($_SERVER['REMOTE_ADDR']))
		{
			$ip .= $_SERVER['REMOTE_ADDR'];
		}

		$query = $db->getQuery(true)
			->insert($db->quoteName('#__ka_reviews'))
			->columns($db->quoteName(array('id', 'uid', 'movie_id', 'review', 'created', 'type', 'ip', 'state')))
			->values("'', '" . (int) $user->get('id') . "', '" . (int) $movie_id . "', '" . $db->escape($cleaned_text) . "', '" . $datetime . "', '" . (int) $data['type'] . "', '" . $ip . "', '" . (int) $state . "'");

		$db->setQuery($query);

		try
		{
			$db->execute();
			$insertid = $db->insertid();
			$app->enqueueMessage($params->get('reviews_premod') == 1 ? JText::_('COM_KA_REVIEWS_SAVED_PREMOD') : JText::_('COM_KA_REVIEWS_SAVED'));
		}
		catch (Exception $e)
		{
			KAComponentHelper::eventLog($e->getMessage());

			return false;
		}

		$this->sendEmails(
			array(
				'review'   => $cleaned_text,
				'id'       => (int) $movie_id,
				'ip'       => $ip,
				'datetime' => $datetime,
				'insertid' => $insertid
			)
		);

		return true;
	}

	/**
	 * Send an email to specified users
	 *
	 * @param   array  $data  An array of form array('review'=>$review, 'id'=>$id, 'ip'=>$ip, 'datetime'=>$datetime)
	 *
	 * @return  boolean
	 *
	 * @since  3.0
	 */
	protected function sendEmails($data)
	{
		$db = $this->getDbo();
		$user = JFactory::getUser();
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$params = JComponentHelper::getParams('com_kinoarhiv');
		$movie_title = '';

		if ($params->get('reviews_send_email') == 1 || $params->get('reviews_send_email_touser') == 1)
		{
			$query = $db->getQuery(true)
				->select('title, year')
				->from($db->quoteName('#__ka_movies'))
				->where('id = ' . (int) $data['id']);

			$db->setQuery($query);
			$result = $db->loadObject();

			if (!empty($result->year) && $result->year != '0000')
			{
				$movie_title = $result->title . ' (' . $result->year . ')';
			}
			else
			{
				$movie_title = $result->title;
			}
		}

		if ($params->get('reviews_send_email') == 1)
		{
			$_recipients = $params->get('reviews_emails');

			if (empty($_recipients))
			{
				$recipients = $config->get('mailfrom');
			}
			else
			{
				$_recipients = str_replace(' ', '', $params->get('reviews_emails'));
				$recipients = explode(',', $_recipients);
			}

			$subject = JText::sprintf('COM_KA_REVIEWS_ADMIN_MAIL_SUBJECT', $movie_title);
			$admin_url = JUri::base() . 'administrator/index.php?option=com_kinoarhiv&controller=reviews&task=edit&id[]=' . $data['insertid'];
			$movie_url = JRoute::_(JUri::getInstance()) . '&review=' . $data['insertid'] . '#review-' . $data['insertid'];

			$body = JText::sprintf(
				'COM_KA_REVIEWS_ADMIN_MAIL_SUBJECT',
				'<a href="' . $movie_url . '" target="_blank">' . $movie_title . '</a>'
			) . '<br />' . JText::sprintf(
				'COM_KA_REVIEWS_MAIL_INFO',
				$user->get('name'), $data['datetime'], $data['ip']
			) . '<p>' . $data['review'] . '</p>' . JText::_('COM_KA_REVIEWS_ADMIN_MAIL_BODY')
				. '<a href="' . $admin_url . '" target="_blank">' . $admin_url . '</a>';

			$send_a = $mailer->sendMail(
					$config->get('mailfrom'),
					$config->get('fromname'),
					$recipients,
					$subject,
					$body,
					true
			);

			if ($send_a)
			{
				KAComponentHelper::eventLog('Cannot send an email to administrator(s) while save review.');
			}
		}

		if ($params->get('reviews_send_email_touser') == 1)
		{
			// Get Itemid for menu
			$query = $db->getQuery(true);

			$query->select('id')
				->from($db->quoteName('#__menu'))
				->where("link = 'index.php?option=com_kinoarhiv&view=profile'")
				->where("language IN(" . $db->quote(JFactory::getLanguage()->getTag()) . "," . $db->quote('*') . ")")
				->setLimit(1, 0);

			$db->setQuery($query);

			try
			{
				$menu_itemid = $db->loadResult();
			}
			catch (Exception $e)
			{
				KAComponentHelper::eventLog($e->getMessage());

				return false;
			}

			$subject = JText::sprintf('COM_KA_REVIEWS_ADMIN_MAIL_SUBJECT', $movie_title);
			$uprofile_url = JRoute::_(JUri::base() . 'index.php?option=com_kinoarhiv&view=profile&page=reviews&Itemid=' . (int) $menu_itemid);
			$movie_url = JRoute::_(JUri::getInstance() . '&review=' . (int) $data['insertid']) . '#review-' . (int) $data['insertid'];

			$body = JText::sprintf(
				'COM_KA_REVIEWS_ADMIN_MAIL_SUBJECT',
				'<a href="' . $movie_url . '" target="_blank">' . $movie_title . '</a>'
			) . '<br />' . JText::sprintf(
				'COM_KA_REVIEWS_MAIL_INFO',
				$user->get('name'),
				$data['datetime'], $data['ip']
			) . '<p>' . $data['review'] . '</p>' . JText::_('COM_KA_REVIEWS_ADMIN_MAIL_BODY')
				. '<a href="' . $uprofile_url . '" target="_blank">' . $uprofile_url . '</a>';

			$send_b = $mailer->sendMail(
				$config->get('mailfrom'),
				$config->get('fromname'),
				$user->get('email'),
				$subject,
				$body,
				true
			);

			if ($send_b)
			{
				KAComponentHelper::eventLog('Cannot send an email to user while save review.');
			}
		}

		return true;
	}


	/**
	 * Method to delete review(s)
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since  3.0
	 */
	public function delete()
	{
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$user = JFactory::getUser();
		$review_id = $app->input->get('review_id', null, 'int');
		$review_ids = $app->input->get('review_ids', array(), 'array');

		if (!empty($review_ids))
		{
			JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		}

		if (!empty($review_ids))
		{
			if (empty($review_ids))
			{
				return false;
			}

			$query_result = true;
			$db->setDebug(true);
			$db->lockTable('#__ka_reviews');
			$db->transactionStart();

			foreach ($review_ids as $id)
			{
				$query = $db->getQuery(true);

				$query->delete($db->quoteName('#__ka_reviews'));

				if (!$user->get('isRoot'))
				{
					$query->where('uid = ' . $user->get('id'));
				}

				$query->where('id = ' . (int) $id);

				$db->setQuery($query . ';');

				if ($db->execute() === false)
				{
					$query_result = false;
					break;
				}
			}

			if ($query_result === true)
			{
				$db->transactionCommit();

				if (count($review_ids) > 1)
				{
					$app->enqueueMessage(JText::_('COM_KA_REVIEWS_DELETED_MANY'));
				}
				else
				{
					$app->enqueueMessage(JText::_('COM_KA_REVIEWS_DELETED'));
				}
			}
			else
			{
				$db->transactionRollback();
				$this->setError(JText::_('JERROR_ERROR'));
			}

			$db->unlockTables();
			$db->setDebug(false);

			if ($query_result === false)
			{
				return false;
			}
		}
		else
		{
			if (empty($review_id))
			{
				return false;
			}

			$query = $db->getQuery(true);

			$query->delete($db->quoteName('#__ka_reviews'));

			if (!$user->get('isRoot'))
			{
				$query->where('uid = ' . $user->get('id'));
			}

			$query->where('id = ' . (int) $review_id);

			$db->setQuery($query);

			try
			{
				$db->execute();
				$app->enqueueMessage(JText::_('COM_KA_REVIEWS_DELETED'));
			}
			catch (Exception $e)
			{
				$this->setError(JText::_('JERROR_ERROR'));
				KAComponentHelper::eventLog(JText::_('JERROR_ERROR'));

				return false;
			}
		}

		return true;
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
