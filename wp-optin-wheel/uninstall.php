<?php
// Fired when the plugin is uninstalled.
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option('mb-wof-lite-settings');
delete_option('wof-lite-dev-version');

// Delete the logs table & file.
$path = plugin_dir_path(__FILE__) . 'code/services/class-log-service.php';
include_once $path;
\MABEL_WOF_LITE\Code\Services\Log_Service::drop_all_logs();

// Delete the wheels.
global $wpdb;
$wpdb->delete($wpdb->posts, ['post_type' => 'wof_wheel'] );

?>