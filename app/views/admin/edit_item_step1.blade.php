@section('content')
<?php auth_check_mentainer(); ?>

<?php

if (Config::get('arc.LOAD_JS')) {
	// laravel jquery
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

$js_version = 1; //clear-cache

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'vendor/jquery.blockUI.js');
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'vendor/tinymce/tinymce.js');
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'vendor/jquery-ui/jquery-ui.min.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'vendor/jquery-ui/themes/smoothness/jquery-ui.min.css');
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'vendor/mustache/mustache.js');
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'vendor/jquery-deferred-sequence/jquery.deferred.sequence.js');

// unset($_SESSION['printed_warning_messages']);
// unset($_SESSION['info_messages']);
// unset($_SESSION['warn_messages']);

// drupal_add_library('system', 'ui.autocomplete');
// drupal_add_css("http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css",'external');
// drupal_add_css("/_assets/vendor/jquery-ui/themes/base/minified/jquery-ui.min.css",'external');
// drupal_add_css('http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css');
drupal_add_css("/_assets/vendor/select2/select2.css", 'external');
drupal_add_library('system', 'ui');
drupal_add_library('system', 'ui.accordion');
drupal_add_library('system', 'ui.dialog');
drupal_add_library('system', 'ui.autocomplete');

drupal_add_css("/_assets/css/step1.css", 'external');
drupal_add_js("/_assets/vendor/node-uuid/uuid.js", 'external');
drupal_add_js("/_assets/vendor/select2/select2.min.js", 'external');
drupal_add_js("/_assets/js/isbn.js", 'external');
drupal_add_js("/_assets/js/isbn-groups.js", 'external');
drupal_add_js("/_assets/js/putil.js", 'external');

// drupal_add_js("/sites/all/libraries/tinymce/jscripts/tiny_mce/tiny_mce.js",'external');

// drupal_add_js("/_assets/js/edit_item_step1n.js",'external');
// drupal_add_js("/_assets/js/edit_item_step1_conf.js",'external');
// drupal_add_js("/_assets/js/edit_item_step1_form.js",'external');

drupal_add_js('/_assets/js/form/form_controler_gen.js?v='.$js_version, 'external');
// drupal_add_js('/_assets/js/form/step1_conf.js','external');
drupal_add_js('/_assets/js/form/step1_conf_gen.js?v='.$js_version, 'external');
drupal_add_js('/_assets/js/form/commands_gen.js?v='.$js_version, 'external');
drupal_add_js('/_assets/js/form/step1_form_gen.js?v='.$js_version, 'external');
// commands.js
// form_controler.js
// step1_conf.js
// step1_form.js

?>
<div class="arch-wrap">
	<div id="result"></div>
<?php

$idata = $_REQUEST['idata'];
$item_id = $_REQUEST['item_id'];
$submit_id = $_REQUEST['submit_id'];
$edoc = $_REQUEST['edoc'];
$cd = $_REQUEST['cd'];
$item_collection = $_REQUEST['item_collection'];
$wfdata = $_REQUEST['wfdata'];

// Log::info("@@@@@###: " . print_r($wfdata));

$artifact_ref_item_id = $_REQUEST['artifact_ref_item_id'];

$stype = $_REQUEST['stype'];
$site_date_captured = $_REQUEST['site_date_captured'];
$site_url = $_REQUEST['site_url'];
$site_url_base = $_REQUEST['site_url_base'];

$vivliografiki_anafora = $wfdata['vivliografiki_anafora'];
$agg_type = $_REQUEST['agg_type'];
$artifact_type = $_REQUEST['artifact_type'];

$thumb = $_REQUEST['thumb'];

//@DOC: RELATIONS  change Infernece at load step1
$idata = PUtil::changeRelation($idata,1);


?>

<div id="jsmessages" class="fmessages"></div>
<?php
// #####################################################################################
// #####################################################################################
// ###### FORM
// #####################################################################################
// #####################################################################################
?>
<?php

if (! empty($submit_id)) {
	printf('<form id="forms1"  method="post" action="/prepo/edit_step1?s=%s">', $submit_id);
} else {
	printf('<form id="forms1"  method="post" action="/prepo/edit_step1">');
}
?>
<?php

