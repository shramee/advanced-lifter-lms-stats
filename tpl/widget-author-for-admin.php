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
		<th>Course</th>
		<th>Total Views</th>
		<th>Royalty</th>
		<th>Total Sells</th>
		<th>Price</th>
		<th>Sell income</th>
		<th>Total Payment</th>
		<th>Due Payment</th>
	</tr>
	<tr>
		<td rowspan="<?php  ?>">$c[title]</td>
		<td>$c[views]</td>
	<?php
	foreach ( $courses as $c ) {
		echo <<<HTML
			<td>$c[title]</td>
			<td>$c[views]</td>
			<td>$c[royalty]</td>
			<td>$c[sells]</td>
			<td>$c[price]</td>
			<td>$c[sale]</td>
			<td>$c[total_pay]</td>
			<td>$c[due_pay]</td>
			</tr>
			<tr>
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
		<td><?php echo $courses['']['royalties'] * ( 1 - LLMSS_Share ) / LLMSS_Share ?></td>
		<td><?php echo $courses['']['sale_income'] * ( 1 - LLMSS_Share ) / LLMSS_Share ?></td>
		<td><?php echo $courses['']['total_pay'] * ( 1 - LLMSS_Share ) / LLMSS_Share ?></td>
	</tr>
</table>