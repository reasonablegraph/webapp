<?php

class GGraphUtil {





	public static function removeZeroVertices($graph){
		//Log::info("removeZeroVertices");
		$vertices = $graph->getVertices();
		/* @var $v GVertex */
		foreach ($vertices as $v){
			if (!$v->hasProperties()){
				//Log::info("@@: REMOVE ZERO VERTEX:  " . $v->urnStr());
				$graph->removeVertex($v->urnStr());
			}
		}
	}

	/**
	 *
	 * @param GNode $n
	 * @param string[] $elements
	 */
	public static function saveProperties($n,$elements){
		//Log::info("SAVE PROPERTIES " . $n->urn() . "\n");
		foreach ($elements as $el){
			$props = $n->getElementProperties($el);
			foreach ($props as $p){
				Log::info(sprintf("> %s : %s : %s \n",$n->urnStr(),$el,$p->value()));
			}
		}
	}


	/**
	 * @param GGraphO $graph
	 * @param  callable $callback
	 * @throws Exception
	 */
	public static function saveUpdatesTracker($graph,$callback =null){
		//$con = dbconnect();
		$con = prepareService();
// 		$deletesTracker = $graph->_getDeletesTracker();
// 		$SQL = sprintf("DELETE FROM dsd.metadatavalue2 WHERE metadata_value_id = ?");
// 		$st = $con->prepare($SQL);
// 		foreach ($deletesTracker as $delete){

// 		}



		$insertsTracker = $graph->_getInsertTracker();
		foreach ($insertsTracker as $insert){
			/* @var $e GEdge */
			$e = $insert['edge'];
			Log::info("saveUpdatesTracker: INSERT EDGE: " . $e->urnStr() );
			GGraphUtil::saveEdge($e);
		}

		$updatesTracker = $graph->_getUpdatesTracker();

		$SQLDebug = sprintf("SELECT  item_id, element,text_value,inferred,ref_item,relation FROM dsd.metadatavalue2 WHERE metadata_value_id = ?");
		$stDebug = $con->prepare($SQLDebug);


		$SQL1 = sprintf("DELETE FROM dsd.metadatavalue2 WHERE metadata_value_id = ?");
		$stDelete = $con->prepare($SQL1);

		$SQL2 = sprintf("UPDATE dsd.metadatavalue2 SET metadata_field_id = null, text_value = ?, data=?, element=? where metadata_value_id = ?");
		$stUpdate = $con->prepare($SQL2);

		foreach ($updatesTracker as $update){
			$method = isset($update['method'])? $update['method'] : 'update';
			/*@var $p GPropertyO */
			$p = $update['prop'];
			$id = $p->id();
			if (empty($id)){ continue; }
			if ($method == 'delete'){
				$stDebug->bindParam(1, $id);
				$stDebug->execute();
				if ($row = $stDebug->fetch()){
					if (!empty($callback)){ $callback('DELETE',$p);}
// 					$inferred = $row['inferred'];
// 					if ($inferred ){
						Log::info('saveUpdatesTracker: DELETE PROP: ' . $id . ' : '  .  $row['item_id'] .  ' : ' .  $row['element'] . ' : ' . $row['text_value'] . ' : ' . $row['ref_item'] . ' : ' . ($row['inferred']? 'INF' : 'NON'));
						if ($p->element() != $row['element']){ throw new Exception("DELETE PROP ELEMENT NOT MATCH: " . $p->element() . ' <> ' . row['element']);};
						if ($p->refItem() != $row['ref_item']){ throw new Exception("DELETE PROP ELEMENT NOT MATCH: " . $p->refItem() . ' <> ' . row['ref_item']);};
						$stDelete->bindParam(1, $id);
						$stDelete->execute();
// 					} else {
// 						Log::info('saveUpdatesTracker: TRY TO DELETE NOT INFERRED PROP: ' . $id . ' : '  .  $row['item_id'] .  ' : ' .  $row['element'] . ' : ' . $row['text_value'] . ' : ' . $row['ref_item'] . ' : ' . ($row['inferred']? 'INF' : 'NON'));
// 					}
				} else {
					Log::info('saveUpdatesTracker: DELETE PROP: NOT FOUND ' . $id . ' [' .  $p->itemId() . ' : ' . $p->element() . ' :' . $p->refItem() . ' : ' . $p->inferred() . ']');
				}
			} else {
				if (!empty($callback)){  $callback('UPDATE',$p); }

				$value =$p->value();
				$data = $p->data();
				$element = $p->element();
				//Log::info("saveUpdatesTracker: UPDATE PROP: " . $id . " : " . $element . ' : ' . $value);
				$stUpdate->bindParam(1, $value);
				$stUpdate->bindParam(2, $data);
				$stUpdate->bindParam(3, $element);
				$stUpdate->bindParam(4, $id);
				$stUpdate->execute();
			}
		}



	}


