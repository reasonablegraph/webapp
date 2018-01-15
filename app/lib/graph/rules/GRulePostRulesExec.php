<?php

class GRulePostRulesExec extends AbstractBaseRule  {

	/**
	 * @var GRuleContextO
	 */
	private $context;

	/**
	 * @param GRuleContextR $context
	 */
	public function __construct($context, $args) {
		parent::__construct($context);
		$this->context = $context;
	}


	public function execute() {
		$this->context->executePostRules();
	}

}

