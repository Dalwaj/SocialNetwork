<?php
	session_start();

	try{
		$bdd= new PDO('mysql:host=localhost;dbname=social_network;charset=utf8',
					'root',
					'');
	} catch (Exception $e){
		die('Erreur : ' .$e->getMessage());
	}

	if (isset($_GET['search'])){
		$search = htmlspecialchars('%'.$_GET['search'].'%');
		$getProfiles = $bdd->prepare('SELECT pseudo FROM membre WHERE pseudo LIKE ?');
		$getProfiles->execute([
			$search
			]);

		if ($getProfiles->rowCount() == 0){
			$message = "Aucun profil n'a été trouvé";
		} else {
			$message = "<ul>";
			$profils = $getProfiles->fetchAll();
			foreach ($profils as $profil) {
				$message = $message."<li><a href=profil.php?pseudo=".$profil['pseudo'].">".$profil['pseudo']."</a></li>";
			}
			$message = $message."</ul>";
		}
	}

	if ((isset($_POST['description']) || isset($_FILES['importMedia']))
		&& (!empty($_POST['description']) || !empty($_FILES['importMedia']))){

			if ((isset($_FILES['importMedia']['error']) && $_FILES['importMedia']['error'] == 4) || empty($_FILES['importMedia'])){
				$post = $bdd->prepare('INSERT INTO publication(idPublier, description) VALUES(?, ?)');
				$post->execute([
					$_SESSION['id'],
					htmlspecialchars($_POST['description'])
				]);

			} elseif (!isset($_POST['description']) || empty($_POST['description'])){
				if($_FILES['importMedia']['error'] !== 0){
					die('Erreur lors de l\'importation du media. Veuillez réessayer');
				} else {
					$extensionAllowed = ['png', 'jpg', 'gif', 'mp3', 'mp4'];
					$extension = pathinfo($_FILES['importMedia']['name'])['extension'];
					if (!in_array($extension, $extensionAllowed)){
						die('Erreur : Le format du media n\'est pas pas pris en charge');
					} else{
						$getId = $bdd->prepare("SELECT MAX(idPublication) WHERE idPublier = ?");
						$getId->execute([
							$_SESSION['id']
						]);
						$id = $getId->fetch();
						$id++;

						$_FILES['importMedia']['name'] = $_SESSION['id']."media".$id.".".$extension;
						move_uploaded_file($_FILES['importMedia']['tmp_name'], '../css/publication/media/'.$_FILES['importMedia']['name']);
						$post = $bdd->prepare('INSERT INTO publication(idPublier, media) VALUES(?, ?)');
						$post->execute([
							$_SESSION['id'],
							$_FILES['importMedia']['name']
							]);
					}
				}

			} else {
				if($_FILES['importMedia']['error'] !== 0){
					die('Erreur 2 lors de l\'importation du media. Veuillez réessayer');
				} else {
					$extensionAllowed = ['png', 'jpg', 'gif', 'mp3', 'mp4'];
					$extension = pathinfo($_FILES['importMedia']['name'])['extension'];
					if (!in_array($extension, $extensionAllowed)){
						die('Erreur : Le format du media n\'est pas pas pris en charge');
					} else{
						$getId = $bdd->prepare("SELECT MAX(idPublication) WHERE idPublier = ?");
						$getId->execute([
							$_SESSION['id']
						]);
						$id = $getId->fetch();
						$id++;

						$_FILES['importMedia']['name'] = $_SESSION['id']."media".$id.".".$extension;
						move_uploaded_file($_FILES['importMedia']['tmp_name'], '../css/publication/media/'.$_FILES['importMedia']['name']);
						$post = $bdd->prepare('INSERT INTO publication(idPublier, description, media) VALUES(?, ?, ?)');
						$post->execute([
							$_SESSION['id'],
							htmlspecialchars($_POST['description']),
							$_FILES['importMedia']['name']
						]);
					}
				}
			}
	}

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="../css/accueil.css">
	<title>Accueil</title>
</head>
<?php
	include 'header.php';
?>
<body>
	<main>
		<form method="get" action="accueil.php">
			<div>
				<input type="text" name="search" id="search">
				<input type="submit" value="Rechercher">
			</div>
		</form>
		<div>
			<button class="Publier">Publier</button>
		</div>
		<div>
			<?php
				if(isset($message)){
					echo $message;
				}
			?>
		</div>
		<div id="fildactualite">
			<?php
			$getFil = $bdd->prepare("SELECT * FROM publication
				JOIN membre ON idPublier = id
				WHERE id IN (
					SELECT idFollowed FROM follow
					WHERE idFollower = ?
				) ORDER BY date_publication DESC");
			$getFil->execute([
				$_SESSION['id']
			]);

			if ($getFil->rowCount() == 0){
				$message = "Pas de publications pour le moment";
			} else {
				$fil = $getFil->fetchAll();
				$length = count($fil);

				for($index = 0; $index < $length; $index++){
					echo "<div class='filPublication'>".
						"<ul>".
								"<li>".$fil[$index]['pseudo']."</li>".
								"<li>".$fil[$index]['date_publication']."</li>".
								"<li>".$fil[$index]['description']."</li>";
								if (!empty($fil[$index]['media'])){
									echo "<li><img src='../css/publication/media/".$fil[$index]['media']."' style='height: 100px;'></li>";
								}
								echo "<li>".'Likes : '.$fil[$index]['nbLikes']."</li>".
						"</ul>".
						"</div>";
				}
			}
			?>
		</div>
	</main>

</body>
<div class="publierWindow">
	<div>
		<button class="cancelPublier">Annuler</button>
	</div>
	<form method="post" action="accueil.php" enctype="multipart/form-data">
		<div>
			<label for="description">Description : </label>
			<textarea name="description" id="description"></textarea>
		</div>
		<div>
			<label for="importMedia">Importer un media : </label>
			<input type="file" name="importMedia" id="importMedia">
		</div>
		<div>
			<input type="submit" name="envoyer" value="ok">
		</div>
	</form>
</div>
<script type="text/javascript" src="../js/frameworks/jquery.js">
	alert("framework not found");
</script>
<script type="text/javascript" src="../js/publier.js"></script>
</html>