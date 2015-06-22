<?
class MadConfig2Views {
	function __construct( MadConfig $config ) {
		$this->targetDir = "views/$config->name/";
	}
	function setData() {
		$view->preset = $this->getPreset();
		return $this;
	}
	function getPreset() {
		$rv = new MadPhpPreset;
		$rv->foreach = '<? foreach( $model as $row ): ?>';
		$rv->endforeach = '<? endforeach; ?>';
		$rv->endif = '<? endif; ?>';
		$rv->elseif = '<? elseif( condition ) : ?>';
		$rv->else = '<? else: ?>';
		return $rv;
	}
}
