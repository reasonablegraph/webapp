@section('content')

<?php
//@DocGroup(module="item", group="php", comment="item custome page generic")
// drupal_add_css(ARCHIVE_ASSETS_PATH . 'css/item.css');
// drupal_add_css(ARCHIVE_ASSETS_PATH . 'css/tinymce_styles.css');


if (Config::get('arc.LOAD_JS')){
# laravel jquery
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-colorbox/jquery.colorbox.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-colorbox/example5/colorbox.css');
?>

<script>
$(document).ready(function(){
	$(".colorbox-load").colorbox({rel:'nofollow'});
	$(".group").colorbox({rel:'gal' });
});
</script>

<?php



?>



<script type="text/javascript">
// jQuery(document).ready(function() {
	//jQuery("a.group").fancybox();
});
</script>


<?php

	$lang = get_lang();
	#echo("LANG:$lang");

// 	$i = get_get("i");
// 	if (empty($i)){
// 		$i = isset($_REQUEST['item_id']) ?$_REQUEST['item_id'] : null   ;
// 		if (empty($i)){
// 			echo("<p>expected item_id &#160;</p>");
// 			return;
// 		}
// 	}
// 	if (!preg_match("/^\d+$/", $i)) {
// 		return;
// 	}
// 	$d = reset_int(get_get("d"),1);//display method (list,thumb1,thumb2)
//
// 	$rep = item($dbh,$i);
// 	if(empty($rep)){
// 		echo("<p>&#160;</p>");
// 		return;
// 	}

	$i = $_REQUEST['item_id'];
	$rep = $_REQUEST['item'];
	$d = $_REQUEST['display_method'];
	$idata = ItemMetadata::s($rep['idata']);
	$obj_type = $idata->getObjectType();

	$dbh = dbconnect();

	$item_id = $i;

//	drupal_set_title($rep['label']);

	$relations = Lookup::getRelationElementsItemLabelMap();

	$ref_bitstream = $rep['ref_bitstream'];
	$ref_content = $rep['ref_content'];

##############################################
## LIST OF ITEMS
##############################################
	function size_buttons($i,$lang) {
		echo('<div id="sizebtns" >');
		#echo(tr('Αλλαγή εμφάνισης') . ': &nbsp;');
		printf('<a href="/archive/item/%s&lang=%s&d=4"><img class="sizebtn" src="/_assets/img/vthumbs2.png"/></a>',$i,$lang);
		printf('<a href="/archive/item/%s&lang=%s&d=3"><img class="sizebtn" src="/_assets/img/vthumbs.png"/></a>',$i,$lang);
		printf('<a href="/archive/item/%s&lang=%s&d=1"><img class="sizebtn" src="/_assets/img/vlist.png"/></a>',$i,$lang);
		echo('</div>');
	};

	function list_of_items($d, $members, $item_id, $lang,$obj_type_names, $title = null, $list_edit_flag  = false){

		if ($d == 1){
			$colspan="4";
			if ($list_edit_flag){
				$colspan="5";
			}

			echo('<table id="members" class="table table-striped table-bordered">');
			echo('<thead>');
			printf('<tr><th colspan="%s">',$colspan);
			if (! empty($title)){
				printf('<span style="float:left">&nbsp; %s :</span>',tr($title));
			}
			size_buttons($item_id,$lang);
			echo("</th></tr>");
			echo('</thead>');
			echo('<tbody valign="top">');
			echo("\n");

			PUtil::item_list($members, $obj_type_names,false,$list_edit_flag,true);

			echo('</tbody>');
			echo('</table>');
		} elseif ($d > 1){
			#####################################################################################
			### TABLE HEADER THUMBS
			#####################################################################################

			echo('<table id="tresults" class="table table-striped table-bordered">');
			echo("\n");
			echo('<thead>');
			echo('<tr><th>');
			size_buttons($item_id,$lang);
			echo('</th></tr>');
			echo('</thead>');
			echo('</table>');

			PUtil::item_list_thumbs($members,$d,$lang);

			#####################################################################################
			### TABLE FOOTER THUMBS
			#####################################################################################

			echo('<br/>');
			echo('<table class="table table-striped table-bordered"><tr><td style="text-align: center;">');
			echo('</td></tr></table>');
		}
	}
