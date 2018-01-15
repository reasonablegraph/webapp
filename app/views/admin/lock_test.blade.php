@section('content')
<h1>lock test</h1>
<pre>
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


function info($msg){

	//$now = date(DATE_RFC2822);
	$now = date("Y-m-d H:i:s");
	list($usec, $sec) = explode(" ", microtime());
	$t1 = (int) ((float) ($usec * 1000));
	echo $now . ' ' . $t1 . ' | ' . $msg;
	echo "\n";
	//Log::info('|'. $t1 . ' | ' . $msg);
    PUtil::logGreen($msg);
}

info($pid . ": LOCK");
$rlock->lock();
//$rlock->lock();
info($pid . ": LOCK ACK");
info($pid . ": WORK START");
insert_msg($pid . ': LOCK TEST: WORK START: ' . $st);
sleep($st);
insert_msg($pid . ': LOCK TEST: WORK END');
info($pid . ": WORK END");
info($pid . ": LOCK RELEASE" );
$rlock->release();
//$rlock->release();
info($pid . ": LOCK RELEASE  ACK" );





?>
</pre>
@stop