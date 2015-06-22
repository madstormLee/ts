<?
class SitemapController extends MadController {
	function init() {
		parent::init();
		$this->model->fetch( $this->project->id );
	}
	function indexAction() {
		$this->view->index = $this->model->getIndex();
	}
	function viewAction() {
		$current = $this->model->getPath( $this->get->href );
		$this->view->current = $current;
	}
	function skeletonAction() {
		$json = new MadJson( 'Sitemap/sitemap.json' );

		foreach( glob( '*', GLOB_ONLYDIR ) as $component ) {
			$controllerName = $component.'Controller';
			$controllerFile = "$component/$controllerName.php";
			if ( ! is_file( $controllerFile ) ) {
				continue;
			}
			require_once( $controllerFile );
			if ( ! class_exists( $controllerName ) ) {
				continue;
			}
			$controller = new $controllerName;

			$subs = array();
			foreach( $controller->getActions() as $action ) {
				$subs[$action] = array(
					"component" => $component,
					"action" => $action,
					'label' => $action,
				);
			}
			$json->$component = array(
				"component" => $component,
				"action" => 'index',
				"label" => $component,
				"subs" => $subs,
			);
		}
		return $json->save();
	}
	function writeAction() {
	}
	function writeSubAction() {
		$model = $this->model;
		$current = $model->getPath( $this->get->href );

		$this->view->current = $current;
	}
	function saveAction() {
		$model->save( $this->post );
		$this->model->saveContents( $this->post->content );
	}
	function deleteAction() {
		$this->model
			->removePath( $this->get->href )
			->save();
	}
}
