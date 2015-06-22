<?
class ClassController extends MadController {
	function indexAction() {
		$index = new MadDir( 'class/*.json' );
		$this->view->index = $index;
	}
	function viewAction() {
		$model = new ClassDiagram( $this->get->file );

		$this->view->controllerMethods = $model->getControllerMethods();
		$this->view->modelMethods = $model->getModelMethods();
	}
	// 아무래도 답이 안 나온다. default처리하되, writable하게 간다.
	function viewModelAction() {
		$name = 'User';
		$model = new User;

		$model = new ClassAnalyzer( $model );
		$methods = $model->getUsefulMethods( $model );
	}
	function viewControllerAction() {
		$name = 'UserController';
		include "User/$name.php";
		$controller = new $name;
		$methods = $controller->getActions();
		
		$json = new MadJson( $this->configDir . 'User.json' );
		$this->view->model = $json;
	}
}
