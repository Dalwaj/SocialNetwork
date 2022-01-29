<head>
	<link rel="stylesheet" type="text/css" media="screen" href="../css/header.css">
</head>
<header>
	<nav>
		<ul>
			<li><a href="accueil.php">Accueil</a></li>
			<?php
			echo "<li><a href=profil.php?pseudo=".$_SESSION['identifiant'].">Mon profil</a></li>";
			?>
			<li><a href="deconnexion.php">Se déconnecter</a></li>
		</ul>
	</nav>
</header>