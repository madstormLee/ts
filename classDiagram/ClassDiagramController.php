<?
class ClassDiagramController extends MadController {
	function indexAction() {
	}
	function writeAction() {
		$this->layout->setView('views/layouts/write.html');
		$this->js->addNext("http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js", 'jquery');
		$this->right = new MadView( 'views/ClassDiagram/right.html' );

		$this->model->fetch( $this->params->file );
	}
	function viewAction() {
		$model = new ClassDiagram( $this->params->file );
		$this->view->model = $model;
	}
	function saveAction() {
		$post = $this->post;
		$model = $this->model;

		if ( ! $post->file ) {
			$post->file = "data/ClassDiagram/$post->title.json";
		}
		$model->fetch( $post->file )->setData( $post );
		return $model->save();
	}
	function deleteAction() {
		return $this->model->delete( $this->params->id );
	}
}
