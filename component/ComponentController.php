<?
class ComponentController extends MadController {
	function indexAction() {
		$this->view->index = $this->model->getIndex($this->session->project);
	}
	function writeAction() {
		$this->model->fetch( $this->params->id );
	}
	function viewAction() {
		$this->model->fetch( $this->params->id );
	}
	function saveAction() {
		$post = $this->post;
		if ( $file = ! $post->file ) {
			$file = "data/$post->title.json";
		}
		$model = new ComponentDiagram( $file );
		$model->setData( $post );
		return $model->save();
	}
	function deleteAction() {
	}
}
