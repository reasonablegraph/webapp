@section('content')
<?php auth_check_mentainer(); ?>
<?php

$item_id  = get_get("i",null);

if (empty($item_id)){
	return;
}

	$item_label = PDao::item_get_label($item_id);
	printf('<a href="/prepo/edit_step2?i=%s">[back to item: %s]</a>',$item_id,$item_label);


	PSnipets::contents_table($item_id);

?>

<br/>
<hr/>
<form method="post">
<input type="hidden" name="item_id" value="<?=$item_id?>"/>
<b>add content:</b> &nbsp; description: <input type="text" name="description"/>
<input type="submit" name="ADD_article" value="ADD_article"/>
</form>
@stop
