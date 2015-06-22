<?
class ErmList extends MadFileList {
	function __construct( $dir = '' ) {
		parent::__construct( $dir );
		$this->setExt( 'erm' );
		$this->modelName = 'MadFile';
	}
}
