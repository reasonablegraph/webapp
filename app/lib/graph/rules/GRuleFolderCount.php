<?php


class GCmdFolderCountSave implements  GCommand {

	private $itemId;
	private $count;
	private $context;


	public function __construct($itemId,$count) {
		$this->itemId = $itemId;
		$this->count = $count;
	}

	public function execute($context){
		Log::info("GCmdFolderCountSave execute " . $this->itemId . " : " . $this->count);
		$con = dbconnect();
		$SQL = "UPDATE dsd.item2 SET issue_cnt = ? WHERE item_id = ?";
		$st = $con->prepare($SQL);
		$st->bindParam(1, $this->count);
		$st->bindParam(2, $this->itemId);
		$st->execute();

	}

}

class  GRuleFolderCount extends AbstractBaseRule implements  GRule {

	private $my_types = array();
	/**
	 * @param GRuleContextR $context
	*/
	public function __construct($context) {
		parent::__construct($context);

		$con = dbconnect();
		$SQL = "SELECT  obj_type from dsd.obj_type_class_def where class  = 'collection'";
		$st = $con->prepare($SQL);
		$st->execute();
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			$this->my_types[] = $row['obj_type'];
		}

	}

	public function execute(){
		$context = $this->context;

		/*@var $g GGRaph */
		$g = $context->graph();
		$vertices =$g->filterVertices(function($v){
			/*@var $v GVertex */
			$ot = $v->getObjectType();
			foreach ($this->my_types as $type){
				if ($ot == $type){
					return true;
				}
			}
			return false;
		});

		//Log::info(print_r($vertices,true));

			foreach ($vertices as $v){
				$c = count($v->getEdges(GDirection::IN,'ea:item-of:'));
				$id = $v->persistenceId();
				Log::info("FOLDER-COUNT  $v : $id  : $c");
				$cmd = new GCmdFolderCountSave($id, $c);
				$context->putCommand('FOLDER_COUNT_' . $id, $cmd);
			}

	}

}

