<?
class Web2PdfController extends MadController {
	function downloadAction() {
		$post = $this->params;

		$this->model->setFile( "$post->name.pdf" )
			->setTarget( "$post->name.html" );
		return $this->model->download();
	}
}
