@section('content')
<?php auth_check_mentainer(); ?>
<?php
drupal_set_title("artifacts list");
$s = get_get("s");
$c = get_get("c");
$si = get_get("si");
?>




<form method="get">
cn: <input type="text" name="c" />
sn: <input type="text" name="s" />
sn(int): <input type="text" name="si" />

<input type="submit"/> &nbsp;
 <input type="reset"/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="/prepo/artifacts_stats"> stats </a>
</form>




<?php
$show_all_flag = true;
if (! PUtil::isEmpty($c) ){
	echo("call numbers like: " . $c);
	$show_all_flag = false;
} else if (! PUtil::isEmpty($s) ) {
	echo("serial numbers like: " . $s);
	$show_all_flag = false;
} else if (! PUtil::isEmpty($si) ) {
	echo("serial number: " . $si);
	$show_all_flag = false;
}






//$ARTIFACT_STATUS_MAP = Lookup::get_artifact_status_values();
$dbh = dbconnect();

echo('<table class="table table-striped table-bordered">');
echo('<tr>');
echo('<th>id</th>');
echo('<th>title</th>');
echo('<th>call number</th>');
echo('<th>serial</th>');
echo('<th>date</th>');
echo('<th>status</th>');
echo('<th>action</th>');
echo('</tr>');


$print_line = function($v) {//use ($ARTIFACT_STATUS_MAP){
	$id = $v['id'];
	$sn = $v['sn'];
	$cn = $v['call_number'];
	$label = $v['label'];
	$status = $v['status'];
	$item_id = $v['item_id'];
	$create_dt = $v['create_date'];
	$ref_item = $v['item_impl'];
	echo('<tr>');
	printf('<td style="text-align:right;">%s</td>',$id);
	printf('<td><a href="/archive/item/%s">%s</a></td>',$item_id, $label);
	printf('<td>%s</td>',$cn);
	printf('<td>%s</td>',$sn);
	printf('<td>%s</td>',$create_dt);
	//printf('<td>%s</td>',$ARTIFACT_STATUS_MAP[$status]);
	printf('<td>%s</td>',$status);
	echo('<td>');
	//printf('<a href="/prepo/edit_step2?i=%s">[view]</a>',$ref_item);
	printf('<a href="/prepo/edit_step1?i=%s"><span class="glyphicon glyphicon-edit"></span></a>',$ref_item);
	echo('</td>');
	echo("</tr>\n");


};

$SQL_PART = "i.item_id, i.label, a.sn, a.call_number, a.status, a.id, to_char(a.create_dt,'YYYY-MM-DD HH24:MI') as create_date, item_impl
FROM dsd.artifacts a
JOIN dsd.item2 i ON i.item_id =a.item_id ";
if ($show_all_flag ){

//	$SQL = sprintf('select %s WHERE a.call_number is not null order by a.create_dt desc limit 100',$SQL_PART);
	$SQL = sprintf('select %s  order by a.create_dt desc limit 100',$SQL_PART);

	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	while($r = $stmt->fetch()){
		$print_line($r);
	};
	// foreach($r as $k => $v){
		// $print_line($v);
	// }

} else {

	if (! PUtil::isEmpty($c) ){
		$c = str_replace("%","\%",$c);
		if (PUtil::strBeginsWith($c, "^")){
			$ss = substr($c,1) . '%';
		} else {
			$ss = '%' . $c . '%';
		}
		$SQL = sprintf("select %s WHERE  call_number like ?  order by a.call_number limit 100",$SQL_PART);
	}else if (! PUtil::isEmpty($si) ){
		$tmp = PUtil::extract_int($si);
		if ($tmp != null && $tmp >=0){
			$ss = $tmp;
		};
		$SQL = sprintf("select %s WHERE sn_suff = ? order by a.call_number limit 100",$SQL_PART);
  }else {
		$s = str_replace("%","\%",$s);
		if (PUtil::strBeginsWith($s, "^")){
			$ss = substr($s,1) . '%';
		} else {
			$ss = '%' . $s . '%';
		}
		$SQL = sprintf("select %s WHERE sn like ? order by a.call_number limit 100",$SQL_PART);
	}

	if ($ss != null){
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $ss);
		$stmt->execute();
		$r = $stmt->fetchAll();
		foreach($r as $k => $v){
			$print_line($v);
		}
	}

}

echo("<table>");

?>
@stop