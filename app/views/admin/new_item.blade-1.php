@section('content')


<?php
//use League\Url\UrlImmutable;

if (Config::get('arc.LOAD_JS')){
	# laravel jquery
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/jquery-ui.min.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/themes/smoothness/jquery-ui.min.css');

##@##drupal_add_js("/_assets/vendor/jquery.query-object.js",'external');
##@DocGroup(module="search", group="php", comment="tpl")
###############################################
## http://xxx/archive/search?
###############################################

$l = get_get('l');//title
$ss = get_get('t');//keyword from simple
$sss = get_get('tt');//keyword from advance
$c = PUtil::reset_int(get_get("c"),0);//silogi //katigoria
$y = PUtil::reset_int(get_get("y"),null);//year
$y1 = PUtil::reset_int(get_get("y1"),null);//year1
$y2 = PUtil::reset_int(get_get("y2"),null);//year2
$p = get_get('p');//place
$o = PUtil::reset_int(get_get("o"),null);//offset
$r = PUtil::reset_int(get_get("r"),5);//order
$a = get_get('a');//author
$m = get_get('m','s');//method (simple search,advance search)
$d = PUtil::reset_int(get_get("d"),1);//display method (list,thumb1,thumb2)
$sl = get_get('sl','0');
#$k = get_get('k','0'); //katigoria
$k = $c;

$lang = get_lang();



//$org_url = UrlImmutable::createFromServer($_SERVER);

$display_lang_select_flag = variable_get('arc_search_display_lang_select');


if ($y == 0){$y = null;}
if (!empty($y1) || !empty($y2)){
	$y = null;
}

$y_ok = urlencode($y);
$p_ok = urlencode($p);
$a_ok = urlencode($a);
$l_ok = urlencode($l);
$ss_ok = urlencode($ss);
$sss_ok = urlencode($sss);


drupal_add_library('system', 'ui.autocomplete');

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/search.js');




if (user_access_admin()){
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/list_edit.js');
}

	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/search_s.js');


$dbh = dbconnect();

### GET COLLECTION NAMES
$collection_names = array();
$SQL="SELECT id,label FROM dsd.collection2";
$stmt = $dbh->prepare($SQL);
$stmt->execute();
while ($row = $stmt->fetch()){
	$collection_names[$row[0]] = $row[1];
}
$collection_names[0] = 'Ολα';


#####################################################################################
### FETCH DATA
#####################################################################################
	$rep=null;

	$rep = SearchLib::search_item_simple($ss,$o,$r,$k,$y1,$y2,$sl);
	
	//echo("<pre>");	print_r($rep);	echo("</pre>");
	$result = $rep['results'];
	$r = $rep['order'];


?>


<style>
div.col1 {
	padding-right:30px;
	float:left;
}
div.col2 {
	float:left;
	padding-right:30px;
	border-left:1px solid gray;
}
div.col1t {
	padding-right:30px;
	float:left;
}
div.col2t {
	float:left;
	padding-right:30px;
}
div.column_header {
	margin:0px;
	padding-left:4px;
	font-size:1.4em;
	background-color:#E8E8E8;
	border-bottom: 1px solid gray;
	clear: both;
}
div.columns {
	display:table-cell;
	border:1px solid gray;
}
div.columnst {
	display:table-cell;
}

.create tr td, .create tr th {
    border: 2px solid #fff;
    BACKGROUND:#ccc;
    padding: 4px 53px;
    text-align: left;
}

</style>



<?php
#####################################################################################
### SEARCH FORM
#####################################################################################
#Αναζήτηση
#<legend>Περιήγηση στο  Ψηφιακό Αρχείο &nbsp; (<b><=$total_archive_cnt ></b> εγγραφές) </legend>
?>
<div class="row">
<div class="panel panel-default">
  <div class="panel-heading"><?=tr('Search term in Repository') ?> <?=tr(Config::get('arc.INSTALLATION_LEGEND')) ?></div>
  <div class="panel-body">

<form method="get" class="arch-sform  form-horizontal" role="form">
	<input type="hidden" name="lang" value="<?=$lang?>"/>

	<?php
		$terms_class ="sterm_long";
		if ($display_lang_select_flag){
			$terms_class ="sterm";
		}
	?>
		<!-- Simple specific -->
		<input type="hidden" name="m" value="s"/>

		<div class="form-group">
			<label for="terms" class="col-md-2 control-label"><?=tr('Term field')?>:</label>
		    <div class="col-md-8">
				<input id="terms" class="<?=$terms_class?> form-control" type="text" name="t" value="<?php echo($ss)?>" placeholder="<?=tr('Term field')?>" />
			</div>
		</div>


		<div class="form-group">
		    <div class="col-md-7 col-md-offset-2">
				<input type="submit" value="<?=tr('Search')?>" class="btn btn-default" />
				<input type="button" name="clear" value="<?=tr('Clear')?>" onclick="clearForm(this.form);" class="btn btn-default" >
			</div>
		</div>

		<?php 
// 		echo $lang;	echo '<br>';
// 		echo $m	; echo '<br>';
// 		echo $sl;	echo '<br>';
// 		echo $_POST ['t']; echo '<br>';
		?>

    </form>
  </div>
</div>
</div>

<?php


#####################################################################################
### COUNT APOTELESMATA KRITIRIA
#####################################################################################
$total_cnt = $rep['total_cnt'];

$kritiria = function() use ($total_cnt,$c,$m,$sss,$ss,$y,$p,$o,$l,$a,$d,$r,$y1,$y2,$sl,$lang) {

    echo('<div class="row res-infobar">');
	echo('<p class="rescnt">');
	
	if ($total_cnt == 0){
		printf("%s.",tr('No entries found'));
		//return;
	} elseif ($total_cnt == 1){
		printf('%s <strong>%s</strong> %s: ',tr('found'),$total_cnt, tr('entry'));
	} else {
		printf('%s <strong>%s</strong> %s: ',tr('Found'),$total_cnt, tr('entries'));
	}

	if (!empty($lang)){
		echo("&nbsp; ($lang)");
	}


	echo(" &nbsp; &nbsp; ");
	$criteria = "<span class=\"glyphicon glyphicon-filter\" aria-hidden=\"true\"></span> <strong> " . tr('Κριτήρια') .": </strong> ";
	$coma = '';

	//$text = $collection_names[$menu_collection];

	if (!empty($c)){
		$cn = get_menu_name($c);
		if (!empty($cn)){
			echo($criteria); $criteria = '';
			echo ($coma);
			echo(" " . tr($cn));
			$coma = ',';
		}
	}

	if (!empty($l)){
		echo($criteria); $criteria = '';
		echo ($coma);
		echo(" $l");
		//printf(' Τίτλος: %s ' ,$l);
		$coma = ',';
	}

	if (!empty($p)){
		echo($criteria); $criteria = '';
		echo ($coma);
		echo(" $p");
	//	printf(' Τόπος: %s ' ,$p);
		$coma = ',';
	}

	if (!empty($y)){
		echo($criteria); $criteria = '';
		echo ($coma);
		echo(" $y");
		//printf(' Έτος: %s ' ,$y);
		$coma = ',';
	}
	if (!empty($y1) || ! empty($y2)){
		echo($criteria); $criteria = '';
		if($y1 == -11){
			echo ($coma);
			echo(tr(' Άγνωστη Ημ/νία'));
		} else {
			if (empty($y1)){ $y1='...';};
			if (empty($y2)){ $y2='...';};
			echo ($coma);
			echo(" $y1-$y2");
		}
		$coma = ',';
	}


	if (!empty($a)){
		echo($criteria); $criteria = '';
		echo ($coma);
		echo(" $a ");
		//printf(' Συγγραφέας: %s ' ,$a);
		$coma = ',';
	}

	if (!empty($ss)){
		echo($criteria); $criteria = '';
		echo ($coma);
			echo(" $ss");
		//printf( ' Όρος: %s ' ,$ss);
		$coma = ',';
	}
	if (!empty($sss)){
		echo($criteria); $criteria = '';
		echo ($coma);
		echo(" $sss");
		//printf(' Όρος: %s ' ,$sss);
		$coma = ',';
	}

	echo('</p>');
    echo('</div>');
	
};


	$app = App::make('arc');
	$conf = null;
	if ($app->username){
		$conf = Config::get('arc_new_items.' . $app->username);
	} 
	if (!$conf){
		$conf = Config::get('arc_new_items._DEFAULT_',array());
	}

	
	foreach($conf as $i=>$l){
				echo("<table class='create'>");
				echo("<thead><tr><th colspan='12' style='text-align: center;'> $i</th></tr></thead>");
				foreach($l as $ii=>$ll){
// 								print_r($ll);
								echo("<tr>");
								foreach($ll as $obj_type=>$label){
									echo("<td><a href='/prepo/edit_step1?br=2&rd=$obj_type'>$label</a></td>");
								}
								echo("</tr>");
				}
				echo("</table>");
				echo("<br>");
	}
	
?>


<?php
#####################################################################################
###  PAGING BLOCK
#####################################################################################


//$pagingBlock = function() use($result,$m,$d,$rep,$sl) {
$pagingBlock = function() use($result,$m,$rep) {

	$u_term = urlencode($rep['term']);

	$rc = count($result);
	$total_cnt = $rep['total_cnt'];
	$limit = $rep['limit'];
	$offset = $rep['offset'];
	$pageNo = floor($offset/Config::get('arc.PAGING_LIMIT')) + 1;
	$total_pages = ceil($total_cnt / Config::get('arc.PAGING_LIMIT'));

// 	$u_col =  $rep['collection'];
// 	$u_y1 = $rep['year1'];
// 	$u_y2 = $rep['year2'];

	$org_url = Putil::getLocation();
	
	if ($total_pages == 1){
		return;
	}

	//echo('<span class="paging">');
     echo('<ul class="pager">');
     
	if ($m == 'a') {
		//ADVANCE SEARCH
		


		if ($rc > 0 &&  $rep['offset'] > 0){
			$u_offset = urlencode($rep['prev_offset']);
// 			printf('<li><a href="/archive/search?tt=%s&m=a&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&d=%s&y1=%s&y2=%s&sl=%s">&larr; %s</a></li>'
// 			,$u_term,$u_col,$u_year,$u_place,$u_offset,$u_title,$u_author,$d,$u_y1,$u_y2,$sl,tr('Προηγούμενη'));
			$new_url = Putil::replaceUrlGetParams(array('m'=>'a','o'=>$u_offset),$org_url);
			printf('<li><a href="%s">&larr; %s</a></li>',$new_url->getRelativeUrl(),tr('Προηγούμενη'));
			
		}
		else {
			//echo('<span stype="font-color:black;">[' .tr('Προηγούμενη') .']</span>');
            echo(' <li class="disabled"><a href="#">&larr; ' .tr('Προηγούμενη') .'</a></li>');
		}

		if ($rc  == $rep['limit']){
			$u_offset = urlencode($rep['next_offset']);
			//printf('<li class="currpage">%s/%s</li> <a href="/archive/search?tt=%s&m=a&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&d=%s&y1=%s&y2=%s&sl=%s">%s</a>',
			//$pageNo, $total_pages, $u_term,$u_col,$u_year,$u_place,$u_offset,$u_title,$u_author,$d,$u_y1,$u_y2,$sl,tr('Επόμενη'));
			$new_url = Putil::replaceUrlGetParams(array('m'=>'a','o'=>$u_offset),$org_url);
			printf('<li class="currpage">%s/%s</li><li><a href="%s">%s &rarr;</a></li>',$pageNo, $total_pages,$new_url->getRelativeUrl(),tr('Επόμενη'));
		}
		else {
			//printf(' &#160;&#160;&#160; (%s/%s) &#160;&#160;&#160;<span style="color:black;">[%s]</span>',$pageNo, $total_pages,tr('Επόμενη'));
            printf('<li class="currpage">%s/%s</li> <li class="disabled"><a href="#">%s &rarr;</a></li>',$pageNo, $total_pages,tr('Επόμενη'));

        }
	} else {
		//SIMPLE SEARCG
		if ($pageNo > 1){
			$u_offset = urlencode($rep['prev_offset']);
			//printf('<li><a href="/archive/search?m=s&t=%s&o=%s&d=%s&y1=%s&y2=%s&c=%s&sl=%s">&larr; %s</a></li> ',$u_term,$u_offset,$d,$u_y1,$u_y2,$u_col,$sl,tr('Προηγούμενη'));
			$new_url = Putil::replaceUrlGetParams(array('m'=>'s','o'=>$u_offset),$org_url);
			printf('<li><a href="%s">&larr; %s</a></li>',$new_url->getRelativeUrl(),tr('Προηγούμενη'));
		}
		else {
			//echo('<span style="color:black;">[' .tr('Προηγούμενη') .']</span>');
            echo(' <li class="disabled"><a href="#">&larr; ' .tr('Προηγούμενη') .'</a></li>');

        }

		#echo(" (total records $total_cnt) " );
		if ($pageNo<$total_pages){
			$u_offset = urlencode($rep['next_offset']);
// 			printf('<li class="currpage">%s/%s</li> <li><a href="/archive/search?m=s&t=%s&o=%s&d=%s&y1=%s&y2=%s&c=%s&sl=%s">%s &rarr;</a></li>',
// 			$pageNo, $total_pages,$u_term,$u_offset,$d,$u_y1,$u_y2,$u_col,$sl,tr('Επόμενη'));			
			$new_url = Putil::replaceUrlGetParams(array('m'=>'s','o'=>$u_offset),$org_url);
			printf('<li class="currpage">%s/%s</li><li><a href="%s">%s &rarr;</a></li>',$pageNo, $total_pages,$new_url->getRelativeUrl(),tr('Επόμενη'));
			
		}
		else {
			//printf(' &#160;&#160;&#160; (%s/%s) &#160;&#160;&#160;<span style="color:black;">[%s]</span>',$pageNo, $total_pages,tr('Επόμενη'));
            printf(' <li class="currpage">%s/%s</li> <li class="disabled"><a href="#">%s &rarr;</a></li>',$pageNo, $total_pages,tr('Επόμενη'));

		}
	}

	//echo("</span>");
    echo("</ul>");

};
#####################################################################################



?>

<?php if ($ss != null && !empty($ss)){

 //Moved Kritiria and subjects outside of results table
  $kritiria();

	echo('<div class="row">');
	echo('<table id="tresults" class="table table-striped table-bordered">');
	echo("\n");
	#echo('<thead><tr><th>Συλλογή</th><th></th><th>Τίτλος</th><th>Τόπος</th><th>\'Ετος</th><th></th></tr></thead>');
	echo('<thead>');

	$colspan="4";
	if (user_access_admin()){
		$colspan="5";
	}


	$print_years_flag =  variable_get('arc_menu_dates', 1);
	if ($print_years_flag > 0){

		echo('<tbody valign="top">');
		echo("\n");
	}


#####################################################################################
### TABLE BODY LIST
#####################################################################################

	$options = array(
			'edit' => false,
			'list_edit' => false,
			'small_img' => false,
			'edit_link' => true
	);
	
	PUtil::item_listo($result, $options);


#####################################################################################
### TABLE FOOTER LIST
#####################################################################################
	echo("</tbody>\n");
	echo('<tfoot><tr>');
	printf('<th colspan="%s" style="text-align: center;">',$colspan);
	if ($total_cnt > 0){
		$pagingBlock();
	}
	echo("</th></tr></tfoot>\n");

	echo("</table>\n");
	echo("</div>"); //End Row



}?>


@stop