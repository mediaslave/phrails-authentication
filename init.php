<?
$dirname = dirname(__FILE__);
//Include all of the folders that need to be included to run the plugin.
add_include_directory($dirname . '/app/models');
add_include_directory($dirname . '/app/controllers');
add_include_directory($dirname . '/lib');

spl_autoload_register('mediaslave_autoload', true, true);

function mediaslave_autoload($class_name){
	$class_parts = explode('\\', $class_name);
	$class_name = array_pop($class_parts);
	$loaded = @include_once($class_name . '.php');
	if(!$loaded){
		//print $class_name . '<br/>';
	}
}