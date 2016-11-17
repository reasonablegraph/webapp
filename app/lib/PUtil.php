<?php
use League\Url\UrlImmutable;
use Illuminate\Support\Facades\Log;

class PUtil {

	public static final function log($arg1){
		Log::info($arg1);
	}

	public static function opac1($json, $key){
		if ($json && isset($json['opac1']) && isset($json['opac1'][$key])){
			return $json['opac1'][$key];
		}
		return null;
	}

	public static function opac2($json, $key){

		if ($json && isset($json['opac2']) && isset($json['opac2'][$key])){
			return $json['opac2'][$key];
		}
		return null;
	}


/**
 *
 * @param unknown $trace
 */
	public static function logCaller($msg = null){
		$trace = debug_backtrace();
		//Log::info(print_r($trace,true));
		$caller0 = $trace[1];
		$caller1 = $trace[2];
		$str = '#TRACE:  '. $caller0['function'];
		$str .= " Called by {$caller1['function']}";
		if (isset($caller1['class'])){
			$str .=" in {$caller1['class']}";
		}
		if (!empty($msg)){
			$str.= ' : ' . $msg;
		}
		Log::info($str);
	}

	public static function isEmpty($value){
		if ($value === 0){
			return false;
		}
		if ($value === '0'){
			return false;
		}

		if ($value === ''){
			return true;
		}

		if ($value === false){
			return false;
		}

		return empty($value);
	}

	//@DocGroup(module="util", group="general", comment="test integer")
	public static function test_int($txt){
		if (preg_match('/^\d+$/', $txt)) {
			return true;
		}
		return false;
	}

	//@DocGroup(module="util", group="general", comment="reset_numeric")
	public static function reset_numeric($val,$default){
		$val = trim($val);
		if (empty($val)){
			return $default;
		}
		if (!is_numeric($val)){
			return $default;
		}
		return $val;
	}


	//@DocGroup(module="util", group="general", comment="reset_int")
	public static  function chk_int($val){
		if (preg_match("/^-?\d+$/", $val)) {
			return true;
		}
		return false;
	}


	//@DocGroup(module="util", group="general", comment="reset_int")
	public static  function reset_int($val,$default = null){
		$val = trim($val);
		if (empty($val)){
			return $default;
		}
		if (preg_match("/^-?\d+$/", $val)) {
			return $val;
		}
		return $default;
	}

	//@DocGroup(module="util", group="general", comment="reset_int")
	public static  function extract_int($val){
		$val = trim($val);
		if (empty($val)){
			return null;
		}
		if (preg_match("/(-?\d+)/", $val, $m)) {
			return intval($m[0]);
		}
		return null;
	}

	private static function coalesce2($arg1,$arg2=null){
		if (empty($arg1)) {return $arg2;};
		return $arg1;
	}

	/**
	 * @param array $args
	 * @param string $seperator
	 * @return string
	 */
	public static  function concatSeperator($seperator,$args){
		$ok = array();
		foreach ($args as $v) {
			if (!PUtil::isEmpty($v)){
				$ok[] = $v;
			}
		}
		return implode($seperator,$ok);
	}

	//@DocGroup(module="util", group="general", comment="coalesce 2")
	public static  function coalesce($arg1,$arg2=null, $arg3 = null){
		if (empty($arg1)) {return PUtil::coalesce2($arg2,$arg3);};
		return $arg1;
	}


	public static  function coalesceConcat($arg1,$arg2=null, $arg3 = null){
		$rep = '';
		if ($arg1 != null && $arg1 != ''){ $rep .= $arg1;};
		if ($arg2 != null && $arg2 != ''){ $rep .= $arg2;};
		if ($arg3 != null && $arg3 != ''){ $rep .= $arg3;};
		return $rep;
	}


	public static function getMapWithKeys($mapping, $keys) {
		$out = array();
		foreach($keys as $key) {
			if (isset($mapping[$key])){
				$out[$key] = $mapping[$key];
			}
		}
		return $out;
	}

	public static  function coalesceConcatWithSeperator($sep,$prefix,$main=null,$postfix=null){

		if( empty($prefix)){
			throw new Exception("prefix prefix is null");
		}

		$rep = $prefix;
		if (! PUtil::isEmpty($main)){
			$rep .=  ($sep . $main);
		}
		if (! PUtil::isEmpty($postfix)){
			$rep .=  ($sep . $postfix);
		}
		return $rep;


	}

	//@DocGroup(module="util", group="general", comment="strEndsWith")
	public static function strEndsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
		return (substr($haystack, -$length) === $needle);
	}

	//@DocGroup(module="util", group="general", comment="strBeginsWith")
	public static  function strBeginsWith($str, $sub) {
		return (strncmp($str, $sub, strlen($sub)) == 0);
	}

	//@DocGroup(module="util", group="general", comment="strContains")
	public static  function strContains($str, $sub) {
		return (strpos($str, $sub) !== FALSE);
		//return preg_match("/$sub/", $str);
		//return (strncmp($str, $sub, strlen($sub)) > 0);
	}




	//@DocGroup(module="util", group="general", comment="dbToSelect")
	public static  function dbToSelect($SQL,$name,$selectedKey, $echoFlag = true){
		$dbh = dbconnect();
		try {
			$stmt = $dbh->prepare($SQL);
			$stmt->execute();
			$stmt->bindColumn(1, $key);
			$stmt->bindColumn(2, $value);


			while ($stmt->fetch()){
				$select[$key] = $value;
			}

			return PUtil::toSelect($name,$select,$selectedKey,$echoFlag);


		} catch (PDOException $e){
			$error = $e->getMessage();
			#echo("Can not SELECT: $error\n");
			error_log( $error, 0);
			error_log($SQL,0);
			throw $e;
		}


	}


	public static function safeArrGet($array,$key,$defVal){
		return isset($array[$key]) ? $array[$key] : $defVal;
	}


	//@DocGroup(module="util", group="general", comment="toSelect")
	public static  function toSelect($name, $map, $selectedKey = null,$echoFlag = true){
		$out =  sprintf('<select name="%s">',$name);
		$out .= "\n";

		foreach ($map as $k => $v) {
			if ($k == $selectedKey) {
				$out .= sprintf('<option selected="yes" value="%s">%s</option>',$k,$v);
			} else {
				$out .= sprintf('<option value="%s">%s</option>',$k,$v);
			}
			$out .= "\n";
			#echo("\n");
		}

		$out .= sprintf('</select>');
		$out .= "\n";

		if($echoFlag){
			echo($out);
		}
		return $out;
	}



	//@DocGroup(module="util", group="general", comment="fonitiko")
	public static  function fonitiko($in){
		$dbh = dbconnect();
		if (empty($in))
			return  null;

		$SQL = "SELECT rep FROM dsd.gr_fonitiko(?) as rep";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $in );
		$stmt->execute();
		$stmt->bindColumn(1, $tmp);
		$stmt->fetch();
		return $tmp;
	}



	//@DocGroup(module="util", group="general", comment="nextval(sequence_name)")
	public static function nextval($dbh,$sequence_name = null)
	{
		if (empty($sequence_name)){
			$sequence_name = $dbh;
		}
		return PDao::nextval($sequence_name);
	}


	public static function greek2latin($str){
		$str = str_replace('ου','ou',$str);
		$str = str_replace('ού','ou',$str);
		$str = str_replace('ει','i',$str);
		$str = str_replace('εί','i',$str);
		$str = str_replace('ΕΙ','I',$str);
		$str = str_replace('οι','i',$str);
		$str = str_replace('οί','i',$str);
		$str = str_replace('ΟΙ','I',$str);
		$str = str_replace('αι','e',$str);
		$str = str_replace('αί','e',$str);
		$str = str_replace('ΑΙ','E',$str);
		$str = str_replace('ψ','ps',$str);
		$str = str_replace('Ψ','PS',$str);
		$from = 'αβγδεζηθικλμνξοπρστυφχψωΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩάέόώίύΆΈΊΎΌΏϋϊΐΰ "\'\\';
		$to =   'abgdezi8iklmn3oprstyfxcoABGDEZI8IKLMN3OPRSTYFXCOaeooiyAEIYOOyiiy____';
		$keys = array();
		$values = array();
		preg_match_all('/./u', $from, $keys);
		preg_match_all('/./u', $to, $values);
		$mapping = array_combine($keys[0], $values[0]);
		return strtr($str, $mapping);
	}


	public static function formatSizeBytes($bytes){
		$fsize = round($bytes/1000000,1);
		if ($fsize < 0.4){
			$fsize = round($bytes/1000,0);
			if ($fsize < 2){
				$fsize = round($bytes/1000,1);
				if ($fsize >= 0.1){
					$fsize = sprintf('%1.1f&nbsp;kB',$fsize);
				} else {
					$fsize = sprintf('%1d&nbsp;B',$bytes);
				}
			}else{
				$fsize = sprintf('%d&nbsp;kB',$fsize);
			}
		} else {
			$fsize = sprintf('%1.1f&nbsp;MB',$fsize);
		}
		return $fsize;
	}



	public static function image_extension_from_mimetype($mimetype){
		$ext = null;
		if ($mimetype == 'image/jpeg' || $mimetype == 'image/png'){
			$ext = substr($mimetype,6);
		}
		return $ext;
	}

	public static function mimetype_from_image_extension($ext){

		if (strcasecmp($ext, 'png') == 0) {
			return 'image/png';
		}
		if (strcasecmp($ext, 'jpg') == 0) {
			return 'image/jpeg';
		}
		if (strcasecmp($ext, 'jpeg') == 0) {
			return 'image/jpeg';
		}

		return null;
	}


	public static function identify_image($image_file_path){
		$cmd =  Config::get('arc.BIN_DIR') . 'identify -verbose ' . $image_file_path;

		$tmp = exec($cmd,$out,$status);

		$r = array(
				'FORMAT'=>null,
				'WIDTH'=>null,
				'HEIGHT'=>null,
				'RESOLUTION'=>null,
				'PRINT_SIZE'=>null,
				'UNITS'=>null,
				'TYPE'=>null,
				'DATETIME_DIGITIZED'=>null
		);
		foreach ($out as $l){
			if (preg_match('/^\s*Format: (\w+)/', $l, $m)){
				$r['FORMAT'] = $m[1];
			} elseif (preg_match('/^\s*Geometry:\s(\d+)x(\d+)\+/', $l, $m)){
				$r['WIDTH'] = $m[1];
				$r['HEIGHT'] = $m[2];
			} elseif (preg_match('/^\s*Resolution:\s(\d+x\d+)/', $l, $m)){
				$r['RESOLUTION']=$m[1];
			} elseif (preg_match('/^\s*Print\ssize:\s([\d\.]+x[\d\.]+)/', $l, $m)){
				$r['PRINT_SIZE']=$m[1];
			} elseif (preg_match('/^\s*Units:\s(\w+)/', $l, $m)){
				$r['UNITS']=$m[1];
			} elseif (preg_match('/^\s*Type:\s(\w+)/', $l, $m)){
				$r['TYPE']=$m[1];
			} elseif (preg_match('/^\s*exif:DateTimeDigitized:\s(\d+\:\d+\:\d+\s+\d+\:\d+\:\d+)/', $l, $m)){
				$r['DATETIME_DIGITIZED']=$m[1];
			}
		}
		return $r;
	}

	public static function identify_image_to_string($image_file_path, $mode = 0){

		$rep = '';
		$cmd = Config::get('arc.BIN_DIR'). 'identify -verbose ' . $image_file_path;
		$tmp = exec($cmd,$out,$status);
		if ($mode == 0){
			foreach ($out as $l){
				if (
				PUtil::strContains($l, '  Format:') ||
				PUtil::strContains($l, '  Geometry:') ||
				PUtil::strContains($l, '  Resolution:') ||
				PUtil::strContains($l, '  Print size:') ||
				PUtil::strContains($l, '  Units:') ||
				PUtil::strContains($l, '  Type:') ||
				PUtil::strContains($l, '  Colorspace:') ||
				PUtil::strContains($l, '  Depth:') ||
				PUtil::strContains($l, '    exif:')
				){
					$rep .= "$l\n";
				}
			}
		} else {
			foreach ($out as $l){
				$rep .= "$l\n";
			}
		}
		return $rep;
	}

	public static function extract_djvu_page($filename, $page_no){
		$my_idx = $page_no + 1;
		$cmd = BIN_DIR . "djvu_extract_page.sh $filename $my_idx";
		$tmp = exec($cmd,$out,$status);
		if (isset($out[0])){
			$fn = $out[0];
		} else {
			$fn =null;
		}

		return $fn;
	}

	public static function extract_cbr_page($filename, $page_no){

		$fn = $filename;
		$cidx = 0;
		$my_idx = $page_no +1;
		$cmd = BIN_DIR . "cbr_extract_page.sh $filename $my_idx";
		$tmp = exec($cmd,$out,$status);
		if (isset($out[0])){
			$fn = $out[0];
		} else {
			$fn =null;
		}
		return $fn;
	}



	public static function curl_get_data($url){
		$ch = curl_init();
		$timeout = 35;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:16.0) Gecko/20100101 Firefox/16.0');
		//
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	//@DocGroup(module="util", group="general", comment="get_object_type_names")
	public static function	get_object_type_names($dbh = null){
	$dbh = empty($dbh) ? dbconnect() : $dbh;
	$obj_type_names = null;
	if (isset($_REQUEST['get_object_type_names'])){
		return $_REQUEST['get_object_type_names'];
	} else {
		$obj_type_names = array();
	}
	$SQL="SELECT name,mime_label from dsd.obj_type";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	while ($row = $stmt->fetch()){
		$n = $row[1];
		if ($n == 'web-page'){
			$n = 'Web';
		}
		$obj_type_names[$row[0]] = $n;
	}

	$_REQUEST['get_object_type_names'] = $obj_type_names;
	return $obj_type_names;
}

