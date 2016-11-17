@section('content')
<?php

if (Config::get('arc.LOAD_JS')){
	# laravel jquery
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-colorbox/jquery.colorbox.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-colorbox/example5/colorbox.css');
?>

<script>
jQuery(document).ready(function(){
	jQuery(".colorbox-load").colorbox({rel:'nofollow'});
	jQuery(".group").colorbox({rel:'gal' });
});
</script>

<?php

drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'css/patron-cmds.css');

//print_r($_REQUEST);
$item_basics = $_REQUEST['item_basics'];
$item_id = $item_basics['item_id'];
$obj_type = $item_basics['obj_type'];
//$obj_class = $item_basics['obj_class'];
$item_metadata = PDAO::get_item_metadata($item_id);
$thumbs = PDAO::getItemThumbs($item_id);
$bitstreams = PDAO::getItemBitstreams($item_id,$obj_type);
$articles = PDAO::getItemArticles($item_id);

$item_relations_from = PDAO::getItemRelationsFrom($item_id);
$item_relations_to = PDAO::getItemRelationsTo($item_id);
// Log::info('FROM:');
// Log::info($item_relations_from);
// Log::info('TO:');
// Log::info($item_relations_to);
$itemRelations = new ItemRelations($item_relations_from, $item_relations_to);

$init_options = array(
'___@idata' => $item_metadata,
'___@item_basics' => $item_basics,
'___@thumbs' => $thumbs,
'___@bitstreams' => $bitstreams,
'___@articles' => $articles,
'___@item_relations'=>$itemRelations
);




//$item_metadata->dump();

// echo('<pre>');
// print_r($thumbs);
// print_r($bitstreams);
// print_r($item_basics);
// echo('</pre>');

$dispacher = new DisplayDispatcher($obj_type, $init_options);
$dispacher->executeCommands();






// $repo_maintainer_flag = user_access(PERMISSION_REPO_MENTAINER);
// if ($repo_maintainer_flag){
// 	PSnipets::item_admin_bar($rep);
// }

?>
@stop