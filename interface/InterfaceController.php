<?
class InterfaceController extends MadController {
	function indexAction() {
		$this->model = new MadJson;

		$this->js->replace( "$this->projectName$this->controllerName/list" );
		$list = new MadFileList( $this->phpStorm->getDir('diagrams') . 'interface/' );
		$this->view->list = $list;
		$this->view->viewsDir = $this->phpStorm->getDir('views');
	}
	function viewAction() {
		$this->setLayout( new MadView('interface/writeLayout') );
		$this->js->add('/mad/js/prototype');
		$this->layout->footer = new MadView('footer');
		$this->css->clear()
			->add("~/css/writeBase")
			->add("~/css/interface/write");

		$get = $this->get;
		$model = $this->model;

		$model->load( $get->file );

		$preview = new Preview( $get->preview );
		
		$this->view->model = $model->actions->{$get->action};
		$this->view->preview = $preview;
	}
	function writeAction() {
		$this->js->add('/mad/js/prototype');
		$this->layout->footer = new MadView('footer');
		$this->css->clear()
			->add("~/Layout/writeBase.css")
			->add("~/interface/write.css");

		$get = $this->get;
		$model = new MadJson( $get->file );

		$preview = new Preview( $get->preview );
		
		if ( $model->actions ) {
			$this->view->data = $model->actions->{$get->action};
		} else {
			$this->view->data = new MadData;
			$this->view->data->contents = array();
		}
		if ( $preview->isFile() ) {
			$this->view->preview = $preview;
		} else {
			$this->view->preview = '';
		}
	}
	function saveAction() {
		$post = $this->params;
		$model = $this->model;

		$model->fetch( $post->file );

		$target = $model->actions->{$post->action};
		$target->addData( $post );
		return $model->save();
	}
}
