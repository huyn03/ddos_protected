<?php namespace Drhuy\DdosProtected;
/**
 * 
 */
class Helpers{

	public static function getTime(){
		$min = date("i");
		$hour = date("h");
		return ['hour'=> $hour, 'min'=> $min];
	}

	public static function getFileName($time_reset = 5, $fix_name = 'at', $dir_logs = 'ddos_protected/', $time = null){
		if(!isset($time['hour']) || !isset($time['min']))
			$time = self::getTime();
		$hour = $time['hour'];
		$min  = $time['min'];
		$min = $time_reset * floor($min/$time_reset);
		return $dir_logs . $fix_name . "-$hour-$min";
	}

	public static function getIpClient(){
		$ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return str_replace(":","",$ipaddress);;
	}

	public static function getMacClient(){
		$string=exec('getmac');
		$mac=substr($string, 0, 17); 
		return $mac;
	}

}