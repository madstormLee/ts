<?
class Config2Interface {
	private $dir;
	private $name;
	private $actions;
	private $excepts;

	function __construct( $dir ) {
		$this->dir = $dir;
		$this->actions = new MadData( array(
					'list' => array(
						'name' => 'list',
						'label' => '목록',
						'excepts' => array(
							"wDate","content","description"
							),
						),
					'view' => array(
						'name' => 'view',
						'label' => '보기',
						'excepts' => array(
							),
						),
					'write' => array(
						'name' => 'write',
						'label' => '쓰기',
						'excepts' => array(
							"no", "wDate", "uDate",
							),
						),
					));
	}
	function save() {
		$interface = new InterfaceDiagram( $this->dir . $this->config->name );
		$interface->controller = $this->config->name;
		$interface->actions = array();
		foreach( $this->actions as $actionName => $action ) {
			$interface->actions->$actionName = $this->getInfo( $action );
		}
		return $interface->save();
	}
	function setConfig( MadConfig $config ) {
		$this->config = $config;
	}
	function setName( $name ) {
		$this->name = $name;
		return $this;
	}
	function getName() {
		return $this->name;
	}
	private function getInfo( $action ) {
		$config = $this->config;
		$info = new MadData;

		$info->name = $action->name;
		$info->writer = '이명환';
		$info->version = '1';
		$info->id = $config->name;
		$locations = array(
				$this->getName(),
				$config->label,
				$action->label,
				);
		$info->location = implode( ' &gt; ', $locations );
		$info->wDate = date('Y-m-d');
		$info->contents = array();
		$info->comment = '';
		$info->columns = $this->getColumns( $action );
		if ( $action->name == 'list' ) {
			$info->searchColumns = $this->getColumns( $action );
		}
		return $info;
	}
	private function getColumns( $action ) {
		$rv = new MadData;
		$excepts = $action->excepts->getArray();
		foreach( $this->config->columns as $name => $column ) {
			if ( in_array( $name, $excepts ) ) {
			$rv->$name = array( 'show'=>false );
			} else {
			$rv->$name = array( 'show'=>true );
			}
			if ( $action->name == 'list' && $name == 'title' ) {
				$rv->$name->href = 'view';
			}
		}
		return $rv;
	}
}
