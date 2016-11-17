@section('content')

<script>
jQuery(document).ready(function($) {

    var $container = $('#thl_3');
    var $container2 = $('#thl_4');

    // initialize Masonry after all images have loaded
    $container.imagesLoaded( function() {
      $container.masonry();
    });
    $container2.imagesLoaded( function() {
      $container2.masonry();
    });

});
</script>

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
$ot = get_get('ot',null);//object-type
$k = $c;

$lang = get_lang();
#echo("LANG:$lang");


$term_search_flag = false;
if (!empty($ss)){
	$term_search_flag = true;
}

//$org_url = UrlImmutable::createFromServer($_SERVER);

$display_lang_select_flag = variable_get('arc_search_display_lang_select');

#if (!is_numeric($y)){$y = null;}
#if (!is_numeric($y1)){$y1 = null;}
#if (!is_numeric($y2)){$y2 = null;}
#if (!is_numeric($o)){$o = null;}
#if (!is_numeric($r)){$r = 5;}
#if (!is_numeric($d)){$d = 1;}
#if (!is_numeric($c)){$c = 0;}

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

#error_log("OFFSET: $o");

//drupal_add_library('system', 'ui.autocomplete');
#drupal_add_css('http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css','external');
#drupal_add_css(ARCHIVE_ASSETS_PATH . 'css/search.css');

#drupal_add_js(ARCHIVE_ASSETS_PATH .'js/jquery.query-2.1.7.js');
drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/search.js');




if (user_access_admin()){
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/list_edit.js');
}
if ($m == "a") {
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/search_m.js');
} else {
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/search_s.js');
}

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

### GET OBJECT_TYPE_LABELS
$obj_type_names = get_object_type_names();

if ($c < 0){
	$c = 0;
}

#$menu_lines = get_menu_lines();
#echo("<pre>");
#print_r($menu_lines);
#echo("</pre>");


if (!empty($c)){
	$mn = get_menu_name($c);
	if (! empty($mn)){
		drupal_set_title(get_menu_name($c));
	} else {
		drupal_set_title('browse');
	}
}


#####################################################################################
### FETCH DATA
#####################################################################################
	$rep=null;
	if ($m == 'a'){
		$rep = SearchLib2::search_item($sss,$c,$o,$y,$p,$l,$a,$r,$y1,$y2);
		#$rep =  search_item_simple($dbh,$ss,$o,$r,$c,$y1,$y2);
	} else {
		$rep = SearchLib2::search_item_simple($ss,$o,$r,$k,$y1,$y2,$sl,$ot);
	}
	$result = $rep['results'];
	$r = $rep['order'];


?>



<?php
#####################################################################################
### SEARCH FORM
#####################################################################################
#Αναζήτηση
#<legend>Περιήγηση στο  Ψηφιακό Αρχείο &nbsp; (<b><=$total_archive_cnt ></b> εγγραφές) </legend>
?>

<div class="row"  id="searchfconteiner"  <?php echo $term_search_flag ?  'aria-hidden="true"' : 'aria-hidden="false"'; ?>>


<div class="panel panel-default">
  <div class="panel-heading"><?=tr('Search in Repository') ?> <?=tr(Config::get('arc.INSTALLATION_LEGEND')) ?></div>
  <div class="panel-body">

