<?php
class GraphController extends BaseController {


	public function graphviz() {
		Log::info('GraphController:graphviz');
		$graph = null;
		$inferred_flag = ('0' != get_get('inferred','1'));
		//$neighbourhood_flag = ('0' != get_get('neighbourhood','0'));
		$neighbourhood_flag = get_get('neighbourhood','0');
// 		$item_id = isset($_GET['i']) ? $_GET['i'] : null;
// 		if (! empty($item_id)) {
// 			// Log::info("load graph for item: " . $item_id);

// 			// $graph = GGraphIO::loadNodeSubGraph($item_id);
// 			$graph = GGraphIO::loadNodeSubGraphFull($item_id);
// 		} else {
// 			// Log::info("load graph");
// 			$graph = GGraphIO::loadGraph();
// 			// $graph = GGraphIO::loadNodeSubGraph(array(108,111));
// 		}

// 		if ($inferred_flag){
// 			$context= $this->graphResetFn(false);
// 			/** @var $context GRuleContextR **/
// 			$graph = $context->graph();
// 		} else {
// 			$graph = GGraphIO::loadGraph();
// 		}
		$graph = GGraphIO::loadGraph(null,$inferred_flag);



		// $this->testRuleItemOf($graph);
		// $des = $graph->getInferredEdges();
		// foreach ($des as $e){
		// Log::info("save edge: " . $e);
		// GGraphUtil::saveEdge($e);
		// }

		// GGraphUtil::dump1($graph);

		GGraphUtil::dumpGraphviz($graph,null,$inferred_flag,$neighbourhood_flag);
	}
	public function dump() {
		$graph = GGraphIO::loadSubGraph();
		GGraphUtil::dump1($graph);
	}
	private function testTraverse1($graph) {
		$maxDistance = 0;
		$roots = $graph->getRoots();
		foreach ( $roots as $root ) {
			echo ("START TRAVERSE BF: $root \n");
			$graph->traverseBF($root, $maxDistance, function ($c, $vertex, $parent, $distance) {
				/* @var $vertex GVertex */
				/* @var $parent GEdge */
				echo ('a/a: ' . $c . " distance: " . $distance . " vertex: " . $vertex->id() . " parent: " . $parent->getVertexFrom()->id() . "\n");
				// echo('a/a: ' . $c . " distance: " .$distance . " vertex: " . $vertex->id() . "\n");
				return true;
			});
		}
		echo ("\n");
		echo ("\n");
	}
	private function testTraverse2($graph) {
		$roots = $graph->getRoots();
		foreach ( $roots as $root ) {
			echo ("START TRAVERSE DF1: $root \n");
			$graph->traverseDF($root, function ($c, $vertex, $parent) {
				echo ('a/a: ' . $c . " vertex: " . $vertex->id() . " parent: " . $parent->getVertexFrom()->id() . "\n");
				return true;
			});
		}

		echo ("\n");
		echo ("\n");
	}
	private function testTraverse3($graph) {
		$roots = $graph->getRoots();
		foreach ( $roots as $root ) {
			echo ("START TRAVERSE DF2: $root \n");
			$graph->traverseDF($root, function ($c, $vertex, $parent) {
				echo ('a/a: ' . $c . " vertex: " . $vertex->id() . " parent: " . $parent->getVertexFrom()->id() . "\n");
				return true;
			}, array('ea:item-of:' ));
		}

		echo ("\n");
		echo ("\n");
	}
	private function dumpVerticeAttribures($graph) {
		foreach ( $graph->getVertices() as $v ) {
			echo ($v);
			echo ("\n");
			print_r($v->getTmpAttributes());
		}
	}
	private function testRootsLeafs($graph) {
		echo ("ROOTS\n");
		$roots = $graph->getRoots();
		foreach ( $roots as $v ) {
			echo ($v);
			echo ("\n");
		}

		echo ("LEAFS\n");
		$leafs = $graph->getLeafs();
		foreach ( $leafs as $v ) {
			echo ($v);
			echo ("\n");
		}
	}

