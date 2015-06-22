<?
class ErmController extends MadController {
	function indexAction() {
		$this->view->list = glob('Erm/data/*');
	}
	function saveAction() {
		$ermDir = $this->phpStorm->getDir('erm');
		$uploader = new MadUploader($ermDir);
		return $uploader->upload();
	}
	function writeConfigAction() {
		$erm = new Erm( $this->get->file );
		$configs = new Erm2Configs( $erm );
		
		$this->view->preview = $configs->getPreview();
	}
	function saveConfigsAction() {
		$phpStorm = $this->phpStorm;
		$configDir = $phpStorm->getDir('configs');
		$erm = new Erm( $this->get->file );

		$configs = new Erm2Configs( $erm );
		$configs->setConfigDir( $configDir );
		$configs->setData();

		$cnt = 0; foreach( $configs as $config ) {
			$config->setWriteMode( 'force' );
			$cnt += $config->save();
		}
		$this->js->alert( "$cnt 개의 config file이 생성되었습니다.")->replace('./');
	}
}
