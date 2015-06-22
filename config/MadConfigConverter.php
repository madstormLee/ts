<?
class MadConfigConverter {
	protected $config;
	protected $template = '';
	protected $fileName = '';
	protected $data = '';
	protected $templates;
	protected $dirs;

	public function __construct( MadConfig $config = null ) {
		$this->templates = array(
				'controllers' =>  array(
					MAD . 'proto/Controller.php',
					),
				'models' => array(
					'model' => MAD . 'proto/Model.php',
					'listModel' => MAD . 'proto/ListModel.php',
					),
				'views' => array(
					'list' => MAD . "proto/list.html",
					'view' => MAD . "proto/view.html",
					'write' => MAD . "proto/write.html",
					),
				);
		if ( ! is_null( $config ) ) {
			$this->setConfig( $config );
		}
	}
	function setDirs( $dirs ) {
		$this->dirs = $dirs;
		return $this;
	}
	public function setTemplate( $template ) {
		$this->template = $template;
		return $this;
	}
	public function getTemplate( $template ) {
		return $this->template;
	}
	public function setFile( $fileName ) {
		$this->fileName = $fileName;
		return $this;
	}
	public function getFile( $fileName ) {
		return $this->fileName;
	}
	public function setDir( $dir ) {
		$this->dir = $dir;
		return $this;
	}
	public function setConfig( Madconfig $config ) {
		$this->config = $config;
		return $this;
	}
	public function getTemplates() {
		return $this->templates;
	}
	public function setTemplates( $templates ) {
		$this->templates = $templates;
		return $this;
	}
	// setData를 이상하게 사용했다.
	public function setData() {
		$view = new MadView( $this->template );
		$view->config = $this->config;
		$this->data = $view->get();
		return $this;
	}
	public function save() {
		if ( ! is_dir( $this->dir ) ) {
			mkdir( $this->dir, 0755, true );
		}
		if ( empty( $this->data ) ) {
			$this->setData();
		}
		return file_put_contents( $this->dir . $this->fileName, $this->data ) ? 1:0;
	}
	public function saveAll() {
		// 일단 조잡하게 가고, 먼저 controller에, 나중에 MadConfig쪽으로 이관하자.
		// 예외가 너무 많다. 사실상 sub classing을 해도 될 정도이다.
		$dirs = $this->dirs;
		$rv = 0;
		$modelName = $this->config->name;
		foreach( $this->templates as $key => $templates ) {
			$targetDir = ROOT . $dirs->projectRoot . $dirs->$key;
			if ( $key === 'views' ) {
				$targetDir =  $targetDir . $modelName . '/';
				$this->setDir( $targetDir );
			} else {
				$this->setDir( $targetDir );
			}
			if ( ! is_dir( $targetDir ) ) {
				mkdir( $targetDir, 0755, true );
			}
			foreach( $templates as $template ) {
				if ( $key === 'views' ) {
					$fileName = baseName( $template );
				} else {
					$fileName = $modelName . str_replace( 'Model', '', baseName( $template ) );
				}
				$this->setTemplate( $template );
				$this->setFile( $fileName );
				$this->setData();
				$rv += $this->save();
			}
		}
		return $rv;
	}
}
