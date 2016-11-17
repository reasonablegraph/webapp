@section('content')
<?php auth_check_mentainer(); ?>
<?php
// echo("<pre>");
// print_r($_GET);
// echo("----------------------------");
// print_r($_POST);
// echo("</pre>");
//foreach ($_POST as $k=>$v) { Log::info("$k : $v"); };

$userName = get_user_name();
if (empty($userName)){
	echo("auth error");
	return;
}



$dbh = dbconnect();
$submit_id = null;
$item_id = null;


$submit_id = get_post_get('submit_id');

if (empty($submit_id)){
	echo("error (submit_id)");
	return;
}


###########################################################################
## LIB CLOSURES
###########################################################################
###########################################################################

$load_metadata = function($submit_id) use ($dbh, &$idata, &$item_id, &$edoc, &$wfdata,  &$vivliografiki_anafora, &$agg_type, &$item_collection, &$cd, &$thumb1, &$libgenxml){
	$SQL = "SELECT item_id, data, edoc, wf_data,type from dsd.submits where id = ? AND status =1 ";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $submit_id);
	$stmt->execute();
	$stmt->bindColumn(1, $item_id);
	$stmt->bindColumn(2, $data);
	$stmt->bindColumn(3, $edoc);
	$stmt->bindColumn(4, $wfdata_text);
	$stmt->bindColumn(5, $submits_type );
	//$stmt->bindColumn(5, $data_norm);
	Log::info("ST:: " + $submits_type);
	if ($stmt->fetch()){
		if ($submits_type == 2){
				Log::info("#2# LOAD FROM SUBMITS");
				$idata = new ItemMetadata();
				$idata->replaceValuesFromClientModels(json_decode( $data ));
		} else {
				$idata = new ItemMetadata ( unserialize ( $data ) );
		}
				//print_r(unserialize($data));
		$wfdata = array();
		if(!empty($wfdata_text)){
			if ($submits_type == 2){
				$wfdata = json_decode($data);
			} else {
				$wfdata = unserialize ( $wfdata_text );
			}
		}
		if(isset($wfdata['vivliografiki_anafora'])){
			$vivliografiki_anafora = $wfdata['vivliografiki_anafora'];
		}
		if(isset($wfdata['agg_type'])){
			$agg_type = $wfdata['agg_type'];
		}
		if(isset($wfdata['cd'])){
			$cd = $wfdata['cd'];
		}
		if(isset($wfdata['item_collection'])){
			$item_collection = $wfdata['item_collection'];
		}
		if(isset($wfdata['thumb1'])){
			$thumb1 =  $wfdata['thumb1'];
		}
		if(isset($wfdata['libgenxml'])){
			$libgenxml =  $wfdata['libgenxml'];
		}

	}

};

###########################################################################
###########################################################################
###########################################################################

#$idata = new ItemMetadata();
$wfdata = array();
$vivliografiki_anafora = false;
$agg_type = false;
$cd= null;
$item_collection = null;
$thumb1 = null;
$libgenxml = null;

$idata= null;
$item_id = null;
$edoc = null;
$load_metadata($submit_id);
if (empty($idata)){
	Log::info("error (34)");
	return;
}

$is = new ItemSave();
$is->setIdata($idata);
$is->setEdoc($edoc);
$is->setSubmitId($submit_id);
$is->setWfdata($wfdata);
$is->setUserName($userName);
$is->setItemId($item_id);




if (false){
	echo("<pre>");

	if (true){
		echo("##########################\n");
		echo "## ITEM DATA ##\n";
		echo("##########################\n");
		print_r($idata->values);
	}

	if (true){
		echo("\n##########################\n");
		echo "## WFDATA ##\n";
		echo("##########################\n");
		print_r($wfdata);
	}


	if (false){
		echo("\n##########################\n");
		echo("## BASIC KEYS ##\n");
		echo("##########################\n");
		$it = new ItemMetadataIterator($idata);
		$c =0;
		foreach ($it as $key => $values) {
			echo( $key . " : " . count($values) .  "\n");
			$c++;
		}
		echo "\n TOTAL KEYS: $c\n";
	}

	if (true){
		echo("\n##########################\n");
		echo("## ALL KEYS ##\n");
		echo("##########################\n");
		$it = new ItemMetadataIterator($idata,1);
		$c =0;
		foreach ($it as $key => $values) {
			$c++;
			echo( $key . " : " . count($values) .  "\n");
		}
		echo "\n TOTAL KEYS: $c\n";
	}

	echo("</pre>");
}
##############################

#if ( empty($edoc) && empty($item_id)){
#	echo("error (3)");
#	return;
#}

$idata->validate();
$msg_counters = PSnipets::print_mesages($idata);
$err_counter =$msg_counters[0];


echo("<pre>");

if ($err_counter > 0){
	echo('<form method="GET" action="/prepo/edit_step1">');
	printf('<input type="hidden"  name="s" value="%s" />' ."\n",$submit_id);
	echo('<input type="submit"  name="PREV" value="STEP 1"/>');
	echo('</form>');

	return;
}

$item_id = $is->save_item();
Log::info("SAVE ITEM " . $item_id . " FINISH");

if ( ! empty($libgenxml)){

	try {
		$SQL = "DELETE FROM dsd.libgen WHERE item_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->execute();

		$SQL = "insert into dsd.libgen (item_id,xml) values (?,?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $item_id);
		$stmt->bindParam(2, $libgenxml);
		$stmt->execute();
	} catch (PDOException $e){
		$error = $e->getMessage();
		echo("ERROR: $error\n");
		error_log( $error, 0);
	}

// 	$item = PDao::getItem($item_id);
// 	$bitstreams = $item['bitstreams'];
// 	foreach($bitstreams as $b){
// 		if ($b['mimetype'] == 'application/pdf' || $b['mimetype'] == 'image/vnd.djvu'){
// 			$bitem = $b['item'];
// 			break;
// 		}
// 	}
}


$item = PDao::getItemDBRecord($item_id);
$obj_class = $item['obj_class'];
$title = $item['label'];

echo("\n");
echo("\n");
echo("$title\n");
echo("</pre>");

echo("<ul>");
if (! empty($item_collection)){
	printf('<li><a href="/archive/item?i=%s">collection</a></li>',$item_collection);
} else if ($obj_class == 'artifact'){
	$artifact = PDAO::getArtifactDBRecordByItemImpl($item_id);
	$item_id = $artifact['item_id'];
	$sn = $artifact['sn'];
	printf('<li><a href="/prepo/artifacts?i=%s">item artifacts</a></li>',$item_id);
	printf('<li><a href="/prepo/artifacts_list?s=%s">all artifacts</a></li>',urlencode($sn));
	} else {
	printf('<li><a href="/archive/item?i=%s">public item</a></li>',$item_id);
	printf('<li><a href="/prepo/edit_step2?i=%s">admin view</a></li>',$item_id);
	printf('<br/>');
	printf('<li><a href="/prepo/cataloging">cataloging</a></li>');
}
echo("<ul>");

?>
@stop