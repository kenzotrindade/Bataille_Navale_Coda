<?php

require "test_db.php";

$matrice = array_fill(0, 10, array_fill(0, 10, 0));

$tailleMatrice = isset($_POST["tailleMatrice"]) ? (int)$_POST["tailleMatrice"] : 10;

function creerMatrice($taille)
{
    return array_fill(0, $taille, array_fill(0, $taille, 0));
}

$matrice = creerMatrice($tailleMatrice);

$game_id_test = 1;
$player_id_test = 1;


$sql = "SELECT * FROM ships WHERE game_id = ? AND player_id = ?";
$query = $pdo->prepare($sql);
$query->execute([$game_id_test, $player_id_test]);
$bateaux = $query->fetchAll(PDO::FETCH_ASSOC);

function placer($grille, $taille, $depart_x, $depart_y, $direction)
{
    foreach ($bateaux as $bateau) {
        for ($i = 0; $i < $taille; $i++) {
            if ($direction == "horizontale") {
                $grille[$depart_y][$depart_x + $i] = $taille;
            } elseif ($direction == "verticale") {
                $grille[$depart_y + $i][$depart_x] = $taille;
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

$matrice = placer($matrice, 3, 2, 3, "horizontale");
var_dump($matrice);
$matrice = tirer($matrice, 2, 3);