	/**
	 *
	 * @param GGraph $graph
	 */
	private function testRuleItemOf($graph) {
		$parents = array();
		$newEdges = array();
		$finishFlag = false;

		$vertices = $graph->getVertices();
		while ( ! $finishFlag ) {
			$finishFlag = true;
			// Log::debug("LOOP");
			foreach ( $vertices as $v ) {

				if ($v->isOrphan()) {
					continue;
				}
				;
				$fn = function ($c, $vertex, $parent, $distance) use(&$v, &$graph, &$parents, &$newEdges, &$finishFlag) {
					/* @var $vertex GVertex */
					/* @var $parent GEdge */
					$parents[$vertex->urn()->toString()] = $parent;
					if ($distance == 2) {
						if (! $vertex->getFirstEdge(GDirection::OUT, 'ea:inferred-item-of:', $v->urn())) {
							$finishFlag = false;
							$parentVertex = $parent->getVertexFrom();
							$parentVertexUrn = $parentVertex->urn()->toString();
							$de1 = $parent;
							$de2 = $parents[$parentVertexUrn];

							$deps = array();
							if (false) {
								if ($de1->isInferred()) {
									$deps = array_merge($deps, $de1->getDependencies());
								} else {
									$deps[] = $de1->urnStr();
								}
								if ($de2->isInferred()) {
									$deps = array_merge($deps, $de2->getDependencies());
								} else {
									$deps[] = $de2->urnStr();
								}
							}

							$ne = $graph->addEdge($v, $vertex, 'ea:inferred-item-of:', $deps);
							$newEdges[$ne->urnStr()] = $ne;
							// Log::debug('ADD EDGE: '. $ne->urnStr() ." $v --> $vertex");
							// Log::info('DEPS (' . count($deps). ') : ' . implode(', ', $deps));
						}
					}
					return true;
				};
				$graph->traverseBF($v, 2, $fn, array('ea:item-of:','ea:inferred-item-of:' ));
			}
		}

		Log::info("ne: " . count($newEdges));

		// foreach ($newEdges as $e){
		// Log::info($e);
		// $deps = $e->getDependencies();
		// foreach ($deps as $d){
		// //Log::info(">> $d");
		// /* @var $de GEdge */
		// $de = $graph->getEdge($d->toString());
		// if ($de->isInferred()){
		// Log::info(">DERIVATIVE: $de");
		// $deps2 = $de->getDependencies();
		// Log::info("<DERIVATIVE COUNT DEPS: " . count($deps2));
		// foreach ($deps2 as $d2){
		// $de2 = $graph->getEdge($d2->toString());
		// if ($de2->isInferred()){
		// Log::info(">>>>>DERIVATIVE: $de2");
		// } else {
		// Log::info(">>>>>ORIGINAL : $de2");
		// }
		// }
		// }
		// }
		// }
	}

	/**
	 *
	 * @param GGraph $graph
	 */
	public function test1($graph) {
		$des1 = $graph->getInferredEdges();
		foreach ( $des1 as $e ) {
			// Log::info("d1: " . $e);
			echo ("d1: " . $e . "\n");
		}
		$this->testRuleItemOf($graph);
		$des2 = $graph->getInferredEdges();
		foreach ( $des2 as $e ) {
			if (isset($des1[$e->vkey()])) {
				echo ("d2 OLD: " . $e . "\n");
			} else {
				echo ("d2 NEW: " . $e . "\n");
				GGraphUtil::saveEdge($e);
			}
		}
		echo ("-------------------\n");
		GGraphUtil::dump1($graph);
		// GGraphUtil::dumpGraphviz($graph);
	}





	public function graphResetGUI() {
		auth_check_mentainer();

		$is_admin = ArcApp::user_access_admin();

		if (!$is_admin){
			$URL = UrlPrefixes::$cataloging;
			$response = Response::make('', 301);
			$response->header('Location', $URL);
			return $response;
		}

		$out = "";
		$out .= '<form method="post">';
		$out .= '<input type="submit" name="reset_graph" value="reset_graph" onClick="return confirm(\'Are you sure?\')"/>';
		$out .= '</form>';


		$reset_graph = false;
		if (isset($_POST['reset_graph']) && $_POST['reset_graph'] == 'reset_graph') {
			$reset_graph = true;
			$out .= "<h2>reset_graph</h2>";
		}

		if (!$reset_graph ) {
			echo $out;
		} else {
			echo("<pre>");
			$this->graphResetFn(true);
			echo("</pre>");
		}


	}




