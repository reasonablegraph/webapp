<?php
class EditItemController extends BaseController {



	public function create_item() {
		Log::info("CREATE ITEM");
		auth_check_mentainer();

		if (empty($_POST)) {
			return;
		}

		//Log::info(print_r($_POST,true));

		// $idata = new ItemMetadata();
		// foreach ($_POST as $pk=>$pv) {
		// if (! PUtil::strBeginsWith($pk, 'tmp:') ){
			// $idata->replaceValuesFromClient($pk,$pv);
			// }
			// }
			// //$idata->validate();
			// $is = new ItemSave();
			// $is->setIdata($idata);
			// $is->save_item();
		}


		//xx
public function create_subitem() {
		$pid = getmypid();
		Log::info(">>>>>>>>>>>>>>>>>> CREATE SUBITEM pid: " . $pid);
		auth_check_mentainer();

		if (empty($_POST)) {
			Log::info("[EMPTY POST]");
			return;
		}

		$rlock = new GRuleEngineLock();
		$rlock->lock();

		$userName = ArcApp::username();
		// $root = null;
		// if (isset($_GET['root'])){
		// $root = intval($_GET['root']);
			// }
			// if (empty($root)){
			// return;
			// }

			//Log::info(print_r($_POST,true));
		$subitemFlag = true;
		if (isset($_POST['SUBITEM']) && $_POST['SUBITEM'] == 'FALSE'){
			$subitemFlag = false;
		}

			$idata = new ItemMetadata();
			foreach ( $_POST as $pk => $pv ) {
				if (! PUtil::strBeginsWith($pk, 'tmp:')) {
					$idata->replaceValuesFromClient($pk, $pv);
				}
			}

			$options = null;
			$optionsIV = $idata->getFirstItemValue("trn:options:");
			if (! empty($optionsIV)) {
				// Log::info($optionsIV->data());
				$data = $optionsIV->data();
				if (! empty($data) && isset($data['prps'])) {
					$options = $data['prps'];
				}
				$idata->put("options", $options);
			}
			// if (! empty($options)){
			// $options['root'] = $root;
			// $idata->put("root", $root);

			// $idata->validate();
			$is = new ItemSave();
			$is->setIdata($idata);
			$is->setUserName($userName);
			$is->setRlock($rlock);
			$item_id = $is->save_item($subitemFlag);

			$rlock->release();

			//$label = PDao::getItemLabel($item_id);
			$rec = PDao::getItemLabelOT($item_id);
			$label = $rec['label'];
			$ot = $rec['obj_type'];

			$rep = array("item_id"=>$item_id,'label'=>$label, 'ot'=>$ot);

// 			$neighbourhood = GGraphIO::getNeighbourhoodIds($item_id);
// 			Log::info(print_r($neighbourhood,'true'));
// 			if (!empty($neighbourhood)){
// 				foreach ($neighbourhood as $name => $ids){
// 					Log::info('neighbourhood ' . $name . ' : ' . implode(',',$ids));
// 				}
// 			}
			//Log::info(print_r($rep,true));



			Log::info("<<<<<<<<<<<<<<<<<< CREATE SUBITEM: " . $pid . ' ITEM  ID: ' . $item_id);
			$response = Response::make($rep, 200);
			$response->header('Cache-Control', 'no-cache, must-revalidate');
			$response->header('Content-Type', 'application/json');
			return $response;


		}







public function step1() {
		auth_check();

		Log::info("STEP1");
		//if (!empty($_POST)) {	Log::info(print_r($_POST,true));		}

		$app = App::make('arc');

		$user = ArcApp::username();

		if (empty($user)) {
			echo ("auth error");
			return;
		}

		$dbh = dbconnect();

		//activity_log
		$url = $_SERVER['REQUEST_URI'];
		$type_act = 'edit';
		PDao::activity_log($type_act, $user, $url);

		$submit_id = null;
		$item_id = null;
		$idata = new ItemMetadata();
		$wfdata = array();

		$submit_id = get_get("s");
		$item_id = get_get("i");
		$ptype = get_get("type");
		$edoc = get_get("edoc");
		$cd = get_get("cd");
		$wfdata['cd'] = $cd;
		$item_collection = get_get("item_collection");
		$wfdata['item_collection'] = $item_collection;

		// openWindow
		$redirect_item = get_get("rd");
		$personal_name = get_get("pn");

		// check for active submission
		if (!empty($item_id)) {
			$SQL = "select count(id) as count from dsd.submits where item_id = ? and status NOT IN (?, ?) and ((current_timestamp - update_dt) < interval '1 hour')";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->bindParam(2, SubmitsStatus::$finished);
			$stmt->bindParam(3, SubmitsStatus::$error);
			$stmt->execute();
			$r = $stmt->fetch();
			if (intval($r[0]) > 0) {
				ArcApp::template('admin.edit_item_step1_already_active_submission');
				return $this->show();
			}
		}

		$owner = null;
		//lock edit form submitter
		$is_admin = ArcApp::user_access_admin();
		$edit_lock_owner = Config::get('arc.owner_edit_form_lock',0);
		if ( $edit_lock_owner && !empty($item_id) ) {
			$SQL = "SELECT user_create  from dsd.item2 WHERE item_id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$stmt->bindColumn(1, $owner);
			if (! $stmt->fetch()) {
				echo ("ERROR: item submitter not found ");
				return;
			}

			if ( $owner!= $user && !$is_admin){
				$URL = UrlPrefixes::$cataloging;
				$response = Response::make('', 301);
				$response->header('Location', $URL);
				return $response;
			}
		}


		if (! empty($personal_name)) {
			$idata->setValueSK('ea:auth:Person_Name', $personal_name);
		}

		if (! empty($redirect_item)) {
			$idata->setValueSK('ea:obj-type:', $redirect_item);
		}

		$agg_type = false; // FOLDER
		$tmp = get_post_get("agt"); // FOLDER
		if (! empty($tmp) && $tmp == 1) {
			$agg_type = true;
		}
		$wfdata['agg_type'] = $agg_type;

		$artifact_type = false;
		// $tmp = get_post_get("aft");
		// if (!empty($tmp) && $tmp == 1 ){
		// $artifact_type = true;
		// }
		$artifact_ref_item_id = null;
		$tmp = get_post_get("afti");
		if (! empty($tmp) && PUtil::chk_int($tmp)) {
			$artifact_type = true;
			$artifact_ref_item_id = PUtil::extract_int($tmp);
		}

		if (empty($artifact_ref_item_id)) {
			$artifact_type = false;
		}
		$wfdata['artifact_type'] = $artifact_type;

		$vivliografiki_anafora = false;
		$tmp = get_post_get("br"); // VIVLIOGRAFIKI ANAFORA
		if (! empty($tmp) && $tmp == 1) {
			$vivliografiki_anafora = true;
		} else if ($item_id != null) {
			$SQL = "select bibref from dsd.item2 where item_id =?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			if ($rs = $stmt->fetch()) {
				if ($rs['bibref'] == 1) {
					$vivliografiki_anafora = true;
				}
			}
		}
		$wfdata['vivliografiki_anafora'] = $vivliografiki_anafora;

