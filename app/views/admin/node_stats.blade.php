@section('content')

<?php auth_check_mentainer(); ?>
<?php


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
			printf('<td><a href="%s">%s</a></td>',$furl, $value);
		}
		printf('<td style="text-align:right;">%s</td>',$v[1]);
		echo("</tr>\n");
	}
	echo("</table>");

};



$table3 = function($title,$data,$url=null){

  printf('<table class="table table-striped table-bordered">');
  echo('<tr>');
  printf('<th colspan="3">%s</th>',$title);
  echo('</tr>');

  foreach($data as $k => $v){
    echo('<tr>');
    if (empty($url)){
      printf('<td>%s</td>',$v[0]);
    } else {
      $value = $v[0];
      $furl = sprintf($url,urlencode($value));
      printf('<td><a href="%s">%s</a></td>',$furl, $value);
    }
    printf('<td>%s</td>',$v[1]);
    printf('<td style="text-align:right;">%s</td>',$v[2]);
    echo("</tr>\n");
  }
  echo("</table>");

};




$main_stats = function() use ($table2,$table3){
	$dbh = dbconnect();

	echo('<h2>NODES</h2>');

//	$SQL="
//	SELECT obj_type,count(*) from dsd.item2
//	WHERE
//	status in ('finish','hidden','private') AND obj_type in (
//	SELECT obj_type FROM dsd.obj_type_class_def WHERE class='manifestation'
//	INTERSECT
//	SELECT obj_type FROM dsd.obj_type_class_def WHERE class='printed'
//	)
//	group by 1 order by 2 desc;
//	";

//SELECT text_value,count(*) from dsd.metadatavalue2 where element = 'ea:form-type:' group by 1;

//  $SQL="
//	SELECT i.obj_type, m.text_value as form_type,  count(*)
//	FROM dsd.item2 i
//	JOIN dsd.obj_type_class_def d ON (i.obj_type = d.obj_type)
//	JOIN dsd.metadatavalue2 m ON (m.item_id  = i.item_id AND m.element = 'ea:form-type:')
//	WHERE
//	i.status in ('finish','hidden','private')
//	AND d.class = 'manifestation'
//	group by 1, 2 order by 3 desc;
//	";
//  $stmt = $dbh->prepare($SQL);
//	//$stmt->bindParam(1, $item_id);
//	$stmt->execute();
//	$r1 = $stmt->fetchAll();

//	$SQL="
//	SELECT obj_type,count(*) from dsd.item2
//	WHERE
//	status in ('finish','hidden','private') AND obj_type in (
//	SELECT obj_type FROM dsd.obj_type_class_def WHERE class='manifestation'
//	EXCEPT
//	SELECT obj_type FROM dsd.obj_type_class_def WHERE class='printed'
//	)
//	group by 1 order by 2 desc;
//	";
//	$stmt = $dbh->prepare($SQL);
//	$stmt->execute();
//	$r2 = $stmt->fetchAll();
//
	$SQL="SELECT i.obj_type, m.text_value as form_type,  count(*)
	FROM dsd.item2 i
	LEFT JOIN dsd.metadatavalue2 m ON (m.item_id  = i.item_id AND m.element = 'ea:form-type:')
	WHERE i.status in ('finish','hidden','private')
  group by 1,2 order by 3 desc;";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$r3  = $stmt->fetchAll();


		/////////////////////////////////////////
		echo('<div class="nodes">');

//		echo('<div>');
//		$table3('manifestations',$r1,"/archive/recent?t=%s");
//		echo("</div>");

//		echo('<div>');
//		$table2('manifestations other',$r2,"/archive/recent?t=%s");
//		echo("</div>");

		echo('<div>');
		$table3('nodes',$r3,"/archive/recent?t=%s");
		echo("</div>");
		
		

		echo("</div>");


};



$order= get_get('o',1);

echo('<div class="metadata_stats">');

$main_stats($order);


echo '
  <style>
  table tr th {
   font-size: 1.5em;
   font-weight: bold;
   color:black;
  }
</style>
  ';

echo("</div>");
?>

@stop