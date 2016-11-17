@section('content')
<?php auth_check_mentainer(); ?>
<?php
drupal_set_title("edit content");

if (Config::get('arc.LOAD_JS')){
# laravel jquery
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

?>
<style>
table.mceToolbar {
  margin: 0 6px 2px;
  display: inline-table;
}
</style>
<script type="text/javascript" src="/sites/all/libraries/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<?php
//@DocGroup(module="content", group="php", comment="edit content")

$id = get_post_get('cid');
if (empty($id)){
	echo("<h2>expected content_id</h2>");
	return;
}


$CONTENT_SRC_TYPE_MAP = Lookup::get_content_src_type_values();
$CONTENT_TYPE_MAP = Lookup::get_content_type_values();
$VISIBILITY_MAP = Lookup::get_visibility_values();
$BUNDLE_MAP = Lookup::get_content_bundles();

#@DocGroup(module="examples", group="print_select", comment="pull down menu boolean")
$PROMOTE_FP_MAP = array(0=>'false', 1=>'true');





// function compile_content_src($src){
// 	$out = "";
// 	$arr = preg_split("/\n/", $src);

// 	$start = false;
// 	$end = false;
// 	foreach ($arr as $ln => $l) {
// 	//echo(htmlspecialchars($l));
// 		if (preg_match('/^\s*$/',$l)){
// 			continue;
// 		}
// 		if (preg_match('/<!--\s*content\s+end\s*-->/',$l)){
// 			$end =true;
// 		}
// 		if ($start && ! $end){
// 			$out .= $l;
// 		}
// 		if (preg_match('/<!--\s*content\s+start\s*-->/',$l)){
// 			$start =true;
// 		}
// 	}
// 	if (! $start){
// 		return $src;
// 	}
// 	return $out;
// }



function update_content($id,$field,$value){
	$dbh = dbconnect();

	//if((! empty($value)) || (empty($value) && $value == 0)) {
	if (! PUtil::isEmpty($value)){
		$SQL = "UPDATE public.content SET " . $field . " = ? WHERE id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $value);
		$stmt->bindParam(2, $id,PDO::PARAM_INT);
		$stmt->execute();
	} else {
		$SQL = "UPDATE public.content SET " . $field . " = null WHERE id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $id,PDO::PARAM_INT);
		$stmt->execute();
	}
};




function save($create_drupal_node_flag){
//		echo("<pre> save:\n");
//	print_r($_POST);
// 		echo("</pre>");

	$archive_user = get_user_name();
	$u_cid = $_POST['cid'];
	$u_weight = $_POST['weight'];
	$u_desc = $_POST['description'];
	$u_title = $_POST['title'];
	$u_visibility = $_POST['visibility'];
	$u_bundle_name = $_POST['bundle_name'];
	//$u_content_type = $_POST['content_type'];
	$u_content_src_type = $_POST['content_src_type'];
	$u_content_src = $_POST['content_src'];
	$u_node_path = $_POST['node_path'];
	$u_publish_dt = $_POST['publish_dt'];
	$u_publish_user = $_POST['publish_user'];
	$u_drupal_node = $_POST['drupal_node'];
	$u_promote_fp = $_POST['promote_fp'];
	$u_promote_fp_flag = $u_promote_fp == 0? 'false' : 'true';
	$u_bitstream_desc = $_POST['bitstream_desc'];
	$u_download_filename = $_POST['download_filename'];
	$u_content_summary = null;
	if (isset($_POST['content_summary'])){
		$u_content_summary = $_POST['content_summary'];
	}

	$old_content = PDao::getContent($u_cid);
	if (empty($old_content)){
		echo "content not found";
		return;
	}
	$u_content_type = $old_content['content_type'];

	$parent_item = PDao::getItem($old_content['item_id']);
	$content_item = PDao::getItem($old_content['item']);
	$content_compiler = new PContentCompiler();
	$content_compiler->parent_item = $parent_item;
	$content_compiler->content_item = $content_item;

	update_content($u_cid,'weight',$u_weight);
	update_content($u_cid,'description',$u_desc);
	update_content($u_cid,'title',$u_title);
	update_content($u_cid,'visibility',$u_visibility);
	//update_content($u_cid,'content_type',$u_content_type);
	update_content($u_cid,'content_summary',$u_content_summary);
	update_content($u_cid,'node_path',$u_node_path);
	update_content($u_cid,'publish_dt',$u_publish_dt);
	update_content($u_cid,'publish_user',$u_publish_user);
	update_content($u_cid,'drupal_node',$u_drupal_node);
	update_content($u_cid,'promote_fp',$u_promote_fp_flag);
	update_content($u_cid,'bitstream_desc',$u_bitstream_desc);
	update_content($u_cid,'download_filename',$u_download_filename);

	if ($old_content['bundle_name'] != $u_bundle_name){
		change_item_bundle($u_cid, $u_bundle_name);
	}

	$dbh = dbconnect();
	$SQL = "SELECT dsd.add_content_version(?,?,?,?)  as rep;";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $u_cid);
	$stmt->bindParam(2, $u_content_src_type);
	$stmt->bindParam(3, $u_content_src);
	$stmt->bindParam(4, $archive_user);
	$stmt->execute();
	$row = $stmt->fetch();


	$new_version = $row['rep'];
	if (!empty($new_version)){
		printf('<p>new version (%s) saved </p>',$new_version);
		$content_html = null;
		$content_full = null;
		update_content($u_cid,'content_full',$content_full);

		if ($u_content_type == DataFields::DB_content_ctype_article){
			$content_compiler->compile($u_content_src,PContentCompiler::COMPILE_MODE_DRUPAL );
			$u_content = $content_compiler->content;
			$messages = $content_compiler->messages;

			$content_compiler->compile($u_content_src,PContentCompiler::COMPILE_MODE_HTML);
			$content_html = $content_compiler->content;
			$errors = $content_compiler->errors;

			echo("<pre>");
			foreach ($messages as $msg){
				echo($msg);
				echo("\n");
			}
			if (count($errors) > 0){
				echo ("\n ERRORS:\n");
				foreach ($errors as $err){
					echo($err);
					echo("\n");
				}
			}
			echo("</pre>");

		} else {
			$u_content = $u_content_src;
			$content_html = $u_content_src;
		}
		//$size_bytes = mb_strlen($u_content, '8bit');
		$size_bytes = mb_strlen($u_content, '8bit');

		$SQL = "update public.content set content = ? ,size_bytes = ?, content_html = ? WHERE id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $u_content);
		$stmt->bindParam(2, $size_bytes);
		$stmt->bindParam(3, $content_html);
		$stmt->bindParam(4, $u_cid,PDO::PARAM_INT);
		$stmt->execute();
	}

	$cc = PDao::getContent($u_cid);
	$nid = PDrupal::sync_drupal_node($cc,$create_drupal_node_flag);
	if ($create_drupal_node_flag && ! PUtil::isEmpty($nid)){
		update_content($u_cid, 'drupal_node', $nid);
	}
}



