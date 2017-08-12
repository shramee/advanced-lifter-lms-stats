<?php

/**
 * Lifter LMS Stats public class
 */
class Lifter_LMS_Stats_Admin_Widget {

	public $user;

	function user_paypal() {
		$paypal_acc = get_user_meta( $this->user->ID, 'paypal_acc', true );
		if ( $paypal_acc ) {
			echo $paypal_acc;
		} else {
			echo 'Please <a href="' . admin_url( 'profile.php#paypal-info' ) . '">provide your paypal ID</a>.';
		}
	}

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