<!DOCTYPE html>
<html>
<head>

    <title> Menu </title>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="home.css">
</head>
<body>
<?php
include ("Barre_de_Navigation.html");
?>
<h1>
    Home
</h1>
<p>
    <a href="Nouvellepartie.php">
        <button id="boutton">
<table>
    <td width="100px"><img src="pions.png" alt="nouvellepartie" style="width:50px;height:50px;" ></td>
    <td width="100px"><span style="text-align: center;">Nouvelle partie</span></td>
</table>
</button>
</a>
<br><br><br>
<a href="Reprendrepartie.php">
    <button id="boutton">
        <table>
            <td width="100px"><img src="plateau.png" alt="reprendrepartie" style="width:50px;height:50px;" ></td>
            <td width="100px"><span style="text-align: center;">Reprendre partie</span></td>
        </table>
    </button>
</a>
<br><br><br>
<a href="profil.php">
    <button id="boutton">
        <table>

            <td width="100px"><img src="profil.png" alt="profile" style="width:50px;height:50px;" ></td>
            <td width="100px"><span style="text-align: center;">Profil</span></td>

        </table>
    </button>
</a>
<br><br><br>
<a href="classement.php">
    <button id="boutton">
        <table>

            <td width="100px"><img src="classement.png" alt="profile" style="width:50px;height:50px;" ></td>
            <td width="100px"><span style="text-align: center;">Classement</span></td>

        </table>
    </button>
</a>
</p>
<br>
<p>Vous nous quittez déjà ? <a href="connexion.php"> Déconnexion </a> </p>


</body>
</html>
