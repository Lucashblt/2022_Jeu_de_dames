<?php
function caseMangeable($bdd, $id_partie, $x, $y, $typePion, $sensX, $sensY, $nextColor){
    // on cherche un pion d'une autre couleur autour, avec une case vide derrière
     $reqPionDifferent = $bdd->prepare("SELECT id_pion, type_pion, couleur_pion FROM pion WHERE id_partie=? AND pos_x=? AND pos_y=? AND couleur_pion<>?;");
    $reqPionDifferent->execute([$id_partie, intval($x), intval($y), $nextColor]);
    if ($dataPionDifferent = $reqPionDifferent->fetch()) {
        $reqPion = $bdd->prepare("SELECT id_pion, type_pion, couleur_pion FROM pion WHERE id_partie=? AND pos_x=? AND pos_y=?;");
        $reqPion->execute([$id_partie, intval($x) + $sensX, intval($y) + $sensY]);
        if (!($dataPion = $reqPion->fetch())) {
            return true;
        }
    }
    return false;}

function caseMangeableAutour($bdd, $id_partie, $x, $y, $typePion, $nextColor){
    // on vérifie autour s'il y a des cases vides
    $doubleCoup = false;
    if ($typePion == 1)
        $max = 2;
    else
        $max = 9;
    for ($i = 1; $i < $max; $i++) {
        for ($j = -1; $j <= 1; $j += 2) {
            for ($k = -1; $k <= 1; $k += 2) {
                if ($x + $i * $j >= 2 && $x + $i * $j <= 9 &&
                    $y + $i * $k >= 2 && $y + $i * $k <= 9) {
                    if (caseMangeable($bdd, $id_partie, $x + $i * $j, $y + $i * $k, $typePion, $j, $k, $nextColor)) {
                        return true;
                    }
                }
            }
        }
    }
    return $doubleCoup;}

function caseVidePossible($bdd, $id_partie, $x, $y, $typePion, $sens){
    // On cherche les cases vides possibles autour
    $reqPion = $bdd->prepare("SELECT id_pion, type_pion, couleur_pion FROM pion WHERE id_partie=? AND pos_x=? AND pos_y=?;");
    if (intval($typePion) == 1) {
        // Pion simple
        if (intval($x) < 9) {
            $reqPion->execute([$id_partie, intval($x) + 1, intval($y) + $sens]);
            if (!($dataPion = $reqPion->fetch())) {
                return true;
            }
        }
        if (intval($x) > 1) {
            $reqPion->execute([$id_partie, intval($x) - 1, intval($y) + $sens]);
            if (!($dataPion = $reqPion->fetch())) {
                return true;
            }
        }
    } else {
        // Dame
        if (intval($x) < 9) {
            $reqPion->execute([$id_partie, intval($x) + 1, intval($y) + 1]);
            if (!($dataPion = $reqPion->fetch())) {
                return true;
            }
            $reqPion->execute([$id_partie, intval($x) + 1, intval($y) - 1]);
            if (!($dataPion = $reqPion->fetch())) {
                return true;
            }
        }
        if (intval($x) > 1) {
            $reqPion->execute([$id_partie, intval($x) - 1, intval($y) + 1]);
            if (!($dataPion = $reqPion->fetch())) {
                return true;
            }
            $reqPion->execute([$id_partie, intval($x) + 1, intval($y) - 1]);
            if (!($dataPion = $reqPion->fetch())) {
                return true;
            }
        }
    }
    return false;}

function changerJoueur($bdd, $id_partie, $nextColor){
    // on donne la main à l'adverse
    $reqChangementJoueur = $bdd->prepare("UPDATE partie SET next_color=? WHERE id_partie=?;");
    if ($nextColor == 1) {
        $nextColor = 2;
    } else {
        $nextColor = 1;
    }
    $reqChangementJoueur->execute([$nextColor, $id_partie]);
    return $nextColor;}

function deplacerPion($bdd, $x2, $y2, $typePion, $id_partie, $x1, $y1, $nextColor){
    // Déplacement dans la case adjacente, en fonction du sens de la couleur
    $reqUpdatePion = $bdd->prepare("UPDATE pion SET pos_x=?, pos_y=?, type_pion=? WHERE id_partie=? AND pos_x=? AND pos_y=?;");
    if (($typePion == 1 && $y2 == 10 && $nextColor == 1) || ($typePion == 1 && $y2 == 1 && $nextColor == 2)) {
        $typePion = 2; // on remplace par une dame si on arrive au bout du plateau
    }
    $reqUpdatePion->execute([$x2, $y2, $typePion, $id_partie, $x1, $y1]);
    return $typePion;}

function sign($n) {
    // permet de gèrer si positif ou négatif
    return ($n > 0) - ($n < 0);}

function supprimerPion($bdd, $id_pion){
    // Suppression d'un pion
    $reqDeletePion = $bdd->prepare("DELETE FROM pion WHERE id_pion=?;");
    $reqDeletePion->execute([$id_pion]);}
?>