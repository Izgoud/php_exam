<?php
	$avatar = 'noAvatar';

	if( file_exists('avatars/'.$_SESSION['id'].'.jpg') ){
		$avatar = $_SESSION['id'];
	}
?>
<header id="header" class="clearfix">
	<h1 class="left"><a href="index.php">Exam Php</a></h1>

	<?php if( $_SESSION ) {?>
		<a href="#" id="sidebar_link"><img src="avatars/<?php echo $avatar; ?>_mini.jpg" alt="Avatar mini"></a>
	<?php } ?>
</header>
<?php if( $_SESSION ) {?>
<aside id="sidebar">
	<a href="profile.php" id="sidebar_avatar"><img src="avatars/<?php echo $avatar; ?>.jpg" alt="avatar"></a>
	<a href="profile.php" id="sidebar_pseudo" class="sidebar_info">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
			<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
		</svg><?php echo $_SESSION['pseudo']; ?></a>
	<a href="kill.php" id="sidebar_disconnect" class="sidebar_info">
		Se déconnecter</a>
</aside>
<?php } ?>