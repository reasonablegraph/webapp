<style>
div.marc_help table {
	border: 1px solid black;
	padding: 0px;
	margin: 0px;
}
div.marc_help table tr td {
	background-color: white;
	border: 0px;
}

div.marc_help table tr td.indicator {
 border: 1px solid black;
 font-size:1.2em;
 font-weight:bold;
}
div.marc_help table tr td.info {
	border-bottom:1px solid black;
	font-size:1em;
}

span.det {
	font-size:0.8em;
}

</style>
<!--
// // 	$style = '';
// // 	$style .= sprintf('td.indicator { border: 1px solid black; }');
// // 	$style .= "\n";
// // 	$style .= sprintf('td.indicator { font-size:1.2em; font-weight:bold; }');
// // 	$style .= "\n";
// // 	$style .= sprintf('td.info { border-bottom:1px solid black; font-size:1em;  }');
// // 	$style .= "\n";
// // 	$style .= sprintf('span.det { font-size:0.8em; }');
// // 	$style .= "\n";
// // 	$out .= sprintf('<style>%s</style>',$style);
// // 	$out .="\n";
-->
<?php
//metafer8ikan sto step1.php
// printf('<input type="hidden"  name="submit_id" value="%s" />' ."\n",$submit_id);
// printf('<input type="hidden"  name="item_id" value="%s" />' ."\n",$item_id);
// printf('<input type="hidden"  name="edoc" value="%s" />' ."\n",htmlspecialchars($edoc));
// printf('<input type="hidden"  name="stype" value="%s" />' ."\n",htmlspecialchars($stype));
// printf('<input type="hidden"  name="cd" value="%s" />' ."\n",htmlspecialchars($cd));
//
// $br = $vivliografiki_anafora ? 1 : 0;
// printf('<input type="hidden"  name="br" value="%s" />' ."\n",htmlspecialchars($br));
// $agt = $agg_type ? 1 : 0;
// printf('<input type="hidden"  name="agt" value="%s" />' ."\n",htmlspecialchars($agt));
//


echo("<h1>public dedomena</h1>");
#foreach($it as $key => $value) {
	   //var_dump($key, $value);
#############################################
	$key = "ea:obj-type:";
	$value = $idata->getValueArraySK($key);
		echo("<div>");
		if (! empty($item_id) ){
			echo(" type: " . $value[0][0]);
			echo("\n &nbsp;&nbsp;&nbsp; ");
			printf('<input type="hidden" id="dc-type" name="%s[]" value="%s" />' ."\n",$key, $value[0][0]);
		} else {

			if ( $vivliografiki_anafora){
				$SQL="SELECT name,label from dsd.obj_type WHERE can_bibref";
			} elseif ($agg_type) {
				$SQL="SELECT name,label from dsd.obj_type WHERE  agg_type";
			} else {
				$SQL="SELECT name,label from dsd.obj_type WHERE  not agg_type";
			}
			$stmt = $dbh->prepare($SQL);
			$stmt->execute();
			while ($r = $stmt->fetch()){
				$map[$r[0]] = $r[1];
			}
			if (! empty($stype) && $stype="sites"){
				$defVal = "web-site-instance";
			} else {
				$defVal = $value[0][0];
			}
			echo("type:");
			print_select($key,"dc-type",$map, $defVal,true,true);
			echo("\n");
		}

#############################################
	$key = "dc:language:iso";
	$value = $idata->getValueArraySK($key);
		$map = ARRAY(
 			'N/A' => 'N/A',
			'el' => tr('Ελληνικά'),
			'en' => 'English',
 			'it' => 'Italian',
 			'es' => 'Spanish',
	 		'other' => '(other)',
		);
		$lang = empty($value[0][0]) ? 'el' : $value[0][0];

		echo("lang:");
		 print_select($key,"lang",$map, $lang);
		 echo("</div>");

#############################################
$key = "dc:identifier:issn";
$value = $idata->getStaffTextValueArraySK($key);
		echo('<div id="b_dc_issn">');
		print_input_text("issn","dc_issn", $key,$value[0],60);
		echo('</div>');

