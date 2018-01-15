@section('content')
<?php auth_check_mentainer(); ?>

<?php

$o = isset($_GET['o']) ? $_GET['o'] : null;
$o = ! empty($o) ? $o : 1;
$d = isset($_GET['d']) ? $_GET['d'] : null;
$d = ! empty($d) ? $d : 1;

$cd = get_get("cd", "");

$user = ArcApp::user();
//print_r($user);
$organization = null;
if (!empty($user)){
	$organization = $user['org_id'];
}
// $direct_upload_flag = get_post("direct",0);


$demo_enable = Config::get('arc.demo_enable',0);


$uploadData = null;
// if (isset($_FILES['uploadedfile'])){
// $uploadData = $_FILES['uploadedfile'];
// }

$upload_file_data = array();
$app = App::make('arc');
$tmp = $app->upload_files;
if (isset($tmp['uploadedfile'])) {
	$upload_file_data = $tmp['uploadedfile'];
}

// echo ("<pre>");
// print_r($upload_file_data);
// echo ("</pre>");

$handle_file = function ($file_data) {
	$message = array();
	$fileName = $file_data['name'];
	$fileName = str_replace(" ", "_", $fileName);
	$fileName = str_replace(",", "_", $fileName);
	$fileName = str_replace("/", "_", $fileName);
	$fileName = str_replace("'", "_", $fileName);
	$fileName = str_replace("(", "_", $fileName);
	$fileName = str_replace(")", "_", $fileName);
	$fileName = strtolower($fileName);

	$ext = $file_data['extension'];
	$tmp_name = $file_data['tmp_name'];

	$digital_item_type =  PUtil::digital_item_type($file_data['name']);

// 	if (! empty($ext) && ($ext == "pdf" || $ext == "png" || $ext == "jpg" || $ext == "jpeg" || $ext == "cbr" || $ext == "djvu")) {
	if (!empty($ext) && !is_null($digital_item_type)) {

		//$target = $dir_append_org_id(Config::get('arc.SPOOL_dir_pending')) . $fileName;
		$target = SpoolUtil::getPendigSpoolDir() . $fileName;
		//Log::info($target);
		$fileName = htmlspecialchars($fileName);

		$status = 0;
		if (! file_exists($target)) {
			//$rep = move_uploaded_file($tmp_name, $target);
			$rep = rename($tmp_name, $target);
			if ($rep) {
				$status = 1;
				$message=  "$fileName  uploaded";
			} else {
				$status = 2;
				$message=   "ERROR:  $fileName  NOT uploaded (check php max upload size)";
			}
		} else {
			$status = 3;
			$message=   "Upload Error: $fileName file with same name exist";
		}
	} else {
		$status = 4;
		 $message =   "Upload Error: $fileName unkown file type";
	}
	return array('status'=>$status, 'message'=>$message);
};

 if (! empty( $upload_file_data)){
	 echo('<div class="ttools"><ul class="msg_ul"> ');
	 foreach ($upload_file_data as $f){
	 	$rep = $handle_file($f);
	 	$status = $rep['status'];
	 	$message= $rep['message'];
	 	if ($status == 1){
			echo("<li class='valid_msg'>$message</li>");
	 	} else {
	 		printf('<li class="error_msg">%s</li>',$message);
	 	}
	 }
	 echo("</ul></div>");
 }
// //
// if (! empty( $uploadData)){
// echo('<div class="ttools">');
// //$type = $uploadData['type'] ;
// //if ( preg_match('/image/', $type)) {
// $fileName = $uploadData['name'];
// $fileName = str_replace(" ", "_",$fileName);
// $fileName = str_replace(",", "_",$fileName);
// $fileName = str_replace("/", "_",$fileName);
// $fileName = str_replace("'", "_",$fileName);
// $fileName = str_replace("(", "_",$fileName);
// $fileName = str_replace(")", "_",$fileName);
// $fileName = strtolower($fileName);
// preg_match('/\.(\w+)$/', $fileName, $matches);
// $ext = $matches[1];
// // echo("<pre>");
// // print_r($matches);
// // echo("\n ext: ($ext) \n");
// // echo("</pre>");
// if (! empty($ext) && ($ext == "pdf" || $ext=="png" || $ext=="jpg" || $ext=="jpeg" || $ext=="cbr" || $ext=="djvu")){
// $target = Config::get('arc.SPOOL_dir_pending') . $fileName;
// if (! file_exists($target)) {
// $rep = move_uploaded_file($uploadData['tmp_name'], $target);
// if ($rep){
// echo("<h2>$fileName <br/> uploaded<h2>");
// }else{
// echo("<h2>ERROR: $fileName <br/> NOT uploaded (check php max upload size)<h2>");
// }
// if ($direct_upload_flag){

