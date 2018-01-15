<?php

require 'File/MARC.php';

class Z3950Controller extends BaseController {

	public function ws() {
		$term = get_get("term");
		if (empty($term)) {
			return;
		}

		$host = Config::get('arc.Z3950_HOST');
		$syntax = Config::get('arc.Z3950_SYNTAX');
		$charset = Config::get('arc.Z3950_CHARSET');

		$query = sprintf('"%s"', $term);
		Log::info("QUERY: " . $query);

		//### Advancegreek encoding
		if ($charset == "advancegreek"){
			$search_string = "";
			$str_array = PUtil::mb_str_split($query);

			foreach ( $str_array as $chr ) {
				switch ($chr) {
					case 'α' : $search_string .= pack ( "n", 0xC181 ); break;
					case 'β' : $search_string .= pack ( "n", 0xC182 ); break;
					case 'γ' : $search_string .= pack ( "n", 0xC183 ); break;
					case 'δ' : $search_string .= pack ( "n", 0xC184 ); break;
					case 'ε' : $search_string .= pack ( "n", 0xC185 ); break;
					case 'ζ' : $search_string .= pack ( "n", 0xC186 ); break;
					case 'η' : $search_string .= pack ( "n", 0xC187 ); break;
					case 'θ' : $search_string .= pack ( "n", 0xC188 ); break;
					case 'ι' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'κ' : $search_string .= pack ( "n", 0xC18a ); break;
					case 'λ' : $search_string .= pack ( "n", 0xC18b ); break;
					case 'μ' : $search_string .= pack ( "n", 0xC18c ); break;
					case 'ν' : $search_string .= pack ( "n", 0xC18d ); break;
					case 'ξ' : $search_string .= pack ( "n", 0xC18e ); break;
					case 'ο' : $search_string .= pack ( "n", 0xC18f ); break;
					case 'π' : $search_string .= pack ( "n", 0xC190 ); break;
					case 'ρ' : $search_string .= pack ( "n", 0xC191 ); break;
					case 'ς' : $search_string .= pack ( "n", 0xC192 ); break;
					case 'σ' : $search_string .= pack ( "n", 0xC193 ); break;
					case 'τ' : $search_string .= pack ( "n", 0xC194 ); break;
					case 'υ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'φ' : $search_string .= pack ( "n", 0xC196 ); break;
					case 'χ' : $search_string .= pack ( "n", 0xC197 ); break;
					case 'ψ' : $search_string .= pack ( "n", 0xC198 ); break;
					case 'ω' : $search_string .= pack ( "n", 0xC199 ); break;
					case 'Α' : $search_string .= pack ( "n", 0xC181 ); break;
					case 'Β' : $search_string .= pack ( "n", 0xC182 ); break;
					case 'Γ' : $search_string .= pack ( "n", 0xC183 ); break;
					case 'Δ' : $search_string .= pack ( "n", 0xC184 ); break;
					case 'Ε' : $search_string .= pack ( "n", 0xC185 ); break;
					case 'Ζ' : $search_string .= pack ( "n", 0xC186 ); break;
					case 'Η' : $search_string .= pack ( "n", 0xC187 ); break;
					case 'Θ' : $search_string .= pack ( "n", 0xC188 ); break;
					case 'Ι' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'Κ' : $search_string .= pack ( "n", 0xC18a ); break;
					case 'Λ' : $search_string .= pack ( "n", 0xC18b ); break;
					case 'Μ' : $search_string .= pack ( "n", 0xC18c ); break;
					case 'Ν' : $search_string .= pack ( "n", 0xC18d ); break;
					case 'Ξ' : $search_string .= pack ( "n", 0xC18e ); break;
					case 'Ο' : $search_string .= pack ( "n", 0xC18f ); break;
					case 'Π' : $search_string .= pack ( "n", 0xC190 ); break;
					case 'Ρ' : $search_string .= pack ( "n", 0xC191 ); break;
					case 'Σ' : $search_string .= pack ( "n", 0xC193 ); break;
					case 'Τ' : $search_string .= pack ( "n", 0xC194 ); break;
					case 'Υ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'Φ' : $search_string .= pack ( "n", 0xC196 ); break;
					case 'Χ' : $search_string .= pack ( "n", 0xC197 ); break;
					case 'Ψ' : $search_string .= pack ( "n", 0xC198 ); break;
					case 'Ω' : $search_string .= pack ( "n", 0xC199 ); break;
					case 'ά' : $search_string .= pack ( "n", 0xC181 ); break;
					case 'έ' : $search_string .= pack ( "n", 0xC185 ); break;
					case 'ή' : $search_string .= pack ( "n", 0xC187 ); break;
					case 'ί' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'ό' : $search_string .= pack ( "n", 0xC18f ); break;
					case 'ύ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'ώ' : $search_string .= pack ( "n", 0xC199 ); break;
					case 'ΐ' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'ΰ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'Ά' : $search_string .= pack ( "n", 0xC181 ); break;
					case 'Έ' : $search_string .= pack ( "n", 0xC185 ); break;
					case 'Ή' : $search_string .= pack ( "n", 0xC187 ); break;
					case 'Ί' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'Ό' : $search_string .= pack ( "n", 0xC18f ); break;
					case 'Ύ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'Ώ' : $search_string .= pack ( "n", 0xC199 ); break;
					case 'Ϊ' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'Ϋ' : $search_string .= pack ( "n", 0xC195 ); break;
					default :
						$search_string .= $chr;
				}
			}
			$query = $search_string;
		}
		//###


		$rep = array();
		$records = array();

		$max_res = 16;

		$yconn = yaz_connect($host);
// 		yaz_syntax($yconn, "unimarc");
		yaz_syntax($yconn, $syntax);
		yaz_range($yconn, 1, $max_res);
		yaz_search($yconn, "rpn","@attr 1=4 " .$query);

		yaz_wait();

		$error = yaz_error($yconn);
		if (! empty($error)) {
			$rep['ERROR'] = $error;
			$response = Response::make($rep, 200);
			$response->header('Cache-Control', 'no-cache, must-revalidate');
			$response->header('Content-Type', 'application/json');
			return $response;
		}
		$hits = yaz_hits($yconn);
		// echo "Result Count $hits";
		$rep['result_count'] = $hits;

		// Log::info("I: ".$i);
		$rec = array();

		for ($p = 1; $p <= $max_res; $p++) {
			$yaz_rec_raw = yaz_record($yconn, $p, "raw; charset=$charset");
			if (empty($yaz_rec_raw)) {
				continue;
			}

			$bibrec = new File_MARC($yaz_rec_raw, File_MARC::SOURCE_STRING);
			$marc = $bibrec->next();
			// 200 1 $a Η Αλίκη στη χώρα των θαυμάτων $f Λ. Κάρολ $g εικονογράφηση Φ. Ροβίρα $g διασκευή Ε. Χοσέ $g μετάφραση Φ. Λέτζης

			if($syntax == 'unimarc'){

				$title_a = null;
				$title_f = null;
				$title_g = null;
				$titles = $marc->getFields('200');
				if ($titles) {
					// Now print all of the retrieved subjects
					foreach ( $titles as $t ) {
						// Log::info(print_r($title,true));
						// foreach ($title->getSubfields() as $code => $value) {
						// Log::info("$code :: $value");
						// }

						//$title_a = $title->getSubfield('a');
						$title_a = FileMarcUtil::getSubFieldData($t,'a');
						$title_f = FileMarcUtil::getSubFieldData($t,'f');
						$title_g = FileMarcUtil::getSubFieldData($t,'g');
					}
				}
				$title = $title_a;
				if (! empty($title_f)){
					$title  .= ' / ' . $title_f;
				}
				if (! empty($title_g)){
					$title  .= ' - ' . $title_g;
				}

			}else if($syntax == 'usmarc'){

				$title_a = null;
				$title_b = null;
				$title_c = null;
				$titles = $marc->getFields('245');
				if ($titles) {
					foreach ( $titles as $t ) {
							$title_a = FileMarcUtil::getSubFieldData($t,'a');
							$title_b = FileMarcUtil::getSubFieldData($t,'b');
							$title_c = FileMarcUtil::getSubFieldData($t,'c');
					}
				}
				$title = $title_a;
				if (! empty($title_b)){
					$title  .= ' ' . $title_b;
				}
				if (! empty($title_c)){
					$title  .= ' ' . $title_c;
				}

			}


			$rec['title'] = $title;

			$yaz_rec_str = yaz_record($yconn, $p, "string; charset=$charset");
			$rec['marc_raw'] = $yaz_rec_raw;
			$rec['marc_string'] = $yaz_rec_str;
			$rec['marc_pretty'] = $marc . "\n";
			$rec['marc_fields'] = array();

			foreach ($marc->getFields() as $tag => $tagvalue) {
				$marcfield = array();
				if ($tagvalue instanceof File_MARC_Control_Field) {
					$marcfield[$tag] = $tagvalue->getData();
				} else {
					$marcfield[$tag] = array();
					foreach ($tagvalue->getSubfields() as $code => $subdata) {
						$marcfield[$tag][$code] = $subdata->getData();
					}
					// indicators
					$ind1 = trim($tagvalue->getIndicator(1));
					$ind2 = trim($tagvalue->getIndicator(2));
					if (strlen($ind1)) {
						$marcfield[$tag]['ind1'] = $ind1;
					}
					if (strlen($ind2)) {
						$marcfield[$tag]['ind2'] = $ind2;
					}
				}
				$rec['marc_fields'][] = $marcfield;
			}

			$records[] = $rec;

			$rep['records'] = $records;
		}



		$response = Response::make($rep, 200);
		$response->header('Cache-Control', 'no-cache, must-revalidate');
		$response->header('Content-Type', 'application/json');
		return $response;
	}



