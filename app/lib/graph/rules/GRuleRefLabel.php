<?php
//ref_label



class GRuleRefLabel extends AbstractBaseRule implements GRule {


	/**
	 * @param GRuleContextR $context
	 * @param $args array
	 */
	public function __construct($context, $args) {
		parent::__construct($context);
	}

	protected function init(){
		$this->skip_readonly = true;
	}


	public function execute(){
		$context = $this->context;
		/*@var $graph GGraph */
		$graph = $context->graph();
		$edges = $graph->getEdges();
		foreach ($edges as $e){
			$from = $e->getVertexFrom();
			$to = $e->getVertexTO();
			$toOT = $to->getObjectType();
			$to_label = GRuleUtil::getLabel($to);
			$label = $e->label();
			if (! $e->isInferred()){
				//$this->context->addDebugMessage('EDGE2: ' . $e->element() . ' : ' .$e->label() . ' <> ' . $to_label . ' : ' . $e->data());
				//$datap = $pprop->data();

				$data	 = $e->data();
				$jdata = json_decode($data,true);
				//Log::info('##1'  . $to_label);
				if (isset($jdata['data'])){
					$d =  $jdata['data'];
					//Log::info('##2'  . json_encode($d));
					if ($label != $to_label || !isset($d['ref_ot'])){
						//Log::info('##3'  . $to_label);
					//$pprop = $e->persistenceProp();
// 						if (isset($d['rel_type']) && $d['rel_type'] == 'locked'){
// 							$e->setLabel($to_label);
// 							if (! isset($d['org_value'])){
// 								$jdata['data']['org_value']=$label;
// 								$jsonData =json_encode($jdata);
// 								$e->setData($jsonData);
// 							}
// 						} else {
// 							$jdata['data']['ref_label']=$to_label;
// 							$jsonData =json_encode($jdata);
// 							$e->setData($jsonData);
// 						}
							$e->setLabel($to_label);
							if (! isset($d['org_value'])){
								$jdata['data']['org_value']=$label;
							}
							$jdata['data']['ref_label']=$to_label;
							$jdata['data']['ref_ot']= $toOT;
							$jsonData =json_encode($jdata);
							$e->setData($jsonData);


					}
				}
			}
		}
				//$dump  = GGraphUtil::dump1($graph,false);
	}




}








