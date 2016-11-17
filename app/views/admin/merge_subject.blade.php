@section('content')
<?php auth_check_mentainer(); ?>
<?php 

if (Config::get('arc.LOAD_JS')){
	# laravel jquery
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/jquery-ui.min.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/themes/smoothness/jquery-ui.min.css');

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH') . 'js/merge_subjects.js');


$out = empty($_REQUEST['out']) ? null : $_REQUEST['out'];

$out .= "<h2>" . tr('Ετικέτες') . "</h2>";
$out .= '<form method="post">';
$out .= ' s1: <input id="s1" type="text" name="s1" size="40" />';
$out .= ' s2: <input id="s2" type="text" name="s2" size="40" />';
$out .= '<br/><input type="submit" name="merge" value="merge" onClick="return confirm(\'Are you sure?\')"/>';
$out .= '</form>';
$out .= '<p>' . tr('η s1 θα ενσωματωθει στην s2') . '</p>';

echo $out;

?>

@stop