	public function copycatalog() {
		echo '<script src="/_assets/vendor/clipboard/clipboard.min.js"></script>';
		$data = json_decode(get_post("data"), true);

//		echo "<pre>";
//		var_dump($data['marc_fields']);
//		echo "</pre>";

		echo '<div class="jsform"><div class="subitem">';
		echo '<h4>' . $data['title'] . '</h4>';


		echo '<ul class="subitem">';

		if(isset($data['marc_fields'])){
			foreach($data['marc_fields'] as $index => $marcRow) {
				foreach($marcRow as $tag => $marcField) {
					echo '<li><span class="dlab">' . $tag . '</span>';
					if (is_array($marcField)) {
						$ind1 = '&nbsp;';
						$ind2 = '&nbsp;';
						$htmlText = '';
						foreach($marcField as $code => $fieldData) {
							if ($code === 'ind1') {
								$ind1 = $fieldData;
							} else if ($code === 'ind2') {
								$ind2 = $fieldData;
							} else {
								$htmlText .= '<span class="marc-code">&nbsp;'
										. '<button class="btncopy" data-clipboard data-clipboard-target="#s' . $tag . $code . '">&Dagger;' . $code . '</button>'
										. '&nbsp;</span>'
										. '<span id="s' . $tag . $code . '" class="marc-field">' . $fieldData . '</span>';
							}
						}
						echo '<span class="dval-mono"><span class="marc-ind">&nbsp;&nbsp;' . $ind1 . $ind2 . '&nbsp;&nbsp;</span>' . $htmlText . '</span>';
					} else {
						echo '<span class="dval-mono"><span class="marc-ind">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
						echo '<span class="marc-field">' . $marcField . '</span></span>';
					}
					echo '</li>';
				}
			}
	}
		echo '</ul>';

		echo '</div></div>';

echo <<<SCRIPT
<script>
	var btns = document.querySelectorAll('.btncopy');
	for (var i = 0; i < btns.length; i++) {
		btns[i].addEventListener('mouseleave', function (e) {
			e.currentTarget.setAttribute('class', 'btncopy');
			e.currentTarget.removeAttribute('aria-label');
		});
	}
	function showTooltip(elem, msg) {
		elem.setAttribute('class', 'btncopy tooltipped tooltipped-s');
		elem.setAttribute('aria-label', msg);
	}
	function fallbackMessage(action) {
		var actionMsg = '';
		var actionKey = (action === 'cut' ? 'X' : 'C');
		if (/iPhone|iPad/i.test(navigator.userAgent)) {
			actionMsg = 'No support :(';
		}
		else if (/Mac/i.test(navigator.userAgent)) {
			actionMsg = 'Press ⌘-' + actionKey + ' to ' + action;
		}
		else {
			actionMsg = 'Press Ctrl-' + actionKey + ' to ' + action;
		}
		return actionMsg;
	}

	var clipboard = new Clipboard("[data-clipboard]");
	clipboard.on('success', function (e) {
		e.clearSelection();
		//console.info('Action:', e.action);
		//console.info('Text:', e.text);
		//console.info('Trigger:', e.trigger);
		//showTooltip(e.trigger, 'Copied!');
	});
	clipboard.on('error', function (e) {
		//console.error('Action:', e.action);
		//console.error('Trigger:', e.trigger);
		showTooltip(e.trigger, fallbackMessage(e.action));
	});
</script>
SCRIPT;
	}