<form method="get" class="arch-sform  form-horizontal" role="form">
	<input type="hidden" name="lang" value="<?=$lang?>"/>

	<?php
	if ($m != 'a'){
		$terms_class ="sterm_long";
		if ($display_lang_select_flag){
			$terms_class ="sterm";
		}

	?>
		<!-- Simple specific -->
		<input type="hidden" name="m" value="s"/>



		<div class="form-group">
		    <div class="col-md-10">
		    <label for="terms" class="col-md-2 control-label"><?=tr('Όρος')?>:</label>
				<input id="terms" class="col-md-10  <?=$terms_class?> form-search" type="text" name="t" value="<?php echo($ss)?>" placeholder="<?=tr('Όρος')?>" />
			</div>
		    <div class="col-md-2">

					<?php if ($display_lang_select_flag): ?>
						<?php  if ($m != 'a'): ?>
							<?php  /*
							<label for="c"><?=tr('Είδος')?>: </label>
							<select name="c" class="title" id="select_idos">
							  <option value="0" <?php if ($c == 0){echo 'selected="selected"'; } ?> ><?=tr('Oλα')?></option>
							  <option value="3" <?php if ($c == 3){echo 'selected="selected"'; } ?> ><?=tr('Περιοδικά')?></option>
							  <option value="4" <?php if ($c == 4){echo 'selected="selected"'; } ?> ><?=tr('Εφημερίδες')?></option>
							  <option value="5" <?php if ($c == 5){echo 'selected="selected"'; } ?> ><?=tr('Μπροσούρες')?></option>
							  <option value="8" <?php if ($c == 8){echo 'selected="selected"'; } ?> ><?=tr('Βιβλία')?></option>
							  <option value="9" <?php if ($c == 9){echo 'selected="selected"'; } ?> ><?=tr('web-sites')?></option>
							  <option value="13" <?php if ($c == 13){echo 'selected="selected"'; } ?> ><?=tr('Συλλογές')?></option>
							</select>
							*/
							?>
							<label class="element-invisible" for="select_lang"><?=tr('Γλωσσα')?>: </label>
							<select name="sl" class="title form-control" id="select_lang">
							  <option value="0" <?php  if ($sl == '0'){echo 'selected="selected"'; } ?> ><?=tr('Επιλογή Γλώσσας')?></option>
							  <option value="el" <?php if ($sl == 'el'){echo 'selected="selected"'; } ?> ><?=tr('Ελληνικά')?></option>
							  <option value="en" <?php if ($sl == 'en'){echo 'selected="selected"'; } ?> ><?=tr('English')?></option>
							</select>

						<?php endif; ?>
						<?php endif; ?>
			</div>
		</div>









	<?php } else { ?>
	<!-- Advance specific -->
		<input type="hidden" name="m" value="a"/>

		<div class="form-group">
		    <div class="col-md-10">
  				<label for="title" class="col-md-2 control-label"><?=tr('Τίτλος')?>: </label>
  				<input id="title" class="col-md-10 text form-search" type="text" name="l" value="<?php echo($l)?>"/>
			</div>
			 <div class="col-md-2"> </div>
		</div>

<?php
/*
		<div class="clear">&nbsp;</div>

		<div class="ui-widget selem">
			<label for="c"><?=tr('Είδος')?>: </label>
			<select name="c" class="title">
			  <option value="0" <?php if ($c == 0){echo 'selected="selected"'; } ?> ><?=tr('Ολα')?></option>
			  <option value="3" <?php if ($c == 3){echo 'selected="selected"'; } ?> ><?=tr('Περιοδικά')?></option>
			  <option value="4" <?php if ($c == 4){echo 'selected="selected"'; } ?> ><?=tr('Εφημερίδες')?></option>
			  <option value="5" <?php if ($c == 5){echo 'selected="selected"'; } ?> ><?=tr('Μπροσούρες')?></option>
			  <option value="8" <?php if ($c == 8){echo 'selected="selected"'; } ?> ><?=tr('Βιβλία')?></option>
			  <option value="9" <?php if ($c == 9){echo 'selected="selected"'; } ?> ><?=tr('web-sites')?></option>
			  <option value="13" <?php if ($c == 13){echo 'selected="selected"'; } ?> ><?=tr('Συλλογές')?></option>
			</select>
		</div>
*/
?>

		<div class="form-group">
		    <div class="col-md-10">

		     <label for="terms" class="col-md-2 control-label"><?=tr('Όρος')?>: </label>
				 <input id="terms" class="col-md-4 text form-search" type="text" name="tt" value="<?php echo($sss)?>" />
		    <label for="y" class="col-md-2 control-label"><?=tr('Ετος')?>: </label>
				<input type="text" class="col-md-4 text form-search" name="y" value="<?php echo($y)?>"/>
			</div>
			 <div class="col-md-2"> </div>
		</div>



		<div class="form-group">
		   <div class="col-md-10">
		    <label for="author" class="col-md-2 control-label"><?=tr('Συγγραφέας')?>: </label>
				<input id="author" class="col-md-4 text form-search" type="text" name="a" value="<?php echo($a)?>" />
			   <label for="place" class="col-md-2 control-label"><?=tr('Τόπος')?>: </label>
				<input id="place" class="col-md-4 text form-search" type="text" name="p" value="<?php echo($p)?>" />
			</div>
			<div class="col-md-2"> </div>
		</div>


	<?php  } ?>


		<div class="form-group">
		   <div class="col-md-10 "> <!-- <div class="col-md-7 col-md-offset-2">-->
		   <div class="col-md-10  col-md-offset-2 search-buttons">


				<button value="search" class="btn btn-default" ><?=tr('Search')?></button>
				<button name="clear" value="clear" onclick="clearForm(this.form);" class="btn btn-default" ><?=tr('Clear')?></button>


			</div>
			</div>
		    <div class="col-md-2">
				<?php if ($m == 's'){ ?>
					<?php if (variable_get('arc_search_display_advance_link')): ?>
						<a style="padding-left:10px;" href="/archive/search?m=a&lang=<?=$lang?>"><?=tr('Advance search')?></a>
					<?php endif ?>
				<?php  } else { ?>
					<a href="/archive/search?lang=><?=$lang?>"><?=tr('Simple search')?></a>
				<?php  } ?>
		    </div>
		</div>


    </form>
  </div>
