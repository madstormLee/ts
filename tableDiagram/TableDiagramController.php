<?
class TableDiagramController extends MadController {
	function indexAction() {
		$list = new TableDiagramList( $this->phpStorm->getDir('diagrams') . 'table/' );
		$this->view->list = $list;
	}
	function viewAction() {
		$table = new TableDiagram( $this->get->file );
		$table->interpret('oracle');
		$this->view->table = $table;
	}
	function definitionListAction() {
		$list = new ColumnList( $this->get->table );
		$this->view->list = $list;

		$table = new Table( $this->get->table );
		$table->setInfo();
		$table->database = $this->get->database;

		$this->view->table = $table;
	}
}
