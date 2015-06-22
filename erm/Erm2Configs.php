<?
class Erm2Configs extends Mad implements IteratorAggregate {
	private $data = array();
	private $erm = null;
	private $columnTypes;
	private $configDir;

	function __construct( $erm = null ) {
		parent::__construct();
		$this->columnTypes = new MadData( array(
					'default' => 'text',
					'decimal' => 'numeric',
					'date' => 'datetime',
					'varchar' => 'text',
					) );
		$this->setErm( $erm );
	}
	function setConfigDir( $dir ) {
		$this->configDir = $dir;
		return $this;
	}
	function getConfigDir() {
		return $this->configDir;
	}
	function setErm( Erm $erm ) {
		if ( ! is_null( $erm ) ) {
			$this->erm = $erm;
		}
		return $this;
	}
	function getPreview() {
		$data = array();
		foreach( $this->erm->contents->table as $table ) {
			$row = array();
			$row['name'] = (string)$table->logical_name;
			$row['table'] = (string)$table->physical_name;
			$data[] = $row;
		}
		return $data;
	}
	function setData() {
		foreach( $this->erm->contents->table as $table ) {
			$this->data[] = $this->createConfig( $table );
		}
		return $this;
	}
	function createConfig( $table ) {
		$name = ucfirst( (string)$table->physical_name);
		$config = new MadConfig( $this->configDir . $name );

		$config->name = $name;
		$config->label = $table->logical_name;
		$config->description = trim( (string)$table->description );
		if ( ! $config->description ) {
			$config->description = $config->label . ' 테이블';
		}
		$config->columns  = $this->getColumns( $table );
		$config->wDate = trim( $table->created_date );
		if ( ! $config->wDate ) {
			$config->wDate = date('Y-m-d h:i:s');
		}
		$config->uDate = date('Y-m-d h:i:s');

		return $config;
	}
	function getIterator() {
		if ( empty( $this->data ) ) {
			$this->setData();
		}
		return new ArrayIterator( $this->data );
	}
	function getColumns( $table ) {
		$columns = array();
		foreach( $table->columns->normal_column as $column ) {
			$info = $this->getColumnInfo($column);
			$columns[$info->name] = $info->getArray();
		}
		return $columns;
	}
	function getColumnInfo( $column ) {
		// word_id가 있을때도 있고 없을 때도 있다.
		$word_id = $column->word_id;
		$info = new MadData;
		if ( isset( $column->word_id ) ) {
			$word = $this->erm->getWord( $word_id );
			$info->name = $word->physical_name;
			$info->label = $word->logical_name;
			$info->max = $word->length;
			$info->type = $this->getType( $word->type, $word->length );
		} else {
			$info->name = $column->physical_name;
			$info->label = $column->logical_name;
			$info->max = $column->length;
			$info->type = $this->getType( $column->type, $column->length );
		}
		if ( empty( $info->name ) ) {
			return false;
		}
		$info->min = $column->min;
		$info->max = $column->max;
		$info->description = trim( (string)$column->description );
		if ( ! $info->description ) {
			$info->description = $info->label . ' 필드';
		}
		$info->autoIncrement = $column->auto_increment ;
		$info->primaryKey = $column->primary_key;
		$info->unique = $column->unique_key;
		$info->notNull = $column->not_null;
		$info->default = (string)$column->default_value;

		return $info;
	}
	function getType( $type, $length ) {
		return str_replace( '(n)', "($length)", $type );
		/*
		if ( $this->columnTypes->$type ) {
			return $this->columnTypes->$type;
		}
		return $this->columnTypes->default;
		*/
	}
}
