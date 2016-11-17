@section('content')
<?php auth_check_mentainer(); ?>
<?php

if (Config::get('arc.LOAD_JS')){
# laravel jquery
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}


$submit_id = $_REQUEST['submit_id'];
$item_id = $_REQUEST['item_id'];
$idata = $_REQUEST['idata'];
$wfdata = $_REQUEST['wfdata'];
$vivliografiki_anafora =$_REQUEST['vivliografiki_anafora'];
$agg_type = $_REQUEST['agg_type'];
$item_load_flag = $_REQUEST['item_load_flag'];
$status = $_REQUEST['status'];
$cd = $_REQUEST['cd'];
$item_collection = $_REQUEST['item_collection'];
$bitstream_flag = $_REQUEST['bitstream_flag'];
$err_counter =		$_REQUEST['err_counter '];


$item_pages =        $_REQUEST['item_pages'];
$item_site =         $_REQUEST['item_site'];
$item_lang =         $_REQUEST['item_lang'];
$item_uuid =         $_REQUEST['item_uuid'];
$item_issue_aggr =   $_REQUEST['item_issue_aggr'];
$item_fts_catalogs = $_REQUEST['item_fts_catalogs'];
$item_in_folder =    $_REQUEST['item_in_folder'];
$item_folder =       $_REQUEST['item_folder'] ;
$item_in_archive =   $_REQUEST['item_in_archive'];
$item_create_dt =    $_REQUEST['item_create_dt'];
$item_update_dt =    $_REQUEST['item_update_dt'];
$item_user_create =  $_REQUEST['item_user_create'];


$dbh = dbconnect();


$idata->validate();

//$idata->populate();

if ($idata->hasErrors()){
	$err_counter += 1;
}
PSnipets::print_mesages($idata);


#########################
#error_log("##1 $finalize");
#error_log("##2 $err_counter");



if (!empty($item_id) ){
	$SQL="SELECT id, description from public.content WHERE item = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	if ($r = $stmt->fetch()){
		printf('<h4><a href="/prepo/edit_content?cid=%s">[content:  %s]</a></h4>',$r['id'],$r['description']);
	}

	$SQL="SELECT bitstream_id, name, description from public.bitstream WHERE item = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	if ($r = $stmt->fetch()){
		printf('<h4><a href="/prepo/edit_bitstream?bid=%s">[bitstream: %s %s]</a></h4>',$r['bitstream_id'],$r['name'], $r['description']);
	}

}


//lock edit form submitter
$user = ArcApp::username();
$is_admin = ArcApp::user_access_admin();
$edit_lock_owner = Config::get('arc.owner_edit_form_lock',0);
$edit_link = true;
if ( $edit_lock_owner && $item_user_create!= $user && !$is_admin){
	$edit_link = false;
}


if ($item_load_flag && ! PUtil::user_access_item_submiter()){

  ?>

	@include('includes.step2-links')

<?php
// 	echo ('<div class="row">');
// // 	echo ('<div class="col-sm-12">');

// 		echo('<ul  id="admin_area" class="adminbar nav nav-pills">');
// 		printf('<li><a href="/prepo/edit_step1?i=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> '. tr('Edit'). '</a></li>',$item_id);
// 		printf('<li><a href="/prepo/edit_step3?i=%s"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> '. tr('Admin'). '</a></li>',$item_id);

// 		$print_flag =  variable_get('arc_display_artifacts', 0);
// 		if ($print_flag):
// 		printf('<li><a href="/prepo/artifacts?i=%s"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> '. tr('Artifacts'). '</a></li>',$item_id);
// 		endif;
// 		printf('<li><a href="/prepo/thumbs?i=%s"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> '. tr('Thumbnails'). '</a></li>',$item_id);
// 		if (! $bitstream_flag){
// 			#target="_blank"
// 			printf('<li><a href="/prepo/bitstreams?i=%s"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> '. tr('Bitstreams'). '</a></li>',$item_id);
// 			$print_flag =  variable_get('arc_display_notes', 0);
// 			if ($print_flag):
// 			printf('<li><a  href="/prepo/contents?i=%s"><span class="glyphicon glyphicon-copy" aria-hidden="true"></span> '. tr('Content'). '</a></li>',$item_id);
// 			endif;
// 		}

// // 		printf('<li><a href="{{{UrlPrefixes::$cataloging}}}"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>'. tr('Cataloging').'</a></li>');
// // 		printf('<li><a  href="/prepo/edit_metadata?itid=%s"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> '. tr('New metadata'). '</a></li>',$item_id);
// 		echo('</ul> <hr />');
// // 	echo('</div>');
// 	echo('</div>'); //end ROW

}

