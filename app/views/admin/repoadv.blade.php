@section('content')
<?php auth_check_mentainer(); ?>
<?php
		drupal_set_title("advance menu");
?>




<ul>
<li><a href="/prepo/opt_png">optimize png</a> </li>
<li><a href="/prepo/optimize_pdf">optimize pdf</a> </li>
<li><a href="/prepo/convert_png_jpg">converr_png_jpg</a> </li>
<li><a href="/prepo/chksum">bitstreams checksums</a> </li>
<li><a href="/prepo/export_items_range">export items</a> </li>
<li><a href="/prepo/import_items">import items</a> </li>
<li><a href="/prepo/touch">touch items</a> </li>
<li><a href="/prepo/bitstreams_missing_thumbs_gen">generate bitstreams thumbs</a></li>
<li><a href="/prepo/update_folder_thumbs">update folder thumbnails</a> </li>
<li><a href="/prepo/vacuum">vacuum full</a> </li>
<li><a href="/prepo/subjects_visibility_update">subjects visibility update</a></li>

<li><a href="/prepo/firefoxext_list">firefox ext install list</a> </li>




</ul>
@stop