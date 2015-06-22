<?
class MockController extends MadController {
	function insertAction() {
		$this->model->setTable( $this->get->table )
			->setRows( $this->post->rows )
			->create();
		$q = new Q( $this->model );
		return $q->rows();
	}
}
