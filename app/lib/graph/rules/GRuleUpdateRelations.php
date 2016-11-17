<?php

class GRuleUpdateRelations  extends AbstractBaseRule implements  GRule {


	/**
	 * @param GRuleContextR $context
	 */
	public function __construct($context,$args) {
		parent::__construct($context);
	}

	public function execute(){

		$context = $this->context;
		//$graph = $context->graph();
		$context->addDebugMessage("ea:manif:digital-item -> ea:artifact-of:");

		$dbh = dbconnect();
		$SQL = "update dsd.metadatavalue2 set element = 'ea:artifact-of:', text_value=item_id,ref_item=item_id,item_id=ref_item,data=null,  metadata_field_id =(select metadata_field_id from public.metadatafieldregistry where full_element ='ea:artifact-of:' )  WHERE element='ea:manif:digital-item'";
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		//$count = $stmt->rowCount();

	}

}







