<?
class UsecaseController extends MadController {
	function viewAction() {
		$this->view->model = new MadJson("usecase/data/$data->id.json");
	}
}
