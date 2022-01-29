<?php
	session_start();

	if(!isset($_SESSION['id'])){
		header('Location : connexion.php');
	}

	try{
		$bdd= new PDO('mysql:host=localhost;dbname=social_network;charset=utf8',
					'root',
					'');
	} catch (Exception $e){
		die('Erreur : ' .$e->getMessage());
	}

	foreach ($_POST as $values => $value) {
		if (isset($_POST[$values]) && !empty($_POST[$values])){
			if ($values == 'pseudo'){
				$checkQuery = $bdd->prepare("SELECT pseudo FROM membre WHERE pseudo LIKE ?");
			} elseif ($values == 'password'){
				if(strlen($value) < 7){
					$message = "Votre mot de passe fait moins de 7 caractères.";
					break;
				} else {
					$value = sha1($value);
				}
			} elseif ($values == 'email'){
				if (!filter_var($value, FILTER_VALIDATE_EMAIL)){
					$message = "Insérez une adresse mail valide.";
					break;
				} else {
					$checkQuery = $bdd->prepare("SELECT pseudo FROM membre WHERE email LIKE ?");
				}
			} else {
				die("Erreur : formulaire erroné");
			}
		}

		if (isset($checkQuery) && isset($_POST[$values]) && !empty($_POST[$values])){
			$checkQuery->execute([
				htmlspecialchars($value)
			]);

			if ($checkQuery->rowCount() >= 1){
				$message = $value." est déjà utilisé, veuillez en entrer un nouveau.";
				break;
			} elseif ($values == 'pseudo'){ 
				$modifProfil=$bdd->prepare("UPDATE membre SET pseudo = :newPseudo WHERE id = :id");
				$modifProfil->execute([
					'newPseudo' => $value,
					'id' => $_SESSION['id']
				]);

				$_SESSION['identifiant'] = $value;

				$message = "Vos informations ont bien été mise à jour";
			} elseif ($values == 'email'){
				$modifProfil=$bdd->prepare("UPDATE membre SET email = :newEmail WHERE id = :id");
				$modifProfil->execute([
					'newEmail' => $value,
					'id' => $_SESSION['id']
				]);

				$_SESSION['email'] = $value;

				$message = "Vos informations ont bien été mise à jour";
			} elseif ($values == 'password'){
				$modifProfil=$bdd->prepare("UPDATE membre SET email = :newPassword WHERE id = :id");
				$modifProfil->execute([
					'newPassword' => $value,
					'id' => $_SESSION['id']
				]);

				$message = "Vos informations ont bien été mise à jour";
			}
		}
	}

	if (isset($_FILES['pdp']) && $_FILES['pdp']['error'] == 0){
		if ($_FILES['pdp']['size'] <= 6000000){
			$extensionAllowed = ['png', 'jpg', 'gif'];
			$extension = pathinfo($_FILES['pdp']['name'])['extension'];
			if (!in_array($extension, $extensionAllowed)){
				$message = "Le format de l'image n'est pas pris en compte.";
			} else {
				$_FILES['pdp']['name'] = $_SESSION['id']."pdp".".".$extension;
				move_uploaded_file($_FILES['pdp']['tmp_name'], '../css/membre/avatar/'.$_FILES['pdp']['name']);
				$pushPdp = $bdd->prepare('UPDATE membre set pdp = ? WHERE id = ?');
				$pushPdp->execute([
					$_FILES['pdp']['name'],
					$_SESSION['id']
				]);

				$message = "Vos informations ont bien été mise à jour !";
			}
		}
	}
?>
<html>
<head>
	<title>Modifier mon profil</title>
</head>
<body>
	<?php
		include 'header.php';
	?>

	<main>
		<form method="post" action="editerProfil.php" enctype="multipart/form-data">
			<div>
				<label>Nouvelle photo de profil : </label>
				<input type="file" name="pdp">
			</div>
			<div>
				<label for="identifiant">Changer de pseudo : </label>
				<input type="text" name="pseudo">
			</div>
			<div>
				<label for="password">Changer de mot de passe :</label>
				<input type="password" name="password" id="password">
			</div>
			<div>
				<label for=email>Changer d'email :</label>
				<input type="email" name="email" id="email">
			</div>
			<div>
				<input type="submit" value="Modifier">
			</div>
			<?php
				if (isset($message)){
					echo "<div>".$message."</div>";
				}
			?>
		</form>
	</main>
</body>
</html>