<?php
class PDao {

	/**
	 *
	 * @param unknown $item_id
	 */
	public static function getRelations($item_id){
		$dbh = dbconnect();
		$SQL="
		WITH RELATIONS AS (
				SELECT i.item_id, i.element,  i.ref_item , 'OUT' as direction, r.label,i.inferred, r.dt_create, r.user_create, r.obj_type, r.obj_class
				FROM dsd.metadatavalue2 i
				JOIN dsd.item2 r ON (r.item_id = i.ref_item)
				WHERE  i.item_id = ? and i.ref_item is not null AND not i.inferred
				UNION
				SELECT i.ref_item as item_id,  i.element,  i.item_id as ref_item, 'IN' as direction, r.label,i.inferred,r.dt_create, r.user_create, r.obj_type, r.obj_class
				FROM dsd.metadatavalue2 i
				JOIN dsd.item2 r ON (r.item_id = i.item_id)
				WHERE  i.ref_item = ? and i.ref_item is not null
				AND ( not i.inferred)
		)
		SELECT * FROM RELATIONS order by direction desc, obj_type, ref_item;
		";
		//and i.level = 0

		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->bindParam ( 2, $item_id );
		$stmt->execute ();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	/**
	 *
	 * @param unknown $item_id
	 */
	public static function getAllRelations($item_id){
		$dbh = dbconnect();
		$SQL="
		WITH RELATIONS AS (
				SELECT i.item_id, i.element,  i.ref_item , 'OUT' as direction, r.label,i.inferred, r.dt_create, r.user_create, r.obj_type, r.obj_class, i.lid
				FROM dsd.metadatavalue2 i
				JOIN dsd.item2 r ON (r.item_id = i.ref_item)
				WHERE  i.item_id = ? and i.ref_item is not null
				UNION
				SELECT i.ref_item as item_id,  i.element,  i.item_id as ref_item, 'IN' as direction, r.label,i.inferred,r.dt_create, r.user_create, r.obj_type, r.obj_class, i.lid
				FROM dsd.metadatavalue2 i
				JOIN dsd.item2 r ON (r.item_id = i.item_id)
				WHERE  i.ref_item = ? and i.ref_item is not null
		)
		SELECT * FROM RELATIONS order by inferred, direction desc,  obj_type, ref_item;
		";
		//and i.level = 0

		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->bindParam ( 2, $item_id );
		$stmt->execute ();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 *
	 * @param unknown $item_id
	 */
	public static function getInferredRelations($item_id){
		$dbh = dbconnect();
		$SQL="
		WITH RELATIONS AS (
				SELECT i.item_id, i.element,  i.ref_item , 'OUT' as direction, r.label,i.inferred, r.dt_create, r.user_create, r.obj_type, r.obj_class
				FROM dsd.metadatavalue2 i
				JOIN dsd.item2 r ON (r.item_id = i.ref_item)
				WHERE  i.item_id = ? and i.ref_item is not null AND  i.inferred
				UNION
				SELECT i.ref_item as item_id,  i.element,  i.item_id as ref_item, 'IN' as direction, r.label,i.inferred,r.dt_create, r.user_create, r.obj_type, r.obj_class
				FROM dsd.metadatavalue2 i
				JOIN dsd.item2 r ON (r.item_id = i.item_id)
				WHERE  i.ref_item = ? and i.ref_item is not null
				AND (  i.inferred)
		)
		SELECT * FROM RELATIONS order by direction desc, obj_type, ref_item;
		";
		//and i.level = 0

		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->bindParam ( 2, $item_id );
		$stmt->execute ();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function remove_unused_item_relations($item_id) {
// 		$dbh = dbconnect ();
// 		if (empty ( $item_id )) {
// 			throw new Exception ( 'item_id missing' );
// 		}

// 		$SQL = "DELETE from dsd.item_relation where item_1 = ? AND id not in (select relation from dsd.metadatavalue2 where relation is not null)";
// 		$stmt = $dbh->prepare ( $SQL );
// 		$stmt->bindParam ( 1, $item_id );
// 		$stmt->execute ();
// 		$count = $stmt->rowCount ();
// 		return $count;
		return 0;
	}


	public static function insert_item($user_create, $obj_type, $uuid = null,$title= null) {
		// error_log("#1");
		$dbh = dbconnect ();
		if (empty ( $obj_type )) {
			throw new Exception ( 'obj_type missing' );
		}

		// $eperson_id = get_eperson_id($dbh,$user);
		$item_id = PDao::nextval ("dsd.item2_id_seq" );

		$obj_class = PDao::get_obj_class_from_obj_type ( $obj_type );
		if (empty ( $obj_class )) {
			throw new Exception ( 'obj_class not found for obj_type: ' + $obj_type );
		}


		if (empty($title)){
			$title=null;
			$label_data_type = PDO::PARAM_NULL;
		} else {
			$label_data_type = PDO::PARAM_STR;
		}

		$site = Config::get('arc.ARCHIVE_SITE');
		if (empty ( $uuid )) {
			$SQL = "INSERT INTO dsd.item2 (item_id,user_create,in_archive,obj_type,obj_class,site,title,label) values (?,?,true,?,?,?,?,?)";
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->bindParam ( 2, $user_create );
			$stmt->bindParam ( 3, $obj_type );
			$stmt->bindParam ( 4, $obj_class );
			$stmt->bindParam ( 5, $site );
			$stmt->bindParam ( 6, $title,$label_data_type);
			$stmt->bindParam ( 7, $title,$label_data_type);
			$stmt->execute ();
		} else {
			$SQL = "INSERT INTO dsd.item2 (item_id,user_create,in_archive,uuid,obj_type,obj_class,site,title,label) values (?,?,true,?,?,?,?,?,?)";
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->bindParam ( 2, $user_create );
			$stmt->bindParam ( 3, $uuid );
			$stmt->bindParam ( 4, $obj_type );
			$stmt->bindParam ( 5, $obj_class );
			$stmt->bindParam ( 6, $site );
			$stmt->bindParam ( 7, $title,$label_data_type);
			$stmt->bindParam ( 8, $title,$label_data_type);
			$stmt->execute ();
		}
		// error_log("#2: " . $item_id);
		return $item_id;
	}


	public static function insert_item_batch_simple($user_create, $obj_type, $title = null, $item_id=null) {
		$dbh = dbconnect ();
		if (empty ( $obj_type )) {
			throw new Exception ( 'obj_type missing' );
		}

		// $eperson_id = get_eperson_id($dbh,$user);
		if (empty($item_id)) {
			$item_id = PDao::nextval("dsd.item2_id_seq");
		}

		$obj_class = PDao::get_obj_class_from_obj_type ( $obj_type );
		if (empty ( $obj_class )) {
			throw new Exception ( 'obj_class not found for obj_type: ' + $obj_type );
		}

		$collection_id = PDao::get_collection_id($dbh,$obj_type);
		$collection_name = PDao::get_collection_name($dbh,$collection_id);
		//$obj_class = PDao::get_obj_class_from_obj_type($obj_type);
		//$SQL="UPDATE dsd.item2 SET collection = ?, collection_label = ?  WHERE item_id = ?";

		if (empty($title)){
			$title=null;
			$label_data_type = PDO::PARAM_NULL;
		} else {
			$label_data_type = PDO::PARAM_STR;
		}

		$site = Config::get('arc.ARCHIVE_SITE');
		$SQL = "INSERT INTO dsd.item2 (item_id,user_create,in_archive,obj_type,obj_class,site,title,label,collection,collection_label) values (?,?,true,?,?,?,?,?,?,?)";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->bindParam ( 2, $user_create );
		$stmt->bindParam ( 3, $obj_type );
		$stmt->bindParam ( 4, $obj_class );
		$stmt->bindParam ( 5, $site );
		$stmt->bindParam ( 6, $title,$label_data_type);
		$stmt->bindParam ( 7, $title,$label_data_type);
		$stmt->bindParam ( 8, $collection_id );
		$stmt->bindParam ( 9, $collection_name );
		$stmt->execute ();

		return array($item_id, $obj_type, $obj_class, $collection_id, $collection_name);
	}



	public static function get_obj_class_from_obj_type($type) {
		$dbh = dbconnect ();
		$SQL = "SELECT obj_class from dsd.obj_type  WHERE name=?";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $type );
		$stmt->execute ();
		if ($r = $stmt->fetch ()) {
			return $r [0];
		}
		return null;
	}
	public static function find_bitstream_simlinks($id) {
		$dbh = dbconnect ();
		$SQL = "SELECT symlink_id, bitstream_id, bb_create_dt,symlink, bb_weight, bundle_id, bundle, item_id, item_title
				FROM  dsd.bitstream_symlinks WHERE bitstream_id = ?  ";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $id );
		$stmt->execute ();
		$rep = $stmt->fetchAll ();
		return $rep;
	}
	public static function find_content_simlinks($id) {
		$dbh = dbconnect ();
		$SQL = "SELECT symlink_id, content_id, bb_create_dt,symlink, bb_weight, bundle_id, bundle, item_id, item_title
				FROM  dsd.content_symlinks WHERE content_id = ?  ";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $id );
		$stmt->execute ();
		$rep = $stmt->fetchAll ();
		return $rep;
	}
	public static function getContent($id) {
		$dbh = dbconnect ();
		$SQL = "SELECT  content_id, item_id,weight,title, description, publish_dt,create_dt, visibility, bundle_id, bundle_name, content_type, content, item, drupal_node,
				node_path, drupal_node, content_summary, publish_user, size_bytes, promote_fp, content_full, bitstream_desc, download_filename, content_html
				FROM  dsd.content_v1 WHERE content_id = ? ";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $id );
		$stmt->execute ();
		if (! $row = $stmt->fetch ()) {
			return null;
		}
		return $row;
	}
	public static function getLatestContentVersion($id) {
		$dbh = dbconnect ();
		$SQL = "SELECT  id, content_src_type, content_src ,archive_user,create_dt, status
				FROM public.content_version WHERE content_id = ?  AND status = 2";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $id );
		$stmt->execute ();
		if (! $row = $stmt->fetch ()) {
			return ARRAY ();
		}
		return $row;
	}

	/**
	 *
	 * @param ARRAY $key
	 *        	ARRAY('id'=>"") OR ARRAY('uuid'=>'');
	 * @return ItemMetadata
	 */
	public static final function get_artifact_by($key) {
		$id = null;
		$SQL = 'SELECT data, uuid, id,item_id from dsd.artifacts WHERE ';
		if (isset ( $key ['id'] )) {
			$SQL .= " id = ? ";
			$id = $key ['id'];
		} elseif (isset ( $key ['uuid'] )) {
			$SQL .= " uuid = ? ";
			$id = $key ['uuid'];
		} else {
			throw new Exception ( "get_artifact_by: params expected" );
		}

		$dbh = dbconnect ();
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $id );
		$stmt->execute ();
		if (! $row = $stmt->fetch ()) {
			return null;
		}
		$json = $row [0];
		$uuid = $row [1];
		$id = $row [2];
		$item_id = $row [3];
		$rep = ItemMetadata::fromJson ( $json );

		$rep->setValueSK ( DataFields::trn_item_id, $item_id );
		$rep->setValueSK ( DataFields::trn_id, $id );
		$rep->setValueSK ( DataFields::trn_uuid, $uuid );
		return $rep;
	}
	public static final function change_item_site($item_id, $site) {
		$dbh = dbconnect ();

		$SQL = "update dsd.item2 SET site= ? WHERE item_id = ?";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $site );
		$stmt->bindParam ( 2, $item_id );
		$stmt->execute ();

		return null;
	}
	public static final function touch_item($item_id) {
		$dbh = dbconnect ();
		$SQL = "SELECT dsd.touch_item(?)";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();

		return null;
	}
	public static final function dav_list_folder($folder_id) {
		$dbh = dbconnect ();
		$mkey = 'dav_folder_' . $folder_id;
		$rep = dmemcache_get ( $mkey );
		if (! $rep && ! is_array ( $rep )) {
			error_log ( print_r ( $rep, true ) );
			error_log ( "FETCH FROM DB: $folder_id" );
			if ($folder_id == 0) {
				$sql = "SELECT i.label, i.thumb, i.item_id, i.obj_type
						FROM dsd.item2 i
						WHERE i.obj_type = 'silogi' AND status <> 'error'";
				$stmt = $dbh->prepare ( $sql );
			} else {
				$sql = "SELECT i.label, i.thumb, i.item_id, i.obj_type
						FROM dsd.item2 i
						JOIN dsd.item_relation r ON (r.item_1 = i.item_id)
						WHERE r.item_2 = ? AND rel_type=2 AND status <> 'error'";
				$stmt = $dbh->prepare ( $sql );
				$stmt->bindParam ( 1, $folder_id );
			}
			$stmt->execute ();
			$rep = $stmt->fetchAll ();
			dmemcache_set ( $mkey, $rep );
		}
		return $rep;
	}

// /**
//  *
//  * @param unknown $item_id
//  * @param FuzzyDate $fuzzyDate
//  */
// 	public static final function update_item_date_ranges($item_id,$fuzzyDate){
// 		if (empty($fuzzyDate)){
// 			return;
// 		}
// 		$dbh = dbconnect ();

// 		$year1 = $fuzzyDate->y1;
// 		$year2 = $fuzzyDate->y2;
// 		$date_text_vaule = $fuzzyDate->yearsRange;

// 		$SQL = "SELECT 1 from dsd.year_ranges WHERE item_id = ?";
// 		$stmt = $dbh->prepare ( $SQL );
// 		$stmt->bindParam ( 1, $item_id );
// 		$stmt->execute ();
// 		if ($stmt->fetch ()) {
// 			$SQL = 'UPDATE dsd.year_ranges SET y1=?, y2=?, text_value=? WHERE item_id = ?';
// 		} else {
// 			$SQL = 'INSERT INTO dsd.year_ranges (y1, y2,text_value,item_id) values (?,?,?,?)';
// 		}
// 		$stmt = $dbh->prepare ( $SQL );
// 		$stmt->bindParam ( 1, $year1 );
// 		$stmt->bindParam ( 2, $year2 );
// 		$stmt->bindParam ( 3, $date_text_value );
// 		$stmt->bindParam ( 4, $item_id );
// 		$stmt->execute ();


// 	}

	/**
	 *
	 * @param ItemMetadata $idata
	 */
	public static final function update_item2($item_id, $idata) {
		//$dbh = dbconnect ();
		$trees = $idata->getTreesByKeyLink ( 'ea:publication:statement', null );
		//echo("<PRE>");

		//$obj_type = $idata->getTextValue ( 'ea:obj-type:' );
		$tmp = $idata->getFirstItemValue('ea:obj-type:',null);
		if (empty($tmp)){
			Log::info('ERROR: '.$item_id . ' has not object-type');
			throw new Exception('ERROR: '.$item_id . ' has not object-type');
		}
		$obj_type =  $tmp->textValue();

		//error_log("### OBJTYPE2: " . $obj_type);

		$year = null;
		$date_text_value = null;
		$fd = null;
		$year1 = null;
		$year2 = null;
		$year_str = null;
		$date_str = null;
		$json_date = null;
		foreach ( $trees as $tree ) {
			//echo ("============================================\n");
			// print_r($tree);
			foreach ( $tree as $v ) {
				if ($date_str == null && $v ['key'] == 'ea:date:orgissued' && ! empty ( $v [0] )) {
					// print_r($v);
					// print_r($v[0]);
					// echo("\n");
					$date_str = $v [0];
					//$date_text_value = $date_str;
					if (isset ( $v [5]['json'] )) {
						$json_date = $v[5]['json'];
					}
					// break;
				}
			}
		}

		if (PUtil::isEmpty ( $date_str )) {
			$vals = $idata->getItemValuesByKey ( "ea:date:orgissued" );
			if (isset ( $vals [0] )) {
				$vd = $vals [0]->valueArray ();
				if (isset ( $vd [5] )) {
					$date_text_value = $vd [0];
					$json_date = $vd [5] ['json'];

				}
			}
		}

		if (! empty ( $date_str )) {
			preg_match ( '/^(\d+)/', $date_str, $matches );
			if ($matches && isset ( $matches [0] )) {
				$year = $matches [0];
			}
		}

		if (! empty ( $json_date )) {
			$fd = new FuzzyDate ( $json_date ['y'], $json_date ['m'], $json_date ['d'] );
			$year = $fd->y1;
			$year1 = $year;
			$year2 = $fd->y2;
			if (empty($fd->date)){
				throw new Exception("DATE FORMAT ERROR: " . json_encode($json_date));
			}
			$date_str = $fd->date->format('Y-m-d');
			$date_text_value = $fd->yearsRange;
		} else {
			$fd = new FuzzyDate ( $year,null,null );
		}


		$issue_int = null;
		$issue_label = null;
		$issue_no = null;
		if ($obj_type == 'periodiko-tefxos' || $obj_type == 'efimerida-tefxos') {
			$issue_no = null;
			$issue_label = null;
			$ts = $idata->getTreesByKey ( 'ea:issue:' );
			if (! empty ( $ts )) {
				foreach ( $ts [0] as $v ) {
					if ($v ['key'] == 'ea:issue:no') {
						$issue_no = $v [0];
					}
					if ($v ['key'] == 'ea:issue:label') {
						$issue_label = $v [0];
					}
				}
			}
		}

		if (! Putil::isEmpty ( $issue_no )) {
			$issue_int = Putil::extract_int ( $issue_no );
		}
		if ($issue_int == null) {
			$issue_int = Putil::extract_int ( $issue_label );
		}
		if (Putil::isEmpty ( $issue_label )) {
			$issue_label = $issue_no;
		}

//		echo("</PRE>");
		$SQL = 'update dsd.item2 set year=?, issue_ts=dsd.normalize_fuzzy_ts(?) , issue_no=?, issue_label=? WHERE item_id=?';
		$ps = prepareService();
		$stmt = $ps->prepare ( $SQL );
		//$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $year );
		$stmt->bindParam ( 2, $date_str );
		$stmt->bindParam ( 3, $issue_int );
		$stmt->bindParam ( 4, $issue_label );
		$stmt->bindParam ( 5, $item_id );
		$stmt->execute ();

// 		if (! empty ( $fd )) {
// 			PDAO::update_item_date_ranges($item_id,$fd);
// 		}

	}

	/**
	 *
	 * @param ItemMetadata $idata
	 */
	public static final function save_artifact($idata) {
		$dbh = dbconnect ();

		// $uuid = $idata->getValueTextSK(DataFields::trn_uuid);
		// $id = PUtil::extract_int($idata->getValueTextSK(DataFields::trn_id));
		// $item_id = PUtil::extract_int($idata->getValueTextSK(DataFields::trn_item_id));
		// $id = PUtil::extract_int($idata->getValueTextSK('ea:artifact-id:'));
		$uuid = $idata->getValueTextSK ( 'ea:artifact:uuid' );
		$item_id = PUtil::extract_int ( $idata->getValueTextSK ( 'ea:artifact-of:' ) );
		if (empty ( $item_id )) {
			throw new Exception ( "item_id missing canot save artifact" );
		}
		$ref_item = PUtil::extract_int ( $idata->getValueTextSK ( 'ea:ref-item:id' ) );
		if (empty ( $ref_item )) {
			throw new Exception ( "ref_item missing canot save artifact" );
		}

		$obj_type = $idata->getValueTextSK ( DataFields::ea_obj_type );

		$sn = $idata->getValueTextSK ( DataFields::ea_sn );
		$sn_prefix = $idata->getValueTextSK ( DataFields::ea_sn_prefix );
		$sn_suffix = PUtil::extract_int ( $idata->getValueTextSK ( DataFields::ea_sn_suffix ) );

		$call_num_main = null;
		$call_num_ddc = null;
		$call_num = $idata->getValueTextSK ( DataFields::ea_call_number_ea );
		if ($obj_type == 'artifact1') {
			$call_num_prefix = $idata->getValueTextSK ( 'ea:call_number:part-a' );
			$call_num_ddc = $idata->getValueTextSK ( 'ea:call_number:ddc' );
			$call_num_suffix = $idata->getValueTextSK ( 'ea:call_number:part-c' );
		} else {
			$call_num_prefix = $idata->getValueTextSK ( DataFields::ea_call_number_prefix );
			$call_num_main = PUtil::extract_int ( $idata->getValueTextSK ( DataFields::ea_call_number_main ) );
			$call_num_suffix = $idata->getValueTextSK ( DataFields::ea_call_number_suffix );
		}

		$status = $idata->getValueTextSK ( DataFields::ea_status );

		// if (!PUtil::chk_int($sn_prefix)){
		// $sn_prefix = null;
		// }
		if (! PUtil::chk_int ( $sn_suffix )) {
			$sn_suffix = null;
		}
		if (! PUtil::chk_int ( $call_num_main )) {
			$call_num_main = null;
		}

		$id = null;
		if (! empty ( $uuid )) {
			$id = null;
			$SQL = "SELECT id FROM dsd.artifacts WHERE uuid = ? AND item_id = ? ";
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $uuid );
			$stmt->bindParam ( 2, $item_id );
			$stmt->execute ();
			if ($row = $stmt->fetch ()) {
				$id = $row [0];
			}
		}

