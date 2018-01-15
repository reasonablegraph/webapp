<?php




class GRuleDeleteItemCmd implements  GCommand {

	private $itemId;

	public function __construct($itemId) {
		$this->itemId = $itemId;
	}

	/**
	 * @param GRuleContextR $context
	 */
	public function execute($context){
		//Log::info("GRuleDeleteItemCmd execute " . $this->itemId );
		PDao::delete_item($this->itemId);

	}

}







