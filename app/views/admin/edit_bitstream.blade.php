@section('content')
<?php auth_check_mentainer(); ?>
<?php
		drupal_set_title("edit bitstream");
?>
<?php
//@DocGroup(module="bitstream", group="php", comment="edit bitstream")

Log::info("@################################################################################@");

if (Config::get('arc.LOAD_JS')){
# laravel jquery
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}



$id = get_get('bid');
if (empty($id)){
	echo("<h2>expected bitstream_id</h2>");
	return;
}

$BUNDLE_MAP = Lookup::get_bitstream_bundles();


function add_bitstream_version($old_bitstream_record, $new_id){
	$dbh = dbconnect();
	if ($old_bitstream_record == null){
		return "ERROR (8)";
	}
	$oldb = $old_bitstream_record;

	$old_id = $old_bitstream_record['bitstream_id'];


	#ALAGI bundle sto old record
	$bundle_old_id = PDao::get_bundle_id($oldb['item_id'],'OLD');
	if (empty($bundle_old_id)){
		echo ("create bundle: OLD\n");
		$bundle_old_id = PDao::insert_bundle($dbh, 'OLD');
		printf("connect bundle: OLD (%s) TO item (%s)\n",$bundle_old_id,$oldb['item_id']);
		PDao::insert_item2bundle($dbh,$oldb['item_id'],$bundle_old_id);
	} else {
		echo ("bundle: OLD ($bundle_old_id) FOUND\n");
	}
	printf("remove bitstream from original bundle (%s)\n",$oldb['bundle_id']);
	$SQL="DELETE FROM public.bundle2bitstream WHERE bundle_id =? AND bitstream_id =? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $oldb['bundle_id']);
	$stmt->bindParam(2, $old_id);
	$stmt->execute();
	$count = $stmt->rowCount();
	print("$count rows Deleted\n");
	printf("connect bundle: OLD (%s) TO bitstream (%s)\n",$bundle_old_id, $old_id);
	PDao::insert_bundle2bitstream($dbh, $bundle_old_id, $old_id);
	##UPDATE NEW record
	#artifact_id = ?, furl =?, description = ?, src_url = ?, redirect_url = ?, redirect_type = ? , download_fname =?, logging = ?, internal_comment = ?,
	#
	echo("update new record ($new_id)\n");
	$SQL="update public.bitstream SET
	artifact_id = ?, furl =?, description = ?,src_url = ?, redirect_url = ?,
	redirect_type = ? , download_fname =?, logging = ?, internal_comment = ?, info = ?, thumb_description = ?,
	replaces = ?
	WHERE bitstream_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $oldb['artifact_id']);
	$stmt->bindParam(2, $oldb['furl']);
	$stmt->bindParam(3, $oldb['description']);
	$stmt->bindParam(4, $oldb['src_url']);
	$stmt->bindParam(5, $oldb['redirect_url']);
	$stmt->bindParam(6, $oldb['redirect_type']);
	$stmt->bindParam(7, $oldb['download_fname']);
	$stmt->bindParam(8, $oldb['logging']);
	$stmt->bindParam(9, $oldb['internal_comment']);
	$stmt->bindParam(10, $oldb['info']);
	$stmt->bindParam(11, $oldb['thumb_description']);
	$stmt->bindParam(12, $old_id);
	$stmt->bindParam(13, $new_id);
	$stmt->execute();
}

function dump_data($id){
	$dbh = dbconnect();
	$SQL = "SELECT * FROM  dsd.item_bitstream_ext where bitstream_id=?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $id);
	$stmt->execute();

	if ($dump = $stmt->fetch(PDO::FETCH_ASSOC)){
		echo("<pre>");
		print_r($dump);
		echo("</pre>");
	}

}




