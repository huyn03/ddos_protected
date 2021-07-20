<?php namespace Drhuy\DdosProtected;
/**
 * 
 */
use Drhuy\DdosProtected\Helpers;

class App
{
	private $dir_logs 			= 'ddos_protected/';

	private $fix_name 			= 'All';

	public $max_request			= 180;

	public $fix_max_request		= 300;

	public $time_reset			= 1;

	public $auto_remove_log		= false;

	public $n_level_remove_log  = 1;

	public $block_type 			= 'All';

	private $onSupend;

	private $onAcept;

	function __construct($arguments = []){
		if (!file_exists($this-> dir_logs)) {
		    mkdir($this-> dir_logs, 0777, true);
		}
		$this-> initParams($arguments);
	}

	// Process
	public function run($arguments = []){
		$this-> initParams($arguments);
		$fn_name = 'blockBy'.$this-> block_type;
		if(method_exists($this, $fn_name))
			$this-> {$fn_name}();
		else 
			$this-> blockByAll();
	}

	private function getFileName($fix_name = null, $time = null){
		$fix_name = $fix_name? $fix_name: $this-> fix_name;
		return Helpers::getFileName($this-> time_reset, $fix_name, $this-> dir_logs, $time);
	}

	// Action by type All
	private function blockByAll(){
		$this-> fix_name = $this-> block_type;
		$this-> block($this-> getFileName());
	}

	// Action by type IP
	private function blockByIP(){
		$this-> fix_name = $ip = Helpers::getIpClient();
		$this-> block($this-> getFileName($ip));
	}

	// Action by type MAC
	private function blockByMAC(){
		$this-> fix_name = $mac = Helpers::getMacClient();
		$this-> block($this-> getFileName($mac));
	}
	// global
	private function initParams($arguments){
		if(!isset($arguments['block_type']))
			$arguments['block_type'] = 'All';
		if(!isset($arguments['max_request']))
			$arguments['max_request'] = $this-> fix_max_request;
		foreach($arguments as $key => $value) {
			$this-> {$key} = $value;
		}
	}

	private function block($filename){
		$n_requests = $this-> getRequestCount($filename);
		if($n_requests >= $this-> max_request)
			return $this-> supendRequest();
		$this-> updateRequestCount($filename, ++$n_requests);
		return $this-> acceptRequest();
	}

	private function getRequestCount($filename){
		if(!file_exists($filename)){
			$this-> updateRequestCount($filename, 0);
			return 0;
		}
		$data = file_get_contents($filename);
		$data = json_decode($data, true);
		if(!isset($data['requests'])){
			$this-> updateRequestCount($filename, 0);
			return 0;
		}
		return $data['requests'];
	}

	private function updateRequestCount($filename, $n_requests){
		file_put_contents($filename, json_encode(['requests'=> $n_requests]));
		// if($n_requests == 0 && $this-> auto_remove_log)
		if($this-> auto_remove_log)
			$this-> removeLog();
	}

	private function removeLog(){
		$time = Helpers::getTime();
		$hour = $time['hour'];
		$min  = $time['min'];
		$range = ($this-> n_level_remove_log * $this-> time_reset);
		if($min < $range){
			$min += 60;
			$hour -= 1;
			if($hour < 0)
				$hour = 23;
		}
		$min = $min - $range;
		$filename = $this-> getFileName(null, ['hour'=> $hour, 'min'=> $min]);
		if(file_exists($filename))
			unlink($filename);
	}

	private function supendRequest(){
		if(is_callable($this-> onSupend))
			call_user_func($this-> onSupend);
		header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
    	exit;
	}

	private function acceptRequest(){
		if(is_callable($this-> onAcept))
			call_user_func($this-> onAcept);
	}

}