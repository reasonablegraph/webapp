<?php
class GRuleRelationControl extends AbstractGruleProcessVertice implements GRule {

	private $debug = false;
	protected function init(){
		$this->debug = (Config::get('arc.DEBUG_RELATIONS',0) > 0);
	}

	/**
	 *
	 * @param GVertex $v
	 */
	protected function processVertex($v) {
		$debug = $this->debug;
		//@DOC: RELATIONS rule-engine rule
		$context = $this->context;
		// $context->addDebugMessage('>#############################################################################');
		$relCtrl = new RelationControl (); // RC
		$v1UrnStr = $v->urnStr ();
		$edges = $v->getEdges ( GDirection::OUT );
		foreach ( $edges as $e ) {
			if (! $e->isInferred ()) {
				$key = $e->element ();
				$rel = $relCtrl->getRelation ( $key );
				if ($rel) {
					$v2UrnStr = $e->getVertexTO ()->urnStr ();
					if ($rel->isSimetric ()) {
						if ($debug){
							Log::info('@@: symetric relation: ' . $v1UrnStr . '  -['.  $key  . ']-> '. $v2UrnStr .' I: ' . $e->isInferred() .  ' ADD: ' . $v2UrnStr . ' --[' . $key .']--> ' . $v1UrnStr);
						}
						$context->addDebugMessage ( '@@: symetric relation: ' . $key  . ' ADD: ' . $v2UrnStr . ' --[' . $key .']--> ' . $v1UrnStr);
						$context->addNewEdge ( $v2UrnStr, $v1UrnStr, $key, true );
					} else if ($rel->hasReverseRelation()) {
						$revRel = $rel->getReverseRelation();
						$reverseKey = $revRel->getElement();
						if ($debug){
							Log::info('@@: reverse relation: '  . $v1UrnStr . '  --['. $key . ']--> '. $v2UrnStr  . ' ADD: ' . $v2UrnStr . ' --[' . $reverseKey .']--> ' . $v1UrnStr);
						}
						$context->addDebugMessage ( '@@: reverse relation: ' . $key . ' ADD: ' . $v2UrnStr . ' --[' . $reverseKey .']--> ' . $v1UrnStr);
						$context->addNewEdge ( $v2UrnStr, $v1UrnStr, $reverseKey, true );
					}
				}
			}
		}
		// $context->addDebugMessage('<#############################################################################');
	}
}