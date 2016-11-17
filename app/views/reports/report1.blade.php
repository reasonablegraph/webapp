<!DOCTYPE html>
<html>
<head>
<title>MANIFESTATIONS</title>
<meta charset="utf-8">
</head>
<body>

<style>

h1 {
	  text-align: center;
}
a:link {
    color: black;
    text-decoration: none;
}

a:visited {
    color: black;
    text-decoration: none;
}
a:hover {
    color: green;
    text-decoration: underline;
}
a:active {
    color: black;
    text-decoration: none;
}


table {
    border-collapse: collapse;
    font-size: 90%;
}

table, th, td {
		padding: 6px;
    border: 1px solid black;
    vertical-align:top;
}


table.print-table { page-break-inside:auto }
table.print-table tr { page-break-inside:avoid; page-break-after:auto }

}


</style>


<h1>MANIFESTATIONS</h1>

<table class="print-table">
<tr>
<th>item</th>
<th>title</th>
<th>format</th>
<th>w</th>
<th>user</th>
<th>create</th>
</tr>
<?php
$c=0;
foreach ($items as $o=>$item):
$c+=1;
	$basic =  $item['basic'];
	$item_id = $basic['item_id'];
	$authors = $item['authors'];
	$opac = new OpacHelper($basic['jdata']);
	//$create_ts =(new DateTime($basic['dt_create']))->format('d/m/Y\&\n\b\s\p\;H:i');
	$create_ts =(new DateTime($basic['dt_create']))->format('d/m/Y');
	$wc = $item['work_count'];
	$ditems = $opac->opac1('items');

?>

<tr>
<td align="right">
<a href="/prepo/edit_step2?i=<?=$item_id?>">
<?=$item_id?>
</a>
</td>
<td align="left">
<a href="/prepo/edit_step2?i=<?=$item_id?>">
<?php
echo(htmlentities($opac->opac2('Title_punc')));
$delim = '';
$authors_str = '';
$ac = 0;
foreach ($authors as $author){
	$ac +=1;
	$opacw = new OpacHelper($author['jdata']);
	$authors_str .= ($delim . $opacw->value('label'));
	$delim = ' | ';
}

// if ($wc == 0){
// 	echo " / (NO WORKS FOUND)";
// }else

if ($ac == 0){
	echo " / (NO AUTHORS FOUND)";
} else if ($authors_str != '') {
	echo ' / ';
	echo $authors_str;
} else {
	echo ' / ?';
}

// echo '<pre>';
// print_r($ditems);
// echo '</pre>';

?>
</a>
</td>
<td>
<?php
$item_map = array();
foreach ($ditems as $ditem){

	$strarr = preg_split("/\s+/",$ditem['label']);
	$type =  isset($strarr[0]) ? $strarr[0] : 'empty';
	$item_map[$type] = 1;
}

$delim = '';
foreach ($item_map as $type=>$tmp){

	echo $delim;
	echo $type;
	$delim = ' | ';
}

?>
</td>
<td align="right">
<?php
if ($wc <> 1){
	echo $wc;
}
?>
</td>
<td align="left">
<?=$basic['user_create']?>
</td>
<td align="left">
<?=$create_ts?>
</td>

</tr>


<?php endforeach; ?>
</table>

<p>total: <?=$c?></p>
<hr/>
<p>
w: works count
</p>





</body>
</html>

