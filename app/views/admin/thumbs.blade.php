@section('content')
<?php auth_check_mentainer(); ?>

<?php
//error_log(auth_check_all());

$item_id  = get_get("i");

if (empty($item_id)){
	echo("expected item");
	return;
}


$MEDIA_TYPE_MAP = Lookup::get_media_type_values();

$dbh = dbconnect();


if (! empty($_POST)){

	$ok = false;
	$idx = isset( $_POST['idx'] )? $_POST['idx'] : null;
	if (empty($idx) && $idx <> 0){
		echo("<p>IDX EXPECTED</p>");
		$ok = false;
	} else {
		$ok = true;
	}

	if ( $ok && preg_match('/^\d+$/', $idx)) {
		$ok = true;
	} else{
		echo("<p>IDX is not number</p>");
		$ok = false;
	}


	//$isize = 'small';
	$ttype = isset($_POST['ttype']) ? $_POST['ttype'] : 1;
	//if ($ttype == 2){
	//	$isize = 'big';
	//}
	$auto_gen = 0;

	$last = isset($_POST['last']) ? true : false;


	if ($ok){

		$app= App::make('arc');
		$upload_file_data = array();
		$upload_files = $app->upload_files;
		if (isset($upload_files['uploadedfile'])){
			$files = $upload_files['uploadedfile'];
			print_r($files);
		if (isset($files[0])){
			$f=$files[0];
			if (! empty($f['extension'])){
								$idxf = $last ? 'l' : $idx;
								$file_path = $f['tmp_name'];
									$out = "";
									PDao::thumbnail_add($dbh, $file_path, $f['extension'],  $item_id, $idx, $idxf, $ttype, $auto_gen, $out);
			}
		}
	}

// 		(
// 		[name] => ubuntu-wallpapers-12.png
// 		[tmp_name] => /tmp/archive_fu_5476eef30583f
// 		[size] => 919512
// 		[type] => image/png
// 		[extension] => png
// 		)

// 		$uploadData = isset($_FILES['uploadedfile'])?$_FILES['uploadedfile'] : null ;
// 		if (! empty( $uploadData)){
// 			$type = $uploadData['type']	;
// 			if ( preg_match('/image/', $type)) {
// 				$fileName = $uploadData['name'];
// 				preg_match('/\.(\w+)$/', $fileName, $matches);
// 				#print_r($matches);
// 				$ext = $matches[1];
// 				if (! empty($ext)){
// 					$idxf = $last ? 'l' : $idx;
// 					$file_path = $uploadData['tmp_name'];
// 					if (is_uploaded_file($file_path)){
// 						$out = "";
// 						PDao::thumbnail_add($dbh, $file_path, $ext,  $item_id, $idx, $idxf, $ttype, $auto_gen, $out);
// 					}
// 				}
// 			}
// 		}
	}
}
// 					$tdir = rand(100,399);
// 					$cmd = 'mkdir ' . THUMBNAIL_DIR  . $tdir;
// 					$tmp = exec($cmd);
// 					$thumbfile = $tdir . '/th_' . $item_id . '_' . f . '_' . $isize . "." . $ext;
// 					$full_path = THUMBNAIL_DIR . $thumbfile;
// 					move_uploaded_file($uploadData['tmp_name'], $full_path);
// 					if (file_exists($full_path)){
// 						$SQL = "DELETE FROM dsd.thumbs WHERE item_id = ? AND idx = ?";
// 						$stmt = $dbh->prepare($SQL);
// 						$stmt->bindParam(1, $item_id);
// 						$stmt->bindParam(2, $idx);
// 						$stmt->execute();
// 						$SQL = "insert into dsd.thumbs (item_id,file,idx,idxf,ttype) values (?,?,?,?,?)";
// 						$stmt = $dbh->prepare($SQL);
// 						$stmt->bindParam(1, $item_id);
// 						$stmt->bindParam(2, $thumbfile);
// 						$stmt->bindParam(3, $idx);
// 						$stmt->bindParam(4, $idxf);
// 						$stmt->bindParam(5, $ttype);
// 						$stmt->execute();



