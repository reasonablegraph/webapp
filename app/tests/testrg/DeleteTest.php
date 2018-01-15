<?php

require_once __DIR__ . '/GenericRGTestCase.php';

/**
 * @runTestsInSeparateProcesses
 */
class DeleteTest extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}

	private function validateVertex($dkey) {
		Log::info("T: VALIDATE VERTEX: " . $dkey);
		$vertex = $this->test->getVertexByContextKey($dkey);
		$this->_assertNotNull($vertex, "VERTEX INDICATED BY DELETE_KEY SHOULD NOT BE NULL");
		return $vertex;
	}

	private function deleteVertex($vertex) {
		$id = $vertex->id();
		$dbh = dbconnect();

		// delete
		$stmt = $dbh->prepare('SELECT dsd.delete_item(?)');
		$stmt->bindParam(1, $id);
		$stmt->execute();

		// count
		$stmt = $dbh->prepare('select count(item_id) as count from dsd.item2 where item_id = ?');
		$stmt->bindParam(1, $id);
		$stmt->execute();
		$r = $stmt->fetch();

		$this->_assertSame(0, $r[0], "VERTEX WAS NOT DELETED");
	}

	public function testDelete() {
		Log::info("T: testDelete");
		//$this->contextDump();

		$dkey = $this->contextGet('DELETE_KEY');
		$this->_assertNotEmpty($dkey, "DELETE_KEY NOT FOUND IN CONTEXT");

		$vertex = $this->validateVertex($dkey);
		$this->deleteVertex($vertex);
	}

}