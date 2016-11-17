@section('content')

<?php auth_check_mentainer(); ?>
<?php

$dbh = dbconnect();

$s=get_get('t');
$delete_subject_id=get_get('dt');

if (! empty($delete_subject_id)){

	$SQL = "DELETE FROM dsd.subject_relation_arc WHERE id = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $delete_subject_id);
	$stmt->execute();

	$URL="/prepo/subjects/relation?t=$s";
	drupal_add_http_header ('Location',$URL);
}



//drupal_add_library('system', 'ui.autocomplete');
#drupal_add_css('http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css','external');

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/jquery-ui.min.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/themes/smoothness/jquery-ui.min.css');

drupal_add_js("/_assets/js/subject_relation.js",'external');





$s1 = get_post('t1');
$s2 = get_post('t2');

if (! empty($s1) && ! empty($s2)){
	$ok = true;
	$s = get_post('old_s');

	$SQL="SELECT 1 FROM  dsd.metadatavalue2 where metadata_field_id=" . Config::get('arc.DB_METADATA_FIELD_DC_SUBJECT') . "  AND text_value = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $s1);
	$stmt->execute();
	if (!$stmt->fetch()){
		$ok = false;
		echo ("<h2>$s1 dont exists</h2>");
	}

	$SQL="SELECT 1 FROM dsd.metadatavalue2 where  metadata_field_id=" . Config::get('arc.DB_METADATA_FIELD_DC_SUBJECT') . " AND text_value = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $s2);
	$stmt->execute();
	if (!$stmt->fetch()){
		$ok = false;
		echo ("<h2>$s2 dont exists</h2>");
	}


	$SQL = "SELECT 1 from dsd.subject_relation_arc_det where subject=? AND subject_other=?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $s1);
	$stmt->bindParam(2, $s2);
	$stmt->execute();
	if ($stmt->fetch()){
		$ok = false;
		echo ("<h2>$s1 <-> $s2 allredy exist</h2>");
	}

	if ($ok){
		$SQL = "INSERT INTO dsd.subject_relation_arc (subject_1,subject_2,relation_type) values (?,?,1)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $s1);
		$stmt->bindParam(2, $s2);
		$stmt->execute();
	}

}


?>
<h2>Search</h2>

<form method="get">
		<div class="ui-widget">
			<label for="terms">Term: </label>
			<input id="terms" class="sterm" type="text" name="t" value="<?=$s?>" />
		</div>

		<div class="clear">&nbsp;</div>
		<div class="subm">
			<input type="submit" value="Search"/>
			<input type="reset" value="Clear" />
		</div>

</form>

<hr/>

<h2>New Entry</h2>

<form method="POST" action="/prepo/subjects/relation">
 	<input type="hidden" name="old_s" value="<?=$s?>"/>
		<div class="ui-widget">
			<label for="terms">term 1: </label>
			<input id="terms" class="sterm" type="text" name="t1" value="<?=$s?>" />
		</div>
		<div class="ui-widget">
			<label for="terms">term 2: </label>
			<input id="terms" class="sterm" type="text" name="t2" value="" />
		</div>

		<div class="clear">&nbsp;</div>
		<div class="subm">
			<input type="submit" value="CREATE"/>
			<input type="reset" value="Clear" />
		</div>

</form>

<hr/>

<?php




if (! empty($s)) {
	$SQL = "SELECT id,subject_1,subject_2, create_dt FROM dsd.subject_relation_arc WHERE subject_1 = ? OR subject_2 = ?  order by create_dt desc limit 40 ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $s);
	$stmt->bindParam(2, $s);
	$stmt->execute();
	$res = $stmt->fetchAll();
} else {
	$SQL = "SELECT id,subject_1,subject_2, create_dt FROM dsd.subject_relation_arc order by create_dt desc limit 40 ";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$res = $stmt->fetchAll();
}




?>

<table class="table table-striped table-bordered table-hover">

<?php
$return_subject_ok = urlencode($s);
foreach($res as $key => $row){
	echo("<tr>\n");
	printf('<td><a href="/prepo/subjects/subject?s=%s">%s</a></td>',urlencode($row[1]),$row[1]);
	printf('<td><a href="/prepo/subjects/subject?s=%s">%s</a></td>',urlencode($row[2]),$row[2]);
	printf("<td>%s</td>",$row[3]);

	printf('<td><a href="/prepo/subjects/relation?dt=%s&t=%s"  onclick="return confirm(\'Are you sure you want to delete?\')"><span class="glyphicon glyphicon-remove"></span></a>',$row[0],$return_subject_ok);
	echo("<tr>\n");

}


?>
</table>

@stop

