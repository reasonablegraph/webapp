var USER_ENVIROMENT_FILE='user_env.json';
var env = {
//	"GROOVY_HOME":"/opt/groovy",
//	"JAVA_HOME":"/opt/jdk1.8",
//	"M2_HOME":"/opt/dev/opt/maven2/m2",
//	"ANT_HOME":"/opt/dev/opt/ant/ant",
//	"JBOSS_HOME":"/opt/dark/wildfly",
//	"DARK_HOME":"/opt/dark",
	"PATH":"/usr/local/bin:/usr/bin:/bin",
}

var proc_env_keys = ['LANG','LANGUAGE','USER','HOME','PWD'];
for (var i in proc_env_keys){
	var key = proc_env_keys[i];
	env[key] = process.env[key];
}

if (fs.existsSync(USER_ENVIROMENT_FILE)) {
	var user_env = grunt.file.readJSON(USER_ENVIROMENT_FILE);
	for (var attrname in user_env) { env[attrname] = user_env[attrname]; };
}

var set_env = function(str){
	return Handlebars.compile(str)(env);
}

var prepent_path = function(variable){
	if (env[variable] !== undefined){
		var template = '{{'+ variable +'}}/bin:{{PATH}}';
		env['PATH'] = set_env(template);
	}
}

env['PATH'] = set_env(env['PATH']);


//prepent_path('R_HOME');
//prepent_path('JBOSS_HOME');
//prepent_path('M2_HOME');
//prepent_path('ANT_HOME');
//prepent_path('GROOVY_HOME');
//prepent_path('JAVA_HOME');



//console.log(env);
