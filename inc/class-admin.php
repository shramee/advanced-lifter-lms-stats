<?php
/**
 * Lifter LMS Stats Admin class
 */
class Lifter_LMS_Stats_Admin {

	/** @var Lifter_LMS_Stats_Admin Instance */
	private static $_instance = null;

	/* @var string $token Plugin token */
	public $token;

	/* @var string $url Plugin root dir url */
	public $url;

	/* @var string $path Plugin root dir path */
	public $path;

	/* @var string $version Plugin version */
	public $version;

	/** @var Lifter_LMS_Stats_Admin_Widget */
	public $widget;


	/**
	 * Main Lifter LMS Stats Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return Lifter_LMS_Stats_Admin instance
	 * @since 	1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since 	1.0.0
	 */
	private function __construct() {
		$this->token   =   Lifter_LMS_Stats::$token;
		$this->url     =   Lifter_LMS_Stats::$url;
		$this->path    =   Lifter_LMS_Stats::$path;
		$this->version =   Lifter_LMS_Stats::$version;
	} // End __construct()

	/**
	 * Register admin widget
	 * @action wp_dashboard_setup
	 */
	public function wp_dashboard_setup() {

		$user = wp_get_current_user();

		if ( in_array( LLMSS_Admin, $user->roles ) ) {
			$this->widget = new Lifter_LMS_Stats_Admin_Widget( $user );
		} else if ( in_array( LLMSS_Author, $user->roles ) ) {
			$this->widget = new Lifter_LMS_Stats_Author_Widget( $user );
		}

		wp_add_dashboard_widget(
			'lifter-lms-stats',         // Widget slug.
			'Lifter LMS Statistics',         // Title.
			[ $this->widget, 'render' ] // Display function.
		);
	} // End wp_dashboard_setup()

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/admin.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/admin.js', array( 'jquery' ) );
	}
}