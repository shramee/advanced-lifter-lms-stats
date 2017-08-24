<?php
/** @var Lifter_LMS_Stats_Admin_Widget $th */
$th = $this;

$site_title = get_bloginfo( 'name' );

$courses = $th->courses_by_author( $_GET['user'], $this->sale_by_product );
$start_date = $th->start_date();
$end_date = $th->end_date();

?>
<form>
	<label>
		From:
		<input type="date" name="start" value="<?php echo $start_date ?>">
	</label>
	<label>
		To:
		<input type="date" name="end" value="<?php echo $end_date ?>">
	</label>

	<input type="hidden" name="user" value="<?php echo $th->user->ID ?>">

	<input class="button button-primary" type="submit" value="Go">
</form>

<h3><a href='<?php echo "?start=$start_date&end=$end_date" ?>'>Admin Stats</a> <span class="dashicons dashicons-arrow-right-alt2"></span> Author #<?php echo "{$th->user->ID}: {$th->user->display_name}" ?></h3>

<table class="stats second-last-row">
	<tr>
		<th>Author ID</th>
		<th>Author Name</th>
		<th>Course ID</th>
		<th>Course</th>
		<th>Total Views</th>
		<th>Royalty</th>
		<th>Total Sells</th>
		<th>Price</th>
		<th>Sell income</th>
		<th>Total Payment</th>
	</tr>
	<tr>
		<td rowspan="<?php echo count( $courses ) ?>"><?php echo $th->user->ID ?></td>
		<td rowspan="<?php echo count( $courses ) ?>"><?php echo $th->user->display_name ?></td>
	<?php
	foreach ( $courses as $id => $c ) {
		echo <<<HTML
			<td>$id</td>
			<td>$c[title]</td>
			<td class='textright'>$c[views]</td>
			<td class='textright'>$c[royalty] &#8364;</td>
			<td class='textright'>$c[sells]</td>
			<td class='textright'>$c[price] &#8364;</td>
			<td class='textright'>$c[sale] &#8364;</td>
			<td class='textright'>$c[total_pay] &#8364;</td>
			</tr>
			<tr>
HTML;
	}
	$due_pay = $courses['']['total_pay'] - $th->author_paid( $th->user->ID );
	?>
	</tr>
</table>
<?php
?>
<table>
		<th>Author ID</th>
		<th>Author Name</th>
		<th>Account Information</th>
		<th>Due Pay</th>
		<th>Pay with Paypal</th>
	</tr>
	<tr>
		<td><?php echo $th->user->ID ?></td>
		<td><?php echo $th->user->display_name ?></td>
		<td><?php $paypal_acc = $th->user_paypal( 0, 'Author has not provided paypal information.') ?></td>
		<td class="'textright'"><?php echo $due_pay ?> &#8364;</td>
		<td>
			<?php
			if ( $paypal_acc ) { ?>
				<form class="llmss-paypal" action='https://www.paypal.com/cgi-bin/webscr' target="_blank" data-payee="<?php echo $th->user->ID ?>" method='post'>
					<input type='hidden' name='cmd' value='_xclick'>
					<input type='hidden' name='business' value='<?php echo $paypal_acc ?>'>
					<input type='hidden' name='item_name' value='Payment from <?php echo $site_title ?>'>
					<input type='hidden' name='item_number' value='1'>
					<input type='text' name='amount' value='<?php echo $due_pay ?>'>
					<input type='hidden' name='no_shipping' value='0'>
					<input type='hidden' name='no_note' value='1'>
					<input type='hidden' name='currency_code' value='EUR'>
					<input type='hidden' name='lc' value='US'>
					<input type='hidden' name='bn' value='PP-BuyNowBF'>
					<input type='image' src='https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_150x38.png'
								 border='0' name='submit' alt='PayPal - The safer, easier way to pay online.'>
					<img style='position: absolute;' border='0' src='https://www.paypal.com/en_US/i/scr/pixel.gif' width='1'
							 height='1'>
				</form>
				<?php
			} else {
				echo 'Paypal account not specified';
			}
			?>
		</td>
	</tr>
</table>