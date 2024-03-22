<!DOCTYPE html>
<html lang="fr">
<head>
    <title> Jeu de dames </title>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="plateau.css">
</head>
<body>

<?php
include("Barre_de_Navigation.html");
include "fonctions_plateau.php";

// ************************************
// Initialisation
// ************************************
session_start();
$id_partie = $_SESSION['id_partie'];

$doubleCoup = False; // Pour obliger à garder la main, si la prise de pion est encore possible

$bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");
$reqPion = $bdd->prepare("SELECT id_pion, type_pion, couleur_pion FROM pion WHERE id_partie=? AND pos_x=? AND pos_y=?;");

// Récupération des infos de la partie
$reqPartie = $bdd->prepare("SELECT nom_partie, id_joueur1, id_joueur2, next_color FROM partie WHERE  id_partie=?;");
$reqPartie->execute([$id_partie]);
if ($dataPartie = $reqPartie->fetch()) {
    $nextColor = $dataPartie["next_color"];

}

$reqCountPion = $bdd->prepare("SELECT count(id_pion) as nb FROM pion WHERE id_partie=? AND couleur_pion=?;");
$reqPartiefini = $bdd->prepare("UPDATE partie SET partie_finie=? WHERE id_partie=?;");

$reqVainqueur =  $bdd->prepare("UPDATE joueur SET parties_gagnees=parties_gagnees+1, parties_jouees=parties_jouees+1 WHERE id_joueur=?;");
$reqPerdant= $bdd->prepare("UPDATE joueur SET parties_jouees=parties_jouees+1 WHERE id_joueur=?;");

$reqCountPion->execute([$id_partie,1]);
if ( $data = $reqCountPion->fetch()){
    if ( $data['nb'] == 0){
        $partie_finie= 1;
        $reqVainqueur->execute([$dataPartie["id_joueur2"]]);
        $reqPerdant->execute([$dataPartie['id_joueur1']]);
        ?>
        <script type="text/javascript">
            alert("Victoire des noirs")
        </script>
        <?php
    }else {
        $partie_finie=0;
    }
}
$reqCountPion->execute([$id_partie,2]);
if ( $data = $reqCountPion->fetch()){
    if ( $data['nb'] == 0){
        $partie_finie=1;
        $reqVainqueur->execute([$dataPartie["id_joueur1"]]);
        $reqPerdant->execute([$dataPartie['id_joueur2']]);
        ?>
        <script type="text/javascript">
            alert("Victoire des blancs")
        </script>
        <?php
    }else {
        $partie_finie=0;
    }
}
$reqPartiefini->execute([$partie_finie, $id_partie]);


