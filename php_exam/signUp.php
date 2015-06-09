<?php
session_start();
require('connect.inc.php');
include('functions.inc.php');
$errors = [];

if( $_POST ){
	$pseudo = trim(strip_tags( $_POST['pseudo'] ));
	$password = trim(strip_tags( $_POST['password'] ));
	$email = trim(strip_tags( $_POST['email'] ));

	if( !$pseudo ){
		$errors['pseudo'] = "Entrez votre nom de compte.";
	}

	if( !$password ){
		$errors['password'] = "Entrez votre mot de passe.";
	}

	if( !is_valid_email($email) ){
		$errors['email'] = "Ton email est incorrect";
	}

	if( count($errors) == 0 ){
		// Cherche le pseudo dans la DB.
		$verif = "SELECT pseudo FROM phpexam_users WHERE pseudo = :pseudo LIMIT 1";
		$prepareVerif = $connect->prepare($verif);
		$prepareVerif->bindParam(":pseudo", $pseudo);
		$prepareVerif->execute();
		$resultVerif = $prepareVerif->fetch();

		// Si pseudo n'existe pas dans la DB.
		if( !$resultVerif ){
			$activationKey =  md5(uniqid(rand(), true));
			$password = md5($password);

			// INSERT dans la DB.
			$insert = "INSERT INTO phpexam_users SET pseudo='$pseudo', email='$email', password=:password, state='waiting', activation_key=:activation_key";
			$prepareInsert = $connect->prepare($insert);
			$prepareInsert->bindParam(":password", $password);
			$prepareInsert->bindParam(":activation_key", $activationKey);
			$prepareInsert->execute();

			// Envoye l'email d'activation.
			$emailSubject = "PHP Exam - Mail d\'activation.";
			$emailMessage = "Salut ".$pseudo.",\r\nTu peux suivre ce lien pour activer ton compte : \r\n\r\n";
			$emailMessage.= "http://martindenis.be/php_exam/activ.php?u=".$pseudo."&p=".$activationKey;

			mail($email, $emailSubject, $emailMessage);
			header("location:emailSent.php");
		} else {
			$errors["pseudo"] = "Pseudo déjà pris";
		}
	}
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
	<div class="panel center col4">
		<h2>Créer un compte</h2>
		<form action="signUp.php" method="post">
			<ol>
				<li class="clearfix formGroup">
					<label for="pseudo">Pseudo</label>
					<input type="text" id="pseudo" name="pseudo" placeholder="Marty">
					<?php if( $errors['pseudo'] ) echo '<p class="error">'.$errors['pseudo'].'</p>' ?>
				</li>
				<li class="clearfix formGroup">
					<label for="email">Email</label>
					<input type="text" id="email" name="email" placeholder="marty@gmail.com">
					<?php if( $errors['email'] ) echo '<p class="error">'.$errors['email'].'</p>' ?>
				</li>
				<li class="clearfix formGroup">
					<label for="password">Mot de passe</label>
					<input type="password" id="password" name="password" placeholder="password">	
					<?php if( $errors['password'] ) echo '<p class="error">'.$errors['password'].'</p>' ?>
				</li>
				<li class="formGroup mt24">
					<a href="index.php">Se connecter</a>
					<input type="submit" value="Envoyer" class="button right">
				</li>
			</ol>
		</form>
	</div>
</div>

</body>
</html>