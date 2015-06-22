<?
class ClassModel extends MadModel {
	function getIndex() {
		$rv = array();

		$dir = new MadDir( $this->dir );
		foreach( $dir as $file ) {
			$rv[] = new self( $file );
		}
		return new MadData($rv);
	}
	function getControllerMethods() {
		return new MadJson( 'configs/json/mockClass.json');
	}
	function getModelMethods() {
		return new MadJson( 'configs/json/mockClassModel.json');
	}
}
