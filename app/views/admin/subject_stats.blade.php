@section('content')
<?php auth_check_mentainer(); ?>
<?php

drupal_set_title("");

	$dbh = dbconnect();

	$SQL = "select  text_value, dsd.gr_fonitiko(text_value) as text_value_search,cnt
	FROM dsd.mvalue WHERE  cnt > 0 AND text_value_fst in (
	select text_value_fst FROM (
	select text_value_fst,count(*) FROM dsd.mvalue  WHERE cnt>0 AND metadata_field_id = 57 group by 1 having count(*) > 1
	) as foo
	) ORDER BY 2;";


	//$SQL="select text_value,text_value_search,count(*) from dsd.keywords_provlimatika2  group by 1,2 order by 2,1";
	$stmt = $dbh->prepare($SQL);

	$stats = array();
	$stmt->execute();
	while ($r= $stmt->fetch()){
		//print_r($r);
		$vs = $r['text_value_search'];
		$v = $r['text_value'];
		$c = $r['cnt'];

		if (!isset($stats[$vs])){
			$stats[$vs] = array();
		}
		$stats[$vs][] = array($v,$c);

	}

// 	echo("<pre>");
// 	print_r($stats);
// 	echo("</pre>");

	echo('<table class="table table-striped table-bordered table-hover">');
	foreach($stats as $sk => $sv){
		echo('<tr>');
		printf('<td>%s</td>',$sk);
		echo('<td>');
		$sep = '';
		foreach($sv as $v){
			echo($sep);
			$url = sprintf('/archive/term?t=%s',urlencode($v[0]));
			printf('<a href="%s"  target="_blank">%s</a> (%s)',$url, $v[0], $v[1]);
			$sep = ' ; ';
		}
		echo('</td>');
		echo('</tr>');
	}
	echo("</table>");

?>
@stop