@section('content')
<?php auth_check_mentainer(); ?>
<?php

$e1 = get_get("e1");
$v1 = get_get("v1");


if (!empty($v1))  {

	drupal_set_title("");


	$dbh = dbconnect();

	if (!empty($e1)){

		$SQL="SELECT i.label, m.item_id,  m.element, m.obj_type, ref_item
		FROM dsd.metadatavalue2 m
		JOIN dsd.item2 i on (i.item_id = m.item_id)
		WHERE element like ? AND text_value like ? ORDER BY element,item_id limit 160
		";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $e1);
		$stmt->bindParam(2, $v1);
		$stmt->execute();
		$data = $stmt->fetchAll();

	} else {

	$SQL="SELECT i.label, m.item_id,  m.element, m.obj_type, ref_item
			FROM dsd.metadatavalue2 m
			JOIN dsd.item2 i on (i.item_id = m.item_id)
			WHERE text_value like ? ORDER BY element,item_id limit 160
			";

	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $v1);
	$stmt->execute();
	$data = $stmt->fetchAll();

}


}


?>

<h1 class="admin item-title spool">Metadata Search</h1>

<div class="panel panel-primary">
<!-- <div class="a_thead a_files"> Root metadata </div> -->
<div class="panel-files panel-body">

<div id="metsearch">
    <form method="GET" class="form-inline" role="form">
          <div class="form-group">
            <label for="e1" style="width:70px">Element:</label>
            <input type="text"  name="e1" class="form-control" id="e1" value="<?=$e1?>">
          </div>
          <div class="form-group">
            <label for="v1" style="width:70px">Value:</label>
            <input type="text" name="v1" class="form-control" id="v1" value="<?=$v1?>">
          </div>
          <br/>
<!--           <input type="submit" class="btn btn-default"/> -->

<div class="fileUpload uploadbut">
<span>Search Metadata</span>
<input id="uploadBtn" class="upload" type="submit" value="Search_Metadata">
</div>

    </form>
</div>

</div>
</div>




	<?php if (!empty($data)){ 	?>
	<div class="panel panel-primary">
		<div class="a_thead a_files"> Result </div>
		<div class="panel-files panel-body">
			<table id="fileTable" class="table table-striped table-bordered table-hover">
			<?php
			foreach ($data as $r){
				echo("<tr>");
				printf("<td>%s</td>",$r['obj_type']);
				printf("<td>%s</td>",$r['element']);
				printf("<td>%s</td>",$r['item_id']);
				printf('<td><a href="/archive/item/%s">%s</a></td>',$r['item_id'],$r['label']);
				if (empty($r['ref_item'])){
						printf("<td></td>");
				}else {
					printf('<td><a href="/archive/item/%s">%s</a></td>',$r['ref_item'],$r['ref_item']);
				}
				echo("</tr>");
			}
		}
			?>
		</table>
	</div>
</div>
@stop