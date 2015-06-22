<?
class ConfigController extends MadController {
	function indexAction() {
		$this->model = new MadConfig;
		$list = new JsonList( $this->phpStorm->getDir('configs') );
		$this->view->list = $list;
	}
	function viewAction() {
		$this->view->model = $this->model->load( $this->get->file );
	}
	function writeAction() {
		$this->view->model = $this->model->load( $this->get->file );
	}
	function createTableDiagramAction() {
		$get = $this->get;
		$phpStorm = $this->phpStorm;

		$configs = new ConfigList( $phpStorm->getDir('configs') );
		$targetDir = $phpStorm->getDir('diagrams') . 'table/';
		$cnt = 0;
		foreach( $configs as $config ) {
			$tableDiagram = new TableDiagram( $targetDir . $config->name );
			$tableDiagram->setData( $config );
			$tableDiagram->id = $config->name . '00';
			$tableDiagram->dbName = $phpStorm->info->name;
			$tableDiagram->type = 'BASE TABLE';
			$cnt += $tableDiagram->save();
		}
		$this->js->alert( $cnt . ' 개의 파일이 생성되었습니다.')->replaceBack();
	}
	function createClassAction() {
		$get = $this->get;
		$model = $this->model;
		$phpStorm = $this->phpStorm;

		$configs = new ConfigList( $phpStorm->getDir('configs') );
		$cnt = 0;
		$this->view = new MadView('Class/prototype.json');
		foreach( $configs as $config ) {
			$name = new MadString($config->name);
			$config->name = $name->lower()->camel()->ucFirst();
			$model->setFile( $phpStorm->getDir('diagrams') . 'class/' . $config->name );
			$this->view->config = $config;
			$this->view->project = $phpStorm->info->name;
			$model->setFromRaw($this->view);
			$cnt += $model->save();
		}
		$this->js->alert( $cnt . ' 개의 파일이 생성되었습니다.')->replaceBack();
	}
	function createActivityAction() {
		$phpStorm = $this->phpStorm;
		$configs = new ConfigList( $phpStorm->getDir('configs') );
		$this->view = new MadView('Activity/prototype.json');
		$activity = new Activity;
		$cnt = 0;
		foreach( $configs as $config ) {
			$activity->setFile( $phpStorm->getDir('diagrams') . 'activity/' . $config->name );

			$this->view->name = $config->name;
			$this->view->label = $config->label;
			$activity->setData( json_decode($this->view->get()) );

			$cnt += $activity->save();
		}
		$this->js->alert( $cnt . '개의 파일이 생성되었습니다.')->replaceBack();
	}
	function createComponentDiagramAction() {
		$get = $this->params;
		$model = new ComponentDiagram;
		$project = $this->project;

		$configs = new ConfigList( $project->getDir('configs') );
		$cnt = 0;
		foreach( $configs as $config ) {
			$model->setFile( $project->getDir('diagrams') . 'component/' . $config->name );
			$data = array(
					'name' => $config->name,
					'label' => $config->label,
					'description' => $config->label . '의 정보를 제공 하는 데이터 액세스 컴포넌트',
					'interface' => array(
						'name' => $config->name . 'Interface',
						'description' => $config->label . ' 관리 및 검색을 위한 데이터 처리 인터페이스',
						),
					'controller' => array(
						'name' => $config->name . 'Controller',
						'description' => $config->label . ' 정보 제공을 위한 컨트롤러 클래스',
						),
					'model' => array(
						'name' => $config->name,
						'description' => $config->label . ' 관리 및 검색 서비스 구현 클래스',
						),
					);
			$model->setData( $data );
			$cnt += $model->save();
		}
		$this->js->alert( $cnt . ' 개의 파일이 생성되었습니다.')->replaceBack();
	}
	function createInterfaceAction() {
		$phpStorm = $this->phpStorm;

		$targetDir = $phpStorm->getDir('diagrams') . 'interface/';
		$configs = new ConfigList( $phpStorm->getDir('configs') );

		$convertor = new Config2Interface( $targetDir );
		$convertor->setName( $this->phpStorm->info->label );

		$cnt = 0;
		foreach( $configs as $config ) {
			$convertor->setConfig( $config );
			$cnt += $convertor->save();
		}
		$this->js->alert( $cnt . '개의 파일이 생성되었습니다.')->replaceBack();
	}
	function createUsecaseAction() {
		$phpStorm = $this->phpStorm;

		$targetDir = $phpStorm->getDir('diagrams') . 'usecases/';
		$configs = new ConfigList( $phpStorm->getDir('configs') );

		$model = $this->model;
		$template = new MadView( 'json/Usecase/basicUsecase.json' );
		$cnt = 0;
		foreach( $configs as $config ) {
			$model->setFile( $phpStorm->getDir('diagrams') . 'usecase/' . $config->name );
			$template->config = $config;
			$model->setFromRaw( $template->get() );
			$model->getFile();
			$cnt += $model->save();
		}
		$template->setView( 'json/Usecase/basicListUsecase.json' );
		foreach( $configs as $config ) {
			$model->setFile( $phpStorm->getDir('diagrams') . 'usecase/' . $config->name . 'List' );
			$template->config = $config;
			$model->setFromRaw( $template->get() );
			$model->getFile();
			$cnt += $model->save();
		}
		$this->js->alert( $cnt . '개의 파일이 생성되었습니다.')->replaceBack();
	}
}