	/**
	 *
	 * @param GGraphO $graph
	 */
	public static function saveVertexUpdatesTracker($graph){
		//$con = dbconnect();
		//Log::info("saveVertexUpdatesTracker");
			$updatesTracker = $graph->_getVerticesUpdatesTracker();
		//Log::info(print_r($updatesTracker,true));
		if (! empty($updatesTracker)){
// 					$SQL = sprintf("DELETE FROM dsd.metadatavalue2 WHERE (item_id,ref_item) in ?");
// 					$stDelete1= $con->prepare($SQL);
// 					$SQL = sprintf("DELETE FROM dsd.item2 WHERE item_id = ?");
// 					$stDelete2= $con->prepare($SQL);
			foreach ($updatesTracker as $id => $update){
				$method = isset($update['method'])? $update['method'] : 'update';
				//Log::info("saveVertexUpdatesTracker: ". $id . " METHOD: " . $method);
				/** @var $v GVertex **/
				//$v = $update['vertex'];
				//$id = $v->persistenceId();
				if ($method == 'delete' && !empty($id)){
					Log::info("saveUpdatesTracker: DELETE VERTEX: " . $id);
					PDAO::delete_item($id);
				}
			}
		}


	}

// 	public static function saveUpdatesTracker($vertices){

// 		$con = dbconnect();
// 		$SQL = sprintf("UPDATE dsd.metadatavalue2 SET text_value = ?, data=? where metadata_value_id = ? AND item_id = ?");
// 		$st = $con->prepare($SQL);

// 		/*@var $v GVertexO */
// 		foreach ($vertices as $v){
// 			$updatesTracker  = $v->_getUpdatesTracker();
// 			foreach ($updatesTracker as $update){

// 				//array('element'=>$element, 'idx'=>$idx,'prop'=>$p);
// 				/*@var $p GPropertyO */
// 				$p = $update['prop'];

// 				$id = $p->id();
// 				$lid = $p->treeId();
// 				$value =$p->value();
// 				$itemId=  $v->persistenceId();
// 				$data = $p->data();
// 				if (!empty($id)){
// 					//Log::info("saveUpdatesTracker: UPDATE dsd.metadatavalue2 " . $itemId .  ' : ' . $lid . " : " . $p->element() . ' : ' . $value);
// 					$st->bindParam(1, $value);
// 					$st->bindParam(2, $data);
// 					$st->bindParam(3, $id);
// 					$st->bindParam(4, $itemId);
// 					$st->execute();
// 				}
// // 				elseif (!empty($lid)){
// // 					$SQL1 = sprintf("UPDATE dsd.metadatavalue2 SET text_value = ?, data=? where element = ? AND item_id = ? and lid=?");
// // 					$st1 = $con->prepare($SQL);
// // 					$element = $p->element();
// // 					//Log::info("saveUpdatesTracker: UPDATE dsd.metadatavalue2 " . $itemId .  ' : ' . $lid . " : " . $p->element() . ' : ' . $value);
// // 					$st1->bindParam(1, $value);
// // 					$st1->bindParam(2, $data);
// // 					$st1->bindParam(3, $element);
// // 					$st1->bindParam(4, $itemId);
// // 					$st1->bindParam(5, $lid,PDO::PARAM_INT);
// // 					$st1->execute();
// // 				}
// 				else {
// 					Log::info("CANOT UPDATE PROPERTY " . print_r($p,true));
// 				}
// 			}
// 		}

// 	}


