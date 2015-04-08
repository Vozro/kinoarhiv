<?php defined('_JEXEC') or die;
/**
 * @package     Kinoarhiv.Administrator
 * @subpackage  com_kinoarhiv
 *
 * @copyright   Copyright (C) 2010 Libra.ms. All rights reserved.
 * @license     GNU General Public License version 2 or later
 * @url			http://киноархив.com/
 */

class KinoarhivViewReleases extends JViewLegacy {
	protected $items;
	protected $pagination;
	protected $state;
	protected $form;

	public function display($tpl = null) {
		$user = JFactory::getUser();

		if ($tpl == 'add' || $tpl == 'edit') {
			$this->edit($tpl);
			return;
		}

		$items      = $this->get('Items');
		$pagination = $this->get('Pagination');
		$state      = $this->get('State');

		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $this->get('Errors')), 500);
			return false;
		}

		$this->addToolbar();
		$this->canEdit = $user->authorise('core.edit', 'com_kinoarhiv');

		$this->items         = &$items;
		$this->pagination    = &$pagination;
		$this->state         = &$state;
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->params        = JComponentHelper::getParams('com_kinoarhiv');

		parent::display($tpl);
	}

	protected function edit($tpl) {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!$user->authorise('core.create', 'com_kinoarhiv') && !$user->authorise('core.edit', 'com_kinoarhiv')) {
			throw new Exception(JText::_('COM_KA_NO_ACCESS_RIGHTS'), 403);
			return false;
		}

		$params = JComponentHelper::getParams('com_kinoarhiv');
		$this->form = $this->get('Form');

		$this->addToolbar($tpl);
		$this->params = &$params;

		parent::display('edit');
	}

	protected function addToolbar($task='') {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if ($task == 'add' || $task == 'edit') {
			if ($task == 'edit') {
				JToolbarHelper::title(JText::sprintf('COM_KINOARHIV', JText::_('COM_KA_RELEASES_TITLE').': '.JText::_('COM_KA_MOVIES_RELEASE_LAYOUT_EDIT_TITLE')), 'calendar');
			} else {
				JToolbarHelper::title(JText::sprintf('COM_KINOARHIV', JText::_('COM_KA_RELEASES_TITLE').': '.JText::_('COM_KA_MOVIES_RELEASE_LAYOUT_ADD_TITLE')), 'calendar');
			}

			JToolbarHelper::apply('apply');
			JToolbarHelper::save('save');
			JToolbarHelper::save2new('save2new');
			JToolbarHelper::divider();
			JToolbarHelper::cancel();
		} else {
			JToolbarHelper::title(JText::sprintf('COM_KINOARHIV', JText::_('COM_KA_RELEASES_TITLE')), 'calendar');
			if ($user->authorise('core.create', 'com_kinoarhiv')) {
				JToolbarHelper::addNew('add');
			}

			if ($user->authorise('core.edit', 'com_kinoarhiv')) {
				JToolbarHelper::editList('edit');
				JToolbarHelper::divider();
			}

			if ($user->authorise('core.delete', 'com_kinoarhiv')) {
				JToolbarHelper::deleteList(JText::_('COM_KA_DELETE_SELECTED'), 'remove');
			}

			if ($user->authorise('core.create', 'com_kinoarhiv') && $user->authorise('core.edit', 'com_kinoarhiv') && $user->authorise('core.edit.state', 'com_kinoarhiv')) {
				JHtml::_('bootstrap.modal', 'collapseModal');
				$title = JText::_('JTOOLBAR_BATCH');
				$layout = new JLayoutFile('joomla.toolbar.batch');

				$dhtml = $layout->render(array('title' => $title));
				JToolBar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'batch');
			}
		}
	}

	protected function getSortFields() {
		return array(
			'r.release_date' => JText::_('COM_KA_FIELD_RELEASE_DATE_LABEL'),
			'm.title'        => JText::_('COM_KA_FIELD_MOVIE_LABEL'),
			'c.name'         => JText::_('COM_KA_FIELD_RELEASE_COUNTRY'),
			'r.media_type'   => JText::_('COM_KA_RELEASES_MEDIATYPE_TITLE'),
			'r.language'       => JText::_('JGRID_HEADING_LANGUAGE'),
			'r.id'           => JText::_('JGRID_HEADING_ID')
		);
	}
}
