<?php


class  GRuleParentElement  extends AbstractBaseRule implements  GRule {

	private $parents = array();

	/**
	 * @param GRuleContextR $context
	*/
	public function __construct($context) {
		parent::__construct($context);
		//Log::info("GRuleParentElement INIT");
// 		$con = dbconnect();
// 		$SQL ='SELECT element,parent_element from dsd.item_relation_type where parent_element is not null';
// 		$st = $con->prepare($SQL);
// 		$st->execute();
// 		while($row = $st->fetch(PDO::FETCH_ASSOC)){
// 			$this->parents[$row['element']] = $row['parent_element'];
// 		}

		//relation_elements = relation_elements

		$rels = Setting::get('relation_elements');
		foreach ($rels  as $k => $v){
			if (isset($v['parent_element']) && !empty($v['parent_element'])){
			//	Log::info($k . " : " . $v['parent_element']) ;
			$this->parents[$k] = $v['parent_element'];
			}
		}

	}

	public function execute(){
		$context = $this->context;

		$graph = $context->graph();
		$edges = $graph->getEdges();
		foreach($edges as $e){
			$el = $e->element();
			if (isset($this->parents[$el])){
				//Log::info("GRuleParentElement: found parent for: $e");
				$parent = $this->parents[$el];
				$fromVertex = $e->getVertexFrom();
				if ($fromVertex->isReadOnly()){
					Log::info("READONLY SKIP (3)");
					continue;
				}
				$urnStrFrom = $fromVertex->urnStr();
				$urnStrTo = $e->getVertexTO()->urnStr();
				$context->addNewEdge($urnStrFrom, $urnStrTo, $parent,true,null,$e->label());
				//addNewEdge($v1UrnStr, $v2UrnStr, $element, $derivative = true ,$deps = null,$label=null);

				//$context->addPropertyValue($urnStrFrom, 'ea:status:comment', 'value: ' . $urnStrTo);
			}
		}
	}

}