//
//
if (! empty($POST_DATA)){
// 	echo("<pre>");
// 	print_r($_POST);
// 	echo("</pre>");

	$get_post = function ($name,$default = null) use ($POST_DATA){
		if (isset($POST_DATA[$name])){
			return $POST_DATA[$name];
		}
		return $default;
	};

	$replace_bitstream  = false;
	$replace_bitstream_file_from_url = strtolower($get_post('send_url','0')) == 'send';
	if ($replace_bitstream_file_from_url){
		echo("<pre>");
		echo("REPLACE BITSTREAM FILE FROM URL\n");
		$upload_url = $get_post('upload_url');
		$file_prefix = $get_post('file_prefix');
		list($upload_file_name, $upload_file_path,$upload_file_ext) = get_file_from_url($upload_url, $file_prefix, true);
		$replace_bitstream = true;
	}


	$extract_file_info_from_upload_form = function (){
		$app= App::make('arc');
		$upload_files = $app->upload_files;
		if (isset($upload_files['uploadedfile'])){
	  	$files = $upload_files['uploadedfile'];
	  	if (count($files) > 0){
	  		$f = array_shift($files);
// 	  		[name] => 960-221-239-X.epub
// 	  		[tmp_name] => /tmp/archive_fu_55a297f307057
// 	  		[size] => 277271
// 	  		[type] => application/epub+zip
// 	  		[extension] => epub
	  		return array($f['tmp_name'], $f['name'],$f['extension']);
	  	}
	  }
	  return array(null,null,null);
	};

	$replace_bitstream_file_from_file = strtolower($get_post('send_file','0')) == 'send';
	Log::info("replace_bitstream_file_from_file: " . ($replace_bitstream_file_from_file ? 'TRUE' : 'FALSE'));
	if ($replace_bitstream_file_from_file){
		echo("<pre>");
		echo("REPLACE BITSTREAM FILE FROM FILE\n");
		list($upload_file_path, $upload_file_name,$upload_file_ext)= $extract_file_info_from_upload_form();
		Log::info("UPLOAD_FILE_PATH: " . $upload_file_path);
		Log::info("UPLOAD_FILE_NAME: " . $upload_file_name);
		$replace_bitstream = true;
	}

	if ($replace_bitstream) {
		if (empty($upload_file_path) || empty($upload_file_name) || empty($upload_file_ext)){
			echo("ERORR canot update\n");
			echo($upload_file_path);echo("\n");
			echo($upload_file_name);echo("\n");
			echo($upload_file_ext);echo("\n");
			echo("</pre>");
			return;
		}
		$old_bitstream_record = PDao::getBitstream($id);
		$seq_id   = PDao::get_bitstream_next_seq_id($old_bitstream_record['item_id']);
		echo("create new bitstream\n");
		list($bitstream_id,$bundle_id,$uuid) = PUtil::upload_bitsteam($upload_file_path, $upload_file_name,$upload_file_ext, $old_bitstream_record['item_id'], $seq_id, $old_bitstream_record['bundle_name']);
		echo("add version\n");
		add_bitstream_version($old_bitstream_record,$bitstream_id);
		echo("</pre>");

		printf('</br><div style="font-size:300%;font-weight:bold;"><a href="/prepo/edit_bitstream?bid=%s">bitstream: %s</a> created as new version </div></br></br>',$bitstream_id,$bitstream_id,$bitstream_id);
		//$location = sprintf('/edit_bitstream?bid=%s',$bitstream_id);
		//drupal_add_http_header('Location', $location);
	} else {
		$item_id  = $get_post("item_id");
		if (empty($item_id)){
			echo("expected item");
			return;
		}

		##################################################
		### UPDATE
		##################################################
		$update_flag = $get_post('update','0') == 'update';
		if ($update_flag){
			$bundle_name = $get_post('bundle_name');
			if (empty($bundle_name)){
				echo("bundle name expected");
				return;
			}


			echo("<pre>");
			echo("UPDATE RECORD: $id");
			echo("</pre>");
			$dbh = dbconnect();
			$SQL="SELECT bundle_name from dsd.item_bitstream where bitstream_id=?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $id);
			$stmt->execute();
			if ($tmp = $stmt->fetch()){
				$old_bundle_name = $tmp[0];
			} else {
				echo ("bitstream not found");
				return;
			}


			$update_bitstream = function($id,$field,$value){
				Log::info("update_bitstream");
				$dbh = dbconnect();
				if(! empty($value)) {
					$SQL = "UPDATE public.bitstream SET " . $field . " = ? WHERE bitstream_id = ?";
					$stmt = $dbh->prepare($SQL);
					$stmt->bindParam(1, $value);
					$stmt->bindParam(2, $id,PDO::PARAM_INT);
					$stmt->execute();
				} else {
					$SQL = "UPDATE public.bitstream SET " . $field . " = null WHERE bitstream_id = ?";
					$stmt = $dbh->prepare($SQL);
					$stmt->bindParam(1, $id,PDO::PARAM_INT);
					$stmt->execute();
				}
			};

			$dbh->beginTransaction();
			$update_bitstream($id,"name", $get_post("name"));
			$update_bitstream($id,"download_fname", $get_post("download_fname"));
			$update_bitstream($id,"description", $get_post("description"));
			$update_bitstream($id,"thumb_description", $get_post("thumb_description"));
			$update_bitstream($id,"file_ext", $get_post("file_ext"));
			$update_bitstream($id,"src_url", $get_post("src_url"));
			$update_bitstream($id,"redirect_url", $get_post("redirect_url"));
			$update_bitstream($id,"redirect_type", $get_post("redirect_type"));
			$update_bitstream($id,"internal_comment", $get_post("internal_comment"));
			$update_bitstream($id,"furl", $get_post("furl"));
			$update_bitstream($id,"info", $get_post("info"));
			$update_bitstream($id,"sequence_id", $get_post("sequence_id"));
			$update_bitstream($id,"logging", $get_post("logging"));


			if ($old_bundle_name <> $bundle_name){
				//change_bitstream_type($item_id,$id,$bundle_name);
				//hange_bitstream_bundle($id, $bundle_name);
				PDao::change_bundle($id, $bundle_name,'bitstream');
			}
			$dbh->commit();
		}

		##################################################
		### generate_item_thumbnails
		##################################################
		$update_items_thumbs_flag = $get_post('generate_item_thumbnails','0') == 'generate_item_thumbnails';
		if ($update_items_thumbs_flag){
			Log::info('generate_item_thumbnails');
			$dbh = dbconnect();
			$all_flag = true;
			$parent_flag = true;
			$out = "";
			$internal_id = PDao::bitstream_get_internal_id($id);
			if (empty($internal_id)){ echo("INTERNAL ID NOT FOUND");return;};
			PDao::thumbs_generate_from_bitstream($dbh, $internal_id, $out,$all_flag,$parent_flag);
		}

		##################################################
		### generate_bitstream_thumbnails
		##################################################
		$update_bitstream_thumbs_flag = $get_post('generate_bitstream_thumbnails','0') == 'generate_bitstream_thumbnails';
		if ($update_bitstream_thumbs_flag){
			Log::info('generate_bitstream_thumbnails');
			$dbh = dbconnect();
			$all_flag = false;
			$parent_flag = false;
			$out = "";
			$internal_id = PDao::bitstream_get_internal_id($id);

			if (empty($internal_id)){ echo("INTERNAL ID NOT FOUND");return;};
			PDao::thumbs_generate_from_bitstream($dbh, $internal_id, $out,$all_flag,$parent_flag);
		}

		##################################################
		### $generate_custom_thumbnail
		##################################################
		$generate_custom_thumbnail = $get_post('generate_custom_thumbnail','0') == 'generate_custom_thumbnail';
		if($generate_custom_thumbnail){
			Log::info('generate_custom_thumbnail');
			$w = $get_post('width');
			$h = $get_post('height');
			$ext = $get_post('extension');
			$page_no = $get_post('page');
			if( !empty($w) && !empty($h) ){
					echo("<pre>");
					$img = PUtil::create_bitstream_thumbnail($id,$w,$h,$ext,$page_no);
					$url = sprintf("/media/%s", $img);
					echo ($url);
					echo("</pre>");
					printf('<table><tr><td>new&nbsp;thumbnail: </td><td width="%s"><img src="%s"/></td></tr></table>','100%',$url);
			}
		}


		##################################################
		### CREATE_SYMLINK
		##################################################
		$create_symlink = $get_post('create_symlink','0') == 'create_symlink';
		if($create_symlink){
			$dbh = dbconnect();
// 			echo("<pre>");
// 			print_r($_POST);
// 			echo("</pre>");
			$weight = $get_post('weight',null);
			$item_id = $get_post('item_id');
			$bundle_name = $get_post('bundle_name');
			$bitstream_id = $get_post('bitstream_id');

			if (empty($weight)){
				$weight = 10;
			}
			$SQL="SELECT * FROM dsd.ln_bitstream(?, ?, ?, ?)";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $bitstream_id);
			$stmt->bindParam(2, $item_id);
			$stmt->bindParam(3, $bundle_name);
			$stmt->bindParam(4, $weight);
			$stmt->execute();
			$rep = $stmt->fetchAll();
// 			echo ("<pre>");
// 			print_r($rep);
// 			echo ("</pre>");

		}


	}
}

