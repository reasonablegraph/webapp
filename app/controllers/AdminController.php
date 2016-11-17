<?php
class AdminController extends BaseController {


	function parchive_recent() {

		auth_check_mentainer();

		if (isset($_GET['json'])) {

			$out = array();
			$dbh = dbconnect();
			// $obj_type_names = get_object_type_names($dbh);

			$SQL = sprintf("SELECT %s FROM  dsd.item2 i WHERE status='finish' order by i.dt_create desc, i.label limit 80", Config::get('arc.ITEM_LIST_SQL_FIELDS'));
			$stmt = $dbh->prepare($SQL);
			$stmt->execute();

			while ( $row = $stmt->fetch() ) {
				$uuid = $row['uuid'];
				$uri = 'http://' . Config::get('arc.ARCHIVE_HOST') . '/archive/item/' . $uuid;
				$type = $row['obj_type'];
				$lang = $row['lang'];
				$entry[DataFields::dc_title] = $row['title'];
				$entry[DataFields::ea_uuid] = $uuid;
				$entry[DataFields::dc_identifier_uri] = $uri;
				$entry[DataFields::ea_type] = $type;
				$entry[DataFields::dc_language_iso] = $lang;
				$out[] = $entry;
			}

			$response = Response::make($out, 200);
			$response->header('Cache-Control', 'no-cache, must-revalidate');
			$response->header('Content-Type', 'application/json');
			return $response;
		} else {
			$this->show();
		}
	}
	function update_folder_thumbs() {
		auth_check_mentainer();

		//Admin only
		$is_admin = ArcApp::user_access_admin();
		if (!$is_admin){
			$URL = UrlPrefixes::$cataloging;
			$response = Response::make('', 301);
			$response->header('Location', $URL);
			return $response;
		}

		$out = "";
		$out .= '<form method="post">';
		$out .= '<input type="submit" name="update" value="update" onClick="return confirm(\'Are you sure?\')"/>';
		$out .= '</form>';

		$update_flag = false;
		if (isset($_POST['update']) && $_POST['update'] == 'update') {
			$update_flag = true;
		}

		if (! ($update_flag)) {
			return $out;
		}

		$out .= "<h2>update folder thumbnails</h2>";

		// $now = time();
		$dbh = dbconnect();
		$SQL = "SELECT  * from dsd.update_folder_thumbs()";
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$r = $stmt->fetch(PDO::FETCH_ASSOC);
		$out .= '<pre>';
		$out .= print_r($r, true);
		$out .= '</pre>';

		return $out;
	}
	function parchive_download() {
		$i = null;
		$i = get_get("i");
		if (empty($i)) {
			error_log("err #1");
			echo ("ERROR (1)");
			return;
		}
		$item_id = $i;

		$internal_id = get_get("d");
		if (empty($internal_id)) {
			error_log("err #2");
			echo ("ERROR (2)");
			return;
		}

		$direct = false;
		$tmp = get_get("m", null);
		if (! empty($tmp) && $tmp == 'dt') {
			$direct = true;
		}

		$ds = true;
		$tmpds = get_get("ds", 1);
		if ($tmpds == "0" && user_access_mentainer()) {
			$ds = false;
		} else {
			$ds = true;
		}

		$dbh = dbconnect();
		$SQL = "SELECT item_id, artifact_id FROM dsd.item_bitstream_ext WHERE	internal_id = ? and item_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $internal_id);
		$stmt->bindParam(2, $item_id);
		$stmt->execute();
		if (! $r = $stmt->fetch()) {
			return null;
		}

		$direct_param = $direct ? '?m=dt' : '?m=dd';
		$ds_param = $ds ? '' : '&ds=0';

		// $server_name = $_SERVER['SERVER_NAME'];
		// $location = sprintf('http://%s/archive/item/%s/%s%s',$server_name,$r['item_id'],$r['artifact_id'],$direct_param);
		$location = sprintf('/archive/item/%s/%s%s%s', $r['item_id'], $r['artifact_id'], $direct_param, $ds_param);

		// drupal_add_http_header('Location', $location);
		// drupal_add_http_header('Status', '301 Moved Permanently');

		$response = Response::make('', 301);
		$response->header('Location', $location);
		return $response;
	}
	function parchive_delete_thumb() {
		PUtil::populateRequestARGV($pparent, $pitem);

		auth_check_mentainer();

		$item_id = get_get("i");
		if (empty($item_id)) {
			echo ("expected item");
			return;
		}

		// $idx = get_get("idx");
		// if (empty($idx) && $idx != 0){
		// echo("expected idx");
		// return;
		// }

		$ttype = get_get("ttype");
		if (empty($ttype) && $ttype != 0) {
			echo ("expected ttype");
			return;
		}

		$id = get_get("tid");
		if (empty($id)) {
			echo ("expected tid");
			return;
		}

		$dbh = dbconnect();
		$SQL = "DELETE FROM dsd.thumbs WHERE item_id = ? AND ttype = ? AND id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->bindParam(2, $ttype);
		$stmt->bindParam(3, $id);
		$stmt->execute();

		$url = "/prepo/thumbs?i=" . $item_id;
		// drupal_add_http_header('Location', $url);
		// return;

		$response = Response::make('', 301);
		$response->header('Location', $url);
		return $response;
	}


