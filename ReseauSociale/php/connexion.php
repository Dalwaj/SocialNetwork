<?php
	session_start();

	if(isset($_SESSION['id'])){
		header('Location: accueil.php');
	}

	try{
		$bdd= new PDO('mysql:host=localhost;dbname=social_network;charset=utf8',
					'root',
					'');
	} catch (Exception $e){
		die('Erreur : ' .$e->getMessage());
	}

	if(isset($_POST['identifiant']) && isset($_POST['password'])){
		$requete = $bdd->prepare('SELECT password FROM membre WHERE pseudo LIKE ?');
		$requete->execute([
			$_POST['identifiant']
		]);

		$mdp = $requete->fetch();
		if ($requete->rowCount() == 0
			|| $mdp['password'] != sha1($_POST['password'])){
			$erreur = "Identifiant ou mot de passe incorrect";
		} else {
			$getUser = $bdd->prepare('SELECT * FROM membre WHERE pseudo LIKE ?');
			$getUser->execute([
				$_POST['identifiant']
			]);
			$userInfos = $getUser->fetch(); 

			$_SESSION['identifiant'] = $userInfos['pseudo'];
			$_SESSION['id'] = $userInfos['id'];
			$_SESSION['email'] = $userInfos['email'];

			$erreur = "Bravo";
			header('Location: accueil.php');
		}
	}
?>
<html>
<head>
	<title>Connexion</title>
</head>
<body>
	<header>
		
	</header>

	<main>
		<div>
			<form method="post" action="connexion.php">
				<div>
					<label for="identifiant">Identifiant : </label>
					<input type="text" name="identifiant" id="identifiant" placeholder="Votre pseudo">
				</div>
				<div>
					<label for="password">Mot de passe : </label>
					<input type="password" name="password" id="password">
				</div>
				<div>
					<input type="submit" name="connect" id="connect" value="Se connecter">
				</div>
			</form>

			<a href="inscription.php">Pas encore inscrit? Clique ici</a>
		</div>
		<?php
			if (isset($erreur)){
				echo $erreur;
			}
		?>
	</main>

	<footer>
		
	</footer>
</body>
</html>