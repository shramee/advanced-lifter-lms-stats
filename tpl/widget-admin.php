<?php
/** @var Lifter_LMS_Stats_Admin_Widget $th */
$th = $this;

$site_title = get_bloginfo( 'name' );

$authors = $th->authors( $this->sale_by_product );
$start_date = $th->start_date();
$end_date = $th->end_date();
?>
<form>
	<label>
		Start
		<input type="date" name="start" value="<?php echo $start_date ?>">
	</label>
	<label>
		End
		<input type="date" name="end" value="<?php echo $end_date ?>">
	</label>

	<input class="button button-primary" type="submit" value="Go">
</form>

<table class="stats">
	<tr>
		<th>Author ID</th>
		<th>Author Name</th>
		<th>Total Courses</th>
		<th>Total Views</th>
		<th>Royalties</th>
		<th>Total Sells</th>
		<th>Sale Income</th>
		<th>Total Payment</th>
		<th>Due Payment</th>
		<th>Pay</th>
	</tr>
	<?php
	foreach ( $authors as $a_id => $a ) {
		$paypal_acc = get_user_meta( $a_id, 'paypal_acc', true );

		$payment_form = '';
		if ( $paypal_acc ) {
			$payment_form = "<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
					<input type='hidden' name='cmd' value='_xclick'>
					<input type='hidden' name='business' value='$paypal_acc'>
					<input type='hidden' name='item_name' value='Payment from $site_title'>
					<input type='hidden' name='item_number' value='1'>
					<input type='hidden' name='amount' value='$a[due_pay]'>
					<input type='hidden' name='no_shipping' value='0'>
					<input type='hidden' name='no_note' value='1'>
					<input type='hidden' name='currency_code' value='USD'>
					<input type='hidden' name='lc' value='US'>
					<input type='hidden' name='bn' value='PP-BuyNowBF'>
					<input type='image' src='https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_150x38.png' border='0' name='submit' alt='PayPal - The safer, easier way to pay online.'>
					<img style='position: absolute;' border='0' src='https://www.paypal.com/en_US/i/scr/pixel.gif' width='1' height='1'>
				</form>";
		}

		$label = $a['name'];
		if ( $a_id ) {
			$label = "<a href='?start=$start_date&end=$end_date&user=$a_id'>$a[name]</a>";
		}

		echo <<<HTML
			<tr>
			<td>$a_id</td>
			<td>$label</td>
			<td>$a[courses]</td>
			<td>$a[views]</td>
			<td>$a[royalties] &#8364;</td>
			<td>$a[sells]</td>
			<td>$a[sale_income] &#8364;</td>
			<td>$a[total_pay] &#8364;</td>
			<td>$a[due_pay] &#8364;</td>
			<td>$payment_form</td>
			</tr>
HTML;
	}

	?>
</table>
<?php
?>
<table>
	<tr>
		<th>Admin ID</th>
		<th>Admin Name</th>
		<th colspan="2">Account Information</th>
		<th colspan="3">Site Income</th>
	</tr>
	<tr>
		<td rowspan="2"><?php echo $th->user->ID ?></td>
		<td rowspan="2"><?php echo $th->user->display_name ?></td>
		<th>Paypal Account</th>
		<td><?php $th->user_paypal() ?></td>
		<td><?php echo $authors['']['royalties'] * ( 1 - LLMSS_Share ) / LLMSS_Share ?></td>
		<td><?php echo $authors['']['sale_income'] * ( 1 - LLMSS_Share ) / LLMSS_Share ?></td>
		<td><?php echo $authors['']['total_pay'] * ( 1 - LLMSS_Share ) / LLMSS_Share ?></td>
	</tr>
</table>