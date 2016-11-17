	<?php






// 	class  GRulePostSyncDiff implements  GRule {

// 		/*@var $context GRuleContextR */
// 		private $context;
// 		private $g1;
// 		private $g2;

// 		/**
// 		 * @param GRuleContextR context
// 		 */
// 		public function __construct($context,$g1,$g2) {
// 			$this->context = $context;
// 			$this->g1 = $g1;
// 			$this->g2 = $g2;
// 		}

// 		public function execute(){
// 			$context = $this->context;
// 			$g1 = $this->g1;
// 			$g2 = $this->g2;

// 			GRuleEngineUtil::findDiff($g1, $g2, function($type,$edge){
// 				/* @var $edge GEdge */
// 				$v1 = $edge->getVertexFrom();
// 				//Log::info("DIFF: $type $edge");
// 				$deps = $edge->getDependencies();
// 				Log::info("DIFF: $type $edge DEPS: (" . count($deps).  ') : ' . implode(', ', $deps));
// 				if ($type == 'NEW'){
// 					GGraphUtil::saveEdge($edge);
// 				} elseif ($type == 'DEL'){
// 					GGraphUtil::deleteEdge($edge);
// 				}
// 			});


// 		}

// 	}







	class  GRulePostSyncDiffCmd implements  GCommand {

		/*@var $context GRuleContextR */
		private $context;
		/* @var $g1 GGraph */
		private $g1;
		/* @var $g2 GGraph */
		private $g2;

		/**
		 * @param GRuleContextR context
		 */
		public function __construct($context,$g1,$g2) {
			$this->context = $context;
			$this->g1 = $g1;
			$this->g2 = $g2;
		}

		public function execute($context){
			$context = $this->context;
			$g1 = $this->g1;
			$g2 = $this->g2;



// 			$con = dbconnect();
// 			$SQL = sprintf("UPDATE dsd.metadatavalue2 SET text_value = ? where element = ? AND item_id = ? and lid=?");
// 			$st = $con->prepare($SQL);

// 			$vs = $g2->getVertices();
// 			/*@var $v GVertexO */
// 			foreach ($vs as $v){
// 				$updatesTracker  = $v->_getUpdatesTracker();
// 				foreach ($updatesTracker as $update){

// 					//array('element'=>$element, 'idx'=>$idx,'prop'=>$p);
// 					/*@var $p GPropertyO */
// 					$p = $update['prop'];
// 					$lid = $p->treeId();
// 					if (!empty($lid)){
// 						$value =$p->value();
// 						$itemId=  $v->persistenceId();
// 						$element = $p->element();
// 					//	Log::info("UPDATE dsd.metadatavalue2 " . $itemId .  ' : ' . $lid . " : " . $p->element() . ' : ' . $value);
// 						$st->bindParam(1, $value);
// 						$st->bindParam(2, $element);
// 						$st->bindParam(3, $itemId);
// 						$st->bindParam(4, $lid,PDO::PARAM_INT);
// 						$st->execute();
// 					}
// 				}
// 			}
			//$vs = $g2->getVertices();
// 			$callback = function($method, $p){
// 				/*@var $p GPropertyO */
// 				if (!$p->inferred()){ return; };

// 				if ($method == 'DELETE'){
// 					Log::info("@@@@@@@@@@@@@@ DELETE");

// 				}
// 				elseif ($method == 'UPDATE'){
// 					Log::info("@@@@@@@@@@@@@@ UPDATE");
// 				}
// 			};
			GGraphUtil::saveUpdatesTracker($g2,null);

// 			GRuleEngineUtil::findDiff($g1, $g2, function($type,$edge){
// 				/* @var $edge GEdge */
// 				$v1 = $edge->getVertexFrom();
// 				//Log::info("@@: DIFF: $type $edge");
// 				//$context->addDebugMessage("DIFF: $type | $edge");
// 				//$deps = $edge->getDependencies();
// 				//Log::info("DIFF: $type $edge DEPS: (" . count($deps).  ') : ' . implode(', ', $deps));
// 				if ($type == 'NEW'){
// 					GGraphUtil::saveEdge($edge);
// 				} elseif ($type == 'DEL'){
// 					GGraphUtil::deleteEdge($edge);
// 				}
// 			});
			$edges1 = $g1->getInferredEdges();
// 			foreach ($edges1 as $e1){
// 				GGraphUtil::deleteEdge($e1);
// 			}
			$con = dbconnect();
			$SQL='DELETE from dsd.metadatavalue2 where metadata_value_id = ? AND inferred ';
			$stmt = $con->prepare ( $SQL );

			$c=0;
			foreach ($edges1 as $e1){
				//if ($e1->getVertexFrom()->isReadOnly()){ continue;}
				$id = $e1->persistenceId();
				//Log::info('@@: deleteEdge id:' . $id  . ' urn: ' . $e1->urnStr() );
				$stmt->bindParam ( 1, $id );
				$stmt->execute();
				$cnt = $stmt->rowCount();
				if ($cnt > 0) {
					$c+=1;
				} else {
					Log::info("@@: EDGE $id NOT FOUND TO DELETE " . $e1->urnStr() );
				}
			}
			Log::info('@@: DELETE EDGE COUNT: ' . $c);

			$edges2 = $g2->getInferredEdges();
			$c = 0;
			foreach ($edges2 as $e2){
				//if ($e2->getVertexFrom()->isReadOnly()){ continue;}
				$c+=1;
				GGraphUtil::saveEdge($e2);
			}
			Log::info('@@: SAVE EDGE COUNT: ' . $c);
			GGraphUtil::saveVertexUpdatesTracker($g2);

		}

	}



	class  GRuleSyncDiff extends AbstractBaseRule implements  GRule {

		private $graph_1_index = null;
		private $graph_2_index = null;
		private $saveInferenceFlag = true;

		/**
		 * @param GRuleContextR $context
		 * @param $args array
		 */
		public function __construct($context, $args) {
			if (empty($args)){
				throw new Exception("GRuleSaveDiff arguments expected");
			}
			$this->saveInferenceFlag = (Config::get('arc.SAVE_INFERENCE_AS_TUPLE',1) > 0);

			$this->graph_1_index = $args['graph_1_index'];
			$this->graph_2_index = $args['graph_2_index'];

			parent::__construct($context);

		}

		public function execute(){
			if (! $this->saveInferenceFlag){  return; }

			$context = $this->context;



			$idx1 = $this->graph_1_index;
			$idx2 = $this->graph_2_index;
			$g1 = $idx1 == 'main' ?  $context->graph()  : $context->getGraph($idx1);
			$g2 = $idx2 == 'main' ?  $context->graph()  : $context->getGraph($idx2);

			//[50,'SyncDiff',{'graph_1_index':'old','graph_2_index':'main'}],
			$context->putCommand('PostSyncDiff', new GRulePostSyncDiffCmd($context, $g1, $g2));


		}

	}


