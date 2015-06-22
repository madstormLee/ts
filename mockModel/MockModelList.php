<?
class MockModelList extends MadList {
	private $rows = 300;
	private $config = null;
	private $mockData = null;
	function __construct() {
		parent::__construct();
	}
	function setRows( $rows ) {
		$this->rows = $rows;
		return $this;
	}
	function getRows() {
		return $this->rows;
	}
	function setConfig( $config ) {
		$this->config = $config;
	}
	function setMockData( $mockData ) {
		$this->mockData = $mockData;
	}
	function getSelectedHeaders() {
		if ( ! $this->mockData || ! $this->mockData->columns ) {
			return array();
		}
		$rv = new MadData;
		foreach( $this->mockData->columns as $name => $list ) {
			if ( $list->show != true ) {
				continue;
			}
			$rv->$name = $this->config->columns->$name->label;
		}
		return $rv;
	}
	function getSearchables() {
		$rv = new MadData;
		foreach( $this->mockData->searchColumns as $name => $search ) {
			if ( $search->show != true ) {
				continue;
			}
			$rv->$name = array(
				'type' => $this->config->columns->$name->type,
				'label' => $this->config->columns->$name->label,
			);
		}
		return $rv;
	}
	function setData() {
		$this->searchTotal = $this->rows;
		$this->limit->setTotal( $this->rows );
		$this->data = $this->getMockRows( 10 );

		return $this;
	}
	private function getMockRows( $rows ) {
		$rv = array();
		$listColumns = $this->getSelectedHeaders();
		$mockColumn = new MockColumn;
		for ( $i = 0; $i < $rows; ++$i ) {
			$row = array();
			foreach( $listColumns as $key => $columns ) {
				$row[$key] = $mockColumn->setData( $this->config->columns->$key )->get();
			}
			$rv[] = $row;
		}
		return $rv;
	}
}
