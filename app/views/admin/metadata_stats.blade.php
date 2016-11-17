@section('content')
<?php auth_check_mentainer(); ?>

<style>
table tbody tr th {
	color:black;
	font-weight: bold;
}
</style>

<script>

	function view_value(i){
		jQuery('form#vform_' + i).submit();
	};

</script>



<?php

drupal_set_title("");


$table3 = function($title,$element, $type, $data){

	echo('<div class="panel panel-primary">');
	printf('<div class="a_thead a_files"> %s </div>',$title);
	echo('<div class="panel-files panel-body">');

	printf('<table id="fileTable" class="table table-striped table-bordered table-hover">');
// 	echo('<tr>');
// 	printf('<th colspan="3">%s</th>',$title);
// 	echo('</tr>');

	$i = 0;
	foreach($data as $k => $v){
		echo('<tr>');
		//printf('<td>%s</td>',$v[1]);
		$evar = 'e1';
		if ($type == 2){
			$evar = 'e2';
		}

		$link = $v[1];
		if (empty($link)){
			printf('<td>%s</td>', $v[0]);
		} else {
			printf('<td> %s <a href="/archive/item/%s"> [%s]</a></td>', $v[0], $link, $link);
		}
		echo('<td style="text-align:right;"> ');

		$i +=1;
		printf('<form id="vform_%s" method="POST">',$i);
		printf('<input type="hidden" name="%s" value="%s"/>',$evar, htmlspecialchars($element) );
		printf('<input type="hidden" name="v" value="%s"/>',htmlspecialchars($v[0]));
		echo('</form>');

		printf('<a onclick="view_value(%s)">[&nbsp;%s&nbsp;]</a>',$i, $v[2]);

		//printf('<a href="?%s=%s&v=%s">[&nbsp;%s&nbsp;]</a>',$evar,urlencode($element), urlencode($v[0]), $v[2]);

		echo('</td>');

		echo("</tr>\n");
	}
	echo("</table>");

	echo('</div>');
	echo('</div>');


};



$table2 = function($title,$data,$url=null){

	echo('<div class="panel panel-primary">');
	printf('<div class="a_thead a_files"> %s </div>',$title);
	echo('<div class="panel-files panel-body">');

	printf('<table id="fileTable" class="table table-striped table-bordered table-hover">');
// 	echo('<tr>');
// 	printf('<th colspan="2">%s</th>',$title);
// 	echo('</tr>');

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

	echo('</div>');
	echo('</div>');
};







$element_value_stats = function($el,$type,$value) {
	$dbh = dbconnect();
// 	echo("<h2>$el</h2>");
	echo("<h1 class='admin item-title spool'>$el</h1>");

	echo("<p style='padding:0px; font-size: 1em;' ><a href='/prepo/metadata_stats' style='border: 1px solid; color: #006366; padding: 2px 8px; border-radius: 4px;'>");
	echo("<span class='glyphicon glyphicon-link' aria-hidden='true'></span> Metadata Stats");
	echo("</a></p>");
// 	printf("<p>$value</p>");

	echo("<div class='panel panel-primary'>");
	echo("<div class='a_thead a_files'> Value: $value </div>");
	echo("<div class='panel-files panel-body'>");

	$SQL="SELECT i.obj_type, i.item_id, i.label
	FROM dsd.metadatavalue2 m
	JOIN dsd.item2 i ON (m.item_id = i.item_id)
	WHERE
	element=? AND m.text_value=?
	AND i.status in ('finish','hidden','private')  AND %s
	ORDER BY 1,2 desc
	";

	$t1 = "m.link is null";
	$t2 = "m.link is not null";

	if($type ==1 ){
		$SQL = sprintf($SQL,$t1);
	} else {
		$SQL = sprintf($SQL,$t2);
	}

	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $el);
	$stmt->bindParam(2, $value);
	$stmt->execute();
	$data = $stmt->fetchAll();


	echo("<table id='fileTable' class='table table-striped table-bordered table-hover'>");

		echo('<tr>');
			echo('<th style="width:30%" >Object type</th>');
			echo('<th style="width:70%" >Item link</th>');
		echo('</tr>');

		foreach($data as $k => $v){
			echo('<tr>');
			printf('<td>%s</td>',$v[0]);
			printf('<td><a href="/archive/item/%s">%s</a></td>',$v[1],$v[2]);
			echo("</tr>\n");
		}
		echo('</table>');
	echo("</div>");
	echo("</div>");




};

