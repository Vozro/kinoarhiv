<?php defined('_JEXEC') or die;

class KinoarhivViewMovie extends JViewLegacy {
	protected $item = null;

	public function display($tpl = null) {
		$app = JFactory::getApplication();

		$this->watch($app->input->get->get('type', 'trailer'));
	}

	protected function watch($type) {
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$params = $app->getParams('com_kinoarhiv');

		if ($params->get('allow_guest_watch') == 0 && $user->get('guest') && $type == 'movie') {
			echo '<div style="width: 200px; margin: 20px 5px 5px 5px;">'.GlobalHelper::showMsg(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), array('icon'=>'alert', 'type'=>'error')).'</div>';
			return;
		}

		if ($type == 'trailer' || $type == 'movie') {
			$item = $this->get('Trailer');

			$this->item = &$item;
			$this->params = &$params;
			$this->user = &$user;

			if ($params->get('player_type') == '-1') {
				parent::display('trailer');
			} else {
				parent::display('trailer_'.$params->get('player_type'));
			}
		} else {
			echo '<div style="width: 200px; margin: 20px 5px 5px 5px;">'.GlobalHelper::showMsg(JText::_('ERROR'), array('icon'=>'alert', 'type'=>'error')).'</div>';
		}
	}
}
