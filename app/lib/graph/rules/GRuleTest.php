<?php

class GRuleTest extends AbstractGruleProcessVertice implements GRule {

	protected function init(){
		$this->context->addDebugMessage("INIT");
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see AbstractGruleProcessVertice::processVertex()
	 */
	protected function processVertex($v){
		$vid = $v->id();

		$ps = $v->getProperties('ea:manif:Publication');
		if (empty($ps)){
			return;
		}
		$this->context->addDebugMessage('vertex: ' . $vid);
		foreach ($ps as $id => $p){
			//$this->context->addDebugMessage(print_r($p,true));
			$tid = $p->treeId();
			if (!empty($tid)){
				$this->context->addDebugMessage('#-----------------------------------------------------------------------------------------#');
				$this->context->addDebugMessage('treeId: ' . $tid);
				$this->context->addDebugMessage('#--------------------------------------#');
				$tps = $v->getChildProperties($tid);
				//$this->context->addDebugMessage(print_r($tp,true));
				foreach ($tps as $tp){
					 	/* @var $tp GPropertyGraph  */


//////////////////////////////////////////////////////////////////////////////////////////////////////
// PARADIGMATA JSON DATA
//////////////////////////////////////////////////////////////////////////////////////////////////////
					$data = $tp->data();//OLA TA JSON DATA SAN STRING
					$data = $tp->dataArray();//OLA TA JSON DATA SAN ARRAY
					$tp->jdata(); // ta data ton jsondata (perni ke orisma sigkekrimeno klidi)   SAN ARRAY
					$tp->prps(); // ta prps ton jsondata  (perni ke orisma sigkekrimeno klidi)   SAN ARRAY
					$tp->valueJson(); //ta json ton jsondata //gia xrisi se imerominies && urls  SAN ARRAY
//					$this->context->addDebugMessage(print_r($data,true));
//////////////////////////////////////////////////////////////////////////////////////////////////////


					$org_value = $tp->jdata('org_value');
					$jsonValue = $tp->valueJson();
					//$this->context->addDebugMessage(print_r($jsonValue,true));

					$prps = $tp->prps();
					//$this->context->addDebugMessage(print_r($prps,true));
					if (empty($tp->refItem())){
						$this->context->addDebugMessage($tp->element() . ' : ' . $tp->value()) ;
						if ($tp->element() == 'ea:manif:Publication_Date'){
							$this->context->addDebugMessage(print_r($jsonValue,true));
						}
					} else {
						$val = $tp->value();
						$el = $tp->element();
						$msg = $el . ' : ' . $tp->refItem()  . ' : ' . $val  . ' | ORG: ' . $org_value;
						$this->context->addDebugMessage($msg) ;
						Log::info($msg);
					}
				}
				$this->context->addDebugMessage('#-----------------------------------------------------------------------------------------#');
			}

		}

	}


	public function postExecute() {
	}

}
