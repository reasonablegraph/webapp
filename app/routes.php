<?php

Route::get('/', function(){ return View::make('hello'); });
Route::get('test', function(){ return View::make('test'); });
Route::get('/testControler', array( 'as' => 'test', 'uses' => 'ArchiveController@show'));

//PUBLIC
Route::get('/archive/tagcloud', array( 'as' => 'public.tagcloud', 'uses' => 'ArchiveController@show'));
//Route::get('/archive/search', array( 'as' => 'public.search', 'uses' => 'ArchiveController@show'));
//Route::get('/archive/search', array('uses' => 'SearchController@search'));

Route::get('/ws/archive/search-all', array( 'uses' => 'ArchiveController@parchive_search_general'));
Route::get('/archive/search-all', array( 'uses' => 'ArchiveController@parchive_search_general'));
Route::get('/ws/archive/search-title', array( 'uses' => 'ArchiveController@parchive_search_title'));
Route::get('/archive/search-title', array( 'uses' => 'ArchiveController@parchive_search_title'));
Route::get('/ws/archive/search-terms', array( 'uses' => 'ArchiveController@parchive_search_terms'));
Route::get('/archive/search-terms', array( 'uses' => 'ArchiveController@parchive_search_terms'));
Route::get('/ws/archive/search-author', array( 'uses' => 'ArchiveController@parchive_search_author'));
Route::get('/archive/search-author', array( 'uses' => 'ArchiveController@parchive_search_author'));
Route::get('/ws/archive/search-place', array( 'uses' => 'ArchiveController@parchive_search_place'));
Route::get('/archive/search-place', array( 'uses' => 'ArchiveController@parchive_search_place'));

Route::get('/archive/term/{term?}', array('before'=>'argv', 'as' => 'public.subject', 'uses' => 'ArchiveController@parchive_term'));

//ITEMS
Route::get('/archive/itemxml/{pitem}',array('before'=>'argv', function($pitem = null){
	return App::make('ArchiveController')->callAction('parchive_display_item_xml',$parameters=array($pitem));
}));
Route::get('/archive/edm/{pitem}',array('before'=>'argv', function($pitem = null){
	return App::make('ArchiveController')->callAction('parchive_display_item_europeana',$parameters=array($pitem));
}));

Route::get('/archive/edm/agent/{pitem}',array('before'=>'argv', function($pitem = null){
	return App::make('ArchiveController')->callAction('parchive_europeana_agent_search',$parameters=array($pitem));
}));




Route::get('/archive/item', array('before'=>'argv',function($pitem = null){
	return App::make('ArchiveController')->callAction('parchive_display_item',$parameters=array(null,get_get("i")));
}));
Route::get('/archive/item/{pitem}',array('before'=>'argv', function($pitem = null){
	return App::make('ArchiveController')->callAction('parchive_display_item',$parameters=array(null,$pitem));
}));
Route::get('/archive/item/{pitem}/{partifact}',array('before'=>'argv', function($pitem = null,$partifact = null){
		return App::make('ArchiveController')->callAction('parchive_download_artifact',$parameters=array($pitem,$partifact,null));
}));
Route::get('/archive/item/{pitem}/download/{partifact}',array('before'=>'argv', function($pitem = null,$partifact = null){
		return App::make('ArchiveController')->callAction('parchive_download_artifact',$parameters=array($pitem,'download',$partifact));
}));
Route::get('/archive/item/{pitem}/direct/{partifact}',array('before'=>'argv', function($pitem = null,$partifact = null){
		return App::make('ArchiveController')->callAction('parchive_download_artifact',$parameters=array($pitem,'direct',$partifact));
	}));
Route::get('/archive/media/{partifact}/{mtype}',array(function($partifact = null,$mtype = null){
		return App::make('ArchiveController')->callAction('parchive_media',$parameters=array($partifact,$mtype));
	}));
