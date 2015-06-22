<?
// 중간 단계인 config를 만든다.
// 결국 configuration Management로 모든 data를 생성한다.
// 사실 class명도 Config 쪽으로 바꿀 수 있을 것이다.

class Erm2Project extends Mad {
	private $primaryKeys = array();

	function __construct( $fileName = '' ) {
		parent::__construct();
		if ( ! empty( $fileName ) ) {
			$this->loadFile( $fileName );
		}
	}
	function loadFile( $fileName ) {
		if ( ! file_exists( $fileName ) ) {
			return false;
		}
		$this->xml = simplexml_load_file( $fileName );
		$this->dic = $this->getDictionary( $this->xml->dictionary->word );
		return $this;
	}
	function getDictionary( $words ) {
		$dic = array();
		foreach( $words as $word ) {
			$dic[(int)$word->id] = $word;
		}
		return $dic;
	}
	function createData() {
		$success = 0;
		$i = 0;
		foreach( $this->xml->contents->table as $table ) {
			$success += $this->createTableQuery( $table );
			++$i;
		}
		return $success;
	}
	function createTableQuery( SimpleXMLElement $table ) {
		$query = array();
		foreach( $table->columns->normal_column as $column ) {
			$query[] = $this->getColumn($column);
		}
		$query[] = $this->getPrimaryKeys();

		$queryString = "create table $table->physical_name (\n";
		$queryString .= implode( ",\n", $query );
		$queryString .= ");";

		$word = $this->getWord( $column->word_id );
		return file_put_contents( "schemes/$word->physical_name.sql", $queryString ) ? 1:0;
	}
	function getPrimaryKeys() {
		if ( ! empty( $this->primaryKeys ) ) {
			$key = implode(',', $this->primaryKeys );
			return "primary key($key)";
		}
		return '';
	}
	function getColumn( $column ) {
		$word = $this->getWord( $column->word_id );
		$info = array();
		$info[] = "$word->physical_name";
		$info[] = $this->getType( $word );
		if ( (string)$column->auto_increment == 'true' ) {
			$info[] = "auto_increment";
		}
		if ( (string)$column->primary_key == 'true' ) {
			$this->primaryKeys[] = (string)$word->physical_name;
		}
		if ( (string)$column->unique_key == 'true' ) {
			$info[] = "unique";
		}
		if ( (string)$column->not_null == 'true' ) {
			$info[] = "not null";
		}
		if ( trim( (string)$column->default_value) ) {
			var_dump( trim( (string)$column->default_value ) );
			$info[] = "default $column->default_value";
		}
		return implode(' ', $info);
	}
	function getType( $word ) {
		$type = (string)$word->type;
		$types = array(
				'default' => 'varchar(255)',
				'decimal' => 'integer unsigned',
				'date' => 'datetime',
				'varchar(n)' => 'varchar(n)',
				);
		$rv = isset( $types[$type] ) ? $types[$type] : $types['default'];
		if ( $rv == 'varchar(n)' ) {
			$rv = str_replace( 'n', $word->length, $rv );
		}
		return $rv;

	}
	function getWord( $id ) {
		return $this->dic[(int)$id];
	}
}

