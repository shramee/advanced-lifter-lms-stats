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
		$url   = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/admin.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/admin.js', array( 'jquery' ) );

		wp_localize_script( $token . '-js', 'llmss_data', [
			'user'    => wp_get_current_user()->ID,
			'start'   => ! empty( $_GET['start'] ) ? $_GET['start'] : date( 'Y-m' ) . '-01',
			'end'     => ! empty( $_GET['end'] ) ? $_GET['end'] : date( 'Y-m-d' ),
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		] );

	}

	function user_fields( $user ) {
		?>
		<h2 id="paypal-info"><?php _e("Paypal information", "blank"); ?></h2>

		<table class="form-table">
		<tr>
			<th><label for="paypal_acc"><?php _e("Paypal Account"); ?></label></th>
			<td>
				<input type="text" name="paypal_acc" id="paypal_acc" value="<?php echo esc_attr( get_the_author_meta( 'paypal_acc', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e("Please enter your paypal account email."); ?></span>
			</td>
		</tr>
		</table>
		<?php
	}

	public function ajax_handler() {
		if ( isset( $_GET['request'] ) ) {
			$method = "ajax_handler_$_GET[request]";
			if ( method_exists( $this, $method ) ) {
				$this->$method();
			}
		}
	}

	private function ajax_handler_request_payment() {
		$user = wp_get_current_user();

		$site = get_bloginfo( 'name' );
		$site_url = site_url();

		$pay_url = admin_url( "?user=$user->ID" );

		$success = wp_mail( LLMSS_ADMIN_EMAIL, $user->display_name . ' requested payment', "
		Hi from $site($site_url), <br> <br>

		User <b>$user->display_name</b> requested payment. <br> <br>
		
		<a href='$pay_url'>Click here to pay now</a>.
		" );

		header( 'Location: ' . admin_url( '?payment_requested=' . $success ) );
		die();
	}

	private function ajax_handler_paid() {
		if ( ! empty( $_POST['payee'] ) && ! empty( $_POST['amount'] ) ) {
			$id = "llmss_paid_$_POST[payee]_" . date( 'Ym' );

			$val = get_option( $id, 0 );
			if ( ! $val ) {
				$val = 0;
			}
			$val += $_POST['amount'];

			update_option( $id, $val, 'no' );
		}
	}

	function save_user_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, 'paypal_acc', $_POST['paypal_acc'] );
	}
}