<?
class Configs2PhpStorm {
	protected $phpStorm; 
	protected $configs; 
	protected $targetDir; 
	protected $presetDir;

	function __construct( ConfigList $configs ) {
		$this->configs = $configs;
	}
	function setPhpStorm( $phpStorm ) {
		$this->phpStorm = $phpStorm;
	}
	function setTargetDir( $targetDir ) {
		$this->targetDir  = $targetDir; 
	}
	function setPresetDir( $presetDir ) {
		if ( ! is_dir( $presetDir ) ) {
			throw new Exception('presetDir not found!');
		}
		$this->presetDir = $presetDir;
	}
	function createPhpStormIni() {
		$ini = $this->phpStorm->getIni();
		$ini->setFile( $this->targetDir . '.phpStorm.ini' );
		return $ini->save();
	}
	function createHtaccess() {
		return $this->copyPresetFile( '.htaccess' );
	}
	function createFrontController() {
		return $this->copyPresetFile( 'index.php' );
	}
	function createIndexController() {
		return $this->copyPresetFile( 'controllers/IndexController.php' );
	}
	function createModels() {
		$template = new MadTemplate( $this->presetDir . 'models/Model.php' );
		$cnt = 0;
		foreach( $this->configs as $config ) {
			$template->name = $config->name;
			$target = $this->targetDir . "models/$config->name.php";
			$cnt += $this->saveFile( $target, $template );
		}
		return $cnt;
	}
	function createListModels() {
		$template = new MadTemplate( $this->presetDir . 'models/List.php' ); $cnt = 0;
		foreach( $this->configs as $config ) {
			$template->name = $config->name;
			$target = $this->targetDir . "models/{$config->name}List.php";
			$cnt += $this->saveFile( $target, $template );
		}
		return $cnt;
	}
	function createViews() {
		$views = array( 'list', 'view', 'write' );
		$cnt = 0;
		foreach( $views as $view ) {
			$cnt += $this->createView( $view );
		}
		return $cnt;
	}
	function createControllers() {
		$template = new MadTemplate( $this->presetDir . 'controllers/Controller.php' );
		$cnt = 0;
		foreach( $this->configs as $config ) {
			$template->name = $config->name;
			$template->project = ucFirst( $this->phpStorm->info->name );
			$target = $this->targetDir . "controllers/{$config->name}Controller.php";
			$cnt += $this->saveFile( $target, $template );
		}
		return $cnt;
	}
	function createSchemes() {
		$converter = new MadConfig2Scheme;
		$cnt = 0;
		foreach( $this->configs as $config ) {
			$converter->setConfig( $config );
			$target = $this->targetDir . "schemes/{$config->name}.sql";
			$cnt += $this->saveFile( $target, $converter );
		}
		return $cnt;
	}
	function createConfigs() {
		$sourceDir = $this->phpStorm->getDir('configs');
		$dir = new MadFile( $this->targetDir . 'json/configs/' );
		$dir->copyDir( $sourceDir, $targetDir );
	}
	function createDiagrams() {
		$sourceDir = $this->phpStorm->getDir('diagrams');
		$dir = new MadFile( $this->targetDir . 'json/diagrams/' );
		$dir->copyDir( $sourceDir, $targetDir );
	}
	private function createView( $view ) {
		$template = new MadTemplate( $this->presetDir . "views/$view.html" );
		$cnt = 0;
		foreach( $this->configs as $config ) {
			$target = $this->targetDir . "views/{$config->name}/$view.html";
			$cnt += $this->saveFile( $target, $template );
		}
		return $cnt;
	}
	private function saveFile( $target, $data ) {
		if ( ! is_dir( dirName( $target ) ) ) {
			mkdir( dirName( $target ), 0777, true );
		}
		return file_put_contents( $target, $data ) ? 1:0;
	}
	private function copyPresetFile( $fileName ) {
		$resource = $this->presetDir . $fileName;
		$target = $this->targetDir . $fileName;
		return $this->copyFile( $resource, $target );
	}
	private function copyFile( $resource, $target ) {
		if ( ! is_dir( dirName( $target ) ) ) {
			mkdir( dirName( $target ), 0777, true );
		}
		return copy( $resource, $target );
	}
}
