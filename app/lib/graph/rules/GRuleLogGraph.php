<?php
class GRuleLogGraph  extends AbstractBaseRule implements  GRule {

	private $title = null;
	/**
	 * @param GRuleContextR $context
	 */
	public function __construct($context,$args) {

		if (!empty($args)){
			if (isset($args['title'])){
				$this->title = $args['title'];
			}
		}

		parent::__construct($context);
	}

	public function execute(){
		Log::info("GRuleLogGraph");
		$context = $this->context;
		$g = $context->graph();
		//GGraphUtil::dump1($g,true);
		GGraphUtil::dumpEdges($g,$this->title);

	}

}