function create_symlink(){


	$dbh = dbconnect();
	$u_cid = get_post('cid');
	$weight = get_post('weight',null);
	$item_id = get_post('item_id');
	$bundle_name = get_post('bundle_name');

	if (empty($weight)){
		$weight = 10;
	}
	$SQL="SELECT * FROM dsd.ln_content(?, ?, ?, ?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $u_cid);
	$stmt->bindParam(2, $item_id);
	$stmt->bindParam(3, $bundle_name);
	$stmt->bindParam(4, $weight);
	$stmt->execute();
	$rep = $stmt->fetchAll();
	// 	echo ("<pre>");
	// 	print_r($rep);
	// 	echo ("</pre>");

}



if (! empty($_POST) && isset($_POST['create_symlink']) && ($_POST['create_symlink'] == 'create_symlink')){
	save(false);
	create_symlink();
}

if (! empty($_POST) && isset($_POST['undelete']) && ($_POST['undelete'] == 'undelete')){
	$u_cid = $_POST['cid'];
	update_content($u_cid,'visibility',2);
}

if (! empty($_POST) && isset($_POST['delete']) && ($_POST['delete'] == 'delete')){
	$u_cid = $_POST['cid'];
	update_content($u_cid,'visibility',20);

}
if (! empty($_POST) && isset($_POST['save']) && ($_POST['save'] == 'save')){
	save(false);
}
if (! empty($_POST) && isset($_POST['sync_drupal']) && ($_POST['sync_drupal'] == 'sync_drupal')){
	save(true);
	//PDrupal::createContentNodel($title);
}



