<?php
/* @var $graph GGraphO  */
$graph = $_REQUEST['graph'];
$item_basic  = $_REQUEST['item_basic'];
$item_id = $item_basic['item_id'];
$v = $graph->getVertex(GURN::createOLDWithId($item_id));
GGraphUtil::dumpVertex($v);
$opac = new OpacHelper($item_basic['jdata']);
$public_title = $opac->opac1('public_title');

$uuid = $item_basic['item_id'];
if (isset($public_title['title'])){
	$title = $public_title['title'];
} else {
	$title = $v->getPropertyValue('dc:title:');
}

$URI = Config::get('arc.RDA_LINK').$uuid;
$URL = Config::get('arc.ITEM_URL').$item_id;

?>
<rdf:RDF
      xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
      xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
      xmlns:foaf="http://xmlns.com/foaf/0.1/"
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:bibo="http://purl.org/ontology/bibo/"
      xmlns:dcterms="http://purl.org/dc/terms/"
      >
<foaf:PersonalProfileDocument rdf:about="">
  <foaf:maker rdf:resource="{{$URI}}"/>
  <foaf:primaryTopic rdf:resource="{{$URI}}"/>
</foaf:PersonalProfileDocument>

<foaf:Person rdf:about="{{$URI}}">
	<dc:identifier>{{$URI}}</dc:identifier>
	<dc:description>{{$URL}}</dc:description>
	<foaf:name>{{$title}}</foaf:name>
</foaf:Person>
</rdf:RDF>