public static function item_listo($result, $options = array()){
	$edit_flag = isset($options['edit']) ? $options['edit'] : false ;
	$list_edit_flag = isset($options['list_edit']) ? $options['list_edit'] :false ;
	$small_img_flag = isset($options['small_img']) ? $options['small_img'] :false ;
	$edit_link = isset($options['edit_link']) ? $options['edit_link'] : false ;
	$obj_type_names = get_object_type_names();
	return PUtil::item_list($result, $obj_type_names,$edit_flag,$list_edit_flag,$small_img_flag,$edit_link);
}

		// @DocGroup(module="util", group="general", comment="item_list")
	/**
	 *
	 * @deprecated see item_listo
	 * @param unknown $result
	 * @param unknown $obj_type_names
	 * @param string $edit_flag
	 * @param string $list_edit_flag
	 * @param string $small_img_flag
	 */
	public static function item_list($result, $obj_type_names, $edit_flag = false, $list_edit_flag = false, $small_img_flag = false, $edit_link = false) {

		// ####################################################################################
		// ## TABLE BODY LIST
		// ####################################################################################
		$lang = get_lang();

		$img_class1 = "resimg";
		$img_class2 = "";
		if ($small_img_flag) {
			$img_class1 = "smallimg1";
			$img_class2 = "smallimg2";
		}

		$no_download = '<img title="not available for download" alt="not available for download" src="/_assets/img/no-download.png"/>';
		$download = '<span class="glyphicon glyphicon-download-alt" aria-hidden="true" title="' . tr('Available for download') . '"></span>';

		foreach ( $result as $row ) {
			// echo("<pre>");
			// print_r($row);
			// echo("<pre>");
			$obj_type = $row['obj_type'];
			$folder_flag = $row['folder'];
			$folders = $row['folders'];
			if (! PUtil::isEmpty($folders)) {
				$folders = sprintf('(%s)', $folders);
			}
			$opac = isset($row['jdata']) ? new OpacHelper($row['jdata']) : new OpacHelper(null);

			// $folder_flag = false;
			// if ($obj_type == DB_OBJ_TYPE_EFIMERIDA || $obj_type == DB_OBJ_TYPE_PERIODIKO ||$obj_type == DB_OBJ_TYPE_WEBSITE ){
			// $folder_flag = true;
			// }

			if (empty($row['bibref'])) {
				$download_img = $download;
			} else {
				$download_img = $no_download;
			}

			echo ("<tr>\n");
			if ($list_edit_flag && user_access_admin()) {
				echo ("<td>");
				printf('<input class="listedit" type="checkbox" name="%s" value="%s" />', $row['item_id'], $row['item_id']);
				echo ("</td>");
			}

			echo ('<td class="std1">');

			// if ($folder_flag){
			// printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" alt="folder" src="/_assets/img/items/folder.png"/></a>',$row[5],$lang, $img_class2);
			// } else if ($obj_type == Config::get('DB_OBJ_TYPE_WEBSITE_INSTANCE')){
			// printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" alt="website" src="/_assets/img/items/text-html.png"/></a>',$row[5],$lang, $img_class2);
			// } else if ($obj_type == Config::get('arc.DB_OBJ_TYPE_PERSON') || $obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-PERSON')){
			// printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" alt="person" src="/_assets/img/items/user.png"/></a>',$row[5],$lang, $img_class2);
			// } else {
			// printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" alt="entity" src="/_assets/img/items/document.png"/></a>',$row[5],$lang, $img_class2);
			// }
			// echo('<br/>');

			//echo(tr($obj_type_names[$obj_type]));

			$obj_type_name = tr($obj_type_names[$obj_type]);

			if ($folder_flag) {
				printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" alt="folder" src="/_assets/img/items/folder.png"/></a>', $row[5], $lang, $img_class2);
			} else if ($obj_type == Config::get('DB_OBJ_TYPE_WEBSITE_INSTANCE')) {
				printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" alt="website" src="/_assets/img/items/text-html.png"/></a>', $row[5], $lang, $img_class2);
				// } else if ($obj_type == Config::get('arc.DB_OBJ_TYPE_PERSON') || $obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-PERSON')){
				// $bg_img= 'user.png';
				// printf('<span class="obj_type_per" style="background-image:url(/_assets/img/items/%s);">%s</span>',$bg_img,$obj_type_name);
			} else {
				if ($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-WORK')){
					$bg_img= 'work.jpg';
				}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-EXPRESSION')){
					$bg_img= 'expression.jpg';
				}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-MANIFESTATION')){
					$bg_img= 'manif.jpg';
				}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-PERSON')){
					$bg_img= 'person.jpg';
				}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-FAMILY')){
					$bg_img= 'family.jpg';
				}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-ORGANIZATION')){
					$bg_img= 'organ.jpg';
				}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-PLACE')){
					$bg_img= 'map.jpg';
				}else{
					// 	$bg_img= 'document.png';
					$bg_img= 'subject.jpg';
				}



				printf('<span class="obj_type" style="background-image:url(/_assets/img/items/%s);">%s</span>',$bg_img,$obj_type_name);

// 				printf('<span class="obj_type_bg_img" style="background-image:url(/_assets/img/items/%s);"></span><span class="element-invisible">%s</span>  ', $bg_img, $obj_type_name);
			}

			if ($folder_flag) {
				$txt = ($obj_type == Config::get('arc.DB_OBJ_TYPE_WEBSITE')) ? tr('σελίδες') : ($obj_type == Config::get('arc.DB_OBJ_TYPE_SILOGI')) ? tr('τεκμήρια') : tr('τεύχη');
				printf('<br/>%s:&nbsp;%s', $txt, PUtil::coalesce($row['issue_cnt'], '1'));
			}

			echo ('</td>');

			$thumb = $row['thumb'];
			$pages = $row['pages'];
			if (! empty($pages)) {
				$pagesStr = sprintf('<br/> %s: %s', tr('σελιδες'), $pages);
			} else {
				$pagesStr = "";
			}
			if ($folder_flag) {
				$txt = ($obj_type == Config::get('arc.DB_OBJ_TYPE_WEBSITE')) ? tr('σελίδες') : ($obj_type == Config::get('arc.DB_OBJ_TYPE_SILOGI')) ? tr('τεκμήρια') : tr('τεύχη');
				$tefxiStr = sprintf('<br/>%s:%s', $txt, PUtil::coalesce($row['issue_cnt'], '1'));
			} else {
				$tefxiStr = "";
			}
			if ($edit_flag) {
				$tefxiStr .= sprintf('<br>id: %s 	&#160; 	&#160; status: <a href="/archive/recent?s=%s">%s</a>  ', $row['item_id'], $row['status'], $row['status']);
				if (! empty($row['user_create'])) {
					$tefxiStr .= sprintf('&#160; &#160;  create: %s', $row['user_create']);
				}
				if (! empty($row['user_update'])) {
					$tefxiStr .= sprintf('&#160; &#160;  update: %s', $row['user_update']);
				}

				$dt = PUtil::coalesce($row['dt_update'], $row['dt_create']);

				$phpdate = strtotime($dt);
				$tefxiStr .= sprintf('&#160; &#160; %s', date('d/m/Y', strtotime($dt)));
			}

// 			if (! empty($thumb)) {

// 				printf('<span class="thumb_bg_img" style="background-image:url(/media/%s); "></span>  ', $thumb);
// 			//Sreen Reader
// 			//printf(' <a href="/archive/item/%s?lang=%s" title="%s"><img class="%s" src="/media/%s" alt="%s"/></a>', $row[5], $lang, htmlspecialchars($row[1]), $img_class1, $thumb, htmlspecialchars($row[1]));

// 			} else {
// 				if ($obj_type == 'silogi') {
// 					printf('<a href="/archive/item/%s?lang=%s" title="%s"><img class="%s" src="/_assets/img/books4_64.png" alt="%s"/></a>', $row[5], $lang, htmlspecialchars($row[1]), $img_class1, htmlspecialchars($row[1]));
// 				} else {
// 					echo ('<span class="empty_td"></span>');
// 					// printf('<img class="resimg" src="/_assets/img/pixel.gif" alt="empty cover"/>');
// 					// printf('<a href="/archive/item/%s?lang=%s" title="%s"><img class="resimg" src="/_assets/img/pixel.gif" alt="%s"/></a>',$row[5],$lang,$row[1], $row[1]);
// 				}
// 			}

			printf('<td style="width:100%%">');

