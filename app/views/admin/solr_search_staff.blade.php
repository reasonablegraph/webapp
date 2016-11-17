@section('content')

<?php auth_check_mentainer(); ?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.4/themes/redmond/jquery-ui.css">

<script type="text/javascript">
$(function () {
    $('input[name="term"]').autocomplete({
        source: '/prepo/solr_suggest_staff',
        minLength: 2
    });
});
</script>

<h1>SOLR SEARCH</h1>

<hr/>

<form action="" method="get">
Search Term: <input name="term" type="text" /> <input name="submit" type="submit" />
</form>

<?php

// SUBMIT QUERY

if (get_get('submit') !== null) {

	// solr connection configuration
	$config = array(
			'endpoint' => array(
					'localhost' => array(
							'host' => '127.0.0.1',
							'port' => 8983,
							'path' => '/solr/',
							'core' => 'staff_index'
					)
			)
	);

	$client = new Solarium\Client($config);

	$term = get_get('term');
	$query = $client->createSelect();
	
	$edismax = $query->getEDisMax();
	$edismax->setQueryFields("fts_a^10.0 fts_b^5.0 fts_c^3.0 fts_d^1.0");
	$edismax->setPhraseBigramFields("fts_a^10.0 fts_b^5.0 fts_c^3.0 fts_d^1.0");
	
	$query->setStart(0);
	$query->setQuery($term);
	$query->setFields(array('item_id','title', 'object_type', 'status', 'create_date', 'opac1', 'fts_a', 'fts_b', 'fts_c', 'fts_d', 'id', 'record_type'));
	$query->addSort('create_date', $query::SORT_DESC);
	
	$query->createFilterQuery('my_filter_query')->setQuery("flags: flag1");

	$resultset = $client->select($query);

	////////////////// PRESENTATION ////////////////////////////

	echo "<h3>Solr Results for term '" . $term . "'</h3>";
	
	foreach ($resultset as $document) {
	
		echo '<hr/><table>';
	
		foreach ($document as $field => $value) {
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
		}
	
		echo '</table>';
	}
	
}
	
?>	

@stop