		$thumb = get_post('thumb');
		if (! empty($thumb)) {
			$wfdata['thumb1'] = $thumb;
		}

		// $libgenxml = get_post ( 'libgenxml' );
		// if (! empty ( $libgenxml )) {
		// $wfdata ['libgenxml'] = $libgenxml;
		// }

		// //////////////////////////////////////////

		$stype = null;
		$site_date_captured = null;
		$site_url = null;
		$site_url_base = null;

		if (! empty($edoc) && preg_match('/^\/sites\//', $edoc)) {
			$stype = "sites";

			// preg_match('/\/(\w+)\.\w\w\w$/', $edoc, $matches);
			// $base = $matches[1];
			// $ext = $matches[2];
			// $edoc_file_name = $base . ".txt";

			$tmp = substr($edoc, 7);
			$edoc_file = SPOOL_dir_sites_pending . (substr($tmp, 0, (strlen($tmp) - 3))) . "txt";
			// echo("## $edoc_file ##");
			// $edoc_file = SPOOL_dir_sites_pending . $edoc_file_name;

			$cmd = "cat $edoc_file|head -1 ";
			$cmd_out = array();
			$status = 0;
			$tmp = exec($cmd, $cmd_out, $status);
			$site_date_captured = $cmd_out[0];

			$cmd = "cat $edoc_file|head -2 |tail -1 ";
			$cmd_out = array();
			$status = 0;
			$tmp = exec($cmd, $cmd_out, $status);
			$site_url = $cmd_out[0];
			if (! empty($site_url)) {
				$tmp = parse_url($site_url);
				if (isset($tmp['scheme']) && isset($tmp['host'])) {
					$site_url_base = $tmp['scheme'] . "://" . $tmp['host'];
				}
			}

			echo ("<ul>");
			echo ("<li>$site_date_captured</li>");
			echo ("<li>$site_url</li>");
			echo ("<li>$site_url_base</li>");
			echo ("</ul>");

			$cmd = "cat $edoc_file";
			$status = 0;
			$site_info = ARRAY();
			$tmp = exec($cmd, $site_info, $status);
			if (count($site_info) > 0) {
				echo ("<pre>");
				foreach ( $site_info as $line ) {
					echo ("$line\n");
				}
				echo ("</pre>");
			}
		}

		$item_load_flag = false;

		$data = null;
		$wfdata_text = null;
		$submits_type = null;
		if (! empty($submit_id)) {
			//error_log("edit_step1: load data from submits ($submit_id)", 0);
			$SQL = "SELECT item_id, data, edoc, wf_data, type from dsd.submits where id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $submit_id);
			$stmt->execute();
			$stmt->bindColumn(1, $item_id);
			$stmt->bindColumn(2, $data);
			$stmt->bindColumn(3, $edoc);
			$stmt->bindColumn(4, $wfdata_text);
			$stmt->bindColumn(5, $submits_type);

			if ($stmt->fetch()) {
				if ($submits_type == 2) {
					Log::info("#2# LOAD FROM SUBMITS");
					$idata = new ItemMetadata();
					$idata->replaceValuesFromClientModels(json_decode($data, true));
				} else {
					$idata = new ItemMetadata(unserialize($data));
				}
				if (! empty($wfdata_text)) {
					if ($submits_type == 2) {
						$wfdata = json_decode($wfdata_text, true);
					} else {
						$wfdata = unserialize($wfdata_text);
					}
				}
			}

			if (isset($wfdata['vivliografiki_anafora'])) {
				$vivliografiki_anafora = $wfdata['vivliografiki_anafora'];
			} else {
				$wfdata['vivliografiki_anafora'] = null;
			}
			if (isset($wfdata['agg_type'])) {
				$agg_type = $wfdata['agg_type'];
			}
			if (isset($wfdata['cd'])) {
				$cd = $wfdata['cd'];
			}
			if (isset($wfdata['item_collection'])) {
				$item_collection = $wfdata['item_collection'];
			}
		} else if (! empty($item_id)) {
			$item_load_flag = true;
			Log::info('edit_step1: load data from item: ' . $item_id);
			$idata = PDao::get_item_metadata($item_id);

			$SQL = "select bibref FROM dsd.item2 WHERE item_id = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$stmt->bindColumn(1, $vivliografiki_anafora);
			if (! $stmt->fetch()) {
				echo ("<h1>CANOT FIND item: $item_id<h1/>");
				exit();
			}
		}