$c = PDao::getContent($id);
if (empty($c)){
	echo "content not found";
	return;
}

$nid = $c['drupal_node'];
if (! empty($nid)){
	$node = node_load($nid);
	if (empty($node)){
		$nid= null;
	}
}


$item_id = $c['item_id'];
$citem_id = $c['item'];
$citem = Pdao::getItem($citem_id);
$tags = $citem['keywords'];
$ptags = $citem['pkeywords'];


//$item_label =  item_get_label($item_id);
echo('<div id="edit_content_nav">');
if (! empty($item_id)){
	printf('<a href="/archive/item/%s">[back to item]</a>',$item_id);
	echo("&#160;&#160;&#160;&#160;&#160;&#160;");
	printf('<a href="/prepo/contents?i=%s">[back to contents]</a>',$item_id);
}

echo("&#160;&#160;&#160;&#160;&#160;&#160;");
printf('<a href="/prepo/edit_step2?i=%s">[item_ref edit]</a>',$citem_id);
echo("&#160;&#160;&#160;&#160;&#160;&#160;");
printf('<a href="/archive/item/%s">[item_ref view]</a>',$citem_id);
echo('</div>');


$visibility = $c['visibility'];
error_log("visibility: ".$visibility);
if ($visibility == 20){
	printf('<p> the content (%s) is deleted</p>', $id);
	printf('<form method="post"><input type="hidden"  name="cid" value="%s"/><button type="submit" name="undelete" value="undelete">undelete</button></form></form>',$id);
	return;
}


$lv = PDao::getLatestContentVersion($id);
$content_src = null;
if (isset($lv['content_src'])){
	$content_src = $lv['content_src'];
}
$content_src_type = 1;
if (isset($lv['content_src_type'])){
	$content_src_type = $lv['content_src_type'];
}

$content= $c['content'];
$content_summary = $c['content_summary'];

#@DocGroup(module="examples", group="print_select", comment="pull down menu boolean")
$promote_fp_flag = $c['promote_fp'];
$promote_fp = $promote_fp_flag ? 1 : 0;

$content_type_str = $CONTENT_TYPE_MAP[$c['content_type']];
?>


<h2>content</h2>
<form method="post">
	<input type="hidden"  name="cid" value="<?=$id?>"/>
<table>
	<tr><td>type</td><td><?=$content_type_str?></td></tr>
	<tr><td>id</td><td><?=$id?> </td></tr>
	<tr><td>item_ref</td><td><a href="/prepo/edit_step2?i=<?=$citem_id?>"><?=$citem_id?></a></td></tr>
	<tr><td>weight</td><td ><input type="text" name="weight"  size="6" value="<?=$c['weight']?>"/></td></tr>
	<tr><td>description</td><td><input type="text" name="description" size="140" value="<?=$c['description']?>"/></td></tr>
	<tr><td>title</td><td><input type="text" name="title" size="140" value="<?=$c['title']?>"/></td></tr>
	<?php if ($content_type_str == 'article'):?>
		<tr><td>bitstream description</td><td><input type="text" name="bitstream_desc" size="140" value="<?=$c['bitstream_desc']?>"/></td></tr>
		<tr><td>download filename</td><td><input type="text" name="download_filename" size="80" value="<?=$c['download_filename']?>"/></td></tr>
	<?php endif;?>
	<tr><td>visibility</td><td><?php PUtil::print_select("visibility","select_visibility",$VISIBILITY_MAP, $c['visibility'],false); ?></td></tr>
	<tr><td>bundle</td><td><?php PUtil::print_select("bundle_name","select_bundle_name",$BUNDLE_MAP, $c['bundle_name'],false); ?></td></tr>
	<tr><td>publish timestamp</td><td><input type="text" name="publish_dt" size="80" value="<?=$c['publish_dt']?>"/></td></tr>
	<?php if ($content_type_str == 'article'):?>
		<tr><td>drupal node</td><td><input type="text" name="drupal_node" size="20" value="<?=$nid?>"/></td></tr>
		<?php if (! empty($nid)):?>
			<tr><td>url_path</td><td><input type="text" name="node_path" size="80" value="<?=$c['node_path']?>"/>(xoris / stin arxi)</td></tr>
			<tr><td>publish user</td><td><input type="text" name="publish_user" size="50" value="<?=$c['publish_user']?>"/>(prepei na iparxi sto drupal)</td></tr>
			<tr><td>promote front page</td><td><?php PUtil::print_select("promote_fp","select_promote_fp",$PROMOTE_FP_MAP, $promote_fp,false); ?></td></tr>
		<?php endif;?>
	<?php endif;?>
	<tr><td>content size</td><td ><?=$c['size_bytes']?> Bytes</td></tr>
	<tr><td colspan="2">
	<?php if (! empty($content_summary)):?>
		<p style="margin:1px">summary:</p>
		<div id="item_content_summary" style="border:1px solid gray;padding:3px">
		<?=$content_summary?>
		</div>
		</td>
	<?php endif;?>
	<tr><td colspan="2">
	<button type="button" id="show_content">hide content</button> <button type="button" id="show_src">show src</button>
	<div id="item_content" style="display: block;border:1px solid gray;padding:3px">
	<?=$content?>
	</div>
	<div id="item_content_src" style="display: none;border:1px solid gray;padding:3px">
	<pre>
	<?=htmlspecialchars($content)?>
	</pre>
	</div>
	</td>
	</tr>
