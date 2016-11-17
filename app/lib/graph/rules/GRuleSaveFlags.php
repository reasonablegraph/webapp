<?php

class GRuleSaveFlags extends AbstractGruleProcessVertice implements GRule {

	private $flags = array();

	/**
	 *
	 * @param GVertex $v
	 */
	protected function processVertex( $v){

		$json_flags = $v->getFlagsJson();
		if (!empty($json_flags)){
			$this->flags[$v->id()] = $json_flags;
		}

	}



	public function postExecute() {
		$this->context->put('FLAGS_DATA', $this->flags);
		$this->context->putCommand("SAVE-FLAGS", new GRuleSaveFlagsCmd());
	}



}


class GRuleSaveFlagsCmd  implements  GCommand {

	protected function init(){
		$this->skip_readonly = true;
	}

	/**
	 *
	 * @param GRuleContextR $context
	 */
	public function execute($context){
		/** @var $flags string[] */
		$flags = $context->get('FLAGS_DATA');

		$con = prepareService();
 		$SQL = 'UPDATE dsd.item2 SET flags=dsd.jsonb_arr2text_arr(?) WHERE item_id =?';
		/* @var $stmt PDOStatement */
 		$stmt = $con->prepare($SQL);

		foreach ($flags as $id=>$item_flags){
			$stmt->bindParam(1,$item_flags);
			$stmt->bindParam(2,$id);
			$GLOBALS['GRuleSaveFlagsCmd-ITEMID'] = $id;
			$stmt->execute();
		}

	}

}


?>