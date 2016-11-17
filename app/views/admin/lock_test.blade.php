@section('content')
<h1>lock test</h1>
<?php

$pid = getmypid();

$st = get_get('t',12);

$rlock = new GRuleEngineLock();

function insert_msg($msg){
	//CREATE TABLE dsd.ruleengine_lock_log (id serial primary key, msg varchar, ts timestamp with time zone default now());
	$dbh = dbconnect();
	$SQL = "INSERT INTO dsd.ruleengine_lock_log (msg) VALUES (?)";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1,$msg);
	$stmt->execute();
}


Log::info($pid . ": #LOCK");
$rlock->lock();
//$rlock->lock();
Log::info($pid . ": #LOCK ACK");
Log::info($pid . ": #WORK START");
insert_msg($pid . ': WORK START: ' . $st);
sleep($st);
insert_msg($pid . ': WORK END');
Log::info($pid . ": #WORK END");
Log::info($pid . ": #LOCK RELEASE" );
$rlock->release();
//$rlock->release();
Log::info($pid . ": #LOCK RELEASE  ACK" );





?>
@stop