	function parchive_move_bitstream() {
		auth_check_mentainer();
		$artifact_id = get_get("aid", null);
		if (empty($artifact_id)) {
			return;
		}

		if (isset($_POST['move']) && $_POST['move'] == 'move') {
			$item_id = get_post("item", null);
			if (empty($item_id)) {
				return;
			}

			$dbh = dbconnect();

			$SQL = "SELECT count(*) from dsd.item2 WHERE item_id = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$r = $stmt->fetch();
			if ($r[0] == 0) {
				echo "CANOT FIND ITEM";
				return;
			}

			// MOVE BITSTREAM
			$SQL = "SELECT dsd.move_bitstream(bitstream_id, ?) from public.bitstream where artifact_id = ?";
			error_log($SQL);
			error_log($item_id);
			error_log($artifact_id);

			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->bindParam(2, $artifact_id);
			$stmt->execute();

			$URL = sprintf("/archive/item/%s", $item_id);
			// drupal_add_http_header('Location', $URL);

			$response = Response::make('', 301);
			$response->header('Location', $URL);
			return $response;
		}

		$out = '<h1 class="admin item-title bitstream">Move to item</h1>';
		$out .= '<form method="post">';
		$out .= 'Item id: <input id="item" type="text" name="item" size="10" />';
		$out .= '<input type="submit" name="move" value="move" onClick="return confirm(\'Are you sure?\')"/>';
		$out .= '</form>';

		return $out;
	}


