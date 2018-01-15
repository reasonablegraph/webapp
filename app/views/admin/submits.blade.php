@section('content')
<?php auth_check_mentainer(); ?>

<?php

	$dbh = dbconnect();

	$SQL="SELECT id, title, item_id, create_dt, user_name, edoc, status, final_item_id, update_dt
			FROM dsd.submits
			WHERE status IN (?, ?)
			AND type = 2
			ORDER BY create_dt desc ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, SubmitsStatus::$active);
	$stmt->bindParam(2, SubmitsStatus::$active_edit);
	$stmt->execute();
	$submits = $stmt->fetchAll();






#<a href="/prepo/edit_step1">[NEW]</a>

?>



<table class="table table-striped table-bordered table-hover">
	<tr>
		<th>id</th>
		<th>user name</th>
		<th>item id</th>
		<th>final item id</th>
		<th>title</th>
		<th>edoc</th>
		<th>status</th>
		<th>create datetime</th>
		<th>update datetime</th>
		<th>edit</th>
		<th>delete</th>
	</tr>

<?php
foreach($submits as $k => $r){
	$id = $r[0];
	$title = $r[1];
	$item_id = $r[2];
	$create_dt = $r[3];
	$user_name = $r[4];
	$edoc = $r[5];
	$status = $r[6];
	$final_item_id = $r[7];
	$update_dt = $r[8];

?>

	<tr>
	<td><?php echo($id); ?></td>
	<td><?php echo($user_name); ?></td>
	<td><?php echo($item_id); ?></td>
	<td><?php echo($final_item_id); ?></td>
	<td><?php echo($title); ?></td>
	<td><?php echo($edoc); ?></td>
	<td><?php echo($status); ?></td>
	<td><?php echo($create_dt); ?></td>
	<td><?php echo($update_dt); ?></td>
	<td><a href="/prepo/edit_step1?s=<?php echo($id); ?>"><span class="glyphicon glyphicon-edit"></span></a>
	<td><a href="/prepo/delete_submit?s=<?php echo($id); ?>"  onclick="return confirm('Are you sure you want to delete?')"><span class="glyphicon glyphicon-remove"></span></a>

	</tr>


<?php
}
?>
</table>
@stop