#
#echo("<pre>");
#print_r($rep);
#echo("</pre>");
	$type = $rep['type'];
	$periodical =($type == 'periodiko' || $type=='efimerida' || $type == 'web-site');
	$website =($type == 'web-site-instance');
	$silogi =($type == 'silogi');
	$person = $type == 'actor';
	$place = $type == 'place';
	$work = $type == 'work';
	$recipe = $type == 'recipe';
	$instrument = $type == 'instrument';
	$periodicalayout = $periodical ? 'iteminf-periodic'  : 'iteminf';
	$bibref = $rep['bibref'];

	$list_edit_flag = (/*user_access_admin()*/true && $silogi);
?>


<?php if($periodicalayout == 'iteminf-periodic') : $leftclmnclass = 'col-sm-12'; ?>
    <script>
    jQuery(document).ready(function($) {
        var $container = $('#pagesthumbs');
        // initialize Masonry after all images have loaded
        $container.imagesLoaded( function() {
          $container.masonry();
        });

    });
    </script>

<?php else : $leftclmnclass = 'col-sm-9'; endif; ?>

<div id="<?php echo $periodicalayout; ?>" class="row">
    <div class="<?php echo $leftclmnclass; ?> item-left">


<?php

	$thumbs_s = $rep['thumbs_small'];
	$thumbs_b = $rep['thumbs_big'];
	$has_thumbs_s = (!empty($thumbs_s));
	$has_thumbs_b = (!empty($thumbs_b));

	$has_page_thumbs_s = ($has_thumbs_s && count($thumbs_s) > 0);
	$has_page_thumbs_b = ($has_thumbs_b && count($thumbs_b) > 0);
	#echo("#1################ $has_page_thumbs_s ###############");
	#echo("#2################ $has_page_thumbs_b ###############");

