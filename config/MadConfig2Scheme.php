<?
class MadConfig2Scheme {
	protected $table = '';
	protected $target = '';
	protected $query = '';
	protected $config;
	protected $database = 'mysql';
	protected $prefix;

	protected $createDefinition = '';
	protected $tableOption = '';
	protected $partitionOption = '';
	protected $primaryKeys = array();

	protected $templates = array(
		'mysql' => 'Config/schemes/createMysql.sql',
		'oracle' => 'Config/schemes/createOracle.sql',
	);

	function __construct( $database = '' ) {
		if ( ! empty( $database ) ) {
			$this->setDatabase( $database );
		}
	}
	function setPrefix( $prefix ) {
		$this->prefix = $prefix;
		return $this;
	}
	function setDatabase( $database ) {
		$this->database = strToLower( $database );
		return $this;
	}
	function getDatabase() {
		return $this->database;
	}
	function setConfig( MadJson $config ) {
		$this->config = $config;
		$this->table = $config->name;
		return $this;
	}
	function setDir( $dir = '' ) {
		if ( ! empty( $dir ) ) {
			$this->dir = $dir;
		}
		return $this;
	}
	function getDir() {
		return $this->dir;
	}
	function setFile( $file ) {
		$this->file = $file . '.json';
		return $this;
	}
	function getData() {
		return $this->data;
	}
	function setData( $data = array() ) {
		$this->data = $data;
	}
	// ini쪽을 따라했지만, 뭔가 어색하다.
	function load( $file = '' ) {
		if ( ! empty( $file ) ) {
			$this->setFile( $file );
		}
		if ( is_file( $this->file ) ) {
			$this->data = json_decode( file_get_contents( $this->file ), 1 );
		}
	}
	// 뭔가 검사가 부족하다.
	function save() {
		$query = $this->getQuery();
		$target = $this->dir . $this->table . '.sql';
		return file_put_contents( $target, $query ) ? 1:0;
	}
	function setQuery() {
		if ( $this->database == 'mysql' ) {
			$this->setMysqlQuery();
		} else if ( $this->database == 'oracle' ) {
			$this->setOracleQuery();
		}
		return $this;
	}
	// 일단 template만 다르다.
	function getTemplate() {
		// temporal
		return ROOT . 'phpStorm/' . $this->templates[$this->database];
		return $this->templates[$this->database];
	}
	function setOracleQuery() {
		// temporal
		$this->config->tableName = $this->prefix . $this->config->name;
		$view = new MadView( $this->getTemplate() );
		$view->table = $this->config;
		$this->config->query = $this->getColumn();
		$this->query = $view->get();
		return $this;
	}
	function setMysqlQuery() {
		$view = new MadView( $this->getTemplate() );
		$view->table = $this->config;
		$this->config->query = $this->getColumn();
		$this->query = $view->get();
		return $this;
	}
	function clearPrimaryKey() {
		$this->primaryKeys = array();
	}
	function getQuery() {
		if ( empty( $this->query ) ) {
			$this->setQuery();
		}
		return $this->query;
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
		$key = implode(',', $this->primaryKeys );
		return "primary key($key)";
	}
	function getFields() {
	}
	function getType( $column ) {
		if ( $column->type == 'numeric' ) {
			return array(
				'type' => 'integer unsigned',
			);
		} elseif ( preg_match('/uDate/i', $column->name ) ) {
			return array(
				'type' => 'timestamp',
				'default' => 'current_timestamp',
				'on update' => 'current_timestamp',
			);
		} elseif ( preg_match('/date/i', $column->name ) ) {
			return array(
				'type' => 'date',
			);
		}
		return array();
	}
	function getColumnDefinition( $data ) {
		$info = new MadData;
		$info->name = '`'.$data->name.'`';
		$info->type = 'varchar(255)';

		if ( $data->autoIncrement == 'true' ) {
			$info->autoIncrement = "auto_increment";
		}
		if ( $data->primaryKey == 'true' ) {
			$this->primaryKeys[] = $data->name;
		}
		if ( $data->uniqueKey == 'true' ) {
			$info->key = "unique";
		}
		if ( $data->notNull == 'true' ) {
			$info->null = "not null";
		}
		if ( $data->default ) {
			if ( 0 !== strpos($data->type,'date' ) &&
				$data->autoIncrement != 'true') {
				$info->default = "default $data->default";
			}
		}
		if ( $data->label ) {
			$info[] = "comment '$data->label'";
		}
		$info->addData( $this->getType( $data ) );
		return implode(' ', $info->get() );
	}
	function __toString() {
		return $this->getQuery();
	}
	// 이 아래에서 ProjectQ가 나오는 것은 부자연스럽다.
	// 아예 이 class 자체를 phpStorm의 model로 옮기는 것이 나을지도...
	function install() {
		if ( empty( $this->query ) ) {
			$this->setQuery();
		}
		if ( $this->isTable() ) {
			$this->dropTable();
		}
		$q = new ProjectQ( $this->query );
		return $q->rows();
	}
	function isTable() {
		$query = "show tables like '$this->table'";
		$q = new ProjectQ($query);
		return $q->rows();
	}
	function dropTable() {
		$query = "drop table $this->table";
		$q = new ProjectQ($query);
		return $q->rows();
	}
}
