@section('content')
<?php auth_check_mentainer(); ?>
<?php

drupal_set_title('');
drupal_add_js("/_assets/js/putil.js",'external');

?>

<?php
	$tmp = get_get('view',1);
	if ($tmp == 1){ ?>
	
	<script>

	var item_ref = function( src_item_id,  trg_item_id, element,value){


		var response=confirm("do you want connect?");
		if (response){
			var e =  decodeURIComponent(element);
			var v =  decodeURIComponent(value);
			var i1 = decodeURIComponent(src_item_id);
			var i2 = decodeURIComponent(trg_item_id);
			pl.post_to_url("/prepo/elements_item_ref", {  i1:i1,i2:i2, e:e, v:v, });
		}

	};
</script>

<?php

	$dbh = dbconnect();

	$i1 = get_post('i1');
	$i2 = get_post('i2');
	$e = get_post('e');
	$v = get_post('v');

	if (!empty($e) && !empty($v)  && !empty($i1)  && !empty($i2)) {
		$SQL="UPDATE dsd.metadatavalue2 SET ref_item=?  WHERE text_value=?  AND element=? AND item_id = ? AND ref_item is null";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $i2);
		$stmt->bindParam(2, $v);
		$stmt->bindParam(3, $e);
		$stmt->bindParam(4, $i1);
		$stmt->execute();
		$count = $stmt->rowCount();
		echo("<p>$count record updated</p>");


		$SQL="SELECT distinct dsd.touch_item(item_id) FROM dsd.metadatavalue2 WHERE ref_item = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $i2);
		$stmt->execute();
		$count = $stmt->rowCount();
		echo("<p>$count record updated</p>");


	}


	$SQL="select item_id, label, element, text_value, obj_type, target_i FROM dsd.elements_without_item_ref_det";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();


	printf('<a href="/prepo/elements_item_ref?view=2">[QUICK VIEW]</a><br/>');
	echo('<table class="table table-striped table-bordered table-hover">');
	echo('<tr> <th> item</th> <th>element</th>  <th>obj_type</th> <th>text value</th>  <th>command</th> </tr>');
	while($r = $stmt->fetch()){
		$src_item_id = $r[0];
		$label = $r[1];
		$elem = $r[2];
		$value = $r[3];
		$obj_type = $r[4];
		$trg_item_id = $r[5];
		$fn = sprintf("javascript:item_ref('%s','%s','%s','%s')",rawurlencode($src_item_id),rawurlencode($trg_item_id), rawurlencode($elem),rawurlencode($value));
		printf('<tr> <td><a href="/archive/item/%s"  target="_blank" >%s</a> </td>  <td>%s </td> <td>%s </td>   <td> <a href="/archive/item/%s"  target="_blank" >%s</a> </td><td> <a href="%s">[fix]</a> </td> </tr>',
		$src_item_id, $label, $elem, $obj_type, $trg_item_id, htmlspecialchars($value),$fn);
	}
	echo("</table>");

?>
		
<?php } else if ($tmp == 2) { ?>
		
		
		<script>
		
		var item_ref = function(  trg_item_id, element,value){
		
		
			var response=confirm("do you want connect?");
			if (response){
				var e =  decodeURIComponent(element);
				var v =  decodeURIComponent(value);
				//var i1 = decodeURIComponent(src_item_id);
				var i2 = decodeURIComponent(trg_item_id);
				pl.post_to_url("/prepo/elements_item_ref?view=2", {  i2:i2, e:e, v:v, });
			}
		
		};
		</script>
		
		<?php
		
		$dbh = dbconnect();
		
		//$i1 = get_post('i1');
		$i2 = get_post('i2');
		$e = get_post('e');
		$v = get_post('v');
		
		if (!empty($e) && !empty($v)  && !empty($i2)) {
			$SQL="UPDATE dsd.metadatavalue2 SET ref_item=?  WHERE text_value=?  AND element=?  AND ref_item is null";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $i2);
			$stmt->bindParam(2, $v);
			$stmt->bindParam(3, $e);
			$stmt->execute();
			$count = $stmt->rowCount();
			echo("<p>$count record updated</p>");
		
		
			$SQL="SELECT distinct dsd.touch_item(item_id) FROM dsd.metadatavalue2 WHERE ref_item = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $i2);
			$stmt->execute();
			$count = $stmt->rowCount();
			echo("<p>$count record updated</p>");
		
		
		}
		
		
		$SQL="select element, text_value, obj_type, item_id as target_i,count FROM dsd.elements_without_item_ref";
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		
		
		printf('<a href="/prepo/elements_item_ref?view=1">[DETAIL VIEW]</a><br/>');
		echo("<table>");
		echo('<tr>  <th>element</th>  <th>obj_type</th> <th>text value</th><th>count</th>  <th>command</th> </tr>');
		while($r = $stmt->fetch()){
			//	$src_item_id = $r[0];
			//		$label = $r[1];
			$elem = $r[0];
			$value = $r[1];
			$obj_type = $r[2];
			$trg_item_id = $r[3];
			$cnt = $r[4];
			$fn = sprintf("javascript:item_ref('%s','%s','%s')",rawurlencode($trg_item_id), rawurlencode($elem),rawurlencode($value));
			printf('<tr>  </td>  <td>%s </td> <td>%s </td>   <td> <a href="/archive/item/%s"  target="_blank" >%s</a> </td><td>%s</td><td> <a href="%s">[fix]</a> </td> </tr>',
			$elem, $obj_type, $trg_item_id, htmlspecialchars($value),$cnt,$fn);
		}
		echo("</table>");
				
		?>
		
		
<?php } ?>


@stop