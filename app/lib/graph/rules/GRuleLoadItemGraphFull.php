<?php 


class GRuleLoadItemGraphFull extends AbstractBaseRule implements  GRule {


	/**
	 * @param GRuleContextR $context
	 */
	public function __construct($context) {
		parent::__construct($context);
	}

	public function execute(){

		$context = $this->context;
		
		/* @var  $graph GGraph  */
		$graph = $context->graph();

		$item_id = $context->get("LOAD_ITEM_ID",null);
		if (empty($item_id) ){
			throw new Exception("GRuleLoadItemGraphDefault LOAD_ITEM_IDS param expected");
		}

		$item_refs = $context->get("LOAD_ITEM_REFS",array());

		GGraphIO::loadNodeSubGraphFull($item_id,$item_refs,$graph);

	}



}