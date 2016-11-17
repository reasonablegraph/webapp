<?php
class GRuleLoadItemGraphDefault extends AbstractBaseRule implements  GRule {



	public function execute(){
		$context = $this->context;

		/* @var  $graph GGraph  */
		$graph = $context->graph();


		$item_id = $context->get("LOAD_ITEM_ID",null);
		if (empty($item_id) ){
			throw new Exception("GRuleLoadItemGraphDefault LOAD_ITEM_IDS param expected");
		}
		//$context->addDebugMessage("GRuleLoadItemGraphDefault: " . $item_id);

		$item_refs = $context->get("LOAD_ITEM_REFS",array());
		//Log::info("ITEM_REFS1: " . print_r($item_refs,true));
		//$context->addDebugMessage("ITEM_REFS1: " . print_r($item_refs,true));

		/* @var  $old_graph GGraph  */
		$old_graph = $context->getGraph('old');
// 		$vertices = $old_graph->getVertices();
// 		foreach ($vertices as $v){
// 			//if ($v->getPropertiesCount() > 0){
// 				//$context->addDebugMessage("V item count: " . $v->getPropertiesCount());
// 				$id = $v->persistenceId();
// 				if (!in_array($id,$item_refs)){
// 					$item_refs[] = $id;
// 				}
// 			//}
// 		}
		$oldVertex = $old_graph->getVertex(GURN::createOLDWithId($item_id));
		if (!empty($oldVertex)){
			$oldEdges = $oldVertex->getEdges(GDirection::BOTH);
			foreach ($oldEdges as $e){
				$direction = $e->getTmpAttribute('DIRECTION');
				if ($direction == GDirection::OUT){
					$tmpv = $e->getVertexTo();
				} else {
					$tmpv = $e->getVertexFrom();
				}
				$id = $tmpv->persistenceId();
				if (!empty($id) && !in_array($id,$item_refs)){
					$item_refs[] = $id;
				}
			}
		}


		//$context->addDebugMessage("ITEM_REFS2: " . print_r($item_refs,true));
		//Log::info("ITEM_REFS2: " . print_r($item_refs,true));

		$dbh = dbconnect ();
		$SQL = "SELECT item_id,obj_type,obj_class,site,status,collection from dsd.item2 WHERE item_id = ?";
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $item_id );
		$stmt->execute ();
		if (! $row = $stmt->fetch ()) {
			return;
		}
		//Log::info(print_r($row,true));
		//GGraphIO::loadNodeSubGraph($item_id,$item_refs,$graph);
		GGraphIO::loadNodeNeighbourhood($item_id,$item_refs,$graph,'edit-item');

		$oldVertices = $old_graph->getVertices();
		foreach ($oldVertices as $ov){
			if ($ov->isReadOnly()){
				$v = $graph->getVertex($ov->urnStr());
				if (!empty($v)){$v->setReadOnly();}
			}
		}


//		//GGraphUtil::dump1($graph);

	}



}