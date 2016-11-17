module.exports = function(grunt) {

	grunt.config.set('options.MAIN_DIR','/opt/ins/dev/archive');
	var USER_OPTIONS_FILE = 'user_options.js';

	require('time-grunt')(grunt);

	var fs = require("fs"), vm = require('vm'), jsesc = require('jsesc'), util=require('util'),
	Handlebars  = require('handlebars'),process = require('process');
	var dataContext = {fs:fs, console:console,jsesc:jsesc,util:util,Handlebars:Handlebars,grunt:grunt,process:process};
	vm.runInNewContext(fs.readFileSync('shell-env.js'), dataContext);
	var shell_env = dataContext['env'];

	vm.runInNewContext(fs.readFileSync('options.js'), dataContext);
	var options = dataContext['options'];
	options['env'] = shell_env;
	options['MAIN_DIR'] = grunt.config.get('options.MAIN_DIR');


	var user_opts = {};
	if (fs.existsSync(USER_OPTIONS_FILE)) {
		vm.runInNewContext(fs.readFileSync(USER_OPTIONS_FILE), dataContext);
		user_opts = dataContext['options'];
	}
	grunt.config.set('options',options);
	var oconf = {'options':user_opts};
	grunt.config.merge(oconf);
	options = require(options['dir']['jsproject'] + '/build/src/load-bconfig.js')(grunt);



	grunt.loadNpmTasks('grunt-shell');
	grunt.loadNpmTasks('grunt-cat');
	grunt.loadNpmTasks('grunt-prompt');
	grunt.loadNpmTasks('grunt-step');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-sync');
	grunt.loadNpmTasks('grunt-contrib-watch');
	//grunt.loadNpmTasks('grunt-rsync-2');
  grunt.loadTasks('build/tasks');


	grunt.registerTask('default', [ 'help' ]);
	grunt.registerTask('devel', [ 'deploy','laravel:reload', 'watch' ]);
	grunt.registerTask('help', [ 'cat:help' ]);

	grunt.registerTask('make', [ 'deploy' ]);
	grunt.registerTask('deploy', [ 'php-conf','copy:composer', 'sync:app' ]);

	grunt.registerTask('archive-assets', [
	                                      'copy:archive-assets-bootstrap',
	                                      'copy:archive-assets-css',
	                                      'copy:archive-assets-vendor-bootstrap',
	                                      'copy:archive-assets-vendor-select2',
	                                      'copy:archive-assets-vendor-node-uuid',
	                                      'copy:archive-assets-vendor-jquery',
	                                      'copy:archive-assets-vendor-jquery-ui',
	                                      'copy:archive-assets-vendor-tinymce',
	                                      'copy:archive-assets-vendor-jquery-colorbox',
	                                      'copy:archive-assets-vendor-autocomplete',
	                                      'copy:archive-assets-vendor-dialog',
	                                      'copy:archive-assets-js',
	                                      'copy:archive-assets-img']);

	grunt.registerTask('apache-conf',['generate-hbs:apache-conf']);


//  grunt.registerTask('db-drop-conditional', function() {
//  	if (grunt.config.get('confirm.reply')){
//  		grunt.task.run('shell:db-drop');
//  		return true;
//  	}
//  	return false;
//  });


	function skipErr(err, stdout, stderr, cb) {
    if (err !== null){
    	console.log(err);
    }
    cb();
	}


	var options = grunt.config.get('options');
	// Project configuration.
	grunt.initConfig({

		pkg : grunt.file.readJSON('package.json'),
		options: options,
	  icmd : {
      'cwd': '.',
	  },

		cat : {
			help : {
				file : 'etc/help.txt'
			},
		},


		copy : {
			composer : {
				files : [ {
					expand : true,
					cwd : 'laravel',
					src : [ 'composer.json' ],
					dest : '<%=options.dir.laravel%>/',
					filter : 'isFile'
				}, ],
			},

			'archive-assets-css':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'css/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-bootstrap':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'bootstrap/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-vendor-bootstrap':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/bootstrap/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-vendor-select2':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/select2/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-vendor-node-uuid':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/node-uuid/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-vendor-jquery':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/jquery/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-vendor-jquery-ui':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/jquery-ui/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-vendor-tinymce':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/tinymce/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-vendor-jquery-colorbox':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/jquery-colorbox/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-vendor-autocomplete':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/autocomplete/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},
			'archive-assets-vendor-dialog':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'vendor/dialog/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-js':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'js/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},

			'archive-assets-img':{
				files : [ {
					expand : true,
					cwd : '<%=options.dir.apache_assets%>',
					src : [ 'img/**/*' ],
					dest : '<%=options.dir.laravel_assets%>/',
					//filter : 'isFile'
				}, ],
			},


	},

		 sync: {
	      app: {
	        files: [{
	          cwd: 'app',
	          src: [
	            '**',
	          ],
	          dest : '<%=options.dir.laravel%>/app',
	        }],
	       ignoreInDest: ["storage/**","config/database.php"], // Never remove js files from destination
	       // pretend: true, // Don't do any IO. Before you run the task with `updateAndDelete` PLEASE MAKE SURE it doesn't remove too much.
	        verbose: true, // Display log messages when copying files
	        updateAndDelete:true,
	      }
	    },



			watch : {
				app : {
					files : [ 'app/**/*' ],
					tasks : [ 'sync:app' ],
				},
			},



			'generate-hbs':{

		    'apache-conf':{
		            template :grunt.file.read('etc/conf/apache-conf.hbs'),
		            context  :grunt.file.readJSON('etc/conf/apache.json'),
		            output   :'target/apache/site.conf'
		    },

			},



		shell : {


			'icmd' : {
				options :{
					stdout : true,
					stderr : true,
					execOptions : {
						cwd : '<%= icmd.cwd %>',
						env: shell_env,
					}
				},
			  command: function () {
			  	var conf = grunt.config.get('icmd');
			  	return conf['command'];
			  }
			},


			"show-env" : {
				options : {
					execOptions : {
						cwd : '.',
						env: shell_env,
					}
				},
				command : 'set'
			},


			"laravel-reload" : {
				options : {
					execOptions : {
						cwd : '<%= options.dir.artisan %>',
						env: shell_env,
					}
				},
				command : '<%= options.goals.laravel_reload.command %>'
			},

			"laravel-start" : {
				options : {
					execOptions : {
						cwd : '<%= options.dir.laravel %>',
						env: shell_env,
					}
				},
				command : '<%= options.goals.laravel_start.command %>'
			},



		},

	});





	 grunt.registerTask("dump-environment",['shell:show-env']);

   grunt.registerTask('dump-options', 'dump-options', function() {
           console.log('------------------------------------------------');
           console.log(JSON.stringify(grunt.config.get('options'), undefined, 2));
           console.log('------------------------------------------------');
   });

   grunt.registerTask('dump-config', 'dump-config', function() {
           console.log('------------------------------------------------');
           console.log(JSON.stringify(grunt.config.get(), undefined, 2));
           console.log('------------------------------------------------');
   });
   grunt.registerTask('dump-config-raw', 'dump-config-raw', function() {
           console.log('------------------------------------------------');
           console.log(JSON.stringify(grunt.config.getRaw(), undefined, 2));
           console.log('------------------------------------------------');
   });



 	grunt.registerTask('timestamp', function() {
		grunt.log.subhead(Date());
		//grunt.log.subhead('MAIN_DIR3: ' + grunt.config.get('options.MAIN_DIR'));
	});



	grunt.registerTask('laravel', function(arg) {
		if (arg == 'reload') {
			grunt.task.run('shell:laravel-reload');
		}else if (arg == 'start') {
			grunt.task.run('shell:laravel-start');
		} else {
			console.log("USAGE:");
			console.log("grunt laravel:reload");
			console.log("grunt laravel:start");

		}
	});

};