// 			echo ('<div class="col-md-10">');

			echo ((!empty($thumb))?'<div class="col-md-11">':'<div class="col-md-12">');
			if ($opac->hasOpac1('label')) {
				$title = htmlspecialchars($opac->opac1('label'));
			} else {
				$title = htmlspecialchars($row['title']);
			}

			if ($edit_link) {
				printf('<a href="/prepo/edit_step1?i=%s">%s</a><br/> %s %s %s %s %s', $row[5], $title, $row[3], $row[2], $folders, $pagesStr, $tefxiStr);
			} else {
				if ($opac->hasOpac1('public_title')){
					$public_title_data = $opac->opac1('public_title');
					$t = $public_title_data['title'];
					$id = $public_title_data['id'];
// 					printf('<a href="/archive/item/%s">%s</a><br/>',$id,$t);
					printf('<a href="/archive/item/%s">%s</a>',$id,$t);
					if ($opac->hasOpac1('public_lines')){
						$public_lines = $opac->opac1('public_lines');

						if (!empty($public_lines)) {
								echo ('<dl class="opac_list">');
								echo '<dt>'.tr('Available versions').'</dt>';
								foreach ($public_lines as $line){
									$t = $line['title'];
									$id = $line['id'];
// 								printf('&nbsp;&nbsp;&nbsp; <a href="/archive/item/%s">%s</a><br/>',$id,$t);
									printf('<dd> <a href="/archive/item/%s">%s</a></dd>',$id,$t);
								}
						 		echo '</dl>';
						}else{
								echo('<br/>');
						}
					}else{
						echo('<br/>');
					}
					printf('%s %s %s %s %s',$row[3], $row[2], $folders, $pagesStr, $tefxiStr);
				} else {
					printf('<a href="/archive/item/%s?lang=%s">%s</a><br/> %s %s %s %s %s', $row[5], $lang, $title, $row[3], $row[2], $folders, $pagesStr, $tefxiStr);
				}
			}
			// if ($folder_flag){
			// printf('<div class="tefxi">%s:&nbsp;%s</div>',($obj_type == DB_OBJ_TYPE_WEBSITE)? 'σελίδες' : 'τεύχη' , coalesce($row['issue_cnt'],'1'));
			// }

// 			printf('</div><span class="thumb_bg_img" style="display:inline-block;float: right;background-image:url(/media/%s); "></span>  ', $thumb);

			echo ('</div>');

			if (! empty($thumb)) {

								printf('<div class="col-md-1"><span  aria-hidden="true" class="thumb_s_bg_img" style="background-image:url(/media/%s);"></span></div>  ', $thumb);

							} else {
								if ($obj_type == 'silogi') {
									printf('<a href="/archive/item/%s?lang=%s" title="%s"><img class="%s" src="/_assets/img/books4_64.png" alt="%s"/></a>', $row[5], $lang, htmlspecialchars($row[1]), $img_class1, htmlspecialchars($row[1]));
								} //else {
									//echo ('<div class="col-md-2"><span  aria-hidden="true" class="empty_td"></span></div>');
									// printf('<img class="resimg" src="/_assets/img/pixel.gif" alt="empty cover"/>');
									// printf('<a href="/archive/item/%s?lang=%s" title="%s"><img class="resimg" src="/_assets/img/pixel.gif" alt="%s"/></a>',$row[5],$lang,$row[1], $row[1]);
// 								}
						}
			echo ('</td>');

		}
	}


//@DocGroup(module="util", group="general", comment="item_list_thumbs")
	public static function item_list_thumbs($results,$d,$lang){



	printf('<div id="thl_%s">',$d);



	#####################################################################################
	### TABLE BODY THUMBS
	#####################################################################################

	foreach($results as $row){
		$title =$row['title'];
		$item_id = $row['item_id'];
		$thumb1 = $row['thumb'];
		$thumb2 = $row['thumb1'];
		$thumb3 = $row['thumb2'];
		$obj_type = $row['obj_type'];


		$folder_flag = $row['folder'];


		$aggr_flag = $row['issue_aggr'];
		if ($d == 2){
			$thumb = $thumb1;
		} else if($d == 3){
			$thumb = $thumb2;
		} else {
			$thumb = $thumb3;
		}
		if (! empty($thumb)){
			$src = sprintf("/media/%s",$thumb);
		} else{
			if ($obj_type == "silogi"){
				if ($d == 4){
					$src = "/_assets/img/books4_200.png";
				} else {
					$src = "/_assets/img/books4_110.png";
				}
			} else{
				$src = "/_assets/img/pixel.gif";
			}
		}
		printf('<div class="item_thl">');
		printf('<a href="/archive/item/%s&lang=%s" title="%s"><img class="resimg" src="%s" alt="%s"/></a>',$item_id,$lang,$title, $src,$title);
		if ($folder_flag){
			if ($d == 4){
				printf('<img class="folderico" src="/_assets/img/items/folder48.png"/>');
			} else {
				printf('<img class="folderico" src="/_assets/img/items/folder24.png"/>');
			}
		}
		echo('</div>');
	}

	echo('<div class="spacer">&nbsp;</div>');
	echo('</div>');



}



public static function populateRequestARGV(&$p1 =null, &$p2=null, &$p3=null, &$p4=null ){
	if (isset($_REQUEST['ARGV'])){
		$argv =$_REQUEST['ARGV'];
		if (isset($argv[0])){ $p1 = $argv[0];};
		if (isset($argv[1])){ $p2 = $argv[1];};
		if (isset($argv[2])){ $p3 = $argv[2];};
		if (isset($argv[3])){ $p4 = $argv[3];};
	}
}

public static function parchive_item_json() {

	$lang = get_lang();

	$i = $_REQUEST['item_id'];
	$rep = $_REQUEST['item'];
	// 	$i =isset($_REQUEST['item_id']) ?$_REQUEST['item_id'] : null;
	// 	if (empty($i)){
	// 		return null;
	// 	}
	// 	$dbh = dbconnect();
	// 	$rep = item($dbh,$i);
	// 	if(empty($rep)){
	// 		return;
	// 	}
	#echo("<pre>");
	#print_r($rep);
	#echo("</pre>");
	$type = $rep['type'];
	$periodical = ($type == 'periodiko' || $type == 'efimerida' || $type == 'web-site');
	$website = ($type == 'web-site-instance');
	$silogi = ($type == 'silogi');
	$periodicalayout = $periodical ? 'iteminf-periodic' : 'iteminf';
	$bibref = $rep['bibref'];
	$uuid = $rep['uuid'];

	$uri = 'http://' . Config::get('arc.ARCHIVE_HOST') . '/archive/item/' . $uuid;

	$out = array();
	$out[DataFields::dc_identifier_uri] = $uri;
	$out[DataFields::ea_uuid] = $uuid;
	$out[DataFields::ea_type] = $type;
	$out[DataFields::dc_title] = $rep['title'];
	$out[DataFields::ea_subtitle] = PUtil::coalesce($rep['subtitle'], '');
	$out[DataFields::ea_date] = PUtil::coalesce($rep['date'], '');
	$out[DataFields::ea_size] = PUtil::coalesce($rep['size'], '');
	$out[DataFields::ea_place] = PUtil::coalesce($rep['place'], '');
	$out[DataFields::ea_desc] = PUtil::coalesce($rep['desc_desc'], '');
	$out[DataFields::ea_abstract] = PUtil::coalesce($rep['desc_abstract'], '');
	$out[DataFields::ea_year] = PUtil::coalesce($rep['year'], '');
	$out[DataFields::ea_author] = PUtil::coalesce($rep['authors'], '');
	$out[DataFields::ea_pages] = PUtil::coalesce($rep['pages'], '');
	$out[DataFields::dc_date_available] = PUtil::coalesce($rep['date_available'], '');
	$out[DataFields::dc_language_iso] = PUtil::coalesce($rep['lang_code'], '');
	//$out['ea:page_size'] = $rep['page_size'];

	$out_related_urls = array();

	if (!empty($rep['url_related'])) {
		foreach ($rep['url_related'] as $val) {
			$url_arr = explode_url($val);
			$out_related_urls[] = $url_arr;
		}
	}
	$out[DataFields::ea_related_urls] = $out_related_urls;

	$out_origin_url = null;
	if (!empty($rep['url_origin'])) {
		$out_origin_url = explode_url($rep['url_origin']);
	}

	$out[DataFields::ea_origin_url] = PUtil::coalesce($out_origin_url, '');

	$out_keywords = array();
	$keywords = $rep['keywords'];

	if (!empty($keywords)) {
		foreach ($keywords as $k => $v) {
			$url = sprintf('http://%s/archive/term?t=%s', Config::get('arc.ARCHIVE_HOST'), urlencode($v));
			$out_keywords[] = array(DataFields::tag_name => $v, DataFields::tag_url => $url);
			#printf('<a href="%s">%s</a>',$url,$v);
		}
	}
	$out[DataFields::ea_tags] = $out_keywords;

	$thumbs_s = $rep['thumbs_small'];
	$thumbs_b = $rep['thumbs_big'];
	$has_thumbs_s = (!empty($thumbs_s));
	$has_thumbs_b = (!empty($thumbs_b));

	$out_thumbs_small = array();
	if ($has_thumbs_s) {
		foreach ($thumbs_b as $k => $v) {
			$url = sprintf("http://%s/media/%s", Config::get('arc.ARCHIVE_HOST'), $v);
			$out_thumbs_small[] = array(DataFields::thumb_index => $k, DataFields::thumb_url => $url);
		}
	}
	$out[DataFields::ea_thumbs_small] = $out_thumbs_small;

	$out_thumbs_big = array();
	if ($has_thumbs_b) {
		foreach ($thumbs_b as $k => $v) {
			$url = sprintf("http://%s/media/%s", Config::get('arc.ARCHIVE_HOST'), $v);
			$out_thumbs_big[] = array(DataFields::thumb_index => $k, DataFields::thumb_url => $url);
		}
	}
	$out[DataFields::ea_thumbs_big] = $out_thumbs_big;

	$out_bitstreams = array();
	$bitstreams = $rep['bitstreams'];
	if (count($bitstreams) > 0) {
		$item_id = $rep['id'];
		foreach ($bitstreams as $seq_id => $v) {
			$bitstream = PUtil::coalesce($v['bitstream_id'], '');
			$mimetype = PUtil::coalesce($v['mimetype'], '');
			$fname = PUtil::coalesce($v['name'], '');
			$fbytes = null;//PUtil::coalesce($v['bytes'], '');
			$desc = PUtil::coalesce($v['description'], '');
			$url1 = 'http://' . Config::get('arc.ARCHIVE_HOST') . '/archive/download?i=' . urlencode($item_id) . '&d=' . urlencode($bitstream);
			$checksum = PUtil::coalesce($v['checksum'], '');
			$checksum_algorithm = PUtil::coalesce($v['checksum_algorithm'], '');
			#$fsize = round($fbytes/1000000,2);
			#$msg = empty($desc) ? $fname : $desc;
			$out_bitstreams[] = array('url' => $url1, 'size_bytes' => $fbytes, 'description' => $desc, 'mimetype' => $mimetype, 'name' => $fname, 'checksum_algorithm' => $checksum_algorithm, 'checksum' => $checksum);
		}
	}
	$out[DataFields::ea_bitstreams] = $out_bitstreams;

	drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
	drupal_add_http_header('Content-type', 'application/json');
	echo json_encode($out);
	return null;
}

