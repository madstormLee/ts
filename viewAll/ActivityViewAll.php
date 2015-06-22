<?
//madTodo order를 미리 만들어서 저장하는 쪽이 낫다.
// headers를 없앨 것. 나중에 시간 남으면...
class ActivityViewAll extends MadCacheView {
	private $version;
	private $page = 1;
	private $template;
	private $list;
	private $phpStorm;
	private $pageDirection = 'portrait';

	function __construct( $view = '' ) {
		parent::__construct( $view );
		$this->phpStorm = PhpStorm::getInstance();
		$this->version = 1;
		$this->order = new ViewAllOrder;

		$this->template = new MadTemplate( 'viewAllTemplate' );
		$this->template->setData( array(
					'title' => $this->phpStorm->info->label,
					'documentType' => '액티비티 정의서',
					'version' => $this->version,
					'page' => $this->page,
					));

		$this->list = new ActivityList( $this->phpStorm->getDir('diagrams') . 'activity/' );
	}
	function getFrontPage() {
		$view = new MadView('formats/documentFront');
		$view->version = $this->version;
		$front->pageDirection = $this->pagedirection;
		$view->phpStorm = $this->phpStorm;
		$view->documentType = '액티비티 정의서';
		return $view;
	}
	function getListView() {
		$this->order->add('액티비티 목록', $this->page);

		$view = new MadView('Activity/list');
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
		$componentListView = $this->getListView();

		$this->order->add('인터페이스 정의', ++$this->page);
		$this->order->in();

		$mainContents = $this->getMainContents();

		$tableOfContents = new MadView('tableOfContents');
		$tableOfContents->order = $this->order;

		$contents .= $this->getFrontPage();
		$contents .= $tableOfContents;
		$contents .= $componentListView;
		$contents .= $mainContents;
		return $contents;
	}
	function getMainContents() {
		$rv = '';
		$view = new MadView( 'Activity/view' );
		foreach( $this->list as $model ) {
			$phpStorm = $this->phpStorm;
			$this->template->page = $this->page;
			$view->model = $model;

			$this->order->add( $model->name, $this->page );
			$headers = "<h2 id='page$this->page'>$this->order</h2>";
			$rv .= $this->template->set('content' , $headers . $view );
			++$this->page;
		}
		return $rv;
	}
}

