<?php

function echon($out){
	echo "\n";
	echo $out;
	echo "\n";
}

$old_ts=microtime(true);
$old = 0;
//$sleep dekata tou defteroleptou
function mem($msg=null, $sleep=1) {
	global $old, $old_ts;
	gc_collect_cycles();
	usleep($sleep*100000);// 1000000: 1 sec
	$m1 = memory_get_usage();
	$m2 = memory_get_usage(true);
	$m3 = memory_get_peak_usage();
	$m4 = memory_get_peak_usage(true);
	$diff = $m1 - $old;
	$old = $m1;
	$ts = microtime(true);
	$ts_diff = $ts - $old_ts;
	$old_ts = $ts;
	echo "usage:  $m1 , $m2  peak: $m3 , $m4 usage1 diff: $diff time: $ts_diff  $msg\n";

}


$c = 0;
function appendToArray($a){
	global $c;
	$c+=1;
	$a[]= 'X' . $c;
};

$y=0;
function appendToObjArray($a){
	global $y;
	$y+=1;
	$a->append('X' . $y);
}


	mem();
	$a1 = array('αβγδεzηθικλμνξοπρστυφχψω','ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩ');
	mem('array def');
	$o1 = new ArrayObject($a1);
	mem('arrayObjectdef def');
	$o2 = new ArrayObject($a1, ArrayObject::STD_PROP_LIST);
	mem('arrayObjectdef STD_PROP_LIST def');

	$o3 = new ArrayObject($a1);
	for ($i=0;$i<10000000;$i++){
		$o3->append($a1);
	}
	$o3->append('koko');
	$o3->append($a1);
	mem();
	echon("1---------------------------------------------------");
	print_r($a1);
	echon("2---------------------------------------------------");
	print_r($o1);
	echon("3---------------------------------------------------");
	print_r($o2);
	echon("4---------------------------------------------------");
	print_r($o1[1]);
	echon("5---------------------------------------------------");
	print_r($o2[1]);
	echon("6---------------------------------------------------");
	$o1[] = 10;
	print_r($o1);
	echon("7---------------------------------------------------");


	echon(in_array('α',$a1));

	function in_arrayobj($needle,$heystack){
		$found = false;
		foreach ($heystack as $i){
			if ($i == $needle){
				$found = true;
				break;
			}
		}return $found;
	}


	function in_arrayobj2($needle,$heystack){
		mem('in_arra2y#1');
		$found = false;
		foreach ($heystack as $i){
			if ($i == $needle){
				$found = true;
				break;
			}
		}
		mem('in_array2#2');
		return $found;
	}

	mem('in_array start');
	echon('found: ' . in_array('koko',(array)$o3));
	mem('in_array me cast  (1)');
	echon('found: ' . in_array('koko',(array)$o3));
	mem('in_array me cast  (2)');
	echon('found: ' . in_arrayobj('koko', $o3));
	mem('in_array me foreach (1)');
	echon('found: ' . in_arrayobj('koko', $o3));
	mem('in_array me foreach (2)');
	echon('found: ' . in_arrayobj2('koko', $o3));
	mem('in_array mem test end');
	echon("7b---------------------------------------------------");


	mem();
	$t1 = $a1; $t1[1]=$t1[1];$t1[0]=$t1[0];
	mem('cp a1 1',2);
	$t2 = $a1; $t2[1]=$t2[1];
	mem('cp a1 2',2);
	$t3 = $a1; $t3[0]=$t3[1];
	mem('cp a1 3',2);
	$t4 = $a1; $t4[1]=$t4[1];$t4[0]=$t4[0];
	mem('cp a1 4',2);
	$t5 = $a1; $t5[1]=$t5[1];
	mem('cp a1 5',2);

	echon("8---------------------------------------------------");
	mem();
	$t10 = $o1; $t10[1]=$t10[1];
	mem('cp o1');
	$t11 = $o1; $t11[1]=$t11[1];
	mem('cp o1');
	$t12 = $o1; $t12[1]=$t12[1];
	mem('cp o1');
	$t13 = $o1; $t13[1]=$t13[1];
	mem('cp o1');
	$t14 = $o1; $t14[1]=$t14[1];
	mem('cp o1');
	$t15 = $o1; $t15[1]=$t15[1];
	mem('cp o1');
	echon("9---------------------------------------------------");
	$t1 = $a1;
	$t2 = $a1;
	mem();
	$t1[]='x';
	mem();
	print_r($t1);
	print_r($t2);
	echon("10---------------------------------------------------");
	$t1 = $o1;
	$t2 = $o1;
	mem();
	$t1[]='x';
	mem();
	print_r($t1);
	print_r($t2);
	echon("11---------------------------------------------------");
	print_r($a1);
	appendToArray($a1);
	print_r($a1);
	echon("12---------------------------------------------------");
	print_r($o1);
	appendToArray($o1);
	print_r($o1);
	echon("13---------------------------------------------------");
	print_r($o1);
	appendToObjArray($o1);
	print_r($o1);
	echon("14---------------------------------------------------");





	echo "\n";
?>
