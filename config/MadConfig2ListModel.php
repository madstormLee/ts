<?
class MadConfig2ListModel {
	protected $template = MAD . 'proto/Model.php';

	function setData() {
		$view = new MadView( $this->template );
		$view->name = $this->config->name;
		$this->data = "<?\n" . $view->get();
	}
	function setConfig( Madconfig $config ) {
		if( $config->name ) {
			$this->setFileName( $config->name . '.php' );
		}
		return parent::setConfig( $config );
	}
}
