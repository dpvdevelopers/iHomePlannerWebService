<?php
	require './../utils/functions.php';
	//Se recupera la ip del usuario conectado por motivos de seguridad
	$ip = locate_user ();
	//Se abre una conexión a la base de datos y se verifica
	$conn = connect($servername, $username, $password, $database);
	if($conn){
		echo "Conexión correcta";
	}else{
		echo "Conexión fallida";
	}
	//Se recogen los datos enviados por la app, si no han llegado datos se inicializan las variables
	if( isset($_POST['usuario']) && isset($_POST['pass']) && !empty($_POST['usuario']) && !empty($_POST['pass'])){
		$postUser = htmlspecialchars($_POST["usuario"]);
		$postPass = htmlspecialchars($_POST["pass"]);
	}else{
		$postUser = "";
		$postPass = "";
	}
	echo "nombre de usuario -> " . $postUser . "<br>";
	echo "contraseña -> " . $postPass . "<br>";
	echo "Usuario post -> " . $_POST['usuario'] . "<br>";
	echo "Pass post -> " . $_POST['pass'] . "<br>";
	echo $ip;

	//Se crea un nuevo usuario con los datos proporcionados por la app, si los datos no han llegado correctamente no hace nada
	if($postUser != "" && $postPass != ""){
		create_new_user($postUser, $postPass, $ip, $conn);
	}
	close_connection($conn);
	 echo "<br>hola mundo";
?>