?>

	<h2 class="item-title"><?php html_echo($rep['label'])?></h2>

	<?php


	if( ! empty($rep['subtitle'])){
		echo("<p>" . html_data_view($rep['subtitle']) . "</p>\n");
	} else {
		if ($idata->hasKey('marc:title-statement:title')){
 				$title_m = ItemValue::c($idata->getValueSK('marc:title-statement:title'));
 				$link = $title_m->recordId();
 				$subtitle_m = $idata->getFirstValueByKeyLink('marc:title-statement:remainder',$link);
 				if (!empty($subtitle_m) && !empty($subtitle_m[0])){
 					$subtitle = $subtitle_m[0];
 					$format = ItemValue::c($idata->getFirstValueByKeyLink('marc:title-statement:format-formula',$link))->textValue();
 					if (! PUtil::strContains($format, '${b}')){
 						echo("<p>" . html_data_view($subtitle) . "</p>\n");
 					}
 				}
		}
	}


	if ($idata->hasKey('marc:edition:statement')){
		$es = array();
		$es[] = htmlspecialchars($idata->getPatronTextValue('marc:edition:statement'));
		$es[] = htmlspecialchars($idata->getPatronTextValue('marc:edition:remainder'));
		$txt = PUtil::concatSeperator('; ', $es);
		printf('<div><span style="font-weight:bold;">edition: </span>  %s</div>',$txt);
	}

	if ($idata->hasKey('ea:publication:statement')){
		$vals =$idata->getItemValues('ea:publication:statement');
		foreach ($vals as $v) {
			echo('<div class="p_publication_statement">');
			echo('<span style="font-weight:bold;">publication: </span> ');
			//echo("<pre>");print_r($v);
			$lnk = $v->recordId();
			//echo("# $lnk #");
			$sep ='';
			$vals1 =$idata->getValuesByKeyLink('ea:publication:place', $lnk);
			foreach ($vals1 as $v) {
				echo($sep);
				printf('<a href="/archive/search?m=a&p=%s" class="authlink">%s <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <span class="sr-only">' . tr('Search') .'</span></a> ',urlencode($v[0]),htmlspecialchars($v[0]));
				$sep = '&#160;&#160; | &#160;&#160; ';
			}
			$vals2 =$idata->getValuesByKeyLink('ea:date:orgissued', $lnk);
			foreach ($vals2 as $v) {
				echo($sep);
				// if (!empty($rep['year'])) {
			 // printf('<a href="/archive/search?m=a&y=%s" class="authlink">',$rep['year']);
			 // echo $rep['year'];
			 // printf(' <img src="/_assets/img/find.png" alt="%s" /></a><br/>',tr('Αναζήτηση'));
		// }
				printf('<a href="/archive/search?m=a&y=%s" class="authlink">%s <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <span class="sr-only">' . tr('Search') .'</span></a> ',urlencode($v[0]),htmlspecialchars($v[0]));
				//printf(' <span class="sp_year">%s</span>',htmlspecialchars($v[0]));
				$sep = '&#160;&#160; | &#160;&#160; ';
			}

			$vals3 =$idata->getValuesByKeyLink('dc:publisher:', $lnk);
			foreach ($vals3 as $v) {
				echo($sep);
				printf(' <span class="sp_publisher">%s</span>',htmlspecialchars($v[0]));
				$sep = '; ';
			}


			$vals4 =$idata->getValuesByKeyLink('ea:publication:printing-place', $lnk);
			$vals5 =$idata->getValuesByKeyLink('ea:publication:printer-name', $lnk);
			if (count($vals4) > 0 or count($vals5) > 0 ){
				echo(" (");
					$sep = '';
					foreach ($vals4 as $v) {
						echo($sep);
						printf(' <span class="sp_prn_place">%s</span>',htmlspecialchars($v[0]));
						$sep = '; ';
					}
					foreach ($vals5 as $v) {
						echo($sep);
						printf(' <span class="sp_prn_name">%s</span>',htmlspecialchars($v[0]));
						$sep = '; ';
					}
				echo(") ");
			}


			echo("</div>");

		}
	//	printf('<p>%s</p>',$idata->getPatronTextValue('ea:publication:statement'));
	}

?>



<div class="clear">&#160;</div>
<div class="basicinf">
<?php
	$contributors = Lookup::getRelationElementsWithParentItemLabelMap('dc:contributor:');

	unset($contributors['dc:publisher:']);
	unset($contributors['ea:publication:printer-name']);

	foreach ($contributors as $key => $label) {
		PSnipets::item_property_line($idata, $key, $label);
	}
?>

<div class="clear">&#160;</div>
<?php
		$key = 'ea:material:product';
		$label = $relations[$key];
		PSnipets::item_property_line($idata, $key, $label);
?>

<div class="clear">&#160;</div>
<?php
		$key = 'ea:material:ingredient';
		$label = $relations[$key];
		PSnipets::item_property_line($idata, $key, $label);
?>

<div class="clear">&#160;</div>
<?php
		$key = 'ea:situation:';
		$label = $relations[$key];
		PSnipets::item_property_line($idata, $key, $label);
?>



<div class="clear">&#160;</div>
<?php
		$key = 'ea:process:';
		$label = $relations[$key];
		PSnipets::item_property_line($idata, $key, $label);
?>

<div class="clear">&#160;</div>
<?php
		$key = 'ea:instrument:';
		$label = $relations[$key];
		PSnipets::item_property_line($idata, $key, $label);
?>

<div class="clear">&#160;</div>
<?php
		$key = 'ea:symbol:';
		$label = $relations[$key];
		PSnipets::item_property_line($idata, $key, $label);