if (! empty($cd)) {
	echo ("<br/>");
	print_input_text("collection id", "item_collection", "item_collection", $item_collection, 8);
	echo ("<br/>");
}
?>
<!-- <input type="submit" name="save" value="save"/> -->
	<!-- <input  type="submit" name="next" value="next"/> -->
	<!-- <input type="submit" style="float:right" name="save_finalize" value="save_finalize"/> -->
	<button type="submit" name="save" value="save"><?php echo tr('save'); ?></button>
	<button type="submit" name="next" value="next" style="margin-left: 20px"><?php echo tr('next');  ?></button>
	<button type="submit" name="save_finalize" value="save_finalize" style="float: right"><?php echo tr('save_finalize'); ?></button>


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<?php
$print_copy_cataloging = variable_get('arc_copy_cataloging', 0);
if ($print_copy_cataloging) :
	?>
<!--
<span style="margin-left:200px">
<button id="biblionet1" type="button" class="inline_button">biblionet</button>
</span>
 -->
	<span style="margin-left: 200px">
		<button id="z3950" type="button" class="inline_button">z3950</button>
	</span>

<?php endif;?>

<br />
<?php

printf('<input type="hidden"  name="submit_id" value="%s" />' . "\n", $submit_id);
printf('<input type="hidden"  name="item_id" value="%s" />' . "\n", $item_id);
printf('<input type="hidden"  name="edoc" value="%s" />' . "\n", htmlspecialchars($edoc));
printf('<input type="hidden"  name="stype" value="%s" />' . "\n", htmlspecialchars($stype));
printf('<input type="hidden"  name="cd" value="%s" />' . "\n", htmlspecialchars($cd));
$br = $vivliografiki_anafora ? 1 : 0;
printf('<input type="hidden"  name="br" value="%s" />' . "\n", htmlspecialchars($br));
$agt = $agg_type ? 1 : 0;
printf('<input type="hidden"  name="agt" value="%s" />' . "\n", htmlspecialchars($agt));
// $aft = $artifact_type ? 1 : 0;
// printf('<input type="hidden" name="aft" value="%s" />' ."\n",htmlspecialchars($aft));
if (! empty($artifact_ref_item_id)) {
	printf('<input type="hidden"  name="afti" value="%s" />' . "\n", $artifact_ref_item_id);
}

$dbh = dbconnect();

if (! empty($stype) && $stype = "sites") {

	$key = "ea:website:url";
	$value = $idata->getValueSK($key);
	if (empty($value)) {
		$idata->setValueSK($key, $site_url);
	}

	$key = "ea:website:url-base";
	$value = $idata->getValueSK($key);
	if (empty($value)) {
		$idata->setValueSK($key, $site_url_base);
	}

	$key = "ea:date:captured";
	$value = $idata->getValueSK($key);
	if (empty($value)) {
		$idata->setValueSK($key, $site_date_captured);
	}
}

if ($artifact_type && ! empty($artifact_ref_item_id)) {
	//$idata->setValueSK('ea:artifact-of:', $artifact_ref_item_id);
	$idata->resetSK('ea:artifact-of:');
	$idata->addValueSK('ea:artifact-of:',$artifact_ref_item_id,null,null,null,$artifact_ref_item_id);
	$artifact_uuid = $idata->getValueTextSK('ea:artifact:uuid');
	if (empty($artifact_uuid)) {
		$artifact_uuid = PDao::createUUID();
		$idata->setValueSK('ea:artifact:uuid', $artifact_uuid);
	}
}
// {
// pending:'pending',
// incomplete:'incomplete',
// error:'error',
// //internal:'internal',
// private:'private',
// hidden:'hidden',
// finish:'finish'
// }

$avail_statuses = Lookup::get_item_statuses();
// $avail_statuses = array(
// 'pending'=>'pending',
// 'incomplete'=>'incomplete',
// 'error'=>'error',
// 'direct_only'=>'direct_only',
// 'private'=>'private',
// 'hidden'=>'hidden',
// 'finish'=>'finish',
// );

$ovalue = $idata->getValueTextSK("ea:obj-type:");
$oclass = PDao::get_obj_class_from_obj_type($ovalue);
if ($oclass == 'artifact' || $oclass == 'digital-item') {
	$artifact_type = true;
}
;

if ($artifact_type) {
	$avail_statuses = array('internal' => 'internal' );
	$idata->setValueSK('ea:status:', 'internal');
}

// $artifact_parent = $idata->getValueTextSK('ea:artifact-of:');
// if (! empty($artifact_parent)) {
// 	$pitem_label = PDao::getItemLabel($artifact_parent);
// 	$idata->setValueSK('tmp:parent_item_label:', $pitem_label);
// }