Route::get('/archive/items/{pitem?}',array('before'=>'argv', function($pitem = null){
	if (empty($pitem)){return Redirect::to('/archive/search');};
	return App::make('ArchiveController')->callAction('parchive_display_item',$parameters=array(null,$pitem));
}));
Route::get('/archive/items/{pparent}/{pitem?}', array('before'=>'argv','uses' => 'ArchiveController@parchive_display_item'));

//ADMIN
Route::get('/prepo/menu', array('before'=>'argv', 'as' => 'admin.repomenu', 'uses' => 'AdminController@show'));
Route::get('/prepo/cataloging', array('before'=>'argv', 'as' => 'admin.cataloging', 'uses' => 'AdminCatalogingController@cataloging'));
Route::get('/prepo/new_item', array('before'=>'argv', 'as' => 'admin.cataloging', 'uses' => 'AdminCatalogingController@cataloging'));
Route::get('/prepo/update_folder_thumbs', array('uses'=>'AdminController@update_folder_thumbs'));
Route::post('/prepo/update_folder_thumbs', array('uses'=>'AdminController@update_folder_thumbs'));

Route::get('/archive/recent', array('before'=>'argv', 'as' => 'admin.recent', 'uses' => 'AdminController@parchive_recent'));
Route::get('/prepo/node_stats', array('before'=>'argv', 'as' => 'admin.node_stats', 'uses' => 'AdminController@show'));

//EDIT ITEM STEP 1
Route::get('/prepo/edit_step1',  array('as' => 'admin.edit_item_step1', 'uses' => 'EditItemController@step1'));
Route::post('/prepo/edit_step1', array('as' => 'admin.edit_item_step1', 'uses' => 'EditItemController@step1'));
Route::get('/prepo/edit_step2',  array('as' => 'admin.edit_item_step2', 'uses' => 'EditItemController@step2'));

Route::get('/prepo/edit_step3', array( 'uses' => 'EditItemController@step3'));
Route::post('/prepo/edit_step3', array( 'uses' => 'EditItemController@step3'));

#Route::get('/prepo/edit_step3', array('as' => 'admin.edit_item_step3', 'uses' => 'AdminController@show'));
#Route::post('/prepo/edit_step3', array('as' => 'admin.edit_item_step3', 'uses' => 'AdminController@show'));
Route::get('/prepo/edit_metadata', array('as' => 'admin.edit_metadata', 'uses' => 'AdminController@show'));
Route::post('/prepo/edit_metadata', array('as' => 'admin.edit_metadata','uses' => 'AdminController@edit_metadata'));
Route::get('/prepo/edit_bitstream', array('before'=>'argv', 'as' => 'admin.edit_bitstream', 'uses' => 'AdminController@edit_bitstream'));
Route::post('/prepo/edit_bitstream', array('before'=>'argv', 'as' => 'admin.edit_bitstream', 'uses' => 'AdminController@edit_bitstream'));

Route::get('/prepo/bitstreams', array('before'=>'argv', 'as' => 'admin.bitstreams', 'uses' => 'ArchiveController@show'));
Route::post('/prepo/bitstreams', array('before'=>'argv', 'as' => 'admin.bitstreams', 'uses' => 'ArchiveController@show'));

Route::get('/archive/download', array('before'=>'argv', 'uses' => 'AdminController@parchive_download'));
Route::get('/prepo/thumbs', array('before'=>'argv','as' => 'admin.thumbs', 'uses' => 'ArchiveController@show'));
Route::post('/prepo/thumbs', array('before'=>'argv','as' => 'admin.thumbs', 'uses' => 'ArchiveController@show'));
Route::get('/prepo/delete_thumb', array('before'=>'argv', 'uses' => 'AdminController@parchive_delete_thumb'));
Route::get('/prepo/move_bitstream', array('before'=>'argv', 'uses' => 'AdminController@parchive_move_bitstream'));
Route::post('/prepo/move_bitstream', array('before'=>'argv', 'uses' => 'AdminController@parchive_move_bitstream'));

Route::get('/prepo/items/relation', array('before'=>'argv', 'as' => 'admin.item_relation', 'uses' => 'ArchiveController@show'));
Route::post('/prepo/items/relation', array('before'=>'argv', 'as' => 'admin.item_relation', 'uses' => 'ArchiveController@show'));