$item_label = PDao::item_get_label($item_id);
printf('<a href="/prepo/edit_step2?i=%s">[back to item:(%s)]</a>',$item_id,$item_label);
// printf('<a href="/prepo/edit_bitstream?bid=%s">[back to item:(%s)]</a>',$item_id,$item_label);




#THUMBNAIL_DIR

$SQL="SELECT id, item_id, file, idx, idxf, ttype, auto_gen from dsd.thumbs where item_id = ? ORDER BY idx, ttype  ";
$stmt = $dbh->prepare($SQL);
$stmt->bindParam(1, $item_id);
$stmt->execute();
$r = $stmt->fetchAll();


?>

<h1 class="admin item-title bitstream">Edit Thumbs</h1>

<?php


echo("<table>");
echo("<thead><tr>");

echo("<th>id</th>");
echo("<th>idx</th>");
echo("<th>last</th>");
echo("<th>type</th>");
echo("<th>img</th>");
echo("<th>info</th>");
echo("<th>auto</th>");
echo('<th>actions</th>');
echo("</tr></thead>\n");

foreach($r as $key => $row){
	$type = "small";
	$ttt = $row['ttype'];
	if ($ttt == 2){
		$type = "big";
	} elseif ($ttt == 3){
		$type = "icon_small";
	}elseif ($ttt == 4){
		$type = "icon_big";
	}elseif ($ttt == 5){
		$type = "max";
	}elseif ($ttt == 10){
		$type = "custom";
	}

	$auto = "custom";
	if ($row[6] == 1){
		$auto = "auto";
	}
	echo("<tr>\n");
	printf('<td>%s</td>',$row[0]);
	printf('<td>%s</td>',$row[3]);
	$last = $row[4] == 'l' ? true : false;
	$lastStr = $last ? "last" : "";
	printf('<td>%s</td>',$lastStr);
	printf('<td>%s</td>',$type);
	echo("<td>");
	printf('<a href="/media/%s"><img style="max-width: 180px;max-height: 180px;" src="/media/%s"/>',$row[2],$row[2]);
	echo("</td>");
	echo("<td>");
	echo("<pre>");
	echo(PUtil::identify_image_to_string(Config::get('arc.THUMBNAIL_DIR') . $row[2]));
	echo("</pre>");
	echo("</td>");
	printf('<td>%s</td>',$auto);
	printf('<td><a href="/prepo/delete_thumb?i=%s&ttype=%s&tid=%s">[delete]</a></td>',$row[1],$row['ttype'],$row['0']);
	echo("\n</tr>\n");
}
echo("</table>");


?>


<div class="ttools">

<FORM method="POST" enctype="multipart/form-data" action="?i=<?php echo($item_id)?>">

<?php PUtil::print_select("ttype","select_ttype",$MEDIA_TYPE_MAP, null,false); ?>
<?php
// <select name="ttype">
//   <option value="1">small</option>
//   <option value="2">big</option>
//   <option value="3">icon_small</option>
//   <option value="4">icon_big</option>
//   <option value="5">max</option>
// </select>
?>
<br/>
idx:<input type="text" name="idx" value="0" size="4"> last:<INPUT TYPE=CHECKBOX NAME="last"/>
<br/>
image upload:
<input   name="uploadedfile[]"  type="file" />
<br/>
<input type="submit" value="send">
</form>
</div>
<table border="0">
<tr>
<td>
e3ofilo (idx = 0)
</td>
<td>
selides (idx &gt; 0)
</td>
</tr>
<tr>
<td>
<ul>
<li> icon_small:  65X  </li>
<li> icon_big:  110X   </li>
<li> small:  200X</li>
<li> big:  600X</li>
</ul>
</td>
<td>
<ul>
<li> small:  120X</li>
<li> big:  600X</li>
</ul>
</td>
</tr>

</table>

@stop