		// $json = $idata->toJson();
		$json = '';
		if (! empty ( $id )) {
			$SQL = "UPDATE dsd.artifacts SET
					data = ?, sn = ?, sn_pref= ?, sn_suff = ?,
					call_number = ?, call_number_pref = ?, call_number_sn = ?, call_number_suff = ?,
					status = ?, call_number_ddc = ?
					WHERE id = ?";
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $json );
			$stmt->bindParam ( 2, $sn );
			$stmt->bindParam ( 3, $sn_prefix );
			$stmt->bindParam ( 4, $sn_suffix );
			$stmt->bindParam ( 5, $call_num );
			$stmt->bindParam ( 6, $call_num_prefix );
			$stmt->bindParam ( 7, $call_num_main );
			$stmt->bindParam ( 8, $call_num_suffix );
			$stmt->bindParam ( 9, $status );
			$stmt->bindParam ( 10, $call_num_ddc );
			$stmt->bindParam ( 11, $id );
			$stmt->execute ();
		} else {
			$id = PDao::nextval ( 'dsd.artifacts_id_seq' );
			$SQL = 'INSERT INTO dsd.artifacts ' . ' (data, sn, sn_pref, sn_suff, call_number, call_number_pref, call_number_sn, call_number_suff, status, item_id, id, uuid, item_impl,call_number_ddc) ' . ' values (?,?,?,?,?,?,?,?,?,?,?,?,?,?); ';
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $json );
			$stmt->bindParam ( 2, $sn );
			$stmt->bindParam ( 3, $sn_prefix );
			$stmt->bindParam ( 4, $sn_suffix );
			$stmt->bindParam ( 5, $call_num );
			$stmt->bindParam ( 6, $call_num_prefix );
			$stmt->bindParam ( 7, $call_num_main );
			$stmt->bindParam ( 8, $call_num_suffix );
			$stmt->bindParam ( 9, $status );
			$stmt->bindParam ( 10, $item_id );
			$stmt->bindParam ( 11, $id );
			$stmt->bindParam ( 12, $uuid );
			$stmt->bindParam ( 13, $ref_item );
			$stmt->bindParam ( 14, $call_num_ddc );
			$stmt->execute ();
		}

		return $id;
	}
	public static final function create_content($content_ctype, $ntitle, $bundle_name, $visibility, $parent_item_id) {
		if (empty ( $content_ctype ) || PUtil::isEmpty ( $ntitle ) || PUtil::isEmpty ( $bundle_name ) || PUtil::isEmpty ( $visibility )) {
			throw new Exception ( 'content_ctype || title || bundle || visibility name missing ' );
		}

		$dbh = dbconnect ();
		try {
			$dbh->beginTransaction ();

			$content_id = PUtil::nextval ( 'public.content_id_seq' );
			$SQL = "INSERT INTO public.content (id, description,content_type,visibility,publish_user) VALUES (?,?,?,?,?)";
			$stmt = $dbh->prepare ( $SQL );

			$stmt->bindParam ( 1, $content_id );
			$stmt->bindParam ( 2, $ntitle );
			$stmt->bindParam ( 3, $content_ctype );
			$stmt->bindParam ( 4, $visibility );
			$stmt->bindParam ( 5, $user_name );
			$stmt->execute ();

			$bundle_id = null;
			if (! empty ( $parent_item_id )) {
				$bundle_id = PDao::get_bundle_id ( $parent_item_id, $bundle_name );
			}
			if (empty ( $bundle_id )) {
				$bundle_id = PDao::insert_bundle ( $dbh, $bundle_name );
				PDao::insert_item2bundle ( $dbh, $parent_item_id, $bundle_id );
			}

			PDao::insert_bundle2content ( $bundle_id, $content_id );
			$dbh->commit ();

			$URL = sprintf ( '/prepo/edit_content?cid=%s', $content_id );
			//drupal_add_http_header ( 'Location', $URL );
			$response = Response::make('', 301);
			$response->header('Location', $URL);
			return $response;

		} catch ( PDOException $e ) {
			$dbh->rollback ();
			$error = $e->getMessage ();
			echo ("Can not insert: $error\n");
			error_log ( $error, 0 );
			return;
		}
	}
	public static function getItemLabel($item_id) {
		$id = null;
		$SQL = "SELECT label FROM dsd.item2 WHERE item_id = ? ";
		$dbh = dbconnect ();
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		if ($row = $stmt->fetch ()) {
			$rep = $row [0];
			return $rep;
		}
		return null;
	}


	public static function getItemLabelOT($item_id) {
		$id = null;
		$SQL = "SELECT label,obj_type FROM dsd.item2 WHERE item_id = ?";
		$dbh = dbconnect ();
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		if ($row = $stmt->fetch (PDO::FETCH_ASSOC )) {
			return $row;
		}
		return null;
	}

	public static function getItemBasic($item_id) {
		$dbh = dbconnect ();
		$mentainer_mode = user_access_mentainer () ? true : false;

		$SQL = 'SELECT item_id,uuid,title,label,archive_date,status,in_archive,dt_create,dt_update,obj_type,obj_class,site,folders,incoming,user_update,user_create,lang,bibref,issue_aggr,jdata::text, flags_json::text, fts, fts2, prop_fts  from dsd.item2 ';
		preg_match ( '/^\d+$/', $item_id, $matches );
		if ($matches) {
			$SQL .= ' WHERE item_id = ? ';
		} else {
			$SQL .= ' WHERE uuid = ? ';
		}
		//if (! user_access ( Config::get('arc.PERMISSION_VIEW_ITEMS_ALL_STATUS') )) {
		if (!ArcApp::has_permission(Permissions::$VIEW_ITEMS_ALL_STATUS )){
			$SQL .= sprintf ( " AND status  in('%s','%s','%s','%s')  ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_HIDDEN'), Config::get('arc.ITEM_STATUS_INTERNAL'),'direct_only');
		}
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}

	public static function getItemDBRecord($item_id) {
		if (empty($item_id)){
			throw  new Exception("getItemDBRecord empty item_id");
		}
		$dbh = dbconnect ();
		$mentainer_mode = user_access_mentainer () ? true : false;
		$SQL = 'SELECT *  FROM dsd.item2  ';
		preg_match ( '/^\d+$/', $item_id, $matches );
		if ($matches) {
			$SQL .= ' WHERE item_id = ? ';
		} else {
			$SQL .= ' WHERE uuid = ? ';
		}
		//if (! user_access ( Config::get('arc.PERMISSION_VIEW_ITEMS_ALL_STATUS'))) {
		if (!ArcApp::has_permission(Permissions::$VIEW_ITEMS_ALL_STATUS )){
			$SQL .= sprintf ( " AND status  in('%s','%s','%s','%s')  ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_HIDDEN'), Config::get('arc.ITEM_STATUS_INTERNAL'),'direct_only'  );
		}
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	public static function getArtifactDBRecordByItemImpl($item_id) {
		$dbh = dbconnect ();
		$SQL = 'SELECT *  FROM dsd.artifacts WHERE item_impl = ? ';
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		return $stmt->fetch ();
	}



	public static function getItem($item_id) {
		Log::info("getItem");
		$dbh = dbconnect ();

	  $mentainer_mode = user_access_mentainer () ? true : false;

		$collection_id = null;
		$collection_name = null;
		$bibref = false;
		// $SQL = "SELECT c.collection_id, c.name from public.collection2item ci join public.collection c ON (c.collection_id = ci.collection_id) where ci.item_id = ?";

		$SQL = 'SELECT  i.collection, i.collection_label , i.year, i.rand, i.thumb, i.label, i.obj_type, i.bibref, i.thumb1, i.thumb2, i.data, i.status,i.uuid, i.item_id, i.site, i.incoming,  i.user_create  FROM dsd.item2 i ';

		preg_match ( '/^\d+$/', $item_id, $matches );
		if ($matches) {
			$SQL .= ' WHERE item_id = ? ';
		} else {
			$SQL .= ' WHERE uuid = ? ';
		}

		//if (! user_access ( Config::get('arc.PERMISSION_VIEW_ITEMS_ALL_STATUS') )) {
		if (!ArcApp::has_permission(Permissions::$VIEW_ITEMS_ALL_STATUS )){
			$SQL .= sprintf ( " AND status  in('%s','%s','%s','%s')  ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_HIDDEN'), Config::get('arc.ITEM_STATUS_INTERNAL'),'direct_only' );
		}

		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$stmt->bindColumn ( 1, $collection_id );
		$stmt->bindColumn ( 2, $collection_name );
		$stmt->bindColumn ( 3, $year );
		$stmt->bindColumn ( 4, $rand );
		$stmt->bindColumn ( 5, $thumb );
		$stmt->bindColumn ( 6, $label );
		$stmt->bindColumn ( 7, $obj_type );
		$stmt->bindColumn ( 8, $bibref );
		$stmt->bindColumn ( 9, $thumb1 );
		$stmt->bindColumn ( 10, $thumb2 );
		$stmt->bindColumn ( 11, $data_txt );
		$stmt->bindColumn ( 12, $status );
		$stmt->bindColumn ( 13, $uuid );
		$stmt->bindColumn ( 14, $item_id );
		$stmt->bindColumn ( 15, $site );
		$stmt->bindColumn ( 16, $incoming );
		$stmt->bindColumn ( 17, $user_create );

		if (! $stmt->fetch ()) {
			return null;
		}
		$in_folder = ! $incoming;

		$data = ArrayToXML::toArray ( $data_txt );
		// print_r($data);

		$SQL = "SELECT file,idx,idxf,ttype from dsd.thumbs where item_id = ? ORDER BY idx";
// 		$SQL = "SELECT t.file,t.idx,t.idxf,t.ttype from dsd.thumbs t LEFT JOIN public.bitstream b ON (b.bitstream_id = t.bitstream) where t.item_id = ? ORDER BY t.idx";


		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();

		$thumbs_small = array ();
		$thumbs_big = array ();
		while ( $r = $stmt->fetch () ) {
			$file = $r [0];
			$idxf = $r [2];
			$ttype = $r [3];

			if ($ttype == 1) {
				$thumbs_small [$idxf] = $file;
			} else if ($ttype == 2) {
				$thumbs_big [$idxf] = $file;
			}
		}

		$rep = array ();
		// SELECT mv1.item_id, r.metadata_schema_id, r.metadata_field_id, r.element, r.qualifier, mv1.text_value AS search_value, mv1.text_value, mv1.text_lang, mv1.metadata_value_id
		// FROM dsd.metadatavalue2 mv1
		// JOIN metadatafieldregistry r ON mv1.metadata_field_id = r.metadata_field_id;

		// from dsd.item_metadata_all m

		// prefix, element, qualifier, text_value, text_lang, metadata_value_id, relation, ref_item, data, lid, weight,link
		// $SQL = "SELECT m.metadata_field_id, m.text_value, d.element, m.ref_item
		// from dsd.metadatavalue2 m
		// join dsd.metadatafieldregistry_view d ON (d.mfid = m.metadata_field_id)
		// where item_id = ? order by m.metadata_value_id";

		// FIXME: ORDER
		$SQL = "SELECT element, text_value, text_lang, metadata_value_id, relation, ref_item, data, lid, weight,link, inferred
		FROM dsd.metadatavalue2 m  WHERE item_id = ? ORDER BY weight";

		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );

		$stmt->execute ();

		$keywords = array ();
		$pkeywords = array ();
		// $authors = null;
		$pplace = null;
		$size = null;
		$date = null;
		$type = null;
		$title = null;
		$desc_abstract = null;
		$desc_sponsor = null;
		$desc_desc = null;
		$date_archive = null;
		$pages = null;
		$page_size = null;
		$url_related = array ();
		$url_origin = null;
		$issue_of = null;
		$item_of = array ();
		$item_of_label = array ();
		$issue_of_label = null;
		$date_captured = null;
		$website_url = null;
		$website_url_base = null;
		$publisher = null;
		$issn = null;
		$isbn = null;
		$subtitle = null;
		$title_uniform = array ();
		$date_available = null;
		$lang_code = null;
		$authors = array ();
		$editors = array ();
		$iloustrators = array ();
		$contributors_other = array ();
		$translators = array ();
		$epimelites = array ();
		$ipef8inoi = array ();
		$ref_bitstream = null;
		$ref_content = null;

		$idata = new ItemMetadata ();

		// #debug:1# echo("<pre>");
		while ( $r = $stmt->fetch () ) {
			// print_r($r);
			// m.metadata_field_id, m.text_value, d.element, m.ref_item
			// $mid = $r[0];
			$value = $r ['text_value'];
			$el = $r ['element'];
			$ref_item = $r ['ref_item'];

			// element, text_value, text_lang, metadata_value_id, relation, ref_item, data, lid, weight,link
			// 0 1 2 3 4 5 6 7 8 9
			// key, text_value, text_lang, metadata_value_id, relation, ref_item, data, staff, weight, grp
			$idata->addValueFromDBTextSK ( $r [0], $r [1], $r [2], $r [3], $r [4], $r [5], $r [6], $r [7], $r [8], $r [9],$r [9] );

			// #debug:1# echo("$el : $value\n");

			// authors
			// f ($mid == 3){

			if ($el == 'dc:contributor:author') {
				$authors [] = array (
						$value,
						$ref_item
				);
			}
			if ($el == 'dc:contributor:editor') {
				$editors [] = array (
						$value,
						$ref_item
				);
			}
			if ($el == 'dc:contributor:illustrator') {
				$iloustrators [] = array (
						$value,
						$ref_item
				);
			}
			if ($el == 'dc:contributor:other') {
				$contributors_other [] = array (
						$value,
						$ref_item
				);
			}
			if ($el == 'ea:contributor:translator') {
				$translators [] = array (
						$value,
						$ref_item
				);
			}
			if ($el == 'ea:contributor:editor') {
				$epimelites [] = array (
						$value,
						$ref_item
				);
			}
			if ($el == 'ea:contributor:responsible') {
				$ipef8inoi [] = array (
						$value,
						$ref_item
				);
			}

			// keywords
			// lse if ($mid == 57) {
			else if ($el == 'dc:subject:') {
				$ok = true;
				foreach ( $keywords as $k ) {
					if (strpos ( $k, $value ) !== false) {
						$ok = false;
						break;
					}
				}
				if ($ok) {
					$keywords [] = $value;
				}
			} else if ($el == 'ea:subject:') {
				$pkeywords [] = $value;
			} else if ($el == 'ea:bitstream:id') {
				$ref_bitstream = $value;
			} else if ($el == 'ea:content:id') {
				$ref_content = $value;
			} 			// title
			// lse if ($mid == 64) {
			else if ($el == 'dc:title:') {
				$title = $value;
			} 			// ea-obj-type
			// lse if ($mid == 101) {
			else if ($el == 'ea:obj-type:') {
				$type = $value;
			} 			// date
			// lse if ($mid == 72) {
			else if ($el == 'ea:date:orgissued') {

				$date = $value;
			} 			// size
			// lse if ($mid == 74) {
			else if ($el == 'ea:size:') {
				$size = $value;
			} 			// publication place
			// lse if ($mid == 75) {
			else if ($el == 'ea:publication:place') {
				$pplace = $value;
			} 			// desc general
			// lse if ($mid == 26) {
			else if ($el == 'dc:description:') {
				$desc_desc = $value;
			} 			// desc abstract
			// lse if ($mid == 27) {
			else if ($el == 'dc:description:abstract') {
				$desc_abstract = $value;
			} 			// desc sponsor
			// lse if ($mid == 29) {
			else if ($el == 'dc:description:sponsorship') {
				$desc_sponsor = $value;
			} 			// date arxio
			// lse if ($mid == 15) {
			else if ($el == 'dc:date:issued') {
				$date_archive = $value;
			} 			// ari8mos selidon
			// lse if ($mid == 86) {
			else if ($el == 'ea:edoc:Pages') {
				$pages = $value;
			} 			// page_size apo to pdf
			// lse if ($mid == 88) {
			else if ($el == 'ea:edoc:Page-size') {
				$page_size = $value;
			} 			// url-related
			// lse if ($mid == 98) {
			else if ($el == 'ea:url:related') {
				$url_related [] = $value;
			} 			// url-origin
			// lse if ($mid == 99) {
			else if ($el == 'ea:url:origin') {
				$url_origin = $value;
			} else if ($el == 'ea:issue-of:') {
				if (empty ( $ref_item )) {
					$issue_of = $value;
				} else {
					$issue_of = $ref_item;
				}
			} else if ($el == 'ea:item-of:') {
				$item_of [] = $value;
			} else if ($el == 'ea:date:captured') {
				$ts = null;
				try {
					$ts = new DateTime ( $value );
				} catch ( Exception $e ) {
				}
				$date_captured = $ts;
			} else if ($el == 'ea:website:url') {
				$website_url = $value;
			} else if ($el == 'ea:website:url-base') {
				$website_url_base = $value;
			} else if ($el == 'dc:publisher:') {
				$publisher = $value;
			} else if ($el == 'dc:identifier:isbn') {
				$isbn = $value;
			} else if ($el == 'dc:identifier:issn') {
				$issn = $value;
			} else if ($el == 'ea:subtitle:') {
				$subtitle = $value;
			} else if ($el == 'ea:title:uniform') {
				$title_uniform [] = $value;
			} else if ($el == 'dc:date:available') {
				$date_available = $value;
			} else if ($el == 'dc:language:iso') {
				$lang_code = $value;
			}
		} // END WhILE RS LOOP
		  // #debug:1# echo("</pre>");

		$rep ['id'] = $item_id;
		$rep ['title'] = $title;
		$rep ['subtitle'] = $subtitle;
		$rep ['label'] = $label;
		$rep ['collection_id'] = $collection_id;
		$rep ['collection_name'] = $collection_name;
		$rep ['bibref'] = empty ( $bibref ) ? 0 : 1;
		$rep ['site'] = $site;
		$rep ['in_folder'] = $in_folder;
		$rep ['date'] = $date;
		$rep ['size'] = $size;
		$rep ['place'] = $pplace;
		$rep ['desc_desc'] = $desc_desc;
		$rep ['desc_abstract'] = $desc_abstract;
		$rep ['desc_sponsor'] = $desc_sponsor;
		$rep ['date_archive'] = $date_archive;
		$rep ['year'] = $year;
		$rep ['rand'] = $rand;
		$rep ['type'] = $type; // bj-type
		$rep ['authors'] = $authors;
		$rep ['keywords'] = $keywords;
		$rep ['pkeywords'] = $pkeywords;
		$rep ['thumb'] = $thumb;
		$rep ['thumb1'] = $thumb1;
		$rep ['thumb2'] = $thumb2;
		$rep ['pages'] = $pages;
		$rep ['page_size'] = $page_size;
		$rep ['thumbs_small'] = $thumbs_small;
		$rep ['thumbs_big'] = $thumbs_big;
		$rep ['url_related'] = $url_related;
		$rep ['url_origin'] = $url_origin;
		$rep ['issue_of'] = $issue_of;
		// $rep['item_of'] = $item_of;
		$rep ['date_captured'] = $date_captured;
		$rep ['website_url'] = $website_url;
		$rep ['website_url_base'] = $website_url_base;
		$rep ['publisher'] = $publisher;
		$rep ['isbn'] = $isbn;
		$rep ['issn'] = $issn;
		$rep ['title_uniform'] = $title_uniform;
		$rep ['status'] = $status;
		$rep ['uuid'] = $uuid;
		$rep ['date_available'] = $date_available;
		$rep ['lang_code'] = $lang_code;
		$rep ['editors'] = $editors;
		$rep ['iloustrators'] = $iloustrators;
		$rep ['contributors_other'] = $contributors_other;
		$rep ['translators'] = $translators;
		$rep ['epimelites'] = $epimelites;
		$rep ['ipef8inoi'] = $ipef8inoi;
		$rep ['ref_content'] = $ref_content;
		$rep ['ref_bitstream'] = $ref_bitstream;
		$rep ['user_create'] =$user_create;

		$rep ['idata'] = $idata;

		if (! empty ( $issue_of )) {
			$SQL = "select label FROM dsd.item2 WHERE item_id = ?";
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $issue_of );
			$stmt->execute ();
			if ($r = $stmt->fetch ()) {
				$issue_of_label = $r [0];
			}
		}

		$rep ['issue_of_label'] = $issue_of_label;

		// $item_of_label = array();
		if (! empty ( $item_of )) {
			$SQL = null;
			$SQL = "
					SELECT  i.item_id, i.label
					FROM dsd.relation r
					JOIN dsd.item2 i ON (r.item_2 = i.item_id)
					WHERE  r.rel_type=2 AND r.item_1 = ?;
					";
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->execute ();
			$tmp = array ();
			while ( $r = $stmt->fetch () ) {
				$item = $r [0];
				$label = $r [1];
				$tmp [] = ARRAY (
						$item,
						$label
				);
			}
			$rep ['item_of'] = $tmp;
		}
		// print_r($item_of);
		// $rep['item_of_label'] = $item_of_label;
		// $rep['item_of'] = $item_of;

		$periodic = ($type == 'periodiko' || $type == 'efimerida' || $type == 'web-site');
		// website =($type == 'web-site-instance');

		if ($periodic) {
			$SQL = null;
			if ($type == 'web-site') {
				$SQL = "
						SELECT  t.item_id, t.file, i.issue_label
						FROM dsd.relation r
						JOIN dsd.thumbs t ON (r.item_1 = t.item_id)
						JOIN dsd.item2 i ON (r.item_1 = i.item_id)
						WHERE  t.idx=0  AND t.ttype=1 AND r.item_2 = ?
						ORDER BY i.issue_ts;
						";
			} else {
				$SQL = "
						SELECT  t.item_id, t.file, i.issue_label
						FROM dsd.relation r
						JOIN dsd.thumbs t ON (r.item_1 = t.item_id)
						JOIN dsd.item2 i ON (r.item_1 = i.item_id)
						WHERE  t.idx=0  AND t.ttype=1 AND r.item_2 = ?
						ORDER BY i.issue_no;
						";
			}

			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->execute ();

			$tefxi = array ();
			while ( $r = $stmt->fetch () ) {
				$item = $r [0];
				$file = $r [1];
				$issue = $r [2];
				$tefxi [] = ARRAY (
						$item,
						$file,
						$issue
				);
			}

			$rep ['e3ofila'] = $tefxi;
		}

		if ($type == 'work') {
			$SQL = sprintf ( "SELECT %s
					FROM dsd.item_relation_v2 r
					JOIN dsd.item2 i ON (r.item_1 = i.item_id)
					WHERE r.rel_type = 26 AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->execute ();
			$rep ['works_items_manifestation'] = $stmt->fetchAll ();
		} else {
			$rep ['works_items_manifestation'] = null;
		}

		if ($type == 'actor') {

			// SQL=sprintf("SELECT %s from dsd.item2 i JOIN dsd.metadatavalue2 v ON (v.item_id = i.item_id) where ref_item = ? AND i.status= '%s' order by i.dt_create desc",
			// TEM_LIST_SQL_FIELDS, ITEM_STATUS_FINISH);
			$SQL = sprintf ( "SELECT %s
					FROM dsd.relation r
					JOIN dsd.item2 i ON (r.item_1 = i.item_id)
					WHERE r.rel_type = 11 AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ",Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->execute ();
			$rep ['persons_items_author'] = $stmt->fetchAll ();

			$SQL = sprintf ( "SELECT distinct %s
							FROM dsd.relation r
							JOIN dsd.item2 i ON (r.item_1 = i.item_id)
							WHERE r.rel_type in(12,13,14,15,16,17,18,53) AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->execute ();
			$rep ['persons_items_contributor'] = $stmt->fetchAll ();
		} else {
			$rep ['persons_items_author'] = null;
			$rep ['persons_items_contributor'] = null;
		}

		if ($type == 'place') {

			// SQL=sprintf("SELECT %s from dsd.item2 i JOIN dsd.metadatavalue2 v ON (v.item_id = i.item_id) where ref_item = ? AND i.status= '%s' order by i.dt_create desc",
			// TEM_LIST_SQL_FIELDS, ITEM_STATUS_FINISH);
			$SQL = sprintf ( "SELECT distinct %s
							FROM dsd.item_relation_v2 r
							JOIN dsd.item2 i ON (r.item_1 = i.item_id)
							WHERE r.rel_type in(41,52) AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label limit 40 ",Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->execute ();
			$rep ['place_items'] = $stmt->fetchAll ();
		} else {
			$rep ['place_items'] = null;
		}

		if ($type == 'silogi') {

			if ($mentainer_mode) {
				$SQL = sprintf ( "SELECT %s
						FROM dsd.item_relation_v2 r
						JOIN dsd.item2 i ON (r.item_1 = i.item_id)
						WHERE r.item_2 = ? order by i.dt_create desc, i.label ",Config::get('arc.ITEM_LIST_SQL_FIELDS')  );
			} else {
				$SQL = sprintf ( "SELECT %s
						FROM dsd.item_relation_v2 r
						JOIN dsd.item2 i ON (r.item_1 = i.item_id)
						WHERE r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
			}

			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->execute ();

			$members = $stmt->fetchAll ();
			$rep ['members'] = $members;
		}

		$SQL = sprintf ( "SELECT %s
					FROM dsd.item_relation_v2 r
					JOIN dsd.item2 i ON (r.item_2 = i.item_id)
					WHERE r.rel_type = 26 AND r.item_1 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$rep ['works'] = $stmt->fetchAll ();

		// if ($type == 'recipe'){

		$SQL = sprintf ( "SELECT %s
					FROM dsd.item_relation_v2 r
					JOIN dsd.item2 i ON (r.item_1 = i.item_id)
					WHERE r.rel_type = 75 AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'),Config::get('arc.ITEM_STATUS_FINISH ') );
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$rep ['items_with_instrument'] = $stmt->fetchAll ();

		$SQL = sprintf ( "SELECT %s
					FROM dsd.item_relation_v2 r
					JOIN dsd.item2 i ON (r.item_1 = i.item_id)
					WHERE r.rel_type = 74 AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$rep ['items_with_symbol'] = $stmt->fetchAll ();

		$SQL = sprintf ( "SELECT %s
					FROM dsd.item_relation_v2 r
					JOIN dsd.item2 i ON (r.item_1 = i.item_id)
					WHERE r.rel_type = 73 AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH'));
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$rep ['items_with_process'] = $stmt->fetchAll ();

		$SQL = sprintf ( "SELECT %s
					FROM dsd.item_relation_v2 r
					JOIN dsd.item2 i ON (r.item_1 = i.item_id)
					WHERE r.rel_type = 70 AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$rep ['items_with_situation'] = $stmt->fetchAll ();

		$SQL = sprintf ( "SELECT %s
					FROM dsd.item_relation_v2 r
					JOIN dsd.item2 i ON (r.item_1 = i.item_id)
					WHERE r.rel_type = 69 AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH '));
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$rep ['items_with_product'] = $stmt->fetchAll ();

		$SQL = sprintf ( "SELECT %s
					FROM dsd.item_relation_v2 r
					JOIN dsd.item2 i ON (r.item_1 = i.item_id)
					WHERE r.rel_type = 68 AND r.item_2 = ? AND i.status = '%s' order by i.dt_create desc, i.label ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$rep ['items_with_ingredient'] = $stmt->fetchAll ();

		// }

		// RELATED ITEMS
		// $SQL=sprintf(
		// "SELECT %s
		// FROM dsd.item_relation_arc_det r
		// JOIN dsd.item2 i ON (r.item = i.item_id)
		// WHERE r.rel_type = 31 AND r.item_other = ? AND i.status = '%s' order by i.dt_create desc, i.label ",
		// ITEM_LIST_SQL_FIELDS,ITEM_STATUS_FINISH);
		// $stmt = $dbh->prepare($SQL);
		// $stmt->bindParam(1, $item_id);
		// $stmt->execute();
		// $rep['related_items'] = $stmt->fetchAll();

		// $SQL=sprintf(
		// "SELECT %s
		// FROM dsd.item2 i
		// JOIN dsd.metadatavalue2 m ON (m.item_id = i.item_id AND m.element = 'dc:subject:')
		// JOIN dsd.subject s ON (s.subject = m.text_value)
		// WHERE s.item = ? AND i.status = '%s' order by i.dt_create desc, i.label ",
		// ITEM_LIST_SQL_FIELDS,ITEM_STATUS_FINISH);
		// $stmt = $dbh->prepare($SQL);
		// $stmt->bindParam(1, $item_id);
		// $stmt->execute();
		// $rep['related_items'] = $stmt->fetchAll();

		$SQL = sprintf ( "
				SELECT distinct q.* FROM (
				SELECT %s
				FROM dsd.item_relation_arc_det r
				JOIN dsd.item2 i ON (r.item = i.item_id)
				WHERE r.rel_type = 31 AND r.item_other = ? AND i.status = '%s'
				UNION
				SELECT %s
				FROM dsd.item2 i
				JOIN dsd.metadatavalue2 m ON (m.item_id = i.item_id AND m.element = 'dc:subject:')
				JOIN dsd.subject s ON (s.subject = m.text_value)
				WHERE s.item = ? AND i.status = '%s'
		) as q ", Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_FINISH') );
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->bindParam ( 2, $item_id );
		$stmt->execute ();
		$rep ['related_items'] = $stmt->fetchAll ();

		$p_content = null;
		if ($type == DataFields::DB_OBJ_TYPE_ARTICLE && ! empty ( $ref_content )) {
			$p_content = PDao::getContent ( $ref_content );
		}
		$rep ['primary_content'] = $p_content;

		$SQL = 'SELECT id,sn,call_number,status FROM dsd.artifacts WHERE item_id = ?';
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();

		$artifacts = array ();
		while ( $r = $stmt->fetch () ) {
			$artifact ['artifact_id'] = $r ['id'];
			$artifact ['sn'] = $r ['sn'];
			$artifact ['call_number'] = $r ['call_number'];
			$artifact ['status'] = $r ['status'];
			$artifacts [] = $artifact;
		}
		$rep ['artifacts'] = $artifacts;

		// SQL = "SELECT name,size_bytes,internal_id from dsd.item_bitstream
		// HERE item_id = ? AND internal_id is not null AND sequence_id = 1 LIMIT 1 ";

		// ELECT b.bitstream_id, i.label,t.file, t.idx,t.idxf, t.ttype, t.extension
		// rom public.bitstream b left join dsd.item2 i on (i.item_id = b.item) join dsd.thumbs t ON (t.item_id = i.item_id);


		$SQL = "SELECT
				b.bitstream_id, b.name, b.size_bytes, b.internal_id, b.sequence_id, b.bundle_name, b.item,
				b.checksum, b.checksum_algorithm, b.description, b.mimetype, b.bundle_name, b.bitstream_id, b.download_fname,
				b.file_ext, b.artifact_id, b.furl, b.redirect_type, b.redirect_url, b.src_url, b.info, b.md5_org,
				i.label, t.file as thumb_file, t.idx, t.idxf, t.ttype, t.extension, bb_weight, symlink, symlink_id
				FROM dsd.item_bitstream_ext3 b
				LEFT JOIN   dsd.item2  i ON (i.item_id = b.item)
				LEFT JOIN dsd.thumbs t ON (t.item_id = i.item_id AND t.ttype = 3)
				WHERE b.item_id = ? AND b.internal_id is not null AND b.bundle_name in ('ORIGINAL','ALT','SAMPLE') ORDER BY  sequence_id limit 80";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		Log::info($SQL);
		$bitstreams = array ();
		while ( $r = $stmt->fetch () ) {
			$sequence_id = $r ['sequence_id'];
			if ($sequence_id == 1) {
				$rep ['bitstream_id'] = $r ['bitstream_id'];
				$rep ['fname'] = $r ['name']; // $fname;
				$rep ['fbytes'] = $r ['size_bytes']; // $fbytes;
				$rep ['internal_id'] = $r ['internal_id'];
				$rep ['fchecksum'] = $r ['checksum']; // $checksum;
				$rep ['fchecksum_algorithm'] = $r ['checksum_algorithm']; // $checksum_algorithm;
				$rep ['mimetype'] = $r ['mimetype']; // $mimetype;
				$rep ['bundle_name'] = $r ['bundle_name']; // $bandle_name;
					                                         // $rep['thumb_file'] = $r['thumb_file'];
					                                         // $rep['thumb_idx'] = $r['idx'];
					                                         // $rep['thumb_idxf'] = $r['idxf'];
					                                         // $rep['thumb_type'] = $r['ttype'];
					                                         // $rep['thumb_extension'] = $r['extension'];
			}
			$symlink = $r ['symlink'];
			if ($symlink) {
				$sequence_id = $r ['bb_weight'];
			}
			// bitstreams[$sequence_id] = array('name'=>$fname, 'bytes' =>$fbytes, 'bitstream_id'=>$bitstream_id,
			// bundle_name'=>$bundle_name, 'checksum'=>$checksum, 'checksum_algorithm'=>$checksum_algorithm, 'description'=>$description,
			// mimetype' => $mimetype, 'bundle_name' => $bundle_name );

			$name = $r ['download_fname'];
			if (empty ( $name )) {
				$name = $r ['name'];
				// $name = sprintf('%s_%s.%s',$item_id,$r['sequence_id'],$r['file_ext']);
			}

			$desc = $r ['description'];
			// f (!empty($desc)){
			// $desc = sprintf('%s (%s)',$desc,$r['file_ext']);
			//
			if (empty ( $desc )) {
				$desc = $name;
			}

			$bindex = $sequence_id;
			if (empty ( $bindex )) {
				$bindex = 0;
			}
			while ( isset ( $bitstreams [$bindex] ) ) {
				$bindex += 1;
			}
			$bitstreams [$bindex] = array (
					'bitstream_id' => $r ['bitstream_id'],
					'name' => $name,
					'size_bytes' => $r ['size_bytes'],
					'internal_id' => $r ['internal_id'],
					'bundle_name' => $r ['bundle_name'],
					'checksum' => $r ['checksum'],
					'checksum_algorithm' => $r ['checksum_algorithm'],
					'md5_org' => $r ['md5_org'],
					'description' => $desc,
					'mimetype' => $r ['mimetype'],
					'bundle_name' => $r ['bundle_name'],
					'artifact_id' => $r ['artifact_id'],
					'furl' => $r ['furl'],
					'redirect_type' => $r ['redirect_type'],
					'redirect_url' => $r ['redirect_url'],
					'src_url' => $r ['src_url'],
					'file_ext' => $r ['file_ext'],
					'info' => $r ['info'],
					'thumb_file' => $r ['thumb_file'],
					'thumb_idx' => $r ['idx'],
					'thumb_idxf' => $r ['idxf'],
					'thumb_type' => $r ['ttype'],
					'thumb_extension' => $r ['extension'],
					'symlink' => $r ['symlink'],
					'symlink_id' => $r ['symlink_id'],
					'download_fname' => $r ['download_fname'],
					'item' => $r ['item']
			);
		}

		ksort ( $bitstreams );
		$rep ['bitstreams'] = $bitstreams;


		$fields = 'title,content, item as citem, content_id, fweight, publish_dt, visibility, bundle_name, node_path, drupal_node, size_bytes';
		//if (user_access_admin ()) {
			$SQL = sprintf ( "SELECT %s FROM dsd.item_content_all  WHERE
					content_type = %s AND visibility <> %s AND item_id = ? AND bundle_name in ('ORIGINAL','ALT','PRIVATE') ORDER BY fweight", $fields, DataFields::DB_content_ctype_note, DataFields::DB_visibility_deleted );
		//} else {
		//	$SQL = sprintf ( "SELECT %s  FROM dsd.item_content_all  WHERE
		//			content_type = %s AND visibility = %s AND item_id = ? AND bundle_name in ('ORIGINAL','ALT') ORDER BY fweight", $fields, DataFields::DB_content_ctype_note, DataFields::DB_visibility_public );
		//}
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$r = $stmt->fetchAll ();
		$rep ['notes'] = $r;

		$fields = 'title, item as citem, content_id, fweight, publish_dt, visibility, bundle_name, node_path, drupal_node, size_bytes, content_type, description, bitstream_desc, download_filename';
		//if (user_access_admin ()) {
			$SQL = sprintf ( "SELECT %s FROM dsd.item_content_all  WHERE
					item_id = ? AND content_type = %s and visibility <> %s AND bundle_name in ('ORIGINAL','ALT','PRIVATE') ORDER BY fweight", $fields, DataFields::DB_content_ctype_article, DataFields::DB_visibility_deleted );
		//} else {
		//	$SQL = sprintf ( "SELECT %s  FROM dsd.item_content_all WHERE
		//			item_id = ? AND content_type = %s and visibility = %s   AND bundle_name in ('ORIGINAL','ALT') ORDER BY fweight ", $fields, DataFields::DB_content_ctype_article, DataFields::DB_visibility_public );
		//}
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$r = $stmt->fetchAll ();

		// if (! empty($p_content)){
		// $r[] = $p_content;
		// }
		$rep ['articles'] = $r;

		$rep ['primary_bitstream'] = null;
		if ($type == Config::get('arc.DB_OBJ_TYPE_BITSTREAM')) {
			$SQL = "SELECT
					bitstream_id, name, size_bytes,  description,  user_format_description ,internal_id,info,
					sequence_id, create_dt, item, file_ext, download_fname, src_url, redirect_url, redirect_type,
					artifact_id, furl, logging, pages, ctype, visibility, mimetype, bundle_id, bundle_name, bb_weight, bb_id,
					thumb_file, idx, idxf, ttype, extension as thumb_ext, false as symlink
					FROM dsd.bitstream_v1  WHERE item = ?";
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $item_id );
			$stmt->execute ();
			$r = $stmt->fetchAll ();

			if (!empty($r)) {
				$rep ['primary_bitstream'] = $r [0];
			}

		}

		// echo("<pre>");
		// print_r($rep);
		// echo("</pre>");

		return $rep;
	}



	public static final function getItemRelationsTo($item_id){
		$dbh = dbconnect ();
		$SQL = sprintf ( "SELECT
		i.collection_label, i.label as title, i.year,i.place, i.archive_date, i.item_id, i.collection,
		i.thumb, i.thumb1, i.thumb2,EXTRACT(EPOCH FROM i.dt_create), i.bibref,
		i.obj_type, i.issue_aggr,
		i.pages, i.issue_cnt, i.weight, i.folder, i.status,
		i.uuid, i.lang, i.user_create, i.user_update, i.dt_update, i.dt_create, i.folders,
		EXTRACT(EPOCH FROM  i.issue_ts) as issue_ts, i.issue_no, i.jdata::text,
		r.element as rel_type, r.text_value as rel_label, r.inferred
		FROM dsd.metadatavalue2 r
		JOIN dsd.item2 i ON (r.item_id = i.item_id)
		WHERE r.ref_item = ?  AND i.status = '%s'
		ORDER BY i.dt_create desc, i.label ", Config::get('arc.ITEM_STATUS_FINISH') );

// 		Log::info('TO');
// 		Log::info($SQL);
// 		Log::info($item_id);
		//order by i.dt_create desc, i.label
		$stmt = $dbh->prepare( $SQL );
		$stmt->bindParam( 1, $item_id );
		$stmt->execute();
		$rep = $stmt->fetchAll(PDO::FETCH_ASSOC);


		if (Config::get('arc.ITEM_LOAD_INFERENCE_FROM_JSON',1) >0){
			$SQL = sprintf ( "SELECT
			i.collection_label, i.label as title, i.year,i.place, i.archive_date, i.item_id, i.collection,
			i.thumb, i.thumb1, i.thumb2,EXTRACT(EPOCH FROM i.dt_create), i.bibref,
			i.obj_type, i.issue_aggr,
			i.pages, i.issue_cnt, i.weight, i.folder, i.status,
			i.uuid, i.lang, i.user_create, i.user_update, i.dt_update, i.dt_create, i.folders,
			EXTRACT(EPOCH FROM  i.issue_ts) as issue_ts, i.issue_no, i.jdata::text,
			r.element as rel_type, null as rel_label, true as inferred
			FROM (select (edge->>'from')::integer as ifrom , (edge->>'to')::integer as ito ,(edge->>'element')::varchar as element FROM (select json_array_elements(jdata->'edges_in') as edge from dsd.item2 WHERE item_id = ?) as foo) as r
			JOIN dsd.item2 i ON (r.ifrom = i.item_id)
			WHERE i.status = '%s'
			ORDER BY i.dt_create desc, i.label ", Config::get('arc.ITEM_STATUS_FINISH') );

			// 		Log::info('TO');
			// 		Log::info($SQL);
			// 		Log::info($item_id);
			//order by i.dt_create desc, i.label
			$stmt = $dbh->prepare( $SQL );
			$stmt->bindParam( 1, $item_id );
			$stmt->execute();
			$r2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$rep = array_merge($rep,$r2);
		}

		return $rep;

	}



	public static final function getItemRelationsFrom($item_id){
		$dbh = dbconnect ();
		$SQL = sprintf ( "SELECT
		i.collection_label, i.label as title, i.year,i.place, i.archive_date, i.item_id, i.collection,
		i.thumb, i.thumb1, i.thumb2,EXTRACT(EPOCH FROM i.dt_create), i.bibref,
		i.obj_type, i.issue_aggr,
		i.pages, i.issue_cnt, i.weight, i.folder, i.status,
		i.uuid, i.lang, i.user_create, i.user_update, i.dt_update, i.dt_create, i.folders,
		EXTRACT(EPOCH FROM  i.issue_ts) as issue_ts, i.issue_no, i.jdata::text,
		r.element as rel_type, r.text_value as rel_label, r.inferred
		FROM dsd.metadatavalue2 r
		JOIN dsd.item2 i ON (r.ref_item = i.item_id)
		WHERE r.item_id = ?  AND i.status = '%s'
		ORDER BY i.dt_create desc, i.label ", Config::get('arc.ITEM_STATUS_FINISH') );

// 		Log::info('FROM');
// 		Log::info($SQL);
// 		Log::info($item_id);

		$stmt = $dbh->prepare( $SQL );
		$stmt->bindParam( 1, $item_id );
		$stmt->execute();
		$rep =  $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (Config::get('arc.ITEM_LOAD_INFERENCE_FROM_JSON',1) >0){
			$SQL = sprintf ( "SELECT
			i.collection_label, i.label as title, i.year,i.place, i.archive_date, i.item_id, i.collection,
			i.thumb, i.thumb1, i.thumb2,EXTRACT(EPOCH FROM i.dt_create), i.bibref,
			i.obj_type, i.issue_aggr,
			i.pages, i.issue_cnt, i.weight, i.folder, i.status,
			i.uuid, i.lang, i.user_create, i.user_update, i.dt_update, i.dt_create, i.folders,
			EXTRACT(EPOCH FROM  i.issue_ts) as issue_ts, i.issue_no, i.jdata::text,
			r.element as rel_type, null as rel_label, true as inferred
			FROM (select (edge->>'from')::integer as ifrom , (edge->>'to')::integer as ito ,(edge->>'element')::varchar as element FROM (select json_array_elements(jdata->'edges_out') as edge from dsd.item2 WHERE item_id = ?) as foo) as r
			JOIN dsd.item2 i ON (r.ito = i.item_id)
			WHERE i.status = '%s'
			ORDER BY i.dt_create desc, i.label ", Config::get('arc.ITEM_STATUS_FINISH') );

			// 		Log::info('FROM');
			// 		Log::info($SQL);
			// 		Log::info($item_id);

			$stmt = $dbh->prepare( $SQL );
			$stmt->bindParam( 1, $item_id );
			$stmt->execute();
			$r2 =  $stmt->fetchAll(PDO::FETCH_ASSOC);


			$rep = array_merge($rep,$r2);
		}

		return $rep;
	}



	public static final function getItemArticles($item_id){
		$dbh = dbconnect ();

		$fields = 'title, item as citem, content_id, fweight, publish_dt, visibility, bundle_name, node_path, drupal_node, size_bytes, content_type, description, bitstream_desc, download_filename';
		if (user_access_admin ()) {
			$SQL = sprintf ( "SELECT %s FROM dsd.item_content_all  WHERE
					item_id = ? AND content_type = %s and visibility <> %s AND bundle_name in ('ORIGINAL','ALT','PRIVATE') ORDER BY fweight", $fields, DataFields::DB_content_ctype_article, DataFields::DB_visibility_deleted );
		} else {
			$SQL = sprintf ( "SELECT %s  FROM dsd.item_content_all WHERE
					item_id = ? AND content_type = %s and visibility = %s   AND bundle_name in ('ORIGINAL','ALT') ORDER BY fweight ", $fields, DataFields::DB_content_ctype_article, DataFields::DB_visibility_public );
		}
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $r;
	}


	public static final function getItemBitstreams($item_id,$obj_type =null) {
		$dbh = dbconnect ();
		Log::info("getItemBitstreams: " . $item_id);

		if (empty($obj_type) || $obj_type != 'digital-item'){
			$view = sprintf('dsd.item_bitstream_ext3');
		} else {
			$view = sprintf('dsd.item_bitstream_ext4');
		}
			$SQL = sprintf("SELECT
					b.bitstream_id, b.name, b.size_bytes, b.internal_id, b.sequence_id, b.bundle_name, b.item,
					b.checksum, b.checksum_algorithm, b.description, b.mimetype, b.bundle_name, b.bitstream_id, b.download_fname, b.jdata,
					b.file_ext, b.artifact_id, b.furl, b.redirect_type, b.redirect_url, b.src_url, b.info, b.md5_org,
					i.label, t.file as thumb_file, t.idx, t.idxf, t.ttype, t.extension, bb_weight, symlink, symlink_id
					FROM %s b
					LEFT JOIN   dsd.item2  i ON (i.item_id = b.item)
					LEFT JOIN dsd.thumbs t ON (t.item_id = i.item_id AND t.ttype = 3)
					WHERE b.item_id = ? AND b.internal_id is not null AND b.bundle_name in ('ORIGINAL','ALT','SAMPLE') ORDER BY  sequence_id limit 80", $view);
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();

		$bitstreams = array ();
		while ( $r = $stmt->fetch () ) {
			$sequence_id = $r ['sequence_id'];

			$symlink = $r ['symlink'];
			if ($symlink) {
				$sequence_id = $r ['bb_weight'];
			}

			$name = $r ['download_fname'];
			if (empty ( $name )) {
				$name = $r ['name'];
			}

			$desc = $r ['description'];
			// f (!empty($desc)){
			// $desc = sprintf('%s (%s)',$desc,$r['file_ext']);
			//
			if (empty ( $desc )) {
				$desc = $name;
			}

			$bindex = $sequence_id;
			if (empty ( $bindex )) {
				$bindex = 0;
			}
			while ( isset ( $bitstreams [$bindex] ) ) {
				$bindex += 1;
			}
			$bitstreams [$bindex] = array (
					'bitstream_id' => $r ['bitstream_id'],
					'name' => $name,
					'size_bytes' => $r ['size_bytes'],
					'internal_id' => $r ['internal_id'],
					'bundle_name' => $r ['bundle_name'],
					'checksum' => $r ['checksum'],
					'checksum_algorithm' => $r ['checksum_algorithm'],
					'md5_org' => $r ['md5_org'],
					'description' => $desc,
					'mimetype' => $r ['mimetype'],
					'bundle_name' => $r ['bundle_name'],
					'artifact_id' => $r ['artifact_id'],
					'furl' => $r ['furl'],
					'redirect_type' => $r ['redirect_type'],
					'redirect_url' => $r ['redirect_url'],
					'src_url' => $r ['src_url'],
					'file_ext' => $r ['file_ext'],
					'info' => $r ['info'],
					'thumb_file' => $r ['thumb_file'],
					'thumb_idx' => $r ['idx'],
					'thumb_idxf' => $r ['idxf'],
					'thumb_type' => $r ['ttype'],
					'thumb_extension' => $r ['extension'],
					'symlink' => $r ['symlink'],
					'symlink_id' => $r ['symlink_id'],
					'download_fname' => $r ['download_fname'],
					'jdata'=> $r ['jdata'],
					'item' => $r ['item']
			);
		}
		ksort ( $bitstreams );
		return $bitstreams;
	}


	public static final function getItemThumbs($item_id) {
		$dbh = dbconnect ();

		$SQL = "SELECT b.thumb_description
						FROM dsd.item_bitstream_ext b
						LEFT JOIN dsd.thumbs t ON (t.item_id = b.item_id)
						WHERE b.item_id =? AND (t.bitstream = b.bitstream_id)
						GROUP BY b.item_id, b.thumb_description ";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();

		$res  = $stmt->fetch();
		$thumbs_description = $res[0];

		$SQL = "SELECT file,idx,idxf,ttype from dsd.thumbs where item_id = ? ORDER BY idx";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();

		$thumbs_small = array ();
		$thumbs_big = array ();
		while ( $r = $stmt->fetch () ) {
			$file = $r [0];
			$idxf = $r [2];
			$ttype = $r [3];

			if ($ttype == 1) {
				$thumbs_small [$idxf] = $file;
			} else if ($ttype == 2) {
				$thumbs_big [$idxf] = $file;
			}
		}

		return array (
				'big' => $thumbs_big,
				'small' => $thumbs_small,
				'thumbs_description' => $thumbs_description
		);
	}

	// @DocGroup(module="util", group="archive", comment="get_item_metadata")



	/**
	 *
	 * @param PDO $dbh
	 * @param integer $item
	 *        	(item_id)
	 * @return ItemMetadataAccess
	 */
	//public static final function get_item_metadata($item) {
	public static final function get_item_metadata($item, $keys = null) {
		//Log::info("@@: get_item_metadata: " . $item);
		//$dbh = dbconnect ();
		$dbh =  prepareService();

		$idata = new ItemMetadata ();

		$maxRecordId = 1;
		$addRec = function($row) use(&$maxRecordId,$idata){
			$recId = $row ['lid'];
			if ($maxRecordId < $recId) {
				$maxRecordId = $recId;
			}
			//addValueFromDBTextSK($key, $value, $lang, $vid, $relation=null, $ref_item=null, $data=null,$staff=null,$weight=null,$grp = null,$inferred = false)
			$idata->addValueFromDBTextSK($row['element'], $row ['text_value'], $row['text_lang'], $row['metadata_value_id'], $row['relation'], $row['ref_item'], $row['data'], $row['lid'], $row ['weight'], $row ['link'], $row['inferred']);
		};


// 		$addToData = function($row,$data){
// 			//$data['remote_rel'] =true;
// 			if (empty($row['data'])){
// 				$row['data']  = '[]';
// 			}
// 			$tmp = json_decode( $row['data'], true );
// 			//Log::info('DATA:  '.$row['data']);
// 			if (isset($tmp['data'])){
// 				$jdata = $tmp['data'];
// 			} else {
// 				$jdata = array();
// 			}
// 			$jdata = array_merge($jdata , $data);
// 			$row['data'] = json_encode(array_merge($tmp , array('data'=>$jdata)));
// 			//Log::info("FINAL DATA: ". $row['data']);
// 			return $row;

// 		};
		$EXTRA_WHERE = '';
		if (!empty($keys)){

			$add_quotes = function($in){ return "'" . $in . "'"; };
			$EXTRA_WHERE = sprintf(' AND element in (%s)',implode(',',array_map($add_quotes,$keys)));
		}
		$FIELDS1='element, text_value, text_lang, metadata_value_id, relation, ref_item, data, lid, weight,link, inferred, item_id';
		$SQL = sprintf('SELECT %s  FROM dsd.metadatavalue2 WHERE item_id = ? %s ORDER BY weight',$FIELDS1, $EXTRA_WHERE);





		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item );
		$stmt->execute ();
		while ( $row = $stmt->fetch (PDO::FETCH_ASSOC) ) {
			$addRec($row);
		}



		if (Config::get('arc.ITEM_LOAD_INFERENCE_FROM_JSON',1) >0){
			Log::info("get_item_metadata load_inference from json-data");
			$SQL="SELECT (edge->>'to')::integer as ref_item ,(edge->>'element')::varchar as element
			FROM (SELECT json_array_elements(jdata->'edges_out') as edge from dsd.item2 WHERE item_id = ?) as foo";
			$stmt = $dbh->prepare( $SQL );
			$stmt->bindParam( 1, $item );
			$stmt->execute();
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				$maxRecordId += 1;
				//Log::info("ADD: " . print_r($row,true) );
				$erow = array('text_value'=>$row['ref_item'],'text_lang'=>null,'metadata_value_id'=>null,'relation'=>null,'data'=>null,'lid'=>$maxRecordId,
						'weight'=>null,'link'=>null,'inferred'=>true,'element'=>$row['element'],'ref_item'=>$row['ref_item']);
				$addRec($erow);
			}
		}
		$maxRecordId += 1; 		// echo("MAX RECORD ID $maxRecordId\n");
		$idata->setStafRecordId ( $maxRecordId );

		return $idata;
	}

	/**
	 *
	 * @param PDO $dbh
	 * @param integer $item
	 *        	(item_id)
	 * @return ItemMetadataAccess
	 */
	public static final function getItemMetadata($item){
			return PDao::get_item_metadata($item);
	}

		// $values: array of values
		// example:
		// [dc:subject:] => Array (
		// [0] => ARRAY( [0] => 'subject1', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6]=> [7]=> [8]=>),
		// [1] => ARRAY( [0] => 'subject2', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6]=> [7]=> [8]=>),
		// [2] => ARRAY( [0] => 'subject3', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6]=> [7]=> [8]=>)
		// )
		//
		// * #SINGLE VALUE FIELDS:
		// * 0: text value
		// * 1: lang
		// * 2: database id: dsd.metadatavalue2(metadata_value_id)
		// * 3: relation
		// * 4: ref_item
		// * 5: json_data
		// * 6: lid (id egrafis mesa sto record)
		// * 7: weight
		// * 8: link (pointer sto lid) diktis st parent
		// * 9: inferred
		// *10: level
		//
	public static final function update_item_metadata($item_id, $key, $values, $append = false, $permit_relation_inference = true, $save_inferred = true){
		//Log::info("@@: UPDATE_ITEM_METADATA: $item_id | $key ");
		//Log::info(print_r($values,true));
		//$dbh = dbconnect();
		$dbh = prepareService();
		if (! $append) {
			// Log::info("dsd.delete_metadata: $item_id , $key\n");
			// $SQL1 = "SELECT dsd.delete_metadata(?,?)";
			$SQL1 = "DELETE FROM dsd.metadatavalue2 WHERE item_id=? AND element= ?";
			$stmt = $dbh->prepare($SQL1);
			$stmt->bindParam(1, $item_id);
			$stmt->bindParam(2, $key);
			$stmt->execute();
			// $stmt->fetch ();
		}
		if (empty($values)) {
			return;
		}

		// @DOC: RELATIONS
		$relCtrl = new RelationControl(); // RC
		$reltype = null;
		if ($relCtrl->isRelation($key)) {
			$rel = $relCtrl->getRelation($key);
			$reltype = $rel->getRelType();
		}

		$i = 0;
		foreach ( $values as $val ) {
			$i ++;
			$data = null;
			$date_json = null;
			$date_comment = null;
			// print_r($val);
			$val_ok = trim(strval($val[0]));
			if (strlen($val_ok) == 0) {
				continue;
			}

			$lang = $val[1];
			$ref_item = isset($val[4]) ? $val[4] : null;

			// @DOC: RELATIONS
			if (! $permit_relation_inference && ! empty($reltype)) {
				$inferred = false;
				if (isset($val[9]) && $val[9]) {
					Log::info("@@: update_item_metadata: CHANGE INFERENCE TO FALSE: " . $item_id . ' key: ' . $key . ' ref: ' . $ref_item);
				}
			} else {
				$inferred = isset($val[9]) ? $val[9] : false;
			}

			if ($inferred && ! $save_inferred) {
				continue;
			}

			if (isset($val[5])) {
				$vdata = $val[5];
				if (isset($vdata['json']['z']) && $vdata['json']['z'] == 'date') {
					$date_json = $vdata['json'];
					$date_comment = isset($date_json['t']) ? $date_json['t'] : null;
				}
				$vdata = PUtil::clearJdata($vdata);
				if (! empty($vdata)) {
					$data = json_encode($vdata);
				}
			}
			$lid = null;
			if (isset($val[6])) {
				$lid = intval($val[6]);
			}
			$w = (isset($val[7]) && ! empty($val[7])) ? intval($val[7]) : $i;

			$pointer = null;
			if (isset($val[8])) {
				$pointer = intval($val[8]);
			}

			$inferred_str = $inferred ? 'true' : 'false';
			$level = isset($val[10]) ? $val[10] : null;

			//Log::info("@@: UPDATE_ITEM_METADATA: $item_id | $key |  $val_ok");
			// if (!empty($ref_item)){ Log::info('@@: update_item_metadata SAVE RELATION: ' . $item_id . ' : ' . $key . ' ref: ' . $ref_item . ' reltype: ' . $reltype . " inf: " . $inferred_str . ' DATA: ' . $data); }

			// 1 a_item_id integer,
			// 2 a_element varchar,
			// 3 a_value text,
			// 4 a_lang varchar default null,
			// 5 a_ref_item integer default null,
			// 6 a_relation bigint default null, -- RELATION TYPE
			// 7 a_data text default null,
			// 8 a_staff integer default null, -- LID
			// 9 a_weight integer default null,
			// 10 a_grp integer default null --LINK
			// 11 a_inferred boolean default false
			// 12 a_level integer default null --tree level
			// $create_rlation = true;
			// Log::info("$i | $item_id | $key | $val_ok | $lang | $relation | $ref_item ");
			$SQL2 = "SELECT dsd.insert_metadata_ext4(?,?, ?,?, ?,?, ?,?, ?,?, ?,?)";
			$stmt = $dbh->prepare($SQL2);
			$stmt->bindParam(1, $item_id);
			$stmt->bindParam(2, $key);
			$stmt->bindParam(3, $val_ok);
			$stmt->bindParam(4, $lang);
			$stmt->bindParam(5, $ref_item);
			$stmt->bindParam(6, $reltype);
			$stmt->bindParam(7, $data);
			$stmt->bindParam(8, $lid);
			$stmt->bindParam(9, $w);
			$stmt->bindParam(10, $pointer);
			$stmt->bindParam(11, $inferred_str);
			$stmt->bindParam(12, $level);
			$stmt->execute();
			$rep = $stmt->fetch();
			$mid = $rep[0];
			if (! empty($date_json)) {
				$y = $date_json['y'];
				$m = $date_json['m'];
				$d = $date_json['d'];
				$fd = new FuzzyDate($y, $m, $d);
				PDao::update_value_date_ranges($mid, $lid, $item_id, $key, $fd, $date_comment);
			}
		}
	}

	/**
	 *
	 * @param unknown $bitstream_id
	 * @param unknown $item_id
	 */
	public static final function move_bitstream($bitstream_id, $item_id){
		$dbh = dbconnect();
		$SQL = "SELECT dsd.move_bitstream(?, ?);";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $bitstream_id );
		$stmt->bindParam ( 2, $item_id);
		$stmt->execute ();
		$rep = $stmt->fetch();

	}




	/**
	 *
	 * @param unknown $item_id
	 * @param FuzzyDate $fuzzyDate
	 */
	public static final function update_value_date_ranges($mfid, $lid, $item_id, $element, $fuzzyDate, $comment){
		$dbh = dbconnect ();

		if (empty($fuzzyDate)){
			$SQL = 'DELETE FROM dsd.value_year_ranges WHERE id = ? AND item_id = ? ';
			$stmt->bindParam ( 1, $mfid );
			$stmt->bindParam ( 2, $item_id );
			$stmt->execute();
			return;
		}

		$year1 = $fuzzyDate->y1;
		$year2 = $fuzzyDate->y2;
		$date_text_val = $fuzzyDate->yearsRange;

		$SQL = "SELECT 1 from dsd.value_year_ranges WHERE id = ?";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $mfid );
		$stmt->execute ();
		if ($stmt->fetch ()) {
			$SQL = 'UPDATE dsd.value_year_ranges SET y1=?, y2=?, text_value=?, comment=? item_id =?, element = ?, lid=? WHERE id = ?';
		} else {
			$SQL = 'INSERT INTO dsd.value_year_ranges (y1, y2,text_value, comment,  item_id,element, lid, id) values (?,?,?,?,?,?,?,?)';
		}
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $year1 );
		$stmt->bindParam ( 2, $year2 );
		$stmt->bindParam ( 3, $date_text_val );
		$stmt->bindParam ( 4, $comment);
		$stmt->bindParam ( 5, $item_id );
		$stmt->bindParam ( 6, $element );
		$stmt->bindParam ( 7, $lid );
		$stmt->bindParam ( 8, $mfid );

		$stmt->execute ();


	}



	// public static final function save_subitem_author($idata,&$out){
	//
	// $userName = get_user_name();
	// $dbh = dbconnect();
	//
	// $trees = array();
	// $elements = DataFields::getContributorElements();
	// $obj_type = 'actor';
	// foreach ($elements as $element) {
	// $etrees = $idata->getTrees($element,null);
	// $trees = array_merge($trees,$etrees);
	// }
	// foreach($trees as $ct){ //contributor tree
	// $c = $ct[0]; //contriburor
	// $ce = $c['key']; //contributor key
	// $rec_id = $c[6];
	// $sidata = new ItemMetadata();
	// $w=0;
	// $sidata->addValueSK(DataFields::ea_obj_type,$obj_type,null,null,null,null,null,null,$w++);
	// $sidata->addValueSK(DataFields::dc_title, $c[0],null,null,null,null,null,$rec_id,$w++ );
	//
	// foreach ($ct as $idx => $v) {
	// $kk = $v['key'];
	// if ($idx > 0){
	// $w++;
	// $link_id = $v[8];
	// if ($link_id == $rec_id && $kk != 'ea:name:_type' ){
	// $link_id = null;
	// }
	// $sidata->addValueSK($kk,$v[0],$v[1],null,$v[3],$v[4],$v[5],$v[6],$w,$link_id);
	// $idata->deleteByClientId($v[6]);
	// }
	// }
	//
	//
	// if (!(empty($c[4]))){
	// $out .= "LINK TO OLD ACTOR " . $c[4] . "\n";
	// } else {
	// $sub_item_id = PDAO::insert_item($userName, $obj_type);
	// //$sub_item_id = insert_item($dbh, $userName);
	// $out .= "INSERT NEW ACTOR: $sub_item_id\n";
	//
	//
	// $idata->updateRefItem($rec_id,$sub_item_id);
	// foreach ($sidata->values as $key2 => $values2) {
	// PDao::update_item_metadata($sub_item_id, $key2, $values2);
	// }
	// insert_generated_metadata($dbh, $sub_item_id,$userName);
	// insert_collection($dbh, $sub_item_id,$obj_type);
	//
	// }
	// //print_r($ct);
	// }
	// return $idata;
	// }




	public static final function save_subitem($idata, $elements, $obj_type, &$out) {
		$status_rep = "none";
		$userName = get_user_name ();
		$dbh = dbconnect ();

		$sidata = null;
		$sub_item_id = null;

		$trees = array ();
		if (is_array ( $elements )) {
			foreach ( $elements as $element ) {
				$etrees = $idata->getTreesByKey ( $element );
				// echo(">> " . $element ."\n");
				// print_r($etrees);
				$trees = array_merge ( $trees, $etrees );
			}
		} else {
			$trees = $idata->getTreesByKey ( $elements );
		}

		foreach ( $trees as $ct ) { // tree
			$c = $ct [0]; //
			$ce = $c ['key']; // key
			$rec_id = $c [6];
			$sidata = new ItemMetadata ();
			$w = 0;

			$sidata->addValueSK ( DataFields::ea_obj_type, $obj_type, null, null, null, null, null, null, $w ++ );
			$sidata->addValueSK ( DataFields::dc_title, $c [0], null, null, null, null, null, $rec_id, $w ++ );

			$max_rec_id = $rec_id;
			$arrbuf = array ();
			foreach ( $ct as $idx => $v ) {
				$kk = $v ['key'];
				if ($kk == 'ea:title:specific'){
					continue;
				}
				if ($idx > 0) {
					$w ++;
					$link_id = $v [8];
					if ($link_id == $rec_id) {
						$link_id = null;
					}
					if ($v [6] > $max_rec_id) {
						$max_rec_id = $v [6];
					}
					// echo("#sub item key: $kk \n");
					$sidata->addValueSK ( $kk, $v [0], $v [1], null, $v [3], $v [4], $v [5], $v [6], $w, $link_id );
					// $idata->deleteByClientId($v[6]);
					$arrbuf [] = $v [6];
				}
			}

			$nid = $max_rec_id + 1;
			$sidata->addValueSK ( 'marc:title-statement:title', $c [0], null, null, null, null, null, $nid, $w ++ );
			$sidata->addValueSK ( 'marc:title-statement:format-formula', '${a}', null, null, null, null, null, null, $w ++, $nid );

			$sidata->generate ();
			$status = $sidata->getValueTextSK ( DataFields::ea_status );
			if (empty ( $status )) {
				$status = 'hidden';
				$sidata->addValueSK ( DataFields::ea_status, $status, null, null, null, null, null, null, $w ++ );
			}
			if ($status != 'incomplete') {

				foreach ( $arrbuf as $vd ) {
					$idata->deleteByClientId ( $vd );
				}

				if (! (empty ( $c [4] ))) {
					$status_rep = "LINK_OLD";
					// $out .= "LINK TO OLD $obj_type $c[4] ($ce $c[0])\n";
					$out .= sprintf ( 'LINK TO OLD %s: <a href="/archive/item/%s">%s (%s  %s)</a>', $obj_type, $c [4], $c [4], $ce, htmlspecialchars ( $c [0] ) );
					$out .= "\n";
				} else {
					$status_rep = "INSERT";
					// $sub_item_id = insert_item($dbh, $userName);
					$sub_item_id = PDAO::insert_item ( $userName, $obj_type );

					// $out .= "INSERT NEW $obj_type: $sub_item_id ($ce $c[0])\n";
					$out .= sprintf ( 'INSERT NEW %s: <a href="/archive/item/%s">%s (%s  $%s)</a>', $obj_type, $sub_item_id, $sub_item_id, $ce, htmlspecialchars ( $c [0] ) );
					$out .= "\n";
					$idata->updateRefItem ( $rec_id, $sub_item_id );
					$sidata->generate ();
					foreach ( $sidata->values as $key2 => $values2 ) {
						PDao::update_item_metadata ( $sub_item_id, $key2, $values2 );
					}
					PDao::insert_generated_metadata ( $dbh, $sub_item_id, $userName );
					PDao::insert_collection ( $dbh, $sub_item_id, $obj_type );
					PDao::touch_item ($sub_item_id );
				}
			} else {
				$out .= "skip link $obj_type  $ce  $c[0]\n";
			}
		}

		return array (
				$idata,
				$sidata,
				$sub_item_id,
				$status_rep
		);
	}

	// public static final function update_object_ref( $group_name , $item_id, $text_value){
	// $dbh = dbconnect();
	// //echo("G: $group_name : $item_id : $text_value\n");

	// $SQLu="UPDATE dsd.metadatavalue2 SET ref_item=? WHERE text_value=? AND element=? AND ref_item is null";
	// $stmtu = $dbh->prepare($SQLu);

	// //$SQLs="select t.id,t.element from dsd.item_relation_type t join dsd.item_relation_type_groups g on (g.element=t.element) WHERE group_name=?";

	// $SQLs="select element from dsd.item_relation_type_groups WHERE group_name=?";
	// $stmts = $dbh->prepare($SQLs);
	// $stmts->bindParam(1, $group_name);
	// $stmts->execute();
	// while($r = $stmts->fetch()){
	// $elem = $r[0];
	// echo(": $elem : $item_id : $text_value \n");
	// echo("SQL: $SQLu \n");
	// $stmtu->bindParam(1, $item_id);
	// $stmtu->bindParam(2, $text_value);
	// $stmtu->bindParam(3, $elem);
	// $ur = $stmtu->execute();
	// }
	// }
	public static final function save_subitem_author($idata, &$out) {
		Log::info("save_subitem_author");
		// print_r($idata->values);
		$contributorsMap = Lookup::getContributors ( 'printed' );
		$elements = array_keys ( $contributorsMap );
		$elements [] = 'dc:publisher:';
		$elements [] = 'ea:publication:printer-name';
		$obj_type = 'actor';
		$rep = PDAO::save_subitem ( $idata, $elements, $obj_type, $out );
		return $rep [0];
	}
	public static final function save_subitem_place($idata, &$out) {
		$elements = array (
				'ea:publication:place',
				'ea:publication:printing-place'
		);
		$obj_type = 'place';
		$rep = PDAO::save_subitem ( $idata, $elements, $obj_type, $out );
		return $rep [0];
	}
	public static final function save_subitem_work($idata, &$out) {
		$elements = array (
				DataFields::ea_work
		);
		$obj_type = 'work';
		$rep = PDAO::save_subitem ( $idata, $elements, $obj_type, $out );
		return $rep [0];
	}
	public static final function createUUID() {
		$dbh = dbconnect ();
		$SQL = "SELECT uuid from uuid_generate_v1mc() as uuid";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->execute ();
		$res = $stmt->fetch ();
		$uuid = $res [0];
		return $uuid;
	}


	public static function terms_cloud($cat = null){

		$dbh= dbconnect();

		$visibility =null;
		if (user_access_mentainer()){
		$visibility = "";
		} else {
		$visibility = sprintf("  AND visibility = %s ",DataFields::DB_visibility_public);
		}

		$stmt =  null;
		if (empty($cat)){
			$SQL=sprintf(" SELECT subject as text_value,cnt as count from dsd.subject where cnt > 0 %s order by 1",$visibility);
			$stmt = $dbh->prepare($SQL);
		} else {
			$SQL=sprintf(" SELECT subject as text_value,cnt as count from dsd.subject where cnt > 0  %s AND cat = ? order by 1",$visibility);
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $cat);
		}

		$stmt->execute();
		$rep = $stmt->fetchAll();
		return $rep;
	}

	public static function get_object_type_names(){
		$dbh =  dbconnect();
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

	//@DocGroup(module="util", group="archive", comment="get_item_label")
	public static function get_item_label($item_id){
		$label = null;
		$dbh = dbconnect();
		$SQL = "select label FROM dsd.item2 WHERE item_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		if  ($r = $stmt->fetch()){
			$label = $r[0];
		}
		return $label;
	}



	public static function search_metadata_all($term) {

		#error_log(">search_metadata_all: $term");

		$dbh = dbconnect();

		$ss = trim($term);

		$SQL = "SELECT distinct m.text_value as value FROM dsd.mvalue m,  dsd.to_gr_tsquery(?) as q WHERE  q @@ m.text_value_fst limit 26;";
		// 	$SQL = "SELECT  distinct m.text_value as value "
		// 	. " from dsd.metadatavalue2 m, dsd.to_gr_tsquery(?) as q where m.metadata_field_id in ( "
		// 	. DB_METADATA_FIELD_DC_AUTHOR . " , "
		// 	. DB_METADATA_FIELD_DC_TITLE  . " , "
		// 	. DB_METADATA_FIELD_DC_SUBJECT . ", "
		// 	. DB_METADATA_FIELD_EA_PUBLICATION_PLACE
		// 	. ") AND q @@ m.text_value_fst limit 22 ";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->execute();
		$rep = array();
		while ($r = $stmt->fetch()){
		$rep[] = array('value' => $r['value']);
	}
	return $rep;

	}

	public static function search_metadata($element, $term, $closure = null, $obj_type = null, $limit = 24){
		$dbh = dbconnect();

		$ss = trim($term);

		$SQL  = "SELECT  distinct m.text_value as value, char_length(m.text_value) as len ";
		$SQL .= " FROM dsd.metadatavalue2 m, dsd.to_gr_tsquery(?) as q ";
		if (is_array($element)) {
			$SQL .= "WHERE element in (";
			$sep ="";
			foreach ($element as $el) {
				$SQL .= $sep . "'$el'";
				$sep = ",";
			}
			$SQL .= ") ";
		} else {
			$SQL .= " WHERE element = '$element' ";
		}
		if (!empty($obj_type)){
			if (is_array($obj_type)) {		$SQL .= " AND obj_type in (";
			$sep ="";
			foreach ($obj_type as $ot) {
				$SQL .= $sep . "'$ot'";
				$sep = ",";
			}
			$SQL .= ") ";
			} else {
				$SQL .= "AND obj_type = '$obj_type'";
			}
		}
		$SQL .= "AND  q @@ m.text_value_fst ";
		$SQL .= " ORDER BY len limit " . $limit;
		//error_log($SQL);
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->execute();

		$rep = array();
		if (empty($closure)) {
			while ($r = $stmt->fetch()){
				//$rep[] = array('label' => $r['label'], 'value' => $r['value']);
				$val = $r['value'];
				$rep[] = array('value' => $val);
			}

		} else {
			while ($r = $stmt->fetch()){
				//$rep[] = array('label' => $r['label'], 'value' => $r['value']);
				$val = $closure($r['value']);
				$rep[] = array('value' => $val);
			}
		}
		return $rep;

	}


	public static function search_metadata_ac($term, $field_id, $closure = null, $limit = 22){

		$dbh = dbconnect();

		$ss = trim($term);

		$SQL  = "SELECT  distinct m.text_value as value ";
		$SQL .= " FROM dsd.metadatavalue2 m, dsd.to_gr_tsquery(?) as q ";
		$SQL .= " WHERE metadata_field_id = ? AND q @@ m.text_value_fst ";
		$SQL .= " limit " . $limit;
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->bindParam(2, $field_id);
		$stmt->execute();

		$rep = array();
		if (empty($closure)) {
			while ($r = $stmt->fetch()){
				//$rep[] = array('label' => $r['label'], 'value' => $r['value']);
				$val = $r['value'];
				$rep[] = array('value' => $val);
			}

		} else {
			while ($r = $stmt->fetch()){
				//$rep[] = array('label' => $r['label'], 'value' => $r['value']);
				$val = $closure($r['value']);
				$rep[] = array('value' => $val);
			}
		}
		return $rep;

	}


	public static function search_subject_relations($subject,$limit){
		$dbh = dbconnect();
		$SQL = "SELECT subject_other from dsd.subject_relation_arc_det where subject=? limit ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $subject);
		$stmt->bindParam(2, $limit);
		$stmt->execute();
		$result = $stmt->fetchAll();
		return $result;
	}


	public static function search_subject_db($term, $limit ,$exact = false){
		$dbh = dbconnect();
		$ss = trim($term);
		if ($ss == ''){
			return ARRAY();
		}
		$SQL  = "SELECT m.subject as value ";
		if ($exact){
			$SQL .= " FROM dsd.subject m, dsd.to_gr_tsquery(?,true) as q";
		} else {
			$SQL .= " FROM dsd.subject m, dsd.to_gr_tsquery(?,false) as q";
		}
		$SQL .= " where ";
		$SQL .= " q @@ subject_fst limit ? ";

		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->bindParam(2, $limit);

		$stmt->execute();
		$rep = $stmt->fetchAll();
		return $rep;
	}


	public static function download_db_log($bitstream_id, $mime, $filesize, $user_agent, $remote_addr, $referer, $user_id) {
		$dbh = dbconnect();
		$nextval = PDao::nextval('dsd.download_log_id_seq');
		$SQL="INSERT INTO dsd.download_log (id, bitstream_id, mime, size, user_agent, remote_addr, referer, user_id) values (?,?,?,?,?,?,?,?);";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $nextval);
		$stmt->bindParam(2, $bitstream_id);
		$stmt->bindParam(3, $mime);
		$stmt->bindParam(4, $filesize );
		$stmt->bindParam(5, $user_agent);
		$stmt->bindParam(6, $remote_addr);
		$stmt->bindParam(7, $referer);
		$stmt->bindParam(8, $user_id);
		$stmt->execute();
		return $nextval;
	}

	public static function download_db_log_append($log_id, $signature, $checksum) {
		$dbh = dbconnect();
		$SQL = "UPDATE dsd.download_log SET signature = ?, checksum = ? WHERE id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $signature);
		$stmt->bindParam(2, $checksum);
		$stmt->bindParam(3, $log_id);
		$stmt->execute();
	}


	public static function item_get_label($item_id){
		$dbh = dbconnect();
		$label= null;
		$SQL="SELECT label from dsd.item2 where item_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		if ($tmp = $stmt->fetch()){
			$label = $tmp['label'];
		}
		return $label;
	}


	public static function getBitstream($id){
		$dbh = dbconnect();
		$SQL = "SELECT  item_id, bitstream_id, bundle_id,bundle_name,name,size_bytes, deleted, internal_id, sequence_id, checksum,checksum_algorithm, description, store_number,
	source, bitstream_format_id, mimetype, create_dt, item, file_ext, download_fname, src_url, redirect_url, redirect_type, internal_comment, artifact_id, furl,logging, replaces, info,
	pages, ctype, auto_gen, md5_org, thumb_description
	FROM  dsd.item_bitstream_ext WHERE bitstream_id = ? ";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $id);
		$stmt->execute();
		if (!$row = $stmt->fetch()){
			echo("bitstream NOT FOUND $id");
			return null;
		}
		return $row;
	}


	public static function getBitstreams($item_id){
		$dbh = dbconnect();
		$SQL=
		"SELECT
	b.name,
	b.internal_id,
	b.size_bytes,
	b.sequence_id,
	ib.bundle_id as bundle_id,
	be.name as bundle_name,
	b.bitstream_id,
	b.checksum,
	b.create_dt::date,
	b.item,
	b.download_fname,
	b.src_url, b.redirect_url, b.redirect_type, b.replaces,b.description, b.artifact_id
	FROM dsd.item2 i
	LEFT JOIN item2bundle ib ON i.item_id = ib.item_id
	LEFT JOIN public.bundle be ON ib.bundle_id = be.bundle_id
	LEFT JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
	LEFT JOIN bitstream b ON bb.bitstream_id = b.bitstream_id
	WHERE i.item_id = ? AND bb.symlink = false AND internal_id is not null ORDER BY  b.sequence_id, b.create_dt";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public static function bitstreams_table($item_id, $table_flag = true){
		$dbh = dbconnect();
		$SQL=
		"SELECT
	b.name,
	b.internal_id,
	b.size_bytes,
	b.sequence_id,
	ib.bundle_id as bundle_id,
	be.name as bundle_name,
	b.bitstream_id,
	b.checksum,
	b.create_dt::date,
	b.item,
	b.download_fname,
	b.src_url, b.redirect_url, b.redirect_type, b.replaces,b.description, b.artifact_id
	FROM dsd.item2 i
	LEFT JOIN item2bundle ib ON i.item_id = ib.item_id
	LEFT JOIN public.bundle be ON ib.bundle_id = be.bundle_id
	LEFT JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
	LEFT JOIN bitstream b ON bb.bitstream_id = b.bitstream_id
	WHERE i.item_id = ? AND bb.symlink = false AND internal_id is not null ORDER BY  b.sequence_id, b.create_dt";


		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		$r = $stmt->fetchAll();
		if (count($r) > 0){
			if ($table_flag){
				echo('<table class="table table-bordered">');
			}
			foreach($r as $k => $v){
				echo('<tr style="vertical-align:top;">');
				#printf('<td>%s</td>',$v[6]);
				printf('<td><a href="/prepo/edit_bitstream?bid=%s">[%s]</a></td>',$v[6],$v[6]);
				printf('<td>%s</td>',$v['artifact_id']);
				printf('<td>%s (%s)</td>',$v[5],$v[4]);
				echo("<td>");
				if (! empty($v['replaces'])){
					printf('<a href="/prepo/edit_bitstream?bid=%s">[%s]</a>',$v['replaces'],$v['replaces']);
				}
				echo("</td>");

				printf('<td>%s</td>',$v[3]);
				echo("<td>");
				printf('<a href="/archive/download?i=%s&d=%s">%s</a> &nbsp; (%sK)',$item_id,$v[1],$v[0], ceil($v[2]/1000));
				if (! empty($v['download_fname'])){
					printf('<br/>download&nbsp;file&nbsp;name:&nbsp;%s',$v['download_fname']);
				}
				if (! empty($v['description'])){
					printf('<br/>description:&nbsp;%s',$v['description']);
				}
				if (! empty($v['redirect_url'])){
					printf('<br/>redirect_url:&nbsp;%s',$v['redirect_url']);
				}

				echo("</td>");
				printf('<td>%s</td>',$v[8]);

				echo('<td style="text-align:right;">');
				printf('<a href="/prepo/edit_bitstream?bid=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span><span class="sr-only">'. tr('Edit'). '</span></a>',$v[6]);
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
				printf('<a href="/prepo/edit_step2?i=%s"><span class="glyphicon glyphicon-file" aria-hidden="true"></span><span class="sr-only">'. tr('Item'). '</span></a>',$v[9]);
				echo("</td>");
				#printf(' <a href="/prepo/delete_bitstream?i=%s">[delete]</a></td>',$v[6]);
				echo("</tr>\n");
			}

			if ($table_flag){
				echo('<table class="table table-striped table-bordered table-hover">');
			}

		}


	}


	public static  function bitstream_symlinks_table($item_id){
		$dbh = dbconnect();
		$SQL=
		"SELECT
	b.name,
	b.internal_id,
	b.size_bytes,
	ib.bundle_id as bundle_id,
	be.name as bundle_name,
	b.bitstream_id,
	bb.id as bb_id,
	bb.weight as bb_weight
	FROM dsd.item2 i
	LEFT JOIN item2bundle ib ON i.item_id = ib.item_id
	LEFT JOIN public.bundle be ON ib.bundle_id = be.bundle_id
	LEFT JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
	LEFT JOIN bitstream b ON bb.bitstream_id = b.bitstream_id
	WHERE i.item_id = ? AND bb.symlink = true AND internal_id is not null ORDER BY  b.sequence_id";


		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		$r = $stmt->fetchAll();
		if (count($r) > 0){
			echo('<table class="table">');
			foreach($r as $k => $v){
				echo('<tr style="vertical-align:top;">');
				printf('<td>symlink: %s</td>',$v['bb_id']);

				printf('<td>%s (%s)</td>',$v['bundle_name'],$v['bundle_id']);

				printf('<td>%s</td>',$v['bb_weight']);

				echo("<td>");
				printf('<a href="/archive/download?i=%s&d=%s">%s</a> &nbsp; (%sK)',$item_id,$v['internal_id'],$v['name'], ceil($v['size_bytes']/1000));
				echo("</td>");

				echo('<td style="text-align:right;">');
				printf('<a href="/prepo/edit_bitstream_symlink?sid=%s">[edit]</a>',$v['bb_id']);
				echo("</td>");
				echo("</tr>\n");
			}
			echo('<table class="table table-striped table-bordered table-hover">');
		}


	}


	public static function get_bitstream_next_seq_id($item_id) {

		$dbh = dbconnect();
		$SQL=
		"SELECT (max(sequence_id) + 1) as sequence_id
	FROM dsd.item2 i
	LEFT JOIN item2bundle ib ON i.item_id = ib.item_id
	LEFT JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
	LEFT JOIN bitstream b ON bb.bitstream_id = b.bitstream_id WHERE i.item_id = ? AND internal_id is not null ";

		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		if ($row = $stmt->fetch()){
			$rep = $row[0];
			return empty($rep) ? 1 : $rep;
		}

		return 1;
	}


	//@DocGroup(module="util", group="archive", comment="get_bundle_id")
	public static function get_bundle_id($item_id, $bundle_name){
		$dbh = dbconnect();
		$SQL="SELECT bundle_id from dsd.item_bundle where bundle=? AND item_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $bundle_name);
		$stmt->bindParam(2, $item_id);
		$stmt->execute();
		if ($row = $stmt->fetch()){
			return $row[0];
		}
		return null;
	}



	public static function get_safe_uiid($dbh){
		$c = 0;
		$ok = false;
		while (! $ok  && $c < 12) {
			$c++;

			$SQL="SELECT uuid from uuid_generate_v1mc() as uuid";
			$stmt = $dbh->prepare($SQL);
			$stmt->execute();
			$res = $stmt->fetch();
			$uuid = $res[0];

			#error_log ("uuid: ($c) : $uuid");

			$SQL="SELECT 1 FROM public.bitstream WHERE internal_id = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $uuid);
			$stmt->execute();
			if (! $stmt->fetch()){
				return $uuid;
			}
		}

		throw new Exception("Error CANOT GET UUID", 1);
		return null;
	}


	public static function insert_bundle2bitstream($dbh, $bundle_id, $bitstream_id) {
		$nextval = PUtil::nextval($dbh,'public.bundle2bitstream_seq');
		$SQL = "INSERT INTO public.bundle2bitstream (id, bundle_id, bitstream_id) values (?,?,?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $nextval);
		$stmt->bindParam(2, $bundle_id);
		$stmt->bindParam(3, $bitstream_id);
		$stmt->execute();
		return $nextval;
	}


	//@DocGroup(module="util", group="archive", comment="get_bitstream_format_id")
	public static function get_bitstream_format_id($dbh,$extension)
	{
		$SQL="SELECT bitstream_format_id from public.fileextension WHERE lower(extension)=lower(?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $extension);
		$stmt->execute();
		$stmt->bindColumn(1, $fid);
		if ($stmt->fetch()){
			return $fid;
		}
		return null;
	}


	//@DocGroup(module="util", group="archive", comment="get_mime_type_from_bitstream_format_id")
	public static function get_mime_type_from_bitstream_format_id($bitstream_format_id)
	{
		$dbh = dbconnect();
		$SQL="SELECT mimetype from public.bitstreamformatregistry where bitstream_format_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $bitstream_format_id);
		$stmt->execute();
		$stmt->bindColumn(1, $mime_type);
		if ($stmt->fetch()){
			return $mime_type;
		}
		return null;
	}


	//@DocGroup(module="util", group="general", comment="nextval(sequence_name)")
	public static function nextval($sequence_name = null)
	{
		$dbh = dbconnect();

		$SQL = "SELECT nextval(?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $sequence_name);
		$stmt->execute();
		$stmt->bindColumn(1, $nextval);
		if ($stmt->fetch()){
			return $nextval;
		}
		return null;
	}



	public static function thumbs_generate_from_bitstream($dbh, $internal_id,  &$output, $generate_pages = true, $parent_flag = true, $only_icons = false){
		//error_log("<> thumbs_generate_from_bitstream: $internal_id\n");
		if ($parent_flag){

			$SQL = "SELECT item_id from dsd.item_bitstream where internal_id = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $internal_id);
			$stmt->execute();
			if ($row = $stmt->fetch()){
				$item_id = $row[0];
			} else {
				$output .= "canot find item_id for internal_id = $internal_id (1)\n";
				return;
			}
		} else {

			$SQL = "SELECT item from public.bitstream where internal_id = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $internal_id);
			$stmt->execute();
			if ($row = $stmt->fetch()){
				$item_id = $row[0];
			} else {
				$output .= "canot find item_id for internal_id = $internal_id (2)\n";
				return;
			}
		}

		$SQL = "SELECT pages, bitstream_id  from public.bitstream where internal_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $internal_id);
		$stmt->execute();
		$row = $stmt->fetch();
		$pages = $row[0];
		$bitstream_id = $row[1];

		//echo("PaGES : $pages");
		#	error_log("thumbs_generate_from_bitstream item: " . $item_id);


		#	$SQL="SELECT  i.obj_type, i.item_id, r.mimetype
		#	FROM dsd.item2 i
		#	JOIN item2bundle ib ON i.item_id = ib.item_id
		#	JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
		#	JOIN bitstream b ON bb.bitstream_id = b.bitstream_id
		#	JOIN public.bitstreamformatregistry r ON r.bitstream_format_id = b.bitstream_format_id
		#	WHERE  b.internal_id = ? ";

		$mime_type = null;
		$SQL="SELECT  i.obj_type FROM dsd.item2 i WHERE i.item_id = ? ";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		if ($row = $stmt->fetch()){
			$obj_type = $row[0];
		} else {
			error_log("canot find item");
			$output .= "canot find item\n";
			return;
		}

		$mime_type = PDao::get_bitstream_mime_type_from_internal_id($internal_id);
		if (empty($mime_type)){
			error_log("canot find mymetype for bitstream $internal_id" );
			$output .= "canot find mymetype for bitstream $internal_id\n";
			return;
		}


		$filename  = PUtil::bitream2filename($internal_id);

		$out = array();
		$status = 0;


		if (empty($pages)){
// 			echo("error: number of pages not found\n");
			echo '<div class="arch-wrap" ><div class="error_msg">';
			echo tr('Error: number of pages not found');
			echo '</div></div>';
			error_log("number of pages not found");
		}
		//$output .= "number of pages found: $pages\n";


		$tdir = PUtil::thumb_create_dir();

		$SQL = "DELETE FROM dsd.thumbs WHERE item_id = ? AND auto_gen ";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();

		$image_info = ARRAY();
		if (PUtil::strBeginsWith($mime_type,"image")){
			$image_info = PUtil::identify_image($filename);
			$image_width = $image_info['WIDTH'];
			//$image_height = $image_info['HEIGHT'];
			if (!empty($image_width) && $image_width > 1100 && ! $only_icons ){
				//MAX
				$thumbname = PDao::generate_thumbnail('0',0,0,1100,5 ,$item_id, $filename,  $tdir, $obj_type, $mime_type, $image_info ,$bitstream_id );
			}
		}

		$idx0 = 0;
		$filename0 = $filename;
		if ($mime_type == 'application/x-cbr'){
			$filename0 = PUtil::extract_cbr_page($filename, 0);
			$idx0 = 0;
		}elseif ($mime_type == 'image/vnd.djvu'){
			$filename0 = PUtil::extract_djvu_page($filename, 0);
			$idx0 = 0;
		}
		$thumbname = PDao::generate_thumbnail('0',0,0,65 ,3 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id );
		$thumbname = PDao::generate_thumbnail('0',0,0,110,4 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id  );

		if (!$only_icons){
			$thumbname = PDao::generate_thumbnail('0',0,0,200,1 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id  );
			$thumbname = PDao::generate_thumbnail('0',0,0,600,2 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id  );
		}
		if ($generate_pages && !empty($pages))
		{
			if ($pages > 1){
				$idx0=1;
				$filename0 = $filename;
				if ($mime_type == 'application/x-cbr'){
					$filename0 = PUtil::extract_cbr_page($filename, 1);
					$idx0 = 0;
				}elseif ($mime_type == 'image/vnd.djvu'){
					$filename0 = PUtil::extract_djvu_page($filename, 1);
					$idx0 = 0;
				}
				PDao::generate_thumbnail('1',1,$idx0,120,1 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id  );
				if ($mime_type != 'application/x-cbr'){
					PDao::generate_thumbnail('1',1,$idx0,600,2, $item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id  );
				}
			}
			if ($pages > 2){
				$idx0=2;
				$filename0 = $filename;
				if ($mime_type == 'application/x-cbr'){
					$filename0 = PUtil::extract_cbr_page($filename, 2);
					$idx0 = 0;
				}elseif ($mime_type == 'image/vnd.djvu'){
					$filename0 = PUtil::extract_djvu_page($filename, 2);
					$idx0 = 0;
				}
				PDao::generate_thumbnail('2',2,$idx0,120,1, $item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id  );
				if ($mime_type != 'application/x-cbr'){
					PDao::generate_thumbnail('2',2,$idx0,600,2, $item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id );
				}
			}
			if ($pages > 3){
				$idx0=3;
				$filename0 = $filename;
				if ($mime_type == 'application/x-cbr'){
					$filename0 = PUtil::extract_cbr_page($filename, 3);
					$idx0 = 0;
				}elseif ($mime_type == 'image/vnd.djvu'){
					$filename0 = PUtil::extract_djvu_page($filename, 3);
					$idx0 = 0;
				}
				PDao::generate_thumbnail('3',3,$idx0,120,1 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id   );
				if ($mime_type != 'application/x-cbr'){
					PDao::generate_thumbnail('3',3,$idx0,600,2 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info,$bitstream_id  );
				}
			}
			if ($pages > 4){
				$idx0=4;
				$filename0 = $filename;
				if ($mime_type == 'application/x-cbr'){
					$filename0 = PUtil::extract_cbr_page($filename, 4);
					$idx0 = 0;
				}elseif ($mime_type == 'image/vnd.djvu'){
					$filename0 = PUtil::extract_djvu_page($filename, 4);
					$idx0 = 0;
				}
				PDao::generate_thumbnail('4',4,$idx0,120,1 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info ,$bitstream_id );
				if ($mime_type != 'application/x-cbr'){
					PDao::generate_thumbnail('4',4,$idx0,600,2 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, $image_info ,$bitstream_id );
				}
			}
			if ($pages > 5){
				$idx0=$pages-1;
				$filename0 = $filename;
				if ($mime_type == 'application/x-cbr'){
					$filename0 = PUtil::extract_cbr_page($filename, $idx0);
					$idx0 = 0;
				}elseif ($mime_type == 'image/vnd.djvu'){
					$filename0 = PUtil::extract_djvu_page($filename, $idx0);
					$idx0 = 0;
				}
				$last = $pages - 1;
				PDao::generate_thumbnail('l',$last,$idx0,120,1 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type, array(), $bitstream_id );
				if ($mime_type != 'application/x-cbr'){
					PDao::generate_thumbnail('l',$last,$idx0,600,2 ,$item_id, $filename0,  $tdir, $obj_type, $mime_type , array(), $bitstream_id);
				}
			}
		}
	}


	//@DocGroup(module="util", group="archive", comment="get_bitstream_mime_type_from_internal_id")
	public static function get_bitstream_mime_type_from_internal_id($internal_id){
		$dbh = dbconnect();

		$SQL="SELECT   r.mimetype
	FROM public.bitstream b
	JOIN public.bitstreamformatregistry r ON r.bitstream_format_id = b.bitstream_format_id
	WHERE  b.internal_id = ? ";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $internal_id);
		$stmt->execute();
		if ($row = $stmt->fetch()){
			$mime_type = $row[0];
			return $mime_type;
		}
		return null;
	}


	public static function generate_thumbnail($ff, $idx,$cidx,$size,$ttype, $item_id, $filename,  $tdir, $obj_type, $mime_type, $bitstream_info = ARRAY(), $bitstream) {
	$dbh = dbconnect();

	$post = null;
	if ($ttype == 1){
		$post = 'small';
	} else if ($ttype == 2){
		$post = 'big';
	} else if ($ttype == 3){
		$post = 'icon_small';
	} else if ($ttype == 4){
		$post = 'icon_big';
	} else if ($ttype == 5){
		$post = 'max';
	} else {
		return null;
	}

	$fn = $filename;
// 	$cidx = $idx;
// 	$fn = $filename;
// 	if ($mime_type == 'application/x-cbr'){
// 		$cidx = 0;
// 		$my_idx = $idx +1;
// 		$cmd = BIN_DIR . "cbr_extract_page.sh $filename $my_idx";
// 		//error_log($cmd);
// 		$tmp = exec($cmd,$out,$status);
// 		if (isset($out[0])){
// 			$fn = $out[0];
// 		} else {
// 			$fn =null;
// 		}
// 	}

// 	if ($mime_type == 'image/vnd.djvu'){
// 		$cidx = 0;
// 		$my_idx = $idx + 1;
// 		$cmd = BIN_DIR . "djvu_extract_page.sh $filename $my_idx";
// 		$tmp = exec($cmd,$out,$status);
// 		if (isset($out[0])){
// 			$fn = $out[0];
// 		} else {
// 			$fn =null;
// 		}
// 	}

	if (PUtil::strBeginsWith($mime_type,"image") && !empty($bitstream_info)){
		//$image_info = PUtil::identify_image($fn);
		$image_info = $bitstream_info;
		$image_width = $image_info['WIDTH'];
		//$image_height = $image_info['HEIGHT'];
		if (!empty($image_width) && $image_width < $size ){
			$size = $image_width;
		}
	}

	$extension = 'png';
	if($post == 'max' || $post == 'big' || $post == 'small') {
		$extension = 'jpg';
	}
	$thumbfile = $tdir . '/th_' . $item_id . '_' . $ff . '_' . $post . "." .  $extension;
	$full_path = Config::get('arc.THUMBNAIL_DIR') . $thumbfile;


	if (file_exists($full_path)){
		unlink($full_path);
	}
	$cmd = null;
	if (! empty($obj_type) && $obj_type == 'web-site-instance' && $ttype == 1){
		$cmd = Config::get('arc.BIN_DIR') . 'convert -thumbnail ' . $size . 'x  ' . $fn . '[' . $cidx . ']  -crop x300+0+0 ' . $full_path;
	}else if (! empty($obj_type) && $obj_type == 'web-site-instance' && $ttype == 2){
		$cmd = Config::get('arc.BIN_DIR'). 'convert -thumbnail ' . $size . 'x  ' . $fn . '[' . $cidx . ']  -crop x700+0+0 ' . $full_path;
	} else if ($ttype == 3){
		$cmd = Config::get('arc.BIN_DIR') . 'convert -thumbnail ' . $size . 'x  ' . $fn . '[' . $cidx . ']  -crop x100+0+0 ' . $full_path;
	} else if ($ttype == 4){
		$cmd = Config::get('arc.BIN_DIR') . 'convert -thumbnail ' . $size . 'x  ' . $fn . '[' . $cidx . ']  -crop x190+0+0 ' . $full_path;
	} else {
		$cmd =Config::get('arc.BIN_DIR') . 'convert -thumbnail ' . $size . 'x  ' . $fn . '[' . $cidx . '] ' . $full_path;
	}
// 	echo("<pre>");
// 	echo("\n========================\n");
// 	echo("$mime_type\n");
// 	echo("$cmd\n");
// 	echo("========================\n");
// 	echo("</pre>");
	#error_log($cmd);

	$tmp = exec($cmd,$out,$status);
//  	echo("<pre>");
//  	print_r($out);
//  	echo("</pre>");
	#error_log($out);


	if (file_exists($full_path)){
		PUtil::correct_file_privs($full_path);
		$SQL = "insert into dsd.thumbs (item_id,file,idx,idxf,ttype,extension,bitstream) values (?,?,?,?,?,?,?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->bindParam(2, $thumbfile);
		$stmt->bindParam(3, $idx);
		$stmt->bindParam(4, $ff);
		$stmt->bindParam(5, $ttype);
		$stmt->bindParam(6,$extension);
		$stmt->bindParam(7,$bitstream);

		$stmt->execute();


	} else {
		return null;
	}
	return $thumbfile;
}


//@DocGroup(module="util", group="archive", comment="get_bitstream_item_ref")
public static function get_bitstream_item_ref($bitstream_id)
{
	$dbh=dbconnect();
	$SQL="SELECT item from public.bitstream WHERE bitstream_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bitstream_id);
	$stmt->execute();
	$stmt->bindColumn(1, $id);
	if ($stmt->fetch()){
		return $id;
	}
	return null;
}


public static function delete_item($item_id){

	$dbh = dbconnect();

	$SQL = "SELECT dsd.delete_item(?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	if ($r=$stmt->fetch()){
		return true;
	}
	return false;
}


public static function delete_bitstream($item_id, $bid){

	$dbh = dbconnect();

	$SQL = "SELECT 1 from dsd.item_bitstream where item_id =? and bitstream_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->bindParam(2, $bid);
	$stmt->execute();
	if (!$stmt->fetch()){
		echo("error bid (2)");
		return;
	}

	$dbh->beginTransaction();

	$SQL = "DELETE FROM dsd.download_log WHERE bitstream_id = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bid);
	$stmt->execute();

	$SQL = "DELETE FROM public.bitstream_md5 WHERE bitstream = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bid);
	$stmt->execute();

	$SQL = "DELETE FROM public.bundle2bitstream WHERE bitstream_id = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bid);
	$stmt->execute();

	$SQL = "UPDATE dsd.thumbs SET bitstream=null WHERE bitstream = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bid);
	$stmt->execute();

	$SQL = "DELETE FROM public.bitstream WHERE bitstream_id = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bid);
	$stmt->execute();

	$dbh->commit();
}

public static function bitstream_get_internal_id($bitstream_id){
	$dbh = dbconnect();
	$rep = null;
	$SQL="SELECT internal_id from dsd.item_bitstream where bitstream_id=?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bitstream_id);
	$stmt->execute();
	if ($tmp = $stmt->fetch()){
		$rep = $tmp[0];
	}
	return $rep;
}

public static function insert_bundle($dbh, $bundle_name) {
	$bundle_id = PUtil::nextval($dbh,'public.bundle_seq');
	$SQL = "INSERT INTO public.bundle (bundle_id, name) VALUES (?,?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bundle_id);
	$stmt->bindParam(2, $bundle_name);
	$stmt->execute();
	return $bundle_id;
}

public static function insert_item2bundle($dbh, $item_id,$bundle_id) {

	$nextval = PUtil::nextval($dbh,'public.item2bundle_seq');
	$SQL = "INSERT INTO public.item2bundle (id ,item_id , bundle_id) values (?,?,?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $nextval);
	$stmt->bindParam(2, $item_id);
	$stmt->bindParam(3, $bundle_id);
	$stmt->execute();

	return $nextval;

}

public static  function thumbnail_add($dbh, $file_path, $extension,  $item_id, $idx, $idxf, $ttype, $auto_gen, &$out){
	$ext = $extension;
	$tdir = rand(100,399);
	$cmd = 'mkdir ' . Config::get('arc.THUMBNAIL_DIR')  . $tdir;
	$tmp = exec($cmd);
	if (empty($auto_gen)){
		$auto_gen = 0;
	}

	$nv = PUtil::nextval($dbh,'dsd.thumbs_name_idx');
	#$thumbfile = $tdir . '/th_' . $item_id . '_' . $idxf . '_' . $ttype . "." . $ext;
	$thumbfile = $tdir . '/th_' . $nv . "." . $ext;

	$full_path = Config::get('arc.THUMBNAIL_DIR') . $thumbfile;


	$cmd = "mv $file_path $full_path";
	$tmp = exec($cmd);
	#move_uploaded_file($uploadData['tmp_name'], $full_path);
	$out .= "add thumbfile: $full_path ($item_id, $idx, $idxf, $ttype, $auto_gen)\n";

	if (file_exists($full_path)){
		$SQL = "DELETE FROM dsd.thumbs WHERE item_id = ? AND idx = ? AND ttype = ? AND not auto_gen";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->bindParam(2, $idx);
		$stmt->bindParam(3, $ttype);
		$stmt->execute();

		$SQL = "insert into dsd.thumbs (item_id,file,idx,idxf, ttype,extension, auto_gen) values (?,?,?,?,?,?,?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->bindParam(2, $thumbfile);
		$stmt->bindParam(3, $idx);
		$stmt->bindParam(4, $idxf);
		$stmt->bindParam(5, $ttype);
		$stmt->bindParam(6, $extension);
		$stmt->bindParam(7, $auto_gen);

		$stmt->execute();
	}else {
		error_log("ERROR at uplaod thumbnail");
	}

}


public static function search_item_from_metadata($term,$field_id, $disp_obj_type = true,  $folder = false, $closure = null, $obj_type = null){

	$dbh = dbconnect();

	$ss = trim($term);

	$SQL = "SELECT ";

	if ($disp_obj_type){
		$SQL .= " m.item_id, i.label || ' (' || i.obj_type || ')' as label, m.text_value as value ";
	} else {
		$SQL .= " m.item_id, i.label, m.text_value as value ";
	}
	$SQL .=  " FROM dsd.metadatavalue2 m join dsd.item2 i ON (i.item_id = m.item_id), dsd.to_gr_tsquery(?) as q ";
	$SQL .=  " WHERE i.status <> 'error' AND ";
	if ($field_id ==  Config::get('arc.DB_METADATA_FIELD_DC_TITLE')) {
		$SQL .= sprintf(' metadata_field_id in (%s, %s) ', Config::get('arc.DB_METADATA_FIELD_DC_TITLE'),  Config::get('arc.DB_METADATA_FIELD_EA_TITLE_UNIFORM'));
	} else {
		$SQL .= " metadata_field_id = ? ";
	}
	$SQL .= " AND q @@ m.text_value_fst ";
	if (! empty($folder)){
		$SQL .= " AND i.folder";
	}
	if (! empty($obj_type)){
		$SQL .= " AND i.obj_type = ?";
	}

	$SQL .=  " limit 18";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $ss);
	$idx = 1;
	if ($field_id !=  Config::get('arc.DB_METADATA_FIELD_DC_TITLE')) {
		$idx +=1;
		$stmt->bindParam($idx, $field_id);
	}
	if (! empty($obj_type)){
		$idx +=1;
		$stmt->bindParam($idx, $obj_type);
	}

	$stmt->execute();

	$rep = array();
	if (empty($closure)){
		while ($r = $stmt->fetch()){
			$rep[] = array('id'=>$r[0], 'label'=>$r[1], 'value' => $r[1]);
		}
	} else {
		while ($r = $stmt->fetch()){
			$rep[] = array('id'=>$r[0], 'label'=>$r[1], 'value' => $r[1]);
		}
	}
	return $rep;

}

//@DEPRECATED
//@DocGroup(module="relations-items", group="php", comment="insert a relation")
public static function insert_relation_items($item_id_1, $item_id_2, $rel_type){
	$s1 = $item_id_1;
	$s2 = $item_id_2;

	$ok = true;
	$errors = array();
	$dbh = dbconnect();

	$obj_type_1 = null;
	$obj_type_2 = null;

	if ($rel_type == 'auto'){
		$rel_type = null;
	}

	$SQL = "SELECT dsd.save_item_relation(?,?,?,null::varchar) as rep";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id_1);
	$stmt->bindParam(2, $item_id_2);
	//$stmt->bindParam(3, null);
	$stmt->bindParam(3, $rel_type);


	$stmt->execute();
	if (! $row = $stmt->fetch()){
		$ok = false;
		$errors[]= "canot create relation";
	}

	$SQL = "SELECT dsd.touch_item(?,true,false) as rep";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id_1);
	$stmt->execute();

	$SQL = "SELECT dsd.touch_item(?,true,false) as rep";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id_2);
	$stmt->execute();

	return $errors;
}




public static function export_one_item($item_id, $subdir = null){

	$dbh = dbconnect();
	$basic_data = PUtil::get_item_basic_data($dbh, $item_id);
	$uuid = $basic_data->uuid;

	$SQL="SELECT item_id, bitstream_id,name,size_bytes,checksum,checksum_algorithm,description,user_format_description,internal_id,store_number,sequence_id,mimetype,optimized
	from dsd.bitstream_export  where item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	$bitstreams  = $stmt->fetchAll($fetch_style = PDO::FETCH_ASSOC);


	$SQL="SELECT item_id,bundle_id,bundle, internal_id from dsd.item_bundle_bitstream  where item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	$bundles  = $stmt->fetchAll($fetch_style = PDO::FETCH_ASSOC);


	$SQL="SELECT id,item_id,file,idx,idxf,ttype,auto_gen,extension from dsd.thumbs where item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	$thumbs = $stmt->fetchAll($fetch_style = PDO::FETCH_ASSOC);


	$SQL="SELECT id, item_1, uuid_1, item_2, uuid_2, rel_type, label, create_dt FROM dsd.item_relation_uuid WHERE item_1 = ? OR item_2 = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->bindParam(2, $item_id);
	$stmt->execute();
	$relations = $stmt->fetchAll($fetch_style = PDO::FETCH_ASSOC);



	$export_base_dir = $subdir == null ? Config::get('arc.EXPORT_DIR') : Config::get('arc.EXPORT_DIR') . "/" . $subdir . "/";

	$export_dir = $export_base_dir  . $uuid . "/";
	$cmd_out = array();

	$cmd = ' mkdir -p ' . $export_dir;
	$tmp = exec($cmd,$cmd_out,$status);

	foreach ($bitstreams as $f){

		$internal_id = $f['internal_id'];
		$filename  = PUtil::bitream2filename($internal_id);
		$cmd = ' cp ' . $filename . " $export_dir";
		$tmp = exec($cmd,$cmd_out,$status);

	}

	foreach ($thumbs as $f){
		$file = Config::get('arc.THUMBNAIL_DIR') .  $f['file'];
		$cmd = ' cp ' . $file . " $export_dir" . $f['id'];
		$tmp = exec($cmd,$cmd_out,$status);
	}


	$now = new DateTime(null, new DateTimeZone('UTC'));
	$now_str = $now->format('Y-m-d\TH:i:s\Z');
	$server_addr = $_SERVER['SERVER_ADDR'];

	$export_version  = "1.0";
	$basic_data_arr = $basic_data->toArray();
	$arr = ARRAY(
			"export_version" => $export_version,
			"uuid" => $uuid,
			"item_id" => $item_id,
			"export_time" => $now_str,
			"server_addr" => $server_addr,
			"basic_data" => $basic_data_arr,
			"bitsreams" => $bitstreams,
			"bundles" => $bundles,
			"thumbnails" => $thumbs,
			"relations" => $relations
	);
	$txt = json_encode($arr);
	#echo("<pre>");
	#echo("\n==========================\n");
	#echo($txt);
	#echo("\n==========================\n");
	#echo(json_last_error());
	#echo("\n==========================\n\n");
	#echo("</pre>");


	$cmd = "du $export_base_dir |awk '{print $1}'";
	$tmp = exec($cmd,$cmd_out,$status);
	$size = $cmd_out[1];


	$f1 = $export_dir . "metadata.json";
	$fh = fopen($f1, 'a+') or die("can't open file for append");
	fwrite($fh, $txt);
	fclose($fh);


	$title = $basic_data_arr['metadata']['dc:title:'][0][0];
	$obj_type = $basic_data_arr['metadata']['ea:obj-type:'][0][0];
	$uri  = $basic_data_arr['metadata']['dc:identifier:uri'][0][0];
	$date_av = $basic_data_arr['metadata']['dc:date:available'][0][0];
	$bitstreams_count = count($bitstreams);
	$bundles_count = count($bundles);
	$thumbs_count = count($thumbs);
	$relations_count = count($relations);





	$txt="";
	$txt .= "export_version: $export_version\n";
	$txt .= "uuid          : $uuid\n";
	$txt .= "export_time   : $now_str\n";
	$txt .= "server_addr   : $server_addr\n";
	$txt .= "item_id       : $item_id\n";
	$txt .= "uri           : $uri\n";
	$txt .= "obj_type      : $obj_type\n";
	$txt .= "title         : $title\n";
	$txt .= "date_available: $date_av\n";
	$txt .= "bitstreams_cnt: $bitstreams_count\n";
	$txt .= "bundles_cnt   : $bundles_count\n";
	$txt .= "thumbs_cnt    : $thumbs_count\n";
	$txt .= "relations_cnt : $relations_count\n";
	$txt .= "size          : $size\n";


	$f1 = $export_dir . "info.txt";
	$fh = fopen($f1, 'a+') or die("can't open file for append");
	fwrite($fh, $txt);
	fclose($fh);

	$fname =  $uuid . ".tgz";
	$export_file =  Config::get('arc.EXPORT_DIR') . $fname;
	$cmd = "tar  --directory  " . $export_base_dir . " -czvf " . $export_base_dir . "$fname $uuid ";
	$tmp = exec($cmd,$cmd_out,$status);

	$cmd = "gzip $export_file";
	$tmp = exec($cmd,$cmd_out,$status);

	$cmd = ' rm -rf ' . $export_dir;
	$tmp = exec($cmd,$cmd_out,$status);

	return array($export_file,$fname);
}



public static function search_metadata_element($element_name, $closure = null, $limit = 18){

	$dbh = dbconnect();
	$ss = trim($element_name) .  "%";

	$SQL  = "SELECT  element as value ";
	$SQL .= " FROM dsd.metadatafieldregistry_view m";
	$SQL .= " WHERE element like ? ";
	$SQL .= " limit " . $limit;
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $ss);
	$stmt->execute();


	$rep = array();
	if (empty($closure)) {
		while ($r = $stmt->fetch()){
			//$rep[] = array('label' => $r['label'], 'value' => $r['value']);
			$val = $r['value'];
			$rep[] = array('value' => $val);
		}

	} else {
		while ($r = $stmt->fetch()){
			//$rep[] = array('label' => $r['label'], 'value' => $r['value']);
			$val = $closure($r['value']);
			$rep[] = array('value' => $val);
		}
	}
	return $rep;

}


public static  function insert_bundle2content($bundle_id, $content_id) {
	$dbh = dbconnect();
	$nextval = PDao::nextval('public.bundle2content_id_seq');
	$SQL = "INSERT INTO public.bundle2content (id, bundle_id, content_id) values (?,?,?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $nextval);
	$stmt->bindParam(2, $bundle_id);
	$stmt->bindParam(3, $content_id);
	$stmt->execute();
	return $nextval;
}


public static function update_bibref($item_id, $bibref){
	$dbh = dbconnect();
	if (!  empty($bibref) && $bibref){
		$bibref_txt = 'true';
	} else {
		$bibref_txt = 'false';
	}
	$SQL="UPDATE dsd.item2 SET bibref = ?  WHERE item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $bibref_txt);
	$stmt->bindParam(2, $item_id);
	$stmt->execute();

	return $bibref;
}


public static function get_metadata_value_count($element, $term){

	$dbh = dbconnect();

	$ss = trim($term);

	$SQL  = "SELECT  count(*) as cnt ";
	$SQL .= " FROM dsd.metadatavalue2 m ";
	$SQL .= " WHERE element = ? ";
	$SQL .= " AND  text_value = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $element);
	$stmt->bindParam(2, $ss);
	$stmt->execute();

	$r = $stmt->fetch();
	$val = $r['cnt'];
	error_log($val);
	return $val;
}





/**
 *
 * @param unknown $value
 * @param string[] $flags  (den ginete escape, prepei na ine trusted gia apofigi SQL INJECTION)
 * @param string $prependObjType
 * @param number $qlimit
 */
public static function find_node_for_subject($search_string, $flags, $prependObjType = false, $qlimit = 30, $search_init = false){

	if (!is_int($qlimit)){
		$qlimit = 30;
	}

	$ss = trim($search_string);

	if (is_array($flags)){
		$flags_str = 'ARRAY[';
		$sep='';
		foreach ($flags as $f){
			$flags_str .= sprintf("%s'%s'",$sep,$f);
			$sep = ', ';
		}
		$flags_str.=']';
	} else {
		$flags_str .= sprintf("ARRAY['%s']",$flags);
	}

	$dbh = dbconnect();
	$rep = array();

	if ( $search_init && (empty($ss) || $ss == 'null') ){
		$SQL =sprintf("SELECT item_id as ref_item, label, prop_int[1] as count, obj_type
		FROM dsd.item2 WHERE flags @> %s AND status in ('finish')
		ORDER BY CHAR_LENGTH(label) ASC  LIMIT %s", $flags_str,  $qlimit);
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
	}else{
		$SQL =sprintf("SELECT item_id as ref_item, label, prop_int[1] as count, obj_type
		FROM dsd.item2, dsd.to_gr_tsquery(?) as q WHERE q @@ %s AND flags && %s AND status in ('finish')
		ORDER BY CHAR_LENGTH(label) ASC  LIMIT %s",PgFtsConstants::COLUMN_SUBJECT, $flags_str,  $qlimit);
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->execute();
	}

// 	$SQL =sprintf("SELECT item_id as ref_item, label, prop_int[1] as count, obj_type
// 			FROM dsd.item2, dsd.to_gr_tsquery(?) as q WHERE q @@ %s AND flags @> %s AND status in ('finish')
// 			ORDER BY CHAR_LENGTH(label) ASC  LIMIT %s",PgFtsConstants::COLUMN_SUBJECT, $flags_str,  $qlimit);

// 	$stmt = $dbh->prepare($SQL);
// 	$stmt->bindParam(1, $ss);
// 	$stmt->execute();

	$i= 0;
	while ($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		$ref_item = $r['ref_item'];
		$label  = htmlspecialchars_decode($r['label']);
		$count = $r['count'];

		$obj_type = null;
		if ($prependObjType){
			$obj_type = tr('cat_'.$r['obj_type']);
		}

		//$rep[$ref_item] = array('value' => array($ref_item, $label, $count,0,$obj_type));
		$rep[$i] = array('value' => array($ref_item, $label, $count,0,$obj_type));
		$i++;
	}
	return $rep;
}




/**
 *
 * @param unknown $value
 * @param string[] $flags  (den ginete escape, prepei na ine trusted gia apofigi SQL INJECTION)
 * @param string $prependObjType
 * @param number $qlimit
 */
public static function find_node_for_category($search_string, $flags, $prependObjType = false, $qlimit = 30, $search_init = false){

	if (!is_int($qlimit)){
		$qlimit = 30;
	}

	$ss = trim($search_string);

	if (is_array($flags)){
		$flags_str = 'ARRAY[';
		$sep='';
		foreach ($flags as $f){
			$flags_str .= sprintf("%s'%s'",$sep,$f);
			$sep = ', ';
		}
		$flags_str.=']';
	} else {
		$flags_str .= sprintf("ARRAY['%s']",$flags);
	}

	$dbh = dbconnect();
	$rep = array();

	if ( $search_init && (empty($ss) || $ss == 'null') ){
		$SQL =sprintf("SELECT item_id as ref_item, label, prop_int[1] as count, obj_type
		FROM dsd.item2 WHERE flags @> %s AND status in ('finish') AND label not like '%%>%%'
		ORDER BY CHAR_LENGTH(label) ASC  LIMIT %s", $flags_str,  $qlimit);
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
	}else{
		$SQL =sprintf("SELECT item_id as ref_item, label, prop_int[1] as count, obj_type
		FROM dsd.item2, dsd.to_gr_tsquery(?) as q WHERE q @@ %s AND flags && %s AND status in ('finish')
		ORDER BY CHAR_LENGTH(label) ASC  LIMIT %s",PgFtsConstants::COLUMN_STUFF, $flags_str,  $qlimit);
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->execute();
	}

	// 	$SQL =sprintf("SELECT item_id as ref_item, label, prop_int[1] as count, obj_type
	// 			FROM dsd.item2, dsd.to_gr_tsquery(?) as q WHERE q @@ %s AND flags @> %s AND status in ('finish')
	// 			ORDER BY CHAR_LENGTH(label) ASC  LIMIT %s",PgFtsConstants::COLUMN_SUBJECT, $flags_str,  $qlimit);

	// 	$stmt = $dbh->prepare($SQL);
	// 	$stmt->bindParam(1, $ss);
	// 	$stmt->execute();

	$i= 0;
	while ($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		$ref_item = $r['ref_item'];
		$label  = htmlspecialchars_decode($r['label']);
		$count = $r['count'];

		$obj_type = null;
		if ($prependObjType){
			$obj_type = tr('cat_'.$r['obj_type']);
		}

		//$rep[$ref_item] = array('value' => array($ref_item, $label, $count,0,$obj_type));
		$rep[$i] = array('value' => array($ref_item, $label, $count,0,$obj_type));
		$i++;
	}
	return $rep;
}






/**
 *
 * @param unknown $value
 * @param string[] $flags  (den ginete escape, prepei na ine trusted gia apofigi SQL INJECTION)
 * @param string $prependObjType
 * @param number $qlimit
 */
public static function find_node_for_artifact($search_string, $flags, $prependObjType = false, $qlimit = 30){

	if (!is_int($qlimit)){
		$qlimit = 30;
	}

	$ss = trim($search_string);

	if (is_array($flags)){
		$flags_str = 'ARRAY[';
		$sep='';
		foreach ($flags as $f){
			$flags_str .= sprintf("%s'%s'",$sep,$f);
			$sep = ', ';
		}
		$flags_str.=']';
	} else {
		$flags_str .= sprintf("ARRAY['%s']",$flags);
	}

	$dbh = dbconnect();
	$rep = array();
	$SQL =sprintf("SELECT item_id as ref_item, prop_int[1] as count, obj_type, title
			FROM dsd.item2, dsd.to_gr_tsquery(?) as q WHERE q @@ %s AND flags @> %s AND status in ('finish')
			ORDER BY label ASC  LIMIT %s",PgFtsConstants::COLUMN_SUBJECT, $flags_str,  $qlimit);

	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $ss);
	$stmt->execute();
	while ($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		$ref_item = $r['ref_item'];
		// 		$label  = $r['label'];
		$label  = $r['title'];
		$count = $r['count'];

		$obj_type = null;
		if ($prependObjType){
			$obj_type = tr('cat_'.$r['obj_type']);
			// 			$label =  $label.'['. $obj_type.']' ;
		}

		$rep[$ref_item] = array('value' => array($ref_item, $label, $count,0,$obj_type));
	}
	return $rep;
}




public static function find_node_for_link($name, $link_elements, $obj_types, $prependObjType = false, $qlimit = 30){

	$dbh = dbconnect();

	$ss = trim($name);


//$m1 = array();
	$m2 = array();
	$rep = array();


	if (!empty($obj_types)){
		$sql_obj_types = "";
		$sep ="";
		foreach ($obj_types as $el) {
			$sql_obj_types .= $sep . "'$el'";
			$sep = ",";
		}

// 		$SQL = "
// 		SELECT m.item_id as ref_item, m.text_value as value, count(*)
// 		FROM dsd.metadatavalue2 m
// 		LEFT JOIN dsd.relation r ON (m.item_id = r.item_2), dsd.to_gr_tsquery(?) as q
// 		WHERE m.obj_type in  ($sql_obj_types)
// 		AND (m.element = 'dc:title:'  OR m.element = 'ea:title:uniform')
// 		AND  q @@ m.text_value_fst
// 		group by 1, 2 limit $qlimit
// 		";
		$SQL = "
		SELECT m.item_id as ref_item, i.obj_type as obj_type,  m.text_value as value, i.label as label, count(*)
		FROM dsd.metadatavalue2 m
		LEFT JOIN dsd.item2 i ON (m.item_id = i.item_id), dsd.to_gr_tsquery(?) as q
		WHERE m.obj_type in  ($sql_obj_types) AND (m.element = 'dc:title:'  OR m.element = 'ea:title:uniform')
		AND  q @@ m.text_value_fst
		GROUP BY 1, 2, 3, 4
		ORDER BY value ASC
		LIMIT $qlimit
		";

		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->execute();
		while ($r = $stmt->fetch()){
			$ref_item = $r['ref_item'];
			$val = $r['value'];
			$label  = $r['label'];

			$obj_type = null;
			if ($prependObjType){
				$obj_type = tr('cat_'.$r['obj_type']);
			}

			if (!empty($label)){
				$val = htmlspecialchars_decode($label);
			}

			$count = $r['count'];
			$m2[$val] = $ref_item;
			$rep[$ref_item] = array('value' => array($ref_item, $val, $count, 0, $obj_type));
		}

	}

	if (! empty($link_elements)){

		$sql_elements = "";
		$sep ="";
		foreach ($link_elements as $el) {
			$sql_elements .= $sep . "'$el'";
			$sep = ",";
		}


		$SQL = "
		SELECT m.ref_item, m.text_value as value, count(*) as count
		FROM dsd.metadatavalue2 m, dsd.to_gr_tsquery(?) as q
		WHERE m.element in  ( $sql_elements )
		AND m.ref_item is null  AND  q @@ m.text_value_fst
		group by 1, 2  limit $qlimit
		";

		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->execute();

		while ($r = $stmt->fetch()){
			$ref_item = null;
			$val = $r['value'];
			$count = $r['count'];
			if(! isset($m2[$val])){
				$rep[] = array('value' => array($ref_item, $val,0,$count));
			} else {
				$ref_item = $m2[$val];
				$rep[$ref_item]['value'][3] += $count;
			}
		}
	}
	return $rep;

}


public static function find_relation($name,$key, $limit = 30){

// 	'ea:issue-of:'
// 	'ea:relation:other'
// 	'ea:item-of:'

	$dbh = dbconnect();

	$ss = trim($name);

		$SQL = " SELECT i.item_id as ref_item, m.text_value || '  (' || o.label || ')' as value ";
		$SQL .= " FROM dsd.metadatavalue2 m ";
		$SQL .= " JOIN dsd.item2 i ON (i.item_id = m.item_id) ";
		$SQL .= " JOIN dsd.obj_type o ON (i.obj_type = o.name) ";
		$SQL .= " , dsd.to_gr_tsquery(?) as q  ";
		$SQL .= " WHERE m.element = 'dc:title:' ";
		if ($key == 'ea:issue-of:'){
			$SQL .= " AND i.obj_type in ('periodiko', 'efimerida') ";
		}else if ($key == 'ea:item-of:'){
			$SQL .= " AND i.obj_type in ('silogi') ";
		} else {
			$SQL .= " AND i.obj_class in ('manifestation','actor','place','work') ";
		}
		$SQL .= " AND  q @@ m.text_value_fst ";
		$SQL .= " limit " . $limit;

		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->execute();
		$rep = array();
		while ($r = $stmt->fetch()){
			$ref_item = $r['ref_item'];
			$val = $r['value'];
			$count = 0;
			$rep[$ref_item] = array('value' => array($ref_item, $val));
		}

	return $rep;
}



//@DOC: RELATIONS
//@DEPRECATED
public static function delete_relation_values($item_id) {
  throw new Exception('DEPRECATED');
	$dbh = dbconnect();

	//LOGGING
	//Log::info("@@: delete_relation_values: " . $item_id);
	$SQL = 'SELECT * FROM dsd.metadatavalue2  WHERE ref_item = ? AND relation = 1 AND not inferred';
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	//Log::info($stmt->fetchAll($fetch_style = PDO::FETCH_ASSOC));
// 	while ($row = $stmt->fetch($fetch_style = PDO::FETCH_ASSOC)){
// 		Log::info(print_r($row,true));
// 	}

	$SQL = 'DELETE FROM dsd.metadatavalue2  WHERE ref_item = ? AND relation = 1 AND not inferred';
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();

	$dcount = $stmt->rowCount();
	Log::info("@@: delete_relation_values: " . $item_id . ' count: ' . $dcount);

}

#updates submits status
public static function update_submits_status($submit_id,$item_id, $status) {
	$dbh = dbconnect();
	$SQL ="UPDATE dsd.submits set status=?,final_item_id = ? WHERE id = ? ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $status);
	$stmt->bindParam(2, $item_id);
	$stmt->bindParam(3, $submit_id);
	$stmt->execute();
}

public static function update_item2_access($item_id, $user) {
	$dbh = dbconnect();
	$SQL ="UPDATE dsd.item2 set user_update = ?, dt_update = now() WHERE item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $user);
	$stmt->bindParam(2, $item_id);
	$stmt->execute();
}

public static function item_add_flag($item_id, $flag){
	$dbh = dbconnect();
	$SQL = "select dsd.item_add_flag(?, ?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->bindParam(2, $flag);
	$stmt->execute();
}

public static function item_remove_flag($item_id, $flag){
	$dbh = dbconnect();
	$SQL = "select dsd.item_remove_flag(?, ?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->bindParam(2, $flag);
	$stmt->execute();
}

public static function item_add_history($item_id, $user, $data, $wfdata){
	$dbh = dbconnect();
	$nextval = PUtil::nextval($dbh, 'dsd.item2_history_id_seq');

	$SQL="insert into dsd.item2_history (id, item_id, user_name, data, wf_data) values (?,?,?,?,?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $nextval);
	$stmt->bindParam(2, $item_id);
	$stmt->bindParam(3, $user);
	$stmt->bindParam(4, $data);
	$stmt->bindParam(5, $wfdata_text);
	$stmt->execute();
	return $nextval;

}

public static function get_collection_name($dbh, $id){
	$SQL = "SELECT label from dsd.collection2 where id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $id);
	$stmt->execute();
	if($r = $stmt->fetch()){
		return $r[0];
	}
	return null;
}


public static function get_collection_id($dbh, $type){

	$SQL ="SELECT collection from dsd.obj_type  WHERE name=?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $type);
	$stmt->execute();
	if($r = $stmt->fetch()){
		return $r[0];
	}
	return 10;//others

}

public static function insert_collection($dbh, $item_id,$obj_type) {

	$collection_id = PDao::get_collection_id($dbh,$obj_type);
	$collection_name = PDao::get_collection_name($dbh,$collection_id);
	//$obj_class = PDao::get_obj_class_from_obj_type($obj_type);

	$SQL="UPDATE dsd.item2 SET collection = ?, collection_label = ?  WHERE item_id = ?";
	//$SQL="UPDATE dsd.item2 SET collection = ?, collection_label = ? WHERE item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $collection_id);
	$stmt->bindParam(2, $collection_name);
	$stmt->bindParam(3, $item_id);
	//$stmt->bindParam(3, $obj_class);
	// $stmt->bindParam(4, $obj_type);
	// $stmt->bindParam(5, $item_id);
	$stmt->execute();

	return $collection_id;
}


public static function update_metadata($dbh,$item_id, $key, $values, $append = false){
	return PDao::update_item_metadata($item_id, $key, $values, $append);
}


#GENERATED METADATA
public static function insert_generated_metadata($dbh, $item_id, $userName) {
	$now = new DateTime(null, new DateTimeZone('UTC'));
	$dc_date_accessioned = $now->format('Y-m-d\TH:i:s\Z');
	$dc_date_available = $dc_date_accessioned;
	$dc_date_issued = $now->format('Y-m-d');
	$dc_description_provenance = " Submitted by " . $userName . " on " . $dc_date_available . " (UTC)";
	$dc_identifier_uri = sprintf("http://%s/archive/item/%s",Config::get('arc.ARCHIVE_HOST') , $item_id);
	$ea_identifier_id = $item_id;
	PDao::update_metadata($dbh, $item_id, "dc:date:accessioned",ARRAY(ARRAY($dc_date_accessioned,null,null)));
	PDao::update_metadata($dbh, $item_id, "dc:date:available",ARRAY(ARRAY($dc_date_available,null,null)));
	PDao::update_metadata($dbh, $item_id, "dc:date:issued",ARRAY(ARRAY($dc_date_issued,null,null)));
	PDao::update_metadata($dbh, $item_id, "dc:description:provenance",ARRAY(ARRAY($dc_description_provenance,"en",null)));
	PDao::update_metadata($dbh, $item_id, "dc:identifier:uri",ARRAY(ARRAY($dc_identifier_uri,null,null)));
	PDao::update_metadata($dbh, $item_id, "ea:identifier:id",ARRAY(ARRAY($ea_identifier_id,null,null)));
}


public static function item_basic_works($dbh,$item_id,$internal_id, &$out){
	try {
		$SQL ="
		 SELECT i.item_id, b.internal_id, r.mimetype, i.obj_type, b.pages
	     FROM dsd.item2 i
	     JOIN item2bundle ib ON i.item_id = ib.item_id
	     JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
	     JOIN bitstream b ON bb.bitstream_id = b.bitstream_id
	     JOIN public.bitstreamformatregistry r ON r.bitstream_format_id = b.bitstream_format_id
	     WHERE i.item_id = ? AND b.internal_id = ?  ";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->bindParam(2, $internal_id);
		$stmt->execute();
		if ($row = $stmt->fetch())
		{
			$mimetype = $row[2];
			$obj_type = $row[3];
			$pages = $row[4];
			$out .="set document pages: $pages\n";
			PDao::update_item_metadata($item_id, DataFields::ea_edoc_pages ,ARRAY(ARRAY($pages,null,null)));

			if ($mimetype =='application/pdf'){
				#metadata_basic($item_id, $internal_id, $out);
				$out .="set_pdf_metadata_from_db\n";
				PDao::set_pdf_metadata_from_db($dbh,$item_id,$internal_id, $out);
				$out .="pdfinfo\n";
				PUtil::pdfinfo($dbh, $item_id, $internal_id,$out);
			}
			$out.="thumb_generate for item\n";
			PDao::thumb_generate($dbh, $item_id, $internal_id);


//# 			$out.="thumb_generate for bitstream\n";
//# 			$all_flag = false;
//# 			$parent_flag = false;
//# 			thumbs_generate_from_bitstream($dbh, $internal_id, $out,$all_flag,$parent_flag);

			$out .="corect_privs\n";
			PUtil::corect_privs($internal_id);
		}

	}catch(Exception $ex){
		echo("\n=======\n");
		echo($out);
		echo("\n=======\n");
		#echo("ERROR item_basic_works: $ex\n");
		#error_log( $ex, 0);
		echo("ERROR item_basic_works:".  $ex->getMessage() . "\n");
		error_log( $ex->getMessage(), 0);
	}

}

public static function set_pdf_metadata_from_db($dbh,$item_id,$internal_id, &$out){
	$title = null;
	$title_safe = $item_id . ".pdf";
	$SQL = "SELECT title FROM dsd.item2 WHERE item_id = ?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $item_id);
	$stmt->execute();
	if ($row = $stmt->fetch()){
		$title = $row[0];
	}
	if (empty($title)){
		$title = $title_safe;
	}
	if (strlen($title) > 80){
		$title =  substr($title, 0, 80);
	}


	#metadata_basic($item_id, $internal_id, $out);
	PUtil::set_pdf_metadata($item_id, $internal_id, $title, $out);
}

public static function thumb_generate($dbh, $item_id, $internal_id = null){

	if (empty($internal_id)){
		$SQL="SELECT i.pages, i.obj_type, b.internal_id
	     FROM dsd.item2 i
	     JOIN item2bundle ib ON i.item_id = ib.item_id
	     JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
	     JOIN bitstream b ON bb.bitstream_id = b.bitstream_id
	     WHERE  i.item_id = ? AND b.sequence_id = 1";

		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();
		if ($row = $stmt->fetch()){
			$internal_id = $row[2];
		}
	}

	if (empty($internal_id)){
		return null;
	}

	$out = "";
	$rep = PDao::thumbs_generate_from_bitstream($dbh, $internal_id,$out);
	return $rep;
}




public static function change_bundle($bid, $bundle_name, $table = 'bitstream'){
	$dbh = dbconnect();

	if ($table == 'bitstream'){
		$SQL1 = "SELECT item_id from dsd.item_bitstream where  bitstream_id = ?";
		$SQL2 = "SELECT b.bundle_id, b.name  from public.bundle2bitstream bb join bundle b on (b.bundle_id = bb.bundle_id) where bb.bitstream_id = ? AND b.name = ?";
		$SQL3 = " UPDATE public.bundle2bitstream SET bundle_id = ? WHERE bitstream_id = ?";
		$SQL4 = "INSERT into public.bundle2bitstream  (bundle_id, bitstream_id ) values (?,?)";
	} else if ($table == 'content'){
		$SQL1 = "SELECT item_id from dsd.item_content where content_id = ?";
		$SQL2 = "SELECT b.bundle_id, b.name  from public.bundle2content bb join bundle b on (b.bundle_id = bb.bundle_id) where bb.content_id = ? AND b.name = ?";
		$SQL3 = "UPDATE public.bundle2content SET bundle_id = ? WHERE content_id = ?";
		$SQL4 = "INSERT into public.bundle2content  (bundle_id, content_id ) values (?,?)";
	} else {
		throw new Exception("Error Processing Request", 1);
	}

	$item_id = null;
	$stmt = $dbh->prepare($SQL1);
	$stmt->bindParam(1, $bid);
	$stmt->execute();
	if ( $r = $stmt->fetch()){
		$item_id  = $r['item_id'];
	}

	$new_bundle_id = null;
	$new_bundle_name = null;
	$stmt = $dbh->prepare($SQL2);
	$stmt->bindParam(1, $bid);
	$stmt->bindParam(2, $bundle_name);
	$stmt->execute();
	if ($r = $stmt->fetch()){
		$new_bundle_id = $r[0];
	}
	if (empty($new_bundle_id)){
		$new_bundle_id =  PDao::insert_bundle($dbh, $bundle_name);
		if ( !empty($item_id)){
			 PDao::insert_item2bundle($dbh, $item_id, $new_bundle_id);
		}
	}

	$stmt = $dbh->prepare($SQL3);
	$stmt->bindParam(1, $new_bundle_id);
	$stmt->bindParam(2, $bid);
	$stmt->execute();
	$rcount = $stmt->rowCount();
	if ($rcount == 0){
		$stmt = $dbh->prepare($SQL4);
		$stmt->bindParam(1, $new_bundle_id);
		$stmt->bindParam(2, $bid);
		$stmt->execute();
	}
}
	public static function loadSubmit($submit_id) {

		$dbh = dbconnect();
		$rep = array();

		// use ($dbh, &$idata, &$item_id, &$edoc, &$wfdata, &$vivliografiki_anafora, &$agg_type, &$item_collection, &$cd, &$thumb1, &$libgenxml){
		$SQL = "SELECT item_id, data, edoc, wf_data,type,status,final_item_id from dsd.submits where id = ? ";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $submit_id);
		$stmt->execute();
// 		$stmt->bindColumn(1, $item_id);
// 		$stmt->bindColumn(2, $data);
// 		$stmt->bindColumn(3, $edoc);
// 		$stmt->bindColumn(4, $wfdata_text);
// 		$stmt->bindColumn(5, $submits_type);
		// $stmt->bindColumn(5, $data_norm);
		//Log::info("ST:: " + $submits_type);
		if ($rec = $stmt->fetch()) {
			$submits_type = $rec['type'];
			$idata = null;
			$data = $rec['data'];
			if ($submits_type == 2) {
				//Log::info("#2# LOAD FROM SUBMITS");
				$idata = new ItemMetadata();
				$idata->replaceValuesFromClientModels(json_decode($data));
			} else {
				$idata = new ItemMetadata(unserialize($data));
			}
			$rep['idata'] = $idata;
			// print_r(unserialize($data));
			$wfdata = array();
			$wfdata_text = $rec['wf_data'];
			if (! empty($wfdata_text)) {
				if ($submits_type == 2) {
					$wfdata = json_decode($wfdata_text);
				} else {
					$wfdata = unserialize($wfdata_text);
				}
			}
			$rep['wfdata'] = $wfdata;
			$rep['status'] = $rec['status'];
			$rep['item_id'] = $rec['item_id'];
			$rep['final_item_id'] = $rec['final_item_id'];
			$rep['edoc'] = $rec['edoc'];
			$rep['submit_id']= $submit_id;
		}


		return $rep;
	}


	public static function getThumbs($item_id) {

		$dbh = dbconnect ();
		$SQL = "SELECT  i.thumb, i.thumb1, i.thumb2  FROM dsd.item2 i  WHERE item_id = $item_id ";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->execute ();
		$thumbs = array();
		if ($r = $stmt->fetch ()) {
			if ( !empty($r[0]) && !empty($r[1]) && !empty($r[2]) ) {
			$thumbs = array('thumb'=>$r[0],'thumb1'=>$r[1],'thumb2'=>$r[2]);
			}
		}
		return $thumbs;

	}


	/**
	 * @param string $log_entry
	 * @param string $details
	 * @param string $severity
	 */
	public static final function ruleengine_log($log_entry,$details,$severity = 'log'){
		$dbh = prepareService();
		$SQL = 'INSERT INTO dsd.ruleengine_log ("serverity" , "log_entry","details") values (?,?,?)';
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam(1, $severity);
		$stmt->bindParam(2, $log_entry);
		$stmt->bindParam(3, $details);
		$stmt->execute ();

	}


	/**
	 * @param string $type_act
	 * @param string $user
	 * @param string $url
	 */
	public static function activity_log($type_act, $user, $url){
		$dbh = dbconnect();
		$SQL = "INSERT INTO dsd.activity_log ( type, event_ts, user_name, url ) VALUES (?,now(),?,?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1,$type_act);
		$stmt->bindParam(2,$user);
		$stmt->bindParam(3,$url);
		$stmt->execute();
	}



}
?>