<?php

class  GRuleRelationTransitive extends AbstractBaseRule implements  GRule {

	private $add_depentencies = false;
	private $keysEquivalenceTransitive = null;
	private $keysDirectedTransitive = null;
	private $relationControl;
	/**
	 * @param GRuleContextR $context
	 */
	public function __construct($context) {
		parent::__construct($context);
		$rc = new RelationControl();
		$this->relationControl = $rc;
		$this->keysEquivalenceTransitive = $rc->getRelationKeys(false,true);
		$this->keysDirectedTransitive = $rc->getRelationKeys(true,true);
	}


	public function execute(){
		$context = $this->context;
		//$context->addDebugMessage('');
		/* @var  $graph  GGraph */
		$graph = $context->graph();
		$this->equivalenceTransitive($graph);
		$this->directedTransitive($graph);

	}


/**
 *
 * @param GGraph $graph
 */
	private function equivalenceTransitive($graph){
		//GGraphUtil::dumpEdges($graph,'########1');

		$context = $this->context;
		$edges = $graph->getEdges();
		$graphs = array();
		foreach($this->keysEquivalenceTransitive as $k){
			$map = array();
			foreach ($edges as $e){
				if ($e->element() == $k){
					$v1 = $e->getVertexFrom();
					$v2 = $e->getVertexTo();
					$map[$v1->urnStr()] = $v1;
					$map[$v2->urnStr()] = $v2;
				}
			}
			if (count($map) > 0){
				$graphs[$k] = array_values($map);
			}
		}

		// 		foreach ($graphs as $kk=>$vv){
		// 			$context->addDebugMessage(' >>> '. $kk . " : " . count($vv));
		// 		}

		$fn = function ($c, $vertex, $e,$root) use(&$graph, $context) {
			//$context->addDebugMessage('TRAVERSE: ' . $vertex->urnStr());
			/* @var $e GEdge */
			/* @var $vertex GVertex */
			/* @var $root GVertex */
			$mykey = $root->getTmpAttribute("NEIBORHOODET_KEY");
			$neiborhood =$root->getTmpAttribute($mykey);
			$neiborhood[] = $vertex;
			$root->setTmpAttribute($mykey,$neiborhood);
			$vertex->setTmpAttribute('VISITED:'. $mykey  ,$neiborhood);
		};

		foreach ($graphs as $k=>$vertices){
			//$context->addDebugMessage('KEY: ' . $k);
			$myKey = 'NEIBORHOODET:' . $k;
			/* @var $g GGRaph */
			foreach ($vertices as $v){
				//$context->addDebugMessage('0: ' . $v->urnStr());
				if ($v->hasTmpAttribute('VISITED:'. $myKey) || $v->isOrphan()){
					//$context->addDebugMessage('SKIP: ' . $v->urnStr());
					continue;
				}
				$v->setTmpAttribute("NEIBORHOODET_KEY",$myKey);
				/* @var $neiborhood GVertex[] */
				$neiborhood = array($v);
				$v->setTmpAttribute($myKey,$neiborhood);
				$graph->traverseDF($v, $fn, $this->keysEquivalenceTransitive,GDirection::BOTH);
				$neiborhood =$v->getTmpAttribute($myKey);
				if (count($neiborhood) > 1){
					//$context->addDebugMessage('3: ' . $v->urnStr() . ' count: ' . count($neiborhood));
					foreach ($neiborhood as $v1){
						foreach ($neiborhood as $v2){
							//$context->addDebugMessage('4: ' . $v1->urnStr() . ' - '  . $v2->urnStr());
							$u1 = $v1->urnStr();
							$u2 = $v2->urnStr();
							if ($u1 != $u2){
								if (!$v1->hasEdge(GDirection::OUT, $k, $u2)){
									$graph->addEdge($u1, $u2, $k);
								}
							}
						}
					}
				}

			}
		}

		//GGraphUtil::dumpEdges($graph,'########3');
	}






	/**
	 *
	 * @param GGraph $graph
	 */
	private function directedTransitive($graph){

		$context = $this->context;
		$edges = $graph->getEdges();

		$context->addDebugMessage('directedTransitive');

		$graphs = array();
		foreach($this->keysDirectedTransitive as $k){
			$map = array();
			foreach ($edges as $e){
				if ($e->element() == $k){
					$v1 = $e->getVertexFrom();
					$v2 = $e->getVertexTo();
					$map[$v1->urnStr()] = $v1;
					$map[$v2->urnStr()] = $v2;
				}
			}
			if (count($map) > 0){
				$graphs[$k] = array_values($map);
			}
		}

		/* @var $vertices GVertex[] */
		foreach ($graphs as $k=>$vertices){

			$transitive_element = $this->relationControl->getRelation($k)->getTrnasitiveElement();
			//$context->addDebugMessage(' >>> '. $k . " : " . count($vertices) . ' transitive_element: ' . $transitive_element);
			if (empty($transitive_element)){
				$context->addDebugMessage('ERROR: CANOT FIND TRANSITIVE ELEMENT FOR: ' . $k);
				Log::info('ERROR: CANOT FIND TRANSITIVE ELEMENT FOR: ' . $k);
				continue;
			}
			foreach ($vertices as $v){
				//$context->addDebugMessage(' >>>>>> '. $v->urnStr());
					//METAVATIKH IDIOTITA 'ea:inferred-chain-link:'
					$finishFlag = false;
					$fn = function ($c, $vertex, $element, $distance) use(&$v, &$graph, &$finishFlag, $context,$k,$transitive_element) {
						//$context->addDebugMessage ( '###FN1: c:' . $c . ' vertext: ' . $vertex  .  ' element: ' . $element->urnStr() . ' distance: ' . $distance);
						/* @var $vertex GVertex */
						if ($distance == 2) {
							//$context->addDebugMessage ( '###FN2: c:' . $c . ' vertext: ' . $vertex  .  ' element: ' . $element->urnStr() . ' distance: ' . $distance);
							if (! $vertex->getFirstEdge(GDirection::IN, $k, $v->urn () )) {
								//$context->addDebugMessage ( '###FN3 NE: ' . $v . ' -- [' . $transitive_element . '] --> ' . $vertex );
								$context->addNewEdge($v->urnStr(), $vertex->urnStr(), $transitive_element,true);
								$finishFlag = false;
							}
						}
						return true;
					};
					while ( ! $finishFlag ) {
						$finishFlag = true;
						$graph->traverseBF($v, 2,$fn, array($k,$transitive_element),GDirection::OUT);
					}
			}


		}
















	}



}

?>