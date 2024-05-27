<?php 
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "gestao_ascsacb";

	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_errno){
		echo "A conexão falhou ". $mysqli->connect_error;
		exit();
	}
