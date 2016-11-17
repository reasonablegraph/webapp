@section('content')
<?php

drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'css/item.css');

$item_id = $_REQUEST['item_id'];
$item = $_REQUEST['item'];
$mimetype = $_REQUEST['mimetype'];
$dbh = dbconnect();
$bitstream =  $item['primary_bitstream'];

$title = $bitstream['name'];


drupal_set_title($title);



$img1 = $item['thumbs_big'][0];
$tags = $item['keywords'];


$pages = $item['pages'];
$thumbs_s = $item['thumbs_small'];
$thumbs_b = $item['thumbs_big'];
$has_thumbs_s = (!empty($thumbs_s));
$has_thumbs_b = (!empty($thumbs_b));

$has_page_thumbs_s = ($has_thumbs_s && count($thumbs_s) > 0);
$has_page_thumbs_b = ($has_thumbs_b && count($thumbs_b) > 0);

PSnipets::item_pages_preview($pages, $thumbs_s, $thumbs_b);


$options = array();
$bitstreams = array($bitstream);
PSnipets::bitstream_downlads($item_id, $bitstreams, null, $options);


?>


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





<?php
//$repo_maintainer_flag = user_access(Config::get('arc.PERMISSION_REPO_MENTAINER'));
$repo_maintainer_flag = ArcApp::has_permission(Permissions::$REPO_MAINTAINER );
if ($repo_maintainer_flag){
	PSnipets::item_admin_bar($item);
}
?>






<?php
//$options = array();
//$bitstreams = array($bitstream);
//PSnipets::bitstream_downlads($item_id, $bitstreams, null, $options);


?>

<?php
//print_r($item);
//print_r($bitstream);
?>

@stop
