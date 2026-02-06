<?php

namespace MABEL_WOF_LITE\Code\Services {

	use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;

	class Helper_Service {

		public static $iv = 'S3puVVRNZ2tKZHlsbHdmRzlTVmFJUT09';

		public static function encrypt($string) {
			$key = hash( 'sha256', Config_Manager::$settings_key );
			$iv = substr( hash( 'sha256', self::$iv ), 0, 16 );
			return base64_encode( openssl_encrypt( $string, "AES-256-CBC", $key, 0, $iv ) );
		}

		public static function decrypt($string) {
			$key = hash( 'sha256', Config_Manager::$settings_key );
			$iv = substr( hash( 'sha256', self::$iv ), 0, 16 );
			return  openssl_decrypt( base64_decode( $string ), "AES-256-CBC", $key, 0, $iv );
		}
        
	}

}