Route::get('/ws/archive/search_item_by_title', array('uses' => 'AdminController@parchive_search_item_by_title'));
Route::get('/archive/search_item_by_title', array('uses' => 'AdminController@parchive_search_item_by_title'));
Route::get('/ws/archive/search_folder_by_title', array('uses' => 'AdminController@parchive_search_folder_by_title'));
Route::get('/archive/search_folder_by_title', array('uses' => 'AdminController@parchive_search_folder_by_title'));
Route::get('/ws/archive/search_actor_by_title', array('uses' => 'AdminController@parchive_search_actor_by_title'));
Route::get('/archive/search_actor_by_title', array('uses' => 'AdminController@parchive_search_actor_by_title'));
Route::get('/ws/archive/search-metadata-element', array('uses' => 'AdminController@parchive_search_metadata_elements'));
Route::get('/archive/search-metadata-element', array('uses' => 'AdminController@parchive_search_metadata_elements'));
Route::get('/ws/archive/search-isbn', array('uses' => 'AdminController@parchive_search_isbn'));
Route::get('/archive/search-isbn', array('uses' => 'AdminController@parchive_search_isbn'));
Route::get('/ws/archive/search-subtitle', array('uses' => 'AdminController@parchive_search_subtitle'));
Route::get('/archive/search-subtitle', array('uses' => 'AdminController@parchive_search_subtitle'));
Route::get('/ws/archive/find-relation', array('uses' => 'AdminController@parchive_find_relation'));
Route::get('/archive/find-relation', array('uses' => 'AdminController@parchive_find_relation'));
Route::get('/ws/archive/find-work', array('uses' => 'AdminController@parchive_find_work'));
Route::get('/archive/find-work', array('uses' => 'AdminController@parchive_find_work'));
Route::get('/ws/archive/find-place', array('uses' => 'AdminController@parchive_find_place'));
Route::get('/archive/find-place', array('uses' => 'AdminController@parchive_find_place'));
Route::get('/ws/archive/search-bookbinding-type', array('uses' => 'AdminController@parchive_search_bookbinding_type'));
Route::get('/archive/search-bookbinding-type', array('uses' => 'AdminController@parchive_search_bookbinding_type'));
Route::get('/ws/archive/search-material-type', array('uses' => 'AdminController@parchive_search_material_type'));
Route::get('/archive/search-material-type', array('uses' => 'AdminController@parchive_search_material_type'));
Route::get('/ws/archive/search-country', array('uses' => 'AdminController@parchive_search_country'));
Route::get('/archive/search-country', array('uses' => 'AdminController@parchive_search_country'));

Route::get('/ws/prepo/check-value-exists', array('uses' => 'AdminController@parchive_check_value_exists'));
Route::get('/prepo/check-value-exists', array('uses' => 'AdminController@parchive_check_value_exists'));
Route::get('/ws/archive/find-contributor', array('uses' => 'AdminController@parchive_find_contributor'));
Route::get('/archive/find-contributor', array('uses' => 'AdminController@parchive_find_contributor'));

Route::get('/prepo/ws/item_metadata', array('uses' => 'AdminController@parchive_ws_item_metadata'));

Route::get('/prepo/spool', array('before'=>'argv', 'as' => 'admin.spool', 'uses' => 'AdminController@show'));
// Route::post('/prepo/spool', array('before'=>'argv', 'as' => 'admin.spool', 'uses' => 'AdminController@show'));
Route::post('/prepo/spool', array('before'=>'argv', 'as' => 'admin.spool', 'uses' => 'AdminController@spool'));


Route::get('/prepo/move', array('before'=>'argv', 'uses' => 'AdminController@parchve_repo_move'));
Route::get('/prepo/sites/spool', array('before'=>'argv', 'as' => 'admin.sites_spool', 'uses' => 'AdminController@show'));

Route::get('/prepo/artifacts', array('before'=>'argv', 'as' => 'admin.artifacts', 'uses' => 'ArchiveController@show'));
Route::get('/prepo/export_item', array('before'=>'argv', 'uses' => 'AdminController@export_item'));