</div>



</div>

<?php


#####################################################################################
### COUNT APOTELESMATA KRITIRIA
#####################################################################################
$total_cnt = $rep['total_cnt'];
$counters = $rep['counters'];

$kritiria = function() use ($total_cnt,$c,$m,$sss,$ss,$y,$p,$o,$l,$a,$d,$r,$y1,$y2,$sl,$lang,$counters,$display_lang_select_flag,$ot) {


  //  echo('<div class="row res-infobar">');
	//echo('<p class="rescnt">');

	if ($total_cnt == 0){
		printf("%s.",tr('No entries found'));
		//return;
	} elseif ($total_cnt == 1){
		printf('%s <strong>%s</strong> %s: ',trChoise('Found',$total_cnt),$total_cnt, tr('entry'));
	} else {
		printf('%s <strong>%s</strong> %s: ',trChoise('Found',$total_cnt),$total_cnt, tr('entries'));
		if (! empty($counters)){
			$sep = '';
			foreach ($counters as $count_obj_type => $count){
				echo ($sep); $sep = ', ';
				printf(' %s <a href="%s">%s</a>',$count, Putil::replaceRelativeUrlGetParams(array('ot'=>$count_obj_type)),  trChoise($count_obj_type . 's',$count));
				///$count_obj_type
			}


		}
	}


	if ($display_lang_select_flag){
		if (!empty($lang)){
			echo("&nbsp; ($lang)");
		}
	}

	#echo(" &nbsp; &nbsp; ");
	#if ($r == 1){
	#	echo(" <b>Ταξινόμηση:</b> ");
	#	echo("Ημερομ. Αρχειοθ.");
	#} elseif ($r == 2){
	#	echo(" <b>Ταξινόμηση:</b> ");
	#		echo("Τίτλος");
	#} elseif ($r == 3){
	#	echo(" <b>Ταξινόμηση:</b> ");
	#		echo("Έτος desc");
	#} elseif ($r == 4){
	#	echo(" <b>Ταξινόμηση:</b> ");
	#		echo("Έτος asc");
	#}



	echo(" &nbsp; &nbsp; ");
	$criteria = "<span class=\"glyphicon glyphicon-filter\" aria-hidden=\"true\"></span> <strong> " . tr('Κριτήρια') .": </strong> ";
	$coma = '';

	if (!empty($ss)){
		echo($criteria); $criteria = '';
		echo ($coma);
		//echo(" $ss");


		printf('<a href="%s">%s<span class="glyphicon glyphicon-remove" aria-hidden="true" style="position:relative;top:3px; left: 1px;"></span> </a>',PUtil::replaceRelativeUrlGetParams(array('t'=>null)),tr($ss));
// 		printf('<a href="%s">%s<img src="/_assets/img/remove-icon.png"/></a>',PUtil::replaceRelativeUrlGetParams(array('t'=>null)),tr($ss));
		//printf( ' Όρος: %s ' ,$ss);
		$coma = ' ';
	}
	if (!empty($sss)){
		echo($criteria); $criteria = '';
		echo ($coma);
		echo(" $sss");
		//printf(' Όρος: %s ' ,$sss);
		$coma = ',';
	}

	if (!empty($c)){
		$cn = get_menu_name($c);
		if (!empty($cn)){
			echo($criteria); $criteria = '';
			echo ($coma);
			echo(" " . tr($cn));
			$coma = ',';
		}
	}

	if (!empty($ot)){
		echo($criteria); $criteria = '';
		echo ($coma);
		printf('<a href="%s">%s<span class="glyphicon glyphicon-remove" aria-hidden="true" style="position:relative;top:3px; left: 1px;"></a>',PUtil::replaceRelativeUrlGetParams(array('ot'=>null)),tr($ot));
		$coma = ' ';
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


	//echo('</p>');
  //  echo('</div>');

};

//$kritiria();

#####################################################################################
### SUBJECTS
#####################################################################################
$subjects_print = function($subjects) use ($lang){
	#$subjects = $rep['subjects'];
	if (!empty($subjects)){
		echo('<div class="row res-subjects">');
		echo('<span class="res-subjects-label"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> ' . tr('Σχετικές Ετικέτες'). ':  </span> ');
			$coma = "";
			foreach ($subjects as $row){
				$my = $row;
				echo($coma);
				$ok = urlencode($my);
				printf('<a href="/archive/term?t=%s&lang=%s"> %s</a>',$ok,$lang,$my);
				$coma =", ";
			}

		echo('</div>');
	}

};
#$subjects = $rep['subjects'];
#$subjects_print($subjects);


	#####################################################################################
	### COLLECTIONS BLOCK
	#####################################################################################
	//$collections_block_line = function($print_line, $k) use ($c, $collection_names,$m,$sss_ok,$ss_ok,$y_ok,$p_ok,$o,$l_ok,$a_ok,$d,$r,$y1,$y2,$sl) {
	$collections_block_line = function($print_line, $k) {
		$k = $k == 0? 1 : $k;
		$menu_lines = get_menu_lines();
		if (! isset($menu_lines[$k])){
			$keys = array_keys($menu_lines);
			$k = $keys[0];
		}
		$master_line = $menu_lines[$k]['line1'];

// 	$print_link = function($print_line, $key, $menu_item) use ($master_line, $menu_lines, $k,$c,$collection_names,$m,$sss_ok,$ss_ok,$y_ok,$p_ok,$o,$l_ok,$a_ok,$d,$r,$y1,$y2,$sl){
		$print_link = function($print_line, $key, $menu_item) use ($master_line, $menu_lines, $k){
			$menu_item_line = $menu_item['line'];
			$menu_item_line1 = $menu_item['line1'];
			$menu_item_line2 = $menu_item['line2'];

			if ($menu_item_line == $print_line){
				$text = tr($menu_item['name']);
				if ($print_line == 1 || $menu_item_line1 == $master_line){
					if ($k == $key || $master_line == $key) {
						$class = 'class="btn btn-primary btn-xs"';
					} else {
						$class = 'class="btn btn-default btn-xs"';
					}
// 					$ahref  = sprintf('<a %s href="/archive/search?m=%s&tt=%s&t=%s&y=%s&p=%s&l=%s&a=%s&d=%s&r=%s&y1=%s&y2=%s&sl=%s&c=%s">%s</a>',
// 					$class,$m,$sss_ok,$ss_ok,$y_ok,$p_ok,$l_ok,$a_ok,$d,$r,$y1,$y2,$sl,$key,$text);

// 					$org_url = UrlImmutable::createFromServer($_SERVER);
// 					$query = $org_url->getQuery();
// 					$query->modify(array('c'=>$key));
// 					$new_url=$org_url->setQuery($query);

					$new_url = Putil::replaceRelativeUrlGetParams(array('c'=>$key));
// 					$org_url = Putil::getLocation();
// 					$new_url = Putil::replaceUrlGetParams(array('c'=>$key),$org_url);
					$ahref  = sprintf('<a %s href="%s">%s</a>',$class,$new_url,$text);
					return $ahref;
				}
			}
			return null;
		};

		$rep = array();
		foreach ($menu_lines as $key => $menu_item){
			$tmp = $print_link($print_line, $key, $menu_item);
			if (!empty($tmp)){
				$rep[] = $tmp;
			}
		}
		return $rep;
	};


#####################################################################################
### YEAR RANGE BLOCK
#####################################################################################
	//$years_block = function($y1,$y2) use ($collection_names,$m,$sss_ok,$ss_ok,$y_ok,$p_ok,$o,$l_ok,$a_ok,$d,$r,$c,$sl) {
	$years_block = function($y1,$y2) use ($collection_names) {
		if (empty($y1)){
			if ($y2 == 1984){
				$y1 = -1;
			} else {
				$y1 = 0;
			}
		}
		//$print_link = function($menu_y1) use ($c,$collection_names,$m,$sss_ok,$ss_ok,$y_ok,$p_ok,$o,$l_ok,$a_ok,$d,$r,$y1,$sl){
		$print_link = function($menu_y1) use ($collection_names,$y1){
			$year_names = array('0'=>tr('Ολα'),
			'1980' => "....-1984",'1985' => "1985-1989",'1990' => "1990-1994",'1995' => "1995-1999",'2000' => "2000-2004",'2005' => "2005-...." , "-11"=>tr("Άγνωστο"));
			$text = $year_names[$menu_y1];
			if ($y1 != $menu_y1) {
				if ($menu_y1 == 2005){
						$menu_y2 = null;
				} elseif ($menu_y1 == -1){
					$menu_y1 = null;
					$menu_y2 = 1984;
				} elseif ($menu_y1 == -11){
					$menu_y1 = -11;
					$menu_y2 = null;
				} elseif ($menu_y1 == 0){
					$menu_y1 = null;
					$menu_y2 = null;
				} else {
					$menu_y2 = $menu_y1 + 4;
				}
				$class = 'class="btn btn-default btn-xs"';
			} else {
				$class = 'class="btn btn-primary btn-xs"';
				$menu_y1 = null;
				$menu_y2 = null;
			}
			// if ($menu_y1 == 1980){
				// $menu_y1 = null;
			// }


			//printf('<a %s href="/archive/search?m=%s&tt=%s&t=%s&c=%s&y=%s&p=%s&l=%s&a=%s&d=%s&r=%s&y1=%s&y2=%s&sl=%s">%s</a>',
			//$class,$m,$sss_ok,$ss_ok,$c,$y_ok,$p_ok,$l_ok,$a_ok,$d,$r,$menu_y1,$menu_y2,$sl,$text);

			$new_url = Putil::replaceRelativeUrlGetParams(array('y1'=>$menu_y1,'y2'=>$menu_y2));
		  printf('<a %s href="%s">%s</a>',$class,$new_url,$text);

			//echo('&#160;&#160;&#160;&#160;&#160;');
		};

		echo('<div id="browsec2">');
		$print_link(0);
		$print_link(1980);
		$print_link(1985);
		$print_link(1990);
		$print_link(1995);
		$print_link(2000);
		$print_link(2005);
		$print_link(-11);

		echo("</div>");
	};

?>
<?php
#####################################################################################
###  ORDER BLOCK
#####################################################################################
$order_block = function($r) {
?>
<div id="order2d">
<label for="order2s"><?=tr('Ταξινόμηση')?>:</label>
	  	<select id="order2s" name="r2" class="text" onChange="change_sort();">
		  <option value="5" <?php if ($r == 5){echo 'selected="selected"'; } ?> ><?=tr('Συνάφεια')?></option>
		  <option value="1" <?php if ($r == 1){echo 'selected="selected"'; } ?> ><?=tr('Ημ. Αρχειοθ.')?></option>
		  <option value="2" <?php if ($r == 2){echo 'selected="selected"'; } ?> ><?=tr('Τίτλος')?></option>
		  <option value="3" <?php if ($r == 3){echo 'selected="selected"'; } ?> ><?=tr('Έτος desc')?></option>
		  <option value="4" <?php if ($r == 4){echo 'selected="selected"'; } ?> ><?=tr('Έτος asc')?></option>
		</select>
</div>
<?php
};

#####################################################################################
###  BUTTONS BLOCK
#####################################################################################
//$size_buttons = function() use ($m,$ss,$sss,$c,$y,$p,$p,$l,$a,$o,$ss_ok,$sss_ok,$y_ok,$p_ok,$l_ok,$a_ok,$r,$y1,$y2,$sl){
$size_buttons = function(){
	$print_flag =  variable_get('arc_display_sizebtns', 1);
	if (!$print_flag){
		return;
	}
	echo('<div class="viewbtns" >');
		//echo(tr('Αλλαγή εμφάνισης') . ': &nbsp;');
// 		printf('<a href="/archive/search?m=%s&t=%s&tt=%s&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&r=%s&y1=%s&y2=%s&sl=%s&d=4"><img class="sizebtn" src="/_assets/img/vthumbs2.png"/></a>',$m,$ss_ok,$sss_ok,$c,$y_ok,$p_ok,$o,$l_ok,$a_ok,$r,$y1,$y2,$sl);
// 		printf('<a href="/archive/search?m=%s&t=%s&tt=%s&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&r=%s&y1=%s&y2=%s&sl=%s&d=3"><img class="sizebtn" src="/_assets/img/vthumbs.png"/></a>',$m,$ss_ok,$sss_ok,$c,$y_ok,$p_ok,$o,$l_ok,$a_ok,$r,$y1,$y2,$sl);
// 		printf('<a href="/archive/search?m=%s&t=%s&tt=%s&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&r=%s&y1=%s&y2=%s&sl=%s&d=1"><img class="sizebtn" src="/_assets/img/vlist.png"/></a>',$m,$ss_ok,$sss_ok,$c,$y_ok,$p,$o,$l_ok,$a_ok,$r,$y1,$y2,$sl);

		$org_url = Putil::getLocation();

		$new_url = Putil::replaceRelativeUrlGetParams(array('d'=>'1'),$org_url);
        printf('<a href="%s"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span></a>',$new_url);

        $new_url = Putil::replaceRelativeUrlGetParams(array('d'=>'3'),$org_url);
        printf('<a href="%s"><span class="glyphicon glyphicon-th" aria-hidden="true"></span></a>',$new_url);

		$new_url = Putil::replaceRelativeUrlGetParams(array('d'=>'4'),$org_url);
		printf('<a href="%s"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span></a>',$new_url);

		echo('</div>');
};



#####################################################################################
###  PAGING BLOCK
#####################################################################################


//$pagingBlock = function() use($result,$m,$d,$rep,$sl) {
$pagingBlock = function() use($result,$m,$rep) {

	//$query_string = $_SERVER['QUERY_STRING'];
	//$query_map = array();
	//parse_str($query_string,$query_map);
	// echo("<pre>");
		// print_r($query_map);
	// echo("</pre>");


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

// 		$u_year = urlencode($rep['year']);
// 		$u_place = urlencode($rep['place']);
// 		$u_title = urlencode($rep['title']);
// 		$u_author = urlencode($rep['author']);


		if ($rc > 0 &&  $rep['offset'] > 0){
			$u_offset = urlencode($rep['prev_offset']);
// 			printf('<li><a href="/archive/search?tt=%s&m=a&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&d=%s&y1=%s&y2=%s&sl=%s">&larr; %s</a></li>'
// 			,$u_term,$u_col,$u_year,$u_place,$u_offset,$u_title,$u_author,$d,$u_y1,$u_y2,$sl,tr('Προηγούμενη'));
			$new_url = Putil::replaceRelativeUrlGetParams(array('m'=>'a','o'=>$u_offset),$org_url);
			printf('<li><a href="%s">&larr; %s</a></li>',$new_url,tr('Previous page'));

		}
		else {
			//echo('<span stype="font-color:black;">[' .tr('Προηγούμενη') .']</span>');
            echo(' <li class="disabled"><a href="#">&larr; ' .tr('Previous page') .'</a></li>');
		}

		if ($rc  == $rep['limit']){
			$u_offset = urlencode($rep['next_offset']);
			//printf('<li class="currpage">%s/%s</li> <a href="/archive/search?tt=%s&m=a&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&d=%s&y1=%s&y2=%s&sl=%s">%s</a>',
			//$pageNo, $total_pages, $u_term,$u_col,$u_year,$u_place,$u_offset,$u_title,$u_author,$d,$u_y1,$u_y2,$sl,tr('Επόμενη'));
			$new_url = Putil::replaceRelativeUrlGetParams(array('m'=>'a','o'=>$u_offset),$org_url);
			printf('<li class="currpage">%s/%s</li><li><a href="%s">%s &rarr;</a></li>',$pageNo, $total_pages,$new_url,tr('Next page'));
		}
		else {
			//printf(' &#160;&#160;&#160; (%s/%s) &#160;&#160;&#160;<span style="color:black;">[%s]</span>',$pageNo, $total_pages,tr('Επόμενη'));
            printf('<li class="currpage">%s/%s</li> <li class="disabled"><a href="#">%s &rarr;</a></li>',$pageNo, $total_pages,tr('Next page'));

        }
	} else {
		//SIMPLE SEARCG
		if ($pageNo > 1){
			$u_offset = urlencode($rep['prev_offset']);
			//printf('<li><a href="/archive/search?m=s&t=%s&o=%s&d=%s&y1=%s&y2=%s&c=%s&sl=%s">&larr; %s</a></li> ',$u_term,$u_offset,$d,$u_y1,$u_y2,$u_col,$sl,tr('Προηγούμενη'));
			$new_url = Putil::replaceRelativeUrlGetParams(array('m'=>'s','o'=>$u_offset),$org_url);
			printf('<li><a href="%s">&larr; %s</a></li>',$new_url,tr('Previous page'));
		}
		else {
			//echo('<span style="color:black;">[' .tr('Προηγούμενη') .']</span>');
            echo(' <li class="disabled"><a href="#">&larr; ' .tr('Previous page') .'</a></li>');

        }

		#echo(" (total records $total_cnt) " );
		if ($pageNo<$total_pages){
			$u_offset = urlencode($rep['next_offset']);
// 			printf('<li class="currpage">%s/%s</li> <li><a href="/archive/search?m=s&t=%s&o=%s&d=%s&y1=%s&y2=%s&c=%s&sl=%s">%s &rarr;</a></li>',
// 			$pageNo, $total_pages,$u_term,$u_offset,$d,$u_y1,$u_y2,$u_col,$sl,tr('Επόμενη'));
			$new_url = Putil::replaceRelativeUrlGetParams(array('m'=>'s','o'=>$u_offset),$org_url);
			printf('<li class="currpage">%s %s %s %s</li><li><a href="%s">%s &rarr;</a></li>',tr('page'),$pageNo,tr('from'),$total_pages,$new_url,tr('Next page'));

		}
		else {
			//printf(' &#160;&#160;&#160; (%s/%s) &#160;&#160;&#160;<span style="color:black;">[%s]</span>',$pageNo, $total_pages,tr('Επόμενη'));
            printf(' <li class="currpage">%s %s %s %s</li> <li class="disabled"><a href="#">%s &rarr;</a></li>',tr('page'),$pageNo,tr('from'),$total_pages,tr('Next page'));

		}
	}

	//echo("</span>");
    echo("</ul>");

};
#####################################################################################