	function parchive_search_item_by_title() {
		$term = get_get('term');
		if (empty($term))
			return;

		$items = PDao::search_item_from_metadata($term, Config::get('arc.DB_METADATA_FIELD_DC_TITLE'), true, false);

		// drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
		// drupal_add_http_header('Content-type', 'application/json');
		// echo json_encode($items);
		$response = Response::make($items, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_search_folder_by_title() {
		$term = get_get('term');
		if (empty($term))
			return;

		$items = PDao::search_item_from_metadata($term, Config::get('arc.DB_METADATA_FIELD_DC_TITLE'), true, true);

		// drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
		// drupal_add_http_header('Content-type', 'application/json');
		// echo json_encode($items);
		$response = Response::make($items, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_search_actor_by_title() {
		$term = get_get('term');
		if (empty($term))
			return;

		$items = PDao::search_item_from_metadata($term, Config::get('arc.DB_METADATA_FIELD_DC_TITLE'), true, false, null, 'actor');

		// drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
		// drupal_add_http_header('Content-type', 'application/json');
		// echo json_encode($items);
		$response = Response::make($items, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_search_metadata_elements() {
		$term = null;
		$term = get_get("term");

		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
		$rep = PDao::search_metadata_element($term);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_search_isbn() {
		$term = null;
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$rep = PDao::search_metadata(DataFields::dc_identifier_isbn, $term);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_search_subtitle() {
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
		$rep = PDao::search_metadata(array('marc:title-statement:remainder' ), $term);
		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_find_relation() {
		$term = get_get("term");
		if (empty($term)) {
			return;
		}

		$key = get_get("key");
		if (empty($key)) {
			return;
		}

		$rep = PDao::find_relation($term, $key);
		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_find_work() {
		$term = null;
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$rep = PUtil::find_work($term);
		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_find_place() {
		$term = null;
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
		$rep = PUtil::find_place($term);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_search_bookbinding_type() {
		$term = null;
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$rep = PDao::search_metadata('ea:bookbinding:type', $term);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_search_material_type() {
		$term = null;
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$rep = PDao::search_metadata('ea:material:type', $term);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_search_country() {
		$term = null;
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$rep = PDao::search_metadata('ea:country:name', $term);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
	function parchive_check_value_exists() {
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$element = get_get("e");
		if (empty($element)) {
			return AdminController::ws_empty_json_response();
		}

		$c = PDao::get_metadata_value_count($element, $term);
		$rep = array("count" => $c );
		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function parchive_find_contributor() {
		$term = null;
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
// 		$rep = PUtil::find_contributor($term);

		$flags = array('IS:actor','OT:auth-person','OT:auth-organization','OT:auth-family');
		$limit = 30;

		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function parchive_ws_item_metadata() {
		auth_check_mentainer();
		if (isset($_GET['i'])) {
			$item_id = PUtil::reset_int(get_get("i"), null);
			if (empty($item_id)) {
				return AdminController::ws_empty_json_response();
			}
			/* @var  $idata ItemMetadata */
			$idata = PDao::get_item_metadata($item_id);
			$basic = PDAO::getItemBasic($item_id);
			if ($basic){
				//Log::info(array_keys($basic));
				$idata->addItemValueNK(ItemValue::cKeyTextValue('tmp:jdata', json_decode($basic['jdata'], true)));


// 				$jdata = ItemValue::cKeyTextValue('tmp:jdata', 'jdata');
// 				$jdata->setData(json_decode($basic['jdata'], true));
// 				$idata->addItemValueNK($jdata);
			}
			// drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
			// drupal_add_http_header('Content-type', 'application/json');
			// echo($idata -> toJson());
			$idata = $idata->toJson();
			$response = Response::make($idata, 200);
			$response->header('Cache-Control', 'no-cache, must-revalidate');
			$response->header('Content-Type', 'application/json');
			return $response;
			// return Response::json($idata);
		}
		return null;
	}

	function export_item() {
		auth_check_mentainer();
		$user = ArcApp::username();

		$item_id = get_get("i");
		if (empty($item_id)) {
			echo ("expected item");
			return;
		}

		//lock edit form submitter
		$is_admin = ArcApp::user_access_admin();
		$edit_lock_owner = Config::get('arc.owner_edit_form_lock',0);
		if ( $edit_lock_owner && !empty($item_id) ) {
			$dbh = dbconnect();
			$SQL = "SELECT user_create  from dsd.item2 WHERE item_id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$stmt->bindColumn(1, $owner);
			if (! $stmt->fetch()) {
				echo ("ERROR: item submitter not found ");
				return;
			}

			if ( /* $owner!= $user && */ !$is_admin){ //TODO export correction before check owner
				$URL = UrlPrefixes::$cataloging;
				$response = Response::make('', 301);
				$response->header('Location', $URL);
				return $response;
			}
		}



		list ( $filename, $fname ) = PDao::export_one_item($item_id);
		$handle = fopen($filename, "rb");
		$filesize = filesize($filename);

		$mime = "application/x-gtar-compressed";

		$response = Response::make('', 200);
		$response->header('X-Sendfile', $filename);
		$response->header('Content-Type', $mime);
		$response->header('Content-Length', $filesize);
		$response->header('Content-Disposition', sprintf('attachment; filename="%s"', $fname));
		$response->header('Content-Transfer-Encoding', 'binary');
		return $response;
	}


	function edit_metadata() {
		Log::info("AdminController::edit_metadata");
		auth_check();
		$dbh = dbconnect();
		$item_id = get_get('itid');

		if (empty($item_id)) {
			return;
		}

		if (! empty($_POST)) {

			// echo("<pre>");
			// print_r($_POST);
			// echo("</pre>");

			$mvid = get_post('mvid');
			$text_value = get_post('text_value');
			$text_lang = get_post('text_lang');
			$element = get_post('element');
			$delete = get_post('delete', null);

			if (! empty($delete) && $delete == 'delete') {
				if (empty($mvid)) {
					return;
				}
				$query = "DELETE FROM dsd.metadatavalue2 WHERE  metadata_value_id = ?";
				$stmt = $dbh->prepare($query);
				$stmt->bindParam(1, $mvid, PDO::PARAM_INT);
				$stmt->execute();

				// $count = $stmt->rowCount();
				// print("Deleted $count rows.\n");
				$header = sprintf('/prepo/edit_step2?i=%s', $item_id);
				// drupal_add_http_header('Location',$header);

				$response = Response::make('', 301);
				$response->header('Location', $header);
				return $response;
			}
		}
		if (! empty($mvid)) {
			// UPDATE
			$SQL = "UPDATE dsd.metadatavalue2 SET text_value=?, text_lang=? WHERE  metadata_value_id = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $text_value);
			$stmt->bindParam(2, $text_lang);
			$stmt->bindParam(3, $mvid);

			$stmt->execute();
			$count = $stmt->rowCount();

			// echo("$count record updated");
			$_REQUEST['message'] = "$count record updated";
		} else {
			// INSERT
			if (empty($element)) {
				return;
			}
			if (empty($text_value) && empty($text_lang)) {
				return;
			}

			$SQL = "SELECT mfid from dsd.metadatafieldregistry_view  where element = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $element);
			$stmt->execute();
			if ($row = $stmt->fetch()) {
				$mfid = $row[0];
			} else {
				echo ("element: $element not found");
				return;
			}

			$SQL = "SELECT nextval('dsd.metadatavalue2_id_seq')";
			$stmt = $dbh->prepare($SQL);
			$stmt->execute();
			$row = $stmt->fetch();
			$id = $row[0];
			$mvid = $row[0];

			$SQL = "INSERT INTO  dsd.metadatavalue2 (metadata_value_id, metadata_field_id, item_id, text_value, text_lang) values (?,?,?,?,?)";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $id);
			$stmt->bindParam(2, $mfid);
			$stmt->bindParam(3, $item_id);
			$stmt->bindParam(4, $text_value);
			$stmt->bindParam(5, $text_lang);

			$stmt->execute();
			$header = sprintf('/prepo/edit_metadata?itid=%s&id=%s', $item_id, $id);
			// drupal_add_http_header('Location',$header);
			$response = Response::make('', 301);
			$response->header('Location', $header);
			return $response;
		}

		$this->show();
	}


	function edit_bitstream() {
		Log::info("AdminController::edit_bitstream");

		$user = ArcApp::username();

		//lock edit form submitter
		$is_admin = ArcApp::user_access_admin();
		$edit_lock_owner = Config::get('arc.owner_edit_form_lock',0);

		$bid = get_get('bid');
		$row = PDao::getBitstream($bid);
		$item_id = $row['item_id'];

		if ( $edit_lock_owner && !empty($item_id) ) {
			$dbh = dbconnect();
			$SQL = "SELECT user_create  from dsd.item2 WHERE item_id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$stmt->bindColumn(1, $owner);

			if (! $stmt->fetch()) {
				echo ("ERROR: item submitter not found ");
				return;
			}

			if ( $owner!= $user && !$is_admin){ //TODO export correction before check owner
				$URL = UrlPrefixes::$cataloging;
				$response = Response::make('', 301);
				$response->header('Location', $URL);
				return $response;
			}
		}


		$POST_DATA = null;
		if (! empty($_POST)) {

			Log::info(print_r($_POST,true));
			$POST_DATA = $_POST;

			$delete_flag = get_post('delete', '0') == 'delete';

			if ($delete_flag) {

// 				if (! $app->user_access(Config::get('arc.APERMISSION_BITSTREAM_DELETE'))) {
				if (! user_access_mentainer()) {
					return;
				}
				$item_id = get_post("item_id");
				if (empty($item_id)) {
					return;
				}

				$id = get_get('bid');
				if (empty($id)) {
					return;
				}
				PDao::delete_bitstream($item_id, $id);
				$bitstreams_url = sprintf('/prepo/bitstreams?i=%s', $item_id);
				$response = Response::make('', 302);
				$response->header('Location', $bitstreams_url);
				return $response;
			}
		}
		$data = array('POST_DATA'=>$POST_DATA);
		$this->show($data);
	}


	function spool() {
		$data = ARRAY();
		if (! empty($_POST)) {
			$delete = get_post('delete', null);

			if (! empty($delete) && $delete == 'delete') {
				$file_path = get_post('file_path', null);
				$file_name = get_post('file_name', null);

				if (file_exists($file_path)){
					unlink($file_path);
					$data['delete_msg'] = $file_name .tr(' deleted!');
				}
			}
		}

		$this->show($data);
	}



	function add_content() {
		if (! empty($_POST) && isset($_POST['ADD_article'])) {

			$description = get_post('description');
			$c_item_id = get_post('item_id');

			$bundle_name = 'PRIVATE';
			$content_ctype = DataFields::DB_content_ctype_article;
			$visibility = DataFields::DB_visibility_private;

			$ntitle = $description;
			if (empty($ntitle)) {
				$now = time();
				$ntitle = "article: " . $now;
			}
			return PDao::create_content($content_ctype, $ntitle, $bundle_name, $visibility, $c_item_id);
		}

		if (! empty($_POST) && isset($_POST['ADD_note'])) {

			$description = get_post('description');
			$c_item_id = get_post('item_id');

			$bundle_name = 'PRIVATE';
			$content_ctype = DataFields::DB_content_ctype_note;
			$visibility = DataFields::DB_visibility_private;

			$ntitle = $description;
			if (empty($ntitle)) {
				$now = time();
				$ntitle = "note: " . $now;
			}
			return PDao::create_content($content_ctype, $ntitle, $bundle_name, $visibility, $c_item_id);
		}
	}


	function delete_item() {
		auth_check_mentainer();

		$item_id = get_get("i");
		if (empty($item_id)) {
			echo ("expected item");
			return;
		}

		//activity_log
		$url = $_SERVER['REQUEST_URI'];
		$user = ArcApp::username();
		if (empty($user)) {
			$user = 'Unknown';
		}
		$type_act = 'delete';
		PDao::activity_log($type_act, $user, $url);

		return ItemSave::delete_item($item_id);
	}


	function parchive_change_obj_type() {
		auth_check_mentainer();
		$item_id = get_post_get("i", null);

		if (empty($item_id))
			return null;

		$dbh = dbconnect();

		$ot = get_post("obj_type", null);
		$change = get_post("change", null);
		if (! empty($ot) && ! empty($change) && $change == "change") {

			$SQL = "SELECT dsd.set_collection(?,?);";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->bindParam(2, $ot);
			$stmt->execute();
			// $stmt->fetch();
		}

		$SQL = "SELECT obj_type FROM dsd.item2 WHERE item_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		if (! $row = $stmt->fetch()) {
			return null;
		}
		$obj_type = $row[0];

		$out = "";
		$out .= '<form method="post">';
		$out .= sprintf('<input type="hidden" name="i" value="%s"/>', $item_id);

		$out .= PUtil::dbToSelect("SELECT name,label from dsd.obj_type", "obj_type", $obj_type, false);

		$out .= '<input type="submit" name="change" value="change"/>';
		$out .= '</form><br/>';
		// $out .='';
		// $out .='';
		// $out .='';

		$out .= sprintf('<a href="/prepo/edit_step2?i=%s">[adm view]</a>', $item_id);
		$out .= "<br/>\n";
		return $out;
	}


	function parchive_change_site() {
		auth_check_mentainer();
		$item_id = get_post_get("i", null);

		if (empty($item_id))
			return null;

		$dbh = dbconnect();

		$site = get_post("site", null);
		$change = get_post("change", null);
		if (! empty($site) && ! empty($change) && $change == "change") {
			PDao::change_item_site($item_id, $site);
		}

		$SQL = "SELECT site,label FROM dsd.item2 WHERE item_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		if (! $row = $stmt->fetch()) {
			return null;
		}
		$site = $row[0];
		$label = $row[1];

		$out = "";
		$out .= "<p>$label</p>";
		$out .= '<form method="post">';
		$out .= sprintf('<input type="hidden" name="i" value="%s"/>', $item_id);

		$out .= PUtil::dbToSelect("SELECT distinct site,site from dsd.item2", "site", $site, false);

		$out .= '<input type="submit" name="change" value="change"/>';
		$out .= '</form><br/>';
		// $out .='';
		// $out .='';
		// $out .='';

		$out .= sprintf('<a href="/prepo/edit_step2?i=%s">[adm view]</a>', $item_id);
		$out .= "<br/>\n";
		return $out;
	}


	function parchive_bibref_togle() {
		auth_check_mentainer();
		$item_id = get_get('i', null);
		if (empty($item_id)) {
			return null;
		}

		$dbh = dbconnect();
		$SQL = "SELECT item_id, bibref FROM dsd.item2  WHERE item_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		$r = $stmt->fetchAll();

		if (isset($r[0]) && isset($r[0]['bibref']) && isset($r[0]['item_id'])) {
			$db_item_id = $r[0]['item_id'];
			$bibref = $r[0]['bibref'];
			$new_bibref = $bibref ? 0 : 1;
			PDao::update_bibref($item_id, $new_bibref);
		}
		$URL = "/prepo/edit_step2?i=$db_item_id";
		// drupal_add_http_header('Location', $URL);
		// return;
		$response = Response::make('', 301);
		$response->header('Location', $URL);
		return $response;
	}


	function parchve_repo_move() {
		auth_check_mentainer();

		// global $spool_url_prefix_pending;
		// global $spool_url_prefix_ok;
		// global $spool_dir_pending;
		// global $spool_dir_ok;

		$d = $_GET['d'];
		if (empty($d)) {
			$d = 1;
		}

		$f = $_GET['f'];
		if (empty($f)) {
			return;
		}

		$is_admin = ArcApp::user_access_admin();
		$orgPrefix = get_get("org");
		$admin_move = false;
		if (!empty($orgPrefix) && $is_admin) {
			$admin_move = true;
		}

		if ($d == 1) {
// 			$urlpath1 = Config::get('arc.SPOOL_url_prefix_pending');
// 			$urlpath2 = Config::get('arc.SPOOL_url_prefix_ok');
// 			$dir1 = Config::get('arc.SPOOL_dir_pending');
// 			$dir2 = Config::get('arc.SPOOL_dir_ok');
			if($admin_move){
				$orgPrefix = ($orgPrefix != '/') ?  '/' .$orgPrefix.'/' : null  ;
				$dir_pending = Config::get('arc.SPOOL_dir_pending');
				$dir1 = $dir_pending . $orgPrefix ;
				$dir_ok = Config::get('arc.SPOOL_dir_ok');
				$dir2 = $dir_ok . $orgPrefix ;
// 				$dir_pending = Config::get('arc.SPOOL_dir_pending');
// 				$dir1 = $dir_pending . '/' . $orgPrefix . '/';
// 				$dir_ok = Config::get('arc.SPOOL_dir_ok');
// 				$dir2 = $dir_ok . '/' . $orgPrefix . '/';

			}else{
				$dir1 = SpoolUtil::getPendigSpoolDir();
				$dir2 = SpoolUtil::getOKSpoolDir();
			}

		} else {
// 			$urlpath1 = Config::get('arc.SPOOL_url_prefix_ok');
// 			$urlpath2 = Config::get('arc.SPOOL_url_prefix_pending');
// 			$dir1 = Config::get('arc.SPOOL_dir_ok');
// 			$dir2 = Config::get('arc.SPOOL_dir_pending');
			if($admin_move){
				$orgPrefix = ($orgPrefix != '/') ?  '/' .$orgPrefix.'/' : null  ;
				$dir_ok = Config::get('arc.SPOOL_dir_ok');
				$dir1 = $dir_ok . $orgPrefix ;
				$dir_pending = Config::get('arc.SPOOL_dir_pending');
				$dir2 = $dir_pending . $orgPrefix ;

			}else{
				$dir1 = SpoolUtil::getOKSpoolDir();
				$dir2 = SpoolUtil::getPendigSpoolDir();
			}

		}
		// error_log("dir1 : $dir1",0);
		// error_log("dir2 : $dir2",0);

		$f1 = $dir1 . $f;
		$f2 = $dir2 . $f;
		// error_log("rename $f1 to $f2",0);
		rename($f1, $f2);

		$rep = "";
		$rep .= '<div class="arch-wrap">';

		if ($d == 1) {
			$rep .= "<h1>  moved to ok</h1>";
		} else {
// 			$rep .= "<h1>moved to spool</h1>";
			$rep .= "<div class='valid_msg' >moved to spool </div>";
		}

		$rep .= ("<br/>");

// 		if ($d == 1) {
// 			$rep .= "<a href=\"/prepo/spool?d=1\">[BACK]</a>";
// 		} else {
// 			$rep .= "<a href=\"/prepo/spool?d=2\">[BACK]</a>";
// 		}

// 		$rep .= "&#160; &#160;<a href=\"/prepo/spool?d=1\">[SPOOL]</a>";
// 		$rep .= "&#160; &#160;<a href=\"/prepo/spool?d=2\">[OK]</a>";
// 		$rep .= "&#160; &#160;<a href=\"/prepo/menu\">[MENU]</a>";

		$rep .= '<div class="ttools">';
		$rep .= '<a href="/prepo/spool?d=1"><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> Pending spool</a>';
		$rep .= '&#160; &#160;<a href="/prepo/spool?d=2"><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> Submited spool</a>';
		$rep .= '</div>';

		$rep .= '</div>';

		echo ($rep);
		return null;
		// return $rep;
	}


	function edit_item_step1() {
		auth_check_mentainer();

		if (! empty($_POST)) {

			$dbh = dbconnect();

			$submit_id = isset($_POST['submit_id']) ? $_POST['submit_id'] : null;
			$item_id = isset($_POST['item_id']) ? $_POST['item_id'] : get_get("i");
			$edoc = isset($_POST['edoc']) ? $_POST['edoc'] : null;
			$cd = isset($_POST['cd']) ? $_POST['cd'] : null;
			$wfdata['cd'] = $cd;
			$item_collection = isset($_POST['item_collection'][0]) ? $_POST['item_collection'][0] : null;
			$wfdata['item_collection'] = $item_collection;

			if (! empty($item_id)) {
				error_log("edit_step1: load data from item ($item_id) merge with post", 0);
				$idata = PDao::get_item_metadata($item_id);
			} else {
				$idata = new ItemMetadata();
			}

			// echo("<pre>");
			// print_r($idata->values);
			// echo("</pre>");

			// "ea:source:","ea:source:print", "ea:original:print",
			// #########################################################################
			// kataskevi IDATA
			// #########################################################################
			// $it = new ItemMetadataIterator($idata);
			// foreach($it as $key => $value) {
			// if (isset($_POST[$key])){
			// if
			// (!(!empty($post_obj_type)
			// && ($post_obj_type == 'periodiko' || $post_obj_type == 'efimerida'
			// || $post_obj_type =='web-site-instance' || $post_obj_type == 'web-site' || $vivliografiki_anafora)
			// && ($key == "ea:source:" || $key == "ea:source:print" || $key == "ea:original:print" )))
			// {
			// $idata->setStaffValueSK($key,$_POST[$key]);
			// }
			// }
			// }

			foreach ( $_POST as $pk => $pv ) {
				// if (PUtil::strBeginsWith($pk, 'ea:') || PUtil::strBeginsWith($pk, 'dc:') ){
				if (! PUtil::strBeginsWith($pk, 'tmp:')) {
					$idata->replaceValuesFromClient($pk, $pv);
				}
			}
			// echo("<pre>");
			// echo("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n");
			// print_r($idata->values);
			// echo("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n");
			// echo("</pre>");

			// ///////////////////////////////////
			// /////////////////////////////////////
			// /////////////////////////////////////
			// $proc_post_date = function($post_var_prefix, $key) use ($idata) {
			// $var_d = $post_var_prefix . "_d";
			// $var_m = $post_var_prefix . "_m";
			// $var_y = $post_var_prefix . "_y";
			//
			// $d = isset($_POST[$var_d]) ? $_POST[$var_d] : null;
			// $m = isset($_POST[$var_m]) ? $_POST[$var_m] : null;
			// $y = isset($_POST[$var_y]) ? $_POST[$var_y] : null;
			//
			// if (!preg_match('/\d+/',$d)){
			// $d = null;
			// }
			// if (!preg_match('/\d+/',$m)){
			// $m = null;
			// }
			// if (!preg_match('/\d+/',$y)){
			// $y = null;
			// }
			//
			// if (! empty($y) && ! empty($m) && !empty($d) && $y > 1900 && $m < 13 && $d < 32){
			// $idata->setStaffValueSK($key,ARRAY("$y-$m-$d"));
			// }else if (! empty($y) && ! empty($m) && $y > 1900 && $m < 13){
			// $idata->setStaffValueSK($key,ARRAY("$y-$m"));
			// }else if (! empty($y) && $y > 1900){
			// $idata->setStaffValueSK($key,ARRAY("$y"));
			// }
			// };
			// /////////////////////////////////////
			//
			//
			// $proc_post_date('orgissued','ea:date:orgissued');
			// $proc_post_date('date_start','ea:date:start');
			// $proc_post_date('date_end','ea:date:end');
			//
			//
			// // $url_related = ARRAY();
			// $url1_related = isset($_POST['url1_ea:url:related'])? $_POST['url1_ea:url:related'] : ARRAY() ;
			// $url2_related = isset($_POST['url2_ea:url:related'])? $_POST['url2_ea:url:related'] : ARRAY() ;
			// $idata->resetSK("ea:url:related");
			// foreach($url1_related as $k => $v){
			// $url = $v;
			// $desc = $url2_related[$k];
			// $ahref = sprintf('%s|%s',$url,$desc);
			// if ($ahref != '|'){
			// $idata->addValueSK("ea:url:related", $ahref);
			// }
			// }
			//
			// // $url_origin = ARRAY();
			// $url1_origin = isset($_POST['url1_ea:url:origin'])? $_POST['url1_ea:url:origin'] : ARRAY() ;
			// $url2_origin = isset($_POST['url2_ea:url:origin'])? $_POST['url2_ea:url:origin'] : ARRAY() ;
			// foreach($url1_origin as $k => $v){
			// $url = $v;
			// $desc = $url2_origin[$k];
			// $ahref = sprintf('%s|%s',$url,$desc);
			// if ($ahref != '|'){
			// // $url_origin[$k] =$ahref;
			// $idata->setStaffValueSK("ea:url:origin",ARRAY($ahref));
			// } else {
			// $idata->setStaffValueSK("ea:url:origin",ARRAY());
			// }
			// }
			// #########################################################################

			$idata->validate();
			$msg_counters = PSnipets::print_mesages($idata);
			// $err_counter =$msg_counters[0];

			// ############################################################################
			// echo("<pre>");
			// echo("\n##########2########\n");
			// print_r($idata->values);
			// echo("\n##########3########\n");
			// print_r($idata->values_norm);
			// echo("</pre>");
			$data = serialize($idata->values);
			// $data_norm = serialize($idata->values_norm);
			$wfdata_text = serialize($wfdata);

			$title = $idata->getValueSK("dc:title:");
			$title = empty($title) ? null : $title[0];

			if (! empty($submit_id)) {
				if (empty($item_id)) {
					$item_id = null;
				}

				$SQL = " update dsd.submits set user_name = ?, data = ?, item_id = ? , title = ?, edoc = ?, wf_data = ? WHERE id = ? ";
				$stmt = $dbh->prepare($SQL);
				$stmt->bindParam(1, $user);
				$stmt->bindParam(2, $data);
				$stmt->bindParam(3, $item_id);
				$stmt->bindParam(4, $title);
				$stmt->bindParam(5, $edoc);
				$stmt->bindParam(6, $wfdata_text);
				// $stmt->bindParam(7, $data_norm);
				$stmt->bindParam(7, $submit_id);

				$stmt->execute();
			} else {
				if (empty($item_id)) {
					$item_id = null;
				}

				$SQL = "SELECT nextval('dsd.submits_id_seq')";
				$stmt = $dbh->prepare($SQL);
				$stmt->execute();
				$stmt->bindColumn(1, $nextval);
				$stmt->fetch();

				$SQL = "insert into dsd.submits (id, user_name, data, item_id, title, edoc, wf_data) values (?,?,?,?,?,?,?)";
				$stmt = $dbh->prepare($SQL);
				$stmt->bindParam(1, $nextval);
				$stmt->bindParam(2, $user);
				$stmt->bindParam(3, $data);
				$stmt->bindParam(4, $item_id);
				$stmt->bindParam(5, $title);
				$stmt->bindParam(6, $edoc);
				$stmt->bindParam(7, $wfdata_text);
				// $stmt->bindParam(8, $data_norm);
				$stmt->execute();

				$submit_id = $nextval;
			}

			if (! $idata->hasErrors()) {
				$infos = $idata->getInfos();
				$warns = $idata->getWarnings();
				// $_SESSION['info_messages'] = $infos;
				// $_SESSION['warn_messages'] = $warns;
				// print_r($_SESSION['warn_messages']);

				if (isset($_POST['next']) && ! empty($submit_id)) {
					$URL = "/prepo/edit_step2?s=$submit_id";
					$response = Response::make('', 301);
					$response->header('Location', $URL);
					return $response;
				}
				if (isset($_POST['save_finalize']) && ! empty($submit_id)) {
					$URL = "/prepo/edit_step2?s=$submit_id&finalize=1";
					$response = Response::make('', 301);
					$response->header('Location', $URL);
					return $response;
				}
			}

			$_REQUEST['idata'] = $idata;
			$_REQUEST['item_id'] = $item_id;
			$_REQUEST['submit_id'] = $submit_id;
			$_REQUEST['edoc'] = $edoc;
			$_REQUEST['cd'] = $cd;
			$_REQUEST['item_collection'] = $item_collection;
		}

		$this->show();
	}

	// function edit_item_step2() {

	// $tmp = get_get('finalize');
	// $finalize = empty($tmp) ? false : true;
	// //if ($finalize && $err_counter == 0){
	// if ($finalize){
	// $submit_id = $_GET['s'];
	// $URL="/prepo/edit_step3?submit_id=$submit_id";
	// error_log("redirect $URL");
	// $response = Response::make('', 301);
	// $response->header('Location', $URL);
	// return $response;
	// }else{
	// $this->show();
	// }
	// }



	function parchive_delete_submit() {
		auth_check_mentainer();

		$s = null;
		$s = get_get("s");
		if (empty($s)) {
			return;
		}

		$dbh = dbconnect();
		$SQL = "DELETE FROM dsd.submits WHERE id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $s);
		$stmt->execute();
		// drupal_add_http_header('Location', '/prepo/submits');
		$URL = '/prepo/submits';
		$response = Response::make('', 301);
		$response->header('Location', $URL);
		return $response;
	}


	function parchive_merge_subjects() {
		$chk_subject = function ($subject) {

			$dbh = dbconnect();

			$SQL = "SELECT count(*) from dsd.subject WHERE subject = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $subject);
			$stmt->execute();
			$r = $stmt->fetch();
			return $r[0] > 0;
		};

		$update_subject_like = function ($pattern_text, $replacement, $matcher) {
			$dbh = dbconnect();

			$SQL = "
					UPDATE dsd.metadatavalue2 SET text_value = regexp_replace(text_value,?,?)
					WHERE  element = 'dc:subject:' AND text_value  like ?
				";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $pattern_text);
			$stmt->bindParam(2, $replacement);
			$stmt->bindParam(3, $matcher);
			$stmt->execute();
			$count = $stmt->rowCount();

			return $count;
		};

		if (isset($_POST['merge']) && $_POST['merge'] == 'merge') {
			$s1 = get_post('s1');
			$s2 = get_post('s2');
			if (! empty($s1) && ! empty($s2)) {
				$dbh = dbconnect();

				if ($chk_subject($s1) && $chk_subject($s2)) {

					$SQL = "update dsd.metadatavalue2 SET text_value = ? WHERE text_value = ? AND element = 'dc:subject:'  ";
					$stmt = $dbh->prepare($SQL);
					$stmt->bindParam(1, $s2);
					$stmt->bindParam(2, $s1);
					$stmt->execute();
					$count = $stmt->rowCount();

					$pattern_text = $s1 . '>';
					$replacement = $s2 . '>';
					$matcher = $s1 . '>%';
					$count += $update_subject_like($pattern_text, $replacement, $matcher);

					$pattern_text = '>' . $s1;
					$replacement = '>' . $s2;
					$matcher = '%>' . $s1;
					$count += $update_subject_like($pattern_text, $replacement, $matcher);

					$pattern_text = '>' . $s1 . '>';
					$replacement = '>' . $s2 . '>';
					$matcher = '%>' . $s1 . '>%';
					$count += $update_subject_like($pattern_text, $replacement, $matcher);

					$out = '<p>';
					$out .= sprintf(' %s records of %s merged to  <a href="/archive/term?t=%s">%s</a>', $count, $s1, urlencode($s2), $s2);
					$out .= '</p>';
				} else {
					$out .= '<p>';
					$out .= 'term not found';
					$out .= '</p>';
				}
				$_REQUEST['out'] = $out;
			}
		}
		$this->show();
	}



	static function ws_empty_json_response(){
		$response = Response::make(array(), 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_folder(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

// 		$elements  = null;
// 		$obj_types = array('collection');
// 		$limit = 30;
// 		$rep  =  PDao::find_node_for_link($term,$elements,$obj_types,true,$limit);

		$flags = array('OT:collection');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);


		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_work(){

		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
// 		$elements  = null;
// 		$obj_types = array('auth-work');
// 		$limit = 30;
// 		$rep  =  PDao::find_node_for_link($term,$elements,$obj_types,true,$limit);

		$flags = array('OT:auth-work');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}

	function ws_search_expression(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
// 		$elements  = null;
// 		$obj_types = array('auth-expression');
// 		$limit = 30;
// 		$rep  =  PDao::find_node_for_link($term,$elements,$obj_types,true,$limit);

		$flags = array('OT:auth-expression');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}

	function ws_search_work_all(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
// 		$elements  = null;
// 		$obj_types = array('auth-expression','auth-work');
// 		$limit = 30;
// 		$rep  =  PDao::find_node_for_link($term,$elements,$obj_types,true,$limit);

		$flags = array('OT:auth-work','OT:auth-expression');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_manifestation(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
// 		$elements  = array();
// 		$obj_types = array('auth-manifestation');
// 		$limit = 30;
// 		$rep  =  PDao::find_node_for_link($term,$elements,$obj_types,true,$limit);

		$flags = array('OT:auth-manifestation');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


// 	function ws_search_item(){
// 		$term = get_get("term");
// 		if (empty($term)) {
// 			return;
// 		}

// 		$flags = array('OT:physical-item','OT:digital-item');
// 		$limit = 30;
// 		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);



// // 		$elements  = array();
// // 		$obj_types = array('physical-item','digital-item');

// // 		$limit = 30;
// // 		$rep  =  PDao::find_node_for_link($term,$elements,$obj_types,false,$limit);

// 		$response = Response::make($rep, 200);
// 		$response->header('Cache-Control', 'no-cache, must-revalidate');
// 		$response->header('Content-Type', 'application/json');
// 		return $response;
// 	}


	function ws_search_item(){
		//Log::info("ws_search_person");
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('IS:item');
		$limit = 30;
		$rep  =  PDao::find_node_for_artifact($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}



	function ws_search_person(){
		//Log::info("ws_search_person");
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-person');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}



	function ws_search_digital_item(){

		$term = get_get("term");
// 		if (empty($term)) {
// 			return;
// 		}
		$flags = array('OT:digital-item','ORPHAN');
		$limit = 30;
		$search_init = true;

		$rep  =  PDao::find_node_for_subject($term,$flags ,true, $limit, $search_init);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}

	function ws_search_lemma_category(){

		$term = get_get("term");

		$flags = array('OT:lemma-category');
		$limit = 50;

		$rep  =  PDao::find_node_for_category($term,$flags ,true, $limit, true);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_periodic_publication(){

		$term = get_get("term");

		$flags = array('OT:periodic-publication');
		$limit = 30;

		$rep  =  PDao::find_node_for_subject($term,$flags ,true, $limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_web_site_instance(){

		$term = get_get("term");
// 		if (empty($term)) {
// 			return AdminController::ws_empty_json_response();
// 		}

		$flags = array('OT:web-site-instance');
		$limit = 30;

		$rep  =  PDao::find_node_for_subject($term,$flags ,true, $limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_media(){

		$term = get_get("term");
		// 		if (empty($term)) {
		// 			return AdminController::ws_empty_json_response();
		// 		}

		$flags = array('OT:media');
		$limit = 30;

		$rep  =  PDao::find_node_for_subject($term,$flags ,true, $limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}

// 	function ws_search_lemma_manif_citations(){

// 		$term = get_get("term");

// 		$flags = array('OT:web-site-instance','OT:periodic-publication');
// 		$limit = 30;

// 		$rep  =  PDao::find_node_for_subject($term,$flags ,true, $limit);

// 		$response = Response::make($rep, 200);
// 		$response->header('Cache-Control', 'no-cache, must-revalidate');
// 		$response->header('Content-Type', 'application/json');
// 		return $response;
// 	}


	function ws_search_lemma_manif_citations(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:web-site-instance','OT:periodic-publication','OT:auth-manifestation','OT:media');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_family(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-family');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_organization(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-organization');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_place(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-place');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}



	function ws_search_subject(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-person','OT:auth-organization','OT:auth-family','OT:auth-work','OT:auth-general','OT:auth-concept','OT:auth-object','OT:auth-event','OT:auth-place','OT:auth-genre');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}




	function ws_search_subject_limited(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}
// 		$elements  = array();
// 		$obj_types = array('auth-general','auth-concept','auth-object','auth-event','auth-place','auth-genre');
// 		$limit = 30;
// 		$rep  =  PDao::find_node_for_link($term,$elements,$obj_types,true,$limit);

		$flags = array('OT:auth-general','OT:auth-concept','OT:auth-object','OT:auth-event','OT:auth-place','OT:auth-genre');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_subject_all(){
		$term = get_get("term");

		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$key = get_get('key');

		$primary_subject_obj_type_map = Setting::get('primary_subject_obj_type_map');
		$subject_obj_type_map = Setting::get('subject_obj_type_map');
		$obj_type_map = array_merge($primary_subject_obj_type_map , $subject_obj_type_map);

		if(isset($obj_type_map[$key])){
			if (PUtil::strEndsWith($key,':primary')){
					$flags = array("HAS:$obj_type_map[$key]");
			} else {
					$flags = array("IS:$obj_type_map[$key]");
			}
			$limit = 30;
			$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);
		}else{
			Log::info("Key '$key' does not exist in 'primary_subject_obj_type_map' && 'subject_obj_type_map'");
			return;
		}

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_subject_form(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-genre');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_subject_event(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-event');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_subject_object(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-object');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_subject_concept(){

		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-concept');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;

	}


	function ws_search_subject_general(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-general');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_subject_chain(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:subject-chain');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}


	function ws_search_type_event(){
		$term = get_get("term");
		if (empty($term)) {
			return AdminController::ws_empty_json_response();
		}

		$flags = array('OT:auth-concept','OT:auth-genre');
		$limit = 30;
		$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit);

		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}



	public function lockTransactionReset() {

		auth_check_mentainer();
		$reset_lock = false;

		if (isset($_POST['reset_lock_transaction']) && $_POST['reset_lock_transaction'] == 'reset_lock_transaction') {
			$reset_lock =  $this->lockTransactionResetFn();
		}

		$_REQUEST['reset_lock_transaction'] = $reset_lock;
		$this->show();

	}


	public function lockTransactionResetFn() {

			Log::info("LOCK TRANSACTION RESET");
			$con = dbconnect();
			$SQL = "UPDATE dsd.ruleengine_lock SET pid=NULL, ts_start=NULL WHERE id=1";
			$stmt = $con->prepare($SQL);
			$stmt->execute();
			return true;

	}

}