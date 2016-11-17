<?php




class GruleSubjectsAllCmd implements  GCommand {


	public function __construct() {

	}

	public function execute($context){
		//$context->addDebugMessage("GruleSubjectsAllCmd EXEC");

		$map = $context->get('SUBJECTS_COUNT');

		$con = dbconnect();
		$SQL = sprintf('UPDATE dsd.item2 SET %s  = ? WHERE item_id = ?', PgPropIntConstants::COLUMN_SUBJECT_COUNT);
		$st = $con->prepare($SQL);

		foreach ($map as $k=>$v){
		//	$context->addDebugMessage("GruleSubjectsAllCmd #1: " . $k . " = " . $v);
			$st->bindParam(1, $v);
			$st->bindParam(2, $k);
			$st->execute();
		}
	}

}




class GRuleSubjectsAll  extends AbstractGruleProcessVertice implements GRule {

	private $countArray = array();

	protected function init(){
		//$this->context->addDebugMessage("GRuleSubjects INIT");
	//	$this->context->put('SUBJECTS_COUNT',array());
		$this->context->putCommand("SUBJECTS_COUNT", new GruleSubjectsAllCmd());

		$this->skip_readonly = true;


	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){



		$id = $v->persistenceId();

		$c1 = count($v->getEdges(GDirection::IN,'ea:subj:'));
		if ($c1 > 0){
			//$this->context->addDebugMessage("SUBJECT-COUNT  : " . $v . ' :: ' .  $c1 );
			//$this->context->get('SUBJECTS_COUNT')[$id]=$c1;
			$this->countArray[$id]=$c1;
		//	$this->context->addDebugMessage("GRuleSubjectsAll proc: " . $v . " COUN:T " . $c1);
		}





		// 		$c2 = count($v->getEdges(GDirection::IN,'ea:subj:general'));
		//
		// 		echo("DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD\n");
		// 		echo("SUBJECT-COUNT1  $v : $id  : $c1 \n");
		// 		echo("SUBJECT-COUNT2  $v : $id  : $c2 \n");
		// 		echo("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n");
		//
		// 		$cmd = new GCmdFolderCountSave($id, $c);
		// 		$context->putCommand('FOLDER_COUNT_' . $id, $cmd);




	}


	public function postExecute() {
		$this->context->put('SUBJECTS_COUNT', $this->countArray);
	}

}