<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Laravel PHP Framework</title>
	
<style type="text/css">
	@import url(//fonts.googleapis.com/css?family=Lato:700);
	
body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center;
			color: #999;
}
 
div {
  width: 700px;
	margin-left: auto ;
  margin-right: auto ;
  margin-top:1%;
  margin-bottom:2%;
}
 
h2 {
  font: 400 30px/1.5 Helvetica, Verdana, sans-serif;
  margin: 0;
  padding: 0;
}
 
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  text-align:left;
}
 
li {
  font: 200 15px/1.5 Helvetica, Verdana, sans-serif;
  border-bottom: 1px solid #ccc;
}
 
li:last-child {
  border: none;
}
 
li a {
  text-decoration: none;
  color: #FF726E;
  font-weight:bold;
  display: block;
  width: 700px;
 
  -webkit-transition: font-size 0.3s ease, background-color 0.3s ease;
  -moz-transition: font-size 0.3s ease, background-color 0.3s ease;
  -o-transition: font-size 0.3s ease, background-color 0.3s ease;
  -ms-transition: font-size 0.3s ease, background-color 0.3s ease;
  transition: font-size 0.3s ease, background-color 0.3s ease;
}
 
li a:hover {
  font-size: 18px;
  background: #f6f6f6;
}
	
	</style>
	
	
</head>
<body>