?>



<?php if ($d == 1): #DISPLAY 1 (LIST) ?>

<?php

#####################################################################################
### TABLE HEADER LIST
#####################################################################################


if (variable_get('arc_search_display_collection_filters')){
	$ahrefs = $collections_block_line(1,$k);
	echo('<div class="row sfilters">');
		echo('<div class="col-md-11">');

			foreach ($ahrefs as $ah) {
				echo($ah);
				//echo('&#160;&#160;&#160;');
			}
		echo("</div>");
	echo('<div class="col-md-1">');
		$size_buttons();
		echo("</div>");
	echo("</div>"); //End Row

	$ahrefs = $collections_block_line(2,$k);
	if (count($ahrefs) > 0){
	echo('<div class="row sfilters">');
		echo('<div class="col-md-12">');

		//echo('<tr><th colspan="4">');
			printf('<div id="browsec2">&#160;&#160;&#160;&#160;');
			foreach ($ahrefs as $ah) {
				echo($ah);
				echo('&#160;&#160;&#160;');
			}
			echo("</div>");

		echo("</div>");
	echo("</div>"); //End Row
		//echo('</th></tr>');
	}

}else {
	echo('<div class="row sfilters">');
	echo('<div class="col-md-12">');
	echo(tr('search results'));
	echo('</div>');
	echo('</div>');
}



	$print_years_flag =  variable_get('arc_menu_dates', 1);
	if ($print_years_flag > 0){

		echo('<div class="row sfilters">');
		//printf('<tr><th colspan="%s">',$colspan);
		echo('<div class="col-md-9">');
			$years_block($y1,$y2);
		echo("</div>");
		echo('<div class="col-md-3">');
			$order_block($r);
		echo("</div>");

		echo("</div>"); //End Row

		//echo('</th></tr>');
		//echo('</thead>');
		//echo('<tbody valign="top">');
		//echo("\n");
	}


    //Moved Kritiria and subjects outside of results table
