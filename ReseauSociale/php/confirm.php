<?php
	try{
		$bdd= new PDO('mysql:host=localhost;dbname=social_network;charset=utf8',
					'root',
					'');
	} catch (Exception $e){
		die('Erreur : ' .$e->getMessage());
	}
	
	if(!isset($_POST['identifiant']) || !isset($_POST['email']) || !isset($_POST['email2'])
		|| !isset($_POST['password']) || !isset($_POST['password2'])){
		die('Erreur : Il faut remplir tout les champs');
	} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !filter_var($_POST['email2'], FILTER_VALIDATE_EMAIL)){
		die('Erreur : Veuillez entrez des adresses email valides');
	} elseif ($_POST['email'] != $_POST['email2']){
		die('Erreur : Les emails entrés sont différents');
	} elseif ($_POST['password'] != $_POST['password2']){
		die('Erreur : les mots de passes entrés sont différents');
	} elseif (strlen($_POST['identifiant']) > 20) {
		die('Erreur : l\'identifiant fait plus de 20 caractères');
	} elseif(strlen($_POST['password']) < 7){
		die('Erreur : le mot de passe fait moins de 7 caractères.');
	} else {
		$identifiant = htmlspecialchars($_POST['identifiant']);
		$email = htmlspecialchars($_POST['email']);
		$password = sha1($_POST['password']);

		$requete = $bdd->prepare('SELECT * FROM membre WHERE pseudo LIKE :identifiant OR email LIKE :email');
		$requete->execute([
			'identifiant' => $identifiant,
			'email' => $email
		]);

		if ($requete->rowCount()>= 1){
			die('Erreur : le pseudo est déjà utilisé.');
		} else {
			$insertMembre = $bdd->prepare('INSERT INTO membre(pseudo, password, email) VALUES(:identifiant, :password, :email)');
			$insertMembre->execute([
				'identifiant' => $identifiant,
				'password' => $password,
				'email' => $email
			]);

			echo "Votre compte a bien été créé";
			header('Location: connexion.php');
		}
	}
?>

<html>
<head>
	<title> </title>
</head>
<body>
	<p>It works !!</p>

</body>
</html>