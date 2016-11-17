<?php


class  GRuleExample extends AbstractBaseRule implements  GRule {
	/**
	 * @param GRuleContextR $context
	 * @param $args array
	 */
	public function __construct($context, $args) {
		parent::__construct($context);
	}

	public function execute(){
		$context = $this->context;
		$graph = $context->graph();
		$context->addDebugMessage("example debug msg");
		//GGraphUtil::dumpVertex($v);
		//$dump  = GGraphUtil::dump1($graph,false);
	}

}


class GRuleExampleVertice extends AbstractGruleProcessVertice implements GRule {

	protected function init(){
		$this->context->addDebugMessage("GRuleExampleVertice INIT");
	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		$context = $this->context;
		$graph = $v->graph();
		$context->addDebugMessage("GRuleExampleVertice process: " . $v);
		//$dumpV  = GGraphUtil::dumpVertex($v,false);

	}


}