if (! empty($cd)){


	echo('<table class="table table-striped table-bordered table-hover">');
	echo("<tr>");
	echo("<td>directory:</td>");
	echo("<td>");
	echo($cd);
	echo("</td>");
	echo("</tr>");

	echo("<tr>");
	echo("<td>collection id:</td>");
	echo("<td>");
	echo($item_collection);
	echo("</td>");
	echo("</tr>");

	echo("<tr>");
	echo("<td>collection name:</td>");
	echo("<td>");
	echo($collection_name);
	echo("</td>");
	echo("</tr>");

	echo("</table>");

}



if (!empty($item_id) ){
	$item_basic = PDAO::getItemBasic($item_id);
// 	echo('<table class="table table-striped table-bordered table-hover">');
// 	printf('<tr><td>%s</td><td>%s</td></tr>','label:',$item_basic['label']);
// 	printf('<tr><td>%s</td><td>%s</td></tr>','obj_type:',$item_basic['obj_type']);
// 	printf('<tr><td>%s</td><td>%s</td></tr>','flags:',$item_basic['flags_json']);
// 	printf('<tr><td>%s</td><td>%s</td></tr>','status:',$item_basic['status']);
// 	echo("</table>");
?>
	<div class="panel panel-primary">
	<table class="table table-bordered table-condensed">
	<thead class="a_thead"><tr><th colspan="8"><span class="a_shead">Basics</span></th></tr></thead>
		<tr>
			<td class="datd">Label</td><td colspan="7"><strong>{{$item_basic['label']}}</strong></td>
		</tr>
		<tr>
			<td class="datd">Obj type:</td><td colspan="7">{{tr("object type: ".$item_basic['obj_type'])}}</td>
		</tr>
		<tr>
			<td class="datd">Flags:</td><td colspan="7">{{$item_basic['flags_json']}}</td>
		</tr>
		<tr>
			<td class="datd">Status:</td> <td>{{$item_basic['status']}}</td>
			<td class="datd">Create:</td> <td>{{{ (new DateTime($item_basic['dt_create']))->format('d/m/Y H:i') }}}</td>
			<td class="datd">Update:</td> <td>{{{ (new DateTime($item_basic['dt_update']))->format('d/m/Y H:i') }}}</td>
				<td class="datd">UUID:  </td> <td>{{$item_uuid}}</td>
		</tr>
	</table>
	</div>
<?php
}



$functor = function($k,$v) {

	if ($k == DataFields::ea_description_abstract
			|| $k == DataFields::dc_descrption
			|| $k == DataFields::ea_status_comment
			|| $k == DataFields::ea_oring_comment
			){
		$v =  str_replace("\n",'<br/>',$v);
	}
	return $v;
};


	echo '<div class="panel panel-primary">';
	PSnipets::print_admin_item_metadata($item_id, $idata, $edit_link);
	echo '</div>';


// echo('<hr/>');
// //$it = new ItemMetadataIterator($idata->normalizeKeys(),1);
// $it = new ItemMetadataIterator($idata,1);
// PSnipets::print_item_metadata_iterator($item_id, $it,$functor);
// echo('<hr/>');

// echo("<table>");
// $c = 0;
// foreach($it as $key => $value) {
// 	$cc = 0;
// 	if (! empty($value)){
// 		foreach ($value as $k => $v){
// 			$c++; $cc++;
// 			if ($cc > 1){
// 				$okc = $cc;
// 			} else {
// 				$okc = "";
// 			}
// 			$val = $v[0];
// 			if (! empty($val)){
// 				$val = htmlspecialchars($val);
// 				$key = htmlspecialchars($key);
// 				echo("<tr>");
// 				echo("<td>$c</td><td>$okc</td><td>$key</td>");
// 				if (empty($v[4])){
// 					echo("<td>$val</td>");
// 				} else {
// 					printf('<td><a href="/archive/item/%s">%s</a>',$v[4],$val);
// 					if (! empty($v[3])){
// 						printf(" &nbsp; (rel: %s)",$v[3]);
// 					}

// 				}