		if (! empty($_POST)) {

// 			 echo("<pre>");
// 			 echo("### POST\n");
// 			 print_r($_POST);
// 			 echo("###############################\n");
// 			 echo("</pre>");
// 			 echo("EXIT"); return;

			$post_obj_type = null;
			if (isset($_POST["ea:obj-type:"])) {
				$post_obj_type = isset($_POST["ea:obj-type:"][0]) ? $_POST["ea:obj-type:"][0] : null;
			}

			$submit_id = isset($_POST['submit_id']) ? $_POST['submit_id'] : null;
			$item_id = isset($_POST['item_id']) ? $_POST['item_id'] : null;
			$edoc = isset($_POST['edoc']) ? $_POST['edoc'] : null;
			$cd = isset($_POST['cd']) ? $_POST['cd'] : null;
			$wfdata['cd'] = $cd;
			$item_collection = isset($_POST['item_collection'][0]) ? $_POST['item_collection'][0] : null;
			$wfdata['item_collection'] = $item_collection;

			if (! empty($item_id)) {
				//error_log("edit_step1: load data from item ($item_id) merge with post", 0);
				$idata = PDao::get_item_metadata($item_id);
			}

			// echo("<pre>");
			// print_r($idata->values);
			// echo("</pre>");

			// "ea:source:","ea:source:print", "ea:original:print",
			// #########################################################################
			// kataskevi IDATA
			// #########################################################################
			$it = new ItemMetadataIterator($idata);

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

			$idata->validate();
			$msg_counters = PSnipets::print_mesages($idata);
			// $err_counter =$msg_counters[0];

			// ############################################################################
			// echo("<pre>");
			// echo("\n##########2########\n");
			// print_r($idata->values);
			// echo("\n##########3########\n");
			// // print_r($idata->values_norm);
			// echo("</pre>");
			$data = serialize($idata->values);
			// $data_norm = serialize($idata->values_norm);
			$wfdata_text = serialize($wfdata);

			$title = $idata->getValueSK("dc:title:");
			$title = empty($title) ? null : $title[0];

			if (! empty($submit_id)) {
			    $submit_status = 2;
				if (empty($item_id)) {
					$item_id = null;
					$submit_status = 1;
				}

				$SQL = " update dsd.submits set user_name = ?, data = ?, item_id = ? , title = ?, edoc = ?, wf_data = ?, status = ?, type=1 WHERE id = ? ";
				$stmt = $dbh->prepare($SQL);
				$stmt->bindParam(1, $user);
				$stmt->bindParam(2, $data);
				$stmt->bindParam(3, $item_id);
				$stmt->bindParam(4, $title);
				$stmt->bindParam(5, $edoc);
				$stmt->bindParam(6, $wfdata_text);
                $stmt->bindParam(7, $submit_status);
				// $stmt->bindParam(7, $data_norm);
				$stmt->bindParam(8, $submit_id);

				$stmt->execute();
			} else {
                $submit_status = 2;
				if (empty($item_id)) {
					$item_id = null;
                    $submit_status = 1;
				}

				$nextval = null;
				$SQL = "SELECT nextval('dsd.submits_id_seq')";
				$stmt = $dbh->prepare($SQL);
				$stmt->execute();
				$stmt->bindColumn(1, $nextval);
				$stmt->fetch();

				$SQL = "insert into dsd.submits (id, user_name, data, item_id, title, edoc, wf_data, status, final_item_id) values (?,?,?,?,?,?,?,?,?)";
				$stmt = $dbh->prepare($SQL);
				$stmt->bindParam(1, $nextval);
				$stmt->bindParam(2, $user);
				$stmt->bindParam(3, $data);
				$stmt->bindParam(4, $item_id);
				$stmt->bindParam(5, $title);
				$stmt->bindParam(6, $edoc);
				$stmt->bindParam(7, $wfdata_text);
				$stmt->bindParam(8, $submit_status);
				$stmt->bindParam(9, $item_id);
				// $stmt->bindParam(8, $data_norm);
				$stmt->execute();

				$submit_id = $nextval;
			}
			if (! $idata->hasErrors()) {
				$infos = $idata->getInfos();
				$warns = $idata->getWarnings();

				// // $_SESSION ['info_messages'] = $infos;
				// //$_SESSION ['warn_messages'] = $warns;
				// print_r($_SESSION['warn_messages']);

				if (isset($_POST['next']) && ! empty($submit_id)) {
					$URL = "/prepo/edit_step2?s=$submit_id";
					// drupal_add_http_header ( 'Location', $URL );
					$response = Response::make('', 301);
					$response->header('Location', $URL);
					return $response;

					return;
				}
				if (isset($_POST['save_finalize']) && ! empty($submit_id)) {
					$URL = "/prepo/edit_step2?s=$submit_id&finalize=1";
					// drupal_add_http_header ( 'Location', $URL );
					$response = Response::make('', 301);
					$response->header('Location', $URL);
					return $response;
					return;
				}
			}
			// else {
			// unset($_SESSION['info_messages']);
			// unset($_SESSION['warn_messages']);
			// }
		} else {
			PUtil::log("#wdebug# #e#: edit requested");
		}

		$type = $idata->getValueTextSK(DataFields::ea_obj_type);
		if (! empty($type)) {
			$SQL = "SELECT agg_type from dsd.obj_type WHERE  name =?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $type);
			$stmt->execute();
			$res = $stmt->fetch();
			$agg_type = $res[0];
		}

		// $site = ($type == 'web-site-instance' || $stype == "sites");
		if (! empty($edoc)) {
			// drupal_set_title ( "step1: $edoc" );
			$app->title = "step1: $edoc";
		} else if (! empty($item_id)) {
			$title_org = null;
			$SQL = "SELECT title from dsd.item2 WHERE item_id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$stmt->bindColumn(1, $title_org);
			if (! $stmt->fetch()) {
				echo ("ERROR: (7)");
			}
			// drupal_set_title ( "step1: ($item_id) $title_org" );
			$app->title = "step1: ($item_id) $title_org";
		}

