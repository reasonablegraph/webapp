@section('content')
<?php auth_check_mentainer(); ?>
<?php
drupal_set_title("isis admin");
$mfn = get_get("mfn");
$book = get_get("book");
$node = get_get("node");
?>




<form method="get">
mfn: <input type="text" name="mfn" />
book: <input type="text" name="book" />
node:  <input type="text" name="node" />

<input type="submit"/> &nbsp;
<input type="reset"/>
</form>




<?php
	$show_flag = false;
if (! PUtil::isEmpty($mfn) ){
	echo("mfn: " . $mfn);
	$show_flag = true;
} else if (! PUtil::isEmpty($book) ) {
	echo("book " . $book);
	$show_flag = true;
} else if (! PUtil::isEmpty($node) ) {
	echo("node " . $node);
	$show_flag = true;
}


 if($show_flag){


//$ARTIFACT_STATUS_MAP = Lookup::get_artifact_status_values();
$dbh = dbconnect();


$print_line = function($v) {
	$item_id = $v['item_id'];
	$label = $v['label'];
	echo('<tr>');
	printf('<td style="text-align:right;">%s</td>',$item_id);
	printf('<td><a href="/archive/item/%s">%s</a></td>',$item_id, $label);
	echo("</tr>\n");
};

if (!empty($node)){


$is = PUtil::extract_int($node);
if ($is == null  || $is < 0 ){
	return;
}
echo('<table>');
echo('<tr>');
echo('<th>node</th>');
echo('<th>title</th>');
echo('</tr>');

	$SQL =  "select i.item_id, i.label from dsd.item2  i  WHERE item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $is);
	$stmt->execute();
	$r = $stmt->fetchAll();


	foreach($r as $k => $v){
		$print_line($v);
	}
echo("<table>");

return;
}


	$SQL_PART =  "select i.item_id, i.label from dsd.item2  i  join dsd.metadatavalue2 v on (i.item_id = v.item_id and element='%s')  where i.obj_type = 'book' AND text_value=?";
	$ss = null;
	if (!empty($mfn)){
		$SQL = sprintf($SQL_PART,'isis:mfn:');
		$ss = $mfn;
	} else if (!empty($book)){
	$SQL = sprintf($SQL_PART,'isis:book:id');
		$ss = $book;
	}

$ss = trim($ss);
if (empty($ss) || PUtil::strContains($ss, ' ')){
	return;
}

$is = PUtil::extract_int($ss);
if ($is == null  || $is < 0 ){
	return;
}
$ss = $is;

echo('<table>');
echo('<tr>');
echo('<th>node</th>');
echo('<th>title</th>');
echo('</tr>');

	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $ss);
	$stmt->execute();
	$r = $stmt->fetchAll();


	foreach($r as $k => $v){
		$print_line($v);
	}




echo("<table>");
 }
?>
@stop