<?php

class ArchiveController extends BaseController {





	function parchive_term($term = null) {
	PUtil::populateRequestARGV($term);
	if (empty($term)) {
		$term = get_get('t');
	}
	$_REQUEST['term'] = $term;
	$this->show();
}




function parchive_display_item_xml($item_id = null){
	Log::info("parchive_display_item_xml: " . $item_id);
	if (empty($item_id)){
		return null;
	}


	$itemBasic = PDao::getItemBasic($item_id);
	if (! empty ($itemBasic)){
		//$item = PDao::getItemMetadata($itemId);

	//	Log::info(print_r($itemBasic,true));

		/* @var $graph GGraphO  */
		$graph = GGraphIO::loadNodeSubGraph($item_id);
		$_REQUEST['item_basic'] = $itemBasic;
		$_REQUEST['graph'] = $graph;
		return Response::view('foaf')->header('Content-Type', 'text/xml');
	}
	return null;


// 	$response = Response::make('<t1></t1>');
// 	$response->header('Content-Type', 'text/xml');
// 	return $response;
}



function parchive_display_item_europeana($search_id = null) {
	// validate search id
	$search_id = (empty($search_id)) ? null : intval($search_id);
	if (empty($search_id)) {
		$response = Response::make("EMPTY ID\n", 404)->header('Content-Type', 'text/plain');
		return $response;
	}





	//$dbh = dbconnect();
	//$q = $dbh->prepare("SELECT item_id FROM dsd.metadatavalue2 WHERE element = 'ea:europeana:key1' AND text_value = ?");
	$ps1 =  prepareService();
	$q = $ps1->prepareNamed($ps1::$METADATAVALUE2_EUROPEANA);
	$q->bindParam(1, $search_id);
	$q->execute();
	$r = $q->fetch();

	$jsonData = null;

	if ($r) {
		$item_id = $r[0];
		$keys = array(DataFields::ea_obj_type, DataFields::ea_status, DataFields::dc_title, 'ea:europeana:key1', 'ea:europeana:json-data', 'ea:europeana:type');
		$_idata = PDao::get_item_metadata($item_id, $keys);
//		$type = $_idata->getFirstItemValueOrEmpty('ea:europeana:type')->textValue();
		$jsonData = $_idata->getFirstItemValueOrEmpty('ea:europeana:json-data')->textValue();
	}

	if (!empty($jsonData)) {
		$_REQUEST['data'] = json_decode($jsonData, true);
		$_REQUEST['item_id'] = $item_id;
		return Response::view('europeana-agent')->header('Content-Type', 'text/xml');
	}


	//Log::info('europeana item not found: ' . $search_id);
	$response = Response::make("Not Found\n", 404)->header('Content-Type', 'text/plain');
	return $response;
}


function parchive_europeana_agent_search($search_string = null){
	//Log::info('parchive_europeana_agent_search: ' . $search_string);
	if (empty($search_string)){return null;}
	$search_string = preg_replace('/\s+/', '', $search_string);
	if (empty($search_string)){return null;}

	$dbh = dbconnect();
	$keys = array(DataFields::ea_obj_type, DataFields::ea_status, DataFields::dc_title,
			'ea:europeana:key2', 'ea:europeana:json-data','ea:europeana:type');
	$q = $dbh->prepare("SELECT item_id  FROM dsd.metadatavalue2  WHERE element = 'ea:europeana:key2' AND text_value = ?");
	$q->bindParam(1, $search_string);
	$q->execute();
	$jsonData = null;
	$item_id = null;
	while (empty($jsonData) && $r = $q->fetch()){
		$item_id = $r[0];
		$_idata = PDao::get_item_metadata($item_id,$keys);
		$type = $_idata->getFirstItemValueOrEmpty('ea:europeana:type')->textValue();
		if ($type == 'agent'){
			$jsonData =$_idata->getFirstItemValueOrEmpty('ea:europeana:json-data')->textValue();
		}
	}
	if (! empty($jsonData)){
		$_REQUEST['data'] = json_decode($jsonData, true);
		$_REQUEST['item_id'] = $item_id;

		return Response::view('europeana-agent')->header('Content-Type', 'text/xml');
	}

	$response = Response::make("Not Found\n", 404)->header('Content-Type', 'text/plain');
	return $response;
}



function parchive_display_item($pparent=null, $pitem=null) {
	//Log::info("#1");

	PUtil::populateRequestARGV($pparent,$pitem);

	$item_id = $pitem;
	$parent_item_id = $pparent;
	if ($parent_item_id == ''){
		$parent_item_id = null;
	}

	if (empty($item_id) || !PUtil::test_int($item_id)) {
		error_log("parchive_item  err: ##1: " . $item_id  . '>');
		return;
	}
	$display_method = PUtil::reset_int(get_get("d"), 1);

	$_REQUEST['item_id'] = $item_id;
	$_REQUEST['display_method'] = $display_method;
	$_REQUEST['parent_item_id'] = $parent_item_id;


	$itemrec = PDao::getItemBasic($item_id);
	$obj_type = $itemrec['obj_type'];
	$app = App::make('arc');
	$app->title = ($itemrec['label']);

	//Log::info("#2");

// 	$dconf = variable_get('patron_display',array());
	$dconf = Config::get('arc_display_command.patron_display');


	if (isset($dconf[$obj_type])){
		$_REQUEST['item_basics'] = $itemrec;
		//$rep = theme($variables = array('item-cmds'));
		//return $rep;

		//$app = App::make('arc');
		$app->template='public.item-patron';
		$this->show();
		return;
	}

	$item = PDao::getItem($item_id);
	if (empty($item)) {
		error_log("parchive_item err: ##3");
		return;
	}


	$_REQUEST['item'] = $item;

	if (isset($_GET['json'])) {
		return PUtil::parchive_item_json();
	}

	$obj_type = $item['type'];
	if ($obj_type == Config::get('arc.DB_OBJ_TYPE_BITSTREAM')) {
		$bm = $item['primary_bitstream'];
		if (!empty($bm)) {
			$mimetype = $bm['mimetype'];
			if ($mimetype == 'image/jpeg' || $mimetype == 'image/png') {
				$_REQUEST['mimetype'] = $mimetype;
// 				$rep = theme($variables = array('item-image'));
// 				return $rep;
				//$app = App::make('arc');
				$app->template='public.item-image';
				$this->show();
				return;
			}
			if ($mimetype == 'application/pdf' || $mimetype == 'application/x-cbr' || $mimetype == 'image/vnd.djvu') {
				$_REQUEST['mimetype'] = $mimetype;
// 				$rep = theme($variables = array('item-ddoc'));
// 				return $rep;
				//$app = App::make('arc');
				$app->template='public.item-ddoc';
				$this->show();
				return;

			}
		}
	}

	$app->template = 'public.item';
	$this->show();
}


function parchive_search_general() {

	$term = null;
	$term = get_get("term");

	if (empty($term)) {
		return;
	}
	$rep = PDao::search_metadata_all($term);

// 	drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
// 	drupal_add_http_header('Content-type', 'application/json');
// 	echo json_encode($rep);
	$response = Response::make($rep, 200);
	$response->header('Cache-Control', 'no-cache, must-revalidate');
	$response->header('Content-Type', 'application/json');
	return $response;

}

function parchive_search_title() {

	$term = get_get("term");
	if (empty($term)) {
		return;
	}//'marc:title-statement:title'
	$rep = PDao::search_metadata(array(DataFields::ea_title_uniform, 'marc:title-statement:title'), $term);

// 	drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
// 	drupal_add_http_header('Content-type', 'application/json');
// 	echo json_encode($rep);
	$response = Response::make($rep, 200);
	$response->header('Cache-Control', 'no-cache, must-revalidate');
	$response->header('Content-Type', 'application/json');
	return $response;
}

function parchive_search_terms() {
	$term = null;
	$term = get_get("term");
	if (empty($term)) {
		return;
	}
	Log::info("parchive_search_terms: " . $term);
	//$rep = search_metadata_ac($term,DB_METADATA_FIELD_DC_SUBJECT);//57
	$rep = PDao::search_metadata(DataFields::dc_subject, $term,null,null,30);

	//      drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
	//      drupal_add_http_header('Content-type', 'application/json');
	//      echo json_encode($rep);
	$response = Response::make($rep, 200);
	$response->header('Cache-Control', 'no-cache, must-revalidate');
	$response->header('Content-Type', 'application/json');
	return $response;

}


function parchive_search_author() {

	$term = null;
	$term = get_get("term");
	if (empty($term)) {
		return;
	}

	//$rep = search_metadata_ac($term, DB_METADATA_FIELD_DC_AUTHOR);
	$rep = PDao::search_metadata(array(DataFields::dc_contributor_author, 'ea:contributor:responsible', 'ea:contributor:editor', 'ea:contributor:translator', 'dc:contributor:illustrator', 'dc:contributor:advisor', 'dc:contributor:other'), $term);

// 	drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
// 	drupal_add_http_header('Content-type', 'application/json');
// 	echo json_encode($rep);
	$response = Response::make($rep, 200);
	$response->header('Cache-Control', 'no-cache, must-revalidate');
	$response->header('Content-Type', 'application/json');
	return $response;
}

function parchive_search_place() {


	$term = null;
	$term = get_get("term");
	if (empty($term)) {
		return;
	}

	$rep = PDao::search_metadata_ac($term, Config::get('arc.DB_METADATA_FIELD_EA_PUBLICATION_PLACE'));
	//75

// 	drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
// 	drupal_add_http_header('Content-type', 'application/json');
// 	echo json_encode($rep);
	$response = Response::make($rep, 200);
	$response->header('Cache-Control', 'no-cache, must-revalidate');
	$response->header('Content-Type', 'application/json');
	return $response;
}


//@DocGroup(module="bitstream", group="php", comment="download")
function parchive_download_artifact($arg1, $arg2, $arg3) {
	//Log::info('parchive_download_artifact ' . $arg1 . ' : ' . $arg2 . ' : ' . $arg3);

	if (empty($arg2)) {
		error_log("download_artifact err #0");
		return;
	}
	$item_id = $arg1;

	$direct = true;
	if ($arg2 == 'direct') {
		$direct = true;
		$artifact_id = $arg3;
	} else if ($arg2 == 'download') {
		$direct = false;
		$artifact_id = $arg3;
	} else {
		$artifact_id = $arg2;
	}

	$dbh = dbconnect();

	if ($artifact_id == 0 ) {
		$SQL_FIELDS = 'artifact_id';
		$SQL_WHERE = 'ditem_id = ?';
		$SQL_ORDER = 'sequence_id';
		$SQL = sprintf("SELECT %s FROM dsd.item_bitstream_ext3 WHERE %s ORDER BY %s ASC LIMIT 1", $SQL_FIELDS, $SQL_WHERE, $SQL_ORDER);
		$stmt = $dbh -> prepare($SQL);
		$stmt -> bindParam(1, $item_id);
		$stmt -> execute();
		if ($r = $stmt -> fetch()) {
			$artifact_id = $r[0];
		}
	}

	if (empty($item_id) || empty($artifact_id)) {
		error_log("download_artifact err #1");
		return;
	}

	if (!preg_match("/^\d+$/", $item_id)) {
		error_log("download_artifact err #3");
		return "error(1): $item_id";
	}

	$tmp = get_get("m", null);
	if (!empty($tmp)) {
		if ($tmp == 'dt') {
			$direct = true;
		} else if ($tmp == 'dd') {
			$direct = false;
		}
	}

	$ds = true;
	$tmpds = get_get("ds", 1);
	if ($tmpds == "0" && user_access_mentainer()) {
		$ds = false;
	} else {
		$ds = true;
	}

	error_log("download artifact: " . $artifact_id . " direct: $direct ds: $ds", 0);

	$artifact_id_flag = (preg_match("/^\d+$/", $artifact_id) || preg_match("/^\d+\.jpeg$/", $artifact_id) || preg_match("/^\d+\.png$/", $artifact_id));

	if ($artifact_id_flag) {
		$artifact_id = PUtil::extract_int($artifact_id);
	}

	$SQL_FIELDS = 'internal_id, mimetype , name, bitstream_id, download_fname, logging, redirect_url, redirect_type, size_bytes';
	if ($artifact_id_flag) {
		$SQL_WHERE = ' item_id = ? AND artifact_id = ? ';
	} else {
		$SQL_WHERE = ' item_id = ? AND furl = ? ';
	}

	$ok = false;
	$SQL = sprintf("SELECT %s FROM dsd.item_bitstream_ext2 WHERE %s  AND bundle_name <> 'OLD' ", $SQL_FIELDS, $SQL_WHERE);
	$stmt = $dbh -> prepare($SQL);
	$stmt -> bindParam(1, $item_id);
	$stmt -> bindParam(2, $artifact_id);

	$stmt -> execute();
	if ($r = $stmt -> fetch()) {
		$ok = true;
		$bitstream = $r[0];
		$mime = $r[1];
		$fname = $r[2];
		$bitstream_id = $r[3];
		$download_fname = $r[4];
		$logging = $r[5];
		$size_bytes = $r[8];
	}

	if (!$ok) {
		$SQL = sprintf("SELECT %s FROM dsd.item_primary_bitstream WHERE %s ", $SQL_FIELDS, $SQL_WHERE);
		$stmt = $dbh -> prepare($SQL);
		$stmt -> bindParam(1, $item_id);
		$stmt -> bindParam(2, $artifact_id);

		$stmt -> execute();
		if ($r = $stmt -> fetch()) {
			$ok = true;
			$bitstream = $r[0];
			$mime = $r[1];
			$fname = $r[2];
			$bitstream_id = $r[3];
			$download_fname = $r[4];
			$logging = $r[5];
			$size_bytes = $r[8];
		}
	}


	if (!$ok) {
		return;
	}

	$logId = null;
	if ($logging == 1) {
		$remote_addr = $_SERVER['REMOTE_ADDR'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		$user_data = PUtil::getUserData();

		$logId = PDao::download_db_log($bitstream_id, $mime, $size_bytes, $user_agent, $remote_addr, $referer, $user_data['id']);
	}

	#	echo("$bitstream |  $mime | $fname | $download_fname | $direct\n");
	#	return;
	PUtil::download_bitstream($bitstream, $mime, $fname, $download_fname, $direct, $ds, $logId);
	//download_bitstream($bitstream, $mime, $fname, $bitstream_id, $download_fname, $referer,$direct, $logging );
	return null;
}


function parchive_media($bitstream_item_id, $media_type_str) {
	$b_item = PUtil::extract_int($bitstream_item_id);
	if (empty($b_item)) {
		return;
	}
	$media_type_str = strtolower($media_type_str);

	$MEDIA_TYPE_MAP = Lookup::get_media_type_values_reverse(false);
	$media_type_id = $MEDIA_TYPE_MAP[$media_type_str];
	if (empty($media_type_id)) {
		$media_type_id = $MEDIA_TYPE_MAP['small'];
	}

	function get_thumb($b_item, $media_type_id) {
		$SQL = "SELECT id,item_id, file, idx,idxf,ttype,auto_gen,extension from dsd.thumbs where item_id = ? AND ttype =?";
		$dbh = dbconnect();
		$stmt = $dbh -> prepare($SQL);
		$stmt -> bindParam(1, $b_item);
		$stmt -> bindParam(2, $media_type_id);

		$stmt -> execute();
		return $stmt -> fetch();
	}

	$r = get_thumb($b_item, $media_type_id);

	if (empty($r) && $media_type_id == DataFields::DB_MEDIA_THUMB_TYPE_MAX) {
		$media_type_id = DataFields::DB_MEDIA_THUMB_TYPE_BIG;
		$r = get_thumb($b_item, $media_type_id);
	}

	if (empty($r)) {
		$media_type_id = DataFields::DB_MEDIA_THUMB_TYPE_ICON_SMALL;
		$r = get_thumb($b_item, $media_type_id);
	}

	if (empty($r)) {
		return;
	}

	$fpath = $r['file'];
	$full_fpath = Config::get('arc.THUMBNAIL_DIR') . $fpath;
	$ext = $r['extension'];
	$mime = PUtil::mimetype_from_image_extension($ext);

	//$filesize = filesize($full_fpath);
  //error_log("filesize : " . $filesize);
  error_log("Content-Type : " . $mime);
  error_log("File : " . $fpath);
 ;


	//drupal_add_http_header('X-Sendfile', $full_fpath);
	//drupal_add_http_header('Content-Type', $mime);


// 	$file = File::get($full_fpath);
// 	$response = Response::make($file, 200);
// 	$response->header('Content-Type', $mime);


	$response = Response::make('',200);
	$response->header('X-Sendfile', $full_fpath);
	$response->header('Content-Type', $mime);
	return $response;


}





function epub_viewer(){
	$uuid=get_get('e');
	Log::info("epub_viewer: " . $uuid);

	if (empty($uuid)){
		$url ="/";
	}else {
		$pcli = new PShellClient(array(
				'bin_dir'=>'node',
		));


		$user_data = PUtil::getUserData();
		$uid = $user_data['uid'];
		$full_name= htmlspecialchars($user_data['full_name']);
		if (empty($full_name)){
			$full_name = $user_data['username'];
		}


		$filename  = PUtil::bitream2filename($uuid);


		putenv('EPUB_READER_CONTENT=' . Config::get('readers.EPUB_READER_CONTENT'));
		$final_uuid = $pcli->exec("epub-ds-viewer.sh", array($filename,$full_name,"123456"));

		if (empty($final_uuid)){
			$url ="/";
		} else {
			$url = Config::get('readers.EPUB_READER_URL_REFIX') .$final_uuid;
		}
	}
	Log::info("epub_viewer#1: " . $url);
	$response = Response::make('', 301);
	$response->header('Location', $url);
	return $response;


}

function solr_suggest() {

// 	$config = array(
// 			'endpoint' => array(
// 					'localhost' => array(
// 							'host' => '127.0.0.1',
// 							'port' => 8983,
// 							'path' => '/solr/',
// 							'core' => 'opac_index'
// 					)
// 			)
// 	);

	//SOLR connection configuration
	$config = array('endpoint' => PUtil::getSolrConfigEndPoints('opac'));
	$client = new Solarium\Client($config);

	$searchTerm = Input::get('term');
	$query = $client->createSuggester();
	$query->setQuery($searchTerm);

	$isAllEnglishChars = preg_match('/[^A-Za-z0-9]/', $searchTerm) ? false : true;

	if ($isAllEnglishChars) {
		$query->setHandler('suggest_en');
		$query->setDictionary('suggest_en');
	} else {
		$query->setHandler('suggest_el');
		$query->setDictionary('suggest_el');
	}

	$query->setOnlyMorePopular(true);
	$query->setCount(10);
	$query->setCollate(true);

	$result = json_decode($client->suggester($query)->getResponse()->getBody());

	if ($isAllEnglishChars) {
		$suggestionTerms = $result->suggest->suggest_en;
	} else {
		$suggestionTerms = $result->suggest->suggest_el;
	}

	$suggestionsArr = array();
	foreach ($suggestionTerms as $suggestionTerm) {
		$suggestions = $suggestionTerm->suggestions;
		foreach ($suggestions as $res) {
			$term = $res->term;
// 			Log::info("Adding suggestion: " . $term);
			$suggestionsArr[] = $term;
		}
	}

	return Response::json($suggestionsArr);

}

function solr_suggest_staff() {


	$config = array(
			'endpoint' => array(
					'localhost' => array(
							'host' => '127.0.0.1',
							'port' => 8983,
							'path' => '/solr/',
							'core' => 'staff_index'
					)
			)
	);
	$client = new Solarium\Client($config);

	$searchTerm = Input::get('term');
	$query = $client->createSuggester();
	$query->setQuery($searchTerm);

	$isAllEnglishChars = preg_match('/[^A-Za-z0-9]/', $searchTerm) ? false : true;

	if ($isAllEnglishChars) {
		$query->setHandler('suggest_en');
		$query->setDictionary('suggest_en');
	} else {
		$query->setHandler('suggest_el');
		$query->setDictionary('suggest_el');
	}

	$query->setOnlyMorePopular(true);
	$query->setCount(10);
	$query->setCollate(true);

	$result = json_decode($client->suggester($query)->getResponse()->getBody());

	if ($isAllEnglishChars) {
		$suggestionTerms = $result->suggest->suggest_en;
	} else {
		$suggestionTerms = $result->suggest->suggest_el;
	}

	$suggestionsArr = array();
	foreach ($suggestionTerms as $suggestionTerm) {
		$suggestions = $suggestionTerm->suggestions;
		foreach ($suggestions as $res) {
			$term = $res->term;
			// 			Log::info("Adding suggestion: " . $term);
			$suggestionsArr[] = $term;
		}
	}

	return Response::json($suggestionsArr);

}


}
