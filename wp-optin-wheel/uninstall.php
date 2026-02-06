<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'mb-wof-lite-settings' );
delete_option( 'wof-lite-dev-version' );

// Delete the log table & file.
$path = plugin_dir_path(__FILE__) . 'code/services/class-log-service.php';
include_once $path;
\MABEL_WOF_LITE\Code\Services\Log_Service::drop_all_logs();

global $wpdb;
$wpdb->delete($wpdb->posts, ['post_type' => 'wof_wheel'] );
