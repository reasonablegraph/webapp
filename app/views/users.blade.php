<div  class="arch-wrap" id="tagcloud">
<?php
##@##drupal_add_css(ARCHIVE_ASSETS_PATH . 'css/tagcloud.css');

//$lang = get_lang();
#echo("LANG:$lang");
$lang = 'el';

$dbh = dbconnect();


//echo PConstants::COPYRIGHT;
//$cat = get_get("c",0);

$cat =0;

$tags= Ndao::terms_cloud($cat);


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
				printf('<a class="assetlink" href="/archive/tagcloud?lang=%s&c=%s">%s</a>',$lang,$c,$text);
			} else {
				printf('<span id="selectedcat">%s</span>',$text);
			}
			echo('&#160;&#160;&#160;&#160;');
	};

	echo('<div id="browsec1">&#160;&#160;&#160;&#160;');
	echo(tr('Κατηγορίες:'));
	echo('&#160;&#160;&#160;&#160;');
	$print_link(0);
	$print_link(1);
	$print_link(11);
	$print_link(12);
	$print_link(13);
	$print_link(14);
	$print_link(100);
	echo("</div>");

	echo('<div class="clearb">&nbsp;</div>');



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

?>
</div>