<?php



//  ea:subj:
//  ea:subj:concept
//  ea:subj:event
//  ea:subj:form
//  ea:subj:general
//  ea:subj:object
//  ea:subj:person
//  ea:subj:place
//  ea:subj:title
//  ea:subj:work



// class GruleSubjectChainLabelsCmd implements  GCommand {


// 	public function __construct() {

// 	}

// 	public function execute($context){
// 		//$context->addDebugMessage("GruleSubjectChainLabelsCmd INIT");

// 		$map = $context->get('SUBJECT_CHAIN_LABELS_ARRAY');
// 		//$context->addDebugMessage(print_r($map,true));

// 		$con = dbconnect();
// // 		$SQL1 = sprintf('UPDATE dsd.item2 SET label  = ?, title= ? WHERE item_id = ?');
// // 		$st1 = $con->prepare($SQL1);

// 		$SQL2 = sprintf("UPDATE dsd.metadatavalue2 SET text_value = ? where element = 'dc:title:' AND item_id = ?");
// 		$st2 = $con->prepare($SQL2);

// 		foreach ($map as $k=>$v){
// 			//$context->addDebugMessage("@GruleSubjectChainLabelsCmd: " . $k . " = " . $v);

// //  			$st1->bindParam(1, $v);
// //  			$st1->bindParam(2, $v);
// //  			$st1->bindParam(3, $k);
// //  			$st1->execute();

// 			$st2->bindParam(1, $v);
// 			$st2->bindParam(2, $k);
// 			$st2->execute();


// 		}
// 	}

// }



class GRuleSubjects  extends AbstractGruleProcessVertice implements GRule {


	//private $chainLAbelsArray = array();
	private $primary_subject_type_map;
	private $subject_type_map;
	private $subject_keys;

