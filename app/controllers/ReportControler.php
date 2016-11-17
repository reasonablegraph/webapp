<?php
class ReportControler extends BaseController {


	public function report1() {
		Log::info('report1');

		ArcApp::auth_check();

		//$dbh = prepareService();
		$dbh = dbconnect();



		$SQL="SELECT item_id FROM dsd.item2 WHERE status='finish' AND obj_type='auth-manifestation' ORDER BY dt_create desc";
		$stmt1 = $dbh->prepare($SQL);

		$SQL = "SELECT m.ref_item FROM dsd.metadatavalue2 m JOIN dsd.item2 i ON (m.ref_item = i.item_id) WHERE m.ref_item IS NOT NULL AND m.element = 'ea:work:authorWork' and m.item_id = ? AND i.status = 'finish'";
		$stmt2 = $dbh->prepare($SQL);

		$SQL = "SELECT count(*) FROM dsd.metadatavalue2 m JOIN dsd.item2 i ON (m.ref_item = i.item_id) WHERE m.ref_item IS NOT NULL AND m.element = 'ea:work:' and m.item_id = ? AND i.status = 'finish' AND NOT m.inferred";
		$stmt3 = $dbh->prepare($SQL);

// 		$SQL = "SELECT obj_type FROM dsd.metadatavalue2 m JOIN dsd.item2 i ON (m.ref_item = i.item_id) WHERE m.ref_item IS NOT NULL AND m.element = 'ea:artifact-of:' and m.item_id = ? AND i.status = 'finish' AND NOT m.inferred";
// 		$stmt3 = $dbh->prepare($SQL);


		$stmt1->execute();
		$items=array();
		while ($item_id = $stmt1->fetch()[0]){
			$basic = PDao::getItemBasic($item_id);


			$authors = array();
			$stmt2->bindParam(1, $item_id);
			$stmt2->execute();
			while ($author_id = $stmt2->fetch()[0]){
				$authors[] = PDao::getItemBasic($author_id);
			}

			$stmt3->bindParam(1, $item_id);
			$stmt3->execute();
			$wc = $stmt3->fetch()[0];

			$items[] = array('basic'=>$basic , 'authors'=>$authors, 'work_count' => $wc);


		}
		return Response::view('reports/report1',array('items'=>$items))->header('X-DRUPAL-DECORATE', 'NONE')->header('Content-Type', 'text/html');
	}


}