		$item_rec = null;
		if (! empty($item_id)){
			$SQL = "SELECT * from dsd.item2 WHERE item_id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$item_rec = $stmt->fetch(PDO::FETCH_ASSOC);
			//Log::info(print_r($item,true));
		}

		$_REQUEST['idata'] = $idata;
		$_REQUEST['item_id'] = $item_id;
		$_REQUEST['submit_id'] = $submit_id;
		$_REQUEST['edoc'] = $edoc;
		$_REQUEST['cd'] = $cd;
		$_REQUEST['type'] = $ptype;
		$_REQUEST['item_collection'] = $item_collection;
		$_REQUEST['wfdata'] = $wfdata;

		$_REQUEST['artifact_ref_item_id'] = $artifact_ref_item_id;

		$_REQUEST['stype'] = $stype;
		$_REQUEST['site_date_captured'] = $site_date_captured;
		$_REQUEST['site_url'] = $site_url;
		$_REQUEST['site_url_base'] = $site_url_base;
		$_REQUEST['agg_type'] = $agg_type;
		$_REQUEST['artifact_type'] = $artifact_type;
		$_REQUEST['thumb'] = $thumb;

		$this->show(array('item_rec' => $item_rec ));
	}

	// ///////
public function step2() {
		$app = App::make('arc');

		$dbh = dbconnect();
		$submit_id = null;
		$item_id = null;

		$idata = new ItemMetadata();
		$wfdata = array();
		$vivliografiki_anafora = false;
		$agg_type = false;

		$item_load_flag = false; // true: edit old item
		$status = null;

		$cd = null;
		$item_collection = null;
		$bitstream_flag = false;

		$item_pages = null;
		$item_site = null;
		$item_lang = null;
		$item_uuid = null;
		$item_issue_aggr = null;
		$item_fts_catalogs = null;
		$item_in_folder = null;
		$item_folder = null;
		$item_in_archive = null;
		$item_create_dt = null;
		$item_update_dt = null;
		$has_bitstreams = null;

		//activity_log
		$url = $_SERVER['REQUEST_URI'];
		$user = ArcApp::username();
		if (empty($user)) {
			$user = 'Unknown';
		}
		$type_act = 'admin';
		PDao::activity_log($type_act, $user, $url);



		if (isset($_GET['s'])) {

			$data = null;
			$edoc = null;
			$wfdata_text = null;
			$submit_id = $_GET['s'];
			$SQL = "SELECT item_id, data, edoc, wf_data from dsd.submits where id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $submit_id);
			$stmt->execute();
			$stmt->bindColumn(1, $item_id);
			$stmt->bindColumn(2, $data);
			$stmt->bindColumn(3, $edoc);
			$stmt->bindColumn(4, $wfdata_text);
			// $stmt->bindColumn(5, $data_norm);

			if ($stmt->fetch()) {
				$idata = new ItemMetadata(unserialize($data));
				if (! empty($wfdata_text)) {
					$wfdata = unserialize($wfdata_text);
				}
				if (isset($wfdata['vivliografiki_anafora'])) {
					$vivliografiki_anafora = $wfdata['vivliografiki_anafora'];
				}
				if (isset($wfdata['agg_type'])) {
					$agg_type = $wfdata['agg_type'];
				}
				if (isset($wfdata['cd'])) {
					$cd = $wfdata['cd'];
				}
				if (isset($wfdata['item_collection'])) {
					$item_collection = $wfdata['item_collection'];
				}
				$obj_type = $idata->getValueText('ea', 'obj-type');
				$bitstream_flag = $obj_type == 'bitstream';
			}
		} else if (isset($_GET['i'])) {
			$item_load_flag = true;
			$item_id = intval($_GET['i']);
			$idata = PDao::get_item_metadata($item_id);


			#################################################################################################################

			if (! empty($_POST['error'])) {
				$idata->setValueSK('ea:status:', 'error');
				$is = new ItemSave();
				$is->setIdata($idata);
				$is->setItemId($item_id);
				$item_id = $is->save_item();
				$idata = PDao::get_item_metadata($item_id);

				if (Config::get('arc.ENABLE_SOLR',1)>0) {
					Log::info("SOLR TRY DELETE ITEM ".$item_id);
					try {
						$client = new Solarium\Client(array('endpoint' => PUtil::getSolrConfigEndPoints('opac')));
						$update = $client->createUpdate();
						//$update->addDeleteQuery('id:'.$item_id);
						$update->addDeleteById($item_id);
						$update->addCommit();
						$result = $client->update($update);
						Log::info("SOLR DELETE ITEM ".$item_id);
						Log::info('SOLR Query status: ' . $result->getStatus());
						Log::info('SOLR Query time: ' . $result->getQueryTime());
					} catch (Exception $e) {
						Log::info("SOLR DELETE ITEM ".$item_id." FAILED " . $e->getMessage());
						Log::info($e);
					}
				}
			}

			#################################################################################################################

			$obj_type = $idata->getValueText('ea', 'obj-type');
			$bitstream_flag = $obj_type == 'bitstream';
			$item_incoming = null;
			$user_create = null;
			$SQL = "select status,bibref,fts_catalogs,lang,uuid,folder,issue_aggr,in_archive,pages, dt_create ,dt_update,incoming,site,user_create FROM dsd.item2 WHERE item_id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$stmt->bindColumn(1, $status);
			$stmt->bindColumn(2, $vivliografiki_anafora);
			$stmt->bindColumn(3, $item_fts_catalogs);
			$stmt->bindColumn(4, $item_lang);
			$stmt->bindColumn(5, $item_uuid);
			$stmt->bindColumn(6, $item_folder);
			$stmt->bindColumn(7, $item_issue_aggr);
			$stmt->bindColumn(8, $item_in_archive);
			$stmt->bindColumn(9, $item_pages);
			$stmt->bindColumn(10, $item_create_dt);
			$stmt->bindColumn(11, $item_update_dt);
			$stmt->bindColumn(12, $item_incoming);
			$stmt->bindColumn(13, $item_site);
			$stmt->bindColumn(14, $user_create);

			if (! $stmt->fetch()) {
				echo ("<h1>CANOT FIND item: $item_id<h1/>");
				exit();
			}

			if (PUtil::user_access_item_submiter()) {
				if ($status !=  Config::get('arc.ITEM_STATUS_PENDING')) {
					return;
				}
			}

			$item_in_folder = ! $item_incoming;
		}

		$tmp = get_get('finalize');
		$finalize = empty($tmp) ? false : true;
		// echo("<pre>");
		// print_r($idata->values);
		// echo("</pre>");

		if (! empty($edoc)) {
			// drupal_set_title("step2: $edoc");
			$app->title = "step2: $edoc";
		} else if (! empty($item_id)) {
			$title_org =  null;
			$SQL = "SELECT title from dsd.item2 WHERE item_id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			$stmt->bindColumn(1, $title_org);
			if (! $stmt->fetch()) {
				echo ("ERROR: (7)");
			}
			// drupal_set_title("step2: ($item_id) $title_org");
			$app->title = "step2: ($item_id) $title_org";
		}

		$err_counter = 0;
		if (! empty($cd) && empty($item_collection)) {
			echo ("<pre>");
			$err_counter ++;
			echo ("ERROR: collection id is missing\n");
			echo ("</pre>");
		}

		$collection_name = null;
		if (! empty($item_collection)) {
			$SQL = sprintf("SELECT label FROM dsd.item2 WHERE item_id = ? AND obj_type = '%s'", DB_OBJ_TYPE_SILOGI);
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_collection);
			$stmt->execute();
			if (! $row = $stmt->fetch()) {
				echo ("<pre>");
				echo ("ERROR: canot found collection $item_collection \n");
				$err_counter ++;
				echo ("</pre>");
			}
			$collection_name = $row[0];
		}

		if ($finalize && $err_counter == 0) {
			$submit_id = $_GET['s'];
			$URL = "/prepo/edit_step3?submit_id=" . $submit_id; // default redirection url for non-async-workers mode
			$async_workers_enable = Config::get('arc.async_workers_enable', 0);

			if ($async_workers_enable) {
				// construct job workload
				$wl = new stdClass();
				$wl->submit_id = $submit_id;
				$wl->userName = ArcApp::username();
				$wl->uid = ArcApp::uid();
				$wl->locale = App::getLocale();

				// send job to worker
				$gmclient = new GearmanClient();
				$gmclient->addServer();

				$sync_flag = get_get('sf', '0') == '1'; // mainly for testing workers in sync mode

				PUtil::log("#wdebug# #s#: submit save requested");
				if ($sync_flag) {
					$gmclient->doNormal(WorkerPrefixes::$edit_step3, json_encode($wl));
				} else {
					$gmclient->doBackground(WorkerPrefixes::$edit_step3, json_encode($wl));
				}

				$URL = "/prepo/cataloging?s_id=" . $submit_id; // redirect to cataloging if new item
				if (!empty($item_id)) {
					$URL = "/prepo/edit_step3?i=" . $item_id; // redirect to admin page if old item
				}
			}

			//error_log("redirect $URL");
			$response = Response::make('', 301);
			$response->header('Location', $URL);
			return $response;
		}

		if (!empty($item_id)) {
		$has_bitstreams = PDao::hasBitstreams($item_id);
		}



		//DRYLL
		$basic = PDao::getItemBasic($item_id);
		$project = Config::get('arc.PROJECT_NAME');
		$is_periodic = false;
		$submit_periodic_send_email = false;
		$is_book = false;
		$submit_book_send_email = false;
		$is_issue = false;
		$submit_issue_send_email = false;

		if ($project == 'dryl'){

			if ($basic['obj_type'] == 'periodic'){
				$is_periodic = true;
				if (!empty($_POST['periodic_send_email'])) {
					$prm_secretary_email = Config::get('arc.PRIMARY_SECRETARY_EMAIL');
					$item_label = $basic['label'];
					Mail::send('emails.dryl.secretary_periodic', array('label' => $item_label), function($message) use ($prm_secretary_email,$item_label)
					{
						$message->to($prm_secretary_email, tr('email_to_secretary'));
						$message->subject(tr('email_secretary_periodic_subject').$item_label);
					});
					$submit_periodic_send_email = true;
				}
			}
			if ($basic['obj_type'] == 'auth-manifestation'){
				if (!empty($basic['flags_json'])){
					$flags = json_decode($basic['flags_json'], true);
					if (in_array('IS:book',$flags)){
						$is_book = true;
						if (!empty($_POST['book_send_email'])) {
							$prm_secretary_email = Config::get('arc.PRIMARY_SECRETARY_EMAIL');
							$scd_secretary_email = Config::get('arc.SECONDARY_SECRETARY_EMAIL', null);
							$item_label = $basic['label'];
							Mail::send('emails.dryl.secretary_book', array('label' => $item_label), function($message) use ($prm_secretary_email,$scd_secretary_email,$item_label)
							{
								$message->to($prm_secretary_email, tr('email_to_secretary'));
								if(!empty($scd_secretary_email)){
									$message->cc($scd_secretary_email, tr('email_to_lawyers'));
								}
								$message->subject(tr('email_secretary_book_subject').$item_label);
							});
							$submit_book_send_email = true;
						}
					}elseif(in_array('IS:issue',$flags)){
						$is_issue = true;
						if (!empty($_POST['issue_send_email'])) {
							$item_label = $basic['label'];
							$itemRelationsFrom = PDao::getItemRelationsFrom($item_id);
							$emails = array();
							if(!empty($itemRelationsFrom)){
								foreach ($itemRelationsFrom as  $key => $relitem){
									if ($relitem['obj_type'] == 'periodic'){
										$jdata = json_decode($relitem['jdata'], true);
										if(!empty($jdata['opac2']['lawyers_emails'])){
											$lawyers_emails = $jdata['opac2']['lawyers_emails'];
											foreach ($lawyers_emails as  $email){
												$emails[]=$email;
											}
										}
									}
								}
							}
							if(!empty($emails)){
								$prm_secretary_email = Config::get('arc.PRIMARY_SECRETARY_EMAIL');
								Mail::send('emails.dryl.secretary_issue', array('label' => $item_label), function($message) use ($prm_secretary_email,$emails,$item_label){
										foreach ($emails as  $email){
											$message->to($email, tr('related_lawyer'));
										}
										$message->cc($prm_secretary_email, tr('email_to_secretary'));
										$message->subject(tr('email_secretary_issue_subject').$item_label);
									});
									$submit_issue_send_email = true;
							}
						}
					}
				}
			}
		}

		$_REQUEST['is_periodic'] = $is_periodic; //DRYLL
		$_REQUEST['periodic_notification'] = $submit_periodic_send_email; //DRYLL
		$_REQUEST['is_book'] = $is_book; //DRYLL
		$_REQUEST['book_notification'] = $submit_book_send_email; //DRYLL
		$_REQUEST['is_issue'] = $is_issue; //DRYLL
		$_REQUEST['issue_notification'] = $submit_issue_send_email; //DRYLL
		//**


		$_REQUEST['submit_id'] = $submit_id;
		$_REQUEST['item_id'] = $item_id;
		$_REQUEST['idata'] = $idata;
		$_REQUEST['wfdata'] = $wfdata;
		$_REQUEST['vivliografiki_anafora'] = $vivliografiki_anafora;
		$_REQUEST['agg_type'] = $agg_type;
		$_REQUEST['item_load_flag'] = $item_load_flag;
		$_REQUEST['status'] = $status;
		$_REQUEST['cd'] = $cd;
		$_REQUEST['item_collection'] = $item_collection;
		$_REQUEST['bitstream_flag'] = $bitstream_flag;
		$_REQUEST['err_counter '] = $err_counter;

		$_REQUEST['item_pages'] = $item_pages;
		$_REQUEST['item_site'] = $item_site;
		$_REQUEST['item_lang'] = $item_lang;
		$_REQUEST['item_uuid'] = $item_uuid;
		$_REQUEST['item_issue_aggr'] = $item_issue_aggr;
		$_REQUEST['item_fts_catalogs'] = $item_fts_catalogs;
		$_REQUEST['item_in_folder'] = $item_in_folder;
		$_REQUEST['item_folder'] = $item_folder;
		$_REQUEST['item_in_archive'] = $item_in_archive;
		$_REQUEST['item_create_dt'] = $item_create_dt;
		$_REQUEST['item_update_dt'] = $item_update_dt;
		$_REQUEST['item_user_create'] = $user_create;
		$_REQUEST['has_bitstreams'] = $has_bitstreams;

		$this->show();
	}


	public function save_submit() {
		//Log::info("SAVE_SUBMIT");
		// $postdata = file_get_contents("php://input");
		// Log::info($postdata);
		$data = json_decode(file_get_contents("php://input"), true);
	 //Log::info(print_r($data,true));


		$sdata = $data['sdata'];
		$wfdata = $data['wfdata'];
		Log::info(print_r($wfdata, true));
		$item_id = $wfdata['item_id'];
		if ($item_id == '') {
			$item_id = null;
		}
		$edoc = $wfdata['edoc'];
		$submit_id = $wfdata['submit_id'];
		if ($submit_id == '') {
			$submit_id = null;
		}

		$app = App::make('arc');
		$user = $app->username;

		$title = null;
		foreach ( $sdata as $m ) {
			//Log::info(print_r($m, true));
			if ($m['k'] == 'dc:title:' || $m['k'] == 'marc:title-statement:title') {
				$title = $m['v'];
			}
		}
		Log::info("TITLE: $title");
		Log::info("USER: $user");

		// $json_sdata = json_encode($sdata, JSON_PRETTY_PRINT);
		// $json_wfdata = json_encode($wfdata, JSON_PRETTY_PRINT);
		$json_sdata = json_encode($sdata);
		$json_wfdata = json_encode($wfdata);

		$dbh = dbconnect();

		if (! empty($submit_id)) {
			if (empty($item_id)) {
				$item_id = null;
			}

			$SQL = " update dsd.submits set user_name = ?, data = ?, item_id = ? , title = ?, edoc = ?, wf_data = ? WHERE id = ? ";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $user);
			$stmt->bindParam(2, $json_sdata);
			$stmt->bindParam(3, $item_id);
			$stmt->bindParam(4, $title);
			$stmt->bindParam(5, $edoc);
			$stmt->bindParam(6, $json_wfdata);
			// $stmt->bindParam(7, $data_norm);
			$stmt->bindParam(7, $submit_id);

			$stmt->execute();
		} else {

			$nextval = PDao::nextval('dsd.submits_id_seq');
			$SQL = "insert into dsd.submits (id, user_name, data, item_id, title, edoc, wf_data,type) values (?,?,?,?,?,?,?,2)";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $nextval);
			$stmt->bindParam(2, $user);
			$stmt->bindParam(3, $json_sdata);
			$stmt->bindParam(4, $item_id);
			$stmt->bindParam(5, $title);
			$stmt->bindParam(6, $edoc);
			$stmt->bindParam(7, $json_wfdata);
			// $stmt->bindParam(8, $data_norm);
			$stmt->execute();

			$submit_id = $nextval;
		}

		Log::info("submit ID: " . $submit_id);

		$rep = array("submit_id" => $submit_id );

		$response = Response::make(json_encode($rep), 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}
















	/**
	 *  STEP3
	 */
