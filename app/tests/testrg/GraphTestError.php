<?php



/**
 * @runTestsInSeparateProcesses
 */
class GraphTestError extends TestCase {

	protected $stack;


////	/* @var $graph GGraphO  */
////	protected static $graph;
//
//	//SKANE
////	public function setUp() {
////	}
////	public static function setUpBeforeClass()
////	{
////		self::$graph = GGraphIO::loadGraph();
////	}
//
	public function testINIT() {
		echo "\n TestINIT \n";
		$this->stack = [];
	}

	/**
	 * @depends testINIT
	 */
	public function testLoadGraph() {
		echo "\n testLoadGraph \n";
		$graph = GGraphIO::loadGraph();
		array_push($this->stack, $graph);
//
//		//self::$graph = GGraphIO::loadGraph();
////		$graph = GGraphIO::loadGraph();
////		$GLOBALS['TEST_GRAPH'] = $graph;
//		echo "GRAPH LOADED";
//
//		echo "\n";
	}
//
//
	/**
	 * @depends testLoadGraph
	 */
	public function testCounts(){
		echo "\n testCounts \n";
//		//$g = self::$graph;
//		$g = $GLOBALS['TEST_GRAPH'];
//
//		$cv = $g->countVertices();
//		$ce = $g->countEdges();
//		printf("VERTICES COUNT: %s\n",$cv);
//		printf("EDGES COUNT   : %s\n",$ce);
//		$this->assertSame(15,$cv);
//		$this->assertSame(28,$ce);

		echo "\n";
	}
//

}