Route::get('/prepo/contents', array('before'=>'argv', 'as' => 'admin.contents', 'uses' => 'AdminController@show'));
Route::post('/prepo/contents', array('before'=>'argv', 'uses' => 'AdminController@add_content'));
Route::get('/prepo/edit_content', array('before'=>'argv', 'as' => 'admin.edit_content', 'uses' => 'AdminController@show'));
Route::post('/prepo/edit_content', array('before'=>'argv', 'as' => 'admin.edit_content', 'uses' => 'AdminController@show'));

Route::get('prepo/delete_item', array('before'=>'argv','uses' => 'AdminController@delete_item'));
Route::get('prepo/change_ob_type', array('before'=>'argv','uses' => 'AdminController@parchive_change_obj_type'));
Route::post('prepo/change_ob_type', array('before'=>'argv','uses' => 'AdminController@parchive_change_obj_type'));
Route::get('prepo/change_site', array('before'=>'argv','uses' => 'AdminController@parchive_change_site'));
Route::post('prepo/change_site', array('before'=>'argv','uses' => 'AdminController@parchive_change_site'));
Route::get('prepo/bibref_togle', array('before'=>'argv','uses' => 'AdminController@parchive_bibref_togle'));

Route::get('/prepo/edit_bitstream_symlink', array('before'=>'argv', 'as' => 'admin.edit_bitstream_symlink', 'uses' => 'AdminController@show'));
Route::post('/prepo/edit_bitstream_symlink', array('before'=>'argv', 'as' => 'admin.edit_bitstream_symlink', 'uses' => 'AdminController@show'));

Route::get('/prepo/subjects/subject', array('before'=>'argv', 'as' => 'admin.subject_adm', 'uses' => 'AdminController@show'));
Route::post('/prepo/subjects/subject', array('before'=>'argv', 'as' => 'admin.subject_adm', 'uses' => 'AdminController@show'));

Route::get('/prepo/subjects/relation', array('before'=>'argv', 'as' => 'admin.subject_relation', 'uses' => 'AdminController@show'));
Route::post('/prepo/subjects/relation', array('before'=>'argv', 'as' => 'admin.subject_relation', 'uses' => 'AdminController@show'));

Route::get('/prepo/submits', array('before'=>'argv', 'as' => 'admin.submits', 'uses' => 'AdminController@show'));
Route::get('/prepo/delete_submit', array('before'=>'argv','uses' => 'AdminController@parchive_delete_submit'));
Route::get('/prepo/merge_subjects', array('before'=>'argv', 'as' => 'admin.merge_subject','uses' => 'AdminController@show'));
Route::post('/prepo/merge_subjects', array('before'=>'argv', 'as' => 'admin.merge_subject','uses' => 'AdminController@parchive_merge_subjects'));
Route::get('/prepo/serials_np', array('before'=>'argv', 'as' => 'admin.serials_np','uses' => 'AdminController@show'));
Route::get('/prepo/metadata_stats', array('before'=>'argv', 'as' => 'admin.metadata_stats','uses' => 'AdminController@show'));
Route::post('/prepo/metadata_stats', array('before'=>'argv', 'as' => 'admin.metadata_stats','uses' => 'AdminController@show'));
Route::get('/prepo/metadata_search', array('before'=>'argv', 'as' => 'admin.metadata_search','uses' => 'AdminController@show'));
Route::get('/prepo/subject_stats', array('before'=>'argv', 'as' => 'admin.subject_stats','uses' => 'AdminController@show'));

Route::get('/prepo/artifacts_list', array('before'=>'argv', 'as' => 'admin.artifacts_list','uses' => 'AdminController@show'));
Route::get('/prepo/artifacts_stats', array('before'=>'argv', 'as' => 'admin.artifacts_stats','uses' => 'AdminController@show'));
Route::get('/prepo/elements_item_ref', array('before'=>'argv', 'as' => 'admin.elements_item_ref','uses' => 'AdminController@show'));
Route::post('/prepo/elements_item_ref', array('before'=>'argv', 'as' => 'admin.elements_item_ref','uses' => 'AdminController@show'));
Route::get('/prepo/menu_advance', array('before'=>'argv', 'as' => 'admin.repoadv','uses' => 'AdminController@show'));
//Route::get('/prepo/isis', array('before'=>'argv', 'as' => 'admin.isis_admin','uses' => 'AdminController@show'));