// 				if($item_load_flag){
// 					echo ("<td>");
// 					echo ($v[2]);
// 					echo("</td>");
// 					printf('<td><a href="/prepo/edit_metadata?itid=%s&id=%s">[edit]</a></td>',$item_id,$v[2]);
// 				}
// 				echo("</tr>");
// 				if($key == DataFields::ea_status){
// 					$collspan= $item_load_flag ? 6 : 4;
// 					printf('<tr><td colspan="%s"> &nbsp; </td></tr>',$collspan);
// 				}
// 			}


// 		}
// 	}

// }
// echo("</table>\n");




// if ($item_load_flag){
// 	echo('<table class="table table-striped table-bordered table-hover">');
// 	echo('<tr>');
// 	printf('<td>uuid</td><td colspan="3">%s</td> <td>fts_catalogs</td><td colspan="3">%s</td>',$item_uuid,$item_fts_catalogs);
// 	echo('</tr><tr>');
// 	echo("<td>site</td><td> $item_site </td> <td>lang</td><td> $item_lang </td>   <td>pages</td><td> $item_pages </td> <td>is issue_aggr</td><td> $item_issue_aggr </td>");
// 	echo('</tr><tr>');
// 	echo("<td>is in folder </td><td> $item_in_folder  </td> <td>is folder</td><td> $item_folder </td> <td>is in_archive</td><td> $item_in_archive</td> <td>is bibref</td><td> $vivliografiki_anafora </td> ");
// 	echo('</tr><tr>');
// 	//printf('<td>is in folder</td><td colspan="3">%s</td> <td>site</td><td colspan="3">%s</td>',$item_in_folder,$item_site);
// 	//echo('</tr><tr>');
// 	printf('<td>create_dt</td><td colspan="3">%s</td> <td>update_dt</td><td colspan="3">%s</td>',$item_create_dt,$item_update_dt);
// 	echo('</tr>');
// 	echo("</table>");
// }



// ean to item kremete kato apo bitstream
// mpike stin arxi ena link
// if (!empty($item_id) ){
// 	$SQL="SELECT b.bitstream_id, b.bundle_name || ' ('|| b.bundle_id || ')',
// 	b.name, b.internal_id, b.size_bytes, b.item_id, i.label
// 	FROM dsd.item_bitstream_ext2 b
// 	LEFT JOIN dsd.item2 i ON (b.item_id = i.item_id)
// 	WHERE item = ? AND internal_id is not null";
// 	$stmt = $dbh->prepare($SQL);
// 	$stmt->bindParam(1, $item_id);
// 	$stmt->execute();
// 	$r = $stmt->fetchAll();
// 	if (count($r) > 0){
// 		echo("<table>");
// 		echo("<thead>");
// 		echo('<tr><th colspan="7">');
// 		echo('primary bitstream:');
// 		echo('</th></tr>');
// 		echo("</thead>");
// 		echo("<tbody>");

// 		foreach($r as $k => $v){
// 			echo("<tr>\n");
// 			printf('<td><a href="/prepo/edit_step2?i=%s">%s</a></td>',$v[5],$v[6]);
// 			printf('<td>%s</td>',$v[0]);
// 			printf('<td>%s</td>',$v[1]);
// 			printf('<td>%s</td>',$v[2]);
// 			printf('<td>%s</td>',$v[4]);
// 			printf('<td><a href="/archive/download?i=%s&d=%s">[view]</a></td>',$item_id,$v[3]);
// 			printf('<td><a href="/prepo/edit_bitstream?bid=%s">[edit]</a></td>',$v['bitstream_id']);

// 			echo("</tr>\n");
// 		}
// 		echo("</tbody>");
// 		echo("</table>\n");

// 	}
// }
?>

@if ($item_load_flag)
	<?php $relations = PDao::getAllRelations($item_id); ?>
	<?php $inferred_relations = PDao::getInferredRelations($item_id); ?>
	@if (count($relations) >0)
		@include('includes.item-all-relations')
	@endif



@endif

