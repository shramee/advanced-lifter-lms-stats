<?php

/**
 * Lifter LMS Stats public class
 */
class Lifter_LMS_Stats_Admin_Widget {

	public $user;

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 * @param WP_User $user
	 */
	public function __construct( $user ) {
		$this->user = $user;
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function render() {
		require dirname( __FILE__ ) . '/../tpl/widget-admin.php';
	}
}