@section('content')
<?php auth_check_mentainer(); ?>
<?php
drupal_set_title("artifacts stats");

$artifact_type = variable_get('arc_artifact_object_type', 'artifact');


$dbh = dbconnect();

echo('<a href="/prepo/artifacts_list"> artifacts </a>');

$SQL ="select count(*) from dsd.artifacts";
$stmt = $dbh->prepare($SQL);
$stmt->execute();
$r = $stmt->fetch();
$cnt = $r[0];
printf('<p>total artifacts: %s</a>',$cnt);



$SQL ="select sn_pref, max(sn_suff) from dsd.artifacts where sn_suff is not null group by 1 order by 1";
$stmt = $dbh->prepare($SQL);
//$stmt->bindParam(1, $item_id);
$stmt->execute();
$r = $stmt->fetchAll();
	echo('<table style="width:400px">');
	echo('<tr>');
	echo('<th colspan="2">serial numbers</th>');
	echo('</tr>');
	echo('<tr>');
	echo('<th>sn prefix</th>');
	echo('<th>max</th>');
	echo('</tr>');
	foreach($r as $k => $v){
		$prefix = $v[0];
		$cnt = $v[1];
		echo('<tr>');
		printf('<td>%s</td>',$prefix);
		printf('<td style="text-align:right;">%s</td>',$cnt);
		echo("</tr>\n");
	}
	echo("</table>");


if ($artifact_type == 'artifact'){

		$SQL ="select call_number_pref, max(call_number_sn) from dsd.artifacts where call_number_sn is not null group by 1 order by 1";
		$stmt = $dbh->prepare($SQL);
		//$stmt->bindParam(1, $item_id);
		$stmt->execute();
		$r = $stmt->fetchAll();
		echo('<table style="width:400px">');
		echo('<tr>');
		echo('<th colspan="2">call numbers</th>');
		echo('</tr>');
		echo('<tr>');
		echo('<th>call number prefix</th>');
		echo('<th>max</th>');
		echo('</tr>');
		foreach($r as $k => $v){
			$prefix = $v[0];
			$cnt = $v[1];
			echo('<tr>');
			printf('<td>%s</td>',$prefix);
			printf('<td style="text-align:right;">%s</td>',$cnt);
			echo("</tr>\n");
		}
		$SQL ="select call_number_pref, count(*) from dsd.artifacts  group by 1 order by 1";
		$stmt = $dbh->prepare($SQL);
		//$stmt->bindParam(1, $item_id);
		$stmt->execute();
		$r = $stmt->fetchAll();
		echo('<tr>');
		echo('<th>call number prefix</th>');
		echo('<th>count</th>');
		echo('</tr>');
		foreach($r as $k => $v){
			$prefix = $v[0];
			$cnt = $v[1];
			echo('<tr>');
			printf('<td>%s</td>',$prefix);
			printf('<td style="text-align:right;">%s</td>',$cnt);
			echo("</tr>\n");
		}
		echo("<table>");
	}


	$SQL="select sn, count(*) from dsd.artifacts where sn is not null group by 1 having count(*) > 1;";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$r = $stmt->fetchAll();
	if (count($r) > 0){
		echo('<table style="width:400px">');
		echo('<tr>');
		echo('<th colspan="2" style="color:red">serial number errors</th>');
		echo('</tr>');
		echo('<tr>');
		echo('<th>serial numbers</th>');
		echo('<th>count</th>');
		echo('</tr>');
		foreach($r as $k => $v){
			$prefix = $v[0];
			$cnt = $v[1];
			echo('<tr>');
			printf('<td>%s</td>',$prefix);
			printf('<td style="text-align:right;">%s</td>',$cnt);
			echo("</tr>\n");
		}
		echo("<table>");
	}



if ($artifact_type == 'artifact'){

	$SQL="select call_number, count(*) from dsd.artifacts where call_number is not null group by 1 having count(*) > 1;";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$r = $stmt->fetchAll();
	if (count($r) > 0){
		echo('<table style="width:400px">');
		echo('<tr>');
		echo('<th colspan="2" style="color:red">call number errors</th>');
		echo('</tr>');
		echo('<tr>');
		echo('<th>call numbers</th>');
		echo('<th>count</th>');
		echo('</tr>');
		foreach($r as $k => $v){
			$prefix = $v[0];
			$cnt = $v[1];
			echo('<tr>');
			printf('<td>%s</td>',$prefix);
			printf('<td style="text-align:right;">%s</td>',$cnt);
			echo("</tr>\n");
		}
		echo("<table>");
	}
}



if ($artifact_type == 'artifact1'){
// having count(*) > 1
	$SQL="select call_number_ddc, count(*) from dsd.artifacts where call_number is not null group by 1  order by 2 desc;";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$r = $stmt->fetchAll();
	if (count($r) > 0){
		echo('<table style="width:400px">');
		echo('<tr>');
		echo('<th>call number ddc</th>');
		echo('<th>count</th>');
		echo('</tr>');
		foreach($r as $k => $v){
			$prefix = $v[0];
			$cnt = $v[1];
			echo('<tr>');
			printf('<td>%s</td>',$prefix);
			printf('<td style="text-align:right;">%s</td>',$cnt);
			echo("</tr>\n");
		}
		echo("<table>");
	}
}


?>
@stop