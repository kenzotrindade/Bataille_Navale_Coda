<?php

require "test_db.php";

$matrice = array_fill(0, 10, array_fill(0, 10, 0));

$tailleMatrice = isset($_POST["tailleMatrice"]) ? (int)$_POST["tailleMatrice"] : 10;

function creerMatrice($taille)
{
    return array_fill(0, $taille, array_fill(0, $taille, 0));
}

$matrice = creerMatrice($tailleMatrice);

function placer($pdo, $grille, $game_id, $player_id){
    $sql = "SELECT * FROM ships WHERE game_id = ? AND player_id = ?";
    $query = $pdo->prepare($sql);
    $query->execute([$game_id, $player_id]);
    $bateaux = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bateaux as $bateau) {

        $x = $bateau['start_x'];
        $y = $bateau['start_y'];
        $taille = $bateau['size'];
        $direction = $bateau['orientation'];


        for ($i = 0; $i < $taille; $i++) {
            if ($direction == "horizontale") {
                $grille[$y][$x + $i] = $taille;
            } elseif ($direction == "verticale") {
                $grille[$y + $i][$x] = $taille;
            } else {
                echo "Veuillez entrer une direction valide !";
                break;
            }
        }
    }
    return $grille;
}

function tirer($grille, $x, $y)
{
    if ($grille[$y][$x] != 0) {
        $grille[$y][$x] = "X";
    } else {
        $grille[$y][$x] = "O";
    }
    return $grille;
}

$matrice = creerMatrice(10);
$matrice = placer($pdo, $matrice, 1, 1); 
$matrice = tirer($matrice, 2, 3);