	public function graphReset() {
		$verbose = ! empty(get_get('verbose', null));
		$this->graphResetFn($verbose);
	}

	public function graphResetFn($verbose) {



		$full_resest = function () {
			Log::info("GRAPH RESET FULL");
			return GGraphUtil::graphResetFull();
		};

		$item_graph_save = function ($itemId) {
			Log::info("GRAPH RESET ITEM: " . $itemId);

			$idata = PDao::getItemMetadata($itemId);
			$is = new ItemSave();
			$is->setIdata($idata);
			$is->setItemId($itemId);
			$is->setSubmitId("1");
			$item_id = $is->save_item();
			$context = $is->getRuleContext();
			return $context;
		};

		$dumpContext = function ($context) use ($verbose) {
			$graph=  $context->graph();
			echo ("------------------------------------------\n");
			echo ("DEBUG MESSAGES:\n");
			echo ("------------------------------------------\n");
			$dms = $context->getDebugMessages();
			foreach ( $dms as $dm ) {
				echo ("  $dm\n");
			}
			echo ("\n");
			echo ("\n");

			echo ("------------------------------------------\n");
			echo ("INFO MESSAGES:\n");
			echo ("------------------------------------------\n");
			$ims = $context->getMessages();
			foreach ( $ims as $im ) {
				echo ("  $im\n");
			}
			echo ("\n");
			echo ("\n");

			echo ("------------------------------------------\n");
			echo ("INFERED EDGES:\n");
			echo ("------------------------------------------\n");

			$des2 = $graph->getInferredEdges();
			foreach ( $des2 as $e ) {
				if ($verbose) {
					echo ($e);
					echo ("\n");
				}
			}

			echo ("------------------------------------------\n");

			echo ("------------------------------------------\n");
			echo ("EDITED PROPERTIES:\n");
			echo ("------------------------------------------\n");

			echo ("------------------------------------------\n");
			$eps = $context->getEditPropUrns();
			foreach ( $eps as $urnStr ) {
				echo (" $urnStr :\n");
				$elements = $context->getEditProps($urnStr);
				foreach ( $elements as $el ) {
					echo (" >> $el \n");
				}
			}
			echo ("------------------------------------------");
		};

		$context = null;
		$itemId = get_get('item', null);

		if (empty($itemId)) {
			$context = $full_resest();
		} else {
			$context = $item_graph_save($itemId);
		}

		if ($verbose && ! empty($context)){
			$dumpContext($context);
		}

		return $context;
	}

	/**
	 *
	 * @param GGraph $graph
	 */
	public function test2($graph) {
		// GGraphUtil::dump1($graph);
		$v = $graph->getVertex(GURN::createOLDWithId(153));
		Log::info($v);

		Log::info('##########################');
		$es1 = $v->getEdges(GDirection::OUT);
		foreach ( $es1 as $e ) {
			Log::info($e);
		}
		Log::info('##########################');
		$es1 = $v->getEdges(GDirection::IN);
		foreach ( $es1 as $e ) {
			Log::info($e);
		}
		Log::info('##########################');
		$es1 = $v->getEdges(GDirection::BOTH);
		foreach ( $es1 as $e ) {
			Log::info($e);
		}
		Log::info('##########################');

		$v = $graph->getVertex(GURN::createOLDWithId(145));
		Log::info('##########################');
		$es1 = $v->getEdges(GDirection::BOTH, 'ea:work:');
		foreach ( $es1 as $e ) {
			Log::info($e);
		}
		Log::info('##########################');
	}
	public function test($data = null) {
		$graph = GGraphIO::loadGraph();
		// $this->testTraverse1($graph);
		// $this->testTraverse2($graph);
		// $this->testTraverse3($graph);
		// $this->dumpVerticeAttribures($graph);
		// $this->testRootsLeafs($graph);
		// GGraphUtil::dump1($graph);
		// $this->testRuleItemOf($graph);

		$this->test2($graph);
	}
}