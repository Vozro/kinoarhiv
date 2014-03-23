<?php defined('_JEXEC') or die;

class KinoarhivControllerReviews extends JControllerLegacy {
	public function save() {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$id = $this->input->get('id', null, 'int');

		if ($user->guest) {
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);

			return false;
		}

		$app->setUserState('data.com_kinoarhiv.movie.review.'.$id, $_REQUEST['form_editor']);
		$model = $this->getModel('reviews');
		$result = $model->save();

		$this->setMessage($result['message'], $result['success'] ? 'message' : 'error');

		$this->setRedirect(JRoute::_('index.php?option=com_kinoarhiv&view=movie&id='.$this->input->get('id', 0, 'int'), false));
	}

	/*public function delete() {
		$user = JFactory::getUser();
		$document = JFactory::getDocument();

		if ($user->guest) {
			if ($document->getType() == 'raw' || $document->getType() == 'json') {
				$document->setMimeEncoding('application/json');

				echo json_encode(array('success'=>0, 'message'=>JText::_('JERROR_ALERTNOAUTHOR')));
			} else {
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}

			return false;
		}

		$canDelete = $user->authorise('core.delete.reviews', 'com_kinoarhiv');

		if ($canDelete !== true) {
			if ($document->getType() == 'raw' || $document->getType() == 'json') {
				$document->setMimeEncoding('application/json');

				echo json_encode(array('success'=>0, 'message'=>JText::_('JERROR_ALERTNOAUTHOR')));
			} else {
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}

			return false;
		} else {
			$view = $this->input->get('view', 'movies', 'cmd');
			$model = $this->getModel('reviews');
			$result = $model->delete();
		}

		if ($document->getType() == 'raw' || $document->getType() == 'json') {
			$document->setMimeEncoding('application/json');

			echo json_encode($result);
		} else {
			$tab = $this->input->get('tab', '', 'cmd');

			$page = $this->input->get('page', '', 'cmd');
			$id = $this->input->get('id', 0, 'int'); // Item ID. Movie, person etc. Not a review ID
			$_id = ($id != 0) ? '&id='.$id : '';
			$tab = !empty($tab) ? '&tab='.$tab : '';
			$page = !empty($page) ? '&page='.$page : '';
			$return = $this->input->get('return', 'movies', 'cmd');
			echo '<pre>';
			print_r($_REQUEST);

			$url = JRoute::_('index.php?option=com_kinoarhiv&view='.$return.$tab.$page.$_id.'&Itemid='.$this->input->get('Itemid', 0, 'int'), false);

			$this->setMessage($result['message'], $result['success'] ? '' : 'error');

			$this->setRedirect($url);
		}
	}*/
}
