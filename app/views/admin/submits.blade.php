@section('content')
<?php auth_check_mentainer(); ?>

<?php

	$dbh = dbconnect();

	$SQL="SELECT id, title, item_id, create_dt, user_name, edoc from dsd.submits WHERE status = 1 ORDER BY create_dt desc ";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$submits = $stmt->fetchAll();






#<a href="/prepo/edit_step1">[NEW]</a>

?>



<table class="table table-striped table-bordered table-hover">

<?php
foreach($submits as $k => $r){
	$id = $r[0];
	$title = $r[1];
	$item_id = $r[2];
	$create_dt = $r[3];
	$user_name = $r[4];
	$edoc = $r[5];

?>

	<tr>
	<td><?php echo($id); ?></td>
	<td><?php echo($user_name); ?></td>
	<td><?php echo($item_id); ?></td>
	<td><?php echo($title); ?></td>
	<td><?php echo($edoc); ?></td>
	<td><?php echo($create_dt); ?></td>
	<td><a href="/prepo/edit_step1?s=<?php echo($id); ?>"><span class="glyphicon glyphicon-edit"></span></a>
	<td><a href="/prepo/delete_submit?s=<?php echo($id); ?>"  onclick="return confirm('Are you sure you want to delete?')"><span class="glyphicon glyphicon-remove"></span></a>

	</tr>


<?php
}
?>
</table>
@stop