?>




	<div class="clear">&#160;</div>

		<?php
		// if (!empty($rep['place'])) {
			 // echo("<a href=\"/archive/search?m=a&p=" . $rep['place'] ."\" class=\"authlink\">");
			 // echo $rep['place'];
			 // echo(" <img src=\"/_assets/img/find.png\" alt=\"Αναζήτηση\" /> &#160;&#160; | &#160;&#160;</a>");
		// }
		?>

		<?php
		// if (!empty($rep['publisher'])){
				// echo(" " . $rep['publisher'] . " &#160;&#160; | &#160;&#160; ");
		// }
		?>
		<?php
		// if (!empty($rep['year'])) {
			 // printf('<a href="/archive/search?m=a&y=%s" class="authlink">',$rep['year']);
			 // echo $rep['year'];
			 // printf(' <img src="/_assets/img/find.png" alt="%s" /></a><br/>',tr('Αναζήτηση'));
		// }
		?>




		<?php if (!empty($rep['date_captured'])) {
			echo ($rep['date_captured']->format('d/m/Y'));
			echo("<br/>");
		}?>
		<?php if (!empty($rep['website_url'])) {
			$url = $rep['website_url'];
			$label = substr($url,0,90);
			printf('<a href="%s">%s</a><br/>',$url,html_data_view($label));
		}?>

		<?php
		if (!empty($rep['pages'])){
			printf('<p><i>%s: %s</i><br/>',tr('Σελίδες'),html_data_view($rep['pages']));
		}


		if (! empty($rep['title_uniform'])){
			$ut_label = $person ? 'alt name: &nbsp; ' : 'uniform title: &nbsp; ';
			$arr = $rep['title_uniform'];
			foreach ($arr as $k => $v){
				echo($ut_label . $v . "<br/>\n");
			}
		}


		if (! empty($rep['isbn'])){
			echo("isbn: " .$rep['isbn'] . "<br/>\n");
		}
		if (! empty($rep['issn'])){
			echo("issn: " .$rep['issn'] . "<br/>\n");
		}

		if (!empty($rep['url_related'])){
			foreach($rep['url_related'] as $val){
				$val1 = null; $val2=null;
				$arr = explode('|',$val,2);
				if (count($arr) >= 1){
					$val1 = $arr[0];
				}
				if (count($arr) >= 2){
					$val2 = $arr[1];
				}
				if (! empty($val1) && ! empty($val2) ){
					printf('<i>%s: </i> <a class="assetlink" href="%s">%s</a> <br/> ',tr('σχετικό url'),$val1,html_data_view($val2));
				}
			}
		}
		if (!empty($rep['url_origin'])){
			$val1 = null;$val2=null;
			$arr = explode('|',$rep['url_origin'],2);
			if (count($arr) >= 1){
				$val1 = $arr[0];
			}
			if (count($arr) >= 2){
				$val2 = $arr[1];
			}
			if (! empty($val1) && ! empty($val2) ){
				printf('<i>πηγή: </i> <a class="assetlink" href="%s">%s</a> <br/> ',$val1,html_data_view($val2));
			}
		}

		if (! empty($rep['issue_of'])){
			$word1="τεύχος";
			if ($type == 'web-site-instance'){
				$word1=tr("Στυγμιότυπο");
			}
			$word2 = null;
			if ($type == 'periodiko-tefxos') {
				$word2 = tr("του περιοδικού");
			} else if ($type == 'efimerida-tefxos') {
				$word2 = tr("της εφημερίδας");
			} else {
				$word2 = tr("του");
			}
			$val1 = "/archive/item/" . $rep['issue_of'] . "?lang=" . $lang;
			$val2 = $rep['issue_of_label'];
			printf('<i>%s %s: </i> <a class="assetlink" href="%s">%s</a> <br/> ',$word1, $word2,$val1,$val2);
		}

		if (! empty($rep['item_of'])){
			$word1=tr("μέλος της συλλογής");
			foreach ($rep['item_of'] as $it){
				$val1 = "/archive/item/" . $it[0]  . "?lang=" . $lang;
				$val2 = $it[1];
				printf('<i>%s: </i> <a class="assetlink" href="%s">%s</a> <br/> ',$word1,$val1,$val2);
			}
		}


		echo("</p>");
		?>
		<?php

		$notes = $idata->getArrayValues('ea:note:generic');
		//print_r($notes);
		foreach ($notes as $v){
			$note = ItemValue::c($v);
			$id = $note->recordId();
			$tf = $idata->getFirstItemValue('ea:text-format:',$id);
			$tf = empty($tf) ? 'text' : $tf->textValue();
			if ($tf == 'html'){
				printf('<p>%s</p>', $note->textValue());
			} else  {
				printf('<p>%s</p>', str_replace("\n",'<br/>',htmlspecialchars($note->textValue())));
			}
		}
