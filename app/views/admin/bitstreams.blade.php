@section('content')
<?php auth_check_mentainer(); ?>
<?php

if (Config::get('arc.LOAD_JS')){
# laravel jquery
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

$item_id  = get_get("i");

if (empty($item_id)){
	echo("expected item");
	return;
}


if (! empty($_POST)){
	//echo("<pre>");
	//print_r($_POST);
// 	echo("\n============================\n");
// 	print_r($_FILES);
//	echo("</pre>");
		PUtil::upload_bitstream_from_post_data($item_id);
}

$item_label = PDao::item_get_label($item_id);
printf('<h4><a href="/prepo/edit_step2?i=%s">[%s]</a></h4>',$item_id,$item_label);

PDao::bitstreams_table($item_id);

?>

<?php
 $action= sprintf('?i=%s',$item_id);
 $display_bundle = true;
 $display_seq_id = true;
 $bundle="ORIGINAL";
 //include 'bitstream_upload_form.php';
 ?>
 @include('admin.bitstream_upload_form')

<hr/>
@stop
