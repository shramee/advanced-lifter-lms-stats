/**
 * Plugin front end scripts
 *
 * @package Lifter_LMS_Stats
 * @version 1.0.0
 */
jQuery(function ($) {

	var
		$widget = $( '#lifter-lms-stats' );

	$('#dashboard-widgets').prepend( $widget )

	$('body').on( 'submit', 'form.llmss-paypal', function() {
		var $t = $( this );
		$.post( llmss_data.ajaxUrl + '?action=llmss_ajax&request=paid', {
			amount: $t.find( '[name="amount"]' ).val(),
			payee: $t.data( 'payee' ),
		} );
	} );

});