	/**
	 * @param GEdgeO $edge
	 */
	public static function saveEdge($edge){
		//Log::info("saveEdge: " . $edge);
		//$con = dbconnect();
		$el = $edge->element();
		$v1 = $edge->getVertexFrom();
		$v2 = $edge->getVertexTo();
		if($v1->urn()->temporalType() != GURN::TEMPORAL_TYPE_OLD){
			throw new Exception('saveEdge, v1 is not TEMPORAL_TYPE_OLD');
		}
		if($v2->urn()->temporalType() != GURN::TEMPORAL_TYPE_OLD){
			throw new Exception('saveEdge, v2 is not TEMPORAL_TYPE_OLD');
		}

		$id1 = $v1->persistenceId();
		$id2 = $v2->persistenceId();

		$isInfered  = $edge->isInferred();
		$deps = $edge->getDependencies();



		$label = GRuleUtil::getLabel($v2);
		//$label = $edge->label();
		$edge->setLabel($label);
		$data = json_encode(ARRAY('data'=>ARRAY('ref_label'=>$label)));

		GGraphUtil::saveEdgeRaw($id1,$el,$id2,$isInfered,$deps,$label,$data);

// 		//$isd = $edge->isInferred() ? 'true' : 'false';
// 		$isd = $edge->isInferred() ? 'true' : 'false';
// 		//$ot1 = $v1->getPropertyValue('ea:obj-type:');
// 		//$ot2 = $v2->getPropertyValue('ea:obj-type:');

// 		$deps = $edge->getDependencies();
// 		//Log::info('DEPS: (' . count($deps).  ') : ' . implode(', ', $deps));

// 		if (! empty($deps)){
// 			$tmp = "{";
// 			$sep  = '';
// 			foreach ($deps as $d){
// 				//$tmp .= $sep . '"' . $d . '"';
// 				$tmp .= $sep . "'$d'";
// 				$sep = ',';
// 			}
// 			$deps = $tmp . "}";
// 		} else {
// 			$deps = null;
// 		}

// 		//Log::info("save_graph_edge: $id1 | $el | $id2 | $isd | $deps ");
// 		Log::info("## save_graph_edge: $id1 | $el | $id2 | $isd ");
// 		//Log::info("SAVE DEPS: $deps");
// 		$SQL = 'SELECT dsd.save_graph_edge(?,?,?,?,?,null,?)';
// 		$stmt = $con->prepare ( $SQL );
// 		$stmt->bindParam ( 1, $id1 );
// 		$stmt->bindParam ( 2, $el );
// 		$stmt->bindParam ( 3, $id2);
// 		$stmt->bindParam ( 4, $isd );
// 		$stmt->bindParam ( 5, $id2 );
// 		$stmt->bindParam ( 6, $deps );
// 		$stmt->execute ();
// 		$rep = $stmt->fetch();

	}


	public static function saveEdgeRaw($id1,$el,$id2,$isInfered,$deps=null,$label = null, $data =null){
		$con = prepareService();
		$isd = $isInfered? 'true' : 'false';

		$relCtrl = new RelationControl();
		$reltype = null;
		if ($relCtrl->isRelation($el)){
			$rel = $relCtrl->getRelation($el);
			$reltype = $rel->getRelType();
		}


		if (! empty($deps)){
			$tmp = "{";
			$sep  = '';
			foreach ($deps as $d){
				//$tmp .= $sep . '"' . $d . '"';
				$tmp .= $sep . "'$d'";
				$sep = ',';
			}
			$deps = $tmp . "}";
		} else {
			$deps = null;
		}
		$label = empty($label)? $id2 : $label;


		$SQL = 'SELECT dsd.save_graph_edge(?,?,?,?,?,?,?,?) as rep';
		$stmt = $con->prepare ( $SQL );
		$stmt->bindParam ( 1, $id1 );
		$stmt->bindParam ( 2, $el );
		$stmt->bindParam ( 3, $id2);
		$stmt->bindParam ( 4, $isd );
		$stmt->bindParam ( 5, $label );
		$stmt->bindParam ( 6, $data );
		$stmt->bindParam ( 7, $deps );
		$stmt->bindParam ( 8, $reltype );
		$stmt->execute ();
		$rep = $stmt->fetch(PDO::FETCH_ASSOC);

		//Log::info("@@: save_graph_edge: $id1 | $el | $id2 | $label | $isd | $reltype |REP: " . print_r($rep['rep'],true));

	}

	/**
	 *
	 * @param GGraph $graph
	 */
	public static function removeRelationEdges($graph, $item_id){
		// @DOC: RELATIONS step0 save remove relations
		// 'DELETE FROM dsd.metadatavalue2 WHERE ref_item = ? AND relation = 1 AND not inferred';
		$debug = (Config::get('arc.DEBUG_RELATIONS',0) > 0);

		//if ($debug){Log::info('@@: REMOVE ITEM RELATIONS EDGES : ' . $item_id );}
		$relCtrl = new RelationControl();//RC

		//$dbh = dbconnect();
		$dbh =  prepareService();
		$SQL = 'DELETE FROM dsd.metadatavalue2  WHERE metadata_value_id = ?';
		$stmt = $dbh->prepare($SQL);

		$vertex = $graph->getVertex(GURN::createOLDWithId($item_id));
		if (empty($vertex)) {
			// Log::info ( "@@: EMPTY VERTEX (removeRelationFalseEdges) " . $ite );
			return;
		}

		// $edges= $graph->getEdges();
		$edges = $vertex->getEdges(GDirection::BOTH);
		foreach ( $edges as $e ) {
			$key = $e->element();
			$rel = $relCtrl->getRelation($key);
			if (empty($rel) || $rel->getSkipItemLoadRemove()){continue;}
			$id = $e->persistenceId();
			if (empty($id)) {continue;}
			$stmt->bindParam(1, $id);
			$stmt->execute();
			//$dcount = $stmt->rowCount();
			//Log::info("@@: REMOVE EDGE : " . $e . ' id: ' . $id . ' I: ' . (empty($e->isInferred()) ? 'F' : 'T') . ' delete count: ' . $dcount);
			if ($debug){Log::info("@@: REMOVE EDGE : " . $e . ' id: ' . $id  .  ' I: ' . (empty($e->isInferred()) ? 'F' : 'T') );}
			$graph->removeEdge($e->vkey());
		}
	}


