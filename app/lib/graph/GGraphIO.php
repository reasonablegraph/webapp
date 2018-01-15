<?php
class GGraphIORowProc {

	const METADATA_VALUE_FIELDS = "metadata_value_id, item_id, element, ref_item, text_value, text_lang, link, lid, inferred,data::text, weight, level, relation";
	const METADATA_VALUE_ORDER_BY = 'ORDER BY item_id, level, weight';

	/**
	 * @var GGraphO
	 */
	private $g;

	private $p_item_id = null;

	/**
	 * @param GGraphO $g
	 */
	public function __construct($g) {
		//Log::info('@:: GGraphIORowProc: __construct');
		$this->g = $g;
	}


	public function finishMetadataValue() {
		//Log::info('@:: GGraphIORowProc: finishMetadataValue');
	}

	public function graph() {
		return $this->g;
	}

	public function processMetadataValueRow($row, $nd = 0) {
		//Log::info('@:: GGraphIORowProc: processMetadataValueRow: ' . $nd);

		if ($this->p_item_id == null) {
			$this->p_item_id = $row['item_id'];
		}
		$p = GPropertyO::withRow($row);
		//Log::info('addToGraph: ' .  $p->itemId() .  ' | ' . $p->level() .  " | " . $p->element() );
		$v = $this->g->_addProperty($p);
		if ($this->p_item_id != $row['item_id']) {
			$this->p_item_id = $row['item_id'];
		}

		if (!empty($v) && empty($v->getTmpAttribute('_ND'))){
			$v->setTmpAttribute('_ND', $nd);
//			if ($nd > 1){
//				$v->setReadOnly();
//				//Log::info("@:: SET READONLY: "  . $nd . ' : ' . $v->id() .   " : " . ($v->isReadOnly() ? 'TRUE' : 'FALSE'));
//			}
		}

	}


}

/**
 *
 * @author kostas
 *
 */
class GGraphIO {


	public static function addItemToGraph($graph, $item_id) {
		//Log::info("##addItemToGraph");
// 		$elements = Config::get('arc_rules.DEFAULT_GRAPH_ROOT_ELEMENTS_LOAD', array('dc:title:','ea:obj-type:','ea:status:' ));
// 		$sep = '';
// 		$elementsString = '';
// 		foreach ( $elements as $e ) {
// 			$elementsString .= $sep . "'" . $e . "'";
// 			$sep = ', ';
// 		}
		$elementsString = null;

		$con = dbconnect();
		$proc = new GGraphIORowProc($graph);
		$SQL = sprintf("SELECT %s FROM dsd.metadatavalue2
				WHERE   item_id = ?
				AND (element in (%s) OR ref_item is not null)
				--AND link is null
				%s
				", GGraphIORowProc::METADATA_VALUE_FIELDS, $elementsString, GGraphIORowProc::METADATA_VALUE_ORDER_BY);
		Log::info($SQL);
		$st = $con->prepare($SQL);
		$st->bindParam(1, $item_id);
		$st->execute();
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			// Log::info($row['item_id'] . " | " . $row['element'] . " | " . $row['ref_item']);
			// echo("###: " + $row['item_id'] . " | " . $row['element'] . " | " . $row['ref_item']);
			$proc->processMetadataValueRow($row);
		}
		$proc->finishMetadataValue();
	}


