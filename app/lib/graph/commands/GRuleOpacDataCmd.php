<?php


class GRuleOpacDataCmd implements  GCommand {

	private $saveInferenceFlag = false;
	public function __construct($saveInferenceFlag=false) {
		$this->saveInferenceFlag = $saveInferenceFlag;
	}

	public function execute($context){

		$vertices =  $context->graph()->getVertices();
		$con = prepareService();

// 		$SQL1 = "SELECT jdata FROM dsd.item2 WHERE item_id = ? AND jdata is not null";
// 		$st1 = $con->prepare($SQL1);

		$SQL2 = "UPDATE dsd.item2 SET jdata = ?, label=?, title = ? WHERE item_id = ?";
		$st2 = $con->prepare($SQL2);

		Log::info("UPDATE dsd.item2 (LABEL TITLE JDATA)");
		$context->addDebugMessage("UPDATE dsd.item2 (LABEL TITLE JDATA)");
		foreach($vertices as $vertex){
			/* @var $vertex GVertex */
			$itemId =  $vertex->persistenceId();
			$data = $vertex->getAttributes();
// 			$label = $vertex->getAttribute('label');
			$label = GRuleUtil::getLabel($vertex);

			$title = $vertex->getAttribute('title');
			if (empty($label)){
				Log::info(" !!! ERROR  EMPTY LABEL FOR ITEM: " . $itemId . ' title: ' . $title);
			}

			if ($this->saveInferenceFlag){
				//Log::info("SAVE INFERENCE");
				$edgesDataIN = array();
				$edgesDataOUT = array();
				$edges=  $vertex->getEdges(GDirection::BOTH);
				foreach ($edges as $e){
					if (!$e->isInferred()){
						continue;
					}

	// 				$vTo = $e->getVertexTo();
	// 				$id1 = $e->getVertexFrom()->persistenceId();
	// 				$id2 = $vTo->persistenceId();
	// 				$toLabel = GRuleUtil::getLabel($vTo);
	// 				$element = $e->element();
	// 				$edata = $e->getAttributes();
	// 				$edgesData[]  = array('from'=>$id1, 'to'=>$id2, 'element'=>$element, 'data'=>$edata,'ref_label'=>$toLabel);
					$id1 = $e->getVertexFrom()->persistenceId();
					$vertextTo = $e->getVertexTO();
					$id2 = $vertextTo->persistenceId();
					$v2Label = GRuleUtil::getLabel($vertextTo);
					$element = $e->element();
					//$edata = $e->getAttributes();
					if ($e->getTmpAttribute('DIRECTION') == GDirection::IN){
						$edgesDataIN[]   = array('from'=>$id1, 'to'=>$id2, 'element'=>$element, 'text_value'=>$v2Label);
					} else {
						$edgesDataOUT[]  = array('from'=>$id1, 'to'=>$id2, 'element'=>$element, 'text_value'=>$v2Label);
					}
				}
				$data['edges_in'] = $edgesDataIN;
				$data['edges_out'] = $edgesDataOUT;
			}


// 			$st1->bindParam(1, $itemId);
// 			$st1->execute();
// 			if ($rec = $st1->fetch(PDO::FETCH_ASSOC)){
// 				$db_data = json_decode($rec['jdata'],true);
// 			} else {
// 				$db_data = array();
// 			}
// 			$jdata  =  json_encode(array_merge($db_data,$data));


// 			if (isset($data['neighbourhood']['def'])){
// 				Log::info("neighbourhood " . $itemId . ' :: ' . implode(',',$data['neighbourhood']['def']));
// 			}

			$jdata =  json_encode($data);


			$st2->bindParam(1, $jdata);
			$st2->bindParam(2, $label);
			$st2->bindParam(3, $title);
			$st2->bindParam(4, $itemId);
			$st2->execute();
			//$count = $st2->rowCount();
		}



	}

}


// class GRuleOpacDataCmdOLD implements  GCommand {

// 	private $itemId;
// 	private $data;
// 	private $label;
// 	private $title;


// 	public function __construct($itemId,$data,$label,$title) {
// 		$this->itemId = $itemId;
// 		$this->data = $data;
// 		$this->label = $label;
// 		$this->title = $title;
// 	}

// 	public function execute($context){
// 		//Log::info("GCmdFolderCountSave execute " . $this->itemId);
// 		//Log::info("GCmdFolderCountSave execute " . $this->itemId . " :: "  . print_r($this->data,true ));
// 		$con = dbconnect();


// 		$SQL = "SELECT jdata FROM dsd.item2 WHERE item_id = ? AND jdata is not null";
// 		$st = $con->prepare($SQL);
// 		$st->bindParam(1, $this->itemId);
// 		$st->execute();
// 		if ($rec = $st->fetch(PDO::FETCH_ASSOC)){
// 			$db_data = json_decode($rec['jdata'],true);
// 		} else {
// 			$db_data = array();
// 		}

// 		$jdata  =  json_encode(array_merge($db_data,$this->data));

// 		$SQL = "UPDATE dsd.item2 SET jdata = ?, label=?, title = ? WHERE item_id = ?";
// 		$st = $con->prepare($SQL);
// 		$st->bindParam(1, $jdata);
// 		$st->bindParam(2, $this->label);
// 		$st->bindParam(3, $this->title);
// 		$st->bindParam(4, $this->itemId);
// 		$st->execute();


// // 		$SQL = "SELECT item_id, element,text_value,ref_item,data FROM  dsd.metadatavalue2
// // 		WHERE ref_item = ?";
// // 		$st = $con->prepare($SQL);
// // 		$st->bindParam(1, $this->itemId);
// // 		$st->execute();
// // 		$records = $st->fetchAll(PDO::FETCH_ASSOC);
// // 		Log::info(print_r($records,true));

// 		$SQL = "UPDATE dsd.metadatavalue2
// 		SET data = arc_json_set_path(coalesce(data,'{}'::json), '{\"data\", \"ref_label\"}'::TEXT[],?)
// 		WHERE ref_item = ?";
// // 		Log::info($SQL);
// // 		Log::info($this->label);
// // 		Log::info($this->itemId);
// // 		Log::info('-----------------------------------------');
// 		$st = $con->prepare($SQL);
// 		$st->bindParam(1, $this->label);
// 		$st->bindParam(2, $this->itemId);
// 		$st->execute();
// 		//$count = $stmt->rowCount();

// 	}

// }








?>