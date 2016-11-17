@section('content')

<?php auth_check_mentainer(); ?>
<?php

drupal_set_title("");



$table2 = function($title,$data,$url=null){

	printf('<table class="table table-striped table-bordered">');
	echo('<tr>');
	printf('<th colspan="2">%s</th>',$title);
	echo('</tr>');

	foreach($data as $k => $v){
		echo('<tr>');
		if (empty($url)){
			printf('<td>%s</td>',$v[0]);
		} else {
			$value = $v[0];
			$furl = sprintf($url,urlencode($value));
			printf('<td><a href="%s">%s</a></td>',$furl, $value,$value);
		}
		printf('<td style="text-align:right;">%s</td>',$v[1]);
		echo("</tr>\n");
	}
	echo("</table>");

};





$main_stats = function() use ($table2){
	$dbh = dbconnect();

	echo('<h2>NODES</h2>');

	$SQL="
	SELECT obj_type,count(*) from dsd.item2
	WHERE
	status in ('finish','hidden','private') AND obj_type in (
	SELECT obj_type FROM dsd.obj_type_class_def WHERE class='manifestation'
	INTERSECT
	SELECT obj_type FROM dsd.obj_type_class_def WHERE class='printed'
	)
	group by 1 order by 2 desc;
	";
	$stmt = $dbh->prepare($SQL);
	//$stmt->bindParam(1, $item_id);
	$stmt->execute();
	$r1 = $stmt->fetchAll();

	$SQL="
	SELECT obj_type,count(*) from dsd.item2
	WHERE
	status in ('finish','hidden','private') AND obj_type in (
	SELECT obj_type FROM dsd.obj_type_class_def WHERE class='manifestation'
	EXCEPT
	SELECT obj_type FROM dsd.obj_type_class_def WHERE class='printed'
	)
	group by 1 order by 2 desc;
	";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$r2 = $stmt->fetchAll();

	$SQL="select obj_type,count(*) from dsd.item2 WHERE obj_class <> 'manifestation' group by 1 order by 2 desc;";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$r3  = $stmt->fetchAll();


		/////////////////////////////////////////
		echo('<div class="nodes">');

		echo('<div>');
		$table2('manifestations print',$r1,"/archive/recent?t=%s");
		echo("</div>");

		echo('<div>');
		$table2('manifestations other',$r2,"/archive/recent?t=%s");
		echo("</div>");

		echo('<div>');
		$table2('other',$r3,"/archive/recent?t=%s");
		echo("</div>");
		
		

		echo("</div>");


};



$order= get_get('o',1);

echo('<div class="metadata_stats">');

$main_stats($order);

echo("</div>");
?>

@stop