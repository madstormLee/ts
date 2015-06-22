<?
class DatabaseDiagram extends MadJson {
	function getList() {
		return new DatabaseList($this);
	}
}
