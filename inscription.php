<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="inscription.css">
</head>
<body>
<?php
$bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");
$req = $bdd->prepare("INSERT INTO joueur(nom,prenom,email,mdp,genre,pseudo) VALUES (?,?,?,?,?,?);");

if($_POST) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];
    $genre = $_POST['genre'];
    $pseudo = $_POST['pseudo'];


    $req->execute([$nom, $prenom, $email, $mdp, $genre, $pseudo]);

    header('Location: connexion.php');
}
?>

<form method="post" action="" style="text-align: center;" >
    <h1><u>INSCRIPTION GRATUITE !</u></h1>

    <input class="barre" type="text" name="nom"  size="30" placeholder="Nom" style="text-align: center" required/><br>
    <input class="barre" type="text" name="prenom" size="30" placeholder="Prénom" style="text-align: center" required/><br>
    <input class="barre" type="text" name="pseudo" size="30" placeholder="Pseudo" style="text-align: center" required/><br>
    <input class="rad" type="radio" name="genre" value="femme" />Femme<br>
    <input class="rad" type="radio" name="genre" value="homme" />Homme<br>
    <input class="rad" type="radio" name="genre" value="autre" />Autre<br>
    <input class="barre" id="text_field_1" name="email" size="30" placeholder="Email" style="text-align: center" type="email" required/><br>
    <input class="barre" type="password" name="mdp" size="30" placeholder="Mot de passe" style="text-align: center" required/><br>

    <p><input type="checkbox" required/> J'accepte les <a href="conditionutilisation.html" >conditions d'utilisation </a>ainsi<br>que la politique de non confidentialité.</p>
    <input class="bouton" type="submit" style="width: 130px" value="Inscription"/>

    <p>Vous avez déjà un compte ?<br>
        <a href="connexion.php">Connexion</a></p>
</form>
</body>
</html>