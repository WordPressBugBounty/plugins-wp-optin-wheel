<?php

namespace MABEL_WOF_LITE
{

	use MABEL_WOF_LITE\Code\Controllers\Public_Controller;
	use MABEL_WOF_LITE\Code\Services\Log_Service;
	use MABEL_WOF_LITE\Core\Common\Html;
	use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;
	use MABEL_WOF_LITE\Core\Common\Managers\Language_Manager;
	use MABEL_WOF_LITE\Core\Common\Managers\Settings_Manager;
	use MABEL_WOF_LITE\Core\Common\Registry;
	use MABEL_WOF_LITE\Code\Controllers\Admin_Controller;

	if(!defined('ABSPATH')){die;}

	class Wheel_Of_Fortune
	{
		/**
		 * @var Language_Manager language manager.
		 */
		protected $language_manager;

		/**
		 * Business_Hours_Indicator constructor.
		 *
		 * @param $dir string
		 * @param $url string
		 * @param $slug string
		 * @param $version string
		 */
		public function __construct($dir, $url, $plugin_base, $name, $version, $settings_key)
		{
			// Init meta info.
			Config_Manager::init($dir, $url, $plugin_base, $version, $settings_key, $name);
		}

		public function run()
		{
			// Init translations.
			$this->language_manager = new Language_Manager();

			// Init settings with defaults.
			Settings_Manager::init( [
				'log' => false
			]);

			// Kick off admin page.
			if(is_admin())
				new Admin_Controller();

			// Kick off public side of things.
			new Public_Controller();

			// Register post type
			Registry::get_loader()->add_action('init',$this,'register_post_type');

			Registry::get_loader()->add_action('plugins_loaded',$this,'upgrade_routine');

			// GDPR
			Registry::get_loader()->add_filter( 'wp_privacy_personal_data_exporters', $this,'register_data_exporters');
			Registry::get_loader()->add_filter( 'wp_privacy_personal_data_erasers', $this,'register_data_erasers');
			Registry::get_loader()->add_action( 'admin_init', $this,'add_suggested_privacy_content',30);

			// Kick off!
			Registry::get_loader()->run();

		}

		public function add_suggested_privacy_content() {
			if(function_exists('wp_add_privacy_policy_content')) {
				$content = Html::view('admin/views/privacy-policy-suggestions',null);
				wp_add_privacy_policy_content( Config_Manager::$name, $content );
			}
		}

		public function register_data_erasers($erasers = []) {

			$erasers[] = [
				'eraser_friendly_name' => Config_Manager::$name,
				'callback' => [ $this,'data_eraser' ],
			];
			return $erasers;

		}

		public function data_eraser($email) {

			if ( empty( $email ) ) {
				return [
					'items_removed'  => false,
					'items_retained' => false,
					'messages'       => [],
					'done'           => true,
				];
			}

			$messages = [];
			$items_removed  = false;
			$items_retained = false;

			if(Settings_Manager::get_setting('log') === true) {
				$matches = Log_Service::get_logs_from_email($email);
				if(!empty($matches)) {
					$log = Log_Service::get_log();

					foreach($matches as $match){
						$log = str_replace($match,'[entry deleted on request]'.PHP_EOL,$log);
					}
					Log_Service::overwrite($log);

					$items_removed = true;
				}
			}

			return [
				'items_removed'  => $items_removed,
				'items_retained' => $items_retained,
				'messages'       => $messages,
				'done'           => true
			];

		}

		function register_data_exporters($exporters) {
			$exporters[] = [
				'exporter_friendly_name' => Config_Manager::$name,
				'callback' => [ $this,'data_exporter']
			];
			return $exporters;
		}

		function data_exporter( $email) {

			$export_items = [];

			if(Settings_Manager::get_setting('log') === true) {

				$matches = Log_Service::get_logs_from_email($email);

				if(!empty($matches)) {
					$item_id = "wp-optin-wheel-lite-log-".$email;
					$group_id = 'wp-optin-wheel-lite';
					$group_label = __( 'Optin Wheel Plugin Data' );
					$data = [ [
						'name' => __('Logs for user', Config_Manager::$slug),
						'value' => join('<br/>',$matches)
					] ];

					$export_items[] = [
						'group_id'    => $group_id,
						'group_label' => $group_label,
						'item_id'     => $item_id,
						'data'        => $data
					];

				}

			}

			return [
				'data' => $export_items,
				'done' => true,
			];

		}

		function register_post_type() {
			register_post_type('wof_wheel', [
				'public' => false,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
			] );
		}

		public function upgrade_routine() {
			$version = get_option('wof-lite-dev-version');

			if($version !== Config_Manager::$version) {

				// Update database
				global $wpdb;
				$charset_collate = $wpdb->get_charset_collate();
				$table_name = $wpdb->prefix . 'wof_lite_optins';

				$sql = 'CREATE TABLE ' . $table_name . ' (
					id int(11) NOT NULL AUTO_INCREMENT,
					wheel_id int(11) NOT NULL,
					email varchar(100) NOT NULL,
					created_date datetime NOT NULL,
					PRIMARY KEY  (id)
				) ' .$charset_collate. ' AUTO_INCREMENT=1;';

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta($sql);

				update_option('wof-lite-dev-version', Config_Manager::$version,true);
			}
		}

	}
}