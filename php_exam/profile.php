<?php
session_start();
require('connect.inc.php');
include('functions.inc.php');

if( $_SESSION['state'] != 'active' ) header('location:index.php');
$errors = [];
if( $_FILES ){
	require 'class.upload.php';

	$handle = new upload($_FILES['newAvatar']);
	if( $handle->uploaded ){
		$handle->file_new_name_body 	= $_SESSION['id'];
		$handle->file_overwrite 		= true;
		$handle->image_resize 			= true;
		$handle->image_x 				= 240;
		$handle->image_y 				= 240;
		$handle->image_ratio_crop 		= true;
		$handle->image_convert 			= 'jpg';
		$handle->image_background_color = '#aaaaaa';
		$handle->file_max_size 			= '524288';
		$handle->allowed 				= array('image/jpeg','image/jpg','image/png');
		$handle->process('avatars/');

		$handle->file_new_name_body 	= $_SESSION['id'].'_mini';
		$handle->file_overwrite 		= true;
		$handle->image_resize 			= true;
		$handle->image_x 				= 40;
		$handle->image_y 				= 40;
		$handle->image_ratio_crop 		= true;
		$handle->image_convert 			= 'jpg';
		$handle->image_background_color = '#aaaaaa';
		$handle->file_max_size 			= '524288';
		$handle->allowed 				= array('image/jpeg','image/jpg','image/png','image/JPG');
		$handle->process('avatars/');

		if ($handle->processed) {
			$handle->clean();
		} else {
			$errors['avatar'][] = $handle->error;
		}
	}
}

