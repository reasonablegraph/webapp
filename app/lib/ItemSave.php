<?php

use Illuminate\Support\Facades\Log;


class ItemSave {

	/**
	* @property ItemMetadataAccess $idata
	*/
	private $idata;
	private $wfdata;
	private $submit_id;
	private $item_id;
	private $edoc;
	private $vivliografiki_anafora = false;
	private $agg_type;
	private $cd;
	private $item_collection;
	private $thumb1;
	private $userName;
	private $rlock;

	private $rule_context = null;
	private $messages = array();

	private function addMessage($message){
		$this->messages[] = $message;
	}

	public  function getMessages(){
		return $this->messages;
	}

	/**
	 *
	 * @param ItemMetadata $idata
	 */
	public function setIdata($idata) {
		$this->idata = $idata;
	}
	function setSubmitId($submit_id) {
		$this->submit_id = $submit_id;
	}
	public function setEdoc($edoc) {
		$this->edoc = $edoc;
	}

	public function setUserName($userName){
		$this->userName = $userName;
	}

	public function setItemId($itemId){
		$this->item_id = $itemId;
	}
	/**
	 *
	 * @param array $wfdata
	 */
	public function setWfdata($wfdata) {
		$this->wfdata = $wfdata;
		if (isset($wfdata['vivliografiki_anafora'])){
			$this->vivliografiki_anafora = $wfdata['vivliografiki_anafora'];
		}
		if (isset($wfdata['cd'])){
			$this->agg_type = $wfdata['cd'];
		}
		if (isset($wfdata['cd'])){
			$this->cd = $wfdata['cd'];
		}
		if (isset($wfdata['item_collection'])){
			$this->item_collection = $wfdata['item_collection'];
		}
		if (isset($wfdata['thumb1'])){
			$this->thumb1 = $wfdata['thumb1'];
		};
	}

	/**
	 * @param GRuleEngineLock $rlock
	 */
	public function setRlock($rlock) {
		$this->rlock = $rlock;
	}



	public function getRuleContext(){
		return $this->rule_context;
	}

	//$rule_engine = function($g0,$item_id, $item_refs) {
	public static function rule_engine($g0, $item_id, $item_refs, $subitemFlag = false, $rlock = null, $submit_id = null) {
		Log::info('@:: RULE ENGINE: ' . $item_id . ' SUBITEM: ' . ($subitemFlag ? 'true' : 'false')  .  (empty($item_refs) ? '' : (' refs: ' . implode(',', $item_refs) )));

		$debugFlag = Config::get('arc_rules.DEBUG',false);
		if ($debugFlag){
			GGraphUtil::dumpDOT($g0,array('file'=>'/tmp/g0.dot','label'=>'g0','neighbourhoodFlag'=>false,'inferredFlag'=>true));
			Log::info('@@: g0: REMOVE ITEM RELATIONS EDGES : ' . $item_id );
		}
		GGraphUtil::removeRelationEdges($g0,$item_id);



		$rules = Config::get('arc_rules.DEFAULT_ITEM_RULES',array());
		$rule_mem =  Config::get('arc_rules.INIT_MEMORY',array());
		$rule_mem['LOAD_ITEM_ID']=$item_id;
		$rule_mem['LOAD_ITEM_REFS']=$item_refs;
		$rule_mem['RLOCK'] = $rlock;
		$rule_mem['SUBMIT_ID'] = $submit_id;

		$g2 = new GGraphO();
		$re = new GRuleEngine($rules, $rule_mem,$g2,array('old'=>$g0));
		$context = $re->execute(array('subitemFlag'=>$subitemFlag));

//		if (false){
//			$dmsg = $context->getDebugMessages();
//			$this->addMessage("<pre>");
//			$this->addMessage("\n===========================");
//			$this->addMessage("DEBUG MESSAGES:");
//			$this->addMessage("===========================");
//			foreach ($dmsg as $msg){
//				$this->addMessage("$msg");
//			}
//			$this->addMessage("</pre>");
//		}
		return $context;
	}


