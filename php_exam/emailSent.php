<?php
session_start();
require('connect.inc.php');
include('functions.inc.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Exam PHP</title>
	<meta charset="UTF-8">

	<!-- <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'> -->
	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/default.css"/>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body id="body">

<?php include('header.view.php'); ?>

<div class="container">
	<div class="panel">
		<h2 class="mb24">Confirmation</h2>
		<p>Un mail de confirmation vous a été envoyé. Veuillez suivre le lien de l'email pour <strong>activer</strong> votre compte.</p>
		<a href="index.php">Se connecter</a>
	</div>
</div>

<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> -->
<!-- <script src="lib/jquery-1.8.2.min.js"></script> -->
<!-- <script src="lib/velocity.min.js"></script> -->
</body>
</html>