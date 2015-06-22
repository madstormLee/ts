<?
//madTodo order를 미리 만들어서 저장하는 쪽이 낫다.
// headers를 없앨 것. 나중에 시간 남으면...
// 일단, order부터 refactoring 해야 할 지도 모른다.
class ClassViewAll extends MadCacheView {
	private $version;
	private $page = 1;
	private $template;
	private $list;
	private $phpStorm;

	function __construct( $view = '' ) {
		parent::__construct( $view );
		$this->phpStorm = PhpStorm::getInstance();
		$this->version = 1;
		$this->order = new ViewAllOrder;

		$this->template = new MadTemplate( 'viewAllTemplate' );
		$this->template->setData( array(
					'title' => $this->phpStorm->info->label,
					'documentType' => '클래스 정의서',
					'version' => $this->version,
					'page' => $this->page,
					));
		$this->list = new ClassDiagramList( $this->phpStorm->getDir('diagrams'). 'class/' );
	}
	function getFrontPage() {
		$view = new MadView('formats/documentFront');
		$view->version = $this->version;
		$view->type = '클래스 정의서';
		$view->phpStorm = $this->phpStorm;
		$view->documentType = '클래스 정의서'; 
		return $view;
	}
	function getClassListView() {
		$this->order->add('클래스 목록', $this->page);

		$view = new MadView('views/Class/list');
		$view->list = $this->list; 
		$headers = "<h2 id='page{$this->page}'>{$this->order}</h2>";

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
		$componentListView = $this->getClassListView();

		$this->order->add('클래스 정의', ++$this->page);
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
		$page = 1;
		$pageSeparator = "
			</section>
			<section class='a4inside'>
			<p class='pageHeader'>
			<span class='projectTitle'>고객 관계 관리 시스템</span>
			<span class='documentType'>컴포넌트 정의서</span>
			</p>
			<p class='pageFooter'>
			<span class='page'>$page 페이지</span>
			<span class='version'>Ver. 1</span>
			</p>";
			$view = new MadView( 'Class/view' );
		foreach( $this->list as $model ) {
			$phpStorm = $this->phpStorm;
			// $this->template->page = $this->page;

			$view->model = $model;
			$view->controllerMethods = $model->getControllerMethods();
			$view->modelMethods = $model->getModelMethods();
			$view->order = $this->order;
			$view->page = $this->page;
			$this->order->add($model->name, $this->page);

			$headers = "<h2 id='page{$this->page}'>{$this->order}</h2>";
			$viewUnits = explode( '<hr />', $view->get() );

			foreach( $viewUnits as $viewUnit ) {
				$viewUnit = trim($viewUnit);
				if ( empty( $viewUnit ) ) {
					continue;
				}
				$this->order->in();
				$rv .= $this->template->set('content' , $headers . $viewUnit );
				$this->order->out();
			}

			++$this->page;
		}
		return $rv;
	}
}