//@DocGroup(module="util", group="archive", comment="bitream2filename")
public static function bitream2filename($bitstream){
	$PREFIX= Config::get('arc.ASSETSTORE_DIR');
	$a = substr($bitstream,0,2);
	$b = substr($bitstream,2,2);
	$c = substr($bitstream,4,2);
	$filename = $PREFIX . "/" . $a ."/" . $b . "/". $c . "/" . $bitstream;
	return $filename;
}



public static function getUserData(){
	$app = App::make('arc');
	$uid  = $app->uid;
	if ($uid){
		$dbh = dbconnect();
		$SQL="SELECT id, uid, username, full_name, email FROM dsd.arc_user WHERE cms='drupal' AND uid = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $uid);
		$stmt->execute();
		if  ($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			return $r;
		}
	}
	return array(
			'id' => null,
			'uid'=>0,
			'username'=>'anonymous',
			'full_name'=>'anonymous'
	);
}





public static function download_bitstream($bitstream, $mime, $fname, $download_fname, $direct, $ds, $logId) {

	$filename  = PUtil::bitream2filename($bitstream);
	if (!file_exists($filename)){
		//echo "CANOT FIND FILE NAME";
		error_log("download_bitstream: canot find file: $filename");
		return;
	}
	//$filesize = filesize($filename);
	$pcli = new PShellClient(array(
			'bin_dir'=>'node',
	));

	$signature = null;
	$gds = variable_get('arc_enable_digital_signatures', 1);
	//Log::info("MIME: " .$mime);
	if ($mime == 'application/epub+zip'){

		$user_data = PUtil::getUserData();
		$uid = $user_data['uid'];
		$full_name= htmlspecialchars($user_data['full_name']);
		if (empty($full_name)){
			$full_name = $user_data['username'];
		}
		if (!empty($full_name) && $ds && $gds) {
			$signature = PDao::createUUID();
			Log::info("EPUB SIGNATURE FOR $full_name: $signature");
			//Log::info('#1: ' .$filename);
			$execfilename = $pcli->exec("epub-ds.sh", array($filename, $full_name, $signature));
			$filename = (!empty($execfilename)) ? $execfilename : $filename;
			//Log::info('#2: ' .$filename);
		}
	}
	if ($mime == 'application/pdf') {
		$user_data = PUtil::getUserData();
//		$full_name = htmlspecialchars($user_data['full_name']);
//		if (empty($full_name)) {
		// TODO: we need font-embedding to support utf-8 fullname in pdfs
			$full_name = $user_data['username'];
//		}
		if (!empty($full_name) && $ds && $gds) {
			$signature = PDao::createUUID();
			Log::info("PDF SIGNATURE FOR $full_name: $signature");
			$execfilename = $pcli->exec("pdf-ds.sh", array($filename, $full_name, $signature));
			$filename = (!empty($execfilename)) ? $execfilename : $filename;
		}
	}
	if ($mime == 'audio/mpeg') {
		$user_data = PUtil::getUserData();
		$full_name = htmlspecialchars($user_data['full_name']);
		if (empty($full_name)) {
			$full_name = $user_data['username'];
		}
		if (!empty($full_name) && $ds && $gds) {
			$signature = PDao::createUUID();
			Log::info("MP3 SIGNATURE FOR $full_name: $signature");
			$execfilename = $pcli->exec("mp3-ds.sh", array($filename, $full_name, $signature));
			$filename = (!empty($execfilename)) ? $execfilename : $filename;
		}
	}
	if ($mime == 'application/zip') {
		$user_data = PUtil::getUserData();
		$full_name = htmlspecialchars($user_data['full_name']);
		if (empty($full_name)) {
			$full_name = $user_data['username'];
		}
		if (!empty($full_name) && $ds && $gds) {
			$signature = PDao::createUUID();
			Log::info("ZIP SIGNATURE FOR $full_name: $signature");
			$execfilename = $pcli->exec("zip-ds.sh", array($filename, $full_name, $signature));
			$filename = (!empty($execfilename)) ? $execfilename : $filename;
		}
	}

	if (!empty($logId)) {
		$checksum = hash_file('sha256', trim($filename));
		PDao::download_db_log_append($logId, $signature, $checksum);
	}


	$final_name = empty($download_fname)? $fname : $download_fname;


	#drupal_add_http_header("Cache-Control: ");
	#drupal_add_http_header("Pragma: ");
	#drupal_add_http_header("Content-Type: application/octet-stream");
	#drupal_add_http_header("Content-Length: " .(string)(filesize($path)) );
	#drupal_add_http_header('Content-Disposition: attachment; filename="'.$name.'"');
	#drupal_add_http_header("Content-Transfer-Encoding: binary\n");

	if ($direct){
	//	error_log("direct: " . $mime . " : " . $filesize);
// 		drupal_add_http_header('Content-Type', $mime);
// 		drupal_add_http_header('Content-Length' , $filesize);
// 		#		header('Content-Disposition: attachment; filename="'.$fname.'"');
// 		drupal_add_http_header('Content-Transfer-Encoding','binary');
// 		$handle = fopen( $filename, "rb");
// 		fpassthru($handle);

		//Log::info("**DIRECT: "  . $filename);
		$response = Response::make('', 200);
		$response->header('X-Sendfile', $filename);
		$response->header('Content-Type',$mime);
		$response->header('Content-Transfer-Encoding','binary');
		return $response;

	} else {
		//Log::info("**NORMAL: "  . $filename . " : " . $final_name);
		//error_log("download: 2");
		//$filename2  =   ASSETSTORE_DIR . $filename;

// 		drupal_add_http_header('X-Sendfile', $filename);
// 		drupal_add_http_header('Content-Type','application/octet-stream');
// 		drupal_add_http_header('Content-Disposition',sprintf('attachment; filename="%s"',$final_name));
// 		drupal_add_http_header('Content-Transfer-Encoding','binary');

		//return Response::download($filename, $final_name);


// 		$response = Response::make('', 200);
// 		$response->header('X-Sendfile', $filename);
// 		$response->header('Content-Type','application/octet-stream');
// 		$response->header('Content-Disposition',sprintf('attachment; filename="%s"',$final_name));
// 		$response->header('Content-Transfer-Encoding','binary');
//		return $response;

//		header("X-KOKO: LALA");
		header('Content-Type: application/octet-stream');
		header(sprintf('Content-Disposition:  attachment; filename="%s"',$final_name));
		//header('Content-Transfer-Encoding: binary');
		header(sprintf('X-Sendfile: %s', $filename));
// 		ob_clean();
// 		flush();
		exit;
	}
}


// private static function createUrlFromServer(){
// 	if (isset($server["REQUEST_URI"])) {
// 		return UrlImmutable::createFromUrl($server["REQUEST_URI"]);
// 	}

// 	$request = "";
// 	if (isset($server["PHP_SELF"])) {
// 		$request .= $server["PHP_SELF"];
// 	}

// 	if (isset($server["QUERY_STRING"])) {
// 		$request .= "?".$server["QUERY_STRING"];
// 	}

// 	return UrlImmutable::createFromUrl($request);
// }


public static function getLocation(){
	return UrlImmutable::createFromServer($_SERVER);
}



private static function replaceUrlGetParams($query_params , $org_url = null){
	//$org_url = empty($org_url) ? UrlImmutable::createFromServer($_SERVER) :$org_url;
	$org_url = empty($org_url) ?  PUtil::getLocation() : $org_url;
	$query = $org_url->getQuery();
	$query->modify($query_params);
	$org_url->setQuery($query);
	return $org_url;
}

public static function replaceRelativeUrlGetParams($query_params , $org_url = null){
	$org_url = empty($org_url) ?  PUtil::getLocation() : $org_url;
	$query = $org_url->getQuery();
	$query->modify($query_params);
	return $org_url->setQuery($query)->getRelativeUrl();
}



//@DocGroup(module="util", group="general", comment="print_select")
public static function print_select($name,$id,$map, $value=null, $ext_name_flag = true,$translate_flag = false,$class=null){
	if ($ext_name_flag && ! empty($name)){
		$name = $name . '[]';
	}

	printf('<select name="%s" ',$name);
		if (! empty($id)){
			printf(' id="%s" ',$id);
		}
		if (! empty($class)){
			printf(' class="%s" ',$class);
		}
	echo(">");

	foreach ($map as $k => $v){
		if ($value == $k){
			$s = 'selected="selected"';
		} else {
			$s = '';
		}
		if ($translate_flag && $v != null){
			$v = tr($v);
		}
		$val = $v == null ? '' : htmlspecialchars($v);
		printf('<option value="%s" %s >%s</option>' ."\n", $k,$s,$val);
	}
	echo("</select>");
}



public static function pdf_info_meta($image_file_path){
	$rep = '';
	$cmd = Config::get('arc.BIN_DIR') . 'pdfinfo -meta ' . $image_file_path;
	$tmp = exec($cmd,$out,$status);
	foreach ($out as $l){
		if (trim($l) == ''){
			continue;
		}
		//if (!strBeginsWith($l, '<?')){
		$rep .= htmlentities($l, ENT_COMPAT  ,  'UTF-8') . "\n";
		//}
	}
	return $rep;
}


public static function pdf_pdftk_data($image_file_path){
	$rep = '';
	$cmd = Config::get('arc.BIN_DIR') . 'pdftk ' . $image_file_path . " dump_data";
	$tmp = exec($cmd,$out,$status);
	foreach ($out as $l){
		if (trim($l) == ''){
			continue;
		}
		if (
		PUtil::strContains($l, 'InfoKey: ') ||
		PUtil::strContains($l, 'InfoValue: ')
		){
			$rep .=  urldecode($l) . " \n";
			//$rep .= htmlentities( urldecode($l), ENT_COMPAT  ,  'UTF-8') . " !\n";
		}
	}
	return $rep;
}


//@DocGroup(module="util", group="general", comment="extract_files_info_from_upload_form")
public static function extract_files_info_from_upload_form($fild_name){

	$rep = array();
	$uploadData_arr = isset($_FILES[$fild_name]) ? $_FILES[$fild_name] : null ;

	if (! empty( $uploadData_arr)){
		$file_name_arr = $uploadData_arr['name'];
		$file_arr =  $uploadData_arr['tmp_name'];
		$type_arr =  $uploadData_arr['type'];
		$size_arr =  $uploadData_arr['size'];


		foreach ($file_name_arr as $key => $file_name) {
			$upload_file_name = null;
			$upload_file_path = null;
			$upload_file_ext = null;
			$upload_file_type = null;
			$upload_file_size = null;

			preg_match('/\.(\w+)$/', $file_name, $matches);
			$ext = $matches[1];
			if (! empty($ext)){

				$upload_file_ext = $ext;
			}
			$upload_file_name = $file_name;
			$upload_file_path = $file_arr[$key];
			$upload_file_type = $type_arr[$key];
			$upload_file_size = $size_arr[$key];
			$rep[] = array($upload_file_name, $upload_file_path, $upload_file_ext, $upload_file_type, $upload_file_size);
		}
	}
	return $rep;
}


