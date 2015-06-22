<?
class DatabaseDiagramController extends MadController {
	function __construct() {
		parent::__construct();
		$this->model = new DatabaseDiagram;
	}
	function indexAction() {
		$this->js->replace( "$this->projectRoot$this->controllerName/list" );
	}
	function listAction() {
		$dir = $this->phpStorm->getDir('diagrams') . 'database/';
		$list = new DatabaseDiagramList;
		$list->setDir( $dir );
		
		$this->main->list = $list;
		return $this->main;
	}
	function writeAction() {
		
		$form = new MadForm;
		$data = new MadJson('json/forms/DatabaseDiagram/setInfo');
		$form->setData( $data );
		$this->main->form = $form;
		return $this->main;
	}
	function saveAction() {
		$post = $this->post;
		$model = $this->model;
		$file = $this->phpStorm->getDir('diagrams') . 'database/' . $post->name;
		$model->setFile( $file );
		$model->setData( $post->get() );
		$this->js->alert( $model->save() . '개의 데이타가 저장되었습니다.')->replaceBack();
	}
}
