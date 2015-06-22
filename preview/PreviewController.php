<?
class PreviewController extends MadController {
	function listAction() {
		$list = $this->model->getList();
		
		$this->main->list = $list;
		return $this->main;
	}
	function writeAction() {
		$this->setLayout( new MadView('Interface/writeLayout') );
		$this->layout->footer = new MadView('footer');
		$this->js->add('/mad/js/prototype');
		$this->style->add('~/css/Preview/view');
		$this->style->clear()
			->add("~/css/writeBase")
			->add("~/css/Preview/write")
			->add("~/css/Interface/write");

		$preview = new Preview( $this->get->file );

		
		$this->main->preview = $preview;
		return $this->main;
	}
	function saveAction() {
		$post = $this->post;
		$file = urlDecode( $this->get->file );
		$content = stripSlashes( urlDecode( $post->content ) );
		$preview = new Preview( $file );
		
		if ( $preview->saveContents( $content ) == 1 ) {
			print $content;
		} else {
			print 0;
		}
		die;
	}
	function saveDefaultAction() {
		$preview = new Preview( $this->get->file );
		if( $preview->save() == 1 ) {
			print $preview;
		} else {
			print 0;
		}
	}
	function getDefaultAction() {
		$preview = new Preview( $this->get->file );
		print $preview->getDefault();
		die;
	}
	function getUrlAction() {
		return file_get_contents( $this->post->siteUrl );
	}
	function viewAction() {
		return new Preview( $this->get->file );
	}
	function insertAction() {
		if ( $this->model->insert($this->post) ) {
			return true;
		}
		return false;
	}
	function updateAction() {
		if ( $this->model->update($this->post) ) {
			return true;
		}
		return false;
	}
	function deleteAction() {
		if ( $this->model->delete( $this->get->no ) ) {
			return true;
		}
		return false;
	}
	function getMessageCode( $code ) {
		return MadMessageCodeManager::getInstance()->$code();
	}
}
