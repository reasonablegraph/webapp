@section('content')
<?php auth_check_mentainer(); ?>
<?php
drupal_set_title("artifacts");

$item_id  = (PUtil::extract_int(get_get("i")));
if (empty($item_id)){
	echo("expected item");
	return;
}



$label = PDao::getItemLabel($item_id);
if (empty($label)){
	echo("?");
	return;
}


?>

<h2>parent item: <a href="/archive/item/<?=$item_id?>">[<?=$label?>]</a></h2>
<br/><br/>
<?php
//prepo/edit_artifact?i=$item_id
?>
<a href="/prepo/edit_step1?aft=1&afti=<?=$item_id?>">[add new artifact]</a>

<?php


PSnipets::artifacts_table($item_id);

# select sn_pref, max(sn_suff) from dsd.artifacts where sn_suff is not null group by 1;
?>
@stop 