//     $kritiria();
    $subjects = $rep['subjects'];
    if (!empty($subjects)){
        $subjects_print($subjects);
    }

	echo('<div class="row">');
	echo('<table id="tresults" class="table table-striped table-bordered" summary="Αναλυτικά αποτελέσματα αναζήστησης με παρουσίαση του τύπου της οντότητας και του αντίστοιχου τίτλου">');
	echo('<caption class="rescnt row res-infobar">');
			$kritiria();
	echo('</caption>');
	echo("\n");
	#echo('<thead><tr><th>Συλλογή</th><th></th><th>Τίτλος</th><th>Τόπος</th><th>\'Ετος</th><th></th></tr></thead>');
	echo('<thead>');
	#if ($items_with_pages){
	#	echo('<tr><th colspan="4">'); $size_buttons(); echo('</th></tr>');
	#}

	$colspan="4";
	if (user_access_admin()){
		$colspan="5";
	}

	//echo('<tr>');
	//printf('<th colspan="%s">',$colspan);
		//Removed Buttons and view options - No need to be in the table
	//echo('</th></tr>');

		//Remover Browsec2

	$print_years_flag =  variable_get('arc_menu_dates', 1);
	if ($print_years_flag > 0){

		//printf('<tr><th colspan="%s">',$colspan);
		//$years_block($y1,$y2);
		//$order_block($r);
		//echo('</th></tr>');
		//echo('</thead>');
		echo('<tbody valign="top">');
		echo("\n");
	}