public static function upload_bitsteam($file, $file_name, $file_ext, $item_id, $seq_id, $bundle_name, $thumb_description = null){
	Log::info("upload_bitsteam: FILE: $file , FILE_NAME: $file_name , $file_ext , $item_id , $seq_id , $bundle_name, $thumb_description");
	// 	 echo("<pre>");
	// 	 echo("FILE: $file\n");
	// 	 echo("FILENAME: $file_name\n");
	// 	 echo("$file_ext\n");
	// 	 echo("$item_id\n");
	// 	 echo("#SEQ ID: $seq_id #\n");
	// 	 echo("$bundle_name\n");
	// 	 echo("</pre>");
	$dbh = dbconnect();
	$out = "upoload_bitstream $file ($file_name) to: $item_id bundle: $bundle_name\n";

	$bundle_id = PDao::get_bundle_id($item_id,$bundle_name);
	if (empty($bundle_id)){
		$bundle_id = PDao::insert_bundle($dbh, $bundle_name);
		PDao::insert_item2bundle($dbh, $item_id,$bundle_id);
	}
	$edoc_file_name = $file;

	if (! empty($seq_id)){
		$edoc_file_seq = $seq_id;
	} else {
		$SQL ="SELECT  max(sequence_id) from dsd.item_bitstream where item_id = ? AND sequence_id is not null";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		$r = $stmt->fetchAll();
		$edoc_file_seq = $r[0]['max'];
	}

	if (empty($edoc_file_seq)){
		$edoc_file_seq = 1;
	}
	$edoc_file_seq = $edoc_file_seq   + 1;

	$uuid = PDao::get_safe_uiid($dbh);
	$bitstream_id = PUtil::create_bitstream($dbh, $uuid, $file, $file_name, $edoc_file_seq,null,0,$file_ext, $thumb_description);
	PDao::insert_bundle2bitstream($dbh, $bundle_id, $bitstream_id);


	return array($bitstream_id,$bundle_id,$uuid, $out);

}





public static function mkdir($dir){
	//Log::info("MKDIR " . $dir);

	$cmd = 'mkdir -p ' . $dir;
	$cmd_out = array();
	$status = 0;
	$tmp = exec($cmd,$cmd_out,$status);
	if ($status != 0){
		$msg = print_r($cmd_out,true);
		error_log($msg);
		Log::info($msg);
	}

}


public static function create_bitstream($dbh, $uuid, $edoc_file, $edoc_file_name,  $edoc_file_seq, $spool_dir_ok = null, $optimized = 0, $file_ext = null, $thumb_description = null){

	$out = "";

	Log::info("create_bitstream: " . $uuid . " : " . $edoc_file . " : " . $edoc_file_name . " : " .$edoc_file_seq . ' : ' . $optimized . ' : ' . $file_ext. ' : ' .$thumb_description);

	if (empty($uuid)){
		throw new Exception("NULL UUID");
	}


	$dst_dir = PUtil::get_assetstore_dir_name_for_file($uuid);
	$dst_file = $dst_dir . $uuid;

	PUtil::mkdir($dst_dir);
// 	$cmd = "mkdir -p $dst_dir";
// 	$cmd_out = array();
// 	$status = 0;
// 	$tmp = exec($cmd,$cmd_out,$status);

	if (!empty($file_ext)){
		$edoc_file_extension = $file_ext;
	} else {
		$edoc_file_extension = PUtil::extract_extension($edoc_file_name);
		if (empty($edoc_file_extension)){
			error_log("CANOT extract EXTENSION $edoc_file_name");
			echo("CANOT extract EXTENSION $edoc_file_name\n");
			return;
		}
	}
	#$edoc_file = $spool_dir_pending . $edoc_file_name;
	$edoc_file_size = filesize($edoc_file);
	$edoc_file_md5 = md5_file($edoc_file);
	#echo("#3# $edoc_file_size\n");
	#echo("#4# $edoc_file_md5\n");
	$edoc_file_format = PDao::get_bitstream_format_id($dbh,$edoc_file_extension);
	#echo("#5 $edoc_file_format \n");
	if (empty($edoc_file_format)){
		error_log("CANOT find file_format for EXTENSION $edoc_file_extension");
		echo("CANOT find file_format for EXTENSION $edoc_file_extension\n");
	return;
	}

	$mime_type = PDao::get_mime_type_from_bitstream_format_id($edoc_file_format);
	#echo("#6 $mime_type \n");

	$cmd = "cp '" . $edoc_file . "' " . $dst_file;
		#echo "#7# $cmd\n";
	$cmd_out = array();
	$status = 0;
	$tmp = exec($cmd,$cmd_out,$status);

	$file_metadata_json = PUtil::get_file_metadata_json($dst_file,$mime_type);
// 	if (!empty($file_meta_data)){
// 		$file_metadata_json = json_encode($file_meta_data);
// 	} else {
// 		$file_metadata_json = '{}';
// 	}
	if (empty($file_metadata_json)){
		$file_metadata_json = '{}';
	}

	if (! empty($spool_dir_ok)){

	//preg_match('/\/(\w+\.\w\w\w)$/', $edoc_file_name, $matches);
		//$edoc_ok_file_name = $matches[1];

		//$ok_file = $spool_dir_ok . $edoc_ok_file_name;
		$cmd = "mv '" . $edoc_file ."' " . $spool_dir_ok;
		//echo ("$cmd\n");
		$cmd_out = array();
		$status = 0;
		$tmp = exec($cmd,$cmd_out,$status);
	}


	$pages = 0;
	$cmd =  Config::get('arc.BIN_DIR') . "file_pages.sh $dst_file";
	//echo($cmd);
	$tmp = exec($cmd,$out,$status);
		if (isset($out[0])){
		$pages = $out[0];
	}

	echo '<div class="arch-wrap" ><div class="valid_msg">';
	echo tr('Pages cardinality').' : '.$pages;
	echo '</div></div>';

	$bitstream_id = PUtil::nextval($dbh,'public.bitstream_seq');
	#		echo("<pre>");
#		echo("1: " . $bitstream_id); echo("\n");
		#		echo("2: " .$edoc_file_format); echo("\n");
		#		echo("3: " .$edoc_file_name); echo("\n");
		#		echo("4: " .$edoc_file_size); echo("\n");
		#		echo("5: " .$edoc_file_md5); echo("\n");
		#		echo("6: " .$edoc_file); echo("\n");
		#		echo("7: " .$uuid); echo("\n");
		#		echo("8: " . $edoc_file_seq); echo("\n");
		#		echo("9: " . $pages); echo("\n");
		#		echo("</pre>");


		$SQL="INSERT INTO public.bitstream (
		bitstream_id, bitstream_format_id, name, size_bytes,
		checksum, checksum_algorithm, source, internal_id,
		deleted, store_number, sequence_id, optimized,pages, metadata, thumb_description
		) VALUES ( ?,?, ?,?, ?,'MD5', ?,?, false, 0, ?,?,?,?,?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $bitstream_id);
		$stmt->bindParam(2, $edoc_file_format);
		$stmt->bindParam(3, $edoc_file_name);
		$stmt->bindParam(4, $edoc_file_size);
		$stmt->bindParam(5, $edoc_file_md5);
		$stmt->bindParam(6, $edoc_file);
		$stmt->bindParam(7, $uuid);
		$stmt->bindParam(8, $edoc_file_seq);
		$stmt->bindParam(9, $optimized);
		$stmt->bindParam(10, $pages);
		$stmt->bindParam(11, $file_metadata_json);
		$stmt->bindParam(12, $thumb_description);

		$stmt->execute();

		// 		$mime_type = get_bitstream_mime_type_from_id($bitstream_id);
		// 		if (empty($mime_type)){
		// 			error_log("canot find mymetype for bitstream $id" );
		// 			$out .= "canot find mymetype for bitstream $id\n";
		// 			return;
		// 		}


		//$out .="thumb_generate for bitstream\n";
		$all_flag = false;
		$parent_flag = false;

		//error_log("#######################\n");
		PDao::thumbs_generate_from_bitstream($dbh, $uuid, $out ,$all_flag,$parent_flag);

		if ($mime_type == 'application/pdf'){
				$item_bitstream = PDao::get_bitstream_item_ref($bitstream_id);
				PUtil::pdfinfo($dbh, $item_bitstream , $uuid, $out);
				PUtil::set_pdf_metadata(null, $uuid, null, $out);
		}


// 		if ($mime_type == 'application/epub+zip'){
// 			$f_path  = PUtil::bitream2filename($uuid);
// 			$f_name = $uuid;
// 			$f_dir = PUtil::frepo_finame_to_dir($f_name);
// 			//$f_name = PUtil::frepo_finame_to_full_path($file_name);

// 			$reader_content_dir = Config::get('readers.EPUB_READER_CONTENT');
// 			$nodeClient =  new PNodeClientEpubInstall();
// 			$out  = $nodeClient->exec_json(array(
// 					'file' => $f_path,
// 					'f_dir' =>$f_dir,
// 					'f_name' =>$f_name,
// 					'reader_content_dir' =>$reader_content_dir,
// 			));
// 			Log::info($out);

// 		}

				//error_log($out);

				return $bitstream_id;
	}




	public static function get_file_metadata($file, $mime_type = null){

		$info = PUtil::get_file_metadata_json($file,$mime_type);
		$rep = json_decode($info,true);
		return $rep;
	}

	public static function get_file_metadata_json($file, $mime_type = null){
		$nodeClient = new PNodeClientFileInfo();
		return $nodeClient->exec_json(array(
				'file'=>$file,
				'mimetype'=>$mime_type
		));
	}




	public static function frepo_finame_to_full_path($file_name, $prefix = null){
		return PUtil::frepo_finame_to_dir($file_name,$prefix) . $file_name;
	}

	public static function frepo_finame_to_dir($file_name, $prefix = null){
		$p1 = substr($file_name,0,2);
		$p2 = substr($file_name,2,2);
		$p3 = substr($file_name,4,2);
		$dst_dir =  $p1 . "/" . $p2 . "/" . $p3 ."/";
		if (! empty($prefix)){
			return ($prefix . $dst_dir);
		}
		return $dst_dir;
	}

	public static function get_assetstore_dir_name_for_file($file_name){
// 		$p1 = substr($file_name,0,2);
// 		$p2 = substr($file_name,2,2);
// 		$p3 = substr($file_name,4,2);
		//$dst_dir = Config::get('arc.ASSETSTORE_DIR') . $p1 . "/" . $p2 . "/" . $p3 ."/";
		return PUtil::frepo_finame_to_dir($file_name,Config::get('arc.ASSETSTORE_DIR'));
	}



	public static function thumb_create_dir(){
	$rnd = rand(100,499);
	$tdir = Config::get('arc.THUMBNAIL_DIR')  . $rnd;
	if (!file_exists($tdir)) {
		$cmd = ' mkdir ' . $tdir;
		$tmp = exec($cmd);
	}
	return $rnd;
}


