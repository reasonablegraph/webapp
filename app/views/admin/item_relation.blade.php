@section('content')

<?php auth_check_mentainer(); ?>
<?php

if (Config::get('arc.LOAD_JS')){
# laravel jquery
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/jquery-ui.min.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/themes/smoothness/jquery-ui.min.css');

//@DocGroup(module="relations-items", group="php", comment="item_relation_create")

drupal_add_library('system', 'ui.autocomplete');
//drupal_add_js(ARCHIVE_ASSETS_PATH . 'js/jquery.query-2.1.7.js');



$dbh = dbconnect();

$s=get_post_get('t');
$delete_item_id=get_get('dt');
$rel_type = get_get('rt');


$v = get_get("v",0);
$show_search =  $v == 1 ? false : true;

$s1g = get_get("t1");
$s2g = get_get("t2");

$s1org = get_post("t1org");
$s2org = get_post("t2org");


$s1 = get_post('t1');
$s2 = get_post('t2');


#echo("<pre>");
#echo("s     : $s\n");
#echo("v     : $v\n");
#echo("s1g   : $s1g\n");
#echo("s2g   : $s2g\n");
#echo("s1org : $s1org\n");
#echo("s2org : $s2org\n");
#echo("s1    : $s1\n");
#echo("s2    : $s2\n");
#echo("</pre>");

if (! empty($delete_item_id)){

	$SQL = "DELETE FROM dsd.item_relation WHERE id = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $delete_item_id);
	$stmt->execute();

	$URL="/prepo/items/relation?t=$s&t1=$s1g&t2=$s2g";
	drupal_add_http_header('Location', $URL); 
}



#drupal_add_library('system', 'ui.autocomplete');
#drupal_add_css('http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css');
#drupal_add_js("/_assets/js/item_relation.js",'external');





$rel_type = get_post('rt',$rel_type);


if (! empty($s1) && ! empty($s2) && ! empty ($rel_type)){
	$ok = true;

	$errors = PDao::insert_relation_items($s1,$s2,$rel_type);
	foreach ($errors as $k=>$v){
		echo("<h2>$v</h2>");
	}

}


?>

<?php

$obj_type = null;
if (! empty($s)){
	$SQL="SELECT label, obj_type FROM  dsd.item2 where item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $s);
	$stmt->execute();
	if ($r = $stmt->fetch()){
		$obj_type = $r[1];
	}
}
if (! empty ($obj_type)){
	if ($obj_type == 'person'){
		drupal_add_js("/_assets/js/person_relation.js",'external');
	} else {
		drupal_add_js("/_assets/js/item_relation.js",'external');
	}
	printf('<p>%s: [<a href="/archive/item/%s">public view</a>] &nbsp; [<a href="/prepo/edit_step2?i=%s">admin view</a>] <p>',$r[0],$s,$s);
} else {
	drupal_add_js("/_assets/js/item_relation.js",'external');
}

?>

<?php if ($show_search): ?>
<div>
<?php else: ?>
<div style="display:none">
<?php endif;?>

<h2>Search</h2>

<form method="get">
		<div class="ui-widget">
			<label for="terms">item id: </label>
			<input id="terms" class="sterm" type="text" name="t" value="<?=$s?>" />
		</div>

		<div class="clear">&nbsp;</div>
		<div class="subm">
			<input type="submit" value="search"/>
			<input type="reset" value="clear" />
		</div>

</form>
</div>
<hr/>


<?php

$s1def = PUtil::coalesce($s1g, $s1org);
$s2def = PUtil::coalesce($s2g, $s2org);

$s1h = PUtil::coalesce($s1, $s1g);
$s2h = PUtil::coalesce($s2, $s2g);

if ($s1def != $s1h){
	$s1def = null;
}

if ($s2def != $s2h){
	$s2def = null;
}

$s1def_label = null;
if (! empty($s1def)){
	$SQL="SELECT label, obj_type FROM  dsd.item2 where item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $s1def);
	$stmt->execute();
	if ($r = $stmt->fetch()){
		$s1def_label = $r[0];
	}
}


?>
<h2>New Entry</h2>

<form method="POST" action="/prepo/items/relation">
		<input type="hidden" name="t" value="<?=$s?>"/>
		<input type="hidden" name="t1org" value="<?=$s1h?>"/>
		<input type="hidden" name="t2org" value="<?=$s2h?>"/>
			<b>item 1</b> :
			<input id="term1_name" type="text" name="t1_name"  value="<?=$s1def_label?>" size="70"/>
			 id:
			<input id="term1"  type="text" name="t1" value="<?=$s1def?>" size="4" />
			<br/>
			<b>item 2</b> :
			<input id="term2_name" type="text" name="t2_name" size="70"/>
			id:
			<input id="term2" type="text" name="t2" value="<?=$s2def?>" size="4" />

		<!--<div><a href="/prepo/items/relations_management">*Ακολουθήστε αυτό το σύνδεσμο για δημιουργία νέου είδους σχέσης</a></div> -->
		<div class="ui-widget">
		<label for="rt">Relation type: </label>
			<select id="rel_select" name="rt">
			<?php
				$flag_selected=false;
				//$sql = "SELECT id, info, directed FROM dsd.item_relation_type";
				if (empty($obj_type)){
					$SQL  = 'SELECT distinct rel_type,label from dsd.item_relation_type_def order by 1';
					$stmt = $dbh->prepare($SQL);
				}else {
					$SQL  = 'SELECT distinct rel_type,label from dsd.item_relation_type_def WHERE obj_type_1 = ? order by 1';
					$stmt = $dbh->prepare($SQL);
					$stmt->bindParam(1, $obj_type);
				}
				$stmt->execute();
				while ($row = $stmt->fetch()){
					//$id = $row['rel_type'];
					$id = $row['label'];
					$description = $row['label'];
					//if ($row['directed'] == true) {
					//	$dir = ' (κατευθυντική)';
					//} else {
					//	$dir = ' (αμφίδρομη)';
					//}
					//$description = $description .  $dir;
					$selected = "";
					if ($rel_type == $id){
						$selected = ' selected="selected" ';
						$flag_selected = true;
					}
					?>
					<option value="<?=$id ?>" <?=$selected ?>><?=$description ?></option>
					<?php
				}
				$attr_selected = $flag_selected ? '' : 'selected="selected"';
				//$attr_selected = 'selected="selected"';
				printf('<option value="auto" %s >automatic selection</option>',$attr_selected);
				?>
			</select>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="subm">
			<input type="submit" value="CREATE"/>
			<input type="reset" value="Clear" />
		</div>

</form>

<hr/>

<?php



$SQL  = " SELECT r.id, ";
$SQL .= "  i1.title || ' (' || i1.obj_type || ') ',";
$SQL .= "  i2.title || ' (' || i2.obj_type || ') ',";
$SQL .= " r.create_dt::date as create_d, r.create_dt, r.rel_type, rt.label as rel_type_label, ";
$SQL .= " r.item_1 as item1, r.item_2 as item2 ";
$SQL .= " FROM dsd.item_relation r ";
$SQL .= " JOIN dsd.item2 i1 ON (i1.item_id = r.item_1) ";
$SQL .= " JOIN dsd.item2 i2 ON (i2.item_id = r.item_2) ";
$SQL .= " JOIN dsd.item_relation_type rt ON (r.rel_type = rt.id) ";

if (! empty($s)) {
	$SQL .= " WHERE r.item_1 = ? OR r.item_2 = ? ";
	$SQL .= " ORDER BY r.create_dt desc limit 40";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $s);
	$stmt->bindParam(2, $s);
	$stmt->execute();
	$res = $stmt->fetchAll();
} else {
	$SQL .= " ORDER BY r.create_dt desc limit 40";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	$res = $stmt->fetchAll();
}



?>

<table class="table table-striped table-bordered table-hover">

<?php
$return_item_ok = urlencode($s);
foreach($res as $key => $row){
	echo("<tr>\n");
	printf('<td>%s &nbsp;&nbsp; <a href="/prepo/edit_step2?i=%s">%s</a></td>',$row[7], $row[7], $row[1]);
	printf("<td>%s</td>",$row[6]);
	printf('<td>%s &nbsp;&nbsp; <a href="/prepo/edit_step2?i=%s">%s</a></td>',$row[8], $row[8], $row[2]);
	printf("<td>%s</td>",$row[3]);

	$s1d = PUtil::coalesce($s1, $s1g);
	$s2d = PUtil::coalesce($s2, $s2g);

	printf('<td><a href="/prepo/items/relation?dt=%s&t=%s&t1=%s&t2=%s"  onclick="return confirm(\'Are you sure you want to delete?\')"><span class="glyphicon glyphicon-remove"></span></a>',$row[0],$return_item_ok,$s1d,$s2d);
	echo("<tr>\n");

}


?>
</table>

@stop