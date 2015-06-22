<?
class Erm extends Mad implements IteratorAggregate {
	private $file = '';
	private $data;
	private $words = null;

	function __construct( $file = '' ) {
		parent::__construct();
		$this->words = new MadData;
		$this->data = new MadData;
		if ( ! empty( $file ) ) {
			$this->setFile( $file );
			$this->loadFile();
		}
	}
	function getIterator() {
		return $this->data;
	}
	function setFile( $file ) {
		$this->file = $file;
		return $this;
	}
	function getFile() {
		return $this->file;
	}
	function loadFile() {
		if ( ! is_file( $this->file ) ) {
			return false;
		}
		$this->setData( json_decode( json_encode( simplexml_load_file( $this->file ) ), 1 ) );
		$this->setWords();
		return $this;
	}
	function setData( $data ) {
		$this->data->setData( $data );
		return $this;
	}
	function getData() {
		return $this->data;
	}
	function setWords() {
		foreach( $this->dictionary->word as $word ) {
			$this->words[$word->id] = $word;
		}
		return $this;
	}
	function getWords() {
		return $this->word;
	}
	function getWord( $key ) {
		return $this->words->$key;
	}
	function __get( $key ) {
		return $this->data->$key;
	}
	function __set( $key, $value ) {
		$this->data->$key = $value;;
	}
	function test() {
		printR( $this->data );
	}
}
