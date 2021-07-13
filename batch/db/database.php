<?php

$config =  array(
		'username' => 'sunhrm',
		'password' => 'H&3ss83^3#@i',
		'hostname' => '172.16.1.29',
		'dbname'   => 'sunhrm' 
);


function connect ($config){

	try{

		$conn = new PDO('mysql:host='.$config['hostname'].';dbname='.$config['dbname'] ,
				$config['username'] , 
				$config['password']
		);
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		return $conn;
		
	}catch(Exception $e){

		echo "Exception" . $e->getMessage();
		return false;
	}
}

function query($query , $conn , $bindings = null){

	$stmt = $conn->prepare($query);
	$stmt->execute($bindings);

	$results = $stmt->fetchAll();

	return $results ? $results : false;
}

function update_query_execute ($query , $conn , $bindings = null){
	$stmt = $conn->prepare($query);
	$stmt->execute($bindings);
	return $stmt->rowCount();
}

function insert_query_execute ($query , $conn , $bindings = null){
	$stmt = $conn->prepare($query);
	$stmt->execute($bindings);
	
	return $conn->lastInsertId();
}


?>