<?php
if (!empty($item_id)){
	$bitstreams = PDao::getBitstreams($item_id);
	?>
	@if (count($bitstreams) >0)
		@include('includes.bitstreams')
	@endif
	<?php

	PDao::bitstream_symlinks_table($item_id);

	$print_flag =  variable_get('arc_display_notes', 0);
	if ($print_flag):
	PSnipets::contents_table($item_id);
	endif;
// 	$SQL="SELECT bitstream_id, bundle_name || ' ('|| bundle_id || ')', name, internal_id, size_bytes, item as item_ref FROM dsd.item_bitstream where item_id = ? AND internal_id is not null";
// 	$stmt = $dbh->prepare($SQL);
// 	$stmt->bindParam(1, $item_id);
// 	$stmt->execute();
// 	$r = $stmt->fetchAll();
// 	if (count($r) > 0){
// 		echo("<table>");
// 		echo("<thead>");
// 		echo('<tr><th colspan="6">');
// 		echo('bitstreams:');
// 		echo('</th></tr>');
// 		echo("</thead>");
// 		echo("<tbody>");
// 		foreach($r as $k => $v){
// 			echo("<tr>\n");
// 			printf('<td>%s</td>',$v[0]);
// 			printf('<td>%s</td>',$v[1]);
// 			printf('<td><a href="/archive/download?i=%s&d=%s">%s</a></td>',$item_id,$v[3],$v[2]);
// 			printf('<td>%s</td>',$v[4]);
// 			#printf('<td><a href="/archive/download?i=%s&d=%s">[view]</a></td>',$item_id,$v[3]);
// 			printf('<td><a href="/prepo/edit_bitstream?bid=%s">[edit]</a></td>',$v['bitstream_id']);
// 			printf('<td><a href="/prepo/edit_step2?i=%s">[item]</a></td>',$v['item_ref']);
// 			echo("</tr>\n");
// 		}
// 		echo("</tbody>");
// 		echo("</table>\n");
// 	}


}


if ($item_load_flag){

// 	$SQL="SELECT item_id, label, obj_type, rel_type, rt_label FROM (
// 	SELECT i.item_id, i.label, i.obj_type, r.rel_type ,rt.label as rt_label  FROM dsd.relation r
// 	join dsd.item2 i ON (r.item_2=i.item_id)
// 	join dsd.item_relation_type rt ON (rt.id = r.rel_type)
// 	WHERE r.item_1 = ?
// 	UNION
// 	SELECT i.item_id, i.label, i.obj_type, r.rel_type ,rt.label as rt_label  FROM dsd.relation r
// 	join dsd.item2 i ON (r.item_1=i.item_id)
// 	join dsd.item_relation_type rt ON (rt.id = r.rel_type)
// 	WHERE r.item_2 = ? ) AS TMP ORDER BY 5
// 	";
// 	$stmt = $dbh->prepare($SQL);
// 	$stmt->bindParam(1, $item_id);
// 	$stmt->bindParam(2, $item_id);
// 	$stmt->execute();
// 	$r = $stmt->fetchAll();

// 	if (count($r) > 0){
// 		echo('<table class="table table-striped table-bordered table-hover">');
// 		echo('<thead>');
// 		echo('<tr><th colspan="3">');
// 		echo('item relations:');
// 		echo('</th></tr>');
// 		echo('</thead>');
// 		echo("<tbody>");
// 		foreach($r as $k => $v){
// 			echo("<tr>\n");
// 			printf('<td>%s</td>',$v[4]);
// 			printf('<td>%s (%s)</td>',$v[2],$v[0]);
// 			//printf('<td>%s</td>',$v[0]);
// 			printf('<td><a href="/archive/item/%s">%s</td>',$v[0],$v[1]);
// 			#printf('<td>%s</td>',$v[3]);
// 			echo("</tr>\n");
// 		}
// 		echo("</tbody>");
// 		echo('<table class="table table-striped table-bordered table-hover">');
// 	}



// 	$subjects_print = function($subjects){
// 		if (empty($subjects)){
// 			return;
// 		}
// 		echo('<div class="subjects">');
// 		echo('<span class="subjectTitle">' . tr('Ετικέτες'). ': </span>');
// 		$coma = "";
// 		foreach ($subjects as $row){
// 			$my = $row[0];
// 			echo($coma);
// 			$ok = urlencode($my);
// 			printf('<a href="/prepo/subjects/subject?s=%s">%s (%s)</a>',$ok,$my,$row[1]);
// 			$coma =", ";
// 		}
// 		echo('</div>');
// 	};

// 	$SQL=" SELECT subject, cnt from dsd.subject where item = ?";
// 	$stmt = $dbh->prepare($SQL);
// 	$stmt->bindParam(1, $item_id);
// 	$stmt->execute();
// 	$r = $stmt->fetchAll();
// 	$subjects_print($r);



	//printf('<tr><td>%s</td><td>%s</td></tr>','jdata:',$item_basic['jdata']);
	//printf('<tr><td>%s</td><td>%s</td></tr>','jdata:', json_encode(json_decode($item_basic['jdata']),JSON_UNESCAPED_UNICODE));
	// 	echo("<tr><td>");echo('jdata:');echo("</td><td>");
	// 	$jdata = json_decode($item_basic['jdata']);
	// 	echo('<table border="1">');
	// 	foreach ($jdata as $k=>$v){
	// 		echo('<tr>');
	// 		printf("<td>%s</td><td><pre>%s</pre></td>",$k,json_encode($v, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
	// 		echo('</tr>');
	// 	}
	// 	echo('</table>');
	// 	echo("</td></tr>");


	$jdata = json_decode($item_basic['jdata']);

// 	if(!empty($jdata)){
// 		echo ('<div class="panel panel-primary">');
// 		echo ('<div class="a_dhead">JData</div>');
// 		echo('<pre class="a_pre">');
// 		foreach ($jdata as $k=>$v){
			//printf("%s : %s\n",$k,json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
			?>




			@if (!empty($jdata))
				<div class="panel panel-primary">
				<div class="a_dhead">JData</div>
				<pre class="a_pre">
					@foreach ($jdata as $k=>$v)
					<b>{{$k}}</b>:{{{json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)}}}
				<br>
				@endforeach
				</pre>
				</div>
			@endif


