<?php
/*
Plugin Name: Lifter LMS Stats
Plugin URI: http://pootlepress.com/
Description: Boilerplate for fast track Pootle Page Builder Addon Development
Author: Shramee
Version: 1.0.0
Author URI: http://shramee.com/
@developer shramee <shramee.srivastav@gmail.com>
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Admin widget class */
require 'inc/class-widget-admin.php';
/** Author widget class */
require 'inc/class-widget-author.php';

/** Admin role */
define( 'LLMSS_Admin', 'administrator' );
/** Author role */
define( 'LLMSS_Author', 'author' );
/** Admin Email */
define( 'LLMSS_ADMIN_EMAIL', 'payments@wixbu.com' );
/** Share percentage */
define( 'LLMSS_Share', 0.7 );

/**
 * Lifter LMS Stats main class
 * @static string $token Plugin token
 * @static string $file Plugin __FILE__
 * @static string $url Plugin root dir url
 * @static string $path Plugin root dir path
 * @static string $version Plugin version
 */
class Lifter_LMS_Stats{

	/** @var Lifter_LMS_Stats Instance */
	private static $_instance = null;

	/** @var string Token */
	public static $token;

	/** @var string Version */
	public static $version;

	/** @var string Plugin main __FILE__ */
	public static $file;

	/** @var string Plugin directory url */
	public static $url;

	/** @var string Plugin directory path */
	public static $path;

	/** @var Lifter_LMS_Stats_Admin Instance */
	public $admin;

	/**
	 * Return class instance
	 * @return Lifter_LMS_Stats instance
	 */
	public static function instance( $file ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}
		return self::$_instance;
	}

	/**
	 * Constructor function.
	 * @param string $file __FILE__ of the main plugin
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct( $file ) {

		self::$token   = 'lifter-lms-stats';
		self::$file    = $file;
		self::$url     = plugin_dir_url( $file );
		self::$path    = plugin_dir_path( $file );
		self::$version = '1.0.0';

		define( 'LLMSS_PATH', self::$path );

		$this->_admin(); //Initiate admin

		//Enqueue admin end JS and CSS
		add_action( 'wp_enqueue_scripts',	[ $this, 'log_page_views' ] );

	}

	/**
	 * Initiates admin class and adds admin hooks
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = Lifter_LMS_Stats_Admin::instance();

		//Enqueue admin end JS and CSS
		add_action( 'admin_enqueue_scripts',	[ $this->admin, 'enqueue' ] );

		//Enqueue admin end JS and CSS
		add_action( 'wp_ajax_llmss_ajax',	[ $this->admin, 'ajax_handler' ] );

		// Register widgets
		add_action( 'wp_dashboard_setup', [ $this->admin, 'wp_dashboard_setup' ] );

		// Add custom user fields for paypal etc.
		add_action( 'show_user_profile', [ $this->admin, 'user_fields' ] );
		add_action( 'edit_user_profile', [ $this->admin, 'user_fields' ] );

		// Save extra fields
		add_action( 'personal_options_update', [ $this->admin, 'save_user_fields' ] );
		add_action( 'edit_user_profile_update', [ $this->admin, 'save_user_fields' ] );

	}

	public function increment_option( $id ) {
		$val = get_option( $id, 0 );
		if ( ! $val ) {
			$val = 0;
		}
		$val ++;
		update_option( $id, $val, 'no' );
	}

	public function log_page_views() {
		global $post;
		/** @var WP_Post $p */
		$p = $post;

		if ( is_singular( 'course' ) && $p->post_author != get_current_user_id() ) {
			// Daily course visit counter
			$this->increment_option( 'llmss-' . date( 'Ymd' ) );
			// Daily counter by author
			$this->increment_option( 'llmss-' . date( 'Ymd' ) . '-' . $p->post_author );
			// Daily counter by course
			$this->increment_option( 'llmss-' . date( 'Ymd' ) . '-' . $p->ID );
			// Monthly course visit counter
			$this->increment_option( 'llmss-' . date( 'Ym' ) );
			// Monthly counter by author
			$this->increment_option( 'llmss-' . date( 'Ym' ) . '-' . $p->post_author );
			// Monthly counter by course
			$this->increment_option( 'llmss-' . date( 'Ym' ) . '-' . $p->ID );
		}
	}
}

/** Intantiating main plugin class */
Lifter_LMS_Stats::instance( __FILE__ );
