<?
class Guessor {
	private $data;
	private $answerType = 'field';
	private $answerTypes = array (
			'field' => array(
				'method' => 'guessField',
				),
			'mock' => array(
				'method' => 'guessMock',
				),
			);

	function __construct() {
		$file = 'json/Guessor/fields.json';
		$this->addGuessData( $file );
	}
	function addGuessData( $file ) {
		$this->data = new MadJson( $file );
	}
	function setAnswerType( $answerType ) {
		if ( isset( $this->answerTypes[$answerType] ) ) {
			$answerType = $this->answerTypes[$answerType];
			$this->answerMethod = $answerType['method'];
		}
		return $this;
	}
	function guessField( $field, $type = '' ) {
		if ( $this->data->$field ) {
			return $this->data->$field->method;
		} else if ( 0 === strpos( $field, 'varchar' ) ) {
			return 'getJunkText';
		} else if ( false !== preg_match( '/date$/i', $field ) || 0 === stripos( $type, 'datetime' ) ) {
			return 'getDate';
		} else if ( false !== preg_match( '/int$/i', $type ) ) {
			return 'getNumber';
		}
		return 'getJunkText';
	}
	function guessMock( $key ) {
		return 'tested';
	}
	function __get( $key ) {
		return $this->{$this->answerMethod}( $key );
	}
}


