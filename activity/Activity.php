<?
class Activity extends MadJson {
	private $db = null;

	function setDb( DatabaseDiagram $db = null ) {
		if ( ! is_null( $db ) ) {
			$this->db = $db;
		}
		return $this;
	}
	function interpret() {
		$db = $this->db;

		if ( $db && $db->server == 'oracle' ) {
			foreach( $this->columns as $column ) {
				$column->name = MadString::create( $column->name )->format('upper|underscore' );
			}
			if ( $db->prefix ) {
				$this->name = MadString::create( $db->prefix . '_' . $this->name )->upper();
			}
		}
	}
	function getList() {
		$list = new DatabaseList($this);
		if ( $this->db ) {
			$list->setDb( $this->db );
		}
		return $list;
	}
}
