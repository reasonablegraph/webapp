
@section('content')
<?php



##@##drupal_add_css(ARCHIVE_ASSETS_PATH . 'css/tagcloud.css');

//$lang = get_lang();
#echo("LANG:$lang");
$lang = 'el';

$dbh = dbconnect();

echo PConstants::COPYRIGHT;
$cat = get_get("c",0);

$tags= PDao::terms_cloud($cat);


	$SQL = "SELECT id,label FROM dsd.subject_cat ";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();

	//$subject_cats = $stmt->fetchAll();
	$subject_cats = array('0' => tr('Όλες'));
	while ($r = $stmt->fetch()){
		$subject_cats[$r[0]] = tr($r[1]);
	}

#	echo("<pre>");
#	print_r($subject_cats);
#	echo("</pre>");



	$print_link = function($c) use ($subject_cats,$cat,$lang) {

			$text = $subject_cats[$c];
			if ($c != $cat) {
                printf('<li><a href="/archive/tagcloud?lang=%s&c=%s">%s</a></li>',$lang,$c,$text);
			} else {
                printf('<li class="active"><a href="#">%s</a></li>',$text);
			}
			//echo('&#160;&#160;&#160;&#160;');
	};

    echo('<nav class="navbar navbar-default" role="navigation"> <div class="container-fluid">');
    echo '<div class="navbar-header"><button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#tagcloud-navbar-collapse-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>';
    echo '<p class="navbar-text">';
    echo(tr('Κατηγορίες:'));
    echo '</p></div>';
    //echo('&#160;&#160;&#160;&#160;');
    echo '<div class="collapse navbar-collapse" id="tagcloud-navbar-collapse-1"><ul class="nav navbar-nav">';
    $print_link(0);
    $print_link(1);
    $print_link(11);
    $print_link(12);
    $print_link(13);
    $print_link(14);
    $print_link(100);
    echo '</ul></div>';
    echo("</div></nav>");

	echo('<div class="clearb">&nbsp;</div>');
    
    echo('<div id="tagcloud">');

$coma = '';
foreach($tags as  $term){
	$word = trim($term[0]);
	if (! PUtil::strContains($word, ">")){
		$count = $term[1];
		$class = null;
		$cnt = floor($count/2);
		if ($cnt <2){
			#$tc = 0;
			$class = "";
		} elseif ($cnt > 10){
			#$tc = 10;
			$class = 'class="tag10"';
		} else {
			#$tc = $cnt -1;
			$class = 'class="tag' . $cnt .'"';
		}

		$ok= urlencode($word);
		#DEPR#printf('<a href="/archive/search?t=%s" %s>%s</a>, ',$ok,$class,$word);
		#printf('%s <a href="/archive/term?lang=%s&t=%s" %s>%s (%s)</a>',$coma,$lang,$ok,$class,$word,$count);
		#printf('%s <a href="/archive/term?lang=%s&t=%s" %s>%s (%s)</a>',$coma,$lang,$ok,$class,$word,$tc);
		printf('%s <a href="/archive/term?lang=%s&t=%s" %s>%s</a>',$coma,$lang,$ok,$class,$word);

		#print "$cnt";
		$coma =',';
	}
}

echo('</div>');

?>
@stop