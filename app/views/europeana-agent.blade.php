<?php
$data = $_REQUEST['data'];

$lang = 'el';
$item_id = $_REQUEST['item_id'];
$title = $data['title'];
$date = isset($data['dc:date']) ? $data['dc:date'] : null;

$URI = Config::get('arc.EUROPEANA_RDA_LINK','EUROPEANA_RDA_LINK/').$item_id;
$URL = Config::get('arc.EUROPEANA_ITEM_URL','EUROPEANA_ITEM_URL/').$item_id;

//<rdf:RDF xsi:schemaLocation="http://www.w3.org/1999/02/22-rdf-syntax-ns# http://www.europeana.eu/schemas/edm/EDM.xsd"
//<dc:description>{{$URL}}</dc:description>
?>
<rdf:RDF
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
  xmlns:foaf="http://xmlns.com/foaf/0.1/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:bibo="http://purl.org/ontology/bibo/"
  xmlns:dcterms="http://purl.org/dc/terms/"
  xmlns:edm="http://www.europeana.eu/schemas/edm/"
  xmlns:ore="http://www.openarchives.org/ore/terms/"
  xmlns:owl="http://www.w3.org/2002/07/owl#"
  xmlns:skos="http://www.w3.org/2004/02/skos/core#"
  xmlns:rdau="http://www.rdaregistry.info/Elements/u/"
  xmlns:wgs84_pos="http://www.w3.org/2003/01/geo/wgs84_pos#"
  xmlns:crm="http://www.cidoc-crm.org/rdfs/cidoc-crm#"
  xmlns:cc="https://creativecommons.org/ns#"
  xmlns:rdaGr2="http://rdvocab.info/ElementsGr2/"
  xmlns:oai="http://www.openarchives.org/OAI/2.0/"
>
<edm:Agent rdf:about="{{$URI}}">
<skos:prefLabel xml:lang="{{$lang}}">{{$title}}</skos:prefLabel>
<foaf:name>{{$title}}</foaf:name>
<dc:identifier>{{$URI}}</dc:identifier>
@if (!empty($date))<dc:date>{{$date}}</dc:date>@endif
</edm:Agent>
</rdf:RDF>