<?php
				$fts = $item_basic['fts'];
				$fts2 = $item_basic['fts2'];
				$prop_fts = $item_basic['prop_fts'];
?>
	@if (!empty($fts) || !empty($fts2) || !empty($prop_fts) )
				<div class="panel panel-primary">
				<div class="a_dhead">FTS</div>
				<pre class="a_pre">
				@if (!empty($fts))
					<b>fts:</b>{{$fts}}
					<br>
				@endif
				@if (!empty($fts2))
					<b>fts2:</b>{{$fts2}}
					<br>
				@endif
				@if (!empty($prop_fts))
					<b>prop_fts:</b>{{$prop_fts}}
				@endif
				</pre>
				</div>
	@endif




			<?php
// 			echo('<br>');
// 			}
// 		echo('</pre>');
// 		echo ('</div>');
// 	}

	if (!PUtil::user_access_item_submiter() && $item_load_flag){

		?>

			@include('includes.step2-links')

		<?php

// 		echo ('<div class="row">');
// // 		echo ('<div class="col-sm-12">');

// 			echo('<ul  id="admin_area" class="adminbar nav nav-pills">');
// 			printf('<li><a href="/prepo/edit_step1?i=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> '. tr('Edit'). '</a></li>',$item_id);
// 			printf('<li><a href="/prepo/edit_step3?i=%s"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> '. tr('Admin'). '</a></li>',$item_id);
// 			$print_flag =  variable_get('arc_display_artifacts', 0);
// 			if ($print_flag):
// 				printf('<li><a  href="/prepo/artifacts?i=%s"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> '. tr('Artifacts'). '</a></li>',$item_id);
// 			endif;
// 			printf('<li><a href="/prepo/thumbs?i=%s"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> '. tr('Thumbnails'). '</a></li>',$item_id);
// 			if (! $bitstream_flag){
// 				printf('<li><a href="/prepo/bitstreams?i=%s"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> '. tr('Bitstreams'). '</a></li>',$item_id);
// 				$print_flag =  variable_get('arc_display_notes', 0);
// 				if ($print_flag):
// 					printf('<li><a href="/prepo/contents?i=%s"><span class="glyphicon glyphicon-copy" aria-hidden="true"></span> '. tr('Content'). '</a></li>',$item_id);
// 				endif;
// 			}

// // 			printf('<li><a href="{{{UrlPrefixes::$cataloging}}}"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>'. tr('Cataloging').'</a></li>');
// //			printf('<li><a href="/prepo/edit_metadata?itid=%s"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> '. tr('New metadata'). '</a></li>',$item_id);
// 			echo('</ul><hr />');

// // 		echo('</div>'); //end col-sm
// 		echo('</div>'); //end ROW
	}
}


//echo('<table class="table table-striped table-bordered table-hover"><tr><td>');
echo ('<div class="row">');
// 	echo ('<div class="col-sm-6">');