</table>

<table>
	<tr><td colspan="2">SOURCE:</td></tr>
	<tr><td>type</td><td>
		<?php PUtil::print_select("content_src_type","select_content_src_type",$CONTENT_SRC_TYPE_MAP, $content_src_type,false); ?>
		 <button type="button" id="show_edit_src">hide editors</button>
		<?php if ($content_type_str == 'note'):?>
		&#160;&#160; <button type="button" id="btinymce">disable WYSIWYG</button>
		<?php endif;?>
	</td></tr>
	<tr><td colspan="2">
	<div id="edit_src_area" style="display:block">
	<?php if ($content_type_str == 'article'):?>
	<div style="margin:1px">summary:</div>
	<textarea id="content_summary_area" class="tinymce" name="content_summary" style="width:98%;height:160px"  rows="20"><?=htmlspecialchars($content_summary); ?></textarea>
	<?php endif;?>
	<div style="margin:1px;padding:1px">body:</div>
	<textarea id="content_src_area"  class="tinymce" name="content_src" style="width:98%;height:400px"  rows="80"><?=htmlspecialchars($content_src); ?></textarea>

	</div>
	</td></tr>
</table>


<button  style="float:right;margin-left: 40px" type="button" id="reset_button">reset form</button>
<button  style="float:right;margin-left: 40px" type="button" id="clear_src_button">clear src</button>
<?php if ($content_type_str == 'article'):?>
<button  style="float:right;margin-left: 40px" type="button" id="add_article_template">add article template</button>
<?php endif;?>

<br/>
<br/>
<br/>


	<button type="submit" name="save" value="save" style="float:left;margin-left: 10px">save</button> &#160;&#160;&#160;&#160;
<?php if ( empty($nid) && $c['content_type'] == DataFields::DB_content_ctype_article){ ?>
	<button type="submit" name="sync_drupal" value="sync_drupal" style="float:left;margin-left: 10px">create drupal node</button>
<?php }  ?>


	<button id="delete_button" type="submit"  name="delete" value="delete" style="float:right;margin-right: 10px"  onClick="return confirm('ARE YOU SURE ?')">DELETE</button>
</form>

<br/>
<br/>


<table>
<tr><th colspan="9">bitstreams <a href="/prepo/bitstreams?i=<?=$citem_id?>">[edit]</a></th></tr>
<?php
PDao::bitstreams_table($citem_id,false);
?>
</table>




<?php
$symlinks = Pdao::find_content_simlinks($id);
if (!empty($symlinks)): ?>

<table>
<thead>
	<tr>
	<th colspan="4">symlinks</th>
	</tr>
	<tr>
	<th>budnle</th>
	<th>item</th>
	<th>weight</th>
	</tr>
</thead>
<?php
foreach ($symlinks as $s) {
	printf('<tr><td>%s</td> <td><a href="/archive/item/%s">%s</a></td> <td>%s</td></tr>',$s['bundle'],$s['item_id'],$s['item_title'],$s['bb_weight']);
}
?>
</table>
<?php endif;?>