	public function test() {

		// open tcp:z3950.nlg.gr:210/biblios
		$host = array();
		$host[] = 'z3950.nlg.gr:210/biblios';
		$query = '"αλίκη στη χώρα των θαυμάτων"';
		$num_hosts = count($host);

		echo 'You searched for ' . htmlspecialchars($query) . '<br />';
		for($i = 0; $i < $num_hosts; $i ++) {
			$id[] = yaz_connect($host[$i]);
			yaz_syntax($id[$i], "unimarc");
			yaz_range($id[$i], 1, 1);
			yaz_search($id[$i], "rpn", $query);
		}
		yaz_wait();
		for($i = 0; $i < $num_hosts; $i ++) {
			echo '<hr />' . $host[$i] . ':';
			$error = yaz_error($id[$i]);
			if (! empty($error)) {
				echo "Error: $error";
			} else {
				$hits = yaz_hits($id[$i]);
				echo "Result Count $hits";
			}
			echo '<dl>';
			for($p = 1; $p <= 10; $p ++) {
				$rec = yaz_record($id[$i], $p, "raw"); // raw string
				if (empty($rec))
					continue;
				echo "<pre>";
				print_r($rec);
				echo "</pre>";

				$bibrec = new File_MARC($rec, File_MARC::SOURCE_STRING);

				echo ("<pre>");
				while ( $record = $bibrec->next() ) {
					// Pretty print each record
					print $record;
					print "\n";
				}
				echo ("</pre>");

				echo ("<pre>");
				while ( $record = $bibrec->next() ) {
					// Retrieve an array of all of the 702 fields
					$subjects = $record->getFields('702');
					if ($subjects) {
						// Now print all of the retrieved subjects
						foreach ( $subjects as $field ) {
							print $field;
							print "\n";
						}
						print "\n";
					}
				}
				echo ("</pre>");

				// echo "<dt><b>$p</b></dt><dd>";
				// echo nl2br($rec);
				// echo "</dd>";
			}
			echo '</dl>';
		}
	}



