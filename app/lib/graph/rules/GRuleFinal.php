	<?php



	class  GRuleSaveFinalCmd  implements  GCommand {


		/**
		 * @param GRuleContextR context
		 */
		public function __construct() {
		}

		public function execute($context){

			Log::info('##############################################################');
			Log::info('################ Graph Reset Finish Commands #################');
			Log::info('##############################################################');

		}
	}



	class  GRuleSaveFinal extends AbstractBaseRule implements  GRule {

		public function __construct($context, $args) {
			parent::__construct($context);
		}

		public function execute(){

			$context =$this->context;

			$context->putCommand("SAVE-FINAL", new GRuleSaveFinalCmd());

			Log::info('##############################################################');
			Log::info('################ Graph Reset Finish Rules ####################');
			Log::info('##############################################################');
		}

	}




