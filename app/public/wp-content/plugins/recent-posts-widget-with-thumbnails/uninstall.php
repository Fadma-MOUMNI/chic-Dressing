<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Recent_Posts_Widget_With_Thumbnails
 * @author    Kybernetik Services <wordpress@kybernetik.com.de>
 * @license   GPL-2.0+
 * @link      https://wordpress.org/plugins/recent-posts-widget-with-thumbnails/
 * @copyright 2014 
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// if not allowed to delete plugins go to plugins page
if ( ! current_user_can( 'delete_plugins' ) ) {
	$text = 'Sorry, you are not allowed to delete plugins for this site.';
	wp_die( esc_html__( $text ) );
}

/*
// if wrong referer exit
check_admin_referer( 'bulk-plugins' );

//$_POST = from the plugin form; $_GET = from the FTP details screen.
$status	= isset( $_GET[ 'plugin_status' ] )	? $_GET[ 'plugin_status' ] : 'all';
$page 	= isset( $_GET[ 'paged' ] )			? $_GET[ 'paged' ] : '1';
$s		= isset( $_GET[ 's' ] )				? $_GET[ 's' ] : '';

$plugins = isset( $_REQUEST[ 'checked' ] ) ? (array) $_REQUEST[ 'checked' ] : array();

// if no plugins to delete go to plugins page
if ( empty( $plugins ) ) {
	wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
	exit;
}

// if current plugin not in list go to plugins page
if ( false === array_search ( dirname( plugin_basename( __FILE__ ) ) . '/recent-posts-widget-with-thumbnails.php', $plugins ) ) {
	wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
	exit;
}
*/

// clean up the database considering multisite installation
if ( is_multisite() ) {

	// get registered site IDs
	$site_ids = array();
	if ( version_compare( get_bloginfo( 'version' ), '4.6', '>=' ) ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			$site_ids[] = $site->id;
		}
	} else {
		$sites = wp_get_sites();
		foreach ( $sites as $site ) {
			$site_ids[] = $site[ 'blog_id' ];
		}
	}

	if ( empty ( $site_ids ) ) return;

	foreach ( $site_ids as $site_id ) {
		// switch to next blog
		switch_to_blog( $site_id );

		// remove settings
		delete_option( 'widget_recent-posts-widget-with-thumbnails' );
	}
	// restore the current blog, after calling switch_to_blog()
	restore_current_blog();
} else {
	// remove settings
	delete_option( 'widget_recent-posts-widget-with-thumbnails' );
}
