@section('content')
<?php auth_check_mentainer(); ?>
<?php

$dbh = dbconnect();

$i=get_get('i');

$s=get_get('s');

if (!empty($s)){
	$SQL = "SELECT id FROM dsd.subject  where subject=?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $s);
	$stmt->execute();
	$res = $stmt->fetch();
	if (empty($res)){
		echo("NOT FOUND $i");
		exit();
	}
	$i = $res[0];
}

if (empty($i)){
	echo ("CANOT FIND TERM");
	return;
}
$id = $i;



function update_subject($dbh,$id,$field,$value){
	if(! empty($value)) {
		$SQL = "UPDATE dsd.subject SET " . $field . " = ? WHERE id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $value);
		$stmt->bindParam(2, $id,PDO::PARAM_INT);
		$stmt->execute();
	} else {
		$SQL = "UPDATE dsd.subject SET " . $field . " = null WHERE id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $id,PDO::PARAM_INT);
		$stmt->execute();
	}
};


if (! empty($_POST)){
	try {
	echo ("UPDATE $id");
		$dbh->beginTransaction();
		update_subject($dbh,$id,"subject", get_post("subject"));
		update_subject($dbh,$id,"internal_comment", get_post("intcom"));
		update_subject($dbh,$id,"description", get_post("desc"));
		update_subject($dbh,$id,"body", get_post("body"));
		update_subject($dbh,$id,"wikipedia_url", get_post("wikipedia"));
		//update_subject($dbh,$id,"type", get_post("type"));
		update_subject($dbh,$id,"cat", get_post("cat"));
		update_subject($dbh,$id,"item", get_post("item"));
		$dbh->commit();


		$SQL="update dsd.item2 set fst = dsd.get_item_fst(item_id);";
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();


	} catch (PDOException $e){
		$dbh->rollback();
		$error = $e->getMessage();
		echo("Can not UPDATE: $error\n");
		error_log( $error, 0);
		exit;
	}
}




$SQL = "SELECT id,subject, internal_comment , description, body, wikipedia_url, in_cloud, text_lang, cnt, type, create_dt, cat, item,type FROM dsd.subject  where id=?";
$stmt = $dbh->prepare($SQL);
$stmt->bindParam(1, $i);
$stmt->execute();
$res = $stmt->fetch();
if (empty($res)){
	echo("NOT FOUND $i");
	return;
}



$stype = null;
if (! empty($res['type'])){
	//$SQL="select full_element FROM public.metadatafieldregistry WHERE metadata_field_id = ?";
	$SQL="select label from dsd.subject_type  where id=?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $res['type']);
	$stmt->execute();
	$restype = $stmt->fetch();
	if (!empty($restype)){
		$stype=$restype[0];
	}
};

//echo("<pre>");
//print_r($res);
//echo("</pre>");



$SQL =" SELECT subject_other from dsd.subject_relation_arc_det where subject = ?";
$stmt = $dbh->prepare($SQL);
$stmt->bindParam(1, $res[1]);
$stmt->execute();
$relations = $stmt->fetchAll();

$subjects_print = function($subjects) use($res){
	echo('<div class="subjects">');
	if (!empty($subjects)){
		echo('<span class="subjectTitle">' . tr('Σχετικές Ετικέτες'). ': </span>');
		$coma = "";
		foreach ($subjects as $row){
			$my = $row[0];
			echo($coma);
			$ok = urlencode($my);
			printf('<a href="/prepo/subjects/subject?s=%s">%s</a>',$ok,$my);
			$coma =", ";
		}
	}
	printf(' &nbsp; &nbsp; <a href="/prepo/subjects/relation?t=%s">[relations admin]</a>',urlencode($res[1]));
	echo('</div>');
};
?>

<?php $subjects_print($relations)?>


<form method="post" action="/prepo/subjects/subject?i=<?php echo($i)?>" >

<table border="1">
<?php
echo ("<tr><td>items count:</td><td>");
echo($res[8]);
echo("</td></tr>");

echo ("<tr><td>id:</td><td>");
echo($res[0]);
echo("</td></tr>");


echo ("<tr><td>subject:</td><td>");
printf('<input type="text" name="subject" value="%s" size="80"/>',$res[1]);
echo("</td></tr>");

echo ("<tr><td>category:</td><td>");
PUtil::dbToSelect("SELECT id,label FROM dsd.subject_cat", "cat", $res['cat']);
echo("</td></tr>");


echo ("<tr><td>internal comment:</td><td>");
printf('<input type="text" name="intcom" value="%s" size="100"/>',$res[2]);
echo("</td></tr>");

echo ("<tr><td>description:</td><td>");
printf('<input type="text" name="desc" value="%s" size="100"/>',$res[3]);
echo("</td></tr>");

echo ("<tr><td>body:</td><td>");
printf('<textarea rows="2" cols="160" name="body">');
echo($res[4]);
echo("</textarea>");
echo("</td></tr>");

echo ("<tr><td>wikipedia url:</td><td>");
printf('<input type="text" name="wikipedia" value="%s" size="100"/>',$res[5]);
echo("</td></tr>");


echo ("<tr><td>item id:</td><td>");
printf('<input type="text" name="item" value="%s" size="10"/>',$res[12]);
if (!empty($res[12])){
	$SQL="SELECT label from dsd.item2 WHERE item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $res[12]);
	$stmt->execute();
	if ($r = $stmt->fetch()){
		echo(" &nbsp; ");
		echo($r[0]);
	}
}
echo("</td></tr>");

echo ("<tr><td>:</td><td>");
echo($res[6]);
echo("</td></tr>");

echo ("<tr><td>:</td><td>");
echo($res[7]);
echo("</td></tr>");

#echo ("<tr><td>items count:</td><td>");
#echo($res[8]);
#echo("</td></tr>");

echo ("<tr><td>type:</td><td>");
echo($res[9]); echo(" ");  echo($stype);
#printf('<input type="text" name="type" value="%s" size="10"/>',$res[9]);
echo("</td></tr>");

echo ("<tr><td>create dt:</td><td>");
echo($res[10]);
echo("</td></tr>");

?>
</table>

<input type="submit" value="save"/>
</form>

<br/>
<a href="/archive/term?i=<?php echo($i)?>">public</a>

@stop
