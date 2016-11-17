@section('content')

<?php auth_check_mentainer(); ?>
<?php
	//drupal_set_title("edit bitstream symlink");

	$id = get_post_get('sid');
	if (empty($id)){
		return null;
	}

	$dbh = dbconnect();

	if (!empty($_POST)){

	//	echo("<pre>");
//	print_r($_POST);
	//	echo("</pre>");

		$save = get_post('SAVE',null);
		$delete = get_post('DELETE',null);



		if ($save == 'SAVE'){

			$item_id = get_post('item_id');
			$bitstream_id = get_post('bitstream_id');
			$bundle = get_post('bundle');
			$weight = get_post('weight');
			if (empty($bitstream_id) || empty($bundle) || (empty($weight) && $weight != 0) || empty($item_id)){
				echo ("ERROR#1");
				return;
			}
			echo "<p>SAVE</p>";

			//$SQL="SELECT * FROM dsd.ln_bitstream(?, ?, ?)";
		//	$stmt = $dbh->prepare($SQL);
		//	$stmt->bindParam(1, $bitstream_id);
		//	$stmt->bindParam(2, $item_id);
		//	$stmt->bindParam(3, $bundle);
		//	$stmt->execute();
		///	$rep = $stmt->fetchAll();
		//	echo ("<pre>");
		//	print_r($rep);
		//	echo ("</pre>");

		$SQL="SELECT * FROM dsd.update_bitstream_symlink( ?,  ?,  ?,  ?,  ?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $id);
		$stmt->bindParam(2, $bitstream_id);
		$stmt->bindParam(3, $item_id);
		$stmt->bindParam(4, $bundle);
		$stmt->bindParam(5, $weight);
		$stmt->execute();
		$rep = $stmt->fetchAll();

		//echo ("<pre>");
		//print_r($rep);
		//echo ("</pre>");


	// id  | bundle_id | bitstream_id |         create_dt          | symlink | weight
	// ------+-----------+--------------+----------------------------+---------+--------
	// 2663 |      2500 |         2687 | 2012-11-28 14:26:20.458891 | t       |      0


		} elseif ($delete == 'DELETE') {
			$item_id = get_post('item_id');
			echo "<p>DELETE</p>";
			$SQL=" DELETE FROM public.bundle2bitstream where id = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $id);
			$stmt->execute();
			}
			printf('<a href="/archive/item/%s">goto item %s</a>',$item_id,$item_id);

	}



	$SQL = "SELECT symlink_id, bitstream_id, bb_create_dt, symlink, bb_weight, bundle_id, bundle, item_id, item_title
	FROM  dsd.bitstream_symlinks WHERE symlink_id = ?  ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $id);
	$stmt->execute();
	if (! ($data = $stmt->fetch())){
		return;
	}

	$weight=$data['bb_weight'];
	$bundle=$data['bundle'];
	
	
	
	$item_id=$data['item_id'];
	$bitstream_id=$data['bitstream_id'];



?>

<table>
<tr>
	<td>id:</td><td><?php echo $id?></td>
</tr>
<tr>
	<td>item_id:</td><td><?php echo $item_id?></td>
</tr>
<tr>
	<td>item_title:</td><td><?php echo $data['item_title']?></td>
</tr>
<?php if (!empty($bitstream_id)): ?>
<tr>
	<td>bitstream:</td><td><a href="/prepo/edit_bitstream?bid=<?php echo $bitstream_id?>"><?php echo $bitstream_id?></a></td>
</tr>
<?php endif; ?>
<tr>
	<td>create_dt:</td><td><?php echo $data['bb_create_dt']?></td>
</tr>

</table>
<table>
<form method="POST" action="/prepo/edit_bitstream_symlink?sid=<?php echo $id?>">
	<input type="hidden" name="item_id" value="<?php echo $item_id?>"/>
	<tr><td>weight:  </td><td> <input type="text" name="weight" value="<?php echo $weight?>"/></td></tr>
	<tr><td>bundle:  </td><td>
		<?php
				$BUNDLE_MAP = Lookup::get_bitstream_bundles();
				PUtil::print_select("bundle","select_bundle_name",$BUNDLE_MAP, $bundle ,false);
?>
	</td></tr>
	<tr><td>bitstream:  </td><td> <input type="text" name="bitstream_id" value="<?php echo $bitstream_id?>"/> </td></tr>
</table>


<table>
	<tr><td>
	<input type="submit" name="SAVE" value="SAVE"/>
	</form>
</td>
<td>
	<form method="POST" action="/prepo/edit_bitstream_symlink?sid=<?php echo $id?>">
	<input type="hidden" name="item_id" value="<?php echo $item_id?>"/>
	<input type="hidden" name="did" value="<?php echo $id?>"/>
	<input type="submit" name="DELETE" value="DELETE"/>
	</form>
</td>
</tr>
</table>

@stop