#####################################################################################
### TABLE BODY LIST
#####################################################################################

	PUtil::item_list($result, $obj_type_names, false, true);


#####################################################################################
### TABLE FOOTER LIST
#####################################################################################
	echo("</tbody>\n");
	echo('<tfoot><tr>');
	printf('<th colspan="%s" style="text-align: center;">',$colspan);
	if ($total_cnt > 0){
		$pagingBlock();
	}

	if ($term_search_flag){
		printf('<div>%s, <a href="/archive/search">%s</a></div>',tr('End of search'),tr('return to search page'));
	}

	echo("</th></tr></tfoot>\n");

	echo("</table>\n");
	echo("</div>");

	if (user_access_admin()){
		echo('<div class="folder_tools">');
		printf('<input type="hidden" name="src_folder"/>');


		#dbToSelect($dbh, "SELECT item_id,label from dsd.item2 where obj_type='silogi'", "folder", null);

		printf('<button type="button" name="action" value="clear_flder" onclick="clear_folder()">cls</button>');
		printf('<input id="id_folder_name" type="text" name="folder_name" value="%s" size="26"/>',null);
		#printf('<input id="id_folder" type="hidden" name="folder" /> ');
		printf('<input id="id_folder"  type="text" name="folder" value="%s" size="1" />',null);

		printf('<button type="button" name="action" value="copy" onclick="copy_to_folder()">copy</button>');
		echo('<span class="sep">&#160;</span>');
		printf('<button type="button" name="action" value="select_all" onclick="select_all()">select_all</button>');

		echo("</div>");
	}