	protected function init(){
		$this->primary_subject_type_map = Setting::get('primary_subject_type_map');
		$this->subject_type_map = Setting::get('subject_type_map');

		$this->subject_keys = array_merge(array_keys($this->primary_subject_type_map),array_keys($this->subject_type_map));
		//$this->context->addDebugMessage("GRuleSubjects INIT");
	//	$this->context->putCommand("SUBJECT_CHAIN_LABELS", new GruleSubjectChainLabelsCmd());
		$this->skip_readonly = true;

	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		$title = $v->getPropertyValue('dc:title:');
		$v->setTmpAttribute('label',$title);

// 		$this->context->addDebugMessage("@GRuleSubjects proc: " . $v);
		//Log::info(Log::info("@@1 GRuleSubjects proc: " . $v));
		//echo("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n");

		//$ot = $v->getPropertyValue("ea:obj-type:");
		//Log::info("###@1 $v");
		$g = $v->graph();
		$src_id = $v->id();

		$subitemFlag =  $this->context->get('subitemFlag',false);
		if (!$subitemFlag) {
			$subjects = $v->getVertices(GDirection::OUT,'ea:inferred-chain-link:');
			//$subjects = $v->getVertices(GDirection::OUT,$this->subject_keys);
			$cnt = count($subjects);
			//Log::info("%%###0 cnt: " . $cnt);
			if ($cnt == 1){
				$s = array_values($subjects)[0];
				//Log::info("%%###1 v: " . $s->urnStr());
				$ses = $v->getEdges(GDirection::IN, 'ea:subj:');
				//if (!empty($ses)){
					foreach ($ses as $srcEdge){
						//Log::info("%%###2 e: " . $srcEdge->urnStr() . ' TO REMOVE');
						$srcV =  $srcEdge->getVertexFrom();
						//Log::info("%%###2 v: " . $srcV->urnStr());
						$g->addEdge($srcV->urnStr(), $s->urnStr(), 'ea:subj:',false);
						$g->removeEdge($srcEdge->vkey(),true);
					}
					//Log::info("%%###3 v: " . $v->urnStr());
					//REMOVE CHAIN
					$this->context->removeVertext($v->urnStr(),true);
				//}
			}
		}

		//METAVATIKH IDIOTITA 'ea:inferred-chain-link:'
		$context = $this->context;
		$graph = $g;
		$finishFlag = false;
		$fn = function ($c, $vertex, $element, $distance) use(&$v, &$graph, &$finishFlag, $context) {
			//$context->addDebugMessage ( '###FN1: c:' . $c . ' vertext: ' . $vertex  .  ' element: ' . $element->urnStr() . ' distance: ' . $distance);
			/* @var $vertex GVertex */
			if ($distance == 2) {
				//$context->addDebugMessage ( '###FN2: c:' . $c . ' vertext: ' . $vertex  .  ' element: ' . $element->urnStr() . ' distance: ' . $distance);
				if (! $vertex->getFirstEdge(GDirection::IN, 'ea:inferred-chain-link:', $v->urn () )) {
					//$context->addDebugMessage ( '###FN3 NE: ' . $v . ' -- [ea:inferred-chain-link:] --> ' . $vertex );
					$context->addNewEdge($v->urnStr(), $vertex->urnStr(), 'ea:inferred-chain-link:',true);
					$finishFlag = false;
				}
			}
			return true;
		};
		while ( ! $finishFlag ) {
			$finishFlag = true;
			$graph->traverseBF($v, 2,$fn, array('ea:inferred-chain-link:'),GDirection::OUT);
		}


		//REMOVE ea:inferred-chain-link:  stin periptosi pou dixnoun se alo subject-chain
		$subject_edges = $v->getEdges(GDirection::OUT,'ea:inferred-chain-link:');
		foreach ($subject_edges as $e){
			$s = $e->getVertexTO();
			if ($s->getObjectType() == 'subject-chain'){
				$context->addDebugMessage("REMOVE CHAIN: " . $e->vkey());
				$context->removeEdge($e->vkey());
			}
		}

// 		// KATASKEVI LABEL
// 		// HAS: FLAGS
// 		//////////////////////////////////////
// 		$label = '';
// 		$sep ='';
// 		$subject_edges = $v->getEdges(GDirection::OUT,$this->subject_keys);
// 		//$edges =$v->getEdges(GDirection::OUT);
// 		foreach ($subject_edges as $e){
// 			$s = $e->getVertexTO();
// 				//$this->context->addDebugMessage("@@PROC: " . $s);
// 			$sl = GRuleUtil::getLabel($s);
// 			$label .= $sep . $sl;
// 			$sep = ' -- ';
// 			$ot = $s->getObjectType();
// 			if ($ot != 'subject-chain'){
// 				$v->addFlag('HAS:'.$ot);
// 			}
// 			if ($ot == 'auth-person' || $ot == 'auth-family' || $ot == 'auth-organization'){
// 				$v->addFlag('HAS:actor');
// 			}
// 			$surn = $s->urnStr();
// 			// 				/** @var $e GEdge **/
// 			// 				foreach ($edges as $e){
// 			// 					if ($e->element() != 'ea:inferred-chain-link:' && $e->getVertexTO()->urnStr() == $surn){
// 			// 						$this->context->addDebugMessage("TEST: " . $e->vkey() . " : " . $ot);
// 			// 					}
// 			// 				}
// 		}



		$sc = 0;
		$label = '';
		$sep ='';
		$subject_childs = array();
		//$c, $v, $parent
		$fnDF = function ($c, $s, $edge) use(&$v, &$graph, &$finishFlag, $context,&$label, &$sep, &$sc, &$subject_childs) {
			//$context->addDebugMessage ( '###FN@@@: c:' . $c . ' vertex: ' . $s  .  ' | edge: ' . $edge);
			$ot = $s->getObjectType();
			if ($ot != 'subject-chain'){
				//$context->addDebugMessage ( '###FN@@@2: c:' . $c . ' vertex: ' . $s  .  ' | edge: ' . $edge);
				$sc += 1;

				$sl = GRuleUtil::getLabel($s);
				$label .= $sep . $sl;
				$sep = ' -- ';
				$ot = $s->getObjectType();
				$v->addFlag('HAS:'.$ot);
				if ($ot == 'auth-person' || $ot == 'auth-family' || $ot == 'auth-organization'){
					$v->addFlag('HAS:actor');
				}

				$subject_childs[] = array('label' => $sl, 'ot' => $ot, 'id'=>$s->persistenceId());

			}
			return true;
		};
		//$context->addDebugMessage ( print_r($this->subject_keys,true));
 		$graph->traverseDF($v, $fnDF,$this->subject_keys,GDirection::OUT);



 		//$context->addDebugMessage(print_r($subject_childs,true));
		//$context->addDebugMessage("LABEL: " . $label );

		$jdata = $v->getTmpAttribute('jdata');
		if (empty($jdata)){  $jdata = array(); }
		$jdata['chain_subjects'] = $subject_childs;
		$v->setTmpAttribute('jdata',$jdata);
		$v->setTmpAttribute('label',$label);
		$v->updatePropertyValue('dc:title:',null,$label);
		//$this->chainLAbelsArray[$v->persistenceId()] = $label;


		// 		$fn2 = function ($c, $vertex, $element, $distance) use(&$v, &$graph, &$finishFlag, $context) {
		// 			$context->addDebugMessage ( '###FN@@@: c:' . $c . ' vertext: ' . $vertex  .  ' element: ' . $element->urnStr() . ' distance: ' . $distance);
		// 			return true;
		// 		};
		// 		$context->addDebugMessage('TRAVERSE: BF');
		// 		$graph->traverseBF($v, 100,$fn2,$this->subject_keys ,GDirection::OUT);
		// 		$context->addDebugMessage('--------------------------------');

		//$graph->traverseBF($v, null,$fn2,'ea:inferred-chain-link:' ,GDirection::OUT);


// 		$subjects = $v->getVertices(GDirection::OUT,'ea:inferred-chain-link:');
// 		while(!$finishFlag){
// 			$finishFlag = true;
// 			foreach ($subjects as $v1){
// 				$context->addDebugMessage('###v1: '  . $v1);
// 				if ($v1->isOrphan()){ continue; };
// 				$fn = function($c, $vertex,$parent, $distance) use (&$v1,&$graph,&$finishFlag,$context){
// 					$context->addDebugMessage('###FN: ' . $c . ' : ' . $vertex);
// 					/* @var $vertex GVertex */
// 					if ($distance == 2){
// 						if (!$vertex->getFirstEdge(GDirection::OUT, 'ea:inferred-chain-link:',$v1->urn())){
// 							$finishFlag = false;
// 							$context->addDebugMessage('###FN NE: ' . $v  .  ' -- [ea:inferred-chain-link:] --> ' . $vertex->urnStr());
// 							//$context->addNewEdge($v->urnStr(), $vertex->urnStr(), 'ea:inferred-chain-link:',true);
// 						}
// 					}
// 					return true;
// 				};
// 				$graph->traverseBF($v1, 2,$fn, array('ea:inferred-chain-link:'));
// 			}
// 		}





//		foreach ($subjects as $s){
			////$s->getProperties($element)
// 			$ot = $s->getObjectType();
// 			$urnStr = $s->urnStr();
// 			$id = $s->id();
// 			if ($ot == 'auth-general'){
// 				//echo(">> $urnStr :  $id\n ");
// 				$src = $v->getFirstVertex(GDirection::IN, 'ea:subj:');
// 				if ($src){
// 					$urnSrc = $src->urnStr();
// 					Log::info("###@2 $urnSrc -->  $urnStr ");
// 					$e = $g->addEdge($urnSrc, $urnStr, 'ea:subj:general',false);
// 					if (! empty($e)){
// 						Log::info("NE: $e\n");
// 						GGraphUtil::saveEdge($e);
// 						Log::info("DELETE: $src_id\n");
// 						$cmd = new GRuleDeleteItemCmd($src_id);
// 						$context->putCommand('V_DELETE_ITEM_' . $src_id, $cmd);
// 					}
// 				}
// 			}
//		}

// 		///////////////////////////////////////////////
// 		//TAG CLOUD
// 		///////////////////////////////////////////////
// 		$c1 = count($v->getEdges(GDirection::IN,'ea:subj:'));
// 		$c2 = count($v->getEdges(GDirection::IN,'ea:subj:general'));
// 		$id = $v->persistenceId();
// 		echo("DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD\n");
// 		echo("SUBJECT-COUNT1  $v : $id  : $c1 \n");
// 		echo("SUBJECT-COUNT2  $v : $id  : $c2 \n");
// 		echo("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n");
//
// 		Log::info("FOLDER-COUNT  $v : $id  : $c");
// 		$cmd = new GCmdFolderCountSave($id, $c);
// 		$context->putCommand('FOLDER_COUNT_' . $id, $cmd);



// 		echo("DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD\n");
// 		echo(GGraphUtil::dump1($g,false));
 		//echo("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n");

	}


	public function postExecute() {
	//	$this->context->put('SUBJECT_CHAIN_LABELS_ARRAY', $this->chainLAbelsArray);
	}



}



// class  GRuleSubjects  implements  GRule {
// 	/**
// 	 * @param GRuleContextR $context
// 	 */
// 	public function __construct($context) {

// 	}

// 	/**
// 	 * @param GRuleContextR $context
// 	 */
// 	public function execute($context){
// 		$graph = $context->graph();


// 	}


// }