// Gestion des différents cas, selon les paramètres passés par l'URL
if (count($_GET) > 0) {
    $type = $_GET['type'];
    if ($type == 'pion') {
        // on vient de cliquer sur un pion (pion simple ou dame)
        $x = $_GET['x'];
        $y = $_GET['y'];
        $typePion = $_GET['typePion'];
    } else {
        // on vient de cliquer sur une case, donc pour faire un mouvement
        $x = 0;
        $y = 0;
        $x1 = intval($_GET['x1']);
        $y1 = intval($_GET['y1']);
        $x2 = intval($_GET['x2']);
        $y2 = intval($_GET['y2']);
        $typePion = intval($_GET['typePion']);

        if ($typePion == 1) {
            // Cas du pion simple
            if ((abs($x1 - $x2) == 1 && ($y2 - $y1) == 1 && $nextColor == 1) ||
                (abs($x1 - $x2) == 1 && ($y2 - $y1) == -1 && $nextColor == 2)) {
                $typePion = deplacerPion($bdd, $x2, $y2, $typePion, $id_partie, $x1, $y1, $nextColor);
                $nextColor = changerJoueur($bdd, $id_partie, $nextColor);

            } elseif ((abs($x1 - $x2) == 2 && abs($y2 - $y1) == 2)) {
                // On mange un pion adverse
                $reqPionAManger = $bdd->prepare("SELECT id_pion, couleur_pion FROM pion WHERE id_partie=? AND pos_x=? AND pos_y=? AND couleur_pion<>?;");
                $x_a_manger = $x1 - ($x1 - $x2) / 2;
                $y_a_manger = $y1 - ($y1 - $y2) / 2;
                $reqPionAManger->execute([$id_partie, $x_a_manger, $y_a_manger, $nextColor]);
                if ($dataPionAManger = $reqPionAManger->fetch()) {
                    supprimerPion($bdd, $dataPionAManger["id_pion"]);
                    $typePion = DeplacerPion($bdd, $x2, $y2, $typePion, $id_partie, $x1, $y1, $nextColor);
                    $x = $x2;
                    $y = $y2;
                    $type = 'pion';
                    $doubleCoup = caseMangeableAutour($bdd, $id_partie, $x, $y, $typePion, $nextColor);
                    if ($doubleCoup == false) {
                        $nextColor = changerJoueur($bdd, $id_partie, $nextColor);
                        $x = 0;
                        $y = 0;
                    }
                } else {
                    $x = $x1;
                    $y = $y1;
                    $type = 'pion';
                }
            } else {
                $x = $x1;
                $y = $y1;
                $type = 'pion';
            }
        } else {
            // Cas d'une dame
            if (abs($x1 - $x2) == abs($y1 - $y2)) {
                $changementJoueur = true;
                $nb = 0;
                $idpion_manger = 0;

                for ($i = 1; $i < abs($x1 - $x2); $i++) {
                    $reqPion->execute([$id_partie, $x1 + $i * sign($x2 - $x1), $y1 + $i * sign($y2 - $y1)]);
                    if ($dataPion = $reqPion->fetch()) {
                        if ($dataPion['couleur_pion'] == $nextColor) {
                            $nb = 2; // si même couleur, on ne pourra rien faire
                        } else {
                            $idpion_manger = $dataPion['id_pion'];
                            $nb++;
                        }
                    }
                }

                if ($nb == 1) {
                    supprimerPion($bdd, $idpion_manger);
                    $nb = 0;
                    $doubleCoup = caseMangeableAutour($bdd, $id_partie, $x2, $y2, $typePion, $nextColor);
                    if ($doubleCoup == true) {
                        //echo '$changementJoueur faux <br>';
                        $changementJoueur = false;
                    }
                }
                if ($nb == 0) {
                    $typePion = deplacerPion($bdd, $x2, $y2, $typePion, $id_partie, $x1, $y1, $nextColor);
                    $x = $x2;
                    $y = $y2;
                    $type = 'pion';

                    if ($changementJoueur == true) {
                        $nextColor = changerJoueur($bdd, $id_partie, $nextColor);
                        $x = 0;
                        $y = 0;
                    }
                } else {
                    //on fait rien
                    $x = $x1;
                    $y = $y1;
                    $type = 'pion';
                }
            } else {
                //on fait rien
                $x = $x1;
                $y = $y1;
                $type = 'pion';
            }
        }
    }
} else {
    $type = '';
    $x = 0;
    $y = 0;
    $x1 = 0;
    $y1 = 0;
    $x2 = 0;
    $y2 = 0;
    $typePion = '';
}
?>

<script type="text/javascript">
    <!--
    function clickcase(url, couleur_pion) {
        if (couleur_pion == <?php echo $nextColor; ?>)
            document.location.href = url;
    }

    -->