	/**
	 *
	 * @param Long[] $ids
	 * @param GGraph $graph
	 */
	public static function loadGraph($graph = null, $inferredFlag = false,$only_test_nodes=false) {
		//Log::info('##loadGraph:');
		if ($graph == null) {
			$graph = new GGraphO();
		}
		$elementsString = null;
// 		if (false){
// 			$elements = Config::get('arc_rules.DEFAULT_GRAPH_ROOT_ELEMENTS_LOAD', array('dc:title:','ea:obj-type:','ea:status:' ));
// 			$sep = '';
// 			$elementsString = '';
// 			foreach ( $elements as $e ) {
// 				$elementsString .= $sep . "'" . $e . "'";
// 				$sep = ', ';
// 			}
// 		}

		$ots = Config::get('arc_rules.DEFAULT_GRAPH_OBJ_CLASSES', array('manifestation','actor' ));
		$sep = '';
		$otsString = '';
		foreach ( $ots as $ot ) {
			$otsString .= $sep . "'" . $ot . "'";
			$sep = ', ';
		}

		$con = dbconnect();
		$proc = new GGraphIORowProc($graph);

		if (!$only_test_nodes) {
			$only_test_nodes_query = '';
		} else{
			$only_test_nodes_query = "AND item_id in (SELECT i.item_id FROM dsd.item2 i JOIN dsd.metadatavalue2 v ON (i.item_id = v.item_id AND v.element = 'ea:test:key1' )  )";
		}

		$elementsStringOk = '';
		if ($elementsString){
			$elementsStringOk = sprintf(' AND element in (%s) ',$elementsString);
		}
			//--AND link is null
		$SQL = sprintf("SELECT %s FROM dsd.metadatavalue2
				WHERE obj_class in (%s) 
				%s
				%s
				%s
				", GGraphIORowProc::METADATA_VALUE_FIELDS, $otsString, $elementsStringOk,$only_test_nodes_query, GGraphIORowProc::METADATA_VALUE_ORDER_BY);

		//Log::info($SQL);
		$st = $con->prepare($SQL);
		$st->execute();
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			//Log::info($row['item_id'] . " | " . $row['element'] . " | " . $row['ref_item']);
			$proc->processMetadataValueRow($row);
		}
		//Log::info('##loadGraph: finishMetadataValue');

		$proc->finishMetadataValue();


		$loadInferneceFromJsonFlag = Config::get('arc.GRAPH_LOAD_INFERENCE_FROM_JSON', 0) >0;

		$JOIN = $only_test_nodes ? "JOIN dsd.metadatavalue2 v ON (i.item_id = v.item_id AND v.element = 'ea:test:key1' )" : '';
			$SQL = sprintf("SELECT i.item_id,i.jdata FROM dsd.item2 i %s
					WHERE i.obj_class in (%s) AND i.status <> 'error'
					", $JOIN, $otsString);
			Log::info($SQL);
			$st = $con->prepare($SQL);
			$st->execute();
			while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$v = $graph->getVertex(GURN::createOLDWithId($row['item_id']));

				if (!$inferredFlag){
					$v->_setAttributes(json_decode($row['jdata'],true));
				} else {
					$jdata = json_decode($row['jdata'],true);
					//
					$v->_setAttributes($jdata);

					if ($loadInferneceFromJsonFlag && isset($jdata['edges_out'])){
						//Log::info(print_r($jdata['edges_out'],true));
						foreach ($jdata['edges_out'] as $e){
							$edge = $graph->addEdge(GURN::createOLDWithId($e['from'])->toString(), GURN::createOLDWithId($e['to'])->toString(), $e['element']);

// 							if (empty($edge)){
// 								Log::info("SKIP ADD EDGE FROM JDATA " . $e['from']. '--[' . $e['element'] . '-->' . $e['to']);
// 							} else {
// 								Log::info("ADD EDGE FROM JDATA: " . $edge);
// 							}

						}
					}
				}
			}



		$vertices = $graph->getVertices();
		/* @var $v GVertex */
		foreach ($vertices as $v){
			$status = $v->getPropertyValue('ea:status:');
			if ($status == 'error'){
				Log::info("@@: REMOVE VERTEX status error:  " . $v->urnStr());
				$graph->removeVertex($v->urnStr());
//			}elseif (!$v->hasProperties()){
//				Log::info("@@: REMOVE ZERO VERTEX:  " . $v->urnStr());
//				$graph->removeVertex($v->urnStr());
			}
		}



		// $count = $graph->countVertices();
		// Log::info("LOAD $count VERTICES");
		return $graph;
	}

	// private static function loadNodeSubGraphIds($item_ids){

	// $con = dbconnect();

	// $sep='';
	// $q = 'in (';
	// foreach ($item_ids as $e){
	// $id = intval($e);
	// $q = $q . $sep . $id;
	// $sep = ', ';
	// }
	// $q1 = $q . ')';

	// $SQL = sprintf("SELECT ref_item FROM dsd.metadatavalue2 WHERE item_id %s AND ref_item is not null",$q1) ;