$jvalues = array();
$keys = $idata->getKeys();
foreach ( $keys as $key ) {
	$tmp = $idata->getClientValues($key);
	if (! empty($tmp)) {
		$jvalues[$key] = $tmp;
	}
}

// echo("</pre>");

// echo("<p>#OBJECT-TYPE: $ovalue </p>");
if (! empty($stype) && $stype = "sites") {
	$defVal = "web-site-instance";
} else {
	$defVal = $ovalue;
}
$new_record = 0;
if (empty($defVal)) {
	$new_record = 1;
	if ($agg_type) {
		$defVal = 'silogi';
	} elseif ($artifact_type) {
		$defVal = variable_get('arc_artifact_object_type', 'artifact');
	} else {
		$defVal = variable_get('arc_default_new_item', 'digital-item');
	}

	$user = ArcApp::user();
	$org_id = $user['org_id'];
	$jvalues['ea:item:sublocation'] =array(array('v'=>$org_id));

	if (!empty($edoc)){
		$edoc_fname  = substr(strrchr($edoc, '/'), 1);
		$jvalues['dc:title:'] =array(array('v'=>$edoc_fname));
		$digital_item_type=  PUtil::digital_item_type($edoc_fname);
		if (!empty($digital_item_type)){
			$jvalues['ea:item:type'] =array(array('v'=>$digital_item_type));
		}
	}
// 	$accepted_ext = array('pdf','daisy','epub','docx','wma','mp3','zip','7z');
// 	if (!empty($edoc)){
// 		$edoc_fname  = substr(strrchr($edoc, '/'), 1);
// 		$jvalues['dc:title:'] =array(array('v'=>$edoc_fname));
// 		$ext = substr(strrchr($edoc, '.'), 1);
// 		if (!empty($ext)){
// 			$ext = strtolower($ext);
// 			if (in_array($ext,$accepted_ext)){
// 				$jvalues['ea:item:type'] =array(array('v'=>$ext));
// 			}
// 		}
// 	}


}
//Log::info("DEF-VAL:" . $defVal);

// echo("<pre>obj_type:");
// print_r($defVal);
// echo("\n===============\n");
// print_r($map);
// echo("</pre>");
// $defVal = 'auth-person';


if ($vivliografiki_anafora) {
	// $SQL="SELECT name,label from dsd.obj_type WHERE can_bibref AND can_new";
	$SQL = "select  g.obj_type,t.label from dsd.obj_type_groups g
		JOIN  dsd.obj_type t ON (t.name = g.obj_type)
		where g.group_name in
		(SELECT group_name from dsd.obj_type_groups where obj_type='$defVal' AND group_type = 'alternatives') AND can_bibref AND can_new
		ORDER BY g.w;";
} elseif ($agg_type) {
	// $SQL="SELECT name,label from dsd.obj_type WHERE agg_type AND can_new";
	$SQL = "select  g.obj_type,t.label from dsd.obj_type_groups g
		JOIN  dsd.obj_type t ON (t.name = g.obj_type)
		where g.group_name in
		(SELECT group_name from dsd.obj_type_groups where obj_type='$defVal' AND group_type = 'alternatives') AND agg_type AND can_new
		ORDER BY g.w;";
} elseif ($artifact_type) {
	// $SQL="SELECT name,label from dsd.obj_type WHERE obj_class = 'artifact'";
	$SQL = "select  g.obj_type,t.label from dsd.obj_type_groups g
		JOIN  dsd.obj_type t ON (t.name = g.obj_type)
		where g.group_name in
		(SELECT group_name from dsd.obj_type_groups where obj_type='$defVal' AND group_type = 'alternatives')
		ORDER BY g.w;";
} else {
	// ---deprecated---$SQL="SELECT name,label from dsd.obj_type WHERE not agg_type and obj_class in ('manifestation','actor','place')";
	// $SQL="SELECT name,label from dsd.obj_type WHERE not agg_type and obj_class <> 'artifact' and can_new";
	$SQL = "select  g.obj_type,t.label from dsd.obj_type_groups g
		JOIN  dsd.obj_type t ON (t.name = g.obj_type)
		where g.group_name in
		(SELECT group_name from dsd.obj_type_groups where obj_type='$defVal' AND group_type = 'alternatives') AND not agg_type AND obj_class <> 'artifact' AND can_new
		ORDER BY g.w;";
}

