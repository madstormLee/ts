<?
class TestController extends MadController {
	function indexAction() {
		$this->view->list = new MadDir( 'test/data' );
		$this->view->actions = $this->getActions();
	}
	function testAll() {
		$model = $this->model;
		$model->initFiles();

		$model->initConfig();
		$model->testConfig();
	}
	function writeAction() {
	}
	function viewAction() {
		$get = $this->get;
		if ( ! is_file( $get->file ) ) {
			throw new Exception( 'File not found!' );
		}
		include_once $get->file;
		$className = basename( $get->file , '.php' );

		$test = new $className;

		$this->view->test = $test;
	}
}
