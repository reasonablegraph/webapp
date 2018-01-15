

<?php


return array(


		'_DEFAULT_' => array(
				'staff'=>array(
										tr('Available entities for Creation')=> array(
												1 =>array('Work' =>'auth-work','Expression' => 'auth-expression','Manifestation' => 'auth-manifestation','Lemma' => 'lemma', 'Lemma Category' => 'lemma-category', 'Person' => 'auth-person','Family' => 'auth-family',),
												2 =>array('Organization' => 'auth-organization', 'Place' => 'auth-place','Concept' => 'auth-concept','Event' =>'auth-event','Genre' => 'auth-genre','Object' => 'auth-object','Subject Chain' => 'subject-chain'),
										),
								),
						),

// 		'dev' => array(
// 				'admin'=>array(
// 								tr('Available entities for Creation')=> array(
// 										1 =>array('auth-work' => 'Work','auth-expression' => 'Expression','auth-manifestation' => 'Manifestation','auth-person' => 'Person','auth-family' => 'Family','auth-organization' => 'Organization',),
// 										2 =>array('auth-place' => 'Place','auth-concept'=>'Concept','auth-event' =>'Event','auth-genre' =>'Genre','auth-object' =>'Object','subject-chain' =>'Subject Chain'),
// 								),
// 						),
// 				),

		'amelib_dev' => array(
				'staff'=>array(
								tr('Available entities for Creation')=> array(
										1 =>array('Work' =>'auth-work','Expression' => 'auth-expression','Manifestation' => 'auth-manifestation','Person' => 'auth-person','Family' => 'auth-family','Organization' => 'auth-organization',),
										2 =>array('Place' => 'auth-place','Concept' => 'auth-concept','Event' =>'auth-event','Genre' => 'auth-genre','Object' => 'auth-object','Subject Chain' => 'subject-chain'),
								),
						),
				),

		'amelib' => array(
				'staff'=>array(
								tr('Available entities for Creation')=> array(
										1 =>array('Work' =>'auth-work','Expression' => 'auth-expression','Manifestation' => 'auth-manifestation','Person' => 'auth-person','Family' => 'auth-family','Organization' => 'auth-organization',),
										2 =>array('Place' => 'auth-place','Concept' => 'auth-concept','Event' =>'auth-event','Genre' => 'auth-genre','Object' => 'auth-object','Subject Chain' => 'subject-chain'),
								),
						),
				),

		'demo_en' => array(
				'staff'=>array(
						tr('Available entities for Creation')=> array(
								1 =>array('Work' =>'auth-work','Expression' => 'auth-expression','Manifestation' => 'auth-manifestation','Person' => 'auth-person','Family' => 'auth-family','Organization' => 'auth-organization',),
								2 =>array('Place' => 'auth-place','Concept' => 'auth-concept','Event' =>'auth-event','Genre' => 'auth-genre','Object' => 'auth-object','Subject Chain' => 'subject-chain'),
						),
				),
		),

		'bibframe' => array(
				'staff'=>array(
						tr('Available entities for Creation')=> array(
								1 =>array('Work' =>'auth-work','Expression' => 'auth-expression','Manifestation' => 'auth-manifestation','Person' => 'auth-person','Organization' => 'auth-organization','Family' => 'auth-family','Relator' => 'auth-genre',),
								2 =>array('Place' => 'auth-place','Concept' => 'auth-concept','Event' =>'auth-event','Genre' => 'auth-genre','Object' => 'auth-object','Subject Chain' => 'subject-chain'),
						),
				),
		),

		'ghr' => array(
				'staff'=>array(
								tr('Available entities for Creation')=> array(
										1 =>array('Work' =>'auth-work','Expression' => 'auth-expression','Manifestation' => 'auth-manifestation','Person' => 'auth-person','Family' => 'auth-family','Organization' => 'auth-organization',),
										2 =>array('Place' => 'auth-place','Concept' => 'auth-concept','Event' =>'auth-event','Genre' => 'auth-genre','Object' => 'auth-object','Subject Chain' => 'subject-chain'),
								),
						),
				),

		'oralhistory' => array(
				'staff'=>array(
						tr('Available entities for Creation')=> array(
								1 =>array('Oral History' =>'auth-work','Manifestation' => 'auth-manifestation','Person' => 'auth-person','Family' => 'auth-family','Organization' => 'auth-organization','Place' => 'auth-place',),
								2 =>array('Concept' => 'auth-concept','Event' =>'auth-event','Genre' => 'auth-genre','Object' => 'auth-object','Subject Chain' => 'subject-chain'),
						),
				),
		),

// 		'scorp' => array(
// 				tr('Available entities for Creation')=> array(
// 						1 =>array('lemma' => 'Lemma','lemma-category' => 'Lemma Category' ,'auth-work' => 'Work',/*'auth-expression' => 'Expression',*/'auth-manifestation' => 'Book','web-site-instance' => 'Web site', 'periodic-publication' => 'Periodic Publication', 'media' => 'Media', 'auth-person' => 'Person',),
// 						2 =>array('auth-family' => 'Family','auth-organization' => 'Organization','auth-place' => 'Place','auth-concept'=>'Concept','auth-event' =>'Event','auth-genre' =>'Genre','auth-object' =>'Object',/*'subject-chain' =>'Subject Chain'*/),
// 				),
// 		),

		'scorp' => array(
						'admin'=>array(
								tr('Available entities for Creation')=> array(
										1 =>array('Lemma' => 'lemma', 'Lemma Category' => 'lemma-category','Work' => 'auth-work','Book' => 'auth-manifestation','Web site' => 'web-site-instance','Periodic Publication' => 'periodic-publication','Media' => 'media',),
										2 =>array( 'Person' => 'auth-person','Family' => 'auth-family','Organization' => 'auth-organization','Place' => 'auth-place','Concept' => 'auth-concept','Event' => 'auth-event',/*'auth-object' =>'Object',*/'General' => 'auth-general'/*'subject-chain' =>'Subject Chain'*/),
								),
						),
						'staff'=>array(
								tr('Available entities for Creation')=> array(
										1 =>array('Lemma' => 'lemma','Work' => 'auth-work','Book' => 'auth-manifestation','Web site' => 'web-site-instance','Periodic Publication' => 'periodic-publication','Media' => 'media',),
										2 =>array( 'Person' => 'auth-person','Family' => 'auth-family','Organization' => 'auth-organization','Place' => 'auth-place','Concept' => 'auth-concept','Event' => 'auth-event',/*'auth-object' =>'Object',*/'General' => 'auth-general'/*'subject-chain' =>'Subject Chain'*/),
								),
						),
			),

		'dryl' => array(
				'staff'=>array(
						tr('Available entities for Creation')=> array(
								1 =>array('Έργο' =>'auth-work', 'Βιβλίο' => array('obj_type' => 'auth-manifestation', 'parameter' => array('type' => 'book')), 'Περιοδικό' => 'periodic', 'Τεύχος Περιοδικού' => array('obj_type' => 'auth-manifestation', 'parameter' => array('type' => 'issue')),'Κατηγορία' => array('obj_type' => 'auth-concept', 'parameter' => array('type' => 'category')),'Γεγονός' => 'auth-event','Έννοια' => 'auth-concept',),
								2 =>array('Πρόσωπο' => 'auth-person','Οργανισμός' => 'auth-organization','Περιοχή' => 'auth-place','Συνέδριο' => array('obj_type' => 'auth-event', 'parameter' => array('type' => 'conference')),'Οικογένεια' => 'auth-family','Είδος' => 'auth-genre',/*'Αντικείμενο' => 'auth-object',*/'Θεματική Αλυσίδα' => 'subject-chain',),
						),
				),
		),


		'music' => array(
				'staff'=>array(
						tr('Available entities for Creation')=> array(
								1 =>array('Work' =>'auth-work',
													'Expression' => 'auth-expression',
													'Manifestation' => array('obj_type' => 'auth-manifestation', 'parameter' => array('type' => 'auth-manifestation')),
													'Μουσικό Work' => array('obj_type' => 'auth-work', 'parameter' => array('type' => 'auth-work-music')),
													'Μουσικό Expression' => array('obj_type' => 'auth-expression', 'parameter' => array('type' => 'auth-expression-music')),
													'Μουσικό Manifestation' => array('obj_type' => 'auth-manifestation', 'parameter' => array('type' => 'auth-manifestation-music')),
													'Πρόσωπο' => 'auth-person',
													'Οργανισμός' => 'auth-organization',),
								2 =>array('Οικογένεια' => 'auth-family','Περιοχή' => 'auth-place','Έννοια' => 'auth-concept','Γεγονός' => 'auth-event','Είδος' => 'auth-genre','Αντικείμενο' => 'auth-object','Θεματική Αλυσίδα' => 'subject-chain',),
						),
				),
		),




);
