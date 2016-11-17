<?php

class  GRuleItemOf extends AbstractBaseRule implements  GRule {

	private $add_depentencies = false;

	/**
	 * @param GRuleContextR $context
	 */
	public function __construct($context) {
		parent::__construct($context);
		$this->add_depentencies = $this->context->get("GRuleItemOf:add_depentencies",false);
	}


	public function execute(){
		$context = $this->context;

		//Log::info('GRuleItemOf execute1: ' );
		/* @var  $graph  GGraph */
		$graph = $context->graph();

		$parents = array();
		//$newEdges = array();
		$finishFlag = false;

		$vertices = $graph->getVertices();
		while(!$finishFlag){
			$finishFlag = true;
			//Log::debug("LOOP");
			foreach ($vertices as $v){
				if ($v->isOrphan()){ continue; };
				if ($v->isReadOnly()){ continue; }
				$fn = function($c, $vertex,$parent, $distance) use (&$v,&$graph,&$parents,&$newEdges,&$finishFlag,$context){
					/* @var $vertex GVertex */
					/* @var $parent GEdge */
					$parents[$vertex->urn()->toString()] = $parent;
					if ($distance == 2){
						if (!$vertex->getFirstEdge(GDirection::OUT, 'ea:inferred-item-of:',$v->urn())){
							$finishFlag = false;
							$parentVertex = $parent->getVertexFrom();
							$parentVertexUrn = $parentVertex->urn()->toString();
							$de1 = $parent;
							$de2 = $parents[$parentVertexUrn];

							$deps=array();

							//depentencies
							if ($this->add_depentencies){
								if ($de1->isInferred()){
									$deps = array_merge($deps,$de1->getDependencies());
								} else {
									$deps[] = $de1->urnStr();
								}
								if ($de2->isInferred()){
									$deps = array_merge($deps,$de2->getDependencies());
								} else {
									$deps[] = $de2->urnStr();
								}
							}

							$context->addNewEdge($v->urnStr(), $vertex->urnStr(), 'ea:inferred-item-of:',true,$deps);
							//Log::debug("GRuleItemOf ADD NEW EDGE: $ne");
							//Log::debug('ADD EDGE: '. $ne->urnStr() ." $v --> $vertex");
							//Log::info('ADD EDGE: ' . $ne->urnStr() . ' DEPS: (' . count($deps).  ') : ' . implode(', ', $deps));
						}
					}
					return true;
				};
				$graph->traverseBF($v, 2,$fn, array('ea:item-of:','ea:inferred-item-of:'));
			}


		}


		//Log::info("ne: " . count($newEdges));

	}

}

