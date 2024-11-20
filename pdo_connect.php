<?php 

// This file contains the database access information. 
// This file establishes a connection to MySQL and selects the database.

// Set the database access information as constants:
const DBCONNSTRING ='mysql:host=localhost;dbname=CSCFA24Hospital';
const DB_USER = 'djc3088';
const DB_PASSWORD = '8120zrp60jRm6e';


// Make the connection:
try{
	$dbc = new PDO(DBCONNSTRING, DB_USER, DB_PASSWORD);
} catch (PDOException $e){
	echo $e->getMessage();
}

?>