<?
class Sitemap extends MadModel {
	private $index = array();

	function fetch( $id ) {
		$this->id = $id;
	}
	function getIndex() {
		$this->data = new MadJson( $this->id );
		$this->init( $this->data );
		return $this->data;
	}
	public function findPath( $path ) {
		$args = explode( '/', $path );
		$rv = $this->data;
		foreach( $args as $arg ) {
			if ( $rv->$args ) {
				$rv = $rv->args;
			}
		}
		return $rv;
	}
	private function init( $data, $parentId = '' ) {
		foreach( $data as $key => &$row ) {
			if( ! empty( $parentId ) ) {
				$row->id = $parentId . '/' . $key;
			} else {
				$row->id = $key;
			}
			$this->index[$row->id] = $row;
			$row->parentId = $parentId;
			$row->href = '~/' . $row->id;
			if ( isset( $row->subs ) ) {
				$this->init( $row->subs, $key );
			}
		}
	}
}