	public function update_item() {//&$out
		//$debug = (Config::get('arc.DEBUG_RELATIONS',0) > 0);

		$dbh = dbconnect();
		$idata = $this->idata;

		$item_id = $this->item_id;

		try {
			Log::info("@@: UPDATE_ITEM");
			$dbh->beginTransaction();


			//Log::info("@@: SKIP delete_relation_values");
			//$SQL = 'DELETE FROM dsd.metadatavalue2  WHERE ref_item = ? AND relation = 1 AND not inferred';
			//PDAO::delete_relation_values($item_id);



			$this->addMessage ("#basic metadata");
			// update basic medatada
			$out = "";
			PUtil::save_basic_metadata($dbh, $item_id, $idata, $out);
			$this->addMessage ($out);

			//$delete_count = PDao::remove_unused_item_relations($item_id);
			//$this->addMessage ("#remove unused relations: $delete_count");

// 			$this->addMessage("#bibref: (" . $this->vivliografiki_anafora . ")");
// 			PDao::update_bibref($item_id, $this->vivliografiki_anafora);

			// echo("#update fst\n");
			// update_fst($dbh, $item_id);

			//$this->addMessage ("#touch");
			//PDao::touch_item($item_id);

			PDao::update_item2_access($item_id, $this->userName);

			$data = serialize($idata->values);
			$wfdata_text = serialize($this->wfdata);
			PDao::item_add_history($item_id, $this->userName, $data, $wfdata_text);

			$dbh->commit();
			Log::info("@@: UPDATE_ITEM FINISH");
		} catch ( PDOException $e ) {
			$dbh->rollback();
			$error = $e->getMessage();
			echo ("ERROR ITEM NOT SAVED: $error\n");
			error_log($error, 0);
			exit();
		}
	}


	public function update_item_batch_simple() {
		$dbh = dbconnect();
		$idata = $this->idata;
		$item_id = $this->item_id;

		try {
			Log::info("@@: UPDATE_ITEM");
			$dbh->beginTransaction();
			$this->addMessage ("#basic metadata");

			// update basic medatada
			PUtil::save_basic_metadata_batch_simple($dbh, $item_id, $idata);
			PDao::update_item2_access($item_id, $this->userName);

			$data = serialize($idata->values);
			$wfdata_text = serialize($this->wfdata);
			PDao::item_add_history($item_id, $this->userName, $data, $wfdata_text);

			$dbh->commit();
			Log::info("@@: UPDATE_ITEM FINISH");
		} catch ( PDOException $e ) {
			$dbh->rollback();
			$error = $e->getMessage();
			echo ("ERROR ITEM NOT SAVED: $error\n");
			error_log($error, 0);
			exit();
		}
	}


	public function insert_new_item_batch_simple($title_element = 'dc:title:', $item_id = null) {//&$out
		$dbh = dbconnect();
		$idata = $this->idata;
		$obj_type = $idata->getFirstItemValue('ea:obj-type:',null)->textValue();
		$rep = PDao::insert_item_batch_simple($this->userName, $obj_type, $idata->getFirstItemValue($title_element,null)->textValue());
		$item_id = $rep[0];
		$this->item_id = $item_id;
		PUtil::save_basic_metadata_batch_simple($dbh, $item_id, $idata);
		PDao::insert_generated_metadata($dbh, $item_id, $this->userName);
		return $item_id;
	}


