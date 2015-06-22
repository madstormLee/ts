<?
class MockModel {
	private $config;
	private $mockData;
	private $guessor;

	function __construct( MadData $config = null ) {
		$this->setConfig( $config );
		$this->guessor = new Guessor;
		$this->guessor->setAnswerType( 'mock' );
	}
	function setConfig( $config ) {
		if ( ! is_null( $config ) ) {
			$this->config = $config;
		}
		return $this;
	}
	function setMockData( $mockData ) {
		$this->mockData = $mockData;
	}
	function getWritables() {
		$rv = new MadData;
		foreach( $this->mockData->columns as $name => $row ) {
			$rv->$name = array(
				'type' => $this->config->columns->$name->type,
				'label' => $this->config->columns->$name->label,
				'name' => $name,
			);
			if ( $row->show == true ) {
				$rv->$name->writable = true;
			}
		}
		return $rv;
	}
	function __get( $key ) {
		if ( $this->config ) {
			return $this->config->$key;
		}
		return $this->guessor->$key;
	}
	function __set( $key, $value ) {
	}
}