Route::get('/prepo/ws/search-folder', array('uses' => 'AdminController@ws_search_folder'));
Route::get('/prepo/ws/search-work', array('uses' => 'AdminController@ws_search_work'));
Route::get('/prepo/ws/search-expression', array('uses' => 'AdminController@ws_search_expression'));
Route::get('/prepo/ws/search-work-all', array('uses' => 'AdminController@ws_search_work_all'));
Route::get('/prepo/ws/search-manifestation', array('uses' => 'AdminController@ws_search_manifestation'));
Route::get('/prepo/ws/search-item', array('uses' => 'AdminController@ws_search_item'));
Route::get('/prepo/ws/search-place', array('uses' => 'AdminController@ws_search_place'));
Route::get('/prepo/ws/search-person', array('uses' => 'AdminController@ws_search_person'));
Route::get('/prepo/ws/search-family', array('uses' => 'AdminController@ws_search_family'));
Route::get('/prepo/ws/search-organization', array('uses' => 'AdminController@ws_search_organization'));

Route::get('/prepo/ws/search-digital-item', array('uses' => 'AdminController@ws_search_digital_item'));

Route::get('/prepo/ws/search-lemma-category', array('uses' => 'AdminController@ws_search_lemma_category'));
Route::get('/prepo/ws/search-web-site-instance', array('uses' => 'AdminController@ws_search_web_site_instance'));
Route::get('/prepo/ws/search-periodic-publication', array('uses' => 'AdminController@ws_search_periodic_publication'));
Route::get('/prepo/ws/search-media', array('uses' => 'AdminController@ws_search_media'));
Route::get('/prepo/ws/search-lemma-manif-citations', array('uses' => 'AdminController@ws_search_lemma_manif_citations'));


Route::get('/prepo/ws/search-subject', array('uses' => 'AdminController@ws_search_subject'));
Route::get('/prepo/ws/search-subject-all', array('uses' => 'AdminController@ws_search_subject_all'));
Route::get('/prepo/ws/search-subject-chain', array('uses' => 'AdminController@ws_search_subject_chain'));
Route::get('/prepo/ws/search-subject-limited', array('uses' => 'AdminController@ws_search_subject_limited'));
Route::get('/prepo/ws/search-subject-concept', array('uses' => 'AdminController@ws_search_subject_concept'));
Route::get('/prepo/ws/search-subject-object', array('uses' => 'AdminController@ws_search_subject_object'));
Route::get('/prepo/ws/search-subject-event', array('uses' => 'AdminController@ws_search_subject_event'));
Route::get('/prepo/ws/search-subject-form', array('uses' => 'AdminController@ws_search_subject_form'));
Route::get('/prepo/ws/search-subject-general', array('uses' => 'AdminController@ws_search_subject_general'));

Route::get('/prepo/ws/search-type-event', array('uses' => 'AdminController@ws_search_type_event'));

Route::post('/ws/prepo/create_item', array('uses' => 'EditItemController@create_item'));
Route::post(   '/prepo/create_item', array('uses' => 'EditItemController@create_item'));
Route::post('/ws/prepo/create_subitem', array('uses' => 'EditItemController@create_subitem'));
Route::post(   '/prepo/create_subitem', array('uses' => 'EditItemController@create_subitem'));

// Route::post('/ws/prepo/save_submit', array('uses' => 'EditItemController@save_submit'));
// Route::post(   '/prepo/save_submit', array('uses' => 'EditItemController@save_submit'));

Route::post('/prepo/save_submit', array('uses' => 'EditItemController@save_submit'));