//NOTES

// 		if (!empty($rep['desc_desc'])){
// 			printf('<p><i>%s: </i> %s</p>',tr('Περιγραφή'),$rep['desc_desc']);
// 		}
// 		if (!empty($rep['desc_abstract'])){
// 			printf("<p><i>%s: </i> %s</p>",tr('Σύντομη Περιγραφή'), $rep['desc_abstract'] );
// 		}


		if ($idata->hasKey('isis:book:record') && user_access_admin() ){
			 printf('<pre>%s</pre>', htmlspecialchars($idata->getPatronTextValue('isis:book:record')));
		}

		// if ($idata->hasKey('dc:subject:') ){
			// $pvs = $idata->getPatronTextValues('dc:subject:');
			// //print_r($pvs);
		 // }
		// echo("<pre>");
		// print_r($idata->values);
		// echo("</pre>");

		?>


		<div class="clear">&nbsp;</div>

		<?php
		$keywords = $rep['keywords'];

		if (!empty($keywords)){
			printf("<i>%s: </i> ",tr('Ετικέτες'));

			$tmp = count($keywords) -1;
			foreach ($keywords as $k => $v){
				$url = sprintf("/archive/term?t=%s&lang=%s", urlencode($v) , $lang);
				printf('<a href="%s">%s</a>',$url,$v);
				if ($k < $tmp){
					echo(", ");
				}
			}
		}


		if ($website){
			echo("\n<br/><br/><br/><br/>\n");
		}


		?>


		<?php

		if ($person){
			$obj_type_names = PUtil::get_object_type_names();

			$members = $rep['persons_items_author'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'author of');
			}

			$members = $rep['persons_items_contributor'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'contributor of');
			}

			$members = $rep['related_items'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'related');
			}

		}

		?>


		<?php

		if ($work){
				$obj_type_names = PUtil::get_object_type_names();
				$members = $rep['works_items_manifestation'];
				if (count($members) > 0 ){
					list_of_items($d, $members, $i, $lang,$obj_type_names,tr('manifestations'));
				}

		}
		?>


		<?php

		if ($place){
			$obj_type_names = PUtil::get_object_type_names();

			$members = $rep['place_items'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'items');
			}


		}

		?>



	<?php

		if ($instrument){
			$obj_type_names = PUtil::get_object_type_names();

			$members = $rep['items_with_instrument'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'recipes');
			}


		}

		?>
<?php

			$obj_type_names = PUtil::get_object_type_names();

			$members = $rep['items_with_situation'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'recipes');
			}
			$members = $rep['items_with_product'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'recipes (product)');
			}
			$members = $rep['items_with_ingredient'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'recipes (ingredient)');
			}
			$members = $rep['items_with_symbol'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'recipes');
			}
			$members = $rep['items_with_process'];
			if (count($members) > 0 ){
				list_of_items($d, $members, $i, $lang,$obj_type_names,'recipes');
			}


		?>


	<div class="clear">&nbsp;</div>

