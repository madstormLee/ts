<?
class Mock {
	private $rows = 100;
	private $table = '';
	private $data = array();
	private $types;
	private $startTime;
	private $endTime;
	private $kNames = array();
	private $kFirstNames = array();

	private $fieldNType = array();

	const DateFormat = 'Y-m-d m:i:s';

	function __construct() {
		$this->types = new MadIni('configs/ini/MadFieldTypes.ini');
		$this->startTime = strtotime("2000/01/01");
		$this->endTime = strToTime( self::DateFormat );

		$names = new MadJson( MAD . 'phpStorm/json/names' );
		$this->kNames = $names->family;
		$this->kFirstNames = $names->last;
	}
	function setRows( $rows ) {
		$this->rows = $rows;
		return $this;
	}
	function setTable( $table ) {
		$this->table = $table;
		$query = "show full columns from `{$this->table}`";
		$q = new ProjectQ($query);
		$this->fieldNType = $q->toDictionary();

		return $this;
	}
	function create() {
		$guesser = new MadFieldNameGuessor;

		foreach( $this->fieldNType as $field => $type ) {
				$getType = $guesser->guess( $field, $type );
				$this->data[$field] = $this->$getType( $this->rows );
		}
	}
	function getData() {
		return $this->data;
	}
	// below is getter.
	function getNull() {
		return array_fill( 0, $this->rows, '' );
	}
	function getNumber() {
		$rv = array();
		for( $i = 0; $i < $this->rows; ++$i ) {
			srand();
			$rv[] = rand();
		}
		return $rv;
	}
	function getJunkText() {
		return array_fill( 0, $this->rows, 'ㅂㅈ기아ㅂㅈㄱ이ㅏㅈㅂㄱ열바ㅁ차ㅇㅂ치아ㅈㅂㄱ이ㅏㅂ');
	}
	function getAddress() {
		return array_fill( 0, $this->rows, '서울 양천구 신월6동 572-15');
	}
	function getContent() {
		return array_fill( 0, $this->rows, '이것은 테스트 content text입니다. 사실 content는 같은 글이 들어가도 별 살관이 없지요.');
	}
	function getTel() {
		return array_fill( 0, $this->rows, '019-318-5703');
	}
	function getCell() {
		return array_fill( 0, $this->rows, '019-318-5703');
	}
	function getEmail() {
		return array_fill( 0, $this->rows, 'madstorm.lee@gmail.com');
	}
	function getDate() {
		$rv = array();
		for ( $i=0; $i < $this->rows; ++$i ) {
			$time = rand( $this->startTime, $this->endTime );
			$rv[] = date( self::DateFormat, $time);
		}
		return $rv;
	}
	function getName() {
		$nameCnt = count( $this->kNames );
		$firstNameCnt = count( $this->kFirstNames );
		for ( $i=0; $i < $this->rows; ++$i ) {
			$kNameNo = $i % $nameCnt;
			$kFirstNameNo = $i % $firstNameCnt;
			if ( $kNameNo == 0 ) {
				shuffle( $this->kNames );
			}
			if ( $kFirstNameNo == 0 ) {
				shuffle( $this->kFirstNames );
			}
			$rv[] =  $this->kFirstNames[$kFirstNameNo] . $this->kNames[$kNameNo];
		}
		return $rv;
	}
	function __toString() {
		$values = array();
		for( $i = 0; $i < $this->rows; ++$i ) {
			$data = array();
			foreach( $this->fieldNType as $field => $type ) {
				$data[] = '"' . $this->data[$field][$i] . '"';
			}
			$value = implode(',', $data);
			$values[] = "( $value )";
		}
		$values = implode( ",\n", $values );
		$rv = "insert into `$this->table` values $values";
		return $rv;
	}
}
