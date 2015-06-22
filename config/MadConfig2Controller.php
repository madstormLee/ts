<?
class MadConfig2Controller extends MadConfigConverter {
	protected $template = MAD . 'proto/Controller.php';

	function setConfig( Madconfig $config ) {
		if( $config->name ) {
			$this->setFileName( $config->name . 'Controller.php' );
		}
		return parent::setConfig( $config );
	}
}