	/**
	 * @param GEdgeO $edge
	 */
	public static function deleteEdge($edge){
		//$debug = (Config::get('arc.DEBUG_RELATIONS',0) > 0);
		//if ($debug){Log::info("@@: deleteEdge urn: " . $edge->urnStr() . ' vkey: ' . $edge->vkey() . ' id: ' . $edge->persistenceId());}
		Log::info('@@: deleteEdge id:' . $edge->persistenceId()  . ' urn: ' . $edge->urnStr() );
		$con = dbconnect();
		$el = $edge->element();
		$v1 = $edge->getVertexFrom();
		$v2 = $edge->getVertexTo();
		$inferred = $edge->isInferred();
		if($v1->urn()->temporalType() != GURN::TEMPORAL_TYPE_OLD){
			throw new Exception('saveEdge, v1 is not TEMPORAL_TYPE_OLD');
		}
		if($v2->urn()->temporalType() != GURN::TEMPORAL_TYPE_OLD){
			throw new Exception('saveEdge, v2 is not TEMPORAL_TYPE_OLD');
		}


		if (! $inferred){
			Log::info("TRY TO DELETE NOT INFERRED (SKIP) : " . $edge->urnStr());
			return;
// 			$element = $edge->element();
// 			if (PUtil::strEndsWith($element, 'tmp')){
// 				throw new Exception('DELETE FOR NOT INFERED EDGE : ' . $edge->urnStr() . ' NOT PERMITED');
// 			}
		}
//  		$persistenceId = $edge->persistenceId();
//  		if (!empty($persistenceId)){
//  			//Log::info("@@@@: TD1: " .$persistenceId);
//  		}
//  		$ppop = $edge->persistenceProp();
//  		if (!empty($persistenceId)){
//  			Log::info("@@@@: TD2: " . $ppop->id() . ' : ' . $ppop->element());
//  		}

		$id1 = $v1->persistenceId();
		$id2 = $v2->persistenceId();

		$SQL='DELETE from dsd.metadatavalue2 where item_id = ? AND ref_item = ? AND element = ? ';
		if ($inferred){
			$SQL .= ' AND inferred';
		} else {
			$SQL .= ' AND not inferred';
		}

		$stmt = $con->prepare ( $SQL );
		$stmt->bindParam ( 1, $id1 );
		$stmt->bindParam ( 2, $id2);
		$stmt->bindParam ( 3, $el );
		$stmt->execute();
		//$count = $stmt->rowCount();
	}


	public static function deleteEdgeRaw($id1,$element,$id2){
		//Log::info("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@  TRY TO DELETE RAW: " . $id1 . ' -- '  . $element . ' --> ' . $id2);
		$con = dbconnect();

		$SQL="DELETE from dsd.metadatavalue2 where item_id = ? AND ref_item = ? AND element = ?";

		$stmt = $con->prepare ( $SQL );
		$stmt->bindParam ( 1, $id1 );
		$stmt->bindParam ( 2, $id2);
		$stmt->bindParam ( 3, $element );
		$stmt->execute();
		//$count = $stmt->rowCount();
	}



