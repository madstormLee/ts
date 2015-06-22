<?
//madTodo order를 미리 만들어서 저장하는 쪽이 낫다.
// headers를 없앨 것. 나중에 시간 남으면...
class TableViewAll extends MadCacheView {
	private $version;
	private $page = 1;
	private $template;
	private $db;

	function __construct( $view = '' ) {
		parent::__construct( $view );
		$this->phpStorm = PhpStorm::getInstance();
		
		$this->version = 1;

		$this->order = new ViewAllOrder;

		$this->template = new MadTemplate( 'viewAllTemplate' );
		$this->template->setData( array(
					'title' => $this->phpStorm->info->label,
					'documentType' => '테이블 정의서',
					'version' => $this->version,
					'page' => $this->page,
					));
		$this->list = new TableDiagramList( $this->phpStorm->getDir('diagrams') . 'table/' );
	}
	function getFrontPage() {
		$view = new MadView('formats/documentFront');
		$view->version = $this->version;
		$view->phpStorm = $this->phpStorm;
		$view->documentType = '테이블 정의서'; 

		return $view;
	}
	function getListView() {
		if ( ! $this->list ) {
			$this->setList();
		}
		$this->order->add('테이블 목록', $this->page);

		$view = new MadView('TableDiagram/list');
		$view->list = $this->list; 
		$headers = "<h2 id='page$this->page'>$this->order</h2>";

		return $this->template->set('content' , $headers . $view )->getContent();
	}
	function updateCache( $contents = '' ) {
		if ( empty( $contents ) ) {
			$contents = $this->getContents();
		}
		return parent::updateCache( $contents );
	}
	function getContents() {
		$contents = '';

		// 이게 순서에 걸린다.
		$listView = $this->getListView();

		$this->order->add('테이블 정의', ++$this->page);
		$this->order->in();

		$mainContents = $this->getMainContents();

		$contents .= $this->getFrontPage();
		$contents .= $this->getTableOfContentsView();
		$contents .= $listView;
		$contents .= $mainContents;
		return $contents;
	}
	function getMainContents() {
		$rv = '';
		$view = new MadView( 'TableDiagram/view' );
		foreach( $this->list as $file => $table ) {
			$this->template->page = $this->page;
			$view->page = $this->page;

			$view->table = $table;

			$this->order->add( $table->name, $this->page);
			$headers = "<h2 id='page$this->page'>$this->order</h2>";

			$rv .= $this->template->set('content' , $headers . $view );
			++$this->page;
		}
		return $rv;
	}
	function getTableOfContentsView() {
		$view = new MadView('tableOfContents');
		$view->order = $this->order;
		return $view;
	}

}