	// Log::info($SQL);
	// $st = $con->prepare($SQL);
	// $st->execute();
	// while($row = $st->fetch(PDO::FETCH_ASSOC)){
	// $id = $row['ref_item'];
	// if (!in_array($id,$item_ids)){
	// $q = $q . $sep . $id;
	// }
	// }
	// $q = $q . ')';
	// Log::info("Q: $q");
	// return $q;
	// }
	private static function loadNodeSubGraphIds($item_ids) {
		if (empty($item_ids)) {
			return null;
		}
		$con = dbconnect();

		$sep = '';
		$q = 'in (';
		foreach ( $item_ids as $e ) {
			$id = intval($e);
			$q = $q . $sep . $id;
			$sep = ', ';
		}
		$q1 = $q . ')';

		//Log::info("XX###: " . implode(', ', $item_ids));
		//Log::info("XX###: Q1: $q1");
		$SQL = sprintf('SELECT  ref_item FROM dsd.metadatavalue2 WHERE item_id  %s AND ref_item is not null ', $q1); // AND link is null
		                                                                                                            // $c = 0;
		//Log::info($SQL);
		$st = $con->prepare($SQL);
		// $st->bindParam ( 1, $item_id );
		$st->execute();
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			// $c+=1;
			$id = $row['ref_item'];
			$q = $q . $sep . $id;
			$sep = ', ';
		}
		$q = $q . ')';
	//	Log::info("XX###: #Qf1: ".$q);

