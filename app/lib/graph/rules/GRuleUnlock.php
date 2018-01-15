<?php

class GRuleUnlockCmd implements GCommand {

	private $rlock;
	private $submit_id;
	private $item_id;
	private $prefix;
	private $suffix;

	public function __construct($rlock, $submit_id, $item_id, $prefix, $suffix) {
		$this->rlock = $rlock;
		$this->submit_id = $submit_id;
		$this->item_id = $item_id;
		$this->prefix = $prefix;
		$this->suffix = $suffix;
	}

	public function execute($context) {

		$rlock = $this->rlock;
		$prefix = $this->prefix;
		$suffix = $this->suffix;

		if (!empty($rlock) && $rlock instanceof GRuleEngineLock) {
			PUtil::log("#" . $prefix . "# #" . $suffix . "#: GRuleUnlockCmd: lock release, pid: " . getmypid());
			$rlock->release();
		}

		$submit_id = $this->submit_id;
		$item_id = $this->item_id;
		if (!empty($submit_id) && !empty($item_id)) {
			// release and update submits table
			PUtil::log("#" . $prefix . "# #" . $suffix . "#: GRuleUnlockCmd: release/finish submit: " . $submit_id . " for item: " . $item_id . " pid: " . getmypid());
			PDao::update_submits_status($submit_id, $item_id, SubmitsStatus::$finished);
		}

	}


}

class GRuleUnlock extends AbstractBaseRule implements GRule {

	private $prefix = 'wdebug';
	private $suffix = 'R';

	/**
	 * @param GRuleContextR $context
	 * @param $args array
	 */
	public function __construct($context, $args) {
		if (!empty($args)) {
			if (isset($args['prefix'])) {
				$this->prefix = $args['prefix'];
			}
			if (isset($args['suffix'])) {
				$this->suffix = $args['suffix'];
			}
		}
		parent::__construct($context);
	}


	public function execute() {

		$context = $this->context;
		$rlock = $context->get('RLOCK', null);
		$submit_id = $context->get('SUBMIT_ID', null);
		$item_id = $context->get("LOAD_ITEM_ID", null);

		$cmd = new GRuleUnlockCmd($rlock, $submit_id, $item_id, $this->prefix, $this->suffix);
		$context->putCommand('RULE_UNLOCK',  $cmd);

	}

}








