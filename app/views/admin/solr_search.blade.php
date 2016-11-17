@section('content')

<?php auth_check_mentainer(); ?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.4/themes/redmond/jquery-ui.css">

<script type="text/javascript">
$(function () {
    $('input[name="term"]').autocomplete({
        source: '/prepo/solr_suggest',
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
	// reset search
	$input_copy = Input::all();
	unset($input_copy['term']);
	unset($input_copy['record_type']);
	unset($input_copy['authors']);
	unset($input_copy['authors_with_ids']);
	unset($input_copy['subjects']);
	unset($input_copy['publication_places']);
	unset($input_copy['publication_places_with_ids']);
	unset($input_copy['publication_types']);
	unset($input_copy['publishers']);
	unset($input_copy['digital_item_types']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset search</a>


<hr/>

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
							'core' => 'opac_index'
					)
			)
	);

	$client = new Solarium\Client($config);

	// get search term
	$term = get_get('term');

	// set parameters
	$start = PUtil::reset_int(get_get("start"),0);
	$resultsPerPage = 10;
	$maxResults = 100;
	$maxRelevantSubjects = 10;

	// create query
	$query = $client->createSelect();
	$edismax = $query->getEDisMax();
	$edismax->setQueryFields("catch_all_1^1000.0 catch_all_2^3.0 catch_all_3^2.0 catch_all_4^0.5");
	$edismax->setPhraseBigramFields("catch_all_1^1000.0 catch_all_2^3.0 catch_all_3^2.0 catch_all_4^0.5");
	$query->setQuery($term);
	$query->setRows($maxResults);
	$query->setStart($start)->setRows($resultsPerPage);
	$query->setFields(array('id','opac1'));
	// filter query to return only Works, Persons and Manifestations
	$query->createFilterQuery('types_filter_query')->setQuery("record_type:work OR record_type:person OR record_type:manifestation");
	
	// add stats settings
	$stats = $query->getStats();
	$stats->createField('num_of_manifestations');
	$stats->createField('num_of_digital_items');

	// add filters in case the page has been resubmitted by facet selection
	if (
			Input::has('record_type') ||
			Input::has('authors') ||
			Input::has('authors_with_ids') ||
			Input::has('subjects') ||
			Input::has('publication_places') ||
			Input::has('publication_places_with_ids') ||
			Input::has('publication_types') ||
			Input::has('publishers') ||
			Input::has('digital_item_types') ||
			Input::has('languages')
			) {

		$filters = array();

		if (Input::has('record_type')) {
			$filters[] = "record_type:" . "\"" . Input::get('record_type') . "\"";
		}
		if (Input::has('authors')) {
			$filters[] = "authors:" . "\"" . Input::get('authors') . "\"";
		}
		if (Input::has('authors_with_ids')) {
			$filters[] = "authors_with_ids:" . "\"" . Input::get('authors_with_ids') . "\"";
		}
		if (Input::has('subjects')) {
			$filters[] = "subjects:" . "\"" . Input::get('subjects') . "\"";
		}
		if (Input::has('publication_places')) {
			$filters[] = "publication_places:" . "\"" . Input::get('publication_places') . "\"";
		}
		if (Input::has('publication_places_with_ids')) {
			$filters[] = "publication_places_with_ids:" . "\"" . Input::get('publication_places_with_ids') . "\"";
		}
		if (Input::has('publication_types')) {
			$filters[] = "publication_types:" . "\"" . Input::get('publication_types') . "\"";
		}
		if (Input::has('publishers')) {
			$filters[] = "publishers:" . "\"" . Input::get('publishers') . "\"";
		}
		if (Input::has('digital_item_types')) {
			$filters[] = "digital_item_types:" . "\"" . Input::get('digital_item_types') . "\"";
		}
		if (Input::has('languages')) {
			$filters[] = "languages:" . "\"" . Input::get('languages') . "\"";
		}

		$filters_string = implode(" AND ", $filters);
		$query->createFilterQuery('filter_query')->setQuery($filters_string);

	}

	// setup highlighting
	$hl = $query->getHighlighting();
	$hl->setUseFastVectorHighlighter(true);
	$hl->setFields(array('title_hl','secondary_titles_hl','descriptions_hl','subjects_hl','authors_hl','places_hl'));

	// create facets
	$facetSet = $query->getFacetSet();
	$facetSet->createFacetField('record_type')->setField('record_type');
	$facetSet->createFacetField('authors')->setField('authors');
	$facetSet->createFacetField('authors_with_ids')->setField('authors_with_ids');
	$facetSet->createFacetField('subjects')->setField('subjects');
	$facetSet->createFacetField('publication_places')->setField('publication_places');
	$facetSet->createFacetField('publication_places_with_ids')->setField('publication_places_with_ids');
	$facetSet->createFacetField('publication_types')->setField('publication_types');
	$facetSet->createFacetField('publishers')->setField('publishers');
	$facetSet->createFacetField('digital_item_types')->setField('digital_item_types');
	$facetSet->createFacetField('languages')->setField('languages');

// 	$facetSet = $query->getFacetSet();
// 	$facet = $facetSet->createFacetPivot('type-author-subject');
// 	$facet->addFields('record_type,authors,subjects');
// 	$facet->setMinCount(0);

	// get results
	$resultset = $client->select($query);

	// get stats results
	$statsResult = $resultset->getStats();

	// get highlighting results
	$highlighting = $resultset->getHighlighting();

	// get facets results
	$record_type_facet = $resultset->getFacetSet()->getFacet('record_type');
	$authors_facet = $resultset->getFacetSet()->getFacet('authors');
	$authors_with_ids_facet = $resultset->getFacetSet()->getFacet('authors_with_ids');
	$subjects_facet = $resultset->getFacetSet()->getFacet('subjects');
	$publication_places_facet = $resultset->getFacetSet()->getFacet('publication_places');
	$publication_places_with_ids_facet = $resultset->getFacetSet()->getFacet('publication_places_with_ids');
	$publication_types_facet = $resultset->getFacetSet()->getFacet('publication_types');
	$publishers_facet = $resultset->getFacetSet()->getFacet('publishers');
	$digital_item_types_facet = $resultset->getFacetSet()->getFacet('digital_item_types');
	$languages_facet = $resultset->getFacetSet()->getFacet('languages');

	// get pivot facet result
// 	$facetResult = $resultset->getFacetSet()->getFacet('type-author-subject');

	// QUERY 2 - GET RELEVANT SUBJECTS
	$subjectsQuery = $client->createSelect();
	$subjectsEdismax = $subjectsQuery->getEDisMax();
	$subjectsEdismax->setQueryFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");
	$subjectsEdismax->setPhraseBigramFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");
	$subjectsQuery->setQuery($term);
	$subjectsQuery->setRows($maxRelevantSubjects);
	$subjectsQuery->setFields(array('id','opac1','is_subject'));
	$subjectsQuery->createFilterQuery('subjects_filter_query')->setQuery("is_subject:true");
	$subjectsResultset = $client->select($subjectsQuery);

	////////////////// PRESENTATION ////////////////////////////

	$numManifsFound = 0;
	$numDigitalItemsFound = 0;

	foreach ($statsResult as $field) {
		if ($field->getName() == 'num_of_manifestations') {
			$numManifsFound = $field->getSum();
		}
		if ($field->getName() == 'num_of_digital_items') {
			$numDigitalItemsFound = $field->getSum();
		}
	}

	echo "<h3>Solr Results for term '" . $term . "'</h3>";

	// number of results
	$numFound = $resultset->getNumFound();
	echo "Number of Results: " . $numFound . "<br/>";
	echo "Number of Manifestations: " . $numManifsFound . "<br/>";
	echo "Number of Digital Items: " . $numDigitalItemsFound . "<br/>";

	echo "<hr>";

	// links to relevant subjects
	echo "<h3>Relevant Subjects</h3>";
	echo "<ul>";
	foreach ($subjectsResultset as $document) {
		echo '<li><a href="http://localhost/archive/item/' . $document->id . '">' . $document->id . "</a></li>"; // . ": "$document->opac1;
	}
	echo "</ul>";

	echo "<hr>";

	// pagination
	echo "<h3>More result pages</h3>";
	$numPages = ceil($numFound / $resultsPerPage);
	for ($i = 0; $i < $numPages; $i++) {
		$start = $i * $resultsPerPage;
		$input_copy = Input::all();
		$currentStart = PUtil::reset_int(get_get("start"),0);
		unset($input_copy['start']);
		if ($start != $currentStart) {
			?><a href="?{{ http_build_query(array_merge($input_copy, array('start' => $start))) }}"><?php
			echo $i + 1;
			?></a>&nbsp;<?php
		} else {
			echo $i + 1;
			echo "&nbsp;";
		}
	}

	////// FACETS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	echo "<hr>";

	echo "<h3>Facets</h3>";
	// reset all facets
	$input_copy = Input::all();
	unset($input_copy['record_type']);
	unset($input_copy['authors']);
	unset($input_copy['authors_with_ids']);
	unset($input_copy['subjects']);
	unset($input_copy['publication_places']);
	unset($input_copy['publication_places_with_ids']);
	unset($input_copy['publication_types']);
	unset($input_copy['publishers']);
	unset($input_copy['digital_item_types']);
	?>
		<a href="?{{ http_build_query($input_copy) }}">[X] Reset all facets</a>
	<?php

	// PIVOT FACET CODE
	/*
	foreach ($facetResult as $pivot) {
		displayPivotFacet($pivot, Input::all());
	}

	// reset pivot facet 'type-author-subject'
	$input_copy = Input::all();
	unset($input_copy['record_type']);
	unset($input_copy['authors']);
	unset($input_copy['subjects']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">Reset facets</a>
	<?php
	*/


	// FACET 1: record type
	echo "<hr>";
	echo "<h4>Record Type Facet</h4>";
	echo "<ul>";
	foreach($record_type_facet as $value => $count) {
		if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('record_type' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['record_type']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset record type facet</a>
	<?php

	// FACET 2a: authors
	echo "<hr>";
	echo "<h4>Authors Facet</h4>";
	echo "<ul>";
	foreach($authors_facet as $value => $count) {
		if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('authors' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['authors']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset authors facet</a>
	<?php
	
	// FACET 2b: authors
	echo "<hr>";
	echo "<h4>Authors With Ids Facet</h4>";
	echo "<ul>";
	foreach($authors_with_ids_facet as $value => $count) {
	if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('authors_with_ids' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['authors_with_ids']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset authors with ids facet</a>
	<?php

	// FACET 3: subjects
	echo "<hr>";
	echo "<h4>Subjects Facet</h4>";
	echo "<ul>";
	foreach($subjects_facet as $value => $count) {
		if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('subjects' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['subjects']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset subjects facet</a>
	<?php

	// FACET 4a: publication place
	echo "<hr>";
	echo "<h4>Publication Place Facet</h4>";
	echo "<ul>";
	foreach($publication_places_facet as $value => $count) {
		if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('publication_places' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['publication_places']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset publication places facet</a>
	<?php
	
	// FACET 4b: publication place with id
	echo "<hr>";
	echo "<h4>Publication Place With Id Facet</h4>";
	echo "<ul>";
	foreach($publication_places_with_ids_facet as $value => $count) {
	if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('publication_places_with_ids' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['publication_places_with_ids']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset publication places with ids facet</a>
	<?php

	// FACET 5: publication type
	echo "<hr>";
	echo "<h4>Publication Type Facet</h4>";
	echo "<ul>";
	foreach($publication_types_facet as $value => $count) {
		if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('publication_types' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['publication_types']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset publication types facet</a>
	<?php

	// FACET 6: publisher
	echo "<hr>";
	echo "<h4>Publishers Facet</h4>";
	echo "<ul>";
	foreach($publishers_facet as $value => $count) {
		if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('publishers' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['publishers']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset publishers facet</a>
	<?php

	// FACET 7: digital item type
	echo "<hr>";
	echo "<h4>Digital Items Facet</h4>";
	echo "<ul>";
	foreach($digital_item_types_facet as $value => $count) {
		if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('digital_item_types' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['digital_item_types']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset digital items facet</a>
	<?php

	// FACET 8: language
	echo "<hr>";
	echo "<h4>Language Facet</h4>";
	echo "<ul>";
	foreach($languages_facet as $value => $count) {
		if ($count) { ?>
			<li><a href="?{{ http_build_query(array_merge(Input::all(), array('languages' => $value))) }}">{{ $value }} ({{ $count }})</a></li>
		<?php }
	}
	echo "</ul>";
	// reset facet
	$input_copy = Input::all();
	unset($input_copy['languages']);
	?>
	<a href="?{{ http_build_query($input_copy) }}">[X] Reset languages facet</a>
	<?php

	echo "<hr>";

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// print results
	echo "<h3>Results</h3>";
	echo "<table>";
	echo "<tr><th>&nbsp;node_id&nbsp;</th><th>&nbsp;matches&nbsp;</th><th>&nbsp;opac1&nbsp;</th></tr>";
	foreach ($resultset as $document) {
		echo "<tr>";
		echo "<td style='padding-left:5px; border:solid 1px #ddd;'>" . $document->id . "</td>";
		echo "<td style='padding-left:5px; border:solid 1px #ddd;'>";

		$highlightedDoc = $highlighting->getResult($document->id);
		if ($highlightedDoc) {
			foreach ($highlightedDoc as $field => $highlight) {
				echo $field . " => " . implode(' (...) ', $highlight) . '<br/>';
			}
		}
		echo "</td>";
		echo "<td style='padding-left:5px; border:solid 1px #ddd;'>" . $document->opac1 . "</td>";
		echo "</tr>";
	}
	echo '</table>';

}

/**
 * Recursively render pivot facets
 *
 * @param $pivot
 */
function displayPivotFacet($pivot, $outerfilters) {

	if ($pivot->getCount() > 0) {

		echo '<ul>';

		$field = $pivot->getField();
		$value = $pivot->getValue();
		$count = $pivot->getCount();
		$filters = array_merge($outerfilters, array($field => $value))

		?>
		<li><a href="?{{ http_build_query($filters) }}">{{ $value }} ({{ $count }})</a></li>
		<?php

		foreach ($pivot->getPivot() as $nextPivot) {
			displayPivotFacet($nextPivot, $filters);
		}

		echo '</ul>';

	}

}

?>

@stop