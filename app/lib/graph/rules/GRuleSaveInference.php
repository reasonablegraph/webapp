	<?php






// 	class  GRulePostSaveInference  implements  GRule {

// 		/*@var $context GRuleContextR */
// 		private $context;

// 		/**
// 		 * @param GRuleContextR context
// 		 */
// 		public function __construct($context) {
// 			$this->context = $context;
// 		}

// 		public function execute(){
// 			$context = $this->context;

// 			//$context->addDebugMessage('GRulePostSaveInference exec');

// 			$graph  = $context->graph();

// 			$des2 = $graph->getInferredEdges();
// 			foreach ( $des2 as $e ) {
// 				GGraphUtil::saveEdge($e);
// 			}
// 			//$context->addDebugMessage( "POSTSAVE: HAS POST_RULES: "   . ($context->hasPostRules() ? 'TRUE' : 'FALSE'));
// 			if ($context->hasPostRules() ){
// 				$context->addPostRule(new GRulePostSaveInference($context));
// 			}
// 		}

// 	}


	class  GRulePostSaveInferenceCmd  implements  GCommand {

		/*@var $context GRuleContextR */
		private $graph;

		/**
		 * @param GRuleContextR context
		 */
		public function __construct($graph) {
			$this->graph = $graph;
		}

		public function execute($context){

			$graph  = $this->graph;

			GGraphUtil::saveUpdatesTracker($graph);

			$des2 = $graph->getInferredEdges();
			foreach ( $des2 as $e ) {
				GGraphUtil::saveEdge($e);
			}
		}
	}


	class  GRuleSaveInference extends AbstractBaseRule implements  GRule {

		public function __construct($context, $args) {
			$this->saveInferenceFlag = (Config::get('arc.SAVE_INFERENCE_AS_TUPLE',1) > 0);
			parent::__construct($context);
		}

		public function execute(){
			if (! $this->saveInferenceFlag){  Log::info("SKIP GRuleSaveInference: SAVE_INFERENCE_AS_TUPLE == 0"); return; }
			$context =$this->context;
			$context->putCommand("SAVE-INFERENCE", new GRulePostSaveInferenceCmd($context->graph()));
		}

	}




