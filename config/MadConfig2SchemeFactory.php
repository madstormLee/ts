<?
class MadConfig2SchemeFactory {
	private static $databases = array(
		'oracle' => 'MadConfig2SchemeOracle',
		'mysql' => 'MadConfig2Scheme',
		'default' => 'MadConfig2Scheme',
	);
	static function create( $database ) {
		$database = strToLower( $database );
		if ( isset( self::$databases[$database] ) ) {
			$target = self::$databases[$database];
		} else {
			$target = self::$databases['default'];
		}
		return new $target;
	}
}
