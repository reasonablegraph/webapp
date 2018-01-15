<?php

require_once 'TestContext.php';

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Runner\BaseTestRunner;

class TestRGHelper {

	/* @var $graph GGraphO */
	private $graph;
	/* @var $context TestContext */
	private $context = null;
	/* @var $testCase TestCase */
	private $testCase = null;
	private $className;
	public static $CONTEXT_KEY_TEST_RESULTS = "phpunit";

	public function __construct($graph, $testCase) {
		// context load from /tmp/context.json
		$str = file_get_contents("/tmp/context.json");
		$this->context = new TestContext(json_decode($str, true));

		$this->graph = $graph;
		$this->testCase = $testCase;
		$this->className = get_class($testCase);

		// context initialize test info
		$test_results = (($this->context->has(TestRGHelper::$CONTEXT_KEY_TEST_RESULTS))) ? $this->context->get(TestRGHelper::$CONTEXT_KEY_TEST_RESULTS) : array();
		$test_results[$this->className] = array();
		$test_results[$this->className]['final_status'] = 'error';
		$this->context->set(TestRGHelper::$CONTEXT_KEY_TEST_RESULTS, $test_results);
		$this->context->set('PHPUNIT_FINAL_STATUS', 'error');
	}

	//// CONTEXT METHODS ////

	/**
	 * @return TestContext
	 */
	public function getContext() {
		return $this->context;
	}

	public function contextSet($key, $value) {
		return $this->context->set($key, $value);
	}

	public function contextGet($key,$defaultValue=null) {
		return $this->context->get($key,$defaultValue);
	}

	public function contextHas($key) {
		$this->context->has($key);
	}

	public function contextDump() {
		$this->context->dump();
	}




	public function writeContext() {
		// final status
		Log::info("T: phpunit status: " . $this->testCase->getStatus());
		$test_results = $this->context->get(TestRGHelper::$CONTEXT_KEY_TEST_RESULTS);
		$final_status = ($this->testCase->getStatus() === PHPUnit_Runner_BaseTestRunner::STATUS_PASSED) ? 'ok' : 'error';
		$test_results[$this->className]['final_status'] = $final_status;
		$this->context->set(TestRGHelper::$CONTEXT_KEY_TEST_RESULTS, $test_results);
		$this->context->set('PHPUNIT_FINAL_STATUS', $final_status);

		// write back to context
		$str = json_encode($this->context->asArray());
		file_put_contents("/tmp/context.json", $str);
	}

	/**
	 * @param string $contextKey
	 * @return GVertex|null
	 */
	public function getVertexByContextKey($contextKey) {

		$id = null;
		$id1 = ($this->context->has($contextKey)) ? (int) $this->context->get($contextKey)['item_id'] : null;

		$id2=null;
		$dbh = dbconnect();

		$SQL="select count(*)  from dsd.metadatavalue2 where element = 'ea:test:key1' AND text_value=?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $contextKey);
		$stmt->execute();
		$rec = $stmt->fetch();
		$count = (int) $rec[0];
		$this->assertSame(1,$count,$contextKey  . ' HAS NO OR MANY VERTICES WITH ea:test:key1 (is database clean? )');


		$SQL="select item_id  from dsd.metadatavalue2 where element = 'ea:test:key1' AND text_value=?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $contextKey);
		$stmt->execute();
		if ($rec = $stmt->fetch()) {
			$id2 = (int) $rec[0];
		}

//		//Log::info("T: " . $contextKey .  " ID1: " . $id1 . " ID2: " . $id2);
//		if (!empty($id1) && empty($id2)){
//			Log::info("T: WARNING: " . $contextKey . ' HAS context_key BUT NO ea:test:key1 ');
//		}

		if (!empty($id1) && !empty($id2)) {
			$this->assertSame($id1, $id2, 'CONTEXT_KEY: ' . $contextKey . ' ID FROM context_key AND ea:test:key1 NOT MATCH');
		}
		$id = empty($id1) ? $id2 :$id1;
		if (empty($id)){
			return null;
		}
		$urnStr = GURN::createOLDWithId($id);
		$v = $this->graph->getVertex($urnStr);
		return $v;
	}

	/**
	 * @param string $key1
	 * @param string $key2
	 * @param GDirection $direction
	 * @param string $element
	 */
	public function checkRelationUnique($key1, $key2, $direction, $element, $inferred, $reltype) {
		$v = $this->getVertexByContextKey($key1);
		$this->assertNotNull($v);

		$v2 = $this->getVertexByContextKey($key2);
		$this->assertNotNull($v2);
		$v2IdOK = $v2->id();

		$edges = $v->getEdges($direction, array($element));
		$this->assertCount(1, $edges);

		$v2Rel = array_shift($edges);

		if ($direction === GDirection::IN) {
			$targetId = $v2Rel->getVertexFrom()->id();
		} else if ($direction === GDirection::OUT) {
			$targetId = $v2Rel->getVertexTo()->id();
		}

		$this->assertSame($v2IdOK, $targetId);
	}

