<?
class TableDiagram extends MadJson {
	function interpret( $type ) {
		if ( $type == 'oracle' ) {
			$this->data = $this->interpretOracle( $this->data );
		}
	}
	function interpretOracle( $data ) {
		foreach( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[$key] = $this->interpretOracle( $value );
			} else {
				$data[$key] = strToUpper( $value );
			}
		}
		return $data;
	}
}
