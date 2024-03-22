<!DOCTYPE html>
<html>
<head>
    <title> Connexion </title>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>
<?php
@session_start();
$bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");
$req = $bdd->prepare("SELECT id_joueur FROM joueur WHERE email=? AND mdp=?;");

if($_POST) {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];
    $req->execute([$email, $mdp]);
    // on regarde si le mail et le mdp est existe
    if($data = $req->fetch()){
        //on passe dans une valeur de session l'id de l'utilisateur
        $id_session = $data["id_joueur"];
        $_SESSION['id_joueur'] = $id_session;
        header('Location:home.php');
    }
    else {
        echo '<h3 align="center"><u>Erreur d email ou mot de passe</u></h3>';
    }
}
?>
<h1><u>Connexion</u></h1>
<form method="post" action="" style="text-align: center; ">

    <input class="barre" name="email" size="50" type="email" placeholder="E-mail" style="text-align: center" required><br>
    <input class="barre" name="mdp" size="50" type="password" placeholder="Mot de passe" style="text-align: center" required> <br>
    <input class="bouton" style="width: 130px" type="submit" value="Connexion">
</form>
<p style="text-align: center;" >
    Pas de compte ?<br><a href="inscription.php">Inscrivez-vous !</a>
</p>

</body>
</html>