	public static function dumpDOT($graph,$options=array()){
		$file = isset($options['file']) ? $options['file'] : null;
		$replaceFileFlag =  isset($options['replaceFileFlag']) ? $options['replaceFileFlag'] : true;
		$label = isset($options['label']) ? $options['label'] : null;
		$inferred_flag = isset($options['inferredFlag']) ? $options['inferredFlag'] : true;
		$neighbourhood_flag = isset($options['neighbourhoodFlag']) ? $options['neighbourhoodFlag'] : false;
		$graph_dump_file = isset($options['graph_dump_file']) ? $options['graph_dump_file'] : null;
		GGraphUtil::dumpGraphviz($graph,$file,$inferred_flag,$neighbourhood_flag,$replaceFileFlag,$label,$graph_dump_file);
	}
/**
 * @param GGraph $graph
 */
	public static function dumpGraphviz($graph, $file = null, $inferred_flag = true, $neighbourhood_flag = false, $replaceFileFlag = true, $glabel = null, $graph_dump_file = null){
		if (is_string($neighbourhood_flag)) {
			if ($neighbourhood_flag == 'false') {
				$neighbourhood_flag = false;
			} elseif ($neighbourhood_flag == 'true') {
				$neighbourhood_flag = true;
			}
		}

		$neighborhood_get = function($neighbourhood,$all_ids, $name='def'){

			$neighbors = '';
			$neighbors_count= 0;
			$neighbors_in = 0;
			$neighbors_out =0;

			if (!empty($neighbourhood[$name])) {
				$sep = '';
				$def = $neighbourhood[$name];
					$nc = 0;
					$max_neighbors = 10;
					foreach ($def as $n) {
						if (in_array($n,$all_ids)){
							$neighbors_in+=1;
						} else {
							$neighbors_out+=1;
						}
						if ($nc < $max_neighbors) {
							$neighbors .= ($sep . $n);
						}
						$sep = ' ';
						$nc += 1;
					}
					$neighbors_count = $nc;
					if ($neighbors_count > $max_neighbors) {
						$neighbors .= '...';
					}
			}

			return array($neighbors, $neighbors_count,$neighbors_in, $neighbors_out);
		};


		$norm_value = function ($str) {
			$str = str_replace('|', ' ', $str);
			$len = mb_strlen($str);
			if ($len > 38) {
				$str = substr($str, 0, 38);
				$str .= '...';
			}
			return htmlspecialchars($str);
		};

		$ok_elemens = array(
			//'dc:title:'=>0,
			'ea:obj-type:' => 0,
			//	'ea:status:'=>0,
		);

		if (!empty($file)) {
			ob_start();
		}
		echo('digraph finite_state_machine {');
		echo("\n");
		echo('rankdir=LR;');
		echo("\n");
		//echo('size="8,5"');echo("\n");
		//echo('size="180"');echo("\n");

		$memV = $graph->getVertices();
		$memE = $graph->getEdges();
		$i = 0;
		foreach ($memV as $v) {

			$color = 'white';

			if (!$v->isOrphan()) {
				$i++;
				$id = $v->urn()->toString();
				$read_only = $v->getTmpAttribute('_READONLY');
				//$label = sprintf('%s pc: %s',$v->id(),count($v->getAllProperties()));
				$pc = count($v->getAllProperties());
				//if ($pc > 0){
				$nd = $v->getTmpAttribute("_ND");
				//Log::info("###: ". $id);
				$obj_type = null;
				$pc = 0;
				//$label = sprintf('%s pc: %s',$v->id(),count($v->getAllProperties()));
				$label = '<table  border="0" >';
				$label .= sprintf('<tr><td align="left">urn:</td><td align="left">%s</td></tr>', $v->urnStr());

				$v_title = $v->getPropertyValue("dc:title:");
				$v_label = GRuleUtil::getLabel($v);
				if (empty($v_title)) {
					$label .= sprintf('<tr><td align="left">label:</td><td align="left">%s</td></tr>', $norm_value($v_label));
				} else {
					$label .= sprintf('<tr><td align="left">title:</td><td align="left">%s</td></tr>', $norm_value($v_title));
					if ($v_title != $v_label) {
						$label .= sprintf('<tr><td align="left">label:</td><td align="left">%s</td></tr>', $norm_value($v_label));
					}
				}

				$props = $v->getAllProperties();
				foreach ($props as $pp) {
					foreach ($pp as $p) {

						$mv = $p->value();
						$mvok = $norm_value($mv);
						if (method_exists($p, 'element')) {
							$element = $p->element();
							if ($element == 'ea:obj-type:') {
								$obj_type = $mv;
							}
							if (isset($ok_elemens[$element])) {
								$label .= sprintf('<tr><td align="left">%s</td><td align="left">%s</td></tr>', htmlspecialchars($element), $mvok);
							}
						} else {
							$label .= sprintf('<tr><td align="left">??</td><td align="left">%s</td></tr>', $mvok);
						}
						$pc += 1;
					}
				}

				$label .= sprintf('<tr><td align="left">props cnt:</td><td align="left">%s</td></tr>', $pc);
//        $neighbourhood = $v->getAttribute('neighbourhood');
//          if (! empty($neighbourhood)) {
//            if (! empty($neighbourhood['def'])) {
//              $def = $neighbourhood['def'];
//              $vid = $v->persistenceId();
//              $nlabel = $vid;
//              $arc_color = 'green';
//              foreach ( $def as $n ) {
//                $vn = $graph->getVertex(GURN::createOLDWithId($n));
//                if (! empty($vn)) {
//                  printf('"%s" -> "%s" [ label = "%s",color="%s"];', $v->urnStr(), $vn->urnStr(), $nlabel, $arc_color);
//                }
//              }
//            }
//          }


				$neighbors = '';
				$neighbors_count = 0;
				$intFlag = (!is_bool($neighbourhood_flag) && $neighbourhood_flag > 0);
				$neighbourhood = $v->getAttribute('neighbourhood');
				if (!empty($neighbourhood)) {

					$all_ids = array();
					foreach ($memV as $v) {
						$all_ids[] = $v->persistenceId();
					}
					$neighbors_in = 0;
					$neighbors_out = 0;
					$neighbors_count = 0;
					$vid = $v->persistenceId();
					if ((!$intFlag) || $vid == $neighbourhood_flag) {
						list($neighbors, $neighbors_count, $neighbors_in, $neighbors_out) = $neighborhood_get($neighbourhood, $all_ids, 'def');
						$label .= sprintf('<tr><td align="left">neighbors1:</td><td align="left">%s</td></tr>', $neighbors);
						list($neighbors2, $neighbors2_count, $neighbors2_in, $neighbors2_out) = $neighborhood_get($neighbourhood, $all_ids, 'item');
						if ($neighbors2_count > 0) {
							$label .= sprintf('<tr><td align="left">neighbors2:</td><td align="left">%s</td></tr>', $neighbors);
						}
					}
				}

					//$label .= sprintf('<tr><td align="left"> &nbsp; »» </td><td align="left">all: %s in: %s out: %s</td></tr>', $neighbors_count,$neighbors_in,$neighbors_out);
					//$label .= sprintf('<tr><td align="left">neighbors:</td><td align="left">%s</td></tr>', $neighbors_count);

				if (!empty($nd)) {
					$bgcolor = '';
					if (!empty($read_only) && $read_only) {
						$bgcolor = 'bgcolor="red"';
					} elseif ($nd == 'ROOT') {
						$bgcolor = 'bgcolor="green"';
					}
					$rest_label ='';
					if ($neighbors_count > 0) {
						$rest_label = sprintf('count: %s in: %s out: %s',$neighbors_count,$neighbors_in,$neighbors_out);
					}
					$label .= sprintf('<tr><td align="left">neigborhood:</td><td %s align="left">%s %s</td></tr>', $bgcolor, $nd,$rest_label);
				}


//      if (!empty($read_only) && $read_only){
//        $label .= sprintf('<tr><td align="left" bgcolor="blue">READONLY:</td><td align="left" bgcolor="blue">TRUE</td></tr>');
//      }
				$label .= "</table>";
				//} else {
				//	$label = sprintf('%s',$v->id());
				//}

				if (!empty($obj_type)) {
					if ($obj_type == 'auth-work') {
						$color = '#FF33FF';
					} elseif ($obj_type == 'auth-expression') {
						$color = '#FF99FF';
					} elseif ($obj_type == 'auth-manifestation') {
						$color = '#CCFFCC';
					} elseif ($obj_type == 'auth-person') {
						$color = '#FFCC99';
					}
				}

				printf('"%s" [shape = "Mrecord",  label=<%s>, style="filled", fillcolor="%s"];', $id, $label, $color);
				echo("\n");
			}
		}

		$i = 0;
		foreach ($memE as $e) {
			$i++;
			$id = $e->id();
			$v1 = $e->getVertexFrom();
			$v2 = $e->getVertexTO();


			$pc = count($e->getAllProperties());
			if ($pc > 0) {
				$label .= sprintf(' pc:%s', $pc);
			}


			if ($e->isInferred()) {
				$label = 'I: ';
				$arc_color = 'red';
			} else {
				$label = '';
				$arc_color = 'black';
			}
			$label .= sprintf('%s', $e->urn());


			if ($inferred_flag || !$e->isInferred()) {
				printf('"%s" -> "%s" [ label = "%s",color="%s"];', $v1->urnStr(), $v2->urnStr(), $label, $arc_color);
			}

			echo("\n");
		}
		if ($neighbourhood_flag) {
			$intFlag = (!is_bool($neighbourhood_flag) && $neighbourhood_flag > 0);
			//Log::info('intFlag: ' . $intFlag);
			foreach ($memV as $v) {
				$neighbourhood = $v->getAttribute('neighbourhood');
				//Log::info('neighbourhood: ' . print_r($neighbourhood,true));
				if (!empty($neighbourhood)) {
					if (!empty($neighbourhood['def'])) {
						$def = $neighbourhood['def'];
						$vid = $v->persistenceId();
						if ((!$intFlag) || $vid == $neighbourhood_flag) {
							$nlabel = $vid;
							$arc_color = 'green';
							foreach ($def as $n) {
								$vn = $graph->getVertex(GURN::createOLDWithId($n));
								if (!empty($vn)) {
									printf('"%s" -> "%s" [ label = "%s",color="%s"];', $v->urnStr(), $vn->urnStr(), $nlabel, $arc_color);
								}
							}
						}
					}
				}
			}
		}


		if (!empty($glabel)) {
			printf('label="%s"', $glabel);
			echo("\n");
		}
		echo('}');
		echo("\n");


		if (!empty($file)) {
			if (!$replaceFileFlag) {
				$c = 0;
				while (file_exists($file) && $c < 100) {
					Log::info('DUMP GRAPHVIZ FILE: ' . $file . ' EXIST');
					$c += 1;
					$file = $file . '.' . $c;
				}
			}
			file_put_contents($file, ob_get_clean());
		}

		if (!empty($graph_dump_file)) {
			file_put_contents($graph_dump_file, GGraphUtil::dump1($graph, false, $glabel));

		}
		//GGraphUtil::dump1($graph,false);
	}




/**
 * @param GGraph $graph
 */
public static function dumpEdges($graph,$title=null){
	Log::info("-----------------------------------------------------------------------------------------------------------------------------------------");
	Log::info("DUMP EDGES: " . $title);
	$edges = $graph->getEdges();
	$i = 0;
	foreach ($edges as $k=>$e){
		$i++;
		$id = $k;
		$v1 = $e->getVertexFrom();
		$v2 = $e->getVertexTO();
		$d = $e->isInferred() ? 'I' : ' ';
		$pc = count($e->getAllProperties());
		$pcs =($pc > 0) ?sprintf('   pc: %s',$pc) :'';
		//Log::info(sprintf('%-4s %2s %8s --[%-50s]-> %8s %s',$i, $d, $v1->urn(),$e->element(),$v2->urn(), $pcs  ));
		Log::info(sprintf('%-4s %2s %8s --[%s]-> %8s %s',$i, $d, $v1->urn(),$e->element(),$v2->urn(), $pcs  ));
	}
	Log::info("-----------------------------------------------------------------------------------------------------------------------------------------");

}


/**
 * @param GGraph $graph
 */
	public static function dump1($graph,$dumpToLog = true, $title=null){
		$memV = $graph->getVertices();
		$memE = $graph->getEdges();

		$out = "\n";
		if (!empty($title)){
			$out .= $title;
			$out .="\n---------------------------------------------------------------------------\n";
			$out .="\n";
		}
		$out .= "VERTICES: " . count($memV);
		$out .="\n";
		$i = 0;
		foreach ($memV as $k=>$v){
			$i++;
			$urnStr = $v->urnStr();
			$id = $k;
			if ($urnStr != $id){
				$out .=  'ERROR KEY: ' . $id . ' <> ' . $urnStr;
				$out .="\n";
			}

			$pc = count($v->getAllProperties());
			$out .=  sprintf('%s',$id) ;

			if ($pc > 0){
				$out .=  sprintf(' pc: %s',$pc) ;
			}

			$title = $v->getPropertyValue('dc:title:');
			if (! empty($title)){
				$out .=  sprintf(' [%s]',$title);
			}

			$out .= " N:(";
			$neighbourhood = $v->getAttribute('neighbourhood');
			if (! empty($neighbourhood) && ! empty($neighbourhood['def'])) {
				$def = $neighbourhood['def'];
				$sep = '';
				foreach ( $def as $n ) {
					$out .= $sep;
					$out .= $n;
					$sep = ' ';
				}
			}
			$out .= ") ";

			$out .="\n";
		}


		$out .=  "EDGES: " . count($memE) . "\n";
		$i = 0;
		foreach ($memE as $k=>$e){
			$i++;
			//$id = $e->urn();
			$id = $k;
// 			if ($e->urn()->toString() != $id){
// 				echo( 'ERROR KEY: ' . $id . ' <> ' . $e->urn()->toString());
// 			}
			$v1 = $e->getVertexFrom();
			$v2 = $e->getVertexTO();
			$d = $e->isInferred() ? 'I' : ' ';
			//printf('%s',$e->urnStr());
			$out .=  sprintf(' %2s %8s --> %8s %-30s %-50s ',$d, $v1->urn(),$v2->urn() , $e->element(), $e->urnStr());

			//printf('%s:  %s --> %s ( %s )',$id,$v1->urn(),$v2->urn() , $e->element());
 			$pc = count($e->getAllProperties());
 			if ($pc > 0){
 				$out .= sprintf('   pc: %s',$pc);
 			}
 			$out .="\n";
		}

		if ($dumpToLog){
			Log::info($out);
		}


		return $out;

	}



