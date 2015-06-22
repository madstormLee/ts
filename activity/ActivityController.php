<?
class ActivityController extends MadController {
	function indexAction() {
		$list = new ActivityList( $this->project->getDir('diagrams') . 'activity/' );
		$this->view->list = $list;
	}
	function writeAction() {
		$this->setLayout( new MadView('Interface/writeLayout') );
		$this->layout->footer = new MadView('footer');
		$this->style->clear()
			->add("~/css/writeBase")
			->add("~/css/Activity/write");

		$this->view->table = new MadData;
		$this->view->activityList = new MadList;
	}
	function viewAction() {
		$this->view->model = $this->model->load( $this->get->file );
	}
}
