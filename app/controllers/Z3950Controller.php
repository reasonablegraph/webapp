<?php

require 'File/MARC.php';

class Z3950Controller extends BaseController {
	
	public function ws() {
		$term = get_get("term");
		if (empty($term)) {
			return;
		}
		
		$host = 'z3950.nlg.gr:210/biblios';
		
		//$term = 'αλίκη';
		$query = sprintf('"%s"', $term);
		
		//Log::info("QUERY: " . $query);
		
		$rep = array();
		$records = array();
		
		$max_res = 16;
		
		$yconn = yaz_connect($host);
		yaz_syntax($yconn, "unimarc");
		yaz_range($yconn, 1, $max_res);
		yaz_search($yconn, "rpn", $query);
		
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
			$yaz_rec_raw = yaz_record($yconn, $p, "raw");
			if (empty($yaz_rec_raw)) {
				continue;
			}
			
			$bibrec = new File_MARC($yaz_rec_raw, File_MARC::SOURCE_STRING);
			$marc = $bibrec->next();
			// 200 1 $a Η Αλίκη στη χώρα των θαυμάτων $f Λ. Κάρολ $g εικονογράφηση Φ. Ροβίρα $g διασκευή Ε. Χοσέ $g μετάφραση Φ. Λέτζης
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
			$rec['title'] = $title;
			
			$yaz_rec_str = yaz_record($yconn, $p, "string");
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
}
?>