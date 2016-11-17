@section('content')
<?php auth_check_mentainer(); ?>
<?php

$dbh = dbconnect();

$SQL= "SELECT item_id,obj_type,issue_label, label
FROM dsd.item2 where obj_type in ('periodiko-tefxos','efimerida-tefxos')
AND status='finish'
AND item_id not in (select item_1 from dsd.item_relation r join dsd.item_relation_type t on (t.id = r.rel_type) where t.label='issue_of')
ORDER BY label,issue_label;";

	$stmt = $dbh->prepare($SQL);
//	$stmt->bindParam(1, $e1);
	$stmt->execute();
	$data = $stmt->fetchAll();



	echo('<table class="table table-striped table-bordered table-hover">');
	if (!empty($data)){
		foreach ($data as $r){
			echo("<tr>");
			printf("<td>%s</td>",$r['obj_type']);
			printf("<td>%s</td>",$r['item_id']);
			printf('<td><a  href="/prepo/edit_step1?i=%s">%s</a></td>',$r['item_id'],$r['label']);
			echo("</tr>");
		}
	}
	echo('</table>');

?>
@stop