#######################################################################################
#### VIEW  BITSTREAM
#######################################################################################
	$dbh = dbconnect();

	$row = PDao::getBitstream($id);
	$item_id = $row['item_id'];
	$item_label = null;
	if (empty($item_id) ){
		echo("item_id expected");
		return ;
	}
	$item_label =  PDao::item_get_label($item_id);
	$item_ref_id = $row['item'];
	$internal_id = $row['internal_id'];
	$bitstream_filename  = PUtil::bitream2filename($internal_id);
	$seq_id = $row['sequence_id'];
	$mimeType = $row['mimetype'];

	//$disabled = 'disabled="disabled"';

	$SQL = "SELECT i.label, t.file as thumb_file, t.idx, t.idxf, t.ttype, t.extension
	FROM dsd.bitstream b
	LEFT JOIN   dsd.item2  i ON (i.item_id = b.item)
	LEFT JOIN dsd.thumbs t ON (t.item_id = i.item_id AND t.ttype = 3)
	WHERE  b.bitstream_id =?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $id);
	$stmt->execute();
	$thumbs = $stmt->fetchAll();


##############################################
$symlinks = Pdao::find_bitstream_simlinks($id);
##############################################

drupal_add_library('system', 'ui.autocomplete');
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH')."js/edit_metadata.js",'external');


