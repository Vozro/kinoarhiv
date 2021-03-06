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
 * Release View class
 *
 * @since  3.0
 */
class KinoarhivViewRelease extends JViewLegacy
{
	protected $item;

	protected $params;

	protected $user;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed
	 *
	 * @since  3.0
	 */
	public function display($tpl = null)
	{
		JLoader::register('KAContentHelper', JPath::clean(JPATH_COMPONENT . '/helpers/content.php'));

		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$lang = JFactory::getLanguage();
		$this->itemid = $app->input->get('Itemid', 0, 'int');
		$this->params = JComponentHelper::getParams('com_kinoarhiv');

		$item = $this->get('Item');

		if (count($errors = $this->get('Errors')) || is_null($item))
		{
			KAComponentHelper::eventLog(is_null($errors) ? $errors : implode("\n", $errors), 'ui');

			return false;
		}

		// Prepare the data
		$ka_theme = $this->params->get('ka_theme');
		$itemid = $this->itemid;

		// Replace country BB-code
		$item->text = preg_replace_callback('#\[country\s+ln=(.+?)\](.*?)\[/country\]#i', function ($matches) use ($ka_theme)
		{
			$html = JText::_($matches[1]);

			$cn = preg_replace('#\[cn=(.+?)\](.+?)\[/cn\]#', '<img src="' . JUri::base() . 'components/com_kinoarhiv/assets/themes/component/' . $ka_theme . '/images/icons/countries/$1.png" border="0" alt="$2" class="ui-icon-country" /> $2', $matches[2]);

			return $html . $cn;
		},
		$item->text
		);

		// Replace genres BB-code
		$item->text = preg_replace_callback('#\[genres\s+ln=(.+?)\](.*?)\[/genres\]#i', function ($matches)
		{
			return JText::_($matches[1]) . $matches[2];
		},
		$item->text
		);

		// Replace person BB-code
		$item->text = preg_replace_callback('#\[names\s+ln=(.+?)\](.*?)\[/names\]#i', function ($matches) use ($itemid)
		{
			$html = JText::_($matches[1]);

			$name = preg_replace('#\[name=(.+?)\](.+?)\[/name\]#', '<a href="' . JRoute::_('index.php?option=com_kinoarhiv&view=name&id=$1&Itemid=' . $itemid, false) . '" title="$2">$2</a>', $matches[2]);

			return $html . $name;
		},
		$item->text
		);

		if ($this->params->get('throttle_image_enable', 0) == 0)
		{
			$checking_path = JPath::clean($this->params->get('media_posters_root') . '/' . $item->fs_alias . '/' . $item->id . '/posters/' . $item->filename);

			if (!is_file($checking_path))
			{
				$item->poster = JUri::base() . 'components/com_kinoarhiv/assets/themes/component/' . $this->params->get('ka_theme')
					. '/images/no_movie_cover.png';
			}
			else
			{
				$item->fs_alias = rawurlencode($item->fs_alias);

				if (StringHelper::substr($this->params->get('media_posters_root_www'), 0, 1) == '/')
				{
					$item->poster = JUri::base() . StringHelper::substr($this->params->get('media_posters_root_www'), 1) . '/'
						. $item->fs_alias . '/' . $item->id . '/posters/thumb_' . $item->filename;
				}
				else
				{
					$item->poster = $this->params->get('media_posters_root_www') . '/' . $item->fs_alias . '/' . $item->id . '/posters/thumb_' . $item->filename;
				}
			}
		}
		else
		{
			$item->poster = JRoute::_(
				'index.php?option=com_kinoarhiv&task=media.view&element=movie&content=image&type=2&id=' . $item->id .
				'&fa=' . urlencode($item->fs_alias) . '&fn=' . $item->filename . '&format=raw&Itemid=' . $itemid . '&thumbnail=1'
			);
		}

		$item->plot = JHtml::_('string.truncate', $item->plot, $this->params->get('limit_text'));

		if ($this->params->get('ratings_show_frontpage') == 1)
		{
			if (!empty($item->rate_sum_loc) && !empty($item->rate_loc))
			{
				$plural = $lang->getPluralSuffixes($item->rate_loc);
				$item->rate_loc_c = round($item->rate_sum_loc / $item->rate_loc, (int) $this->params->get('vote_summ_precision'));
				$item->rate_loc_label = JText::sprintf(
					'COM_KA_RATE_LOCAL_' . $plural[0], $item->rate_loc_c,
					(int) $this->params->get('vote_summ_num'),
					$item->rate_loc
				);
				$item->rate_loc_label_class = ' has-rating';
			}
			else
			{
				$item->rate_loc_c = 0;
				$item->rate_loc_label = '<br />' . JText::_('COM_KA_RATE_NO');
				$item->rate_loc_label_class = ' no-rating';
			}
		}

		$item->event = new stdClass;
		$item->params = new JObject;
		$item->params->set('url', JRoute::_('index.php?option=com_kinoarhiv&view=release&id=' . $item->id . '&Itemid=' . $this->itemid, false));

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onContentPrepare', array('com_kinoarhiv.releases', &$item, &$this->params, 0));

		$results = $dispatcher->trigger('onContentAfterTitle', array('com_kinoarhiv.release', &$item, &$item->params, 0));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_kinoarhiv.release', &$item, &$item->params, 0));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_kinoarhiv.release', &$item, &$item->params, 0));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		$this->item = $item;
		$this->user = $user;

		$this->_prepareDocument();
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->title, JRoute::_('index.php?option=com_kinoarhiv&view=release&id=' . $this->item->id . '&Itemid=' . $this->itemid));

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since  3.0
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$pathway = $app->getPathway();

		$title = ($menu && $menu->title && $menu->link == 'index.php?option=com_kinoarhiv&view=release') ? $menu->title : JText::_('COM_KA_RELEASES');

		// Create a new pathway object
		$path = (object) array(
			'name' => $title,
			'link' => 'index.php?option=com_kinoarhiv&view=releases&Itemid=' . $this->itemid
		);

		$pathway->setPathway(array($path));
		$this->document->setTitle($this->item->title);

		if ($menu && $menu->params->get('menu-meta_description') != '')
		{
			$this->document->setDescription($menu->params->get('menu-meta_description'));
		}
		else
		{
			$this->document->setDescription($this->params->get('meta_description'));
		}

		if ($menu && $menu->params->get('menu-meta_keywords') != '')
		{
			$this->document->setMetadata('keywords', $menu->params->get('menu-meta_keywords'));
		}
		else
		{
			$this->document->setMetadata('keywords', $this->params->get('meta_keywords'));
		}

		if ($menu && $menu->params->get('robots') != '')
		{
			$this->document->setMetadata('robots', $menu->params->get('robots'));
		}
		else
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($this->params->get('generator') == 'none')
		{
			$this->document->setGenerator('');
		}
		elseif ($this->params->get('generator') == 'site')
		{
			$this->document->setGenerator($this->document->getGenerator());
		}
		else
		{
			$this->document->setGenerator($this->params->get('generator'));
		}
	}
}
