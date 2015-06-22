<?
class Config extends MadModel {
	function getIndex() {
		$index = new MadDir('config/data');
		return $index;
	}
}