// //$file_path = empty($cd) ? urlencode($fileName) : urlencode($cd) . "/" . urlencode($fileName);
// //$url = sprintf('/prepo/edit_step1?edoc=/docs/%s&direct=1',$file_path);
// //drupal_add_http_header('Location' ,$url) ;
// //return;
// }
// //echo("<h2>$fileName <br/> uploaded<h2>");
// } else {
// echo("<h2>Upload Error: file with same name exist<h2>");
// }

// } else {
// echo("<h2>upload Error: unkown file type<h2>");
// }
// //}
// echo("</div>");

// }

 if (!empty($delete_msg)){
 echo("<div class='valid_msg'>$delete_msg</div>");
 }

$title  = '';
if (!empty($organization)){
	$title .= $user['org_name'];
	$title .= ': ';
}

if ($d == 1) {
	$title .= ' Pending spool';
	//drupal_set_title("SPOOL");
} else {
	$title .= 'Submited spool';
	//drupal_set_title("OK");

}
// printf('<h3>%s</h3>',$title);

printf('<h1 class="admin item-title spool">%s</h1>',$title);

echo '<div class="ttools">';
echo '<a href="?o=1&d='.$d.'"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Order 1</a>'; //glyphicon-time
echo '&nbsp;&nbsp;&nbsp;';
echo '<a href="?o=2&d='.$d.'"><span class="glyphicon glyphicon-sort-by-alphabet" aria-hidden="true"></span> Order 2</a>';
echo '&nbsp;&nbsp;&nbsp;';

if ($d == 1) {
	echo '<a href="?o=1&d=2"><span class="glyphicon glyphicon-folder-close" aria-hidden="true"></span> Submited spool</a>';
} else {
	echo '<a href="?o=1&d=1"><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> Pending spool</a>';
}
echo "</div>";

echo "\n<br/>\n";

?>


<div class="panel panel-primary">
	<div class="a_thead a_files"> Spool </div>
	<div class="panel-files panel-body">

<table id="fileTable" class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Files</th>
			<th class="cnum">Size</th>
			<th class="cnum">Actions</th>
		</tr>
	</thead>
	<tbody>
<?php
$dirs = array();

$is_admin = ArcApp::user_access_admin();
// $is_admin = false;

if ($d == 1) {
	$urlpath = Config::get('arc.SPOOL_url_prefix_pending');
	$directory = $is_admin ? Config::get('arc.SPOOL_dir_pending') : SpoolUtil::getPendigSpoolDir();
// 	$directory = Config::get('arc.SPOOL_dir_pending');
// 	$directory = SpoolUtil::getPendigSpoolDir();
	$movephp = "/prepo/move?d=1&f=";
} else {
	$urlpath = Config::get('arc.SPOOL_url_prefix_ok');
	$directory = $is_admin ? Config::get('arc.SPOOL_dir_ok') : SpoolUtil::getOKSpoolDir();
// 	$directory = Config::get('arc.SPOOL_dir_ok');
// 	$directory = SpoolUtil::getOKSpoolDir();
	$movephp = "/prepo/move?d=2&f=";
}

$mtimes = array();
$sizes = array();
$filenames = array();
$file_info = array();
$file_infos = array();

//$directory = $dir_append_org_id($directory);
//echo $directory;

$directory = $directory . $cd;
//$directory = rtrim($directory, "/");

$iterator = new DirectoryIterator($directory);
foreach ( $iterator as $fileinfo ) {
	if ($fileinfo->isFile()) {
		//echo "<pre>"; print_r($fileinfo); echo "</pre>";
		$filenames[] = $fileinfo->getFilename();
		$mtimes[] = $fileinfo->getMTime();
		$sizes[$fileinfo->getFilename()] = round($fileinfo->getSize() / 1000000, 2);
		$file_info['path'] = $fileinfo->getPathname();
		$file_info['name'] = $fileinfo->getFilename();
		$file_infos[]= $file_info;

	} elseif ($fileinfo->isDir() && empty($cd)) {
		$fn = $fileinfo->getFilename();
		if ($fn != "." && $fn != "..") {
			$dirs[] = $fn;
		}
	}
}

if ($o == 1) {
	array_multisort($mtimes, SORT_DESC, $filenames);
} else {
// 	sort($filenames);
	rsort($filenames);
}

