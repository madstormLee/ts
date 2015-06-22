<?
class MadConfig2SchemeOracle extends MadConfig2Scheme {
	protected $database = 'oracle';
	protected $table = '';
	protected $target = '';
	protected $query = '';
	protected $config;

	protected $prefix = '';

	protected $createDefinition = '';
	protected $tableOption = '';
	protected $partitionOption = '';

	protected $primaryKeys = array();
	protected $uniqueKeys = array();
	protected $comments = array();
	protected $fields = array();
	protected $autoIncrement = '';

	protected $name = '';

	function __construct() {
	}
	function setName( $name ) {
		$this->name = $name;
	}
	function getName() {
		return $this->name;
	}
	function setPrefix( $prefix ) {
		$this->prefix = $prefix;
		return $this;
	}
	function setConfig( MadConfig $config ) {
		$this->config = $config;
		$this->table = $this->prefix . $config->name;
		return $this;
	}
	function getConfig() {
		return $this->config;
	}
	function setData( $data = array() ) {
		$this->data = $data;
	}
	function getData() {
		return $this->data;
	}
	function setQuery() {
		$this->config->tableName = $this->prefix . $this->config->name;

		$this->query = "create table \"$this->table\" (\n";
		$this->query .= $this->getColumn();
		$this->query .= ");\n";
		$this->query .= $this->getComments() . "\n";
		$this->query .= $this->getSequence() . "\n";

		return $this;
	}
	function getComments() {
		$rv = array();
		foreach( $this->comments as $column => $comment ) {
			$rv[] = "comment on column $this->table.$column is '$comment';";
		}
		return implode("\n", $rv);
	}
	function getSequence() {
		if ( empty( $this->autoIncrement ) ) {
			return '';
		}
		$query = "create sequence \"{$this->table}_sequence\"
		start with 1
		increment by 1
		minvalue 1
		nomaxvalue
		order;";
		return $query;
	}
	function getQuery() {
		if ( empty( $this->query ) ) {
			$this->setQuery();
		}
		return strToUpper( $this->query );
	}
	function getTable() {
		return strToUpper( $this->table );
	}
	function getColumn() {
		$rv = array();
		foreach( $this->config->columns as $column ) {
			$rv[] = $this->getColumnDefinition($column);
		}
		if ( $this->isPrimaryKeys() ) {
			$rv[] = $this->getPrimaryKeys();
		}

		return implode( ",\n", $rv );
	}
	function isPrimaryKeys() {
		return ! empty( $this->primaryKeys );
	}
	function getPrimaryKeys() {
		$key =  implode(',', $this->primaryKeys );
		$name = $this->table . '_' . implode('_', $this->primaryKeys ) . '_PK';
		return "CONSTRAINT \"$name\" primary key(\"$key\")";
	}
	function getUniqueKeys() {
		$key = implode('","', $this->uniqueKeys );
		$name = implode('_', $this->uniqueKeys ) . '_UK';
		return "CONSTRAINT \"$name\" unique(\"$key\")";
	}
	function getFields() {
		return strToUpper('"' . implode( "\", \"", $this->fields ) . '"');
	}
	function getValues() {
		$rv = array();
		foreach( $this->fields as $field ) {
			$field = strToLower( $field );
			$rv[] = "#$field#";
		}
		return implode( ", ", $rv );
	}
	function getUpdateSet() {
		$rv = array();
		foreach( $this->fields as $field ) {
			$fieldLower = strToLower( $field );
			$field = strToUpper( $field );
			$rv[] = "\"$field\" = #$fieldLower#";
		}
		return implode( ", ", $rv );
	}
	// columnDefinition과 함께 엮을 것.
	function setFields() {
		foreach( $this->config->columns as $column ) {
			$this->fields[] = $column['name'];
		}
		return $this;
	}
	function getDataType( $typeText ) {
		if ( 0 === strpos( $typeText, 'int' ) ) {
			return 'number';
		}
		$typeText = preg_replace( "/numeric/", "number", $typeText );
		$typeText = preg_replace( "/varchar\(/i", "varchar2(", $typeText );
		$typeText = preg_replace( "/text/i", "varchar2(4000)", $typeText );
		return $typeText;
	}
	function getColumnDefinition( $column ) {
		$data = new MadData($column);
		$info = array();
		$info[] = '"'.$data->name.'"';
		$info[] = $this->getDataType( $data->type );

		if ( $data->autoIncrement == 'true' ) {
			$this->autoIncrement = $data->name;
		}
		if ( $data->primaryKey == 'true' ) {
			$this->primaryKeys[] = $data->name;
		}
		if ( $data->uniqueKey == 'true' ) {
			$this->uniqueKeys[] = $data->name;
		}
		if ( $data->notNull == 'true' ) {
			$info[] = "not null";
		}
		if ( $data->default ) {
			if ( 0 !== strpos($data->type,'date' ) &&
				$data->autoIncrement != 'true') {
				$info[] = "default $data->default";
			}
		}
		if ( $data->label ) {
			$this->comments[$data->name] = $data->label;
		}
		return implode(' ', $info);
	}
	function __toString() {
		return $this->getQuery();
	}
}