// 		echo('<form method="GET" action="/prepo/edit_step1">');
// 		if (! empty($submit_id)){
// 			printf('<input type="hidden"  name="s" value="%s" />' ."\n",$submit_id);
// 		}
// 		if (! empty($item_id)){
// 			printf('<input type="hidden"  name="i" value="%s" />' ."\n",$item_id);
// 		}
// 		echo('<input type="submit" name="BACK" value="EDIT" class="btn btn-default" />');
// 		echo('</form>');

// 	echo('</div>');

	//echo("</td><td>");
	echo ('<div class="col-sm-6">');
		echo('<form method="POST" action="/prepo/edit_step3">');
		if (! empty($edoc) && !empty($submit_id) && $err_counter == 0){
			//echo('<form method="POST" action="/prepo/edit_step3">');
			printf('<input type="hidden"  name="submit_id" value="%s" />' ."\n",$submit_id);
			echo('<input type="submit"  name="CREATE" value="CREATE RECORD" class="btn btn-default"/>');
		} else if (! empty($item_id) && !empty($submit_id) && $err_counter == 0){
			//echo('<form method="POST" action="/prepo/edit_step3">');
			printf('<input type="hidden"  name="submit_id" value="%s" />' ."\n",$submit_id);
			printf('<input type="hidden"  name="item_id" value="%s" />' ."\n",$item_id);
			echo('<input type="submit"  name="SAVE" value="SAVE" class="btn btn-default"/>');
		} else if (!empty($submit_id) && $err_counter == 0) {
			//echo('<form method="POST" action="/prepo/edit_step3">');
			printf('<input type="hidden"  name="submit_id" value="%s" />' ."\n",$submit_id);
			echo('<input type="submit" name="CREATE" value="CREATE RECORD" class="btn btn-default"/>');
		}
		echo('</form>');

		// echo("</td><td>");
		// echo("<form>");
		// echo('<form method="POST" action="/prepo/edit_step3">');
		// echo("</form>");

		//echo("</td></tr></table>");
	echo('</div>');
echo('</div>'); //end ROW
?>

<script>

jQuery.fn.preventDoubleSubmission = function() {
  jQuery(this).on('submit',function(e){
    var form = jQuery(this);

    if (form.data('submitted') === true) {
      e.preventDefault();
    } else {
      form.data('submitted', true);
    }
  });
  // Keep chainability
  return this;
};

jQuery('form').preventDoubleSubmission();

</script>


 <?php //if ($item_load_flag && ! PUtil::user_access_item_submiter()): ?>
<!-- <table class="table table-striped table-bordered table-hover"> -->
<!-- 	<tr> -->
<!-- 		<td bgcolor="FFFFFF"><a href="/archive/item/<?php //echo($item_id)?>">[public]</a>  -->
<!-- 		</td> -->
<!-- 		<td bgcolor="FFFFFF" style="width: 30%">&nbsp;</td> -->
<!-- 		<td bgcolor="FFFFFF"> -->
		<?php 	//if (! $bitstream_flag): ?>
<!-- 			<a href="/prepo/change_ob_type?i=<? //$item_id?>">[change_item_type]</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  -->
<!-- 			<a href="/prepo/change_site?i=<? //$item_id?>">[change_site]</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  -->
			<?php
// 				if ($vivliografiki_anafora){
// 					printf('<a href="/prepo/bibref_togle?i=%s" onclick="return confirm(\'Are you sure that you want to remove the bibref property?\')">[remove bibref]</a>',$item_id);
// 				} else {
// 					printf('<a href="/prepo/bibref_togle?i=%s"   onclick="return confirm(\'Are you sure that you want convert the record to bibref?\')" >[covert to bibref]</a>',$item_id);
// 				}
// 			?>
			<?php    //<a href="/prepo/pdf_set_metadata?i=<=$item_id>">[pdf_metadata]</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ?>
		<?php //endif;?>
<!-- 		</td> -->
	<!-- 	<td bgcolor="FFFFFF"><a href="/prepo/export_item?i=<? //$item_id?>">[export]</a>  -->
		<? //if ($status == 'error' || $obj_class = 'artifact'): ?>
<!-- 		<a onClick="return confirm('Are you sure you want to delete this?')" href="/prepo/delete_item?i=<? //$item_id?>">[delete]</a>  -->
		<? //endif ?>
<!-- 		</td> -->
<!-- 	</tr> -->
<!-- </table> -->
 <?php //endif;?>

<hr />


<?php
// echo("<pre>");
// print_r($idata->values);
// echo("</pre>");
//include 'dump.php';
?>

@stop
