<?
class Web2Pdf extends MadFile {
	private $filename = 'download.pdf';
	private $targetUrl = 'http://www.web2pdfconvert.com/engine';
	private $defaultServer = 'm.enewskorea.com';
	private $target = '';
	private $data = array();

	const ID = 322257067;

	function __construct( $filename = '' ) {
		// setServer 를 이용할 수 있겠지만, 일단 고정시켰다.
		if ( $_SERVER['SERVER_NAME'] == 'localhost' ) {
			$this->server = $this->defaultServer;
		} else {
			$this->server = $_SERVER['SERVER_NAME'];
		}

		if ( ! empty( $filename ) ) {
			$this->setFile( $filename );
		}
	}
	function setFile( $filename ) {
		$units = explode( '.', $filename );
		$extension = end( $units );
		if ( $extension != 'pdf' ) {
			$filename .= '.pdf';
		}
		$this->filename = $filename;
		return $this;
	}
	function getFile() {
		return $this->filename;
	}
	function getContents() {
		return file_get_contents( $this->getUrl() );
	}
	function setTarget( $target ) {
		$this->target = $target;
		return $this;
	}
	function download() {
		MadHeaders::download( $this->file, 'pdf' );
		return $this->getContents();
	}
	private function getUrl() {
		$this->data = array(
				'id' => self::ID,
				'disposition' => 'inline',
				'curl' => "http://$this->server$this->target",
				'outputmode' => 'service',
				'filename' => $this->filename,
				);
		$parameters = array();
		foreach( $this->data as $key => $value ) {
			$parameters[] = "$key=$value";
		}
		return $this->targetUrl . '?' . implode( '&', $parameters );
	}
	function __toString() {
		return $this->getContents();
	}
}
