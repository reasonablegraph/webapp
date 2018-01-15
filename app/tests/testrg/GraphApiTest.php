<?php

require_once __DIR__ . '/GenericRGTestCase.php';

/**
 * @runTestsInSeparateProcesses
 */
class GraphApiTest extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}


	private function createVertex($id,$title){
		$v1 = $this->graph->createVertex(GURN::createNew($id));
		$v1->addPropertyValue('dc:title:',$title);
		return $v1;
	}

	/////////////////////////////////////////////////////////////////////////////////////////
	//TEST1
	/////////////////////////////////////////////////////////////////////////////////////////
	public function test1() {
		Log::info("T: test1");

		$self = $this;
		$g = $this->graph;
		$v1 = $this->createVertex(1,'v1');
		$v2 = $this->createVertex(2,'v2');
		$g->addEdge($v1->urnStr(),$v2->urnStr(),'ea:relation:other');

		$v1_edges=$v1->getEdges(GDirection::BOTH);
		$v2_edges=$v2->getEdges(GDirection::BOTH);
		$this->_assertSame(count($v1_edges),1,'v1 edges count');
		$this->_assertSame(count($v2_edges),1,'v2 edges count');
		$e1 = $v1->getFirstEdge(GDirection::OUT,'ea:relation:other');
		$e2 = $v2->getFirstEdge(GDirection::IN,'ea:relation:other');
		$this->_assertNotEmpty($e1,'edge1 test1');
		$this->_assertNotEmpty($e2,'edge1 test2');
		$this->_assertSame($e1->urnStr(),$e2->urnStr(),'e1 <> e2');

		$this->_assertSame('v1',$v1->getPropertyValue('dc:title:'),'v1 title');
		$this->_assertSame('v2',$v2->getPropertyValue('dc:title:'),'v2 title');
		GGraphUtil::dumpGraphviz($g,"/tmp/api-g1.dot",true,false,true,"g1",'/tmp/api-g1.txt');

		Log::info("T: #1");
		$work1 = $this->test->getVertexByContextKey("WORK1");
		$this->_assertNotEmpty($work1,'WORK1 NOT FOUND');

		Log::info("T: TRAVERSE BF");
		Log::info("===========================================");
		//($root, $maxDistance, $handler, $elements = null, $direction=GDirection::OUT, $vertexFilter = null)
		//$handler($c, $s, $e, $distance)
		$maxC= 0;
		$g->traverseBF($work1,null,function($c,$s,$e,$distance)use($self,&$maxC){
			$maxC = $c;
			/* @var $s GVertex  */
			/* @var $e GEdge  */
			//Log::info($c);
			$KEY=$s->getPropertyValue('ea:test:key1');
			Log::info($c. "," . $KEY);
			if ($c == 1){
				$self->_assertSame($KEY,'PERSON1','traverseBF 1');
				$self->_assertSame(1,$distance);
			} elseif ($c == 2){
				$self->_assertSame($KEY,'EXPRESSION1','traverseBF 2');
				$self->_assertSame(1,$distance);
			} elseif ($c == 3){
				$self->_assertSame($KEY,'CHAIN2','traverseBF 3');
				$self->_assertSame(1,$distance);
			} elseif ($c == 4){
				$self->_assertSame($KEY,'CONCEPT1','traverseBF 4');
				$self->_assertSame(1,$distance);
			} elseif ($c == 5){
				$self->_assertSame($KEY,'CONCEPT21','traverseBF 5');
				$self->_assertSame(2,$distance);
			} elseif ($c == 6){
				$self->_assertSame($KEY,'CONCEPT22','traverseBF 6' );
				$self->_assertSame(2,$distance);
			} elseif ($c == 7){
				$self->_assertSame($KEY,'MANIFESTATION1','traverseBF 7');
				$self->_assertSame(2,$distance);
			} elseif ($c == 8){
				$self->_assertSame($KEY,'PUBLISHER1','traverseBF 8');
				$self->_assertSame(3,$distance);
			} elseif ($c == 9){
				$self->_assertSame($KEY,'PLACE1','traverseBF 9');
				$self->_assertSame(3,$distance);
			}
//			Log::info($c);
//			Log::info($distance);
//			Log::info($s->urnStr());
//			Log::info($e->vkey());
//			Log::info("===========================================");
			return true;
		});
		$this->_assertSame($maxC,9);
		Log::info($maxC);
		Log::info("===========================================");

		Log::info("T: TRAVERSE DF");
		Log::info("===========================================");
		//$root, $handler, $elements = null, $direction = GDirection::OUT\
		//$handler($c, $v, $edge, $root);
		$g->traverseDF($work1,function($c,$s,$e,$root)use($self,&$maxC){
			$maxC = $c;
			/* @var $s GVertex  */
			/* @var $e GEdge  */
			//Log::info($c);
			$KEY=$s->getPropertyValue('ea:test:key1');
			Log::info($c. "," . $KEY);
			if ($c == 1){
				$self->_assertSame($KEY,'PERSON1','traverseDF 1');
			} elseif ($c == 2){
				$self->_assertSame($KEY,'EXPRESSION1','traverseDF 2');
			} elseif ($c == 3){
				$self->_assertSame($KEY,'MANIFESTATION1','traverseDF 3');
			} elseif ($c == 4){
				$self->_assertSame($KEY,'PUBLISHER1','traverseDF 4');
			} elseif ($c == 5){
				$self->_assertSame($KEY,'PLACE1','traverseDF 5');
			} elseif ($c == 6){
				$self->_assertSame($KEY,'CHAIN2','traverseDF 6');
			} elseif ($c == 7){
				$self->_assertSame($KEY,'CONCEPT21','traverseDF 7');
			} elseif ($c == 8){
				$self->_assertSame($KEY,'CONCEPT22','traverseDF 8');
			} elseif ($c == 9){
				$self->_assertSame($KEY,'CONCEPT1','traverseDF 9');
			}

			return true;
		});
		Log::info($maxC);
		$this->_assertSame($maxC,9);
		Log::info("===========================================");
	}


}