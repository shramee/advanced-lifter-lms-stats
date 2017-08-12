<?php
/** @var Lifter_LMS_Stats_Admin_Widget $th */
$th = $this;
?>
<table>
	<tr>
		<th>Author ID</th>
		<th>Author Name</th>
		<th>Total Video Tutorials</th>
		<th>Total Views</th>
		<th>Royalties</th>
		<th>Total Sells</th>
		<th>Sale Income</th>
		<th>Total Payment</th>
		<th>Due Payment</th>
		<th>Pay</th>
	</tr>
	<?php

	?>
</table>
<?php
?>
<table>
	<tr>
		<th>Admin ID</th>
		<th>Admin Name</th>
		<th colspan="2">Account Information</th>
		<th colspan="2">Site Income</th>
		<th rowspan="3"><button>Pay selected with paypal</button></th>
	</tr>
	<tr>
		<td rowspan="2"><?php echo $th->user->ID ?></td>
		<td rowspan="2"><?php echo $th->user->display_name ?></td>
		<th>Paypal Account</th>
		<td><?php $th->user_paypal() ?></td>
		<td><?php echo $th->user->ID ?></td>
		<td><?php echo $th->user->ID ?></td>
	</tr>
</table>