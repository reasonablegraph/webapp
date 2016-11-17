@section('content')
<?php auth_check_mentainer(); ?>
<?php


	$cd = get_get("cd","");

	/*
    $d = isset($_GET['d'])? $_GET['d']  : null;
	$d = !empty($d) ? $d :1;

	if ($d == 1){
		drupal_set_title("sites SPOOL");
	} else {
		drupal_set_title("sites OK");
	}
	*/
	
	
	$urlpath =  Config::get('arc.SPOOL_url_prefix_sites_pending');
	$base_directory = Config::get('arc.SPOOL_dir_sites_pending');
	
	$title = "sites SPOOL:" . $cd;
	drupal_set_title($title);
	

	echo "<div class=\"ttools\">\n";

	echo '&nbsp;&nbsp;&nbsp;';
	echo "[<a href=\"/prepo/sites/spool\">SPOOL</a>]";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "[<a href=\"/prepo/menu\">MENU</a>]";
	echo "</div>";


    echo "\n<br/>\n";



?>

<table id="fileTable">
     <thead>
    </thead>
     <tbody>
<?php

	
	////$sizes=array();
    $filenames = array();
   // $iterator = new DirectoryIterator($directory);
    
    $dirs=array();
    $mtimes=array();
	$sizes=array();
    $filenames = array();
    $directory = $base_directory . "$cd";
    $iterator = new DirectoryIterator($directory);
    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile()) {
        	//echo "<pre>"; print_r($fileinfo); echo "</pre>";
            //$filenames[$fileinfo->getFilename()] = $fileinfo->getMTime();
           // $sizes[$fileinfo->getFilename()] =  round($fileinfo->getSize()/1000000,2);

        	$filenames[] = $fileinfo->getFilename();
        	$mtimes[] =$fileinfo->getMTime();
            $sizes[$fileinfo->getFilename()] =  round($fileinfo->getSize()/1000000,2);
        } elseif ($fileinfo->isDir() && empty($cd)){
        	$fn = $fileinfo->getFilename();
        	if ($fn != "." && $fn != ".."){
        		$dirs[] = $fn;
        	}
        }
        
        
        
    }
	
    
    $lines = ARRAY();
    //arsort($filenames);
    array_multisort($mtimes,SORT_DESC,$filenames);
    if(sizeof($filenames)>0){
	foreach ($filenames as $file) {
			$m = preg_match('/^site_(\d+)\.(jpg|html|png|txt)$/', $file, $matches);
			#print_r($matches);
			if ($m){
				$base = $matches[1];
				$ext = $matches[2];
				$lines[$base][$ext] = $file;
			} 
		#	else {
		#		echo("<p>ignore filename: $file</p>");
		#	}
	        #$filedisp = substr($file,0,50);
# 			$filedisp = $file;
#		        echo "<tr>";
#	        	echo "<td> &nbsp; </td>\n";
#	        	echo "<td><a href=\"$path\">$filedisp</a></td>\n";
#	        	echo "<td class=\"num\" align=\"right\">${size}&#160;MB</td>\n";
#	        	//echo "<td>[submit]</td>";
#	        	printf('<td><a href="/prepo/edit_step1?edoc=/sites/%s">[submit]</a></td>',urlencode($file));
#	        	echo "</tr>\n";
        }


#		echo("<pre>");
#		print_r($lines);
#		echo("</pre>");

        $i=0;
		foreach ($lines as $base => $files){
	        $i++;
			echo("<tr>\n");
			echo("<td>");
			$coma = "";
			ksort($files);
			$imageFile = null;
			$imageFilePath = null;
			$size = null;
			foreach($files as $ext => $f){
				if ($ext == "jpg" || $ext == "png") {
					$size = $sizes["$f"];
					$imageFile = $f;
					$imageFilePath = empty($cd) ? $imageFile : $cd . "/" . $imageFile;
				}
	        	$path = $urlpath . (empty($cd) ? urlencode($f) : urlencode($cd) . "/" . urlencode($f));
	        	$url =  $path;
	 			echo ($coma);
	 			printf('<a href="%s" target="new" >%s</a>',$url,$f);
	 			$coma =", ";
	 			$textFile = null;
				$user = null;
				$label = null;
				if ($ext == 'txt'){
						$textFile = (empty($cd) ? $f : $cd . "/" . $f);
						$edoc_file = Config::get('arc.SPOOL_dir_sites_pending') .  	$textFile;
						#$cmd = "cat $edoc_file|head -1 ";
						#$cmd_out = array(); $status = 0;
						#$tmp = exec($cmd,$cmd_out,$status);
						#$site_date_captured = $cmd_out[0];
						
						$txt_file = array(); 
	  				    $file_handle = fopen($edoc_file, "rb");
						$k = null;
						$c = 0;
					    while (!feof($file_handle)  && $c <= 11 ) {
							$line_of_text = fgets($file_handle);
							$p = explode(':', $line_of_text,2);
							 //echo "$c: $line_of_text\n";
							 if ($c == 1){
							 	$site_url = $line_of_text;
							 }
							 if ($c >= 4 && count($p) == 2){
								$k = $p[0];
								$v = $p[1];
								$txt_file[$k] = $v;
							 }
							 $c ++;
						}
						fclose($file_handle);
						//echo("<pre>");
						//print_r($txt_file);
						//echo("</pre>");
						//
						$tags = "";
						if (! empty($txt_file['tag1'])){
							$tags .= $txt_file['tag1'] . " ";
						}
						if (! empty($txt_file['tag2'])){
							$tags .= $txt_file['tag2'] . " ";
						}
						if (! empty($txt_file['tag3'])){
							$tags .= $txt_file['tag3'] . " ";
						}
						if (! empty($txt_file['tag4'])){
							$tags .=  $txt_file['tag4'] . " ";
						}
						if (! empty($txt_file['tag5'])){
							$tags .=   $txt_file['tag5'] . " ";
						}
						
						if (isset($txt_file['user'])){
							$user = $txt_file['user'];
						}
						if (isset($txt_file['label'])){
							$label = $txt_file['label'];
						}
						
						if (! empty($site_url)){
							$urlarr = parse_url($site_url);
							if (isset($urlarr['host'])){
								printf('&nbsp; (%s) ',$urlarr['host']);
							}
						}
				}
			}
			if (!empty($label)){
				echo "<br/>$label";
			}
			echo("</td>");
			echo "<td>${user}</td>\n";
			echo "<td>${tags}</td>\n";
			echo "<td class=\"num\" align=\"right\">${size}&#160;MB</td>\n";
			printf('<td><a href="/prepo/edit_step1?edoc=/sites/%s">[submit]</a></td>',urlencode($imageFilePath));
			if (empty($cd)){
				echo("<td>");
				printf('<a href="/prepo/sites/spool/replace_site_file?fn=%s">[replace img]</a>',urlencode($imageFilePath));
				echo("</td>");
				echo("<td>");
				if (!empty($textFile)){
					printf('<a href="/prepo/sites/spool/edit_info?fn=%s">[edit info]</a>',urlencode($textFile));
				}
				echo("</td>");
			} else {
				echo("<td>&nbsp;</td>");
			}
			echo("</td>");
			echo("</tr>\n");
		}
		
		asort($dirs);
	    foreach ($dirs as $dir) {
	    	
	    		echo("<tr>");
	    		echo('<td colspan="4">');
				printf('<a href="/prepo/sites/spool?cd=%s">%s</a>',urlencode($dir),$dir);
				echo("</td>");
				echo("</tr>\n");
	    }
	    



    }

    echo " </tbody></table>\n";


?>


<?php
    echo "\n<br/>\n";

    echo "<div class=\"ttools\">\n";
    
	echo '&nbsp;&nbsp;&nbsp;';
	echo "[<a href=\"/prepo/menu\">MENU</a>]";

	echo "</div>";

?>

@stop