		// if ($c == 0){
		// return null;
		// }
		return $q;
	}



	public static function getNeighbourhoodIds($item_id) {
		Log::info('getNeighbourhoodIds: ' . $item_id);
		$SQL = "SELECT jdata->'neighbourhood' as neighbourhood from dsd.item2 WHERE item_id = ?";
		$con = dbconnect();
		$st = $con->prepare($SQL);
		$st->bindParam(1, $item_id);
		$st->execute();
		if ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			$rep = $row['neighbourhood'];
			return json_decode($rep);
		}

		return null;
	}



	/**
	 * @param integer $item_id
	 * @param integer[] $item_refs
	 * @param GGraphO $graph
	 * @param string $action
	 * @return GGraphO|null
	 * @throws Exception
	 */
	public static function loadNodeNeighbourhood($item_id, $item_refs = null, $graph = null, $action = null) {
	//	PUtil::logRed('@:: loadNodeNeighbourhood: ' . $item_id . ' : action: ' . $action . (empty($item_refs) ? '' : ' : ' . implode(',',$item_refs)));

		$item_id = (int)$item_id;

		$con = dbconnect();
		//$con =  prepareService();

		if ($graph == null) {
			$graph = new GGraphO();
		}
		if ($item_refs == null) {
			$item_refs = array();
		}


		$proc = new GGraphIORowProc($graph);

		$elementsString = null;

		$ots = Config::get('arc_rules.DEFAULT_GRAPH_OBJ_CLASSES', array('manifestation','actor' ));
		$sep = '';
		$otsString = '';
		foreach ( $ots as $ot ) {
			$otsString .= $sep . "'" . $ot . "'";
			$sep = ', ';
		}

		if (! empty($item_id) && ! in_array($item_id,$item_refs)) {
			$item_refs[] = $item_id;
		}

		$item_refs_csv = implode(', ', $item_refs);
		//Log::info('loadNodeNeighbourhood: ' . $item_id . ' : ' . $item_refs_csv);
		$q = 'in (' . $item_refs_csv .')';

		$elementsStringOk = '';
		if ($elementsString){
			$elementsStringOk = sprintf(' AND element in (%s) ',$elementsString);
		}

		if ( empty($q)) { throw new Exception("empty q");}
		//Log::info("#q: " . $q);
		$SQL = sprintf("
			SELECT %s FROM dsd.metadatavalue2
			WHERE obj_class in (%s)  %s
			AND  item_id  in (
			SELECT  distinct neigh FROM(  (SELECT json_array_elements(jdata->'neighbourhood'->'def')::varchar::bigint as neigh from dsd.item2 WHERE item_id %s)
				UNION (SELECT '%s'::bigint as neigh)
				UNION (SELECT ref_item as neigh from dsd.metadatavalue2 where item_id = %s and ref_item is not null and not inferred) ) as foo
			) %s
			" , GGraphIORowProc::METADATA_VALUE_FIELDS, $otsString, $elementsStringOk, $q, $item_id,$item_id, GGraphIORowProc::METADATA_VALUE_ORDER_BY);
//			Log::info("@:: #item_id: " . $item_id);
//	    Log::info("@:: #item_refs: " . implode(',' , $item_refs));
//			Log::info('@:: #SQL1: ' . $SQL);
		$st = $con->query($SQL);
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			//Log::info($row['item_id'] . " | " . $row['element'] . " | " . $row['ref_item'] . ' | ' . $row['text_value']);
			$proc->processMetadataValueRow($row,1);
		}

		//SELECT distinct json_array_elements(jdata->'neighbourhood'->'def')::varchar::bigint as neigh from dsd.item2 WHERE item_id in ( SELECT json_array_elements(jdata->'neighbourhood'->'def')::varchar::bigint as neigh from dsd.item2 WHERE item_id =106);

		$SQL = sprintf("
			SELECT %s FROM dsd.metadatavalue2
			WHERE obj_class in (%s)  %s
			AND  item_id  in (
			SELECT  distinct neigh FROM(
				SELECT json_array_elements(jdata->'neighbourhood'->'item')::varchar::bigint as neigh from dsd.item2 WHERE item_id in
				((SELECT json_array_elements(jdata->'neighbourhood'->'item')::varchar::bigint as neigh from dsd.item2 WHERE item_id %s)
				UNION (SELECT '%s'::bigint as neigh)
				--UNION (SELECT ref_item as neigh from dsd.metadatavalue2 where item_id = %s and ref_item is not null and not inferred)
				)) as foo
			) %s
			" , GGraphIORowProc::METADATA_VALUE_FIELDS, $otsString, $elementsStringOk, $q, $item_id,$item_id, GGraphIORowProc::METADATA_VALUE_ORDER_BY);
//			  Log::info("@:: #item_id: " . $item_id);
//			  Log::info("@:: #item_refs: " . implode(',' , $item_refs));
//				Log::info('@:: #SQL2: ' . $SQL);
		$st = $con->query($SQL);
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			//Log::info($row['item_id'] . " | " . $row['element'] . " | " . $row['ref_item'] . ' | ' . $row['text_value']);
			$proc->processMetadataValueRow($row,2);
		}


		$proc->finishMetadataValue();


		$SQL = sprintf("
			SELECT item_id,jdata, flags_json FROM dsd.item2
			WHERE obj_class in (%s)
			AND  item_id  in (
			SELECT  distinct neigh FROM(  (SELECT json_array_elements(jdata->'neighbourhood'->'def')::varchar::bigint as neigh from dsd.item2 WHERE item_id %s)
				UNION (SELECT '%s'::bigint as neigh)
				UNION (SELECT ref_item as neigh from dsd.metadatavalue2 where item_id = %s and ref_item is not null and not inferred) ) as foo
			)
			" , $otsString, $q, $item_id,$item_id);
			//Log::info("@:: #SQL3: " . $SQL);
		$st = $con->query($SQL);
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			$v = $graph->getVertex(GURN::createOLDWithId($row['item_id']));
			$v->_setAttributes(json_decode($row['jdata'],true));
			if (!empty($row['flags_json'])) {
				$v->setFlags(json_decode($row['flags_json'],true));
			}
		}



		$SQL = sprintf("
			SELECT item_id,jdata, flags_json FROM dsd.item2
			WHERE obj_class in (%s) AND status <> 'error'
			AND  item_id  in (
			SELECT  distinct neigh FROM(
				SELECT json_array_elements(jdata->'neighbourhood'->'item')::varchar::bigint as neigh from dsd.item2 WHERE item_id in
				((SELECT json_array_elements(jdata->'neighbourhood'->'item')::varchar::bigint as neigh from dsd.item2 WHERE item_id %s)
				UNION (SELECT '%s'::bigint as neigh)
				--UNION (SELECT ref_item as neigh from dsd.metadatavalue2 where item_id = %s and ref_item is not null and not inferred)
				)) as foo
			)
			" , $otsString, $q, $item_id, $item_id);
//			Log::info('@:: #SQL4: ' . $SQL);
//			Log::info("@:: #PARAMS: " . $otsString . ' : ' . $q . ' : ' . $item_id . ' : ' . $item_id);
// 		$st = $con->prepare($SQL);
// 		$st->execute();
		$st = $con->query($SQL);
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			$v = $graph->getVertex(GURN::createOLDWithId($row['item_id']));
			$v->_setAttributes(json_decode($row['jdata'],true));
			if (!empty($row['flags_json'])) {
				$v->setFlags(json_decode($row['flags_json'],true));
			}
		}

		$vroot = $graph->getVertex(GURN::createOLDWithId($item_id));
		if ($vroot){
			$vroot->setTmpAttribute('_ND', 'ROOT');
		}


		$vertices = $graph->getVertices();
		/* @var $v GVertex */
		foreach ($vertices as $v){
			$status = $v->getPropertyValue('ea:status:');
			if ($status == 'error'){
				Log::info("@@: REMOVE VERTEX status error:  " . $v->urnStr());
				$graph->removeVertex($v->urnStr());
			}elseif (!$v->hasProperties()){
				Log::info("@@: REMOVE ZERO VERTEX:  " . $v->urnStr());
				$graph->removeVertex($v->urnStr());
			} else {
				$nd = $v->getTmpAttribute('_ND');
				if (!empty($nd) && $nd != 'ROOT' && $nd > 1 && (!in_array($v->id(),$item_refs)) ){
					$v->setReadOnly();
				}
			}
		}

		//GGraphUtil::removeZeroVertices($graph);
		return $graph;
	}

	/**
	 *
	 * @param integer $item_id
	 * @param integer $item_ids
	 * @param GGraphO $graph
	 * @return GGraphO
	 */
	public static function loadNodeSubGraph($item_id, $item_refs = null, $graph = null) {
		$con = dbconnect();
		//Log::info("##loadNodeSubGraph: ");

		if ($graph == null) {
			$graph = new GGraphO();
		}
		if ($item_refs == null) {
			$item_refs = array();
		}

		$proc = new GGraphIORowProc($graph);

		// $q = GGraphIO::loadNodeSubGraphIds($item_ids);

		$elementsString = null;
// 		if (false){
// 			$elements = Config::get('arc_rules.DEFAULT_GRAPH_ROOT_ELEMENTS_LOAD', array('dc:title:','ea:obj-type:','ea:status:' ));
// 			$sep = '';
// 			$elementsString = '';
// 			foreach ( $elements as $e ) {
// 				$elementsString .= $sep . "'" . $e . "'";
// 				$sep = ', ';
// 			}
// 		}

		$ots = Config::get('arc_rules.DEFAULT_GRAPH_OBJ_CLASSES', array('manifestation','actor' ));
		$sep = '';
		$otsString = '';
		foreach ( $ots as $ot ) {
			$otsString .= $sep . "'" . $ot . "'";
			$sep = ', ';
		}

		// $SQL = sprintf("
		// SELECT %s FROM dsd.metadatavalue2
		// WHERE obj_class in ('manifestation','actor')
		// AND link is null
		// AND (element in (%s) OR ref_item is not null)
		// AND (
		// item_id %s
		// OR item_id in (SELECT item_id FROM dsd.metadatavalue2 WHERE ref_item %s)
		// OR item_id in (SELECT ref_item FROM dsd.metadatavalue2 WHERE item_id %s AND ref_item is not null)
		// ) %s
		// ",GGraphIORowProc::METADATA_VALUE_FIELDS,$elementsString, $q,$q,$q, GGraphIORowProc::METADATA_VALUE_ORDER_BY) ;

		if (! empty($item_id)) {
			$item_refs[] = $item_id;
		}
		$q = GGraphIO::loadNodeSubGraphIds($item_refs);
		// $SQL = sprintf("SELECT %s FROM dsd.load_sub_graph(?,%s)",GGraphIORowProc::METADATA_VALUE_FIELDS, $elementsString);

		$elementsStringOk = '';
		if ($elementsString){
			$elementsStringOk = sprintf(' AND element in (%s) ',$elementsString);
		}

		if (! empty($q)) {
			//--AND link is null
			//AND (element in (%s) OR ref_item is not null)

			if (empty($item_id)) {
				//Log::info("#q1");
				$SQL = sprintf("
						SELECT %s FROM dsd.metadatavalue2
						WHERE obj_class in (%s)
						%s
						AND (
							item_id %s
						) %s
				", GGraphIORowProc::METADATA_VALUE_FIELDS, $otsString, $elementsStringOk, $q, GGraphIORowProc::METADATA_VALUE_ORDER_BY);
			} else {
				//Log::info("#q2");
				$SQL = sprintf("
				SELECT distinct * FROM (
					(
						SELECT %s FROM dsd.metadatavalue2
						WHERE obj_class in (%s)
						%s
						AND (
							item_id = ?
							OR item_id in (SELECT item_id FROM dsd.metadatavalue2 WHERE ref_item  = ?)
							)
					) UNION (
						SELECT %s FROM dsd.metadatavalue2
						WHERE obj_class in (%s)
						%s
						AND (
							item_id %s
						)
					)
				) as foo %s

				", GGraphIORowProc::METADATA_VALUE_FIELDS, $otsString, $elementsStringOk, GGraphIORowProc::METADATA_VALUE_FIELDS, $otsString, $elementsStringOk, $q,
						// $q,
						GGraphIORowProc::METADATA_VALUE_ORDER_BY);
			}
		} else {
			//Log::info("#q3");
			$SQL = sprintf("
						SELECT %s FROM dsd.metadatavalue2
						WHERE obj_class in (%s)
						%s
						AND (
							item_id = ?
							OR item_id in (SELECT item_id FROM dsd.metadatavalue2 WHERE ref_item  = ?)
							) %s
				", GGraphIORowProc::METADATA_VALUE_FIELDS, $otsString, $elementsStringOk, GGraphIORowProc::METADATA_VALUE_ORDER_BY);
		}

//		Log::info("#@ SQL: " . $SQL);
//		Log::info("#@ item_id: " . $item_id);
//		Log::info("#@ item_refs: " . implode(',' , $item_refs));
		$st = $con->prepare($SQL);
		if (! empty($item_id)) {
			$st->bindParam(1, $item_id);
			$st->bindParam(2, $item_id);
		}
		$st->execute();
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			// Log::info($row['item_id'] . " | " . $row['element'] . " | " . $row['ref_item']);
			// Log::info($row['item_id'] . " | " . $row['element']. " | " . $row['text_value']);
			//Log::info("LOAD: ". $row['item_id'] . " | " . $row['element']);
			$proc->processMetadataValueRow($row);
		}

		$proc->finishMetadataValue();
		// return $proc->graph();

		GGraphUtil::removeZeroVertices($graph);
		return $graph;
	}

	/**
	 *
	 * @param integer $item_id
	 * @param integer $item_ids
	 * @param GGraphO $graph
	 * @return GGraphO
	 */
	public static function loadNodeSubGraphFull($item_id, $item_refs = null, $graph = null) {
		$con = dbconnect();
		//Log::info("loadNodeSubGraphFull: " . $item_id);

		if ($graph == null) {
			$graph = new GGraphO();
		}
		if ($item_refs == null) {
			$item_refs = array();
		}
		if (! empty($item_id)) {
			$item_refs[] = $item_id;
		}

		$proc = new GGraphIORowProc($graph);

// 		$elements = Config::get('arc_rules.DEFAULT_GRAPH_ROOT_ELEMENTS_LOAD', array('dc:title:','ea:obj-type:','ea:status:' ));
// 		$sep = '';
// 		$elementsString = '';

// 		foreach ( $elements as $e ) {
// 			$elementsString .= $sep . "'" . $e . "'";
// 			$sep = ', ';
// 		}
		$elementsString = null;

		$ots = Config::get('arc_rules.DEFAULT_GRAPH_OBJ_CLASSES', array('manifestation','actor' ));
		$sep = '';
		$otsString = '';
		foreach ( $ots as $ot ) {
			$otsString .= $sep . "'" . $ot . "'";
			$sep = ', ';
		}

		$sep = '';
		$q = '';
		foreach ( $item_refs as $id ) {
			$q .= $sep . $id;
			$sep = ', ';
		}

		$SQL = sprintf("
			SELECT %s FROM dsd.metadatavalue2
			WHERE obj_class in (%s)
			--AND link is null
			AND (element in (%s) OR ref_item is not null)
			AND (
				item_id in (%s)
				OR item_id in (SELECT item_id FROM dsd.metadatavalue2 WHERE ref_item  in (%s))
			) %s
			", GGraphIORowProc::METADATA_VALUE_FIELDS, $otsString, $elementsString, $q, $q, GGraphIORowProc::METADATA_VALUE_ORDER_BY);

		//Log::info("#SQL: " . $SQL);
		// Log::info("#items: " . print_r($item_refs,true));
		$st = $con->prepare($SQL);
		$st->execute();
		while ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			// Log::info($row['item_id'] . " | " . $row['element'] . " | " . $row['ref_item']);
			$proc->processMetadataValueRow($row);
		}

		$proc->finishMetadataValue();

		return $graph;
	}
}