	/**
	 *
	 * @param GVertex $v
	 */
	public static function dumpVertex($v,$dumpToLog = true){

		$flags = array();
		if ($v->isRoot()){$flags[] = 'root';}
		if ($v->isLeaf()){$flags[] = 'leaf';}
		if ($v->isOrphan()){$flags[] = 'orphan';}

		$flags_str = implode(",", $flags);
		if (!empty($flags_str)){
			$flags_str = '(' . $flags_str .')';
		}

		$out = "\n";
		$ot  = $v->getObjectType();
		$out .= "=================================================================\n";
		$out .= "DUMP VERTEX: " . $v->id() .  "  :  " . $ot  . ' ' . $flags_str . "\n";
		$out .= "-----------------------------------------------------------------\n";

		$props = $v->getAllProperties();
		$out .= "PROPERTIES:\n";
		$out .= ".................................................................\n";

		$tbl = new Console_Table();
		foreach ($props as $k => $ps){
			foreach ($ps as $i=>$p){
				$j  = $i +1;

				/*@var $p GPropertyItem */
				$parent = '/';
				if (method_exists($p,'parent')) {
					$parent = $p->parent();
				}
				$element = $k;
				if (method_exists($p,'element')) {
					$element = $p->element();
					if ($element != $k){
						$element .= " <> $k";
					}
				}
				$tbl->addRow(array($parent, $element, $p->value(),$p->data()));
			}
		}
		$out .= PUtil::printConsoleTable($tbl,array('method'=>1,'left_padding'=> '   '));


		$attrs = $v->getTmpAttributes();
		$out .= ".................................................................\n";
		$out .= "ATTRIBUTES:\n";
		$out .= ".................................................................\n";
		//Log::info(print_r($attrs,true));
		foreach ($attrs as $k => $a){
			$out .= '   ' . $k . ' => ' . $a . "\n";
		}

		$out .= ".................................................................\n";
		$out .= "IN EDGES:\n";
		$out .= ".................................................................\n";
		$tbl = new Console_Table();
		$inEdges = $v->getEdges(GDirection::IN);
		foreach ($inEdges as $e){
			$tbl->addRow(array($e->element(),$e->getVertexFrom()->id(),$e->getVertexFrom()->getObjectType(), $e->isInferred(),  count($e->getAllProperties()) ));
		}
		$out .= PUtil::printConsoleTable($tbl,array('method'=>1,'left_padding'=> '   '));

		$out .= ".................................................................\n";
		$out .= "OUT EDGES:\n";
		$out .= ".................................................................\n";
		$outEdges = $v->getEdges(GDirection::OUT);
		$tbl = new Console_Table();
		foreach ($outEdges as $e){
			$tbl->addRow(array($e->element(),$e->getVertexTO()->id(), $e->getVertexTO()->getObjectType() ,$e->isInferred() ? 'infered' : '',  count($e->getAllProperties()) ));
		}
		$out .= PUtil::printConsoleTable($tbl,array('method'=>1,'left_padding'=> '   '));
		$out .= "=================================================================\n";

		if ($dumpToLog){
			Log::info($out);
		}
		return $out;

	}

