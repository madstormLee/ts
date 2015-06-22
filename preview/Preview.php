<?
class Preview extends MadView {
	function __construct( $viewFile ) {
		parent::__construct( $viewFile );
		if ( ! $this->isFile() ) {
			$this->save();
		}
	}
	function getAction() {
		return array_shift( explode( '.', baseName($this->getFile()) ) );
	}
	function getController() {
		return baseName( dirName( $this->getFile() ) );
	}
	function getDefault() {
		$phpStorm = PhpStorm::getInstance();
		$controller = $this->getController();
		$action = $this->getAction();

		$interface = new InterfaceDiagram( $phpStorm->getDir('diagrams') . "/interface/$controller" );
		$mockData = $interface->actions->{$action};

		$config = new MadConfig( $phpStorm->getDir('configs') . $controller );

		$model = new MockModel( $mockData );
		$model->setConfig( $config );
		$model->setMockData( $mockData );

		$view = new MadView("views/MockModel/$action");
		$view->model = $model;
		$view->formFactory = new MadFormFactory;

		if( $action == 'list' ) {
			$list = new MockModelList;
			$list->setConfig( $config );
			$list->setMockData( $mockData );

			$view->list = $list;
		}
		return $view;
	}
	function save() {
		return $this->saveContents( $this->getDefault() );
	}
}
