<?php

class GRuleSleepCmd implements GCommand {

	private $interval;
	private $prefix;
	private $suffix;

	public function __construct($interval, $prefix, $suffix) {
		$this->interval = $interval;
		$this->prefix = $prefix;
		$this->suffix = $suffix;
	}

	public function execute($context) {

		$interval = $this->interval;
		$prefix = $this->prefix;
		$suffix = $this->suffix;

		PUtil::log("#" . $prefix . "# #" . $suffix . "#: GRuleSleepCmd: START sleeping for: " . $interval . "seconds, pid: " . getmypid());
		sleep($interval);
		PUtil::log("#" . $prefix . "# #" . $suffix . "#: GRuleSleepCmd: STOP sleeping, pid: "  . getmypid());
	}


}

class GRuleSleep extends AbstractBaseRule implements GRule {

	private $sleep_interval = 35;
	private $prefix = 'wdebug';
	private $suffix = 'ssss';
	private $mode = 'rulecmd';

	/**
	 * @param GRuleContextR $context
	 * @param $args array
	 */
	public function __construct($context, $args) {
		if (!empty($args)) {
			if (isset($args['sleep_interval'])) {
				$this->sleep_interval = intval($args['sleep_interval']);
			}
			if (isset($args['prefix'])) {
				$this->prefix = $args['prefix'];
			}
			if (isset($args['suffix'])) {
				$this->suffix = $args['suffix'];
			}
			if (isset($args['mode'])) {
				$this->mode = $args['mode'];
			}
		}
		parent::__construct($context);
	}


	public function execute() {

		$context = $this->context;
		$interval = $this->sleep_interval;
		$prefix = $this->prefix;
		$suffix = $this->suffix;

		if ($this->mode == 'rulecmd') {
			$cmd = new GRuleSleepCmd($interval, $prefix, $suffix);
			$context->putCommand('RULE_SLEEP', $cmd);
		} else {
			PUtil::log("#" . $prefix . "# #" . $suffix . "#: GRuleSleep: START sleeping for: " . $interval . "seconds, pid: " . getmypid());
			sleep($interval);
			PUtil::log("#" . $prefix . "# #" . $suffix . "#: GRuleSleep: STOP sleeping, pid: "  . getmypid());
		}

	}

}








