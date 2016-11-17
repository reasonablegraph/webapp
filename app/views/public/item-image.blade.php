@section('content')

<?php
if (Config::get('arc.LOAD_JS')){
# laravel jquery
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-colorbox/jquery.colorbox.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-colorbox/example5/colorbox.css');
?>

<script>jQuery(document).ready(function(){jQuery(".colorbox-load").colorbox({rel:'nofollow', photo:'true'});});</script>

<?php
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'css/item-image.css');

$item_id = $_REQUEST['item_id'];
$item = $_REQUEST['item'];
$mimetype = $_REQUEST['mimetype'];
$dbh = dbconnect();

drupal_set_title('image');

//  echo("<pre>");
//  print_r($_REQUEST);
//  echo("</pre>");

$parent_item_id = null;
if (isset($_REQUEST['parent_item_id'])){
	$parent_item_id = $_REQUEST['parent_item_id'];
}
$bitstream =  $item['primary_bitstream'];

// echo("<pre>");
// print_r($bitstream);
// echo("</pre>");

$internal_id = $bitstream['internal_id'];
$bitstream_file = PUtil::bitream2filename($internal_id);
$image_info = PUtil::identify_image($bitstream_file);
//print_r($image_info);

//Array ( [FORMAT] => JPEG [WIDTH] => 3744 [HEIGHT] => 5616 [RESOLUTION] => 240x240 [PRINT_SIZE] => 15.6x23.4 [UNITS] => PixelsPerInch [TYPE] => TrueColor [DATETIME_DIGITIZED] => 2012:11:17 12:50:25 )

$artifact_id = $bitstream['artifact_id'];
$furl = $bitstream['furl'];
$p = $furl;
if (empty($p)){
	$ext = PUtil::image_extension_from_mimetype($mimetype);
	$p = $artifact_id . '.' . $ext;
}
//return;
$download_fname = $bitstream['download_fname'];
if (empty($download_fname)){
	$download_fname = $p;
}
$url_direct = sprintf('/archive/item/%s/%s',$item_id,$p);
$url_download = sprintf('/archive/item/%s/download/%s',$item_id,$p);


if (isset($item['thumbs_big'][0])){
	$img1 = $item['thumbs_big'][0];
} else {

}

$tags = $item['keywords'];




//<img src="/media/<=$img1>"/>

// echo '<pre>';print_r($item);echo '</pre>';

?>



<div class="itemfoto">
<div align="center" id="image41">
<a class="colorbox-load" href="/archive/media/<?=$item_id?>/max?photo=true" title="<?=$item['primary_bitstream']['name'] ?>"><img src="/archive/media/<?=$item_id?>/big"   alt="<?=$item['primary_bitstream']['name'] ?>" ></a>
</div>
<div id="imagedet">
<?php


if (!empty($bitstream['description'])){
	printf('<p class="ilabel">description:</p>');
	printf('<p class="d1">%s</p>',$bitstream['description']);
}

if (!empty($bitstream['info'])){
	printf('<p class="ilabel">info:</p>');
	printf('<p  class="d1">%s</p>',$bitstream['info']);
}

if (!empty($bitstream['src_url'])){
	printf('<span class="ilabel">src url:</span>');
	printf('<span  class="d2"><a href="%s">%s</a></span>',$bitstream['src_url'],$bitstream['src_url']);
	echo("<br/>");
}

$w = $image_info['WIDTH'];
$h = $image_info['HEIGHT'];
$format = $image_info['FORMAT'];
$type = $image_info['TYPE'];
$resolution = $image_info['RESOLUTION'];
$print_size=  $image_info['PRINT_SIZE'];
$units =  $image_info['UNITS'];
$datetime_digitized = $image_info['DATETIME_DIGITIZED'];
// unset($image_info['WIDTH']);
// unset($image_info['HEIGHT']);

printf('<div class="ilabel ilabel1">format:</div>');
printf('<span  class="d2">%s</span>',$format);
echo("<br/>");

printf('<div class="ilabel ilabel1">type:</div>');
printf('<span  class="d2">%s</span>',$type);
echo("<br/>");

printf('<div class="ilabel ilabel1">size (pixels):</div>');
printf('<span class="ilabel2 ilabel">width:</span>');
printf('<span  class="d2">%s</span>',$w);
echo(" ");
printf('<span class="ilabel">height:</span>');
printf('<span  class="d2">%s</span>',$h);
echo("<br/>");

// foreach ($image_info as $k => $v){
// 	//$kk = strtolower($k);
// 	if (! empty($v)){
// 		printf('<span class="ilabel">%s:</span>',$k);
// 		printf('<span  class="d2">%s</span>',$v);
// 		echo(" ");
// 	}
// }
// echo("<br/>");

printf('<div class="ilabel ilabel1">size:</div>');
$size =PUtil::formatSizeBytes($bitstream['size_bytes']);
printf('<span  class="d2">%s</span>',$size);
echo("<br/>");

printf('<div class="ilabel ilabel1">resolution:</div>');
printf('<span  class="d2">%s (%s)</span>',$resolution,$units);
echo("<br/>");

printf('<div class="ilabel ilabel1">print size:</div>');
printf('<span  class="d2">%s</span>',$print_size);
echo("<br/>");

if ( !empty($datetime_digitized)){

	printf('<div class="ilabel ilabel1">digitized dt:</div>');
	printf('<span  class="d2">%s</span>',$datetime_digitized);
	echo("<br/>");


}

printf('<div class="ilabel ilabel1">download:</div>');
$size =PUtil::formatSizeBytes($bitstream['size_bytes']);
printf('<span  class="d2"><a href="%s">%s</a></span>',$url_download,$download_fname);
echo("<br/>");

if (!empty($parent_item_id)){
	$label = PDao::get_item_label($parent_item_id);
	printf('<div class="ilabel ilabel1">ανήκει στο: </div>');
	printf('<span  class="d2"><a href="/archive/items/%s">%s</a></span>',$parent_item_id,$label);
}
?>
</div>
<br/>



<?php if (!empty($tags)): ?>
<div class="field field-name-field-tags field-type-taxonomy-term-reference field-label-inline clearfix">
<h3 class="field-label">Tags: </h3>
<ul class="links inline">

<?php
 foreach ($tags as $k => $v){
	printf('<li class="taxonomy-term-reference-%s" rel="dc:subject">',$k);
	printf('<a href="/tags/%s" typeof="skos:Concept" property="rdfs:label skos:prefLabel">%s</a></li>',urlencode($v),htmlspecialchars($v));
}
?>
</ul>
</div>
<?php endif;?>



</div>
<?php
//$repo_maintainer_flag = user_access(Config::get('arc.PERMISSION_REPO_MENTAINER'));
$repo_maintainer_flag = ArcApp::has_permission(Permissions::$REPO_MAINTAINER );
if ($repo_maintainer_flag){
	PSnipets::item_admin_bar($item);
}


?>
@stop