</script>
<h1>Jeu de dames</h1>
<table style=" margin-left: auto; margin-right: auto; width: 100%;">
    <tr>
        <td class="grille">
            <table id="damier">
                <?php
                //creation du damier et de l'affichage des pions
                $ligne = 1;
                $a = 2;
                for ($ligne = 1; $ligne < 11; $ligne++) {
                    $colonne = 1;
                    ?>
                    <tr style="width: 520px; height: 52px">
                        <?php
                        for ($colonne = 1; $colonne < 11; $colonne++) {
                            if (($ligne % $a == 0 && $colonne % $a != 0) || ($ligne % $a != 0 && $colonne % $a == 0)) {
                                $reqPion->execute([$id_partie, $colonne, $ligne]);
                                if ($dataPion = $reqPion->fetch()) {
                                    $couleur_pion = $dataPion["couleur_pion"];
                                    $typePion = $dataPion["type_pion"];
                                    if ($doubleCoup == True) {
                                        ?>
                                        <td class="noir" style="text-align: center;">
                                        <?php
                                    } else {
                                        ?>
                                        <td class="noir" style="text-align: center;" onclick="clickcase('plateau.php?type=pion&amp;x=<?php echo $colonne; ?>&amp;y=<?php echo $ligne; ?>&amp;typePion=<?php echo $typePion; ?>', <?php echo $couleur_pion; ?>)">
                                        <?php
                                    }
                                    if ($couleur_pion == 1) {
                                        // Couleur blanc
                                        $sens = 1;
                                        $pionNormal = "pionblanc";
                                        $pionGris = "piongrise";
                                        $dameNormale = "reineblanc";
                                        $dameGrise = "reinegris";
                                        $imageNormale = "damen.png";
                                        $imageGrise = "dameb.png";
                                    } else {
                                        // Couleur noir
                                        $sens = -1;
                                        $pionNormal = "pionnoir";
                                        $pionGris = "piongrise";
                                        $dameNormale = "reinenoir";
                                        $dameGrise = "reinegris";
                                        $imageNormale = "dameb.png";
                                        $imageGrise = "damen.png";
                                    }
                                    if ($couleur_pion != $nextColor || $_SESSION['id_joueur'] != $dataPartie["id_joueur" . $nextColor]) {
                                        $disabled = 'disabled';
                                    } else {
                                        $disabled = '';
                                    }
                                    $verif = false;
                                    if ($type == 'pion' && $colonne == $x && $ligne == $y) {
                                        if (caseVidePossible($bdd, $id_partie, $x, $y, $typePion, $sens)) {
                                            $verif = true;
                                        }
                                        if ($verif == false) {
                                            $verif = caseMangeableAutour($bdd, $id_partie, $x, $y, $typePion, $nextColor);
                                        }
                                    }
                                    if ($verif == true) {
                                        if ($typePion == 1) {
                                            ?>
                                            <button class="<?PHP echo $pionGris; ?>"></button>
                                            <?php
                                        } else { ?>
                                            <button class="<?PHP echo $dameGrise; ?>"><img
                                                        src="<?PHP echo $imageGrise; ?>"
                                                        style="width: 80%;height:80%;"></button>
                                            <?php
                                        }
                                    } else {
                                        if ($typePion == 1) {
                                            ?>
                                            <button class="<?PHP echo $pionNormal; ?>" <?PHP echo $disabled; ?>></button>
                                            <?php
                                        } else {
                                            ?>
                                            <button class="<?PHP echo $dameNormale; ?>" <?PHP echo $disabled; ?>><img
                                                        src="<?PHP echo $imageNormale; ?>"
                                                        style="width: 80%;height:80%;">
                                            </button>
                                            <?php
                                        }
                                    }
                                } else {
                                    // case vide
                                    if ($type == 'pion') {
                                        ?>
                                        <td class="noir" style="text-align: center;" onclick="clickcase('plateau.php?type=case&amp;x1=<?php echo $x; ?>&amp;y1=<?php echo $y; ?>&amp;x2=<?php echo $colonne; ?>&amp;y2=<?php echo $ligne; ?>&amp;typePion=<?php echo $_GET['typePion']; ?>', <?php echo $nextColor; ?>)" >
                                        <?php
                                    } else {
                                        ?>
                                        <td class="noir" style="text-align: center;" >
                                        <?php
                                    }
                                }
                                ?>
                                </td>
                                <?php
                            } else if (($ligne % $a == 0 && $colonne % $a == 0) || ($ligne % $a != 0 && $colonne % $a != 0)) {
                                ?>
                                <td class="blanc"></td>
                                <?php
                            }
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>

            </table>
        </td>
        <td style="border: 1px solid #333;" class="infopartie">
            <?php
            // informations de la partie
            $reqJoueur = $bdd->prepare("SELECT pseudo, id_joueur  FROM joueur WHERE  id_joueur=?;");
            ?>
            <p class="profilpartie">
                Nom de la partie :
                <br>
                <?php
                echo $dataPartie["nom_partie"];
                $reqJoueur->execute([$dataPartie["id_joueur1"]]);
                ?>
                <br><br>
                Joueurs :
                <br>
                <?php
                if ($dataJoueur = $reqJoueur->fetch()) {
                    if ($_SESSION['id_joueur'] == $dataJoueur["id_joueur"]){
                        echo '<b><i>'.$dataJoueur["pseudo"].'</i></b>';
                    }else{
                        echo $dataJoueur["pseudo"];
                    }
                }
                $reqJoueur->execute([$dataPartie["id_joueur2"]]);
                ?>
                vs
                <?php
                if ($dataJoueur = $reqJoueur->fetch()) {
                    if ($_SESSION['id_joueur'] == $dataJoueur["id_joueur"]){
                        echo '<b><i>'.$dataJoueur["pseudo"].'</i></b>';
                    }else{
                        echo $dataJoueur["pseudo"];
                    }
                }
                ?>
                <br>
                Blanc vs Noir
                <br><br>
                Prochain joueur :
                <br>
                <?php
                if ($nextColor == 1) {
                    $reqJoueur->execute([$dataPartie["id_joueur1"]]);
                    if ($dataJoueur = $reqJoueur->fetch()) {
                        echo $dataJoueur["pseudo"];
                    }
                } else {
                    $reqJoueur->execute([$dataPartie["id_joueur2"]]);
                    if ($dataJoueur = $reqJoueur->fetch()) {
                        echo $dataJoueur["pseudo"];
                    }
                }
                //on rafraichit la page tout les 5 secondes pour redessiner les pions en fonctions des changements
                if (($_SESSION['id_joueur'] != $dataPartie["id_joueur" . $nextColor]) && $partie_finie=0) {
                    echo '<META HTTP-EQUIV="Refresh" CONTENT="3;URL=plateau.php">';
                }
                ?>
            </p>
        </td>
    </tr>
</table>
</body>
</html>