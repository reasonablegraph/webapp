<?php



use File\MARC;
class MarcController extends BaseController {


	private function _import(&$out,$file_path){

		require 'File/MARC.php';
		$journals = new File_MARC($file_path);
		while ($record = $journals->next()) {
			$title_a = null;
			$title_b = null;
			$title_c = null;
			$titles = $record->getFields('245');
			if ($titles) {
				foreach ( $titles as $t ) {
						$title_a = FileMarcUtil::getSubFieldData($t,'a');
						$title_b = FileMarcUtil::getSubFieldData($t,'b');
						$title_c = FileMarcUtil::getSubFieldData($t,'c');
				}
			}
			$out .= sprintf("$title_a   ‡  $title_b ‡ $title_c\n");


			$idata = new ItemMetadata();
			//$uuid = PDao::createUUID();

			$title = $title_a;
			$obj_type = "auth-manifestation";
			$w = 0;
			$idata->addValueSK ( DataFields::ea_obj_type, $obj_type, null, null, null, null, null, null, $w ++ );
			$idata->addValueSK ( DataFields::dc_title, $title, null, null, null, null, null, null, $w ++ );
			$idata->addValueSK ('ea:manif:Title_Remainder', $title_b, null, null, null, null, null, null, $w ++ );
			$idata->addValueSK ('ea:manif:Title_Responsibility', $title_c, null, null, null, null, null, null, $w ++ );
			$idata->setValueSK('ea:status:', 'pending');

			$is = new ItemSave();
			$is->setIdata($idata);
			$nitem_id = $is->save_item();

		}

	}

	public function import() {

		auth_check_mentainer();

		//Admin only
		$is_admin = ArcApp::user_access_admin();
		if (!$is_admin){
			$URL = UrlPrefixes::$cataloging;
			$response = Response::make('', 301);
			$response->header('Location', $URL);
			return $response;
		}

		echo '<div>';
		echo '<form method="post" enctype="multipart/form-data">';
		//echo '<input type="file" name="fileToUpload" id="fileToUpload" style="float:left">';
		echo '<input name="uploadedfile[]" type="file" multiple="multiple" style="float:left" />';
		echo '<input type="submit" name="import" value="import"  style="clear:right"/>';
		echo '</form>';
		echo '</div>';


		$import = false;
		if (isset($_POST['import']) && $_POST['import'] == 'import') {
			$import = true;
		}
		if (!($import)) {
			return;
		}


		$extract_file_info_from_upload_form = function (){
			$app= App::make('arc');
			$upload_files = $app->upload_files;
			if (isset($upload_files['uploadedfile'])){
				$files = $upload_files['uploadedfile'];
				if (count($files) > 0){
					$f = array_shift($files);
					return array($f['tmp_name'], $f['name'],$f['extension']);
				}
			}
			return array(null,null,null);
		};

		$upload_info = $extract_file_info_from_upload_form();
		$file_path = $upload_info[0];
		if ($file_path != null ){

			$out ='';
			$this->_import($out, $file_path);
			echo("<pre>");
			echo $out;
			echo("</pre>");

		}


// 		print_r($_FILES);
// 		return;
// 		$uploadData = $_FILES['fileToUpload'];
// 		if (! empty( $uploadData)){
// 			$type = $uploadData['type']	;
// 			if ( preg_match('/image/', $type)) {
// 				$fileName = $uploadData['name'];
// 				$pattern = '/^def/';
// 				preg_match('/\.([\d|\w]+)$/', $fileName, $matches, PREG_OFFSET_CAPTURE);
// 				print_r($matches);
// 				$ext = $matches[1][0];
// 				if (! empty($ext)){
// 					print_r($uploadData);
// 					//$uploadData['tmp_name']
// 					//move_uploaded_file($uploadData['tmp_name'], $target);
// 				}
// 			}
// 		}



	}

	public function importAuthorities() {

		auth_check_mentainer();

		// TODO
		// exec AuthoritiesImport command
	}

