<?php
/**
 * @wordpress-plugin
 * Plugin Name: Media Folders Lite
 * Description: Upload files to custom folders in WP Media Library.
 * Version:     1.0.2
 * Author:      jbulies
 * Author URI:  https://www.instagram.com/pepebulies
 * Text Domain: media-folders-lite
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'MEDIAFOLDERS_PLUGIN_FILE_NAME', basename(__FILE__) );
define( 'MEDIAFOLDERS_PLUGIN_BASENAME', plugin_basename(__FILE__) );
define( 'MEDIAFOLDERS_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'MEDIAFOLDERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MEDIAFOLDERS_VERSION', '1.0.2' );
define( 'MEDIAFOLDERS_TEXT_DOMAIN', 'media-folders-lite' );
define( 'MEDIAFOLDERS_PLUGIN_NAME', 'Media Folders Lite' );

// Include the .php files
foreach( glob( MEDIAFOLDERS_PLUGIN_DIR . '/includes/*.php') as $phpfile ) {
    require $phpfile;
}

// Add languages
function mediafolders_languages() {
	load_plugin_textdomain( 'media-folders-lite', false, 'media-folders-lite/languages' );
}
add_action('init', 'mediafolders_languages');

// Tasks after activation
function mediafolders_activation() {
    set_transient( 'admin-notice', true, 5 );
    add_option( 'mediafolders_option', NULL );
}
register_activation_hook( __FILE__, 'mediafolders_activation' );