?>
<?php
endif;
if ($d > 1):  #DISPLAY = 2 (thumbs)
?>

<?php


#####################################################################################
### TABLE HEADER THUMBS
#####################################################################################

	//Browsec1
	$ahrefs = $collections_block_line(1,$k);
	echo('<div class="row sfilters">');
		echo('<div class="col-md-11">');

			foreach ($ahrefs as $ah) {
				echo($ah);
				//echo('&#160;&#160;&#160;');
			}
		echo("</div>");
	echo('<div class="col-md-1">');
		$size_buttons();
		echo("</div>");
	echo("</div>"); //End Row


	//Browsec2
	$ahrefs = $collections_block_line(2,$k);
	if (count($ahrefs) > 0){
	echo('<div class="row sfilters">');
		echo('<div class="col-md-12">');

		//echo('<tr><th colspan="4">');
			printf('<div id="browsec2">&#160;&#160;&#160;&#160;');
			foreach ($ahrefs as $ah) {
				echo($ah);
				echo('&#160;&#160;&#160;');
			}
			echo("</div>");

		echo("</div>");
	echo("</div>"); //End Row
		//echo('</th></tr>');
	}



		echo('<div class="row sfilters">');
		//printf('<tr><th colspan="%s">',$colspan);
		echo('<div class="col-md-9">');
				$years_block($y1,$y2);
		echo("</div>");
		echo('<div class="col-md-3">');
				$order_block($r);
		echo("</div>");

		echo("</div>"); //End Row

		//echo('</th></tr>');
		//echo('</thead>');
		//echo('<tbody valign="top">');
		//echo("\n");



	//echo('<table id="tresults">');
	echo("\n");
	#echo('<thead><tr><th>Συλλογή</th><th></th><th>Τίτλος</th><th>Τόπος</th><th>\'Ετος</th><th></th></tr></thead>');
	//echo('<thead>');