Route::get('/prepo/graph', array('uses' => 'GraphController@test'));
Route::get('/prepo/graphviz', array('uses' => 'GraphController@graphviz'));
Route::get('/prepo/graph-dump', array('uses' => 'GraphController@dump'));
Route::get('/prepo/graphReset', array('uses' => 'GraphController@graphReset'));
Route::get('/prepo/reset-graph', array('uses' => 'GraphController@graphResetGUI'));
Route::post('/prepo/reset-graph', array('uses' => 'GraphController@graphResetGUI'));

Route::get('/prepo/reset-lock', array('before'=>'argv', 'as' => 'admin.lock_transaction_reset', 'uses' => 'AdminController@lockTransactionReset'));
Route::post('/prepo/reset-lock', array('before'=>'argv', 'as' => 'admin.lock_transaction_reset', 'uses' => 'AdminController@lockTransactionReset'));

Route::get('/prepo/z3950', array('uses' => 'Z3950Controller@test'));
Route::get('/ws/prepo/z3950', array('uses' => 'Z3950Controller@ws'));
Route::post('/prepo/z3950/copycatalog', array('uses' => 'Z3950Controller@copycatalog'));

Route::get('/prepo/marc-export', array('uses' => 'MarcController@export'));
Route::post('/prepo/marc-export', array('uses' => 'MarcController@export'));

Route::get('/prepo/marc-import', array('uses' => 'MarcController@import'));
Route::post('/prepo/marc-import', array('uses' => 'MarcController@import'));
Route::get('/prepo/authorities-import', array('uses' => 'MarcController@importAuthorities'));

Route::get('/prepo/marc-download', array('uses' => 'MarcController@download_marc'));
Route::post('/prepo/marc-download', array('uses' => 'MarcController@download_marc'));

Route::get('/archive/epub-viewer', array('uses' => 'ArchiveController@epub_viewer'));

Route::post('/ws/prepo/sync_users', array('uses' => 'SyncUsersController@syncusers'));

// SOLR RELATED
Route::get('/prepo/solr_search', array('before'=>'argv', 'as' => 'admin.solr_search', 'uses' => 'AdminController@show'));
Route::get('/prepo/solr_suggest', array('before'=>'argv', 'as' => 'admin.solr_suggest', 'uses' => 'ArchiveController@solr_suggest'));


Route::get('/prepo/solr_search_staff', array('before'=>'argv', 'as' => 'admin.solr_search_staff', 'uses' => 'AdminController@show'));
Route::get('/prepo/solr_suggest_staff', array('before'=>'argv', 'as' => 'admin.solr_suggest_staff', 'uses' => 'ArchiveController@solr_suggest_staff'));

// Route::get('/prepo/search_solr', array('before'=>'argv', 'as' => 'public.search_solr', 'uses' => 'AdminController@show'));
Route::get('/prepo/search_solr', array('before'=>'argv', 'as' => 'public.search_solr', 'uses' => 'SearchController@solr_search'));


if (Config::get('arc.SOLR_SEARCH_AS_DEFAULT',0)>0) {
	Route::get('/archive/search', array('before' => 'argv', 'as' => 'public.search_solr', 'uses' => 'SearchController@solr_search'));
	Route::get('/archive/search_s', array('uses' => 'SearchController@search'));
} else {
	Route::get('/archive/search_s', array('before'=>'argv', 'as' => 'public.search_solr', 'uses' => 'SearchController@solr_search'));
	Route::get('/archive/search', array('uses' => 'SearchController@search'));
}

Route::get('/prepo/lock_test', array('before'=>'argv', 'as' => 'admin.lock_test', 'uses' => 'AdminController@show'));


Route::get('/prepo/report1', array('uses' => 'ReportControler@report1'));

Route::get('/prepo/action1', array('before'=>'argv', 'as' => 'actions.action1', 'uses' => 'ActionControler@action1'));
Route::get('/prepo/action2', array('before'=>'argv', 'as' => 'actions.action2', 'uses' => 'ActionControler@action2'));
Route::post('/prepo/action2', array('before'=>'argv', 'as' => 'actions.action2', 'uses' => 'ActionControler@action2'));







