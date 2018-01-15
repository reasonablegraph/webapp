@section('content')
<?php auth_check_mentainer(); ?>

<?php

	$dbh = dbconnect();

	$SQL="SELECT id, title, item_id, final_item_id, create_dt, user_name, edoc, update_dt
			FROM dsd.submits
			WHERE status NOT IN (?, ?)
			AND type = 1
			ORDER BY create_dt DESC";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, SubmitsStatus::$finished);
	$stmt->bindParam(2, SubmitsStatus::$error);
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
		<th>create datetime</th>
		<th>update datetime</th>
		<th>finish</th>
		<th>delete</th>
	</tr>

<?php
foreach($submits as $k => $r){
	$id = $r[0];
	$title = $r[1];
	$item_id = $r[2];
	$final_item_id = $r[3];
	$create_dt = $r[4];
	$user_name = $r[5];
	$edoc = $r[6];
	$update_dt = $r[7];

?>

	<tr>
	<td><?php echo($id); ?></td>
	<td><?php echo($user_name); ?></td>
	<td><?php echo($item_id); ?></td>
	<td><?php echo($final_item_id); ?></td>
	<td><?php echo($title); ?></td>
	<td><?php echo($edoc); ?></td>
	<td><?php echo($create_dt); ?></td>
	<td><?php echo($update_dt); ?></td>
	<td><a href="/prepo/finish_submit?s=<?php echo($id); ?>&f=a" onclick="return confirm('Are you sure you want to finish?')"><span class="glyphicon glyphicon-edit"></span></a>
	<td><a href="/prepo/delete_submit?s=<?php echo($id); ?>&f=a" onclick="return confirm('Are you sure you want to delete?')"><span class="glyphicon glyphicon-remove"></span></a>

	</tr>


<?php
}
?>
</table>
@stop
