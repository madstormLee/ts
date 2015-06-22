<?
//madTodo order를 미리 만들어서 저장하는 쪽이 낫다.
// headers를 없앨 것. 나중에 시간 남으면...
class ComponentViewAll extends MadCacheView {
	private $version;
	private $page = 1;
	private $template;
	private $list;

	function __construct( $view = '' ) {
		parent::__construct( $view );
		$this->phpStorm = PhpStorm::getInstance();
		$this->version = 1;

		$this->order = new ViewAllOrder;

		$this->template = new MadTemplate( 'viewAllTemplate' );
		$this->template->setData( array(
					'title' => $this->phpStorm->info->label,
					'documentType' => '컴포넌트 정의서',
					'version' => $this->version,
					'page' => $this->page,
					));

		$this->list = new ComponentList( $this->phpStorm->getDir('diagrams') . 'component/' );
	}
	function getFrontPage() {
		$view = new MadView('formats/documentFront');
		$view->version = $this->version;
		$view->type = '컴포넌트 정의서';
		$view->phpStorm = $this->phpStorm;
		$view->documentType = '컴포넌트 정의서'; 
		return $view;
	}
	function getComponentListView() {
		$this->order->add('컴포넌트 목록', $this->page);

		$view = new MadView('Component/list');
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
		$componentListView = $this->getComponentListView();

		$this->order->add('컴포넌트 정의', ++$this->page);
		$this->order->in();

		$mainContents = $this->getMainContents();

		$contents .= $this->getFrontPage();
		$contents .= $this->getTableOfContentsView();
		$contents .= $componentListView;
		$contents .= $mainContents;
		return $contents;
	}
	function getMainContents() {
		$rv = '';
		$view = new MadView( 'Component/view' );
		foreach( $this->list as $model ) {
			$this->template->page = $this->page;
			$view->model = $model;
			$this->order->add($model->name, $this->page);
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
