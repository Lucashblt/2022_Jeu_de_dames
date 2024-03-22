<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Nouvelle partie</title>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="Nouvellepartie.css">
    <?php
    include("Barre_de_Navigation.html");
    session_start();
    // on importe la bdd et on va chercher les informations des joueurs différent de l'utilisateur
    $bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");
    $req = $bdd->prepare("SELECT id_joueur, pseudo, parties_gagnees FROM joueur WHERE id_joueur<>?;");
    $req->execute([$_SESSION['id_joueur']]);

    ?>
    <h1> Création de la partie </h1>
    <form method="post" action="" style="text-align: center;" >
    <table align="center" class="table1">
        <tr>
            <td style="border: 1px solid #333;" align="center" class="colonne1">
                <table >
                     <tr>
                        <input type="text" name="nom"  size="30" placeholder="Nom de la partie" style="text-align: center; font-size: 115%" required/>
                    </tr>
                    <br>
                    <br>
                    <tr style="height: 100%;">
                       <td  align="center">
                           <img src="couleurpion.png" alt="couleur" style="width: 40%;height:40%;">
                           <p>
                           <input  type="radio" name="pioncouleur" value="blanc" required/>
                               <label class="profillabel"> Blanc </label>
                           <input  type="radio" name="pioncouleur" value="noir" required/>
                               <label class="profillabel"> Noir </label>
                           </p>
                        </td>
                    </tr>
                    </form>
                </table>
            </td>
            <td class="colonne2" ></td>
            <td style="border: 1px solid #333;" class="colonne3">
                    <label class="profillabel"> Adversaire (nb victoire) : </label>
                    <p>

                        <select name="adversaire" size="5" style="width: 80%;" required>
                            <?php
                            while($data = $req->fetch())
                            {
                                ?>

                                <option value="<?php echo $data["id_joueur"]; ?>">
                                    <?php

                                    echo $data["pseudo"];?> (<?php
                                    echo $data["parties_gagnees"];
                                    ?>)</option>
                                <?php
                            }
                        ?>
                        </select>
                    </p>
            </td>
        </tr>
    </table>
    <?php
    $bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");
    $req = $bdd->prepare("INSERT INTO partie(nom_partie,id_joueur1,id_joueur2,next_color) VALUES (?,?,?,?);");

    // on crée une partie dans laquelle on repertorie les joueurs

    if($_POST) {
        $nom_partie = $_POST['nom'];

        // id_joueur1 prends les pions blancs
        $pioncouleur= $_POST['pioncouleur'];
        if ($pioncouleur == 'blanc') {
            $id_joueur1 = $_SESSION['id_joueur'];
            $id_joueur2 = $_POST['adversaire'];
            $next_color = 1;
        }else if($pioncouleur == 'noir') {
            $id_joueur1 = $_POST['adversaire'];
            $id_joueur2 = $_SESSION['id_joueur'];
            $next_color = 1;
        }

        $req->execute([$nom_partie,$id_joueur1,$id_joueur2,$next_color]);

        // on affecte les coordonnées des pions en fonction de la couleur et on affecte la couleur aux joueur
        $req = $bdd->prepare("INSERT INTO pion(type_pion, id_joueur, pos_x, pos_y, couleur_pion, id_partie) VALUES (?,?,?,?,?,?);");

        $id_partie = $bdd->lastInsertId();
        $_SESSION['id_partie'] = $id_partie;

        $type_pion = 1;
        $id_joueur = $id_joueur1;
        $couleur_pion= 1;

        $a = 2;
        $colonne = 1;
        for ($colonne = 1; $colonne < 11; $colonne++) {
            $ligne = 1;
            for ($ligne = 1; $ligne < 5; $ligne++) {
                if (($ligne % $a == 0 && $colonne % $a != 0) || ($ligne % $a != 0 && $colonne % $a == 0)) {

                    $pos_x = $colonne;
                    $pos_y = $ligne;
                    $req->execute([$type_pion,$id_joueur,$pos_x,$pos_y,$couleur_pion,$id_partie]);
                }
            }
        }

        $id_joueur=$id_joueur2;
        $couleur_pion= 2;

        $ligne = 7;
        for ($ligne = 7; $ligne < 11; $ligne++) {
            $colonne = 1;
            for ($colonne = 1; $colonne < 11; $colonne++) {
                if (($ligne % $a == 0 && $colonne % $a != 0) || ($ligne % $a != 0 && $colonne % $a == 0)) {

                    $pos_x = $colonne;
                    $pos_y = $ligne;
                    $req->execute([$type_pion,$id_joueur,$pos_x,$pos_y,$couleur_pion,$id_partie]);
                }
            }
        }
        //header('Location: plateau.php');
        echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=plateau.php">';
    }
    ?>
    <br><br>
    <input class="bouton" type="submit"  value="Créer une partie"/>
    </body>
</html>