<?php

/**
 * Lifter LMS Stats public class
 */
class Lifter_LMS_Stats_Author_Widget extends Lifter_LMS_Stats_Admin_Widget {

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function render() {
		require dirname( __FILE__ ) . '/../tpl/widget-author.php';
	}
}