//var options = {
//
//	"dir":{
//		"artisan" : "../../docker/",
//		"laravel": "../opt/laravel/www/archive",
//    "apache_assets" : "../dspace-custom/apache/htdocs/_assets/",
//		"laravel_assets" : "../opt/laravel/www/archive/public/_assets",
//	},
//
//	cmd:{
//		//"wildfly_cli": "<%= opt.dir.dark %>/wildfly/bin/jboss-cli.sh"
//	},
//
//
//	maven:{
//
//		"projects":{
//			"laravel":"./",
//		},
//	},
//
//	goals:{
//		'laravel_reload':{command :'php artisan dump-autoload'},
//		'laravel_start':{command:'php artisan serve'}
//	},
//
//
//}
//
//


var options = {

	'INS_HOME': '/opt/ins',

	"dir" : {
		'jsproject' : '/opt/ins/dev/dspace-custom/jsproject',
		"artisan" : "/data/www/laravel/archive/",
		"laravel" : "/data/www/laravel/archive/",
		"laravel_assets" : "/data/www/laravel/archive/public/_assets",
		"apache_assets" : "/data/www/assets",
	},

	goals : {
		'laravel_reload' : {
			command : 'php artisan dump-autoload'
		},
		'laravel_start' : {
			command : 'php artisan serve'
		}
	},

}




//OPTIONS FOR DOCKER
//var options = {
//
//		'INS_HOME': '/opt/ins',
//		"dir" : {
//			"artisan" : "<%= options.INS_HOME %>/docker/",
//			"laravel" : "<%= options.INS_HOME %>/docker/data/www/laravel/archive/",
//			"laravel_assets" : "<%= options.INS_HOME %>/docker/data/www/laravel/archive/public/_assets",
//			"apache_assets" : "<%= options.INS_HOME %>/docker/data/www/assets",
//		},
//
//		goals : {
//			'laravel_reload' : {
//				command : './artisan dump-autoload'
//			},
//			'laravel_start' : {
//				command : './artisan serve'
//			}
//		},
//
//	}


