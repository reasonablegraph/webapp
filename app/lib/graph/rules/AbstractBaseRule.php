<?php

abstract class AbstractBaseRule implements GRule {

	/*@var $context GRuleContextR */
	protected $context;



	/**
	 *
	 * @param GRuleContextR $context
	 */
	public function __construct($context, $args= null) {
		$this->context = $context;
	}








}




?>