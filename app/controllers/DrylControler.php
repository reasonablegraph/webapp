<?php
class DrylControler extends BaseController {


		function parchive_find_lawyer() {

			$term = get_get("term");
			if (empty($term)) {
				return AdminController::ws_empty_json_response();
			}
			$flags = array('IS:lawyer');
			$limit = 60;
			$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit, false, 'label2');
			$response = Response::make($rep, 200);
			$response->header('Cache-Control', 'no-cache, must-revalidate');
			$response->header('Content-Type', 'application/json');
			return $response;

		}


		function parchive_find_conference() {

			$term = get_get("term");
// 			if (empty($term)) {
// 				return AdminController::ws_empty_json_response();
// 			}
			$flags = array('IS:conference');
			$limit = 60;
			$rep  =  PDao::find_node_for_subject($term,$flags ,true,$limit, true, 'label2');
			$response = Response::make($rep, 200);
			$response->header('Cache-Control', 'no-cache, must-revalidate');
			$response->header('Content-Type', 'application/json');
			return $response;

		}



}



