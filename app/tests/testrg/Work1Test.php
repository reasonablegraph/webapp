<?php

require_once 'GenericRGTestCase.php';

/**
 * @runTestsInSeparateProcesses
 */
class Work1Test extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function testRelations() {
		Log::info("T: testRelations");

		$this->test->checkRelationUnique("WORK1", "MANIFESTATION1", GDirection::IN, "inferred:ea:work:", true);
		$this->test->checkRelationUnique("MANIFESTATION1", "WORK1", GDirection::OUT, "inferred:ea:work:", true);

		$this->test->checkRelation("WORK1", "MANIFESTATION1", GDirection::IN, "inferred:ea:work:", true);
		$this->test->checkRelation("MANIFESTATION1", "WORK1", GDirection::OUT, "inferred:ea:work:", true);
	}

	public function testJdata() {
		Log::info("T: testJdata");

		$contextKey = "WORK1";

		$v = $this->test->getVertexByContextKey($contextKey);
		$this->_assertNotNull($v);

		$ctx = $this->test->getContext();
		$opac1Data = $v->getAttribute('opac1');
		$opac2Data = $v->getAttribute('opac2');

		$this->_assertSame($v->id(), $opac1Data['id']);
		$this->_assertSame($v->getObjectType(), $opac1Data['obj_type']);
		$this->_assertSame($ctx->get($contextKey)['label'], $opac2Data['Title_punc']);
		$this->_assertSame($ctx->get($contextKey)['label'], $v->getAttribute('label'));
	}

}
