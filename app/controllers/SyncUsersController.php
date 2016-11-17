<?php

class SyncUsersController extends BaseController {

	public function syncusers() {
		$user = get_post("user");
		$cms = get_post("cms");
		if (empty($user) || empty($cms)) {
			return;
		}
		
		$dbh = dbconnect();
		$stmt1 = $dbh->prepare("SELECT 1 FROM dsd.arc_user WHERE cms = ? AND uid = ?");
		$stmt1->bindParam(1, $cms, PDO::PARAM_STR);
		$stmt1->bindParam(2, $user['uid'], PDO::PARAM_INT);
		$stmt1->execute();
		
		if (($row = $stmt1->fetch())) {
			$SQL = "UPDATE dsd.arc_user SET email = ?, full_name = ?, org_id = ?, org_name = ? WHERE cms = ? AND uid = ? ";
			$stmt2 = $dbh->prepare($SQL);
			$stmt2->bindParam(1, $user['mail'], PDO::PARAM_STR);
			$stmt2->bindParam(2, $user['full_name'], PDO::PARAM_STR);
			$stmt2->bindParam(3, $user['organization_id'], PDO::PARAM_INT);
			$stmt2->bindParam(4, $user['organization_name'], PDO::PARAM_STR);
			$stmt2->bindParam(5, $cms, PDO::PARAM_STR);
			$stmt2->bindParam(6, $user['uid'], PDO::PARAM_INT);
			$stmt2->execute();
		} else {
			$SQL = "INSERT INTO dsd.arc_user (cms,username,uid,email,full_name,org_id,org_name) values (?, ?, ?, ?, ?, ?, ?)";
			$stmt3 = $dbh -> prepare($SQL);
			$stmt3->bindParam(1, $cms, PDO::PARAM_STR);
			$stmt3->bindParam(2, $user['name'], PDO::PARAM_STR);
			$stmt3->bindParam(3, $user['uid'], PDO::PARAM_INT);
			$stmt3->bindParam(4, $user['mail'], PDO::PARAM_STR);
			$stmt3->bindParam(5, $user['full_name'], PDO::PARAM_STR);
			$stmt3->bindParam(6, $user['organization_id'], PDO::PARAM_INT);
			$stmt3->bindParam(7, $user['organization_name'], PDO::PARAM_STR);
			$stmt3->execute();
		}

		$stmt4 = $dbh->prepare("DELETE FROM dsd.arc_user_roles WHERE username = ?");
		$stmt4->bindParam(1, $user['name'], PDO::PARAM_STR);
		$stmt4->execute();

		$stmt5 = $dbh -> prepare("DELETE FROM dsd.arc_user_perms WHERE username = ?");
		$stmt5->bindParam(1, $user['name'], PDO::PARAM_STR);
		$stmt5->execute();

		$stmt6 = $dbh->prepare("INSERT INTO dsd.arc_user_roles (role_id, role_name, username) values (?, ?, ?)");
		foreach($user['roles'] as $role_id => $role_name) {
			$stmt6->bindParam(1, $role_id, PDO::PARAM_INT);
			$stmt6->bindParam(2, $role_name, PDO::PARAM_STR);
			$stmt6->bindParam(3, $user['name'], PDO::PARAM_STR);
			$stmt6->execute();
		}

		$stmt7 = $dbh->prepare("INSERT INTO dsd.arc_user_perms (perm, username) values (?, ?)");
		foreach($user['permissions'] as $perm) {
			$stmt7->bindParam(1, $perm, PDO::PARAM_STR);
			$stmt7->bindParam(2, $user['name'], PDO::PARAM_STR);
			$stmt7->execute();
		}
		
		// plain ECHO
		$rep = $user;
		
		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}

}
