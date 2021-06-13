<?php
	$servername = "localhost";
	$database = "ipreg";
	$username = "****";
	$password = "****************************";
	/*
	//create connection
	$conn = mysqli_connect($servername, $username, $password, $database);
	//Check connection
	if(!$conn){
		echo "Connection failed\n";
	}
	echo "Connected successfully\n";
	mysqli_close($conn);
	*/
	function locate_user (){
		return get_client_ip();
	}
	function get_client_ip(){
		$ipaddress = ' ';
		if(getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddres = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	function connect($servername, $username, $password, $database){

		$connection = mysqli_connect($servername, $username, $password, $database);
		return $connection;
	}
	function close_connection(&$connection){
		mysqli_close($connection);
	}

?>
