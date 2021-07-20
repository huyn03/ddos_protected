<?php require 'vendor/autoload.php';

use Drhuy\DdosProtected\App;

$t = new App([
	'fix_max_request'	=> 30,
	'max_request'		=> 10,
	'time_reset'		=> 1,
	'auto_remove_log'	=> true,
	'n_level_remove_log'=> 1,
	'block_type'		=> 'abc',
	'onSupend'			=> function(){
		// do something without print data
	},
	'onAcept'			=> function(){
		// do something without print data
	}
]);

$t-> run(['max_request'=> 10, 'block_type'=> 'IP']);
$t-> run();

?>
