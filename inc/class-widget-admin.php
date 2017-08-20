<?php

/**
 * Lifter LMS Stats public class
 */
class Lifter_LMS_Stats_Admin_Widget {

	/** @var WP_User Current user */
	public $user;

	/** @var int Total view on site */
	public $total_views;

	/** @var float revenue from membership */
	public $subscriptions_revenue;

	/** @var array Array of product IDs */
	public $products = [];

	/** @var array|mixed Product sales object {amt:x,qty:x}[] */
	public $sale_by_product;

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 *
	 * @param WP_User $user
	 */
	public function __construct( $user ) {
		$this->user                  = $user;
		$this->total_views           = get_option( 'llmss-' . date( 'Ym' ) );
		$this->sale_by_product = $this->sale_by_product();

		$this->subscriptions_revenue = $this->sale_by_product[1723]->amt;
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function render() {
		if ( empty( $_GET['user'] ) ) {
			require dirname( __FILE__ ) . '/../tpl/widget-admin.php';
		} else {
			require dirname( __FILE__ ) . '/../tpl/widget-author-for-admin.php';
		}
	}

	function user_paypal( $id = 0 ) {
		$id         = $id ? $id : $this->user->ID;
		$paypal_acc = get_user_meta( $id, 'paypal_acc', true );
		if ( $paypal_acc ) {
			echo $paypal_acc;
		} else {
			echo 'Please <a href="' . admin_url( 'profile.php#paypal-info' ) . '">provide your paypal ID</a>.';
		}
	}

	function sale_by_product() {

		// Try grabbing cached copy
		$result = get_transient( 'llmss_orders' );

		if ( ! $result ) {
			// If no cache
			$orders          = $this->sale_orders();
			$result          = [];
			$total_sale      = new stdClass();
			$total_sale->amt = 0;
			$total_sale->qty = 0;
			foreach ( $orders as $order ) {
				if ( empty( $result[ $order->product_id ] ) ) {
					$result[ $order->product_id ]      = new stdClass();
					$result[ $order->product_id ]->amt = 0;
					$result[ $order->product_id ]->qty = 0;
				}
				$result[ $order->product_id ]->amt += $order->amount;
				$result[ $order->product_id ]->qty ++;
				$total_sale->amt += $order->amount;
				$total_sale->qty ++;
			}

			$result['total'] = $total_sale;

			// Set cache for a day
			set_transient( 'llmss_orders', $result, DAY_IN_SECONDS );
		}

		return $result;
	}

	function sale_orders( $args = array() ) {
		global $wpdb;

		return $wpdb->get_results(
			sprintf(
				$this->sale_orders_query(),
				$this->start_date(),
				$this->end_date()
			)
		);
	}

	public function sale_orders_query() {
		global $wpdb;

		return
			"SELECT ID, post_date, prodID.meta_value as product_id, amnt.meta_value as amount" .
			"	FROM {$wpdb->posts} AS orders" .
			"	JOIN {$wpdb->postmeta} AS prodID ON orders.ID = prodID.post_id" .
			"	JOIN {$wpdb->postmeta} AS amnt ON orders.ID = amnt.post_id" .
			"	WHERE" .
			"		orders.post_type = 'llms_order' AND" .
			"		orders.post_date BETWEEN CAST( '%s' AS DATETIME ) and CAST( '%s' AS DATETIME ) AND" .
			"		prodID.meta_key = '_llms_product_id' AND" .
			"		amnt.meta_key = '_llms_total';";
	}

	/**
	 * Period start date
	 * @return string start date
	 */
	public function start_date() {
		return ! empty( $_GET['start'] ) ? $_GET['start'] : date( 'Y-m' ) . '-01';
	}

	/**
	 * Period end date
	 * @return string end date
	 */
	public function end_date() {
		return ! empty( $_GET['end'] ) ? $_GET['end'] : date( 'Y-m-d' );
	}

	public function authors( $sales = [] ) {
		$courses = new WP_Query( [ 'post_type' => 'course', 'post_status' => 'publish' ] );

		$authors = [];

		global $post;

		$totals = [
			'name'        => 'Totals',
			'courses'     => 0,
			'views'       => 0,
			'royalties'   => 0,
			'sells'       => 0,
			'sale_income' => 0,
			'total_pay'   => 0,
			'due_pay'     => 0,
		];

		while ( $courses->have_posts() ) {
			$courses->the_post();
			/** @var WP_Post $course */
			$course = $post;
			if ( empty( $authors[ $course->post_author ] ) ) {

				$views     = $this->author_views( $course->post_author );
				$royalties = $this->royalty_by_views( $views );

				$authors[ $course->post_author ] = [
					'name'        => get_the_author(),
					'courses'     => 0,
					'views'       => $views,
					'royalties'   => $royalties,
					'sells'       => 0,
					'sale_income' => 0,
					'total_pay'   => $royalties, // Sale income will be added later
					'due_pay'     => $royalties - $this->author_paid( $course->post_author ),
				];

				$totals['royalties'] += $royalties;
				$totals['views']     += $views;
				$totals['total_pay'] += $royalties;
				$totals['due_pay'] += $authors[ $course->post_author ]['due_pay'];
			}

			if ( ! empty( $sales[ $course->ID ] ) ) {
				$authors[ $course->post_author ]['sells'] += $sales[ $course->ID ]->qty;
				$authors[ $course->post_author ]['sale_income']  += $sales[ $course->ID ]->amt * LLMSS_Share;
				$authors[ $course->post_author ]['total_pay']  += $sales[ $course->ID ]->amt * LLMSS_Share;
				$authors[ $course->post_author ]['due_pay']  += $sales[ $course->ID ]->amt * LLMSS_Share;

				$totals['sells'] += $sales[ $course->ID ]->qty;
				$totals['sale_income']  += $sales[ $course->ID ]->amt * LLMSS_Share;
				$totals['total_pay']  += $sales[ $course->ID ]->amt * LLMSS_Share;
				$totals['due_pay']  += $sales[ $course->ID ]->amt * LLMSS_Share;
			}

			$authors[ $course->post_author ]['courses'] ++;
			$this->products[] = $course->ID;
			$totals['courses'] ++;
		}

		$authors[''] = $totals;

		return $authors;
	}

	public function courses_by_author( $author, $sales = [] ) {
		$courses_query = new WP_Query( [
			'post_type' => 'course',
			'post_status' => 'publish',
			'author' => $author,
		] );

		$courses = [];

		global $post;

		$totals = [
			'course'      => 0,
			'views'       => 0,
			'royalties'   => 0,
			'sells'       => 0,
			'sale_income' => 0,
			'total_pay'   => 0,
			'due_pay'     => 0,
		];

		while ( $courses_query->have_posts() ) {
			$courses_query->the_post();
			/** @var WP_Post $course */
			$course = $post;
			$views = $this->course_views( $course->ID );
			$royalty = $this->royalty_by_views( $views );

			$qty = 0;
			$sale = 0;
			if ( isset( $sales[ $course->ID ] ) ) {
				$qty = $sales[ $course->ID ]->qty;
				$sale = $sales[ $course->ID ]->amt;
			}

			$courses[] = [
				'title' => $course->post_title,
				'views' => $views,
				'royalty' => $royalty,
				'sells' => $qty,
				'price' => '',
				'sale' => $sale * LLMSS_Share,
				'total_pay' => '',
				'due_pay' => '',
			];
		}

		return $courses;
	}

	public function author_views( $id = 0 ) {
		$id = $id ? $id : $this->user->ID;

		return get_option( 'llmss-' . date( 'Ym' ) . '-' . $id );
	}

	public function course_views( $id ) {
		return get_option( 'llmss-' . date( 'Ym' ) . '-' . $id );
	}

	public function author_paid( $id = 0 ) {
		$id = $id ? $id : $this->user->ID;

		// @TODO Get paid amount
		return 0;
	}

	public function royalty_by_views( $views ) {
		return round( $this->subscriptions_revenue * LLMSS_Share * $views / $this->total_views - 0.001, 2 );
	}
}