$i = 0;
if (sizeof($filenames) > 0) {
	// 	foreach ( $filenames as $file ) {
	foreach ( $file_infos as $index => $infos) {
		$file = $infos['name'];
		$file_path = empty($cd) ? urlencode($file) : urlencode($cd) . "/" . urlencode($file);
		$file_path_op = ($is_admin) ?  $file_path : SpoolUtil::getOrganizationPrefix().'/'.$file_path;
// 		$file_path_op = SpoolUtil::getOrganizationPrefix() . '/' . $file_path;

		$size = $sizes[$file];
		$path = $urlpath . $file_path_op;
// 		$move = $movephp . $file_path;
		$move = ($is_admin) ?  $movephp . $file_path.'&org=/' : $movephp . $file_path ;

		$url = $path;
		$i ++;

		// $filedisp = substr($file,0,50);
		$filedisp = $file;

		$class_filename_ref = null;
		$location_folder = null;
		if($is_admin){
		$org_id= 'org_' . $organization;
		$class_filename_ref = ($org_id != 'org_') ?  'class="other_owner"' : null  ;
		$location_folder = ($org_id != 'org_') ? '(root)' : "(mine :: root)" ;
		}

		echo "<tr>";
		echo "<td><a $class_filename_ref href=\"$path\">$filedisp</a> $location_folder</td>\n";
		echo "<td class=\"cnum\" align=\"right\">${size}&#160;MB</td>\n";

		if (!$demo_enable || $is_admin) {
			if ($d == 1) {
				printf('<td class="cnum"><form class="form-horizontal" method = "post">
					<a class="action-btn" href="/prepo/edit_step1?edoc=/docs/%s">Submit</a>
					<button name="delete" value="delete" class="action-btn delete"  onClick="return confirm(\'%s\')">Delete</button>
					<input type="hidden" name="file_path" value="%s"/>
		 			<input type="hidden" name="file_name" value="%s"/></form></td>', $file_path_op, $file.tr(' will be Deleted! Are you sure?'), $infos['path'], $file);
			}else{
	// 			echo "<td><a href=\"$move\">[move to spool]</a> &nbsp; </td>\n";
				printf('<td class="cnum"><form class="form-horizontal" method = "post">
					<a class="action-btn" href="%s">Move to spool</a>
					<button name="delete" value="delete" class="action-btn delete"  onClick="return confirm(\'%s\')">Delete</button>
					<input type="hidden" name="file_path" value="%s"/>
					<input type="hidden" name="file_name" value="%s"/></form></td>', $move, $file.tr(' will be Deleted! Are you sure?'), $infos['path'], $file);
			}
		}else{
			if ($d == 1) {
				printf('<td class="cnum"><form class="form-horizontal" method = "post">
					<a class="action-btn" href="/prepo/edit_step1?edoc=/docs/%s">Submit</a>
					<input type="hidden" name="file_path" value="%s"/>
		 			<input type="hidden" name="file_name" value="%s"/></form></td>', $file_path_op, $infos['path'], $file);
			}else{
				printf('<td class="cnum"><form class="form-horizontal" method = "post">
					<a class="action-btn" href="%s">Move to spool</a>
					<input type="hidden" name="file_path" value="%s"/>
					<input type="hidden" name="file_name" value="%s"/></form></td>', $move, $infos['path'], $file);
			}
		}


			// echo "<td> <img src=\"/prepo/_assets/img/page_white_copy.png\" alt=\"copy to clipboard\" title=\"Copy to Clipboard\" /> </td>\n";
		echo "</tr>\n";
	}
}


######### Subdirectories #########
if($is_admin){
	asort($dirs);
	foreach ( $dirs as $dir ) {
		$mtimes_sub = array();
		$sizes_sub = array();
		$filenames_sub = array();
		$file_info_sub = array();
		$file_infos_sub= array();

		$sub_directory = ($d == 1) ? Config::get('arc.SPOOL_dir_pending').$dir : Config::get('arc.SPOOL_dir_ok').$dir;
		$sub_directory  = str_replace('//', '/',$sub_directory);

		$iterator = new DirectoryIterator($sub_directory);
		foreach ( $iterator as $fileinfo ) {
			if ($fileinfo->isFile()) {
				$filenames_sub[] = $fileinfo->getFilename();
				$mtimes_sub[] = $fileinfo->getMTime();
				$sizes_sub[$fileinfo->getFilename()] = round($fileinfo->getSize() / 1000000, 2);
				$file_info_sub['path'] = $fileinfo->getPathname();
				$file_info_sub['name'] = $fileinfo->getFilename();
				$file_infos_sub[]= $file_info_sub;
			}
		}

		if ($o == 1) {
			array_multisort($mtimes_sub, SORT_DESC, $filenames_sub);
		} else {
			// 	sort($filenames);
			rsort($filenames_sub);
		}

		$i = 0;
		if (sizeof($filenames_sub) > 0) {
			foreach ( $file_infos_sub as $index => $infos) {
				$file = $infos['name'];
				$file_path = empty($cd) ? urlencode($file) : urlencode($cd) . "/" . urlencode($file);
				$file_path_op = $dir . '/' . $file_path;
				$size = $sizes_sub[$file];
				$path = $urlpath . $file_path_op;
				$move = $movephp . $file_path.'&org='.$dir;

				$url = $path;
				$i ++;
				$filedisp = $file;

				$org_id= 'org_' . $organization;
				$class_filename_ref = ($org_id != $dir) ?  'class="other_owner"' : null  ;
				$location_folder = ($org_id != $dir) ? "($dir)" : "(mine :: $dir)" ;

				echo "<tr>";
				echo "<td><a $class_filename_ref href=\"$path\">$filedisp</a> $location_folder</td>\n";
				echo "<td class=\"cnum\" align=\"right\">${size}&#160;MB</td>\n";
				if ($d == 1) {
					printf('<td class="cnum"><form class="form-horizontal" method = "post">
				<a class="action-btn" href="/prepo/edit_step1?edoc=/docs/%s">Submit</a>
				<button name="delete" value="delete" class="action-btn delete"  onClick="return confirm(\'%s\')">Delete</button>
				<input type="hidden" name="file_path" value="%s"/>
	 			<input type="hidden" name="file_name" value="%s"/></form></td>', $file_path_op, $file.tr(' will be Deleted! Are you sure?'), $infos['path'], $file);
				}else{
					printf('<td class="cnum"><form class="form-horizontal" method = "post">
				<a class="action-btn" href="%s">Move to spool</a>
				<button name="delete" value="delete" class="action-btn delete"  onClick="return confirm(\'%s\')">Delete</button>
				<input type="hidden" name="file_path" value="%s"/>
				<input type="hidden" name="file_name" value="%s"/></form></td>', $move, $file.tr(' will be Deleted! Are you sure?'), $infos['path'], $file);
				}
				echo "</tr>\n";
			}
		}
	}
}
##################################




if(!empty(Config::get('arc.SPOOL_DISPLAY_DIRS'))){
	asort($dirs);
	foreach ( $dirs as $dir ) {
		echo ("<tr>");
		echo ('<td colspan="2">');
		printf('<a href="/prepo/spool?cd=%s">%s</a>', urlencode($dir), $dir);
		echo ("</td>");
		echo ("<td>");
		printf('<a href="/prepo/edit_step1?cd=/docs/%s">[submit]</a>', urlencode($dir), $dir);
		echo ("</td>");
		echo ("</tr>\n");
	}
}

echo " </tbody></table>\n";

echo " </div> </div>";
// echo "\n<br/>\n";

?>
<?php 	if ($d == 1) : ?>


	@if (!$demo_enable || $is_admin)
		<div class="panel panel-primary">
			<div class="a_thead a_bitstream"> File Upload</div>
			<div class="panel-upload panel-body">
				<form method="POST" enctype="multipart/form-data">
					<div class="fileUpload">
							<input id="uploadFile" name="uploadedfile[]" placeholder="Choose File"  type="file" multiple  />
					</div>
					<div class="fileUpload uploadbut">
							<span>Upload</span>
							<input id="uploadBtn" class="upload" type="submit" value="send_file"/>
					</div>
				</form>
			</div>
		</div>
	@else
		<div class="panel panel-primary">
		  <div class="a_thead a_bitstream">
			 <?php echo tr(' File Upload');?>
		  </div>
		  <div class="panel-body bitstream demo">
				Upload functionality has been disabled for this demo account for security reasons.
				<br>Please get in contact with the admins (<a href="mailto:info@reasonablegraph.org">info@reasonablegraph.org</a>) to provide you with an account with full rights.
		  </div>
		</div>
	@endif

<?php
endif
?>

<?php
echo '<div class="ttools">';
echo '<a href="?o=1&d='.$d.'"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Order 1</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a href="?o=2&d='.$d.'"><span class="glyphicon glyphicon-sort-by-alphabet" aria-hidden="true"></span> Order 2</a>';
echo '&nbsp;&nbsp;&nbsp;';
if ($d == 1) {
	echo '<a href="?o=1&d=2"><span class="glyphicon glyphicon-folder-close" aria-hidden="true"></span> Submited spool</a>';
} else {
	echo '<a href="?o=1&d=1"><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> Pending spool</a>';
}
echo "</div>";
?>


@stop