#############################################
$key = "dc:identifier:isbn";
$options =array(
	'label'=>'isbn',
	'div_id'=>'b_dc_isbn',
	'show_help'=>false,
	'add_button'=>true,
);
FormSnipets::displayField($key, $idata, $options);


#############################################
$key = "dc:title:";
	$options =array(
			'label'=>'τιτλος',
			'append_group'=>false,
			'div_id'=>'dc_title',
			'show_help'=>false,
			'add_button'=>false,
			'autocomplete_url'=>'/archive/search-title'
	);
	FormSnipets::displayField($key, $idata, $options);


#############################################
$key = "ea:subtitle:";
$value = $idata->getStaffTextValueArraySK($key);
	#echo("<br/>");
			print_input_text("υποτιτλος", "ea_subtitle" ,$key,$value[0],80);

#############################################

$key = "ea:title:uniform";
$options =array(
	'label'=>'title:uniform',
	'append_group'=>false,
	'div_id'=>'titles_uniform',
	'show_help'=>true,
	'add_button'=>true,
	//'add_button_id'=>'add_title_uniform',
	'add_button_label'=>'add_title'
);
FormSnipets::displayField($key, $idata, $options);

echo('<div id="issue">');

#############################################
$key = "ea:issue:no";
	$value = $idata->getStaffTextValueArraySK($key);
	print_input_text("τευχος: &nbsp; <i>αριθμος</i>","ea_issue-no", $key,$value[0],2);

#############################################
$key = "ea:issue:label";
	$value = $idata->getStaffTextValueArraySK($key);
	print_input_text("<i>label</i>","ea_issue-label", $key,$value[0],6);

echo("</div>");


#############################################
$key = "ea:website:url";
$value = $idata->getValueArraySK($key);
		$ok = empty($site_url) ? $value[0][0] : $site_url;
		print_input_text("site url&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;","ea_website_url", $key,$ok,70);
		#echo("<br/>");

#############################################
$key = "ea:website:url-base";
$value = $idata->getValueArraySK($key);
		$ok = empty($site_url_base) ? $value[0][0] : $site_url_base;
		print_input_text("site url-base","ea_website_url_base", $key,$ok,70);
		#echo("<br/>");

#############################################
$key = "ea:date:captured";
$value = $idata->getValueArraySK($key);
		$ok = empty($site_date_captured) ? $value[0][0] : $site_date_captured;
		print_input_text("date captured","ea_date_captured", $key,$ok,40);



#@DocGroup(module="actor", group="php", comment="form step1")
echo('<div id="authors">');
#############################################
$author_type_map = ARRAY(
		 			'dc:contributor:author[]' => 'Συγραφεας',
					'ea:contributor:responsible[]' => 'Υπευθυνος εκδοσης',
					'ea:contributor:editor[]' => 'Επιμελιτης',
					'dc:contributor:editor[]' => 'Συντάκτης',
					'ea:contributor:translator[]' => 'Μεταφραστης',
					'dc:contributor:ilustrator[]' => 'Ilustrator',
					'dc:contributor:advisor[]' => 'Advisor',
					'dc:contributor:other[]' => 'Contributor:other',
);


		$key = "dc:contributor:author";
		$options =array(
				'label'=>'contributor',
				'div_id'=>'dc_contributor',
				'show_help'=>false,
				'add_button'=>true,
				'select_key_map'=>$author_type_map,
				'autocomplete_url'=>'/archive/search-authorsurname',
				'width'=>64
		);
		FormSnipets::displayField($key, $idata, $options);


		$key = "ea:contributor:responsible";
		$options =array(
				'label'=>'contributor',
				'show_help'=>false,
				'add_button'=>false,
				'select_key_map'=>$author_type_map,
				'autocomplete_url'=>'/archive/search-authorsurname',
				'skip_on_empty' => true,
				'width'=>64
		);
		FormSnipets::displayField($key, $idata, $options);

		$key = "dc:contributor:editor";
		FormSnipets::displayField($key, $idata, $options);

		$key = "ea:contributor:editor";
		FormSnipets::displayField($key, $idata, $options);

		$key= "ea:contributor:translator";
		FormSnipets::displayField($key, $idata, $options);

		$key= "dc:contributor:illustrator";
		FormSnipets::displayField($key, $idata, $options);

		$key= "dc:contributor:advisor";
		FormSnipets::displayField($key, $idata, $options);

		$key= "dc:contributor:other";
		FormSnipets::displayField($key, $idata, $options);


