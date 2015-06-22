<?
class PersonaController extends MadController {
	private $file = 'json/Persona/list';
	function listAction() {
		// $this->main->list = new MadJson( $this->project->getRoot() . 'json/Persona/list' );
		$this->main->list = new MadJson( $this->file );
		return $this->main;
	}
	function writeAction() {
		$form = new MadForm( new MadJson('json/Persona/write') );
		if ( $this->post->id ) {
			$list = new MadJson( $this->file );
			$model = $list->{$this->get->id};
		} else {
			$model = new MadData;
		}
		$form->setModel( $model );

		$this->main->model = $model;
		$this->main->form = $form;

		return $this->main;
	}
	function viewAction() {
		$list = new MadJson( $this->file );
		$this->main->model = $list->{$this->get->id};
		return $this->main;
	}
	function insertAction() {
		$post = $this->post;
		$post->test();
		$json = new MadJson( $this->file );
		if ( $json->{$post->id} ) {
			throw new Exception('겹치는 아이디가 존재합니다.');
		}
		$json->{$post->id} = $post;
		$json->save();
		$this->js->replace('list');
	}
}