public function step3() {
		Log::info(">>>>>>>>>>>>>>>>>> EDIT_ITEM_STEP3: ");
		ArcApp::auth_check();

		$userName = ArcApp::username();

		$item_id  = (int)get_get("i");
		$submit_id = (int)get_post_get('submit_id');

		//activity_log
		$url = $_SERVER['REQUEST_URI'];
		$user = ArcApp::username();
		if (empty($user)) {
			$user = 'Unknown';
		}
		$type_act = 'admin';
		PDao::activity_log($type_act, $user, $url);

		if (! empty($_POST) && !empty($item_id)){
			PUtil::upload_bitstream_from_post_data($item_id);
		}

		if (empty($submit_id)) {
			ArcApp::template('admin.edit_item_step3_old_rec');
		} else {

			$submit_data = PDao::loadSubmit($submit_id);
			if (empty($submit_data)) {
				echo ("error (submit_data)");
			}

			$wfdata = $submit_data['wfdata'];
			$status = $submit_data['status'];

			$item_id = null;
			if ($status == 10) {

				$item_id = $submit_data['final_item_id'];
				ArcApp::template('admin.edit_item_step3_old_rec');

			} else {
				$lock_msg = isset($submit_data['item_id'])? $submit_data['item_id'] : null;
				$rlock = new GRuleEngineLock();
				$rlock->lock($lock_msg);


				ArcApp::template('admin.edit_item_step3_new_rec');

				$vivliografiki_anafora = false;
				$agg_type = null;
				$cd = null;
				$item_collection = null;
				$thumb1 = null;

				if (isset($wfdata['vivliografiki_anafora'])) {
					$vivliografiki_anafora = $wfdata['vivliografiki_anafora'];
				}
				if (isset($wfdata['agg_type'])) {
					$agg_type = $wfdata['agg_type'];
				}
				if (isset($wfdata['cd'])) {
					$cd = $wfdata['cd'];
				}
				if (isset($wfdata['item_collection'])) {
					$item_collection = $wfdata['item_collection'];
				}
				if (isset($wfdata['thumb1'])) {
					$thumb1 = $wfdata['thumb1'];
				}
				// if (isset($wfdata['libgenxml'])) {
				// $libgenxml = $wfdata['libgenxml'];
				// }
				$idata = $submit_data['idata'];
				$is = new ItemSave();
				$is->setIdata($idata);
				$is->setEdoc($submit_data['edoc']);
				$is->setSubmitId($submit_data['submit_id']);
				$is->setWfdata($submit_data['wfdata']);
				$is->setUserName($userName);
				$is->setItemId($submit_data['item_id']);
				$is->setRlock($rlock);

				$idata->validate();
				$errors = $idata->getErrors();
				$warnings = $idata->getWarnings();
				$infos = $idata->getInfos();

				//$msg_counters = PSnipets::print_mesages($idata);
				$err_counter = count($errors);
				if ($err_counter > 0) {
					$data = array(
						'errors'=>$errors,
						'warnings'=>$warnings,
						'infos'=>infos,
						'submit_id'=>$submit_id,
					);

					$rlock->release();
					ArcApp::template('admin.edit_item_step3_error');
					return $this->show($data);
				}

				//echo ("<pre>");
				$item_id = $is->save_item();
				$rlock->release();
			//	Log::info("SAVE ITEM " . $item_id . " FINISH");
				//echo ("<pre>");

				// echo("\n");
				// echo("\n");
				// echo("$title\n");
			}

		}


		$item = PDao::getItemDBRecord($item_id);

		$create_ts =(new DateTime($item['dt_create']))->getTimestamp();
		$create_user =$item['user_create'];
		$item['user_org'] = PDao::getUserOrganization($create_user);

// 		$dbh = dbconnect();
// 		$SQL="
// 		WITH RELATIONS AS (
// 				SELECT i.item_id, i.element,  i.ref_item , 'OUT' as direction, r.label,i.inferred, r.dt_create, r.user_create, r.obj_type, r.obj_class
// 				FROM dsd.metadatavalue2 i
// 				JOIN dsd.item2 r ON (r.item_id = i.ref_item)
// 				WHERE  i.item_id = ? and i.level = 0 and i.ref_item is not null AND not i.inferred
// 				UNION
// 				SELECT i.ref_item as item_id,  i.element,  i.item_id as ref_item, 'IN' as direction, r.label,i.inferred,r.dt_create, r.user_create, r.obj_type, r.obj_class
// 				FROM dsd.metadatavalue2 i
// 				JOIN dsd.item2 r ON (r.item_id = i.item_id)
// 				WHERE  i.ref_item = ? and i.level = 0 and i.ref_item is not null
// 				AND ( not i.inferred OR element in('inferred:ea:work:'))
// 		)
// 		SELECT * FROM RELATIONS order by direction desc, obj_type, ref_item;
// 		";

// 		$stmt = $dbh->prepare ( $SQL );
// 		$stmt->bindParam ( 1, $item_id );
// 		$stmt->bindParam ( 2, $item_id );
// 		$stmt->execute ();
// 		//$relations = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 		$relations = array();
// 		while($rec= $stmt->fetch(PDO::FETCH_ASSOC)){
// 			Log::info(print_r($rec,true));
// 			$other_user = $rec['user_create'];
// 			$other_ts = (new DateTime($rec['dt_create']))->getTimestamp();
// 			$temporal_status = 'OLD';
// 			if ($other_user == $create_user && abs($create_ts - $other_ts) < 30){
// 				$temporal_status = 'NEW';
// 			}
// 			$rec['temporal_status'] = $temporal_status;
// 			$relations[] = $rec;
// 		}


		$relations = PDao::getRelations($item_id);
		if (! empty($submit_id)){
			foreach ($relations as &$rec){
					$other_user = $rec['user_create'];
					$other_ts = (new DateTime($rec['dt_create']))->getTimestamp();
					$temporal_status = 'OLD';
					if ($other_user == $create_user && abs($create_ts - $other_ts) < 30){
						$temporal_status = 'NEW';
					}
					$rec['temporal_status'] = $temporal_status;
			}
		}

		$bitstreams = PDao::getBitstreams($item_id);

		#################################################################################################################
		$status = null;
		$basic = PDao::getItemBasic($item_id);
		$status = $basic['status'];

		if (! empty($_POST['error'])) {
			$idata->setValueSK('ea:status:', 'error');
			$is = new ItemSave();
			$is->setIdata($idata);
			$is->setItemId($item_id);
			$item_id = $is->save_item();
			$idata = PDao::get_item_metadata($item_id);
		}
		#################################################################################################################

		//$idata = Pdao::getItemMetadata($item_id);
		//$obj_class = $item['obj_class'];
		//$title = $item['label'];


		//DRYLL
		$project = Config::get('arc.PROJECT_NAME');
		$is_periodic = false;
		$submit_periodic_send_email = false;
		$is_book = false;
		$submit_book_send_email = false;
		$is_issue = false;
		$submit_issue_send_email = false;

		if ($project == 'dryl'){

			if ($basic['obj_type'] == 'periodic'){
				$is_periodic = true;
				if (!empty($_POST['periodic_send_email'])) {
					$prm_secretary_email = Config::get('arc.PRIMARY_SECRETARY_EMAIL');
					$item_label = $basic['label'];
					Mail::send('emails.dryl.secretary_periodic', array('label' => $item_label), function($message) use ($prm_secretary_email,$item_label)
					{
						$message->to($prm_secretary_email, tr('email_to_secretary'));
						$message->subject(tr('email_secretary_periodic_subject').$item_label);
					});
					$submit_periodic_send_email = true;
				}
			}

			if ($basic['obj_type'] == 'auth-manifestation'){
				if (!empty($basic['flags_json'])){
					$flags = json_decode($basic['flags_json'], true);
					if (in_array('IS:book',$flags)){
						$is_book = true;
						if (!empty($_POST['book_send_email'])) {
							$prm_secretary_email = Config::get('arc.PRIMARY_SECRETARY_EMAIL');
							$scd_secretary_email = Config::get('arc.SECONDARY_SECRETARY_EMAIL', null);
							$item_label = $basic['label'];
							Mail::send('emails.dryl.secretary_book', array('label' => $item_label), function($message) use ($prm_secretary_email,$scd_secretary_email,$item_label)
							{
								$message->to($prm_secretary_email, tr('email_to_secretary'));
								if(!empty($scd_secretary_email)){
									$message->cc($scd_secretary_email, tr('email_to_lawyers'));
								}
								$message->subject(tr('email_secretary_book_subject').$item_label);
							});
							$submit_book_send_email = true;
						}
					}elseif(in_array('IS:issue',$flags)){
						$is_issue = true;
						if (!empty($_POST['issue_send_email'])) {
							$item_label = $basic['label'];
							$itemRelationsFrom = PDao::getItemRelationsFrom($item_id);
							$emails = array();
							if(!empty($itemRelationsFrom)){
								foreach ($itemRelationsFrom as  $key => $relitem){
									if ($relitem['obj_type'] == 'periodic'){
										$jdata = json_decode($relitem['jdata'], true);
										if(!empty($jdata['opac2']['lawyers_emails'])){
											$lawyers_emails = $jdata['opac2']['lawyers_emails'];
											foreach ($lawyers_emails as  $email){
												$emails[]=$email;
											}
										}
									}
								}
							}
							if(!empty($emails)){
								$prm_secretary_email = Config::get('arc.PRIMARY_SECRETARY_EMAIL');
								Mail::send('emails.dryl.secretary_issue', array('label' => $item_label), function($message) use ($prm_secretary_email,$emails,$item_label)
								{
									foreach ($emails as  $email){
										$message->to($email, tr('related_lawyer'));
									}
									$message->cc($prm_secretary_email, tr('email_to_secretary'));
									$message->subject(tr('email_secretary_issue_subject').$item_label);
								});
								$submit_issue_send_email = true;
							}
						}
					}
				}
			}

		}
		//**


		$data = array(
				'item' => $item,
				'item_id'=>$item_id,
				//'itemData' => $idata,
				'relations'=>$relations,
				'bitstreams'=>$bitstreams,
				//'fm'=>get_get('fm'),
       'submit_status' => PUtil::getSubmitStatusByItem($item_id),
       'submits_pending' => PUtil::getSubmitsPending(),
       'status' => $status,
       'is_periodic' => $is_periodic, //DRYLL
       'periodic_notification' => $submit_periodic_send_email, //DRYLL
       'is_book' => $is_book, //DRYLL
       'book_notification' => $submit_book_send_email, //DRYLL
       'is_issue' => $is_issue, //DRYLL
       'issue_notification' => $submit_issue_send_email, //DRYLL

		);

		Log::info("<<<<<<<<<<<<<<<<<< EDIT_ITEM_STEP3: " . $item_id);
//		Log::info("TEMPLATE: " . 	ArcApp::template());
		return $this->show($data);
	}


}