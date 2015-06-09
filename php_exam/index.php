<?php
session_start();
require('connect.inc.php');
include('functions.inc.php');

if( $_SESSION['state'] == 'active' ) header('location:app.php');
$errors = [];

if( $_POST ){
	$pseudo = trim(strip_tags( $_POST['pseudo'] ));
	$password = trim(strip_tags( $_POST['password'] ));

	if( !$pseudo ){
		$errors['pseudo'] = 'Entrez votre nom de compte.';
	}

	if( !$password ){
		$errors['password'] = 'Entrez votre mot de passe.';
	}

	if( count($errors) == 0 ){
		// Cherche le pseudo dans la DB.
		$verif = "SELECT id, pseudo, email, password, state FROM phpexam_users WHERE pseudo = :pseudo LIMIT 1";
		$prepareVerif = $connect->prepare($verif);
		$prepareVerif->bindParam(":pseudo", $pseudo);
		$prepareVerif->execute();
		$resultVerif = $prepareVerif->fetch();

		// Si pseudo existe dans la db, continue.
		if( $resultVerif && $resultVerif['state'] == 'active' ){
			// Si le mot de passe correspond à celui de la DB, diriger vers l'appli.
			if( $resultVerif['password'] === md5($password) ){
				$_SESSION['pseudo'] = $pseudo;
				$_SESSION['email'] = $resultVerif['email'];
				$_SESSION['state'] = $resultVerif['state'];
				$_SESSION['id']    = $resultVerif['id'];
				header('location:app.php');
			} else {
				$errors['password'] = "Le mot de passe ne correspond pas.";
			}
		} else {
			$errors['pseudo'] = "Ce compte n\'existe pas.";
		}
	}
}	

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Exam PHP</title>
	<meta charset="UTF-8">

	<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/default.css"/>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body id="body">

<?php include('header.view.php'); ?>

<div class="container">
	<div class="clearfix panel center col4">
		<h2>Se connecter</h2>
		<form action="index.php" method="post">
			<ol>
				<li class="clearfix formGroup">
					<label for="pseudo">Pseudo</label>
					<input type="text" id="pseudo" name="pseudo" placeholder="Marty">
					<?php if( $errors['pseudo'] ) echo '<p class="error">'.$errors['pseudo'].'</p>' ?>
				</li>
				<li class="clearfix formGroup">
					<label for="password">Mot de passe</label>
					<input type="password" id="password" name="password" placeholder="password">	
					<?php if( $errors['password'] ) echo '<p class="error">'.$errors['password'].'</p>' ?>
				</li>
				<li class="formGroup mt24">
					<a href="signUp.php">Créer un compte</a>
					<input type="submit" value="Connexion" class="button right">
				</li>
			</ol>
		</form>
	</div>
</div>
</body>
</html>