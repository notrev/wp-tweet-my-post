<?php
/**
 * Tweet_my_Post
 *
 * @package   Tweet_my_Post_Admin
 * @author    Éverton Arruda <root@earruda.eti.br>
 * @license   GPL-2.0+
 * @link      http://earruda.eti.br
 * @copyright 2014 Éverton Arruda
 */

require_once( 'includes/class-twitterpublisher.php' );

/**
 * Tweet_my_Post_Admin class. This class works with the * administrative side of
 * the WordPress site.
 *
 * @package Tweet_my_Post_Admin
 * @author  Éverton Arruda <root@earruda.eti.br>
 */
class Tweet_my_Post_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = Tweet_my_Post::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->plugin_settings_slug = $this->plugin_slug . '_settings';

		// Load admin style sheet and JavaScript.
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Register the fields for the settings page
		add_action( 'admin_init', array( $this, 'setup_settings_fields' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Add meta box to post type content page
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// Add action for publishing the post to twitter after it is published in wordpress
		add_action( 'save_post', array( $this, 'publish_to_twitter' ), 10, 2 );

		// Register action for displaying notice after save_post
		add_action( 'admin_notices', array( $this, 'display_notice' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		/*
		 * Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Tweet_my_Post::VERSION );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Tweet_my_Post::VERSION );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		// Add a settings page for this plugin to the Settings menu.
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Tweet my Post Settings', $this->plugin_slug ),
			__( 'Tweet my Post', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Display admin page
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include( 'views/admin.php' );
	}

	/**
	 * Callback for add_settings_section method
	 *
	 * @since    1.0.0
	 */
	public function admin_page_section_twitter() {
		echo __( 'Settings related to the Twitter account', $this->plugin_slug );
	}

	/**
	 * Callback for add_settings_section method
	 *
	 * @since    1.0.0
	 */
	public function admin_page_section_plugin() {
		echo __( 'Settings related to Tweet my Post plugin', $this->plugin_slug );
	}

	/**
	 * Register and build fields used to store Twitter and Plugin related settings
	 * using WP's Settings API
	 *
	 * @since    1.0.0
	 */
	public function setup_settings_fields() {
		// Register fields to Twitter section
		register_setting( $this->plugin_settings_slug, $this->plugin_slug . '_twitter_api_key' );
		register_setting( $this->plugin_settings_slug, $this->plugin_slug . '_twitter_api_key_secret' );
		register_setting( $this->plugin_settings_slug, $this->plugin_slug . '_twitter_access_token' );
		register_setting( $this->plugin_settings_slug, $this->plugin_slug . '_twitter_access_token_secret' );
		register_setting( $this->plugin_settings_slug, $this->plugin_slug . '_plugin_default_publish_value' );
		register_setting( $this->plugin_settings_slug, $this->plugin_slug . '_plugin_admin_notice' );

		/*
		 * Twitter settings section
		 */
		add_settings_section(
			$this->plugin_settings_slug . '_twitter',
			__( 'Twitter Settings', $this->plugin_slug ),
			array( $this, 'admin_page_section_twitter' ),
			$this->plugin_slug
		);

		// Adding fields
		add_settings_field(
			$this->plugin_slug . '_twitter_api_key',
			__( 'API Key', $this->plugin_slug ),
			array( $this, 'display_twitter_api_key_field'),
			$this->plugin_slug,
			$this->plugin_settings_slug . '_twitter'
		);

		add_settings_field(
			$this->plugin_slug . '_twitter_api_key_secret',
			__( 'API Key Secret', $this->plugin_slug ),
			array( $this, 'display_twitter_api_key_secret_field'),
			$this->plugin_slug,
			$this->plugin_settings_slug . '_twitter'
		);

		add_settings_field(
			$this->plugin_slug . '_twitter_access_token',
			__( 'Access Token', $this->plugin_slug ),
			array( $this, 'display_twitter_access_token_field'),
			$this->plugin_slug,
			$this->plugin_settings_slug . '_twitter'
		);

		add_settings_field(
			$this->plugin_slug . '_twitter_access_token_secret',
			__( 'Access Token Secret', $this->plugin_slug ),
			array( $this, 'display_twitter_access_token_secret_field'),
			$this->plugin_slug,
			$this->plugin_settings_slug . '_twitter'
		);

		/*
		 * Plugin settings section
		 */
		add_settings_section(
			$this->plugin_settings_slug . '_plugin',
			__( 'Plugin Settings', $this->plugin_slug ),
			array( $this, 'admin_page_section_plugin' ),
			$this->plugin_slug
		);

		// Adding fields
		add_settings_field(
			$this->plugin_slug . '_plugin_default_publish_value',
			__( 'Default value for post publishing', $this->plugin_slug ),
			array( $this, 'display_plugin_default_publish_value'),
			$this->plugin_slug,
			$this->plugin_settings_slug . '_plugin'
		);
	}

	/**
	 * Display HTML for a text input field
	 *
	 * @since    1.0.0
	 * @param    string     $field_name     Field name
	 * @param    string     $field_value    Field value
	 */
	private function display_text_input_field( $field_name, $field_value ) {
		include( 'views/generic-text-input-field.php' );
	}

	/**
	 * Display HTML for a text input field
	 *
	 * @since    1.0.0
	 * @param    string     $field_name     Field name
	 * @param    string     $field_value    Field value
	 */
	private function display_radio_input_field( $field_name, $field_value, $field_options ) {
		include( 'views/generic-radio-input-field.php' );
	}

	/**
	 * Display HTML for the Twitter API Key field
	 *
	 * @since    1.0.0
	 */
	public function display_twitter_api_key_field() {
		$field_name  = $this->plugin_slug . '_twitter_api_key';
		$field_value = get_option( $field_name );

		if ( false == $field_value ) {
			$field_value = '';
		}

		$this->display_text_input_field( $field_name, $field_value );
	}

	/**
	 * Display HTML for the Twitter API Key Secret field
	 *
	 * @since    1.0.0
	 */
	public function display_twitter_api_key_secret_field() {
		$field_name  = $this->plugin_slug . '_twitter_api_key_secret';
		$field_value = get_option( $field_name );

		if ( false == $field_value ) {
			$field_value = '';
		}

		$this->display_text_input_field( $field_name, $field_value );
	}

	/**
	 * Display HTML for the Twitter Access Token
	 *
	 * @since    1.0.0
	 */
	public function display_twitter_access_token_field() {
		$field_name  = $this->plugin_slug . '_twitter_access_token';
		$field_value = get_option( $field_name );

		if ( false == $field_value ) {
			$field_value = '';
		}

		$this->display_text_input_field( $field_name, $field_value );
	}

	/**
	 * Display HTML for the Twitter Access Token Secret
	 *
	 * @since    1.0.0
	 */
	public function display_twitter_access_token_secret_field() {
		$field_name  = $this->plugin_slug . '_twitter_access_token_secret';
		$field_value = get_option( $field_name );

		if ( false == $field_value ) {
			$field_value = '';
		}

		$this->display_text_input_field( $field_name, $field_value );
	}

	/**
	 * Display HTML for the plugin's default publish value
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_default_publish_value() {
		$field_name  = $this->plugin_slug . '_plugin_default_publish_value';
		$field_value = get_option( $field_name );
		$field_options = array(
			'on' => __( 'On', $this->plugin_slug ),
			'off' => __( 'Off', $this->plugin_slug )
		);

		if ( false == $field_value ) {
			$field_value = '';
		}

		$this->display_radio_input_field( $field_name, $field_value, $field_options );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
	}

	/**
	 * Add Tweet my Post metabox on post type contents page.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->plugin_slug,
			'Tweet my Post',
			array( $this, 'display_meta_box_admin_page' ),
			'post',
			'side',
			'low'
		);
	}

	/**
	 * Render meta box to be displayed in post type contents page.
	 *
	 * @since     1.0.0
	 */
	public function display_meta_box_admin_page() {
		$is_checked = get_option( $this->plugin_slug . '_plugin_default_publish_value' );

		if ( 'on' == $is_checked ) {
			$is_checked = true;
		}

		include_once( 'views/meta_box.php' );
	}

	/**
	 * Publish post to twitter.
	 *
	 * @since    1.0.0
	 * @param    int        $post_id    Post ID
	 * @param    object     $post       Post object
	 * @return   null       Return early if post should not be published
	 */
	public function publish_to_twitter( $post_id, $post ) {
		// TODO: Instantiate TwitterPlublisher and publish the new post
		$is_to_publish = ( isset( $_POST['tweetmypost'] ) && $_POST['tweetmypost'] ) ? true : false;

		if ( !$is_to_publish ) {
			return;
		}

		$twitter_settings = array(
			'oauth_access_token' => get_option( $this->plugin_slug . '_twitter_api_key' ),
			'oauth_access_token_secret' => get_option( $this->plugin_slug . '_twitter_api_key_secret' ),
			'consumer_key' => get_option( $this->plugin_slug . 'twitter_access_token' ),
			'consumer_secret' => get_option( $this->plugin_slug . '_twitter_access_token_secret' )
		);

		$tweet = $this->get_tweet_message( $post );

		$twitter_publisher = new TwitterPublisher();
		$twitter_publisher->set_settings( $twitter_settings );

		$prefix = '[' . __( 'Tweet my Post', $this->plugin_slug ) . '] ';

		// Try to publish. Treat errors
		try {
			$response = $twitter_publisher->publish( $tweet );
			$response = json_decode( $response );

			if ( isset( $response['created_at'] ) ) {
				// successful
				$notice = array(
					'status' => 'updated',
					'message' => $prefix . __( 'Post was successfully published to Twitter', $this->plugin_slug )
				);
			} else {
				// unsuccessful
				$notice = array(
					'status' => 'error',
					'message' => $prefix . $response['errors'][0]['message']
				);
			}
		} catch ( Exception $e ) {
			$notice = array(
				'status' => 'error',
				'message' => $prefix . $e->getMessage()
			);
		} finally {
			update_option( $this->plugin_slug . '_plugin_admin_notice', json_encode( $notice ) );
		}
	}

	/**
	 * Display a notice with informations from the last response
	 *
	 * @since    1.0.0
	 * @return   null       Return early if there is no information from the last response.
	 */
	public function display_notice() {
		$notice = get_option( $this->plugin_slug . '_plugin_admin_notice' );
		update_option( $this->plugin_slug . '_plugin_admin_notice', '' );

		$notice = json_decode( $notice );

		if ( null == $notice ) {
			return;
		}

		if ( ! isset( $notice->status ) || ! isset( $notice->message ) ) {
			return;
		}

		$type = $notice->status;
		$message = $notice->message;

		include( 'views/admin-notice.php' );
	}

	/**
	 * Prepare the message to be tweeted
	 *
	 * @since    1.0.0
	 * @return   string     Message prepared for a tweet
	 */
	private function get_tweet_message( $post ) {
		$tweet_message = "{post_title} {post_permalink}";

		$tweet_data = array(
			'post_title'     => $post->post_title,
			'post_permalink' => get_permalink( $post->ID )
		);

		/*
		 * Check length of the post_title.
		 * Length must not be more than 96, because twitter shortens URL to 23
		 * chars.
		 *
		 */
		if ( strlen( $tweet_data['post_title'] ) > 96 ) {
			$tweet_data['post_title'] = mb_strimwidth(
				$tweet_data['post_title'], 0, 96, "... "
			);
		}

		// replace patterns
		$patterns = array();
		$patterns[0] = '/{post_title}/';
		$patterns[1] = '/{post_permalink}/';

		$replacements = array();
		$replacements[0] = $tweet_data['post_title'];
		$replacements[1] = $tweet_data['post_permalink'];

		return preg_replace( $patterns, $replacements, $tweet_message );
	}
}