$bitstreams_url = sprintf('/prepo/bitstreams?i=%s', $item_id);
echo('<div class="row">');
echo('<ul class="adminbar nav nav-pills bit-admin"  id="admin_area">');

// printf('<li><a href="%s"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> '. tr('Back to bitstreams'). '</a></li>',$bitstreams_url);
printf('<li><a href="/prepo/edit_step3?i=%s"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> '. tr('Back to item'). '</a></li>',$item_id,$item_label);
// printf('<li><a href="/prepo/edit_step2?i=%s"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> '. tr('Item ref'). '</a></li>',$item_ref_id);
printf('<li><a target="_new" href="/prepo/thumbs?i=%s"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> '. tr('Thumbs'). '</a></li>',$item_ref_id);
printf('<li><a target="_new" href="/prepo/move_bitstream?aid=%s"><span class="glyphicon glyphicon-move" aria-hidden="true"></span> '. tr('Move to item'). '</a></li>',$row['artifact_id']);

echo('</ul>');
echo('</div>');

/** Grid classes variables **/
$labelwidth 	 = "col-sm-2 col-md-4 col-lg-4";
$inputwidth 	 = "col-sm-10 col-md-8 col-lg-8";
$inputwidthsmall = "col-sm-4";

?>

<div class="row">

<h1 class="admin item-title bitstream">Edit bitstream</h1>


	<div class="panel panel-primary">
		<div class="panel-files panel-body">
			<form class="form-horizontal" method = "post">

			  <input type="hidden" name="item_id" value="<?=$item_id ?>"/>
			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Thumbs') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
						<p class="form-control-static">
						<?php
						foreach ($thumbs as $k => $v){
							printf('<a href="/archive/download?i=%s&d=%s"><img src="/media/%s"/></a> ',$item_id,$internal_id,$v['thumb_file']);
							#printf('<td><img src="/media/%s"/></td>',$v['thumb_file']);
						}
						?>
						</p>
			    </div>
			  </div>

			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('ID') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
			      <p class="form-control-static"> <?php echo tr('Bitstream') ?>:  &nbsp; <?=$row['bitstream_id']?>  &nbsp;&nbsp;&nbsp;&nbsp; <?php echo tr('Artifact') ?>: &nbsp; <?=$row['artifact_id']?></p>
			    </div>
			  </div>

			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Parent item') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
			      <p class="form-control-static"><a href="/prepo/edit_step2?i=<?=$item_id?>"><?=$item_label?></a></p>
			    </div>
			  </div>



			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('File name') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				    <input type="text" name="name" value="<?=$row['name']?>" class="form-control" size="60"/>
			    </div>
			  </div>

			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Download file name') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				    <input type="text" name="download_fname" value="<?=$row['download_fname']?>" class="form-control" size="60"/>
			    </div>
			  </div>

			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('File extension') ?>:</label>
			    <div class="<?php echo $inputwidthsmall; ?>">
				    <input type="text" name="file_ext" value="<?=$row['file_ext']?>" class="form-control" size="10"/>
			    </div>
			  </div>


			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Thumb description') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
					<input type="text" name="thumb_description" value=" <?=$row['thumb_description'] ?>" class="form-control" size="102"/>
			    </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Description') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
					<input type="text" name="description" value="<?=$row['description']?>" class="form-control" size="102"/>
			    </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Info') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
					<textarea name="info" rows="3" class="form-control"><?=$row['info']?></textarea>
			    </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Bundle') ?>:</label>
			    <div class="<?php echo $inputwidthsmall; ?>">
					<?php

					$defVal = $row['bundle_name'];
					if ($defVal == 'OLD'){
					echo('OLD');
						printf('<input type="hidden" name="bundle_name" value="OLD"/>');
					} else {
						PUtil::print_select("bundle_name","select_bundle_name",$BUNDLE_MAP, $defVal,false,false,"form-control");
					}
					?>
			    </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Furl') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				    <input type="text" name="furl" value="<?=$row['furl']?>" class="form-control"/>
			    </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Src url') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				   <input type="text" name="src_url" value="<?=$row['src_url']?>" class="form-control"/>
			    </div>
			  </div>

			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Redirect url') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
					<input type="text" name="redirect_url" value="<?=$row['redirect_url']?>" class="form-control" />
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Redirect type') ?>:</label>
			    <div class="<?php echo $inputwidthsmall; ?>">
					<?php
					$map = array(
						'1' => 'none',
						'2' => 'redirect to external',
						'3' => 'direct from external',
					);
					$defVal = $row['redirect_type'];
					#-@DocGroup(module="examples", group="print_select", comment="pull down menu")
					PUtil::print_select("redirect_type","select_redirect_type",$map, $defVal,false,false,"form-control");
					?>
			    </div>
			  </div>

			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Internal comment') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				    <input type="text" name="internal_comment" value="<?=$row['internal_comment']?>" class="form-control" />
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Logging') ?>:</label>
			    <div class="<?php echo $inputwidthsmall; ?>">
					<?php
					$map = array(
						'0' => 'no logging',
						'1' => 'db logging',
					);
					$defVal = $row['logging'];
					#-@DocGroup(module="examples", group="print_select", comment="pull down menu")
					PUtil::print_select("logging","select_logging",$map, $defVal,false,false,"form-control");
					?>
			    </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Sequence id') ?>:</label>
			    <div class="<?php echo $inputwidthsmall; ?>">
			    	<input type="text" name="sequence_id" value="<?=$row['sequence_id']?>" class="form-control" />
			    </div>
			  </div>



			  <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Internal id') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static"> <?=$row['internal_id']?></p>
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('File path') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static"><?php printf('<a href="/archive/download?i=%s&d=%s">%s</a>',$item_id,$internal_id,$bitstream_filename);?></p>
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Size bytes') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static"><?=$row['size_bytes']?></p>
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Original md5') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static">
				      	<?php if (!empty($row['md5_org'])):?>
							  	<?=$row['md5_org']?>
			  					<a target="_new" href="http://libgen.org/book/index.php?md5=<?=$row['md5_org']?>">[libgen]</a>
			  			<?php endif;?>
				     </p>
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Checksum') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static">
						<?=$row['checksum']?>  (<?=$row['checksum_algorithm']?>)
							<?php if (!empty($row['checksum'])):?>
						  		<?=$row['md5_org']?>
						  		<a target="_new" href="http://libgen.org/book/index.php?md5=<?=$row['checksum']?>">[libgen]</a>
						  	<?php endif;?>
				     </p>
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Mimetype') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static"><?=$mimeType?></p>
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Item ref') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static"><?=$row['item']?> </p>
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Newer version of') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static">
					     <?php if (!empty($row['replaces'])): ?>
							<a href="/prepo/edit_bitstream?bid=<?=$row['replaces']?>">bitstream: <?=$row['replaces']?></a>
						<?php endif ?>
				     </p>
			   </div>
			  </div>

				<div class="form-group col-sm-12 bitsr">
					<label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Pages') ?>:</label>
				    <div class="<?php echo $inputwidth; ?>">
					     <p class="form-control-static"><?=$row['pages']?></p>
				   </div>
				</div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Source') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static"><?=$row['source']?></p>
			   </div>
			  </div>

			   <div class="form-group col-sm-12 bitsr">
			    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Create dt') ?>:</label>
			    <div class="<?php echo $inputwidth; ?>">
				     <p class="form-control-static"><?=$row['create_dt']?></p>
			   </div>
			  </div>

				<?php if (PUtil::strContains($mimeType, "png")||PUtil::strContains($mimeType, "jpeg")):	?>
				   <div class="form-group col-sm-12 bitsr">
				    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Info') ?>:</label>
				    <div class="<?php echo $inputwidth; ?>">
					     <p class="form-control-static">
							<pre>
							<?php
							$filename  = PUtil::bitream2filename($internal_id);
							//$out =PUtil::identify_image($filename);
							$out =PUtil::identify_image_to_string($filename);
							echo($out);

							//print_r($out);

							?>
							</pre>
					     </p>
				   </div>
				  </div>
				<?php endif;?>

				<?php if (PUtil::strContains($mimeType, "pdf")): ?>
				   <div class="form-group col-sm-12 bitsr">
				    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Info') ?>:</label>
				    <div class="<?php echo $inputwidth; ?>">
					     <p class="form-control-static">
							<pre>
								<?php
								$filename  = PUtil::bitream2filename($internal_id);
								$out = PUtil::pdf_info_meta($filename);
								echo($out);
								?>
							</pre>
							<pre>
								<?php
								$out = PUtil::pdf_pdftk_data($filename);
								echo($out);
								?>
							</pre>
					     </p>
				   </div>
				  </div>
				<?php endif;?>

				<?php if (!empty($symlinks)): ?>
				   <div class="form-group col-sm-12 bitsr">
				    <label class="<?php echo $labelwidth; ?> control-label"><?php echo tr('Symlinks') ?>:</label>
				    <div class="<?php echo $inputwidth; ?>">
						<table class="table table-condensed">
						<thead>
							<tr>
							<th>weight</th>
							<th>budnle</th>
							<th>item</th>
							<th></th>
						</tr>
						</thead>
						<?php
						foreach ($symlinks as $s) {
							printf('<tr><td>%s</td> <td>%s</td> <td><a href="/archive/item/%s">%s</a></td>  <td>[edit]</td></tr>',$s['bb_weight'],$s['bundle'],$s['item_id'],$s['item_title']);
						}
						?>
						</table>
			   	   </div>
				  </div>
				<?php endif; ?>

			  <div class="form-group col-sm-12 bitsr bit_br">
			  	<div class="col-sm-10 col-sm-offset-2">
			  	<button name="update" value="update" class="btn btn-default bitstream-btn"><?php echo tr('Update') ?></button>
			  	<button name="delete" value="delete" class="btn btn-default bitstream-bt-delete"  onClick="return confirm('DELETE ARE YOU SURE ?')"><?php echo tr('Delete') ?></button>
					<button name="generate_item_thumbnails" value="generate_item_thumbnails" onClick="return confirm('OVERIGHT ITEM THUMBNAILS, ARE YOU SURE ?')" class="btn btn-default bitstream-btn"><?php echo tr('Generate item thumbnails') ?></button>
					<button name="generate_bitstream_thumbnails" value="generate_bitstream_thumbnails" onClick="return confirm('OVERIGHT BITSTREAM THUMBNAILS, ARE YOU SURE ?')" class="btn btn-default bitstream-btn"><?php echo tr('Generate bitstream thumbnails') ?></button>
			 	</div>
			 </div>

			</form>
		</div>
	</div>