	public function insert_new_item_without_edoc() {//&$out

		$dbh = dbconnect();
		$idata = $this->idata;

		//$item_id = $this->item_id;


		try {

			$thumb_uuid = null;
			$dbh->beginTransaction();
			//$obj_type = $idata->getValueText('ea', 'obj-type');
			$obj_type = $idata->getFirstItemValue('ea:obj-type:',null)->textValue();
//			error_log("### OBJTYPE1: " . $obj_type);
			$item_id = PDao::insert_item($this->userName, $obj_type);
			$this->item_id = $item_id;
			// $item_id = insert_item($dbh,$this->userName);

			$this->addMessage("2.NEW ITEM: " . $item_id );

			if (empty($item_id)) {
				echo ("ERROR NEW item_id ");
				$dbh->rollback();
				return;
			}

			// echo("<PRE>");
			// print_r($idata->values);
			// echo("</PRE>");

			$this->addMessage("#collection handling");
			// collection handling
			$collection_id = PDao::insert_collection($dbh, $item_id, $obj_type);

			$this->addMessage("#basic metadata");
			// update basic medatada
			$out = "";
			PUtil::save_basic_metadata($dbh, $item_id, $idata, $out);
			$this->addMessage($out);

			$this->addMessage("#generated metadata");
			// generated_metadata
			PDao::insert_generated_metadata($dbh, $item_id, $this->userName);

			$delete_count = PDao::remove_unused_item_relations($item_id);
			$this->addMessage("#remove unused relations: $delete_count");

			$this->addMessage("#bibref: ($this->vivliografiki_anafora)");
			PDao::update_bibref($item_id, $this->vivliografiki_anafora);

			// echo("#update fst\n");
			// update_fst($dbh, $item_id);
			$thumb1 = $this->thumb1;
			if (! empty($thumb1) && $thumb1 != 'undefined') {
				$this->addMessage("thumb: $thumb1");
				list ( $upload_file_name, $upload_file_path, $upload_file_ext ) = get_file_from_url($thumb1, "bnet_", true);
				// echo("$upload_file_name\n");
				// echo("$upload_file_path\n");
				// echo("$upload_file_ext\n");

				$bundle_name_original = "INTERNAL";
				$bundle_id_original = insert_bundle($dbh, $bundle_name_original);
				insert_item2bundle($dbh, $item_id, $bundle_id_original);

				$thumb_uuid = get_safe_uiid($dbh);
				$thumb_bitstream_id = create_bitstream($dbh, $thumb_uuid, $upload_file_path, $upload_file_name, 1, SpoolUtil::getOKSpoolDir());
				$this->addMessage("create thumb bitstream: $thumb_bitstream_id , $thumb_uuid , (1) , $upload_file_name ");
				insert_bundle2bitstream($dbh, $bundle_id_original, $thumb_bitstream_id);
			}

			// printf("#item change site to: %s\n",ARCHIVE_SITE);
			// PDao::change_item_site($item_id,ARCHIVE_SITE );

			//$this->addMessage("#touch");
			// touch_item($dbh, $item_id);
		//	PDao::touch_item($item_id);

			$dbh->commit();

			if (! empty($thumb_uuid)) {
				$this->addMessage("generate thumb");
				$out = "";
				thumbs_generate_from_bitstream($dbh, $thumb_uuid, $out, false, true);
				$this->addMessage($out);
			}

			return $item_id;

		} catch ( PDOException $e ) {
			$dbh->rollback();
			$error = $e->getMessage();
			echo ("ERROR ITEM NOT CREATED: $error\n");
			error_log($error, 0);
			exit();
		}

	}





	// $insert_new_item = function($userName, $submit_id, $idata, $vivliografiki_anafora, $edoc, $wfdata) use($dbh, &$out) {
	public function insert_new_item_with_Edoc() { //&$out
		$dbh = dbconnect();

		$idata = $this->idata;
		$item_id = $this->item_id;


		try {

			// $dbh->beginTransaction();
			$obj_type = $idata->getValueText('ea', 'obj-type');
			$item_id = PDao::insert_item($this->userName, $obj_type);
			$this->item_id = $item_id;
			// $item_id = insert_item($dbh, $this->userName);
			$this->addMessage("1.NEW ITEM: " . $item_id);
			if (empty($item_id)) {
				$dbh->rollback();
				echo ("ERROR NEW item_id ");
				return;
			}

			$data = serialize($idata->values);
			$wfdata_text = serialize($this->wfdata);
			PDao::item_add_history($item_id, $this->userName, $data, $wfdata_text);

			// error_log("@@1");
			// echo("<PRE>");
			// print_r($idata->values);
			// echo("</PRE>");
			// error_log("@@2");
			// // return;

			$this->addMessage("#obj_type collection handling");
			// collection handling

			$collection_id = PDao::insert_collection($dbh, $item_id, $obj_type);

			$this->addMessage ("#basic metadata");
			// update basic medatada
			$out = "";
			PUtil::save_basic_metadata($dbh, $item_id, $idata, $out);
			$this->addMessage($out);

			$this->addMessage ("#generated metadata");
			// generated_metadata
			PDao::insert_generated_metadata($dbh, $item_id, $this->userName);

			$delete_count = PDao::remove_unused_item_relations($item_id);
			$this->addMessage ("#remove unused relations: $delete_count");

			$this->addMessage ("#bibref: ($this->vivliografiki_anafora)");
			PDao::update_bibref($item_id, $this->vivliografiki_anafora);

			$this->addMessage ("#bitstream handling");
			$out = "";
			$bitstream_id = PUtil::submit_from_spool($dbh, $this->edoc, $item_id, $out);
			$this->addMessage($out);

			// echo("#update fst\n");
			// update_fst($dbh, $item_id);

			//$this->addMessage ("#touch");
			//PDao::touch_item($item_id);

			// $dbh->commit();

			$this->addMessage ("#item basic works");
			$out = "";
			PDao::item_basic_works($dbh, $item_id, $bitstream_id, $out);
			$this->addMessage ($out);

			// printf("#item change site to: %s\n",ARCHIVE_SITE);
			// PDao::change_item_site($item_id,ARCHIVE_SITE );

			return $item_id;
		} catch ( PDOException $e ) {
			$error = $e->getMessage();
			$this->addMessage ("ERROR ITEM NOT CREATED: $error");
			error_log($error, 0);
			// try {$dbh->rollback();}catch(PDOException $e){ error_log( $e->getMessage(), 0); }
			exit();
		}
	}