public static function pdfinfo($dbh, $item_id,$internal_id,&$out){




	$filename  = PUtil::bitream2filename($internal_id);
	$cmd = ' pdfinfo ' . $filename;
	$cmd_out = array();
	$status = 0;
	$tmp = exec($cmd,$cmd_out,$status);

	if ($status = 1){

		foreach ($cmd_out as $line)
		{

			$SQL_DELETE = "SELECT dsd.delete_metadata(?,?)";
			$stmt_DELETE = $dbh->prepare($SQL_DELETE);

			$arr = explode(":", $line,2);
			$k = trim(str_replace(" ", "-", $arr[0]));
			$stmt_DELETE->bindParam(1, $item_id);
			$stmt_DELETE->bindParam(2, $k);
			$stmt_DELETE->execute();
			$stmt_DELETE->fetch();
		}

		$info = "";
		foreach ($cmd_out as $line)
		{

			try {
				$SQL = " SELECT dsd.insert_pdf_metadata(?,?,?)";
				$stmt = $dbh->prepare($SQL);

				$arr = explode(":", $line,2);
				$k = trim(str_replace(" ", "-", $arr[0]));
				$v = trim($arr[1]);
				$stmt->bindParam(1, $item_id);
				$stmt->bindParam(2, $k);
				$stmt->bindParam(3, $v);
				$stmt->execute();
				$r = $stmt->fetchAll();
				$info .= $line . "\n";
			} catch (PDOException $e){
				$msg = sprintf("ERROR AT insert_pdf_metadata :(%s,%s,%s)\n",$item_id, $k , $v);
				echo($msg);
				error_log($msg,0);
			}


		}
		//$out .="---------------\n";
		//$out .= $info;
		//$out .="---------------\n";

	} else {
		error_log("pdfinfo  exit-status=" . $status,0);
	}
}



public static function set_pdf_metadata($item_id, $internal_id, $title, &$out){
	//$out.= "set_pdf_metadata to: $internal_id\n";
	$mtitle = empty($title) ? "" : $title;
	if (empty($internal_id)){
		throw new Exception("set_pdf_metadata: bitstream uuid (internal_id) expected");
	}

	$txt  = "InfoKey: Creator\n";
	$txt .= "InfoValue: " . $internal_id . "\n";
	$txt .= "InfoKey: Producer\n";
	$txt .= "InfoValue: " . Config::get('arc.BITSTREAM_METADATA_PRODUCER') . "\n";
	$txt .= "InfoKey: Title\n";
	$txt .= "InfoValue: $mtitle\n";
#	$txt .= "InfoKey: ID\n";
#	$txt .= "InfoValue: " . $internal_id . "\n";
	$txt .= "InfoKey: EA.Bitstream.ID\n";
	$txt .= "InfoValue: " . $internal_id . "\n";
	#$txt .= "InfoKey: EA.Item.ID\n";
	#$txt .= "InfoValue: " . $item_id . "\n";

	$metadata_file = Config::get('arc.TMP_DIR') . "metadata-" . $internal_id . ".txt";
	$cmd="rm -f $metadata_file";
	exec($cmd);
	$fh = fopen($metadata_file, 'x') or die("can't open  $metadata_file for write");
	fwrite($fh, $txt);
	fclose($fh);

	$filename  = PUtil::bitream2filename($internal_id);

	$cmd = Config::get('arc.BIN_DIR')  . 'set-metadata.sh ' . $filename . " " . $metadata_file;
	$cmd_out = array();
	$status = 0;
	$tmp = exec($cmd,$cmd_out,$status);
	if ($status = 1){
		$info = "";
		foreach ($cmd_out as $line)
		{
			$info .= $line . "\n";
		}
		//$out .="---------------\n";
		//$out .= $info;
		//$out .="---------------\n";

	} else {
		error_log("metadata_basic exit-status=" . $status,0);
	}

// $out.= "/set_pdf_metadata\n";
}


public static function create_bitstream_thumbnail($bitstream_id, $width = null, $height=null, $ext=null, $page_no=null){

	if (empty($width) && empty($height)){
		throw new Exception('width or height expected');
	}


	if (empty($ext)){
		$ext = 'png';
	}
	if (empty($page_no)){
		$page_no = 0;
	}

	$dbh = dbconnect();

	$SQL="SELECT item,internal_id from public.bitstream WHERE bitstream_id = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bitstream_id);
	$stmt->execute();
	if (!$row = $stmt->fetch()){
		throw new Exception('item not found');
	}
	$item_id  = $row[0];
	$internal_id = $row[1];



	$thumb_type = 10;

	if(empty($width) && !empty($height)){
		$file_part = sprintf('_x%s',$height);
		$size = sprintf('x%s',$height);
	} elseif (!empty($width) && empty($height)){
		$file_part = sprintf('%sx_',$width);
		$size = sprintf('%sx',$width);
	} else  {
		$file_part = sprintf('%sx_%s',$width,$height);
		$size = sprintf('%sx%s',$width,$height);
	}


	$tdir = PUtil::thumb_create_dir();
	$extension = $ext;
	$thumbfile = sprintf('%s/th_b%s_custom-%s.%s',$tdir,$bitstream_id,$file_part,$extension);
	$full_path = Config::get('arc.THUMBNAIL_DIR') . $thumbfile;

	if (file_exists($full_path)){
		unlink($full_path);
	}

	$filename = PUtil::bitream2filename($internal_id);
	$cmd = sprintf('%sconvert -thumbnail %s %s[%s] %s',Config::get('arc.BIN_DIR'),$size,$filename,$page_no,$full_path);
	echo("$cmd\n");
	$tmp = exec($cmd,$out,$status);

	if (file_exists($full_path)){
		$SQL = "insert into dsd.thumbs (item_id,file,idx,idxf,ttype,extension) values (?,?,null,null,?,?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->bindParam(2, $thumbfile);
		$stmt->bindParam(3, $thumb_type);
		$stmt->bindParam(4, $extension);
		$stmt->execute();

	} else {
		return null;
	}

	return $thumbfile;
}


//@DocGroup(module="util", group="general", comment="extract_file_info_from_upload_form")
// public static function extract_file_info_from_upload_form($fild_name){
//   Log::info("extract_file_info_from_upload_form: " . $fild_name);

//   //$upload_file_data = array();
//   $app= App::make('arc');
//   $upload_files = $app->upload_files;

//   if (isset($upload_files[$fild_name])){
//   	$files = $upload_files[$fild_name];
//   	if (count($files) > 0){
//   		$f = array_shift($files);
//   		return array($f[0], $f[1],$f[2]);
//   	}
//   }
//   return null;

// //   Log::info(print_r($upload_file_data,true));

// // //   [0] => Array
// // //   (
// // //             [0] => Lewis_Carroll_-_Alice's_Adventures_in_Wonderland.epub
// // //             [1] => /tmp/archive_fu_55a295b8df9b1
// // //             [2] => epub
// // //             [3] => application/epub+zip
// // //             [4] => 3205893
// // //         )



// // 	$upload_file_name = null;
// // 	$upload_file_path = null;
// // 	$upload_file_ext = null;

// // 	$uploadData = isset($_FILES[$fild_name])?$_FILES[$fild_name] : null ;
// // 	if (! empty( $uploadData) && !empty($uploadData['name'][0])){
// // 		$file_name = $uploadData['name'][0];
// // 		$file =  $uploadData['tmp_name'][0];
// // 		preg_match('/\.(\w+)$/', $file_name, $matches);
// // 		$ext = $matches[1];
// // 		if (! empty($ext)){
// // 			$upload_file_name = $file_name;
// // 			$upload_file_path = $file;
// // 			$upload_file_ext = $ext;
// // 		}
// // 	}

// // 	return array($upload_file_name, $upload_file_path,$upload_file_ext);
// }


//@DocGroup(module="util", group="AAA", comment="item_submiter")
public static function user_access_item_submiter(){

	return ArcApp::has_permission(Permissions::$ITEM_SUBMITER ) && ! ArcApp::has_permission(Permissions::$REPO_MAINTAINER);

// 	}
// 	if (user_access(Config::get('arc.PERMISSION_ITEM_SUBMITER')) && ! user_access(Config::get('arc.PERMISSION_REPO_ADMIN')) && ! user_access(Config::get('arc.PERMISSION_REPO_MENTAINER'))){
// 		return true;
// 	}
// 	return false;
}


public static function create_item_basic_data($uuid, $item_metadata, $bibref){
	$rep = new ItemBasicData();
	$rep->item_metadata = $item_metadata;
	$rep->bibref = $bibref;
	$rep->uuid = $uuid;
	return $rep;
}


public static function get_item_basic_data($dbh, $item_id){

	$SQL="select  bibref,uuid FROM dsd.item2 WHERE item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	if (!( $res = $stmt->fetch())){
		return null;
	}

	$bibref = 0;
	if (!empty($res[0])){
		$bibref = 1;
	}
	$uuid = $res[1];

	$item_metadata = PDao::get_item_metadata($item_id);

	$rep = PUtil::create_item_basic_data($uuid, $item_metadata, $bibref);
	return $rep;
	// 	$rep = new ItemBasicData();
	// 	$rep->item_metadata = $item_metadata;
	// 	if (empty($res[0])){
	// 		$rep->bibref = 0;
	// 	} else {
	// 		$rep->bibref = 1;
	// 	}
	// 	$rep->uuid = $res[1];
	// 	return $rep;
}


public static function get_file_from_url($url, $file_prefix, $echo_flag = true){

	$upload_file_name = null;
	$upload_file_path = null;
	$upload_file_ext = null;


	$file_name = null;
	$file_content_type = null;
	$file_ext = null;
	$file_path = null;
	//$cmd = BIN_DIR  . "url_get.sh '" . $url . "' "   . $item_id . '_' . $seq_id . '_';
	$cmd =  Config::get('arc.BIN_DIR')   . "url_get.sh '" . $url . "' '". $file_prefix ."'";

	if ($echo_flag){
		echo(" $cmd\n");
	}
	$cmd_out = array();
	$status = 0;
	$tmp = exec($cmd,$cmd_out,$status);
	if ($status = 1){
		foreach ($cmd_out as $line)
		{
			if ($echo_flag){
				echo "$line\n";
			}
			preg_match('/#FILE_PATH:\s+([\w|\_|\-|\.|\/]+)$/', $line, $matches);
			//print_r($matches);
			if (! empty($matches)){
			$file_path = $matches[1];
			}
			preg_match('/#FILE_NAME:\s+([\w|\_|\-|\.|\/]+)$/', $line, $matches);
			//print_r($matches);
			if (! empty($matches)){
			$file_name = $matches[1];
			}
			preg_match('/#EXTENSION:\s+(\w+)$/', $line, $matches);
			//print_r($matches);
			if (! empty($matches)){
			$file_ext = $matches[1];
			}
				preg_match('/#CONTENT_TYPE:\s+([\w|\_|\-|\.|\/]+)$/', $line, $matches);
				//print_r($matches);
				if (! empty($matches)){
				$file_content_type = $matches[1];
			}
		}
	} else {
		if ($echo_flag){
			echo("ERROR\n");
		}
			error_log("error_img_url_get.sh" . $status,0);
	}


	if (! empty($file_name) && ! empty($file_path) && ! empty($file_content_type)  && ! empty($file_ext)){
		$upload_file_name = $file_name;
		$upload_file_path = $file_path;
		$upload_file_ext = $file_ext;
	}

	return array($upload_file_name, $upload_file_path, $upload_file_ext);

}


public static function find_contributor($name, $limit = 30){
	$elements  = array_keys(Lookup::getContributors());
	$elements[] = 'dc:publisher:';
	$obj_types = array('actor','auth-person','auth-organization','auth-family');
	return PDao::find_node_for_link($name,$elements,$obj_types,true,$limit);
}


public static function find_work($name, $limit = 30){
	$elements  = array('ea:work:');
	$obj_types = array('work','auth-work');
	return PDao::find_node_for_link($name,$elements,$obj_types,false,$limit);
}


//@DocGroup(module="save", group="php", comment="save_basic_metadata")
#BASIC METADATA
/**
 *
 * @param PDO $dbh
 * @param unknown $item_id
 * @param ItemMetadata $idata
 * @param unknown $out
 */