$element_stats = function($element,$type,$order,$object_type) use ($table2,$table3){

	//echo ("ELEMENT STATS");
	$dbh = dbconnect();

// 	echo("<a href='/prepo/metadata_stats' style='border: 1px solid;padding: 2px 8px;border-radius: 4px;'>");
// 	echo("<span class='glyphicon glyphicon-link' aria-hidden='true'></span> Metadata Stats");
// 	echo("</a>");

	if (empty($object_type)){
// 		echo("<h2>$element</h2>");
		echo("<h1 class='admin item-title spool'>$element</h1>");
	} else {
		echo("<h1 class='admin item-title spool'>$element ($object_type)</h1>");
// 		echo("<h2>$element    &nbsp; ($object_type)</h2>");
	}

	echo("<p style='padding:0px; font-size: 1em;'><a href='/prepo/metadata_stats' style='border: 1px solid; color: #006366; padding: 2px 8px; border-radius: 4px;'>");
	echo("<span class='glyphicon glyphicon-link' aria-hidden='true'></span> Metadata Stats");
	echo("</a></p>");

	$t1 = "m.link is null";
	$t2 = "m.link is not null";

$SQL="SELECT i.obj_type, count(*)
	FROM dsd.metadatavalue2 m
	JOIN dsd.item2 i ON (m.item_id = i.item_id)
	WHERE
	element=?
	AND i.status in ('finish','hidden','private') AND %s";

	if($type == 1 ){
		$SQL = sprintf($SQL,$t1);
	} else {
		$SQL = sprintf($SQL,$t2);
	}

	$SQL .= " group by 1 order by 2 desc";

	//echo($SQL);

	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $element);
	$stmt->execute();
	$r1 = $stmt->fetchAll();


	$SQL="SELECT m.text_value,m.ref_item,count(*)
	FROM dsd.metadatavalue2 m
	JOIN dsd.item2 i ON (m.item_id = i.item_id)
	WHERE
	element=?
	AND i.status in ('finish','hidden','private') AND %s";
	if (!empty($object_type)){
	 		$SQL .= ' AND i.obj_type = ? ';
	}
	$SQL .= ' GROUP BY 1,2';

	//echo($SQL);

	$qurl = null;
	if($type ==1 ){
		$qurl = sprintf('?e1=%s', urlencode($element));
		$SQL = sprintf($SQL,$t1);
	} else {
		$qurl= sprintf('?e2=%s' ,urlencode($element));
		$SQL = sprintf($SQL,$t2);
	}
	$qurl = str_replace("%","%%",$qurl);
	$qurl .= '&c=%s';

	if ($order == '2'){
		$SQL .= " order by 3 desc, 1,2";
	} else {
		$SQL .= " order by 1,2,3 desc";
	}

	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $element);
	if (!empty($object_type)){
	 		$stmt->bindParam(2, $object_type);
	}

	$stmt->execute();
	$r2 = $stmt->fetchAll();




	echo('<div class="metadata">');


	echo('<div class="col-sm-3" style="padding-left:1px;">');
// 	echo('<div class="metadata_1">');
	$table2('obj types',$r1,$qurl);
	echo("</div>");

	echo('<div class="col-sm-9" style="padding-right:1px;">');
// 	echo('<div class="metadata_3">');
	$table3('values',$element,$type, $r2);
	echo("</div>");



	echo("</div>");



};


$main_stats = function() use ($table2){
	$dbh = dbconnect();



	$SQL="SELECT m.element,count(*)
	FROM dsd.metadatavalue2 m
	JOIN dsd.item2 i ON (m.item_id = i.item_id)
	WHERE i.status in ('finish','hidden','private') AND m.link is null
	group by 1 order by 1 asc";

	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$r4  = $stmt->fetchAll();


	$SQL="SELECT m.element,count(*)
	FROM dsd.metadatavalue2 m
	JOIN dsd.item2 i ON (m.item_id = i.item_id)
	WHERE i.status in ('finish','hidden','private') AND m.link is not null
	group by 1 order by 1 asc";

	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$r5  = $stmt->fetchAll();

		/////////////////////////////////////////
		echo('<div class="metadata">');
			echo("<h1 class='admin item-title spool'>Metadata Stats</h1>");
			echo('<div class="col-sm-6" style="padding-left:1px;">');
				$table2('Root metadata',$r4,'?e1=%s');
			echo("</div>");
			echo('<div class="col-sm-6" style="padding-right:1px;">');
				$table2('Child metadata',$r5,'?e2=%s');
			echo("</div>");
		echo("</div>");

};



$order= get_get('o',1);
$element1 = get_get('e1');
$element2 = get_get('e2');
$value = get_post_get('v');
$c = get_get('c');

echo('<div class="metadata_stats">');

if (!empty($element1) || !empty($element2) ){
	$type = 1;
	$el = $element1;
	if (!empty($element2)){
		$type = 2;
		$el = $element2;
	}
	if (!PUtil::isEmpty($value)){
		//echo("##1");
		$element_value_stats($el,$type,$value);
	} else {
		$element_stats($el,$type,$order,$c);
	}
} else {
	$main_stats($order);
}

echo("</div>");
?>

@stop