	public function mass_import(){

		$dbh = dbconnect();
		$idata = $this->idata;

		$cd = $this->cd;

		//MAZIKO IMPORT
		$this->addMessage("cd= $cd");
		$this->addMessage("$item_collection");

		$initTitle = $idata->getValueTextSK('dc:title:');
		$i=0;
		//$this->addMessage("\n");
		$base_dir = SPOOL_dir_pending_base;
		$directory = $base_dir . $cd;
		$iterator = new DirectoryIterator($directory);
		foreach ($iterator as $fileinfo) {
			if ($fileinfo->isFile()) {
				$i++;
				$filename = $fileinfo->getFilename();
				$edoc = $cd . "/" . $filename;
				$this->addMessage("TRY TO CREATE $edoc");
				$title = sprintf('%s (%s)',$initTitle ,$i);
				$idata->setValueSK('dc:title:',$title);
				//$item_id = $insert_new_item($this->userName, $this->submit_id, $idata, $this->vivliografiki_anafora, $edoc, $this->wfdata);
				$item_id = $this->insert_new_item_with_Edoc();
				$this->addMessage("ITEM: $item_id CREATED");
				if (! empty($item_collection)){
					$errors = insert_relation_items($item_id ,$item_collection ,DB_ITEM_RELATION_TYPE_COLLECTION_MEMBER);
					foreach ($errors as $k=>$v){
						$this->addMessage("collection add error: $v");
					}
				}
			}
		}
		return $item_id;

	}


// 	public function save_item(){

// 		$dbh = dbconnect();
// 		try {
// 			$dbh->beginTransaction();

// 			_save_item();

// 			$dbh->commit();
// 			$dbh = null;
// 		} catch (PDOException $e){
// 			$dbh->rollback();
// 			$error = $e->getMessage();
// 			echo("error : $error\n");
// 			error_log( $error, 0);
// 		}

// 	}


