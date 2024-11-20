<?php
	ini_set ('error_reporting', 1); //Turns on error reporting - remove once everything works.
	
	try{
		require_once('../pdo_connect.php'); //Connect to the database
		$sql = '';
		$result = $dbc-> query($sql);
	} catch (PDOException $e){
		echo $e->getMessage();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hospital name here</title>
	<meta charset ="utf-8"> 
</head>

