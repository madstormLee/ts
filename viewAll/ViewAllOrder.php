<?
class ViewAllOrder implements IteratorAggregate {
	private $no = 0;

	private $relNo = array( 0 );
	private $orderNo = array( 0 );

	private $depth = 0;
	private $currentData = null;

	private $data = array();

	function __construct() {
	}
	function getTitleNo() {
		return implode( '.', $this->orderNo );
	}
	function getTree() {
		return new MadTree( $this->data );
	}
	function getData() {
		return $this->data;
	}
	function getCurrent() {
		return $this->currentData;
	}
	function getIterator() {
		return new ArrayIterator( $this->data );
	}
	function add( $title, $page ) {
		$orderNo = ++ $this->orderNo[ $this->depth ];

		$this->currentData = array(
				'no' => $this->no,
				'relNo' => $this->relNo[$this->depth],
				'titleNo' => $this->getTitleNo(),
				'orderNo' => $orderNo,
				'title' => $title,
				'page' => $page,
				);
		++ $this->no;
		$this->data[] = $this->currentData;
		return $this;
	}
	function in() {
		array_push( $this->orderNo, 0 );
		array_push( $this->relNo, $this->no );
		++ $this->depth;
	}
	function out() {
		array_pop( $this->orderNo );
		array_pop( $this->relNo );
		-- $this->depth;
	}
	function __toString() {
		$rv = '';
		if ( $this->currentData ) {
			$rv = $this->getTitleNo() . '. ' . $this->currentData['title'];
		}
		return $rv;
	}
}