	public function save_item($subitemFlag=false){
		Log::info("@:: ItemSave::save_item");
		$async_workers_enable = Config::get('arc.async_workers_enable', 0);
		$crud_lock = Config::get('arc.CRUD_LOCK', 0);

		/* @var $idata ItemMetadataAccess  */
		$idata = $this->idata;
		//$idata->dumpLaravelLog();
		//$options = $idata->getFirstItemValue('trn:options:');
		//return $this->item_id;
		$idata = PUtil::changeRelation($idata,2);


		if (! empty($this->cd)){
			Log::info("MASS IMPORT");
			//MAZIKO IMPORT
			return $this->mass_import();
		}

		if (!empty($this->item_id)){
			$item_id = $this->item_id;
			Log::info("OLD ITEM: $item_id");
			Log::info("@:: LOAD INIT GRAPH (1)  UPDATE OLD RECORD");
			//$g0 = GGraphIO::loadNodeSubGraph($item_id);
			$g0 = GGraphIO::loadNodeNeighbourhood($item_id,null,null,'update'); //UPDATE OLD RECORD
			////////////////////////////////////
			/// OLD ITEM
			////////////////////////////////////
			//("item: " .  $this->item_id);
			$this->update_item();

			$edgesiv = $idata->getEdgeItemValues();
			$ref_items = array();
			foreach ($edgesiv as $eiv){
				//Log::info("NEW REF_ITEM : " . $eiv->refItem());
				$ref_items[]  = $eiv->refItem();
			}
			//$g2 = GGraphIO::loadNodeSubGraph($item_id);

			// early submits release during development, in case rule engine faults
			if(!$crud_lock){
				PDao::update_submits_status($this->submit_id, $item_id, SubmitsStatus::$finished);
			}

			$this->rule_context = ItemSave::rule_engine($g0, $item_id, $ref_items, $subitemFlag, $this->rlock, $this->submit_id);

			if (!$async_workers_enable) {
				PDao::update_submits_status($this->submit_id, $item_id, SubmitsStatus::$finished);
			}

			return $this->item_id;
		}


		Log::info("# NEW ITEM ");
		$edgesiv = $idata->getEdgeItemValues();

		$ref_items = array();
		$has_relation = false;
		foreach ($edgesiv as $eiv){
			//Log::info("NEW REF_ITEM : " . $eiv->refItem());
			$ref_items[]  = $eiv->refItem();
		}
		if (!empty($ref_items)){
// 			Log::info("REF ITEMS: " . print_r($ref_items,true));
 			$has_relation = true;
 			Log::info("@:: LOAD INIT GRAPH (2) CREATE NEW WITH REFS");
			//$g0 = GGraphIO::loadNodeSubGraph(null, $ref_items);
			$g0 = GGraphIO::loadNodeNeighbourhood(null, $ref_items,null,'create'); //CREATE NEW WITH REFS
		}


		if (! empty($this->edoc)){
			Log::info("NEW ITEM WITH EDOC");
			////////////////////////////////////
			/// NEW ITEM WITH EDOC
			////////////////////////////////////
			$this->addMessage("edoc: $this->edoc");
			$item_id =  $this->insert_new_item_with_Edoc();
		} else {
			Log::info("NEW ITEM WITHOUT EDOC");
			////////////////////////////////////
			/// NEW ITEM WITHOUT EDOC
			////////////////////////////////////
			$item_id = $this->insert_new_item_without_edoc();
		}

		if (!$has_relation){
			Log::info("@:: LOAD INIT GRAPH (3) CREATE NEW NO REFS");
			//$g0 = GGraphIO::loadNodeSubGraph($item_id);
			$g0 = GGraphIO::loadNodeNeighbourhood($item_id,null,null,'create');
		}

		// early update of final_item_id, useful for edit blocking of newly created item
		PDao::update_submits_final_item_id($this->submit_id, $item_id);

		// early submits release during development, in case rule engine faults
		if(!$crud_lock){
			PDao::update_submits_status($this->submit_id, $item_id, SubmitsStatus::$finished);
		}

		$this->rule_context = ItemSave::rule_engine($g0, $item_id, $ref_items, $subitemFlag, $this->rlock, $this->submit_id); //CREATE NEW NO REFS

		if (!$async_workers_enable) {
			PDao::update_submits_status($this->submit_id, $item_id, SubmitsStatus::$finished);
		}

		return $item_id;

	}


	static function delete_item($item_id) {

		$item = PDao::getItemDBRecord($item_id);
		// print_r($item);
		$status = $item['status'];
		$label = $item['label'];
		$obj_class = $item['obj_class'];

// 		if ($status != 'error' && $obj_class != 'artifact') {
// 			echo ("ERROR status: $status </br>");
// 			echo (tr("for deletion must be status: error"));
// 			return;
// 		}

		$dbh = dbconnect();
		//$g0 = GGraphIO::loadNodeSubGraph($item_id);
		Log::info("@:: LOAD INIT GRAPH (4) DELETE OLD RECORD");
		$g0 = GGraphIO::loadNodeNeighbourhood($item_id,null,null,'delete'); //DELETE OLD RECORD

		$vs = $g0->getVertices();
		$ref_items = array();
		foreach ($vs as $v){
			$ref_items[] =$v->persistenceId();
		}

		$artifact_parent_item = null;
		if ($obj_class == 'artifact') {
			$SQL = "select  item_id from dsd.artifacts where item_impl = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item_id);
			$stmt->execute();
			if (! $r = $stmt->fetch()) {
				echo ("ERROR 2");
				return;
			}
			$artifact_parent_item = $r[0];
		}

		$out = "<p>try to delete item $item_id  $label</p>";

		$SQL = "SELECT dsd.delete_item(?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		if ($r = $stmt->fetch()) {
			$out .= "<p>item deleted</p>";
		}

		if (! empty($artifact_parent_item)) {

			$out .= sprintf('<p><a href="/prepo/artifacts?i=%s">[artifacts list]</a></p>', $artifact_parent_item);
		} else {
			$out .= '<p><a href="/archive/search">[main search]</a></p>';
			$out .= '<p><a href="/archive/recent?s=error">[errors list]</a></p>';
		}

		ItemSave::rule_engine($g0,$item_id,$ref_items);

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

		return $out;
	}

}


