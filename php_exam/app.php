<?php
session_start();
require('connect.inc.php');
include('functions.inc.php');

if( $_SESSION['state'] != 'active' ) header('location:index.php');

$req = "SELECT id, pseudo, email, state FROM phpexam_users ORDER BY id ASC";
$prepare = $connect->prepare($req);
// $prepare->bindParam(":pseudo", $pseudo);
$prepare->execute();
$users = $prepare->fetchAll();

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
	<div class="panel">
		<h2>Utilisateurs enregistrés.</h2>
		<ul class="clearfix">
			<?php foreach ($users as $key => $value) { 
				if( $value['state'] != 'active' ) continue;
				?>
				<li class="clearfix users_user">
					<img class="left users_avatar" src="avatars/<?php echo file_exists('avatars/'.$value['id'].'.jpg') ? $value['id'] : 'noAvatar'; ?>_mini.jpg" alt="avatar_<?php echo $value['pseudo'] ?>">
					<div class="users_pseudo"><?php echo $value['pseudo'] ?></div>
					<div class="users_email"><?php echo $value['email'] ?></div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>

<script src="lib/jquery-1.8.2.min.js"></script>
<script src="lib/app.js"></script>
</body>
</html>