<?php

$dbhost='localhost';
$dbname='php_exam';
$dbuser='root';
$dbpassword='';

// $dbhost='localhost';
// $dbname='martindenis';
// $dbuser='martindenis';
// $dbpassword='xDrxMVAfcsgoiEvv';

try {
	$connect = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
} catch(PDOException $e) {
	die( $e->getMessage() );
}