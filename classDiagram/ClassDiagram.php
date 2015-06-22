<?
class ClassDiagram extends MadJson {
	function __construct( $file ) {
		parent::__construct( $file );
	}
	function getIndex() {
		return new MadDir('data/ClassDiagram/*.json');
	}
}
