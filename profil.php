<!DOCTYPE html>
<html>
<head>
    <title>Profil</title>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="profil.css">
</head>
<body>
<?php
    include("Barre_de_Navigation.html");
    session_start();
    $bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");
    $req = $bdd->prepare("SELECT * FROM joueur WHERE id_joueur=?;");
    $req->execute([$_SESSION['id_joueur']]);

    if ($data = $req->fetch()) {

?>
<h1>Profil</h1>
<table>
    <tr>
        <td style="border: 1px solid #333;" align="center" class="colonne1">
            <table>
                <tr>
                    <?php
                    if ($data["genre"] == 'h'){
                        ?>
                        <img src="profilHomme.png" alt="profile" style="width: 80%;height:80%;">
                    <?php
                    } elseif($data["genre"] == 'f'){
                    ?>
                        <img src="profilFemme.png" alt="profile" style="width: 80%;height:80%;">
                    <?php
                    }else{
                        ?>
                        <img src="profil.png" alt="profile" style="width: 80%;height:80%;">
                        <?php
                    }
                    ?>

                </tr>
                <br>
                <tr style="border: 1px solid #333;" style="height: 100%;">

                    <label class="profillabel"> Statistiques :</label><br><br>
                    <label class="profilstat"> Parties jouées : <?php echo $data["parties_jouees"];?></label><br><br>
                    <label class="profilstat"> Parties gagnées : <?php echo $data["parties_gagnees"];?></label><br><br>
                    <label class="profilstat"> Taux de victoire : <?php
                        if ($data["parties_jouees"] == 0 ){
                             echo 'NaN';
                             ?>
                             </label><br><br>
                             <?php
                        }else{
                            echo intval(($data["parties_gagnees"]/$data["parties_jouees"])*100);?> %</label><br><br>
                        <?php
                            }
                            ?>
                </tr>
            </table>
        </td>
        <td class="colonne2" ></td>
        <td style="border: 1px solid #333;" class="colonne3">
            <table style="margin-top: 1%; width : 100%; padding-left: 5%;" >

                <td style="padding-top: 2%;">
                    <label class="profillabel"> Pseudo : </label> <br><br>
                    <label class="profillabel"> Prénom : </label><br><br>
                    <label class="profillabel"> Nom : </label><br><br>
                    <label class="profillabel"> Email :</label><br><br>
                    <label class="profillabel"> Mot de passe :</label><br><br>
                </td>
                <td style="padding-left: 5%;padding-top: 7%">
                    <form method="post" ><br>

                        <input class="input" type="text" name="pseudo" style="text-align: center" size="40" value="<?php echo $data["pseudo"];?>" > <br><br>
                        <input class="input" type="text" name="prenom" style="text-align: center" size="40" value="<?php echo $data["prenom"];?>" > <br><br>
                        <input class="input" type="text" name="familyname" style="text-align: center" size="40" value="<?php echo $data["nom"];?>" ><br><br>
                        <input class="input" type="email" name="email" style="text-align: center" size="40" value="<?php echo $data["email"];?>" ><br><br>
                        <input class="input" type="password" name="password" style="text-align: center" size="40" value="<?php echo $data["mdp"];?>"> <br><br>

                        <input class="bouton" type="submit" style="text-align: center" value="Modifier et Sauvegarder"/>
                    </form>
                </td>
                <?php
                $bdd = new PDO("mysql:host=localhost;dbname=jeu_de_dames;charset=utf8", "root", "");
                $req = $bdd->prepare("UPDATE joueur SET pseudo=? , prenom=?, nom=? , email=? , mdp=? WHERE id_joueur=?;");
                if($_POST){
                    $pseudo = $_POST['pseudo'];
                    $prenom = $_POST['prenom'];
                    $nom = $_POST['familyname'];
                    $email = $_POST['email'];
                    $mdp = $_POST['password'];

                    $req->execute([$pseudo, $prenom, $nom, $email, $mdp, $_SESSION['id_joueur']]);
                    header('Location: profil.php');
                }
                ?>
            </table>
        </td>
    </tr>
</table>
        <?php
    }
?>
</body>
</html>