	public function test2() {

		// open tcp:z3950.nlg.gr:210/biblios
		$host = array();
// 		$host[] = 'z3950.nlg.gr:210/biblios';
		$host[] = 'dsalib.gr:210/ADVANCE';
		$query = '"greece"';
		$num_hosts = count($host);

		echo 'You searched for ' . htmlspecialchars($query) . '<br />';

		for($i = 0; $i < $num_hosts; $i ++) {
			$id[] = yaz_connect($host[$i]);
			yaz_syntax($id[$i], "unimarc");
			yaz_range($id[$i], 1, 1);
			yaz_search($id[$i], "rpn", "@attr 1=4 " .$query);
		}

		yaz_wait();

		for($i = 0; $i < $num_hosts; $i ++) {
			echo '<hr />' . $host[$i] . ':</br>';
			$error = yaz_error($id[$i]);

			if (! empty($error)) {
				echo "Error: $error";
			} else {
				$hits = yaz_hits($id[$i]);
				echo "Result Count: $hits";
			}


			echo '<dl>';
			for($p = 1; $p <= 10; $p ++) {
			$rec = yaz_record($id[$i], $p, "raw"); // raw string
			if (empty($rec))
			continue;
			echo "<pre>";
			print_r($rec);
			echo "</pre>";

			$bibrec = new File_MARC($rec, File_MARC::SOURCE_STRING);

			echo ("<pre>");
			while ( $record = $bibrec->next() ) {
			// Pretty print each record
				print $record;
				print "\n";
			}
			echo ("</pre>");

			echo ("<pre>");
			while ( $record = $bibrec->next() ) {
			// Retrieve an array of all of the 702 fields
				$subjects = $record->getFields('702');
				if ($subjects) {
				// Now print all of the retrieved subjects
					foreach ( $subjects as $field ) {
					print $field;
					print "\n";
				}
				print "\n";
				}
			}
			echo ("</pre>");

				// ech"<dt><b>$p</b></dt><dd>";
				// echo nl2br($rec);
						// echo "</dd>";
			}
			echo '</dl>';
		}
	}