public static function save_basic_metadata($dbh, $item_id, $idata, &$out, $params = array()){


		$idata->generate();


		$options = $idata->getFirstItemValue('trn:options:');

		$idata->setTreeLevels();

		//echo("<pre>");
		foreach ($idata->values as $key => $values) {
		//	Log::info("update_item_metadata: $key    \n");
			//print_r($values);
			if (! PUtil::strBeginsWith($key, 'tmp:')  && ! PUtil::strBeginsWith($key, 'trn:') && ! PUtil::strBeginsWith($key, 'reverse:') ){
				//@DOC: RELATIONS  $permit_relation_inference  = false, $save_inferred = true
				PDao::update_item_metadata($item_id, $key, $values,false,false,false);
			}
		}
		//echo("</pre>");

		$obj_type = $idata->getValueTextSK("ea:obj-type:");
		$obj_class = PDao::get_obj_class_from_obj_type($obj_type);

		if ($obj_class == 'artifact'){
			$idata->setValueSK('ea:ref-item:id',$item_id);
			PDao::save_artifact($idata);
		}

		PDao::update_item2($item_id, $idata);



// 		$dbh = dbconnect();
// 		$SQL="Select metadata_value_id, item_id, element, ref_item, link,inferred from dsd.metadatavalue2 where item_id  = ? order by inferred,ref_item;";
// 		$stmt = $dbh->prepare ( $SQL );
// 		$stmt->bindParam ( 1, $item_id );
// 		$stmt->execute();
// 		Log::info(print_r($stmt->fetchAll(),true));


	}


	/**
	 *
	 * @param PDO $dbh
	 * @param unknown $item_id
	 * @param ItemMetadata $idata
	 * @param unknown $out
	 */
	public static function save_basic_metadata_batch_simple($dbh, $item_id, $idata){
		$idata->setTreeLevels();
		foreach ($idata->values as $key => $values) {
			//Log::info("update_item_metadata: $key    \n");  //print_r($values);
			if (! PUtil::strBeginsWith($key, 'tmp:')  && ! PUtil::strBeginsWith($key, 'trn:') && ! PUtil::strBeginsWith($key, 'reverse:') ){
				//@DOC: RELATIONS  $permit_relation_inference  = false, $save_inferred = true
				PDao::update_item_metadata($item_id, $key, $values,false,false,false);
			}
		}
	}


	public static function submit_from_spool($dbh, $edoc, $item_id, &$out){

	$bundle_name_original="ORIGINAL";
	$bundle_id_original = PDao::insert_bundle($dbh, $bundle_name_original);
	PDao::insert_item2bundle($dbh, $item_id, $bundle_id_original);

	$edoc = str_replace('//', '/', $edoc);

	if (PUtil::strBeginsWith( $edoc , '/docs/')){

		$edoc_file_name = substr($edoc,6);
		$edoc_file_seq = 1;
		$edoc_file =  Config::get('arc.SPOOL_dir_pending') . $edoc_file_name;
		$uuid = PDao::get_safe_uiid($dbh);
		$bitstream_id = PUtil::create_bitstream($dbh, $uuid, $edoc_file, $edoc_file_name, $edoc_file_seq, SpoolUtil::getOKSpoolDir());
		$out.="create doc bitstream: $bitstream_id , $uuid , ($edoc_file_seq) , $edoc_file_name \n";
		PDao::insert_bundle2bitstream($dbh, $bundle_id_original, $bitstream_id);



	} else if (PUtil::strBeginsWith( $edoc , '/sites/')){

		$bundle_name_src="SRC";
		$bundle_id_src = PDao::insert_bundle($dbh, $bundle_name_src);
		PDao::insert_item2bundle($dbh, $item_id, $bundle_id_src);

		$edoc_file_name = substr($edoc,7);
		$edoc_file_seq = 1;
		$edoc_file =  Config::get('arc.SPOOL_dir_sites_pending') . $edoc_file_name;
		$uuid = PDao::get_safe_uiid($dbh);
		$bitstream_id = PUtil::create_bitstream($dbh, $uuid, $edoc_file, $edoc_file_name, $edoc_file_seq,  Config::get('arc.SPOOL_dir_sites_ok'));
		$out.="create site bitstream img: $bitstream_id , $uuid , ($edoc_file_seq) , $edoc_file_name \n";
		PDao::insert_bundle2bitstream($dbh, $bundle_id_original, $bitstream_id);


		#preg_match('/\/(\w+)\.\w\w\w$/', $edoc, $matches);
		#$file_base = $matches[1];
		#$edoc_ok_file_name = $file_base . ".html";

		$base = substr($edoc_file_name,0,(strlen($edoc_file_name) -4));
		$edoc_file_name = $base . ".html" ;

		$edoc_file_seq = 2;
		$edoc_file =  Config::get('arc.SPOOL_dir_sites_pending') . $edoc_file_name;
		$uuid_tmp = PDao::get_safe_uiid($dbh);
		$bitstream_id = PUtil::create_bitstream($dbh, $uuid_tmp, $edoc_file, $edoc_file_name,$edoc_file_seq,Config::get('arc.SPOOL_dir_sites_ok'));
		$out.="create site bitstream src: $bitstream_id , $uuid_tmp , ($edoc_file_seq) ,  $edoc_file_name \n";
		insert_bundle2bitstream($dbh, $bundle_id_src, $bitstream_id);



		$edoc_file_name = $base . ".txt";
		$edoc_file =  Config::get('arc.SPOOL_dir_sites_pending') . $edoc_file_name;
		$cmd = "mv  $edoc_file " .  Config::get('arc.SPOOL_dir_sites_ok');
		$out.= "mv: $edoc_file " .  Config::get('arc.SPOOL_dir_sites_ok') . "\n";
		$cmd_out = array();
		$status = 0;
		$tmp = exec($cmd,$cmd_out,$status);


	}

	return $uuid;
}


public static function extract_extension($file_name){
	$pattern = '/\.(\w+)$/';
	preg_match($pattern, $file_name, $matches, PREG_OFFSET_CAPTURE);
	$ext = $matches[1][0];
	return $ext;
}


public static function correct_file_privs($filename){
	$cmd = ' chmod +rw ' . $filename;
	$cmd_out = array();
	$status = 0;
	$tmp = exec($cmd,$cmd_out,$status);

	$cmd = ' chmod g+rw ' . $filename;
	$cmd_out = array();
	$status = 0;
	$tmp = exec($cmd,$cmd_out,$status);
}


//@DocGroup(module="util", group="archive", comment="correct-privs")
public static function corect_privs($internal_id){
	$filename  = PUtil::bitream2filename($internal_id);
	PUtil::correct_file_privs($filename);
}



public static function find_place($name, $limit = 30){
	$elements  = array('ea:publication:place','ea:publication:printing-place');
	$obj_types = array('place');
	return PDao::find_node_for_link($name,$elements,$obj_types,false,$limit);
}


public static function get_year_from_date($date){
	if(!empty($date)){
		$date_arr = $date->data();
		 if(isset($date_arr['json']['y'])){
			$date_txt = $date_arr['json']['y'];
			$year = empty($date_txt)? null :htmlspecialchars($date_txt) ;
			return $year;
		 }
		return null;
	}
	return null;
}

public static function get_punctuation($item){
	if(!empty($item)){
		$punc_arr = $item->data();
		if(isset($punc_arr['prps']['pnctn'])){
			$punc_txt = $punc_arr['prps']['pnctn'];
			$punctuation = empty($punc_txt)? null :htmlspecialchars($punc_txt) ;
			return $punctuation;
		}
		return null;
	}
	return null;
}

/**
 *
 * @param Console_Table $table
 * @param string $left_padding_string
 */
Public static function printConsoleTable($table,$options=array()){
	$methd = 0;
	if (!empty($options) && !empty($options['method'])){
		$method = $options['method'];
	}

	$table_string = $table->getTable();
	if ($method == 1){
		$left_padding_string = isset($options['left_padding']) ? $options['left_padding'] : '';
		$out = '';
		$table_lines = explode("\n",$table_string);
		foreach ($table_lines as $line){
			if (PUtil::strBeginsWith($line,'|')){
				//$out .= $left_padding_string . substr($line,2) ."\n";
				$out .= $left_padding_string . $line ."\n";
			}
		}
	} else {
		$out = $table_string;
	}
	return $out;
}


	public static function digital_item_type($file_name){

// 		$accepted_ext = array('pdf','daisy','epub','docx','wma','mp3','zip','7z');
		$item_type_map = Setting::get('item_type_map');
		$accepted_ext = array();

		foreach ($item_type_map as $item_type_key => $item_type_value){
			$accepted_ext[] = $item_type_key;
		}
		$ext = substr(strrchr($file_name, '.'), 1);
		if (!empty($ext)){
			$ext = strtolower($ext);
			if (in_array($ext,$accepted_ext)){
				return $ext;
			}
		}

		return null;
	}