	/**
	 * @param string  $from
	 * @param string  $to
	 * @param string  $element
	 * @param boolean $inferred
	 */
	public function checkRelation($from, $to, $element, $inferred,$reltype,$text_value) {
		$edgeLabel = '|'. $from . ' -[' . $element . ']-> ' . $to . '|';
		Log::info("T: checkRelation: " . $edgeLabel);

		$v = $this->getVertexByContextKey($from);
		$this->assertNotNull($v, 'VERTEX: ' . $from . ' NOT FOUND');

		$v2 = $this->getVertexByContextKey($to);
		$this->assertNotNull($v2, 'VERTEX: ' . $to . ' NOT FOUND');

		if (empty($v)||empty($v2)){
			return;
		}

		$edges = $v->getEdges(GDirection::OUT, $element);
		$edgeFound = (!empty($edges));
		$this->assertTrue($edgeFound, "NOT FOUND EDGE: " . $edgeLabel);

		$validation_version = $this->contextGet("validation_version");
		//Log::info("VALIDATION VERSION: " . $validation_version);
		foreach ($edges as $edge) {
			if ($edge->getVertexTO()->id() === $v2->id()) {

				if ($validation_version >= 1) {
					$pp = $edge->persistenceProp();
					if (!empty($pp)) {
						//Log::info("T: checkRelType: " . $reltype);
						$actual_reltype = $pp->relType();
						$this->assertSame($reltype, $actual_reltype, 'RELTYPE NOT MATCH');
					}
				}
				$this->assertTrue($inferred == $edge->isInferred(), $edgeLabel . " FAILED INFERRED TEST ");
				$this->assertSame($text_value,$edge->label()  , $edgeLabel . " EDGE LABEL DIFFER ");
			}
		}
	}

	/**
	 * @param int      $okVerticesCount
	 * @param int      $okEdgesCount
	 */
	public function checkCounts($okVerticesCount, $okEdgesCount) {
		$g = $this->graph;
		$cv = $g->countVertices();
		$ce = $g->countEdges();

		Log::info(sprintf("T: VERTICES COUNT: %s in clean db expect: %s", $cv, $okVerticesCount));
		Log::info(sprintf("T: EDGES COUNT   : %s in clean db expect: %s", $ce, $okEdgesCount));

		$this->assertSame($okVerticesCount, $cv);
		$this->assertSame($okEdgesCount, $ce);
	}





	/**
	 * @param PHPUnit_Framework_AssertionFailedError $e
	 * @param string $message
	 */
	private function assertionFailed($e, $message = null) {
		$this->contextSet('PHPUNIT_HAS_FAILURES',true);
		$stop_on_failure = $this->contextGet('PHPUNIT_STOP_ON_FAILURE',true);
		if ($stop_on_failure == 'false'||$stop_on_failure == 'FALSE'){
			$stop_on_failure = false;
		};
		$final_message = $e->getMessage()  . "\n" . $message;
		//$final_message = $message . "\n" . $e->getMessage();
		$failures = $this->contextGet('PHPUNIT_FAILURES',array());
		$failures[] = (array('msg'=>$final_message));
		Log::info("T: assertFail: " . $final_message);
		$failures = $this->contextSet('PHPUNIT_FAILURES',$failures);
		if ($stop_on_failure) {
			TestCase::fail($final_message);
		}

	}



	//// ASSERTS ////
	public function  assertLessThan($expected, $actual, $message = ''){
		try {
			TestCase::assertLessThan($expected, $actual, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg = $actual . " NOT LESS THAN EXPECTED: " . $expected;
			$this->assertionFailed($e, $msg);
		}
	}
	public function  assertLessThanOrEqual($expected, $actual, $message = ''){
		try {
			TestCase::assertLessThanOrEqual($expected, $actual, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg = $actual . " NOT LESS OR EQUAL THAN EXPECTED: " . $expected;
			$this->assertionFailed($e, $msg);
		}
	}

	public function assertSame($expected, $actual, $message = '') {
		try {
			TestCase::assertSame($expected, $actual, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg = $actual . " NOT SAME WITH EXPECTED: " . $expected;
			$this->assertionFailed($e, $msg);
		}
	}

	public function assertNotSame($expected, $actual, $message = '') {
		try {
			TestCase::assertNotSame($expected, $actual, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg = $actual . " IS SAME WITH EXPECTED: " . $expected;
			$this->assertionFailed($e, $msg);
		}
	}

	public function assertNull($actual, $message = '') {
		try {
			TestCase::assertNull($actual, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg = $actual . 'is not null';
			$this->assertionFailed($e, $msg);
		}


	}

	public function assertNotNull($actual, $message = '') {
		try {
			 TestCase::assertNotNull($actual, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg = $actual . 'is  null';
			$this->assertionFailed($e, $msg);
		}
	}

	public function assertCount($expectedCount, $haystack, $message = '') {
		try {
			TestCase::assertCount($expectedCount, $haystack, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg = 'collection count is not equal to expected ' + $expectedCount;
			$this->assertionFailed($e, $msg);
		}
	}

	public function assertEmpty($actual, $message = '') {
		try {
			TestCase::assertEmpty($actual, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg = $actual + ' is  not empty';
			$this->assertionFailed($e, $msg);
		}
	}

	public function assertNotEmpty($actual, $message = '') {
		try {
			TestCase::assertNotEmpty($actual, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg =  ' actual is empty';
			$this->assertionFailed($e, $msg);
		}
	}

	public function assertTrue($condition, $message = '') {
		try {
			TestCase::assertTrue($condition, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg =  ' condition is false';
			$this->assertionFailed($e, $msg);
		}
	}

	public function assertFalse($condition, $message = '') {
		try {
			TestCase::assertFalse($condition, $message);
		}catch (PHPUnit_Framework_AssertionFailedError $e) {
			$msg =  ' condition is true';
			$this->assertionFailed($e, $msg);
		}

	}

}


