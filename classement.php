<!DOCTYPE html>
<html lang="fr">
<head>
    <title> Jeu de dames </title>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="classement.css">
</head>
<body>
<?php
session_start();
include("Barre_de_Navigation.html");

$bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");

$reqClassement = $bdd->prepare( "SELECT pseudo, parties_jouees, parties_gagnees FROM joueur WHERE parties_jouees > 0 ORDER BY (parties_gagnees/parties_jouees * 100) DESC;");
$reqClassement->execute();

$reqPseudo = $bdd->prepare("SELECT id_joueur FROM joueur WHERE pseudo=?;");

if($_POST) {
    $pseudo = $_POST['pseudo'];
    $reqPseudo->execute([$pseudo]);
    if($data = $reqPseudo->fetch()){
        $id_joueur_chercher = $data["id_joueur"];
        $_SESSION['id_joueur_chercher'] = $id_joueur_chercher;
        header('Location: profilinvite.php');
    }
    else {
        echo '<h3 align="center"><u> Erreur de pseudo</u></h3>';
    }
}
?>
<h1>
    Classement
    <form class="RechercheJoueur" method="post">
        <input class="text" type="text" name="pseudo" style="text-align: center" size="30" placeholder="Pseudo">
        <input class="bouton" type="submit" style="text-align: center; "  value="Chercher Joueur"/>
        <br><br>
    </form>
</h1>

<table style="border: 1px solid #333; margin-left: auto; margin-right: auto; width: 80%; text-align: center; padding-top: 0px; ">
    <tr>
        <th>
            Rank
        </th>
        <th>
            Pseudo
        </th>
        <th>
            Nombre de partie jouees
        </th>
        <th>
            Nombre de partie gagnees
        </th>
        <th>
            Pourcentage de victoire
        </th>
    </tr>


        <?php
        $rank=1;

        while ($data = $reqClassement->fetch()) {
            ?>
            <tr>
            <?php

            $winrate= intval(($data["parties_gagnees"]/$data["parties_jouees"])*100);
            echo "<td class='rank'>{$rank}</td>
                <td class='pseudo'>{$data['pseudo']}</td>
                <td class='chiffre'>{$data['parties_jouees']}</td>
                <td class='chiffre'>{$data['parties_gagnees']}</td>
                <td class='chiffre'>{$winrate}</td>";
            $rank++;
            ?>
            </tr>
        <?php
            }

        ?>

</table>



</body>
</html>