if( $_POST['form_submit'] ){
	$form = $_POST['form_submit'];
	$oldPseudo = $_SESSION['pseudo'];

	if( $form == 'pseudo' ){
		$newPseudo = trim(strip_tags( $_POST['newPseudo'] ));
		if( $_POST['newPseudo'] ){

			$verif = "SELECT pseudo FROM phpexam_users WHERE pseudo=:pseudo LIMIT 1";
			$prepareVerif = $connect->prepare($verif);
			$prepareVerif->bindParam(":pseudo", $newPseudo);
			$prepareVerif->execute();
			$resultVerif = $prepareVerif->fetch();

			if( !$resultVerif ){
				$insert = "UPDATE phpexam_users SET pseudo=:newPseudo WHERE pseudo=:oldPseudo LIMIT 1";
				$prepareInsert = $connect->prepare($insert);
				$prepareInsert->bindParam(":oldPseudo", $oldPseudo );
				$prepareInsert->bindParam(":newPseudo", $newPseudo );
				$prepareInsert->execute();

				$_SESSION['pseudo'] = $newPseudo;
			} else {
				$errors["pseudo"] = "Pseudo déjà pris";
			}
		} else {
			$errors['pseudo'] = 'Entrez votre nouveau pseudo.';
		}
	} else if( $form == 'email' ){
		$newEmail = trim(strip_tags( $_POST['newEmail'] ));
		if( is_valid_email($_POST['newEmail']) ){
			$update = "UPDATE phpexam_users SET email='$newEmail' WHERE pseudo=:pseudo LIMIT 1";
			$prepareUpdate = $connect->prepare($update);
			$prepareUpdate->bindParam(":pseudo", $oldPseudo );
			$prepareUpdate->execute();

			$_SESSION['email'] = $newEmail;
		} else {
			$errors['email'] = 'Addresse email incorrecte.';
		}

	} else if( $form == 'password' ){
		$oldPassword = trim(strip_tags( $_POST['oldPassword'] ));
		$newPassword = trim(strip_tags( $_POST['newPassword'] ));

		if( $oldPassword && $newPassword ){
			$verif = "SELECT password FROM phpexam_users WHERE pseudo=:pseudo LIMIT 1";
			$prepareVerif = $connect->prepare($verif);
			$prepareVerif->bindParam(":pseudo", $oldPseudo);
			$prepareVerif->execute();
			$resultVerif = $prepareVerif->fetch();

			if( $resultVerif['password'] == $oldPassword ){
				$update = "UPDATE phpexam_users SET password='$newPassword' WHERE pseudo=:pseudo LIMIT 1";
				$prepareUpdate = $connect->prepare($update);
				$prepareUpdate->bindParam(":pseudo", $oldPseudo );
				$prepareUpdate->execute();
			} else {
				$errors["password"] = "Votre ancien mot de passe est incorrect";
			}
		} else {
			$errors['password'] = 'Remplissez les deux champs svp.';
		}
	} else if( $form == 'drop' ){
		$verif = "DELETE FROM phpexam_users WHERE pseudo=:pseudo LIMIT 1";
		$prepareVerif = $connect->prepare($verif);
		$prepareVerif->bindParam(":pseudo", $oldPseudo);
		$prepareVerif->execute();

		$target1 = "avatars/".$_SESSION['id'].".jpg";
		$target2 = "avatars/".$_SESSION['id']."_mini.jpg";

		if (file_exists($target1) && file_exists($target2) ) {
			unlink( $target1 );
			unlink( $target2 );
		} 

		header('location:kill.php');
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
	<div class="panel clearfix">
		<h2>Profil</h2>
		<div class="row">
			<div class="col6">
				<div id="profile_pseudo" class="clearfix profile_line">
					<h3>Pseudo</h3>
					<button class="left button_svg profile_change">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path d="M3 17.25v3.75h3.75l11.06-11.06-3.75-3.75-11.06 11.06zm17.71-10.21c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
						</svg>
					</button>
					<p class="left"><?php echo $_SESSION['pseudo'] ?></p>
					<form action="profile.php" method="post" class="clear clearfix">
						<input type="text" value="" placeholder="Nouveau Pseudo" name="newPseudo">
						<input type="hidden" name="form_submit" value="pseudo">
						<button class="left button mr12 profile_save">Enregistrer</button>
						<button class="left button default profile_cancel">Annuler</button>
					</form>
					<?php
						if( $errors['pseudo'] ){
							echo '<p class="red clear">'.$errors['pseudo'].'</p>';
						}
					?>
				</div>
				<div id="profile_email" class="clearfix profile_line">
					<h3>Addresse email</h3>
					<button class="left button_svg profile_change">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path d="M3 17.25v3.75h3.75l11.06-11.06-3.75-3.75-11.06 11.06zm17.71-10.21c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
						</svg>
					</button>
					<p class="left"><?php echo $_SESSION['email'] ?></p>
					<form action="profile.php" method="post" class="clear clearfix">
						<input type="email" value="" placeholder="Nouvelle Addresse Email" name="newEmail">
						<input type="hidden" name="form_submit" value="email">
						<button class="left button mr12 profile_save">Enregistrer</button>
						<button class="left button default profile_cancel">Annuler</button>
					</form>
					<?php
						if( $errors['email'] ){
							echo '<p class="red clear">'.$errors['email'].'</p>';
						}
					?>
				</div>
				<div id="profile_password" class="clearfix profile_line">
					<h3>Mot de passe</h3>
					<button class="button default profile_change mb12">Modifier</button>
					<form action="profile.php" method="post" class="clear clearfix">
						<input type="password" value="" placeholder="Ancien mot de passe" name="oldPassword">
						<input type="password" value="" placeholder="Nouveau mot de passe" name="newPassword">
						<input type="hidden" name="form_submit" value="password">
						<button class="left button mr12 profile_save">Enregistrer</button>
						<button class="left button default profile_cancel">Annuler</button>
					</form>
					<?php
						if( $errors['password'] ){
							echo '<p class="red clear">'.$errors['password'].'</p>';
						}
					?>
				</div>
			</div>
			<div class="col6">
				<div id="profile_avatar" class="clearfix profile_line">
					<h3>Avatar</h3>
					<img src="avatars/<?php echo $avatar ?>.jpg" alt="Profile Image" width="96" height="96">
					<button class="button default mt8 profile_change mb12">Modifier</button>
					<form action="profile.php" method="post" enctype="multipart/form-data" class="clear clearfix">
						<input type="file" class="mb12" name="newAvatar">
						<button class="left button mr12 profile_save">Enregistrer</button>
						<button class="left button default profile_cancel">Annuler</button>
					</form>
					<?php 
					if( $errors['avatar'] ){
						$html = '<ul>';
						foreach ($errors['avatar'] as $key => $value) {
							$html .= '<li class="red">'.$value.'</li>';
						}
						$html .= '</ul>';
						echo $html;
					} ?>
				</div>
			</div>
		</div>
		<button class="button default right" id="drop_button">Supprimer le compte</button>
		<div id="drop">
			<div class="table">
				<div class="tableCell">
					<form action="#" method="post" class="clearfix panel center col4">
						<p class="mb24">Voulez-vous vraiment supprimer votre compte ?</p>
						<input type="hidden" name="form_submit" value="drop">
						<button class="button" id="drop_cancel">Annuler</button>
						<input type="submit" value="Supprimer mon compte" class="button default right">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="lib/jquery-1.8.2.min.js"></script>
<script src="lib/app.js"></script>
<script>
$(document).ready(function(){
	$('.profile_line form input[type="text"]').click(function(e){
		e.stopPropagation();
	});

	$('.profile_line p').click(function(e){
		$('.profile_line form').slideUp(100);
		$(this).parent().find('form').stop().slideDown(100);
		$(this).parent().find('form input[type="text"]').select();

		e.stopPropagation();
	});

	$('.profile_change').click(function(e){
		$('.profile_line form').slideUp(100);
		$(this).parent().find('form').stop().slideDown(100);
		$(this).parent().find('form input[type="text"]').select();

		e.stopPropagation();
	});

	$('.profile_cancel').click(function(e){
		$(this).parent().slideUp(100);

		e.preventDefault();
		e.stopPropagation();
	});

	$('.profile_save').click(function(e){
		$(this).parent().submit();

		e.stopPropagation();
		e.preventDefault();
	});

	$('.profile_line form').submit(function(e){
		// e.preventDefault();
	});

	$('#drop_button').click(function(e){
		$('#drop').show();
		e.preventDefault();
	});

	$('#drop_cancel').click(function(e){
		$('#drop').hide();
		e.preventDefault();
	});

	$('#drop').click(function(e){
		$('#drop').hide();
	});

	$('#drop form').click(function(e){
		e.stopPropagation();
	});

});
</script>
</body>
</html>