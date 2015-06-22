<?
class UsecaseController extends MadController {
	function indexAction() {
		$list = new UsecaseList;
		$list->setDir( $this->phpStorm->getDir('diagrams') . 'usecase/' );
		$this->view->list = $list;
	}
	function viewAction() {
		$model = new MadJson( $this->get->file );
		$this->view->model = $model;
	}
	function writeAction() {
		$this->model->fetch( $this->get->no );
		$form = new MadForm( $this->model );
		
		$this->view->form = $form;
		$this->view->model = $this->model;
		// from XP
		$users = new MadJson('User/data/users.json');
		$this->view->users = $users;
	}
	function saveAction() {
		return $this->model->setData( $this->post->get() )->save();
	}
	function deleteAction() {
		return $this->model->delete( $this->get->no );
	}
}