public static  function upload_bitstream_from_post_data($item_id){

	Log::info("upload_bitstream_from_post_data: " . $item_id);

	$dbh = dbconnect();

	$seq_id = get_post('seq_id',null);
	$bundle_name = get_post('bundle','ORIGINAL');

	$thumb_description = get_post('thumb_desc',null);

	$create_item_flag = false;
	if ($bundle_name == 'ORIGINAL_CI'){
		$create_item_flag = true;
		//Log::info("ORIGINAL_CI");
		$bundle_name = 'ORIGINAL';
	}

	$userName = ArcApp::username();

	$upload_file_name = null;
	$upload_file_path = null;
	$upload_file_ext = null;

	$upload_file_data = array();
	$app= App::make('arc');
	$upload_files = $app->upload_files;

	if (isset($upload_files['uploadedfile'])){
		$files = $upload_files['uploadedfile'];
		foreach ($files as $f){
			$upload_file_data[] = array($f['name'],$f['tmp_name'],$f['extension'],$f['type'],$f['size']);
		}
	}
	//print_r($upload_file_data);
	// 	$uploadData = isset($_FILES['uploadedfile'])?$_FILES['uploadedfile'] : null ;

	// 	if (! empty( $uploadData)){
	// 		//list($upload_file_name, $upload_file_path,$upload_file_ext)= extract_file_info_from_upload_form('uploadedfile');
	// 				if ( !empty( $uploadData ['name'][0])){
	// 						$upload_file_data = PUtil::extract_files_info_from_upload_form('uploadedfile');
	// 				}else{
	// 						$upload_file_data=array();
	// 				}
	// 	}


	$upload_url = get_post('upload_url');
	if (! empty($upload_url)){
		$file_prefix = $item_id . '_' . $seq_id . '_';
		echo("<pre>");
		//list($upload_file_name, $upload_file_path,$upload_file_ext) = get_file_from_url($upload_url, $file_prefix, true);
		$tmp = PUtil::get_file_from_url($upload_url, $file_prefix, true);
		echo("</pre>");
		$upload_file_data = array($tmp);
	}

	if (empty($seq_id)){
		$seq_id   = PDao::get_bitstream_next_seq_id($item_id);
	}

// 	echo "<pre>";
	$c = 0;
	$cc = 0;


	if(!empty($upload_file_data)){


		foreach ($upload_file_data as $idx => $file_data) {
	 	$seq_id_ok =  $seq_id + $c;
	 	$c += 2;


	 	//Log::info(print_r($file_data,true));

	 	//list($upload_file_name, $upload_file_path,$upload_file_ext, $upload_file_type, $upload_file_size) = $file_data;
	 	$upload_file_name = $file_data[0];
	 	$upload_file_path = $file_data[1];
	 	$upload_file_ext = $file_data[2];
	 	//$mime_type = $file_data[3];
	 	if (! empty($upload_file_name) && ! empty($upload_file_path) && ! empty($upload_file_ext)) {
	 		$cc++;

// 	 		echo $description;
	 		list($bitstream_id,$bundle_id,$uuid) = PUtil::upload_bitsteam($upload_file_path, $upload_file_name, $upload_file_ext, $item_id, $seq_id_ok, $bundle_name, $thumb_description);
	 		$SQL = "SELECT count(*) from dsd.item_bitstream where item_id = ?";
	 		$stmt = $dbh->prepare($SQL);
	 		$stmt->bindParam(1, $item_id);
	 		$stmt->execute();
	 		$r = $stmt->fetch();
	 		if ($r[0] == 1){
	 			$out="thumb_generate for item\n";
	 			$all_flag = true;
	 			$parent_flag = true;
	 			PDao::thumbs_generate_from_bitstream($dbh, $uuid, $out,$all_flag,$parent_flag);
	 		}

	 		if ($create_item_flag){
	 			$idata = new ItemMetadata ();
	 			$uuid = PDao::createUUID();

	 			//$title = 'item: ' . $uuid;
	 			$title = $upload_file_name;
	 			$obj_type = "digital-item";
	 			$status = "finish";

	 			$user = ArcApp::user();
	 			$org_id = $user['org_id'];
	 			$digital_item_type=  PUtil::digital_item_type($upload_file_name);

	 			$w = 0;
	 			$idata->addValueSK ( DataFields::ea_obj_type, $obj_type, null, null, null, null, null, null, $w ++ );
	 			$idata->addValueSK ( DataFields::dc_title, $title, null, null, null, null, null, null, $w ++ );
	 			$idata->addValueSK ( 'ea:artifact-of:',$item_id , null, null, null, $item_id, null, null, $w ++ );
	 			$idata->addValueSK ( 'ea:status:', $status  , null, null, null, null, null, null, $w ++ );

				if (!empty($org_id)){
					$idata->addValueSK ( 'ea:item:sublocation',$org_id , null, null, null, $item_id, null, null, $w ++ );
				}
				if (!empty($digital_item_type)){
					$idata->addValueSK ( 'ea:item:type',$digital_item_type , null, null, null, $item_id, null, null, $w ++ );
				}

	 			$is = new ItemSave();
	 			$is->setIdata($idata);
	 			$is->setUserName($userName);

	 			$nitem_id = $is->save_item();
	 			PDAO::move_bitstream($bitstream_id, $nitem_id);

	 		}
	 		else{

// 	 			$full_resest = function () {
// 	 				Log::info("GRAPH RESET FULL");
// 	 				$con = dbconnect();
// 	 				$SQL = "DELETE FROM dsd.metadatavalue2 WHERE inferred";
// 	 				$stmt = $con->prepare($SQL);
// 	 				$stmt->execute();

// 	 				// $graph = GGraphIO::loadGraph();
// 	 				// $des1 = $graph->getInferredEdges();
// 	 				$graph = new GGraphO();

// 	 				$rules = Config::get('arc_rules.DEFAULT_RULES', array());
// 	 				$rule_mem = Config::get('arc_rules.INIT_MEMORY', array());
// 	 				$re = new GRuleEngine($rules, $rule_mem, $graph);
// 	 				$context = $re->execute();

// 	 				// 			$des2 = $graph->getInferredEdges();
// 	 				// 			foreach ( $des2 as $e ) {
// 	 				// 				GGraphUtil::saveEdge($e);
// 	 				// 			}

// 	 					$eps = $context->getEditPropUrns();
// 	 					foreach ( $eps as $urnStr ) {
// 	 						$v = $graph->getVertex($urnStr);
// 	 						$elements = $context->getEditProps($urnStr);
// 	 						GGraphUtil::saveProperties($v, $elements);
// 	 					}
// 	 					return $context;
// 	 				};

	 				$item_graph_save = function ($itemId) {
	 					Log::info("GRAPH RESET ITEM: " . $itemId);

	 					$idata = PDao::getItemMetadata($itemId);
	 					$is = new ItemSave();
	 					$is->setIdata($idata);
	 					$is->setItemId($itemId);
	 					$is->setSubmitId("1");
	 					$item_id = $is->save_item();
	 					$context = $is->getRuleContext();
	 					return $context;
	 				};

	 				$context = $item_graph_save ($item_id);
	 		}

	 	}
	 }
	}

// 	echo "</pre>";
}


	public static function explodeIdFacet($value){
		if (!empty($value)){
			$value_expl = explode("‡", $value);
			return $value_expl[0];
		}

		return null;
	}


	public static function getItemValueArrLabel($value){
		$title = $value[0];
		$sel_val = $value[5];

		if (!empty($sel_val)){
			$ri = $value[4];
			if (!empty($ri) && isset($sel_val['data']) && isset($sel_val['data']['ref_label']) && !empty($sel_val['data']['ref_label'])){
				$title  = $sel_val['data']['ref_label'];
			} else if(isset($sel_val['prps']) && isset($sel_val['prps']['pnctn']) && !empty($sel_val['prps']['pnctn'])){
				$title =$sel_val['prps']['pnctn'];
			}

		}
		return $title;
	}



	/**
	 *
	 * @param array() $vdata
	 * @param number $method
	 */
	public static function clearJdata($vdata, $method = 1){

		if (isset($vdata['data'])){
			unset($vdata['data']['level']);
			if (empty($vdata['data'])){
				unset($vdata['data']);
			}
		}

		return $vdata;

	}

	/**
	 *type 1 PRIN TA DEDOMENA PANE STIN CLIENT
	 *type 2 META TIN EPISTROFI TON  DEDOMENON APO TON CLIENT
	 *
	 * @param ItemMetadata $idata
	 */
	public static function changeRelation($idata, $type = 1){
		//@DOC: RELATIONS  change Infernece

		$debugF= (Config::get('arc.DEBUG_RELATIONS',1) >0);
		if ($debugF){ Log::info('@@: changeRelation: ' . $type); };

		if ($type == 1){

			$fn0 = function($rel,$key,$vals) use ($debugF){
				if ($rel->forStep1Rename()){
					if ($debugF){ Log::info('@@: RENAME: ' .  $key);};
					return $rel->getStep1Rename();
				}
				return false;
			};

			$fn1 = function($rel,$key,$vals) use ($debugF){
				$flag1 = false;
				if ($rel->forInferenceReverse()){
					//Log::info(print_r($vals,true));
					foreach ($vals as $idx => $val){
						//if (! empty($val[9]) && $val[3]){//RELTYPE & INFERRED
						if (!empty($val[9])){
							$flag1 = true;
							if ($debugF){ Log::info('@@: CHANGE INFERENCE: ' . $val[0] . ' : ' . $key . ' ref: ' . $val[4]);};
							$iv = ItemValue::c($val,$key);
							$iv->setToJData('set_infrnc',false );
							$iv->setRelation($rel->getRelType());
							$iv->setInferred(false);
							$vals[$idx] = $iv->valueArray();
						}
					}
				}
				if ($flag1){
					return $vals;
				}
				return false;
			};

		} else if ($type == 2) {

			$fn0 = function($rel,$key,$vals) use ($debugF){
				if ($rel->forStep2Rename()){
					if ($debugF){ Log::info('@@: RENAME: ' .  $key);};
					return $rel->getStep2Rename();
				}
				return false;
			};

			$fn1 = function($rel,$key,$vals) use ($debugF){ return false;};

		} else {
			throw new Exception('CHANGE RELATION UNKNOWN TYPE: ' . $type);
		}



		$flag1 = false;
		$relCtrl = new RelationControl();//RC
		$values = $idata->values;
		foreach ($values as $key => $vals){
			if (empty($vals)){
				continue;
			}
			if ($relCtrl->isRelation($key)){
				$k = $key;
				$rel = $relCtrl->getRelation($key);
				$nk = $fn0($rel, $key, $vals);
				if (!empty($nk)){
					$flag1 = true;
					unset($values[$key]);
					$values[$nk] = $vals;
					$k = $nk;
				}
				$nvals  = $fn1($rel, $k, $vals);
				if (!empty($nvals)){
					$flag1 = true;
					$values[$k] = $nvals;
				}
			}
		}
		if ($flag1){
			$idata->values = $values;
		}

		return $idata;
	}





	public static final function getSolrConfigEndPoints($index = 'opac'){
		$solr_endpoints_all = Config::get('arc.SOLR_ENDPOINTS', null);

		$solr_endpoints = isset($solr_endpoints_all[$index]) ? $solr_endpoints_all[$index] : null;
		if (empty($solr_endpoints)) {
			if ($index == 'staff'){
				$solr_endpoints = array(
					'localhost' => array(
						'host' => '127.0.0.1',
						'port' => '8983',
						'path' => '/solr/',
						'core' => 'staff_index'
					)
				);
			} else {
				$solr_endpoints = array(
					'localhost' => array(
						'host' => '127.0.0.1',
						'port' => '8983',
						'path' => '/solr/',
						'core' => 'opac_index'
					)
				);
			}
		}
		return $solr_endpoints;
	}



}


class SpoolUtil
{


	public static final function getOrganizationPrefix()
	{
		$prefix = null;
		$user = ArcApp::user();
		$organization = empty($user) ? null : $user['org_id'];
		if (!empty($organization) && !empty(Config::get('arc.SPOOL_ORGANIZATION'))) {
			$prefix .= 'org_' . $organization;
		}
		return $prefix;
	}

	private static final function _getSpoolDir($dir)
	{
		$orgPrefix = SpoolUtil::getOrganizationPrefix();
		if (empty($orgPrefix)) {
			return $dir;
		}
		$directory = $dir . '/' . $orgPrefix . '/';
		$directory = str_replace('//', '/', $directory);
		return $directory;
	}

	public static final function getPendigSpoolDir()
	{
		$dir = Config::get('arc.SPOOL_dir_pending');
		return SpoolUtil::_getSpoolDir($dir);
	}

	public static final function getOKSpoolDir()
	{
		$dir = Config::get('arc.SPOOL_dir_ok');
		return SpoolUtil::_getSpoolDir($dir);
	}


}


class JsonHelper {

	/**
	 *
	 * @var array
	 */
	private $data;

	/**
	 *
	 * @param array $jsonData
	 */
	public function __construct($jsonData) {
		if (empty($jsonData)) {
			$this->data = array();
		} else {
			if (is_string($jsonData)){
				$this->data = json_decode(jsonData, true);
			} else if (is_array($jsonData)){
				$this->data = $jsonData;
			} else {
				throw new Exception("jsondata unknown tupe (mas be string or array)");
			}
		}
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	public function put($k,$v){
		$this->data[$k]= $v;
	}


	public function get($k){
		return $this->data[$k];
	}

	public function merge($data){
		$this->data = array_merge($this->data , $data);
	}

	public function getJSON(){
		return  json_encode($this->data);
	}

}