<?php
	if ($has_page_thumbs_s &&  $has_page_thumbs_b  && count($thumbs_s)  > 1) {   #&&  (! empty($rep['pages']))
		if ( isset($thumbs_s['l']) && isset($thumbs_b['l'])  ) {
			$msg = tr("Ενδεικτικές Πρώτες και τελευταία σελίδα");
		} else {
			$msg = tr("Ενδεικτικές σελίδες");
		}
?>
	<h3 class="pthumbstitle"><?=$msg ?></h3>

	<?php
	$pages = $rep['pages'];
	PSnipets::item_pages_preview($pages, $thumbs_s, $thumbs_b);
	?>

		<p class="mynote"><?=tr('Κάντε κλίκ στις μικρογραφίες για μεγένθυση. Τα μεγέθη των φωτογραφιών είναι ενδεικτικά για προεπισκόπηση.')?></p>



<?php } else if ($periodical)
{
		if ($type == 'web-site'){
		 	$label1 = tr("Στιγμιότυπα:");
		 	$label2 = "";
		} else {
		 	$label1 = tr("Τεύχη:");
		 	$label2 = tr("τευχος:");
		 }

		echo("<h3>" . $label1 . "</h3>\n");
		echo('<div id="pagesthumbs" class="thumbsl2">');

		if (isset($rep['e3ofila'])){
	 		$e3ofila = $rep['e3ofila'];
	 		if (!empty($e3ofila)){
				foreach ($e3ofila as $t){
					$item_id = $t[0];
					$thumb = $t[1];
					$title1 = $label2 . "&#160;" . $t[2];
					printf('<a rel="gal" href="/archive/item/%s?lang=%s" title="%s" ><img src="/media/%s" alt="%s" class="img-responsive" /> <span>%s</span> </a>', $item_id,$lang, $title1, $thumb, $title1, $title1);
				}
	 		}
		}
		echo('</div>');
}
?>


<?php

if ( ! $periodical) {
	$bitstreams = $rep['bitstreams'];
	$articles = $rep['articles'];
	if (false && $bibref){
		echo("<br/>");
		echo('<div class="downinf">');
		//echo('<p>');
		//printf("<p>%s<br/>%s</p> ",tr('η εγγραφή αποτελεί βιβλιογραφική αναφορά'),tr('δεν υπάρχει διαθέσιμο τεκμήριο για download'));
		echo(tr('η εγγραφή αποτελεί βιβλιογραφική αναφορά'));
		echo('<br/>');
		if (count($bitstreams ) == 0) {
			echo(tr('δεν υπάρχει διαθέσιμο τεκμήριο για download'));
		} else {

			echo(tr('τα τεκμήρια είναι ενδείκτικα.'));
		}
		//echo('</p>');
		echo('</div>');

	}

		#	[name] => foul-search-text.txt
		#	[bytes] => 627
		#	[bitstream_id] => 3414343542965997412923004397307187076111
		#	[bundle_name] => ALT
		#	[checksum] => a15004694b36d53c1227af5c0cc52c2b
		#	[checksum_algorithm] => MD5
		#	[description] =>

		/*
		if (count($bitstreams ) == 1){
			if (isset($rep['fname'])){
					echo('<div class="downinf">');
					$bitstream = $rep['bitstream'];
					$fname = $rep['fname'];
					$fbytes = $rep['fbytes'];
					$fsize = round($fbytes/1000000,2);
					$chksum = $rep['fchecksum'];
					$chksum_alg = $rep['fchecksum_algorithm'];
					echo "\n";
					$url = "/archive/download?i=".urlencode($rep['id']) . "&d=" . urlencode($bitstream);
					printf('<a href="%s"><img src="/_assets/img/down.png" alt="Download" /></a>',$url);

					$msg ="";
					if ($type  == 'web-site-instance'){
						$url = $url . "&m=dt";
						$msg = "view screenshot: &nbsp; ";
					}
					printf('<br />%s <a href="%s">%s</a> (%sMb)',$msg, $url,$fname, $fsize);
					echo "\n";
					echo('</div>');
			}
		} else
		*/

		$options = array();
		if (!empty($rep['primary_content'])){
			array_push($articles, $rep['primary_content']);
			#PSnipets::bitstream_downlads($item_id, ARRAY(), ARRAY($rep['primary_content']), $options);
		}
		PSnipets::bitstream_downlads($item_id, $bitstreams, $articles, $options);

		$artifacts = $rep['artifacts'];
		if (count($artifacts ) > 0) {
				PSnipets::artifacts($item_id, $artifacts);
		}
 }


 ?>

    </div>

    </div> <!-- End item-left -->

    <?php
    if ( ! $periodical && ! $silogi && ! $work) {

    	$thumb_s = null;
    	$thumb_b = null;
    	if ($has_thumbs_s && isset($thumbs_s[0])){
    		$thumb_s = $thumbs_s[0];
    	}
    	if ($has_thumbs_b && isset($thumbs_b[0])){
    		$thumb_b = $thumbs_b[0];
    	}

    //	if (($obj_type != 'book' && $obj_type != 'place') || !empty($thumb_b)){
    	if (($obj_type != 'book' && $obj_type != 'place') || !empty($thumb_b)){

    		$title = $rep['title'];
            echo('<div class="col-sm-3 item-right">');
    		  echo('<div class="itemthumb">');
    		if (empty($thumb_s)){
    			printf('<img src="/_assets/img/na.png" alt="%s" />',$title);
    		} else{
    			if ($has_page_thumbs_b){
    				printf('<a class="group colorbox-load" rel="gal" href="/media/%s" title="%s" ><img src="/media/%s" alt="%s" /></a>', $thumb_b, $title, $thumb_s, $title);
    			} else {
    				printf('<img src="/media/%s" alt="%s" />',  $thumb_s, $title);
    			}
    		}
    		echo('</div>');
            echo('</div> <!-- End item-right -->');

    	}

    }
    ?>


