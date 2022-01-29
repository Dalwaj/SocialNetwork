<?php
	try{
		$bdd= new PDO('mysql:host=localhost;dbname=social_network;charset=utf8',
					'root',
					'');
	} catch (Exception $e){
		die('Erreur : ' .$e->getMessage());
	}

?>

<html>
<head>
	<title>Inscription</title>
</head>
<body>
	<main>
		<div>
			<form method="post" action="confirm.php" enctype="multipart/form-data">
				<div>
					<label>Identifiant : </label>
					<input type="text" name="identifiant" id="identifiant">
				</div>
				<div>
					<label>E-mail : </label>
					<input type="email" name="email" id="email">
				</div>
				<div>
					<label>Confirmer votre e-mail : </label>
					<input type="email" name="email2" id="email2">
				</div>
				<div>
					<label>Mot de passe : </label>
					<input type="password" name="password" id="password">
				</div>
				<div>
					<label>Confirmez votre mot de passe : </label>
					<input type="password" name="password2" id="password2">
				</div>
				<div>
					<input type="submit" name="signin" id="signin" value="S'inscrire">
				</div>
			</form>
			<a href="connexion.php">Déjà inscrit? Connectez-vous</a>
		</div>
	</main>
</body>
</html>