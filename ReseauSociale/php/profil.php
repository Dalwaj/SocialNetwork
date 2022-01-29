<?php
	session_start();

	try{
		$bdd= new PDO('mysql:host=localhost;dbname=social_network;charset=utf8',
					'root',
					'');
	} catch (Exception $e){
		die('Erreur : ' .$e->getMessage());
	}

	$getProfile = $bdd->prepare('SELECT id, pseudo, email, pdp FROM membre WHERE pseudo LIKE ?');
	$getProfile->execute([
		$_GET['pseudo']
	]);

	if($getProfile->rowCount() == 0){
		die("Erreur : le profil n'existe pas");
	} else {
		$profil = $getProfile->fetch();
	}

	if (isset($_POST['follow'])){
		$suivre = $bdd->prepare("INSERT INTO follow VALUES(? , ?)");
		$suivre->execute([
			$_SESSION['id'],
			$profil['id']
		]);
	} elseif (isset($_POST['unfollow'])){
		$unfollow = $bdd->prepare("DELETE FROM follow WHERE idFollower = ? AND idFollowed = ?");
		$unfollow->execute([
			$_SESSION['id'],
			$profil['id']
		]);
	}

	$getSuivi = $bdd->prepare('SELECT idFollowed FROM follow WHERE idFollower = ? AND idFollowed = ?');
	$getSuivi->execute([
		$_SESSION['id'],
		$profil['id']
	]);

	$estSuivi = $getSuivi->rowCount() != 0 ? true : false;

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="../css/profil.css">
	<title><?php echo $profil['pseudo']?></title>
</head>
<?php
	include 'header.php';
?>
<body>
	<main>
		<div>
			<?php
				echo "<h1>".$profil['pseudo']."</h1>";

				if(isset($profil['pdp']) && !empty($profil['pdp'])){
					echo "<img class='pdp' src='../css/membre/avatar/".$profil['id']."pdp'>";
				}

				if ($profil['pseudo'] != $_SESSION['identifiant']){?>
					<form action="" method="POST">
					<?php
					$div = $estSuivi ? "<div><input name='unfollow' type='submit' value='Ne plus suivre'></div>" : "<div><input name='follow' type='submit' value='Suivre'></div>";
					echo $div; ?>
					</form>
					<?php
				}
				

				echo "<p>".$profil['email']."</p>";

				if ($profil['pseudo'] == $_SESSION['identifiant']){
					echo "<p><a href=\"editerProfil.php\">Modifier mon profil</a></p>";
				}
			?>
		</div>
		<div class="journal">
			<?php
			$getJournal = $bdd->prepare('SELECT * FROM publication WHERE idPublier = ? ORDER BY date_publication DESC');
			$getJournal->execute([
				$profil['id']
			]);
			if($getJournal->rowCount() == 0){
				echo "<p>".$profil['pseudo']." n'a pas encore publié</p>";
			} else {
				$journal = $getJournal->fetchAll();
				$length=count($journal);
				for($index = 0; $index<$length; $index++){
					echo "<div class='journalPublication'>".
						"<ul>".
								"<li>".$profil['pseudo']."</li>".
								"<li>".$journal[$index]['date_publication']."</li>".
								"<li>".$journal[$index]['description']."</li>";
								if (!empty($journal[$index]['media'])){
									echo "<li><img src='../css/publication/media/".$journal[$index]['media']."' style='height: 100px;'></li>";
								}
								echo "<li>".'Likes : '.$journal[$index]['nbLikes']."</li>".
						"</ul>".
						"</div>";
					}
				}
			?>
		</div>
	</main>

</body>
</html>