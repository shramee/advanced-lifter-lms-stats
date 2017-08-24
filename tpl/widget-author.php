<?php
/** @var Lifter_LMS_Stats_Author_Widget $th */
$th = $this;

$site_title = get_bloginfo( 'name' );

$courses = $th->courses_by_author( $th->user->ID, $this->sale_by_product );
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

	<input class="button button-primary" type="submit" value="Go">
</form>

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
			<td>$c[views]</td>
			<td>$c[royalty] &#8364;</td>
			<td>$c[sells]</td>
			<td>$c[price] &#8364;</td>
			<td>$c[sale] &#8364;</td>
			<td>$c[total_pay] &#8364;</td>
			</tr>
			<tr>
HTML;
		}
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
	<th>Request Payment</th>
	</tr>
	<tr>
		<td><?php echo $th->user->ID ?></td>
		<td><?php echo $th->user->display_name ?></td>
		<td><?php $paypal_acc = $th->user_paypal() ?></td>
		<td><?php echo $courses['']['total_pay'] - $th->author_paid( $th->user->ID ) ?> &#8364;</td>
		<td>
			<?php
			if ( isset( $_GET['payment_requested'] ) ) {
				if ( $_GET['payment_requested'] ) {?>
					<span style="color:#0a0">Payment request received</span>
					<?php
				} else {?>
					<span style="color:#c20">Payment request failed</span>
					<a href='<?php echo admin_url( 'admin-ajax.php?action=llmss_ajax&request=request_payment' ) ?>'>
						Try again
					</a>
					<?php
				}
			} else { ?>
				<a class="button" href='<?php echo admin_url( 'admin-ajax.php?action=llmss_ajax&request=request_payment' ) ?>'>
					Request payment
				</a>
				<?php
			}
			?>
		</td>
	</tr>
</table>