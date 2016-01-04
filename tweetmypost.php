<?php
/**
 * Tweet my Post
 *
 * A plugin that publishes your post to a Twitter account.
 *
 * @package   Tweet_my_Post
 * @author    Éverton Arruda <root@earruda.eti.br>
 * @license   GPL-2.0+
 * @link      http://earruda.eti.br
 * @copyright 2014 Éverton Arruda
 *
 * @wordpress-plugin
 * Plugin Name:       Tweet my Post
 * Plugin URI:        http://earruda.eti.br
 * Description:       A plugin that publishes your post to a Twitter account.
 * Version:           0.0.1
 * Author:            Éverton Arruda
 * Author URI:        http://earruda.eti.br
 * Text Domain:       tweetmypost
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-tweetmypost.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Tweet_my_Post', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Tweet_my_Post', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Tweet_my_Post', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-tweetmypost-admin.php' );
	add_action( 'plugins_loaded', array( 'Tweet_my_Post_Admin', 'get_instance' ) );

}
