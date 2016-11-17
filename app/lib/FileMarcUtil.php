<?php

class FileMarcUtil {

	public static function getSubFieldData($field, $code){

		if (empty($field)) return null;

		$sf = $field->getSubField($code);

		if (empty($sf)) return null;

		return $sf->getData();
	}




/**
 *
 * @param File_MARC_Data_Field $field
 */
	public static function field2String($field){


	}


// 	foreach ($marc->getFields() as $tag => $tagvalue) {
// 		$marcfield = array();
// 		if ($tagvalue instanceof File_MARC_Control_Field) {
// 			$marcfield[$tag] = $tagvalue->getData();
// 		} else {
// 			$marcfield[$tag] = array();
// 			foreach ($tagvalue->getSubfields() as $code => $subdata) {
// 				$marcfield[$tag][$code] = $subdata->getData();
// 			}
// 			// indicators
// 			$ind1 = trim($tagvalue->getIndicator(1));
// 			$ind2 = trim($tagvalue->getIndicator(2));
// 			if (strlen($ind1)) {
// 				$marcfield[$tag]['ind1'] = $ind1;
// 			}
// 			if (strlen($ind2)) {
// 				$marcfield[$tag]['ind2'] = $ind2;
// 			}
// 		}
// 		$rec['marc_fields'][] = $marcfield;
// 	}
	
	
	
	public static function record2HtmlString($record) {
		
		$ret = "<b>LEADER</b>&nbsp;" . $record->getLeader() . "\n";
		
		
		foreach ($record->getFields() as $tag => $field) {
		
			$ret .= "&nbsp;&nbsp;&nbsp;<b>" . $tag . "</b>&nbsp;";

			if ($field instanceof File_MARC_Control_Field) { // control field

				$ret .= $field->getData();
				$ret .= "\n";

			} else if ($field instanceof File_MARC_Data_Field) { // data field

				$ret .= $field->getIndicator(1);
				$ret .= $field->getIndicator(2);
				$ret .= "&nbsp;";

				foreach ($field->getSubfields() as $code => $subdata) {
					$ret .= "<b>&Dagger;" . $code . "</b>&nbsp;" . $subdata->getData() . "&nbsp;";
				}

				$ret .= "\n";

			}
		}
		
		
		return $ret;
	}
}
