<?php

class GRuleRemoveFlags extends AbstractGruleProcessVertice implements GRule {

	/**
	 *
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		$v->removeFlags();
	}


}
?>

