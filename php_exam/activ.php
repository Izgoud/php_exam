<?php
session_start();
require('connect.inc.php');
include('functions.inc.php');

if( $_GET ){
	$user 	 = trim(strip_tags( $_GET['u'] ));
	$activationKey = trim(strip_tags( $_GET['p'] ));

	$verif = "SELECT id, pseudo, email, activation_key, state FROM phpexam_users WHERE pseudo=:pseudo LIMIT 1";
	$prepareVerif = $connect->prepare($verif);
	$prepareVerif->bindParam(":pseudo", $user);
	$prepareVerif->execute();
	$resultVerif = $prepareVerif->fetch();

	if( $resultVerif['activation_key'] == $activationKey && $resultVerif['pseudo'] == $user && $resultVerif['state'] == 'waiting' ){
		$update = "UPDATE phpexam_users SET state='active', activation_key=0 WHERE pseudo=:pseudo LIMIT 1";
		$prepareUpdate = $connect->prepare($update);
		$prepareUpdate->bindParam(":pseudo", $user);
		$prepareUpdate->execute();

		$_SESSION['pseudo']= $user;
		$_SESSION['email'] = $resultVerif['email'];
		$_SESSION['state'] = 'active';
		$_SESSION['id']    = $resultVerif['id'];
		header('location:app.php');
	} else {
		header('location:index.php');
	}
} else {
	header('location:index.php');
}

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
		<h2 class="mb24">Activation de votre compte</h2>
		<p>Votre compte est en cours d'activation, veuillez patienter.</p>
	</div>
</div>



<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> -->
<!-- <script src="lib/jquery-1.8.2.min.js"></script> -->
<!-- <script src="lib/velocity.min.js"></script> -->
</body>
</html>