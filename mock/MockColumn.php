<?
class MockColumn {
	private $data;
	function setData( $data ) {
		$this->data = $data;
		return $this;
	}
	function get() {
		$data = $this->data;
		if ( $data->name == 'no' ) {
			return rand();
		} else if ( preg_match( '/date/i', $data->name ) ) {
			return date('Y-m-d');
		} else {
			return 'mockData';
		}
	}
}
