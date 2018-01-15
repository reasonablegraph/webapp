<?php

return array(
'empty' => 'Empty Field Template',

'person_title' => '
			 {{title}}{{#person_numeration}},
								{{person_numeration}}{{/person_numeration}}{{#titles_associated}},
												{{#list}}{{#delimiter}}, {{/delimiter}}{{value}}{{/list}}{{/titles_associated}}
												{{#person_dates_associated}}({{person_dates_associated}}){{/person_dates_associated}}
						{{#person_fuller_name}}
							({{person_fuller_name}})
						{{/person_fuller_name}}',

'person_title_opac_detail' => '{{{title}}}',

'basic_title_opac_detail' => '{{{title}}}',

'simple' => '<label>{{{label_sr}}}</label>{{#text_value}}{{text_value}}{{/text_value}}',

'organization_title' => '
{{title}}{{#subdivision}}. {{#list}}{{#delimiter}} - {{/delimiter}}{{value}}{{/list}}{{/subdivision}}{{#addition}}. {{#list}}{{#delimiter}},{{/delimiter}}{{value}}{{/list}}{{/addition}}
{{#number}}({{/number}}{{^number}}{{#date}}({{/date}}{{/number}}{{^number}}{{^date}}{{#location}}({{/location}}{{/date}}{{/number}}{{#number}}{{number}}{{#date}} : {{/date}}{{/number}}{{#date}}{{date}}{{/date}}{{#location}}{{#number}}{{^date}} : {{/date}}{{/number}}{{#date}} : {{/date}}{{location}}{{/location}}{{#number}}){{/number}}{{^number}}{{#date}}){{/date}}{{/number}}{{^number}}{{^date}}{{#location}}){{/location}}{{/date}}{{/number}}{{#type}} ({{type}}){{/type}}
',

'family_title' => '{{title}} {{#type}}({{type}}){{/type}}{{#titles_place}}. {{#list}}{{#delimiter}}, {{/delimiter}}{{value}}{{/list}}{{/titles_place}}{{#dates_associated}} ({{dates_associated}}) {{/dates_associated}}',

'work_title' => '{{title}}{{#part_number}}. {{part_number}}{{/part_number}}{{#title_part_name}}, {{title_part_name}}{{/title_part_name}} {{#form}}[{{form}}]{{/form}}{{#date}} ({{date}}).{{/date}}{{#place}} {{place}}.{{/place}}{{#language}} {{language}}.{{/language}} {{version}}',

'manifestation_title_details' => '{{title}}
{{#title_remainder}} : {{title_remainder}} {{/title_remainder}}
{{#title_medium}}[{{title_medium}}] {{/title_medium}}
{{title_partNumber}}{{#title_partName}}, {{/title_partName}}{{title_partName}}
{{#title_responsibility}} / {{title_responsibility}}. {{/title_responsibility}}
{{#edition}} - {{edition}}.{{/edition}}
{{#publication}} - {{publication}}.{{/publication}}
{{#distribution}} - {{distribution}}.{{/distribution}}
{{#production}} - {{production}}.{{/production}}
{{#manufactur}} - {{manufactur}}.{{/manufactur}}',

'manifestation_title' => '{{title}}
{{#title_remainder}} : {{title_remainder}} {{/title_remainder}}
{{#title_medium}}[{{title_medium}}] {{/title_medium}}
{{#list_part}}
{{#list}}{{#delimiter}} - {{/delimiter}}{{value}}{{/list}}{{#list_number}},{{/list_number}}
{{/list_part}}
{{#list_number}}
{{#list}}{{#delimiter}} - {{/delimiter}}{{value}}{{/list}}
{{/list_number}}
{{#title_responsibility}} / {{title_responsibility}}. {{/title_responsibility}}',

// 'manifestation_contained' => '
// <div class="sr-only">
// {{list_title}}:
// <ol>{{#works_array}}<li><a href="/archive/item/{{id}}">{{label}}</a></li>{{/works_array}}</ol>
// </div>',

'work_contained' => '
<div class="sr-only">
{{list_title}}:
<ol>{{#works_array}}<li><a href="/archive/item/{{id}}">{{label}}</a></li>{{/works_array}}</ol>
</div>',

'object_type' => '
						<label>{{{label_sr}}}</label>
									{{#object_type}}{{object_type}}{{/object_type}}',

'one_line_relation' => '
							<label>{{{label_sr}}}</label>
									{{#line_list}}
										<ol>
											{{#link_list}}<li>{{#url}}<a href="{{url}}" >{{title}}</a>{{/url}}{{^url}}{{title}}{{/url}}</li>{{/link_list}}
										</ol>
									{{/line_list}}',

'one_label_line' => '
									<label>{{{label_sr}}}</label>
										{{#line_list}}
												{{#list}}{{#delimiter}} - {{/delimiter}}{{value}}{{/list}}
												{{#link_list}}{{#delimiter}} - {{/delimiter}}<a class="{{class}}" href="{{url}}" target="_blank">{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}</a>{{/link_list}}
									{{/line_list}}',

'one_label_list' => '
						<label>{{{label_sr}}}</label>
									{{#line_list}}
										 <ol>
											{{#list}}<li>{{value}}</li>{{/list}}
											{{#link_list}}<li><a class="{{class}}" href="{{url}}" target="_blank">{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}</a></li>{{/link_list}}
										</ol>
									{{/line_list}}',


'one_label_line_multi' => '
		{{#ex_array}}
						{{#ex_list}}
								{{#label}}
										<li><label>{{label}}</label>
											<ol>{{#link_list}}<li>{{value}}</li> {{/link_list}} </ol>
										</li>
								{{/label}}
						{{/ex_list}}
		{{/ex_array}}',


'oneline' => '
					<label>{{{label_sr}}}</label>
							 <ol>
								{{#array_val}}
									<li>{{#value}}{{#url}}<a class="{{class}}" href="{{url}}" target="_blank">{{/url}}{{value}}{{#url}}</a>{{/url}}{{/value}}</li>
								{{/array_val}}
							</ol>',


'one_label_combination' => '
	<label>{{{label_sr}}}</label>
		{{#all_array}}
						 <ol>
							{{#pos}}
										<li>
												{{#list_text}} {{#url}}<a href="{{url}}" target="_blank">{{/url}}{{{value}}}{{#url}}</a>{{/url}}{{/list_text}}
												{{#list_url}}{{^delimiter}}⟶ {{/delimiter}}{{/list_url}}{{#list_url}}{{#delimiter}}, {{/delimiter}}<a class="{{class}}" href="{{url}}" target="_blank">{{#description}}{{description}}{{/description}}{{^description}}{{value}}{{/description}}</a>{{/list_url}}
										</li>
							{{/pos}}
						</ol>
		{{/all_array}}',

'citations_list' => '
					<label>{{{label_sr}}}</label>
						<ol class="nested-list">
							{{#citation_list}}
								<li>	{{#value}}{{.}}{{/value}}</li>
							{{/citation_list}}
						</ol>',

// CITATIONS RULE-ENGINE
// 'citations' => '
// 	<label>{{{label_sr}}}</label>
// 		{{^is_manif_citation}}
// 				<ol>
// 					{{#citations}}<li>{{.}}</li>{{/citations}}
// 				</ol>
// 		{{/is_manif_citation}}
// 		{{#is_manif_citation}}
// 				{{citations}}
// 		{{/is_manif_citation}}
// ',

'date1' => '<label>{{{label_sr}}}</label> {{#comment}}{{comment}}{{/comment}} {{^comment}}{{day}}{{#day}}{{#month}} {{/month}}{{/day}}{{#day}}{{^month}}{{#year}} {{/year}}{{/month}}{{/day}}{{month}}{{#month}}{{#year}} {{/year}}{{/month}}{{year}}{{/comment}}',

'relations' => '
		{{#ex_array}}
						{{#ex_list}}
										<li><label>{{label}}</label>
												{{#link_list}}{{#delimiter}}, {{/delimiter}}<a href="{{url}}" target="_blank">{{value}}</a>{{/link_list}}
										</li>
						{{/ex_list}}
		{{/ex_array}}',

'relations_list' => '
		{{#ex_array}}
						{{#ex_list}}
								{{#label}}
										<li><label>{{label}}</label>
											<ol>{{#link_list}}<li>{{#url}}<a href="{{url}}" target="_blank">{{/url}}{{value}}{{#url}}</a>{{/url}} {{#entity_lang}}[{{entity_lang}}]{{/entity_lang}}</li> {{/link_list}} </ol>
										</li>
								{{/label}}
						{{/ex_list}}
		{{/ex_array}}',


'subject_list' => '
		{{{label}}}
		{{#ex_array}}
							<ol class="delimiter">
									{{#ex_list}}
											<li><a href="{{url}}" target="_blank">{{value}}</a> ({{obj_type}})</li>
									{{/ex_list}}
							</ol>
		{{/ex_array}}',

);

