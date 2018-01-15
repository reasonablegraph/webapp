<?php



$arr = new ArrayObject();


echo "#1:\n";
$arr[] = "1";
$arr['a'] = "1";
print_r($arr);
echo "\n#==========================";

echo "#2:\n";
echo isset($arr['a']);
echo "\n#==========================";