//	echo('<tr><th>');
#	$collections_block($c);
#	$size_buttons();

//	$ahrefs = $collections_block_line(1,$k);
//	printf('<div id="browsec1">&#160;&#160;&#160;&#160;');
//	foreach ($ahrefs as $ah) {
//		echo($ah);
//		echo('&#160;&#160;&#160;');
//	}
//	echo("</div>");
//	$size_buttons();
//	echo('</th></tr>');

//	$ahrefs = $collections_block_line(2,$k);
//	if (count($ahrefs) > 0){
//		echo('<tr><th>');
//		printf('<div id="browsec2">&#160;&#160;&#160;&#160;');
//		foreach ($ahrefs as $ah) {
//			echo($ah);
//			echo('&#160;&#160;&#160;');
//		}
//		echo("</div>");
//	}


//	echo('</th></tr>');
//	echo('<tr><th>');
//	$years_block($y1,$y2);
//	$order_block($r);
//	echo('</th></tr>');
//	echo('</thead>');
	//echo('</table>');

	$kritiria();
	$subjects = $rep['subjects'];
	$subjects_print($subjects);

	printf('<div id="thl_%s" class="row res-items-cont">',$d);

#####################################################################################
### TABLE BODY THUMBS
#####################################################################################


	foreach($result as $row){
		$title =$row['title'];
		$item_id = $row['item_id'];
		$thumb1 = $row['thumb'];
		$thumb2 = $row['thumb1'];
		$thumb3 = $row['thumb2'];
		$obj_type = $row['obj_type'];


		$folder_flag = $row['folder'];

		$aggr_flag = $row['issue_aggr'];
		if ($d == 2){
			$thumb = $thumb1;
		} else if($d == 3){
			$thumb = $thumb2;
		} else {
			$thumb = $thumb3;
		}
		if (! empty($thumb)){
			$src = sprintf("/media/%s",$thumb);
		} else{
			if ($obj_type == "silogi"){
				if ($d == 4){
					$src = "/_assets/img/books4_200.png";
				} else {
					$src = "/_assets/img/books4_110.png";
				}
			} else{
				$src = "/_assets/img/pixel.gif";
			}
		}
		printf('<div class="item_thl">');
		printf('<a href="/archive/item/%s&lang=%s" title="%s"><img class="resimg" src="%s" alt="%s"/></a>',$item_id,$lang,htmlspecialchars($title), $src, htmlspecialchars($title));
			if ($folder_flag){
				if ($d == 4){
					printf('<img class="folderico" src="/_assets/img/items/folder48.png"/>');
				} else {
					printf('<img class="folderico" src="/_assets/img/items/folder24.png"/>');
				}
			}
		echo('</div>');
	}

	echo('<div class="spacer">&nbsp;</div>');
	echo('</div>');



#####################################################################################
### TABLE FOOTER THUMBS
#####################################################################################

	//echo('<br/>');
	//echo('<table><tr><td style="text-align: center;">');
	$pagingBlock();
	//echo('</td></tr></table>');
?>

<?php endif;?>




@stop