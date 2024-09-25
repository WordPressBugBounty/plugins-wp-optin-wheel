<?php

namespace MABEL_WOF_LITE\Code\Services {

	class Log_Service {

        private static function get_file_name() {

            $name = get_option('wof_log_name');

            if( empty( $name ) ) {
                $name = str_replace('.','', uniqid('wof_log_', true ));
                add_option( 'wof_log_name', $name , '', false);
            }

            return $name;

        }

        private static function create_dir() {

            $path = wp_upload_dir()['basedir'] . '/' . trailingslashit('wof');
            $name = self::get_file_name();

            if( ! file_exists( $path ) ) {
                wp_mkdir_p( $path );
                @file_put_contents( $path . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
                @file_put_contents( $path . '/.htaccess', '<Files "'. $name.'.txt">' . PHP_EOL . 'Order Allow,Deny' . PHP_EOL . 'Deny from all' . PHP_EOL . '</Files>' . PHP_EOL .'Options -Indexes' );
            }

            return $path . $name . '.txt'; 

        }

        private static function switch_dir() {

            $old_path = trailingslashit(WP_CONTENT_DIR) . 'wof-log.txt';
            $old_path_2 = wp_upload_dir()['basedir'] . '/wof/wof-log.txt';
            $new_path = self::create_dir();

            if( ! file_exists( $new_path ) && ( file_exists( $old_path ) || file_exists( $old_path_2 ) ) ) {
                if( file_exists( $old_path ) ) rename($old_path, $new_path);
                if( file_exists( $old_path_2 ) ) rename($old_path_2, $new_path);
            }

            return $new_path;
        }

		public static function get_logs_from_email($email) {
            $file_url = self::switch_dir();
			$matches = [];
			$handle = @fopen($file_url, "r");
			if ($handle)
			{
				while (!feof($handle))
				{
					$buffer = fgets($handle);
					if(strpos($buffer, $email) !== FALSE)
						$matches[] = $buffer;
				}
				fclose($handle);
			}
			return $matches;
		}

		public static function get_log() {
            $file_url = self::switch_dir();
            if( ! file_exists( $file_url ) ) return '';

			$content = file_get_contents($file_url);
			return $content === false ? '' : $content;

		}

		public static function log($message) {
            $file_url = self::switch_dir();
			$message = current_time('mysql') .' : '.$message;
			file_put_contents($file_url,$message . PHP_EOL, FILE_APPEND);
		}

		public static function overwrite($str) {
            $file_url = self::switch_dir();
			file_put_contents($file_url, $str);
		}

		public static function clear() {
            $file_url = self::switch_dir();
			file_put_contents($file_url,'');
		}

		public static function is_in_log($email,$wheel_id) {
			global $wpdb;
			$table = $wpdb->prefix.'wof_lite_optins';

			$results = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT email FROM '.$table.' WHERE email = %s AND wheel_id = %d',
					hash('md5',$email),
					$wheel_id
				)
			);

			return count($results) > 0;
		}

		public static function add_to_db_log($email, $wheel_id){
			global $wpdb;
			$table = $wpdb->prefix.'wof_lite_optins';
			$wpdb->insert(
				$table,
				[ 'wheel_id' => $wheel_id, 'email' => hash('md5',$email),'created_date' => current_time('Y-m-d H:i:s',true) ]
			);
		}

		public static function drop_all_logs(){
			global $wpdb;
			$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix.'wof_lite_optins' );
			self::clear();
		}

	}
}