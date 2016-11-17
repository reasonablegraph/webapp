@section('content')
<?php auth_check_mentainer(); ?>

<?php
		drupal_set_title("edit metadata..");
?>
<?php

$item_id = get_get('itid');
if (empty($item_id)){
	echo("<h2>expected item_id</h2>");
	return;
}


$flag_insert = true;
$id  = get_get('id');
if (!empty($id)){
	$flag_insert = false;
}
$flag_update = ! $flag_insert;



//$fl = get_get('fl',0);
//$flag_redirect_from_insert = ($fl == 1);

$dbh = dbconnect();




function dump_data($dbh, $id){
	$SQL = "SELECT * FROM  dsd.metadatavalue2 where metadata_value_id=?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $id);
	$stmt->execute();

	if ($dump = $stmt->fetch(PDO::FETCH_ASSOC)){
		echo("<pre>");
		print_r($dump);
		echo("</pre>");
	}

}


$item_label =  PDao::item_get_label($item_id);



#if ($flag_redirect_from_insert){
#	dump_data($dbh,$id);
#}

if ($flag_insert){
	$row = array();
	$disabled = null;
	$mvid = null;
	$ref_item = null;
	$ref_item_label = null;
	$row['element'] = null;
	$row['text_value'] = null;
	$row['text_lang'] = null;

} else {

// 	metadata_value_id | 104
// 	item_id           | 107
// 	metadata_field_id | 802
// 	text_value        | P5
// 	text_value_search | P5
// 	text_lang         | ␀
// 	dt_create         | 2015-11-10 13:16:53.335032
// 	text_value_fst    | 'p5':1
// 	weight            | 12
// 	mvalue            | ␀
// 	so                | f
// 	ref_item          | 106
// 	relation          | ␀
// 	element           | ea:manif:Publication_Place
// 	data              | {"data":{"sub-root":true,"new_type":"new_0","ref_label":"P5"}}
// 	lid               | 70
// 	link              | 55
// 	obj_type          | auth-manifestation
// 	obj_class         | auth-manifestation
// 	deps              | ␀
// 	inferred          | f
// 	level             | 2


	$SQL = "SELECT  m.metadata_value_id, m.item_id, m.text_value, m.text_lang, m.ref_item, m.element, m.dt_create, m.weight, m.data, m.lid, m.link, m.inferred, m.level, i.label as item_label
		FROM  dsd.metadatavalue2 m
		LEFT JOIN dsd.item2 i ON (i.item_id = m.item_id)
		where m.metadata_value_id= ?
		";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $id);
	$stmt->execute();

	if (!$row = $stmt->fetch()){
		echo("metadata NOT FOUND $id");
		return;
	}

	$mvid = $row['metadata_value_id'];
	$ref_item = $row['ref_item'];
	$ref_item_label = null;
	if (! empty($ref_item)){
		$ref_item_label =  PDao::item_get_label($ref_item);
	}

	if (!empty($row['data'])){
		$dataArray = json_decode($row['data'],true);
	} else {
		$dataArray = null;
	}

	$disabled = 'disabled="disabled"';

}


if (Config::get('arc.LOAD_JS')){
# laravel jquery
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/jquery-ui.min.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/themes/smoothness/jquery-ui.min.css');


drupal_add_library('system', 'ui.autocomplete');
drupal_add_js("/_assets/js/edit_metadata.js",'external');

if(isset($_REQUEST['message'])){echo $_REQUEST['message'];}

?>


<form method = "post">

<input type="hidden" name="mvid" value="<?php echo($mvid)?>" />
<table class="table">

<?php
$item_record_label = 'item';
if ($item_id <> $row['item_id']){
	$item_record_label = 'record item';
?>
<tr>
<td>item:</td>
<td>
<?php
	printf('<a href="/prepo/edit_step2?i=%s">%s</a>',$item_id,$item_label);
	?>
</td>
</tr>
<?php
}
?>

<tr>
<td><?=$item_record_label?>:</td>
<td>
<?php
printf('<a href="/prepo/edit_step2?i=%s">%s</a>',$row['item_id'],$row['item_label']);
?>
</td>

</tr>

<tr>
<td>metadata id:</td>
<td><?php echo($mvid); ?></td>
</tr>
<tr>
<td>element:</td>
<td><input type="text" name="element" size="80" value="<?=htmlspecialchars($row['element']); ?>"   <?php echo($disabled)?>  ></td>
</tr>

<tr>
<td>text value:</td>
<td>
<?php

// echo(json_encode($dataArray,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))


	$srlzn = 'text';
	if (!empty($dataArray) && !empty($dataArray['data']) && !empty($dataArray['data']['srlzn'])){
		$srlzn = $dataArray['data']['srlzn'];
	}
	if ($srlzn == 'json'){
		$tv = htmlspecialchars(json_encode(json_decode($row['text_value']),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
	}else {
		$tv = htmlspecialchars($row['text_value']);
	}
	printf('<pre>%s</pre>',$tv);
	//if (empty($tv) || strlen($tv) < 80){
	//	printf('<input type="text" name="text_value" size="80" value="%s">',$tv);
	//} else {
		//printf('<textarea name="text_value" style="width:700px">%s</textarea>',$tv);
	//}

?>
</td>
</tr>
<?php if (! empty($ref_item_label)):?>
<tr>
<td>item ref:</td>
<td>
<?php printf('<a href="/archive/item/%s">%s</a>',$ref_item,$ref_item_label);?>
</td>
</tr>
<?php endif;?>

<tr>
<td>data:</td>
<td>
 <pre>
<?php echo(json_encode($dataArray,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) ?>
</pre>
</td>
</tr>

<tr>
<td>lang:</td>
<td><?=htmlspecialchars($row['text_lang'])?></td>
</tr>

<?php
//metadata_value_id, item_id, text_value, text_lang,ref_item, element,dt_create, weight, data,lid,link,inferred,level
?>
<tr>
<td>level:</td>
<td>
<?php echo($row['level']) ?>
</td>
</tr>

<tr>
<td>lid:</td>
<td>
<?php echo($row['lid']) ?>
</td>
</tr>


<?php if (!empty($row['link'])):?>
<tr>
<td>parent lid:</td>
<td>
<?php echo($row['link']) ?>
</td>
</tr>
<?php endif?>


<?php if (!empty($row['weight'])):?>
<tr>
<td>weight:</td>
<td>
<?php echo($row['weight']) ?>
</td>
</tr>
<?php endif?>

<?php if (!empty($row['relation'])):?>
<tr>
<td>relation:</td>
<td>
<?php echo($row['relation']) ?>
</td>
</tr>
<?php endif?>

<tr>
<td>inferred:</td>
<td>
<?php
	if($row['inferred']){
		echo "TRUE";
	} else {
		echo "FALSE";
	}
?>
</td>
</tr>

<tr>
<td>dt_create:</td>
<td>
<?php echo($row['dt_create']) ?>
</td>
</tr>

</table>
<!--   input type="submit" value="save"/-->
</form>



<?php
if ($flag_update):
//dump_data($dbh, $id);
?>

	<form method="post">
	<input type="hidden" name="delete" value="delete"/>
	<input type="hidden" name="mvid" value="<?php echo($mvid)?>" />
	<input type="submit" value="delete" onClick="return confirm('ARE YOU SURE ?')">
	</form>


<?php endif;  ?>

@stop