<div>
  <h2>Routing</h2>
 <br>
 Public
  <ul>
    <li><a href="/archive/tagcloud" target="_blank">/archive/tagcloud</a></li> 
    <li><a href="/archive/search" target="_blank">/archive/search</a></li>
    <li><a href="/archive/search-all?term=art" target="_blank">/archive/search-all?term=art (autocomplete)</a></li>
    <li><a href="/archive/search-title?term=te" target="_blank">/archive/search-title?term=te (autocomplete)</a></li>
    <li><a href="/archive/search-terms?term=te" target="_blank">/archive/search-terms?term=te (autocomplete)</a></li>
    <li><a href="/archive/search-author?term=te" target="_blank">/archive/search-author?term=te (autocomplete)</a></li>
    <li><a href="/archive/search-place?term=a" target="_blank">/archive/search-place?term=a (autocomplete)</a></li>
    <li><a href="/archive/term?t=Brixton" target="_blank">/archive/term/</a></li>
    <li><a href="/archive/term/Brixton" target="_blank">/archive/term/{term?}</a></li>
    <li><a href="/archive/item?i=12509" target="_blank">/archive/item/</a></li>
    <li><a href="/archive/item/12509" target="_blank">/archive/item/{pitem}</a></li>
    <li><a href="/archive/item/12320/11131.png" target="_blank">/archive/item/{pitem}/{partifact}</a></li>
    <li><a href="/archive/item/12320/download/11131.png" target="_blank">/archive/item/{pitem}/download/{partifact}</a></li>
    <li><a href="/archive/item/12320/direct/11131.png" target="_blank">/archive/item/{pitem}/direct/{partifact}</a></li>   
    <li><a href="/archive/media/11131/big" target="_blank">/archive/media/{partifact}/{mtype}</a></li>    
    <li><a href="/archive/items/12523" target="_blank">/archive/items/{pitem?}</a></li>
    <li><a href="/archive/items/12523/12525" target="_blank">/archive/items/{pparent}/{pitem?} image & pdf</a></li>  
    <li><a href="/prepo/update_folder_thumbs" target="_blank">/prepo/update_folder_thumbs (get)</a></li>
    <li><a href="#">/prepo/update_folder_thumbs (post)</a></li>
  </ul>
   <br>
 Admin  
  <ul>
    <li><a href="/prepo/menu" target="_blank">/prepo/menu</a></li>
    <li><a href="/prepo/new_item" target="_blank">/prepo/new_item</a></li>
    <li><a href="#">/prepo/new_item (post)</a></li>
    <li><a href="/archive/recent" target="_blank">/archive/recent</a></li>
    <li><a href="/prepo/node_stats" target="_blank"> /prepo/node_stats</a></li>
    <li><a href="/prepo/edit_step1" target="_blank">/prepo/edit_step1</a></li>
    <li><a href="/prepo/edit_step2?i=12523" target="_blank">/prepo/edit_step2</a></li>
    <li><a href="/prepo/edit_step3" target="_blank">/prepo/edit_step3 (get)</a></li>
    <li><a href="#">/prepo/edit_step3 (post)</a></li>
    <li><a href="/prepo/update_folder_thumbs" target="_blank">/prepo/update_folder_thumbs</a></li>
    <li><a href="/prepo/thumbs?i=12511" target="_blank">/prepo/thumbs</a></li>
    <li><a href="#">/prepo/update_folder_thumbs (post)</a></li>
    <li><a href="/prepo/edit_bitstream?bid=11332" target="_blank">/prepo/edit_bitstream (get)</a></li>
    <li><a href="#">/prepo/edit_bitstream (post)</a></li>
    <li><a href="/prepo/bitstreams?i=12523" target="_blank">/prepo/bitstreams (get)</a></li>
    <li><a href="#">/prepo/bitstreams (post)</a></li>
    <li><a href="/archive/download?i=12523&d=01bd4c7c-68ca-11e4-a0b6-0bcde9b3a64d" >/archive/download</a></li>
    <li><a href="/prepo/delete_thumb?i=12523&ttype=1&tid=76158" >/prepo/delete_thumb</a></li>
    <li><a href="/prepo/move_bitstream?aid=11231" target="_blank">/prepo/move_bitstream (get)</a></li>
    <li><a href="#">/prepo/move_bitstream (post)</a></li>
    <li><a href="/prepo/items/relation?v=1&t=12525&t1=12525" target="_blank">/prepo/items/relation (get)</a></li>
    <li><a href="#">/prepo/items/relation (post)</a></li>
    <li><a href="/prepo/artifacts?i=12525" target="_blank">/prepo/artifacts</a></li>
    <li><a href="/archive/search_item_by_title?term=bit" target="_blank">/archive/search_item_by_title</a></li>
    <li><a href="/archive/search_folder_by_title?term=bit" target="_blank">/archive/search_folder_by_title</a></li>
    <li><a href="/archive/search_actor_by_title?term=ath" target="_blank">/archive/search_actor_by_title</a></li>
    <li><a href="/archive/search-metadata-element?term=dc" target="_blank">/archive/search-metadata-element</a></li>
    <li><a href="/archive/search-isbn?term=960-260-887-" target="_blank">/archive/search-isbn</a></li>
    <li><a href="/archive/search-subtitle?term=ath" target="_blank">/archive/search-subtitle</a></li> 
    <li><a href="/archive/find-contributor?term=at&key=dc%3Acontributor%3Aauthor" target="_blank">/archive/find-contributor</a></li>
    <li><a href="/archive/find-relation?term=koi&key=ea%3Aitem-of%3A" target="_blank">/archive/find-relation</a></li>
    <li><a href="/archive/find-work?term=test&key=ea%3Awork%3A" target="_blank">/archive/find-work</a></li>
    <li><a href="/archive/find-place?term=ag&key=ea%3Apublication%3Aplace" target="_blank">/archive/find-place</a></li>
    <li><a href="/archive/search-bookbinding-type?term=ygug" target="_blank">/archive/search-bookbinding-type</a></li>
    <li><a href="/archive/search-material-type?term=huh" target="_blank">/archive/search-material-type</a></li>
    <li><a href="/archive/search-country?term=set" target="_blank">/archive/search-country</a></li>
    <li><a href="/prepo/check-value-exists?term=An+anthology+of+Atheism&e=marc%3Atitle-statement%3Aremainder" target="_blank">/prepo/check-value-exists</a></li>
    <li><a href="/prepo/ws/item_metadata?i=12354" target="_blank">/prepo/ws/item_metadata</a></li> 
    <li><a href="/prepo/spool" target="_blank">/prepo/spool (get)</a></li> 
    <li><a href="#" target="_blank">/prepo/spool (post)</a></li> 
    <li><a href="/prepo/move" target="_blank">/prepo/move</a></li> 
    <li><a href="/prepo/sites/spool" target="_blank">/prepo/sites/spool</a></li> 
    <li><a href="/prepo/export_item?i=12523" target="_blank">/prepo/export_item</a></li>    
    <li><a href="/prepo/contents?i=12523" target="_blank">/prepo/contents(get)</a></li>
    <li><a href="#">/prepo/contents (post)</a></li>
    <li><a href="/prepo/edit_content?cid=14" target="_blank">/prepo/edit_content(get)</a></li>
    <li><a href="#">/prepo/edit_content (post)</a></li>  
    <li><a href="/prepo/delete_item?i=12465" target="_blank">/prepo/delete_item</a></li> 
    <li><a href="/prepo/change_ob_type?i=12523" target="_blank">/prepo/change_ob_type (get)</a></li> 
    <li><a href="#">/prepo/change_ob_type (post)</a></li> 
    <li><a href="/prepo/change_site?i=12523" target="_blank">/prepo/change_site (get)</a></li> 
    <li><a href="#">/prepo/change_site (post)</a></li> 
    <li><a href="/prepo/bibref_togle?i=12523" target="_blank">/prepo/bibref_togle</a></li> 
    <li><a href="/prepo/subjects/subject?s=Anne+Hansen" target="_blank">/prepo/subjects/subject</a></li> 
    <li><a href="#">/prepo/subjects/subject (post)</a></li> 
  	<li><a href="/prepo/edit_bitstream_symlink?sid=11433" target="_blank">/prepo/edit_bitstream_symlink</a></li> 
    <li><a href="#">/prepo/edit_bitstream_symlink (post)</a></li> 
 		<li><a href="/prepo/subjects/relation?dt=286&t=Anne+Hansen" target="_blank">/prepo/subjects/relation</a></li> 
    <li><a href="#">/prepo/subjects/relation (post)</a></li> 
    <li><a href="/prepo/submits" target="_blank">/prepo/submits</a></li> 
    <li><a href="#">/prepo/delete_submit</a></li> 
    <li><a href="/prepo/merge_subjects" target="_blank">/prepo/merge_subjects</a></li> 
    <li><a href="#">/prepo/merge_subjects (post)</a></li> 
    <li><a href="/prepo/serials_np" target="_blank">/prepo/serials_np</a></li> 
    <li><a href="/prepo/metadata_stats" target="_blank">/prepo/metadata_stats</a></li> 
    <li><a href="#">/prepo/metadata_stats (post)</a></li> 
    <li><a href="/prepo/metadata_search" target="_blank">/prepo/metadata_search</a></li> 
    <li><a href="/prepo/subject_stats" target="_blank">/prepo/subject_stats</a></li> 
    <li><a href="/prepo/artifacts_list" target="_blank">/prepo/artifacts_list</a></li> 
    <li><a href="/prepo/artifacts_stats" target="_blank">/prepo/artifacts_stats</a></li> 
    <li><a href="/prepo/elements_item_ref" target="_blank">/prepo/elements_item_ref</a></li> 
    <li><a href="#">/prepo/elements_item_ref (post)</a></li> 
    <li><a href="/prepo/menu_advance" target="_blank">/prepo/menu_advance</a></li>   
  </ul>
  <br><br><br>
  <a href="http://laravel.com" title="Laravel PHP Framework" target="_blank"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIcAAACHCAYAAAA850oKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyNUVCMTdGOUJBNkExMUUyOTY3MkMyQjZGOTYyREVGMiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyNUVCMTdGQUJBNkExMUUyOTY3MkMyQjZGOTYyREVGMiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjI1RUIxN0Y3QkE2QTExRTI5NjcyQzJCNkY5NjJERUYyIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjI1RUIxN0Y4QkE2QTExRTI5NjcyQzJCNkY5NjJERUYyIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+g6J7EAAAEL1JREFUeNrsXQmU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" alt="Laravel PHP Framework"></a>		
</div>

</body>
</html>