<hr/>

	<div class="panel panel-primary">

	<div class="a_thead a_bitstream"> More actions </div>
		<div class="panel-body bitpanel">

			<form method="POST" class="form-inline bit_br">
				<input type="hidden" name="item_id" value="<?=$item_id ?>"/>
				<div class="form-group">
			  		<label for="width">Width:</label>
			    	<input type="text" class="form-control" name="width" value="" size="3"/>
			  	</div>
			  <div class="form-group">
			  		<label for="height">Heiht:</label>
			    	<input type="text" class="form-control" name="height" value="" size="3"/>
			  </div>
			  <div class="form-group">
			  		<label for="height">Ext:</label>
			 		<input type="text" name="extension" class="form-control" value="png" size="4"/>
			  </div>
			  <div class="form-group">
			  		<label for="height">Page:</label>
			 		<input type="text" name="page" class="form-control" value="0" size="4"/>
			  </div>
			  <button class="btn btn-default bitstream-btn" name="generate_custom_thumbnail" value="generate_custom_thumbnail" onClick="return confirm('<?php echo tr('ARE YOU SURE?') ?>')"><?php echo tr('Generate custom thumbnail') ?></button>
			 </form>


			<form method="POST" class="form-inline bit_br">
				<input type="hidden" name="bitstream_id" value="<?= $id ?>"/>
				<div class="form-group">
			  		<label for="item_id">Item id:</label>
			 		<input type="text" class="form-control" name="item_id" value="" size="6"/>
			  	</div>
				<div class="form-group">
			  		<label for="weight">Weight:</label>
					<input type="text" class="form-control" name="weight" value="" size="3"/>
			  	</div>
				<div class="form-group">
			  		<label for="bundle_name">Bundle:</label>
					<?php PUtil::print_select("bundle_name","id2_select_bundle_name",$BUNDLE_MAP, 'ORIGINAL',false,false,"form-control"); ?>
			  	</div>
			  	<button name="create_symlink" class="btn btn-default bitstream-btn" value="create_symlink" onClick="return confirm('<?php echo tr('ARE YOU SURE?') ?>')"><?php echo tr('Create symlink') ?></button>
			</form>


			<?php
			$bundle_name = $row['bundle_name'];
			if ($bundle_name != 'OLD'):
				// $action=null;
				// $display_bundle = false;
				// $display_seq_id = false;
				// $file_prefix = $item_id . '_' . $seq_id . '_';
				// //include 'bitstream_upload_form.php';

			?>

			<form method="POST" class="form-inline" id="form_file" enctype="multipart/form-data" >
				<div class="form-group">
<!-- 			  		<label for="uploadedfile[]">Upload:</label> -->
<!-- 					<input name="uploadedfile[]" type="file" class="form-control" /> -->
<!-- 			   	</div> -->
<!-- 				<input type="submit" name="send_file" value="Send" class="btn btn-default"> -->
				<div class="form-group">
							<div class="fileUpload">
									<input id="uploadFile" name="uploadedfile[]" placeholder="Choose File"  type="file" multiple  />
							</div>
							<div class="fileUpload uploadbut">
									<span>Upload</span>
									<input id="uploadBtn" class="upload" type="submit" value="Send" name="send_file"/>
							</div>
				</div>
			</form>


		</div>
	</div>

</div> <!-- end row -->


<hr/>

</div>
<?php endif; ?>


<?php
//print_r($thumbs);
//dump_data($id);
?>
@stop