</div> <!-- End row - #iteminf -->

<?php
//######### NOTES #################
		$notes = $rep['notes'];
		//echo("<pre>");
		//print_r($notes);
	//	echo("</pre>");

		if (count($notes) > 0){
			echo ('<h3 id="hnotes">notes</h3>');
			echo ('<div id="notes">');
			foreach($notes as $k => $v){
				echo('<div class="note" class="note">');
				echo($v['content']);
				echo('</div>');
			}
			echo('</div>');
		}

?>




<div class="clearb">&nbsp;</div>


<?php

 if ($silogi) {


 	if (isset($rep['members'])){

		$obj_type_names = PUtil::get_object_type_names($dbh);

		$members = $rep['members'];
		$obj_type_names = PUtil::get_object_type_names();
		list_of_items($d, $members, $i, $lang,$obj_type_names,null,$list_edit_flag);
		if ($list_edit_flag){
			if ($silogi){
				printf('<input type="hidden" name="src_folder" value="%s"/>',$i);
			}else{
				printf('<input type="hidden" name="src_folder"/>');
			}
			#$SQL=sprintf("SELECT item_id,label from dsd.item2 where obj_type='silogi' AND item_id <> %s",$i);
			#dbToSelect($dbh, $SQL, "folder", null);

			printf('<button type="button" name="action" value="clear_flder" onclick="clear_folder()">cls</button>');
			printf('<input id="id_folder_name" type="text" name="folder_name" value="%s" size="26"/>',null);
			#printf('<input id="id_folder" type="hidden" name="folder" /> ');
			printf('<input id="id_folder"  type="text" name="folder" value="%s" size="1" />',null);

			echo("&nbsp;&nbsp;&nbsp;&nbsp;");
			printf('<button type="button" name="action" value="move" onclick="move_to_folder()">move</button>');
			echo("&nbsp;&nbsp;&nbsp;&nbsp;");
			printf('<button type="button" name="action" value="copy" onclick="copy_to_folder()">copy</button>');
		}
	}
}

if (! $person){
	$obj_type_names = PUtil::get_object_type_names($dbh);
	$members = $rep['related_items'];
	if (count($members) > 0 ){
		list_of_items($d, $members, $i, $lang,$obj_type_names,'related');
	}

	$obj_type_names = PUtil::get_object_type_names($dbh);
	$members = $rep['works'];
	if (count($members) > 0 ){
		list_of_items($d, $members, $i, $lang,$obj_type_names,'works');
	}


}

?>


<?php
//$repo_maintainer_flag = user_access(PERMISSION_REPO_MENTAINER);
//if ($repo_maintainer_flag){
	PSnipets::item_admin_bar($rep);
//}

?>


@stop