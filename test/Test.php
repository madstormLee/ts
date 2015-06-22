<?
class Test {
	private $result;
	private $file;
	private $config;

	function __construct() {
		$this->result = new MadData;
	}
	function add( $text = '' ) {
		$this->result->add( $text );
	}
	function sep() {
		$this->add( "==================================================\n" );
	}
	function initFiles() {
		$this->add( "Initialize files..." );
		$file = new MadFile( '.' );
		$file->filter('^\.');

		$i = 0;
		foreach( $file as $row ) {
			++$i;
			$this->add( $row->getFile() );
		}
		$this->file = $file;
		$this->add( "- target files: $i" );
		$this->sep();
	}
	function initConfig() {
		$this->add( "Initialize config..." );
		$config = new MadJson( 'config.json' );
		if(!$config->isFile()) {
			$config->setData( array(
				'controllers' => array(),
				'phps' => array(),
				'etc' => array(),
			));
			foreach( $this->file as $row ) {
				if( preg_match('/Controller.php$/', $row->getFile()) ) {
					$config->controllers[] = $row->getFile();
				} elseif ( preg_match('/php$/', $row->getFile()) ) {
					$config->phps[] = $row->getFile();
				} else {
					$config->etc[] = $row->getFile();
				}
			}
		}
		$this->add( '============ controllers ==============' );
		$this->result->addData( $config->controllers );
		$this->add( '============ phps ==============' );
		$this->result->addData( $config->phps );
		$this->add( '============ etc ==============' );
		$this->result->addData( $config->etc );
		$this->config = $config;
	}
}