echo("</div>\n");


#############################################
$key = "dc:publisher:";
$options =array(
		'label'=>'εκδοτης',
		'div_id'=>'b_dc_publicer',
		'show_help'=>false,
		'add_button'=>false,
		'autocomplete_url'=>'/archive/search-publisher'
);
FormSnipets::displayField($key, $idata, $options);


#############################################
$key = "ea:publication:place";
$options =array(
		'label'=>'publication place',
		'div_id'=>'ea_publication_place',
		'show_help'=>false,
		'add_button'=>false,
		'autocomplete_url'=>'/archive/search-place'
);
FormSnipets::displayField($key, $idata, $options);


#############################################
$key = "ea:date:orgissued";
		$value = $idata->getValueArraySK($key);
		$y = null;
		$m = null;
		$d = null;
		$subject = $value[0][0];
		if (preg_match('/(\d+)-(\d+)-(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = $matches[2];
			$d = $matches[3];
		}
		else if (preg_match('/(\d+)-(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = $matches[2];
			$d = null;
		}
		else if (preg_match('/(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = null;
			$d = null;
		}

		echo('<div id="date_org_issued">');
		echo(tr("ημ/νια εκδοσης &nbsp;"));
		printf('%s: <input type="text" name="orgissued_y" value="%s"  size="4" /> %s: ',tr('ετος'),htmlspecialchars($y),tr('μηνας'));
		printf('<input type="text" name="orgissued_m" value="%s"  size="2" /> %s: ',htmlspecialchars($m),tr('μερα'));
		printf('<input type="text" name="orgissued_d" value="%s" size="2" />',htmlspecialchars($d));
		echo("</div>\n");

#############################################
$key = "ea:date:start";
echo ('<div id="dates_start_end">');
		$value = $idata->getValueArraySK($key);
		$y = null;
		$m = null;
		$d = null;
		$subject = $value[0][0];
		if (preg_match('/(\d+)-(\d+)-(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = $matches[2];
			$d = $matches[3];
		}
		else if (preg_match('/(\d+)-(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = $matches[2];
			$d = null;
		}
		else if (preg_match('/(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = null;
			$d = null;
		}

		echo('<span class="input_label">' . tr('ημ/νια πρωτης εκδοσης &nbsp;'). '</span>');
		printf('%s: <input type="text" name="date_start_y" value="%s"  size="4" /> %s: ',tr('ετος'), htmlspecialchars($y),tr('μηνας'));
		printf('<input type="text" name="date_start_m" value="%s"  size="2" /> %s: ',htmlspecialchars($m),tr('μερα'));
		printf('<input type="text" name="date_start_d" value="%s" size="2" />',htmlspecialchars($d));
		printf("<br/>\n");

#############################################

$key = "ea:date:end";
	$value = $idata->getValueArraySK($key);
		$y = null;
		$m = null;
		$d = null;
		$subject = $value[0][0];
		if (preg_match('/(\d+)-(\d+)-(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = $matches[2];
			$d = $matches[3];
		}
		else if (preg_match('/(\d+)-(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = $matches[2];
			$d = null;
		}
		else if (preg_match('/(\d+)/', $subject, $matches)){
			$y = $matches[1];
			$m = null;
			$d = null;
		}

		echo('<span class="input_label">' . tr('ημ/νια τελευτ&nbsp; εκδοσης &nbsp;') . ' </span>');
		printf('%s: <input type="text" name="date_end_y" value="%s"  size="4" /> %s: ',tr('ετος'),htmlspecialchars($y),tr('μηνας'));
		printf('<input type="text" name="date_end_m" value="%s"  size="2" /> %s: ',htmlspecialchars($m),tr('μερα'));
		printf('<input type="text" name="date_end_d" value="%s" size="2" />',htmlspecialchars($d));
echo("</div>");


#############################################
$key = "ea:size:";
$value = $idata->getValueArraySK($key);
$msg = "(" . tr('πλάτος&times;ύψος') . ")";
print_input_text("διαστασεις","ea_size", $key,$value[0][0],50,$msg);

$key = DataFields::ea_size_pages;
$value = $idata->getValueArraySK($key);
print_input_text("αριθμος σελιδων","ea_pages_cardinality", $key,$value[0][0],10);
#############################################

$key = "dc:subject:";
//$value = $idata->getValueArraySK($key);
		echo("<hr/>");

		$options =array(
				'div_id'=>'dcsubjects',
				'label'=>'pub&nbsp;keywords',
				'show_help'=>false,
				'add_button'=>true,
				'add_button_label'=>'add keyword',
				'add_button_first'=>true,
				'autocomplete_url'=>'/archive/search-terms',
				'autocomplete_fn'=>'subjects_autocomplete',
				//'size'=>32,
				'size'=>44,
				//'size'=>70,
				'skip_on_empty'=>true,
				'print_label'=>false,
				'input_br' =>false
		);
		FormSnipets::displayField($key, $idata, $options);


// 		echo('pub_keywords: <button id="add_dc_subject" type="button">add_keyword</button>');
// 		echo('<div id="dcsubjects">');
// 		if (!empty($value)){
// 			foreach($value as $k=>$v){
// 				printf(' <input type="text" name="dc:subject:[]" value="%s"/>',htmlspecialchars($v[0]));

// 			}
// 		}
// 		echo("</div>\n");

#############################################

echo('<div id="ext-comments">');
$key = "dc:description:abstract";
$value = $idata->getValueArraySK($key);
		echo("<hr/>");
		echo('<table><tr><td>abstract</td><td>description</td><tr>');
		echo("<tr><td>");
		printf('<textarea name="%s[]">',$key);
		echo(isset($key,$value[0]) ? htmlspecialchars($value[0][0]): null);
		echo("</textarea>");
		echo("</td>\n");

#############################################
$key = "dc:description:";
$value = $idata->getValueArraySK($key);
		printf('<td><textarea name="%s[]">',$key);
		echo(isset($key,$value[0]) ? htmlspecialchars($value[0][0]): null);
		echo("</textarea>");
		echo("</td></tr></table>");
echo("</div>");

#############################################
$key = "ea:url:related";
$value = $idata->getValueArraySK($key);

		$val1 = null; $val2 = null;
		if (!empty($value)){
			$val=$value[0][0];
			$arr = explode('|',$val,2);
			if (count($arr) >= 1){
				$val1 = $arr[0];
			}
			if (count($arr) >= 2){
				$val2 = $arr[1];
			}
		}
		echo('<div id="urls1">');
		print_input_url("url related", $key,$val1,$val2);
		echo('<button id="add_url1" type="button">add</button><br/>');

		if (!empty($value)){
			$c = 0;
			foreach($value as $k=>$v){
				$c ++;
				if ($c > 1){
					$val1 = null; $val2 = null;
					$val=$v[0];
					$arr = explode('|',$val,2);
					if (count($arr) >= 1){
						$val1 = $arr[0];
					}
					if (count($arr) >= 2){
						$val2 = $arr[1];
					}
					print_input_url("url related", $key,$val1,$val2);
					echo("<br/>\n");
				}
			}
		}
		echo("</div>");

#############################################
echo ('<div id="url-origin">');
$key = "ea:url:origin";
$value = $idata->getValueArraySK($key);
		$val1 = null; $val2 = null;
		if (!empty($value)){
			$val=$value[0][0];
			$arr = explode('|',$val,2);
			if (count($arr) >= 1){
				$val1 = $arr[0];
			}
			if (count($arr) >= 2){
				$val2 = $arr[1];
			}
		}
		print_input_url("url πηγης &nbsp;", $key,$val1,$val2);
echo ('</div>');



#############################################
echo("<h1>esoterika dedomena</h1>");
echo("<hr/>");


#############################################
$key = "ea:subject:";
$value = $idata->getValueArraySK($key);
		echo('priv_keywords: <button id="add_ea_subject" type="button">add_keyword</button>');

		echo('<div id="easubjects">');
		if (!empty($value)){
			foreach($value as $k=>$v){
				printf('  <input type="text" name="ea:subject:[]" value="%s"/>',htmlspecialchars($v[0]));
			}
		}
		echo("</div>\n");
		echo("<hr/>");

#############################################
$key = "ea:collection:name";
$value = $idata->getValueArraySK($key);
	print_input_text("collection name", "ea_collection_name", $key,$value[0][0]);


	#############################################
$key = "ea:collection:place";
$value = $idata->getValueArraySK($key);
print_input_text("collection place", "ea_collection_place", $key,$value[0][0]);



echo('<div id="source-data">');
#############################################
$key = "ea:original:print";
	$value = $idata->getValueArraySK($key);
		$map = ARRAY(
 		'unknown' => 'Unknown',
 		'tipografio' => 'Τυπογραφειο',
 	  'fototipia' => 'Photocopy',
		'poligrafos' => 'Πολύγραφος',
 		'digital-document' => 'Digital Document',
		'other' => '(other)',
		);
		echo(" org print: ");
		 print_select($key,"ea-org-print",$map, $value[0][0],true,true);
		 //echo("<br/>\n");
#############################################
$key = "ea:source:";
$value = $idata->getValueArraySK($key);
			$map = ARRAY(
 			'unknown' => 'Unknown',
 			'original' => 'Original',
 		    'antigrafo' => 'Copy',
 			'digital-document' => 'Digital Document',
		 	'other' => '(other)',
			);
		echo(" source: ");
		 print_select($key,"ea-source",$map, $value[0][0]);
#############################################
$key = "ea:source:print";
$value = $idata->getValueArraySK($key);
			$map = ARRAY(
 			'unknown' => 'Unknown',
 			'tipografio' => 'Τυπογραφειο',
 		    'fototipia' => 'Photocopy',
 			'poligrafos' => 'Πολύγραφος',
 			'digital-document' => 'Digital Document',
		 	'other' => '(other)',
			);
		echo(" source print: ");
	 	print_select($key,"ea-source-print",$map, $value[0][0],true,true);
echo("</div>");
#############################################



#############################################


echo('<div id="int-comments">');
$key = "ea:origin:comment";
$value = $idata->getValueArraySK($key);

		echo('<table><tr><td colspan="2">comments</td><tr><tr><td>origin</td><td>status</td><tr>');
		echo("<tr><td>");
		printf('<textarea name="%s[]">',$key);
		echo(isset($key,$value[0]) ? htmlspecialchars($value[0][0]): null);
		echo("</textarea>");
		echo("</td>");

#############################################


$key = "ea:status:comment";
$value = $idata->getValueArraySK($key);

		printf('<td><textarea name="%s[]">',$key);
		echo(isset($key,$value[0]) ? htmlspecialchars($value[0][0]): null);
		echo("</textarea>");
		echo("</td></tr></table>\n");

echo("</div>");
#############################################


$key = "ea:status:";
$value = $idata->getValueArraySK($key);

	if (user_access_item_submiter()){
		$map = ARRAY(
		ITEM_STATUS_PENDING => ITEM_STATUS_PENDING
		);
	} else {
		$map = ARRAY(
		ITEM_STATUS_PENDING => ITEM_STATUS_PENDING,
 		ITEM_STATUS_INCOMPLETE => ITEM_STATUS_INCOMPLETE,
		ITEM_STATUS_ERROR => ITEM_STATUS_ERROR,
		ITEM_STATUS_INTERNAL => ITEM_STATUS_INTERNAL,
		ITEM_STATUS_PRIVATE => ITEM_STATUS_PRIVATE,
		ITEM_STATUS_HIDDEN => ITEM_STATUS_HIDDEN,
		ITEM_STATUS_FINISH => ITEM_STATUS_FINISH
		);
	}


		print_select($key,"ea-org-print",$map, $value[0][0]);
		echo("<br/>");





#	else {
	#		echo("<br/>");
	#		$label = "??? #" . $key . "#";
	#		print_input_text($label, $key ,$key,$value[0][0]);
#	}




#}


?>
