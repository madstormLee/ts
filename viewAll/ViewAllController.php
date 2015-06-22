<?
class ViewAllController extends MadController {
	function componentDiagramAction() {
		$this->setLayout( new MadView('views/layouts/printView') );
		$this->style->add("~/css/componentDiagram/view")
			->add('/project/css/viewAll')
			->add('/project/css/Component/list');

		$this->view = new ComponentViewAll( $this->project->getDir('views') . 'Component/viewAll' );
		$this->view->updateCache();

		if ( ! $this->view->cacheExists() &&
				! $this->view->updateCache() ) {
			return new MadMessageCode('fileCreateFailed');
		}
	}
	function classAction() {
		$this->setLayout( new MadView('layouts/printView') );
		$this->style->add("~/css/class/view")
			->add('~/css/viewAll')
			->add("~/css/class/list");

		$this->main = new ClassViewAll( $this->phpStorm->getDir('views') . 'Class/viewAll.html' );
		$this->main->updateCache();

		if ( ! $this->main->cacheExists() &&
				! $this->main->updateCache() ) {
			return new MadMessageCode('fileCreateFailed');
		}

		return $this->main;
	}
	function activityAction() {
		$this->setLayout( new MadView('layouts/printView') );
		$this->style->add("~/css/activity/view")
			->add('/phpStorm/css/viewAll')
			->add('/phpStorm/css/Component/list');

		$this->main = new ActivityViewAll($this->phpStorm->getDir('views') . 'Activity/viewAll.html');
		$this->main->updateCache();

		if ( ! $this->main->cacheExists() &&
				! $this->main->updateCache() ) {
			return new MadMessageCode('fileCreateFailed');
		}

		return $this->main;
	}
	function interfaceAction() {
		$this->setLayout( new MadView('layouts/printView') );
		$this->style->clear()
			->add("~/css/Interface/write")
			->add('~/css/Interface/list')
			->add("~/css/writeBase")
			->add('~/css/viewAll');

		$this->main = new InterfaceViewAll( $this->phpStorm->getDir('views') . 'Interface/viewAll.html' );
		// $this->main->clear();

		if ( ! $this->main->cacheExists() &&
				! $this->main->updateCache() ) {
			return new MadMessageCode('fileCreateFailed');
		}

		return $this->main;
	}
	function tableAction() {
		$this->setLayout( new MadView('layouts/printView') );
		$this->style->add("~/css/Table/definitionList")
			->add('/phpStorm/css/viewAll')
			->add("~/css/Table/definitionList")
			->add("~/css/Database/definitionList");

		$this->main = new TableViewAll;

		if ( ! $this->main->cacheExists() &&
				! $this->main->updateCache() ) {
			return new MadMessageCode('fileCreateFailed');
		}
		return $this->main;
	}
	function tableDiagramAction() {
		$this->setLayout( new MadView('layouts/printView') );
		$this->style->add("~/css/TableDiagram/definitionList")
			->add("~/css/$this->controllerName/list")
			->add("~/css/$this->controllerName/view")
			->add('/phpStorm/css/viewAll')
			->add("~/css/Table/definitionList")
			->add("~/css/Database/definitionList");

		$this->main = new TableViewAll( $this->phpStorm->getDir('views') . 'Table/viewAll.html' );
		// $this->main->updateCache();

		if ( ! $this->main->cacheExists() &&
				! $this->main->updateCache() ) {
			return new MadMessageCode('fileCreateFailed');
		}
		return $this->main;
	}
	function usecaseAction() {
		$this->setLayout( new MadView('layouts/printView') );
		$this->style->add("~/css/$this->controllerName/view")
			->add('~/css/viewAll')
			->add("~/css/$this->controllerName/list");

		$this->view = new UsecaseViewAll;
		$this->view->updateCache();

		if ( ! $this->view->cacheExists() &&
				! $this->view->updateCache() ) {
			return new MadMessageCode('fileCreateFailed');
		}

		return $this->view;
	}
}