<table>
<tr><th>tags</th></tr>
<tr><td>
<div class="tags">
<?php
	foreach ($tags as $k => $v){
		printf("<span>%s</span>",$v);
	}
?>
</div>
</td></tr>
</table>

<table>
<tr><th>ptags</th></tr>
<tr><td>
<div class="tags">
<?php
	foreach ($ptags as $k => $v){
		printf("<span>%s</span>",$v);
	}
?>
</div>
</td></tr>
</table>

<style>
.tags span {
	margin-left: 10px;
}
</style>

<hr/>
<form method="POST">
<input type="hidden" name="cid" value="<?= $id ?>"/>
item_id:<input type="text" name="item_id" value="" size="6"/>
weight:<input type="text" name="weight" value="" size="3"/>
bundle: <?php PUtil::print_select("bundle_name","id2_select_bundle_name",$BUNDLE_MAP, 'ORIGINAL',false); ?>

<input type="submit" name="create_symlink" value="create_symlink" onClick="return confirm('ARE YOU SURE ?')">
</form>



<script>
	var next_content_display = "none";
	var next_content_src_display = "block";
	var next_edit_src_display = "none";
	var tynmc_flag = true;
	jQuery("#show_content").click(function(event){
		jQuery("#item_content").css("display",next_content_display);
		if (next_content_display == 'block'){
			next_content_display = 'none';
			jQuery("#show_content").text("hide content");
		} else {
			next_content_display = 'block';
			jQuery("#show_content").text("show content");
		}
	});


	jQuery("#btinymce").click(function(event){
		jQuery("#item_content").css("display",next_content_display);
		if (tynmc_flag){
			tinyMCE.execCommand('mceRemoveControl', false, 'content_src_area');
			tinyMCE.execCommand('mceRemoveControl', false, 'content_summary_area');
			tynmc_flag = false;
			jQuery("#btinymce").text("enable WYSIWYG");
		} else {
			tinyMCE.execCommand('mceAddControl', false, 'content_src_area');
			tinyMCE.execCommand('mceAddControl', false, 'content_summary_area');
			tynmc_flag = true;
			jQuery("#btinymce").text("disable WYSIWYG");
		}
	});

	jQuery("#show_src").click(function(event){
		jQuery("#item_content_src").css("display",next_content_src_display);
		if (next_content_src_display == 'block'){
			next_content_src_display = 'none';
			jQuery("#show_src").text("hide src");
		} else {
			next_content_src_display = 'block';
			jQuery("#show_src").text("show src");
		}
	});


	jQuery("#show_edit_src").click(function(event){
		jQuery("#edit_src_area").css("display",next_edit_src_display);
		if (next_edit_src_display == 'block'){
			next_edit_src_display = 'none';
			jQuery("#show_edit_src").text("hide editors");
		} else {
			next_edit_src_display = 'block';
			jQuery("#show_edit_src").text("edit src");
		}

	});


	jQuery("#clear_src_button").click(function(event){
		jQuery("#content_src_area").val("");
	});


	jQuery("#reset_button").click(function(event){
		window.location = window.location;
	});

	jQuery("#add_article_template").click(function(event){
		var src_textarea = jQuery("#content_src_area");
		var src = src_textarea.val();
		var template = '\
<\?xml version="1.0" encoding="utf-8"\?>  \n\
<!DOCTYPE html>\n\
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:arc="http://maistrelis.com/" >\n\
<head>\n\
 <meta charset="UTF-8" />\n\
 <meta name="author" content="Kostas Maistrelis" />\n\
 <meta name="keywords" content=""/>\n\
 <title></title>\n\
 <base href="http://maistrelis.com/"/>\n\
</head>\n\
<body>\n\
\n\
<h1></h1>\n\
\n\
<!-- content start -->\n\
\n\
\n\
<!-- content end -->\n\
</body>\n\
</html>\n\
\n\
\n\
';
		src = template + src;
		src_textarea.val(src);
	});




<?php if ($content_type_str == 'note'):?>

	tinyMCE.init({
		entities : '160,nbsp,38,amp,60,lt,62,gt',
		mode : "textareas",
		relative_urls : false,
		theme : "advanced",
		content_css : "/_assets/css/tinymce_styles.css",

	});

<?php endif;?>
</script>
@stop
