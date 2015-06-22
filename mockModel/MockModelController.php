<?
class MockModelController extends MadController {
	function init() {
		$action = $this->get->action;
		$this->view = new MadView("views/MockModel/{$action}");

		$interface = new InterfaceDiagram( $this->get->file );
		$this->mockData = $interface->{$action};

		$configFile = $this->phpStorm->getDir('configs') . baseName( $this->get->file );
		$this->config = new MadConfig( $configFile );

		$model = new MockModel( $this->mockData );
		$model->setConfig( $this->config );
		$model->setMockData( $this->mockData );
		$this->model = $model;

		$this->view->config = $this->config;
		$this->view->model = $this->model;
		$this->view->mockData = $this->mockData;
		$this->view->formFactory = new MadFormFactory;
	}
	function listAction() {
		$this->init();

		$list = new MockModelList;
		$list->setConfig( $this->config );
		$list->setMockData( $this->mockData );

		$this->view->list = $list;
	}
	function writeAction() {
		$this->init();
	}
	function viewAction() {
		$this->init();
	}
}
