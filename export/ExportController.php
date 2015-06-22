<?
class ExportController extends MadController {
	function indexAction() {
		$this->view->list = new MadDir( 'exports/java/' );
		$this->view->list2 = new MadDir( 'exports/php/' );
	}
	function downloadAction() {
		$path = $this->params->path;
		$file = 'exports/' . baseName( $path ) . '.tar.gz';
		`tar -czf $file $path`;
		if ( ! is_file( $file ) ) {
			throw new Exception( '파일 생성 중 문제가 발생하였습니다.');
		}
		MadHeaders::download( baseName( $name ) );
		readfile( $name );
		unlink( $name );
		die;
	}
	function presetListAction() {
	}
	function phpAction() {
		$get = $this->params;
		$exportDir = 'exports/php/';
		$preset = new MadJson("json/Export/presets/$get->preset");
		if ( $preset->isEmpty() ) {
			$this->js->alert( '존재하지 않는 preset입니다.')->replaceBack();
		}

		$this->view->preset = $preset;
		$this->view->form = new MadForm( $preset->forms );
	}
	function exportingPhpAction() {
		$get = $this->params;
		$post = $this->post;
		$phpStorm = $this->phpStorm;

		// 현재는 이 내용을 쓰지 않고 phpStorm을 따라간다.
		$post->addData( $get );

		$configs = new ConfigList( $phpStorm->getDir('configs') );

		$creator = new Configs2PhpStorm( $configs );
		$creator->setPresetDir( "proto/$post->preset/" );
		$creator->setTargetDir('exports/php/' . $phpStorm->info->name . '/admin/' );
		$creator->setPhpStorm( $phpStorm );

		/*************** temporal ********************/
		$creator->createControllers();
		$this->js->alert( 'done', 'back', 'replace' );
		die;

		$creator->createPhpStormIni();
		$creator->createHtaccess();
		$creator->createFrontController();
		$creator->createIndexController();
		$creator->createModels();
		$creator->createViews();
		$creator->createControllers();
		$creator->createSchemes();
		$creator->createConfigs();
		$creator->createDiagrams();
		$this->js->alert( 'done', 'back', 'replace' );
		//madTemp $creator->createListModels();
	}
	function etcAction() {
		$mock = new MadMock;

		$sitemap = new MadSiteMap( $phpStorm->getFile('siteMap') );
		$sitemap->clear();

		$schemeInstaller = new MadConfig2Scheme;
		$schemeInstaller->setDir( $phpStorm->getDir('schemes') );
		$schemeInstaller->setConfig( $config );
		$schemeInstaller->save();
		$schemeInstaller->install();

		$converter = new MadConfigConverter;
		$converter->setConfig( $config );
		$converter->setDirs( $phpStorm->dirs );

		$converter->saveAll();

		$sitemap->addSubFromConfig( $config );

		$mock->setTable( $config->name )
			->setRows( 1000 )
			->create();

		$q = new ProjectQ( $mock );
	}
	function javaSpringAction() {
		$this->view->model = $this->phpStorm;
		$this->view->directorySelector = 'directorySelector';
	}
	function exportingJavaSpringAction() {
		$phpStorm = $this->phpStorm;
		$post = $this->post;
		$dirs = new MadData;
		$dirs->export = 'exports/java/';
		$dirs->target =  $dirs->export . $post->projectName . '/';
		$dirs->src = $dirs->target . $post->srcRoot;

		$configDir = $phpStorm->getDir('configs');
		$configs = new ConfigList( $configDir );
		$configs->setData();

		$prototypes = new MadJson('json/JavaSpringCreator/prototypes');
		$creator = new JavaSpringCreator( $dirs->target );
		$creator->remove();
		$creator->create();

		/******************** create schemes ********************/
		$schemeCount = 0;
		$schemes = array();
		foreach( $configs as $config ) {
			$projectName = $phpStorm->info->name;
			$scheme = MadConfig2SchemeFactory::create( $post->database );

			$name = new MadString($config->name);
			$scheme->setName( $name->lower()->camel()->ucFirst() );
			$scheme->setConfig( $config );
			$schemes[] = $scheme;
			$targetPath = $dirs->src . $post->qry . 'schemes/' . $scheme->getName() . '.sql';

			$targetDir = dirName( $targetPath );
			if ( ! is_dir( $targetDir ) ) {
				mkdir( $targetDir, 0777, true );
			}
			$schemeCount += file_put_contents( $targetPath, $scheme ) ? 1:0;
		}

		/******************* create queries *******************/
		$queryCount = 0;
		$this->view = new MadView("Project/template/javaSpring/queries/query.xml");
		foreach( $schemes as $scheme ) {
			$scheme->setFields();
			$this->view->updateSet = $scheme->getUpdateSet();

			$this->view->scheme = $scheme;

			$targetPath = $dirs->src . $post->qry . $scheme->getName() . '.xml';
			$targetDir = dirName( $targetPath );
			if ( ! is_dir( $targetDir ) ) {
				mkdir( $targetDir, 0777, true );
			}
			$queryCount += file_put_contents( $targetPath, $this->view ) ? 1:0;
		}

		/****************** regist queries ******************/
		$fileName = $dirs->src . $post->sqlMapFile;
		if ( ! is_file( $fileName ) ) {
			continue;
		}
		$content = file_get_contents( $fileName );
		$registQueryCount = 0;
		foreach( $schemes as $scheme ) {
			$xml = new SimpleXMLElement( $content );
			$resource = $post->qry . $scheme->getName() . '.xml';

			$path = $xml->xpath("sqlMap[@resource='$resource']");
			if ( ! empty( $path ) ) {
				continue;
			}
			++ $registQueryCount;
			$xml->addChild('sqlMap')->addAttribute('resource', $resource);
			$label = $scheme->getConfig()->label;
			// 이건 조금 조잡하다.
			$comment = "<!-- $label -->";
			$content = str_replace('><', "> $comment\n<",$xml->asXML() );
		}
		$result = file_put_contents( $fileName, $content ) ? 1:0;

		/******************** create classes ********************/
		$classDiagrams = new ClassDiagramList( $phpStorm->getDir('diagrams') . 'class/' );
		$converter = new ClassDiagram2JavaSpring;
		$classCount = 0;
		foreach( $classDiagrams as $fileName => $classDiagram ) {
			$name = $configDir . baseName( $fileName );
			$config = $configs->$name;

			$converter->base = $post;
			$converter->classDiagram = $classDiagram;
			$converter->project = $phpStorm->info->name;
			$converter->config = $config;
			$converter->setViews();
			foreach( $converter as $target => $this->view ) {
				$targetPath = $dirs->src . $target;
				$targetDir = dirName( $targetPath );
				if ( ! is_dir( $targetDir ) ) {
					mkdir( $targetDir, 0777, true );
				}
				$classCount += file_put_contents( $targetPath, $this->view ) ? 1:0;
			}
		}

		/******************** create views ********************/
		$interfaceDiagrams = new InterfaceDiagramList( $phpStorm->getDir('diagrams') . 'interface/' );
		$converter = new InterfaceDiagram2View;
		$converter->projectName = $phpStorm->info->name;

		$this->mainCount = 0;
		foreach( $interfaceDiagrams as $fileName => $interfaceDiagram ) {
			$name = $configDir . baseName( $fileName );
			$config = $configs->$name;

			$converter->interfaceDiagram = $interfaceDiagram;
			$converter->config = $config;
			$converter->setViews();

			foreach( $converter as $target => $this->view ) {
				$name = new MadString($config->name);
				$targetPath = $dirs->target . $post->views . "mng/conts/" . $post->projectName . $name->lower()->camel()->ucFirst() . '/' .  $target;
				$targetDir = dirName( $targetPath );
				if ( ! is_dir( $targetDir ) ) {
					mkdir( $targetDir, 0777, true );
				}
				$this->mainCount += file_put_contents( $targetPath, $this->view ) ? 1 : 0;
			}
		}
		$alertText = array(
			$phpStorm->info->name . " skeleton이 생성되었습니다.",
			"$schemeCount 개의 스키마가 생성되었습니다.",
			"$queryCount 개의 쿼리 파일이 생성되었습니다.",
			"$registQueryCount 개의 쿼리 파일이 등록되었습니다.",
			"$this->mainCount 개의 jsp파일이 생성되었습니다.",
			"$classCount 개의 java 파일이 생성되었습니다.",
			$phpStorm->info->name . " 프로젝트가 출력되었습니다.",
		);
		$alertText = implode( "\\n", $alertText );

		$this->js->alert( $alertText, 'list','replace');
	}
	function removeAction() {
		$get = $this->params;
		$dir = new MadFile( $params->dir );
		$dir->removeDirAll();
		$this->js->replace( 'list' );
	}
	function removeJavaSpringAction() {
		$creator = new JavaSpringCreator;
		$creator->removeJavaSpring();
	}
}
