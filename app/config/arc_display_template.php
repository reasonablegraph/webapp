<?php

return array(
'empty' => 'Empty Field Template',

'person_title' => '
			 {{{title}}}{{#person_numeration}},
								{{{person_numeration}}}{{/person_numeration}}{{#titles_associated}},
												{{#list}}{{#delimiter}}, {{/delimiter}}{{{value}}}{{/list}}{{/titles_associated}}
												{{#person_dates_associated}}({{{person_dates_associated}}}){{/person_dates_associated}}
						{{#person_fuller_name}}
							({{{person_fuller_name}}})
						{{/person_fuller_name}}',

'organization_title' => '
{{{title}}}{{#subdivision}}. {{#list}}{{#delimiter}} - {{/delimiter}}{{{value}}}{{/list}}{{/subdivision}}{{#addition}}. {{#list}}{{#delimiter}},{{/delimiter}}{{{value}}}{{/list}}{{/addition}}
{{#number}}({{/number}}{{^number}}{{#date}}({{/date}}{{/number}}{{^number}}{{^date}}{{#location}}({{/location}}{{/date}}{{/number}}{{#number}}{{{number}}}{{#date}} : {{/date}}{{/number}}{{#date}}{{{date}}}{{/date}}{{#location}}{{#number}}{{^date}} : {{/date}}{{/number}}{{#date}} : {{/date}}{{{location}}}{{/location}}{{#number}}){{/number}}{{^number}}{{#date}}){{/date}}{{/number}}{{^number}}{{^date}}{{#location}}){{/location}}{{/date}}{{/number}}{{#type}} ({{type}}){{/type}}
',

'basic_title_opac_detail' => '<h1 class="{{class}}">{{{title}}}</h1>',

'person_title_opac_detail' => '<h1 class="{{class}}">{{{title}}}</h1>',

'person_title_opac_detail_rdf' => '<div class="{{class_item}}"><h1 class="{{class}}">{{{title}}}</h1></div>{{#rdf_link}}<div class="{{class_rda_link}}"><a href="{{rdf_link}}" target="_blank"><img src="{{src_rda_icon}}"></a></div>{{/rdf_link}}',

'family_title' => '{{{title}}} {{#type}}({{{type}}}){{/type}}{{#titles_place}}. {{#list}}{{#delimiter}}, {{/delimiter}}{{{value}}}{{/list}}{{/titles_place}}{{#dates_associated}} ({{{dates_associated}}}) {{/dates_associated}}',

'work_title' => '{{#element_title}}{{element_title}} [{{contributor_label}}].{{/element_title}} {{{title}}}{{#part_number}}. {{{part_number}}}{{/part_number}}{{#title_part_name}}, {{{title_part_name}}}{{/title_part_name}} {{#form}}[{{{form}}}]{{/form}}{{#date}} ({{{date}}}).{{/date}}{{#place}} {{{place}}}.{{/place}}{{#language}} {{{language}}}.{{/language}} {{{version}}}',

'work_title_opac' => '{{#element_dc_title}}{{element_dc_title}}{{#full_stop}}.{{/full_stop}}{{/element_dc_title}} {{title}}{{#part_number}}. {{part_number}}{{/part_number}}{{#title_part_name}}, {{title_part_name}}{{/title_part_name}} {{#form}}[{{form}}]{{/form}}{{#date}} ({{date}}).{{/date}}{{#place}} {{place}}.{{/place}}{{#language}} {{language}}.{{/language}} {{version}}',

'periodic_title' => '{{{title}}}{{#title_remainder}} : {{{title_remainder}}}{{/title_remainder}}',

'digital_item_catalogue_title' => '{{type}} {{#part_number}}({{part_number}}{{#page}},{{page}}{{/page}}) {{/part_number}} {{^part_number}}{{#page}}({{page}}) {{/page}}{{/part_number}}',

'digital_item_connected_label' =>  tr('Digital item of manifestation').' «{{{manif_title}}}»',

'digital_item_label' => tr('digital-item').': {{id}} {{#type}} [{{{type}}}]{{/type}} {{#part}} ({{{part}}}{{#page}}, {{{page}}}{{/page}}){{/part}} {{^part}}{{#page}} ({{page}}){{/page}}{{/part}}',

'digital_item_title' => '{{label}} [{{element_title}}]',

'physical_item_catalogue_title' => '{{type}} {{#barcode}}({{barcode}}{{#part_number}} - {{part_number}}{{/part_number}}{{#copyNumber}} - {{copyNumber}}{{/copyNumber}}){{/barcode}}{{^barcode}}{{#part_number}}({{part_number}}{{#copyNumber}} - {{copyNumber}}{{/copyNumber}}){{/part_number}}{{/barcode}}{{^barcode}}{{^part_number}}{{#copyNumber}}({{copyNumber}}){{/copyNumber}}{{/part_number}}{{/barcode}}',

'physical_item_label' => tr('physical-item').': {{id}} {{#type}} [{{type}}]{{/type}} {{#barcode}}({{barcode}}{{#part}} - {{part}}{{/part}}{{#copyNumber}} - {{copyNumber}}{{/copyNumber}}){{/barcode}}{{^barcode}}{{#part}}({{part}}{{#copyNumber}} - {{copyNumber}}{{/copyNumber}}){{/part}}{{/barcode}}{{^barcode}}{{^part}}{{#copyNumber}}({{copyNumber}}){{/copyNumber}}{{/part}}{{/barcode}}',

'physical_item_title' => '{{label}} [{{{element_title}}}]',

'place_label' => '{{{title}}}{{#type}} ({{{type}}}){{/type}}',

'lemma_category_label' => '{{{parent_label}}} > {{{dc_title}}}',

'subcategory_label' => '{{{parent_label}}} > {{{dc_title}}}',

'manifestation_citation'=> '{{#authors}}{{#delimiter}}, {{/delimiter}}{{{name}}}{{/authors}}{{#publication_date}}({{{publication_date}}}){{/publication_date}}{{#authors}}{{^delimiter}}.{{/delimiter}}{{/authors}}{{^authors}}{{#publication_date}}.{{/publication_date}}{{/authors}} {{title}}{{#title_remainder}}: {{{title_remainder}}}{{/title_remainder}}. {{#publication_place}}{{{publication_place}}}{{#publication_name}}:{{/publication_name}}{{/publication_place}} {{publication_name}}',

'web_site_instance_citation'=> '{{#authors}}{{authors}}{{/authors}} {{#publication_date}}({{publication_date}}){{/publication_date}}{{#authors}}.{{/authors}}{{^authors}}{{#publication_date}}.{{/publication_date}}{{/authors}} {{title}}{{#title_remainder}}: {{title_remainder}}{{/title_remainder}}.{{#website_url}} Retrieved from {{website_url}}{{/website_url}}',

'periodic_publication_citation'=> '{{#publisher_name}}{{publisher_name}}{{/publisher_name}} {{#publication_date}}({{publication_date}}){{/publication_date}}{{#publisher_name}}.{{/publisher_name}}{{^publisher_name}}{{#publication_date}}.{{/publication_date}}{{/publisher_name}} {{title}}{{#title_remainder}}: {{title_remainder}}{{/title_remainder}}({{#issue}}{{issue}}{{/issue}})',

'media_citation'=> '{{title}}{{#title_remainder}}: {{{title_remainder}}}{{/title_remainder}}',

'manifestation_title_details' => '{{{title}}}{{#issue}} #{{{issue}}}{{#issue_publication_month}}, {{#issue_publication_day}}{{issue_publication_day}}{{/issue_publication_day}} {{issue_publication_month}}{{/issue_publication_month}}{{#issue_publication_year}} ({{issue_publication_year}}){{/issue_publication_year}}{{/issue}}
{{^issue}} {{#issue_publication_day}}# {{issue_publication_day}} {{/issue_publication_day}}{{#issue_publication_month}}{{^issue_publication_day}}# {{/issue_publication_day}}{{issue_publication_month}}{{/issue_publication_month}}{{#issue_publication_year}} ({{issue_publication_year}}){{/issue_publication_year}}{{/issue}}
{{#title_remainder}} : {{{title_remainder}}} {{/title_remainder}}
{{#title_medium}}[{{{title_medium}}}] {{/title_medium}}
{{{title_partNumber}}}{{#title_partName}}, {{/title_partName}}{{{title_partName}}}
{{#title_responsibility}} / {{{title_responsibility}}}. {{/title_responsibility}}
{{#edition}} - {{{edition}}}.{{/edition}}
{{#publication}} - {{{publication}}}.{{/publication}}
{{#distribution}} - {{{distribution}}}.{{/distribution}}
{{#production}} - {{{production}}}.{{/production}}
{{#manufactur}} - {{{manufactur}}}.{{/manufactur}}',

'manifestation_title' => '{{{title}}}
{{#title_remainder}} : {{{title_remainder}}} {{/title_remainder}}
{{#title_medium}}[{{{title_medium}}}] {{/title_medium}}
{{#list_part}}
{{#list}}{{#delimiter}} - {{/delimiter}}{{{value}}}{{/list}}{{#list_number}},{{/list_number}}
{{/list_part}}
{{#list_number}}
{{#list}}{{#delimiter}} - {{/delimiter}}{{{value}}}{{/list}}
{{/list_number}}
{{#title_responsibility}} / {{{title_responsibility}}}. {{/title_responsibility}}',


'manifestation_publication' =>'
		{{#publication_place}}
		{{#place}}{{#delimiter}};{{/delimiter}}{{value}}{{/place}}{{#publication_name}}: {{/publication_name}}
		{{/publication_place}}
		{{#publication_name}}{{^publication_place}}: {{/publication_place}}
		{{#name}}{{#delimiter}}-{{/delimiter}}{{value}}{{/name}}{{#publication_date}},{{/publication_date}}
		{{/publication_name}}
		{{publication_date}}',




// 'manifestation_contained' => '
// <div aria-hidden="true">
// 	<span class="res_relation">
// 		{{list_title}}:
// 		<br>{{#works_array}}<a href="/archive/item/{{id}}">{{label}}</a>{{#delimiter}} | {{/delimiter}}{{/works_array}}
// 	</span>
// </div>',

'work_contained' => '
<div aria-hidden="true">
	<span class="res_relation">{{list_title}}:
		<br>{{#works_array}}<a href="/archive/item/{{id}}">{{label}}</a>{{#delimiter}} | {{/delimiter}}{{/works_array}}
	</span>
</div>',

'object_type' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#object_type}}{{object_type}}{{/object_type}}
						</div>',


'simple' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#text_value}}{{text_value}}{{/text_value}}
						</div>',

'one_line_relation' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#line_list}}
												{{#link_list}}{{#url}}<a href="{{url}}" >{{title}}</a>{{/url}}{{^url}}{{title}}{{/url}}{{^del_stop}} | {{/del_stop}}{{/link_list}}
									{{/line_list}}
						</div>',

'one_list_relation' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#line_list}}
										<ul class="nested-list">
											{{#link_list}}<li>{{#url}}<a href="{{url}}" >{{title}}</a>{{/url}}{{^url}}{{title}}{{/url}}</li>{{/link_list}}
										</ul>
									{{/line_list}}
						</div>',

'one_label_line' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#line_list}}
												{{#list}}{{#delimiter}} - {{/delimiter}}{{value}}{{/list}}
												{{#link_list}}{{#delimiter}} - {{/delimiter}}{{#url}}<a class="{{class}}" href="{{url}}" >{{/url}}{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}{{#url}}</a>{{/url}}{{/link_list}}
									{{/line_list}}
						</div>',

'one_label_line_dash' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#line_list}}
												{{#list}}{{#delimiter}} | {{/delimiter}}{{value}}{{/list}}
												{{#link_list}}{{#delimiter}} | {{/delimiter}}{{#url}}<a class="{{class}}" href="{{url}}" >{{/url}}{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}{{#url}}</a>{{/url}}{{/link_list}}
									{{/line_list}}
						</div>',

'one_label_line_isbn' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#line_list}}
												{{#list}}{{#delimiter}}, {{/delimiter}}{{value}}{{/list}}
												{{#link_list}}{{#delimiter}}, {{/delimiter}}{{#url}}<a class="{{class}}" href="{{url}}" >{{/url}}{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}{{#url}}</a>{{/url}}{{/link_list}}
									{{/line_list}}
						</div>',

//BIBFRAME
'agent_associated' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
								{{#container}}
									{{#inner}}{{#del}}{{del}}{{/del}}
											{{#list}}{{#delimiter}}({{value}}){{/delimiter}}{{/list}}
											{{#link_list}}{{#delimiter}} ({{/delimiter}}{{#url}}<a class="{{class}}" href="{{url}}" >{{/url}}{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}{{#url}}</a>{{/url}}{{#delimiter}}){{/delimiter}}{{/link_list}}{{/inner}}
								{{/container}}
							</div>',

'one_label_list' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#line_list}}
										 <ul class="nested-list">
											{{#list}}<li>{{value}}</li>{{/list}}
											{{#link_list}}<li><a class="{{class}}" href="{{url}}" >{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}</a></li>{{/link_list}}
										</ul>
									{{/line_list}}
						</div>',

'one_label_line_multi' => '
		{{#ex_array}}
						{{#ex_list}}
							{{#label}}
									<div class="row {{class}}">
											<label class="{{grid_col_class_label}}" style="text-align:left;">{{label}}</label>
											<div class="{{grid_col_class}}">
												{{#link_list}}{{#delimiter}},{{/delimiter}} {{value}}{{/link_list}}
											</div>
									</div>
							{{/label}}
						{{/ex_list}}
		{{/ex_array}}',

'oneline' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
								{{#array_val}}
									{{#value}}{{#url}}<a class="{{class}}" href="{{url}}" >{{/url}}{{value}}{{#url}}</a>{{/url}}{{/value}}
								{{/array_val}}
							</div>',


'oneline_item_with_notes' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#line_list}}
												{{#link_list}}{{#delimiter}} - {{/delimiter}}{{#url}}<a class="{{class}}" href="{{url}}" >{{/url}}{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}{{#url}}</a>{{/url}} {{notes}}{{/link_list}}
									{{/line_list}}
							</div>',

'notes' => '{{#dateStart}}({{dateStart}}{{#dateEnd}},{{/dateEnd}} {{dateEnd}}){{/dateStart}}
						{{^dateStart}}{{#dateEnd}}({{dateEnd}}){{/dateEnd}}{{/dateStart}}
						{{#note}}[{{note}}]{{/note}}',

'one_label_combination' => '
	{{{label}}}
	<div class="{{grid_col_class}}">
		{{#all_array}}
						 <ul class="delimiter">
							{{#pos}}
										<li>
												{{#list_text}} {{#url}}<a href="{{url}}" >{{/url}}{{{value}}}{{#url}}</a>{{/url}}{{/list_text}}
												{{#list_url}}{{^delimiter}}⟶ {{/delimiter}}{{/list_url}}{{#list_url}}{{#delimiter}}, {{/delimiter}}<a class="{{class}}" href="{{url}}" >{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}</a>{{/list_url}}
										</li>
							{{/pos}}
						</ul>
		{{/all_array}}
	</div>',

'citations_line' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
									{{#citation_list}}
												{{#value}}{{#delimiter}} - {{/delimiter}}{{{.}}}{{/value}}
									{{/citation_list}}
						</div>',

'citations_list' => '
							{{{label}}}
							<div class="{{grid_col_class}}">
								<ul class="nested-list">
									{{#citation_list}}
										<li>	{{#value}}{{{.}}}{{/value}}</li>
									{{/citation_list}}
								</ul>
							</div>',

		// CITATIONS RULE-ENGINE
		// 'citations' => '{{{label}}}
		// 									<div class="{{grid_col_class}}">
		// 										{{^is_manif_citation}}
		// 												<ul class="delimiter">
		// 														{{#citations}}<li>{{.}}</li>{{/citations}}
		// 												</ul>
		// 										{{/is_manif_citation}}
		// 										{{#is_manif_citation}}
		// 														{{citations}}
		// 										{{/is_manif_citation}}
		// 									</div>',


'solr_link' => '<div class="solr_link label label-success"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> <a href="{{url}}" >{{title}} "{{label}}" {{#mnumber}}({{mnumber}}){{/mnumber}}</a></div>',


'date1' => '{{{label}}} <div class="{{grid_col_class}}"> {{#comment}}{{comment}}{{/comment}} {{^comment}}{{day}}{{#day}}{{#month}} {{/month}}{{/day}}{{#day}}{{^month}}{{#year}}/ /{{/year}}{{/month}}{{/day}}{{month}}{{#month}}{{#year}} {{/year}}{{/month}}{{year}}{{#day}}{{^month}}{{^year}}/ /{{/year}}{{/month}}{{/day}}{{/comment}}</div>',


'relations' => '
		{{#ex_array}}
						{{#ex_list}}
								<div class="row {{class}}">
										<label class="{{grid_col_class_label}}" style="text-align:left;">{{label}}</label>
										<div class="{{grid_col_class}}">
												{{#link_list}}{{#delimiter}} | {{/delimiter}}{{#url}}<a href="{{url}}" >{{/url}}{{value}}{{#url}}</a>{{/url}}{{/link_list}}
										</div>
								</div>
						{{/ex_list}}
		{{/ex_array}}',


'relations_list' => '
		{{#ex_array}}
						{{#ex_list}}
							{{#label}}
									<div class="row {{class}}">
											<label class="{{grid_col_class_label}}" style="text-align:left;">{{label}}</label>
											<div class="{{grid_col_class}}">
												<ul class="delimiter">
													{{#link_list}}<li>{{#url}}<a href="{{url}}" >{{/url}}{{value}}{{#url}}</a>{{/url}} {{#entity_lang}}[{{entity_lang}}]{{/entity_lang}}</li>{{/link_list}}
												</ul>
											</div>
									</div>
							{{/label}}
						{{/ex_list}}
		{{/ex_array}}',


'subject_list' => '
		{{{label}}}
		{{#ex_array}}
		<div class="{{grid_col_class}}">
							<ul class="delimiter">
									{{#ex_list}}
											<li><a href="{{url}}" >{{value}}</a> ({{obj_type}})</li>
									{{/ex_list}}
							</ul>
		</div>
		{{/ex_array}}',

);