//Log::info('edit_item_step1.blade:' . print_r($SQL,true));
$map = array();
$stmt = $dbh->prepare($SQL);
$stmt->execute();
while ( $r = $stmt->fetch() ) {
	$map[$r[0]] = tr('object type: '.$r[1]);
}

$fobj_types = array('values' => $map,'default' => $defVal );

// Log::info("DEF_VAL:" . $defVal);
// Log::info("OBJ_TYPES:". print_r($fobj_types,true));

echo ('<hr/>');
echo ('<div id="step1f" class="jsform"></div>');
echo ('<hr/>');

echo ("\n<script>\n");
$default_form = get_get('f', null);
if ($default_form) {
	echo ("var default_form_obj_type = '$default_form';\n");
} else {
	echo ("var default_form_obj_type = null\n");
}
echo ("var js_params = {};\n");
$SQL = "select param,text_value,var from dsd.js_params;";
$stmt = $dbh->prepare($SQL);
$stmt->execute();
while ( $r = $stmt->fetch() ) {
	if ($r[2] == 'true') {
		echo ("var ");
		echo ($r[0]);
		echo (" = ");
		echo ($r[1]);
		echo (";\n");
	} else {
		printf("js_params['%s']=%s;\n", $r[0], $r[1]);
	}
}


if (!empty($item_rec)){
	// 	* (1) i  id client
	// 	* (2) v  VALUE-FOR-CLIENT   (JSON OR TEXT(simple or marc or html) )
	// 	* (3) g  lang
	// 	* (4) r  relation//DEPRACTED
	// 	* (5) f  ref item
	// 	* (6) w  weight
	// 	* (7) l  link (tree anchestor pointer)
	// 	*     p  props
	// 	*     s  server id
	// 	*     e  inferred
	//array('i'=>$i,'l'=>$l, 'v'=>$v,'w'=>$w, 'g'=>$g, 'r'=>$r, 'f'=>$f, 's'=>$s, 'p'=>$p, 'd'=>$d, 'e'=>$e );

	$jvalues['trn:label:'] = array(0=>array('v'=>$item_rec['label']));
	$jvalues['trn:jdata:'] = array(0=>array('v'=>$item_rec['jdata']));
}
//Log::info(print_r($jvalues,true));

$avs = json_encode($avail_statuses);
echo ("var avail_statuses = $avs;\n");
$jot = json_encode($fobj_types);
echo ("var object_types = $jot;\n");

if (!isset($jvalues['ea:status:']) || empty($jvalues['ea:status:'])){
	$jvalues['ea:status:'] =array(array('v'=>'finish'));
}

$jt = json_encode($jvalues);
echo ("var data = $jt;\n");

echo ("step1($new_record, object_types, data);");
echo ("\n</script>\n");

?>

<div>
		<!-- <input type="submit" name="save" value="save"/> -->
		<!-- <input style="margin-left:20px" type="submit" name="next" value="next"/> -->
		<!-- <input style="float:right" type="submit" name="save finalize" value="save_finalize"/> -->
		<button type="submit" name="save" value="save"><?php echo tr('save'); ?></button>
		<button type="submit" name="next" value="next" style="margin-left: 20px"><?php echo tr('next');  ?></button>
		<button type="submit" name="save_finalize" value="save_finalize" style="float: right"><?php echo tr('save_finalize'); ?></button>
	</div>
	<div>
<?php
printf('<input style="float:right" id="thumb1" type="text" name="thumb" value="%s" size="86"/>', htmlspecialchars($thumb));
printf('<button style="float:right" id="b_tree">tree</button>');
echo ("\n");
// printf('<input id="libgenxml" type="hidden" name="libgenxml" value="%s" size="46"/><br/>',htmlspecialchars($libgenxml));
echo ("\n");
?>
</div>


<?php echo"</form>"?>

<div id="xmldata"></div>

	<script>

function checkEnter(e){
 e = e || event;
 var txtArea = /textarea/i.test((e.target || e.srcElement).tagName);
 return txtArea || (e.keyCode || e.which || e.charCode || 0) !== 13;
}
document.querySelector('form').onkeypress = checkEnter;

</script>


</div>
<!-- Close of arch-wrap div  -->



<?php
// echo("<br/><br/><hr/>");
// echo("<pre>");
// $idata->dump();
// echo("</pre>");
?>

@stop
