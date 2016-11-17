<?php

return array(
		

		'BROWSE_FILTERS' => array(
		0 => array(),


		1 => array( 'SQL_TOKEN' => " to_tsquery('simple', '1|2|3|4|5|6|8|11') @@  fts_catalogs"), #periodika
		#AND not issue_aggr
		10 => array( 'SQL_TOKEN' => " to_tsquery('simple','3') @@  fts_catalogs ", 'collection'=>3), #periodika
		11 => array( 'SQL_TOKEN' => " to_tsquery('simple','4') @@  fts_catalogs ", 'collection'=>4), #efimerides

		12 => array( 'SQL_TOKEN' => "  to_tsquery('simple','1') @@  fts_catalogs ", 'collection'=>1),
		13 => array( 'SQL_TOKEN' => "  to_tsquery('simple','2') @@  fts_catalogs ", 'collection'=>2),
		14 => array( 'SQL_TOKEN' => "  to_tsquery('simple','6') @@  fts_catalogs ", 'collection'=>6),
		15 => array( 'SQL_TOKEN' => "  to_tsquery('simple','11') @@  fts_catalogs ", 'collection'=>11),
		16 => array( 'SQL_TOKEN' => "  to_tsquery('simple','7') @@  fts_catalogs ", 'collection'=>7),
		17 => array( 'SQL_TOKEN' => "  to_tsquery('simple','5') @@  fts_catalogs ", 'collection'=>5),
		18 => array( 'SQL_TOKEN' => "  to_tsquery('simple','10') @@  fts_catalogs ", 'collection'=>10),
		19 => array( 'SQL_TOKEN' => "  to_tsquery('simple','9') @@  fts_catalogs ", 'collection'=>9),
		20 => array( 'SQL_TOKEN' => "  to_tsquery('simple','12') @@  fts_catalogs ", 'collection'=>12),
		21 => array( 'SQL_TOKEN' => "  to_tsquery('simple','9') @@  fts_catalogs ", 'collection'=>9),
		22 => array( 'SQL_TOKEN' => "  to_tsquery('simple','9') @@  fts_catalogs ", 'collection'=>9),
		24 => array( 'SQL_TOKEN' => "  to_tsquery('simple','8') @@  fts_catalogs ", 'collection'=>8),
		25 => array( 'SQL_TOKEN' => "  to_tsquery('simple','13') @@  fts_catalogs ", 'collection'=>13),
		26 => array( 'SQL_TOKEN' => "  to_tsquery('simple','14') @@  fts_catalogs ", 'collection'=>14),

		60 => array( 'SQL_TOKEN' => "  item_id = 273 "),
		61 => array( 'SQL_TOKEN' => "  item_id = 300 "),
),



'BROWSE_lines' => array(
		1 =>  array('line' => 1, 'line1' => 1, 'line2' => 0, 'name' => 'all'),
		//2 =>  array('line' => 1, 'line1' => 2, 'line2' => 0, 'name' => 'Συλλογές', 'filter' => 25),
		//3 =>  array('line' => 1, 'line1' => 3, 'line2' => 0, 'name' => 'Εντυπα', 'filter' => 1),
		//4 =>  array('line' => 1, 'line1' => 4, 'line2' => 0, 'name' => 'web-sites', 'filter' => 19),
		5 =>  array('line' => 1, 'line1' => 5, 'line2' => 0, 'name' => 'Πρόσωπα', 'filter' => 26),
		//6 =>  array('line' => 1, 'line1' => 6, 'line2' => 0, 'name' => 'Χειρόγραφα', 'filter' => 1),


		########################################################################################

		#########  collections
		//11 => array('line' => 2, 'line1' => 2, 'line2' => 11, 'name' => 'Occupy Oakland', 'filter' => 60),
		//12 => array('line' => 2, 'line1' => 2, 'line2' => 12, 'name' => 'Δεκέμβρης 2008', 'filter' => 61),
#########  entipa
20 => array('line' => 2, 'line1' => 3, 'line2' => 20, 'name' => 'Περιοδικά', 'filter' => 10),
21 => array('line' => 2, 'line1' => 3, 'line2' => 21, 'name' => 'Εφημερίδες', 'filter' => 11),
22 => array('line' => 2, 'line1' => 3, 'line2' => 22, 'name' => 'Μπροσούρες', 'filter' => 17),
23 => array('line' => 2, 'line1' => 3, 'line2' => 23, 'name' => 'Βιβλία', 'filter' => 24),
24 => array('line' => 2, 'line1' => 3, 'line2' => 24, 'name' => 'Αφίσες', 'filter' => 12),
25 => array('line' => 2, 'line1' => 3, 'line2' => 25, 'name' => 'Προκηρύξεις', 'filter' => 13),
#########  webiste
//36 => array('line' => 2, 'line1' => 4, 'line2' => 36, 'name' => 'blogs', 'filter' => 21),
//37 => array('line' => 2, 'line1' => 4, 'line2' => 37, 'name' => 'social media', 'filter' => 22),
//38 => array('line' => 2, 'line1' => 4, 'line2' => 38, 'name' => 'other', 'filter' => 9),

)
);
