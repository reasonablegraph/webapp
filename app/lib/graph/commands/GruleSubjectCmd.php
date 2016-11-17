<?php



//@DEPRICATED
class GruleSubjectCmd implements  GCommand {

	private $urnStr;
	private $mId;
	private $s1Id;
	private $s2Id;

	public function __construct($urnStr,$mId,$s1Id,$s2Id) {
		$this->mId = $mId;
		$this->s1Id = $s1Id;
		$this->s2Id = $s2Id;
		$this->urnStr = $urnStr;
	}

	public function execute($context){
		//Log::info("GruleSubjectCmd execute " . $this->mId  . ' : ' . $this->s2Id);
		$context->addDebugMessage("GruleSubjectCmd execute " . $this->mId  . ' : ' . $this->s2Id);

		$context->removeVertext($this->urnStr);

		$dbh = dbconnect();
		$SQL="UPDATE dsd.metadatavalue2  SET ref_item = ?  WHERE item_id = ? AND ref_item = ?  AND element = 'ea:subj:' AND NOT inferred";
		Log::info("GruleSubjectCmd:  UPDATE dsd.metadatavalue2  SET ref_item = ?  WHERE item_id = ? AND ref_item = ?  AND element = 'ea:subj:' AND NOT inferred'  >> " . $this->s2Id  . ' : ' . $this->mId . ' : ' . $this->s1Id);

		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $this->s2Id);
		$stmt->bindParam(2, $this->mId);
		$stmt->bindParam(3, $this->s1Id);
		$stmt->execute();
		$count = $stmt->rowCount();
		if ($count > 1){
			Log::info("ERROR UPDATE COUNT > 1: " + count);
		}


		PDao::delete_item($this->s1Id);


	}

}








?>