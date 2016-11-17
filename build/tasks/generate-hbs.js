'use strict';

module.exports = function(grunt) {

	grunt.task.registerMultiTask('generate-hbs', 'generate file from handlebras template', function() {
	        var context = this.data.context;
	        var template = this.data.template;
	        var outputFile = this.data.output;
	        var Handlebars  = require('handlebars');
	        grunt.file.write(outputFile, Handlebars.compile(template)(context));
	        grunt.log.write(outputFile + ' generated');
	});




	var _ = require('lodash');

	module.exports = function (data, options) {
		options = _.extend({
			compress: false
		}, options);

		var comma = ', ';
		var arrow = ' => ';

		if (options.compress) {
			arrow = arrow.trim();
			comma = comma.trim();
		}

		var convert = function (obj) {
			switch (Object.prototype.toString.call(obj)) {
				case '[object Boolean]':
					return obj ? 'true' : 'false';
				case '[object String]':
					return '"' + obj.replace(/\\/g, '\\\\').replace(/\"/g, '\\"') + '"';
				case '[object Number]':
					return String(obj);
				case '[object Array]':
					return 'array(' +
						obj.map(convert).join(comma) +
					')';
				case '[object Object]':
					return 'array(' +
						_.map(obj, function (v, k) {
							return '"' + k + '"' + arrow + convert(v);
						}).join(comma) +
					')';
			}

			return 'null';
		};

		data = JSON.parse(JSON.stringify(data)); // remove non-JSON properties

		return convert(data);
	};



//	<?php
//			return array(


	grunt.task.registerTask('php-conf', 'php-conf', function() {
		//var phparr = require('phparr');



		var comma = ', ';
		var arrow = ' => ';

		var convert = function (obj) {
			switch (Object.prototype.toString.call(obj)) {
				case '[object Boolean]':
					return obj ? 'true' : 'false';
				case '[object String]':
					return '"' + obj.replace(/\\/g, '\\\\').replace(/\"/g, '\\"') + '"';
				case '[object Number]':
					return String(obj);
				case '[object Array]':
					return 'array(' +
						obj.map(convert).join(comma) +
					')';
				case '[object Object]':
					return 'array(' +
						_.map(obj, function (v, k) {
							return '"' + k + '"' + arrow + convert(v);
						}).join(comma) +
					')';
			}
			return 'null';
		};



		var create_php = function(conf_name){
			var json = grunt.config.get('options.' + conf_name);
			//var phpArrayString = phparr(json);
			var data = JSON.parse(JSON.stringify(json)); // remove non-JSON properties
			//console.log(JSON.stringify(data,null,2));
			var phpArrayString = convert(data);
			var php = '<?php' +"\n" +  '//GENEREATED FILE' + "\n"+ 'return ' + phpArrayString + ';' + "\n";
			var php_file = 'app/config/'+conf_name+'.php';
			grunt.file.write(php_file, php);
			grunt.log.writeln('File "' + php_file + '" created.');
		}
		create_php('arc');
		create_php('arc_rules');


	});



};