	public static function graphResetFull() {


		if (Config::get('arc.ENABLE_SOLR',1)>0) {
			Log::info("RESET_SOLR");
			try {
				$client = new Solarium\Client(array('endpoint' => PUtil::getSolrConfigEndPoints('opac')));
				$update = $client->createUpdate();
				$update->addDeleteQuery('*:*');
				$update->addCommit();
				$result = $client->update($update);
				Log::info('SOLR Query status: ' . $result->getStatus());
				Log::info('SOLR Query time: ' . $result->getQueryTime());
			} catch (Exception $e) {
				Log::info("SOLR RESET FAILED " . $e->getMessage());
				Log::info($e);
			}
		}

		$con = dbconnect();
		$SQL = "DELETE FROM dsd.metadatavalue2 WHERE inferred";
		$stmt = $con->prepare($SQL);
		$stmt->execute();

		$graph = new GGraphO();

		$rules = Config::get('arc_rules.DEFAULT_RULES', array());
		$rule_mem = Config::get('arc_rules.INIT_MEMORY', array());
		$re = new GRuleEngine($rules, $rule_mem, $graph);
		$context = $re->execute();

		$eps = $context->getEditPropUrns();
		foreach ( $eps as $urnStr ) {
			$v = $graph->getVertex($urnStr);
			$elements = $context->getEditProps($urnStr);
			GGraphUtil::saveProperties($v, $elements);
		}
		return $context;
	}

}