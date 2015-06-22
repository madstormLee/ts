<?
//madTodo order를 미리 만들어서 저장하는 쪽이 낫다.
// headers를 없앨 것. 나중에 시간 남으면...
class InterfaceViewAll extends MadCacheView {
	private $version;
	private $page = 1;
	private $template;
	private $list;

	function __construct( $view = '' ) {
		parent::__construct( $view );
		$this->phpStorm = PhpStorm::getInstance();
		$this->version = 1;

		$this->order = new ViewAllOrder;

		$this->template = new MadTemplate( 'viewAllLandscapeTemplate' );
		$this->template->setData( array(
					'title' => $this->phpStorm->info->label,
					'documentType' => '인터페이스 정의서',
					'version' => $this->version,
					'page' => $this->page,
					));

		$this->list = new InterfaceDiagramList;
		$this->phpStorm->getDir('diagrams') . 'interface' ;
		$this->list->setDir( $this->phpStorm->getDir('diagrams') . 'interface/' );
	}
	function getFrontPage() {
		$view = new MadView('formats/documentLandscapeFront');
		$view->version = $this->version;
		$view->type = '인터페이스 정의서';
		$view->phpStorm = $this->phpStorm;
		$view->documentType = '인터페이스 정의서'; 
		return $view;
	}
	function getInterfaceListView() {
		$this->order->add('인터페이스 목록', $this->page);

		$view = new MadView('Interface/list');
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
		$componentListView = $this->getInterfaceListView();

		$this->order->add('인터페이스 정의', ++$this->page);
		$this->order->in();

		$mainContents = $this->getMainContents();

		$contents .= $this->getFrontPage();
		$tableOfContents = new MadView('tableOfContents');
		$tableOfContents->order = $this->order;
		// $contents .= $tableOfContents;
		// $contents .= $componentListView;
		$contents .= $mainContents;
		return $contents;
	}
	function getMainContents() {
		$rv = '';
		$view = new MadView( 'Interface/view' );
		$viewsDir = $this->phpStorm->getDir('views');
		foreach( $this->list as $file => $interface ) {
		foreach( $interface->actions as $action => $row ) {
		$phpStorm = $this->phpStorm;
			$this->template->page = $this->page;
			$view->model = $row;

			$config = new MadConfig( $this->phpStorm->getDir('configs') . baseName( $file ) );
			$previewFile = $viewsDir . $interface->controller ."/$action";
			$preview = new Preview( $previewFile );
			$view->preview = $preview;

			$this->order->add($row->name, $this->page);
			// $headers = "<h2 id='page$this->page'>$this->order</h2>";
			$rv .= $this->template->set('content' ,  $view );
			++$this->page;
		}
		}
		return $rv;
	}
}
