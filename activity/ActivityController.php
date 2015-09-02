<?
class ActivityController extends MadController {
	function indexAction() {
		$this->view->index = new MadDir( $this->component . '/activity' );
	}
	function writeAction() {
		$this->setLayout( new MadView('Interface/writeLayout') );
		$this->layout->footer = new MadView('footer');
		$this->style->clear()
			->add("~/css/writeBase")
			->add("~/css/Activity/write");

		$this->view->table = new MadData;
		$this->view->activityList = new MadIndex( $this->model );
	}
	function viewAction() {
		$this->view->model = $this->model->load( $this->get->file );
	}
}
