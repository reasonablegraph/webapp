<?php


$ts0=microtime();
$ts1=microtime(true);
sleep(1);
$ts2=microtime(true);
sleep(1);
$ts3=microtime(true);

$diff1=$ts2-$ts1;
$diff2=$ts3-$ts2;

echo "$ts0 \n";
echo "$ts1 \n";
echo "$ts2 \n";
echo "$ts3 \n";
echo "$diff1 \n";
echo "$diff2 \n";




echo "\n";
?>