	private function _export(&$out){


		$records = array();
		$dbh = dbconnect();
		$SQL="SELECT item_id,label,obj_type,obj_class from dsd.item2 where status in ('finish') and obj_type = 'auth-manifestation' ";
		$stmt = $dbh->prepare($SQL);
		//$stmt->bindParam(1, $o);
		$stmt->execute();
		while($r =$stmt->fetch()){
			$item_id = $r[0];
			$label = $r[1];
			$obj_type = $r[2];
			$obj_class = $r[3];
			$idata = PDao::get_item_metadata($item_id);
			$titleTxt = $idata->getFirstItemValue('dc:title:')->textValue();

			$out .= sprintf("%s : %s\n",$item_id,$titleTxt);


			$marc = new File_MARC_Record();

			//LEADER
			$marc->appendField(new File_MARC_Control_Field('001', 'GRMNL' . $item_id));

			//245 TITLE
			//$title_statement = $idata->getFirstItemValue('marc:title-statement:title');
			$title_statement = $idata->getFirstItemValue('dc:title:');
			//$lnk = $title_statement->recordId();

			$title_remainder = $idata->getFirstItemValue('ea:manif:Title_Remainder');
			$title_responsib = $idata->getFirstItemValue('ea:manif:Title_Responsibility');

			//echo $title_statement->textValue() . "\n";

			$subfields = array();
			$subfields[] = new File_MARC_Subfield('a', $title_statement->textValue());
			if (!empty($title_remainder)){
				$subfields[] = new File_MARC_Subfield('b', $title_remainder->textValue());
			}
			if (!empty($title_responsib)){
				$subfields[] = new File_MARC_Subfield('c', $title_responsib->textValue());
			}
			$marc->appendField(new File_MARC_Data_Field('245',$subfields, null, null));



			// 			// 		//260 Publication
			// 			$pub_statements = $idata->getItemValues('ea:publication:statement');
			// 			foreach ($pub_statements as $ps){
			// 				$lnk = $ps->recordId();

			// 				$publisher = $idata->getFirstItemValue('dc:publisher:',$lnk);
			// 				$publishDate =$idata->getFirstItemValue('ea:date:orgissued', $lnk);


			// 				$subfields = array();
			// 				if (!empty($publisher)){
			// 					$subfields[] = new File_MARC_Subfield('b', $publisher->textValue());
			// 				}
			// 				if (!empty($publishDate)){
			// 					$subfields[] = new File_MARC_Subfield('c', $publishDate->textValue());
			// 				}
			// 				if (!empty($subfields)){
			// 					$marc->appendField(new File_MARC_Data_Field('260',$subfields, null, null));
			// 				}
			// 			}

			// 			//700 Person
			// 			$contributors = $idata->getItemValues('dc:contributor:author',null);
			// 			foreach ($contributors as $c){
			// 				$subfields = array();
			// 				$subfields[] = new File_MARC_Subfield('a', $c->textValue());
			// 				$subfields[] = new File_MARC_Subfield('e', 'author');
			// 				$marc->appendField(new File_MARC_Data_Field('700',$subfields, null, null));
			// 			}
			// 			$contributors = $idata->getItemValues('ea:contributor:editor',null);
			// 			foreach ($contributors as $c){
			// 				$subfields = array();
			// 				$subfields[] = new File_MARC_Subfield('a', $c->textValue());
			// 				$subfields[] = new File_MARC_Subfield('e', 'editor');
			// 				$marc->appendField(new File_MARC_Data_Field('700',$subfields, null, null));
			// 			}
			// 			$contributors = $idata->getItemValues('ea:contributor:introducer',null);
			// 			foreach ($contributors as $c){
			// 				$subfields = array();
			// 				$subfields[] = new File_MARC_Subfield('a', $c->textValue());
			// 				$subfields[] = new File_MARC_Subfield('e', 'author of introduction');
			// 				$marc->appendField(new File_MARC_Data_Field('700',$subfields, null, null));
			// 			}
			// 			$contributors = $idata->getItemValues('ea:contributor:translator',null);
			// 			foreach ($contributors as $c){
			// 				$subfields = array();
			// 				$subfields[] = new File_MARC_Subfield('a', $c->textValue());
			// 				$subfields[] = new File_MARC_Subfield('e', 'translator');
			// 				$marc->appendField(new File_MARC_Data_Field('700',$subfields, null, null));
			// 			}
			// 			$contributors = $idata->getItemValues('dc:contributor:illustrator',null);
			// 			foreach ($contributors as $c){
			// 				$subfields = array();
			// 				$subfields[] = new File_MARC_Subfield('a', $c->textValue());
			// 				$subfields[] = new File_MARC_Subfield('e', 'illustrator');
			// 				$marc->appendField(new File_MARC_Data_Field('700',$subfields, null, null));
			// 			}

			$records[] =$marc;
			}


			return $records;


	}



	public function export() {

		auth_check_mentainer();

		echo '<div style="float:left">';
		echo '<form method="post">';
		echo '<input type="submit" name="export" value="export" />';
		echo '</form>';
		echo '</div>';


		$export = false;
		if (isset($_POST['export']) && $_POST['export'] == 'export') {
			$export = true;
		}
		if (!($export)) {
			return;
		}

		echo '<div style="float:left; margin-left:40px">';
		echo '<form method="post" action="/prepo/marc-download">';
		echo '<input type="submit" name="download" value="download" />';
		echo '</form>';
		echo '</div>';

		echo ('<br style="clear:both;"/>');
		echo ('<br/>');
		$out = "";
		$records = $this->_export($out);
		echo '<pre>';
		echo $out;
		echo '</pre>';

		$tmp_dir = Config::get('arc.EXPORT_DIR');
		$file_name = $tmp_dir ."/export.mrc";
		error_log($tmp_dir);
		error_log($file_name);

		$marc21_file = fopen($file_name, "wb");
		foreach ($records as $rec ){
			fwrite($marc21_file, $rec->toRaw());
		}
		fclose($marc21_file);

	}


	public function download_marc(){

		//echo "OK";

// 		$tmp_dir = Config::get('arc.EXPORT_DIR');
// 		$file = $tmp_dir ."/export.mrc";
// 		$headers = array(
// 				'Content-Type: application/marc',
// 		);
// 		//PROXY PROBLEM
// 		return Response::download($file, 'export.mrc', $headers);

		$tmp_dir = Config::get('arc.EXPORT_DIR');
		$filename = $tmp_dir ."/export.mrc";
		//$filesize = filesize($filename);
		$fname='export.mrc';
		$mime = "application/marc";
		//$mime = 'application/octet-stream';


		$response = Response::make('', 200);
		$response->header('Content-Type', $mime);
		//$response->header('Content-Length', $filesize);
		$response->header('Content-Disposition', sprintf('attachment; filename="%s"', $fname));
		$response->header('Content-Transfer-Encoding', 'binary');
		$response->header('X-Sendfile', $filename);
		return $response;

	}


}