	public function test3() {



// 		$host = 'z3950.nlg.gr:210/biblios';
		$host = 'dsalib.gr:210/ADVANCE';

		$syntax = Config::get('arc.Z3950_SYNTAX');

		$rep = array();
		$records = array();

		function mb_str_split($string,$string_length=1) {
			if(mb_strlen($string)>$string_length || !$string_length) {
				do {
					$c = mb_strlen($string);
					$parts[] = mb_substr($string,0,$string_length);
					$string = mb_substr($string,$string_length);
				}while(!empty($string));
			} else {
				$parts = array($string);
			}
			return $parts;
		}

		//$binarydata = pack("nvc*", 0xC185, 0xC18A); //εκ

		$query = '"greece"';
		//$query = "Ναυτικόν";

		$search_string = "";
		$str_array = mb_str_split ($query);

		foreach ( $str_array as $chr ) {

			switch ($chr) {
					case 'α' : $search_string .= pack ( "n", 0xC181 ); break;
					case 'β' : $search_string .= pack ( "n", 0xC182 ); break;
					case 'γ' : $search_string .= pack ( "n", 0xC183 ); break;
					case 'δ' : $search_string .= pack ( "n", 0xC184 ); break;
					case 'ε' : $search_string .= pack ( "n", 0xC185 ); break;
					case 'ζ' : $search_string .= pack ( "n", 0xC186 ); break;
					case 'η' : $search_string .= pack ( "n", 0xC187 ); break;
					case 'θ' : $search_string .= pack ( "n", 0xC188 ); break;
					case 'ι' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'κ' : $search_string .= pack ( "n", 0xC18a ); break;
					case 'λ' : $search_string .= pack ( "n", 0xC18b ); break;
					case 'μ' : $search_string .= pack ( "n", 0xC18c ); break;
					case 'ν' : $search_string .= pack ( "n", 0xC18d ); break;
					case 'ξ' : $search_string .= pack ( "n", 0xC18e ); break;
					case 'ο' : $search_string .= pack ( "n", 0xC18f ); break;
					case 'π' : $search_string .= pack ( "n", 0xC190 ); break;
					case 'ρ' : $search_string .= pack ( "n", 0xC191 ); break;
					case 'ς' : $search_string .= pack ( "n", 0xC192 ); break;
					case 'σ' : $search_string .= pack ( "n", 0xC193 ); break;
					case 'τ' : $search_string .= pack ( "n", 0xC194 ); break;
					case 'υ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'φ' : $search_string .= pack ( "n", 0xC196 ); break;
					case 'χ' : $search_string .= pack ( "n", 0xC197 ); break;
					case 'ψ' : $search_string .= pack ( "n", 0xC198 ); break;
					case 'ω' : $search_string .= pack ( "n", 0xC199 ); break;
					case 'Α' : $search_string .= pack ( "n", 0xC181 ); break;
					case 'Β' : $search_string .= pack ( "n", 0xC182 ); break;
					case 'Γ' : $search_string .= pack ( "n", 0xC183 ); break;
					case 'Δ' : $search_string .= pack ( "n", 0xC184 ); break;
					case 'Ε' : $search_string .= pack ( "n", 0xC185 ); break;
					case 'Ζ' : $search_string .= pack ( "n", 0xC186 ); break;
					case 'Η' : $search_string .= pack ( "n", 0xC187 ); break;
					case 'Θ' : $search_string .= pack ( "n", 0xC188 ); break;
					case 'Ι' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'Κ' : $search_string .= pack ( "n", 0xC18a ); break;
					case 'Λ' : $search_string .= pack ( "n", 0xC18b ); break;
					case 'Μ' : $search_string .= pack ( "n", 0xC18c ); break;
					case 'Ν' : $search_string .= pack ( "n", 0xC18d ); break;
					case 'Ξ' : $search_string .= pack ( "n", 0xC18e ); break;
					case 'Ο' : $search_string .= pack ( "n", 0xC18f ); break;
					case 'Π' : $search_string .= pack ( "n", 0xC190 ); break;
					case 'Ρ' : $search_string .= pack ( "n", 0xC191 ); break;
					case 'Σ' : $search_string .= pack ( "n", 0xC193 ); break;
					case 'Τ' : $search_string .= pack ( "n", 0xC194 ); break;
					case 'Υ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'Φ' : $search_string .= pack ( "n", 0xC196 ); break;
					case 'Χ' : $search_string .= pack ( "n", 0xC197 ); break;
					case 'Ψ' : $search_string .= pack ( "n", 0xC198 ); break;
					case 'Ω' : $search_string .= pack ( "n", 0xC199 ); break;
					case 'ά' : $search_string .= pack ( "n", 0xC181 ); break;
					case 'έ' : $search_string .= pack ( "n", 0xC185 ); break;
					case 'ή' : $search_string .= pack ( "n", 0xC187 ); break;
					case 'ί' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'ό' : $search_string .= pack ( "n", 0xC18f ); break;
					case 'ύ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'ώ' : $search_string .= pack ( "n", 0xC199 ); break;
					case 'ΐ' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'ΰ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'Ά' : $search_string .= pack ( "n", 0xC181 ); break;
					case 'Έ' : $search_string .= pack ( "n", 0xC185 ); break;
					case 'Ή' : $search_string .= pack ( "n", 0xC187 ); break;
					case 'Ί' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'Ό' : $search_string .= pack ( "n", 0xC18f ); break;
					case 'Ύ' : $search_string .= pack ( "n", 0xC195 ); break;
					case 'Ώ' : $search_string .= pack ( "n", 0xC199 ); break;
					case 'Ϊ' : $search_string .= pack ( "n", 0xC189 ); break;
					case 'Ϋ' : $search_string .= pack ( "n", 0xC195 ); break;
					default :

					$search_string .= $chr;
			}
		}
		//echo "|".$search_string."|";

		$query = $search_string;
		$yconn = yaz_connect($host);

		$max_res = 1;

		yaz_syntax($yconn, $syntax);
		yaz_range($yconn, 1, $max_res);
		yaz_search($yconn, "rpn","@attr 1=4 " .$query);

		yaz_wait();

		$error = yaz_error($yconn);
		if (! empty($error)) {
			$rep['ERROR'] = $error;
			$response = Response::make($rep, 200);
			$response->header('Cache-Control', 'no-cache, must-revalidate');
			$response->header('Content-Type', 'application/json');
			return $response;
		}
		$hits = yaz_hits($yconn);
		// echo "Result Count $hits";
		$rep['result_count'] = $hits;

		// Log::info("I: ".$i);
		$rec = array();

		for ($p = 1; $p <= $max_res; $p++) {
			$yaz_rec_raw = yaz_record($yconn, $p, "raw; charset=advancegreek");
			if (empty($yaz_rec_raw)) {
				continue;
			}

			$bibrec = new File_MARC($yaz_rec_raw, File_MARC::SOURCE_STRING);
			$marc = $bibrec->next();
			// 200 1 $a Η Αλίκη στη χώρα των θαυμάτων $f Λ. Κάρολ $g εικονογράφηση Φ. Ροβίρα $g διασκευή Ε. Χοσέ $g μετάφραση Φ. Λέτζης

			if($syntax == 'unimarc'){

				$title_a = null;
				$title_f = null;
				$title_g = null;
				$titles = $marc->getFields('200');
				if ($titles) {
					// Now print all of the retrieved subjects
					foreach ( $titles as $t ) {
						// Log::info(print_r($title,true));
						// foreach ($title->getSubfields() as $code => $value) {
						// Log::info("$code :: $value");
							// }

							//$title_a = $title->getSubfield('a');
							$title_a = FileMarcUtil::getSubFieldData($t,'a');
							$title_f = FileMarcUtil::getSubFieldData($t,'f');
							$title_g = FileMarcUtil::getSubFieldData($t,'g');
						}
					}
					$title = $title_a;
					if (! empty($title_f)){
						$title  .= ' / ' . $title_f;
					}
					if (! empty($title_g)){
						$title  .= ' - ' . $title_g;
					}

				}else if($syntax == 'usmarc'){

					$title_a = null;
					$title_b = null;
					$title_c = null;
					$titles = $marc->getFields('245');
					if ($titles) {
						foreach ( $titles as $t ) {
							$title_a = FileMarcUtil::getSubFieldData($t,'a');
							$title_b = FileMarcUtil::getSubFieldData($t,'b');
							$title_c = FileMarcUtil::getSubFieldData($t,'c');
						}
					}
					$title = $title_a;
					if (! empty($title_b)){
						$title  .= ' ' . $title_b;
					}
					if (! empty($title_c)){
						$title  .= ' ' . $title_c;
					}

				}


				$rec['title'] = $title;

				$yaz_rec_str = yaz_record($yconn, $p, "string; charset=advancegreek");
				$rec['marc_raw'] = $yaz_rec_raw;
				$rec['marc_string'] = $yaz_rec_str;
				$rec['marc_pretty'] = $marc . "\n";
				$rec['marc_fields'] = array();

				foreach ($marc->getFields() as $tag => $tagvalue) {
					$marcfield = array();
					if ($tagvalue instanceof File_MARC_Control_Field) {
						$marcfield[$tag] = $tagvalue->getData();
					} else {
						$marcfield[$tag] = array();
						foreach ($tagvalue->getSubfields() as $code => $subdata) {
							$marcfield[$tag][$code] = $subdata->getData();
						}
						// indicators
						$ind1 = trim($tagvalue->getIndicator(1));
						$ind2 = trim($tagvalue->getIndicator(2));
						if (strlen($ind1)) {
							$marcfield[$tag]['ind1'] = $ind1;
						}
						if (strlen($ind2)) {
							$marcfield[$tag]['ind2'] = $ind2;
						}
					}
					$rec['marc_fields'][] = $marcfield;
				}

				$records[] = $rec;

				$rep['records'] = $records;
			}


			//file_put_contents("/tmp/a.txt", var_export($rep,true) );

			echo "<pre>"; print_r($rep);echo "</pre>";





		}


















}