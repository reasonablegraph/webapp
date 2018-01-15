<?php
class GRuleWorkManif  extends AbstractBaseRule implements  GRule {

	public function execute(){
		//$graph = $context->graph();

		$con = dbconnect();



		$work_id = null;
		$expression_id = null;
		$SQL = "SELECT * from dsd.metadatavalue2 where element = 'ea:expressionOf:tmp' AND ref_item is not null AND obj_type='auth-work'";
		$st = $con->prepare($SQL);
		//$st->bindParam(1,);
		$st->execute();
		while ($rec = $st->fetch(PDO::FETCH_ASSOC)){
			//$metadata_value_id = $rec['metadata_value_id'];
			$id1 = $rec['item_id'];
			$id2 = $rec['ref_item'];
			//PUtil::get_item_basic_data($dbh, $item_id)
			$label = PDao::getItemLabel($id1);
			GGraphUtil::deleteEdgeRaw($id1,'ea:expressionOf:tmp',$id2);
			GGraphUtil::saveEdgeRaw($id2,'ea:expressionOf:',$id1,false,null,$label);
			$work_id = $id1;
			$expression_id = $id2;
		}

		//GGraphUtil::saveEdgeRaw($id1,$el,$id2,$isInfered,$deps);


		$SQL = "SELECT item_id,ref_item from dsd.metadatavalue2 where element = 'ea:manifestation:tmp' AND ref_item is not null ";//AND obj_type='auth-work'
		$st = $con->prepare($SQL);
		//$st->bindParam(1,);
		$st->execute();
		while ($rec = $st->fetch(PDO::FETCH_ASSOC)){
			//$metadata_value_id = $rec['metadata_value_id'];
			$id1 = $rec['item_id'];
			$id2 = $rec['ref_item'];
			GGraphUtil::deleteEdgeRaw($id1,'ea:manifestation:tmp',$id2);
			$my_work = empty($expression_id) ? $id1 : $expression_id;
			$label = PDao::getItemLabel($my_work);
			GGraphUtil::saveEdgeRaw($id2,'ea:work:',$my_work,false,null,$label);
		}


	}

}
