<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Reprendre partie</title>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="Reprendrepartie.css">
</head>
    <body>
    <?php
    include("Barre_de_Navigation.html");
    session_start();
    $bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");

    $reqPartie = $bdd->prepare("SELECT id_partie, nom_partie,  id_joueur1, id_joueur2, next_color FROM partie WHERE partie_finie=0 AND (id_joueur1=? OR id_joueur2=?) order by id_partie desc;");
    $reqPartie->execute([$_SESSION['id_joueur'], $_SESSION['id_joueur']]);

    $reqJoueur = $bdd->prepare("SELECT pseudo FROM joueur WHERE id_joueur=?;");


    if ($_POST) {
        $_SESSION['id_partie'] = $_POST['parties'];
        header('Location: plateau.php');
    }


    ?>

    <h1> Reprendre partie </h1>
    <form method="post" action="" style="text-align: center;">
        <table align="center" style="margin-right: auto; margin-left: auto; width: 50%;">
            <tr>
                <td style="border: 1px solid #333;" align="center" class="colonne">
                    <label class="profillabel"> Nom de la partie, adversaire, prochain joueur : </label>
                    <p>

                        <select name="parties" size="10" style="width: 80%;" required>
                            <?php
                            while ($dataPartie = $reqPartie->fetch()) {
                                if ($dataPartie['id_joueur1'] == $_SESSION['id_joueur'] ){
                                    $reqJoueur->execute([$dataPartie['id_joueur2']]);
                                } else {
                                    $reqJoueur->execute([$dataPartie['id_joueur1']]);
                                }
                                if($dataJoueur = $reqJoueur->fetch()){
                                    ?>
                                    <option value="<?php echo $dataPartie["id_partie"]; ?>" >

                                        <?php
                                        echo str_pad($dataPartie["nom_partie"], 32);
                                        ?>
                                         contre
                                        <?php
                                        echo $dataJoueur["pseudo"];
                                        ?>
                                        (
                                        <?php
                                        if ($dataPartie['next_color'] == 1){
                                            echo 'blanc';
                                        }else {
                                            echo 'noir';
                                        }

                                        ?>)
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </p>
                </td>
                </tr>
        </table>
    <br><br>
    <input class="bouton" type="submit" value="Reprendre partie"/>
    </form>
    </body>
</html>
