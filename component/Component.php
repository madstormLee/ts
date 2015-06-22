<?
class Component extends MadJson {
	function getIndex( $dir='.' ) {
		$index = new MadData;
		foreach( glob("$dir/*", GLOB_ONLYDIR) as $file ) {
			$row = new MadData;
			$file = new MadFile( $file );
			$row['file'] = $file;
			$row['id'] = $file;
			$row['name'] = $file->getBasename();
			$row['label'] = $file->getBasename();
			$row['description'] = $file->getBasename();
			$index[] = $row;
		}
		return $index;
	}
	// from MadComponentList
	function getIndex2( $dir = PROJECT_ROOT ) {
		$dir = new MadDir( $dir );
		$dir->setOptions(GLOB_ONLYDIR);
		foreach( $dir as $row ) {
			if ( ! $row->isDir() ) {
				continue;
			}
			$row = new MadData;
			$row->id = lcFirst( $row->getBasename() );
			$row->name = ucFirst($row->id);

			// component need index or controller
			if ( ! is_file( $row->getDir() . "/{$row->name}Controller.php" )  ) {
				continue;
			}
			$row->files = $row->filter('^\.');
			$this->{$row->id} = $row;
		}
	}
	function getControllerMethods() {
		return new MadJson( 'configs/json/mockClass.json');
	}
	function getModelMethods() {
		return new MadJson( 'configs/json/mockClassModel.json');
	}
}

