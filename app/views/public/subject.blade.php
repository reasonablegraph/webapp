@section('content')
<?php
##@##drupal_add_css(ARCHIVE_ASSETS_PATH .'css/subject.css');
?>

<?php


//$s = get_get('t');
$i = PUtil::reset_int(get_get('i'));

$o = PUtil::reset_int(get_get('o'),0);//offset
$r = PUtil::reset_int(get_get('r'),1);//order

$tweet_offset = PUtil::reset_int(get_get('to'));
// $tweet_offset = 0;
// if (isset($_GET('to'))){
// 	$tweet_offset =  PUtil::extract_int($_GET('to'));
// }


$lang = get_lang();
#echo("LANG:$lang");


$s = urldecode($_REQUEST['term']);


if (empty($s) && empty($i)){
    //drupal_set_title("term expected");
    echo("<p>term expected</p>");
    echo("<span></span>");
    return;
}



$display_twitts = function() use ($s,$tweet_offset,$r,$o,$lang) {
	echo('<div class="results">');
	$tlimit=80;
	$tc = PSnipets::display_twitts($s,$tweet_offset,$tlimit);

	if ($tc > 0  && $tc == $tlimit){
		$murl =sprintf('/archive/term?t=%s&lang=%s&r=%s&o=%s&to=%s',urlencode($s),urlencode($lang),urlencode($r),urlencode($o), urlencode($tweet_offset + $tlimit-1));
		printf('<a href="%s">[more tweets]</a>',$murl);
	} else if ($tc > 0){
		$murl =sprintf('/archive/term?t=%s&lang=%s&r=%s&o=%s',urlencode($s),urlencode($lang),urlencode($r),urlencode($o));
		printf('<a href="%s">[first tweets page]</a>',$murl);
	}
	echo('</div>');
};


$dbh = dbconnect();

$param ="";
$BASE_SQL = "SELECT  id,subject, internal_comment , description, body, wikipedia_url, in_cloud, text_lang, cnt, type, create_dt, item
				FROM dsd.subject  WHERE  cnt>0 AND  ";
if (!empty($i)) {
  $WHERE = "id=?";
  $param = $i;
} else if (!empty($s)) {
  $WHERE = "subject = ? ";
  $param = $s;
} else {
 // drupal_set_title("not found");
 echo("<p>not found</p>");
  echo("<br/>");
  return;
}

$SQL = $BASE_SQL . $WHERE;

$stmt = $dbh->prepare($SQL);
$stmt->bindParam(1, $param);
$stmt->execute();
$subject_arr = $stmt->fetch();
if (empty($subject_arr)){
	if (PUtil::strBeginsWith($s, '#')){
		//drupal_set_title($s);
		echo("<h1>$s</h1>");
		$display_twitts();
	} else {
		//drupal_set_title("not found");
		echo("<p>not found</p>");
		echo("<br/>");
	}
	return;
}


$i = $subject_arr['id'];
$s = $subject_arr['subject'];
$item = $subject_arr['item'];
if (! empty($item)){
  $header = sprintf('/archive/item/%s', $item);
  drupal_add_http_header('Location',$header);
  return;
}




$sok = $s;
$delimiter='';
if(strpos($s,'>') !== false){

	//drupal_set_title('');
	$sok='';
	$ttt = explode('>',$s);
	foreach ($ttt as $tag){
		$sok .= sprintf('%s<a href="/archive/term?t=%s&lang=%s">%s</a>',$delimiter, urlencode($tag),$lang,$tag);
		$delimiter=', ';
	}
	printf('<h1>%s</h1>',$sok);
} else {
	//drupal_set_title($s);
	printf('<h1>%s</h1>',$s);

}


echo("<ul>");
if (! empty($subject_arr['wikipedia_url'])){
	printf('<li><a href="%s"> %s </a></li>',$subject_arr['wikipedia_url'],$subject_arr['wikipedia_url']);
}
echo("</ul>");

$SQL="SELECT subject from dsd.subject where cnt>0 AND subject like '%>%' and subject like '$s>%'";
$stmt = $dbh->prepare($SQL);
$stmt->execute();
$rels1 = $stmt->fetchAll();
$SQL="SELECT subject from dsd.subject where cnt>0 AND subject like '%>%' and subject like '%>$s%'";
$stmt = $dbh->prepare($SQL);
$stmt->execute();
$rels2 = $stmt->fetchAll();


$rels = array_merge($rels1,$rels2);

if (!empty($rels)){
	printf("<h2>%s</h2>",tr('Σχετικές Αλυσίδες'));
	echo("<ul>");
	foreach ($rels as $rec){
		$rel = $rec[0];
		$ok = urlencode($rel);
		printf('<li><a href="/archive/term?t=%s&lang=%s">%s</a></li>',$ok,$lang,$rel);
	}
	echo("</ul>");

}



$SQL = " SELECT subject_other from dsd.subject_relation_arc_det where subject = ?";
  $stmt = $dbh->prepare($SQL);
  $stmt->bindParam(1, $s);
  $stmt->execute();
  $res = $stmt->fetchAll();


if (count($res) > 0){
printf("<h2>%s</h2>",tr('Σχετικές Eτικέτες'));
echo('<div class="results">');

  $coma = "";
  foreach ($res as $row){
    $my = $row[0];
    echo($coma);
    $ok = urlencode($my);
    printf('<a href="/archive/term?t=%s&lang=%s">%s</a>',$ok,$lang, $my);
    $coma =", ";
  }

echo('</div>');
}




  echo('<div class="results">');


  $SQL  = "SELECT " . Config::get('arc.ITEM_LIST_SQL_FIELDS') .  " FROM dsd.item2 i ";
  $SQL .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.metadata_field_id  = " . Config::get('arc.DB_METADATA_FIELD_DC_SUBJECT') . " ) ";
  $SQL .= " WHERE  m.text_value = ?  AND i.status = 'finish' AND i.in_archive ";

  $stmt = $dbh->prepare($SQL);
  $stmt->bindParam(1, $s);
  $stmt->execute();

  $members = 	$stmt->fetchAll();

  $obj_type_names = PDao::get_object_type_names();



  echo('<table id="members" class="table table-striped table-bordered">');
  echo('<thead>');
  printf('<tr><th colspan="4">&nbsp; %s</th></tr>',tr('Τεκμήρια'));
  echo('</thead>');
  echo('<tbody valign="top">');
  echo("\n");

  PSnipets::item_list($members, $obj_type_names);

  echo('</tbody>');
  echo('</table>');




  echo('</div>');


  $display_twitts();



//$repo_maintainer_flag = user_access("repo_maintainer");
$repo_maintainer_flag = ArcApp::has_permission(Permissions::$REPO_MAINTAINER );
if ($repo_maintainer_flag){
  echo("<hr/>");
  echo('<div id="admin_area">');
  echo('<table class="table table-striped table-bordered">');
  echo("<tr><td>");
  #$term_ok = urlencode($s);
  printf('admin: <a href="/prepo/subjects/subject?i=%s">[edit]</a>',$i);
  echo("</td></tr>");
  echo("</table>");
  echo("</div>");
}


?>


@stop