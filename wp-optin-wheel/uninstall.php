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

delete_option( 'wof_log_name' );

// Delete the wheels incl. their meta. 'wof_wheel' is the post type name that was registered by mistake in older versions.
$wheel_ids = get_posts( [
	'post_type'   => [ 'wof_lite_wheel', 'wof_wheel' ],
	'post_status' => 'any',
	'numberposts' => -1,
	'fields'      => 'ids',
] );
foreach( $wheel_ids as $wheel_id ) {
	wp_delete_post( $wheel_id, true );
}
